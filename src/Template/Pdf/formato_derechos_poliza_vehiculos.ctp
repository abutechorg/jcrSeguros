<div class="row">
    <main class="infoUser" style="font-size: 200%;">
        <div class="col-lg-12">
            <div class="content">
                <div style="margin-left: 2em;">
                    <table>
                        <tr>
                            <td><strong>Caracas</strong>,&nbsp;</td>
                            <td><?=$data[0]['fecha_formato']?></td>
                        </tr>
                    </table>
                    <br>
                    <table>
                        <tr>
                            <td><strong>Sres.&nbsp;</strong></td>
                            <td><?=$data[0]['compania_nombre']?></td>
                        </tr>
                    </table>
                    <br>
                    Presente.
                </div>

                <br>
                <br>

                <p style="text-align:justify;line-height:50px;margin-right: 5em;margin-left: 2em;">
                    Por medio de la presente, yo,<?=$data[0]['nombre_asegurado']?>, portador(a) de la CI <?=$data[0]['ci_asegurado']?>,
                    y asegurado(a) ante esa compañía bajo la(s) pólizas, <?=$data[0]['polizas']?> me dirijo
                    muy respetuosamente a ustedes en la oportunidad de hacer de su conocimiento que he
                    decidido ceder la misma al ciudadano(a) <?=$data[0]['nombre_cliente']?>, CI <?= $data[0]['ci_cliente']?> quien es el nuevo&nbsp;
                    propietario del vehículo marca <?=$data[0]['vehiculo_marca']?>  modelo <?=$data[0]['vehiculo_modelo']?> año <?=$data[0]['vehiculo_ano']?>
                    placa <?=$data[0]['vehiculo_placa']?>
                </p>
                <br>

                <p style="text-align:justify;line-height:50px;margin-right: 5em;margin-left: 2em;">
                    Sin mas a que hacer referencia y agradeciendo, de antemano, se sirvan de realizar las modificaciones
                    pertinentes a la brevedad posible, quedo de ustedes</p>

                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>

                <div align="center">
                    __________________________________
                    <br>
                    <br>
                    <table style="text-align:center;margin-left:70px;">
                        <tr>
                            <td>CI:&nbsp;</td>
                            <td><?=$data[0]['ci_asegurado']?></td>
                        </tr>
                    </table>
                    <br>
                    <table>
                        <tr>
                            <td>N&uacute;mero de Tel&eacute;fono&nbsp;</td>
                            <td><?=$data[0]['telefono_cliente']?></td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>
