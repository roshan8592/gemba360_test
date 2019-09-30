<?php
/**
 * interface/eRxPage.php Functions for redirecting to NewCrop pages.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Sam Likins <sam.likins@wsi-services.com>
 * @copyright Copyright (c) 2015 Sam Likins <sam.likins@wsi-services.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


class eRxPage
{

    const DEBUG_XML    = 1;
    const DEBUG_RESULT = 2;

    private $xmlBuilder;
    private $authUserId;
    private $destination;
    private $clientId;
    private $prescriptionIds;
    private $prescriptionCount;

    public function __construct($xmlBuilder = null)
    {
        if ($xmlBuilder) {
            $this->setXMLBuilder($xmlBuilder);
        }
    }

    /**
     * Set XMLBuilder to handle eRx XML
     * @param  object  $xmlBuilder The eRx XMLBuilder object to use for processing
     * @return eRxPage             This object is returned for method chaining
     */
    public function setXMLBuilder($xmlBuilder)
    {
        $this->xmlBuilder = $xmlBuilder;

        return $this;
    }

    /**
     * Get XMLBuilder for handling eRx XML
     * @return object The eRx XMLBuilder object to use for processing
     */
    public function getXMLBuilder()
    {
        return $this->xmlBuilder;
    }

    /**
     * Set the Id of the authenticated user
     * @param  integer $userId The Id for the authenticated user
     * @return eRxPage         This object is returned for method chaining
     */
    public function setAuthUserId($userId)
    {
        $this->authUserId = $userId;

        return $this;
    }

    /**
     * Get the Id of the authenticated user
     * @return integer The Id of the authenticated user
     */
    public function getAuthUserId()
    {
        return $this->authUserId;
    }

    /**
     * Set the destination for the page request
     * @param  string  $destination The destination for the page request
     * @return eRxPage              This object is returned for method chaining
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get the destination for the page request
     * @return string The destination for the page request
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set the Client Id for the page request
     * @param  integer $clientId The Client Id for the page request
     * @return eRxPage            This object is returned for method chaining
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get the Client Id for the page request
     * @return string The Client Id for the page request
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set the Prescription Ids to send with page request
     * @param  string  $prescriptionIds The Prescription Ids for the page request
     * @return eRxPage                  This object is returned for method chaining
     */
    public function setPrescriptionIds($prescriptionIds)
    {
        $this->prescriptions = explode(':', $prescriptionIds);

        return $this;
    }

    /**
     * Get the Prescription Ids for the page request
     * @return string The Prescription Ids for the page request
     */
    public function getPrescriptionIds()
    {
        $this->prescriptionIds;
    }

    /**
     * Set the prescription count for the page request
     * @param  string  $count The prescription count for the page request
     * @return eRxPage        This object is returned for method chaining
     */
    public function setPrescriptionCount($count)
    {
        $this->prescriptionCount = $count;

        return $this;
    }

    /**
     * Get the prescription count for the page request
     * @return string The prescription count for the page request
     */
    public function getPrescriptionCount()
    {
        return $this->prescriptionCount;
    }

    /**
     * Check for required PHP extensions, return array of messages for missing extensions
     * @return array Array of messages for missing extensions
     */
    public function checkForMissingExtensions()
    {
        $extensions = array(
            'XML',
            'SOAP',
            'cURL',
            'OpenSSL',
        );

        $messages = array();

        foreach ($extensions as $extension) {
            if (!extension_loaded(strtolower($extension))) {
                $messages[] = xl('Enable Extension').' '.$extension;
            }
        }

        return $messages;
    }

    /**
     * Construct the XML document
     * @return eRxPage This object is returned for method chaining
     */
    public function buildXML()
    {
        $XMLBuilder = $this->getXMLBuilder();
        $NCScript = $XMLBuilder->getNCScript();
        $Store = $XMLBuilder->getStore();
        $authUserId = $this->getAuthUserId();
        $destination = $this->getDestination();
        $clientId = $this->getClientId();

        $NCScript->appendChild($XMLBuilder->getCredentials());
        $NCScript->appendChild($XMLBuilder->getUserRole($authUserId));
        $NCScript->appendChild($XMLBuilder->getDestination($authUserId, $destination));
        $NCScript->appendChild($XMLBuilder->getAccount());
        $XMLBuilder->appendChildren($NCScript, $XMLBuilder->getStaffElements($authUserId, $destination));
        $XMLBuilder->appendChildren($NCScript, $XMLBuilder->getClientElements($clientId, $this->getPrescriptionCount(), $this->getPrescriptionIds()));

        return array(
            'demographics' => $XMLBuilder->getDemographicsCheckMessages(),
            'empty' => $XMLBuilder->getFieldEmptyMessages(),
            'warning' => $XMLBuilder->getWarningMessages(),
        );
    }

    /**
     * Return a string version of the constructed XML cleaned-up for NewCrop
     * @return string NewCrop ready string of the constructed XML.
     *
     * XML has had double-quotes converted to single-quotes and \r and \t has been removed.
     */
    public function getXML()
    {
        return preg_replace(
            '/\t/',
            '',
            preg_replace(
                '/&#xD;/',
                '',
                preg_replace(
                    '/"/',
                    '\'',
                    $this->getXMLBuilder()->getDocument()->saveXML()
                )
            )
        );
    }

    protected function errorLog($message)
    {
        $date = date('Y-m-d');
        $path = $this->getXMLBuilder()->getGlobals()
            ->getOpenEMRSiteDirectory().'/documents/erx_error';

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $fileHandler = fopen($path.'/erx_error'.'-'.$date.'.log', 'a');

        fwrite($fileHandler, date('Y-m-d H:i:s').' ==========> '.$message.PHP_EOL);

        fclose($fileHandler);
    }

    public function checkError($xml)
    {
        $XMLBuilder = $this->getXMLBuilder();

        $result = $XMLBuilder->checkError($xml);

        preg_match('/<textarea.*>(.*)Original XML:/is', $result, $errorMessage);

        if (count($errorMessage) > 0) {
            $errorMessages = explode('Error', $errorMessage[1]);
            array_shift($errorMessages);
        } else {
            $errorMessages = array();
        }

        if (strpos($result, 'RxEntry.aspx')) {
            $this->errorLog($xml);
            $this->errorLog($result);

            if (!count($errorMessages)) {
                $errorMessages[] = xl('An undefined error occurred, please contact your systems administrator.');
            }
        } elseif ($XMLBuilder->getGlobals()->getDebugSetting() !== 0) {
            $debugString = '( '.xl('DEBUG OUTPUT').' )'.PHP_EOL;

            if ($XMLBuilder->getGlobals()->getDebugSetting() & self::DEBUG_XML) {
                $this->errorLog($debugString.$xml);
            }

            if ($XMLBuilder->getGlobals()->getDebugSetting() & self::DEBUG_RESULT) {
                $this->errorLog($debugString.$result);
            }
        }

        return $errorMessages;
    }

    public function updateClientData()
    {
        $XMLBuilder = $this->getXMLBuilder();
        $Store = $XMLBuilder->getStore();
        $page = $this->getDestination();
        $clientId = $this->getClientId();

        if ($page == 'compose') {
            $Store->updateClientImportStatusByClientId($clientId, 1);
        } elseif ($page == 'medentry') {
            $Store->updateClientImportStatusByClientId($clientId, 3);
        }

        $allergyIds = $XMLBuilder->getSentAllergyIds();
        if (count($allergyIds)) {
            foreach ($allergyIds as $allergyId) {
                $Store->updateAllergyUploadedByClientIdAllergyId(1, $clientId, $allergyId);
            }
        }

        $prescriptionIds = $XMLBuilder->getSentPrescriptionIds();
        if (count($prescriptionIds)) {
            foreach ($prescriptionIds as $prescriptionId) {
                $Store->updatePrescriptionsUploadActiveByClientIdPrescriptionId(1, 0, $clientId, $prescriptionId);
            }
        }

        $medicationIds = $XMLBuilder->getSentMedicationIds();
        if (count($medicationIds)) {
            foreach ($medicationIds as $medicationId) {
                $Store->updateErxUploadedByListId($medicationId, 1);
            }
        }
    }
}
