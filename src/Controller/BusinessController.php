<?php
/**
 * Created by PhpStorm.
 * User: Eduardo Luttinger
 * Date: 02/05/2016
 * Time: 09:28 AM
 */

namespace App\Controller;

use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Util\ReaxiumApiMessages;


class BusinessController extends ReaxiumAPIController
{

    /**
     * @api {post} /Business/createBusiness create a business in our reaxium system
     * @apiName CreateBusiness
     * @apiGroup Business
     *
     * @apiParamExample {json} Save-Request:
     *
     *      {"ReaxiumParameters": {
     *          "Business": {
     *              "business_id": null,
     *              "business_name": "Luis Edgardo Eguie Arocha",
     *              "business_id_number": "J-0001044444566555"
     *               },
     *           "BusinessAddress":{
     *                 "address_id":null,
     *                 "address":"Miranda, San antonio de los altos, urbanizacion OPS torre 4, 1204",
     *                 "latitude":"10.37706",
     *                 "longitude":"-66.95635"
     *             },
     *            "BusinessPhoneNumbers":{
     *                  "phone_number_id":null,
     *                  "phone_name":"Office",
     *                  "phone_number":"0212-3734832"
     *            }
     *          }
     *       }
     *
     * @apiParamExample {json} Edit-Request:
     *
     *      {"ReaxiumParameters": {
     *          "Business": {
     *              "business_id": 1,
     *              "business_name": "Luis Edgardo Eguie Arocha",
     *              "business_id_number": "J-0001044444566555"
     *               },
     *           "BusinessAddress":{
     *                 "address_id":15,
     *                 "address":"Miranda, San antonio de los altos, urbanizacion OPS torre 4, 1204",
     *                 "latitude":"10.37706",
     *                 "longitude":"-66.95635"
     *             },
     *            "BusinessPhoneNumbers":{
     *                  "phone_number_id":18,
     *                  "phone_name":"Office",
     *                  "phone_number":"0212-3734832"
     *            }
     *          }
     *       }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     * "ReaxiumResponse": {
     * "code": 0,
     * "message": "SAVED SUCCESSFUL",
     * "object": {
     * "business_name": "Luis Edgardo Eguie Arocha",
     * "business_id_number": "J-0001044444566555",
     * "address_id": 27,
     * "phone_number_id": 70,
     * "business_id": 4
     * }
     * }
     * }
     *
     *
     * @apiErrorExample Error-Response Invalid Parameters:
     *      {"ReaxiumResponse": {
     *          "code": 2,
     *          "message": "Invalid Parameters received, please checkout the api documentation",
     *          "object": []
     *          }
     *      }
     *
     *
     * @apiErrorExample Error-Response Invalid Json Object:
     *      {"ReaxiumResponse": {
     *          "code": 2,
     *          "message": "Invalid Parameters received, please checkout the api documentation",
     *          "object": []
     *          }
     *      }
     */
    public function createBusiness()
    {

        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        try {
            Log::info('Object received: ' . json_encode($jsonObject));
            if (parent::validReaxiumJsonHeader($jsonObject)) {
                if (isset($jsonObject['ReaxiumParameters']["Business"])) {
                    $businessCreated = $this->createOrEditABusiness($jsonObject['ReaxiumParameters']);
                    if (isset($businessCreated)) {
                        $response = parent::setSuccessfulSave($response);
                        $response['ReaxiumResponse']['object'] = $businessCreated;
                    } else {
                        $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$ERROR_CREATING_A_BUSINESS_CODE;
                        $response['ReaxiumResponse']['message'] = ReaxiumApiMessages::$ERROR_CREATING_A_BUSINESS_MESSAGE;
                    }
                } else {
                    Log::info("Parameters received not contain business information in the right way");
                    $response = parent::seInvalidParametersMessage($response);
                }
            } else {
                Log::info("Object receive not valid with reaxium specifications");
                $response = parent::setInvalidJsonHeader($response);
            }
        } catch (\Exception $e) {
            Log::info("Error creating the business");
            Log::info($e->getMessage());
            $response = parent::setInternalServiceError($response);
        }
        $this->response->body(json_encode($response));
    }


