<?php
<<<<<<< HEAD

/**
 * Created by PhpStorm.
 * User: Gualdo de la cruz
 * Date: 05/10/2016
 * Time: 10:50 AM
=======
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 5/10/2016
 * Time: 11:26
>>>>>>> origin/vladimir-dev
 */

use Cake\ORM\Entity;

<<<<<<< HEAD
class TipoCliente extends Entity
{

    protected $_accessible = [
        '*' => true,
    ];

=======
class TipoCliente extends Entity{

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


>>>>>>> origin/vladimir-dev
}