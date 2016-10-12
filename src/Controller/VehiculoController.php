<?php
/**
 * Created by PhpStorm.
 * User: EduardoDeLaCruz
 * Date: 22/03/2016
 * Time: 01:43 AM tets
 */

namespace App\Controller;

use Cake\Log\Log;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use App\Util\ReaxiumApiMessages;
use App\Util\ReaxiumUtil;

class VehiculoController extends JcrAPIController
{
    /*public function crearVehiculo()
    {
        Log::info("Crear o actualiza un vehiculo en sistema");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if (isset($jsonObject['JcrParameters']["Vehiculo"]))
                {
                    $result = $this->createAVehiculo($jsonObject['JcrParameters']);

                    if ($result)
                    {
                        $response = parent::setSuccessfulSave($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudoo crear el vehiculo en sistema';
                    }
                }
                else
                {
                    $response = parent::seInvalidParametersMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error guardando el vehiculo " . $e->getMessage());
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
    }*/

    /**
     * Metodo paraa guardar vehiculo
     * @param $vehiculoTable
     * @param $vehiculoJSON
     * @return array
     * @throws Exception
     */
    public function createAVehiculo($vehiculoTable,$vehiculoJSON)
    {

        try
        {
            $vehiculoEntity = null;
            $vehiculoSavelist = array();
            $result = null;

            foreach($vehiculoJSON as $vehiculo){
                $vehiculoEntity = $vehiculoTable->newEntity();

                if($vehiculo['vehiculo_id']){
                    $vehiculoEntity->vehiculo_id = $vehiculo['vehiculo_id'];
                }

                $vehiculoEntity->vehiculo_placa = $vehiculo['vehiculo_placa'];
                $vehiculoEntity->vehiculo_marca_id = $vehiculo['vehiculo_marca_id'];
                $vehiculoEntity->vehiculo_modelo = $vehiculo['vehiculo_modelo'];
                $vehiculoEntity->vehiculo_ano = $vehiculo['vehiculo_ano'];
                $vehiculoEntity->vehiculo_version = $vehiculo['vehiculo_version'];
                $result = $vehiculoTable->save($vehiculoEntity);
                array_push($vehiculoSavelist,$result);
            }


        }
        catch (\Exception $e)
        {
            Log::info("Error creando vehiculo");
            Log::info($e->getMessage());
            $vehiculoSavelist = null;
        }

        return $vehiculoSavelist;
    }


    /**
     * Metodo para relacionan poliza con vehiculos
     * @param $relationVehiculoPolizaTable
     * @param $poliza_id
     * @param $listVehiculo
     * @param $editMode
     * @return array
     * @throws Exception
     */
    public function relationShipPolizaVehiculo($relationVehiculoPolizaTable,$poliza_id,$listVehiculo,$editMode){

        try{

            $entityRelationPoliza = null;
            $result = array();
            //si estas editando borro todas las relaciones de vehiculo con la poliza
            if($editMode){
                $relationVehiculoPolizaTable->deleteAll(array('poliza_id'=>$poliza_id));
            }

            foreach($listVehiculo as $vehiculo){

                $entityRelationPoliza = $relationVehiculoPolizaTable->newEntity();
                $entityRelationPoliza->poliza_id = $poliza_id;
                $entityRelationPoliza->vehiculo_id = $vehiculo['vehiculo_id'];
                $rel = $relationVehiculoPolizaTable->save($entityRelationPoliza);
                array_push($result,$rel);
            }


        }
        catch (\Exception $e){
            Log::info("Error creando vehiculo");
            Log::info($e->getMessage());
            $result = null;
        }

        return $result;
    }

