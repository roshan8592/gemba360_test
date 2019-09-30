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
namespace Clientvalidation;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Router\Http\Segment;
use Clientvalidation\Controller\ClientvalidationController;
use Clientvalidation\Model\ClientDataTable;
use Clientvalidation\Model\ClientData;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

return array(

    /* declare all controllers */
    'controllers' => array(
        'factories' => [
            ClientvalidationController::class =>  function (ContainerInterface $container, $requestedName) {
                return new ClientvalidationController($container->get(ClientDataTable::class));
            }
        ],
    ),

    /**
     * routing configuration.
     * for more option and details - http://zf2.readthedocs.io/en/latest/in-depth-guide/understanding-routing.html?highlight=routing
     */
    'router' => array(
        'routes' => array(
            'clientvalidation' => array(
                'type'    => Segment::class,
                'options' => array(
                    'route'    => '/clientvalidation[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => ClientvalidationController::class,
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),


    'view_manager' => array(
        'template_path_stack' => array(
            'clientvalidation' => __DIR__ . '/../view',
        ),
    ),

    'service_manager' => [
        'factories' => array(
            ClientDataTable::class =>  function (ContainerInterface $container, $requestedName) {
                $dbAdapter = $container->get(\Zend\Db\Adapter\Adapter::class);
                $resultSetPrototype = new ResultSet();
                $resultSetPrototype->setArrayObjectPrototype(new ClientData());
                $tableGateway = new TableGateway('client_data', $dbAdapter, null, $resultSetPrototype);
                $table = new ClientDataTable($tableGateway);
                return $table;
            }
        ),
    ]
);
