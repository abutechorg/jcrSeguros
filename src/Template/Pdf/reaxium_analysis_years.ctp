<div class="row">
    <div class="headerByUser">
        <div>
            <?= $this->Html->image("logo.png", array('id' => 'logoStatistics', 'fullBase' => true)) ?>
        </div>
    </div>
</div>
<br>
<div class="container infoUser">
    <h3 class="clearfix">
        <!--<div id="DateStar">
            <span>INITIAL DATE</span>
            <br/>
        </div>
        <div id="DateEnd">
            <span>END DATE</span>
            <br/>
        </div>-->
    </h3>
    <br>
</div>

<h1>Statistics Information</h1>
<div class="container infoUser">
    <table class="infoTable">
        <? foreach ($data as $item): ?>
        <tr>
                <td><strong id="Total">Year:</strong>&nbsp;<?= $item['start_date'];?></td>
                <td><strong id="UserIn">Maximum assists month:</strong>&nbsp;
                    <?= $item['attendance_max_month'];?>
                </td>
                <td><strong id="UserOut">Minimum assists month:</strong>&nbsp;
                    <?= $item['attendance_min_month'];?>
                </td>
        </tr>
        <? endforeach; ?>
    </table>
    <br>
</div>

<main class="infoUser">
    <div>
        <canvas id="myChart" width="800" height="450"></canvas>
    </div>

</main>


<script>

    var config = {
        type: 'line',
        data: {
            labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            datasets: []

        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: ""
            },
            tooltips: {
                mode: 'label'
            },
            hover: {
                mode: 'label'
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Attendance'
                    },
                }]
            }
        }
    };


    var ctx = document.getElementById("myChart").getContext("2d");


    <? foreach($data as $item): ?>

    var newDataset = {data: []};
    var arrayData = [];

    newDataset.label = '<?= $item['label']; ?>';
    newDataset.borderColor = '<?= $item['borderColor']; ?>';
    newDataset.backgroundColor = '<?= $item['backgroundColor']; ?>';
    newDataset.pointBorderColor = '<?= $item['pointBorderColor']; ?>';
    newDataset.pointBackgroundColor = '<?= $item['pointBackgroundColor']; ?>';
    newDataset.pointBorderWidth = '<?= $item['pointBorderWidth']; ?>';

    arrayData = <?= $item['data'];?>;

    for (var index = 0; index < arrayData.length; index++) {
        newDataset.data.push(arrayData[index]);
    }

    config.data.datasets.push(newDataset);

    <? endforeach; ?>


    new Chart(ctx, config);

</script>