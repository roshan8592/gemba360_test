<?php
/**
 *
 * CQM NQF 0013 Initial Client Population
 *
 * Copyright (C) 2015 Ensoftek, Inc
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Ensoftek
 * @link    http://www.open-emr.org
 */
 
class NQF_0013_InitialClientPopulation implements CqmFilterIF
{
    public function getTitle()
    {
        return "Initial Client Population";
    }
    
    public function test(CqmClient $client, $beginDate, $endDate)
    {
        $encounterCount = array( Encounter::OPTION_ENCOUNTER_COUNT => 1 );
        if ($client->calculateAgeOnDate($beginDate) >= 18 && $client->calculateAgeOnDate($beginDate) < 85 &&
            ( Helper::check(ClinicalType::DIAGNOSIS, Diagnosis::HYPERTENSION, $client, $beginDate, date('Y-m-d H:i:s', strtotime('+6 month', strtotime($beginDate)))) || (Helper::check(ClinicalType::DIAGNOSIS, Diagnosis::HYPERTENSION, $client, $beginDate, $beginDate))  ) &&
            ( Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_OUTCLIENT, $client, $beginDate, $endDate, $encounterCount) ||
              Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_NURS_FAC, $client, $beginDate, $endDate, $encounterCount) ) ) {
            return true;
        }
        
        return false;
    }
}
