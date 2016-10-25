<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Notificacion</title>
</head>
<body>

<div style="width:100%;" align="center">
    <table width="740" border="0" cellspacing="0" cellpadding="0">
        <thead width:
        "100%" style="background-color:#dcdcdc; color:#FFFFFF; font-size:36px; font-family: Helvetica, Arial,
    sans-serif;">
        <tr style="height:202px;">
            <th style="font-size: 14px; font-family: Helvetica, Arial, sans-serif; padding-right: 260px;"
                align="right"><?= $this->Html->image("logo2.jpeg", array('fullBase' => true,'style'=>'width:230px;
                height:150px')) ?>
            </th>
            <!--<th style="font-size: 14px; font-family: Helvetica, Arial, sans-serif; padding-right: 260px;" align="right"><img src="img/logo_reaxium.png" style="width:230px; height:150px;"><br></th> -->
        </tr>
    </thead>
</table>
<table width="740" border="0" cellspacing="0" cellpadding="0" align="center">
    <tbody style="background-color: #eeedf2;">
    <tr>
        <td style="color:#5d5d5d; font-size:22px; font-family: Helvetica, Arial, sans-serif; line-height: 131%; padding-left: 17%; padding-top: 88px;">
            Póliza en proceso de renovación</td>
    </tr>
    <tr>
        <td style="padding-left: 10%; color:#5d5d5d; font-size:15px; font-family: Helvetica, Arial, sans-serif; padding-top: 50px; ">
           <strong>Estimado cliente <?= $nombre_cliente ?></strong> <br/><br/>
            Su póliza de seguros "<?= $ramo_nombre ?>", numero: <?= $numero_poliza ?> se encuentra en proceso de renovación.<br><br/>
            Próximamente, nos estaremos comunicando con usted para hacerle llegar la información
            más detallada, además de las alternativas que más se adapten a sus necesidades.<br><br>
            Si desea mayor información, contáctenos a través del 0212-761- 9707.
        </td>
    </tr>
    <tr>
        <td style="padding-left: 10%; color:#062744; font-size:14px; font-family: Helvetica, Arial, sans-serif; padding-top: 50px;"><strong>
            Correo para la gerencia: Dirigirlo a Jcrseguros@gmail.com</strong>
        </td>
    </tr>
    </tbody>
</table>
</div>
</body>
        </html>