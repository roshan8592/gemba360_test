<?php
/**
 *
 * AMC 314g_1_2_14 STAGE2 Numerator
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

class AMC_314g_1_2_14_STG2_Numerator implements AmcFilterIF
{
    public function getTitle()
    {
        return "AMC_314g_1_2_14_STG2 Numerator";
    }
    
    public function test(AmcClient $client, $beginDate, $endDate)
    {
        // Need to meet following criteria:
        //  -Offsite client portal is turned on.
        //  -Client permits having access to the client portal.
        //  -Client has an account on the offsite client portal.

        if ($GLOBALS['portal_offsite_enable'] != 1) {
            return false;
        }

        $portal_permission = sqlQuery("SELECT `allow_client_portal` FROM `client_data` WHERE pid = ?", array($client->id));
        if ($portal_permission['allow_client_portal'] != "YES") {
            return false;
        }
                
        $portalQry = "SELECT count(*) as cnt FROM `client_access_offsite` WHERE pid=?";
        $check = sqlQuery($portalQry, array($client->id));
        if ($check['cnt'] > 0) {
            return true;
        } else {
            return false;
        }
    }
}
