<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
class NQF_0038_Numerator12 implements CqmFilterIF
{
    public function getTitle()
    {
        return "Numerator 12";
    }
    
    public function test(CqmClient $client, $beginDate, $endDate)
    {
        if (Immunizations::checkDtap($client, $beginDate, $endDate) &&
            Immunizations::checkIpv($client, $beginDate, $endDate) &&
            ( Immunizations::checkMmr($client, $beginDate, $endDate) &&
               !Helper::checkAllergy(Allergy::POLYMYXIN, $client, $client->dob, $endDate) ) &&
            Immunizations::checkVzv($client, $beginDate, $endDate) &&
            Immunizations::checkHepB($client, $beginDate, $endDate) &&
            Immunizations::checkPheumococcal($client, $beginDate, $endDate) ) {
            return true;
        }
        
        return false;
    }
}
