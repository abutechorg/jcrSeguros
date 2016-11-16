<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 15/11/2016
 * Time: 03:49
 */

namespace App\Controller;

use App\Util\ReaxiumApiMessages;
use Cake\Log\Log;

define('PATH_DIRECTORY', 'C:/xampp/htdocs/JcrReports');
define('ENVIRONMENT', 'http://localhost:8080/JcrReports');
define('TEMPLATE_FORMATO_CAMBIO_INTERMEDIARIO','formato_cambio_intermediario');
define('NOMBRE_FORMATO_CAMBIO_INTERMEDIARIO','formato_cambio_intermediario_');
define('TEMPLATE_FORMATO_DERECHOS_POLIZA_VEHICULO','formato_derechos_poliza_vehiculos');
define('NOMBRE_FORMATO_DERECHOS_POLIZA_VEHICULO','formato_derechos_poliza_vehiculos_');
define('TEMPLATE_FORMATO_ANULACION_POLIZA','formato_anulacion');
define('NOMBRE_FORMATO_ANULACION_POLIZA','formato_anulacion_');

class FormatoController extends JcrAPIController{


    public function formatoExport(){

        Log::info("Crear Formatos Pdf");
        parent::setResultAsAJson();
        $response = parent::getDefaultJcrMessage();
        $jsonObject = parent::getJsonReceived();
        Log::info('Object received: ' . json_encode($jsonObject));


        if(parent::validJcrJsonHeader($jsonObject)){

            try{

            $tipo_formato = !isset($jsonObject['JcrParameters']['Formats']['type_report']) ? null : $jsonObject['JcrParameters']['Formats']['type_report'];

            if(isset($tipo_formato)){

                $arrayDataFormatos = array();

                switch($tipo_formato){
                    case 1:
                        //Formato Cambio Intermediario
                        $fecha_formato = !isset($jsonObject['JcrParameters']['Formats']['date_format']) ? null :$jsonObject['JcrParameters']['Formats']['date_format'];
                        $compania_nombre = !isset($jsonObject['JcrParameters']['Formats']['company_name']) ? null :$jsonObject['JcrParameters']['Formats']['company_name'];
                        $nombre_cliente = !isset($jsonObject['JcrParameters']['Formats']['client_name']) ? null :$jsonObject['JcrParameters']['Formats']['client_name'];
                        $ci = !isset($jsonObject['JcrParameters']['Formats']['ci']) ? null :$jsonObject['JcrParameters']['Formats']['ci'];
                        $polizas = !isset($jsonObject['JcrParameters']['Formats']['polizas']) ? null :$jsonObject['JcrParameters']['Formats']['polizas'];
                        $agente = !isset($jsonObject['JcrParameters']['Formats']['agent']) ? null :$jsonObject['JcrParameters']['Formats']['agent'];
                        $cod_agente = !isset($jsonObject['JcrParameters']['Formats']['cod_agent']) ? null :$jsonObject['JcrParameters']['Formats']['cod_agent'];
                        $telefono_cliente = !isset($jsonObject['JcrParameters']['Formats']['client_phone']) ? null :$jsonObject['JcrParameters']['Formats']['client_phone'];


                        if(isset($fecha_formato) && isset($compania_nombre) && isset($nombre_cliente) && isset($ci) && isset($polizas)
                        && isset($agente) && isset($cod_agente) && isset($telefono_cliente)){

                            array_push($arrayDataFormatos,array('fecha_formato'=>$fecha_formato,
                                'compania_nombre'=>$compania_nombre,
                                'nombre_cliente'=>$nombre_cliente,
                                'ci'=>$ci,
                                'polizas'=>$polizas,
                                'agente'=>$agente,
                                'cod_agente'=>$cod_agente,
                                'telefono_cliente'=>$telefono_cliente));

                           $url =  $this->buildPdfFormatoCambioInter($arrayDataFormatos);

                            if(isset($url)){

                                $response['JcrResponse']['url_format'] = $url;
                                $response = parent::setSuccessfulResponse($response);
                            }
                            else{
                                $response['JcrResponse']['code'] = '1';
                                $response['JcrResponse']['message'] = 'Error en la creacion del pdf Documento Formato Cambio Intermediario';
                            }

                        }
                        else{
                            $response = parent::setInvalidJsonMessage($response);
                        }

                        break;
                    case 2:
                        //Formato cesión de derechos póliza vehículoss

                        //Formato Cambio Intermediario
                        $fecha_formato = !isset($jsonObject['JcrParameters']['Formats']['date_format']) ? null :$jsonObject['JcrParameters']['Formats']['date_format'];
                        $compania_nombre = !isset($jsonObject['JcrParameters']['Formats']['company_name']) ? null :$jsonObject['JcrParameters']['Formats']['company_name'];
                        $nombre_asegurado = !isset($jsonObject['JcrParameters']['Formats']['aseg_name']) ? null :$jsonObject['JcrParameters']['Formats']['aseg_name'];
                        $ci_asegurado = !isset($jsonObject['JcrParameters']['Formats']['ci_aseg']) ? null :$jsonObject['JcrParameters']['Formats']['ci_aseg'];
                        $polizas = !isset($jsonObject['JcrParameters']['Formats']['polizas']) ? null : $jsonObject['JcrParameters']['Formats']['polizas'];
                        $nombre_cliente = !isset($jsonObject['JcrParameters']['Formats']['client_name']) ? null : $jsonObject['JcrParameters']['Formats']['client_name'];
                        $ci_cliente = !isset($jsonObject['JcrParameters']['Formats']['ci_client']) ? null : $jsonObject['JcrParameters']['Formats']['ci_client'];
                        $vehiculo_marca = !isset($jsonObject['JcrParameters']['Formats']['vehicle_brand']) ? null : $jsonObject['JcrParameters']['Formats']['vehicle_brand'];
                        $vehiculo_modelo = !isset($jsonObject['JcrParameters']['Formats']['vehicle_model']) ? null : $jsonObject['JcrParameters']['Formats']['vehicle_model'];
                        $vehiculo_ano = !isset($jsonObject['JcrParameters']['Formats']['vehicle_year']) ? null : $jsonObject['JcrParameters']['Formats']['vehicle_year'];
                        $vehiculo_placa = !isset($jsonObject['JcrParameters']['Formats']['vehicle_license_plate']) ? null : $jsonObject['JcrParameters']['Formats']['vehicle_license_plate'];
                        $telefono_cliente = !isset($jsonObject['JcrParameters']['Formats']['client_phone']) ? null :$jsonObject['JcrParameters']['Formats']['client_phone'];


                        if(isset($fecha_formato) && isset($compania_nombre) &&
                            isset($nombre_asegurado) && isset($ci_asegurado) && isset($polizas)
                            && isset($nombre_cliente) && isset($ci_cliente) &&
                            isset($vehiculo_marca) && isset($vehiculo_modelo) &&
                            isset($vehiculo_ano) && isset($vehiculo_placa) &&isset($telefono_cliente)){

                            array_push($arrayDataFormatos,array('fecha_formato'=>$fecha_formato,
                                'compania_nombre'=>$compania_nombre,
                                'nombre_asegurado'=>$nombre_asegurado,
                                'ci_asegurado'=>$ci_asegurado,
                                'polizas'=>$polizas,
                                'nombre_cliente'=>$nombre_cliente,
                                'ci_cliente'=>$ci_cliente,
                                'vehiculo_marca'=>$vehiculo_marca,
                                'vehiculo_modelo'=>$vehiculo_modelo,
                                'vehiculo_ano'=>$vehiculo_ano,
                                'vehiculo_placa'=>$vehiculo_placa,
                                'telefono_cliente'=>$telefono_cliente));

                            $url =  $this->buildPdfFormatoPolizaVehiculo($arrayDataFormatos);

                            if(isset($url)){

                                $response['JcrResponse']['url_format'] = $url;
                                $response = parent::setSuccessfulResponse($response);
                            }
                            else{
                                $response['JcrResponse']['code'] = '1';
                                $response['JcrResponse']['message'] = 'Error en la creacion del pdf Documento Formato Cambio Intermediario';
                            }

                        }
                        else{
                            $response = parent::setInvalidJsonMessage($response);
                        }


                        break;
                    case 3:
                        //Formato cambio de placas
                        break;
                    case 4:
                        //Formato Anulación

                        $fecha_formato = !isset($jsonObject['JcrParameters']['Formats']['date_format']) ? null :$jsonObject['JcrParameters']['Formats']['date_format'];
                        $compania_nombre = !isset($jsonObject['JcrParameters']['Formats']['company_name']) ? null :$jsonObject['JcrParameters']['Formats']['company_name'];
                        $nombre_asegurado = !isset($jsonObject['JcrParameters']['Formats']['aseg_name']) ? null :$jsonObject['JcrParameters']['Formats']['aseg_name'];
                        $ci_asegurado = !isset($jsonObject['JcrParameters']['Formats']['ci_aseg']) ? null :$jsonObject['JcrParameters']['Formats']['ci_aseg'];
                        $polizas = !isset($jsonObject['JcrParameters']['Formats']['polizas']) ? null : $jsonObject['JcrParameters']['Formats']['polizas'];
                        $telefono_cliente = !isset($jsonObject['JcrParameters']['Formats']['client_phone']) ? null :$jsonObject['JcrParameters']['Formats']['client_phone'];

                        if(isset($fecha_formato) && isset($compania_nombre) && isset($nombre_asegurado) && isset($ci_asegurado) && isset($polizas)){

                            array_push($arrayDataFormatos,array('fecha_formato'=>$fecha_formato,
                                'compania_nombre'=>$compania_nombre,
                                'polizas'=>$polizas,
                                'nombre_cliente'=>$nombre_asegurado,
                                'ci'=>$ci_asegurado,
                                'polizas'=>$polizas,
                                'telefono_cliente'=>$telefono_cliente));

                            $url =  $this->buildPdfFormatoAnulacionPoliza($arrayDataFormatos);

                            if(isset($url)){
                                $response['JcrResponse']['url_format'] = $url;
                                $response = parent::setSuccessfulResponse($response);
                            }
                            else{
                                $response['JcrResponse']['code'] = '1';
                                $response['JcrResponse']['message'] = 'Error en la creacion del pdf Documento Formato Cambio Intermediario';
                            }

                        }else{
                            $response = parent::setInvalidJsonMessage($response);
                        }

                        break;
                }



            }else{
                $response = parent::setInvalidJsonMessage($response);
            }

            }catch (\Exception $e){
                $response['JcrResponse']['code'] = ReaxiumApiMessages::$INTERNAL_SERVER_ERROR_CODE;
                $response['JcrResponse']['message'] = $e->getMessage();
            }

        }else{
            $response = parent::setInvalidJsonMessage($response);
        }


        Log::info("Responde Object: " . json_encode($response));
        $this->response->body(json_encode($response));
    }



