<?php
use Workerman\Worker;
require_once '../Workerman-master/Autoloader.php';
$worker = new Worker('websocket://0.0.0.0:8686');
$worker->count = 1;
// $rooms[1][] = '四娃'
// 结构：二维数组
// 下标：房间的ID
// 值：保存房间中所有的客户端
// */
$rooms = [];
$worker->onConnect = function($connection){
    $connection->onWebSocketConnect = function ($connection, $http_header) {
        // 保存这个客户端所在的房间号
    $connection->room_id = $_GET['room_id'];
    global $rooms;
    $rooms[$_GET['room_id']][] = $connection;
};
};
// 接收消息
$worker->onMessage = function($connection,$data){
    global $worker, $rooms;
    //  转发消息给其他客户端  循环所有客户端 判断是否和发消息的客户端是同一个room_id 如果是给他们发消息
    foreach($rooms[$connection->room_id] as $c){
         // 获取当前信息是哪个房间的     
            $c->send($data);
    }
};
$worker->onClose = function($connection ){
    // 从房间数组中删除这个客户端
    global $rooms;
    foreach($rooms[$connection->room_id] as $k=>$c){
        if($c==$connection){
            unset($rooms[$connection->room_id[$k]]);
        }
    }
};
Worker::runAll();