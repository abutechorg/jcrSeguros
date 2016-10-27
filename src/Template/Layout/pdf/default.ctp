<!DOCTYPE html>
<html>
<head>

    <!--js-->

    <?php echo $this->Html->script('Chart.bundle.min.js',['fullBase' => true]); ?>
    <?php echo $this->Html->script('jquery.min.js',['fullBase' => true]); ?>

    <!--styles-->
    <?php echo $this->Html->css('style_user_perfil.css',['fullBase' => true]);?>


</head>
<body class="main">

    <?php echo $this->fetch('content'); ?>
</body>

</html>