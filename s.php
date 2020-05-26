<?php
header("Content-type: text/html; charset=utf-8");
$user_id= $_GET['i'];//QQ号，5~15位，非0开头数字
$user_number=$_GET['n'];//10位数字，过期时间
$user_exp= $_GET['e'];//过期时间，时间戳，十位数字，永久为十个9
$user_key= $_GET['k'];//这里是固定值，防止被后台地址泄露
$user_isShorten= $_GET['u'];//是否生成短连接
$url="http://192.168.1.4:9999/v2.php";
$shortenUrl="这里是短网址的api，我采用的是yourls";
$My_key='这里是个人混淆参数（可以这样理解吧，需要把获取签名和订阅的两个地方的设置成相同参数）';
if(!preg_match("/^[1-9]\\d{4,14}$/",$user_id)||
    !preg_match("/^\\d{9,10}$/",$user_exp)||
    !preg_match("/^\\d+$/",$user_number)||
    $user_key!="1837"){
    //最后这个k防止被后台地址泄露
    echo "参数错误！";
    return;
}
if($user_isShorten=="1"){
    $apistr=file_get_contents($shortenUrl.$url . "?i=".urlencode(  $user_id . "&n=" . $user_number . "&e=". $user_exp . "&s=" . getSign($user_id,$user_number,$user_exp,$user_sign,$My_key)));
    //echo $apistr.PHP_EOL."---------------------".PHP_EOL;
    $apiobj=simplexml_load_string($apistr);//解析xml代码
    //var_dump($apiobj);
$surl=(string)$apiobj->shorturl;//yourls的api范湖的xml数据解析出
echo $surl;
}else{
    echo $url . "?i=".  $user_id . "&n=" . $user_number . "&e=". $user_exp . "&s=" . getSign($user_id,$user_number,$user_exp,$user_sign,$My_key);
}
function getSign($user_id,$user_number,$user_exp,$user_sign,$My_key){
    return substr (md5(md5(substr (md5($user_id.$user_number),0,5).$user_exp).$My_key),0,30);//算法随便改，只要获取签名和订阅的两个地方的设置成相同方法就成
}

function getShortenUrl($url, $args=null, $method="post", $withCookie = false, $timeout = CURL_TIMEOUT, $headers=array())
{
    $ch = curl_init();
    $data = convert($args);
    if($data){
        if(stripos($url, "?") > 0){
            $url .= "&$data";
        }else{
            $url .= "?$data";
        }
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($headers)) 
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($withCookie)
    {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
    }
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}

?>
