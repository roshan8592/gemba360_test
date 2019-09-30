<?php
/**
 * Routes
 * (All REST routes)
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Matthew Vita <matthewvita48@gmail.com>
 * @author    Jerry Padgett <sjpadgett@gmail.com>
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2018 Matthew Vita <matthewvita48@gmail.com>
 * @copyright Copyright (c) 2018 Jerry Padgett <sjpadgett@gmail.com>
 * @copyright Copyright (c) 2019 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

// Lets keep our controller classes with the routes.
//
use OpenEMR\RestControllers\FacilityRestController;
use OpenEMR\RestControllers\VersionRestController;
use OpenEMR\RestControllers\ProductRegistrationRestController;
use OpenEMR\RestControllers\ClientRestController;
use OpenEMR\RestControllers\EncounterRestController;
use OpenEMR\RestControllers\ProviderRestController;
use OpenEMR\RestControllers\ListRestController;
use OpenEMR\RestControllers\InsuranceCompanyRestController;
use OpenEMR\RestControllers\AppointmentRestController;
use OpenEMR\RestControllers\AuthRestController;
use OpenEMR\RestControllers\ONoteRestController;
use OpenEMR\RestControllers\DocumentRestController;
use OpenEMR\RestControllers\InsuranceRestController;
use OpenEMR\RestControllers\MessageRestController;


// Note some Http clients may not send auth as json so a function
// is implemented to determine and parse encoding on auth route's.
//
RestConfig::$ROUTE_MAP = array(
    "POST /api/auth" => function () {
        $data = (array) RestConfig::getPostData((file_get_contents("php://input")));
        return (new AuthRestController())->authenticate($data);
    },
    "GET /api/facility" => function () {
        RestConfig::authorization_check("admin", "users");
        return (new FacilityRestController())->getAll();
    },
    "GET /api/facility/:fid" => function ($fid) {
        RestConfig::authorization_check("admin", "users");
        return (new FacilityRestController())->getOne($fid);
    },
    "POST /api/facility" => function () {
        RestConfig::authorization_check("admin", "super");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new FacilityRestController())->post($data);
    },
    "PUT /api/facility/:fid" => function ($fid) {
        RestConfig::authorization_check("admin", "super");
        $data = (array)(json_decode(file_get_contents("php://input")));
        $data["fid"] = $fid;
        return (new FacilityRestController())->put($data);
    },
    "GET /api/provider" => function () {
        RestConfig::authorization_check("admin", "users");
        return (new ProviderRestController())->getAll();
    },
    "GET /api/provider/:prid" => function ($prid) {
        RestConfig::authorization_check("admin", "users");
        return (new ProviderRestController())->getOne($prid);
    },
    "GET /api/client" => function () {
        RestConfig::authorization_check("clients", "demo");
        return (new ClientRestController(null))->getAll($_GET);
    },
    "POST /api/client" => function () {
        RestConfig::authorization_check("clients", "demo");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ClientRestController(null))->post($data);
    },
    "PUT /api/client/:pid" => function ($pid) {
        RestConfig::authorization_check("clients", "demo");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ClientRestController(null))->put($pid, $data);
    },
    "GET /api/client/:pid" => function ($pid) {
        RestConfig::authorization_check("clients", "demo");
        return (new ClientRestController($pid))->getOne();
    },
    "GET /api/client/:pid/encounter" => function ($pid) {
        RestConfig::authorization_check("encounters", "auth_a");
        return (new EncounterRestController())->getAll($pid);
    },
    "GET /api/client/:pid/encounter/:eid" => function ($pid, $eid) {
        RestConfig::authorization_check("encounters", "auth_a");
        return (new EncounterRestController())->getOne($pid, $eid);
    },
    "GET /api/client/:pid/encounter/:eid/soap_note" => function ($pid, $eid) {
        RestConfig::authorization_check("encounters", "notes");
        return (new EncounterRestController())->getSoapNotes($pid, $eid);
    },
    "POST /api/client/:pid/encounter/:eid/vital" => function ($pid, $eid) {
        RestConfig::authorization_check("encounters", "notes");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new EncounterRestController())->postVital($pid, $eid, $data);
    },
    "PUT /api/client/:pid/encounter/:eid/vital/:vid" => function ($pid, $eid, $vid) {
        RestConfig::authorization_check("encounters", "notes");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new EncounterRestController())->putVital($pid, $eid, $vid, $data);
    },
    "GET /api/client/:pid/encounter/:eid/vital" => function ($pid, $eid) {
        RestConfig::authorization_check("encounters", "notes");
        return (new EncounterRestController())->getVitals($pid, $eid);
    },
    "GET /api/client/:pid/encounter/:eid/vital/:vid" => function ($pid, $eid, $vid) {
        RestConfig::authorization_check("encounters", "notes");
        return (new EncounterRestController())->getVital($pid, $eid, $vid);
    },
    "GET /api/client/:pid/encounter/:eid/soap_note/:sid" => function ($pid, $eid, $sid) {
        RestConfig::authorization_check("encounters", "notes");
        return (new EncounterRestController())->getSoapNote($pid, $eid, $sid);
    },
    "POST /api/client/:pid/encounter/:eid/soap_note" => function ($pid, $eid) {
        RestConfig::authorization_check("encounters", "notes");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new EncounterRestController())->postSoapNote($pid, $eid, $data);
    },
    "PUT /api/client/:pid/encounter/:eid/soap_note/:sid" => function ($pid, $eid, $sid) {
        RestConfig::authorization_check("encounters", "notes");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new EncounterRestController())->putSoapNote($pid, $eid, $sid, $data);
    },
    "GET /api/client/:pid/medical_problem" => function ($pid) {
        RestConfig::authorization_check("encounters", "notes");
        return (new ListRestController())->getAll($pid, "medical_problem");
    },
    "GET /api/client/:pid/medical_problem/:mid" => function ($pid, $mid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->getOne($pid, "medical_problem", $mid);
    },
    "POST /api/client/:pid/medical_problem" => function ($pid) {
        RestConfig::authorization_check("clients", "med");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ListRestController())->post($pid, "medical_problem", $data);
    },
    "PUT /api/client/:pid/medical_problem/:mid" => function ($pid, $mid) {
        RestConfig::authorization_check("clients", "med");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ListRestController())->put($pid, $mid, "medical_problem", $data);
    },
    "DELETE /api/client/:pid/medical_problem/:mid" => function ($pid, $mid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->delete($pid, $mid, "medical_problem");
    },
    "GET /api/client/:pid/allergy" => function ($pid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->getAll($pid, "allergy");
    },
    "GET /api/client/:pid/allergy/:aid" => function ($pid, $aid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->getOne($pid, "allergy", $aid);
    },
    "DELETE /api/client/:pid/allergy/:aid" => function ($pid, $aid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->delete($pid, $aid, "allergy");
    },
    "POST /api/client/:pid/allergy" => function ($pid) {
        RestConfig::authorization_check("clients", "med");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ListRestController())->post($pid, "allergy", $data);
    },
    "PUT /api/client/:pid/allergy/:aid" => function ($pid, $aid) {
        RestConfig::authorization_check("clients", "med");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ListRestController())->put($pid, $aid, "allergy", $data);
    },
    "GET /api/client/:pid/medication" => function ($pid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->getAll($pid, "medication");
    },
    "POST /api/client/:pid/medication" => function ($pid) {
        RestConfig::authorization_check("clients", "med");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ListRestController())->post($pid, "medication", $data);
    },
    "PUT /api/client/:pid/medication/:mid" => function ($pid, $mid) {
        RestConfig::authorization_check("clients", "med");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ListRestController())->put($pid, $mid, "medication", $data);
    },
    "GET /api/client/:pid/medication/:mid" => function ($pid, $mid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->getOne($pid, "medication", $mid);
    },
    "DELETE /api/client/:pid/medication/:mid" => function ($pid, $mid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->delete($pid, $mid, "medication");
    },
    "GET /api/client/:pid/surgery" => function ($pid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->getAll($pid, "surgery");
    },
    "GET /api/client/:pid/surgery/:sid" => function ($pid, $sid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->getOne($pid, "surgery", $sid);
    },
    "DELETE /api/client/:pid/surgery/:sid" => function ($pid, $sid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->delete($pid, $sid, "surgery");
    },
    "POST /api/client/:pid/surgery" => function ($pid) {
        RestConfig::authorization_check("clients", "med");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ListRestController())->post($pid, "surgery", $data);
    },
    "PUT /api/client/:pid/surgery/:sid" => function ($pid, $sid) {
        RestConfig::authorization_check("clients", "med");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ListRestController())->put($pid, $sid, "surgery", $data);
    },
    "GET /api/client/:pid/dental_issue" => function ($pid) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->getAll($pid, "dental");
    },
    "GET /api/client/:pid/dental_issue/:did" => function ($pid, $did) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->getOne($pid, "dental", $did);
    },
    "DELETE /api/client/:pid/dental_issue/:did" => function ($pid, $did) {
        RestConfig::authorization_check("clients", "med");
        return (new ListRestController())->delete($pid, $did, "dental");
    },
    "POST /api/client/:pid/dental_issue" => function ($pid) {
        RestConfig::authorization_check("clients", "med");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ListRestController())->post($pid, "dental", $data);
    },
    "PUT /api/client/:pid/dental_issue/:did" => function ($pid, $did) {
        RestConfig::authorization_check("clients", "med");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new ListRestController())->put($pid, $did, "dental", $data);
    },
    "GET /api/client/:pid/appointment" => function ($pid) {
        RestConfig::authorization_check("clients", "appt");
        return (new AppointmentRestController())->getAllForClient($pid);
    },
    "POST /api/client/:pid/appointment" => function ($pid) {
        RestConfig::authorization_check("clients", "appt");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new AppointmentRestController())->post($pid, $data);
    },
    "GET /api/appointment" => function () {
        RestConfig::authorization_check("clients", "appt");
        return (new AppointmentRestController())->getAll();
    },
    "GET /api/appointment/:eid" => function ($eid) {
        RestConfig::authorization_check("clients", "appt");
        return (new AppointmentRestController())->getOne($eid);
    },
    "DELETE /api/client/:pid/appointment/:eid" => function ($pid, $eid) {
        RestConfig::authorization_check("clients", "appt");
        return (new AppointmentRestController())->delete($eid);
    },
    "GET /api/client/:pid/appointment/:eid" => function ($pid, $eid) {
        RestConfig::authorization_check("clients", "appt");
        return (new AppointmentRestController())->getOne($eid);
    },
    "GET /api/list/:list_name" => function ($list_name) {
        RestConfig::authorization_check("lists", "default");
        return (new ListRestController())->getOptions($list_name);
    },
    "GET /api/version" => function () {
        return (new VersionRestController())->getOne();
    },
    "GET /api/product" => function () {
        return (new ProductRegistrationRestController())->getOne();
    },
    "GET /api/insurance_company" => function () {
        return (new InsuranceCompanyRestController())->getAll();
    },
    "GET /api/insurance_type" => function () {
        return (new InsuranceCompanyRestController())->getInsuranceTypes();
    },
    "POST /api/insurance_company" => function () {
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new InsuranceCompanyRestController())->post($data);
    },
    "PUT /api/insurance_company/:iid" => function ($iid) {
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new InsuranceCompanyRestController())->put($iid, $data);
    },
    "POST /api/client/:pid/document" => function ($pid) {
        return (new DocumentRestController())->postWithPath($pid, $_GET['path'], $_FILES['document']);
    },
    "GET /api/client/:pid/document" => function ($pid) {
        return (new DocumentRestController())->getAllAtPath($pid, $_GET['path']);
    },
    "GET /api/client/:pid/document/:did" => function ($pid, $did) {
        return (new DocumentRestController())->downloadFile($pid, $did);
    },
    "GET /api/client/:pid/insurance" => function ($pid) {
        return (new InsuranceRestController())->getAll($pid);
    },
    "GET /api/client/:pid/insurance/:type" => function ($pid, $type) {
        return (new InsuranceRestController())->getOne($pid, $type);
    },
    "POST /api/client/:pid/insurance/:type" => function ($pid, $type) {
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new InsuranceRestController())->post($pid, $type, $data);
    },
    "PUT /api/client/:pid/insurance/:type" => function ($pid, $type) {
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new InsuranceRestController())->put($pid, $type, $data);
    },
    "POST /api/client/:pid/message" => function ($pid) {
        RestConfig::authorization_check("clients", "notes");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new MessageRestController())->post($pid, $data);
    },
    "PUT /api/client/:pid/message/:mid" => function ($pid, $mid) {
        RestConfig::authorization_check("clients", "notes");
        $data = (array)(json_decode(file_get_contents("php://input")));
        return (new MessageRestController())->put($pid, $mid, $data);
    },
    "DELETE /api/client/:pid/message/:mid" => function ($pid, $mid) {
        RestConfig::authorization_check("clients", "notes");
        return (new MessageRestController())->delete($pid, $mid);
    },

);

use OpenEMR\RestControllers\FhirClientRestController;
use OpenEMR\RestControllers\FhirEncounterRestController;

RestConfig::$FHIR_ROUTE_MAP = array(
    "POST /fhir/auth" => function () {
        $data = (array) RestConfig::getPostData((file_get_contents("php://input")));
        return (new AuthRestController())->authenticate($data);
    },
    "GET /fhir/Client" => function () {
        RestConfig::authorization_check("clients", "demo");
        return (new FhirClientRestController(null))->getAll($_GET);
    },
    "GET /fhir/Client/:pid" => function ($pid) {
        RestConfig::authorization_check("clients", "demo");
        return (new FhirClientRestController($pid))->getOne();
    },
    "GET /fhir/Encounter" => function () {
        RestConfig::authorization_check("encounters", "auth_a");
        return (new FhirEncounterRestController(null))->getAll($_GET);
    },
    "GET /fhir/Encounter/:eid" => function ($eid) {
        RestConfig::authorization_check("encounters", "auth_a");
        return (new FhirEncounterRestController())->getOne($eid);
    },
);
