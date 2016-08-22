

    <div class="row">
        <div class="headerByUser">
            <div>
                <?= $this->Html->image("logo.png", array('id'=>'logoAccess','fullBase' => true,'style'=>'width:230px;
                       height:150px')) ?>
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
    <h1>Access Information</h1>
    <main class="infoUser" >

      <table class="accessTable">
         <tr>
            <th>Name</th>
            <th>Last Name</th>
            <th>Document ID</th>
            <th>Access Type</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
         <? foreach($data['traffic'] as $user): ?>
                 <tr>
                    <td class="font"><?= $user['users']['first_name']; ?></td>
                    <td class="font"><?= $user['users']['first_last_name']; ?></td>
                    <td class="font"><?= $user['users']['document_id']; ?></td>
                    <td class="font"><?= $user['traf']['traffic_type_name']; ?></td>
                    <td class="font"><?= $user['datetime']; ?></td>
                    </tr>
         <? endforeach; ?>
        </tbody>
      </table>
    </main>