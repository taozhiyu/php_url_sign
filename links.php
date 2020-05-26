<?php
header("Content-type: text/html; charset=utf-8");
$user_id = $_GET['i'];
//QQ号，5~15位，非0开头数字
$user_number = $_GET['n'];
//获取的数量
$user_exp = $_GET['e'];
//过期时间，时间戳，十位数字，永久为十个9
$user_sign = $_GET['s'];
//签名，小写字母+数字，30位
$My_key = '这里是个人混淆参数（可以这样理解吧，需要把获取签名和订阅的两个地方的设置成相同参数）';
$LinkAll = 0;
$isError=false;
if (!preg_match("/^[1-9]\\d{4,14}$/",$user_id)||
    !preg_match("/^\\d{10}$/",$user_exp)||
    !preg_match("/^\\d+$/",$user_number)||
    !preg_match("/^[0-9a-z]{30}$/",$user_sign)||
    getSign($user_id, $user_number, $user_exp, $My_key) != $user_sign) {
    echo base64_encode(retMsg(array("参数错误！", "请联系作者涛之雨", "QQ：2333333333")));
    return;
}
if ((int)$user_exp < time()) {
    echo base64_encode(retMsg(array("您的VIP已过期", "请联系作者涛之雨", "QQ：2333333333")));
    return;
}
$links=getlinks($user_number);
if($isError){
    $retCode=retMsg($links);
} else{
    $retCode=implode($links, "\n");
}
echo base64_encode(retMsg(array("您的ID：" . $user_id, preg_match("/9{10}/", $user_exp) ? "您是尊贵的永久VIP" : "您的VIP到期时间" . date("Y-m-d H:i", $user_exp),  $user_number=="999999"?"读取节点个数:全部":"读取节点个数：" .$user_number . "/" . $LinkAll, "刷新即可随机获取节点",  "如果节点失效，请尝试更换节点")) . PHP_EOL . $retCode);
function getSign($user_id, $user_number, $user_exp, $My_key) {
    return substr(md5(substr(md5(md5($user_id . $user_number) . $user_exp), 0, 5) . $My_key), 0, 30);
}
function retMsg($code) {
    $modle = "";
    $arrlength = count($code);
    for ($x = 0;$x < $arrlength;$x++) {
        $modle.= 'vmess://' . base64_encode('{"add":"公告——此线路无法连接","aid":"1","host":"ssr.xm0.top","id":"' . getUuid() . '","net":"ws","path":"","port":"0","ps":"' . $code[$x] . '","tls":"none","type":"","v":"2"}') . PHP_EOL;
    }
    return $modle;
}
function getUuid() {
    $chars = md5(uniqid(mt_rand(), true));
    $uuid = substr($chars, 0, 8) . '-' . substr($chars, 8, 4) . '-' . substr($chars, 12, 4) . '-' . substr($chars, 16, 4) . '-' . substr($chars, 20, 12);
    return $uuid;
}
function randLink($min, $max, $num, $links) {
    $count = 0;
    $return = array();
    while ($count < $num) {
        $return[] = $links[mt_rand($min, $max) ];
        $return = array_flip(array_flip($return));
        $count = count($return);
    }
    //打乱数组，重新赋予数组新的下标
    shuffle($return);
    return $return;
}
function getlinks($user_number) {
    $file = "mylinks.txt";
    //判断该文件是否存在
    if (file_exists($file)) {
        $fp = fopen($file, "a+"); //已追加的方式打开
        $file_size = filesize($file); //得到文件的大小，单位：字节
        //使用fread函数
        $res = fread($fp, $file_size);
        fclose($fp);
        //将读取的内容数据的处理
        $res_new = str_replace("\r\n", "\n", $res);
        $arr = explode("\n", $res_new);
        $GLOBALS['LinkAll'] = count($arr);
        if ($user_number == "666666") {
            return $arr;
        }elseif (count($arr) < (int)$user_number - 1) {
            $GLOBALS['isError'] = true;
            return array("服务器节点数据读取错误！请稍后刷新重试", "如果多次尝试后请联系作者涛之雨", "QQ：2333333333");
        } else {
            $arr2 = randLink(0, count($arr) - 1, (int)$user_number, $arr);
            return $arr2;
        }
    } else {
        $GLOBALS['isError'] =true;
        return array("服务器节点数据读取错误！请稍后刷新重试", "如果多次尝试后请联系作者涛之雨", "QQ：2333333333");
    }
}
?>
