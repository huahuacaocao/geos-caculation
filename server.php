<?php
define('APP_PATH', __DIR__);
require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$server = new Swoole\Http\Server('127.0.0.1', 10001, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);

$server->on('request', [new \Lib\RequestCallback(), 'onRequest']);
$server->start();

