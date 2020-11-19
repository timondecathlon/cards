<?php
abstract class Unit implements UnitActions
{

    protected $pdo; // Идентификатор соединения
    protected $id;
    public $bdata;

    /*конструктор, подключающийся к базе данных, устанавливающий локаль и кодировку соединения */
    public function __construct(int $id = null ) {
        $this->id = $id;
        //$this->pdo = \Bitkit\Core\Database\Connect::getInstance()->getConnection();
    }

    abstract public function setTable();

    public function getFileByName($name){
        return file_get_contents($_SERVER['DOCUMENT_ROOT'].'/database/'.$name.'.txt');
    }


    public function getLine()
    {
        $data = json_decode($this->getFileByName($this->setTable()));
        foreach ($data as $elem){
            if($elem->id == $this->id){
                $this->bdata = $elem;
                return $elem;
            }
        }
    }

    public function getField($field)
    {
        if(!$this->bdata){
            $this->getLine();
        }
        return trim($this->bdata->$field);
    }

    public function gf($field)
    {
        if(!$this->bdata){
            $this->getLine();
        }
        return trim($this->bdata->$field);
    }


    public function getFieldPreview($field, int $num)
    {
        return substr($this->getField($field),0,$num).'...';
    }

    public function showField($field)
    {
        return $this->getLine()->$field;
    }

    public function showJsonField($field)
    {
        return json_decode($this->getField($field));
    }
    public function getJsonField($field)
    {
        return json_decode($this->getField($field));
    }

    /*
    public function getSchema($table)
    {
        $schema = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/database/schema.txt')
        $schema = json_decode($schema,true);
        return $schema[$table];
    }
    */

    public function createLine($fields_array, $values_array){

        //собираем массив ассоц из пришедших данных
        $elem = [];
        foreach ($fields_array as $i=>$value) {
            $elem[$value] =  $values_array[$i];
        }

        //смотрим порядок полей у данной таблицы и делаем сортированный массив
        $table = $this->setTable();

        //echo $table;

        $schema = json_decode($this->getFileByName('schema'),true);
        //var_dump($schema);
        $fields = $schema[$table]['fields'];
        //var_dump($fields);
        $elem_sorted = [];

        //echo '<br><br>';
        //echo $elem_sorted['id'] ;
        //echo '<br><br>';
        foreach ($fields as $field) {
            if(isset($elem[$field])){
                $elem_sorted[$field] = $elem[$field];
            }else{
                $elem_sorted[$field] = '';
            }
        }

        $elem_sorted['id']  = $schema[$table]['increment']; //получить инкремент
        //echo $elem_sorted['id'] ;

        //считываем всю таблицу
        $data = json_decode($this->getFileByName($this->setTable()),true);
        $data[] = $elem_sorted;

        //var_dump($elem_sorted);

        //прибавляем инкремент
        $schema[$table]['increment'] = $schema[$table]['increment'] + 1;

        //записываем в файл
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/database/'.$this->setTable().'.txt',json_encode($data));
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/database/schema.txt',json_encode($schema));

        return $elem_sorted['id'];  
    }

    public function updateField($field, $param){
        $data = json_decode($this->getFileByName($this->setTable()),true);

        //var_dump($data);

        $i = 0;
        foreach ($data as $elem){
            if($elem['id'] == $this->id){
                break;
            }else{
                $i++;
            }
        }


        echo '<br><br>';
        echo $i;
        echo '<br><br>';

        $data[$i][$field] = $param; 

        //записываем в файл
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/database/'.$this->setTable().'.txt',json_encode($data));


        return $i;
    }

    public function setIncrement($table){
        $data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/database/increment.php');
        $data[$table] = $data[$table] + 1;
    }

    public function updateLine($fields_array, $values_array){

        $data = json_decode($this->getFileByName($this->setTable()),true);

        $count = count($fields_array);

        for($i = 0; $i < $count; $i++){
            $data[$this->id - 1][$fields_array[$i]] = $values_array[$i];
        }

        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/database/'.$this->setTable().'.txt',json_encode($data));
    }


