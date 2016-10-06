<?php

/**
 * Created by PhpStorm.
 * User: Gualdo de la cruz
 * Date: 05/10/2016
 * Time: 10:45 AM
 */


use Cake\ORM\Entity;

class Cliente extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];


}