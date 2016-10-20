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

define('NAMESPACE_RECORDATORIO', 'Recordatorio');

class RecordatorioController extends JcrAPIController
{
    public function sendRecordatorio()
    {
        $result = parent::getDefaultJcrMessage();
        try {
            $result = parent::runWebServiceInitialConfAndValidations(array(), NAMESPACE_RECORDATORIO, 'sendRecordatorio');
            if (parent::isASuccessfulResult($result[WEB_SERVICE_RESPONSE_SIGNATURE])) {
                Log::info("Parametros recividos de forma correcta");
                $to = 'gualdodelacruz@gmail.com';
                $subject = 'Recordatorio';
                $template = 'memo_email';
                $params = array('prueba' => 'Recordatorio 1');
                ReaxiumUtil::sendMail($to, $subject, $template, $params);
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $result = parent::setInternalServiceError($result);

        }
        parent::returnAJson($result);
    }

}