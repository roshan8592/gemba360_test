<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
class AmcResult implements RsResultIF
{
    public $rule;
//    public $numeratorLabel;
//    public $populationLabel;

    public $totalClients; // Total number of clients considered
    public $clientsInPopulation; // Number of clients that pass filter
    public $clientsExcluded; // Number of clients that are excluded
    public $clientsIncluded; // Number of clients that pass target
    public $percentage; // Calculated percentage

    public function __construct($rowRule, $totalClients, $clientsInPopulation, $clientsExcluded, $clientsIncluded, $percentage)
    {
        $this->rule = $rowRule;
//        $this->numeratorLabel = $numeratorLabel;
//        $this->populationLabel = $populationLabel;
        $this->totalClients = $totalClients;
        $this->clientsInPopulation = $clientsInPopulation;
        $this->clientsExcluded = $clientsExcluded;
        $this->clientsIncluded = $clientsIncluded;
        $this->percentage = $percentage;

        // If itemization is turned on, then record the itemized_test_id
        if ($GLOBALS['report_itemizing_temp_flag_and_id']) {
            $this->itemized_test_id = array('itemized_test_id' => $GLOBALS['report_itemized_test_id_iterator']);
        }
    }

    public function format()
    {
        $rowFormat = array(
            'is_main'=>true, // TO DO: figure out way to do this when multiple groups.
//            'population_label' => $this->populationLabel,
//            'numerator_label' => $this->numeratorLabel,
            'total_clients' => $this->totalClients,
            'excluded' => $this->clientsExcluded,
            'pass_filter' => $this->clientsInPopulation,
            'pass_target' => $this->clientsIncluded,
            'percentage' => $this->percentage );
            $rowFormat = array_merge($rowFormat, $this->rule);

        // If itemization is turned on, then record the itemized_test_id
        if ($GLOBALS['report_itemizing_temp_flag_and_id']) {
            $rowFormat = array_merge($rowFormat, $this->itemized_test_id);
        }
        
        return $rowFormat;
    }
}
