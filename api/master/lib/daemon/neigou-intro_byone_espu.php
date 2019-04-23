<?php

/**
 * 内购内容
 */
include 'neigou-base.php';

$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);

$MAP = [];
echo "处理外部SPU\n";
$sku_ids = [63175,63055];
foreach ($sku_ids as $sku_id){
	$ext_spu_id = $mysql->scalar("SELECT `ext_spu_id` FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = {$sku_id}");
	$spu = $mysql->row("SELECT `id`,`bn` FROM `yuemi_sale`.`ext_spu` WHERE id = {$ext_spu_id}");
	$id = $spu['id'];
	$bn = $spu['bn'];
	echo " $bn...";
	$txt = $mysql->scalar("SELECT `intro` FROM `yuemi_sale`.`ext_spu` WHERE `id` = $id");
	if (empty($txt)) {
		echo "空\n";
		continue;
	}
	$txt = preg_replace('/\_src\s*\=\s*\"(.+?)\"/i', '', $txt);
	$pic = [];
	$ret = preg_match_all('/src\s*\=\s*\"(.+?)\"/i', $txt, $pic);
	if ($ret < 1 || empty($pic)) {
		echo "无图\n";
		$mysql->execute("UPDATE `yuemi_sale`.`ext_spu` SET `lo_status` = 2,`lo_time` = UNIX_TIMESTAMP(),`lo_error` = 0 WHERE `id` = $id");
		continue;
	}
	echo "\n";
	$src = [];
	$tgt = [];
	foreach ($pic[1] as $rurl) {
		if (substr($rurl, 0, 4) == '/ext')
			continue;
		$lurl = $MAP[$rurl] ?? '';
		if (empty($lurl)) {
			$lurl = $mysql->scalar("SELECT `file_url` FROM `yuemi_sale`.`ext_spu_material` WHERE `source_url` = '%s'", $rurl);
		}
		if (empty($lurl)) {
			$lurl = $mysql->scalar("SELECT `file_url` FROM `yuemi_sale`.`ext_sku_material` WHERE `source_url` = '%s'", $rurl);
		}
		if (empty($lurl)) {
			echo "   - 缺图待处理\n";
			$mysql->execute("UPDATE `yuemi_sale`.`ext_spu` SET `lo_status` = 1,`lo_time` = UNIX_TIMESTAMP(),`lo_error` = `lo_error` + 1 WHERE `id` = $id");
			continue;
		}
		if (!array_key_exists($rurl, $MAP)) {
			$MAP[$rurl] = $lurl;
		}
		if (!in_array($rurl, $src))
			$src[] = $rurl;
		if (!in_array($lurl, $tgt))
			$tgt[] = URL_RES . '/upload' . $lurl;
		echo "    + " . $lurl . "\n";
	}
	if (!empty($src)) {
		$txt = str_replace($src, $tgt, $txt);
	}
	$mysql->execute("UPDATE `yuemi_sale`.`ext_spu` SET `intro` = '%s',`lo_status` = 2,`lo_time` = UNIX_TIMESTAMP() WHERE `id` = $id", $mysql->encode($txt));
}
