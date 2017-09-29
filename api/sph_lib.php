<?php

require_once 'config.php';

date_default_timezone_set("Asia/Shanghai");

function db_connect()
{
    $db_connection = new PDO('mysql:host='.DB_HOSTNAME.';dbname='.DB_DATABASE, DB_USERNAME, DB_PASSWORD, 
		array(PDO::ATTR_PERSISTENT => false));
    $db_connection->exec("set names 'utf8'");
	return $db_connection;

}
function db_close($db_connect)
{
	$db_connect=null;
}

function msg_debug($text)
{
	$text=date("Y-m-d H:i:s")." ".$_SERVER['SCRIPT_NAME']." Debug: ".$text."\r\n";
	error_log($text, 3, "webroot.log");
}
function msg_info($text)
{
	$text=date("Y-m-d H:i:s")." ".$_SERVER['SCRIPT_NAME']." Info: ".$text."\r\n";
	error_log($text, 3, "webroot.log");
}
function msg_error($text)
{
	$text=date("Y-m-d H:i:s")." ".$_SERVER['SCRIPT_NAME']." Error: ".$text."\r\n";
	error_log($text, 3, "webroot.log");
}

function get($key)
{
    if (isset($_GET[$key])==false){
        if (isset($_POST[$key]))
            return $_POST[$key];
        else
            return null;
    }
    else
        return $_GET[$key];
}

function http_post($url,$data){
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    if($data != null){
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    }
    curl_setopt($curl, CURLOPT_TIMEOUT, 300); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $info = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
       	msg_error('Errno:'.curl_getinfo($curl));
    }
    return $info;
}

?>
