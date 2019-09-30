<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
class DiabetesDenominator implements CqmFilterIF
{
    public function getTitle()
    {
        return "Denominator";
    }
    
    public function test(CqmClient $client, $beginDate, $endDate)
    {
        // TODO how to check for these medication types?
        $beginMinus2Years = strtotime('-2 year', strtotime($beginDate));
        if (Helper::checkMed(Medication::DISP_DIABETES, $client, $beginMinus2Years, $endDate) ||
            Helper::checkMed(Medication::ORDER_DIABETES, $client, $beginMinus2Years, $endDate) ||
            Helper::checkMed(Medication::ACTIVE_DIABETES, $client, $beginMinus2Years, $endDate) ||
            ( Helper::checkDiagActive(Diagnosis::DIABETES, $client, $beginMinus2Years, $endDate) &&
                ( Helper::checkEncounter(Encounter::ENC_ACUTE_INP_OR_ED, $client, $beginDate, $endDate) ||
                  Helper::checkEncounter(Encounter::ENC_NONAC_INP_OUT_OR_OPTH, $client, $beginDate, $endDate, array( Encounter::OPTION_ENCOUNTER_COUNT => 2 )) ) ) ) {
                      return true;
        }
        
        return false;
    }
}
