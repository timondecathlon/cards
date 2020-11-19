<?php

namespace Bitkit\Core\Entities;

/**
 * Class User
 * @package Bitkit\Core\Entities
 */
class User extends \Bitkit\Core\Entities\Unit implements \Bitkit\Core\Interfaces\UserActions
{

    use \Bitkit\Core\Traits\Test;

    public $login;
    public $password;
    public $result;

    /**
     * @return string
     */
    public function setTable() : string
    {
        return 'core_users';
    }


    public function logIn()
    {

    }

    public function logOut()
    {

    }

    public function registration()
    {

    }

    public function recover(){

    }

    public function addCards()
    {

    }

    public function saveCards(){

    }

    public function getCards(){

    }


    public function member_id()
    {
        if ($this->getField('id')) {
            return $this->getField('id');
        }
        return 0;
    }

    public function title()
    {
        if($this->firstName() || $this->lastName() || $this->fatherName()){
            return $this->fullName();
        }else{
            return $this->getField('title');
        }
    }

    public function titleShort(){
        if($this->firstName() || $this->lastName() || $this->fatherName()){
            return $this->lastName().' '.preg_split('//u',$this->firstName(),-1,PREG_SPLIT_NO_EMPTY)[0].'.';
        }else{
            return $this->getField('title');
        }
    }

    public function firstName(){
        return $this->getField('first_name');
    }

    public function lastName(){
        return $this->getField('last_name');
    }

    public function fatherName(){
        return $this->getField('father_name');
    }

    public function fullName(){
        return $this->firstName().' '.$this->lastName().' '.$this->fatherName();
    }

    public function status(){
        return $this->getField('status');
    }

    public function reputation_points(){
        return $this->getField('reputation_points');
    }

    public function photo(){
        if(unserialize($this->getField('photo'))[0] != ''){
            return unserialize($this->getField('photo'))[0];
        }else{
            return 'https://openclipart.org/image/2400px/svg_to_png/247319/abstract-user-flat-3.png';
        }
    }

    public function photos(){
        if(unserialize($this->getField('photo')) != ''){
            return unserialize($this->getField('photo'));
        }
        return false;
    }

    public function coverPhoto(){
        return $this->getField('cover_photo');
    }

    public function respect_points(){
        return $this->getField('respect_points');
    }


    public function isActive(){
        if($this->getField('activity') == 1){
            return true;
        }
        return false;
    }

    public function is_online(){
        if(time() - $this->getField('last_visit') < 60){
            return true;
        }
        return false;
    }


    public function instagram(){
        return $this->getField('instagram');
    }

    public function avatar()
    {
        if($this->getField('avatar')){
            return $this->getField('avatar');
        }
        return '/img/avatar_default.png';
    }




    /* check login and pass  */
    public function loginCheck($login, $password){
        $member_sql = $this->getPDO()->prepare("SELECT * FROM ".$this->setTable()." WHERE login=:login OR email=:email ");
        $member_sql->bindParam(':login',$login);
        $member_sql->bindParam(':email',$login);
        $member_sql->execute();
        $member = $member_sql->fetch();
        if($member_sql->rowCount()){
                if( hash_equals($member['password'], crypt($password, $member['password']))){

                    $id = $member['id'];
                    $user_hash = md5($member['id'].'%'.$member['password'].'%'.$member['email'].'%'.time()); //создаем хэш для защиты куки
                    $sql = $this->getPDO()->prepare("UPDATE ".$this->setTable()." SET  user_hash=:user_hash  WHERE id=:id");
                    $sql->bindParam(":user_hash", $user_hash);
                    $sql->bindParam(":id", $id);
                    $sql->execute();
                    return ['id'=>$id,'hash'=>$user_hash];
                    /*
                    $member_password_hash = $member['password'];
                    setcookie ("member_id", "$id",time()+36000,"/"); //поставить тут переменное занчение
                    $user_hash = md5($_SERVER ['HTTP_USER_AGENT'].'%'.$member_password_hash); //создаем хэш для защиты куки
                    $sql = $this->getPDO()->prepare("UPDATE ".$this->setTable()." SET ip_address=:ip_address , user_hash=:user_hash  WHERE id=:id");
                    $sql->bindParam(":ip_address", $_SERVER['REMOTE_ADDR']);
                    $sql->bindParam(":user_hash", $user_hash);
                    $sql->bindParam(":id", $id);
                    $sql->execute();
                    $flag =1;
                    break;
                    */
                }else{
                    return 0;
                    //setcookie ("member_id","0",time()-3600,"/");
                    //echo 'пароль неверный';
                }
        }else{
            return 0;
            //echo 'такого юзера нету';
        }
    }

    public function exists($email,$title){
        $sql = $this->getPDO()->prepare("SELECT COUNT(id) as num FROM ".$this->setTable()." WHERE email=:email OR title=:title ");
        $sql->bindParam(":email", $email);
        $sql->bindParam(":title", $title);
        $sql->execute();
        $member =  $sql->fetch(\PDO::FETCH_LAZY);
        if($member['num'] > 0){
            return true;
        }else{
            return false;
        }
    }

    /* validate the users id*/
    public function is_valid($hash){
        $sql = $this->getPDO()->prepare("SELECT * FROM ".$this->setTable()." WHERE id=:id");
        $sql->bindParam(":id", $this->id);
        $sql->execute();
        $member =  $sql->fetch(\PDO::FETCH_LAZY);
        if(!hash_equals ( $member->user_hash , $hash )){
            return false;
        }else{
            return true;
        }
    }

    /* check if the user is admin  */
    public function isAdmin(){
        if($this->getField('member_group_id') == 4){
            return true;
        }else{
            return false;
        }
    }


    public function check_password($password, $confirm){
        if($password === $confirm && $password !='' && $password !=' '){
            return true;
        }else{
            return false;
        }
    }


    public function email(){
        return $this->getField('email');
    }

    public function phone(){
        return $this->getField('phone');
    }


    public function group_id(){
        if($this->getField('member_group_id')){
            return $this->getField('member_group_id');
        }else{
            return 1;
        }
    }





}



?>