    public function borrarVehiculo()
    {
        Log::info("Borrar vehiculo");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $vehiculo_id = !isset($jsonObject['JcrParameters']['Vehiculo']['vehiculo_id']) ? null : $jsonObject['JcrParameters']['Vehiculo']['vehiculo_id'];

            if(isset($vehiculo_id))
            {
                try
                {
                    $vehiculoTable = TableRegistry::get("Vehiculo");
                    $result = $vehiculoTable->deleteAll(array('vehiculo_id'=>$vehiculo_id));

                    if ($result)
                    {
                        $response = parent::setSuccessfulDelete($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudo borrar el vehiculo en sistema';
                    }
                }
                catch(\Exception $e)
                {
                    Log::info("Error borrando vehiculo del sistema");
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

    public function allVehiculosInfoWithPagination()
    {
        Log::info("Consulta todos los vehiculos con paginacion");
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
                    $sortedBy = !isset($jsonObject['JcrParameters']["sortedBy"]) ? 'placa' : $jsonObject['JcrParameters']["sortedBy"];
                    $sortDir = !isset($jsonObject['JcrParameters']["sortDir"]) ? 'desc' : $jsonObject['JcrParameters']["sortDir"];
                    $filter = !isset($jsonObject['JcrParameters']["filter"]) ? '' : $jsonObject['JcrParameters']["filter"];
                    $limit = !isset($jsonObject['JcrParameters']["limit"]) ? 10 : $jsonObject['JcrParameters']["limit"];

                    $vehiculoTable = TableRegistry::get('Vehiculo');

                    if(trim($filter) != '')
                    {
                        $whereCondition = array(array('OR' => array(
                            array('placa LIKE' => '%' . $filter . '%'),
                            array('marca LIKE' => '%' . $filter . '%'),
                            array('modelo LIKE' => '%' . $filter . '%'))));

                        //agregar los contain cuando sea necesario
                        $vehiculoFound = $vehiculoTable->find()
                            ->where($whereCondition)
                            ->order(array($sortedBy . ' ' . $sortDir));
                    }
                    else
                    {
                        //agregar los contain cuando sea necesario
                        $vehiculoFound = $vehiculoTable->find()->order(array($sortedBy . ' ' . $sortDir));
                    }

                    $count = $vehiculoFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $vehiculoFound = $this->paginate($vehiculoFound);

                    if ($vehiculoFound->count() > 0)
                    {
                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $vehiculoFound = $vehiculoFound->toArray();
                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $vehiculoFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontraron vehiculos';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error buscando vehiculo en el sistema");
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

    public function vehiculoInfoById()
    {
        Log::info("Informacion vehiculo por ID");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $vehiculo_id = !isset($jsonObject['JcrParameters']['Vehiculo']['vehiculo_id']) ? null : $jsonObject['JcrParameters']['Vehiculo']['vehiculo_id'];

            try
            {
                if(isset($vehiculo_id))
                {
                    $vehiculoFound = $this->getVehiculoById($vehiculo_id);

                    if($vehiculoFound->count() > 0) {
                        $vehiculoFound = $vehiculoFound->toArray();
                        $response['JcrResponse']['object'] = $vehiculoFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = '1';
                        $response['JcrResponse']['message'] = 'No se encontro vehiculo con el ID: '.$vehiculo_id;
                        $response['JcrResponse']['object'] = [];
                    }

                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch(\Exception $e)
            {
                Log::info("Error borrando usuario del sistema");
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
     * @param $vehiculo_id
     * @return $this|array
     */
    private function getVehiculoById($vehiculo_id){

        $vehiculoTable = TableRegistry::get("Vehiculo");
        $vehiculoFound = $vehiculoTable->find()
            ->where(array('vehiculo_id'=>$vehiculo_id,'Vehiculo.status_id'=>1))
            ->contain(array('Usuarios'));

        return $vehiculoFound;
    }


    public function vehiculoFilter()
    {
        Log::info("Informacion vehiculo por filtro");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if(isset($jsonObject['JcrParameters']['Vehiculo']['filter'])) {

                    $filter = $jsonObject['JcrParameters']['Vehiculo']['filter'];
                    $vehiculoTable = TableRegistry::get('Vehiculo');
                    $whereCondition = array(array('OR' => array(
                        array('placa LIKE' => '%' . $filter . '%'),
                        array('marca LIKE' => '%' . $filter . '%'),
                        array('modelo LIKE' => '%' . $filter . '%')
                    )));

                    //agregar el contain cuando sea necesario
                    $vehiculoFound = $vehiculoTable->find()
                        ->where($whereCondition)
                        ->order(array('nombre', 'apellido'));


                    if ($vehiculoFound->count() > 0) {
                        $vehiculoFound = $vehiculoFound->toArray();
                        $response['JcrResponse']['object'] = $vehiculoFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontraron vehiculos';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error buscando vehiculos en el sistema");
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


    public function searchVehiculoByPlaca(){

        Log::info("Informacion vehiculo por filtro");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if(parent::validJcrJsonHeader($jsonObject)){

            try{

                if(isset($jsonObject['JcrParameters']['Vehiculo']['filter'])) {

                    $filter = $jsonObject['JcrParameters']['Vehiculo']['filter'];
                    $vehiculoTable = TableRegistry::get('Vehiculo');

                    $whereCondition = array('vehiculo_placa LIKE' => '%' . $filter . '%');

                    $vehiculoFound = $vehiculoTable->find()->where($whereCondition)->contain(array('MarcaVehiculo'));


                    if ($vehiculoFound->count() > 0) {
                        $vehiculoFound = $vehiculoFound->toArray();
                        $response['JcrResponse']['object'] = $vehiculoFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontraron vehiculos';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }


            }
            catch (\Exception $e){
                Log::info("Error buscando vehiculos en el sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }

        }
        else{
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function marcaVehiculoFilter()
    {
        Log::info("Informacion marca por filtro");

        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if(isset($jsonObject['JcrParameters']['Marca']['filter']))
                {
                    $filter = $jsonObject['JcrParameters']['Marca']['filter'];
                    $marcaTable = TableRegistry::get('MarcaVehiculo');
                    $whereCondition = array(array('OR' => array(
                        array('marca_vehiculo_descripcion LIKE' => '%' . $filter . '%')
                    )));

                    //agregar el contain cuando sea necesario
                    $marcaFound = $marcaTable->find()->where($whereCondition);


                    if ($marcaFound->count() > 0) {
                        $marcaFound = $marcaFound->toArray();
                        $response['JcrResponse']['object'] = $marcaFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontraron vehiculos';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error buscando marcas de vehiculo en el sistema");
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


    public function getRelationPolizaWithVehiculo(){

        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject)){

            try{

                $poliza_id = !isset($jsonObject['JcrParameters']['Vehiculo']['poliza_id']) ? null : $jsonObject['JcrParameters']['Vehiculo']['poliza_id'];

                if(isset($poliza_id)){

                    $result= $this->getVehiculoRelationPoliza($poliza_id);

                    if(isset($result)){

                        $response['JcrResponse']['object'] = $result;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else{
                        $response['JcrResponse']['code'] = 1;
                        $response['JcrResponse']['message'] = 'Vehiculo no found';
                    }
                }
                else{
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e){
                Log::info("Error buscando marcas de vehiculo en el sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }
        }
        else{
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    public function getVehiculoRelationPoliza($poliza_id){

        try{
            $result=array();
            $PolizaVehiTable = TableRegistry::get("PolizaVehiculo");
            $polizaVehiculoFound = $PolizaVehiTable->find()->where(array('poliza_id'=>$poliza_id))->contain(array('Vehiculo'));

            if($polizaVehiculoFound->count() > 0){
                $query = $polizaVehiculoFound->toArray();

                foreach($query as $row){
                    $entityVehiculo = $PolizaVehiTable->newEntity();
                    $entityVehiculo->vehiculo_id = $row['vehiculo']['vehiculo_id'];
                    $entityVehiculo->vehiculo_placa = $row['vehiculo']['vehiculo_placa'];
                    $entityVehiculo->vehiculo_marca = $this->getMarcaVehiculo($row['vehiculo']['vehiculo_marca_id']);
                    $entityVehiculo->vehiculo_modelo = $row['vehiculo']['vehiculo_modelo'];
                    $entityVehiculo->vehiculo_ano = $row['vehiculo']['vehiculo_ano'];
                    $entityVehiculo->vehiculo_version = $row['vehiculo']['vehiculo_version'];
                    array_push($result,$entityVehiculo);
                }

            }
            else{
                $result = null;
            }

        }catch (\Exception $e){
            $result = null;
            Log::info("Error buscando vehiculos relacionados a  la poliza");
        }


        return $result;
    }


    public function getMarcaVehiculo($vehiculo_marca_id){

        try{
            $entityMarca = null;
            $marcaVehiculoTable = TableRegistry::get("MarcaVehiculo");
            $marcaVehiculoFound = $marcaVehiculoTable->find()->where(array('marca_vehiculo_id'=>$vehiculo_marca_id));

            if($marcaVehiculoFound->count()>0){
                $entityMarca = $marcaVehiculoTable->newEntity();
                $aux = $marcaVehiculoFound->toArray();

                $entityMarca->marca_vehiculo_id = $aux[0]['marca_vehiculo_id'];
                $entityMarca->marca_vehiculo_descripcion = $aux[0]['marca_vehiculo_descripcion'];

            }else{
                $entityMarca= null;
            }


        }catch (\Exception $e){
            $entityMarca = null;
            Log::info("Error buscando vehiculos relacionados a  la poliza");
        }

        return $entityMarca;
    }
}
