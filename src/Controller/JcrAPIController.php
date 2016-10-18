<?php
/**
 * Created by PhpStorm.
 * User: SinAsignari54GB1TB
 * Date: 20/03/2016
 * Time: 02:48 PM
 */

namespace App\Controller;

use Cake\Event\Event;
use Cake\Log\Log;
use App\Util\ReaxiumApiMessages;

class JcrAPIController extends AppController
{

    private  $JcrResponseObject = array("JcrResponse" => array("code"=>"","message"=>"","object"=>array()));

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

}