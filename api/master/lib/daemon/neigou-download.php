<?php

/**
 * 内购数据初始化
 */
include "neigou-base.php"; // 载入内购基础配置文件
//初始化下载目录
$ROOT = '/data/nfs/upload/ext';
if (Z_OS == 'WINDOWS' || Z_OS == 'LINUXMINT' || Z_OS == 'UBUNTU' || Z_HOSTNAME == 'winode') {
	$ROOT = "D:\\Work\\yuemee\\dev\\res\\master\\upload\\ext";
}
if (!file_exists($ROOT)) {
	mkdir($ROOT, 0755, true);
}
if (!file_exists($ROOT) || !is_dir($ROOT)) {
	_E("素材下载目录 $ROOT 不存在...");
	return;
}

if (!_LOCK('down')) {
	_LOG("OFFLINE : 锁定失败");
	return;
}

$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
_T('下载ExtSpu图...');
$t0 = $mysql->scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_spu_material` WHERE `status` = 0");
_T("待处理数量 $t0 ...");
if ($t0 > 0) {
	$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
	while ($row) {
		$url = $row['source_url'];
		_T("下载 $url ...");
		if (empty($url) || $url == 'undefined') {
			_E("ExtSpu素材 #{$row['id']} 的 source url 有问题。");
			$mysql->execute("DELETE FROM `yuemi_sale`.`ext_spu_material` WHERE `id` = %d", $row['id']);
			$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
			continue;
		}
		$ext = '';
		if (preg_match('/\.(jpg|jpeg)\b/i', $url)) {
			$ext = 'jpg';
		} else if (preg_match('/\.(png)\b/i', $url)) {
			$ext = 'png';
		} else if (preg_match('/\.(gif)\b/i', $url)) {
			$ext = 'gif';
		}
		if (empty($ext)) {
			_E("ExtSpu素材 #{$row['id']} 的 source url 无法获取扩展名。");
			$mysql->execute("UPDATE `yuemi_sale`.`ext_spu_material` SET `status` = 2 WHERE `id` = %d", $row['id']);
			$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
			continue;
		}

		$bin = null;
		if ($url[0] == '/' && $url[1] == '/') {
			$bin = file_get_contents('http:' . $url);
			if (empty($bin)) {
				$bin = file_get_contents('https:' . $url);
			}
		} else {
			$bin = file_get_contents($url);
		}
		if ($bin === null || $bin === false || empty($bin)) {
			_E("ExtSpu素材 #{$row['id']} 的 source url 无法下载。");
			$mysql->execute("UPDATE `yuemi_sale`.`ext_spu_material` SET `status` = 2 WHERE `id` = %d", $row['id']);
			$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
			continue;
		}
		$mid = \Ziima\Zid::Default()->serial('m');
		$sav = $ROOT . DIRECTORY_SEPARATOR . substr($mid, 0, 2) . DIRECTORY_SEPARATOR . substr($mid, 2, 2);
		$uri = '/ext/' . substr($mid, 0, 2) . '/' . substr($mid, 2, 2);
		if (!file_exists($sav)) {
			mkdir($sav, 0755, true);
		}
		if (!file_exists($sav)) {
			_E("创建目录 $sav 失败。");
			return;
		}
		$msav = $sav . DIRECTORY_SEPARATOR . $mid . '.' . $ext;
		$muri = $uri . '/' . $mid . '.' . $ext;
		$ret = file_put_contents($msav, $bin);
		if ($ret === false || $ret <= 0) {
			_E("保存文件 $msav 失败。");
			$mysql->execute("UPDATE `yuemi_sale`.`ext_spu_material` SET `status` = 2 WHERE `id` = %d", $row['id']);
			$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
			continue;
		}
		$p_file_fmt = ($ext == 'jpg' ? 0 : 1);
		$p_file_size = filesize($msav);

		$sz = getimagesize($msav);
		if ($sz === false || $sz === null) {
			_E("获取图片 $sav 尺寸失败。");
			unlink($sav);
			$mysql->execute("UPDATE `yuemi_sale`.`ext_spu_material` SET `status` = 2 WHERE `id` = %d", $row['id']);
			$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
			continue;
		}
		$p_image_width = $sz[0];
		$p_image_height = $sz[1];

		$p_thumb_path = '';
		$p_thumb_url = '';
		$p_thumb_size = 0;
		$p_thumb_width = 0;
		$p_thumb_height = 0;
		//制作缩略图
		if ($row['type'] == 0) {
			_T("制作缩略图...");
			$p_thumb_path = $sav . DIRECTORY_SEPARATOR . $mid . '-thumb.' . $ext;
			$p_thumb_url = $uri . '/' . $mid . '-thumb.' . $ext;
			$p_thumb_width = 320;
			$p_thumb_height = 320;
			$pic = new \Imagick($msav);
			$pic->adaptiveresizeimage($p_thumb_width, $p_thumb_height, true);
			$ret = file_put_contents($p_thumb_path, $pic);
			$p_thumb_size = filesize($p_thumb_path);
		}
		if (!$mysql->execute(
						"UPDATE `yuemi_sale`.`ext_spu_material` SET " .
						"file_fmt = %d," .
						"file_name = '%s'," .
						"file_size = %d," .
						"file_url = '%s'," .
						"image_width = %d," .
						"image_height = %d," .
						"thumb_url = '%s'," .
						"thumb_size = %d," .
						"thumb_width = 320," .
						"thumb_height = 320," .
						"status = 1," .
						"update_time = UNIX_TIMESTAMP() " .
						"WHERE `id` = %d",
						$p_file_fmt,
						$mid . '.' . $ext,
						$p_file_size,
						$muri,
						$p_image_width,
						$p_image_height,
						$p_thumb_url,
						$p_thumb_size,
						$row['id']
				)) {
			_E("保存素材 {$row['id']} 失败。");
			unlink($msav);
			if ($row['type'] == 0) {
				unlink($p_thumb_path);
			}
			$mysql->execute("UPDATE `yuemi_sale`.`ext_spu_material` SET `status` = 2 WHERE `id` = %d", $row['id']);
			//调用存储过程复制ESPU素材到SPU素材
			$ext_spu_id = $mysql->scalar("SELECT `ext_spu_id` FROM `yuemi_sale`.`ext_spu_material` WHERE `id` = %d",$row['id']);
			$spu_id = $mysql->scalar("SELECT `spu_id` FROM `yuemi_sale`.`ext_spu` WHERE `id` = %d",$ext_spu_id);
			$sql1 = "SELECT COUNT(EPM.`id`) FROM `ext_spu_material` AS EPM 
						LEFT JOIN ( SELECT `file_url` FROM `spu_material` WHERE `spu_id` = {$spu_id} ) AS PM ON PM.`file_url` = EPM.`file_url`
						WHERE EPM.`ext_spu_id` = {$ext_spu_id} AND EPM.`type` = 0 AND PM.`file_url` IS NULL";
			$count = $mysql->scalar($sql1);
			if ($count){
				$Re = \yuemi_sale\ProcedureInvoker::Instance()->copy_espu_to_spu($spu_id, $ext_spu_id, 1,3 ,1928461940);
				if ($Re->ReturnValue == 'OK'){
					echo '复制 '.$ext_spu_id.' 素材成功';
				}
			}
			continue;
		}
		$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
	}
}
_T('下载ExtSku主图...');
$t0 = $mysql->scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_sku_material` WHERE `status` = 0");
_T("待处理数量 $t0 ...");
if ($t0 > 0) {
	$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
	while ($row) {
		$url = $row['source_url'];
		if (empty($url) || $url == 'undefined') {
			$mysql->execute("DELETE FROM `yuemi_sale`.`ext_sku_material` WHERE `id` = %d", $row['id']);
			$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
			continue;
		}
		$ext = '';
		if (preg_match('/\.(jpg|jpeg)\b/i', $url)) {
			$ext = 'jpg';
		} else if (preg_match('/\.(png)\b/i', $url)) {
			$ext = 'png';
		} else if (preg_match('/\.(gif)\b/i', $url)) {
			$ext = 'gif';
		}
		if (empty($ext)) {
			_E("ExtSku素材 #{$row['id']} 的 source url 无法获取扩展名。");
			$mysql->execute("DELETE FROM `yuemi_sale`.`ext_sku_material` WHERE `id` = %d", $row['id']);
			$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
			continue;
		}

		$bin = null;
		if ($url[0] == '/' && $url[1] == '/') {
			$bin = file_get_contents('http:' . $url);
			if (empty($bin)) {
				$bin = file_get_contents('https:' . $url);
			}
		} else {
			$bin = file_get_contents($url);
		}
		if ($bin === null || $bin === false || empty($bin)) {
			_E("ExtSku素材 #{$row['id']} 的 source url 无法下载。");
			$mysql->execute("UPDATE `yuemi_sale`.`ext_sku_material` SET `status` = 2 WHERE `id` = %d", $row['id']);
			$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
			continue;
		}
		$mid = \Ziima\Zid::Default()->serial('m');
		$sav = $ROOT . DIRECTORY_SEPARATOR . substr($mid, 0, 2) . DIRECTORY_SEPARATOR . substr($mid, 2, 2);
		$uri = '/ext/' . substr($mid, 0, 2) . '/' . substr($mid, 2, 2);
		if (!file_exists($sav)) {
			mkdir($sav, 0755, true);
		}
		if (!file_exists($sav)) {
			_E("创建目录 $sav 失败。");
			return;
		}
		$msav = $sav . DIRECTORY_SEPARATOR . $mid . '.' . $ext;
		$muri = $uri . '/' . $mid . '.' . $ext;
		$ret = file_put_contents($msav, $bin);
		if ($ret === false || $ret <= 0) {
			_E("保存文件 $msav 失败。");
			$mysql->execute("UPDATE `yuemi_sale`.`ext_sku_material` SET `status` = 2 WHERE `id` = %d", $row['id']);
			$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
			continue;
		}
		$p_file_fmt = ($ext == 'jpg' ? 0 : 1);
		$p_file_size = filesize($msav);

		$sz = getimagesize($msav);
		if ($sz === false || $sz === null) {
			_E("获取图片 $sav 尺寸失败。");
			unlink($sav);
			$mysql->execute("UPDATE `yuemi_sale`.`ext_sku_material` SET `status` = 2 WHERE `id` = %d", $row['id']);
			$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
			continue;
		}
		$p_image_width = $sz[0];
		$p_image_height = $sz[1];

		$p_thumb_path = '';
		$p_thumb_url = '';
		$p_thumb_size = 0;
		$p_thumb_width = 0;
		$p_thumb_height = 0;
		//制作缩略图
		if ($row['type'] == 0) {
			_T("制作缩略图...");
			$p_thumb_path = $sav . DIRECTORY_SEPARATOR . $mid . '-thumb.' . $ext;
			$p_thumb_url = $uri . '/' . $mid . '-thumb.' . $ext;
			$p_thumb_width = 320;
			$p_thumb_height = 320;
			$pic = new \Imagick($msav);
			$pic->adaptiveresizeimage($p_thumb_width, $p_thumb_height, true);
			$ret = file_put_contents($p_thumb_path, $pic);
			$p_thumb_size = filesize($p_thumb_path);
		}
		if (!$mysql->execute(
						"UPDATE `yuemi_sale`.`ext_sku_material` SET " .
						"file_fmt = %d," .
						"file_name = '%s'," .
						"file_size = %d," .
						"file_url = '%s'," .
						"image_width = %d," .
						"image_height = %d," .
						"thumb_url = '%s'," .
						"thumb_size = %d," .
						"thumb_width = 320," .
						"thumb_height = 320," .
						"status = 1," .
						"update_time = UNIX_TIMESTAMP() " .
						"WHERE `id` = %d",
						$p_file_fmt,
						$mid . '.' . $ext,
						$p_file_size,
						$muri,
						$p_image_width,
						$p_image_height,
						$p_thumb_url,
						$p_thumb_size,
						$row['id']
				)) {
			$mysql->execute("UPDATE `yuemi_sale`.`ext_sku_material` SET `status` = 2 WHERE `id` = %d", $row['id']);
			_E("更新素材 {$row['id']} 失败。");
			unlink($msav);
			if ($row['type'] == 0) {
				unlink($p_thumb_path);
			}
			//调用存储过程复制ESKU素材到SKU素材
			$ext_sku_id = $mysql->scalar("SELECT `ext_sku_id` FROM `yuemi_sale`.`ext_sku_material` WHERE `id` = %d",$row['id']);
			$sku_id = $mysql->scalar("SELECT `sku_id` FROM `yuemi_sale`.`ext_sku` WHERE `id` = %d",$ext_sku_id);
			$sql = "SELECT COUNT(EKM.`id`) FROM `ext_sku_material` AS EKM 
					LEFT JOIN ( SELECT `file_url` FROM `sku_material` WHERE `sku_id` = {$sku_id} ) AS PM ON PM.`file_url` = EKM.`file_url`
					WHERE EKM.`ext_sku_id` = {$ext_sku_id} AND EKM.`type` = 0 AND PM.`file_url` IS NULL";
			if ($mysql->scalar($sql)){
				$Re = \yuemi_sale\ProcedureInvoker::Instance()->copy_esku_to_sku($sku_id, $ext_sku_id, 1, 3, 1928461940);
				if ($Re->ReturnValue == 'OK'){
					echo '复制 '.$ext_sku_id.' 素材成功';
				}
			}
		}
		$row = $mysql->row("SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `status` = 0 ORDER BY RAND() LIMIT 1");
	}
}
_UNLOCK('down');
