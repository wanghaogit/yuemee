<?php

/**
 * 内购增量数据
 * 1、提取最近7天内更新的库存1000条
 */
include 'neigou-base.php';
define('TASK_SIZE', 100);
define('TASK_BATCH', 2);
define('TASK_HOURS', mt_rand(5, 168));

_LOG("INCR : 启动 H=%d,B=%d,P=%d", TASK_HOURS, TASK_BATCH, TASK_SIZE);
if (!_LOCK('incr')) {
	_LOG("OFFLINE : 锁定失败");
	return;
}
$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
for ($p = 1; $p <= TASK_BATCH; $p ++) {
	$list = get_goods_bn_list($p, TASK_SIZE, TASK_HOURS);
	foreach ($list['Data']['goodBns'] as $bn) {
		$cnt = $mysql->scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_spu` WHERE `supplier_id` = 2 AND `bn` = '%s'", $bn);
		if ($cnt > 0) {
			_LOG("INCR : 货品 $bn 已存在");
			continue;
		}
		$ret = get_goods_info($bn);
		if ($ret === null) {
			_LOG("INCR : 拉取商品信息 $bn 失败。");
			continue;
		}
		if (count($ret['Data']['info']['skus']) < 1) {
			_LOG("INCR : 商品 $bn 没有SKU。");
			continue;
		}
		//预检查各SKU价格
		$sel_sku = [];
		foreach ($ret['Data']['info']['skus'] as $k) {
			$k_bn = $k['bn'];
			$k_rel = floatval($k['mktprice']);
			$k_bas = floatval($k['price']);
			$k_stk = intval($k['stock']);
			if ($k_stk <= 0)
				continue;
			if ($k_rel <= 0)
				continue;
			if ($k_bas <= 0)
				continue;
			$k_rto = ($k_rel - $k_bas) / $k_rel;
			if ($k_rto < 0.05)
				continue;
			$sel_sku[] = $k_bn;
		}
		if (empty($sel_sku)) {
			_LOG("INCR : 商品 $bn 没有合适的SKU。");
			continue;
		}
		_LOG("INCR : 商品 $bn 毛利和库存合适，选中");
		$brand_name = $ret['Data']['info']['brand_name'];
		$brand_id = 0;
		if (!empty($brand_name))
			$brand_id = _get_brand_id($mysql, $brand_name);
		$specs_text = '';
		if ($ret['Data']['info']['specs']) {
			foreach ($ret['Data']['info']['specs'] as $spec) {
				$sn = $spec['spec_name'];
				$sv = [];
				foreach ($spec['spec_values'] as $v) {
					$sv[] = $v['spec_value'];
				}
				$specs_text .= $sn . ':' . implode(',', $sv) . "\n";
			}
		}

		$spu = new \yuemi_sale\ExtSpuEntity();
		$spu->supplier_id = 2;
		$spu->bn = $ret['Data']['info']['bn'];
		$spu->ext_cat_id = intval($ret['Data']['info']['mall_goods_cat']);
		$spu->brand_id = $brand_id;
		$spu->spu_id = 0;
		$spu->catagory_id = 0;
		$spu->title = $ret['Data']['info']['name'];
		$spu->price_base = floatval($ret['Data']['info']['price']);
		$spu->video = '';
		$spu->intro = '';
		$spu->specs = $specs_text;
		$spu->status = 1;
		$spu->create_time = time();
		$spu->update_time = time();
		\yuemi_sale\ExtSpuFactory::Instance()->insert($spu);
		if ($spu->id <= 0) {
			_LOG("INCR : 插入SPU（{$spu->bn}）失败");
			continue;
		} else {
			_LOG("INCR : 插入SPU（{$spu->bn}）=> {$spu->id}");
		}
		//记录SKU主图
		if (!empty($ret['Data']['info']['image'])) {
			_save_sku_images($mysql, $spu->id, $ret['Data']['info']['image']);
		}
		if (!empty($ret['Data']['info']['images'])) {
			foreach ($ret['Data']['info']['images'] as $url) {
				_save_sku_images($mysql, $spu->id, $url);
			}
		}
		_LOG("INCR : 处理SPU（{$spu->bn}）下的SKU");

		//处理SKU
		foreach ($ret['Data']['info']['skus'] as $inf) {
			if (!in_array($inf['bn'], $sel_sku)) {
				_LOG("INCR : 跳过SKU（{$spu->bn}）/ {$inf['bn']}失败");
				continue;
			}
			$specs_text = '';
			if ($inf['specs']) {
				foreach ($inf['specs'] as $spec) {
					$specs_text .= $spec['spec_name'] . ':' . $spec['spec_value'] . "\n";
				}
			}
			$sku = new \yuemi_sale\ExtSkuEntity();
			$sku->supplier_id = 2;
			$sku->bn = $inf['bn'];
			$sku->ext_spu_id = $spu->id;
			$sku->sku_id = 0;
			$sku->create_time = time();

			$sku->name = $inf['name'];
			$sku->price_base = floatval($inf['price']);
			$sku->price_ref = floatval($inf['mktprice']);
			$sku->weight = floatval($inf['weight']);
			$sku->stock = intval($inf['stock']);
			$sku->intro = $inf['intro'];
			$sku->spec = $specs_text;
			$sku->status = 1;
			\yuemi_sale\ExtSkuFactory::Instance()->insert($sku);
			if ($sku->id <= 0) {
				_LOG("INCR : 插入SKU（{$spu->bn}/{$sku->bn}）失败");
				continue;
			} else {
				_LOG("INCR : 插入SKU（{$spu->bn}/{$sku->bn}）=> {$sku->id}");
			}
			$imgs = [];
			preg_match_all('/img\s+src=\"(.+?)\"/i', $sku->intro, $imgs);
			foreach ($imgs[1] as $url) {
				_save_sku_image($mysql, $sku->id, $url);
			}
			//搞定
		}
	}
}
_UNLOCK('incr');

function _get_brand_id(\Ziima\Data\MySQLConnection $mysql, string $brand_name): int {
	$brand_id = $mysql->scalar("SELECT `id` FROM `yuemi_sale`.`brand` WHERE `name` = '%s'", $mysql->encode($brand_name));
	if (empty($brand_id)) {
		$mysql->execute("INSERT INTO `yuemi_sale`.`brand` (`name`) VALUES ('%s')", $mysql->encode($brand_name));
		$brand_id = $mysql->lastid();
		if (empty($brand_id)) {
			_LOG("INCR : 插入品牌 $brand_name 失败，跳过.");
			$brand_id = 0;
		}
	}
	return $brand_id;
}

function _save_sku_images(\Ziima\Data\MySQLConnection $mysql, int $spuId, string $url) {
	$pky = md5($url);
	$mat = \yuemi_sale\ExtSpuMaterialFactory::Instance()->loadBySourceHash($pky);
	if ($mat === null) {
		$mysql->execute(
				"INSERT INTO `yuemi_sale`.`ext_spu_material` (`ext_spu_id`,`type`,`source_url`,`source_hash`,`is_default`,`status`,`create_time`) " .
				"VALUES (%d,0,'%s','%s',1,0,UNIX_TIMESTAMP())",
				$spuId,
				$mysql->encode($url),
				$pky
		);
	}
}

function _save_sku_image(\Ziima\Data\MySQLConnection $mysql, int $skuId, string $url) {
	$pky = md5($url);
	$mat = \yuemi_sale\ExtSkuMaterialFactory::Instance()->loadBySourceHash($pky);
	if ($mat === null) {
		$mysql->execute(
				"INSERT INTO `yuemi_sale`.`ext_sku_material` (`ext_sku_id`,`type`,`source_url`,`source_hash`,`is_default`,`status`,`create_time`) " .
				"VALUES (%d,1,'%s','%s',1,0,UNIX_TIMESTAMP())",
				$skuId,
				$mysql->encode($url),
				$pky
		);
	}
}
