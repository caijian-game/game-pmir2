<?php

use App\Libs\Math_BigInteger;
use Hyperf\Utils\Context;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Server;
use App\Controller\ObjectService;

if (!function_exists('getClientIp')) {
    function getClientIp()
    {
        try {
            /**
             * @var ServerRequestInterface $request
             */
            $request = Context::get(ServerRequestInterface::class);
            $ip_addr = $request->getHeaderLine('x-forwarded-for');
            if (verifyIp($ip_addr)) {
                return $ip_addr;
            }
            $ip_addr = $request->getHeaderLine('remote-host');
            if (verifyIp($ip_addr)) {
                return $ip_addr;
            }
            $ip_addr = $request->getHeaderLine('x-real-ip');
            if (verifyIp($ip_addr)) {
                return $ip_addr;
            }
            $ip_addr = $request->getServerParams()['remote_addr'] ?? '0.0.0.0';
            if (verifyIp($ip_addr)) {
                return $ip_addr;
            }
        } catch (Throwable $e) {
            return '0.0.0.0';
        }
        return '0.0.0.0';
    }
}

if (!function_exists('verifyIp')) {
    function verifyIp($realip)
    {
        return filter_var($realip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
}

/**
 * 获取随机6为字符串
 * @param int $length
 * @param int $type
 * @return string
 */
function get_rand_code($length = 5, $type = 0)
{
    $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
    if ($type == 0) {
        array_pop($arr);
        $string = implode("", $arr);
    } elseif ($type == "-1") {
        $string = implode("", $arr);
    } else {
        $string = $arr[$type];
    }
    $count = strlen($string) - 1;
    $code  = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $string[rand(0, $count)];
    }
    return $code;
}

/**
 * 加密算法
 * @param $password
 * @param $salt
 * @return string
 */
function get_password_md5($password, $salt)
{
    return md5($password . $salt);
}

//金额格式化
function num_format($num)
{

    if (!is_numeric($num)) {
        return false;
    }

    if ($num < 1) {
        return $num;
    }

    $num    = explode('.', $num); //把整数和小数分开
    $rl     = isset($num[1]) ? $num[1] : ''; //小数部分的值
    $j      = strlen($num[0]) % 3; //整数有多少位
    $sl     = substr($num[0], 0, $j); //前面不满三位的数取出来
    $sr     = substr($num[0], $j); //后面的满三位的数取出来
    $i      = 0;
    $rvalue = '';
    while ($i <= strlen($sr)) {
        $rvalue = $rvalue . ',' . substr($sr, $i, 3); //三位三位取出再合并，按逗号隔开
        $i      = $i + 3;
    }

    $rvalue = $sl . $rvalue;
    $rvalue = substr($rvalue, 0, strlen($rvalue) - 1); //去掉最后一个逗号
    $rvalue = explode(',', $rvalue); //分解成数组
    if ($rvalue[0] == 0) {
        array_shift($rvalue); //如果第一个元素为0，删除第一个元素
    }

    if (isset($rvalue[0]) == false) {
        return 0;
    }

    $rv = $rvalue[0]; //前面不满三位的数
    for ($i = 1; $i < count($rvalue); $i++) {
        $rv = $rv . ',' . $rvalue[$i];
    }

    if (!empty($rl)) {
        $rvalue = $rv . '.' . $rl; //小数不为空，整数和小数合并
    } else {
        $rvalue = $rv; //小数为空，只有整数
    }
    return $rvalue;
}

//保留两位小数不四舍五入
function num_two_2($num)
{
    return sprintf("%.2f", $num);
}

//百分比
function percent($num)
{
    $num = $num * 10000;
    $num = sprintf("%.2f", $num);
    $num = $num / 100;
    $num = sprintf("%.2f", $num);
    return $num . '%';
}

/**
 * 生成uuid
 * @return string
 */
function getuuid()
{
    $uuid = '';
    if (function_exists('uuid_create') === true) {
        $uuid = uuid_create(1);
    } else {
        $data    = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        $uuid    = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    return $uuid;
}

/**
 * [getIP 获取客户端IP]
 * ------------------------------------------------------------------------------
 * @author
 * ------------------------------------------------------------------------------
 * @version date:2018-07-22
 * ------------------------------------------------------------------------------
 * @return  [type]          [description]
 */
function getIP()
{
    global $ip;
    if (getenv("HTTP_CLIENT_IP")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } else if (getenv("HTTP_X_FORWARDED_FOR")) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    } else if (getenv("REMOTE_ADDR")) {
        $ip = getenv("REMOTE_ADDR");
    } else {
        $ip = "Unknow";
    }

    return $ip;
}

//对象转数组
function objtoarray($obj)
{
    return json_decode(json_encode($obj), true);
}

/**
 * [secToTime 秒转换成时间格式]
 * ------------------------------------------------------------------------------
 * @author
 * ------------------------------------------------------------------------------
 * @version date:2018-11-08
 * ------------------------------------------------------------------------------
 * @return  [type]          [description]
 */
function secToTime($times)
{
    $result = '00:00:00';
    if ($times > 0) {
        $hour = floor($times / 3600);
        if ($hour < 10) {
            $hour = "0" . $hour;
        }
        $minute = floor(($times - 3600 * $hour) / 60);
        if ($minute < 10) {
            $minute = "0" . $minute;
        }
        $second = floor((($times - 3600 * $hour) - 60 * $minute) % 60);
        if ($second < 10) {
            $second = "0" . $second;
        }
        $result = $hour . ':' . $minute . ':' . $second;
    }
    return $result;
}

/**
 * [timeToSec 时分秒时长转换成秒]
 * ------------------------------------------------------------------------------
 * @author
 * ------------------------------------------------------------------------------
 * @version date:2018-11-20
 * ------------------------------------------------------------------------------
 * @return  [type]          [description]
 */
function timeToSec($times)
{
    $arr = explode(':', $times);

    $sec = 0;
    switch (count($arr)) {
        case 3:
            $sec = ($arr[0] * 3600) + ($arr[1] * 60) + $arr[2];
            break;

        case 2:
            $sec = ($arr[0] * 60) + $arr[1];
            break;

        case 1:
            $sec = $arr[0];
            break;

        default:
            # code...
            break;
    }

    return (int) $sec;
}

//获取一个8位唯一值随机数
function getnumber()
{
    return date('Ymd') + date('His') + substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

//过滤空数据
function filterparam($param = null)
{
    foreach ($param as $k => $v) {
        if ($v == null) {
            $param[$k] = '';
        }
    }

    return $param;
}

/**
 * [http_curl 获取]
 * ------------------------------------------------------------------------------
 * @author
 * ------------------------------------------------------------------------------
 * @version date:2018-06-12
 * ------------------------------------------------------------------------------
 * @param   [type]          $url [description]
 * @return  [type]               [description]
 *
 * 示例:
$param = [
//地址
'url'           => 'https:xxxxx',
//头信息(不传为默认)
'header'        => [
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng;q=0.8',
'Accept-Encoding: gzip, deflate, br',
'Accept-Language: zh-CN,zh;q=0.9',
'Cache-Control: no-cache',
],
//浏览器标识(不传为默认)
'user_agent'    => 'User-Agent: Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Mobile Safari/537.36',
'autoreferer'   => true,        //重定向多级跳转
'referer'       => 'https:xxx', //来源
'cookiepath'    => './coookie.log',     //发送和储存cookie文件
'showheader'    => true,        //是否显示返回头信息
'data'          => [
id => 1,
name => '张三'
],                  //post参数,get请求在url后拼接
'timeout'       => 30,          //超时时间
'https'         => true,        //是否开启HTTPS请求
'returndecode'  => false;       //是否需要json解析, true为解析,false不解析
'proxy'         => [
'127.0.0.1',                //代理服务器地址
'8888',                     //端口
'admin',                    //账户
'admin'                     //密码
],

];

http_curl($param);//curl
 */
function http_curl($param = [])
{
    if (!$param || !$param['url']) {
        return false;
    }

    // 初始化
    $ch = curl_init();

    // 设置浏览器的特定header
    $header = [
        "Connection: keep-alive",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
        "Upgrade-Insecure-Requests: 1",
        "DNT:1",
        "Accept-Language: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
    ];

    if (!empty($param['header'])) {
        $header = $param['header'];
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    //访问网页
    curl_setopt($ch, CURLOPT_URL, $param['url']);

    //代理服务器设置
    if (!empty($param['proxy'])) {
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
        curl_setopt($ch, CURLOPT_PROXY, $param['proxy'][0]); //代理服务器地址
        curl_setopt($ch, CURLOPT_PROXYPORT, $param['proxy'][1]); //代理服务器端口
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $param['proxy'][2] . ":" . $param['proxy'][3]); //http代理认证帐号，username:password的格式
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用SOCKS5代理模式
    }

    //浏览器设置
    $user_agent = 'User-Agent: Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Mobile Safari/537.36';
    if (!empty($param['user_agent'])) {
        $user_agent = $param['user_agent'];
    }

    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

    if (!empty($param['autoreferer'])) {
        //重定向
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //多级自动跳转
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        //设置跳转location 最多10次
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    }

    //来源
    if (!empty($param['referer'])) {
        curl_setopt($ch, CURLOPT_REFERER, $param['referer']);
    }

    //cookie设置
    if (!empty($param['cookiepath'])) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $param['cookiepath']); //存储cookies
        curl_setopt($ch, CURLOPT_COOKIEFILE, $param['cookiepath']); //发送cookie
    }

    //是否显示头信息
    if (!empty($param['showheader'])) {
        curl_setopt($ch, CURLOPT_HEADER, 1);
    }

    //是否post提交
    if (!empty($param['data'])) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); // 请求方式
        curl_setopt($ch, CURLOPT_POST, true); // post提交
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param['data']); // post的变量
    }

    //超时设置
    $timeout = isset($param['timeout']) && (int) $param['timeout'] ? $param['timeout'] : 30;
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    //是否为https请求
    if (!empty($param['https'])) {
        // 针对https的设置
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    }

    //获取内容不直接输出
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // 执行
    $response = curl_exec($ch);

    //关闭
    curl_close($ch);

    if (!empty($param['returndecode'])) {
        $response = json_decode($response, true);
    }

    return $response;
}

