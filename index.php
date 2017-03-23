<?php

/**
 * 项目入口文件
 */
/*if(substr($_SERVER['SERVER_NAME'],0,4)!='www.'){
	header('HTTP/1.1 301 Moved Permanently');
	header('Location:http://www.'.$_SERVER['SERVER_NAME']);
	exit();
}*/
define("APP_DEBUG", true);
define('SITE_PATH', getcwd());
define('APP_NAME', 'Thinkcore');
define('APP_PATH', SITE_PATH . '/Thinkcore/');
define("RUNTIME_PATH", SITE_PATH . "/#runtime/");
define('TEMPLATE_PATH', APP_PATH . 'Template/');
define("LANGUAGE", 'CH');
//大小写忽略处理
foreach (array("g", "m") as $v) {
    if (isset($_GET[$v])) {
        $_GET[$v] = ucwords($_GET[$v]);
    }
}


//载入框架核心文件
require APP_PATH . 'Thinkphp/ThinkPHP.php';