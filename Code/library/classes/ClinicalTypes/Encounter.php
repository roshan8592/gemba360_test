<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
require_once('ClinicalType.php');

class Encounter extends ClinicalType
{
    const OPTION_ENCOUNTER_COUNT = 'count';
    
    const ENC_OUTCLIENT = 'enc_outclient';
    const ENC_NURS_FAC = 'enc_nurs_fac';
    const ENC_OFF_VIS = 'enc_off_vis';
    const ENC_HEA_AND_BEH = 'enc_hea_and_beh';
    const ENC_OCC_THER = 'enc_occ_ther';
    const ENC_PSYCH_AND_PSYCH = 'enc_psych_and_psych';
    const ENC_PRE_MED_SER_18_OLDER = 'enc_pre_med_ser_18_older';
    const ENC_PRE_MED_SER_40_OLDER = 'enc_pre_med_ser_40_older';
    const ENC_PRE_IND_COUNSEL = 'enc_pre_ind_counsel';
    const ENC_PRE_MED_GROUP_COUNSEL = 'enc_pre_med_group_counsel';
    const ENC_PRE_MED_OTHER_SERV = 'enc_pre_med_other_serv';
    const ENC_OUT_PCP_OBGYN = 'enc_out_pcp_obgyn';
    const ENC_PREGNANCY = 'enc_pregnancy';
    const ENC_ACUTE_INP_OR_ED = 'enc_acute_inp_or_ed'; // encounter acute inclient or ED
    const ENC_NURS_DISCHARGE = 'enc_nurs_discharge'; // encounter nursing discharge
    const ENC_NONAC_INP_OUT_OR_OPTH = 'enc_nonac_inp_out_or_opth'; // encounter non-acute inpt, outclient, or ophthalmology
    const ENC_INFLUENZA = 'enc_influenza';
    const ENC_OPHTHAL = 'enc_ophthal_serv';
    
    public static function getEncounterTypes()
    {
        $oClass = new ReflectionClass('Encounter');
        $constants = $oClass->getConstants();
        $encounters = array();
        foreach ($constants as $constant) {
            if (strpos($constant, 'enc') === 0) {
                $encounters[]= $constant;
            }
        }

        return $encounters;
    }
    
    public function getListId()
    {
        return "rule_enc_types";
    }
    
    /*
     *  Fetch an array of all dates on which this encounter took place for a client.
     *
     *  @param (CqmClient) $client
     *  @param $beginDate beginning of date range to search in, if specified
     *  @param $endDate end of date range to search in, if specified
     */
    public function fetchDates(RsClient $client, $beginDate = null, $endDate = null)
    {
        $encounters = getEncounters($client->id, $beginDate, $endDate, $this->getOptionId());
        $dates = array();
        foreach ($encounters as $encounter) {
            $dateRow = getEncounterDateByEncounter($encounter['encounter']);
            $dates []= $dateRow['date'];
        }

        return $dates;
    }
    
    public function doClientCheck(RsClient $client, $beginMeasurement = null, $endMeasurement = null, $options = null)
    {
        $encounters = getEncounters($client->id, $beginMeasurement, $endMeasurement, $this->getOptionId());
        ( empty($encounters) ) ? $totalNumberAppt = 0 : $totalNumberAppt = count($encounters);
        $requiredCount = 1;
        if (isset($options[self::OPTION_ENCOUNTER_COUNT])) {
            $requiredCount = $options[self::OPTION_ENCOUNTER_COUNT];
        }

        if ($totalNumberAppt < $requiredCount) {
            return false;
        } else {
            return true;
        }
    }
}
