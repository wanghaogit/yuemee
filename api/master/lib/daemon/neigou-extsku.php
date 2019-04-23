<?php

/**
 * 内购数据上架
 */
include 'neigou-base.php';

$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
$t0 = $mysql->grid("SELECT `id`,`price_base`,`price_ref`,`stock`,`bn` FROM yuemi_sale.ext_sku WHERE sku_id = 0");
foreach ($t0 as $esku) {
	if ($esku['stock'] < 1 || $esku['price_base'] <= 0 || $esku['price_ref'] <= 0) {
		continue;
	}
	$ratio = ($esku['price_ref'] - $esku['price_base']) / $esku['price_ref'];
	if ($ratio < 0.05) {
		continue;
	}
	$ret = \yuemi_sale\ProcedureInvoker::Instance()->import_sku(1, $esku['id'], ip2long('127.0.0.1'));
	if ($ret === null || $ret === false) {
		echo "导入ESKU {$esku['id']} 失败\n";
		continue;
	}
	if($ret->ReturnValue != 'OK'){
		echo "导入ESKU {$esku['id']} 失败，{$ret->ReturnMessage}\n";
		continue;
	}
	echo "导入ESKU {$esku['id']} 成功，SKUID = {$ret->SkuId}\n";
}