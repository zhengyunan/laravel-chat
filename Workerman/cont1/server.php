<?php
use Workerman\Worker;
require_once '../Workerman-master/Autoloader.php';
// 绑定端口
$worker = new Worker('websocket://0.0.0.0:8686');
$worker->count = 1;
$worker->onConnect = function($connection){
    $connection->send('欢迎您');
};

Worker::runAll();
?>