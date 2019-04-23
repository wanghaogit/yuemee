<?php

/**
 * 内购离线数据守护服务
 * 每分钟运行一次
 * 提取100条数据更新价格和库存
 */
include 'neigou-base.php';
define('TASK_SIZE', 100);

_LOG("UPDATE :启动");
if (!_LOCK('update')) {
	_LOG("UPDATE :锁定失败");
	return;
}
$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
/* * ***********************************************************************************************************************
 * 第一步：检查没上架的ExtSku
 * *********************************************************************************************************************** */
_LOG("UPDATE :检查未上架的ExtSKU");
$list = $mysql->column("SELECT `id` FROM yuemi_sale.`ext_sku` WHERE `sku_id` = 0 AND `status` = 1 ORDER BY RAND() LIMIT 100");
foreach ($list as $ext_sku_id) {
	$ret = \yuemi_sale\ProcedureInvoker::Instance()->import_sku(1, $ext_sku_id, ip2long('127.0.0.1'));
	if ($ret === null)
		continue;
	if ($ret->ReturnValue == 'OK') {
		_LOG("UPDATE :外部SKU %d 上架", $ext_sku_id);
	} else {
		_LOG("UPDATE :外部SKU %d 上架失败，%s", $ext_sku_id, $ret->ReturnMessage);
	}
}
/* * ***********************************************************************************************************************
 * 第二步：检查货架上的Sku
 * *********************************************************************************************************************** */
// $list = $mysql->column("SELECT `id` FROM yuemi_sale.`sku` WHERE `status` = 2 ORDER BY RAND() LIMIT 100");

/* * ***********************************************************************************************************************
 * 第三步：检查外部SKU的价格信息
 * *********************************************************************************************************************** */
$list = $mysql->column("SELECT `bn` FROM `yuemi_sale`.`ext_sku` WHERE `supplier_id` = 2 AND `update_time` < UNIX_TIMESTAMP() - 900 ORDER BY RAND() LIMIT " . TASK_SIZE);
if (empty($list)) {
	_LOG("UPDATE :无需更新,结束。");
	_UNLOCK('update');
	return;
}
$ret = get_goods_price(implode(',', $list));
if (empty($ret)) {
	_LOG("UPDATE :拉取内购价格信息失败。");
	_UNLOCK('update');
	return;
}
// 更新价格和库存
foreach ($ret['Data']['list'] as $bn => $inf) {
	$esku = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_sku` WHERE `supplier_id` = 2 AND `bn` = '%s'", $bn);
	if ($esku === null || $esku === false || empty($esku)) {
		_LOG("UPDATE :SKU $bn 数据意外丢失。");
		continue;
	}

	$o_base = floatval($inf['price'] ?? '0');
	$o_rel = floatval($inf['mktprice'] ?? '0');
	$o_stock = intval($inf['stock'] ?? '0');

	$ret = \yuemi_sale\ProcedureInvoker::Instance()->resync_sku($esku['id'], $o_base, $o_rel, $o_stock);
	if ($ret === null) {
		_LOG("UPDATE :SKU $bn 处理失败。");
		continue;
	}
	_LOG("UPDATE :$bn => " . $ret->ReturnValue . ' => ' . $ret->ReturnMessage);

	if ($esku['sku_id'] <= 0)
		continue;
	$ret = \yuemi_sale\ProcedureInvoker::Instance()->resync_price($esku['sku_id']);
	if ($ret === null) {
		_LOG("UPDATE :SKU $bn 价格同步失败。");
		continue;
	}
	_LOG("UPDATE :$bn => " . $ret->ReturnValue . ' => ' . $ret->ReturnMessage);
}
_UNLOCK('update');

