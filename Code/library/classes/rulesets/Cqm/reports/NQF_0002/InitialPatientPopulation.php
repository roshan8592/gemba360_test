<?php
/**
 *
 * CQM NQF 0002 Initial Client Population
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
 
class NQF_0002_InitialClientPopulation implements CqmFilterIF
{
    public function getTitle()
    {
        return "Initial Client Population";
    }
    
    public function test(CqmClient $client, $beginDate, $endDate)
    {
        $age = $client->calculateAgeOnDate($beginDate);
        if ($age >= 2 && $age < 18) {
            //Children 2-18 years of age who had an outclient or emergency department (ED) visit with a diagnosis of pharyngitis during the measurement period and an antibiotic ordered on or three days after the visit
            $antibiotics = implode(',', Codes::lookup(Medication::ANTIBIOTIC_FOR_PHARYNGITIS, 'RXNORM'));
            $query = "SELECT p.drug as drug FROM form_encounter fe ".
                     "INNER JOIN openemr_postcalendar_categories opc ON fe.pc_catid = opc.pc_catid ".
                     "INNER JOIN prescriptions p ON fe.pid = p.client_id ".
                     "WHERE opc.pc_catname = 'Office Visit' AND fe.pid = ? AND (fe.date BETWEEN ? AND ? ) ".
                     " AND p.rxnorm_drugcode in ( $antibiotics ) AND DATEDIFF(fe.date,p.date_added) <= 3";
            
            $check = sqlQuery($query, array($client->id, $beginDate, $endDate));
            if ($check['drug'] != "") {
                if (Helper::check(ClinicalType::DIAGNOSIS, Diagnosis::ACUTE_PHARYNGITIS, $client, $beginDate, $endDate) || Helper::check(ClinicalType::DIAGNOSIS, Diagnosis::ACUTE_TONSILLITIS, $client, $beginDate, $endDate)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        
        return false;
    }
}
