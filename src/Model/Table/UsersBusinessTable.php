<?php
/**
 * Created by PhpStorm.
 * User: Eduardo Luttinger
 * Date: 05/08/2016
 * Time: 12:18 PM
 */

namespace App\Model\Table;


use Cake\ORM\Table;

class UsersBusinessTable extends Table
{


    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('users_business');
        $this->belongsTo('Users', array('foreignKey' => 'user_id'));
        $this->belongsTo('Business', array('foreignKey' => 'business_id'));
    }


}