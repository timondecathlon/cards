<?php
/**
 * Created by PhpStorm.
 * User: Zhitkov
 * Date: 27.07.2020
 * Time: 15:52
 */

namespace Bitkit\Core\Interfaces;

/**
 * Interface UserAuth
 * @package Bitkit\Core\Interfaces
 */
interface UserAuth
{
    public function logIn();

    public function logOut();

    public function registration();

    public function recover();

}