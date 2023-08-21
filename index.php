<?php
    require ('config.php');
$update = json_decode(file_get_contents("php://input"), TRUE);

    require ('inputs.php');
    require ('functions.php');

if (isset($update['action'])) {
    
    $actionData = $update['action'];

    if ($actionData['type'] === 'updateCard' &&
        isset($actionData['data']['listBefore']) && isset($actionData['data']['listAfter'])) {
        $listBefore = $actionData['data']['listBefore']['name'];
        $listAfter = $actionData['data']['listAfter']['name'];

        if (($listBefore == 'InProgress' && $listAfter == 'Done') ||
            ($listBefore == 'Done' && $listAfter == 'InProgress')) {

            $cardName = $update['action']['data']['card']['name'];
            $messagebot = "Карточка '".$cardName."' була переміщена з {$listBefore} до {$listAfter}";
            /* Можливо тут треба було зробити записи в базу */

            $id = $id_group;
            sendMessage();
        }
    }
}

$query = "SELECT * FROM `users` WHERE `user_id`='{$user_id}'";
$result = $mysqli->query($query);

    $user = $result->fetch_assoc();

$check_token = getTrelloToken();


if (isset($update['callback_query'])) {

if(isset($user)){ 
    if($user['is_pm'] == 0 || !isset($check_token['id']))exit();
    $user_token = $user['trello_key']; 
} 

    if ($callbackData == '/reports') {

getTrelloData("https://api.trello.com/1/tokens/{$user_token}/webhooks?key={$trello_apikey}&callbackURL={$trello_callback}&idModel={$trello_board_id}");


$lists_data = getTrelloData("https://api.trello.com/1/boards/{$trello_board_id}/lists?key={$trello_apikey}&token={$user_token}");

if (!empty($lists_data)) {
    $list_id = '';
    foreach ($lists_data as $list) {
        if ($list['name'] == $listname) {
            $list_id = $list['id'];
            break;
        }
    }

    if ($list_id != '') {
        
        $members_with_tasks = [];


    $cards_data = getTrelloData("https://api.trello.com/1/lists/{$list_id}/cards?key={$trello_apikey}&token={$user_token}");


        if (!empty($cards_data)) {
            foreach ($cards_data as $card) {
                $members = $card['idMembers'];
                foreach ($members as $member_id) {
                    if (!isset($members_with_tasks[$member_id])) {
                        $members_with_tasks[$member_id] = 1;
                    } else {
                        $members_with_tasks[$member_id]++;
                    }
                }
            }
        }


        $members_data = getTrelloData("https://api.trello.com/1/boards/{$trello_board_id}/members?key={$trello_apikey}&token={$user_token}");
        
        $messagebot = "Звіт по кількості задач у статусі '{$listname}':\n";
        
        $i = 0;
        foreach ($members_with_tasks as $member_id => $task_count) {

            foreach ($members_data as $member) {
                
                if ($member['id'] == $member_id) {
                    $messagebot .= "- {$member['fullName']}: {$task_count} задач\n";
                    $i++;
                }
            }
        }
    if($i == 0)$messagebot .= "Немає задач в цьому статусі";
    sendMessage();

    }
    
}

}
}



$isBotCommand = isset($data['entities']) && $data['entities'][0]['type'] === 'bot_command';


if(!$isBotCommand && $type == "private") {

$data = getTrelloData("https://api.trello.com/1/members/me?key={$trello_apikey}&token={$message}");

if(isset($data['id'])) {

if(isset($user)) {

$user_token = getTrelloToken();

if(!isset($user_token['id']) || $user['trello_key'] == NULL || $user['trello_key'] != $message) { 
    $mysqli->query("UPDATE `users` SET `trello_key`='{$message}',`fullName`='{$data['fullName']}' WHERE `user_id`={$user_id}");
    $messagebot = "Успішна прив'язка '{$data['fullName']}' до акаунту телеграм";
}
else $messagebot = "Ви вже увійшли в Trello як '{$data['fullName']}'";

}

    sendMessage();

}
}


if($message == "/start") {

if(!isset($user)){
    $query = "INSERT INTO `users` (`username`, `chat_id`, `user_id`) VALUES (?, ?, ?)";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sss", $username, $id, $user_id); 
    $stmt->execute();
    $stmt->close();
    $lastInsertId = $mysqli->insert_id;
    if($lastInsertId == 1){ $mysqli->query("UPDATE `users` SET `is_pm`=1 WHERE `id`=1"); }
    
$query = "SELECT `is_pm`,`trello_key`, `fullName` FROM `users` WHERE `user_id`='{$user_id}'";
$result = $mysqli->query($query);

    $user = $result->fetch_assoc();
}


if(isset($user['trello_key'])) {

$user_token = getTrelloToken();

if(!isset($user_token) && $type == "private"){ 
    $mysqli->query("UPDATE `users` SET `trello_key`=NULL WHERE `user_id`='{$user_id}'");
$messagebot = "Привіт {$names}!
Твій ключ давно застарів, треба оновити - /start"; 

}


if($user['is_pm'] == 1) {


$text = "Звіт";


$keyboard = [
    "inline_keyboard" => [
        [
            ["text" => $text, "callback_data" => "/reports"]
        ]
    ]
];

$keyboard = json_encode($keyboard);
    $messagebot = "Привіт {$names}!
Тисни нижче якщо хочеш отримати звіт по роботі в Trello 
    ";

    } else if($type == "private"){ 
   
        $messagebot = "Привіт {$names}! 
Ти авторизувався в Trello"; 
    
    }

} else {
    if($type == "private"){
$text = "Прив'язати Trello";

$keyboard = [
    "inline_keyboard" => [
        [
            ["text" => $text, "url" => $authUrl]
        ]
    ]
];

$keyboard = json_encode($keyboard);
    $messagebot = "Привіт {$names}!
Для подальшої роботи тобі потрібно авторизуватись і прив'язати Trello, ти можеш це зробити за посиланням, отриманий token напиши у відповідь";
    } else $messagebot = "Тобі треба увійти в аккаунт Trello, але в групі це робити небезпечно. Напиши мені @{$bot_url} - /start"; 
    
}

if(isset($messagebot))sendMessage();

}


