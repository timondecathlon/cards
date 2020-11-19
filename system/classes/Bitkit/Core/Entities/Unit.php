<?php

namespace Bitkit\Core\Entities;

/**
 * Class Unit
 * @package Bitkit\Core\Entities
 */
abstract class Unit implements \Bitkit\Core\Interfaces\UnitActions
{
    /**
     *  id строки в таблице в БД
     *
     * @var int|null
     */
    protected $id ;

    /**
     * Идентификатор соединения
     *
     * @var \PDO $pdo
     */
	protected $pdo;

    /**
     * данные всей строки из БД
     *
     * @var
     */
    public $bdata;


    /**
     * Unit constructor.
     * @param int|null $id
     */
	public function __construct(int $id = null )
    {
		$this->id = $id;
	}

    abstract public function setTable() : string ;

    /**
     * Статический метод для получения объекта для работы с БД
     *
     * @return \PDO
     */
	public static function getPDO() : \PDO
    {
       return \Bitkit\Core\Database\Connect::getInstance()->getConnection();
    }

    /**
     * считывание данных строки из БД в объект - кэширование
     */
	public function getData() : void
    {
        $sql = $this->getPDO()->prepare("SELECT * FROM ".$this->setTable()." WHERE id='".$this->id."'");
        $sql->execute();
        $this->bdata = $sql->fetch(\PDO::FETCH_LAZY);
    }

    /**
     * запись данных строки в объект если она уже была вытащена из БД
     *
     * @param $data
     * @return bool
     */
    public function setData($data) : bool
    {
        if ($this->data = (object)$data) {   
            return true;
        }
        return false;
    }

    /**
     * Получение данных строки из таблицы
     *
     * @return mixed
     */
    public function getLine()
    {
        if (!$this->bdata) {
            $this->getData();
        }
        return $this->bdata;
    }

    /**
     * Получение значения конкретного поля из строки
     *
     * @param string $field
     * @return string
     */
    public function getField(string $field)
    {
        return trim($this->getLine()->$field);
    }

    /**
     * Возвращает раскодированное значения JSON конкретного поля строки
     *
     * @param string $field
     * @return mixed
     */
    public function getJsonField($field)
    {
        return json_decode($this->getField($field));
    }

    /**
     *
     * Метод для создания строки в таблице в БД
     *
     * @param array $fields_array
     * @param array $values_array
     * @return int
     */
    public function createLine(array $fields_array, array $values_array) : int
    {
        $fields_str = implode(',',$fields_array);
        $placeholders_str = '';
        foreach ($fields_array as $key=>$value) {
            $placeholders_str .= ":$value,";
        }
        $sql = $this->getPDO()->prepare("INSERT INTO ".$this->setTable()."($fields_str)VALUES(".trim($placeholders_str,',').") ");
        foreach ($fields_array as $key=>$value) {
            $sql->bindParam(":$fields_array[$key]", $values_array[$key]);
        }
        try {
            $sql->execute();
            $this->id = $this->getPDO()->lastInsertId();
            return $this->id;
        } catch (\PDOException $e) {
            echo 'Подключение не удалось: ' . $e->getMessage();
        }
        return 0;
    }

    /**
     * метод для обновления нескольких полей в строке таблицы
     *
     * @param array $fields_array
     * @param array $values_array
     * @return int
     */
    public function updateLine(array $fields_array, array $values_array)  : int
    {
        $update_str = '';
        foreach ($fields_array as $key=>$value) {
            $update_str .= "$value=:$value,";
        }
        $sql = $this->getPDO()->prepare("UPDATE ".$this->setTable()." SET ".trim($update_str,',')."  WHERE id=".$this->id);
        foreach ($fields_array as $key=>$value) {
            $sql->bindParam(":$value", $values_array[$key]);
        }
        try {
            $sql->execute();
            $this->getPDO()->errorInfo();
            return $this->getField('id');
        } catch (\PDOException $e) {
            echo 'Подключение не удалось: ' . $e->getMessage();
        }
        return 0;
    }

    /**
     * Метод для обновления одного конкретного поля в строке таблицы
     *
     * @param $field
     * @param $param
     * @return bool
     */
    public function updateField($field, $param) : bool
    {
        if ($this->updateLine([$field],[$param])) {
            return true;
        }
        return false;
    }

    /**
     * Метод для удаления строки из таблицы
     *
     * @return int
     */
    public function deleteLine() : int
    {
        $sql = $this->getPDO()->prepare("DELETE FROM ".$this->setTable()." WHERE id=:id");
        $sql->bindParam(':id', $this->id);
        try {
            $post_id = $this->getField('id');
            $sql->execute();
            return $post_id;
        } catch (\PDOException $e) {
            echo 'Подключение не удалось: ' . $e->getMessage();
        }
        return 0;
    }


    public function getPostFilesDir()
    {
        $dir = new Table(0);
        $dir->getTableByName($this->setTable());
        $dir_path = $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$dir->getField('table_directory').'/'.$this->id.'/';
        return $dir_path;
    }

    /**
     * Метод для "мягкого" удаления строки
     *
     * @return bool
     */
    public function softDelete() : bool
    {
        return $this->updateField('deleted', 1);
    }


    /**
     * Получение всех полей в таблице
     *
     * @return array
     */
    public function getTableColumns() : array
    {
        $columns_sql= $this->getPDO()->prepare("SHOW COLUMNS FROM ".$this->setTable()."");
        $columns_sql->execute();
        return $columns_sql->fetchAll();
    }

    /**
     * Получение списка ИМЕН всех полей в таблице
     *
     * @return array
     */
	public function getTableColumnsNames() : array
    {
        $par_arr = [];
        foreach ($this->getTableColumns() as $column) {
            $par_arr[$column['Field']] = $column['Field'];
        }
        return $par_arr;
    }

    /**
     * Проверяет есть ли такое поле в таблице
     *
     * @param string $field_name
     * @return bool
     */
    public function hasField(string $field_name) : bool
    {
        if (in_array($field_name,$this->getTableColumnsNames())) {
            return true;
        }
        return false;
    }

    public function getMaxId()
    {
        $sql = $this->getPDO()->prepare("SELECT MAX(id) FROM ".$this->setTable()." ");
        $sql->execute();
        $id_info = $sql->fetch(\PDO::FETCH_LAZY);
        return $id_info['MAX(id)'];
    }

    public function getRealMaxId()
    {
        $sql = $this->getPDO()->prepare("SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'pennylane' AND TABLE_NAME  = '".$this->setTable()."'");
        $sql->execute();
        $id_info = $sql->fetch(\PDO::FETCH_LAZY);
        return $id_info['AUTO_INCREMENT'];
    }
}


