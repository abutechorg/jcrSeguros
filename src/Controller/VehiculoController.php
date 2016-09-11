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
    public function crearVehiculo()
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
    }

    private function createAVehiculo($vehiculoJSON)
    {
        $result = null;

        try
        {
            $vehiculoTable = TableRegistry::get("Vehiculo");
            $vehiculoEntity = $vehiculoTable->newEntity();

            $vehiculos =$vehiculoTable->patchEntity($vehiculoEntity, $vehiculoJSON['Vehiculo']);

            Log::info($vehiculos);
            $result = $vehiculoTable->save($vehiculos);

        }
        catch (\Exception $e)
        {
            Log::info("Error creando vehiculo");
            Log::info($e->getMessage());
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
                if(isset($jsonObject['JcrParameters']['Vehiculo']['filter']))
                {
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
}
