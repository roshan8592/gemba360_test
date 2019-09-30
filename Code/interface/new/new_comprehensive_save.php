<?php
/**
 * new_comprehensive_save.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Rod Roark <rod@sunsetsystems.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2009-2017 Rod Roark <rod@sunsetsystems.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once("../globals.php");

use OpenEMR\Common\Csrf\CsrfUtils;

if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}

//Storing the image file into image location

// Validation for non-unique external client identifier.
$alertmsg = '';
if (!empty($_POST["form_pubpid"])) {
    $form_pubpid = trim($_POST["form_pubpid"]);
    $result = sqlQuery("SELECT count(*) AS count FROM client_data WHERE " .
    "pubpid = ?", array($form_pubpid));
    if ($result['count']) {
        // Error, not unique.
        $alertmsg = xl('Warning: Client ID is not unique!');
    }
}

require_once("$srcdir/pid.inc");
require_once("$srcdir/client.inc");
require_once("$srcdir/options.inc.php");

// here, we lock the client data table while we find the most recent max PID
// other interfaces can still read the data during this lock, however
// sqlStatement("lock tables client_data read");

$result = sqlQuery("SELECT MAX(pid)+1 AS pid FROM client_data");

$newpid = 1;

if ($result['pid'] > 1) {
    $newpid = $result['pid'];
}

setpid($newpid);

if (empty($pid)) {
  // sqlStatement("unlock tables");
    die("Internal error: setpid(" .text($newpid) . ") failed!");
}

// Update client_data and employer_data:
//
$newdata = array();
$newdata['client_data' ] = array();
$newdata['employer_data'] = array();
$fres = sqlStatement("SELECT * FROM layout_options " .
  "WHERE form_id = 'DEM' AND (uor > 0 OR field_id = 'pubpid') AND field_id != '' " .
  "ORDER BY group_id, seq");
while ($frow = sqlFetchArray($fres)) {
    $data_type = $frow['data_type'];
    $field_id  = $frow['field_id'];
  // $value     = '';
    $colname   = $field_id;
    $tblname   = 'client_data';
    if (strpos($field_id, 'em_') === 0) {
        $colname = substr($field_id, 3);
        $tblname = 'employer_data';
    }

  //get value only if field exist in $_POST (prevent deleting of field with disabled attribute)
    if (isset($_POST["form_$field_id"]) || $field_id == "pubpid") {
        $value = get_layout_form_value($frow);
        if ($field_id == 'pubpid' && empty($value)) {
            $value = $pid;
        }

        $newdata[$tblname][$colname] = $value;
    }
}

updateClientData($pid, $newdata['client_data'], true);
updateEmployerData($pid, $newdata['employer_data'], true);

if(!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {

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

//$i1dob = DateToYYYYMMDD(filter_input(INPUT_POST, "i1subscriber_DOB"));
//$i1date = DateToYYYYMMDD(filter_input(INPUT_POST, "i1effective_date"));

// sqlStatement("unlock tables");
// end table lock

//newHistoryData($pid);
/*newInsuranceData(
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
    filter_input(INPUT_POST, 'i1accept_assignment')
);*/


//$i2dob = DateToYYYYMMDD(filter_input(INPUT_POST, "i2subscriber_DOB"));
//$i2date = DateToYYYYMMDD(filter_input(INPUT_POST, "i2effective_date"));

/*newInsuranceData(
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
    filter_input(INPUT_POST, 'i2accept_assignment')
);*/

//$i3dob  = DateToYYYYMMDD(filter_input(INPUT_POST, "i3subscriber_DOB"));
//$i3date = DateToYYYYMMDD(filter_input(INPUT_POST, "i3effective_date"));

/*newInsuranceData(
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
    filter_input(INPUT_POST, 'i3accept_assignment')
);*/

?>
<html>
<body>
<script language="Javascript">
<?php
if ($alertmsg) {
    echo "alert(" . js_escape($alertmsg) . ");\n";
}

  echo "window.location='$rootdir/client_file/summary/demographics.php?" .
    "set_pid=" . attr_url($pid) . "&is_new=1';\n";
?>
</script>

</body>
</html>

