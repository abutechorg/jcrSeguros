<?php
namespace App\Util;

use Cake\I18n\Time;
use Cake\Log\Log;
define('TIME_ZONE', 'America/New_York');
use DateTime;
use DateInterval;
/**
 * Created by PhpStorm.
 * User: Eduardo Luttinger
 * Date: 20/03/2016
 * Time: 03:46 PM
 */
class ReaxiumUtil
{

    public static function getDate($dateAsString)
    {
        $date = new \DateTime($dateAsString);
        return $date->format("Y-m-d H:i:s");
    }

    public static function validateParameters($arrayToTest, $arrayReceived)
    {
        $result = array('code' => '0', 'message' => '');
        if(sizeof($arrayToTest) > 0){
            foreach ($arrayToTest as $value) {
                if (!isset($arrayReceived[$value])) {
                    $result['code'] = '1';
                    $result['message'] = 'invalid parameters, missing parameter ' . $value;
                    break;
                }
            }
        }
        return $result;
    }



    public static function getSystemDate(){
        $time = Time::now();
        $time->setTimezone(TIME_ZONE);
        $dateAssigned = $time->i18nFormat('YYYY-MM-dd HH:mm:ss');
        return $dateAssigned;
    }

    public static function getSystemDateMinusTime($timeToAdd, $unit){
        $time = Time::now();
        $time->setTimezone(TIME_ZONE);
        $time = new DateTime($time->i18nFormat('YYYY-MM-dd HH:mm:ss'));
        $time->sub(new DateInterval('PT' . $timeToAdd . $unit));
        $dateToReturn =$time->format('Y-m-d H:i:s');
        return $dateToReturn;
    }



    public static function arrayCopy( array $array ) {
        $result = array();
        foreach( $array as $key => $val ) {
            if( is_array( $val ) ) {
                $result[$key] = arrayCopy( $val );
            } elseif ( is_object( $val ) ) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
    }


    /**
     * Metodo para envio de email
     * @param $to
     * @param $subject
     * @param $template
     * @param $params
     */

    public static function sendMail($to, $subject, $template, $params)
    {

        try {

            $email = new Email('default');
            $email->emailFormat('html');
            $email->template($template);
            $email->viewVars($params);
            $email->from(array(ReaxiumApiMessages::$EMAILS[0] => 'Reaxium'));
            $email->to($to);
            $email->subject($subject);
            $email->send();
            Log::info("Email sent to: " . $to);

        } catch (\Exception $e) {
            Log::info("Error enviando correo" . $e->getMessage());

        }

    }
}