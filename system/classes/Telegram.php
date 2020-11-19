<?php

class Telegram
{

    public function __construct($token){
        $this->token = $token;
        $this->url = "https://api.telegram.org/bot$token/";
    }

    public function getToken(){
        return $this->token;
    }

    public function getUpdates(){
        if($response = file_get_contents($this->url."getUpdates")){
            return $response;
        }else{
            return false;
        }
    }

    public function soundToText($file_id){
        $token_bot = '542474368:AAEm9QSHOQpOgvoKgvsrsFPOnmYhXHwmwjU';

        $request = curl_init('https://api.wit.ai/speech');
        $headers[] = 'Authorization: Bearer ZQIRWHQ6G7LXHDLIXWCBX4F3HEUXQ5M3';
        $headers[] = 'Content-Type: audio/mpeg3';
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, "POST");
        //curl_setopt($request, CURLOPT_POSTFIELDS, file_get_contents($this->getFileUrl($file_id)));
        curl_setopt($request, CURLOPT_POSTFIELDS, file_get_contents($_SERVER['DOCUMENT_ROOT']."/speechtest.mp3"));
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $return = json_decode(curl_exec($request));
        $resp_text = $return->_text;
        return $resp_text;
    }

    public function getFileUrl($file_id){
        if($response = file_get_contents($this->url."getFile?file_id=$file_id")){
            $response_obj = json_decode($response);
            $file_path = $response_obj->result->file_path;
            return 'https://api.telegram.org/file/bot'.$this->token.'/'.$file_path;
        }else{
            return false;
        }
    }

    public function sendMessage($msg, int $chat){
        if(file_get_contents($this->url."sendmessage?parse_mode=HTML&text=$msg&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function sendPhoto($msg, int $chat){
        if(file_get_contents($this->url."sendPhoto?parse_mode=HTML&photo=$msg&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function sendDocument($link, int $chat){
        if(file_get_contents($this->url."sendDocument?document=$link&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function sendLocation($latitude, $longitude, int $chat){
        if(file_get_contents($this->url."sendLocation?latitude=$latitude&longitude=$longitude&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function sendLiveLocation($latitude, $longitude, int $livePeriod, int $chat){
        if(file_get_contents($this->url."sendLocation?latitude=$latitude&longitude=$longitude&live_period=$livePeriod&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function editLiveLocation($latitude, $longitude, int $message_id, int $chat){
        if(file_get_contents($this->url."editMessageLiveLocation?latitude=$latitude&longitude=$longitude&message_id=$message_id&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function sendVenue($latitude, $longitude, $title, $address, $chat){
        if(file_get_contents($this->url."sendVenue?latitude=$latitude&longitude=$longitude$title=$title$address=$address&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function sendContact($phone_number, $first_name, $last_name, $chat){
        if(file_get_contents($this->url."sendContact?phone_number=$phone_number&first_name=$first_name&last_name=$last_name&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function setChatPhoto($photo, $chat){
        if(file_get_contents($this->url."setChatPhoto?photo=$message_id&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function setChaTitle($title, $chat){
        if(file_get_contents($this->url."setChaTitle?title=$title&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function setChatDescription($description, $chat){
        if(file_get_contents($this->url."setChatDescription?description=$description&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function pinChatMessage($message_id, $chat){
        if(file_get_contents($this->url."pinChatMessage?message_id=$message_id&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function unpinChatMessage($chat){
        if(file_get_contents($this->url."unpinChatMessage?&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function sendPoll($question, array $options, $chat){
        if(file_get_contents($this->url."sendPoll?question=$question&options=$options&chat_id=$chat")){
            return true;
        }else{
            return false;
        }
    }

    public function getUserProfilePhotos($user_id){
        return file_get_contents($this->url."getUserProfilePhotos?user_id=$user_id");
    }
}