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
                    Por medio de la presente, yo,<?=$data[0]['nombre_cliente']?>, portador(a) de la CI <?=$data[0]['ci']?>,
                    y asegurado(a) ante esa compañía bajo la(s) pólizas, <?=$data[0]['polizas']?> me dirijo
                    muy respetuosamente a ustedes en la oportunidad de hacer de su conocimiento que he
                    decidido anular la(s) póliza(s) antes referida(s) que mantengo con ustedes,
                    en aras de que sea realizada a la brevedad,en caso de aplicar, la devolución de la prima correspondiente.
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
                            <td><?=$data[0]['ci']?></td>
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
