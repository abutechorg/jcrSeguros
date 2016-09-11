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

class SiniestroController extends JcrAPIController
{
    public function crearSiniestro()
    {
        Log::info("Crear o actualiza un Siniestro en sistema");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if (isset($jsonObject['JcrParameters']["Siniestro"]))
                {
                    $result = $this->createASiniestro($jsonObject['JcrParameters']);

                    if ($result)
                    {
                        $response = parent::setSuccessfulSave($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
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

    private function createASiniestro($siniestroJSON)
    {
        $result = null;

        try
        {
            $siniestroTable = TableRegistry::get("Siniestro");
            $siniestroEntity = $siniestroTable->newEntity();

            $siniestro =$siniestroTable->patchEntity($siniestroEntity, $siniestroJSON['Siniestro']);

            Log::info($siniestro);
            $result = $siniestroTable->save($siniestro);
        }
        catch (\Exception $e)
        {
            Log::info("Error creando siniestro");
            Log::info($e->getMessage());
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

        if(trim($filter) != '')
        {
            $whereCondition = array(array('OR' => array(
                array('numero_siniestro LIKE' => '%' . $filter . '%'),
                array('usuario_id LIKE' => '%' . $filter . '%'),
                array('vehiculo_id LIKE' => '%' . $filter . '%'))));

            //agregar los contain cuando sea necesario
            $siniestroFound = $siniestroTable->find()
                ->where($whereCondition)
                ->andWhere(array('status_id'=>1))
                ->contain(array('Poliza','Ramo'))
                ->order(array($sortedBy . ' ' . $sortDir));
        }
        else
        {
            //agregar los contain cuando sea necesario
            $siniestroFound = $siniestroTable->find()
                ->contain(array('Poliza','Ramo'))
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
            $siniestro_id = !isset($jsonObject['JcrParameters']['Siniestro']['siniestro_id']) ? null : $jsonObject['JcrParameters']['Siniestro']['siniestro_id'];

            try
            {
                if(isset($siniestro_id))
                {

                    $siniestroFound = $this->getSiniestroById($siniestro_id);

                    if($siniestroFound->count() > 0)
                    {
                        $siniestroFound = $siniestroFound->toArray();
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


    private function getSiniestroById($siniestroId){

        $siniestroTable = TableRegistry::get("Siniestro");
        $siniestroFound = $siniestroTable->find()
            ->where(array('siniestro_id'=>$siniestroId,'Siniestro.status_id'=>1))
            ->contain(array('Poliza','Ramo','Usuarios'));

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
}