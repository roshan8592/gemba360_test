<?php
// Copyright (C) 2011 Ken Chapple <ken@mi-squared.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
require_once("RsClient.php");
/*  Defines a population of clients
 *
 */
class RsPopulation implements Countable, Iterator, ArrayAccess
{
    protected $_clients = array();

    /*
     * initialize the client population
     */
    public function __construct(array $clientIdArray)
    {
        foreach ($clientIdArray as $clientId) {
            $this->_clients[]= new RsClient($clientId);
        }
    }

    /*
     * Countable Interface
     */
    public function count()
    {
        return count($this->_clients);
    }

    /*
     * Iterator Interface
     */
    public function rewind()
    {
        reset($this->_clients);
    }

    public function current()
    {
        return current($this->_clients);
    }

    public function key()
    {
        return key($this->_clients);
    }

    public function next()
    {
        return next($this->_clients);
    }

    public function valid()
    {
        return $this->current() !== false;
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
            throw new Exception("Value must be an instance of RsClient");
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->_clients[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_clients[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_clients[$offset]) ? $this->container[$offset] : null;
    }
}
