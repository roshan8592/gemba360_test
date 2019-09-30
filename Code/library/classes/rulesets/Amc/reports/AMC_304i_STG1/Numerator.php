<?php
/**
 *
 * AMC 304i STAGE1 Numerator
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

class AMC_304i_STG1_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304i_STG1 Numerator";
    }
    
    public function test(AmcClient $client, $beginDate, $endDate)
    {
        //The number of transitions of care and referrals in the denominator where a summary of care record was provided.
        //  (so basically an amc element needs to exist)
        $amcElement = amcCollect('send_sum_amc', $client->id, 'transactions', $client->object['id']);
        if (!(empty($amcElement))) {
            $no_problems = sqlQuery("select count(*) as cnt from lists_touch where pid = ? and type = 'medical_problem'", array($client->id));
                $problems    = sqlQuery("select count(*) as cnt from lists where pid = ? and type = 'medical_problem'", array($client->id));

                $no_allergy     = sqlQuery("select count(*) as cnt from lists_touch where pid = ? and type = 'allergy'", array($client->id));
                $allergies      = sqlQuery("select count(*) as cnt from lists where pid = ? and type = 'allergy'", array($client->id));

                $no_medication = sqlQuery("select count(*) as cnt from lists_touch where pid = ? and type = 'medication'", array($client->id));
                $medications   = sqlQuery("select count(*) as cnt from lists where pid = ? and type = 'medication'", array($client->id));
                $prescriptions = sqlQuery("select count(*) as cnt from prescriptions where client_id = ? ", array($client->id));

            if (($no_problems['cnt'] > 0 || $problems['cnt'] > 0) && ($no_allergy['cnt'] > 0 || $allergies['cnt'] > 0) && ($no_medication['cnt'] > 0 || $medications['cnt'] > 0 || $prescriptions['cnt'] > 0)) {
                    return true;
            }

            return false;
        } else {
            return false;
        }
    }
}
