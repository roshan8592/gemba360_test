<?php
/**
 * ClientReporter.php
 *
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @author    Jerry Padgett <sjpadgett@gmail.com>
 * @copyright Copyright (c) 2016-2017 Jerry Padgett <sjpadgett@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

/** import supporting libraries */
require_once("verysimple/Phreeze/Reporter.php");

/**
 * This is an example Reporter based on the Client object.  The reporter object
 * allows you to run arbitrary queries that return data which may or may not fith within
 * the data access API.  This can include aggregate data or subsets of data.
 *
 * Note that Reporters are read-only and cannot be used for saving data.
 *
 * @package Openemr::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class ClientReporter extends Reporter
{

    // the properties in this class must match the columns returned by GetCustomQuery().
    public $Id;
    public $Title;
    public $Language;
    public $Financial;
    public $Fname;
    public $Lname;
    public $Mname;
    public $Dob;
    public $Street;
    public $PostalCode;
    public $City;
    public $State;
    public $CountryCode;
    public $DriversLicense;
    public $Ss;
    public $Occupation;
    public $PhoneHome;
    public $PhoneBiz;
    public $PhoneContact;
    public $PhoneCell;
    public $PharmacyId;
    public $Status;
    public $ContactRelationship;
    public $Date;
    public $Sex;
    public $Referrer;
    public $Referrerid;
    public $Providerid;
    public $RefProviderid;
    public $Email;
    public $EmailDirect;
    public $Ethnoracial;
    public $Race;
    public $Ethnicity;
    public $Religion;
    public $Interpretter;
    public $Migrantseasonal;
    public $FamilySize;
    public $MonthlyIncome;
    public $BillingNote;
    public $Homeless;
    public $FinancialReview;
    public $Pubpid;
    public $Pid;
    public $Genericname1;
    public $Genericval1;
    public $Genericname2;
    public $Genericval2;
    public $HipaaMail;
    public $HipaaVoice;
    public $HipaaNotice;
    public $HipaaMessage;
    public $HipaaAllowsms;
    public $HipaaAllowemail;
    public $Squad;
    public $Fitness;
    public $ReferralSource;
    public $Usertext1;
    public $Usertext2;
    public $Usertext3;
    public $Usertext4;
    public $Usertext5;
    public $Usertext6;
    public $Usertext7;
    public $Usertext8;
    public $Userlist1;
    public $Userlist2;
    public $Userlist3;
    public $Userlist4;
    public $Userlist5;
    public $Userlist6;
    public $Userlist7;
    public $Pricelevel;
    public $Regdate;
    public $Contrastart;
    public $CompletedAd;
    public $AdReviewed;
    public $Vfc;
    public $Mothersname;
    public $Guardiansname;
    public $AllowImmRegUse;
    public $AllowImmInfoShare;
    public $AllowHealthInfoEx;
    public $AllowClientPortal;
    public $DeceasedDate;
    public $DeceasedReason;
    public $SoapImportStatus;
    public $CmsportalLogin;
    public $CareTeam;
    public $County;
    public $Industry;

    /*
    * GetCustomQuery returns a fully formed SQL statement.  The result columns
    * must match with the properties of this reporter object.
    *
    * @see Reporter::GetCustomQuery
    * @param Criteria $criteria
    * @return string SQL statement
    */
    static function GetCustomQuery($criteria)
    {
        $sql = "select
			 `client_data`.`id` as Id
			,`client_data`.`title` as Title
			,`client_data`.`language` as Language
			,`client_data`.`financial` as Financial
			,`client_data`.`fname` as Fname
			,`client_data`.`lname` as Lname
			,`client_data`.`mname` as Mname
			,`client_data`.`DOB` as Dob
			,`client_data`.`street` as Street
			,`client_data`.`postal_code` as PostalCode
			,`client_data`.`city` as City
			,`client_data`.`state` as State
			,`client_data`.`country_code` as CountryCode
			,`client_data`.`drivers_license` as DriversLicense
			,`client_data`.`ss` as Ss
			,`client_data`.`occupation` as Occupation
			,`client_data`.`phone_home` as PhoneHome
			,`client_data`.`phone_biz` as PhoneBiz
			,`client_data`.`phone_contact` as PhoneContact
			,`client_data`.`phone_cell` as PhoneCell
			,`client_data`.`pharmacy_id` as PharmacyId
			,`client_data`.`status` as Status
			,`client_data`.`contact_relationship` as ContactRelationship
			,`client_data`.`date` as Date
			,`client_data`.`sex` as Sex
			,`client_data`.`referrer` as Referrer
			,`client_data`.`referrerID` as Referrerid
			,`client_data`.`providerID` as Providerid
			,`client_data`.`ref_providerID` as RefProviderid
			,`client_data`.`email` as Email
			,`client_data`.`email_direct` as EmailDirect
			,`client_data`.`ethnoracial` as Ethnoracial
			,`client_data`.`race` as Race
			,`client_data`.`ethnicity` as Ethnicity
			,`client_data`.`religion` as Religion
			,`client_data`.`interpretter` as Interpretter
			,`client_data`.`migrantseasonal` as Migrantseasonal
			,`client_data`.`family_size` as FamilySize
			,`client_data`.`monthly_income` as MonthlyIncome
			,`client_data`.`billing_note` as BillingNote
			,`client_data`.`homeless` as Homeless
			,`client_data`.`financial_review` as FinancialReview
			,`client_data`.`pubpid` as Pubpid
			,`client_data`.`pid` as Pid
			,`client_data`.`genericname1` as Genericname1
			,`client_data`.`genericval1` as Genericval1
			,`client_data`.`genericname2` as Genericname2
			,`client_data`.`genericval2` as Genericval2
			,`client_data`.`hipaa_mail` as HipaaMail
			,`client_data`.`hipaa_voice` as HipaaVoice
			,`client_data`.`hipaa_notice` as HipaaNotice
			,`client_data`.`hipaa_message` as HipaaMessage
			,`client_data`.`hipaa_allowsms` as HipaaAllowsms
			,`client_data`.`hipaa_allowemail` as HipaaAllowemail
			,`client_data`.`squad` as Squad
			,`client_data`.`fitness` as Fitness
			,`client_data`.`referral_source` as ReferralSource
			,`client_data`.`usertext1` as Usertext1
			,`client_data`.`usertext2` as Usertext2
			,`client_data`.`usertext3` as Usertext3
			,`client_data`.`usertext4` as Usertext4
			,`client_data`.`usertext5` as Usertext5
			,`client_data`.`usertext6` as Usertext6
			,`client_data`.`usertext7` as Usertext7
			,`client_data`.`usertext8` as Usertext8
			,`client_data`.`userlist1` as Userlist1
			,`client_data`.`userlist2` as Userlist2
			,`client_data`.`userlist3` as Userlist3
			,`client_data`.`userlist4` as Userlist4
			,`client_data`.`userlist5` as Userlist5
			,`client_data`.`userlist6` as Userlist6
			,`client_data`.`userlist7` as Userlist7
			,`client_data`.`pricelevel` as Pricelevel
			,`client_data`.`regdate` as Regdate
			,`client_data`.`contrastart` as Contrastart
			,`client_data`.`completed_ad` as CompletedAd
			,`client_data`.`ad_reviewed` as AdReviewed
			,`client_data`.`vfc` as Vfc
			,`client_data`.`mothersname` as Mothersname
			,`client_data`.`guardiansname` as Guardiansname
			,`client_data`.`allow_imm_reg_use` as AllowImmRegUse
			,`client_data`.`allow_imm_info_share` as AllowImmInfoShare
			,`client_data`.`allow_health_info_ex` as AllowHealthInfoEx
			,`client_data`.`allow_client_portal` as AllowClientPortal
			,`client_data`.`deceased_date` as DeceasedDate
			,`client_data`.`deceased_reason` as DeceasedReason
			,`client_data`.`soap_import_status` as SoapImportStatus
			,`client_data`.`cmsportal_login` as CmsportalLogin
			,`client_data`.`care_team` as CareTeam
			,`client_data`.`county` as County
			,`client_data`.`industry` as Industry
		from `client_data`";

        // the criteria can be used or you can write your own custom logic.
        // be sure to escape any user input with $criteria->Escape()
        $sql .= $criteria->GetWhere();
        $sql .= $criteria->GetOrder();

        if ($criteria->Pid_Equals == 0) {
            $sql = "DESCRIBE client_data";
        }

        return $sql;
    }

    /*
    * GetCustomCountQuery returns a fully formed SQL statement that will count
    * the results.  This query must return the correct number of results that
    * GetCustomQuery would, given the same criteria
    *
    * @see Reporter::GetCustomCountQuery
    * @param Criteria $criteria
    * @return string SQL statement
    */
    static function GetCustomCountQuery($criteria)
    {
        $sql = "select count(1) as counter from `client_data`";

        // the criteria can be used or you can write your own custom logic.
        // be sure to escape any user input with $criteria->Escape()
        $sql .= $criteria->GetWhere();

        return $sql;
    }
}
