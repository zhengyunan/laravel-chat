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
$users = [];
$worker->onConnect = function($connection){
    $connection->onWebSocketConnect = function ($connection, $http_header) {
       
    // echo $_GET['token'];
    // 解析token 获取当前的用户信息
    $connection->jwt = $_GET['token'];
    // $connection->id = $_GET['id'];
    // 保存当前用户信息
    global $users ,$worker;
    $users[$_GET['token']] = $_GET['token'];
    foreach($worker->connections as $c){
        $c->send(json_encode([
            'type'=>'users',
            'users'=>$users
        ]));
    }
};
};
// 接收消息
$worker->onMessage = function($connection,$data){
    //  转发消息给其他客户端  循环所有客户端 给他们发消息
    global $worker;
    foreach($worker->connections as $c){
        $c->send(json_encode([
            'type'=>'message',
            'message'=>$data
        ]));
    }
};
// $worker->onClose = function($connection ){
//     // 从房间数组中删除这个客户端
//     global $rooms;
//     foreach($rooms[$connection->room_id] as $k=>$c){
//         if($c==$connection){
//             unset($rooms[$connection->room_id[$k]]);
//         }
//     }
// };
Worker::runAll();