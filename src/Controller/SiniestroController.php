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
                if (isset($jsonObject['JcrParameters']['SiniestroSystem']))
                {
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
                else
                {
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

                $siniestroEntity->poliza_id = $siniestroJSON['siniestro']['poliza_id'];
                $siniestroEntity->numero_siniestro = $siniestroJSON['siniestro']['numero_siniestro'];
                $siniestroEntity->monto_siniestro = $siniestroJSON['siniestro']['monto_siniestro'];
                $siniestroEntity->tipo_siniestro_id = $siniestroJSON['siniestro']['tipo_siniestro_id'];
                $siniestroEntity->observaciones_ordenes = $siniestroJSON['siniestro']['observaciones_ordenes'];


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
                        $siniestroAutoEntity->fecha_ocurrencia = ReaxiumUtil::getDate($siniestroJSON['auto']['fecha_ocurrencia']);
                        $siniestroAutoEntity->fecha_declaracion = ReaxiumUtil::getDate($siniestroJSON['auto']['fecha_declaracion']);
                        $siniestroAutoEntity->fecha_inspeccion = ReaxiumUtil::getDate($siniestroJSON['auto']['fecha_inspeccion']);
                        $siniestroAutoEntity->taller_propuesto = $siniestroJSON['auto']['taller_propuesto'];
                        $siniestroAutoEntity->fecha_entrada_taller = ReaxiumUtil::getDate($siniestroJSON['auto']['fecha_entrada_taller']);
                        $siniestroAutoEntity->fecha_cierre = ReaxiumUtil::getDate($siniestroJSON['auto']['fecha_cierre']);

                        $result = $siniestroAutomovilTable->save($siniestroAutoEntity);

                        if($result){

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
                'observaciones_ordenes',
                'poliza.numero_poliza',
                'poliza.prima_total',
                'poliza.aseguradora_id',
                'poliza.numero_recibo',
                'siniestroAuto.siniestro_automovil_id',
                'siniestroAuto.fecha_ocurrencia',
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
                $siniestroFound[0]['repuestos'] = $this->getRepuestosAuto($siniestroFound[0]['siniestroAuto']['siniestro_automovil_id']);
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


    public function getSiniestralidadInfo($start_date,$end_date,$aseguradora_id,$numero_poliza,$ramo,$mode){

            try{

                $polizaTable = TableRegistry::get("Poliza");
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

                //condicion aseguradora
                if(isset($aseguradora_id)){
                    $aseguradoraCondition = array('Poliza.aseguradora_id'=>$aseguradora_id);
                    array_push($conditions,$aseguradoraCondition);
                }

                //condicion numero de poliza
                if(isset($numero_poliza) && $numero_poliza != ""){
                    $numeroPolizaCondition = array('Poliza.numero_poliza'=>$numero_poliza);
                    array_push($conditions,$numeroPolizaCondition);
                }

                //ramo
                //condicion numero de poliza
                if(isset($ramo)){

                    if($ramo == "P"){
                        $ramoCondition = array('Poliza.ramo_id in'=>array(1,2));
                    }else{
                        $ramoCondition = array('Poliza.ramo_id in'=>array(3,4));
                    }

                    array_push($conditions,$ramoCondition);
                }


                $polizaFound = $polizaTable->find('all',array('fields'=>array(
                    'Poliza.poliza_id',
                    'Poliza.numero_poliza',
                    'Poliza.ramo_id',
                    'Poliza.cliente_id_tomador',
                    'Poliza.cliente_id_titular',
                    'Poliza.agente',
                    'Poliza.aseguradora_id',
                    'Poliza.prima_total',
                    'Poliza.fecha_vencimiento',
                    'siniestro.siniestro_id',
                    'siniestro.numero_siniestro',
                    'siniestro.monto_siniestro',
                    'siniestro.tipo_siniestro_id'
                )))
                ->join(array(
                    'siniestro'=>array('table'=>'siniestro','type'=>'INNER','conditions'=>'Poliza.poliza_id = siniestro.poliza_id')
                ))
                ->where($conditions);


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
                            $entityPoliza->poliza_id = $poliza['poliza_id'];
                            $entityPoliza->numero_poliza = $poliza['numero_poliza'];
                            $entityPoliza->asegurado = $clientCtrl->getClientById($poliza['cliente_id_titular']);
                            $entityPoliza->agente = $poliza['agente'];
                            $entityPoliza->prima_total = $poliza['prima_total'];
                            $entityPoliza->fecha_vencimiento = $poliza['fecha_vencimiento'];
                            $entityPoliza->ramo = $this->getRamoSystem($poliza['ramo_id']);
                            $entityPoliza->coberturas = $polizaCtrl->getCoberturasDeLaPoliza($poliza['poliza_id']);
                            $entityPoliza->tipo_siniestro = $poliza['siniestro']['tipo_siniestro_id'];
                            $entityPoliza->numero_siniestro = $poliza['siniestro']['numero_siniestro'];
                            $entityPoliza->monto_siniestro = $poliza['siniestro']['monto_siniestro'];

                            if($poliza['siniestro']['tipo_siniestro_id'] == SINIESTRO_VEHICULO){
                                $entityPoliza->vehiculo = $vehiculoCtrl->getVehiculoRelationPoliza($poliza['poliza_id']);
                            }

                            $entityPoliza->aseguradora = $polizaCtrl->getAseguradoraByID($poliza['aseguradora_id']);
                            $entityPoliza->calculo = $this->calculoSiniestro($poliza['siniestro']['monto_siniestro'],$poliza['prima_total']);


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

        $result = ($monto_siniestro * 100) / $poliza_prima;
        $result =  number_format($result, 2, '.', '');
        return $result;
    }

}