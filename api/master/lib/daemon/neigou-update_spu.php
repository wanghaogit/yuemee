<?php
/**
 * 内购直接更新到SPU
 * http://a.ym.cn/lib/daemon/neigou-update_spu.php
 * /opt/php/bin/php /data/web/api/lib/daemon/neigou-update_spu.php
 */
define('PAGE_SIZE', 2);
include "neigou-base.php"; // 载入内购基础配置文件
$MySql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);

// 锁定
if (!_LOCK('update_spu')) {
	_LOG("OFFLINE : 锁定失败");
	goto end;
}

// 读取最后处理的商品Id
$LastIdLogPath = __DIR__ . '/../../data/neigou-update-spu-lastid.log';
$LastId = intval(@file_get_contents($LastIdLogPath));

// 读取要处理的数据列表
$Sql = "SELECT * FROM yuemi_sale.spu WHERE supplier_id = 2 AND id  > {$LastId} ORDER BY id ASC LIMIT " . PAGE_SIZE;
$SpuList = $MySql->grid($Sql);
foreach ($SpuList AS $SpuInfo)
{
	file_put_contents($LastIdLogPath, $SpuInfo['id']);
	if (empty($SpuInfo['serial'])) continue;
	
	// 读取内购商品信息
	$GoodsInfo = get_goods_info($SpuInfo['serial']);
	//unset($GoodsInfo['Data']['info']['skus']);
	unset($GoodsInfo['Data']['info']['specs']);
	print_r($GoodsInfo);
	goto end;
}

// 结束，解锁
end:
_UNLOCK('update_spu');
