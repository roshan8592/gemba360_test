<?php

/* +-----------------------------------------------------------------------------+
* Copyright 2016 matrix israel
* LICENSE: This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 3
* of the License, or (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program. If not, see
* http://www.gnu.org/licenses/licenses.html#GPL
*    @author  Dror Golan <drorgo@matrix.co.il>
* +------------------------------------------------------------------------------+
 *
 */
namespace Clientvalidation\Controller;

use Clientvalidation\Model\ClientData;
use Zend\Json\Server\Exception\ErrorException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Listener\Listener;
use Clientvalidation\Model\ClientDataTable;
use Error;

class ClientvalidationController extends BaseController
{

    /**
     * @var ClientDataTable
     */
    private $ClientDataTable;

    /**
     * ClientvalidationController constructor.
     */
    public function __construct(ClientDataTable $dataTable)
    {
        parent::__construct();
        $this->listenerObject = new Listener;
        $this->ClientDataTable = $dataTable;
        //todo add permission of admin
    }

    private function getAllRealatedClients()
    {
        //Collect all of the data received from the new client form
        $clientParams = $this->getRequestedParamsArray();
        if (isset($clientParams["closeBeforeOpening"])) {
            $closeBeforeOpening = $clientParams["closeBeforeOpening"];
        } else {
            $closeBeforeOpening ='';
        }

        //clean the mf_
        foreach ($clientParams as $key => $item) {
                $keyArr=explode("mf_", $key);
                $clientParams[$keyArr[1]]=$item;
                unset($clientParams[$key]);
        }


        $clientData=$this->getClientDataTable()->getClients($clientParams);


        if (isset($clientData)) {
            foreach ($clientData as $data) {
                if ($data['pubpid']==$clientParams['pubpid']) {
                    return array("status"=>"failed","list"=>$clientData,"closeBeforeOpening"=>$closeBeforeOpening);
                }
            }

            return array("status"=>"ok","list"=>$clientData,"closeBeforeOpening"=>$closeBeforeOpening);
        }
    }
    /**
     * @return \Zend\Stdlib\ResponseInterface the index action
     */

    public function indexAction()
    {

        $this->getJsFiles();
        $this->getCssFiles();
        $this->layout()->setVariable('jsFiles', $this->jsFiles);
        $this->layout()->setVariable('cssFiles', $this->cssFiles);
        $this->layout()->setVariable("title", $this->listenerObject->z_xl("Client validation"));
        $this->layout()->setVariable("translate", $this->translate);

         $relatedClients =  $this->getAllRealatedClients();



        return array("related_clients"=>$relatedClients['list'],"translate"=>$this->translate,"closeBeforeOpening"=>$relatedClients['closeBeforeOpening'],"status"=>$relatedClients['status']);
    }
    /**
     * get instance of Clientvalidation
     * @return array|object
     */
    private function getClientDataTable()
    {

        return $this->ClientDataTable;
    }
}
