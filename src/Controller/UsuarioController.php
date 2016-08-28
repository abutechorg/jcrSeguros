<?php
/**
 * Created by PhpStorm.
 * User: Eduardo Luttinger
 * Date: 22/03/2016
 * Time: 01:43 AM tets
 */

namespace App\Controller;

use Cake\Log\Log;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use App\Util\ReaxiumApiMessages;
use App\Util\ReaxiumUtil;



class UsuarioController extends JcrAPIController{


    /**
     * @api {post} /Usuario/crearUsuario Create A New User in the system
     * @apiName crearUsuario
     * @apiGroup Usuarios
     *
     * @apiParamExample {json} Request-Example:
     *
     * {
     *  "JcrParameters":{
     *      "Users":{
     *          "nombre":"Jose",
     *          "apellido":"Perez",
     *          "documento_id":"13456619",
     *          "fecha_nacimiento":"21/03/1984",
     *          "correo":"jPerez21@gmail.com",
     *          "direccion":"Los Teques",
     *          "tipo_usuario":"4",
     *          "clave":"12345"
     *          }
     *      }
     *   }
     *
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *      "ReaxiumResponse": {
     *          "code": 0,
     *          "message": "SAVED SUCCESSFUL",
     *          "object": {
     *              "user_id":"1",
     *              "document_id": "19055085",
     *              "first_name": "Jhon",
     *              "second_name": "Andrew",
     *              "first_last_name": "Doe",
     *              "second_last_name":"Smith"
     *              "status_id":"1"
     *              "status":{
     *                  "status_id":"1",
     *                  "status_name":"Active"
     *                  }
     *              }
     *          }
     *      }
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
    public function crearUsuario()
    {
        Log::info("Crear o actualiza un usuario en sistema");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if (parent::validJcrJsonHeader($jsonObject)) {
            try {
                if (isset($jsonObject['JcrParameters']["Users"])) {
                    $result = $this->createAUser($jsonObject['JcrParameters']);

                    if ($result) {
                        $response = parent::setSuccessfulSave($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudoo crear el usuario en sistema';
                    }
                } else {
                    $response = parent::seInvalidParametersMessage($response);
                }
            } catch (\Exception $e) {
                Log::info("Error Saving the User " . $e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }
        } else {
            $response = parent::setInvalidJsonMessage($response);
        }
        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     *
     * Register a new user in the system
     *
     * @param $userJSON
     * @return created user
     */
    private function createAUser($userJSON)
    {

        $result = null;

        try {

            $userTable = TableRegistry::get("Usuarios");
            $userEntity = $userTable->newEntity();

            $users =$userTable->patchEntity($userEntity, $userJSON['Users']);

            Log::info($users);
            $result = $userTable->save($users);

        } catch (\Exception $e) {
            Log::info("Error creando usuario");
            Log::info($e->getMessage());
        }

        return $result;
    }


