<?php
namespace Bitkit\Core\Routing;


class Router extends \Bitkit\Core\Patterns\Singleton {

    public $url;

    protected static $instance = NULL; // Единственный экземпляр класса, чтобы не создавать множество подключений

    public function setURL()  {
        $this->url = (object)parse_url($this->getDomain().$this->getURI());
    }

    public function getProtocol() {
        if(!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']) ){
            return 'https';
        }
        return 'http';
    }

    public static function getHost() {
        return $_SERVER['HTTP_HOST'];
    }

    public function getDomain() {
        return $this->getProtocol().'://'.$this->getHost();
    }

    public static function getURI() {
        return $_SERVER['REQUEST_URI'];
    }

    public function getPath() {
        return explode('/',trim($this->url->path,'/'));
    }

    public function getParsString() {
        $string = '';
        $pars = $this->getParameters();
        foreach($pars as $key=>$value){
            $string =  $string.'&'.$key.'='.$value;
        }
        return trim($string,'&');

    }

    public function getParameters() {
        $params_arr_assoc = [];
        $params = [];
        foreach(explode('&',$this->url->query) as $param){
            if(!in_array($param,$params)){
                $parts = explode('=',$param);
                $params[]= $parts[0];
                $params_arr_assoc[$parts[0]] = $parts[1];
            }
        }
        return $params_arr_assoc;
    }

    public function getURL() {

        return $this->getDomain().$this->url->path.'?'.$this->getParsString();
    }

    public function getPageName() {
        return $this->getPath()[0];
    }

    public function getPageId() : int {
        if($this->getPageName()){
            $page = new \Page(0);
            $page->getPageByName($this->getPageName());
            if($page->pageId()){
                return $page->pageId();
            }else{
                return 0;
            }
        }
        return 1;
    }

    public function getPageItemId() : int {
        if($item_id = $this->getPath()[1]){
            return $item_id;
        }
        return 0;
    }

    public function getPage() {
        return new \Page($this->getPageId());
    }

    public function goByRoute($path) {
        header("Location: ".$this->getDomain().'/'.$path);
    }

    public function getWay() {
        /*delete if need to get main page*/
        (!$_COOKIE['member_id']) ? $_COOKIE['member_id'] = 0 : '';
        $user = new \Member($_COOKIE['member_id']);

        if(!$this->getPageId()){
            $this->goByRoute('404.php');
        }elseif(!$user->is_valid() && $this->getPageName() != 'auth'){
            $this->goByRoute('auth');
        }elseif(($user->is_valid() && $this->getPageName() == 'auth') || $this->getPageName() == ''){
            $this->goByRoute('offers');
        }else{
           if($_COOKIE['member_id'] == 141){
               //$this->goByRoute($this->getURL());
               //header("Location: ".$this->getURL());
           }
        }
    }



}