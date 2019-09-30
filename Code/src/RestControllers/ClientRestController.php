<?php
/**
 * ClientRestController
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Matthew Vita <matthewvita48@gmail.com>
 * @copyright Copyright (c) 2018 Matthew Vita <matthewvita48@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


namespace OpenEMR\RestControllers;

use OpenEMR\Services\ClientService;
use OpenEMR\RestControllers\RestControllerHelper;

class ClientRestController
{
    private $clientService;

    public function __construct($pid)
    {
        $this->clientService = new ClientService();
        $this->clientService->setPid($pid);
    }

    public function post($data)
    {
        $validationResult = $this->clientService->validate($data);

        $validationHandlerResult = RestControllerHelper::validationHandler($validationResult);
        if (is_array($validationHandlerResult)) {
            return $validationHandlerResult; }

        $serviceResult = $this->clientService->insert($data);
        return RestControllerHelper::responseHandler($serviceResult, array("pid" => $serviceResult), 201);
    }

    public function put($pid, $data)
    {
        $validationResult = $this->clientService->validate($data);

        $validationHandlerResult = RestControllerHelper::validationHandler($validationResult);
        if (is_array($validationHandlerResult)) {
            return $validationHandlerResult; }

        $serviceResult = $this->clientService->update($pid, $data);
        return RestControllerHelper::responseHandler($serviceResult, array("pid" => $pid), 200);
    }

    public function getOne()
    {
        $serviceResult = $this->clientService->getOne();
        return RestControllerHelper::responseHandler($serviceResult, null, 200);
    }

    public function getAll($search)
    {
        $serviceResult = $this->clientService->getAll(array(
            'fname' => $search['fname'],
            'lname' => $search['lname'],
            'dob' => $search['dob']
        ));

        return RestControllerHelper::responseHandler($serviceResult, null, 200);
    }
}
