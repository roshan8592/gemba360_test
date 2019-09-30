<?php
/**
 *
 * CQM NQF 0038(2014) Numerator
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

class NQF_0038_2014_Numerator implements CqmFilterIF
{
    public function getTitle()
    {
        return "Numerator";
    }
    
    public function test(CqmClient $client, $beginDate, $endDate)
    {
        if ((Immunizations::checkDtap($client, $beginDate, $endDate) ) ||
              ( Immunizations::checkIpv($client, $beginDate, $endDate) ) ||
              ( Immunizations::checkMmr($client, $beginDate, $endDate) ) ||
              ( Immunizations::checkHib($client, $beginDate, $endDate) ) ||
              ( Immunizations::checkHepB($client, $beginDate, $endDate) ) ||
              ( Immunizations::checkVzv($client, $beginDate, $endDate) )  ||
              ( Immunizations::checkPheumococcal($client, $beginDate, $endDate) ) ||
              ( Immunizations::checkHepA($client, $beginDate, $endDate) ) ||
              ( Immunizations::checkRotavirus_2014($client, $beginDate, $endDate) ) ||
              ( Immunizations::checkInfluenza($client, $beginDate, $endDate) )
            ) {
            return true;
        }

        return false;
    }
}
