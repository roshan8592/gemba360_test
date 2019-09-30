<?php
// Copyright (C) 2011 Brady Miller <brady.g.miller@gmail.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//

class AMC_304d_Denominator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304d Denominator";
    }
    
    public function test(AmcClient $client, $beginDate, $endDate)
    {
        // All unique clients with age greater than or equal to 65
        //   or less than or equal to 5 at the end report date.
        if (($client->calculateAgeOnDate($endDate) >= 65) ||
             ($client->calculateAgeOnDate($endDate) <= 5) ) {
            return true;
        } else {
            return false;
        }
    }
}
