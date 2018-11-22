<?php
use Workerman\Worker;
require_once '../Workerman-master/Autoloader.php';
$worker = new Worker('websocket://0.0.0.0:8686');
$worker->count = 1;
$worker->onConnect = function($connection){
    $connection->send('欢迎您');
};
// 接收消息
$worker->onMessage = function($connection,$data){
    //  转发消息给其他客户端  循环所有客户端 给他们发消息
    global $worker;
    foreach($worker->connections as $c){
        $c->send($data);
    }
};
Worker::runAll();
?>