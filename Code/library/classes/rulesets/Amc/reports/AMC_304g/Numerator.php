<?php
// Copyright (C) 2011 Brady Miller <brady.g.miller@gmail.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//


class AMC_304g_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_304g Numerator";
    }
    
    public function test(AmcClient $client, $beginDate, $endDate)
    {
        // Simply need to have the client portal allowed.
        // TO DO: THIS ASSUMES THAT THERE IS A FUNCTIONING CLIENT PORTAL
        $check = sqlQuery("SELECT `allow_client_portal` FROM `client_data` WHERE `pid`=?", array($client->id));
        if ($check['allow_client_portal'] == "YES") {
            return true;
        } else {
            return false;
        }
    }
}
