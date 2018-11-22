<?php
require('./vendor/autoload.php');
use Firebase\JWT\JWT;
$pdo = new \PDO('mysql:host=127.0.0.1;dbname=chat','root','');
$pdo->exec('SET NAMES utf8');
// 接收原始数据
$post = file_get_contents('php://input');
// 把JSON转成数组
$_POST = json_decode($post, TRUE);
$stmt = $pdo->prepare("SELECT * FROM user WHERE username=? AND password=?");
$stmt->execute([
      $_POST['username'],
      md5($_POST['password']),
]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if($user){
    $key = "example_key";
    $token = [
        'id'=>$user['id'],
        'name'=>$user['username']
    ];
$jwt = JWT::encode($token, $key);
    echo json_encode([
        'code'=>200,
        'jwt'=>$jwt,
    ]);
}else{
    echo json_encode([
        'code'=>500,
        'error'=>'用户名或密码错误',
    ]);
}



    