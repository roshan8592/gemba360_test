<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
class NQF_0421_InitialClientPopulation2 implements CqmFilterIF
{
    public function getTitle()
    {
        return "Initial Client Population 2";
    }
    
    public function test(CqmClient $client, $beginDate, $endDate)
    {
        $age = intval($client->calculateAgeOnDate($beginDate));
        if ($age >= 65) {
            $oneEncounter = array( Encounter::OPTION_ENCOUNTER_COUNT => 1 );
            if (Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_OUTCLIENT, $client, $beginDate, $endDate, $oneEncounter)) {
                return true;
            }
        }
        
        return false;
    }
}
