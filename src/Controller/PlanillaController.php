<?php
/**
 * Created by PhpStorm.
 * User: VFG.
 * Date: 21/5/2016
 * Time: 08:45
 */

namespace App\Controller;

use Cake\Core\Exception\Exception;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use App\Util\ReaxiumApiMessages;
use Cake\Mailer\Email;


define('PATH_DIRECTORY', '../../download_planilla/');
define("TYPE_USER_ADMIN", 1);
define("TYPE_USER_STUDENT", 2);
define("TYPE_USER_STAKEHOLDER", 3);
define("TYPE_USER_DRIVER", 4);
define("TYPE_USER_ADMIN_SCHOOL", 5);
define("TYPE_USER_ADMIN_CALL_CENTER", 6);
define("TYPE_ACCESS_DOCUMENT_ID", 4);
define("TYPE_ACCESS_USER_LOGIN",1);
define("MIN_RANDOM", 10000000);
define("MAX_RANDOM", 99999999);
define("MAX_COLUMN_CSV_USERS",13);
define("MAX_COLUMN_CSV_STOPS",5);
define("MAX_COLUMN_CSV_SCHOOL",7);
define("REPORT_USERS", 1);
define("REPORT_SCHOOL", 2);
define("REPORT_STOPS", 3);


class Planillacontroller extends JcrAPIController
{
    private function validateHeader($header)
    {

        $validate = true;

        foreach ($header as $column) {
            if ((!preg_match('/^[A-z]+$/', trim(str_replace(" ", "", $column))))) {
                Log::info("No es un nombre de columna valido: " . trim(str_replace(" ", "", $column)));
                $validate = false;
                break;
            }
        }

        return $validate;
    }

    private function readCSV($csvFile, $name_file, $typeDocument)
    {

        try {

            $delimiter = "";
            $file_handle = fopen($csvFile, 'r');

            // extrae cabecera del reporte
            $headerCsv = fgets($file_handle);

            Log::info('Tipo de Header:');
            Log::info($headerCsv);

            //validar el tipo de separador
            $columnsHeader = explode(";", $headerCsv);

            if (count($columnsHeader) > 1) {
                Log::info("column header size: " . count($columnsHeader) . " type delimiter = ';'");
                $delimiter = ";";
            } else {
                throw new \Exception('Csv file error processing incorrect Delimiter', 90);
            }

            //validar cabecera y cantidad de columnas

            $validateColumnAlfha = $this->validateHeader($columnsHeader);

            if ((count($columnsHeader) != MAX_COLUMN_CSV_USERS) && ($typeDocument == REPORT_USERS)) {

                throw new \Exception("Error wrong file format file " . $name_file . ", please check to complete the process", 91);
            } else if (!$validateColumnAlfha && $typeDocument == REPORT_USERS) {

                throw new \Exception("Error wrong file format file " . $name_file . ", please check to complete the process", 91);
            } else if (count($columnsHeader) != MAX_COLUMN_CSV_SCHOOL && $typeDocument == REPORT_SCHOOL) {

                throw new \Exception("Error wrong file format file " . $name_file . ", please check to complete the process", 91);
            } else if (!$validateColumnAlfha && $typeDocument == REPORT_SCHOOL) {

                throw new \Exception("Error wrong file format file " . $name_file . ", please check to complete the process", 91);
            } else if ((count($columnsHeader) != MAX_COLUMN_CSV_STOPS) && ($typeDocument == REPORT_STOPS)) {

                throw new \Exception("Error wrong file format file " . $name_file . ", please check to complete the process", 91);
            } else if (!$validateColumnAlfha && $typeDocument == REPORT_STOPS) {

                throw new \Exception("Error wrong file format file " . $name_file . ", please check to complete the process", 91);
            }

            // se extraelas demas lineas del documento  y se guarda en un arreglo
            while (!feof($file_handle)) {
                $line_of_text[] = fgetcsv($file_handle, 1024, $delimiter);
            }

        } catch (\Exception $e) {
            Log::info("Error leyendo archivo csv: " . $e->getMessage());

            if ($e->getCode() == 90 || $e->getCode() == 91) {
                throw $e;
            }

        } finally {
            fclose($file_handle);
        }

        return array_filter($line_of_text);
    }


