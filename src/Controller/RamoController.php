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

class RamoController extends JcrAPIController
{
    public function crearRamo()
    {
        Log::info("Crear o actualiza un ramo en sistema");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if (isset($jsonObject['JcrParameters']["Ramo"]))
                {
                    $result = $this->createARamo($jsonObject['JcrParameters']);

                    if ($result)
                    {
                        $response = parent::setSuccessfulSave($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudoo crear el ramo en sistema';
                    }
                }
                else
                {
                    $response = parent::seInvalidParametersMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error guardando el ramo " . $e->getMessage());
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

    private function createARamo($ramoJSON)
    {
        $result = null;

        try
        {
            $ramoTable = TableRegistry::get("Ramo");
            $ramoEntity = $ramoTable->newEntity();

            $ramos =$ramoTable->patchEntity($ramoEntity, $ramoJSON['Ramo']);

            Log::info($ramos);
            $result = $ramoTable->save($ramos);

        }
        catch (\Exception $e)
        {
            Log::info("Error creando ramo");
            Log::info($e->getMessage());
        }

        return $result;
    }

    public function borrarRamo()
    {
        Log::info("Borrar ramo");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $ramo_id = !isset($jsonObject['JcrParameters']['Ramo']['ramo_id']) ? null : $jsonObject['JcrParameters']['Ramo']['ramo_id'];

            if(isset($ramo_id))
            {
                try
                {
                    $ramoTable = TableRegistry::get("Ramo");
                    $result = $ramoTable->deleteAll(array('usuario_id'=>$ramo_id));

                    if ($result)
                    {
                        $response = parent::setSuccessfulDelete($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudo borrar el ramo en sistema';
                    }
                }
                catch(\Exception $e)
                {
                    Log::info("Error borrando ramo del sistema");
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

    public function allRamoInfoWithPagination()
    {
        Log::info("Consulta todos los ramos con paginacion");
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
                    $sortedBy = !isset($jsonObject['JcrParameters']["sortedBy"]) ? 'ramo_nombre' : $jsonObject['JcrParameters']["sortedBy"];
                    $sortDir = !isset($jsonObject['JcrParameters']["sortDir"]) ? 'desc' : $jsonObject['JcrParameters']["sortDir"];
                    $filter = !isset($jsonObject['JcrParameters']["filter"]) ? '' : $jsonObject['JcrParameters']["filter"];
                    $limit = !isset($jsonObject['JcrParameters']["limit"]) ? 10 : $jsonObject['JcrParameters']["limit"];

                    $ramoTable = TableRegistry::get('Ramo');

                    if(trim($filter) != '')
                    {
                        $whereCondition = array(array('OR' => array(
                            array('ramo_nombre LIKE' => '%' . $filter . '%'))));

                        //agregar los contain cuando sea necesario
                        $ramoFound = $ramoTable->find()
                            ->where($whereCondition)
                            ->order(array($sortedBy . ' ' . $sortDir));
                    }
                    else
                    {
                        //agregar los contain cuando sea necesario
                        $ramoFound = $ramoTable->find()->order(array($sortedBy . ' ' . $sortDir));
                    }

                    $count = $ramoFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $ramoFound = $this->paginate($ramoFound);

                    if ($ramoFound->count() > 0)
                    {
                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $ramoFound = $ramoFound->toArray();
                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $ramoFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontraron ramos';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
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

    public function ramoInfoById()
    {
        Log::info("Informacion ramo por ID");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $ramo_id = !isset($jsonObject['JcrParameters']['Ramo']['ramo_id']) ? null : $jsonObject['JcrParameters']['Ramo']['ramo_id'];

            try
            {
                if(isset($ramo_id))
                {
                    $ramoTable = TableRegistry::get("Ramo");
                    $ramoFound = $ramoTable->findByRamoId($ramo_id);

                    if($ramoFound->count() > 0)
                    {
                        $ramoFound = $ramoFound->toArray();

                    }
                    else
                    {
                        $ramoFound = null;
                    }

                    $response['JcrResponse']['object'] = $ramoFound;
                    $response = parent::setSuccessfulResponse($response);

                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch(\Exception $e)
            {
                Log::info("Error buscando ramo en el sistema");
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

    public function ramoFilter()
    {
        Log::info("Informacion ramo por filtro");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if(isset($jsonObject['JcrParameters']['Ramo']['filter']))
                {
                    $filter = $jsonObject['JcrParameters']['Ramo']['filter'];
                    $ramoTable = TableRegistry::get('Ramo');
                    $whereCondition = array(array('OR' => array(
                        array('ramo_nombre LIKE' => '%' . $filter . '%')
                    )));

                    //agregar el contain cuando sea necesario
                    $ramoFound = $ramoTable->find()
                        ->where($whereCondition)
                        ->order(array('ramo_nombre'));


                    if ($ramoFound->count() > 0) {
                        $ramoFound = $ramoFound->toArray();
                        $response['JcrResponse']['object'] = $ramoFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontraron ramos';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error buscando ramos en el sistema");
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