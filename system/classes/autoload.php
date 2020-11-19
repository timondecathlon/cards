<?php

function classLoader($class)
{
    require_once( $_SERVER['DOCUMENT_ROOT'].'/system/classes/'.str_replace('\\','/',$class.'.php'));
}
spl_autoload_register('classLoader');    


