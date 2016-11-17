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


define('TIPO_SINIESTRO_AUTO',2);
class SiniestroController extends JcrAPIController{

    /**
     * Servicio para la creacion de siniestro
     */
    public function crearSiniestro(){


        Log::info("Crear o actualiza un Siniestro en sistema");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if (isset($jsonObject['JcrParameters']['SiniestroSystem'])){

                    $result = $this->createASiniestro($jsonObject['JcrParameters']['SiniestroSystem']);

                    if ($result){
                        $response = parent::setSuccessfulSave($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudoo crear el siniestro en sistema';
                    }
                }
                else{
                    $response = parent::seInvalidParametersMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error guardando el Siniestro " . $e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }
        }
        else
        {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * Metodo para guarda siniestro
     * @param $siniestroJSON
     * @return bool
     */
    private function createASiniestro($siniestroJSON){
        $result = true;

        try {

            $siniestroTable = TableRegistry::get("Siniestro");

            $conn = $siniestroTable->connection();

            // bloque transaccional
            $conn->transactional(function() use($siniestroTable,$siniestroJSON){

                $siniestroEntity = $siniestroTable->newEntity();

                if(isset($siniestroJSON['siniestro']['siniestro_id'])){
                    $siniestroEntity->siniestro_id = $siniestroJSON['siniestro']['siniestro_id'];
                }

                if(isset($siniestroJSON['siniestro']['observaciones_ordenes'])){
                    $siniestroEntity->observaciones_ordenes = $siniestroJSON['siniestro']['observaciones_ordenes'];
                }

                $siniestroEntity->poliza_id = $siniestroJSON['siniestro']['poliza_id'];
                $siniestroEntity->numero_siniestro = $siniestroJSON['siniestro']['numero_siniestro'];
                $siniestroEntity->monto_siniestro = $siniestroJSON['siniestro']['monto_siniestro'];
                $siniestroEntity->tipo_siniestro_id = $siniestroJSON['siniestro']['tipo_siniestro_id'];
                $siniestroEntity->fecha_ocurrencia = ReaxiumUtil::getDate($siniestroJSON['siniestro']['fecha_ocurrencia']);


                // guardando el sinestro en tabla siniestro
                $siniestro = $siniestroTable->save($siniestroEntity);

                // si es siniestro automovil guardo el la tabla SiniestroAutomovil
                if($siniestroJSON['siniestro']['tipo_siniestro_id'] == TIPO_SINIESTRO_AUTO){

                    if($siniestro){

                        $siniestroAutomovilTable = TableRegistry::get("SiniestroAutomovil");
                        $siniestroAutoEntity = $siniestroAutomovilTable->newEntity();

                        if(isset($siniestroJSON['auto']['siniestro_automovil_id'])){
                            $siniestroAutoEntity->siniestro_automovil_id = $siniestroJSON['auto']['siniestro_automovil_id'];
                        }

                        $siniestroAutoEntity->siniestro_id = $siniestro['siniestro_id'];
                        $siniestroAutoEntity->fecha_declaracion = ReaxiumUtil::getDate($siniestroJSON['auto']['fecha_declaracion']);
                        $siniestroAutoEntity->fecha_inspeccion = ReaxiumUtil::getDate($siniestroJSON['auto']['fecha_inspeccion']);

                        if(isset($siniestroJSON['auto']['taller_propuesto'])){
                            $siniestroAutoEntity->taller_propuesto = $siniestroJSON['auto']['taller_propuesto'];
                        }

                        if(isset($siniestroJSON['auto']['fecha_entrada_taller']) && $siniestroJSON['auto']['fecha_entrada_taller'] != "Invalid date"){
                            $siniestroAutoEntity->fecha_entrada_taller = ReaxiumUtil::getDate($siniestroJSON['auto']['fecha_entrada_taller']);
                        }

                        if(isset($siniestroJSON['auto']['fecha_cierre']) && $siniestroJSON['auto']['fecha_cierre'] != "Invalid date"){
                            $siniestroAutoEntity->fecha_cierre = ReaxiumUtil::getDate($siniestroJSON['auto']['fecha_cierre']);
                        }


                        $result = $siniestroAutomovilTable->save($siniestroAutoEntity);

                        if($result){

                            if($siniestroJSON['siniestro']['hay_repuestos']){

                                // si se guarda los datos en la tabla Repuestos
                                $repuestoAutoTable =  TableRegistry::get("Repuestos");

                                $listRepuesto = $siniestroJSON['auto']['repuestos'];

                                $repuestoEntity = null;


                                foreach($listRepuesto as $repuesto){

                                    $repuestoEntity = $repuestoAutoTable->newEntity();

                                    if(isset($repuesto['repuesto_id'])){
                                        $repuestoEntity->repuesto_id = $repuesto['repuesto_id'];
                                    }

                                    $repuestoEntity->fecha_llegada = ReaxiumUtil::getDate($repuesto['fecha_llegada']);
                                    $repuestoEntity->descripcion = $repuesto['descripcion'];
                                    $repuestoEntity->observaciones = $repuesto['observaciones'];
                                    $repuestoEntity->siniestro_automovil_id = $result['siniestro_automovil_id'];
                                    $repuestoAutoTable->save($repuestoEntity);

                                }

                            }else{
                                Log::info("Se guardo el siniestor con exito");
                            }

                        }
                        else{
                            Log::info("No se completo el guardado en la tabla SiniestroAutomovil");
                        }

                    }
                }

            });

        }
        catch (\Exception $e)
        {
            Log::info("Error creando siniestro");
            Log::info($e->getMessage());
            $result = false;
        }

        return $result;
    }

    public function borrarSiniestro()
    {
        Log::info("Borrar siniestro");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $siniestro_id = !isset($jsonObject['JcrParameters']['Siniestro']['siniestro_id']) ? null : $jsonObject['JcrParameters']['Siniestro']['siniestro_id'];

            if(isset($siniestro_id))
            {
                try
                {
                    $siniestroTable = TableRegistry::get("Siniestro");
                    $result = $siniestroTable->deleteAll(array('siniestro_id'=>$siniestro_id));

                    if ($result)
                    {
                        $response = parent::setSuccessfulDelete($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudo borrar el siniestro en sistema';
                    }
                }
                catch(\Exception $e)
                {
                    Log::info("Error borrando siniestro del sistema");
                    Log::info($e->getMessage());
                    $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                    $response['JcrResponse']['message'] = 'Error del sistema';
                }
            }
            else
            {
                $response = parent::setInvalidJsonMessage($response);
            }
        }
        else
        {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }

    public function allSiniestrosWithPagination()
    {
        Log::info("Consulta todos los usuarios con paginacion");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if(isset($jsonObject['JcrParameters']['page']))
                {
                    $page = $jsonObject['JcrParameters']["page"];
                    $sortedBy = !isset($jsonObject['JcrParameters']["sortedBy"]) ? 'numero_siniestro' : $jsonObject['JcrParameters']["sortedBy"];
                    $sortDir = !isset($jsonObject['JcrParameters']["sortDir"]) ? 'desc' : $jsonObject['JcrParameters']["sortDir"];
                    $filter = !isset($jsonObject['JcrParameters']["filter"]) ? '' : $jsonObject['JcrParameters']["filter"];
                    $limit = !isset($jsonObject['JcrParameters']["limit"]) ? 10 : $jsonObject['JcrParameters']["limit"];

                    $siniestroFound = $this->getSiniestrosInfo($filter,$sortedBy,$sortDir);

                    $count = $siniestroFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $siniestroFound = $this->paginate($siniestroFound);

                    if ($siniestroFound->count() > 0)
                    {
                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $siniestroFound = $siniestroFound->toArray();
                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $siniestroFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No Siniestros found';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error borrando siniestro del sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }
        }
        else
        {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * Metodo para obtener todos los siniestro del sistema
     * @param $filter
     * @param $sortedBy
     * @param $sortDir
     * @return $this
     */
    private function getSiniestrosInfo($filter,$sortedBy,$sortDir){

        $siniestroTable = TableRegistry::get('Siniestro');

        if(trim($filter) != '') {

            $whereCondition = array(array('OR' => array(
                array('numero_siniestro LIKE' => '%' . $filter . '%'))));

            //agregar los contain cuando sea necesario
            $siniestroFound = $siniestroTable->find()
                ->where($whereCondition)
                ->contain(array('Poliza'))
                ->order(array($sortedBy . ' ' . $sortDir));
        }
        else {
            //agregar los contain cuando sea necesario
            $siniestroFound = $siniestroTable->find()
                ->contain(array('Poliza'))
                ->order(array($sortedBy . ' ' . $sortDir));
        }

        return $siniestroFound;
    }


    /**
     * Servivio para obtener siniestro por ID
     */
    public function searchSiniestroById()
    {
        Log::info("Informacion siniestro por ID");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $siniestro_id = !isset($jsonObject['JcrParameters']['Siniestro']['siniestro_id']) ? null :
                $jsonObject['JcrParameters']['Siniestro']['siniestro_id'];

            $siniestro_type = !isset($jsonObject['JcrParameters']['Siniestro']['tipo_siniestro_id']) ? null :
                $jsonObject['JcrParameters']['Siniestro']['tipo_siniestro_id'];

            try
            {
                if(isset($siniestro_id))
                {

                    $siniestroFound = $this->getSiniestroById($siniestro_id,$siniestro_type);

                    if(isset($siniestroFound))
                    {
                        $response['JcrResponse']['object'] = $siniestroFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['object'] = [];
                        $response['JcrResponse']['code'] = '1';
                        $response['JcrResponse']['message'] = 'No se encontro siniestro con el Id: '.$siniestro_id;
                    }


                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch(\Exception $e)
            {
                Log::info("Error borrando siniestro en el sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }
        }
        else
        {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    private function getSiniestroById($siniestroId,$siniestro_type){

        $siniestroTable = TableRegistry::get("Siniestro");

        $siniestroFound = null;

        if($siniestro_type == TIPO_SINIESTRO_AUTO){

            $siniestroFound = $siniestroTable->find('all',array('fields'=>array(
                'siniestro_id',
                'poliza_id',
                'numero_siniestro',
                'monto_siniestro',
                'monto_siniestro',
                'tipo_siniestro_id',
                'fecha_ocurrencia',
                'observaciones_ordenes',
                'poliza.numero_poliza',
                'poliza.prima_total',
                'poliza.aseguradora_id',
                'poliza.numero_recibo',
                'siniestroAuto.siniestro_automovil_id',
                'siniestroAuto.taller_propuesto',
                'siniestroAuto.fecha_declaracion',
                'siniestroAuto.fecha_inspeccion',
                'siniestroAuto.taller_propuesto',
                'siniestroAuto.fecha_entrada_taller',
                'siniestroAuto.fecha_cierre')))
                ->join(array(
                    'poliza' => array('table'=>'poliza','type'=>'INNER','conditions'=>'Siniestro.poliza_id = poliza.poliza_id'),
                    'siniestroAuto' =>array('table'=>'siniestro_automovil','type'=>'INNER','conditions'=>'Siniestro.siniestro_id = siniestroAuto.siniestro_id')
                ))
                ->where(array('Siniestro.siniestro_id'=>$siniestroId));

            if($siniestroFound->count() > 0){
                $siniestroFound = $siniestroFound->toArray();
                $repuestos = $this->getRepuestosAuto($siniestroFound[0]['siniestroAuto']['siniestro_automovil_id']);


                if(isset($repuestos)){
                    $siniestroFound[0]['repuestos'] = $repuestos;
                    $siniestroFound[0]['hay_repuestos'] = true;
                }
                else{
                    $siniestroFound[0]['repuestos'] =[];
                    $siniestroFound[0]['hay_repuestos'] = false;
                }

            }else{
                $siniestroFound = null;
            }

        }
        else{
            $siniestroFound = $siniestroTable->find()->where(array('siniestro_id'=>$siniestroId))->contain('Poliza');

            if($siniestroFound->count() > 0){
                $siniestroFound = $siniestroFound->toArray();
            }
            else{
                $siniestroFound=null;
            }
        }


        return $siniestroFound;

    }


    public function filterSiniestro()
    {
        Log::info("Informacion siniestro por filtro");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if(isset($jsonObject['JcrParameters']['Siniestro']['filter']))
                {
                    $filter = $jsonObject['JcrParameters']['Siniestro']['filter'];
                    $siniestroTable = TableRegistry::get('Siniestro');
                    $whereCondition = array(array('OR' => array(
                        array('numero_siniestro LIKE' => '%' . $filter . '%'),
                        array('usuario_id LIKE' => '%' . $filter . '%'),
                        array('vehiculo_id LIKE' => '%' . $filter . '%')
                    )));

                    //agregar el contain cuando sea necesario
                    $siniestroFound = $siniestroTable->find()
                        ->where($whereCondition)
                        ->order(array('numero_siniestro', 'vehiculo_id'));


                    if ($siniestroFound->count() > 0) {
                        $siniestroFound = $siniestroFound->toArray();
                        $response['JcrResponse']['object'] = $siniestroFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No Siniestro found';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error borrando siniestro del sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }
        }
        else
        {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }

    /**
     * Metodo para obtener lista de repuestos de un siniestro auto
     * @param $siniestro_automovil_id
     * @return null
     */
    private function getRepuestosAuto($siniestro_automovil_id){

        $repuestoTable = TableRegistry::get("Repuestos");
        $result = null;

        $repuestoFound = $repuestoTable->find()->where(array('siniestro_automovil_id'=>$siniestro_automovil_id));

        if($repuestoFound->count() > 0){
            $result = $repuestoFound->toArray();
        }

        return $result;
    }


    public function getSiniestralidadInfo($start_date,$end_date,$aseguradora_id,$numero_ci_or_placa,$ramo,$mode,$type_search){

            try{

                $polizaTable = TableRegistry::get("Siniestro");
                $conditions = array();

                // condicion de fecha y validacion
                if (isset($start_date)) {
                    $startDateCondition = array('Siniestro.fecha_ocurrencia >=' => $start_date);
                    array_push($conditions, $startDateCondition);
                    if (isset($end_date)) {
                        $endDateCondition = array('Siniestro.fecha_ocurrencia <=' => $end_date);
                        array_push($conditions, $endDateCondition);
                    }
                }

                //condicion aseguradora
                if(isset($aseguradora_id)){
                    $aseguradoraCondition = array('poliza.aseguradora_id'=>$aseguradora_id);
                    array_push($conditions,$aseguradoraCondition);
                }


                //ramo
                //condicion numero de poliza
                if(isset($ramo)){
                    if($ramo == "A"){
                        $ramoCondition = array('poliza.ramo_id in'=>array(3,4));
                    }else{
                        $ramoCondition = array('poliza.ramo_id not in'=>array(3,4));
                    }

                    array_push($conditions,$ramoCondition);
                }

                //poliza status 1
                array_push($conditions,array('poliza.status_id'=>1));

                if($numero_ci_or_placa != ''){

                    switch($type_search){

                        case 1:

                            $whereCondition = array(array('OR' => array(
                                array('asegurado.documento_id_cliente LIKE' => '%' . $numero_ci_or_placa . '%'))));

                            $polizaFound = $polizaTable->find('all',array('fields'=>array(
                                'poliza.poliza_id',
                                'poliza.numero_poliza',
                                'poliza.ramo_id',
                                'poliza.cliente_id_tomador',
                                'poliza.cliente_id_titular',
                                'poliza.agente',
                                'poliza.aseguradora_id',
                                'poliza.prima_total',
                                'poliza.fecha_vencimiento',
                                'Siniestro.siniestro_id',
                                'Siniestro.numero_siniestro',
                                'Siniestro.monto_siniestro',
                                'Siniestro.tipo_siniestro_id'
                            )))
                                ->join(array(
                                    'poliza'=>array('table'=>'poliza','type'=>'LEFT','conditions'=>'Siniestro.poliza_id = poliza.poliza_id'),
                                    'asegurado'=>array('table'=>'clientes','type'=>'LEFT','conditions'=>'poliza.cliente_id_titular = asegurado.cliente_id')))
                                ->where($whereCondition)
                                ->andWhere($conditions);

                            break;

                        case 2:

                            $whereCondition = array(array('OR' => array(
                                array('vehiculo.vehiculo_placa LIKE' => '%' . $numero_ci_or_placa . '%'))));

                            $polizaFound = $polizaTable->find('all',array('fields'=>array(
                                'poliza.poliza_id',
                                'poliza.numero_poliza',
                                'poliza.ramo_id',
                                'poliza.cliente_id_tomador',
                                'poliza.cliente_id_titular',
                                'poliza.agente',
                                'poliza.aseguradora_id',
                                'poliza.prima_total',
                                'poliza.fecha_vencimiento',
                                'Siniestro.siniestro_id',
                                'Siniestro.numero_siniestro',
                                'Siniestro.monto_siniestro',
                                'Siniestro.tipo_siniestro_id'
                            )))
                                ->join(array(
                                    'poliza'=>array('table'=>'poliza','type'=>'LEFT','conditions'=>'Siniestro.poliza_id = poliza.poliza_id'),
                                    'polizaVehiculo' => array('table'=>'poliza_vehiculo','type'=>'LEFT','conditions'=>'poliza.poliza_id = polizaVehiculo.poliza_id'),
                                    'vehiculo' => array('table'=>'vehiculo','type'=>'LEFT','conditions'=>'polizaVehiculo.vehiculo_id = vehiculo.vehiculo_id')))
                                ->where($whereCondition)
                                ->andWhere($conditions);
                             break;

                    }

                }else{

                    $polizaFound = $polizaTable->find('all',array('fields'=>array(
                        'poliza.poliza_id',
                        'poliza.numero_poliza',
                        'poliza.ramo_id',
                        'poliza.cliente_id_tomador',
                        'poliza.cliente_id_titular',
                        'poliza.agente',
                        'poliza.aseguradora_id',
                        'poliza.prima_total',
                        'poliza.fecha_vencimiento',
                        'Siniestro.siniestro_id',
                        'Siniestro.numero_siniestro',
                        'Siniestro.monto_siniestro',
                        'Siniestro.tipo_siniestro_id'
                    )))
                        ->join(array(
                            'poliza'=>array('table'=>'poliza','type'=>'INNER','conditions'=>'Siniestro.poliza_id = poliza.poliza_id')
                        ))
                        ->where($conditions);

                }

                if($mode){

                    if($polizaFound->count() > 0){

                        $polizasFound = $polizaFound->toArray();

                        $clientCtrl = new ClientController();
                        $vehiculoCtrl = new VehiculoController();
                        $polizaCtrl = new PolizaController();

                        $entityPoliza = null;
                        $arrayResultFinal = array();

                        foreach($polizasFound as $poliza){

                            $entityPoliza = $polizaTable->newEntity();
                            $entityPoliza->poliza_id = $poliza['poliza']['poliza_id'];
                            $entityPoliza->numero_poliza = $poliza['poliza']['numero_poliza'];
                            $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['poliza']['cliente_id_titular']);
                            $entityPoliza->agente = $poliza['poliza']['agente'];
                            $entityPoliza->prima_total = $poliza['poliza']['prima_total'];
                            $entityPoliza->fecha_vencimiento = $poliza['poliza']['fecha_vencimiento'];
                            $entityPoliza->ramo = $this->getRamoSystem($poliza['poliza']['ramo_id']);
                            $entityPoliza->coberturas = $polizaCtrl->getCoberturasDeLaPoliza($poliza['poliza']['poliza_id']);
                            $entityPoliza->tipo_siniestro = $poliza['tipo_siniestro_id'];
                            $entityPoliza->numero_siniestro = $poliza['numero_siniestro'];
                            $entityPoliza->monto_siniestro = $poliza['monto_siniestro'];

                            if($poliza['tipo_siniestro_id'] == SINIESTRO_VEHICULO){
                                $entityPoliza->vehiculo = $vehiculoCtrl->getVehiculoRelationPoliza($poliza['poliza']['poliza_id']);
                            }

                            $entityPoliza->aseguradora = $polizaCtrl->getAseguradoraByID($poliza['poliza']['aseguradora_id']);
                            $entityPoliza->calculo = $this->calculoSiniestro($poliza['monto_siniestro'],$poliza['poliza']['prima_total']);

                            array_push($arrayResultFinal,$entityPoliza);

                        }

                        $polizaFound = ReaxiumUtil::arrayCopy($arrayResultFinal);
                    }
                    else{
                        $polizaFound = null;
                    }

                }


            }catch(\Exception $e){
                Log::info("Error buscado siniestros");
                Log::info($e->getMessage());
                $polizaFound = null;
            }

        return $polizaFound;
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

    public function calculoSiniestro($monto_siniestro,$poliza_prima){

        try{

            $result = ($monto_siniestro * 100) / $poliza_prima;
            $result =  number_format($result, 2, '.', '');

        }catch(\Exception $e){
            Log::info("Error calculado: " .$e->getMessage());
            $result = 0;
        }

        return $result;
    }

}