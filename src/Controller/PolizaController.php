<?php
/**
 * Created by PhpStorm.
 * User: EduardoDeLaCruz
 * Date: 23/8/2016
 * Time: 11:30
 */

namespace App\Controller;

use App\Util\ReaxiumUtil;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Util\ReaxiumApiMessages;

define('RAMO_AUTO_FLOTA', 3);
define('RAMO_AUTO_INDIVIDUAL', 4);

class PolizaController extends JcrAPIController
{


    public function servicioCrearPoliza()
    {
        Log::info("Crear o actualiza una poliza en sistema");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));
        if (parent::validJcrJsonHeader($jsonObject)) {
            try {
                if (isset($jsonObject['JcrParameters']["Poliza"])) {

                    $polizaRequest = $jsonObject['JcrParameters']["Poliza"];
                    $polizaTable = TableRegistry::get("Poliza");
                    $polizaCoberturaTable = TableRegistry::get("PolizaCobertura");
                    $clienteTable = TableRegistry::get("Clientes");
                    $vehiculoTable = TableRegistry::get("Vehiculo");
                    $relationPolizaVehiculo = TableRegistry::get("PolizaVehiculo");
                    $financiamientoTable = TableRegistry::get("Financiamientos");

                    $conn = $polizaTable->connection();
                    //bloque transaccional de salvado de poliza
                    $conn->transactional(function () use (
                        $polizaRequest,
                        $polizaTable,
                        $polizaCoberturaTable,
                        $clienteTable,
                        $vehiculoTable,
                        $relationPolizaVehiculo,
                        $financiamientoTable
                    ) {
                        $this->procesarLaCreacionDeLaPoliza($polizaTable, $clienteTable, $polizaCoberturaTable, $vehiculoTable, $relationPolizaVehiculo, $polizaRequest, $financiamientoTable);
                    });

                    $response = parent::setSuccessfulSave($response);

                } else {
                    $response = parent::seInvalidParametersMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error guardando la poliza " . $e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }

    /**
     *
     * Procceso de negocio de creacion de los datos pertinentes a una poliza
     *
     * @param $polizaTable
     * @param $clienteTable
     * @param $polizaCoberturaTable
     * @param $polizaJson
     * @throws Exception
     */
    private function procesarLaCreacionDeLaPoliza($polizaTable, $clienteTable, $polizaCoberturaTable, $vehiculoTable, $relationPolizaVehiculo, $polizaJson, $financiamientoTable)
    {
        $clienteController = new ClientController();
        $polizaCreationResult = null;
        $poliza_id = null;
        if ($polizaJson['asegurado']['es_tomador']) {
            $aseguradoResult = $clienteController->createOrEditAClientBatch($clienteTable, $polizaJson['asegurado']);
            $polizaCreationResult = $this->crearPoliza($polizaTable, $aseguradoResult['cliente_id'], $aseguradoResult['cliente_id'], $polizaJson);
        } else {
            $aseguradoResult = $clienteController->createOrEditAClientBatch($clienteTable, $polizaJson['asegurado']);
            $tomadorResultResult = $clienteController->createOrEditAClientBatch($clienteTable, $polizaJson['tomador']);
            $polizaCreationResult = $this->crearPoliza($polizaTable, $aseguradoResult['cliente_id'], $tomadorResultResult['cliente_id'], $polizaJson);
        }


        if ($polizaCreationResult) {

            $poliza_id = $polizaCreationResult['poliza_id'];
            $ramoPoliza_id = $polizaCreationResult['ramo_id'];


            // valido si hay financiamiento

            if ($polizaJson['es_financiado']) {

                $polizaCoberturaResult = $this->createFinanciamiento($financiamientoTable, $polizaJson['financiamento'], $poliza_id);

                if (!isset($polizaCoberturaResult)) {
                    Log::info($polizaCoberturaResult);
                    throw new Exception("Fallo la creacion del financiamiento");
                }
            }


            $polizaCoberturaResult = $this->agregarCoberturaALaPoliza($polizaCoberturaTable, $polizaCreationResult, $polizaJson);


            if (!$polizaCoberturaResult) {
                Log::info($polizaCoberturaResult);
                throw new Exception("Fallo la creacion de la poliza");
            } else {

                //validando si el ramo de auto
                if ($ramoPoliza_id == RAMO_AUTO_FLOTA || $ramoPoliza_id == RAMO_AUTO_INDIVIDUAL) {
                    $vehiculoCtrl = new VehiculoController();
                    $vehiculoResult = $vehiculoCtrl->createAVehiculo($vehiculoTable, $polizaJson['vehiculo']);

                    if (isset($vehiculoResult)) {

                        $editMode = isset($polizaJson['poliza_id']) ? true : false;
                        $relationVehiPoli = $vehiculoCtrl->relationShipPolizaVehiculo($relationPolizaVehiculo, $poliza_id, $vehiculoResult, $editMode);

                        if (isset($relationVehiPoli)) {
                            Log::info("Proceso de creacion de poliza finaliado con exito");
                        } else {
                            Log::info("Error relacionando vehiculo con poliza");
                            throw new Exception("Error relacionando vehiculo con poliza");
                        }

                    } else {
                        Log::info("Error salvando vehiculo");
                        throw new Exception("Fallo la creacion de vehiculo");
                    }

                }
            }
        } else {
            Log::info($polizaCreationResult);
            throw new Exception("Fallo la creacion de la poliza");
        }
    }

    /**
     *
     * Agrega las coberturas y sus descripciones con monto a la poliza
     *
     * @param $polizaCoberturaTable
     * @param $polizaSalvada
     * @param $polizaJson
     * @return bool
     */
    private function agregarCoberturaALaPoliza($polizaCoberturaTable, $polizaSalvada, $polizaJson)
    {
        $coberturasDeLaPoliza = $polizaJson['ramo']['coberturas'];
        $polizaCoberturaReuslt = false;

        $polizaCoberturaTable->deleteAll(array('poliza_id' => $polizaSalvada['poliza_id']));

        foreach ($coberturasDeLaPoliza as $cobertura) {
            $descripcionDeCoberturasEnLaPoliza = $cobertura['descripciones_cobertura'];
            foreach ($descripcionDeCoberturasEnLaPoliza as $descripcionDeCobertura) {
                $polizaCoberturaObject = $polizaCoberturaTable->newEntity();
                if (isset($descripcionDeCobertura['poliza_coberturas_id'])) {
                    $polizaCoberturaObject->poliza_coberturas_id = $descripcionDeCobertura['poliza_coberturas_id'];
                }
                $polizaCoberturaObject->poliza_id = $polizaSalvada['poliza_id'];
                $polizaCoberturaObject->cobertura_id = $cobertura['cobertura_id'];
                $polizaCoberturaObject->descripcion_cobertura_id = $descripcionDeCobertura['descripcion_cobertura_id'];
                $polizaCoberturaObject->monto = $descripcionDeCobertura['monto'];
                $polizaCoberturaReuslt = $polizaCoberturaTable->save($polizaCoberturaObject);
            }
        }
        return $polizaCoberturaReuslt;
    }

    /**
     *
     * Crea una poliza en sistema con los datos que llegan del cliente web
     *
     * @param $polizaTable
     * @param $tomadorId
     * @param $aseguradoId
     * @param $polizaJson
     * @return mixed
     */
    private function crearPoliza($polizaTable, $tomadorId, $aseguradoId, $polizaJson)
    {
        $polizaEntity = $polizaTable->newEntity();
        $polizaEntity->poliza_id = $polizaJson['poliza_id'];
        $polizaEntity->cliente_id_tomador = $tomadorId;
        $polizaEntity->cliente_id_titular = $aseguradoId;
        $polizaEntity->numero_poliza = $polizaJson['numero_poliza'];
        $polizaEntity->numero_poliza = $polizaJson['numero_poliza'];
        $polizaEntity->ramo_id = $polizaJson['ramo']['ramo_id'];
        $polizaEntity->agente = $polizaJson['agente_helper'];
        $polizaEntity->status_id = 1;
        $polizaEntity->aseguradora_id = $polizaJson['aseguradora_id'];
        $polizaEntity->numero_recibo = $polizaJson['numero_recibo'];
        $polizaEntity->referencia = $polizaJson['referencia'];
        $polizaEntity->prima_total = $polizaJson['prima_total'];
        $polizaEntity->fecha_emision = $polizaJson['fecha_emision'];
        $polizaEntity->fecha_vencimiento = $polizaJson['fecha_vencimiento'];
        $polizaEntity->observacion = $polizaJson['observacion'];
        $polizaResult = $polizaTable->save($polizaEntity);
        return $polizaResult;
    }


    /**
     * Servicio para borrar poliza
     */
    public function borrarPoliza()
    {
        Log::info("Borrar poliza");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject)) {
            $poliza_id = !isset($jsonObject['JcrParameters']['Poliza']['poliza_id']) ? null : $jsonObject['JcrParameters']['Poliza']['poliza_id'];

            if (isset($poliza_id)) {
                try {
                    $polizaTable = TableRegistry::get("Poliza");
                    $entityPoliza = $polizaTable->newEntity();
                    $entityPoliza->poliza_id = $poliza_id;
                    $entityPoliza->status_id = 3;
                    $result = $polizaTable->save($entityPoliza);

                    if ($result) {
                        $response = parent::setSuccessfulDelete($response);
                        $response['JcrResponse']['object'] = $result;
                    } else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudo borrar la poliza en sistema';
                    }
                } catch (\Exception $e) {
                    Log::info("Error borrando poliza del sistema");
                    Log::info($e->getMessage());
                    $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                    $response['JcrResponse']['message'] = 'Error del sistema';
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
     * Servicio para obtener polizas paginadas
     */
    public function allPolizaWithPagination()
    {
        Log::info("Consulta todas las polizas con paginacion");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject)) {
            try {
                if (isset($jsonObject['JcrParameters']['page'])) {
                    $page = $jsonObject['JcrParameters']["page"];
                    $sortedBy = !isset($jsonObject['JcrParameters']["sortedBy"]) ? 'numero_poliza' : $jsonObject['JcrParameters']["sortedBy"];
                    $sortDir = !isset($jsonObject['JcrParameters']["sortDir"]) ? 'desc' : $jsonObject['JcrParameters']["sortDir"];
                    $filter = !isset($jsonObject['JcrParameters']["filter"]) ? '' : $jsonObject['JcrParameters']["filter"];
                    $limit = !isset($jsonObject['JcrParameters']["limit"]) ? 10 : $jsonObject['JcrParameters']["limit"];


                    $polizaFound = $this->getAllPoliza($filter, $sortedBy, $sortDir);

                    $count = $polizaFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $polizaFound = $this->paginate($polizaFound);

                    if ($polizaFound->count() > 0) {
                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $polizaFound = $polizaFound->toArray();
                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $polizaFound;
                        $response = parent::setSuccessfulResponse($response);
                    } else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No Poliza found';
                    }
                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error buscando poliza en el sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * Servicio para obtener polizas paginadas
     */
    public function allPolizaWithPaginationBySiniestro()
    {
        Log::info("Consulta todas las polizas Para los siniestros con paginacion");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject)) {
            try {
                if (isset($jsonObject['JcrParameters']['page'])) {

                    $page = $jsonObject['JcrParameters']["page"];
                    $sortedBy = !isset($jsonObject['JcrParameters']["sortedBy"]) ? 'numero_poliza' : $jsonObject['JcrParameters']["sortedBy"];
                    $sortDir = !isset($jsonObject['JcrParameters']["sortDir"]) ? 'desc' : $jsonObject['JcrParameters']["sortDir"];
                    $filter = !isset($jsonObject['JcrParameters']["filter"]) ? '' : $jsonObject['JcrParameters']["filter"];
                    $limit = !isset($jsonObject['JcrParameters']["limit"]) ? 10 : $jsonObject['JcrParameters']["limit"];
                    $search_by_ci_or_placa = !isset($jsonObject['JcrParameters']["search_code"]) ? 'numero_poliza' : $jsonObject['JcrParameters']["search_code"];

                    $polizaFound = $this->getAllPolizaBySinestros($filter, $sortedBy, $sortDir, $search_by_ci_or_placa);

                    $count = $polizaFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $polizaFound = $this->paginate($polizaFound);

                    if ($polizaFound->count() > 0) {
                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $polizaFound = $polizaFound->toArray();
                        $siniestroCtrl = new SiniestroController();
                        $ramoArray = null;
                        $aseguradoraArray = null;

                        if ($search_by_ci_or_placa > 0 && isset($filter)) {

                            foreach ($polizaFound as $poliza) {

                                $ramoArray = $siniestroCtrl->getRamoSystem($poliza['ramo_id']);
                                if (isset($ramoArray)) {
                                    $poliza['ramo'] = $ramoArray[0];
                                }

                                $aseguradoraArray = $this->getAseguradoraByID($poliza['aseguradora_id']);
                                if (isset($aseguradoraArray)) {
                                    $poliza['aseguradora'] = $aseguradoraArray[0];
                                }
                            }
                        }


                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $polizaFound;
                        $response = parent::setSuccessfulResponse($response);

                    } else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No Poliza found';
                    }
                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error buscando poliza en el sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
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
     * @return $this
     */
    private function getAllPoliza($filter, $sortedBy, $sortDir)
    {
        $polizaTable = TableRegistry::get('Poliza');

        if (trim($filter) != '') {
            $whereCondition = array(array('OR' => array(
                array('numero_poliza LIKE' => '%' . $filter . '%'),
                array('Poliza.ramo_id LIKE' => '%' . $filter . '%'),
                array('Poliza.aseguradora_id LIKE' => '%' . $filter . '%'))));

            //agregar los contain cuando sea necesario
            $polizaFound = $polizaTable->find()
                ->where($whereCondition)
                ->andWhere(array('Poliza.status_id' => 1))
                ->contain(array('Aseguradora', 'Ramo'))
                ->order(array($sortedBy . ' ' . $sortDir));
        } else {
            //agregar los contain cuando sea necesario
            $polizaFound = $polizaTable->find()
                ->where(array('Poliza.status_id' => 1))
                ->contain(array('Aseguradora', 'Ramo'))
                ->order(array($sortedBy . ' ' . $sortDir));
        }

        return $polizaFound;
    }


    /**
     * @param $filter
     * @param $sortedBy
     * @param $sortDir
     * @return $this
     */
    private function getAllPolizaBySinestros($filter, $sortedBy, $sortDir, $search_by_ci_or_placa)
    {
        $polizaTable = TableRegistry::get('Poliza');

        if (trim($filter) != '') {

            switch ($search_by_ci_or_placa) {

                case 1:
                    Log::info("Buscar poliza por CI");

                    $whereCondition = array(array('OR' => array(
                        array('asegurado.documento_id_cliente LIKE' => '%' . $filter . '%'))));

                    $polizaFound = $polizaTable->find('all', array('fields' => array(
                        'Poliza.poliza_id',
                        'Poliza.numero_poliza',
                        'Poliza.ramo_id',
                        'Poliza.cliente_id_tomador',
                        'Poliza.cliente_id_titular',
                        'Poliza.agente',
                        'Poliza.aseguradora_id',
                        'Poliza.prima_total',
                        'Poliza.fecha_vencimiento',
                        'Poliza.numero_recibo',
                        'asegurado.nombre_cliente',
                        'asegurado.apellido_cliente',
                        'asegurado.documento_id_cliente'
                    )))
                        ->join(array(
                            'asegurado' => array('table' => 'clientes', 'type' => 'LEFT', 'conditions' => 'Poliza.cliente_id_titular = asegurado.cliente_id')
                        ))
                        ->where($whereCondition)
                        ->andWhere(array('Poliza.status_id' => 1))
                        ->order(array($sortedBy . ' ' . $sortDir));

                    break;

                case 2:

                    Log::info("Buscar poliza por placa");

                    $whereCondition = array(array('OR' => array(
                        array('vehiculo.vehiculo_placa LIKE' => '%' . $filter . '%'))));

                    $polizaFound = $polizaTable->find('all', array('fields' => array(
                        'Poliza.poliza_id',
                        'Poliza.numero_poliza',
                        'Poliza.ramo_id',
                        'Poliza.cliente_id_tomador',
                        'Poliza.cliente_id_titular',
                        'Poliza.agente',
                        'Poliza.aseguradora_id',
                        'Poliza.prima_total',
                        'Poliza.fecha_vencimiento',
                        'Poliza.numero_recibo',
                        'vehiculo.vehiculo_placa'
                    )))
                        ->join(array(
                            'polizaVehiculo' => array('table' => 'poliza_vehiculo', 'type' => 'LEFT', 'conditions' => 'Poliza.poliza_id = polizaVehiculo.poliza_id'),
                            'vehiculo' => array('table' => 'vehiculo', 'type' => 'LEFT', 'conditions' => 'polizaVehiculo.vehiculo_id = vehiculo.vehiculo_id')))
                        ->where($whereCondition)
                        ->andWhere(array('Poliza.status_id' => 1))
                        ->order(array($sortedBy . ' ' . $sortDir));

                    break;

                default:

                    $whereCondition = array(array('OR' => array(
                        array('numero_poliza LIKE' => '%' . $filter . '%'))));

                    //agregar los contain cuando sea necesario
                    $polizaFound = $polizaTable->find()
                        ->where($whereCondition)
                        ->andWhere(array('Poliza.status_id' => 1))
                        ->contain(array('Aseguradora', 'Ramo'))
                        ->order(array($sortedBy . ' ' . $sortDir));

                    break;
            }

        } else {
            //agregar los contain cuando sea necesario
            $polizaFound = $polizaTable->find()
                ->where(array('Poliza.status_id' => 1))
                ->contain(array('Aseguradora', 'Ramo'))
                ->order(array($sortedBy . ' ' . $sortDir));

        }

        return $polizaFound;
    }

    public function getPolizaById()
    {
        Log::info("Informacion poliza por ID");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject)) {
            $poliza_id = !isset($jsonObject['JcrParameters']['Poliza']['poliza_id']) ? null : $jsonObject['JcrParameters']['Poliza']['poliza_id'];

            try {
                if (isset($poliza_id)) {
                    $polizaTable = TableRegistry::get("Poliza");
                    $polizaFound = $polizaTable->find()
                        ->where(array('poliza_id' => $poliza_id))
                        ->contain(array('Aseguradora', 'Ramo'));

                    if ($polizaFound->count() > 0) {

                        $clienteController = new ClientController();

                        $polizaFound = $polizaFound->toArray();

                        //verifico si existe financiamiento
                        $financiamiento = $this->getFinanciamientoByPoliza($poliza_id);

                        if (isset($financiamiento)) {
                            $polizaFound[0]['es_financiado'] = true;
                            $polizaFound[0]['financiamento'] = $financiamiento[0];
                        } else {
                            $polizaFound[0]['es_financiado'] = false;
                            $polizaFound[0]['financiamento'] = [];
                        }

                        $polizaFound[0]['asegurado'] = $clienteController->getClientById($polizaFound[0]['cliente_id_titular']);
                        $polizaFound[0]['tomador'] = $clienteController->getClientById($polizaFound[0]['cliente_id_tomador']);

                        if ($polizaFound[0]['cliente_id_tomador'] == $polizaFound[0]['cliente_id_titular']) {
                            $polizaFound[0]['asegurado']['es_tomador'] = true;
                        } else {
                            $polizaFound[0]['asegurado']['es_tomador'] = false;
                        }


                        $polizaFound[0]['ramo']['coberturas'] = $this->getCoberturasDeLaPoliza($poliza_id);

                        if ($polizaFound[0]['ramo_id'] == RAMO_AUTO_INDIVIDUAL ||
                            $polizaFound[0]['ramo_id'] == RAMO_AUTO_FLOTA
                        ) {

                            $vehiculoCtrl = new VehiculoController();
                            $vehiculoListPoliza = $vehiculoCtrl->getVehiculoRelationPoliza($poliza_id);

                            if (isset($vehiculoListPoliza)) {
                                $polizaFound[0]['vehiculo'] = $vehiculoListPoliza;
                            } else {
                                $polizaFound[0]['vehiculo'] = array();
                            }

                        }


                        $response['JcrResponse']['object'] = $polizaFound;
                        $response = parent::setSuccessfulResponse($response);

                    } else {
                        $polizaFound = null;
                        $response['JcrResponse']['code'] = "1";
                        $response['JcrResponse']['message'] = "Poliza no encontrada en sistema";
                        $response['JcrResponse']['object'] = $polizaFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }
            } catch (\Exception $e) {
                Log::info($e);
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function getCoberturasDeLaPoliza($polizaId)
    {
        $coberturasDeLaPoliza = array();
        $coberturas = array('cobertura_id', 'cobertura_nombre', 'descripciones_cobertura' => array());
        $desCripcionDeCoberturas = array('descripcion_cobertura_id', 'descripcion_cobertura_nombre', 'monto' => '0');
        $polizaCoberturaTabla = TableRegistry::get("PolizaCobertura");
        $polizaCoberturaResult = $polizaCoberturaTabla->find()
            ->select(array('cobertura_id' => 'Cobertura.cobertura_id',
                'cobertura_nombre' => 'Cobertura.cobertura_nombre',
                'descripcion_cobertura_id' => 'DescripcionCobertura.descripcion_cobertura_id',
                'descripcion_cobertura_nombre' => 'DescripcionCobertura.descripcion_cobertura_nombre',
                'monto' => 'PolizaCobertura.monto'))
            ->where(array('PolizaCobertura.poliza_id' => $polizaId))
            ->contain(array('Cobertura', 'DescripcionCobertura'));
        if ($polizaCoberturaResult->count() > 0) {

            $polizaCoberturaResult = $polizaCoberturaResult->toArray();
            $auxArray = ReaxiumUtil::arrayCopy($polizaCoberturaResult);

            Log::info("POliza Cobertura: " . json_encode($polizaCoberturaResult));


            foreach ($polizaCoberturaResult as $coberturasEnPoliza) {

                $listCoberturas = $this->extraerListDecripcionCoberturas($coberturasEnPoliza['cobertura_id'], $auxArray);

                if (!$this->existeCoberturaLista($coberturasEnPoliza['cobertura_id'], $coberturasDeLaPoliza)) {

                    $arraFinalDCober = array();

                    foreach ($listCoberturas as $cober) {
                        $entityCober = $polizaCoberturaTabla->newEntity();
                        $entityCober->descripcion_cobertura_id = $cober['descripciones_cobertura']['descripcion_cobertura_id'];
                        $entityCober->descripcion_cobertura_nombre = $cober['descripciones_cobertura']['descripcion_cobertura_nombre'];
                        $entityCober->monto = $cober['descripciones_cobertura']['monto'];
                        array_push($arraFinalDCober, $entityCober);
                    }


                    $coberturas = array('cobertura_id' => $coberturasEnPoliza['cobertura_id'],
                        'cobertura_nombre' => $coberturasEnPoliza['cobertura_nombre'],
                        'descripciones_cobertura' => $arraFinalDCober);

                    array_push($coberturasDeLaPoliza, $coberturas);
                }

            }
        }
        return $coberturasDeLaPoliza;
    }


    private function extraerListDecripcionCoberturas($cobertura_id, $auxArray)
    {

        $polizaCoberturaTabla = TableRegistry::get("PolizaCobertura");
        $listCoberturas = array();

        foreach ($auxArray as $cobertura) {

            if ($cobertura['cobertura_id'] == $cobertura_id) {

                $entityCoberturas = $polizaCoberturaTabla->newEntity();
                $entityCoberturas->descripciones_cobertura = array('descripcion_cobertura_id' => $cobertura['descripcion_cobertura_id'],
                    'descripcion_cobertura_nombre' => $cobertura['descripcion_cobertura_nombre'], 'monto' => $cobertura['monto']);

                array_push($listCoberturas, $entityCoberturas);
            }

        }

        return $listCoberturas;
    }


    private function existeCoberturaLista($cobertura_id, $listCobertura)
    {

        $existeCobertura = false;

        foreach ($listCobertura as $cobertura) {

            if ($cobertura['cobertura_id'] == $cobertura_id) {
                $existeCobertura = true;
                break;
            }

        }

        return $existeCobertura;
    }

    private function getUserById($user_id)
    {

        Log::info("User ID: " + $user_id);

        $userTable = TableRegistry::get("Client");
        $userFound = $userTable->find()->where(array('usuario_id' => $user_id))->contain(array("TipoUsuario"));

        $entityUser = null;

        if ($userFound->count() > 0) {
            $userFound = $userFound->toArray();
            $entityUser = $userTable->newEntity();

            foreach ($userFound as $row) {

                $entityUser->usuario_id = $row['usuario_id'];
                $entityUser->nombre = $row['nombre'];
                $entityUser->apellido = $row['apellido'];
                $entityUser->documento_id = $row['documento_id'];
                $entityUser->tipo_usuario_id = $row['tipo_usuario']['tipo_usuario_id'];
                $entityUser->tipo_usuario_name = $row['tipo_usuario']['tipo_usuario_nombre'];
            }

        } else {
            $entityUser = null;
        }

        return $entityUser;
    }


    private function getTypeUser($tipo_usuario_id)
    {

        $typeUserTable = TableRegistry::get("TipoUsuario");
        $typeUserFound = $typeUserTable->findByTipoUsuarioId($tipo_usuario_id);

        if ($typeUserFound->count() > 0) {
            $typeUserFound = $typeUserFound->toArray();
        } else {
            $typeUserFound = null;
        }

        return $typeUserFound;
    }


    public function filterPoliza()
    {
        Log::info("Informacion poliza por filtro");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject)) {
            try {
                if (isset($jsonObject['JcrParameters']['Poliza']['filter'])) {
                    $filter = $jsonObject['JcrParameters']['Poliza']['filter'];
                    $polizaTable = TableRegistry::get('Poliza');
                    $whereCondition = array(array('OR' => array(
                        array('numero_poliza LIKE' => '%' . $filter . '%'),
                        array('ramo_id LIKE' => '%' . $filter . '%'),
                        array('aseguradora_id LIKE' => '%' . $filter . '%')
                    )));

                    //agregar el contain cuando sea necesario
                    $polizaFound = $polizaTable->find()
                        ->where($whereCondition)
                        ->order(array('numero_poliza', 'ramo_id'));


                    if ($polizaFound->count() > 0) {
                        $polizaFound = $polizaFound->toArray();
                        $response['JcrResponse']['object'] = $polizaFound;
                        $response = parent::setSuccessfulResponse($response);
                    } else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No Poliza found';
                    }
                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error poliza en el sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * @param $start_date
     * @param $end_date
     * @param $aseguradora_id
     * @return $this|array|null
     */
    public function getListPolizaRenovaciones($start_date, $end_date, $aseguradora_id, $mode)
    {

        try {

            $polizaTable = TableRegistry::get("Poliza");
            $vehiculoTable = TableRegistry::get("Vehiculo");
            $vehiculoCtrl = new VehiculoController();
            $clientCtrl = new ClientController();

            $conditions = array();

            // condicion de fecha y validacion
            if (isset($start_date)) {
                $startDateCondition = array('Poliza.fecha_vencimiento >=' => $start_date);
                array_push($conditions, $startDateCondition);
                if (isset($end_date)) {
                    $endDateCondition = array('Poliza.fecha_vencimiento <=' => $end_date);
                    array_push($conditions, $endDateCondition);
                }
            }

            //condicion de tipo de acceso
            if (isset($aseguradora_id)) {
                $aseguradoraCondition = array('Poliza.aseguradora_id' => $aseguradora_id);
                array_push($conditions, $aseguradoraCondition);
            }

            $polizaFound = $polizaTable->find()->where($conditions)->contain(array('Aseguradora', 'Ramo'));

            if ($mode) {

                if ($polizaFound->count() > 0) {

                    $polizaFound = $polizaFound->toArray();

                    $result = array();

                    //tratar el arreglo de polizas
                    foreach ($polizaFound as $poliza) {

                        $entityPoliza = $vehiculoTable->newEntity();

                        if ($poliza['ramo']['ramo_id'] == RAMO_AUTO_INDIVIDUAL || $poliza['ramo']['ramo_id'] == RAMO_AUTO_FLOTA) {

                            $entityPoliza->poliza_id = $poliza['poliza_id'];
                            $entityPoliza->numero_poliza = $poliza['numero_poliza'];
                            $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['cliente_id_titular']);
                            $entityPoliza->agente = $poliza['agente'];
                            $entityPoliza->prima_total = $poliza['prima_total'];
                            $entityPoliza->fecha_vencimiento = $poliza['fecha_vencimiento'];
                            $entityPoliza->ramo = $poliza['ramo'];
                            $entityPoliza->vehiculos = $vehiculoCtrl->getVehiculoRelationPoliza($poliza['poliza_id']);
                            $entityPoliza->suma_asegurada = $this->getCoberturasDeLaPoliza($poliza['poliza_id']);
                            $entityPoliza->aseguradora = $this->getAseguradoraByID($poliza['aseguradora_id'])[0]['aseguradora_nombre'];

                        } else {
                            $entityPoliza->poliza_id = $poliza['poliza_id'];
                            $entityPoliza->numero_poliza = $poliza['numero_poliza'];
                            $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['cliente_id_titular']);
                            $entityPoliza->agente = $poliza['agente'];
                            $entityPoliza->prima_total = $poliza['prima_total'];
                            $entityPoliza->fecha_vencimiento = $poliza['fecha_vencimiento'];
                            $entityPoliza->ramo = $poliza['ramo'];
                            $entityPoliza->suma_asegurada = $this->getCoberturasDeLaPoliza($poliza['poliza_id']);
                            $entityPoliza->aseguradora = $this->getAseguradoraByID($poliza['aseguradora_id'])[0]['aseguradora_nombre'];
                        }

                        array_push($result, $entityPoliza);
                    }

                    $polizaFound = ReaxiumUtil::arrayCopy($result);

                } else {
                    $polizaFound = null;
                }
            }

        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $polizaFound = null;
        }

        return $polizaFound;
    }


    public function getAumentoSA($start_date, $end_date, $monto, $aseguradora_id, $mode)
    {

        try {

            $polizaTable = TableRegistry::get("Poliza");

            $conditions = array();

            // condicion de fecha y validacion
            if (isset($start_date)) {
                $startDateCondition = array('Poliza.fecha_emision >=' => $start_date);
                array_push($conditions, $startDateCondition);
                if (isset($end_date)) {
                    $endDateCondition = array('Poliza.fecha_emision <=' => $end_date);
                    array_push($conditions, $endDateCondition);
                }
            }

            //agregar montos de busqueda

            if (isset($monto)) {
                $montoMinCondition = array('cobertura.descripcion_cobertura_id' => 1, 'cobertura.monto <' => $monto);
                array_push($conditions, $montoMinCondition);

            }

            if (isset($aseguradora_id)) {
                $aseguradoraCondition = array('Poliza.aseguradora_id' => $aseguradora_id);
                array_push($conditions, $aseguradoraCondition);
            }


            $polizaFound = $polizaTable->find('all', array('fields' => array(
                'Poliza.poliza_id',
                'Poliza.numero_poliza',
                'Poliza.ramo_id',
                'Poliza.cliente_id_tomador',
                'Poliza.cliente_id_titular',
                'Poliza.agente',
                'Poliza.aseguradora_id',
                'Poliza.prima_total',
                'Poliza.fecha_vencimiento',
                'Poliza.fecha_emision',
                'cobertura.monto'
            )))
                ->join(array(
                    'cobertura' => array('table' => 'poliza_coberturas', 'type' => 'LEFT', 'conditions' => 'Poliza.poliza_id = cobertura.poliza_id')
                ))
                ->where($conditions);


            if ($mode) {

                if ($polizaFound->count() > 0) {

                    $polizaFound = $polizaFound->toArray();

                    $vehiculoTable = TableRegistry::get("Vehiculo");
                    $vehiculoCtrl = new VehiculoController();
                    $clientCtrl = new ClientController();
                    $siniestroCtrl = new SiniestroController();

                    $result = array();

                    //tratar el arreglo de polizas
                    foreach ($polizaFound as $poliza) {

                        $entityPoliza = $vehiculoTable->newEntity();

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
                            $entityPoliza->suma_asegurada = $this->getCoberturasDeLaPoliza($poliza['poliza_id']);
                            $entityPoliza->aseguradora = $this->getAseguradoraByID($poliza['aseguradora_id'])[0]['aseguradora_nombre'];

                        }
                        else {

                            $entityPoliza->poliza_id = $poliza['poliza_id'];
                            $entityPoliza->numero_poliza = $poliza['numero_poliza'];
                            $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['cliente_id_titular']);
                            $entityPoliza->agente = $poliza['agente'];
                            $entityPoliza->prima_total = $poliza['prima_total'];
                            $entityPoliza->fecha_vencimiento = $poliza['fecha_vencimiento'];
                            $entityPoliza->fecha_emision = $poliza['fecha_emision'];
                            $entityPoliza->ramo = $siniestroCtrl->getRamoSystem($poliza['ramo_id'])[0];
                            $entityPoliza->suma_asegurada = $this->getCoberturasDeLaPoliza($poliza['poliza_id']);
                            $entityPoliza->aseguradora = $this->getAseguradoraByID($poliza['aseguradora_id'])[0]['aseguradora_nombre'];

                        }

                        array_push($result, $entityPoliza);
                    }

                    $polizaFound = ReaxiumUtil::arrayCopy($result);
                } else {
                    $polizaFound = null;
                }

            }


        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $polizaFound = null;
        }

        return $polizaFound;

    }


    public function createFinanciamiento($financiamientoTable, $jsonFinanciamiento, $poliza_id)
    {

        $result = null;

        try {

            Log::info("Objeto Financiamiento: " . json_encode($jsonFinanciamiento));
            Log::info("Poliza: " . $poliza_id);

            $entityFinanciamiento = $financiamientoTable->newEntity();

            if (isset($jsonFinanciamiento['financiamiento_id'])) {
                $entityFinanciamiento->financiamiento_id = $jsonFinanciamiento['financiamiento_id'];
            }

            $entityFinanciamiento->poliza_id = $poliza_id;
            $entityFinanciamiento->numero_cuotas = $jsonFinanciamiento['numero_cuotas'];
            $entityFinanciamiento->monto_inicial = $jsonFinanciamiento['monto_inicial'];
            $entityFinanciamiento->numero_financiamiento = $jsonFinanciamiento['numero_financiamiento'];
            $entityFinanciamiento->financiamientos_desde = ReaxiumUtil::getDate($jsonFinanciamiento['financiamientos_desde']);
            $entityFinanciamiento->financiamientos_hasta = ReaxiumUtil::getDate($jsonFinanciamiento['financiamientos_hasta']);

            Log::info("Objeto Financiamiento: " + json_encode($entityFinanciamiento));

            $result = $financiamientoTable->save($entityFinanciamiento);
        } catch (\Exception $e) {
            Log::info($e);
            $result = null;
        }

        return $result;
    }


    public function getFinanciamientoByPoliza($poliza_id)
    {

        $funcionamientoTable = TableRegistry::get("Financiamientos");
        $result = $funcionamientoTable->findByPolizaId($poliza_id);

        if ($result->count() > 0) {
            $result = $result->toArray();
        } else {
            $result = null;
        }

        return $result;
    }

    public function getAseguradoraByID($aseguradora_id)
    {

        $aseguradoraTable = TableRegistry::get("Aseguradora");
        $aseguradoraFound = $aseguradoraTable->find()->where(array('aseguradora_id' => $aseguradora_id, 'status_id' => 1));

        if ($aseguradoraFound->count() > 0) {

            $aseguradoraFound = $aseguradoraFound->toArray();
        } else {
            $aseguradoraFound = null;
        }

        return $aseguradoraFound;
    }
}