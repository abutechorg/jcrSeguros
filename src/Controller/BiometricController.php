<?php
/**
 * Created by PhpStorm.
 * User: SinAsignari54GB1TB
 * Date: 20/04/2016
 * Time: 05:37 PM
 */

namespace App\Controller;

use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Util\ReaxiumApiMessages;

define("BIOMETRIC_FILE_PATH", "/reaxium_user_images/biometric_user_images/");
define("BIOMETRIC_FILE_FULL_PATH","/var/www/html/reaxium_user_images/biometric_user_images/");
//define("BIOMETRIC_FILE_FULL_PATH", "C:/xampp/htdocs/reaxium_user_images/biometric_user_images/");

class BiometricController extends ReaxiumAPIController
{

    public function biometricAccess()
    {
        parent::setResultAsAJson();
        $response = parent::getDefaultReaxiumMessage();
        $object = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($object));

        if(parent::validReaxiumJsonHeader($object)){

            $deviceId = !isset($object['ReaxiumParameters']['device_id']) ? null : $object['ReaxiumParameters']['device_id'];
            $userId = !isset($object['ReaxiumParameters']['user_id']) ? null : $object['ReaxiumParameters']['user_id'];
            $biometricHexaCode = !isset($object['ReaxiumParameters']['biometricHexaCode']) ? null : $object['ReaxiumParameters']['biometricHexaCode'];
            $biometricImageName = !isset($object['ReaxiumParameters']['biometricImageName']) ? null : $object['ReaxiumParameters']['biometricImageName'];
            $biometricImage = !isset($object['ReaxiumParameters']['biometricImage']) ? null : $object['ReaxiumParameters']['biometricImage'];


            if (isset($biometricHexaCode) && isset($biometricImage) && isset($userId) && isset($biometricImageName)) {
                try {
                    $userDataAccessTable = TableRegistry::get("UserAccessData");
                    $userAccessControlTable = TableRegistry::get("UserAccessControl");
                    $biometricInfo = $userDataAccessTable->findByUserIdAndAccessTypeId($userId, 2);
                    if ($biometricInfo->count() > 0) {

                        $biometricInfo = $biometricInfo->toArray();
                        $userDataAccessTable->updateAll(array('biometric_code' => $biometricHexaCode), array('user_access_data_id' => $biometricInfo[0]['user_access_data_id']));

                        if(isset($deviceId)){
                            $userAccessControl = $userAccessControlTable->findByUserAccessDataIdAndDeviceId($biometricInfo[0]['user_access_data_id'],$deviceId);
                            if($userAccessControl->count() < 1){
                                $userAccessControl = $userAccessControlTable->newEntity();
                                $userAccessControl->device_id = $deviceId;
                                $userAccessControl->user_access_data_id = $biometricInfo[0]['user_access_data_id'];
                                $userAccessControlTable->save($userAccessControl);
                            }
                        }

                        Log::info("Biometrico actualizado para el usuario: " .$userId);
                        Log::info(json_encode($biometricInfo));

                    } else {

                        $userAccessData = $userDataAccessTable->newEntity();
                        $userAccessData->user_id = $userId;
                        $userAccessData->access_type_id = 2;
                        $userAccessData->biometric_code = $biometricHexaCode;
                        $userAccessData = $userDataAccessTable->save($userAccessData);

                        if(isset($deviceId)){
                            $userAccessControl = $userAccessControlTable->newEntity();
                            $userAccessControl->device_id = $deviceId;
                            $userAccessControl->user_access_data_id = $userAccessData['user_access_data_id'];
                            $userAccessControlTable->save($userAccessControl);
                        }

                        Log::info("Biometrico creado para el usuario: " + $userId);
                        Log::info(json_encode($userAccessData));

                    }
                    $userTable = TableRegistry::get("Users");
                    $imageFullPath = "http://" . $_SERVER['SERVER_NAME'] . BIOMETRIC_FILE_PATH . $biometricImageName;
                    $userTable->updateAll(array('fingerprint' => $imageFullPath), array('user_id' => $userId));
                    file_put_contents(BIOMETRIC_FILE_FULL_PATH . $biometricImageName, base64_decode($biometricImage));

                    Log::info("Biometrico configurado con exito para el usuario: " + $userId);

                    $response = parent::setSuccessfulResponse($response);

                } catch (\Exception $e) {
                    Log::info('Error loading the biometric information for the user: ' . $userId);
                    Log::info($e->getMessage());
                    $response = parent::setInternalServiceError($response);
                }
            } else {
                $response = parent::seInvalidParametersMessage($response);
            }
        }else{
            $response = parent::setInvalidJsonMessage($response);
        }
        $this->response->body(json_encode($response));
    }

}