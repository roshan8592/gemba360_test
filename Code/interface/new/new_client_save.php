<?php
/**
 * new_client_save.php
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2018 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once("../globals.php");

use OpenEMR\Common\Csrf\CsrfUtils;

if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}

// Validation for non-unique external client identifier.
if (!empty($_POST["pubpid"])) {
    $form_pubpid = trim($_POST["pubpid"]);
    $result = sqlQuery("SELECT count(*) AS count FROM client_data WHERE " .
    "pubpid = ?", array($form_pubpid));
    if ($result['count']) {
        // Error, not unique.
        require_once("new.php");
        exit();
    }
}

require_once("$srcdir/pid.inc");
require_once("$srcdir/client.inc");

//here, we lock the client data table while we find the most recent max PID
//other interfaces can still read the data during this lock, however
sqlStatement("lock tables client_data read");

$result = sqlQuery("select max(pid)+1 as pid from client_data");

// TBD: This looks wrong to unlock the table before we have added our
// client with its newly allocated pid!
//
sqlStatement("unlock tables");
//end table lock
$newpid = 1;

if ($result['pid'] > 1) {
    $newpid = $result['pid'];
}

setpid($newpid);

if ($pid == null) {
    $pid = 0;
}

// what do we set for the public pid?
if (isset($_POST["pubpid"]) && ($_POST["pubpid"] != "")) {
    $mypubpid = $_POST["pubpid"];
} else {
    $mypubpid = $pid;
}

if ($_POST['form_create']) {
    $form_fname = ucwords(trim($_POST["fname"]));
    $form_lname = ucwords(trim($_POST["lname"]));
    $form_mname = ucwords(trim($_POST["mname"]));

  // ===================
  // DBC SYSTEM WAS REMOVED
    $form_sex               = trim($_POST["sex"]) ;
    $form_dob               = DateToYYYYMMDD(trim($_POST["DOB"])) ;
    $form_street            = '' ;
    $form_city              = '' ;
    $form_postcode          = '' ;
    $form_countrycode       = '' ;
    //$form_regdate           = DateToYYYYMMDD(trim($_POST['regdate']));
    $form_regdate           = '' ;
    $form_disability        = trim($_POST["disabilities"]);
    $form_state             = trim($_POST["state"]);
    $contact_relationship   = trim($_POST["contact_relationship"]);
    $phone_contact          = trim($_POST["phone_contact"]);
    $phone_biz              = trim($_POST["phone_biz"]);
    $phone_cell             = trim($_POST["phone_cell"]);
    $email_direct           = trim($_POST["email_direct"]);
    $comm_assistance        = trim($_POST["comm_assistance"]);
    $client_identify        = trim($_POST["client_identify"]);
    $client_identify        = trim($_POST["client_identify"]);
    $birth_country          = trim($_POST["birth_country"]);
    $cultural_support       = trim($_POST["cultural_support"]);
    $primary_issue          = trim($_POST["primary_issue"]);
    $physical_danger        = trim($_POST["physical_danger"]);
    $physical_danger_yes    = trim($_POST["physical_danger_yes"]);
    $full_name              = trim($_POST["full_name"]);
    $phone_number           = trim($_POST["phone_number"]);
    $email_address          = trim($_POST["email_address"]);
    $relation_to_person     = trim($_POST["relation_to_person"]);
    $consent                = trim($_POST["consent"]);
    $background             = trim($_POST["background"]);
    $advocacy_issue         = trim($_POST["advocacy_issue"]);
    $problem_solution       = trim($_POST["problem_solution"]);
    $solve_problem_details  = trim($_POST["solve_problem_details"]);
    $important_dates        = trim($_POST["important_dates"]);
    $potential_risk         = trim($_POST["potential_risk"]);
    $choice_mentor          = trim($_POST["choice_mentor"]);
    $valid_advocacy         = trim($_POST["valid_advocacy"]);
  // EOS DBC
  // ===================

    newClientData(
        $_POST["db_id"],
        $_POST["title"],
        $form_fname,
        $form_lname,
        $form_mname,
        $form_sex, // sex
        $form_dob, // dob
        $form_street, // street
        $form_postcode, // postal_code
        $form_city, // city
        $form_state, // state
        $form_countrycode, // country_code
        "", // ss
        "", // occupation
        "", // phone_home
        $phone_biz, // phone_biz
        $phone_contact, // phone_contact
        "", // status
        $contact_relationship, // contact_relationship
        "", // referrer
        "", // referrerID
        "", // email
        "", // language
        "", // ethnoracial
        "", // interpreter
        "", // migrantseasonal
        "", // family_size
        "", // monthly_income
        "", // homeless
        "", // financial_review
        "$mypubpid",
        $pid,
        "", // providerID
        "", // genericname1
        "", // genericval1
        "", // genericname2
        "", // genericval2
        "", //billing_note
        $phone_cell, // phone_cell
        "", // hipaa_mail
        "", // hipaa_voice
        0,  // squad
        0,  // $pharmacy_id = 0,
        "", // $drivers_license = "",
        "", // $hipaa_notice = "",
        "", // $hipaa_message = "",
        $form_regdate,
        $form_disability,
        $email_direct,
        $comm_assistance,
        $client_identify,
        $birth_country,
        $cultural_support,
        $primary_issue,
        $physical_danger,
        $physical_danger_yes,
        $full_name,
        $phone_number,
        $email_address,
        $relation_to_person,
        $consent,
        $background,
        $advocacy_issue,
        $problem_solution,
        $solve_problem_details,
        $important_dates,
        $potential_risk,
        $choice_mentor,
        $valid_advocacy
    );

    newEmployerData($pid);
    newHistoryData($pid);
    //newInsuranceData($pid, "primary");
    //newInsuranceData($pid, "secondary");
    //newInsuranceData($pid, "tertiary");

  // Set referral source separately because we don't want it messed
  // with later by newClientData().
    if ($refsource = trim($_POST["refsource"])) {
        sqlQuery("UPDATE client_data SET referral_source = ? " .
        "WHERE pid = ?", array($refsource, $pid));
    }
}
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
