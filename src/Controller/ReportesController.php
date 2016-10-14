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


define('PATH_DIRECTORY', 'C:/xampp/htdocs/JcrReports');
define('NAME_FILE', 'JcrReports');
define('TEMPLATE_RENOVACIONES', 'reporte_jrcseguros_renovaciones');
define('ENVIRONMENT', 'http://localhost:8080/JcrReports');



class ReportesController extends JcrAPIController{


    public function getInfoRenovacion(){

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if(parent::validJcrJsonHeader($jsonObject)){

            try{
                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];

                if(isset($start_date) && isset($end_date)){

                    $polizaCtrl = new PolizaController();

                    $result = $polizaCtrl->getListPolizaRenovaciones($start_date,$end_date,$aseguradora_id);

                    if(isset($result)){

                        $response['JcrResponse']['object'] = $result;
                        $response = parent::setSuccessfulResponse($response);
                    }else{
                        $response['JcrResponse']['code'] = '1';
                        $response['JcrResponse']['message'] = 'Polizas no found';
                    }

                }else{
                    $response = parent::setInvalidJsonMessage($response);
                }

            }
            catch (\Exception $e){
                Log::info("Error Saving the User " . $e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }
        }
        else{
            $response = parent::setInvalidJsonMessage($response);
        }
        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));

    }


    public function createReportRenovacion(){

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if(parent::validJcrJsonHeader($jsonObject)){

            try{
                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];


                if(isset($start_date) && isset($end_date)){

                    $polizaCtrl = new PolizaController();
                    $polizaTable = TableRegistry::get("Poliza");
                    $polizaRenovaciones = $polizaTable->newEntity();

                    $result = $polizaCtrl->getListPolizaRenovaciones($start_date,$end_date,$aseguradora_id);

                    if(isset($result)){

                        $name_report = NAME_FILE . rand(10000, 99999) . '.pdf';

                        $polizaRenovaciones->start_date = $start_date;
                        $polizaRenovaciones->end_date = $end_date;
                        $polizaRenovaciones->polizas = $result;


                        $url = $this->buildPDF(TEMPLATE_RENOVACIONES,$name_report, $polizaRenovaciones);

                        $response['JcrResponse']['url_pdf'] = $url;
                        $response = parent::setSuccessfulResponse($response);

                    }else{
                        $response['JcrResponse']['code'] = '1';
                        $response['JcrResponse']['message'] = 'Polizas no found';
                    }

                }else{
                    $response = parent::setInvalidJsonMessage($response);
                }

            }
            catch (\Exception $e){
                Log::info("Error Saving the User " . $e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }

        }else{
            $response = parent::setInvalidJsonMessage($response);
        }

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