    public function deleteLine(){
        $data = json_decode($this->getFileByName($this->setTable()),true);

        $i = 0;
        $num = 0;
        foreach($data as $item){
            if($item['id'] == $this->id){
                $num = $i;
                break;
            }else{
                $i++;
            }
        }

        unset($data[$num]);
        sort($data);
        $data_new = [];
        foreach($data as $key=>$value){
            $data_new[] = $value;
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/database/'.$this->setTable().'.txt',json_encode($data_new));
    }

   

    /*

   public function setJsonField($field, $string, $delimiter)
   {
       $value_str = trim($string,$delimiter);
       $value_arr = explode($delimiter,$value_str);
       $value_json = json_encode($value_arr);
       if($this->updateField($field, $value_json)){
           return true;
       }
       return false;
   }




   public function updateField($field, $param){
       $sql = $this->pdo->prepare("UPDATE ".$this->setTable()." SET $field=:param WHERE id=:id");
       $sql->bindParam(':param', $param);
       $sql->bindParam(':id', $this->id);
       if(in_array($field,$this->getTableColumnsNames())) {
           if($sql->execute()){
               return true;
           }
       }
       return false;
   }


   public function setLimit(int $from, int $amount)
   {
       $this->sortLimit = $amount;
   }

   public function setSortDirection($direction)
   {
       if($direction == 'desc'){
           $this->sortDirection = 'DESC';
       }else{
           $this->sortDirection = 'ASC';
       }
   }

   public function setSortField($field)
   {
       if(in_array($field,$this->getTableColumnsNames())){
           $this->sortField = $field;
       }
   }  


   public function createLine($fields_array, $values_array){
       $fields_str = implode(',',$fields_array);
       $placeholders_str = '';
       foreach ($fields_array as $key=>$value) {
           $placeholders_str .= ":$value,";
       }
       $sql = $this->pdo->prepare("INSERT INTO ".$this->setTable()."($fields_str)VALUES(".trim($placeholders_str,',').") ");
       foreach($fields_array as $key=>$value){
           $sql->bindParam(":$fields_array[$key]", $values_array[$key]);
       }
       try {
           $sql->execute();
           $this->id = $this->pdo->lastInsertId();
           return $this->id;
       }catch (PDOException $e) {
           echo 'Подключение не удалось: ' . $e->getMessage();
       }
       return 0;
   }

   public function updateLine($fields_array, $values_array){
       $update_str = '';
       foreach($fields_array as $key=>$value){
           $update_str .= "$value=:$value,";
       }
       $sql = $this->pdo->prepare("UPDATE ".$this->setTable()." SET ".trim($update_str,',')."  WHERE id=".$this->id);
       foreach($fields_array as $key=>$value){
           $sql->bindParam(":$value", $values_array[$key]);
       }
       try {
           $sql->execute();
           $this->pdo->errorInfo();
           return $this->getField('id');
       }catch (PDOException $e) {
           echo 'Подключение не удалось: ' . $e->getMessage();
       }
       return 0;
   }

   public function deleteLine(){
       $sql = $this->pdo->prepare("DELETE FROM ".$this->setTable()." WHERE id=:id");
       $sql->bindParam(':id', $this->id);
       try {
           $post_id = $this->getField('id');
           $sql->execute();
           return $post_id;
       }catch (PDOException $e) {
           echo 'Подключение не удалось: ' . $e->getMessage();
       }
       return 0;
   }

   public function fileDelete(string $file ){
       if(file_exists($_SERVER["DOCUMENT_ROOT"].$file)){ //delete file
           if(unlink($_SERVER["DOCUMENT_ROOT"].$file)){
               return true;
           }
       }
       return false;
   }


   public function getFileExtension($file){
       $ext = '.'.array_pop(explode('.',$file));
       return $ext;
   }

   public function getFileName($file){
       $name_parts = explode('.',$file);
       array_pop($name_parts);
       return implode('.',$name_parts);
   }

   public function getFilePureName($file){
       $name_parts = explode('/',$this->getFileName($file));
       $name = array_pop($name_parts);
       return $name;
   }

   public function getPostFilesDir(){
       $dir = new Table(0);
       $dir->getTableByName($this->setTable());
       $dir_path = $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$dir->getField('table_directory').'/'.$this->id.'/';
       return $dir_path;
   }

   public function softDelete(){
       $post_id = $this->getField('id');
       if($this->updateField('deleted', 1)){
           return $post_id;
       }
       return 0;
   }

   public function activate(){
       $this->updateField('activity', 1);
   }

   public function deactivate(){
       $this->updateField('activity', 0);
   }



   public function countAllActive(){
       $sql= $this->pdo->prepare("SELECT COUNT(*) FROM ".$this->setTable());
       $sql->execute();
       $all = $sql->fetch();
       return  $all[0];
   }

   public function getTableColumns(){
       $columns_sql= $this->pdo->prepare("SHOW COLUMNS FROM ".$this->setTable()."");
       $columns_sql->execute();
       return $columns_sql->fetchAll();
   }

   public function getTableColumnsNames(){
       $par_arr = [];
       foreach ($this->getTableColumns() as $column){
           $par_arr[$column['Field']] = $column['Field'];
       }
       return $par_arr;
   }

   public function getAllUnits(){
       $sql = $this->pdo->prepare("SELECT * FROM ".$this->setTable()." ORDER BY ".$this->sortField." ".$this->sortDirection." ");
       $sql->execute();
       $units = $sql->fetchAll();
       return $units;
   }

   public function getAllUnitsId() : array{
       $ids =[];
       $sql = $this->pdo->prepare("SELECT * FROM ".$this->setTable()." ORDER BY ".$this->sortField." ".$this->sortDirection." ");
       $sql->execute();
       while($unit = $sql->fetch(\PDO::FETCH_LAZY)){
           $ids[] = $unit->id;
       }
       return $ids;
   }

   public function getAllActiveUnits(){
       $sql = $this->pdo->prepare("SELECT * FROM ".$this->setTable()." WHERE activity='1'");
       $sql->execute();
       $units = $sql->fetchAll();
       return $units;
   }

   public function getAllUnitsReverse(){
       $sql = $this->pdo->prepare("SELECT * FROM ".$this->setTable()." ORDER BY publ_time DESC");
       $sql->execute();
       $units = $sql->fetchAll();
       return $units;
   }

   public function hasField($field_name){
       if(in_array($field_name,$this->getTableColumnsNames())){
           return true;
       }
       return false;
   }

   public function getMaxId(){
       $sql = $this->pdo->prepare("SELECT MAX(id) FROM ".$this->setTable()." ");
       $sql->execute();
       $id_info = $sql->fetch(\PDO::FETCH_LAZY);
       return $id_info['MAX(id)'];
   }

   public function getRealMaxId(){
       $connect = Connect::getInstance();
       $sql = $this->pdo->prepare("SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'pennylane' AND TABLE_NAME  = '".$this->setTable()."'");
       $sql->execute();
       $id_info = $sql->fetch(\PDO::FETCH_LAZY);
       //var_dump($id_info);
       return $id_info['AUTO_INCREMENT'];
   }

   */
}


