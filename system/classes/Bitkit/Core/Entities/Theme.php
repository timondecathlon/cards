<?php

namespace Bitkit\Core\Entities;

class Theme extends \Bitkit\Core\Entities\Post
{

    public  function setTable() : string
    {
        return 'core_themes';
    }

}