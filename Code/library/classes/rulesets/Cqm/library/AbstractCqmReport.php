<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
require_once(dirname(__FILE__)."/../../../../clinical_rules.php");

abstract class AbstractCqmReport implements RsReportIF
{
    protected $_cqmPopulation;

    protected $_resultsArray = array();

    protected $_rowRule;
    protected $_ruleId;
    protected $_beginMeasurement;
    protected $_endMeasurement;

    public function __construct(array $rowRule, array $clientIdArray, $dateTarget)
    {
        // require all .php files in the report's sub-folder
        $className = get_class($this);
        foreach (glob(dirname(__FILE__)."/../reports/".$className."/*.php") as $filename) {
            require_once($filename);
        }

        // require common .php files
        foreach (glob(dirname(__FILE__)."/../reports/common/*.php") as $filename) {
            require_once($filename);
        }

        // require clinical types
        foreach (glob(dirname(__FILE__)."/../../../ClinicalTypes/*.php") as $filename) {
            require_once($filename);
        }

        $this->_cqmPopulation = new CqmPopulation($clientIdArray);
        $this->_rowRule = $rowRule;
        $this->_ruleId = isset($rowRule['id']) ? $rowRule['id'] : '';
        // Calculate measurement period
        $tempDateArray = explode("-", $dateTarget);
        $tempYear = $tempDateArray[0];
        $this->_beginMeasurement = $tempDateArray[0] . "-01-01 00:00:00";
        $this->_endMeasurement = $tempDateArray[0] . "-12-31 23:59:59";
    }

    abstract public function createPopulationCriteria();

    public function getBeginMeasurement()
    {
        return $this->_beginMeasurement;
    }

    public function getEndMeasurement()
    {
        return $this->_endMeasurement;
    }
    
    public function getResults()
    {
        return $this->_resultsArray;
    }

