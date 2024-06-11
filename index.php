<?php
require_once 'vendor/autoload.php';
require_once 'app/Api.php';
require_once 'app/Tasks.php';
require_once 'app/Wallet.php';
require_once 'app/Display.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, 'getEnv.env');
$dotenv->load();
$apiKey = $_ENV['MY_API'];
$apiUrl = $_ENV['MY_URL'];
$tasks = new Tasks($apiKey, $apiUrl);
$wallet = new Wallet($apiKey, $apiUrl, 1000);
$wallet->load("transactions.json");
$show = new Display($tasks, $wallet);
while (true) {
    $show->getMenu();
    $userAction = (int)readline("Enter your action: ");
    $show->chooseAction($userAction);
}