// 创建本地目录
function mkdirpath($path = null)
{
    if (!$path) {
        return false;
    }

    if (file_exists($path) == false) {
        mkdir($path, 0777, 'recursive'); //创建目录 所有者仅可读写（6），组及其他无任何权限（0）
        chmod($path, 0777);
    }
}

//生成hascode
function hashCode($str)
{
    if (empty($str)) {
        return '';
    }

    $str  = strtoupper($str);
    $mdv  = md5($str);
    $mdv1 = substr($mdv, 0, 16);
    $mdv2 = substr($mdv, 16, 16);
    $crc1 = abs(crc32($mdv1));
    $crc2 = abs(crc32($mdv2));
    return bcmul($crc1, $crc2);
}

//时间戳转换为周几
function getTimeWeek($time, $i = 0)
{
    $weekarray = array(1, 2, 3, 4, 5, 6, 7);
    $oneD      = 24 * 60 * 60;
    return $weekarray[date("w", $time + $oneD * $i)];
}

if (!function_exists('EchoLog')) {
    function EchoLog($string = null, $type = null, $sysDebug = false, $save = null, $fileName = 'swoole.log')
    {
        if (is_array($string)) {
            $str = $string = var_export($string, true) . PHP_EOL;
        }

        switch ($type) {

            //success
            case 's':
                $str = "\033[1;36m[" . date('Y-m-d H:i:s') . " SUCCESS]\033[0m \033[3;37m" . $string;
                break;

            //warning
            case 'w':
                $str = "\033[1;33m[" . date('Y-m-d H:i:s') . " WARNING]\033[0m \033[3;37m" . $string;
                break;

            //error
            case 'e':
                $str = "\033[5;31m[" . date('Y-m-d H:i:s') . " ERROR  ]\033[0m \033[3;37m" . $string;
                break;

            //info
            case 'i':
                $str = "\033[1;32m[" . date('Y-m-d H:i:s') . " INFO   ]\033[0m \033[3;37m" . $string;
                break;

            case 'rf':
                echo "\033[s\033[3;37m" . $string . "\033[0m\033[u";
                return true;
                break;

            default:
                $str = "\033[3;37m" . $string;
                break;
        }

        if (env('DEBUG', false) || $sysDebug) {
            echo $str . "\033[0m" . PHP_EOL;
        }
    }
}

