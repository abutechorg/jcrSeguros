<?php
/**
 * Created by PhpStorm.
 * User: SinAsignari54GB1TB
 * Date: 20/03/2016
 * Time: 02:48 PM
 */

namespace App\Controller;

use App\Util\ReaxiumUtil;
use Cake\Event\Event;
use Cake\Log\Log;
use App\Util\ReaxiumApiMessages;

define('WEB_SERVICE_REQUEST_SIGNATURE','JcrParameters');
define('WEB_SERVICE_RESPONSE_SIGNATURE','JcrResponse');

class JcrAPIController extends AppController
{

    private  $JcrResponseObject = array(WEB_SERVICE_RESPONSE_SIGNATURE => array("code"=>"","message"=>"","object"=>array()));

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->response->header(array('Access-Control-Allow-Origin' => '*'));
    }

    public function handleError($code, $description, $file = null, $line = null, $context = null) {
        Log::info("Handling the error fuck");
        if (error_reporting() == 0 || $code === 2048 || $code === 8192) {
            return;
        }
        // throw error for further handling
        throw new exception(strip_tags($description));
    }

    function exception_error_handler($errno, $errstr, $errfile, $errline ) {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    }

    public function setResultAsAJson()
    {
        $this->autoRender = false;
        $this->response->type('json');
    }

    public function getJsonReceived()
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        return $input;
    }

    public function validJcrJsonHeader($jsonObject){
        $isValid = false;
        if(isset($jsonObject['JcrParameters'])){
            $isValid = true;
        }
        return $isValid;
    }

    public function getDefaultJcrMessage(){
        $JcrMessage = $this->JcrResponseObject;
        return $JcrMessage;
    }

    public function seInvalidParametersMessage($JcrMessage){
        $JcrMessage['JcrResponse']['code'] = ReaxiumApiMessages::$INVALID_PARAMETERS_CODE;
        $JcrMessage['JcrResponse']['message'] = ReaxiumApiMessages::$INVALID_PARAMETERS_MESSAGE;
        return $JcrMessage;
    }

    public function setInternalServiceError($JcrMessage){
        $JcrMessage['JcrResponse']['code'] = ReaxiumApiMessages::$INTERNAL_SERVER_ERROR_CODE;
        $JcrMessage['JcrResponse']['message'] = ReaxiumApiMessages::$INTERNAL_SERVER_ERROR_MESSAGE;
        return $JcrMessage;
    }

    public function setInvalidJsonMessage($JcrMessage){
        $JcrMessage['JcrResponse']['code'] = ReaxiumApiMessages::$INVALID_JSON_OBJECT_CODE;
        $JcrMessage['JcrResponse']['message'] = ReaxiumApiMessages::$INVALID_JSON_OBJECT_MESSAGE;
        return $JcrMessage;
    }

    public function setInvalidJsonHeader($JcrMessage){
        $JcrMessage['JcrResponse']['code'] = ReaxiumApiMessages::$INVALID_JSON_HEADER_CODE;
        $JcrMessage['JcrResponse']['message'] = ReaxiumApiMessages::$INVALID_JSON_HEADER_MESSAGE;
        return $JcrMessage;
    }

    public function setSuccessfulResponse($JcrMessage){
        $JcrMessage['JcrResponse']['code'] = ReaxiumApiMessages::$SUCCESS_CODE;
        $JcrMessage['JcrResponse']['message'] = ReaxiumApiMessages::$SUCCESS_MESSAGE;
        return $JcrMessage;
    }

    public function setSuccessAccess($JcrMessage){
        $JcrMessage['JcrResponse']['code'] = ReaxiumApiMessages::$SUCCESS_CODE;
        $JcrMessage['JcrResponse']['message'] = ReaxiumApiMessages::$SUCCESS_ACCESS;
        return $JcrMessage;
    }

    public function setSuccessfulDelete($JcrMessage){
        $JcrMessage['JcrResponse']['code'] = ReaxiumApiMessages::$SUCCESS_CODE;
        $JcrMessage['JcrResponse']['message'] = ReaxiumApiMessages::$SUCCESS_DELETED_MESSAGE;
        return $JcrMessage;
    }

    public function setSuccessfulUpdated($JcrMessage){
        $JcrMessage['JcrResponse']['code'] = ReaxiumApiMessages::$SUCCESS_CODE;
        $JcrMessage['JcrResponse']['message'] = ReaxiumApiMessages::$SUCCESS_UPDATED_MESSAGE;
        return $JcrMessage;
    }

    public function setSuccessfulSave($JcrMessage){
        $JcrMessage['JcrResponse']['code'] = ReaxiumApiMessages::$SUCCESS_CODE;
        $JcrMessage['JcrResponse']['message'] = ReaxiumApiMessages::$SUCCESS_SAVE_MESSAGE;
        return $JcrMessage;
    }

    /**
     *
     * Inicializa las configuraciones necesarias para que el servicio web haga render de las respuestas en JSON
     * Valida la cabecera de la llamada (Agregar validacion de apitoken)
     * Toma el objeto json que se recibe de la llamada y valida que en el existan los parametros esperados
     *
     *
     * @param $parametersToBeTested arreglo de parametros que se esperan en la llamada
     * @param $namespace nombre del controlador "en literal" de donde se recibe la llamada
     * @param $webServiceMethodName "nombre del metodo que se expone como servicio"
     * @return arreglo con el codigo y mensajes del resultado de las validaciones iniciales con el objeto json recibido
     */
    public function runWebServiceInitialConfAndValidations($parametersToBeTested, $namespace, $webServiceMethodName)
    {
        Log::info("Running the webservice method " . $webServiceMethodName);
        $this->setResultAsAJson();
        $jsonObject = $this->getJsonReceived();
        $result = $this->getDefaultJcrMessage();
        Log::debug("Object Received: " . json_encode($jsonObject));
        if ($this->validJcrJsonHeader($jsonObject)) {
            if (isset($jsonObject[WEB_SERVICE_REQUEST_SIGNATURE][$namespace])) {
                $resultValidation = ReaxiumUtil::validateParameters($parametersToBeTested, $jsonObject[WEB_SERVICE_REQUEST_SIGNATURE][$namespace]);
                $result[WEB_SERVICE_RESPONSE_SIGNATURE]['code'] = $resultValidation['code'];
                $result[WEB_SERVICE_RESPONSE_SIGNATURE]['message'] = $resultValidation['message'];
                $result[WEB_SERVICE_RESPONSE_SIGNATURE]['object'] = $jsonObject[WEB_SERVICE_REQUEST_SIGNATURE][$namespace];
            } else {
                $result = $this->setInvalidJsonHeader($result);
            }
        } else {
            $result = $this->setInvalidJsonHeader($result);
        }
        return $result;
    }

    /**
     * Convierta la respuesta y el tipo de respuesta en JSON
     * @param $result
     */
    public function returnAJson($result)
    {
        Log::debug("Json Result: " . json_encode($result));
        $this->response->body(json_encode($result));
    }

    /**
     *
     * Valida si la respuesta es exitosa
     *
     * @param $result
     * @return bool
     */
    public function isASuccessfulResult($result)
    {
        $isSuccessFull = false;
        if ($result['code'] == ReaxiumApiMessages::$SUCCESS_CODE) {
            $isSuccessFull = true;
        } else {
            //se limpia el objeto resultado ya que la respuesta no es exitosa
            $result['object'] = array();
        }
        return $isSuccessFull;
    }

}