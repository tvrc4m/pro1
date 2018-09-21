<?php

// 错误级别
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
// error_reporting(0);

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

if($_SERVER['REQUEST_METHOD']=='OPTIONS') exit('ok');

define('ROOT',dirname(__FILE__));
// 设置时区
date_default_timezone_set('Asia/Shanghai');

include_once(ROOT.'/config/const.php');
include_once(CORE.'function.php');
include_once(CORE.'base.class.php');
include_once(CORE.'db.class.php');
include_once(CORE.'base.router.php');

register_shutdown_function(function(){
    // print_r(error_get_last());
});

// session_set_cookie_params(86400,'/',COOKIE_DOMAIN);
session_start();

// $router=new Router($_SERVER['PATH_INFO']);
$router=new Router($_GET['action']);

$router->dispatch();