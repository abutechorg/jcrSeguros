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


define('PATH_DIRECTORY', 'C:/xampp/htdocs/ProyectosGAndG/reports_school');
define('NAME_FILE', 'Jcr_Reports');
define('TEMPLATE_TRAFFIC_USERS', 'example');
define('ENVIRONMENT', 'http://localhost:8080/ProyectosGAndG/reports_school');



class ReportsController extends ReaxiumAPIController
{


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


        $result = $this->buildPDF(TEMPLATE_TRAFFIC_USERS, "example", array("test"=>"Hola Mundo"));

        $response['ReaxiumResponse']['code'] = '0';
        $response['ReaxiumResponse']['message'] = 'Successful create report';
        $response['ReaxiumResponse']['url_pdf'] = $result;


        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
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