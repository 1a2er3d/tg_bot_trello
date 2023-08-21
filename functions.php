<?php
function getTrelloData($url) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

function getTrelloToken(){
    global $user,$trello_apikey;
    $url = "https://api.trello.com/1/members/me?key={$trello_apikey}&token={$user['trello_key']}";
    return getTrelloData($url);
}


function sendMessage() {
    global $token,$id,$messagebot,$keyboard;
if(isset($keyboard)) {   
    $param = [
        'chat_id' => $id,
        'text' => $messagebot,
        'reply_markup' => $keyboard
        ];
} else { 
  $param = [
    'chat_id' => $id, 
    'text' => $messagebot
    ]; 
} 
    $url = "https://api.telegram.org/bot{$token}/sendMessage?".http_build_query($param);
    $response = @file_get_contents($url);
    exit();
}
