<?php
/**
 * Created by PhpStorm.
 * User: EduardoDeLaCruz
 * Date: 23/8/2016
 * Time: 11:30
 */

namespace App\Controller;

use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Util\ReaxiumApiMessages;


class PolizaController extends JcrAPIController{


    public function crearPoliza()
    {
        Log::info("Crear o actualiza una poliza en sistema");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if (isset($jsonObject['JcrParameters']["Poliza"]))
                {
                    $result = $this->createAPoliza($jsonObject['JcrParameters']);

                    if ($result)
                    {
                        $response = parent::setSuccessfulSave($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudo crear la poliza en sistema';
                    }
                }
                else
                {
                    $response = parent::seInvalidParametersMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error guardando la poliza " . $e->getMessage());
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
     * Metodo para crear una poliza
     * @param $polizaJSON
     * @return bool|\Cake\Datasource\EntityInterface|mixed|null
     */
    private function createAPoliza($polizaJSON){
        $result = null;

        try
        {
            $polizaTable = TableRegistry::get("Poliza");
            $polizaEntity = $polizaTable->newEntity();

            $polizas = $polizaTable->patchEntity($polizaEntity, $polizaJSON['Poliza']);

            $result = $polizaTable->save($polizas);

            Log::info("Poliza ID: ".$result['poliza_id']);

            if(isset($result) && isset($polizaJSON['Poliza']['beneficiarios'])){
              $this->savePolizaBeneficiarios($polizaJSON['Poliza']['beneficiarios'],$result['poliza_id']);
            }

        }
        catch (\Exception $e)
        {
            Log::info("Error creando poliza");
            Log::info($e->getMessage());
        }

        return $result;
    }


    private function savePolizaBeneficiarios($beneficiarios,$poliza_id){

        $polizaBenefTable = TableRegistry::get("PolizaBeneficiario");
        $entityObj= null;

        $validate = $polizaBenefTable->findByPolizaId($poliza_id);

        if($validate->count() > 0){
            $polizaBenefTable->deleteAll(array('poliza_id'=>$poliza_id));
        }

        foreach($beneficiarios as $row){
            $entityObj = $polizaBenefTable->newEntity();
            $entityObj->poliza_id = $poliza_id;
            $entityObj->usuario_id = $row['usuario_id'];
            $polizaBenefTable->save($entityObj);
        }

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

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $poliza_id = !isset($jsonObject['JcrParameters']['Poliza']['poliza_id']) ? null : $jsonObject['JcrParameters']['Poliza']['poliza_id'];

            if(isset($poliza_id))
            {
                try
                {
                    $polizaTable = TableRegistry::get("Poliza");
                    $entityPoliza = $polizaTable->newEntity();
                    $entityPoliza->poliza_id = $poliza_id;
                    $entityPoliza->status_id = 3;
                    $result = $polizaTable->save($entityPoliza);

                    if ($result)
                    {
                        $response = parent::setSuccessfulDelete($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudo borrar la poliza en sistema';
                    }
                }
                catch(\Exception $e)
                {
                    Log::info("Error borrando poliza del sistema");
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

        if(parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if(isset($jsonObject['JcrParameters']['page']))
                {
                    $page = $jsonObject['JcrParameters']["page"];
                    $sortedBy = !isset($jsonObject['JcrParameters']["sortedBy"]) ? 'numero_poliza' : $jsonObject['JcrParameters']["sortedBy"];
                    $sortDir = !isset($jsonObject['JcrParameters']["sortDir"]) ? 'desc' : $jsonObject['JcrParameters']["sortDir"];
                    $filter = !isset($jsonObject['JcrParameters']["filter"]) ? '' : $jsonObject['JcrParameters']["filter"];
                    $limit = !isset($jsonObject['JcrParameters']["limit"]) ? 10 : $jsonObject['JcrParameters']["limit"];


                    $polizaFound = $this->getAllPoliza($filter,$sortedBy,$sortDir);

                    $count = $polizaFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $polizaFound = $this->paginate($polizaFound);

                    if ($polizaFound->count() > 0)
                    {
                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $polizaFound = $polizaFound->toArray();
                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $polizaFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No Poliza found';
                    }
                }
                else {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error buscando poliza en el sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }
        }
        else {
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
    private function getAllPoliza($filter,$sortedBy,$sortDir){

        $polizaTable = TableRegistry::get('Poliza');

        if(trim($filter) != '')
        {
            $whereCondition = array(array('OR' => array(
                array('numero_poliza LIKE' => '%' . $filter . '%'),
                array('ramo_is LIKE' => '%' . $filter . '%'),
                array('aseguradora_id LIKE' => '%' . $filter . '%'))));

            //agregar los contain cuando sea necesario
            $polizaFound = $polizaTable->find()
                ->where($whereCondition)
                ->andWhere(array('Poliza.status_id' => 1))
                ->contain(array('TipoPoliza','Aseguradora','Ramo'))
                ->order(array($sortedBy . ' ' . $sortDir));
        }
        else
        {
            //agregar los contain cuando sea necesario
            $polizaFound = $polizaTable->find()
                ->where(array('Poliza.status_id' => 1))
                ->contain(array('TipoPoliza','Aseguradora','Ramo'))
                ->order(array($sortedBy . ' ' . $sortDir));
        }

        return $polizaFound;
    }


    public function searchPolizaById()
    {
        Log::info("Informacion poliza por ID");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $poliza_id = !isset($jsonObject['JcrParameters']['Poliza']['poliza_id']) ? null : $jsonObject['JcrParameters']['Poliza']['poliza_id'];

            try
            {
                if(isset($poliza_id))
                {
                    $polizaTable = TableRegistry::get("Poliza");
                    $polizaFound = $polizaTable->find()
                    ->where(array('poliza_id'=>$poliza_id))
                    ->contain(array('TipoPoliza','Aseguradora','Ramo'));

                    if($polizaFound->count() > 0) {

                        $polizaFound = $polizaFound->toArray();
                        $polizaFound[0]['tomador'] = $this->getUserById($polizaFound[0]['usuario_id_tomador']);
                        $polizaFound[0]['titular'] = $this->getUserById($polizaFound[0]['usuario_id_titular']);
                        $polizaFound[0]['agente'] = $this->getUserById($polizaFound[0]['usuario_id_agente']);
                        $polizaFound[0]['beneficiarios'] = $this->getBeneficarios($polizaFound[0]['poliza_id']);

                        $response['JcrResponse']['object'] = $polizaFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else {
                        $polizaFound = null;
                        $response['JcrResponse']['code'] = "1";
                        $response['JcrResponse']['message'] = "Poliza no encontrada en sistema";
                        $response['JcrResponse']['object'] = $polizaFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch(\Exception $e)
            {
                Log::info("Error borrando poliza del sistema");
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


    private function getUserById($user_id){

        Log::info("User ID: "+$user_id);

        $userTable = TableRegistry::get("Usuarios");
        $userFound = $userTable->find()->where(array('usuario_id'=>$user_id))->contain(array("TipoUsuario"));

        $entityUser = null;

        if($userFound->count() > 0){
            $userFound = $userFound->toArray();
            $entityUser = $userTable->newEntity();

            foreach($userFound as $row){

                $entityUser->usuario_id = $row['usuario_id'];
                $entityUser->nombre = $row['nombre'];
                $entityUser->apellido = $row['apellido'];
                $entityUser->documento_id = $row['documento_id'];
                $entityUser->tipo_usuario_id = $row['tipo_usuario']['tipo_usuario_id'];
                $entityUser->tipo_usuario_name = $row['tipo_usuario']['tipo_usuario_nombre'];
            }

        }else{
            $entityUser = null;
        }

        return $entityUser;
    }


    private function getBeneficarios($poliza_id){

        $polizaBeneficTable = TableRegistry::get("PolizaBeneficiario");
        $arrayBeneficiarios = array();
        $entityUser = null;

        $poliFound = $polizaBeneficTable->find()
            ->where(array('poliza_id' => $poliza_id))
            ->contain(array('Usuarios'));

        if($poliFound->count()>0){
            $poliFound = $poliFound->toArray();



            foreach($poliFound as $row){

                $entityUser = $polizaBeneficTable->newEntity();
                $entityUser->usuario_id = $row['usuario']['usuario_id'];
                $entityUser->nombre = $row['usuario']['nombre'];
                $entityUser->apellido = $row['usuario']['apellido'];
                $entityUser->documento_id = $row['usuario']['documento_id'];
                $typeUser = $this->getTypeUser($row['usuario']['tipo_usuario_id']);
                $entityUser->tipo_usuario_id = $typeUser[0]['tipo_usuario_id'];
                $entityUser->tipo_usuario_name = $typeUser[0]['tipo_usuario_nombre'];
                array_push($arrayBeneficiarios,$entityUser);
            }

        }

        return $arrayBeneficiarios;
    }


    private function getTypeUser($tipo_usuario_id){

        $typeUserTable = TableRegistry::get("TipoUsuario");
        $typeUserFound = $typeUserTable->findByTipoUsuarioId($tipo_usuario_id);

        if($typeUserFound->count() > 0){
            $typeUserFound=$typeUserFound->toArray();
        }else{
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

        if(parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if(isset($jsonObject['JcrParameters']['Poliza']['filter']))
                {
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
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No Poliza found';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error poliza en el sistema");
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
}