    /**
     * create a business in our server
     * @param $businessJson
     * @return bool|\Cake\Datasource\EntityInterface|\Cake\ORM\Entity|mixed
     */
    private function createOrEditABusiness($businessJson)
    {
        try {

            $businessTable = TableRegistry::get("Business");
            $businessObject = $businessTable->newEntity();
            $businessObject = $businessTable->patchEntity($businessObject, $businessJson['Business']);

            if (isset($businessJson['BusinessAddress'])) {

                $addressTable = TableRegistry::get("Address");
                $addressObject = $addressTable->newEntity();
                $addressObject = $addressTable->patchEntity($addressObject, $businessJson['BusinessAddress']);
                $addressObject = $addressTable->save($addressObject);
                $businessObject->address_id = $addressObject->address_id;

            } else {
                Log::info("Business with no address information");
            }

            if (isset($businessJson['BusinessPhoneNumbers'])) {
                $phoneNumbersTable = TableRegistry::get("PhoneNumbers");
                $phoneNumbersObject = $phoneNumbersTable->newEntity();
                $phoneNumbersObject = $phoneNumbersTable->patchEntity($phoneNumbersObject, $businessJson['BusinessPhoneNumbers']);
                $phoneNumbersObject = $phoneNumbersTable->save($phoneNumbersObject);
                $businessObject->phone_number_id = $phoneNumbersObject->phone_number_id;
            } else {
                Log::info("Business with no phone numbers information");
            }

            $businessObject = $businessTable->save($businessObject);


        } catch (\Exception $e) {
            Log::info("Error creating the user. " . $e->getMessage());
            $businessObject = null;
        }
        return $businessObject;
    }


