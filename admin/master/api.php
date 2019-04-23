<?php
/**
 * API入口
 * 功能：
 * 1、系统后台数据调用
 * 2、员工平台数据调用
 */
include "../../_base/config.php";
include Z_ROOT .'/ZService.php';

$cfg = new \Ziima\MVC\RouterConfig();
$cfg->dir_code  = __DIR__ . '/lib/a';
$app = new \Ziima\Service($cfg);
$app->run();