    public function execute()
    {
        $populationCriterias = $this->createPopulationCriteria();
        if (!is_array($populationCriterias)) {
            $tmpPopulationCriterias = array();
            $tmpPopulationCriterias[]= $populationCriterias;
            $populationCriterias = $tmpPopulationCriterias;
        }

        foreach ($populationCriterias as $populationCriteria) {
            // If itemization is turned on, then iterate the rule id iterator
            if ($GLOBALS['report_itemizing_temp_flag_and_id']) {
                $GLOBALS['report_itemized_test_id_iterator']++;
            }

            if ($populationCriteria instanceof CqmPopulationCrtiteriaFactory) {
                $initialClientPopulationFilter = $populationCriteria->createInitialClientPopulation();
                if (!$initialClientPopulationFilter instanceof CqmFilterIF) {
                    throw new Exception("InitialClientPopulation must be an instance of CqmFilterIF");
                }

                $denominator = $populationCriteria->createDenominator();
                if (!$denominator instanceof CqmFilterIF) {
                    throw new Exception("Denominator must be an instance of CqmFilterIF");
                }

                $numerators = $populationCriteria->createNumerators();
                if (!is_array($numerators)) {
                    $tmpNumerators = array();
                    $tmpNumerators[]= $numerators;
                    $numerators = $tmpNumerators;
                }

                $exclusion = $populationCriteria->createExclusion();
                if (!$exclusion instanceof CqmFilterIF) {
                    throw new Exception("Exclusion must be an instance of CqmFilterIF");
                }

                //Denominator Exception added
                $denomExept = false;
                if (method_exists($populationCriteria, 'createDenominatorException')) {
                    $denomExept = true;
                }

                $totalClients = count($this->_cqmPopulation);
                $initialClientPopulation = 0;
                $denominatorClientPopulation = 0;
                $exclusionsClientPopulation = 0;
                $exceptionsClientPopulation = 0; // this is a bridge to no where variable (calculated but not used below). Will keep for now, though.
                $patExclArr = array();
                $patExceptArr = array();
                $numeratorClientPopulations = $this->initNumeratorPopulations($numerators);
                foreach ($this->_cqmPopulation as $client) {
                    if (!$initialClientPopulationFilter->test($client, $this->_beginMeasurement, $this->_endMeasurement)) {
                        continue;
                    }
                        
                    $initialClientPopulation++;

                    // If itemization is turned on, then record the "Initial Client population" item
                    if ($GLOBALS['report_itemizing_temp_flag_and_id']) {
                        insertItemReportTracker($GLOBALS['report_itemizing_temp_flag_and_id'], $GLOBALS['report_itemized_test_id_iterator'], 3, $client->id);
                    }
                    
                    if (!$denominator->test($client, $this->_beginMeasurement, $this->_endMeasurement)) {
                        continue;
                    }
                            
                    $denominatorClientPopulation++;

                    if ($exclusion->test($client, $this->_beginMeasurement, $this->_endMeasurement)) {
                        $exclusionsClientPopulation++;
                        $patExclArr[] = $client->id;
                    }

                    //Denominator Exception added
                    if ($denomExept) {
                        $denom_exception = $populationCriteria->createDenominatorException();
                        if ($denom_exception->test($client, $this->_beginMeasurement, $this->_endMeasurement)) {
                            $exceptionsClientPopulation++; // this is a bridge to no where variable (not used below). Will keep for now, though.
                            $patExceptArr[] = $client->id;
                        }
                    }
                     
                    foreach ($numerators as $numerator) {
                        $this->testNumerator($client, $numerator, $numeratorClientPopulations);
                    }
                }
                
                // tally results, run exclusion on each numerator
                $pass_filt = $denominatorClientPopulation;
                $exclude_filt = $exclusionsClientPopulation;
                foreach ($numeratorClientPopulations as $title => $pass_targ) {
                    if (count($patExclArr) > 0) {
                        foreach ($patExclArr as $patVal) {
                            // If itemization is turned on, then record the "excluded" item
                            if ($GLOBALS['report_itemizing_temp_flag_and_id']) {
                                insertItemReportTracker($GLOBALS['report_itemizing_temp_flag_and_id'], $GLOBALS['report_itemized_test_id_iterator'], 2, $patVal, $title);
                            }
                        }
                    }

                    if (count($patExceptArr) > 0) {
                        foreach ($patExceptArr as $patVal) {
                            // If itemization is turned on, then record the "exception" item
                            if ($GLOBALS['report_itemizing_temp_flag_and_id']) {
                                insertItemReportTracker($GLOBALS['report_itemizing_temp_flag_and_id'], $GLOBALS['report_itemized_test_id_iterator'], 4, $patVal, $title);
                            }
                        }
                    }

                    $percentage = calculate_percentage($pass_filt, $exclude_filt, $pass_targ);
                    $this->_resultsArray[]= new CqmResult(
                        $this->_rowRule,
                        $title,
                        $populationCriteria->getTitle(),
                        $totalClients,
                        $pass_filt,
                        $exclude_filt,
                        $pass_targ,
                        $percentage,
                        $initialClientPopulation,
                        $exceptionsClientPopulation
                    );
                }
            }
        }

        return $this->_resultsArray;
    }
    
    private function initNumeratorPopulations(array $numerators)
    {
        $numeratorClientPopulations = array();
        foreach ($numerators as $numerator) {
            $numeratorClientPopulations[$numerator->getTitle()] = 0;
        }

        return $numeratorClientPopulations;
    }

    private function testNumerator($client, $numerator, &$numeratorClientPopulations)
    {
        if ($numerator instanceof CqmFilterIF) {
            if ($numerator->test($client, $this->_beginMeasurement, $this->_endMeasurement)) {
                $numeratorClientPopulations[$numerator->getTitle()]++;

                // If itemization is turned on, then record the "passed" item
                if ($GLOBALS['report_itemizing_temp_flag_and_id']) {
                    insertItemReportTracker($GLOBALS['report_itemizing_temp_flag_and_id'], $GLOBALS['report_itemized_test_id_iterator'], 1, $client->id, $numerator->getTitle());
                }
            } else {
                // If itemization is turned on, then record the "failed" item
                if ($GLOBALS['report_itemizing_temp_flag_and_id']) {
                    insertItemReportTracker($GLOBALS['report_itemizing_temp_flag_and_id'], $GLOBALS['report_itemized_test_id_iterator'], 0, $client->id, $numerator->getTitle());
                }
            }
        } else {
            throw new Exception("Numerator must be an instance of CqmFilterIF");
        }
    }
}
