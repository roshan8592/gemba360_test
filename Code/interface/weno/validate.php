<?php
/**
 * weno rx validation.
 *
 * @package OpenEMR
 * @link    http://www.open-emr.org
 * @author  Sherwin Gaddis <sherwingaddis@gmail.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2016-2017 Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright Copyright (c) 2017-2018 Brady Miller <brady.g.miller@gmail.com>
 * @license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


require_once("../globals.php");
require_once("$srcdir/client.inc");

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Rx\Weno\TransmitData;

if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}

$facility = getFacilities($first = '');
$pid = $GLOBALS['pid'];
$uid = $_SESSION['authUserID'];

$validation = new TransmitData();

$client = $validation->validateClient($pid);
$pharmacy = $validation->clientPharmacyInfo($pid);

if (empty($facility[0]['name']) || $facility[0]['name'] == "Your clinic name here") {
    print xlt("Please fill out facility name properly");
    exit;
}

if (empty($facility[0]['phone'])) {
    print xlt("Please fill out facility phone properly");
    exit;
}

if (empty($facility[0]['fax'])) {
    print xlt("Please fill out facility fax properly");
    exit;
}

if (empty($facility[0]['street'])) {
    print xlt("Please fill out facility street properly");
    exit;
}

if (empty($facility[0]['city'])) {
    print xlt("Please fill out facility city properly");
    exit;
}

if (empty($facility[0]['state'])) {
    print xlt("Please fill out facility state properly");
    exit;
}

if (empty($facility[0]['postal_code'])) {
    print xlt("Please fill out facility postal code properly");
    exit;
}

if (empty($GLOBALS['weno_account_id'])) {
    print xlt("Weno Account ID information missing")."<br>";
    exit;
}
if (empty($GLOBALS['weno_provider_id'])) {
    print xlt("Weno Account Clinic ID information missing")."<br>";
    exit;
}
if (empty($client['DOB'])) {
    print xlt("Client DOB missing"). "<br>";
    exit;
}
if (empty($client['street'])) {
    print xlt("Client street missing"). "<br>";
    exit;
}
if (empty($client['postal_code'])) {
    print xlt("Client Zip Code missing"). "<br>";
    exit;
}
if (empty($client['city'])) {
    print xlt("Client city missing"). "<br>";
    exit;
}
if (empty($client['state'])) {
    print xlt("Client state missing"). "<br>";
    exit;
}
if (empty($client['sex'])) {
    print xlt("Client sex missing"). "<br>";
    exit;
}
if (empty($pharmacy['name'])) {
    print xlt("Pharmacy not assigned to the client"). "<br>";
    exit;
}
$ncpdpLength = strlen($pharmacy['ncpdp']);
if (empty($pharmacy['ncpdp']) || $ncpdpLength < 7) {
    print xlt("Pharmacy missing NCPDP ID or less than 7 digits"). "<br>";
    exit;
}
$npiLength = strlen($pharmacy['npi']);
if (empty($pharmacy['npi'] || $npiLength < 10)) {
    print xlt("Pharmacy missing NPI  or less than 10 digits"). "<br>";
    exit;
}
//validate NPI exist
//Test if the NPI is a valid number on file
$seekvalidation = $validation->validateNPI($pharmacy['npi']);
if ($seekvalidation == 0) {
    print xlt("Please use valid NPI");
    exit;
}
header('Location: confirm.php');
