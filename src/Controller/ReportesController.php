<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 8/8/2016
 * Time: 11:37
 */
namespace App\Controller;

use App\Util\ReaxiumUtil;
use Cake\Core\Exception\Exception;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Util\ReaxiumApiMessages;

//define('PATH_DIRECTORY', '/var/www/html/jcr_reports');
define('PATH_DIRECTORY', 'C:/xampp/htdocs/JcrReports');
define('NAME_FILE', 'JcrReports');
define('TEMPLATE_RENOVACIONES', 'reporte_jrcseguros_renovaciones');
define('TEMPLATE_SINIESTRALIDAD', 'reporte_jrcseguros_siniestralidad');
define('TEMPLATE_SA', 'reporte_jrcseguros_aumento_sa');
define('TEMPLATE_VENTAS_CRUZADAS', 'reporte_jrcseguros_ventas_cruzadas');
define('ENVIRONMENT', 'http://localhost:8080/JcrReports');
//define('ENVIRONMENT', 'http://54.213.162.246/jcr_reports');
define('SINIESTRO_PERSONA', 1);
define('SINIESTRO_VEHICULO', 2);


class ReportesController extends JcrAPIController
{


    public function getInfoRenovacion()
    {

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if (parent::validJcrJsonHeader($jsonObject)) {

            try {
                $page = $jsonObject['JcrParameters']['Reports']["page"];
                $limit = !isset($jsonObject['JcrParameters']['Reports']["limit"]) ? 10 : $jsonObject['JcrParameters']['Reports']["limit"];
                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];

                if (isset($start_date) && isset($end_date)) {

                    $polizaTable = TableRegistry::get("Poliza");
                    $polizaCtrl = new PolizaController();
                    $vehiculoCtrl = new VehiculoController();
                    $clientCtrl = new ClientController();

                    $renovaciones = $polizaCtrl->getListPolizaRenovaciones($start_date, $end_date, $aseguradora_id, false);

                    $count = $renovaciones->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $renovaciones = $this->paginate($renovaciones);


                    if ($renovaciones->count() > 0) {

                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $renovaciones = $renovaciones->toArray();

                        $arrayResultFinal = array();

                        //tratar el arreglo de polizas
                        foreach ($renovaciones as $poliza) {

                            $entityPoliza = $polizaTable->newEntity();

                            if ($poliza['ramo']['ramo_id'] == RAMO_AUTO_INDIVIDUAL || $poliza['ramo']['ramo_id'] == RAMO_AUTO_FLOTA) {

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

                            } else {
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

                            array_push($arrayResultFinal, $entityPoliza);
                        }


                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $arrayResultFinal;
                        $response = parent::setSuccessfulResponse($response);
                    } else {
                        $response['JcrResponse']['code'] = '1';
                        $response['JcrResponse']['message'] = 'Polizas no encontradas';
                    }


                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }

            } catch (\Exception $e) {
                Log::info("Error Saving the User " . $e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }
        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));

    }


