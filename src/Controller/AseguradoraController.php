<?php
/**
 * Created by PhpStorm.
 * User: EduardoDeLaCruz
 * Date: 23/8/2016
 * Time: 11:30
 */

namespace App\Controller;

use Cake\Log\Log;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use App\Util\ReaxiumApiMessages;
use App\Util\ReaxiumUtil;

class AseguradoraController extends JcrAPIController{


    public function crearAseguradora()
    {
        Log::info("Crear o actualiza una aseguradora en sistema");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if (isset($jsonObject['JcrParameters']["Aseguradora"]))
                {
                    $result = $this->createAAseguradora($jsonObject['JcrParameters']);

                    if ($result)
                    {
                        $response = parent::setSuccessfulSave($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudoo crear la aseguradora en sistema';
                    }
                }
                else
                {
                    $response = parent::seInvalidParametersMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error guardando la aseguradora " . $e->getMessage());
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

    private function createAAseguradora($aseguradoraJSON)
    {
        $result = null;

        try
        {
            $aseguradoraTable = TableRegistry::get("Aseguradora");
            $aseguradoraEntity = $aseguradoraTable->newEntity();

            $asegura =$aseguradoraTable->patchEntity($aseguradoraEntity, $aseguradoraJSON['Aseguradora']);

            Log::info($asegura);
            $result = $aseguradoraTable->save($asegura);

        }
        catch (\Exception $e)
        {
            Log::info("Error creando aseguradora");
            Log::info($e->getMessage());
        }

        return $result;
    }

    public function borrarAseguradora(){

        Log::info("Borrar aseguradora");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $aseguradora_id = !isset($jsonObject['JcrParameters']['Aseguradora']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Aseguradora']['aseguradora_id'];

            if(isset($aseguradora_id))
            {
                try
                {
                    $aseguradoraTable = TableRegistry::get("Aseguradora");
                    $entityAseguradora = $aseguradoraTable->newEntity();
                    $entityAseguradora->aseguradora_id = $aseguradora_id;
                    $entityAseguradora->status_id=3;

                    $result = $aseguradoraTable->save($entityAseguradora);

                    if ($result)
                    {
                        $response = parent::setSuccessfulDelete($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudo borrar las aseguradora en sistema';
                    }
                }
                catch(\Exception $e)
                {
                    Log::info("Error borrando la aseguradora del sistema");
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

    public function allAseguradorasWithPagination()
    {
        Log::info("Consulta todas las aseguradoras con paginacion");
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
                    $sortedBy = !isset($jsonObject['JcrParameters']["sortedBy"]) ? 'aseguradora_nombre' : $jsonObject['JcrParameters']["sortedBy"];
                    $sortDir = !isset($jsonObject['JcrParameters']["sortDir"]) ? 'desc' : $jsonObject['JcrParameters']["sortDir"];
                    $filter = !isset($jsonObject['JcrParameters']["filter"]) ? '' : $jsonObject['JcrParameters']["filter"];
                    $limit = !isset($jsonObject['JcrParameters']["limit"]) ? 10 : $jsonObject['JcrParameters']["limit"];

                    $aseguradoraTable = TableRegistry::get('Aseguradora');

                    if(trim($filter) != '')
                    {
                        $whereCondition = array(array('OR' => array(
                            array('aseguradora_nombre LIKE' => '%' . $filter . '%'),
                            array('aseguradora_documento_id LIKE' => '%' . $filter . '%'))));

                        //agregar los contain cuando sea necesario
                        $aseguradoraFound = $aseguradoraTable->find()
                            ->where($whereCondition)
                            ->andWhere(array('status_id'=>1))
                            ->order(array($sortedBy . ' ' . $sortDir));
                    }
                    else
                    {
                        //agregar los contain cuando sea necesario
                        $aseguradoraFound = $aseguradoraTable->find()
                            ->where(array('status_id'=>1))
                            ->order(array($sortedBy . ' ' . $sortDir));
                    }

                    $count = $aseguradoraFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $aseguradoraFound = $this->paginate($aseguradoraFound);

                    if ($aseguradoraFound->count() > 0)
                    {
                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $aseguradoraFound = $aseguradoraFound->toArray();
                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $aseguradoraFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontro aseguradora';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error buscando aseguradora en el sistema");
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

    public function aseguradoraInfoById()
    {
        Log::info("Informacion de aseguradora por ID");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $aseguradora_id = !isset($jsonObject['JcrParameters']['Aseguradora']['aseguradora_id']) ? null : $jsonObject['JcrParameters']['Aseguradora']['aseguradora_id'];

            try
            {
                if(isset($aseguradora_id))
                {
                    $aseguradoraTable = TableRegistry::get("Aseguradora");
                    $aseguradoraFound = $aseguradoraTable->find()->where(array('aseguradora_id'=>$aseguradora_id,'status_id'=>1));

                    if($aseguradoraFound->count() > 0) {

                        $aseguradoraFound = $aseguradoraFound->toArray();
                    }
                    else {
                        $aseguradoraFound = null;
                    }

                    $response['JcrResponse']['object'] = $aseguradoraFound;
                    $response = parent::setSuccessfulResponse($response);

                }
                else {

                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch(\Exception $e) {
                Log::info("Error buscando aseguradora en el sistema");
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

    public function aseguradoraFilter()
    {
        Log::info("Informacion de aseguradora por filtro");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if(isset($jsonObject['JcrParameters']['Aseguradora']['filter']))
                {
                    $filter = $jsonObject['JcrParameters']['Aseguradora']['filter'];
                    $aseguradoraTable = TableRegistry::get('Aseguradora');
                    $whereCondition = array(array('OR' => array(
                        array('aseguradora_nombre LIKE' => '%' . $filter . '%'),
                        array('aseguradora_documento_id LIKE' => '%' . $filter . '%'))));

                    //agregar el contain cuando sea necesario
                    $aseguradoraFound = $aseguradoraTable->find()
                        ->where($whereCondition)
                        ->order(array('aseguradora_nombre', 'aseguradora_documento_id'));


                    if ($aseguradoraFound->count() > 0)
                    {
                        $aseguradoraFound = $aseguradoraFound->toArray();
                        $response['JcrResponse']['object'] = $aseguradoraFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontraron Aseguradoras';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error buscando aseguradoras en el sistema");
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
