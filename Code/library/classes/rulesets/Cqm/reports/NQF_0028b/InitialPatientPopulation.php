<?php
// Copyright (C) 2011 Brady Miller <brady.g.miller@gmail.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
class NQF_0028b_InitialClientPopulation implements CqmFilterIF
{
    public function getTitle()
    {
        return "Initial Client Population";
    }
    
    public function test(CqmClient $client, $beginDate, $endDate)
    {
        $oneEncounter = array( Encounter::OPTION_ENCOUNTER_COUNT => 1 );
        $twoEncounters = array( Encounter::OPTION_ENCOUNTER_COUNT => 2 );
    
        if ($client->calculateAgeOnDate($beginDate) >= 18 &&
             ( Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_OFF_VIS, $client, $beginDate, $endDate, $twoEncounters) ||
               Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_HEA_AND_BEH, $client, $beginDate, $endDate, $twoEncounters) ||
               Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_OCC_THER, $client, $beginDate, $endDate, $twoEncounters) ||
               Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_PSYCH_AND_PSYCH, $client, $beginDate, $endDate, $twoEncounters) ||
               Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_PRE_MED_SER_18_OLDER, $client, $beginDate, $endDate, $oneEncounter) ||
               Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_PRE_IND_COUNSEL, $client, $beginDate, $endDate, $oneEncounter) ||
               Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_PRE_MED_GROUP_COUNSEL, $client, $beginDate, $endDate, $oneEncounter) ||
               Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_PRE_MED_OTHER_SERV, $client, $beginDate, $endDate, $oneEncounter)
             ) ) {
            return true;
        }
        
        return false;
    }
}