    public function createReportRenovacion()
    {

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if (parent::validJcrJsonHeader($jsonObject)) {

            try {
                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];


                if (isset($start_date) && isset($end_date)) {

                    $polizaCtrl = new PolizaController();
                    $polizaTable = TableRegistry::get("Poliza");
                    $polizaRenovaciones = $polizaTable->newEntity();

                    $result = $polizaCtrl->getListPolizaRenovaciones($start_date, $end_date, $aseguradora_id, true);

                    if (isset($result)) {

                        $name_report = NAME_FILE . rand(10000, 99999) . '.pdf';

                        $polizaRenovaciones->start_date = $start_date;
                        $polizaRenovaciones->end_date = $end_date;
                        $polizaRenovaciones->polizas = $result;


                        $url = $this->buildPDF(TEMPLATE_RENOVACIONES, $name_report, $polizaRenovaciones);

                        $response['JcrResponse']['url_pdf'] = $url;
                        $response = parent::setSuccessfulResponse($response);

                    } else {
                        $response['JcrResponse']['code'] = '1';
                        $response['JcrResponse']['message'] = 'Polizas no encontradas';
                    }

                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }

            } catch (\Exception $e) {
                Log::info("Error Saving the User " . $e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function getInfoSiniestralidad()
    {

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject)) {

            try {

                $page = $jsonObject['JcrParameters']['Reports']["page"];
                $limit = !isset($jsonObject['JcrParameters']['Reports']["limit"]) ? 10 : $jsonObject['JcrParameters']['Reports']["limit"];

                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguaradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];
                $ci_or_placa = !isset($jsonObject['JcrParameters']['Reports']['ci_or_placa']) ? null : $jsonObject['JcrParameters']['Reports']['ci_or_placa'];
                $ramo_id = !isset($jsonObject['JcrParameters']['Reports']['ramo_id']) ? null : $jsonObject['JcrParameters']['Reports']['ramo_id'];
                $tipo_busqueda = !isset($jsonObject['JcrParameters']['Reports']['tipo_busqueda']) ? null : $jsonObject['JcrParameters']['Reports']['tipo_busqueda'];


                if (isset($page)) {

                    $siniestroCtrl = new SiniestroController();
                    $siniestroFound = $siniestroCtrl->getSiniestralidadInfo($start_date, $end_date, $aseguaradora_id, $ci_or_placa, $ramo_id, false, $tipo_busqueda);

                    Log::info($siniestroFound);

                    $count = $siniestroFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $siniestroFound = $this->paginate($siniestroFound);


                    if ($siniestroFound->count() > 0) {

                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $polizasFound = $siniestroFound->toArray();
                        Log::info($polizasFound);

                        $polizaTable = TableRegistry::get("Poliza");
                        $clientCtrl = new ClientController();
                        $vehiculoCtrl = new VehiculoController();
                        $polizaCtrl = new PolizaController();
                        $siniestroCtrl = new SiniestroController();

                        $entityPoliza = null;
                        $arrayResultFinal = array();

                        foreach ($polizasFound as $poliza) {

                            $entityPoliza = $polizaTable->newEntity();
                            $entityPoliza->poliza_id = $poliza['poliza']['poliza_id'];
                            $entityPoliza->numero_poliza = $poliza['poliza']['numero_poliza'];
                            $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['poliza']['cliente_id_titular']);
                            $entityPoliza->agente = $poliza['poliza']['agente'];
                            $entityPoliza->prima_total = $poliza['poliza']['prima_total'];
                            $entityPoliza->fecha_vencimiento = $poliza['poliza']['fecha_vencimiento'];
                            $entityPoliza->ramo = $this->getRamoSystem($poliza['poliza']['ramo_id']);
                            $entityPoliza->coberturas = $polizaCtrl->getCoberturasDeLaPoliza($poliza['poliza']['poliza_id']);
                            $entityPoliza->numero_siniestro = $poliza['numero_siniestro'];
                            $entityPoliza->monto_siniestro = $poliza['monto_siniestro'];
                            $entityPoliza->tipo_siniestro = $poliza['tipo_siniestro_id'];

                            if ($poliza['tipo_siniestro_id'] == SINIESTRO_VEHICULO) {
                                $entityPoliza->vehiculo = $vehiculoCtrl->getVehiculoRelationPoliza($poliza['poliza']['poliza_id']);
                            }

                            $entityPoliza->aseguradora = $polizaCtrl->getAseguradoraByID($poliza['poliza']['aseguradora_id']);
                            $entityPoliza->calculo = $siniestroCtrl->calculoSiniestro($poliza['monto_siniestro'], $poliza['poliza']['prima_total']);

                            array_push($arrayResultFinal, $entityPoliza);
                        }

                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $arrayResultFinal;
                        $response = parent::setSuccessfulResponse($response);
                    } else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'Usuarios no encontrados.';
                    }

                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }

            } catch (\Exception $e) {
                Log::info("Error Saving the User " . $e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function createReportSiniestralidad()
    {

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if (parent::validJcrJsonHeader($jsonObject)) {

            try {

                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguaradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];
                $ci_or_placa = !isset($jsonObject['JcrParameters']['Reports']['ci_or_placa']) ? null : $jsonObject['JcrParameters']['Reports']['ci_or_placa'];
                $ramo_id = !isset($jsonObject['JcrParameters']['Reports']['ramo_id']) ? null : $jsonObject['JcrParameters']['Reports']['ramo_id'];
                $tipo_busqueda = !isset($jsonObject['JcrParameters']['Reports']['tipo_busqueda']) ? null : $jsonObject['JcrParameters']['Reports']['tipo_busqueda'];

                if (isset($start_date) && isset($end_date)) {
                    $siniestroCtrl = new SiniestroController();
                    $listSiniestro = $siniestroCtrl->getSiniestralidadInfo($start_date, $end_date, $aseguaradora_id, $ci_or_placa, $ramo_id, true, $tipo_busqueda);

                    if (isset($listSiniestro)) {

                        $name_report = NAME_FILE . rand(10000, 99999) . '.pdf';

                        $polizaTable = TableRegistry::get("Poliza");
                        $siniestralidad = $polizaTable->newEntity();

                        $siniestralidad->start_date = $start_date;
                        $siniestralidad->end_date = $end_date;
                        $siniestralidad->lista_siniestralidad = $listSiniestro;

                        $url = $this->buildPDF(TEMPLATE_SINIESTRALIDAD, $name_report, $siniestralidad);

                        $response['JcrResponse']['url_pdf'] = $url;
                        $response = parent::setSuccessfulResponse($response);

                    } else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'Usuarios no encontrados.';
                    }


                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }

            } catch (\Exception $e) {
                Log::info("Error Saving the User " . $e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function getSumasAsegurasInfo()
    {

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if (parent::validJcrJsonHeader($jsonObject)) {

            try {

                $page = $jsonObject['JcrParameters']['Reports']["page"];
                $limit = !isset($jsonObject['JcrParameters']['Reports']["limit"]) ? 10 : $jsonObject['JcrParameters']['Reports']["limit"];

                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguaradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];
                $montoSa = !isset($jsonObject['JcrParameters']['Reports']['monto']) ? null : $jsonObject['JcrParameters']['Reports']['monto'];


                if (isset($montoSa)) {

                    $polizaTable = TableRegistry::get("Poliza");
                    $polizaCtrl = new PolizaController();
                    $vehiculoCtrl = new VehiculoController();
                    $clientCtrl = new ClientController();
                    $siniestroCtrl = new SiniestroController();

                    $polizaFound = $polizaCtrl->getAumentoSA($start_date, $end_date, $montoSa, $aseguaradora_id, false);

                    Log::info($polizaFound);

                    $count = $polizaFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $polizaFound = $this->paginate($polizaFound);


                    if ($polizaFound->count() > 0) {

                        $maxPages = floor((($count - 1) / $limit) + 1);

                        $polizaFound = $polizaFound->toArray();

                        $arrayResultFinal = array();

                        //tratar el arreglo de polizas
                        foreach ($polizaFound as $poliza) {

                            $entityPoliza = $polizaTable->newEntity();

                            if ($poliza['ramo_id'] == RAMO_AUTO_INDIVIDUAL || $poliza['ramo_id'] == RAMO_AUTO_FLOTA) {

                                $entityPoliza->poliza_id = $poliza['poliza_id'];
                                $entityPoliza->numero_poliza = $poliza['numero_poliza'];
                                $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['cliente_id_titular']);
                                $entityPoliza->agente = $poliza['agente'];
                                $entityPoliza->prima_total = $poliza['prima_total'];
                                $entityPoliza->fecha_vencimiento = $poliza['fecha_vencimiento'];
                                $entityPoliza->fecha_emision = $poliza['fecha_emision'];
                                $entityPoliza->ramo = $siniestroCtrl->getRamoSystem($poliza['ramo_id'])[0];
                                $entityPoliza->vehiculos = $vehiculoCtrl->getVehiculoRelationPoliza($poliza['poliza_id']);
                                $entityPoliza->suma_asegurada = $polizaCtrl->getCoberturasDeLaPoliza($poliza['poliza_id']);
                                $entityPoliza->aseguradora = $polizaCtrl->getAseguradoraByID($poliza['aseguradora_id'])[0]['aseguradora_nombre'];

                            } else {
                                $entityPoliza->poliza_id = $poliza['poliza_id'];
                                $entityPoliza->numero_poliza = $poliza['numero_poliza'];
                                $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['cliente_id_titular']);
                                $entityPoliza->agente = $poliza['agente'];
                                $entityPoliza->prima_total = $poliza['prima_total'];
                                $entityPoliza->fecha_vencimiento = $poliza['fecha_vencimiento'];
                                $entityPoliza->ramo = $siniestroCtrl->getRamoSystem($poliza['ramo_id'])[0];
                                $entityPoliza->suma_asegurada = $polizaCtrl->getCoberturasDeLaPoliza($poliza['poliza_id']);
                                $entityPoliza->aseguradora = $polizaCtrl->getAseguradoraByID($poliza['aseguradora_id'])[0]['aseguradora_nombre'];
                            }

                            array_push($arrayResultFinal, $entityPoliza);
                        }

                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $arrayResultFinal;
                        $response = parent::setSuccessfulResponse($response);

                    } else {
                        $response['JcrResponse']['code'] = '1';
                        $response['JcrResponse']['message'] = 'Polizas no encontradas.';
                    }

                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error Saving the User " . $e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function createReportSA()
    {

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if (parent::validJcrJsonHeader($jsonObject)) {

            try {

                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguaradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];
                $montoSa = !isset($jsonObject['JcrParameters']['Reports']['monto']) ? null : $jsonObject['JcrParameters']['Reports']['monto'];


                if (isset($montoSa)) {

                    $polizaCtrl = new PolizaController();
                    $result = $polizaCtrl->getAumentoSA($start_date, $end_date, $montoSa, $aseguaradora_id, true);

                    Log::info(json_encode($result));

                    if (isset($result)) {

                        $name_report = NAME_FILE . rand(10000, 99999) . '.pdf';

                        $polizaTable = TableRegistry::get("Poliza");
                        $polizaSA = $polizaTable->newEntity();

                        $polizaSA->start_date = $start_date;
                        $polizaSA->end_date = $end_date;
                        $polizaSA->monto_filtro = $montoSa;
                        $polizaSA->polizas = $result;

                        $url = $this->buildPDF(TEMPLATE_SA, $name_report, $polizaSA);

                        $response['JcrResponse']['url_pdf'] = $url;
                        $response = parent::setSuccessfulResponse($response);

                    } else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'Poliza no encontradas.';
                    }


                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }

            } catch (\Exception $e) {
                Log::info("Error Saving the User " . $e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }


        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Objects: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function getInfoVentasCruzadas()
    {

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject)) {

            try {
                $page = $jsonObject['JcrParameters']['Reports']["page"];
                $limit = !isset($jsonObject['JcrParameters']['Reports']["limit"]) ? 10 : $jsonObject['JcrParameters']['Reports']["limit"];

                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguaradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];
                $montoSa = !isset($jsonObject['JcrParameters']['Reports']['monto']) ? null : $jsonObject['JcrParameters']['Reports']['monto'];


                if (isset($start_date) && isset($end_date)) {


                    $polizaTable = TableRegistry::get("Poliza");
                    $polizaCtrl = new PolizaController();
                    $vehiculoCtrl = new VehiculoController();
                    $clientCtrl = new ClientController();
                    $siniestroCtrl = new SiniestroController();

                    $polizaFound = $polizaCtrl->getListVentasCruzadas($start_date, $end_date, $montoSa, $aseguaradora_id, false);

                    $count = $polizaFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $polizaFound = $this->paginate($polizaFound);


                    if ($polizaFound->count() > 0) {

                        $maxPages = floor((($count - 1) / $limit) + 1);

                        $polizaFound = $polizaFound->toArray();

                        Log::info("Arreglo final:" + json_encode($polizaFound));
                        $arrayResultFinal = array();

                        //tratar el arreglo de polizas
                        foreach ($polizaFound as $poliza) {

                            $entityPoliza = $polizaTable->newEntity();

                            if ($poliza['ramo_id'] == RAMO_AUTO_INDIVIDUAL || $poliza['ramo_id'] == RAMO_AUTO_FLOTA) {

                                $entityPoliza->poliza_id = $poliza['poliza_id'];
                                $entityPoliza->numero_poliza = $poliza['numero_poliza'];
                                $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['cliente_id_titular']);
                                $entityPoliza->agente = $poliza['agente'];
                                $entityPoliza->prima_total = $poliza['prima_total'];
                                $entityPoliza->fecha_vencimiento = $poliza['fecha_vencimiento'];
                                $entityPoliza->fecha_emision = $poliza['fecha_emision'];
                                $entityPoliza->ramo = $siniestroCtrl->getRamoSystem($poliza['ramo_id'])[0];
                                $entityPoliza->vehiculos = $vehiculoCtrl->getVehiculoRelationPoliza($poliza['poliza_id']);
                                $entityPoliza->suma_asegurada = $polizaCtrl->getCoberturasDeLaPoliza($poliza['poliza_id']);
                                $entityPoliza->aseguradora = $polizaCtrl->getAseguradoraByID($poliza['aseguradora_id'])[0]['aseguradora_nombre'];

                            } else {
                                $entityPoliza->poliza_id = $poliza['poliza_id'];
                                $entityPoliza->numero_poliza = $poliza['numero_poliza'];
                                $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['cliente_id_titular']);
                                $entityPoliza->agente = $poliza['agente'];
                                $entityPoliza->prima_total = $poliza['prima_total'];
                                $entityPoliza->fecha_vencimiento = $poliza['fecha_vencimiento'];
                                $entityPoliza->ramo = $siniestroCtrl->getRamoSystem($poliza['ramo_id'])[0];
                                $entityPoliza->suma_asegurada = $polizaCtrl->getCoberturasDeLaPoliza($poliza['poliza_id']);
                                $entityPoliza->aseguradora = $polizaCtrl->getAseguradoraByID($poliza['aseguradora_id'])[0]['aseguradora_nombre'];
                            }

                            array_push($arrayResultFinal, $entityPoliza);
                        }

                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $arrayResultFinal;
                        $response = parent::setSuccessfulResponse($response);

                    } else {
                        $response['JcrResponse']['code'] = '1';
                        $response['JcrResponse']['message'] = 'Polizas no encontradas';
                    }
                }

            } catch (\Exception $e) {
                Log::info("Error Saving the User " . $e);
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Objects: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function createReportVentasCruzadas()
    {

        Log::info("Obtener informacion siniestralidad");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject)) {

            $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
            $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
            $aseguaradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];
            $montoSa = !isset($jsonObject['JcrParameters']['Reports']['monto']) ? null : $jsonObject['JcrParameters']['Reports']['monto'];

            if (isset($start_date) && isset($end_date)) {

                try {

                    $polizaCtrl = new PolizaController();
                    $polizaFound = $polizaCtrl->getListVentasCruzadas($start_date, $end_date, $montoSa, $aseguaradora_id, true);

                    Log::info(json_encode($polizaFound));

                    if (isset($polizaFound)) {

                        $name_report = NAME_FILE . rand(10000, 99999) . '.pdf';

                        $polizaTable = TableRegistry::get("Poliza");
                        $polizaVC = $polizaTable->newEntity();

                        $polizaVC->start_date = $start_date;
                        $polizaVC->end_date = $end_date;
                        $polizaVC->monto_filtro = $montoSa;
                        $polizaVC->polizas = $polizaFound;

                        $url = $this->buildPDF(TEMPLATE_VENTAS_CRUZADAS, $name_report, $polizaVC);

                        $response['JcrResponse']['url_pdf'] = $url;
                        $response = parent::setSuccessfulResponse($response);

                    } else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'Polizas no encontradas';
                    }


                } catch (\Exception $e) {
                    Log::info("Error Saving the User " . $e);
                    $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                    $response['JcrResponse']['message'] = $e->getMessage();
                }

            } else {
                $response = parent::setInvalidJsonMessage($response);
            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Objects: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function getRamoSystem($ramo_id)
    {

        $ramoTable = TableRegistry::get("Ramo");
        $ramoFound = $ramoTable->findByRamoId($ramo_id);

        if ($ramoFound->count() > 0) {
            $ramoFound = $ramoFound->toArray();
        } else {
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

    public function enviarReportesRenovacion()
    {

        Log::info("Obtener informacion email renovacion");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();


        if (parent::validJcrJsonHeader($jsonObject)) {

            try {
                $start_date = !isset($jsonObject['JcrParameters']['Reports']['start_date']) ? null : $jsonObject['JcrParameters']['Reports']['start_date'];
                $end_date = !isset($jsonObject['JcrParameters']['Reports']['end_date']) ? null : $jsonObject['JcrParameters']['Reports']['end_date'];
                $aseguradora_id = !isset($jsonObject['JcrParameters']['Reports']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Reports']['aseguradora_id'];


                if (isset($start_date) && isset($end_date)) {

                    $polizaCtrl = new PolizaController();
                    $polizaTable = TableRegistry::get("Poliza");
                    $polizaRenovaciones = $polizaTable->newEntity();

                    $result = $polizaCtrl->getListPolizaRenovaciones($start_date, $end_date, $aseguradora_id, true);

                    if (isset($result)) {

                        foreach ($result as $objetos) {

                            Log::info("Numero de poliza: " . $objetos['numero_poliza']);
                            Log::info("Nombre  de Cliente: " . $objetos['asegurado']['nombre_cliente']);
                            Log::info("Apellido  de Cliente: " . $objetos['asegurado']['apellido_cliente']);
                            Log::info("Correo  del Cliente: " . $objetos['asegurado']['correo_cliente']);
                            Log::info("Numero de poliza: " . $objetos['numero_poliza']);
                            Log::info("Ramo  del Cliente: " . $objetos['ramo']['ramo_nombre']);


                            $nombre_cliente = $objetos['asegurado']['nombre_cliente'] . ' ' . $objetos['asegurado']['apellido_cliente'];


                            $to = $objetos['asegurado']['correo_cliente'];
                            $subject = 'Póliza en proceso de renovación';
                            $template = 'email_renovacion';
                            $params = array(
                                'nombre_cliente' => $nombre_cliente,
                                'ramo_nombre' => $objetos['ramo']['ramo_nombre'],
                                'numero_poliza' => $objetos ['numero_poliza']);



                            ReaxiumUtil::sendMail($to, $subject, $template, $params);
                            Log::info("mensaje enviado");
                        }


                    } else {
                        $response['JcrResponse']['code'] = '1';
                        $response['JcrResponse']['message'] = 'Póliza no encontradas';
                    }

                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }

            } catch (\Exception $e) {
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


}