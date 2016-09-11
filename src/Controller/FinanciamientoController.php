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

class FinanciamientoController extends JcrAPIController
{
    public function crearFinanciamiento()
    {
        Log::info("Crear o actualiza un financiamiento en sistema");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if (isset($jsonObject['JcrParameters']["Financiamiento"]))
                {
                    $result = $this->createAFinanciamiento($jsonObject['JcrParameters']);

                    if ($result)
                    {
                        $response = parent::setSuccessfulSave($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudoo crear el financiamiento en sistema';
                    }
                }
                else
                {
                    $response = parent::seInvalidParametersMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error guardando el financiamiento " . $e->getMessage());
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

    private function createAFinanciamiento($financiamientoJSON)
    {
        $result = null;

        try
        {
            $financiamientoTable = TableRegistry::get("Financiamientos");
            $financiamientoEntity = $financiamientoTable->newEntity();

            $financiamientos =$financiamientoTable->patchEntity($financiamientoEntity, $financiamientoJSON['Financiamiento']);

            Log::info($financiamientos);
            $result = $financiamientoTable->save($financiamientos);

        }
        catch (\Exception $e)
        {
            Log::info("Error creando financiamiento");
            Log::info($e->getMessage());
        }

        return $result;
    }

    public function borrarFinanciamiento()
    {
        Log::info("Borrar financiamiento");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $financiamiento_id = !isset($jsonObject['JcrParameters']['Financiamiento']['financiamiento_id']) ? null : $jsonObject['JcrParameters']['Financiamiento']['financiamiento_id'];

            if(isset($financiamiento_id))
            {
                try
                {
                    $financiamientoTable = TableRegistry::get("Financiamiento");
                    $result = $financiamientoTable->deleteAll(array('financiamiento_id'=>$financiamiento_id));

                    if ($result)
                    {
                        $response = parent::setSuccessfulDelete($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudo borrar el financiamiento en sistema';
                    }
                }
                catch(\Exception $e)
                {
                    Log::info("Error borrando financiamiento del sistema");
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

    public function allFinanciamientosInfoWithPagination()
    {
        Log::info("Consulta todos los financiamientos con paginacion");
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
                    $sortedBy = !isset($jsonObject['JcrParameters']["sortedBy"]) ? 'numero_financiamiento' : $jsonObject['JcrParameters']["sortedBy"];
                    $sortDir = !isset($jsonObject['JcrParameters']["sortDir"]) ? 'desc' : $jsonObject['JcrParameters']["sortDir"];
                    $filter = !isset($jsonObject['JcrParameters']["filter"]) ? '' : $jsonObject['JcrParameters']["filter"];
                    $limit = !isset($jsonObject['JcrParameters']["limit"]) ? 10 : $jsonObject['JcrParameters']["limit"];

                    $financiamientoTable = TableRegistry::get('Financiamientos');

                    if(trim($filter) != '')
                    {
                        $whereCondition = array(array('OR' => array(
                            array('numero_financiamiento LIKE' => '%' . $filter . '%'),
                            array('numero_cuotas LIKE' => '%' . $filter . '%'),
                            array('monto_inicial LIKE' => '%' . $filter . '%'))));

                        //agregar los contain cuando sea necesario
                        $financiamientoFound = $financiamientoTable->find()
                            ->where($whereCondition)
                            ->order(array($sortedBy . ' ' . $sortDir));
                    }
                    else
                    {
                        //agregar los contain cuando sea necesario
                        $financiamientoFound = $financiamientoTable->find()->order(array($sortedBy . ' ' . $sortDir));
                    }

                    $count = $financiamientoFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $financiamientoFound = $this->paginate($financiamientoFound);

                    if ($financiamientoFound->count() > 0)
                    {
                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $financiamientoFound = $financiamientoFound->toArray();
                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $financiamientoFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontraron financiamientos';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error borrando financiamiento del sistema");
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

    public function financiamientoInfoById()
    {
        Log::info("Informacion financiamiento por ID");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            $financiamiento_id = !isset($jsonObject['JcrParameters']['Financiamiento']['financiamiento_id']) ? null : $jsonObject['JcrParameters']['Financiamiento']['financiamiento_id'];

            try
            {
                if(isset($financiamiento_id))
                {
                    $financiamientoTable = TableRegistry::get("Financiamientos");
                    $financiamientoFound = $financiamientoTable->findByFinanciamientoId($financiamiento_id);

                    if($financiamientoFound->count() > 0)
                    {
                        $financiamientoFound = $financiamientoFound->toArray();

                    }
                    else
                    {
                        $financiamientoFound = null;
                    }

                    $response['JcrResponse']['object'] = $financiamientoFound;
                    $response = parent::setSuccessfulResponse($response);

                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch(\Exception $e)
            {
                Log::info("Error buscando financiamiento en el sistema");
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

    public function financiamientoFilter()
    {
        Log::info("Informacion financiamiento por filtro");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject))
        {
            try
            {
                if(isset($jsonObject['JcrParameters']['Financiamiento']['filter']))
                {
                    $filter = $jsonObject['JcrParameters']['Financiamiento']['filter'];
                    $financiamientoTable = TableRegistry::get('Financiamientos');
                    $whereCondition = array(array('OR' => array(
                        array('numero_financiamiento LIKE' => '%' . $filter . '%'),
                        array('monto_inicial LIKE' => '%' . $filter . '%'),
                        array('numero_cuotas LIKE' => '%' . $filter . '%')
                    )));

                    //agregar el contain cuando sea necesario
                    $financiamientoFound = $financiamientoTable->find()
                        ->where($whereCondition)
                        ->order(array('numero_financiamiento', 'nuemro_cuotas'));


                    if ($financiamientoFound->count() > 0)
                    {
                        $financiamientoFound = $financiamientoFound->toArray();
                        $response['JcrResponse']['object'] = $financiamientoFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else
                    {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No se encontraron financiamientos';
                    }
                }
                else
                {
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e)
            {
                Log::info("Error buscando financiamiento en el sistema");
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
