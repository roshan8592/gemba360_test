<?php
/**
 * This file is part of OpenEMR.
 *
 * @link https://github.com/openemr/openemr/tree/master
 * @license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */
namespace ClientFilterEventHookTest;

use OpenEMR\Events\Appointments\AppointmentsFilterEvent;
use OpenEMR\Events\ClientDemographics\UpdateEvent;
use OpenEMR\Events\ClientDemographics\ViewEvent;
use OpenEMR\Events\ClientFinder\ClientFinderFilterEvent;
use OpenEMR\Services\UserService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;

/**
 * Module for creating a blacklist on the client finder, which can restrict certain
 * users from accessing certain clients
 *
 * @package ClientFilterEventHookTest
 * @author Ken Chapple <ken@mi-squared.com>
 * @copyright Copyright (c) 2019 Ken Chapple <ken@mi-squared.com>
 */
class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,

                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * load global variables foe every controllers
     * @param ModuleManager $manager
     */
    public function init(ModuleManager $manager)
    {
    }

    /**
     * @param MvcEvent $e
     *
     * Register our event listeners here
     */
    public function onBootstrap(MvcEvent $e)
    {
        // Get application service manager and get instance of event dispatcher
        $serviceManager = $e->getApplication()->getServiceManager();
        $oemrDispatcher = $serviceManager->get(EventDispatcherInterface::class);

        // listen for the filter event in the client finder (hook located in main/finder/dynamic_finder_ajax.php)
        $oemrDispatcher->addListener(ClientFinderFilterEvent::EVENT_HANDLE, [$this, 'filterClientFinderByBlacklist']);

        // listen for filter event in the appointments.inc.php library
        $oemrDispatcher->addListener(AppointmentsFilterEvent::EVENT_HANDLE, [$this, 'filterAppointmentsByBlacklist']);

        // listen for view and update events on the client demographics screen (hooks located in
        // interface/client_file/summary/demogrphics.php and
        // interface/client_file/summary/demogrphics_full.php
        $oemrDispatcher->addListener(ViewEvent::EVENT_HANDLE, [$this, 'checkBlacklistForViewAuth']);
        $oemrDispatcher->addListener(UpdateEvent::EVENT_HANDLE, [$this, 'checkBlacklistForUpdateAuth']);
    }

    /**
     * @param $username
     * @return array
     *
     * Load the list of clients that this user cannot access from our blacklist file
     */
    public function getBlacklist($username)
    {
        $blacklist = include __DIR__."/config/blacklist.php";
        $pids = [];
        foreach ($blacklist as $item) {
            if ($username == $item['username']) {
                $pids = array_merge($pids, $item['blacklist']);
            }
        }

        return $pids;
    }

    /**
     * @param AppointmentsFilterEvent $appointmentsFilterEvent
     * @return AppointmentsFilterEvent
     *
     * Handler for the appointment fetching filter, which is used in client tracker and other
     * places.
     *
     * This filter's query uses SQL binding.
     */
    public function filterAppointmentsByBlacklist(AppointmentsFilterEvent $appointmentsFilterEvent)
    {
        $userService = new UserService();
        $user = $userService->getCurrentlyLoggedInUser();
        $clientsToHide = $this->getBlacklist($user->getUsername());
        if (count($clientsToHide)) {
            $filterString = "(p.pid IS NULL OR p.pid NOT IN (";
            foreach ($clientsToHide as $clientToHide) {
                $filterString .= "?,";
            }
            $filterString = rtrim($filterString, ",");
            $filterString .= "))";
            $boundFilter = $appointmentsFilterEvent->getBoundFilter();
            $boundFilter->setFilterClause($filterString);
            $boundFilter->setBoundValues($clientsToHide);
        }

        return $appointmentsFilterEvent;
    }

    /**
     * @param ClientFinderFilterEvent $event
     * @return ClientFinderFilterEvent
     *
     * Handler for the client finder filter. This function looks at the blacklist
     * and hides the specified clients.
     *
     * This filter does not use binding
     */
    public function filterClientFinderByBlacklist(ClientFinderFilterEvent $event)
    {
        $userService = new UserService();
        $user = $userService->getCurrentlyLoggedInUser();
        $clientsToHide = $this->getBlacklist($user->getUsername());

        // If there are clients to hide from this user, build a filter
        if (count($clientsToHide)) {
            $filterString = "(";
            foreach ($clientsToHide as $clientToHide) {
                $filterString .= "?,";
            }
            $filterString = rtrim($filterString, ",");
            $filterString .= ")";
            $where = " client_data.pid NOT IN $filterString ";

            // Set the query part we constructed as the custom where, which will be appended to client filter query
            $boundFilter = $event->getBoundFilter();
            $boundFilter->setFilterClause($where);
            $boundFilter->setBoundValues($clientsToHide);
        }

        return $event;
    }

    /**
     * @param ViewEvent $event
     *
     * Handler for the view event in client demographics. If the client is in the logged-in user's
     * blacklist, they will not have access.
     */
    public function checkBlacklistForViewAuth(ViewEvent $event)
    {
        $userService = new UserService();
        $user = $userService->getCurrentlyLoggedInUser();
        $clientsToHide = $this->getBlacklist($user->getUsername());
        if (in_array($event->getPid(), $clientsToHide)) {
            $event->setAuthorized(false);
        }
    }

    /**
     * @param UpdateEvent $event
     *
     * Handler for the update event in client demographics. If the client is in the logged-in user's
     * blacklist, they will not have access.
     */
    public function checkBlacklistForUpdateAuth(UpdateEvent $event)
    {
        $userService = new UserService();
        $user = $userService->getCurrentlyLoggedInUser();
        $clientsToHide = $this->getBlacklist($user->getUsername());
        if (in_array($event->getPid(), $clientsToHide)) {
            $event->setAuthorized(false);
        }
    }
}
