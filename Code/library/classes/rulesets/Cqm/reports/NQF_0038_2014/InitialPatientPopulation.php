<?php
/**
 *
 * CQM NQF 0038(2014) Initial Client Population
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

class NQF_0038_2014_InitialClientPopulation implements CqmFilterIF
{
    public function getTitle()
    {
        return "Initial Client Population";
    }

    public function test(CqmClient $client, $beginDate, $endDate)
    {
        // Rs_Client characteristic: birth date� (age) >=1 year and <2 years to capture all Rs_Clients who will reach 2 years during the �measurement period�;
        $age = $client->calculateAgeOnDate($beginDate);
        if ($age >= 1 && $age <= 2 &&  Helper::check(ClinicalType::ENCOUNTER, Encounter::ENC_OFF_VIS, $client, $beginDate, $endDate, 1)) {
            return true;
        }
        
        return false;
    }
}
