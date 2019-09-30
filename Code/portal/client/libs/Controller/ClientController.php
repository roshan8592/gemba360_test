<?php
/**
 * ClientController.php
 *
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @author    Jerry Padgett <sjpadgett@gmail.com>
 * @copyright Copyright (c) 2016-2017 Jerry Padgett <sjpadgett@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

/**
 * import supporting libraries
 */
require_once("AppBaseController.php");
require_once("Model/Client.php");

/**
 * ClientController is the controller class for the Client object.
 * The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 *
 * @package Client Portal::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class ClientController extends AppBaseController
{

    /**
     * Override here for any controller-specific functionality
     *
     * @inheritdocs
     */
    protected function Init()
    {
        parent::Init();
        // require_once ( '../lib/appsql.class.php' );

        // $this->RequirePermission(SecureApp::$PERMISSION_USER,'SecureApp.LoginForm');
    }

    /**
     * Displays a list view of Client objects
     */
    public function ListView()
    {

        $rid = $pid = $user = $encounter = $register = 0;

        if (isset($_GET['id'])) {
            $rid = ( int ) $_GET['id'];
        }

        if (isset($_GET['pid'])) {
            $pid = ( int ) $_GET['pid'];
        }

        if (isset($_GET['user'])) {
            $user = $_GET['user'];
        }

        if (isset($_GET['enc'])) {
            $encounter = $_GET['enc'];
        }

        if (isset($_GET['register'])) {
            $register = $_GET['register'];
        }

        $this->Assign('recid', $rid);
        $this->Assign('cpid', $pid);
        $this->Assign('cuser', $user);
        $this->Assign('encounter', $encounter);
        $this->Assign('register', $register);
        $trow = array();
        $ptdata = $this->startupQuery($pid);
        foreach ($ptdata[0] as $key => $v) {
            $trow[lcfirst($key)] = $v;
        }

        $this->Assign('trow', $trow);
        $this->Render();
    }
    /**
     * API Method queries for startup Client records and return as php
     */
    public function startupQuery($pid)
    {
        try {
            $criteria = new ClientCriteria();
            $recnum = ( int ) $pid;
            $criteria->Pid_Equals = $recnum;

            $output = new stdClass();
            // return row
            $clientdata = $this->Phreezer->Query('ClientReporter', $criteria);
            $output->rows = $clientdata->ToObjectArray(false, $this->SimpleObjectParams());
            $output->totalResults = count($output->rows);
            return $output->rows;
        } catch (Exception $ex) {
            $this->RenderExceptionJSON($ex);
        }
    }
    /**
     * API Method queries for Client records and render as JSON
     */
    public function Query()
    {
        try {
            $criteria = new ClientCriteria();
            $pid = RequestUtil::Get('clientId');
            $criteria->Pid_Equals = $pid;

            $output = new stdClass();

            // if a sort order was specified then specify in the criteria
            $output->orderBy = RequestUtil::Get('orderBy');
            $output->orderDesc = RequestUtil::Get('orderDesc') != '';
            if ($output->orderBy) {
                $criteria->SetOrder($output->orderBy, $output->orderDesc);
            }

            $page = RequestUtil::Get('page');

            // return all results
            $clientdata = $this->Phreezer->Query('Client', $criteria);
            $output->rows = $clientdata->ToObjectArray(true, $this->SimpleObjectParams());
            $output->totalResults = count($output->rows);
            $output->totalPages = 1;
            $output->pageSize = $output->totalResults;
            $output->currentPage = 1;

            $this->RenderJSON($output, $this->JSONPCallback());
        } catch (Exception $ex) {
            $this->RenderExceptionJSON($ex);
        }
    }

    /**
     * API Method retrieves a single Client record and render as JSON
     */
    public function Read()
    {
        try {
            $pk = $this->GetRouter()->GetUrlParam('id');
            $client = $this->Phreezer->Get('Client', $pk);
            $this->RenderJSON($client, $this->JSONPCallback(), true, $this->SimpleObjectParams());
        } catch (Exception $ex) {
            $this->RenderExceptionJSON($ex);
        }
    }

    /**
     * API Method inserts a new Client record and render response as JSON
     */
    public function Create()
    {
        try {
            $json = json_decode(RequestUtil::GetBody());

            if (! $json) {
                throw new Exception('The request body does not contain valid JSON');
            }

            $client = new Client($this->Phreezer);

            // this is an auto-increment. uncomment if updating is allowed
            // $client->Id = $this->SafeGetVal($json, 'id');

            $client->Title = $this->SafeGetVal($json, 'title', $client->Title);
            $client->Language = $this->SafeGetVal($json, 'language', $client->Language);
            $client->Financial = $this->SafeGetVal($json, 'financial', $client->Financial);
            $client->Fname = $this->SafeGetVal($json, 'fname', $client->Fname);
            $client->Lname = $this->SafeGetVal($json, 'lname', $client->Lname);
            $client->Mname = $this->SafeGetVal($json, 'mname', $client->Mname);
            $client->Dob = date('Y-m-d', strtotime($this->SafeGetVal($json, 'dob', $client->Dob)));
            $client->Street = $this->SafeGetVal($json, 'street', $client->Street);
            $client->PostalCode = $this->SafeGetVal($json, 'postalCode', $client->PostalCode);
            $client->City = $this->SafeGetVal($json, 'city', $client->City);
            $client->State = $this->SafeGetVal($json, 'state', $client->State);
            $client->CountryCode = $this->SafeGetVal($json, 'countryCode', $client->CountryCode);
            $client->DriversLicense = $this->SafeGetVal($json, 'driversLicense', $client->DriversLicense);
            $client->Ss = $this->SafeGetVal($json, 'ss', $client->Ss);
            $client->Occupation = $this->SafeGetVal($json, 'occupation', $client->Occupation);
            $client->PhoneHome = $this->SafeGetVal($json, 'phoneHome', $client->PhoneHome);
            $client->PhoneBiz = $this->SafeGetVal($json, 'phoneBiz', $client->PhoneBiz);
            $client->PhoneContact = $this->SafeGetVal($json, 'phoneContact', $client->PhoneContact);
            $client->PhoneCell = $this->SafeGetVal($json, 'phoneCell', $client->PhoneCell);
            $client->PharmacyId = $this->SafeGetVal($json, 'pharmacyId', $client->PharmacyId);
            $client->Status = $this->SafeGetVal($json, 'status', $client->Status);
            $client->ContactRelationship = $this->SafeGetVal($json, 'contactRelationship', $client->ContactRelationship);
            $client->Date = date('Y-m-d H:i:s', strtotime($this->SafeGetVal($json, 'date', $client->Date)));
            $client->Sex = $this->SafeGetVal($json, 'sex', $client->Sex);
            $client->Referrer = $this->SafeGetVal($json, 'referrer', $client->Referrer);
            $client->Referrerid = $this->SafeGetVal($json, 'referrerid', $client->Referrerid);
            $client->Providerid = $this->SafeGetVal($json, 'providerid', $client->Providerid);
            $client->RefProviderid = $this->SafeGetVal($json, 'refProviderid', $client->RefProviderid);
            $client->Email = $this->SafeGetVal($json, 'email', $client->Email);
            $client->EmailDirect = $this->SafeGetVal($json, 'emailDirect', $client->EmailDirect);
            $client->Ethnoracial = $this->SafeGetVal($json, 'ethnoracial', $client->Ethnoracial);
            $client->Race = $this->SafeGetVal($json, 'race', $client->Race);
            $client->Ethnicity = $this->SafeGetVal($json, 'ethnicity', $client->Ethnicity);
            $client->Religion = $this->SafeGetVal($json, 'religion', $client->Religion);
            $client->Interpretter = $this->SafeGetVal($json, 'interpretter', $client->Interpretter);
            $client->Migrantseasonal = $this->SafeGetVal($json, 'migrantseasonal', $client->Migrantseasonal);
            $client->FamilySize = $this->SafeGetVal($json, 'familySize', $client->FamilySize);
            $client->MonthlyIncome = $this->SafeGetVal($json, 'monthlyIncome', $client->MonthlyIncome);
            $client->BillingNote = $this->SafeGetVal($json, 'billingNote', $client->BillingNote);
            $client->Homeless = $this->SafeGetVal($json, 'homeless', $client->Homeless);
            $client->FinancialReview = date('Y-m-d H:i:s', strtotime($this->SafeGetVal($json, 'financialReview', $client->FinancialReview)));
            $client->Pubpid = $this->SafeGetVal($json, 'pubpid', $client->Pubpid);
            $client->Pid = $this->SafeGetVal($json, 'pid', $client->Pid);
            $client->Genericname1 = $this->SafeGetVal($json, 'genericname1', $client->Genericname1);
            $client->Genericval1 = $this->SafeGetVal($json, 'genericval1', $client->Genericval1);
            $client->Genericname2 = $this->SafeGetVal($json, 'genericname2', $client->Genericname2);
            $client->Genericval2 = $this->SafeGetVal($json, 'genericval2', $client->Genericval2);
            $client->HipaaMail = $this->SafeGetVal($json, 'hipaaMail', $client->HipaaMail);
            $client->HipaaVoice = $this->SafeGetVal($json, 'hipaaVoice', $client->HipaaVoice);
            $client->HipaaNotice = $this->SafeGetVal($json, 'hipaaNotice', $client->HipaaNotice);
            $client->HipaaMessage = $this->SafeGetVal($json, 'hipaaMessage', $client->HipaaMessage);
            $client->HipaaAllowsms = $this->SafeGetVal($json, 'hipaaAllowsms', $client->HipaaAllowsms);
            $client->HipaaAllowemail = $this->SafeGetVal($json, 'hipaaAllowemail', $client->HipaaAllowemail);
            $client->Squad = $this->SafeGetVal($json, 'squad', $client->Squad);
            $client->Fitness = $this->SafeGetVal($json, 'fitness', $client->Fitness);
            $client->ReferralSource = $this->SafeGetVal($json, 'referralSource', $client->ReferralSource);
            $client->Pricelevel = $this->SafeGetVal($json, 'pricelevel', $client->Pricelevel);
            $client->Regdate = date('Y-m-d', strtotime($this->SafeGetVal($json, 'regdate', $client->Regdate)));
            $client->Contrastart = date('Y-m-d', strtotime($this->SafeGetVal($json, 'contrastart', $client->Contrastart)));
            $client->CompletedAd = $this->SafeGetVal($json, 'completedAd', $client->CompletedAd);
            $client->AdReviewed = date('Y-m-d', strtotime($this->SafeGetVal($json, 'adReviewed', $client->AdReviewed)));
            $client->Vfc = $this->SafeGetVal($json, 'vfc', $client->Vfc);
            $client->Mothersname = $this->SafeGetVal($json, 'mothersname', $client->Mothersname);
            $client->Guardiansname = $this->SafeGetVal($json, 'guardiansname', $client->Guardiansname);
            $client->AllowImmRegUse = $this->SafeGetVal($json, 'allowImmRegUse', $client->AllowImmRegUse);
            $client->AllowImmInfoShare = $this->SafeGetVal($json, 'allowImmInfoShare', $client->AllowImmInfoShare);
            $client->AllowHealthInfoEx = $this->SafeGetVal($json, 'allowHealthInfoEx', $client->AllowHealthInfoEx);
            $client->AllowClientPortal = $this->SafeGetVal($json, 'allowClientPortal', $client->AllowClientPortal);
            $client->DeceasedDate = date('Y-m-d H:i:s', strtotime($this->SafeGetVal($json, 'deceasedDate', $client->DeceasedDate)));
            $client->DeceasedReason = $this->SafeGetVal($json, 'deceasedReason', $client->DeceasedReason);
            $client->SoapImportStatus = $this->SafeGetVal($json, 'soapImportStatus', $client->SoapImportStatus);
            $client->CmsportalLogin = $this->SafeGetVal($json, 'cmsportalLogin', $client->CmsportalLogin);
            $client->CareTeam = $this->SafeGetVal($json, 'careTeam', $client->CareTeam);
            $client->County = $this->SafeGetVal($json, 'county', $client->County);
            $client->Industry = $this->SafeGetVal($json, 'industry', $client->Industry);

            $client->Validate();
            $errors = $client->GetValidationErrors();

            if (count($errors) > 0) {
                $this->RenderErrorJSON('Please check the form for errors' . $errors, $errors);
            } else {
                $client->Save();
                $this->RenderJSON($client, $this->JSONPCallback(), true, $this->SimpleObjectParams());
            }
        } catch (Exception $ex) {
            $this->RenderExceptionJSON($ex);
        }
    }

    /**
     * API Method updates an existing Client record and render response as JSON
     */
    public function Update()
    {
        try {
            $json = json_decode(RequestUtil::GetBody());

            if (! $json) {
                throw new Exception('The request body does not contain valid JSON');
            }

            $pk = $this->GetRouter()->GetUrlParam('id');
            $client = $this->Phreezer->Get('Client', $pk);
            // this is a primary key. uncomment if updating is allowed
            // $client->Id = $this->SafeGetVal($json, 'id', $client->Id);
            $client->Title = $this->SafeGetVal($json, 'title', $client->Title);
            $client->Language = $this->SafeGetVal($json, 'language', $client->Language);
            $client->Financial = $this->SafeGetVal($json, 'financial', $client->Financial);
            $client->Fname = $this->SafeGetVal($json, 'fname', $client->Fname);
            $client->Lname = $this->SafeGetVal($json, 'lname', $client->Lname);
            $client->Mname = $this->SafeGetVal($json, 'mname', $client->Mname);
            $client->Dob = date('Y-m-d', strtotime($this->SafeGetVal($json, 'dob', $client->Dob)));
            $client->Street = $this->SafeGetVal($json, 'street', $client->Street);
            $client->PostalCode = $this->SafeGetVal($json, 'postalCode', $client->PostalCode);
            $client->City = $this->SafeGetVal($json, 'city', $client->City);
            $client->State = $this->SafeGetVal($json, 'state', $client->State);
            $client->CountryCode = $this->SafeGetVal($json, 'countryCode', $client->CountryCode);
            $client->DriversLicense = $this->SafeGetVal($json, 'driversLicense', $client->DriversLicense);
            $client->Ss = $this->SafeGetVal($json, 'ss', $client->Ss);
            $client->Occupation = $this->SafeGetVal($json, 'occupation', $client->Occupation);
            $client->PhoneHome = $this->SafeGetVal($json, 'phoneHome', $client->PhoneHome);
            $client->PhoneBiz = $this->SafeGetVal($json, 'phoneBiz', $client->PhoneBiz);
            $client->PhoneContact = $this->SafeGetVal($json, 'phoneContact', $client->PhoneContact);
            $client->PhoneCell = $this->SafeGetVal($json, 'phoneCell', $client->PhoneCell);
            $client->PharmacyId = $this->SafeGetVal($json, 'pharmacyId', $client->PharmacyId);
            $client->Status = $this->SafeGetVal($json, 'status', $client->Status);
            $client->ContactRelationship = $this->SafeGetVal($json, 'contactRelationship', $client->ContactRelationship);
            $client->Date = date('Y-m-d H:i:s', strtotime($this->SafeGetVal($json, 'date', $client->Date)));
            $client->Sex = $this->SafeGetVal($json, 'sex', $client->Sex);
            $client->Referrer = $this->SafeGetVal($json, 'referrer', $client->Referrer);
            $client->Referrerid = $this->SafeGetVal($json, 'referrerid', $client->Referrerid);
            $client->Providerid = $this->SafeGetVal($json, 'providerid', $client->Providerid);
            $client->RefProviderid = $this->SafeGetVal($json, 'refProviderid', $client->RefProviderid);
            $client->Email = $this->SafeGetVal($json, 'email', $client->Email);
            $client->EmailDirect = $this->SafeGetVal($json, 'emailDirect', $client->EmailDirect);
            $client->Ethnoracial = $this->SafeGetVal($json, 'ethnoracial', $client->Ethnoracial);
            $client->Race = $this->SafeGetVal($json, 'race', $client->Race);
            $client->Ethnicity = $this->SafeGetVal($json, 'ethnicity', $client->Ethnicity);
            $client->Religion = $this->SafeGetVal($json, 'religion', $client->Religion);
            $client->Interpretter = $this->SafeGetVal($json, 'interpretter', $client->Interpretter);
            $client->Migrantseasonal = $this->SafeGetVal($json, 'migrantseasonal', $client->Migrantseasonal);
            $client->FamilySize = $this->SafeGetVal($json, 'familySize', $client->FamilySize);
            $client->MonthlyIncome = $this->SafeGetVal($json, 'monthlyIncome', $client->MonthlyIncome);
            $client->BillingNote = $this->SafeGetVal($json, 'billingNote', $client->BillingNote);
            $client->Homeless = $this->SafeGetVal($json, 'homeless', $client->Homeless);
            $client->FinancialReview = date('Y-m-d H:i:s', strtotime($this->SafeGetVal($json, 'financialReview', $client->FinancialReview)));
            $client->Pubpid = $this->SafeGetVal($json, 'pubpid', $client->Pubpid);
            $client->Pid = $this->SafeGetVal($json, 'pid', $client->Pid);
            $client->HipaaMail = $this->SafeGetVal($json, 'hipaaMail', $client->HipaaMail);
            $client->HipaaVoice = $this->SafeGetVal($json, 'hipaaVoice', $client->HipaaVoice);
            $client->HipaaNotice = $this->SafeGetVal($json, 'hipaaNotice', $client->HipaaNotice);
            $client->HipaaMessage = $this->SafeGetVal($json, 'hipaaMessage', $client->HipaaMessage);
            $client->HipaaAllowsms = $this->SafeGetVal($json, 'hipaaAllowsms', $client->HipaaAllowsms);
            $client->HipaaAllowemail = $this->SafeGetVal($json, 'hipaaAllowemail', $client->HipaaAllowemail);
            $client->ReferralSource = $this->SafeGetVal($json, 'referralSource', $client->ReferralSource);
            $client->Pricelevel = $this->SafeGetVal($json, 'pricelevel', $client->Pricelevel);
            $client->Regdate = date('Y-m-d', strtotime($this->SafeGetVal($json, 'regdate', $client->Regdate)));
            $client->Contrastart = date('Y-m-d', strtotime($this->SafeGetVal($json, 'contrastart', $client->Contrastart)));
            $client->CompletedAd = $this->SafeGetVal($json, 'completedAd', $client->CompletedAd);
            $client->AdReviewed = date('Y-m-d', strtotime($this->SafeGetVal($json, 'adReviewed', $client->AdReviewed)));
            $client->Vfc = $this->SafeGetVal($json, 'vfc', $client->Vfc);
            $client->Mothersname = $this->SafeGetVal($json, 'mothersname', $client->Mothersname);
            $client->Guardiansname = $this->SafeGetVal($json, 'guardiansname', $client->Guardiansname);
            $client->AllowImmRegUse = $this->SafeGetVal($json, 'allowImmRegUse', $client->AllowImmRegUse);
            $client->AllowImmInfoShare = $this->SafeGetVal($json, 'allowImmInfoShare', $client->AllowImmInfoShare);
            $client->AllowHealthInfoEx = $this->SafeGetVal($json, 'allowHealthInfoEx', $client->AllowHealthInfoEx);
            $client->AllowClientPortal = $this->SafeGetVal($json, 'allowClientPortal', $client->AllowClientPortal);
            $client->CareTeam = $this->SafeGetVal($json, 'careTeam', $client->CareTeam);
            $client->County = $this->SafeGetVal($json, 'county', $client->County);
            $client->Industry = $this->SafeGetVal($json, 'industry', $client->Industry);

            $client->Validate();
            $errors = $client->GetValidationErrors();

            if (count($errors) > 0) {
                $this->RenderErrorJSON('Please check the form for errors', $errors);
            } else {
                $client->Save();
                self::CloseAudit($client);
                $this->RenderJSON($client, $this->JSONPCallback(), true, $this->SimpleObjectParams());
            }
        } catch (Exception $ex) {
            $this->RenderExceptionJSON($ex);
        }
    }
    public function CloseAudit($p)
    {
        $appsql = new ApplicationTable();
        $ja = $p->GetArray();
        try {
            $audit = array ();
            // date("Y-m-d H:i:s");
            $audit['client_id'] = $ja['pid'];
            $audit['activity'] = "profile";
            $audit['require_audit'] = "1";
            $audit['pending_action'] = "completed";
            $audit['action_taken'] = "accept";
            $audit['status'] = "closed";
            $audit['narrative'] = "Changes reviewed and commited to demographics.";
            $audit['table_action'] = "update";
            $audit['table_args'] = $ja;
            $audit['action_user'] = isset($_SESSION['authUserID']) ? $_SESSION['authUserID'] : "0";
            $audit['action_taken_time'] = date("Y-m-d H:i:s");
            $audit['checksum'] = "0";

            $edata = $appsql->getPortalAudit($ja['pid'], 'review');
            $audit['date'] = $edata['date'];
            if ($edata['id'] > 0) {
                $appsql->portalAudit('update', $edata['id'], $audit);
            }
        } catch (Exception $ex) {
            $this->RenderExceptionJSON($ex);
        }
    }
    /**
     * API Method deletes an existing Client record and render response as JSON
     */
    public function Delete()
    {
        try {
            // TODO: if a soft delete is prefered, change this to update the deleted flag instead of hard-deleting

            $pk = $this->GetRouter()->GetUrlParam('id');
            $client = $this->Phreezer->Get('Client', $pk);

            $client->Delete();

            $output = new stdClass();

            $this->RenderJSON($output, $this->JSONPCallback());
        } catch (Exception $ex) {
            $this->RenderExceptionJSON($ex);
        }
    }
}
