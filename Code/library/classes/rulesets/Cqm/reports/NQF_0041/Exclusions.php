<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
class NQF_0041_Exclusions implements CqmFilterIF
{
    public function getTitle()
    {
        return "NQF 0041 Exclusions";
    }
    
    public function test(CqmClient $client, $beginDate, $endDate)
    {
        $encDates = Helper::fetchEncounterDates(Encounter::ENC_INFLUENZA, $client);
        foreach ($encDates as $encDate) {
            if (Helper::checkAllergy(Allergy::EGGS, $client, $encDate, $encDate) ||
                Helper::checkAllergy(Allergy::INFLUENZA_IMMUN, $client, $encDate, $encDate) ||
                Helper::checkMed(Medication::ADVERSE_EVT_FLU_IMMUN, $client, $encDate, $encDate) ||
                Helper::checkMed(Medication::INTOLERANCE_FLU_IMMUN, $client, $encDate, $encDate) ||
                Helper::checkMed(Medication::NO_INFLUENZA_CONTRADICTION, $client, $encDate, $encDate) ||
                Helper::checkMed(Medication::NO_INFLUENZA_DECLINED, $client, $encDate, $encDate) ||
                Helper::checkMed(Medication::NO_INFLUENZA_CLIENT, $client, $encDate, $encDate) ||
                Helper::checkMed(Medication::NO_INFLUENZA_MEDICAL, $client, $encDate, $encDate) ||
                Helper::checkMed(Medication::NO_INFLUENZA_SYSTEM, $client, $encDate, $encDate) ||
                Helper::checkDiagActive(Diagnosis::INFLUENZA_IMMUN_CONTRADICT, $client, $encDate, $encDate) ) {
                return true;
            }
        }
        
        return false;
    }
}
