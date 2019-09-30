<?php
/**
 * ClientMap.php
 *
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @author    Jerry Padgett <sjpadgett@gmail.com>
 * @copyright Copyright (c) 2016-2017 Jerry Padgett <sjpadgett@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");
require_once("verysimple/Phreeze/IDaoMap2.php");

/**
 * ClientMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the ClientDAO to the client_data datastore.
 *
 * WARNING: THIS IS AN AUTO-GENERATED FILE
 *
 * This file should generally not be edited by hand except in special circumstances.
 * You can override the default fetching strategies for KeyMaps in _config.php.
 * Leaving this file alone will allow easy re-generation of all DAOs in the event of schema changes
 *
 * @package Openemr::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class ClientMap implements IDaoMap, IDaoMap2
{

    private static $KM;
    private static $FM;

    /**
     * {@inheritdoc}
     */
    public static function AddMap($property, FieldMap $map)
    {
        self::GetFieldMaps();
        self::$FM[$property] = $map;
    }

    /**
     * {@inheritdoc}
     */
    public static function SetFetchingStrategy($property, $loadType)
    {
        self::GetKeyMaps();
        self::$KM[$property]->LoadType = $loadType;
    }

    /**
     * {@inheritdoc}
     */
    public static function GetFieldMaps()
    {
        if (self::$FM == null) {
            self::$FM = array();
            self::$FM["Id"] = new FieldMap("Id", "client_data", "id", true, FM_TYPE_BIGINT, 20, null, true);
            self::$FM["Title"] = new FieldMap("Title", "client_data", "title", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Language"] = new FieldMap("Language", "client_data", "language", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Financial"] = new FieldMap("Financial", "client_data", "financial", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Fname"] = new FieldMap("Fname", "client_data", "fname", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Lname"] = new FieldMap("Lname", "client_data", "lname", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Mname"] = new FieldMap("Mname", "client_data", "mname", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Dob"] = new FieldMap("Dob", "client_data", "DOB", false, FM_TYPE_DATE, null, null, false);
            self::$FM["Street"] = new FieldMap("Street", "client_data", "street", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["PostalCode"] = new FieldMap("PostalCode", "client_data", "postal_code", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["City"] = new FieldMap("City", "client_data", "city", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["State"] = new FieldMap("State", "client_data", "state", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["CountryCode"] = new FieldMap("CountryCode", "client_data", "country_code", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["DriversLicense"] = new FieldMap("DriversLicense", "client_data", "drivers_license", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Ss"] = new FieldMap("Ss", "client_data", "ss", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Occupation"] = new FieldMap("Occupation", "client_data", "occupation", false, FM_TYPE_LONGTEXT, null, null, false);
            self::$FM["PhoneHome"] = new FieldMap("PhoneHome", "client_data", "phone_home", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["PhoneBiz"] = new FieldMap("PhoneBiz", "client_data", "phone_biz", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["PhoneContact"] = new FieldMap("PhoneContact", "client_data", "phone_contact", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["PhoneCell"] = new FieldMap("PhoneCell", "client_data", "phone_cell", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["PharmacyId"] = new FieldMap("PharmacyId", "client_data", "pharmacy_id", false, FM_TYPE_INT, 11, null, false);
            self::$FM["Status"] = new FieldMap("Status", "client_data", "status", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["ContactRelationship"] = new FieldMap("ContactRelationship", "client_data", "contact_relationship", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Date"] = new FieldMap("Date", "client_data", "date", false, FM_TYPE_DATETIME, null, null, false);
            self::$FM["Sex"] = new FieldMap("Sex", "client_data", "sex", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Referrer"] = new FieldMap("Referrer", "client_data", "referrer", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Referrerid"] = new FieldMap("Referrerid", "client_data", "referrerID", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Providerid"] = new FieldMap("Providerid", "client_data", "providerID", false, FM_TYPE_INT, 11, null, false);
            self::$FM["RefProviderid"] = new FieldMap("RefProviderid", "client_data", "ref_providerID", false, FM_TYPE_INT, 11, null, false);
            self::$FM["Email"] = new FieldMap("Email", "client_data", "email", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["EmailDirect"] = new FieldMap("EmailDirect", "client_data", "email_direct", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Ethnoracial"] = new FieldMap("Ethnoracial", "client_data", "ethnoracial", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Race"] = new FieldMap("Race", "client_data", "race", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Ethnicity"] = new FieldMap("Ethnicity", "client_data", "ethnicity", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Religion"] = new FieldMap("Religion", "client_data", "religion", false, FM_TYPE_VARCHAR, 40, null, false);
            self::$FM["Interpretter"] = new FieldMap("Interpretter", "client_data", "interpretter", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Migrantseasonal"] = new FieldMap("Migrantseasonal", "client_data", "migrantseasonal", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["FamilySize"] = new FieldMap("FamilySize", "client_data", "family_size", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["MonthlyIncome"] = new FieldMap("MonthlyIncome", "client_data", "monthly_income", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["BillingNote"] = new FieldMap("BillingNote", "client_data", "billing_note", false, FM_TYPE_TEXT, null, null, false);
            self::$FM["Homeless"] = new FieldMap("Homeless", "client_data", "homeless", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["FinancialReview"] = new FieldMap("FinancialReview", "client_data", "financial_review", false, FM_TYPE_DATETIME, null, null, false);
            self::$FM["Pubpid"] = new FieldMap("Pubpid", "client_data", "pubpid", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Pid"] = new FieldMap("Pid", "client_data", "pid", false, FM_TYPE_BIGINT, 20, null, false);
            /* self::$FM["Genericname1"] = new FieldMap("Genericname1","client_data","genericname1",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Genericval1"] = new FieldMap("Genericval1","client_data","genericval1",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Genericname2"] = new FieldMap("Genericname2","client_data","genericname2",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Genericval2"] = new FieldMap("Genericval2","client_data","genericval2",false,FM_TYPE_VARCHAR,255,null,false); */
            self::$FM["HipaaMail"] = new FieldMap("HipaaMail", "client_data", "hipaa_mail", false, FM_TYPE_VARCHAR, 3, null, false);
            self::$FM["HipaaVoice"] = new FieldMap("HipaaVoice", "client_data", "hipaa_voice", false, FM_TYPE_VARCHAR, 3, null, false);
            self::$FM["HipaaNotice"] = new FieldMap("HipaaNotice", "client_data", "hipaa_notice", false, FM_TYPE_VARCHAR, 3, null, false);
            self::$FM["HipaaMessage"] = new FieldMap("HipaaMessage", "client_data", "hipaa_message", false, FM_TYPE_VARCHAR, 20, null, false);
            self::$FM["HipaaAllowsms"] = new FieldMap("HipaaAllowsms", "client_data", "hipaa_allowsms", false, FM_TYPE_VARCHAR, 3, "NO", false);
            self::$FM["HipaaAllowemail"] = new FieldMap("HipaaAllowemail", "client_data", "hipaa_allowemail", false, FM_TYPE_VARCHAR, 3, "NO", false);
            //self::$FM["Squad"] = new FieldMap("Squad","client_data","squad",false,FM_TYPE_VARCHAR,32,null,false);
            //self::$FM["Fitness"] = new FieldMap("Fitness","client_data","fitness",false,FM_TYPE_INT,11,null,false);
            self::$FM["ReferralSource"] = new FieldMap("ReferralSource", "client_data", "referral_source", false, FM_TYPE_VARCHAR, 30, null, false);
            /*self::$FM["Usertext1"] = new FieldMap("Usertext1","client_data","usertext1",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Usertext2"] = new FieldMap("Usertext2","client_data","usertext2",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Usertext3"] = new FieldMap("Usertext3","client_data","usertext3",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Usertext4"] = new FieldMap("Usertext4","client_data","usertext4",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Usertext5"] = new FieldMap("Usertext5","client_data","usertext5",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Usertext6"] = new FieldMap("Usertext6","client_data","usertext6",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Usertext7"] = new FieldMap("Usertext7","client_data","usertext7",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Usertext8"] = new FieldMap("Usertext8","client_data","usertext8",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Userlist1"] = new FieldMap("Userlist1","client_data","userlist1",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Userlist2"] = new FieldMap("Userlist2","client_data","userlist2",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Userlist3"] = new FieldMap("Userlist3","client_data","userlist3",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Userlist4"] = new FieldMap("Userlist4","client_data","userlist4",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Userlist5"] = new FieldMap("Userlist5","client_data","userlist5",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Userlist6"] = new FieldMap("Userlist6","client_data","userlist6",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Userlist7"] = new FieldMap("Userlist7","client_data","userlist7",false,FM_TYPE_VARCHAR,255,null,false);
            self::$FM["Pricelevel"] = new FieldMap("Pricelevel","client_data","pricelevel",false,FM_TYPE_VARCHAR,255,"standard",false); */
            self::$FM["Regdate"] = new FieldMap("Regdate", "client_data", "regdate", false, FM_TYPE_DATE, null, null, false);
            self::$FM["Contrastart"] = new FieldMap("Contrastart", "client_data", "contrastart", false, FM_TYPE_DATE, null, null, false);
            self::$FM["CompletedAd"] = new FieldMap("CompletedAd", "client_data", "completed_ad", false, FM_TYPE_VARCHAR, 3, "NO", false);
            self::$FM["AdReviewed"] = new FieldMap("AdReviewed", "client_data", "ad_reviewed", false, FM_TYPE_DATE, null, null, false);
            self::$FM["Vfc"] = new FieldMap("Vfc", "client_data", "vfc", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Mothersname"] = new FieldMap("Mothersname", "client_data", "mothersname", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["Guardiansname"] = new FieldMap("Guardiansname", "client_data", "guardiansname", false, FM_TYPE_VARCHAR, 255, null, false);
            self::$FM["AllowImmRegUse"] = new FieldMap("AllowImmRegUse", "client_data", "allow_imm_reg_use", false, FM_TYPE_VARCHAR, 3, null, false);
            self::$FM["AllowImmInfoShare"] = new FieldMap("AllowImmInfoShare", "client_data", "allow_imm_info_share", false, FM_TYPE_VARCHAR, 3, null, false);
            self::$FM["AllowHealthInfoEx"] = new FieldMap("AllowHealthInfoEx", "client_data", "allow_health_info_ex", false, FM_TYPE_VARCHAR, 3, null, false);
            self::$FM["AllowClientPortal"] = new FieldMap("AllowClientPortal", "client_data", "allow_client_portal", false, FM_TYPE_VARCHAR, 3, null, false);
            //self::$FM["DeceasedDate"] = new FieldMap("DeceasedDate","client_data","deceased_date",false,FM_TYPE_DATETIME,null,null,false);
            //self::$FM["DeceasedReason"] = new FieldMap("DeceasedReason","client_data","deceased_reason",false,FM_TYPE_VARCHAR,255,null,false);
            //self::$FM["SoapImportStatus"] = new FieldMap("SoapImportStatus","client_data","soap_import_status",false,FM_TYPE_TINYINT,4,0,false);
            //self::$FM["CmsportalLogin"] = new FieldMap("CmsportalLogin","client_data","cmsportal_login",false,FM_TYPE_VARCHAR,60,null,false);
            self::$FM["CareTeam"] = new FieldMap("CareTeam", "client_data", "care_team", false, FM_TYPE_INT, 11, null, false);
            self::$FM["County"] = new FieldMap("County", "client_data", "county", false, FM_TYPE_VARCHAR, 40, null, false);
            self::$FM["Industry"] = new FieldMap("Industry", "client_data", "industry", false, FM_TYPE_TEXT, null, null, false);
        }

        return self::$FM;
    }

    /**
     * {@inheritdoc}
     */
    public static function GetKeyMaps()
    {
        if (self::$KM == null) {
            self::$KM = array();
        }

        return self::$KM;
    }
}
