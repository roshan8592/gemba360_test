<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
class Immunizations
{
    public static function checkDtap(CqmClient $client, $beginDate, $endDate)
    {
        $dobPlus42Days = date('Y-m-d 00:00:00', strtotime('+42 day', strtotime($client->dob)));
        $dobPlus2Years = date('Y-m-d 00:00:00', strtotime('+2 year', strtotime($client->dob)));
        $fourCount = array( Medication::OPTION_COUNT => 4, Medication::OPTION_UNIQUE_DATES => true );
        if (Helper::checkMed(Medication::DTAP_VAC, $client, $dobPlus42Days, $dobPlus2Years, $fourCount) &&
            !( Helper::checkAllergy(Allergy::DTAP_VAC, $client, $client->dob, $endDate) ||
               Helper::checkDiagActive(Diagnosis::ENCEPHALOPATHY, $client, $beginDate, $endDate) ||
               Helper::checkDiagActive(Diagnosis::PROG_NEURO_DISORDER, $client, $beginDate, $endDate) ) ) {
            return true;
        }
        
        return false;
    }
    
    public static function checkIpv(CqmClient $client, $beginDate, $endDate)
    {
        $dobPlus42Days = date('Y-m-d 00:00:00', strtotime('+42 day', strtotime($client->dob)));
        $dobPlus2Years = date('Y-m-d 00:00:00', strtotime('+2 year', strtotime($client->dob)));
        $threeCount = array( Medication::OPTION_COUNT => 3 );
        if (Helper::checkMed(Medication::IPV, $client, $dobPlus42Days, $dobPlus2Years, $threeCount) &&
            !( Helper::checkAllergy(Allergy::IPV, $client, $client->dob, $endDate) ||
               Helper::checkAllergy(Allergy::NEOMYCIN, $client, $client->dob, $endDate) ||
               Helper::checkAllergy(Allergy::STREPTOMYCIN, $client, $client->dob, $endDate) ) ) {
            return true;
        }

        return false;
    }
    
    public static function checkMmr(CqmClient $client, $beginDate, $endDate)
    {
        $dobPlus1Year = date('Y-m-d 00:00:00', strtotime('+1 year', strtotime($client->dob)));
        $dobPlus2Years = date('Y-m-d 00:00:00', strtotime('+2 year', strtotime($client->dob)));
        $dateMinus2Years = date('Y-m-d 00:00:00', strtotime('-2 year', strtotime($endDate)));
        if (Helper::checkMed(Medication::MMR, $client, $dobPlus1Year, $dobPlus2Years) ||
             ( Helper::checkMed(Medication::MUMPS_VAC, $client, $client->dob, $dobPlus2Years) &&
               !Helper::checkAllergy(Allergy::MUMPS_VAC, $client, $client->dob, $endDate) &&
               Helper::checkMed(Medication::MEASLES_VAC, $client, $client->dob, $dobPlus2Years) &&
               !Helper::checkAllergy(Allergy::MEASLES_VAC, $client, $client->dob, $endDate) &&
               Helper::checkMed(Medication::RUBELLA_VAC, $client, $client->dob, $dobPlus2Years) &&
               !Helper::checkAllergy(Allergy::RUBELLA_VAC, $client, $client->dob, $endDate) ) ||
             ( Helper::checkDiagResolved(Diagnosis::MEASLES, $client, $client->dob, $endDate) &&
               Helper::checkMed(Medication::MUMPS_VAC, $client, $client->dob, $dobPlus2Years) &&
               !Helper::checkAllergy(Allergy::MUMPS_VAC, $client, $client->dob, $endDate) &&
               Helper::checkMed(Medication::RUBELLA_VAC, $client, $client->dob, $dobPlus2Years) &&
               !Helper::checkAllergy(Allergy::RUBELLA_VAC, $client, $client->dob, $endDate) ) ||
             ( Helper::checkDiagResolved(Diagnosis::MUMPS, $client, $client->dob, $endDate) &&
               Helper::checkMed(Medication::MEASLES_VAC, $client, $client->dob, $dobPlus2Years) &&
               !Helper::checkAllergy(Allergy::MEASLES_VAC, $client, $client->dob, $endDate) &&
               Helper::checkMed(Medication::RUBELLA_VAC, $client, $client->dob, $dobPlus2Years) &&
               !Helper::checkAllergy(Allergy::RUBELLA_VAC, $client, $client->dob, $endDate) ) ||
             ( Helper::checkDiagResolved(Diagnosis::RUBELLA, $client, $client->dob, $endDate) &&
               Helper::checkMed(Medication::MUMPS_VAC, $client, $client->dob, $dobPlus2Years) &&
               !Helper::checkAllergy(Allergy::MUMPS_VAC, $client, $client->dob, $endDate) &&
               Helper::checkMed(Medication::MEASLES_VAC, $client, $client->dob, $dobPlus2Years) &&
               !Helper::checkAllergy(Allergy::MEASLES_VAC, $client, $client->dob, $endDate) ) &&
              !( Helper::checkDiagActive(Diagnosis::CANCER_LYMPH_HIST, $client, $beginDate, $endDate) ||
                 Helper::checkDiagInactive(Diagnosis::CANCER_LYMPH_HIST, $client, $beginDate, $endDate) ||
                 Helper::checkDiagActive(Diagnosis::ASYMPTOMATIC_HIV, $client, $beginDate, $endDate) ||
                 Helper::checkDiagActive(Diagnosis::MULT_MYELOMA, $client, $beginDate, $endDate) ||
                 Helper::checkDiagActive(Diagnosis::LUKEMIA, $client, $beginDate, $endDate) ||
                 Helper::checkAllergy(Allergy::MMR, $client, $client->dob, $dateMinus2Years) ||
                 Helper::checkDiagActive(Diagnosis::IMMUNODEF, $client, $beginDate, $endDate) ) ) {
            return true;
        }
        
        return false;
    }
    
