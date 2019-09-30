<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
class NQF_0024_Numerator2 implements CqmFilterIF
{
    public function getTitle()
    {
        return "Numerator 2";
    }
    
    public function test(CqmClient $client, $beginDate, $endDate)
    {
        //if ( Helper::check( ClinicalType::COMMUNICATION, Communication::COUNS_NUTRITION, $client, $beginDate, $endDate ) ) {
        //    return true;
        //}

        $nutrition = sqlQuery("SELECT * FROM `rule_client_data` " .
                              "WHERE `category`='act_cat_edu' AND `item`='act_nutrition' AND `complete`='YES' " .
                              "AND `pid`=? AND `date`>=? AND `date`<=?", array($client->id,$beginDate,$endDate));
        if (!empty($nutrition)) {
            return true;
        }

        return false;
    }
}
