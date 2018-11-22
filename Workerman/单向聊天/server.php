<?php
use Workerman\Worker;
use Firebase\JWT\JWT;
require_once '../Workerman-master/Autoloader.php';
require('./vendor/autoload.php');
$worker = new Worker('websocket://0.0.0.0:8686');
$worker->count = 1;
// $rooms[1][] = '四娃'
// 结构：二维数组
// 下标：房间的ID
// 值：保存房间中所有的客户端
// */
$users = [];
$userConn = [];
$worker->onConnect = function($connection){
    $connection->onWebSocketConnect = function ($connection, $http_header) {
        // 保存这个客户端所在的房间号
    // echo $_GET['token'];
    // 解析token 获取当前的用户信息
    // 解析令牌
    try
    {  
        global $users, $worker, $userConn;
        $key = "example_key";
        $data = JWT::decode($_GET['token'], $key, array('HS256'));
        $connection->uid = $data->id;
        $connection->uname = $data->name;
        // 保存用户到数组中
        $users[$data->id] = $data->name;
         // 把当前这个客户端的对象保存到数组中，用户ID是下标
        $userConn[$data->id] = $connection;
        // 打印解析出来的数据
        // var_dump($data);
        
        // 若果用户连接成功就告诉其他客户端
        foreach($worker->connections as $c){
        $c->send(json_encode([
            'type'=>'users',
            'users'=>$users
        ]));
    }
    }
    catch(  \Firebase\JWT\ExpiredException $e)
    {   
        
        $connection->close();
    }
    catch( \Exception $e)
    {
        $connection->close();
    }
    // $connection->id = $_GET['id'];
    // 保存当前用户信息
    
    
};
};
// 接收消息
$worker->onMessage = function($connection,$data){
    //  转发消息给其他客户端  循环所有客户端 给他们发消息
    global $worker;
      /* 从消息中解析出第一个:前面的内容，以判断是群发还是单发 */
    // 根据 : 将字符串转成数组
    $ret = explode(':',$data);
     // 取出第一个元素（第一个:前面的内容
    $code = $ret[0];
    unset($ret[0]);
    $rawData = implode(':', $ret);
    if($code=='all'){
        foreach($worker->connections as $c){
        $c->send(json_encode([
            'type'=>'message',
            'message'=>$rawData
        ]));
    }
    }else{
         // 引入保存所有客户端与用户ID对应关系的数组
         global $userConn;
         // 根据用户ID找到对应的客户端对象，
         // 然后调用这个对象的 send 方法给这个客户端发消息
         $userConn[$code]->send(json_encode([
             'type'=>'message',
             'message'=>$rawData
         ]));
    }
    
};
$worker->onClose = function($connection ){
    // 删除这个用户
    global $users, $worker;
    
    // 根据用户id从数组中删除
    unset($users[$connection->uid]);

    // 通知所有其它用户
    foreach($worker->connections as $c)
    {
        $c->send(json_encode([
            'type'=>'users',
            'users'=>$users,
        ]));
    }
};
Worker::runAll();