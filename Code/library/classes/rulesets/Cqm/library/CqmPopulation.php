<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
require_once("CqmClient.php");
/*  Defines a population of clients
 *
 */
class CqmPopulation extends RsPopulation
{
    /*
     * initialize the client population
     */
    public function __construct(array $clientIdArray)
    {
        foreach ($clientIdArray as $clientId) {
            $this->_clients[]= new CqmClient($clientId);
        }
    }

    /*
     * ArrayAccess Interface
     */
    public function offsetSet($offset, $value)
    {
        if ($value instanceof CqmClient) {
            if ($offset == "") {
                $this->_clients[] = $value;
            } else {
                $this->_clients[$offset] = $value;
            }
        } else {
            throw new Exception("Value must be an instance of CqmClient");
        }
    }
}
