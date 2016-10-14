<div class="row">
    <div class="headerByUser">
        <div>
            <?php echo $this->Html->image("logo2.jpeg", array('id' => 'logoAccess', 'fullBase' => true, 'style' => 'width:180px;
                       height:100px;')) ?>
        </div>
    </div>
</div>

<div class="container infoUser">
    <h3 class="clearfix">
        <div id="DateStar">
            <span>FECHA INICIAL</span>
            <br/><?= $data['start_date']?>
        </div>
        <div id="DateEnd">
            <span>FECHA FINAL</span>
            <br/><?= $data['end_date']?>
        </div>
    </h3>
    <br>
</div>

<h1>Reporte de Renovaciones</h1>
<main class="infoUser">

    <table class="accessTable">
        <tr>
            <th>Numero de Poliza</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>CI</th>
            <th>Placa</th>
            <th>Ramo</th>
            <th>Vigencia</th>
            <th>Agente</th>
            <th>Prima</th>
            <th>SA</th>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($data['polizas'] as $poliza): ?>

            <!--cuando el ramo es automovil flota-->
            <?php if($poliza['ramo']['ramo_id'] == 3): ?>
                <!--ojo aqui hay que ajustar suma asegurada-->

                <?php foreach($poliza['vehiculos'] as $vehiculo): ?>
                    <tr>
                        <td class="font"><?= $poliza['numero_poliza']; ?></td>
                        <td class="font"><?= $poliza['asegurado']['nombre_cliente']; ?></td>
                        <td class="font"><?= $poliza['asegurado']['apellido_cliente']; ?></td>
                        <td class="font"><?= $poliza['asegurado']['documento_id_cliente']; ?></td>
                        <td class="font"><?= $vehiculo['vehiculo_placa']?></td>
                        <td class="font"><?= $poliza['ramo']['ramo_nombre']; ?></td>
                        <td class="font"><?= $poliza['fecha_vencimiento']; ?></td>
                        <td class="font"><?= $poliza['agente']; ?></td>
                        <td class="font"><?= $poliza['prima_total']; ?></td>
                        <td class="font"><?= $poliza['suma_asegurada'][0]['descripciones_cobertura'][0]['monto']; ?></td>
                    </tr>

                <?php endforeach?>
                <!--cuando el ramo es automovil individual-->
            <?php elseif($poliza['ramo']['ramo_id'] == 4) : ?>
                    <tr>
                        <td class="font"><?= $poliza['numero_poliza']; ?></td>
                        <td class="font"><?= $poliza['asegurado']['nombre_cliente']; ?></td>
                        <td class="font"><?= $poliza['asegurado']['apellido_cliente']; ?></td>
                        <td class="font"><?= $poliza['asegurado']['documento_id_cliente']; ?></td>
                        <td class="font"><?=$poliza['vehiculos'][0]['vehiculo_placa']?></td>
                        <td class="font"><?= $poliza['ramo']['ramo_nombre']; ?></td>
                        <td class="font"><?= $poliza['fecha_vencimiento']; ?></td>
                        <td class="font"><?= $poliza['agente']; ?></td>
                        <td class="font"><?= $poliza['prima_total']; ?></td>
                        <td class="font"><?= $poliza['suma_asegurada'][0]['descripciones_cobertura'][0]['monto']; ?></td>
                    </tr>

                <?php else: ?>
                    <tr>
                        <td class="font"><?= $poliza['numero_poliza']; ?></td>
                        <td class="font"><?= $poliza['asegurado']['nombre_cliente']; ?></td>
                        <td class="font"><?= $poliza['asegurado']['apellido_cliente']; ?></td>
                        <td class="font"><?= $poliza['asegurado']['documento_id_cliente']; ?></td>
                        <td class="font">N/A</td>
                        <td class="font"><?= $poliza['ramo']['ramo_nombre']; ?></td>
                        <td class="font"><?= $poliza['fecha_vencimiento']; ?></td>
                        <td class="font"><?= $poliza['agente']; ?></td>
                        <td class="font"><?= $poliza['prima_total']; ?></td>
                        <td class="font"><?= $poliza['suma_asegurada'][0]['descripciones_cobertura'][0]['monto']; ?></td>
                    </tr>
                <?php endif?>
        <?php endforeach?>
        </tbody>
    </table>
</main>