    /**
     * @api {post} /Bulk/bulkUsersSystem Create A New User in the system
     * @apiName createUser
     * @apiGroup Users
     *
     * @apiParamExample {json} Request-Example:
     * {
     *  "ReaxiumParameters":{
     *  "BulkUsers":{
     *  "name_file":"test_school_users1.csv"
     *  }
     *  }
     *  }
     *
     * {
     *  "ReaxiumResponse": {
     *  "code": 0,
     *  "message": "SUCCESSFUL REQUEST",
     *  "object": {
     *  "register_saved": 2
     *  }
     *  }
     *  }
     *
     * @apiErrorExample Error-Response: User already exist
     *  {
     *      "ReaxiumResponse": {
     *          "code": 101,
     *          "message": "User id number already exist in the system",
     *          "object": []
     *          }
     *      }
     *
     */
    public function downloadPlanilla()
    {

        Log::info("Service for load massive users in system");

        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();

        if (parent::validJcrJsonHeader($jsonObject)) {

            try {

                $name_file = !isset($jsonObject['JcrParameters']['name_file']) ? null
                    : $jsonObject['JcrParameters']['name_file'];

                if (isset($name_file)) {

                    //Ubicacion del directorio
                    $path = PATH_DIRECTORY . DIRECTORY_SEPARATOR . $name_file;

                    if (file_exists($path)) {

                        //Leer archivo ccv
                        $csv = $this->readCSV($path, $name_file, REPORT_USERS);
                        $arrayObjectUsers = array();

                        Log::info(json_encode($csv));

                        $usersTable = TableRegistry::get("Users");
                        $phoneTable = TableRegistry::get("PhoneNumbers");
                        $addressTable = TableRegistry::get("Address");
                        $userAccessTable = TableRegistry::get("UserAccessData");

                        $validateFile = true;
                        $messageError = array('code' => 0, 'message' => '');

                        if (count($csv) > 0) {

                            //recorre cada row del arreglo csv
                            for ($i = 0; $i < count($csv); $i++) {

                                $lineaFile = $i + 2;
                                $documentId = empty(trim($csv[$i][0])) ? null : trim($csv[$i][0]);
                                $firstName = empty(trim($csv[$i][1])) ? null : trim($csv[$i][1]);
                                $middleName = empty(trim($csv[$i][2])) ? null : trim($csv[$i][2]);
                                $lastName = empty(trim($csv[$i][3])) ? null : trim($csv[$i][3]);
                                $birthdate = empty(trim($csv[$i][4])) ? null : trim($csv[$i][4]);
                                $phoneHome = empty(trim($csv[$i][5])) ? null : trim($csv[$i][5]);
                                $phoneOffice = empty(trim($csv[$i][6])) ? null : trim($csv[$i][6]);
                                $phoneOther = empty(trim($csv[$i][7])) ? null : trim($csv[$i][7]);
                                $businessNumber = empty(trim($csv[$i][8])) ? null : trim($csv[$i][8]);
                                $typeUser = empty(trim($csv[$i][9])) ? null : trim($csv[$i][9]);
                                $emailUser = empty(trim($csv[$i][10])) ? null : trim($csv[$i][10]);
                                $documentIdSForParents = empty(trim($csv[$i][11])) ? null : trim($csv[$i][11]);
                                $userNameStakeholder = empty(trim($csv[$i][12])) ? null : trim($csv[$i][12]);


                                if (isset($documentId) && isset($firstName)
                                    && isset($lastName) && isset($birthdate) && isset($businessNumber)
                                    && isset($typeUser)
                                ) {

                                    $entityUser = $usersTable->newEntity();
                                    $entityAddress = $addressTable->newEntity();
                                    $arrayPhone = array();

                                    // se obtiene id del tipo de usuario
                                    $user_type = $this->findTypeUserId($typeUser);

                                    if (isset($user_type)) {
                                        $entityUser->user_type_id = $user_type[0]['user_type_id'];
                                    } else {

                                        $messageError['code'] = 2;
                                        $messageError['message'] = "User type " . $typeUser . " is invalid in line: " . $lineaFile;
                                        $validateFile = false;
                                        break;
                                    }

                                    // se obtiene el id del negocio
                                    $business = $this->findSchoolId($businessNumber);

                                    if (isset($business)) {
                                        $entityUser->business_id = $business[0]['business_id'];
                                    } else {
                                        $messageError['code'] = 1;
                                        $messageError['message'] = "Business number " . $businessNumber . " is invalid in line: " . $lineaFile;
                                        $validateFile = false;
                                        break;
                                    }

                                    // si el tipo de usuario es estudiante se obtiene el ID de csv de resto se genera automaticamente
                                    //para otro usuario
                                    if ($entityUser->user_type_id == TYPE_USER_STUDENT) {
                                        $entityUser->document_id = $documentId;
                                    }
                                    else if ($entityUser->user_type_id == TYPE_USER_STAKEHOLDER) {

                                        if (isset($documentIdSForParents) && isset($userNameStakeholder)) {

                                            $entityUser->document_id = $this->findAndGenerateDocumentId();
                                            $validateUserName = $this->findUserName($userNameStakeholder);

                                            if($validateUserName){
                                                $entityUser->user_login_name = $userNameStakeholder;
                                                $entityUser->user_password = $this->generaPass();
                                                $entityUser->documentStudentRelation = $documentIdSForParents;

                                                Log::info("Username: " .$userNameStakeholder. " is already registered user,check in line:" .$lineaFile);
                                            }else{
                                                $messageError['code'] = 10;
                                                $messageError['message'] = "Username: " .$userNameStakeholder. " is already registered user,check in line: " .$lineaFile;
                                                $validateFile = false;
                                                break;
                                            }

                                        } else {
                                            $messageError['code'] = 3;
                                            $messageError['message'] = 'field of relationship parents and students is empty: ' . $lineaFile;
                                            $validateFile = false;
                                            break;
                                        }
                                    } else if ($entityUser->user_type_id == TYPE_USER_DRIVER) {
                                        $entityUser->document_id = $this->findAndGenerateDocumentId();
                                    } else if ($entityUser->user_type_id == TYPE_USER_ADMIN) {
                                        $entityUser->document_id = $this->findAndGenerateDocumentId();
                                    } else if ($entityUser->user_type_id == TYPE_USER_ADMIN_SCHOOL) {
                                        $entityUser->document_id = $this->findAndGenerateDocumentId();
                                    } else if ($entityUser->user_type_id == TYPE_USER_ADMIN_CALL_CENTER) {
                                        $entityUser->document_id = $this->findAndGenerateDocumentId();
                                    } else {
                                        $messageError['code'] = 4;
                                        $messageError['message'] = "User type: " . $typeUser . " is invalid in line: " . $lineaFile;
                                        $validateFile = false;
                                        break;
                                    }

                                    $entityUser->first_name = $firstName;
                                    $entityUser->second_name = $middleName;
                                    $entityUser->first_last_name = $lastName;

                                    //validado birthdate
                                    $validateBirthdate = $this->validateDate($birthdate);

                                    if ($validateBirthdate) {

                                        $date = explode("/", $birthdate);
                                        $m = $date[0];
                                        $d = $date[1];
                                        $y = $date[2];

                                        if (!checkdate($m, $d, $y)) {
                                            $messageError['code'] = 6;
                                            $messageError['message'] = "Incorrect date in line:" . $lineaFile;
                                            $validateFile = false;
                                            break;
                                        }

                                        $dateFinal = $d . '/' . $m . '/' . $y;
                                        $entityUser->birthdate = $dateFinal;

                                    } else {

                                        $messageError['code'] = 5;
                                        $messageError['message'] = "Birthdate user " . $birthdate . " has an invalid format suggested is the mm/dd/yyyy in line:" . $lineaFile;
                                        $validateFile = false;
                                        break;
                                    }

                                    $contPhone = 0;

                                    if (isset($phoneHome)) {
                                        $entityPhones = $phoneTable->newEntity();
                                        $entityPhones->phone_name = 'Home';
                                        $entityPhones->phone_number = $phoneHome;
                                        array_push($arrayPhone, $entityPhones);
                                        $contPhone++;
                                    }

                                    if (isset($phoneOffice)) {
                                        $entityPhones = $phoneTable->newEntity();
                                        $entityPhones->phone_name = 'Office';
                                        $entityPhones->phone_number = $phoneOffice;
                                        array_push($arrayPhone, $entityPhones);
                                        $contPhone++;
                                    }

                                    if (isset($phoneOther)) {
                                        $entityPhones = $phoneTable->newEntity();
                                        $entityPhones->phone_name = 'Other';
                                        $entityPhones->phone_number = $phoneOther;
                                        array_push($arrayPhone, $entityPhones);
                                        $contPhone++;
                                    }

                                    if ($contPhone == 0) {
                                        $messageError['code'] = 6;
                                        $messageError['message'] = "You must add at least one phone number to the user in line:" . $lineaFile;
                                        $validateFile = false;
                                        break;
                                    }

                                    $entityUser->arrayPhones = $arrayPhone;
                                    $entityUser->status_id = 1;
                                    $entityUser->user_photo = DEFAULT_URL_PHOTO_USER;


                                    //La direccion del usuario es fija ya que por el momento no se esta tomando en cuenta
                                    //pero esta implementada.
                                    $entityAddress->address = '6000 Glades Rd, Boca Raton, FL 33431, United States';
                                    $entityAddress->latitude = '26.3645341';
                                    $entityAddress->longitude = '-80.1329333';


                                    $entityUser->email = $emailUser;

                                    //guardando los objetos en listas
                                    array_push($arrayObjectUsers, $entityUser);
                                } else {
                                    $messageError['code'] = 80;

                                    if (!isset($documentId)) {
                                        $messageError['message'] = "Document ID field is required in line: " . $lineaFile;
                                    } else if (!isset($firstName)) {
                                        $messageError['message'] = "FirstName field is required in line: " . $lineaFile;
                                    } else if (!isset($lastName)) {
                                        $messageError['message'] = "FirstName field is required in line: " . $lineaFile;
                                    } else if (!isset($birthdate)) {
                                        $messageError['message'] = "Birthdate field is required in line: " . $lineaFile;
                                    } else if (!isset($businessNumber)) {
                                        $messageError['message'] = "Business Number field is required in line: " . $lineaFile;
                                    } else if (!isset($typeUser)) {
                                        $messageError['message'] = "Type User Number field is required in line: " . $lineaFile;
                                    }

                                    $validateFile = false;

                                    break;
                                }
                            }
                            //si el el archivo tiene todas las validaciones exitosas procedo a guardar
                            if ($validateFile) {

                                $validateCreateUser = true;

                                foreach ($arrayObjectUsers as $users) {

                                    if($users->user_type_id == TYPE_USER_STUDENT){

                                        $existDocumentId = $this->findByDocumentIdUser($users->document_id);

                                        if(isset($existDocumentId)){
                                            Log::info("El Document ID: ".$users->document_id." existe no sera registrado el usuario en sistema linea: ".$lineaFile);
                                        }else{
                                            $validateCreateUser = $this->createUser($usersTable, $users, $phoneTable, $arrayPhone, $addressTable, $entityAddress,$userAccessTable);
                                            if(!$validateCreateUser){
                                                Log::info("Error creando estudiante");
                                                break;
                                            }
                                        }
                                    }
                                    else if($users->user_type_id == TYPE_USER_STAKEHOLDER){
                                        $validateCreateUser = $this->createStakeHolder($usersTable, $users, $phoneTable, $arrayPhone, $addressTable, $entityAddress,$userAccessTable);
                                        if(!$validateCreateUser){
                                            Log::info("Error creando usuario stakeholder");
                                            break;
                                        }
                                    }
                                    else{
                                        //otro tipo de usuario
                                        $validateCreateUser = $this->createUser($usersTable, $users, $phoneTable, $arrayPhone, $addressTable, $entityAddress,$userAccessTable);
                                        if(!$validateCreateUser){break;}
                                    }
                                }

                                if($validateCreateUser){
                                    $response = parent::setSuccessfulResponse($response);
                                }else{
                                    $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                                    $response['ReaxiumResponse']['message'] ='Bulk Business no found,Please contact with the api administrator.';
                                }


                            } else {
                                Log::info($messageError['message']);
                                $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                                $response['ReaxiumResponse']['message'] = $messageError['message'];
                            }

                        } else {
                            $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                            $response['ReaxiumResponse']['message'] = 'File not found for processing';
                        }
                    } else {
                        $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['ReaxiumResponse']['message'] = 'File not found for processing';
                    }

                } else {
                    $response = parent::setInvalidJsonMessage($response);
                }
            } catch (\Exception $e) {

                Log::info("Error getting the data of file .csv " . $e->getMessage());

                if ($e->getCode() == 90) {
                    $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                    $response['ReaxiumResponse']['message'] = $e->getMessage();
                } else if ($e->getCode() == 91) {
                    $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                    $response['ReaxiumResponse']['message'] = $e->getMessage();
                } else if ($e->getCode() == 93) {
                    $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                    $response['ReaxiumResponse']['message'] = $e->getMessage();
                } else if ($e->getCode() == 94) {
                    $response['ReaxiumResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                    $response['ReaxiumResponse']['message'] = $e->getMessage();
                } else {
                    $response = parent::setInternalServiceError($response);
                }

            }

        } else {
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }



}