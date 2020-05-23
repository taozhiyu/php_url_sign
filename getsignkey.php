<?php
header("Content-type: text/html; charset=utf-8");
$user_id= $_GET['i'];//QQ号，5~15位，非0开头数字
$user_number=$_GET['n'];//大小写数字，20位...暂时没想好干啥
$user_exp= $_GET['e'];//过期时间，时间戳，十位数字，永久为十个9
$My_key='taozhiyu';
if(!preg_match("/^[1-9]\\d{4,14}$/",$user_id)||
    !preg_match("/^\\d{10}$/",$user_exp)||
    !preg_match("/^\\d+$/",$user_number)){
    //参数错误
    echo "参数错误！";
    return;
}
echo getSign($user_id,$user_number,$user_exp,$user_sign,$My_key);

function getSign($user_id,$user_number,$user_exp,$user_sign,$My_key){
    return substr(md5(substr(md5(md5($user_id . $user_number). $user_exp), 0, 5)  . $My_key), 0, 30);
}

?>
