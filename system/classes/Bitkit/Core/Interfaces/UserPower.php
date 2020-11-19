<?php
/**
 * Created by PhpStorm.
 * User: Zhitkov
 * Date: 27.07.2020
 * Time: 15:55
 */

namespace Bitkit\Core\Interfaces;

/**
 * Interface UserPower
 * @package Bitkit\Core\Interfaces
 */
interface UserPower
{

    public function addCards();

    public function saveCards();

    public function getCards();

}