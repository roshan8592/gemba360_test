<?php
/**
 * interface/modules/zend_modules/module/Carecoordination/src/Carecoordination/Model/CcdTable.php
 *
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @author    Riju KP <rijukp@zhservices.com>
 * @copyright Copyright (c) 2014 Z&H Consultancy Services Private Limited <sam@zhservices.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */
namespace Carecoordination\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Model\ApplicationTable;
use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\XmlRpc\Generator;

use DOMDocument;
use DOMXpath;

use Document;
use CouchDB;

use Documents\Model\DocumentsTable;

class CcdTable extends AbstractTableGateway
{
    protected $ccd_data_array;

    /*
     *  Fetch the component values from the CCDA XML*
     *
     * @param   $components     Array of components
     */
    public function import($xml, $document_id)
    {
        $audit_master_approval_status        = $this->ccd_data_array['approval_status'] = 1;
        $this->ccd_data_array['ip_address']  = $_SERVER['REMOTE_ADDR'];
        $this->ccd_data_array['type']        = '13';

    //Client Details
        $this->ccd_data_array['field_name_value_array']['client_data'][1]['fname']        = $xml['recordTarget']['clientRole']['client']['name']['given'][0];
        $this->ccd_data_array['field_name_value_array']['client_data'][1]['mname']        = $xml['recordTarget']['clientRole']['client']['name']['given'][1];
        $this->ccd_data_array['field_name_value_array']['client_data'][1]['lname']        = $xml['recordTarget']['clientRole']['client']['name']['family'];
        $this->ccd_data_array['field_name_value_array']['client_data'][1]['DOB']          = $xml['recordTarget']['clientRole']['client']['birthTime']['value'];
        $this->ccd_data_array['field_name_value_array']['client_data'][1]['sex']          = $xml['recordTarget']['clientRole']['client']['administrativeGenderCode']['displayName'];
        $this->ccd_data_array['field_name_value_array']['client_data'][1]['street']       = $xml['recordTarget']['clientRole']['addr']['streetAddressLine'];
        $this->ccd_data_array['field_name_value_array']['client_data'][1]['city']         = $xml['recordTarget']['clientRole']['addr']['city'];
        $this->ccd_data_array['field_name_value_array']['client_data'][1]['state']        = $xml['recordTarget']['clientRole']['addr']['state'];
        $this->ccd_data_array['field_name_value_array']['client_data'][1]['postal_code']  = $xml['recordTarget']['clientRole']['addr']['postalCode'];
        $this->ccd_data_array['field_name_value_array']['client_data'][1]['phone_home']   = preg_replace('/[^0-9]+/i', '', $xml['recordTarget']['clientRole']['telecom']['value']);
        $this->ccd_data_array['field_name_value_array']['client_data'][1]['extension']    = $xml['recordTarget']['clientRole']['id']['extension'];


        // TODO: this should be created through DI
        $audit_master_id = \Application\Plugin\CommonPlugin::insert_ccr_into_audit_data($this->ccd_data_array);
        $this->update_document_table($document_id, $audit_master_id, $audit_master_approval_status);
    }

    public function update_document_table($document_id, $audit_master_id, $audit_master_approval_status)
    {
        $appTable   = new ApplicationTable();
        $query = "UPDATE documents 
              SET audit_master_id = ?,
                  imported = ?,
                  audit_master_approval_status=? 
              WHERE id = ?";
        $appTable->zQuery($query, array($audit_master_id,
                                    1,
                                    $audit_master_approval_status,
                                    $document_id));
    }
}
