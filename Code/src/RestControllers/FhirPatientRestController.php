<?php
/**
 * FhirClientRestController
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Jerry Padgett <sjpadgett@gmail.com>
 * @copyright Copyright (c) 2018 Jerry Padgett <sjpadgett@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


namespace OpenEMR\RestControllers;

use OpenEMR\Services\ClientService;
use OpenEMR\Services\FhirResourcesService;
use OpenEMR\RestControllers\RestControllerHelper;
use OpenEMR\FHIR\R4\FHIRResource\FHIRBundle\FHIRBundleEntry;

class FhirClientRestController
{
    private $clientService;
    private $fhirService;

    public function __construct($pid)
    {
        $this->clientService = new ClientService();
        $this->clientService->setPid($pid);
        $this->fhirService = new FhirResourcesService();
    }

    // implement put post in future

    public function getOne()
    {
        $oept = $this->clientService->getOne();
        $pid = 'client-' . $this->clientService->getPid();
        $clientResource = $this->fhirService->createClientResource($pid, $oept, false);

        return RestControllerHelper::responseHandler($clientResource, null, 200);
    }

    public function getAll($search)
    {
        $resourceURL = \RestConfig::$REST_FULL_URL;
        if (strpos($resourceURL, '?') > 0) {
            $resourceURL = strstr($resourceURL, '?', true);
        }

        $searchParam = array(
            'name' => $search['name'],
            'dob' => $search['birthdate']
        );

        $searchResult = $this->clientService->getAll($searchParam);
        if ($searchResult === false) {
            http_response_code(404);
            exit;
        }
        $entries = array();
        foreach ($searchResult as $oept) {
            $entryResource = $this->fhirService->createClientResource($oept['pid'], $oept, false);
            $entry = array(
                'fullUrl' => $resourceURL . "/" . $oept['pid'],
                'resource' => $entryResource
            );
            $entries[] = new FHIRBundleEntry($entry);
        }
        $searchResult = $this->fhirService->createBundle('Client', $entries, false);
        return RestControllerHelper::responseHandler($searchResult, null, 200);
    }
}
