<?php

/**
 * APP动态页面
 */
include "../../_base/config.php";
include Z_ROOT . '/ZApplet.php';

$cfg = new \Ziima\MVC\RouterConfig();
$cfg->dir_code = __DIR__ . '/lib/app';
$cfg->debug = true;
$app = new \Ziima\Applet($cfg);
$app->run();
