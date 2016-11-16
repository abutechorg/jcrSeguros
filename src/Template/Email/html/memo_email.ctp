<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recordatorio</title>

</head>
<body style="background-color:#f5f5f5;
display: block;
font-family: 'Raleway', sans-serif !important;font-size: 14px;
font-weight: 500;
color: #999;overflow-x: hidden;">

<div style="width: 100%;height: auto;margin-left:50px;">

    <div style="display: block;
    background: #d19037 !important;
    width: 87%;
    text-align: center;
    height: auto;
    box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);z-index: 1000;">

        <?= $this->Html->image("logo2.jpeg",
        array('fullBase' => true, 'style' => ' width: 20% !important;padding: 5px;vertical-align: middle;border: 0;')) ?>
    </div>
    <div style="width: 78%;
            display: block;
            height: auto;
            padding: 40px 35px;
            border: 1px solid #e2e2e2;
            box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
            box-sizing: border-box;">

        <div style="margin-bottom: 25px;">
            <h2 style="font-weight: 500;color: #ff9800;font-size: 30px;margin: 0 0 15px;">
                Recordatorio
            </h2>

            <p style=" margin: 0 0 30px;line-height: 30px;">
                A reliable, efficient and fun way to track your children. You can:
            </p>
            <ol style="font-size: 17px;font-weight: 500;color: #636e73;" type="disc">
                <h4><strong>
                    <li> <?= $prueba ?></li>
                </strong></h4>

            </ol>

        </div>
    </div>
</div>

</body>
</html>