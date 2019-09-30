<?php
/**
 * This file is part of OpenEMR.
 *
 * @link https://github.com/openemr/openemr/tree/master
 * @license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace OpenEMR\Events\ClientDemographics;

use Symfony\Component\EventDispatcher\Event;

/**
 *  Event object for restricting access to users viewing clients' demographics screen
 *
 * @package OpenEMR\Events
 * @subpackage ClientDemographics
 * @author Ken Chapple <ken@mi-squared.com>
 * @copyright Copyright (c) 2019 Ken Chapple <ken@mi-squared.com>
 */
class ViewEvent extends Event
{
    /**
     * The checkViewAuth event occurs when a user attempts to view a
     * client record from the demographics screen
     */
    const EVENT_HANDLE = 'clientDemographics.view';

    /**
     * @var null|integer
     *
     * Represents the client we are considering access to
     */
    private $pid = null;

    /**
     * @var bool
     *
     * true if the  user is authorized, false ow
     */
    private $authorized = true;

    /**
     * UpdateEvent constructor.
     *
     * @param integer $pid Client Identifier
     */
    public function __construct($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return int|null
     *
     * Get the client identifier of the client we're attempting to view
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return bool
     *
     * Is user authorized to view client?
     */
    public function authorized()
    {
        return $this->authorized;
    }

    /**
     * @param bool $authorized
     *
     * Use this function to set whether or not this user is authorized to view client
     */
    public function setAuthorized($authorized)
    {
        $this->authorized = $authorized;
    }
}
