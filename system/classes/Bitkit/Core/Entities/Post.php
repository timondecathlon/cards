<?php

namespace Bitkit\Core\Entities;

class Post extends \Bitkit\Core\Entities\Unit
{
    protected $table;

    //Получаем таблицу поста с которой работать и ее проверяем
    public function getTable($table)
    {
        $this->table = $table;
    }

    //выозвращаем имя базы при подключении к БД
    public function setTable() : string
    {
        return $this->table;
    }

    //Получаем таблицу поста с которой работать и ее проверяем
    public function postId() : int
    {
        return $this->getField('id');
    }

    //Получаем таблицу поста с которой работать и ее проверяем
    public function title() : string
    {
        return $this->getField('title');
    }


}


?>