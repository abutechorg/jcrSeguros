<div class="container">
    <div class="row">
        <div class="header">
            <div class="logo">
                <div id="logo">
                    <?= $this->Html->image("logo.png", array('id'=>'logo','fullBase' => true)) ?>
                </div>
                <div>
                    <img id="images" src="<?= $data['dataTraffic'][0]['user']['user_photo']; ?>">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container infoUser">
    <h3 class="clearfix">
        <div id="DateStar">
            <span>INITIAL DATE</span>
            <br/><?= $data['start_date'];?>
        </div>
        <div id="DateEnd">
            <span>END DATE</span>
            <br/><?= $data['end_date'];?>
        </div>
    </h3>
    <br>
</div>
<h1>User Information</h1>
<div class="container infoUser">
    <table class="infoTable">
        <tr>
            <td><strong>Name:</strong>&nbsp;<?= $data['dataTraffic'][0]['user']['first_name']; ?></td>
            <td><strong>Last Name:</strong>&nbsp;<?= $data['dataTraffic'][0]['user']['first_last_name']; ?></td>
            <td><strong>Birthdate:</strong>&nbsp;<?= $data['dataTraffic'][0]['user']['birthdate']; ?></td>

        </tr>
        <tr>
            <td><strong>School:</strong>&nbsp;<?= $data['business'];?></td>
            <td><strong>Grade:</strong>&nbsp;8</td>
            <td><strong>Gender:</strong>&nbsp;M</td>
        </tr>
        <tr>
            <td><strong>Present User:</strong>&nbsp;<?= $data['days_presents']; ?></td>
            <td><strong>Absent User:</strong>&nbsp;<?= $data['days_absents']; ?></td>
        </tr>
    </table>
    <br>
</div>

<h1>Traffic Information</h1>
<main class="infoUser">
    <table class="reaxiumTable">
        <thead>
        <tr>
            <th>Access Date</th>
            <th>Traffic Type</th>
            <th>Device ID</th>
            <th>Device Name</th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($data['dataTraffic'] as $user): ?>
            <tr>
                <td class="font"><?= $user['datetime']; ?></td>
                <td class="font"><?= $user['traffic_type']['traffic_type_name']; ?></td>
                <td class="font"><?= $user['reaxium_device']['device_id']; ?></td>
                <td class="font"><?= $user['reaxium_device']['device_name']; ?></td>
            </tr>
        <? endforeach; ?>
        </tbody>
    </table>
</main>

