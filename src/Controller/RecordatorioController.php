<?php
/**
 * Created by PhpStorm.
 * User: Pc
 * Date: 19/10/2016
 * Time: 05:27 PM
 */

namespace App\Controller;


use App\Util\ReaxiumUtil;
use Cake\Log\Log;
define('NAMESPACE_RECORDATORIO','Recordatorio');
class RecordatorioController extends JcrAPIController
{
    public function sendRecordatorio(){
        $result = parent::getDefaultJcrMessage();
        try{

            //aqui va la logica del negocio

           $result = parent::runWebServiceInitialConfAndValidations(array(),NAMESPACE_RECORDATORIO,'sendRecordatorio');
            if(parent::isASuccessfulResult($result)){

                $to = 'gabo3cr@gmail.com';
                $subject = 'Recordatorio';
                $template = 'memo_email';
                $params = array('prueba'=>'hola como estas');

                ReaxiumUtil::sendMail($to,$subject,$template,$params);
            }



        }catch (\Exception $e){
            Log::info($e->getMessage());
            $result = parent::setInternalServiceError($result);

        }
        parent::returnAJson($result);
    }


}