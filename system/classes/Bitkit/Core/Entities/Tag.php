<?php

namespace Bitkit\Core\Entities;

class Tag extends \Bitkit\Core\Entities\Post

{
    public function setTable() : string
    {
        return 'core_tags';
    }
}
