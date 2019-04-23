<?php
/**
 * 阅米云平台API
 */
include_once "../../_base/config.php";
include_once Z_ROOT .'/ZService.php';

$cfg = new \Ziima\MVC\RouterConfig();
$cfg->dir_code  = __DIR__ . '/lib';
$app = new \Ziima\Service($cfg);
$app->run();
