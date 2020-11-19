<?php

namespace Bitkit\Core\Entities;

/**
 * Class Board
 * @package Bitkit\Core\Entities
 */
class Board extends \Bitkit\Core\Entities\Post
{

    /**
     * метод который показывает из какой таблицы брать данные
     *
     * @return string
     */
    public function setTable() : string
    {
        return 'core_boards';
    }

    /**
     * Получение досок юзера
     *
     *
     * @param int $user_id
     * @return int
     */
    public function getBoardsByUserId(int $user_id) : int
    {
        $sql_board = $this->getPDO()->prepare("SELECT id FROM core_boards WHERE user_id=$user_id");
        $sql_board->execute();
        $boards_info  = $sql_board->fetch(\PDO::FETCH_LAZY);
        $this->id = $boards_info->id;
        return $this->id;
    }
}