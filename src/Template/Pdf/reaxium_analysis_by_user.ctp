<div class="row">
    <div class="header">
        <div class="logo">
            <div id="logo">
                <?= $this->Html->image("logo.png", array('id'=>'logo','fullBase' => true)) ?>
            </div>
            <div>
                <img id="images" src="<?= $data[0]['user_photo']; ?>">
            </div>
        </div>
    </div>
</div>
<br>
<div class="container infoUser">
    <h3 class="clearfix">
        <div id="DateStar">
            <span>INITIAL DATE</span>
            <br/><?= $data[0]['start_date'];?>
        </div>
        <div id="DateEnd">
            <span>END DATE</span>
            <br/><?= $data[0]['end_date'];?>
        </div>
    </h3>
    <br>
</div>
<h1>User Information</h1>
<div class="container infoUser">
    <table class="infoTable">
        <tr>
            <td><strong>Name:</strong>&nbsp;<?= $data[0]['user_name']; ?></td>
            <td><strong>Last Name:</strong>&nbsp;<?= $data[0]['user_last_name']; ?></td>
            <td><strong>Document ID</strong>&nbsp;<?= $data[0]['document_id']; ?></td>
        </tr>
        <tr>
            <td><strong>Present User:</strong>&nbsp;<?= $data[0]['present_days'];?></td>
            <td><strong>Absent User:</strong>&nbsp;<?= $data[0]['absent_days'];?></td>
        </tr>
    </table>
    <br>
</div>
<h1>Statistics Information</h1>

<main class="infoUser">
    <div>
        <canvas id="myChart" width="800" height="450"></canvas>
    </div>

</main>


<script>

    var config = {
        type: 'doughnut',
        data: {
            labels: ["Present Days", "Absent Days"],
            datasets: []
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: "Graphic Comparing Attendance"
            },
            legend: {
                position: 'bottom'
            }
        }
    };



    var ctx = document.getElementById("myChart").getContext("2d");


    <? foreach($data as $item): ?>

    var newDataset = {data: [],backgroundColor: []};
    var arrayData = [];
    var arrayBackGroundColor=[];

    arrayData = <?= $item['data'];?>;
    arrayBackGroundColor = <?= $item['backgroundColor']; ?>;

    for (var index = 0; index < arrayData.length; index++) {
        newDataset.backgroundColor.push(arrayBackGroundColor[index]);
        newDataset.data.push(arrayData[index]);
    }

    config.data.datasets.push(newDataset);

    <? endforeach; ?>

    new Chart(ctx, config);

</script>