<?php

namespace Bitkit\Core\Interfaces;

/**
 * Interface UnitActions
 * @package Bitkit\Core\Interfaces
 */
interface UnitActions
{
    public function createLine(array $fields_array, array $values_array) : int ;

    public function getLine();

    public function updateLine(array $fields_array, array $values_array) : int ;

    public function deleteLine();
}