    private function buildPdfFormatoCambioInter($listDataFormat){

        $url_reponse = null;

        try{
            Log::info("Parametros Lista: ".json_encode($listDataFormat));

            $name_format = NOMBRE_FORMATO_CAMBIO_INTERMEDIARIO . rand(10000, 99999) . '.pdf';
            $url_reponse = $this->buildPDFFormatDocument(TEMPLATE_FORMATO_CAMBIO_INTERMEDIARIO,$name_format,$listDataFormat);

        }catch (\Exception $e){
            Log::info("Error:" .$e->getMessage());
            $url_reponse = null;
        }


        return $url_reponse;
    }


    private function buildPdfFormatoPolizaVehiculo($listDataFormat){

        $url_reponse = null;

        try{
            Log::info("Parametros Lista: ".json_encode($listDataFormat));

            $name_format = NOMBRE_FORMATO_DERECHOS_POLIZA_VEHICULO . rand(10000, 99999) . '.pdf';
            $url_reponse = $this->buildPDFFormatDocument(TEMPLATE_FORMATO_DERECHOS_POLIZA_VEHICULO,$name_format,$listDataFormat);

        }catch (\Exception $e){
            Log::info("Error:" .$e->getMessage());
            $url_reponse = null;
        }


        return $url_reponse;

    }


