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
define('TEMPLATE_SINIESTRALIDAD','reporte_jrcseguros_siniestralidad');
define('ENVIRONMENT', 'http://localhost:8080/JcrReports');
define('SINIESTRO_PERSONA',1);
define('SINIESTRO_VEHICULO',2);


class ReportesController extends JcrAPIController{


    public function getInfoRenovacion(){

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if(parent::validJcrJsonHeader($jsonObject)){

            try{
                $page = $jsonObject['JcrParameters']['Reports']["page"];
                $limit = !isset($jsonObject['JcrParameters']['Reports']["limit"]) ? 10 : $jsonObject['JcrParameters']['Reports']["limit"];
                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];

                if(isset($start_date) && isset($end_date)){

                    $polizaTable = TableRegistry::get("Poliza");
                    $polizaCtrl = new PolizaController();
                    $vehiculoCtrl = new VehiculoController();
                    $clientCtrl = new ClientController();

                    $renovaciones = $polizaCtrl->getListPolizaRenovaciones($start_date,$end_date,$aseguradora_id,false);

                    $count = $renovaciones->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $renovaciones = $this->paginate($renovaciones);


                    if($renovaciones->count() > 0){

                        $maxPages = floor((($count - 1) / $limit) + 1);

                        $arrayResultFinal = array();

                        //tratar el arreglo de polizas
                        foreach($renovaciones as $poliza){

                            $entityPoliza = $polizaTable->newEntity();

                            if($poliza['ramo']['ramo_id'] == RAMO_AUTO_INDIVIDUAL || $poliza['ramo']['ramo_id'] == RAMO_AUTO_FLOTA){

                                $entityPoliza->poliza_id = $poliza['poliza_id'];
                                $entityPoliza->numero_poliza = $poliza['numero_poliza'];
                                $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['cliente_id_titular']);
                                $entityPoliza->agente = $poliza['agente'];
                                $entityPoliza->prima_total = $poliza['prima_total'];
                                $entityPoliza->fecha_vencimiento = $poliza['fecha_vencimiento'];
                                $entityPoliza->ramo = $poliza['ramo'];
                                $entityPoliza->vehiculos = $vehiculoCtrl->getVehiculoRelationPoliza($poliza['poliza_id']);
                                $entityPoliza->suma_asegurada = $polizaCtrl->getCoberturasDeLaPoliza($poliza['poliza_id']);
                                $entityPoliza->aseguradora = $polizaCtrl->getAseguradoraByID($poliza['aseguradora_id'])[0]['aseguradora_nombre'];

                            }
                            else{
                                $entityPoliza->poliza_id = $poliza['poliza_id'];
                                $entityPoliza->numero_poliza = $poliza['numero_poliza'];
                                $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['cliente_id_titular']);
                                $entityPoliza->agente = $poliza['agente'];
                                $entityPoliza->prima_total = $poliza['prima_total'];
                                $entityPoliza->fecha_vencimiento = $poliza['fecha_vencimiento'];
                                $entityPoliza->ramo = $poliza['ramo'];
                                $entityPoliza->suma_asegurada = $polizaCtrl->getCoberturasDeLaPoliza($poliza['poliza_id']);
                                $entityPoliza->aseguradora = $polizaCtrl->getAseguradoraByID($poliza['aseguradora_id'])[0]['aseguradora_nombre'];
                            }

                            array_push($arrayResultFinal,$entityPoliza);
                        }


                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $arrayResultFinal;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else{
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

                    $result = $polizaCtrl->getListPolizaRenovaciones($start_date,$end_date,$aseguradora_id,true);

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



    public function getInfoSiniestralidad(){

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject)){

            try{

                $page = $jsonObject['JcrParameters']['Reports']["page"];
                $limit = !isset($jsonObject['JcrParameters']['Reports']["limit"]) ? 10 : $jsonObject['JcrParameters']['Reports']["limit"];

                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguaradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];
                $numero_poliza = !isset($jsonObject['JcrParameters']['Reports']['numero_poliza']) ? null : $jsonObject['JcrParameters']['Reports']['numero_poliza'];
                $ramo_id = !isset($jsonObject['JcrParameters']['Reports']['ramo_id']) ? null : $jsonObject['JcrParameters']['Reports']['ramo_id'];

                if(isset($page) && isset($start_date) && isset($end_date)){

                    $siniestroCtrl = new SiniestroController();
                    $siniestroFound = $siniestroCtrl->getSiniestralidadInfo($start_date,$end_date,$aseguaradora_id,$numero_poliza,$ramo_id,false);

                    Log::info($siniestroFound);

                    $count = $siniestroFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $siniestroFound = $this->paginate($siniestroFound);


                    if ($siniestroFound->count() > 0) {

                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $polizasFound = $siniestroFound->toArray();

                        $polizaTable = TableRegistry::get("Poliza");
                        $clientCtrl = new ClientController();
                        $vehiculoCtrl = new VehiculoController();
                        $polizaCtrl = new PolizaController();
                        $siniestroCtrl = new SiniestroController();

                        $entityPoliza = null;
                        $arrayResultFinal = array();

                        foreach($polizasFound as $poliza){

                            $entityPoliza = $polizaTable->newEntity();
                            $entityPoliza->poliza_id = $poliza['poliza_id'];
                            $entityPoliza->numero_poliza = $poliza['numero_poliza'];
                            $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['cliente_id_titular']);
                            $entityPoliza->agente = $poliza['agente'];
                            $entityPoliza->prima_total = $poliza['prima_total'];
                            $entityPoliza->fecha_vencimiento = $poliza['fecha_vencimiento'];
                            $entityPoliza->ramo = $this->getRamoSystem($poliza['ramo_id']);
                            $entityPoliza->coberturas = $polizaCtrl->getCoberturasDeLaPoliza($poliza['poliza_id']);
                            $entityPoliza->numero_siniestro = $poliza['siniestro']['numero_siniestro'];
                            $entityPoliza->monto_siniestro = $poliza['siniestro']['monto_siniestro'];
                            $entityPoliza->tipo_siniestro = $poliza['siniestro']['tipo_siniestro_id'];

                            if($poliza['siniestro']['tipo_siniestro_id'] == SINIESTRO_VEHICULO){
                                $entityPoliza->vehiculo = $vehiculoCtrl->getVehiculoRelationPoliza($poliza['poliza_id']);
                            }

                            $entityPoliza->aseguradora = $polizaCtrl->getAseguradoraByID($poliza['aseguradora_id']);
                            $entityPoliza->calculo = $siniestroCtrl->calculoSiniestro($poliza['siniestro']['monto_siniestro'],$poliza['prima_total']);


                            array_push($arrayResultFinal,$entityPoliza);
                        }

                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $arrayResultFinal;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No Users found';
                    }

                }
                else{
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


    public function createReportSiniestralidad(){

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if(parent::validJcrJsonHeader($jsonObject)){

            try{

                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguaradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];
                $numero_poliza = !isset($jsonObject['JcrParameters']['Reports']['numero_poliza']) ? null : $jsonObject['JcrParameters']['Reports']['numero_poliza'];
                $ramo_id = !isset($jsonObject['JcrParameters']['Reports']['ramo_id']) ? null : $jsonObject['JcrParameters']['Reports']['ramo_id'];


                if(isset($start_date) && isset($end_date)){
                    $siniestroCtrl = new SiniestroController();
                    $listSiniestro = $siniestroCtrl->getSiniestralidadInfo($start_date,$end_date,$aseguaradora_id,$numero_poliza,$ramo_id,true);

                    if(isset($listSiniestro)){

                        $name_report = NAME_FILE . rand(10000, 99999) . '.pdf';

                        $polizaTable = TableRegistry::get("Poliza");
                        $siniestralidad = $polizaTable->newEntity();

                        $siniestralidad->start_date = $start_date;
                        $siniestralidad->end_date = $end_date;
                        $siniestralidad->lista_siniestralidad = $listSiniestro;

                        $url = $this->buildPDF(TEMPLATE_SINIESTRALIDAD,$name_report, $siniestralidad);

                        $response['JcrResponse']['url_pdf'] = $url;
                        $response = parent::setSuccessfulResponse($response);

                    }else{
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No Users found';
                    }


                }
                else{
                    $response = parent::setInvalidJsonMessage($response);
                }

            }catch (\Exception $e){
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




    public function getSumasAsegurasInfo(){

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if(parent::validJcrJsonHeader($jsonObject)){

            $page = $jsonObject['JcrParameters']['Reports']["page"];
            $limit = !isset($jsonObject['JcrParameters']['Reports']["limit"]) ? 10 : $jsonObject['JcrParameters']['Reports']["limit"];

            $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
            $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
            $aseguaradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];
            $ramo_id = !isset($jsonObject['JcrParameters']['Reports']['ramo_id']) ? null : $jsonObject['JcrParameters']['Reports']['ramo_id'];
            $monto_min = !isset($jsonObject['JcrParameters']['Reports']['monto_min']) ? null :$jsonObject['JcrParameters']['Reports']['monto_min'];
            $monto_max = !isset($jsonObject['JcrParameters']['Reports']['monto_max']) ? null :$jsonObject['JcrParameters']['Reports']['monto_max'];


            if(parent::validJcrJsonHeader($jsonObject)){

                if(isset($start_date) && isset($end_date) && isset($monto_min) && isset($monto_max)){

                }
                else{
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            else{
                $response = parent::setInvalidJsonMessage($response);
            }
        }
        else{
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function getRamoSystem($ramo_id){

        $ramoTable = TableRegistry::get("Ramo");
        $ramoFound = $ramoTable->findByRamoId($ramo_id);

        if($ramoFound->count() > 0){
            $ramoFound = $ramoFound->toArray();
        }
        else{
            $ramoFound = null;
        }

        return $ramoFound;
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