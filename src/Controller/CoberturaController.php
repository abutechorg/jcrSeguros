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

class CoberturaController extends JcrAPIController
{
    public function crearCobertura()
    {
        Log::info("Crear o actualizar una cobertura en sistema");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if (isset($jsonObject['JcrParameters']["Cobertura"]))
                {
                    $result = $this->createACobertura($jsonObject['JcrParameters']);

                    if ($result)
                    {
                        $response = parent::setSuccessfulSave($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudo crear la cobertura en sistema';
                    }
                }
                else
                {
                    $response = parent::seInvalidParametersMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error guardando la cobertura " . $e->getMessage());
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

    private function createACobertura($coberturaJSON)
    {
        $result = null;

        try
        {
            $coberturaTable = TableRegistry::get("Cobertura");
            $coberturaEntity = $coberturaTable->newEntity();

            $coberturas =$coberturaTable->patchEntity($coberturaEntity, $coberturaJSON['Cobertura']);

            Log::info($coberturas);
            $result = $coberturaTable->save($coberturas);

        }
        catch (\Exception $e)
        {
            Log::info("Error creando cobertura");
            Log::info($e->getMessage());
        }

        return $result;
    }

    public function borrarCobertura()
    {
        Log::info("Borrar cobertura");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $cobertura_id = !isset($jsonObject['JcrParameters']['Cobertura']['cobertura_id']) ? null : $jsonObject['JcrParameters']['Cobertura']['cobertura_id'];

            if(isset($cobertura_id))
            {
                try
                {
                    $coberturaTable = TableRegistry::get("Cobertura");
                    $result = $coberturaTable->deleteAll(array('cobertura_id'=>$cobertura_id));

                    if ($result)
                    {
                        $response = parent::setSuccessfulDelete($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudo borrar la cobertura en sistema';
                    }
                }
                catch(\Exception $e)
                {
                    Log::info("Error borrando cobertura del sistema");
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

    public function allCoberturasInfoWithPagination()
    {
        Log::info("Consulta todas las coberturas con paginacion");
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
                    $sortedBy = !isset($jsonObject['JcrParameters']["sortedBy"]) ? 'cobertura_nombre' : $jsonObject['JcrParameters']["sortedBy"];
                    $sortDir = !isset($jsonObject['JcrParameters']["sortDir"]) ? 'desc' : $jsonObject['JcrParameters']["sortDir"];
                    $filter = !isset($jsonObject['JcrParameters']["filter"]) ? '' : $jsonObject['JcrParameters']["filter"];
                    $limit = !isset($jsonObject['JcrParameters']["limit"]) ? 10 : $jsonObject['JcrParameters']["limit"];

                    $coberturaFound = $this->getAllCobertura($filter,$sortedBy,$sortDir);

                    $count = $coberturaFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $coberturaFound = $this->paginate($coberturaFound);

                    if ($coberturaFound->count() > 0)
                    {
                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $coberturaFound = $coberturaFound->toArray();
                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $coberturaFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontraron coberturas';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error buscando cobertura en el sistema");
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
     * @param $filter
     * @param $sortedBy
     * @param $sortDir
     * @return $this
     */
    private function getAllCobertura($filter,$sortedBy,$sortDir){

        $coberturaTable = TableRegistry::get('Cobertura');

        if(trim($filter) != '')
        {
            $whereCondition = array(array('OR' => array(
                array('cobertura_nombre LIKE' => '%' . $filter . '%'),
                array('ramo_id LIKE' => '%' . $filter . '%'),
                array('monto_deducible LIKE' => '%' . $filter . '%'))));

            //agregar los contain cuando sea necesario
            $coberturaFound = $coberturaTable->find()
                ->where($whereCondition)
                ->contain(array("Ramo"))
                ->order(array($sortedBy . ' ' . $sortDir));
        }
        else
        {
            //agregar los contain cuando sea necesario
            $coberturaFound = $coberturaTable->find()
                ->contain(array("Ramo"))
                ->order(array($sortedBy . ' ' . $sortDir));
        }


        return $coberturaFound;
    }


    public function coberturaInfoById()
    {
        Log::info("Informacion de cobertura por ID");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $cobertura_id = !isset($jsonObject['JcrParameters']['Cobertura']['cobertura_id']) ? null : $jsonObject['JcrParameters']['Cobertura']['cobertura_id'];

            try
            {
                if(isset($cobertura_id))
                {

                    $coberturaFound = $this->getCoberturaById($cobertura_id);

                    if($coberturaFound->count() > 0)
                    {
                        $coberturaFound = $coberturaFound->toArray();
                        $response['JcrResponse']['object'] = $coberturaFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = '1';
                        $response['JcrResponse']['message'] = 'Cobertura no disponoble';
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
                Log::info("Error buscando cobertura en el sistema");
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


    private function getCoberturaById($cobertura_id){

        $coberturaTable = TableRegistry::get("Cobertura");
        $coberturaFound = $coberturaTable->find()
            ->where(array('cobertura_id'=>$cobertura_id))
            ->contain(array("Ramo"));

        return $coberturaFound;

    }

    public function coberturaFilter()
    {
        Log::info("Informacion usuario por filtro");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if(isset($jsonObject['JcrParameters']['Cobertura']['filter']))
                {
                    $filter = $jsonObject['JcrParameters']['Cobertura']['filter'];
                    $coberturaTable = TableRegistry::get('Cobertura');
                    $whereCondition = array(array('OR' => array(
                        array('cobertura_nombre LIKE' => '%' . $filter . '%'),
                        array('ramo_id LIKE' => '%' . $filter . '%'),
                        array('monto_deducible LIKE' => '%' . $filter . '%')
                    )));

                    //agregar el contain cuando sea necesario
                    $coberturaFound = $coberturaTable->find()
                        ->where($whereCondition)
                        ->order(array('cobertura_nombre', 'ramo_id'));


                    if ($coberturaFound->count() > 0) {
                        $coberturaFound = $coberturaFound->toArray();
                        $response['JcrResponse']['object'] = $coberturaFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontraron coberturas';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error buscando coberturas en el sistema");
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