<?php
// Copyright (C) 2011 Brady Miller <brady.g.miller@gmail.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//


class AMC_304c_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304c Numerator";
    }
    
    public function test(AmcClient $client, $beginDate, $endDate)
    {
        // have demographics recorded as structured data
        //  Need preferred language, gender, race, ethnicity, date of birth.
        if ((exist_database_item($client->id, 'client_data', 'language', '', '', 'ge', 1)) &&
             (exist_database_item($client->id, 'client_data', 'sex', '', '', 'ge', 1)) &&
             (exist_database_item($client->id, 'client_data', 'race', '', '', 'ge', 1)) &&
             (exist_database_item($client->id, 'client_data', 'ethnicity', '', '', 'ge', 1)) &&
             (exist_database_item($client->id, 'client_data', 'DOB', '', '', 'ge', 1)) ) {
            return true;
        } else {
            return false;
        }
    }
}