    private function buildPdfFormatoAnulacionPoliza($listDataFormat){

        $url_reponse = null;

        try{
            Log::info("Parametros Lista: ".json_encode($listDataFormat));

            $name_format = NOMBRE_FORMATO_ANULACION_POLIZA . rand(10000, 99999) . '.pdf';
            $url_reponse = $this->buildPDFFormatDocument(TEMPLATE_FORMATO_ANULACION_POLIZA,$name_format,$listDataFormat);

        }catch (\Exception $e){
            Log::info("Error:" .$e->getMessage());
            $url_reponse = null;
        }


        return $url_reponse;

    }

    /**
     * @param $template
     * @param $name_file
     * @param $data
     * @return string
     * @throws \Exception
     */
    private function buildPDFFormatDocument($template, $name_file, $data)
    {

        try {

            $path_pdf_create = PATH_DIRECTORY . '/' . $name_file;

            $CakePdf = new \CakePdf\Pdf\CakePdf();

            $CakePdf->orientation('portrait');

            $CakePdf->template($template);

            $CakePdf->viewVars(array('data' => $data));
            // Get the PDF string returned
            $CakePdf->output();
            // Or write it to file directly
            $CakePdf->write($path_pdf_create);

            $url_pdf = ENVIRONMENT . '/' . $name_file;

        } catch (\Exception $e) {
            Log::info('Error creando pdf' . $e);
            throw $e;
        }

        return $url_pdf;
    }

}