if (!function_exists('bytesToString')) {
    //bytesToString
    function bytesToString($bytes)
    {
        $str = '';
        foreach ($bytes as $ch) {
            $str .= chr($ch); //这里用chr函数
        }
        return $str;
    }
}

if (!function_exists('stringToBytes')) {
    //stringToBytes
    function stringToBytes($string)
    {
        $bytes = [];
        for ($i = 0; $i < strlen($string); $i++) {
            //遍历每一个字符 用ord函数把它们拼接成一个php数组
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }
}

function String2Hex($string)
{
    $hex = '';
    for ($i = 0; $i < strlen($string); $i++) {
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}

function BigInteger($numstr = 0, $base = 10)
{
    return new Math_BigInteger($numstr, $base);
}

function getClientId($fd)
{
    $container = \Hyperf\Utils\ApplicationContext::getContainer();
    $Server    = $container->get(\Swoole\Server::class);
    return sha1($Server->getClientInfo($fd)['remote_ip'] . '_' . $fd);
}

function int8($i)
{
    return is_int($i) ? pack("c", $i) : unpack("c", $i)[1];
}

function uInt8($i)
{
    return is_int($i) ? pack("C", $i) : unpack("C", $i)[1];
}

function int16($i)
{
    return is_int($i) ? pack("s", $i) : unpack("s", $i)[1];
}

function uInt16($i, $endianness = false)
{
    $f = is_int($i) ? "pack" : "unpack";

    if ($endianness === true) {
        // big-endian
        $i = $f("n", $i);
    } else if ($endianness === false) {
        // little-endian
        $i = $f("v", $i);
    } else if ($endianness === null) {
        // machine byte order
        $i = $f("S", $i);
    }

    return is_array($i) ? $i[1] : $i;
}

function int32($i)
{
    return is_int($i) ? pack("l", $i) : unpack("l", $i)[1];
}

function uInt32($i, $endianness = false)
{
    $f = is_int($i) ? "pack" : "unpack";

    if ($endianness === true) {
        // big-endian
        $i = $f("N", $i);
    } else if ($endianness === false) {
        // little-endian
        $i = $f("V", $i);
    } else if ($endianness === null) {
        // machine byte order
        $i = $f("L", $i);
    }

    return is_array($i) ? $i[1] : $i;
}

function int64($i)
{
    return is_int($i) ? pack("q", $i) : unpack("q", $i)[1];
}

function uInt64($i, $endianness = false)
{
    $f = is_int($i) ? "pack" : "unpack";

    if ($endianness === true) {
        // big-endian
        $i = $f("J", $i);
    } else if ($endianness === false) {
        // little-endian
        $i = $f("P", $i);
    } else if ($endianness === null) {
        // machine byte order
        $i = $f("Q", $i);
    }

    return is_array($i) ? $i[1] : $i;
}

function toUint16($v)
{
    return toClamp($v, 0, 65535);
}

function toInt($v)
{
    return toClamp($v, -2147483648, 2147483647);
}

function toInt8($v)
{
    return toClamp($v, -128, 127);
}

function toUint8($v)
{
    return toClamp($v, 0, 255);
}

function toClamp($value, $min, $max)
{
    if ($value < $min) {
        return $min;
    } elseif ($value > $max) {
        return $max;
    } else {
        return $value;
    }
}

function AbsInt($i)
{
    if($i < 0)
    {
        return -$i;
    }

    return $i;
}

function getObject($objectName)
{
    return ObjectService::getObject($objectName);
}
