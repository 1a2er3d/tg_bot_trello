<?php
// Telegram Bot Settings
$token = "6602026779:AAHG2dGCENjO3lKiZ3V9CVKtDzWl1ltfI14";
$bot_url = "tg_forpm_bot";
$link = "https://1e2e3d.pp.ua/index.php";

// Trello Settings
$trello_apikey = "3c0b31608745fce8c50d92b4f174badf";
$trello_token = "ATTA8016d3823e281494f43e899ebc20b23e9427ac8869e2d1ab08b7742a081c5bb6EECC3606";
$trello_callback = "https://1e2er3d.pp.ua/index.php";
$trello_board_id = "anflgxhg";
$listname = "InProgress";
$authUrl = "https://trello.com/1/authorize"
    . "?response_type=token"
    . "&key={$trello_apikey}"
    . "&scope=read,write"
    . "&expiration=never";

// Database Settings
$dbHost = "localhost";
$dbUser = "a1e2er3d_bot";
$dbPass = "W{62X4jfUG,6";
$dbName = "a1e2er3d_bot";

// DB Connect
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($mysqli->connect_error) {
    die("Помилка підключення до бази даних: " . $mysqli->connect_error);
}
