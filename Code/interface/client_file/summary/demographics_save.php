<?php
/**
 * demographics_save.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once("../../globals.php");
require_once("$srcdir/client.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/options.inc.php");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Common\Logging\EventAuditLogger;

if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}

// Check authorization.
if ($pid) {
    if (!acl_check('clients', 'demo', '', 'write')) {
        die(xlt('Updating demographics is not authorized.'));
    }

    $tmp = getClientData($pid, "squad");
    if ($tmp['squad'] && ! acl_check('squads', $tmp['squad'])) {
        die(xlt('You are not authorized to access this squad.'));
    }
} else {
    if (!acl_check('clients', 'demo', '', array('write','addonly'))) {
        die(xlt('Adding demographics is not authorized.'));
    }
}

foreach ($_POST as $key => $val) {
    if ($val == "MM/DD/YYYY") {
        $_POST[$key] = "";
    }
}

// Update client_data and employer_data:
//
$newdata = array();
$newdata['client_data']['id'] = $_POST['db_id'];
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'DEM' AND uor > 0 AND field_id != '' " .
  "ORDER BY group_id, seq");
while ($frow = sqlFetchArray($fres)) {
    $data_type = $frow['data_type'];
    $field_id = $frow['field_id'];
    // $value  = '';
    $colname = $field_id;
    $table = 'client_data';
    if (strpos($field_id, 'em_') === 0) {
        $colname = substr($field_id, 3);
        $table = 'employer_data';
    }

    //get value only if field exist in $_POST (prevent deleting of field with disabled attribute)
    if (isset($_POST["form_$field_id"])) {
        $newdata[$table][$colname] = get_layout_form_value($frow);
    }
}

updateClientData($pid, $newdata['client_data']);
updateEmployerData($pid, $newdata['employer_data']);

//function to delete the row from specific table
function row_delete($table, $where)
{
    $tres = sqlStatement("SELECT * FROM " . escape_table_name($table) . " WHERE $where");
    $count = 0;
    while ($trow = sqlFetchArray($tres)) {
        $logstring = "";
        foreach ($trow as $key => $value) {
            if (! $value || $value == '0000-00-00 00:00:00') {
                continue;
            }

            if ($logstring) {
                $logstring .= " ";
            }

            $logstring .= $key . "= '" . $value . "' ";
        }

        EventAuditLogger::instance()->newEvent("delete", $_SESSION['authUser'], $_SESSION['authProvider'], 1, "$table: $logstring");
        ++$count;
    }

    if ($count) {
        $query = "DELETE FROM " . escape_table_name($table) . " WHERE $where";
        if (!$GLOBALS['sql_string_no_show_screen']) {
            //echo text($query) . "<br>\n";
        }

        sqlStatement($query);
    }
}

//function to delete the document file from document directory as well as delete the entry from documents and categories_to_document table for the correspondig client
function delete_document($document)
{
    
    $trow = sqlQuery("SELECT url, thumb_url, storagemethod, couch_docid, couch_revid FROM documents WHERE id = ?", array($document));
    $url = $trow['url'];
    $thumb_url = $trow['thumb_url'];
    row_delete("categories_to_documents", "document_id = '" . add_escape_custom($document) . "'");
    row_delete("documents", "id = '" . add_escape_custom($document) . "'");
    row_delete("gprelations", "type1 = 1 AND id1 = '" . add_escape_custom($document) . "'");

    switch ((int)$trow['storagemethod']) {
        //for hard disk store
        case 0:
            @unlink(substr($url, 7));

            if (!is_null($thumb_url)) {
                @unlink(substr($thumb_url, 7));
            }
            break;
        //for CouchDB store
        case 1:
            $couchDB = new CouchDB();
            $couchDB->DeleteDoc($GLOBALS['couchdb_dbase'], $trow['couch_docid'], $trow['couch_revid']);
            break;
    }
}

if($_FILES["file"]["name"][0] != "" || !empty($_FILES["additionalBlogImageFile"]["file"][0])) {

    //before adding new file, first delete the file uploaded previously from documents and categories_to_documents tables and also remove the file from folder then add new file
    if($_POST["document_id"]){
        delete_document($_POST["document_id"]);
    }

    $_POST["client_id"] = $pid;

    $_POST["request_check"] = "client";

    $fileExtension = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION);

    //renaming uploaded file name with client's first name middle name and client ID
    $_POST["destination"] = $_POST["form_fname"]."_".$_POST["form_lname"]."_".$pid.".".$fileExtension;

    //creating array of the inputs to be passed as parameter to act method of Controller.class class
    $input_array = array("document"=>"","upload"=>"","client_id"=>$pid,"parent_id"=>"10");

    $controller = new Controller();

    $controller->act($input_array);

}


/*$i1dob = DateToYYYYMMDD(filter_input(INPUT_POST, "i1subscriber_DOB"));
$i1date = DateToYYYYMMDD(filter_input(INPUT_POST, "i1effective_date"));

newInsuranceData(
    $pid,
    "primary",
    filter_input(INPUT_POST, "i1provider"),
    filter_input(INPUT_POST, "i1policy_number"),
    filter_input(INPUT_POST, "i1group_number"),
    filter_input(INPUT_POST, "i1plan_name"),
    filter_input(INPUT_POST, "i1subscriber_lname"),
    filter_input(INPUT_POST, "i1subscriber_mname"),
    filter_input(INPUT_POST, "i1subscriber_fname"),
    filter_input(INPUT_POST, "form_i1subscriber_relationship"),
    filter_input(INPUT_POST, "i1subscriber_ss"),
    $i1dob,
    filter_input(INPUT_POST, "i1subscriber_street"),
    filter_input(INPUT_POST, "i1subscriber_postal_code"),
    filter_input(INPUT_POST, "i1subscriber_city"),
    filter_input(INPUT_POST, "form_i1subscriber_state"),
    filter_input(INPUT_POST, "form_i1subscriber_country"),
    filter_input(INPUT_POST, "i1subscriber_phone"),
    filter_input(INPUT_POST, "i1subscriber_employer"),
    filter_input(INPUT_POST, "i1subscriber_employer_street"),
    filter_input(INPUT_POST, "i1subscriber_employer_city"),
    filter_input(INPUT_POST, "i1subscriber_employer_postal_code"),
    filter_input(INPUT_POST, "form_i1subscriber_employer_state"),
    filter_input(INPUT_POST, "form_i1subscriber_employer_country"),
    filter_input(INPUT_POST, 'i1copay'),
    filter_input(INPUT_POST, 'form_i1subscriber_sex'),
    $i1date,
    filter_input(INPUT_POST, 'i1accept_assignment'),
    filter_input(INPUT_POST, 'i1policy_type')
);

$i2dob = DateToYYYYMMDD(filter_input(INPUT_POST, "i2subscriber_DOB"));
$i2date = DateToYYYYMMDD(filter_input(INPUT_POST, "i2effective_date"));


newInsuranceData(
    $pid,
    "secondary",
    filter_input(INPUT_POST, "i2provider"),
    filter_input(INPUT_POST, "i2policy_number"),
    filter_input(INPUT_POST, "i2group_number"),
    filter_input(INPUT_POST, "i2plan_name"),
    filter_input(INPUT_POST, "i2subscriber_lname"),
    filter_input(INPUT_POST, "i2subscriber_mname"),
    filter_input(INPUT_POST, "i2subscriber_fname"),
    filter_input(INPUT_POST, "form_i2subscriber_relationship"),
    filter_input(INPUT_POST, "i2subscriber_ss"),
    $i2dob,
    filter_input(INPUT_POST, "i2subscriber_street"),
    filter_input(INPUT_POST, "i2subscriber_postal_code"),
    filter_input(INPUT_POST, "i2subscriber_city"),
    filter_input(INPUT_POST, "form_i2subscriber_state"),
    filter_input(INPUT_POST, "form_i2subscriber_country"),
    filter_input(INPUT_POST, "i2subscriber_phone"),
    filter_input(INPUT_POST, "i2subscriber_employer"),
    filter_input(INPUT_POST, "i2subscriber_employer_street"),
    filter_input(INPUT_POST, "i2subscriber_employer_city"),
    filter_input(INPUT_POST, "i2subscriber_employer_postal_code"),
    filter_input(INPUT_POST, "form_i2subscriber_employer_state"),
    filter_input(INPUT_POST, "form_i2subscriber_employer_country"),
    filter_input(INPUT_POST, 'i2copay'),
    filter_input(INPUT_POST, 'form_i2subscriber_sex'),
    $i2date,
    filter_input(INPUT_POST, 'i2accept_assignment'),
    filter_input(INPUT_POST, 'i2policy_type')
);

$i3dob  = DateToYYYYMMDD(filter_input(INPUT_POST, "i3subscriber_DOB"));
$i3date = DateToYYYYMMDD(filter_input(INPUT_POST, "i3effective_date"));

newInsuranceData(
    $pid,
    "tertiary",
    filter_input(INPUT_POST, "i3provider"),
    filter_input(INPUT_POST, "i3policy_number"),
    filter_input(INPUT_POST, "i3group_number"),
    filter_input(INPUT_POST, "i3plan_name"),
    filter_input(INPUT_POST, "i3subscriber_lname"),
    filter_input(INPUT_POST, "i3subscriber_mname"),
    filter_input(INPUT_POST, "i3subscriber_fname"),
    filter_input(INPUT_POST, "form_i3subscriber_relationship"),
    filter_input(INPUT_POST, "i3subscriber_ss"),
    $i3dob,
    filter_input(INPUT_POST, "i3subscriber_street"),
    filter_input(INPUT_POST, "i3subscriber_postal_code"),
    filter_input(INPUT_POST, "i3subscriber_city"),
    filter_input(INPUT_POST, "form_i3subscriber_state"),
    filter_input(INPUT_POST, "form_i3subscriber_country"),
    filter_input(INPUT_POST, "i3subscriber_phone"),
    filter_input(INPUT_POST, "i3subscriber_employer"),
    filter_input(INPUT_POST, "i3subscriber_employer_street"),
    filter_input(INPUT_POST, "i3subscriber_employer_city"),
    filter_input(INPUT_POST, "i3subscriber_employer_postal_code"),
    filter_input(INPUT_POST, "form_i3subscriber_employer_state"),
    filter_input(INPUT_POST, "form_i3subscriber_employer_country"),
    filter_input(INPUT_POST, 'i3copay'),
    filter_input(INPUT_POST, 'form_i3subscriber_sex'),
    $i3date,
    filter_input(INPUT_POST, 'i3accept_assignment'),
    filter_input(INPUT_POST, 'i3policy_type')
);
*/
 include_once("demographics.php");
