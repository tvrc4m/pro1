<?php

// 错误级别
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
// error_reporting(0);

if($_SERVER['REQUEST_METHOD']=='OPTIONS') exit('ok');

define('ROOT',dirname(__FILE__));
// 设置时区
date_default_timezone_set('Asia/Shanghai');

include_once(ROOT.'/config/const.php');
include_once(CORE.'function.php');
include_once(CORE.'base.class.php');
include_once(CORE.'db.class.php');
include_once(CORE.'base.router.php');

// session_set_cookie_params(86400,'/',COOKIE_DOMAIN);
session_start();

$router=new Router($_SERVER['PATH_INFO']);

$router->dispatch();