    /**
     * @api {post} /Usuario/borrarUsuario Create A New User in the system
     * @apiName borrarUsuario
     * @apiGroup Usuario
     *
     * @apiParamExample {json} Request-Example:
     *
     *  {
     *      "JcrParameters":{
     *          "User":{
     *              "user_id":2
     *              }
     *             }
     *          }
     *
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *      "ReaxiumResponse": {
     *          "code": 0,
     *          "message": "SAVED SUCCESSFUL",
     *          "object": [{
     *              "user_id":"1",
     *              "document_id": "19055085",
     *              "first_name": "Jhon",
     *              "second_name": "Andrew",
     *              "first_last_name": "Doe",
     *              "second_last_name":"Smith"
     *              "status_id":"1"
     *              "status":{
     *                  "status_id":"1",
     *                  "status_name":"Active"
     *                  }
     *              }]
     *          }
     *      }
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
    public function borrarUsuario(){

        Log::info("Borrar usuario");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject)){

            $user_id = !isset($jsonObject['JcrParameters']['User']['user_id']) ? null : $jsonObject['JcrParameters']['User']['user_id'];

            if(isset($user_id)){

                try{

                    $userTable = TableRegistry::get("Usuarios");
                    $result = $userTable->deleteAll(array('usuario_id'=>$user_id));

                    if ($result) {
                        $response = parent::setSuccessfulDelete($response);
                        $response['JcrResponse']['object'] = $result;
                    }
                    else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                        $response['JcrResponse']['message'] = 'No se pudo borrar el usuario en sistema';
                    }


                }catch(\Exception $e){
                    Log::info("Error borrando usuario del sistema");
                    Log::info($e->getMessage());
                    $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                    $response['JcrResponse']['message'] = 'Error del sistema';
                }
            }
            else{
                $response = parent::setInvalidJsonMessage($response);
            }

        }else{
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * @api {post} /Usuario/allUsersInfoWithPagination Create A New User in the system
     * @apiName allUsersInfoWithPagination
     * @apiGroup Usuarios
     *
     * @apiParamExample {json} Request-Example:
     *
     * {
     *      "JcrParameters":{
     *                  "page":"1",
     *                  "sortedBy":"nombre",
     *                  "sortDir":"desc",
     *                  "filter":"",
     *                  "limit":"10"
     *                  }
     *      }
     *
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *      "JcrResponse": {
     *                  "code": 0,
     *                  "message": "SUCCESSFUL REQUEST",
     *                  "object": [
     *                              {
     *                              "usuario_id": 1,
     *                              "nombre": "Yajaira",
     *                              "apellido": "Vera",
     *                              "documento_id": "1573934",
     *                              "fecha_nacimiento": "21/03/1984",
     *                              "correo": "yaja.vera21@gmail.com",
     *                              "direccion": "Los Teques",
     *                              "tipo_usuario": 2,
     *                              "clave": ""
     *                              }
     *                          ],
     *                          "totalRecords": 2,
     *                          "totalPages": 1
     *                          }
     *                      }
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
    public function allUsersInfoWithPagination(){

        Log::info("Consulta todos los usuarios con paginacion");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject)){

            try{

                if(isset($jsonObject['JcrParameters']['Users']['page'])){

                    $page = $jsonObject['JcrParameters']['Users']["page"];
                    $sortedBy = !isset($jsonObject['JcrParameters']['Users']["sortedBy"]) ? 'nombre' : $jsonObject['JcrParameters']['Users']["sortedBy"];
                    $sortDir = !isset($jsonObject['JcrParameters']['Users']["sortDir"]) ? 'desc' : $jsonObject['JcrParameters']['Users']["sortDir"];
                    $filter = !isset($jsonObject['JcrParameters']['Users']["filter"]) ? '' : $jsonObject['JcrParameters']['Users']["filter"];
                    $limit = !isset($jsonObject['JcrParameters']['Users']["limit"]) ? 10 : $jsonObject['JcrParameters']['Users']["limit"];

                    $userTable = TableRegistry::get('Usuarios');

                    if(trim($filter) != ''){
                        $whereCondition = array(array('OR' => array(
                            array('nombre LIKE' => '%' . $filter . '%'),
                            array('apellido LIKE' => '%' . $filter . '%'),
                            array('documento_id LIKE' => '%' . $filter . '%'))));


                        //agregar los contain cuando sea necesario
                        $userFound = $userTable->find()
                            ->where($whereCondition)
                            ->order(array($sortedBy . ' ' . $sortDir));
                    }
                    else{
                        //agregar los contain cuando sea necesario
                        $userFound = $userTable->find()->order(array($sortedBy . ' ' . $sortDir));
                    }

                    $count = $userFound->count();
                    $this->paginate = array('limit' => $limit, 'page' => $page);
                    $userFound = $this->paginate($userFound);

                    if ($userFound->count() > 0) {
                        $maxPages = floor((($count - 1) / $limit) + 1);
                        $userFound = $userFound->toArray();
                        $response['JcrResponse']['totalRecords'] = $count;
                        $response['JcrResponse']['totalPages'] = $maxPages;
                        $response['JcrResponse']['object'] = $userFound;
                        $response = parent::setSuccessfulResponse($response);
                    }
                    else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No Users found';
                    }

                }
                else{
                    $response = parent::setInvalidJsonMessage($response);
                }
            }
            catch (\Exception $e){
                Log::info("Error borrando usuario del sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }
        }else{
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }



    /**
     * @api {post} /Usuario/userInfoById Create A New User in the system
     * @apiName userInfoById
     * @apiGroup Usuarios
     *
     * @apiParamExample {json} Request-Example:
     *
     *    {
     *     "JcrParameters":{
     *                  "User":{
     *                      "user_id":1
     *                      }
     *                  }
     *              }
     *
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *    {
     *      "JcrResponse": {
     *                  "code": 0,
     *                  "message": "SUCCESSFUL REQUEST",
     *                  "object": [
     *                          {
     *                          "usuario_id": 1,
     *                          "nombre": "Yajaira",
     *                          "apellido": "Vera",
     *                          "documento_id": "1573934",
     *                          "fecha_nacimiento": "21/03/1984",
     *                          "correo": "yaja.vera21@gmail.com",
     *                          "direccion": "Los Teques",
     *                          "tipo_usuario": 2,
     *                           "clave": ""
     *                          }
     *                       ]
     *                     }
     *                  }
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
    public function userInfoById(){

        Log::info("Informacion usuario por ID");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject)){

            $user_id = !isset($jsonObject['JcrParameters']['User']['user_id']) ? null : $jsonObject['JcrParameters']['User']['user_id'];

            try{

                if(isset($user_id)){
                    $userTable = TableRegistry::get("Usuarios");
                    //find Magico
                    $userFound = $userTable->findByUsuarioId($user_id);

                    if($userFound->count() > 0){
                        $userFound = $userFound->toArray();

                    }else{
                        $userFound = null;
                    }

                    $response['JcrResponse']['object'] = $userFound;
                    $response = parent::setSuccessfulResponse($response);

                }else{
                    $response = parent::setInvalidJsonMessage($response);
                }

            }catch(\Exception $e){
                Log::info("Error borrando usuario del sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }
        }
        else{
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }


    /**
     * @api {post} /Usuario/userFilter Create A New User in the system
     * @apiName userFilter
     * @apiGroup Usuarios
     *
     * @apiParamExample {json} Request-Example:
     *
     *  {
     *      "JcrParameters":{
     *                  "User":{
     *                      "filter":"yaja"
     *                  }
     *                }
     *      }
     *
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "JcrResponse": {
     *                  "code": 0,
     *                  "message": "SUCCESSFUL REQUEST",
     *                  "object": [
     *                              {
     *                              "usuario_id": 1,
     *                              "nombre": "Yajaira",
     *                              "apellido": "Vera",
     *                              "documento_id": "1573934",
     *                              "fecha_nacimiento": "21/03/1984",
     *                              "correo": "yaja.vera21@gmail.com",
     *                              "direccion": "Los Teques",
     *                              "tipo_usuario": 2,
     *                              "clave": ""
     *                              }
     *                            ]
     *                            }
     *                      }
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
    public function userFilter(){

        Log::info("Informacion usuario por filtro");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));

        if(parent::validJcrJsonHeader($jsonObject)){

            try{

                if(isset($jsonObject['JcrParameters']['User']['filter'])){

                    $filter = $jsonObject['JcrParameters']['User']['filter'];

                    $userTable = TableRegistry::get('Usuarios');

                    $whereCondition = array(array('OR' => array(
                        array('nombre LIKE' => '%' . $filter . '%'),
                        array('apellido LIKE' => '%' . $filter . '%'),
                        array('documento_id LIKE' => '%' . $filter . '%')
                    )));

                    //agregar el contain cuando sea necesario
                    $userFound = $userTable->find()
                        ->where($whereCondition)
                        ->order(array('nombre', 'apellido'));


                    if ($userFound->count() > 0) {
                        $userFound = $userFound->toArray();
                        $response['JcrResponse']['object'] = $userFound;
                        $response = parent::setSuccessfulResponse($response);
                    } else {
                        $response['JcrResponse']['code'] = ReaxiumApiMessages::$NOT_FOUND_CODE;
                        $response['JcrResponse']['message'] = 'No Users found';
                    }

                }
                else{
                    $response = parent::setInvalidJsonMessage($response);
                }

            }catch (\Exception $e){
                Log::info("Error borrando usuario del sistema");
                Log::info($e->getMessage());
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$CANNOT_SAVE;
                $response['JcrResponse']['message'] = 'Error del sistema';
            }

        }else{
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }

}
