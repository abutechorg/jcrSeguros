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

<h1>Reporte de Siniestros</h1>
<main class="infoUser">

    <table class="accessTable">
        <tr>
            <th>Numero de Poliza</th>
            <th>Numero de Siniestro</th>
            <th>Cliente</th>
            <th>CI</th>
            <th>Placa</th>
            <th>Ramo</th>
            <th>Vigencia</th>
            <th>Agente</th>
            <th>Aseguradora</th>
            <th>Prima</th>
            <th>SA</th>
            <th>Monto Siniestro</th>
            <th>Siniestralidad</th>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($data['lista_siniestralidad'] as $siniestro): ?>

            <!--cuando el ramo es automovil flota-->
            <?php if($siniestro['tipo_siniestro']== 1): ?>

                    <tr>
                        <td class="font"><?= $siniestro['numero_poliza']; ?></td>
                        <td class="font"><?= $siniestro['numero_siniestro']; ?></td>
                        <td class="font"><?= $siniestro['asegurado']['nombre_cliente']; ?>&nbsp;<?= $siniestro['asegurado']['apellido_cliente']; ?></td>
                        <td class="font"><?= $siniestro['asegurado']['documento_id_cliente']; ?></td>
                        <td class="font">N/A</td>
                        <td class="font"><?= $siniestro['ramo'][0]['ramo_nombre']; ?></td>
                        <td class="font"><?= $siniestro['fecha_vencimiento']; ?></td>
                        <td class="font"><?= $siniestro['agente']; ?></td>
                        <td class="font"><?= $siniestro['aseguradora'][0]['aseguradora_nombre']; ?></td>
                        <td class="font"><?php echo $this->Number->format($siniestro['prima_total'],array('before'=>'Bs','locale'=>'es_VE'))?></td>
                        <td class="font">

                            <?php $flag=false; ?>

                            <?php foreach($siniestro['coberturas'] as $sa) :?>
                                <?php if($sa['cobertura_id'] == 1) : $flag=true; ?>
                                    HCB: <?php echo $this->Number->format($sa['descripciones_cobertura'][0]['monto'],array('locale'=>'es_VE'))?>
                                <?php elseif($sa['cobertura_id'] == 3) : $flag=true;?>
                                    MB:  <?php echo $this->Number->format($sa['descripciones_cobertura'][0]['monto'],array('locale'=>'es_VE')) ?>
                                <?php endif?>
                            <?php endforeach?>

                            <?php if(!$flag): ?>
                                <?php echo  $this->Number->format($siniestro['coberturas'][0]['descripciones_cobertura'][0]['monto'],array('before'=>'Bs','locale'=>'es_VE')) ?>
                            <?php endif?>

                        </td>
                        <td class="font"><?php echo $this->Number->format($siniestro['monto_siniestro'],array('before'=>'Bs','locale'=>'es_VE'))?></td>
                        <td class="font"><?= $siniestro['calculo']?>%</td>
                    </tr>

                <!--cuando el ramo es automovil individual-->
            <?php elseif($siniestro['tipo_siniestro'] == 2) : ?>
                <tr>
                    <td class="font"><?= $siniestro['numero_poliza']; ?></td>
                    <td class="font"><?= $siniestro['numero_siniestro']; ?></td>
                    <td class="font"><?= $siniestro['asegurado']['nombre_cliente']; ?>&nbsp;<?= $siniestro['asegurado']['apellido_cliente']; ?></td>
                    <td class="font"><?= $siniestro['asegurado']['documento_id_cliente']; ?></td>
                    <td class="font"><?= $siniestro['vehiculo'][0]['vehiculo_placa']?></td>
                    <td class="font"><?= $siniestro['ramo'][0]['ramo_nombre']; ?></td>
                    <td class="font"><?= $siniestro['fecha_vencimiento']; ?></td>
                    <td class="font"><?= $siniestro['agente']; ?></td>
                    <td class="font"><?= $siniestro['aseguradora'][0]['aseguradora_nombre']; ?></td>
                    <td class="font"><?php echo $this->Number->format($siniestro['prima_total'],array('before'=>'Bs','locale'=>'es_VE'))?></td>
                    <td class="font"><?php echo $this->Number->format($siniestro['coberturas'][0]['descripciones_cobertura'][0]['monto'],array('before'=>'Bs','locale'=>'es_VE')) ?></td>
                    <td class="font"><?php echo $this->Number->format($siniestro['monto_siniestro'],array('before'=>'Bs','locale'=>'es_VE')) ?></td>
                    <td class="font"><?= $siniestro['calculo']?>%</td>
                </tr>
            <?php endif?>
        <?php endforeach?>
        </tbody>
    </table>
</main>