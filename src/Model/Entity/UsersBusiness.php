<?php
/**
 * Created by PhpStorm.
 * User: Eduardo Luttinger
 * Date: 05/08/2016
 * Time: 12:19 PM
 */

namespace App\Model\Entity;


use Cake\ORM\Entity;

class UsersBusiness extends Entity
{
    protected $_accessible = [
        '*' => true
    ];
}