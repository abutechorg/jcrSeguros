<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 28/8/2016
 * Time: 12:16
 */

namespace App\Controller;

use Cake\Log\Log;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use App\Util\ReaxiumApiMessages;
use App\Util\ReaxiumUtil;



class SystemListController extends JcrAPIController{


    /**
     * @api {post} /SystemList/getMenuJCR Obtener menu para la aplicacion administrativa
     * @apiName getMenuJCR
     * @apiGroup System
     *
     * @apiParamExample {json} Request-Example:
     *
     *  {
     *      "JcrParameters":{
     *              "SystemMenu":{
     *                      "type_user":4
     *                          }
     *                        }
     *                  }
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
    public function getMenuJCR(){

        Log::info("Obtener menu de la aplicacion");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if(parent::validJcrJsonHeader($jsonObject)){

            try{
                $tipo_usuario = !isset($jsonObject['JcrParameters']['SystemMenu']['type_user']) ? null : $jsonObject['JcrParameters']['SystemMenu']['type_user'];

                 if(isset($tipo_usuario)){

                     $menuOptionTable = TableRegistry::get('MenuAplicacion');
                     $menuOptionFound = $menuOptionTable->find()->contain(array('SubMenuAplicacion'))->order(array('name_menu desc'));

                     if($menuOptionFound->count()>0){
                         $menuOptionFound = $menuOptionFound->toArray();

                         $arrayMenuFinal = $this->getActiveMenu($tipo_usuario,$menuOptionFound);

                         Log::info($arrayMenuFinal);

                         if(!empty($arrayMenuFinal)){
                             $response = parent::setSuccessfulResponse($response);
                             $response['JcrResponse']['object'] = $arrayMenuFinal;
                         }else{
                             $response['JcrResponse']['code'] = '1';
                             $response['JcrResponse']['message'] = 'El menu no esta activo para este tipo de usuario';
                             $response['JcrResponse']['object'] = [];
                         }
                     }

                 }else{
                     $response = parent::setInvalidJsonMessage($response);
                 }

            }
            catch (\Exception $e){
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
     * @param $id_user_type
     * @param $arrayMenu
     * @return array
     */

    private function getActiveMenu($id_user_type,$arrayMenu){

        $arrayResponse = [];

        try{

            $accessOptions = TableRegistry::get('AccesoOpcionesRol');

            $accessOptionsFound = $accessOptions->findByTipoUsuarioId($id_user_type);

            if($accessOptionsFound->count() > 0){

                $accessOptionsFound =  $accessOptionsFound->toArray();

                foreach($arrayMenu as $menu){

                    foreach($accessOptionsFound as $access){

                        if($menu['menu_id'] == $access['menu_id']){

                            if($access['active_menu'] == ReaxiumApiMessages::$ACTIVE_MENU_FOR_TYPE_USER){
                                array_push($arrayResponse,$menu);
                            }
                        }
                    }
                }
            }
        }
        catch(\Exception $e){
            Log::info("Error get menu active");
            Log::info($e->getMessage());
            $arrayResponse = [];
        }


        return $arrayResponse;

    }


    /**
     * @api {post} /SystemList/accessUserLogin accesos del usuario al sitema
     * @apiName accessUserLogin
     * @apiGroup System
     *
     * @apiParamExample {json} Request-Example:
     *
     *  {
     *      "JcrParameters":{
     *              "SystemAccess":{
     *                  "user_name":"vladimir.fernandez21@gmail.com",
     *                  "pass_user":12345435
     *                  }
     *              }
     *          }
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
     *          "message": "User id number already exist in the systems",
     *          "object": []
     *          }
     *      }
     *
     */
    public function accessUserLogin(){

        Log::info("Login a la aplicacion administrativa");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if(parent::validJcrJsonHeader($jsonObject)){

            try{

                $userName = !isset($jsonObject['JcrParameters']['SystemAccess']['user_name']) ? null :  $jsonObject['JcrParameters']['SystemAccess']['user_name'];
                $passUser = !isset($jsonObject['JcrParameters']['SystemAccess']['pass_user']) ? null : $jsonObject['JcrParameters']['SystemAccess']['pass_user'];

                if(isset($userName) && isset($passUser)){

                    $userTable = TableRegistry::get("Usuarios");
                    $userFound = $userTable->find()->where(array('correo'=>$userName,'clave'=>$passUser,'tipo_usuario_id'=>4));

                    if($userFound->count() > 0){
                        $arrayMenuFinal = $userFound->toArray();
                        $response = parent::setSuccessfulResponse($response);
                        $response['JcrResponse']['object'] = $arrayMenuFinal;

                    }else{
                        $response['JcrResponse']['code'] = '1';
                        $response['JcrResponse']['message'] = 'UserName o Password Invalidos';
                        $response['JcrResponse']['object'] = [];
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
        }
        else{
            $response = parent::setInvalidJsonMessage($response);
        }

        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }

}