    public static function checkHib(CqmClient $client, $beginDate, $endDate)
    {
        $options = array( Medication::OPTION_COUNT => 2 );
        $dobPlus42Days = date('Y-m-d 00:00:00', strtotime('+42 day', strtotime($client->dob)));
        $dobPlus2Years = date('Y-m-d 00:00:00', strtotime('+2 year', strtotime($client->dob)));
        if (Helper::checkMed(Medication::HIB, $client, $dobPlus42Days, $dobPlus2Years, $options) &&
            !Helper::checkAllergy(Allergy::HIB, $client, $client->dob, $endDate) ) {
            return true;
        }

        return false;
    }
    
    public static function checkHepB(CqmClient $client, $beginDate, $endDate)
    {
        $options = array( Medication::OPTION_COUNT => 3 );
        $dobPlus2Years = date('Y-m-d 00:00:00', strtotime('+2 year', strtotime($client->dob)));
        if (Helper::checkMed(Medication::HEP_B_VAC, $client, $client->dob, $dobPlus2Years, $options) ||
            Helper::checkDiagResolved(Diagnosis::HEP_B, $client, $client->dob, $endDate) &&
            !( Helper::checkAllergy(Allergy::HEP_B_VAC, $client, $client->dob, $endDate) ||
               Helper::checkAllergy(Allergy::BAKERS_YEAST, $client, $client->dob, $endDate) ) ) {
            return true;
        }
        
        return false;
    }
    
    public static function checkVzv(CqmClient $client, $beginDate, $endDate)
    {
        $options = array( Medication::OPTION_COUNT => 1 );
        $dobPlus2Years = date('Y-m-d 00:00:00', strtotime('+2 year', strtotime($client->dob)));
        if (Helper::checkMed(Medication::VZV, $client, $client->dob, $dobPlus2Years, $options) ||
             ( Helper::checkDiagResolved(Diagnosis::VZV, $client, $client->dob, $endDate) &&
               !( Helper::checkDiagActive(Diagnosis::CANCER_LYMPH_HIST, $client, $beginDate, $endDate) ||
                  Helper::checkDiagInactive(Diagnosis::CANCER_LYMPH_HIST, $client, $beginDate, $endDate) ||
                  Helper::checkDiagActive(Diagnosis::ASYMPTOMATIC_HIV, $client, $beginDate, $endDate) ||
                  Helper::checkDiagActive(Diagnosis::MULT_MYELOMA, $client, $beginDate, $endDate) ||
                  Helper::checkDiagActive(Diagnosis::LUKEMIA, $client, $beginDate, $endDate) ||
                  Helper::checkAllergy(Allergy::VZV, $client, $client->dob, $endDate) ||
                  Helper::checkDiagActive(Diagnosis::IMMUNODEF, $client, $beginDate, $endDate) ) ) ) {
            return true;
        }
        
        return false;
    }
    
