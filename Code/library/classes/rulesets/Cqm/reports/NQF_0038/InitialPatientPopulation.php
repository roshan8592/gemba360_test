<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
class NQF_0038_InitialClientPopulation implements CqmFilterIF
{
    public function getTitle()
    {
        return "Initial Client Population";
    }

    public function test(CqmClient $client, $beginDate, $endDate)
    {
        // Rs_Client characteristic: birth date (age) >=1 year and <2 years to capture all Rs_Clients who will reach 2 years during the 'measurement period';
        $age = $client->calculateAgeOnDate($beginDate);
        if ($age >= 1 &&
            $age < 2 ) {
            return true;
        }
        
        return false;
    }
}
