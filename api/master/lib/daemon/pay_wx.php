<?php
/**
 * 微信支付处理
 * http://a.ym.cn/lib/daemon/pay_wx.php
 */
include dirname(__FILE__) . '/../../../../_base/config.php';
include dirname(__FILE__) . '/../../../../_base/WeiXinPayment.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';

// 微信订单信息
// $WeiXinPayment = new WeiXinPayment();
// $Data = $WeiXinPayment->DownloadBill('20180429');
// echo $Data;
// exit;
