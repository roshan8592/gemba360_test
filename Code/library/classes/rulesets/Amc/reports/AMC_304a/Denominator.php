<?php
// Copyright (C) 2011 Brady Miller <brady.g.miller@gmail.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//

class AMC_304a_Denominator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304a Denominator";
    }
    
    public function test(AmcClient $client, $beginDate, $endDate)
    {
        // All unique clients seen by the EP or admitted to the eligible
        // hospital’s or CAH’s inclient or emergency department (POS 21 or 23)
        // Also need at least one medication on the med list.
        //  (basically needs an encounter within the report dates and medications
        //   entered by the endDate)
        $sql = "SELECT drug,1 as cpoe_stat " .
                       "FROM `prescriptions` " .
                       "WHERE `client_id` = ? " .
                       "AND `date_added` BETWEEN ? AND ? ".
                       "UNION ".
                       "SELECT title as drug,0 as cpoe_stat ".
                       "FROM lists l ".
                       "where l.type = 'medication' ".
                       "AND l.pid = ? ".
                       "AND l.date >= ? and l.date <= ? ";
        $check = sqlQuery($sql, array($client->id,$beginDate,$endDate,$client->id,$beginDate,$endDate));
        $options = array( Encounter::OPTION_ENCOUNTER_COUNT => 1 );
        if ((Helper::checkAnyEncounter($client, $beginDate, $endDate, $options)) &&
            !(empty($check)) ) {
            return true;
        } else {
            return false;
        }
    }
}
