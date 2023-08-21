<?php


$data = $update['message'];

$dfrom = $data['from'];
$dchat = $data['chat'];

$id_group = "-949341037"; // це щось трохи костильне, група в телезі. міняти як в базі так і тут, тому тут (не зрозумів як по іншому)


$id = $dchat['id']; 
$type = $dchat['type']; 
$user_id = $dfrom['id'];

$message = $data['text']; 

$first_name = trim($dfrom['first_name']); 
$last_name = isset($dfrom['last_name'])?" ".trim($dfrom['last_name']):"";
$names = $first_name.$last_name;

$username = $dfrom['username'];

    if(isset($update['callback_query']))
    {
        $callbackQuery = $update['callback_query'];
        $id = $callbackQuery['message']['chat']['id'];
        $callbackData = $callbackQuery['data'];
        $message = $callbackData;
        $user_id = $callbackQuery['from']['id'];
    }
