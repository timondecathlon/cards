<?php

namespace Bitkit\Core\Entities;

class Card extends \Bitkit\Core\Entities\Post
{

    public  function setTable() : string
    {
        return 'core_cards';
    }

    public function markAsRead(){
        $this->updateField('is_read',1);
    }


    public function createTask(){
        $this->updateField('is_read',1);
    }


}