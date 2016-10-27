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

<h1>Reporte de Aumento SA</h1>
<main class="infoUser">

    <table class="accessTable">
        <tr>
            <th>Numero de Poliza</th>
            <th>Cliente</th>
            <th>CI</th>
            <th>Placa</th>
            <th>Ramo</th>
            <th>Emision</th>
            <th>Agente</th>
            <th>Aseguradora</th>
            <th>Prima</th>
            <th>SA</th>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($data['polizas'] as $poliza): ?>

            <!--cuando el ramo es automovil flota-->
            <?php if($poliza['ramo']['ramo_id'] == 3 || $poliza['ramo']['ramo_id'] == 4): ?>
                <!--ojo aqui hay que ajustar suma asegurada-->

                    <tr>
                        <td class="font"><?= $poliza['numero_poliza']; ?></td>
                        <td class="font"><?= $poliza['asegurado']['nombre_cliente']; ?>&nbsp;<?= $poliza['asegurado']['apellido_cliente']; ?></td>
                        <td class="font"><?= $poliza['asegurado']['documento_id_cliente']; ?></td>
                        <td class="font"><?= $poliza['vehiculos'][0]['vehiculo_placa']?></td>
                        <td class="font"><?= $poliza['ramo']['ramo_nombre']; ?></td>
                        <td class="font"><?= $poliza['fecha_emision']; ?></td>
                        <td class="font"><?= $poliza['agente']; ?></td>
                        <td class="font"><?= $poliza['aseguradora']; ?></td>
                        <td class="font"><?php echo $this->Number->format($poliza['prima_total'],array('before'=>'Bs','locale'=>'es_VE')) ?></td>
                        <td class="font"><?php echo $this->Number->format($poliza['suma_asegurada'][0]['descripciones_cobertura'][0]['monto'],array('before'=>'Bs','locale'=>'es_VE'))?></td>
                    </tr>


                <!--cuando el ramo es hospitalizacion-->
            <?php elseif($poliza['ramo']['ramo_id'] == 1 || $poliza['ramo']['ramo_id'] == 2 ) : ?>
                <tr>
                    <td class="font"><?= $poliza['numero_poliza']; ?></td>
                    <td class="font"><?= $poliza['asegurado']['nombre_cliente']; ?>&nbsp;<?= $poliza['asegurado']['apellido_cliente']; ?></td>
                    <td class="font"><?= $poliza['asegurado']['documento_id_cliente']; ?></td>
                    <td class="font">N/A</td>
                    <td class="font"><?= $poliza['ramo']['ramo_nombre']; ?></td>
                    <td class="font"><?= $poliza['fecha_emision']; ?></td>
                    <td class="font"><?= $poliza['agente']; ?></td>
                    <td class="font"><?= $poliza['aseguradora']; ?></td>
                    <td class="font"><?php echo $this->Number->format($poliza['prima_total'],array('before'=>'Bs','locale'=>'es_VE'))?></td>

                    <?php foreach($poliza['suma_asegurada'] as $cobertura): ?>

                        <?php if($cobertura['descripciones_cobertura'][0]['monto'] < intval($data['monto_filtro'])): ?>
                            <td class="font"><?php echo $this->Number->format($cobertura['descripciones_cobertura'][0]['monto'],array('before'=>'Bs','locale'=>'es_VE')) ?></td>
                            <?php break;?>
                        <?php endif;?>

                    <?php endforeach?>

                </tr>

            <?php else: ?>
                <tr>
                    <td class="font"><?= $poliza['numero_poliza']; ?></td>
                    <td class="font"><?= $poliza['asegurado']['nombre_cliente']; ?>&nbsp;<?= $poliza['asegurado']['apellido_cliente']; ?></td>
                    <td class="font"><?= $poliza['asegurado']['documento_id_cliente']; ?></td>
                    <td class="font">N/A</td>
                    <td class="font"><?= $poliza['ramo']['ramo_nombre']; ?></td>
                    <td class="font"><?= $poliza['fecha_emision']; ?></td>
                    <td class="font"><?= $poliza['agente']; ?></td>
                    <td class="font"><?= $poliza['aseguradora']; ?></td>
                    <td class="font"><?php echo $this->Number->format($poliza['prima_total'],array('before'=>'Bs','locale'=>'es_VE'))?></td>
                    <td class="font"><?php echo $this->Number->format($poliza['suma_asegurada'][0]['descripciones_cobertura'][0]['monto'],array('before'=>'Bs','locale'=>'es_VE'))?></td>
                </tr>
            <?php endif?>
        <?php endforeach?>
        </tbody>
    </table>
</main>