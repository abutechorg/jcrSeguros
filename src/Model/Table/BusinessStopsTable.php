<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 25/7/2016
 * Time: 10:22
 */

namespace App\Model\Table;
use Cake\ORM\Table;

class BusinessStopsTable extends Table{

    public function initialize(array $config){
        parent::initialize($config);
        $this->table("business_stops");
    }


}