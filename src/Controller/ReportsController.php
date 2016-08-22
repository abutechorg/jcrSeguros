<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 8/8/2016
 * Time: 11:37
 */
namespace App\Controller;

use Cake\Core\Exception\Exception;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Util\ReaxiumApiMessages;

//define('PATH_DIRECTORY', '/var/www/html/reaxium_reports');
define('PATH_DIRECTORY', 'C:/xampp/htdocs/ProyectosGAndG/reports_school');
define('ALL_TRAFFIC', 3);
define('NAME_FILE', 'Reaxium_Attendance_');
define('TEMPLATE_TRAFFIC_USERS', 'reaxium_report_users');
define('TEMPLATE_TRAFFIC_BY_USER', 'reaxium_traffic_by_user');
define('TEMPLATE_ANALYSIS_YEARS', 'reaxium_analysis_years');
define('TEMPLATE_ANALYSIS_BUSINESS', 'reaxium_analysis_business');
define('TEMPLATE_ANALYSIS_STUDENT', 'reaxium_analysis_by_user');
define('ENVIRONMENT', 'http://localhost:8080/ProyectosGAndG/reports_school');

//define('ENVIRONMENT','http://54.213.162.246/reaxium_reports');

class ReportsController extends ReaxiumAPIController
{


    /**
     * @api {post} /Reports/getUsersAttendance Information user attendance
     * @apiName getUsersAttendance
     * @apiGroup Reports
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "ReaxiumParameters": {
     *      "ReaxiumReport": {
     *      "page": 1,
     *      "limit": 5,
     *      "business_id": "3",
     *      "sortDir": "asc",
     *      "sortedBy": "first_name",
     *      "filter": "",
     *      "start_date": "",
     *      "end_date": "",
     *      "business_id": "",
     *      "traffic_type_id": ""
     *      }
     *   }
     * }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * "ReaxiumResponse": {
     *      "code": 0,
     *      "message": "SAVED SUCCESSFUL",
     *      "object":[{data}]
     *
     *      }
     *  }
     *
     *
     * @apiErrorExample Error-Response: Device Access already exists
     *  {
     *      "ReaxiumResponse": {
     *          "code": 1,
     *          "message": "No Data Found",
     *          "object": []
     *          }
     *      }
     *
     */
    public function getUsersAttendance()
    {

        Log::info("Get data student by business");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validReaxiumJsonHeader($jsonObject)) {

            $page = $jsonObject['ReaxiumParameters']['ReaxiumReport']["page"];
            $sortedBy = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']["sortedBy"]) ? 'Users.first_name' : $jsonObject['ReaxiumParameters']['ReaxiumReport']["sortedBy"];
            $sortDir = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']["sortDir"]) ? 'desc' : $jsonObject['ReaxiumParameters']['ReaxiumReport']["sortDir"];
            $filter = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']["filter"]) ? '' : $jsonObject['ReaxiumParameters']['ReaxiumReport']["filter"];
            $limit = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']["limit"]) ? 10 : $jsonObject['ReaxiumParameters']['ReaxiumReport']["limit"];
            $start_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date'];
            $end_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date'];
            $business_id = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['business_id']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['business_id'];
            $traffic_type_id = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['traffic_type_id']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['traffic_type_id'];

            try {

                $result = $this->usersAttendanceWithPaginate($start_date,
                    $end_date,
                    $business_id,
                    $traffic_type_id,
                    $filter,
                    $sortedBy,
                    $sortDir);

                if ($result->count() > 0) {

                    $count = $result->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $usersFound = $this->paginate($result);

                    $maxPages = floor((($count - 1) / $limit) + 1);
                    $usersFound = $usersFound->toArray();
                    $response['ReaxiumResponse']['totalRecords'] = $count;
                    $response['ReaxiumResponse']['totalPages'] = $maxPages;
                    $response['ReaxiumResponse']['object'] = $usersFound;
                    $response = parent::setSuccessfulResponse($response);

                } else {
                    $response['ReaxiumResponse']['code'] = '1';
                    $response['ReaxiumResponse']['message'] = 'No Data Found';
                }
            } catch (\Exception $e) {
                Log::info("Error: " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }


        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * @api {post} /Reports/getTrafficByUserAttendance Information user attendance traffic
     * @apiName getTrafficByUserAttendance
     * @apiGroup Reports
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "ReaxiumParameters": {
     *      "ReaxiumReport": {
     *      "page": 1,
     *      "limit": 5,
     *      "business_id": "3",
     *      "sortDir": "asc",
     *      "sortedBy": "first_name",
     *      "filter": "",
     *      "start_date": "",
     *      "end_date": "",
     *      "business_id": "",
     *      "traffic_type_id": "",
     *      "user_id":""
     *      }
     *   }
     * }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * "ReaxiumResponse": {
     *      "code": 0,
     *      "message": "SAVED SUCCESSFUL",
     *      "object":[{data}]
     *
     *      }
     *  }
     *
     *
     * @apiErrorExample Error-Response: Device Access already exists
     *  {
     *      "ReaxiumResponse": {
     *          "code": 1,
     *          "message": "No Data Found",
     *          "object": []
     *          }
     *      }
     *
     */
    public function getTrafficByUserAttendance()
    {

        Log::info("Get traffic by students id");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validReaxiumJsonHeader($jsonObject)) {

            try {

                $page = $jsonObject['ReaxiumParameters']['ReaxiumReport']["page"];
                $sortedBy = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']["sortedBy"]) ? 'Users.first_name' : $jsonObject['ReaxiumParameters']['ReaxiumReport']["sortedBy"];
                $sortDir = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']["sortDir"]) ? 'desc' : $jsonObject['ReaxiumParameters']['ReaxiumReport']["sortDir"];
                $filter = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']["filter"]) ? '' : $jsonObject['ReaxiumParameters']['ReaxiumReport']["filter"];
                $limit = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']["limit"]) ? 10 : $jsonObject['ReaxiumParameters']['ReaxiumReport']["limit"];
                $start_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date'];
                $end_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date'];
                $traffic_type_id = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['traffic_type_id']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['traffic_type_id'];
                $user_id = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['user_id']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['user_id'];

                if (isset($user_id)) {

                    $result = $this->getTrafficByUserPaginate($user_id,
                        $traffic_type_id,
                        $start_date,
                        $end_date,
                        $filter,
                        $sortedBy,
                        $sortDir);

                    if ($result->count() > 0) {

                        $count = $result->count();
                        $this->paginate = array('limit' => $limit, 'page' => $page);
                        $usersFound = $this->paginate($result);

                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $usersFound = $usersFound->toArray();
                        $response['ReaxiumResponse']['totalRecords'] = $count;
                        $response['ReaxiumResponse']['totalPages'] = $maxPages;
                        $response['ReaxiumResponse']['object'] = $usersFound;
                        $response = parent::setSuccessfulResponse($response);

                    } else {
                        $response['ReaxiumResponse']['code'] = '1';
                        $response['ReaxiumResponse']['message'] = 'No Data Found';
                    }

                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }

            } catch (\Exception $e) {
                Log::info("Error: " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * @api {post} /Reports/getReportTrafficByUser Information user attendance traffic report
     * @apiName getReportTrafficByUser
     * @apiGroup Reports
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "ReaxiumParameters": {
     *      "ReaxiumReport": {
     *      "start_date": "",
     *      "end_date": "",
     *      "traffic_type_id": "",
     *      "user_id":""
     *      }
     *   }
     * }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * "ReaxiumResponse": {
     *      "code": 0,
     *      "message": "SAVED SUCCESSFUL",
     *      "object":[],
     *      "url_pdf":"http://localhost:8080/ProyectosGAndG/reports_school/Reaxium_Attendance_1710.pdf"
     *
     *      }
     *  }
     *
     *
     * @apiErrorExample Error-Response: Device Access already exists
     *  {
     *      "ReaxiumResponse": {
     *          "code": 1,
     *          "message": "No Data Found",
     *          "object": []
     *          }
     *      }
     *
     */
    public function getReportTrafficByUser()
    {

        Log::info("Get traffic by students id");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validReaxiumJsonHeader($jsonObject)) {

            $start_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date'];
            $end_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date'];
            $traffic_type_id = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['traffic_type_id']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['traffic_type_id'];
            $user_id = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['user_id']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['user_id'];

            try {

                if ($user_id) {

                    $trafficTable = TableRegistry::get("Traffic");

                    $result = $this->getTrafficByUser($user_id, $traffic_type_id, $start_date, $end_date, $trafficTable);

                    if ($result->count() > 0) {

                        $name_report = NAME_FILE . rand(1000, 9999) . '.pdf';
                        $usersFound = $result->toArray();

                        $business = $this->getBusinessByUserId($usersFound[0]['user']['business_id']);

                        $objTraffic = $trafficTable->newEntity();
                        $objTraffic->start_date = $start_date;
                        $objTraffic->end_date = $end_date;
                        $objTraffic->dataTraffic = $usersFound;
                        $objTraffic->business = $business[0]['business_name'];
                        $objTraffic->days_presents = $this->presentsUser($user_id, $start_date, $end_date);
                        $objTraffic->days_absents = $this->absentsUsers($user_id, $start_date, $end_date);

                        $url = $this->buildPDF(TEMPLATE_TRAFFIC_BY_USER, $name_report, $objTraffic);

                        $response['ReaxiumResponse']['url_pdf'] = $url;
                        $response = parent::setSuccessfulResponse($response);
                    } else {
                        $response['ReaxiumResponse']['code'] = '1';
                        $response['ReaxiumResponse']['message'] = 'No Data Found';
                    }
                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error: " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * @param $userId
     * @param $traffic_type_id
     * @param $start_date
     * @param $end_date
     * @param $filter
     * @param $sortedBy
     * @param $sortDir
     * @return $this|array
     */
    private function getTrafficByUserPaginate($userId, $traffic_type_id, $start_date, $end_date, $filter, $sortedBy, $sortDir)
    {

        $trafficTable = TableRegistry::get("Traffic");
        $conditions = array();


        // condicion de fecha y validacion
        if (isset($start_date)) {
            $startDateCondition = array('Traffic.datetime >=' => $start_date);
            array_push($conditions, $startDateCondition);

            if (isset($end_date)) {
                $endDateCondition = array('Traffic.datetime <=' => $end_date);
                array_push($conditions, $endDateCondition);
            }
        }

        //condicion de tipo de acceso
        if (isset($traffic_type_id)) {
            $accessTypeCondition = array('Traffic.traffic_type_id' => $traffic_type_id);
            array_push($conditions, $accessTypeCondition);
        }

        array_push($conditions, array('Traffic.user_id' => $userId));

        $trafficFound = $trafficTable->find('all',
            array('fields' => array('Traffic.datetime',
                'Users.user_id',
                'Users.first_name',
                'Users.first_last_name',
                'Users.document_id',
                'Users.user_photo',
                'TrafficType.traffic_type_name',
                'ReaxiumDevice.device_id',
                'ReaxiumDevice.device_name'),
                'conditions' => $conditions))
            ->contain(array('Users', 'TrafficType', 'ReaxiumDevice'));


        if ($filter != "") {
            $whereFilter = array(array('OR' => array(
                array('TrafficType.traffic_type_name LIKE' => '%' . $filter . '%'))));
            $trafficFound->andWhere($whereFilter);
        }

        $trafficFound->order(array($sortedBy . ' ' . $sortDir));

        Log::info($trafficFound);

        return $trafficFound;
    }


    private function getTrafficByUser($userId, $traffic_type_id, $start_date, $end_date, $trafficTable)
    {


        $conditions = array();

        // condicion de fecha y validacion
        if (isset($start_date)) {
            $startDateCondition = array('Traffic.datetime >=' => $start_date);
            array_push($conditions, $startDateCondition);
            if (isset($end_date)) {
                $endDateCondition = array('Traffic.datetime <=' => $end_date);
                array_push($conditions, $endDateCondition);
            }
        }

        //condicion de tipo de acceso
        if (isset($traffic_type_id)) {
            $accessTypeCondition = array('Traffic.traffic_type_id' => $traffic_type_id);
            array_push($conditions, $accessTypeCondition);
        }

        //agregando el id del usuario
        array_push($conditions, array('Traffic.user_id' => $userId));

        $trafficFound = $trafficTable->find('all',
            array('fields' => array('Traffic.datetime',
                'Users.user_id',
                'Users.first_name',
                'Users.first_last_name',
                'Users.document_id',
                'Users.birthdate',
                'Users.user_photo',
                'Users.business_id',
                'TrafficType.traffic_type_name',
                'ReaxiumDevice.device_id',
                'ReaxiumDevice.device_name'),
                'conditions' => $conditions))
            ->contain(array('Users', 'TrafficType', 'ReaxiumDevice'));

        return $trafficFound;

    }


    /**
     * @api {post} /Reports/getReportTrafficUsers Information user attendance traffic report
     * @apiName getReportTrafficUsers
     * @apiGroup Reports
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "ReaxiumParameters": {
     *      "ReaxiumReport": {
     *      "start_date": "",
     *      "end_date": "",
     *      "traffic_type_id": "",
     *      "business_id":""
     *      }
     *   }
     * }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * "ReaxiumResponse": {
     *      "code": 0,
     *      "message": "SAVED SUCCESSFUL",
     *      "object":[],
     *      "url_pdf":"http://localhost:8080/ProyectosGAndG/reports_school/Reaxium_Attendance_1710.pdf"
     *
     *      }
     *  }
     *
     *
     * @apiErrorExample Error-Response: Device Access already exists
     *  {
     *      "ReaxiumResponse": {
     *          "code": 1,
     *          "message": "No Data Found",
     *          "object": []
     *          }
     *      }
     *
     */
    public function getReportTrafficUsers()
    {

        Log::info("Get data student by business");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        $start_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date']) ? null :
            $jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date'];

        $end_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date']) ? null :
            $jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date'];

        $business_id = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['business_id']) ? null :
            $jsonObject['ReaxiumParameters']['ReaxiumReport']['business_id'];

        $traffic_type_id = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['traffic_type_id']) ? null :
            $jsonObject['ReaxiumParameters']['ReaxiumReport']['traffic_type_id'];


        if (isset($start_date)) {

            $trafficUserTable = TableRegistry::get("Traffic");
            $objTrafic = $trafficUserTable->newEntity();

            $result = $this->usersAttendance($start_date, $end_date, $business_id, $traffic_type_id, $trafficUserTable);

            if (isset($result)) {

                $name_report = NAME_FILE . rand(1000, 9999) . '.pdf';

                try {

                    $objTrafic->start_date = $start_date;
                    $objTrafic->end_date = $end_date;
                    $objTrafic->traffic = $result;

                    $result = $this->buildPDF(TEMPLATE_TRAFFIC_USERS, $name_report, $objTrafic);

                    $response['ReaxiumResponse']['code'] = '0';
                    $response['ReaxiumResponse']['message'] = 'Successful create report';
                    $response['ReaxiumResponse']['url_pdf'] = $result;

                } catch (\Exception $e) {
                    $response['ReaxiumResponse']['code'] = '2';
                    $response['ReaxiumResponse']['message'] = 'Error Create PDF';
                }

            } else {
                $response['ReaxiumResponse']['code'] = '1';
                $response['ReaxiumResponse']['message'] = 'No Data Found';
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * @api {post} /Reports/getListAttendanceByYear Information user attendance
     * @apiName getListAttendanceByYear
     * @apiGroup Reports
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "ReaxiumParameters": {
     *      "ReaxiumReport": {
     *       "start_date":"",
     *      "typeTraffic": ""
     *      }
     *   }
     * }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * "ReaxiumResponse": {
     *      "code": 0,
     *      "message": "SAVED SUCCESSFUL",
     *      "object":[{data}]
     *
     *      }
     *  }
     *
     *
     * @apiErrorExample Error-Response: Device Access already exists
     *  {
     *      "ReaxiumResponse": {
     *          "code": 1,
     *          "message": "No Data Found",
     *          "object": []
     *          }
     *      }
     *
     */
    public function getListAttendanceByYear()
    {

        Log::info("Get attendance array by month");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validReaxiumJsonHeader($jsonObject)) {

            $start_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date']) ? null :
                $jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date'];

            $typeTraffic = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['typeTraffic']) ? null :
                $jsonObject['ReaxiumParameters']['ReaxiumReport']['typeTraffic'];

            if (isset($start_date) && isset($typeTraffic)) {

                $result = $this->getAttendanceInAYear($start_date, $typeTraffic);

                if (isset($result)) {
                    $response['ReaxiumResponse']['object'] = $result;
                    $response = parent::setSuccessfulResponse($response);
                } else {
                    $response['ReaxiumResponse']['code'] = '1';
                    $response['ReaxiumResponse']['message'] = 'No Data Found';
                }
            } else {
                $response = parent::setInvalidJsonMessage($response);
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * @api {post} /Reports/getReportTrafficUsers Information user attendance traffic report
     * @apiName getReportTrafficUsers
     * @apiGroup Reports
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "ReaxiumParameters": {
     *      "ReaxiumReport": {
     *      "object": [{data}]
     *      }
     *   }
     * }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * "ReaxiumResponse": {
     *      "code": 0,
     *      "message": "SAVED SUCCESSFUL",
     *      "object":[],
     *      "url_pdf":"http://localhost:8080/ProyectosGAndG/reports_school/Reaxium_Attendance_1710.pdf"
     *
     *      }
     *  }
     *
     *
     * @apiErrorExample Error-Response: Device Access already exists
     *  {
     *      "ReaxiumResponse": {
     *          "code": 1,
     *          "message": "No Data Found",
     *          "object": []
     *          }
     *      }
     *
     */
    public function getReportAnalysisYears()
    {

        Log::info("Get attendance array by month");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validReaxiumJsonHeader($jsonObject)) {

            try {

                $start_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date'];


                $dataSets = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['object']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['object'];


                if (isset($start_date) && isset($dataSets)) {

                    $name_report = NAME_FILE . rand(1000, 9999) . '.pdf';

                    $tableFound = TableRegistry::get('Traffic');
                    $arrayAux = [];

                    foreach ($dataSets as $item) {

                        $obj = $tableFound->newEntity();
                        $obj->label = $item['label'];
                        $obj->borderColor = $item['borderColor'];
                        $obj->backgroundColor = $item['backgroundColor'];
                        $obj->pointBorderColor = $item['pointBorderColor'];
                        $obj->pointBackgroundColor = $item['pointBackgroundColor'];
                        $obj->pointBorderWidth = $item['pointBorderWidth'];
                        $obj->data = json_encode($item['data']);
                        $obj->attendance_max_month = $this->getAttendanceMaxMonthByYear($start_date,$tableFound);
                        $obj->attendance_min_month = $this->getAttendanceMinMounthByYear($start_date,$tableFound);

                        $years = explode(":", trim($item['label']));

                        $obj->start_date = $years[1];

                        array_push($arrayAux, $obj);
                    }


                    $result = $this->buildPDF(TEMPLATE_ANALYSIS_YEARS, $name_report, $arrayAux);

                    $response['ReaxiumResponse']['code'] = '0';
                    $response['ReaxiumResponse']['message'] = 'Successful create report';
                    $response['ReaxiumResponse']['url_pdf'] = $result;
                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }

            } catch (\Exception $e) {
                Log::info("Error: " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * @api {post} /Reports/getReportAttendanceByBusiness Information user attendance traffic report
     * @apiName getReportAttendanceByBusiness
     * @apiGroup Reports
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "ReaxiumParameters": {
     *      "ReaxiumReport": {
     *      "object": [{data}]
     *      }
     *   }
     * }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * "ReaxiumResponse": {
     *      "code": 0,
     *      "message": "SAVED SUCCESSFUL",
     *      "object":[],
     *      "url_pdf":"http://localhost:8080/ProyectosGAndG/reports_school/Reaxium_Attendance_1710.pdf"
     *
     *      }
     *  }
     *
     *
     * @apiErrorExample Error-Response: Device Access already exists
     *  {
     *      "ReaxiumResponse": {
     *          "code": 1,
     *          "message": "No Data Found",
     *          "object": []
     *          }
     *      }
     *
     */
    public function getListAttendanceByBusiness(){

        Log::info("Get attendance array by business");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validReaxiumJsonHeader($jsonObject)) {

            try {

                $start_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date'];

                $type_traffic = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['type_traffic']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['type_traffic'];

                $business_id = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['business_id']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['business_id'];

                if(isset($start_date) && isset($type_traffic) && isset($business_id)){


                    $result = $this->getAttendanceByBusiness($start_date,$type_traffic,$business_id);

                    if (isset($result)) {
                        $response['ReaxiumResponse']['object'] = $result;
                        $response = parent::setSuccessfulResponse($response);
                    } else {
                        $response['ReaxiumResponse']['code'] = '1';
                        $response['ReaxiumResponse']['message'] = 'No Data Found';
                    }

                }else{
                    $response = parent::setInvalidJsonMessage($response);
                }

            } catch (\Exception $e) {
                Log::info("Error: " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }



    /**
     * @api {post} /Reports/getReportAnalysisBusiness Information user attendance traffic report
     * @apiName getReportAnalysisBusiness
     * @apiGroup Reports
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "ReaxiumParameters": {
     *      "ReaxiumReport": {
     *      "object": [{data}]
     *      }
     *   }
     * }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * "ReaxiumResponse": {
     *      "code": 0,
     *      "message": "SAVED SUCCESSFUL",
     *      "object":[],
     *      "url_pdf":"http://localhost:8080/ProyectosGAndG/reports_school/Reaxium_Attendance_1710.pdf"
     *
     *      }
     *  }
     *
     *
     * @apiErrorExample Error-Response: Device Access already exists
     *  {
     *      "ReaxiumResponse": {
     *          "code": 1,
     *          "message": "No Data Found",
     *          "object": []
     *          }
     *      }
     *
     */
    public function getReportAnalysisBusiness(){

        Log::info("Create report by business");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validReaxiumJsonHeader($jsonObject)) {

            try {

                $start_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date'];

                $type_traffic = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['type_traffic']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['type_traffic'];


                $dataSets = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['object']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['object'];


                if (isset($start_date) && isset($dataSets) && isset($type_traffic)) {

                    $name_report = NAME_FILE . rand(1000, 9999) . '.pdf';

                    $tableFound = TableRegistry::get('Traffic');
                    $arrayAux = [];

                    foreach ($dataSets as $item) {

                        $obj = $tableFound->newEntity();
                        $obj->label = $item['label'];
                        $obj->borderColor = $item['borderColor'];
                        $obj->backgroundColor = $item['backgroundColor'];
                        $obj->pointBorderColor = $item['pointBorderColor'];
                        $obj->pointBackgroundColor = $item['pointBackgroundColor'];
                        $obj->pointBorderWidth = $item['pointBorderWidth'];
                        $obj->data = json_encode($item['data']);
                        $obj->attendance_max_month = $this->getAttendanceMaxMonthByBusiness($start_date,$type_traffic,$item['business_id'],$tableFound);
                        $obj->attendance_min_month = $this->getAttendanceMinMonthByBusiness($start_date,$type_traffic,$item['business_id'],$tableFound);

                        array_push($arrayAux, $obj);
                    }


                    $result = $this->buildPDF(TEMPLATE_ANALYSIS_BUSINESS, $name_report, $arrayAux);

                    $response['ReaxiumResponse']['code'] = '0';
                    $response['ReaxiumResponse']['message'] = 'Successful create report';
                    $response['ReaxiumResponse']['url_pdf'] = $result;
                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }

            } catch (\Exception $e) {
                Log::info("Error: " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * @api {post} /Reports/getListAttendanceUser Information user attendance traffic report
     * @apiName getListAttendanceUser
     * @apiGroup Reports
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "ReaxiumParameters": {
     *      "ReaxiumReport": {
     *      "object": [{data}]
     *      }
     *   }
     * }
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * "ReaxiumResponse": {
     *      "code": 0,
     *      "message": "SAVED SUCCESSFUL",
     *      "object":[],
     *      "url_pdf":"http://localhost:8080/ProyectosGAndG/reports_school/Reaxium_Attendance_1710.pdf"
     *
     *      }
     *  }
     *
     *
     * @apiErrorExample Error-Response: Device Access already exists
     *  {
     *      "ReaxiumResponse": {
     *          "code": 1,
     *          "message": "No Data Found",
     *          "object": []
     *          }
     *      }
     *
     */
    public function getListAttendanceUser(){

        Log::info("Create report by business");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validReaxiumJsonHeader($jsonObject)){

            try{

                $start_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date'];
                $end_date = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date'];
                $user_id = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['user_id']) ? null : $jsonObject['ReaxiumParameters']['ReaxiumReport']['user_id'];

                $presentDaysUser = $this->presentsUser($user_id,$start_date,$end_date);
                $absentDaysUser = $this->absentsUsers($user_id,$start_date,$end_date);

                $response['ReaxiumResponse']['present'] = $presentDaysUser;
                $response['ReaxiumResponse']['absent'] = $absentDaysUser;

                $response = parent::setSuccessfulResponse($response);


            }
            catch (\Exception $e){
                Log::info("Error: " . $e->getMessage());
                $response = parent::setInternalServiceError($response);
            }
        }else{
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function getReportAnalysisStudent(){

        Log::info("Create report by student");
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validReaxiumJsonHeader($jsonObject)){

            try{

                $user_id =!isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['user_id']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['user_id'];


                $start_date =!isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['start_date'];

                $end_date =!isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['end_date'];

                $dataSets = !isset($jsonObject['ReaxiumParameters']['ReaxiumReport']['object']) ? null :
                    $jsonObject['ReaxiumParameters']['ReaxiumReport']['object'];

                if (isset($dataSets) && isset($start_date) && isset($end_date)&& isset($user_id)) {

                    $userFound = $this->getUserDataByUserId($user_id);

                    $name_report = NAME_FILE . rand(1000, 9999) . '.pdf';

                    $tableFound = TableRegistry::get('Traffic');
                    $arrayAux = [];

                    foreach ($dataSets as $item) {

                        $obj = $tableFound->newEntity();
                        $obj->backgroundColor = json_encode($item['backgroundColor']);
                        $obj->data = json_encode($item['data']);
                        $obj->present_days = $item['present_days'];
                        $obj->absent_days = $item['absent_days'];
                        $obj->start_date = $start_date;
                        $obj->end_date = $end_date;
                        $obj->user_name = $userFound[0]['first_name'];
                        $obj->user_last_name = $userFound[0]['first_last_name'];
                        $obj->document_id = $userFound[0]['document_id'];
                        $obj->user_photo = $userFound[0]['user_photo'];
                        array_push($arrayAux, $obj);
                    }


                    $result = $this->buildPDF(TEMPLATE_ANALYSIS_STUDENT, $name_report, $arrayAux);

                    $response['ReaxiumResponse']['code'] = '0';
                    $response['ReaxiumResponse']['message'] = 'Successful create report';
                    $response['ReaxiumResponse']['url_pdf'] = $result;

                }else{
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e){

            }

        }else{
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * cantidad de usuarios registrados en cada mes por un ano
     * @param $year
     * @return $this|array|null
     */
    private function getAttendanceInAYear($year, $typeTraffic)
    {

        $trafficUserTable = TableRegistry::get("Traffic");
        $query = $trafficUserTable->find('all', array('fields' => array('month' => 'month(date(datetime))', 'attendance_days' => 'count(distinct(DATE(datetime)))')))
            ->where(array('year(date(datetime))' => $year, 'traffic_type_id' => $typeTraffic))
            ->group(array('month(date(datetime))'));


        if ($query->count() > 0) {
            $query = $query->toArray();
        } else {
            $query = null;
        }

        return $query;
    }


    /**
     * cantidad de usuarios registrados en cada mes por un ano
     * @param $year
     * @param $month
     * @return $this|array|null
     */
    private function getAttendanceInAYearAndMonth($year, $month)
    {

        $trafficUserTable = TableRegistry::get("Traffic");
        $query = $trafficUserTable->find('all', array('fields' => array('month' => 'month(date(datetime))', 'attendance_days' => 'count(distinct(DATE(datetime)))')))
            ->where(array('year(date(datetime))' => $year,
                'month(date(datetime))' => $month,
                'traffic_type_id' => 1))
            ->group(array('month(date(datetime))'));


        if ($query->count() > 0) {
            $query = $query->toArray();
        } else {
            $query = null;
        }

        return $query;

    }

    /**
     * Maximo de asistencia por meses durante el año
     * @param $year
     * @return $this|array|null
     */
    private function getAttendanceMaxMonthByYear($year,$trafficUserTable){

        $days = 0;
        $query = $trafficUserTable->find('all', array('fields' => array('month' => 'month(date(datetime))',
            'attendance_days' => 'count(distinct(DATE(datetime)))')))
            ->where(array('year(date(datetime))' => $year,
                'traffic_type_id' => 1))
            ->group(array('month(date(datetime))'))
            ->order(array('attendance_days' => 'desc'))
            ->limit(1);


        if ($query->count() > 0) {
            $query = $query->toArray();
            $days = $query[0]['attendance_days'];
        }


        return $days;

    }


    /**
     * Minimo de asistencia por meses durante el año
     * @param $year
     * @return $this|array|null
     */
    private function getAttendanceMinMounthByYear($year,$trafficUserTable)
    {

        $days = 0;
        $query = $trafficUserTable->find('all', array('fields' => array('month' => 'month(date(datetime))',
            'attendance_days' => 'count(distinct(DATE(datetime)))')))
            ->where(array('year(date(datetime))' => $year,
                'traffic_type_id' => 1))
            ->group(array('month(date(datetime))'))
            ->order(array('attendance_days' => 'asc'))
            ->limit(1);


        if ($query->count() > 0) {
            $query = $query->toArray();
            $days = $query[0]['attendance_days'];
        }

        return $days;

    }

    /**
     * Maximo de asistencia en un mes filtrado por negocio
     * @param $start_date
     * @param $type_trafic
     * @param $business_id
     * @return $this|array|null
     */
    private function getAttendanceMaxMonthByBusiness($start_date,$type_trafic,$business_id,$trafficUserTable){

        $days=0;
        $query = $trafficUserTable
            ->find('all', array('fields' => array('month' => 'month(date(Traffic.datetime))',
                'attendance_days' => 'count(distinct(DATE(Traffic.datetime)))')))
            ->join(array(
                'users' => array(
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'Traffic.user_id = users.user_id'
                ),
                'busi' => array(
                    'table' => 'business',
                    'type' => 'INNER',
                    'conditions' => 'users.business_id = busi.business_id'
                )

            ))
            ->where(array('year(date(Traffic.datetime))' => $start_date,
                'Traffic.traffic_type_id' => $type_trafic,
                'busi.business_id' => $business_id))
            ->group(array('month(date(Traffic.datetime))'))
            ->order(array('attendance_days' => 'desc'))
            ->limit(1);

        if ($query->count() > 0) {
            $query = $query->toArray();
            $days = $query[0]['attendance_days'];
        }

        return $days;

    }

    /**
     * Minimo de asistencia en un mes filtrado por negocio
     * @param $start_date
     * @param $type_trafic
     * @param $business_id
     * @return $this|array|null
     */
    private function getAttendanceMinMonthByBusiness($start_date,$type_trafic,$business_id,$trafficUserTable){

        $days = 0;
        $query = $trafficUserTable
            ->find('all', array('fields' => array('month' => 'month(date(Traffic.datetime))',
                'attendance_days' => 'count(distinct(DATE(Traffic.datetime)))')))
            ->join(array(
                'users' => array(
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'Traffic.user_id = users.user_id'
                ),
                'busi' => array(
                    'table' => 'business',
                    'type' => 'INNER',
                    'conditions' => 'users.business_id = busi.business_id'
                )

            ))
            ->where(array('year(date(Traffic.datetime))' => $start_date,
                'Traffic.traffic_type_id' => $type_trafic,
                'busi.business_id' => $business_id))
            ->group(array('month(date(Traffic.datetime))'))
            ->order(array('attendance_days' => 'asc'))
            ->limit(1);

        if ($query->count() > 0) {
            $query = $query->toArray();
            $days = $query[0]['attendance_days'];
        }

        return $days;
    }

    /**
     * Obtener los meses de asistencia filtrado por negocio
     * @param $start_date
     * @param $type_trafic
     * @param $business_id
     * @return null
     */
    private function getAttendanceByBusiness($start_date,$type_trafic,$business_id){

        $trafficUserTable = TableRegistry::get('Traffic');

        $query = $trafficUserTable
            ->find('all', array('fields' => array('month' => 'month(date(Traffic.datetime))',
                'attendance_days' => 'count(distinct(DATE(Traffic.datetime)))')))
            ->join(array(
                'users' => array(
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'Traffic.user_id = users.user_id'
                ),
                'busi' => array(
                    'table' => 'business',
                    'type' => 'INNER',
                    'conditions' => 'users.business_id = busi.business_id'
                )

            ))
            ->where(array('year(date(Traffic.datetime))' => $start_date,
                'Traffic.traffic_type_id' => $type_trafic,
                'busi.business_id' => $business_id))
            ->group(array('month(date(Traffic.datetime))'));


        if ($query->count() > 0) {
            $query = $query->toArray();
        } else {
            $query = null;
        }

        return $query;
    }


    /**
     * @param $start_date
     * @param $end_date
     * @param $business_id
     * @param $traffic_type_id
     * @return $this|array|null
     */
    private function usersAttendance($start_date, $end_date, $business_id, $traffic_type_id, $trafficUserTable)
    {

        $conditions = array();

        // condicion de fecha y validacion
        if (isset($start_date)) {
            $startDateCondition = array('Traffic.datetime >=' => $start_date);
            array_push($conditions, $startDateCondition);
            if (isset($end_date)) {
                $endDateCondition = array('Traffic.datetime <=' => $end_date);
                array_push($conditions, $endDateCondition);
            }
        }

        //condicion de tipo de acceso
        if (isset($traffic_type_id)) {
            $accessTypeCondition = array('Traffic.traffic_type_id' => $traffic_type_id);
            array_push($conditions, $accessTypeCondition);

        }

        $query = $trafficUserTable->find()
            ->select(array(
                'Traffic.traffic_id',
                'Traffic.datetime',
                'Traffic.traffic_type_id',
                'Traffic.device_id',
                'users.user_id',
                'users.first_name',
                'users.first_last_name',
                'users.document_id',
                'users.business_id',
                'traf.traffic_type_id',
                'traf.traffic_type_name'))
            ->join(array(
                'users' => array(
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'Traffic.user_id = users.user_id'
                ),
                'traf' => array(
                    'table' => 'traffic_type',
                    'type' => 'INNER',
                    'conditions' => 'Traffic.traffic_type_id = traf.traffic_type_id'
                )
            ))
            ->where($conditions);


        if ($query->count() > 0) {

            $query = $query->toArray();
        } else {
            $query = null;
        }

        return $query;
    }

    /**
     * @param $start_date
     * @param $end_date
     * @param $business_id
     * @param $traffic_type_id
     * @param $filter
     * @param $sortedBy
     * @param $sortDir
     * @return $this
     */
    private function usersAttendanceWithPaginate($start_date, $end_date, $business_id, $traffic_type_id, $filter, $sortedBy, $sortDir)
    {

        $trafficUserTable = TableRegistry::get("Traffic");
        $conditions = array();

        // condicion de fecha y validacion
        if (isset($start_date)) {
            $startDateCondition = array('Traffic.datetime >=' => $start_date);
            array_push($conditions, $startDateCondition);
            if (isset($end_date)) {
                $endDateCondition = array('Traffic.datetime <=' => $end_date);
                array_push($conditions, $endDateCondition);
            }
        }

        //condicion de tipo de acceso
        if (isset($traffic_type_id)) {
            $accessTypeCondition = array('Traffic.traffic_type_id' => $traffic_type_id);
            array_push($conditions, $accessTypeCondition);

        }

        Log::info('condiciones: ' . json_encode($conditions));

        $query = $trafficUserTable->find()
            ->select(array(
                'Traffic.traffic_id',
                'Traffic.datetime',
                'Traffic.traffic_type_id',
                'Traffic.device_id',
                'users.user_id',
                'users.first_name',
                'users.first_last_name',
                'users.document_id',
                'users.business_id',
                'traf.traffic_type_id',
                'traf.traffic_type_name'))
            ->join(array(
                'users' => array(
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'Traffic.user_id = users.user_id'
                ),
                'traf' => array(
                    'table' => 'traffic_type',
                    'type' => 'INNER',
                    'conditions' => 'Traffic.traffic_type_id = traf.traffic_type_id'
                )
            ))
            ->where($conditions);

        if ($filter != "") {
            $whereFilter = array(array('OR' => array(
                array('users.first_name LIKE' => '%' . $filter . '%'),
                array('users.first_last_name LIKE' => '%' . $filter . '%'),
                array('users.document_id LIKE' => '%' . $filter . '%'))));
            $query->andWhere($whereFilter);
        }

        $query->order(array($sortedBy . ' ' . $sortDir));


        return $query;
    }


    /**
     * Metodo que devulve la  asistencia d eun usuario segun la fecha
     * @param $user_id
     * @param $start_date
     * @param $end_date
     * @return \Cake\ORM\Query
     */
    private function presentsUser($user_id, $start_date, $end_date)
    {

        $trafficUserTable = TableRegistry::get("Traffic");
        $conditions = array();
        $days_presents = 0;
        // condicion de fecha y validacion
        if (isset($start_date)) {
            $startDateCondition = array('datetime >=' => $start_date);
            array_push($conditions, $startDateCondition);
            if (isset($end_date)) {
                $endDateCondition = array('datetime <=' => $end_date);
                array_push($conditions, $endDateCondition);
            }

            array_push($conditions, array('user_id' => $user_id));

            array_push($conditions, array('traffic_type_id' => 1));
        }


        $query = $trafficUserTable->find('all',
            array('fields' => array('dias_asistidos' => 'COUNT(DISTINCT(DATE(datetime)))')))->where($conditions);

        if ($query->count() > 0) {
            $query = $query->toArray();
            $days_presents = $query[0]['dias_asistidos'];
        }

        return $days_presents;
    }

    /**
     * Dias de asistencias totales en el sistema
     * @return int
     */
    private function  presentsAttendanceTotal($start_date, $end_date){

        $conditions = array();
        $days_presents = 0;
        $trafficUserTable = TableRegistry::get("Traffic");

        // condicion de fecha y validacion
        if (isset($start_date)) {
            $startDateCondition = array('datetime >=' => $start_date);
            array_push($conditions, $startDateCondition);
            if (isset($end_date)) {
                $endDateCondition = array('datetime <=' => $end_date);
                array_push($conditions, $endDateCondition);
            }

            array_push($conditions, array('traffic_type_id' => 1));
        }


        $query = $trafficUserTable->find('all',
            array('fields' => array('dias_asistencias_total' => 'COUNT(DISTINCT(DATE(datetime)))')))->where($conditions);

        if ($query->count() > 0) {
            $query = $query->toArray();
            $days_presents = $query[0]['dias_asistencias_total'];
        }

        return $days_presents;
    }

    /**
     * @param $user_id
     * @param $start_date
     * @param $end_date
     * @return int
     */
    private function absentsUsers($user_id, $start_date, $end_date){

        $days_absent = 0;

        $dias_asistidos = $this->presentsUser($user_id,$start_date, $end_date);
        $dias_totales_asistencia = $this->presentsAttendanceTotal($start_date, $end_date);
        $days_absent = $dias_totales_asistencia - $dias_asistidos;

        return $days_absent;
    }

    /**
     * Search business
     * @param $business_id
     * @return null
     */
    private function getBusinessByUserId($business_id)
    {

        Log::info("Business id: " . $business_id);
        $businessTable = TableRegistry::get("Business");
        $businessFound = $businessTable->findByBusinessId($business_id);

        if ($businessFound->count() > 0) {
            $businessFound = $businessFound->toArray();
        } else {
            $businessFound = null;
        }

        Log::info($businessFound);

        return $businessFound;
    }

    private function getUserDataByUserId($user_id){

        $userTable = TableRegistry::get("Users");
        $userFound = $userTable->findByUserId($user_id);

        if($userFound->count()>0){
            $userFound = $userFound->toArray();
        }else{
            $userFound= null;
        }

        return $userFound;
    }

    /**
     * @param $template
     * @param $name_file
     * @param $data
     * @return string
     * @throws \Exception
     */
    private function buildPDF($template, $name_file, $data)
    {

        try {

            $path_pdf_create = PATH_DIRECTORY . '/' . $name_file;

            $CakePdf = new \CakePdf\Pdf\CakePdf();

            $CakePdf->template($template);

            $CakePdf->viewVars(array('data' => $data));
            // Get the PDF string returned
            $CakePdf->output();
            // Or write it to file directly
            $CakePdf->write($path_pdf_create);

            $url_pdf = ENVIRONMENT . '/' . $name_file;

        } catch (\Exception $e) {
            Log::info('Error creando pdf' . $e);
            throw $e;
        }

        return $url_pdf;
    }


}