    public static function checkPheumococcal(CqmClient $client, $beginDate, $endDate)
    {
        $options = array( Medication::OPTION_COUNT => 4 );
        $dobPlus42Days = date('Y-m-d 00:00:00', strtotime('+42 day', strtotime($client->dob)));
        $dobPlus2Years = date('Y-m-d 00:00:00', strtotime('+2 year', strtotime($client->dob)));
        if (Helper::checkMed(Medication::PNEUMOCOCCAL_VAC, $client, $dobPlus42Days, $dobPlus2Years, $options) &&
            !Helper::checkAllergy(Allergy::PNEUM_VAC, $client) ) {
            return true;
        }
        
        return false;
    }
    
    public static function checkHepA(CqmClient $client, $beginDate, $endDate)
    {
        $options = array( Medication::OPTION_COUNT => 2 );
        $dobPlus42Days = date('Y-m-d 00:00:00', strtotime('+42 day', strtotime($client->dob)));
        $dobPlus2Years = date('Y-m-d 00:00:00', strtotime('+2 year', strtotime($client->dob)));
        if (Helper::checkMed(Medication::HEP_A_VAC, $client, $dobPlus42Days, $dobPlus2Years, $options) ||
            ( Helper::checkDiagResolved(Diagnosis::HEP_A, $client, $client->dob, $endDate) &&
              !Helper::checkAllergy(Allergy::HEP_A_VAC, $client, $client->dob, $endDate) ) ) {
            return true;
        }
        
        return false;
    }
    
    public static function checkRotavirus(CqmClient $client, $beginDate, $endDate)
    {
        $options = array( Medication::OPTION_COUNT => 4 );
        $dobPlus42Days = date('Y-m-d 00:00:00', strtotime('+42 day', strtotime($client->dob)));
        $dobPlus2Years = date('Y-m-d 00:00:00', strtotime('+2 year', strtotime($client->dob)));
        if (Helper::checkMed(Medication::ROTAVIRUS_VAC, $client, $dobPlus42Days, $dobPlus2Years, $options) &&
            !Helper::checkAllergy(Allergy::ROTAVIRUS_VAC, $client, $client->dob, $endDate) ) {
            return true;
        }

        return false;
    }
    
    public static function checkInfluenza(CqmClient $client, $beginDate, $endDate)
    {
        $options = array( Medication::OPTION_COUNT => 2 );
        $dobPlus180Days = date('Y-m-d 00:00:00', strtotime('+180 day', strtotime($client->dob)));
        $dobPlus2Years = date('Y-m-d 00:00:00', strtotime('+2 year', strtotime($client->dob)));
        if (Helper::checkMed(Medication::INFLUENZA_VAC, $client, $dobPlus180Days, $dobPlus2Years, $options) &&
            !( Helper::checkAllergy(Allergy::INFLUENZA_VAC, $client, $client->dob, $endDate) ||
               Helper::checkDiagActive(Diagnosis::CANCER_LYMPH_HIST, $client, $client->dob, $endDate) ||
               Helper::checkDiagInactive(Diagnosis::CANCER_LYMPH_HIST, $client, $client->dob, $endDate) ||
               Helper::checkDiagActive(Diagnosis::ASYMPTOMATIC_HIV, $client, $client->dob, $endDate) ||
               Helper::checkDiagActive(Diagnosis::MULT_MYELOMA, $client, $client->dob, $endDate) ||
               Helper::checkDiagActive(Diagnosis::LUKEMIA, $client, $client->dob, $endDate) ||
               Helper::checkDiagActive(Diagnosis::IMMUNODEF, $client, $client->dob, $endDate) ) ) {
            return true;
        }
        
        return false;
    }
    
    public static function checkRotavirus_2014(CqmClient $client, $beginDate, $endDate)
    {
        $options = array( Medication::OPTION_COUNT => 2 );
        $dobPlus42Days = date('Y-m-d 00:00:00', strtotime('+42 day', strtotime($client->dob)));
        $dobPlus2Years = date('Y-m-d 00:00:00', strtotime('+2 year', strtotime($client->dob)));
        if (Helper::checkMed(Medication::ROTAVIRUS_VAC, $client, $dobPlus42Days, $dobPlus2Years, $options) &&
            !Helper::checkAllergy(Allergy::ROTAVIRUS_VAC, $client, $client->dob, $endDate) ) {
            return true;
        }

        return false;
    }
}
