<?php
/**
 * Created by PhpStorm.
 * User: Eduardo Luttinger
 * Date: 05/10/2016
 * Time: 10:44 AM
 */

namespace App\Controller;


use App\Util\ReaxiumApiMessages;
use App\Util\ReaxiumUtil;
use Cake\Core\Exception\Exception;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

class ClientController extends JcrAPIController
{

    /**
     *
     * Crea un cliente en sistema
     *
     * @param $clientJson
     * @return bool|\Cake\Datasource\EntityInterface|mixed
     */
    function createOrEditAClient($clientJson)
    {
        $clienteTable = TableRegistry::get("Clientes");
        $clientObject = $clienteTable->newEntity();
        if (isset($clientJson['cliente_id'])) {
            $clientObject->cliente_id = $clientJson['cliente_id'];
        }
        $clientObject->nombre_cliente = $clientJson['nombre_cliente'];
        $clientObject->apellido_cliente = $clientJson['apellido_cliente'];
        $clientObject->documento_id_cliente = $clientJson['documento_id_cliente'];
        $clientObject->fecha_nacimiento = $clientJson['fecha_nacimiento'];
        $clientObject->correo_cliente = $clientJson['correo_cliente'];
        $clientObject->direccion = $clientJson['direccion'];
        $clientObject->tipo_cliente_id = $clientJson['tipo_cliente_id'];
        $clientObject->genero_cliente = $clientJson['genero_cliente'];
        $result = $clienteTable->save($clientObject);
        return $result;
    }

    /**
     *
     * Crea un cliente en sistema
     *
     * @param $clientJson
     * @return bool|\Cake\Datasource\EntityInterface|mixed
     */
    function createOrEditAClientBatch($clienteTable, $clientJson)
    {
        $clientObject = $clienteTable->newEntity();
        if (isset($clientJson['cliente_id'])) {
            $clientObject->cliente_id = $clientJson['cliente_id'];
        }
        $clientObject->nombre_cliente = $clientJson['nombre_cliente'];
        $clientObject->tipo_cliente_id = $clientJson['tipo_cliente_id'];
        $clientObject->documento_id_cliente = $clientJson['documento_id_cliente'];
        if (isset($clientJson['apellido_cliente'])) {
            $clientObject->apellido_cliente = $clientJson['apellido_cliente'];
        }
        if (isset($clientJson['fecha_nacimiento'])) {
            $clientObject->fecha_nacimiento = $clientJson['fecha_nacimiento'];
        }
        if (isset($clientJson['correo_cliente'])) {
            $clientObject->correo_cliente = $clientJson['correo_cliente'];
        }
        if (isset($clientJson['direccion'])) {
            $clientObject->direccion = $clientJson['direccion'];
        }
        if (isset($clientJson['genero_cliente'])) {
            $clientObject->genero_cliente = $clientJson['genero_cliente'];
        }
        $result = $clienteTable->save($clientObject);
        return $result;
    }


    /**
     * Busca clientes por numero de documento
     */
    public function clientByDocument()
    {
        parent::setResultAsAJson();
        $jsonReceived = parent::getJsonReceived();
        $response = parent::getDefaultJcrMessage();
        Log::info("Parameters Received: " . json_encode($jsonReceived));
        try {
            if (parent::validJcrJsonHeader($jsonReceived)) {

                $paramsToFind = array('document_number');
                $parameterValidation = ReaxiumUtil::validateParameters($paramsToFind, $jsonReceived['JcrParameters']["Client"]);
                if ($parameterValidation['code'] == 0) {

                    $documentNumber = $jsonReceived['JcrParameters']["Client"]['document_number'];
                    $clienteTable = TableRegistry::get("Clientes");
                    $clientFound = $clienteTable->find()->where(array('documento_id_cliente LIKE' => '%' . $documentNumber . '%'));

                    Log::info($clientFound);

                    if ($clientFound->count() > 0) {
                        $clientFound = $clientFound->toArray();
                        Log::info("Cliente encontrado: " . json_encode($clientFound));

                        $response = parent::setSuccessfulResponse($response);
                        $response['JcrResponse']['object'] = $clientFound;

                    } else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = "Cliente no encontrado";
                    }

                } else {
                    $response = parent::seInvalidParametersMessage($response);
                    $response['JcrResponse']['message'] = $parameterValidation['message'];
                }
            } else {
                $response = parent::seInvalidParametersMessage($response);
            }
        } catch (Exception $e) {
            Log::info("Error guardando la cobertura " . $e->getMessage());
            $response['JcrResponse']['code'] = ReaxiumApiMessages::$INTERNAL_SERVER_ERROR_CODE;
            $response['JcrResponse']['message'] = $e->getMessage();
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }

    /**
     *
     * Obtiene un cliente de la base de datos segun su id
     *
     * @param $clienteId
     * @return $this|array|null
     */
    function getClientById($clienteId)
    {
        $clienteTable = TableRegistry::get("Clientes");
        $clienteResult = $clienteTable->find()->where(array('cliente_id' => $clienteId));
        if($clienteResult->count() > 0){
            $clienteResult = $clienteResult->toArray()[0];
        }else{
            $clienteResult = null;
        }
        return $clienteResult;
    }

}