    /**
     * @api {post} /Business/allBusiness get all the business registered in reaxium cloud
     * @apiName allBusiness
     * @apiGroup Business
     *
     * @apiParamExample {json} Request:
     *
     *      {"ReaxiumParameters": {
     *              "page": "1",
     *              "limit": "10",
     *              "sortDir": "desc",
     *              "sortedBy": "business_name"
     *              "filter":""
     *          }
     *       }
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *    {
     * "ReaxiumResponse": {
     * "code": 0,
     * "message": "SUCCESSFUL REQUEST",
     * "object": [
     * {
     * "business_id": 1,
     * "business_name": "Reaxium Admin System",
     * "business_id_number": "Reaxium-0001",
     * "address_id": 1,
     * "phone_number_id": 1,
     * "status_id": 1,
     * "status": {
     * "status_id": 1,
     * "status_name": "ACTIVE"
     * }
     * },
     * {
     * "business_id": 4,
     * "business_name": "Luis Edgardo Eguie Arocha",
     * "business_id_number": "J-0001044444566555",
     * "address_id": 27,
     * "phone_number_id": 70,
     * "status_id": 1,
     * "status": {
     * "status_id": 1,
     * "status_name": "ACTIVE"
     * }
     * },
     * {
     * "business_id": 3,
     * "business_name": "Las Comunitarias",
     * "business_id_number": "J-00010444445555",
     * "address_id": 26,
     * "phone_number_id": 69,
     * "status_id": 1,
     * "status": {
     * "status_id": 1,
     * "status_name": "ACTIVE"
     * }
     * },
     * {
     * "business_id": 2,
     * "business_name": "Antonio Ortega Ordoñez",
     * "business_id_number": "J-000102222201",
     * "address_id": 25,
     * "phone_number_id": 68,
     * "status_id": 1,
     * "status": {
     * "status_id": 1,
     * "status_name": "ACTIVE"
     * }
     * }
     * ],
     * "totalRecords": 4,
     * "totalPages": 1
     * }
     * }
     *
     *
     * @apiErrorExample Error-Response Invalid Parameters:
     *      {"ReaxiumResponse": {
     *          "code": 2,
     *          "message": "Invalid Parameters received, please checkout the api documentation",
     *          "object": []
     *          }
     *      }
     *
     *
     * @apiErrorExample Error-Response Invalid Json Object:
     *      {"ReaxiumResponse": {
     *          "code": 2,
     *          "message": "Invalid Parameters received, please checkout the api documentation",
     *          "object": []
     *          }
     *      }
     */
    public function allBusiness()
    {

        Log::info("All business registered service invoked");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        if (parent::validReaxiumJsonHeader($jsonObject)) {
            Log::info('Object received: ' . json_encode($jsonObject));
            try {
                if (isset($jsonObject['ReaxiumParameters']["page"])) {

                    $page = $jsonObject['ReaxiumParameters']["page"];
                    $sortedBy = !isset($jsonObject['ReaxiumParameters']["sortedBy"]) ? 'first_last_name' : $jsonObject['ReaxiumParameters']["sortedBy"];
                    $sortDir = !isset($jsonObject['ReaxiumParameters']["sortDir"]) ? 'desc' : $jsonObject['ReaxiumParameters']["sortDir"];
                    $filter = !isset($jsonObject['ReaxiumParameters']["filter"]) ? '' : $jsonObject['ReaxiumParameters']["filter"];
                    $limit = !isset($jsonObject['ReaxiumParameters']["limit"]) ? 10 : $jsonObject['ReaxiumParameters']["limit"];
                    $business_id_master = !isset($jsonObject['ReaxiumParameters']["business_master_id"]) ? null : $jsonObject['ReaxiumParameters']["business_master_id"];

                    $businessFound = $this->lookUpAllBusiness($filter, $sortedBy, $sortDir,$business_id_master);
                    $count = $businessFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $businessFound = $this->paginate($businessFound);

                    if ($businessFound->count() > 0) {
                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $businessFound = $businessFound->toArray();
                        $response['ReaxiumResponse']['totalRecords'] = $count;
                        $response['ReaxiumResponse']['totalPages'] = $maxPages;
                        $response['ReaxiumResponse']['object'] = $businessFound;
                        $response = parent::setSuccessfulResponse($response);
                    } else {
                        $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['ReaxiumResponse']['message'] = 'No Business found';
                    }
                } else {
                    $response = parent::seInvalidParametersMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error getting all business information " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }
        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function getAllBusinessByType()
    {

        Log::info("All business registered service invoked");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();

        if (parent::validReaxiumJsonHeader($jsonObject)) {

            try {

                $typeBusiness = !isset($jsonObject['ReaxiumParameters']['Business']['business_type_id']) ? null
                    : $jsonObject['ReaxiumParameters']['Business']['business_type_id'];

                $business_master_id = !isset($jsonObject['ReaxiumParameters']['Business']['business_master_id']) ? null
                    : $jsonObject['ReaxiumParameters']['Business']['business_master_id'];


                if (isset($typeBusiness)) {

                    $businessTable = TableRegistry::get("Business");
                    $businessData = $businessTable->findByBusinessTypeId($typeBusiness)
                        ->where(array("Business.status_id" => 1,'Business.business_master_id'=>$business_master_id))
                        ->contain(array('BusinessType'));

                    if ($businessData->count() > 0) {
                        $response['ReaxiumResponse']['object'] = $businessData;
                        $response = parent::setSuccessfulResponse($response);
                    } else {
                        $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['ReaxiumResponse']['message'] = 'No Business found';
                    }

                } else {
                    $response = parent::seInvalidParametersMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error getting all business information " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }

        } else {
            $response = parent::seInvalidParametersMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function getRoutesByBusiness()
    {

        Log::info("All routes registered by business service invoked");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();

        if (parent::validReaxiumJsonHeader($jsonObject)) {

            try {

                if (isset($jsonObject['ReaxiumParameters']["page"])) {

                    $page = $jsonObject['ReaxiumParameters']["page"];
                    $sortedBy = !isset($jsonObject['ReaxiumParameters']["sortedBy"]) ? 'first_last_name' : $jsonObject['ReaxiumParameters']["sortedBy"];
                    $sortDir = !isset($jsonObject['ReaxiumParameters']["sortDir"]) ? 'desc' : $jsonObject['ReaxiumParameters']["sortDir"];
                    $filter = !isset($jsonObject['ReaxiumParameters']["filter"]) ? '' : $jsonObject['ReaxiumParameters']["filter"];
                    $limit = !isset($jsonObject['ReaxiumParameters']["limit"]) ? 10 : $jsonObject['ReaxiumParameters']["limit"];
                    $business_id = !isset($jsonObject['ReaxiumParameters']["business_id"]) ? 10 : $jsonObject['ReaxiumParameters']["business_id"];

                    // busco las rutas relacionadas con el business
                    $businessObject = $this->lookUpAllRoutesByBusiness($filter, $sortedBy, $sortDir, $business_id);

                    $count = $businessObject->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $routesBusinessFound = $this->paginate($businessObject);

                    if ($routesBusinessFound->count() > 0) {

                        $routesBusinessFound = $routesBusinessFound->toArray();
                        $arrayRoutes = [];
                        // busco las paradas relacionadas con esas rutas
                        foreach ($routesBusinessFound as $route) {
                            Log::info("Routes id: " . $route['id_route']);
                            $stops = $this->getStopsByRoutesBusiness($route['id_route']);
                            array_push($arrayRoutes, $stops);
                        }

                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $response['ReaxiumResponse']['totalRecords'] = $count;
                        $response['ReaxiumResponse']['totalPages'] = $maxPages;
                        $response['ReaxiumResponse']['object'] = $arrayRoutes;
                        $response = parent::setSuccessfulResponse($response);
                    } else {
                        $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['ReaxiumResponse']['message'] = 'There are no routes related to this business';
                    }
                } else {
                    $response = parent::seInvalidParametersMessage($response);
                }

            } catch (\Exception $e) {
                Log::info("Error getting all business information " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }
        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }

    /**
     * @param $filter
     * @param $sortedBy
     * @param $sortDir
     * @param $businessId
     * @return mixed
     */
    private function lookUpAllRoutesByBusiness($filter, $sortedBy, $sortDir, $businessId)
    {

        $businessRouteTable = TableRegistry::get('BusinessRoutes');

        if (trim($filter) != "") {

            $whereCondition = array(array('OR' => array(
                array('Routes.route_number LIKE' => '%' . $filter . '%'),
                array('Routes.route_name LIKE' => '%' . $filter . '%'))));

            $businessRouteFound = $businessRouteTable->findByBusinessId($businessId)
                ->where($whereCondition)
                ->andWhere(array('Routes.status_id' => 1))
                ->contain(array('Routes'))
                ->order(array($sortedBy . ' ' . $sortDir));
        } else {
            $businessRouteFound = $businessRouteTable->findByBusinessId($businessId)
                ->where(array('Routes.status_id' => 1))
                ->contain(array('Routes'))
                ->order(array($sortedBy . ' ' . $sortDir));
        }

        return $businessRouteFound;
    }

    /**
     * @param $id_route
     * @return \Cake\Datasource\EntityInterface|\Cake\ORM\Entity|null
     */
    private function getStopsByRoutesBusiness($id_route)
    {

        $routeFound = TableRegistry::get("Routes");
        $entityRoutes = $routeFound->newEntity();

        $stopsByRoutes = $routeFound->findByIdRoute($id_route)->where(array('Routes.status_id' => 1))->contain(array('Stops'));

        if ($stopsByRoutes->count() > 0) {

            $stopsByRoutes = $stopsByRoutes->toArray();

            foreach ($stopsByRoutes as $routes) {
                $entityRoutes->id_route = $routes['id_route'];
                $entityRoutes->route_number = $routes['route_number'];
                $entityRoutes->route_name = $routes['route_name'];
                $entityRoutes->route_address = $routes['route_address'];
                $entityRoutes->route_type = $routes['route_type'];
                $entityRoutes->stops = $routes['stops'];
            }

        } else {
            $entityRoutes = null;
        }

        return $entityRoutes;
    }

    /**
     * obtain all business registered in the reaxium cloud
     * @param $filter
     * @param $sortedBy
     * @param $sortDir
     * @return $this|null
     */
    private function lookUpAllBusiness($filter, $sortedBy, $sortDir, $businessId)
    {
        if (isset($filter) && trim($filter) != '') {
            $whereCondition = array(array('OR' => array(
                array('business_name LIKE' => '%' . $filter . '%'),
                array('business_id_number LIKE' => '%' . $filter . '%'),
                array('BusinessType.business_type_name LIKE' => '%' . $filter . '%')
            )), array('OR' => array(
                array('Business.status_id' => '1'),
                array('Business.status_id' => '2'),
            )));
        } else {
            $whereCondition = array(
                'OR' => array(
                    array('Business.status_id' => '1'),
                    array('Business.status_id' => '2')
                ));
        }
        $businessTable = TableRegistry::get("Business");
        $AllBusinessObject = $businessTable->find()
        ->where($whereCondition)
        ->andWhere(['business_master_id' => $businessId])
        ->order(array($sortedBy . ' ' . $sortDir))
        ->contain(array('Status', 'BusinessType'));

        return $AllBusinessObject;
    }


    /**
     * obtain all business registered in the reaxium cloud
     * @param $filter
     * @param $sortedBy
     * @param $sortDir
     * @return $this|null
     */
    private function lookUpAllBusinessFilter($filter, $sortedBy, $sortDir, $businessId)
    {
        $businessTable = TableRegistry::get("Business");
        $AllBusinessObject = null;

        $whereCondition = array(array('OR' => array(
            array('business_name LIKE' => '%' . $filter . '%'),
            array('business_id_number LIKE' => '%' . $filter . '%')
        )), 'Business.status_id' => '1');

        $AllBusinessObject = $businessTable->find()
            ->where($whereCondition)
            ->andWhere(array('business_master_id' => $businessId))
            ->order(array($sortedBy . ' ' . $sortDir))
            ->contain(array('Status', 'Applications'));

        return $AllBusinessObject;
    }


    /**
     * @api {post} /Business/businessById get a business information by its id
     * @apiName businessById
     * @apiGroup Business
     *
     * @apiParamExample {json} Request:
     *
     *      {"ReaxiumParameters": {
     *              "Business":{
     *              "business_id": "1"
     *             }
     *          }
     *       }
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     * "ReaxiumResponse": {
     * "code": 0,
     * "message": "SUCCESSFUL REQUEST",
     * "object": [
     * {
     * "business_id": 1,
     * "business_name": "Reaxium Admin System",
     * "business_id_number": "Reaxium-0001",
     * "address_id": 1,
     * "phone_number_id": 1,
     * "status_id": 1,
     * "phone_number": {
     * "phone_number_id": 1,
     * "phone_name": "Mi Casa",
     * "phone_number": "0212-3734832"
     * },
     * "addres": {
     * "address_id": 1,
     * "address": "Miranda, San antonio de los altos, urbanizacion OPS torre 4, 1204",
     * "latitude": "10.37706",
     * "longitude": "-66.95635"
     * },
     * "status": {
     * "status_id": 1,
     * "status_name": "ACTIVE"
     * }
     * }
     * ]
     * }
     * }
     *
     *
     * @apiErrorExample Error-Response Invalid Parameters:
     *      {"ReaxiumResponse": {
     *          "code": 2,
     *          "message": "Invalid Parameters received, please checkout the api documentation",
     *          "object": []
     *          }
     *      }
     *
     *
     * @apiErrorExample Error-Response Invalid Json Object:
     *      {"ReaxiumResponse": {
     *          "code": 2,
     *          "message": "Invalid Parameters received, please checkout the api documentation",
     *          "object": []
     *          }
     *      }
     */
    public function businessById()
    {
        Log::info("business By ID service invoked");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        if (parent::validReaxiumJsonHeader($jsonObject)) {
            Log::info('Object received: ' . json_encode($jsonObject));
            try {
                if (isset($jsonObject['ReaxiumParameters']["Business"]) &&
                    isset($jsonObject['ReaxiumParameters']["Business"]['business_id'])
                ) {

                    $businessId = $jsonObject['ReaxiumParameters']["Business"]['business_id'];

                    $businessFound = $this->lookupABusinessByID($businessId);

                    if (isset($businessFound)) {
                        $response = parent::setSuccessfulResponse($response);
                        $response['ReaxiumResponse']['object'] = $businessFound;
                    } else {
                        $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['ReaxiumResponse']['message'] = 'No Business found';
                    }

                } else {
                    $response = parent::seInvalidParametersMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error getting all business information " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }
        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * @api {post} /Business/allBusinessFiltered filter and get all the business registered in reaxium cloud
     * @apiName allBusinessFiltered
     * @apiGroup Business
     *
     * @apiParamExample {json} Request:
     *
     *      {"ReaxiumParameters": {
     *              "Business": {
     * "filter":"Las Comuni"
     *            }
     *          }
     *       }
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     * "ReaxiumResponse": {
     * "code": 0,
     * "message": "SUCCESSFUL REQUEST",
     * "object": [
     * {
     * "business_id": 3,
     * "business_name": "Las Comunitarias",
     * "business_id_number": "J-00010444445555",
     * "address_id": 26,
     * "phone_number_id": 69,
     * "status_id": 1,
     * "status": {
     * "status_id": 1,
     * "status_name": "ACTIVE"
     * }
     * }
     * ]
     * }
     * }
     *
     *
     *
     * @apiErrorExample Error-Response Invalid Parameters:
     *      {"ReaxiumResponse": {
     *          "code": 2,
     *          "message": "Invalid Parameters received, please checkout the api documentation",
     *          "object": []
     *          }
     *      }
     *
     *
     * @apiErrorExample Error-Response Invalid Json Object:
     *      {"ReaxiumResponse": {
     *          "code": 2,
     *          "message": "Invalid Parameters received, please checkout the api documentation",
     *          "object": []
     *          }
     *      }
     */
    public function allBusinessFiltered()
    {
        Log::info("All business filtered service invoked");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        if (parent::validReaxiumJsonHeader($jsonObject)) {
            Log::info('Object received: ' . json_encode($jsonObject));
            try {
                if (isset($jsonObject['ReaxiumParameters']["Business"]["filter"]) &&
                    isset($jsonObject['ReaxiumParameters']['Business']['business_master_id'])
                ) {
                    $sortedBy = "business_name";
                    $sortDir = "desc";
                    $filter = $jsonObject['ReaxiumParameters']["Business"]["filter"];
                    $business_id_master = $jsonObject['ReaxiumParameters']["Business"]["business_master_id"];
                    $businessFound = $this->lookUpAllBusinessFilter($filter, $sortedBy, $sortDir, $business_id_master);

                    if ($businessFound->count() > 0) {
                        $businessFound = $businessFound->toArray();
                        $response = parent::setSuccessfulResponse($response);
                        $response['ReaxiumResponse']['object'] = $businessFound;
                    } else {
                        $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['ReaxiumResponse']['message'] = 'No Business found';
                    }
                } else {
                    $response = parent::seInvalidParametersMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error getting all business information " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }
        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * search a business information by its ID
     * @param $businessId
     * @return null
     */
    private function lookupABusinessByID($businessId)
    {
        $businessTable = TableRegistry::get("Business");
        $whereCondition = array('business_id' => $businessId,
            array('OR' => array(
                array('Business.status_id' => '1'),
                array('Business.status_id' => '2')))
        );
        $businessObject = $businessTable->find()
            ->where($whereCondition)
            ->contain(array('Status', 'Address', 'PhoneNumbers', 'Routes'));

        if ($businessObject->count() > 0) {
            $businessObject = $businessObject->toArray();
        } else {
            $businessObject = null;
        }
        return $businessObject;
    }


    /**
     * @api {post} /Business/deleteBusiness delete
     * @apiName allBusinessFiltered
     * @apiGroup Business
     *
     * @apiParamExample {json} Request:
     *
     *      {"ReaxiumParameters": {
     *              "Business": {
     * "business_id":"1"
     *            }
     *          }
     *       }
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     * "ReaxiumResponse": {
     * "code": 0,
     * "message": "DELETED SUCCESSFULLY",
     * "object": []
     * }
     * }
     *
     *
     * @apiErrorExample Error-Response Invalid Parameters:
     *      {"ReaxiumResponse": {
     *          "code": 2,
     *          "message": "Invalid Parameters received, please checkout the api documentation",
     *          "object": []
     *          }
     *      }
     *
     *
     * @apiErrorExample Error-Response Invalid Json Object:
     *      {"ReaxiumResponse": {
     *          "code": 2,
     *          "message": "Invalid Parameters received, please checkout the api documentation",
     *          "object": []
     *          }
     *      }
     */
    public function deleteBusiness()
    {
        Log::info("delete a business service invoked");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));
        if (parent::validReaxiumJsonHeader($jsonObject)) {
            try {
                if (isset($jsonObject['ReaxiumParameters']["Business"]["business_id"])) {

                    $businessId = $jsonObject['ReaxiumParameters']["Business"]["business_id"];
                    Log::info("DeviceId Recieved: " . $businessId);
                    $businessFound = $this->lookupABusinessByID($businessId);

                    if (isset($businessFound)) {
                        $this->deleteABusiness($businessId);
                        $response = parent::setSuccessfulDelete($response);
                    } else {
                        $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['ReaxiumResponse']['message'] = 'No Business found';
                    }
                } else {
                    $response = parent::seInvalidParametersMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error getting all business information " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }
        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * soft delete of a business on the system
     * @param $businessId
     */
    private function deleteABusiness($businessId)
    {
        $businessTable = TableRegistry::get("Business");
        $businessTable->updateAll(array('status_id' => '3'), array('business_id' => $businessId));
    }


    public function getTypeBusiness()
    {

        Log::info("delete a business service invoked");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();

        try {
            $businessType = TableRegistry::get('BusinessType');
            $businessFound = $businessType->find();

            if ($businessFound->count() > 0) {

                $businessFound = $businessFound->toArray();
                $response['ReaxiumResponse']['object'] = $businessFound;
                $response = parent::setSuccessfulResponse($response);

            } else {
                $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                $response['ReaxiumResponse']['message'] = 'No BusinessType found';
            }
        } catch (\Exception $e) {
            Log::info("Error getting the user " . $e->getMessage());
            $response = parent::setInternalServiceError($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }

}