<?php
/**
 * 后台入口
 * 功能：
 * 1、后台登陆，主框架
 * 2、权限划分
 * 3、数据字典管理
 * 4、用户管理
 */
session_start();
include_once "../../_base/config.php";
include_once Z_ROOT .'/ZApplet.php';

$cfg = new \Ziima\MVC\RouterConfig();
$cfg->debug = true;
$cfg->dir_code  = __DIR__ . '/lib/m';
$app = new \Ziima\Applet($cfg);
$app->run();
