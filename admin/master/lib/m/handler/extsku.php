<?php

include "lib/AdminHandler.php";

/**
 * 售卖管理
 * @auth
 */
class extsku_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0, int $spuid = 0, int $sid = 0, int $spu_id = 0, int $spu_spu = 0, string $bn_serch = '', string $key_serch = '', string $brand_serch = '',
			string $supplier_serch = '', string $status_serch = '') {
		$url = "/index.php?call=depot.extsku";
		$whr = [];
		$whe = [];

		if (!empty($_GET['bn_serch'])) {
			array_push($whe, "`bn` = '{$this->MySQL->encode($_GET['bn_serch'])}'");
		}
		if (!empty($_GET['key_serch'])) {
			array_push($whe, "`name` LIKE '%{$this->MySQL->encode($_GET['key_serch'])}%'");
		}
		if (!empty($_GET['status_serch'])) {
			if ($_GET['status_serch'] == 2) {
				$status = $_GET['status_serch'] - 2;
			}
			array_push($whe, "`status` = '{$status}'");
		}
		if (!empty($_GET['brand_serch'])) {
			$br = (int)$_GET['brand_serch'];
			array_push($whe, "`brand_id` = '{$br}'");
		}
		if (!empty($_GET['supplier_serch'])) {
			$su = (int)$_GET['supplier_serch'];
			array_push($whe, "`supplier_id` = '{$su}'");
		}

		if ($sid > 0) {
			$sql = "SELECT * FROM `yuemi_sale`.`ext_sku` where supplier_id ='" . $sid . "'";
		} else {
			$sql = "SELECT * FROM `yuemi_sale`.`ext_sku` ";
		}



		if ($spu_id > 0) {

			$whr[] = "`ext_spu_id` = $spu_id";
		}
		if ($spu_spu > 0) {
			$extspu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = " . $spu_spu);
			if (!empty($extspu_id)) {
				$whr[] = "`ext_spu_id` = $extspu_id";
			} else {
				$whr[] = "`id` = 0";
			}
		}
		if (!empty($whr) && empty($whe)) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		} elseif (!empty($whr) && !empty($whe)) {
			$sql .= ' WHERE ' . implode(' AND ', $whr) . ' AND ' . implode(' AND ', $whe);
		} elseif (empty($whr) && !empty($whe)) {
			$sql .= ' WHERE ' . implode(' AND ', $whe);
		}
		$sql .= ' ORDER BY `id` DESC';
		//echo $sql;
		$res = $this->MySQL->paging($sql, 25, $p);
		foreach ($res->Data as $k => $v) {
			$sql = "SELECT name FROM `yuemi_main`.`supplier` WHERE id = {$res->Data[$k]['supplier_id']}";
			$row = $this->MySQL->row($sql);
			if (empty($row)) {
				$res->Data[$k]['supplier'] = '';
			} else {
				$res->Data[$k]['supplier'] = $row['name'];
			}
		}

		foreach ($res->Data as $k => $v) {
			$supplier = \yuemi_main\SupplierFactory::Instance()->load($res->Data[$k]['supplier_id']);
			$tableName = 'ext_' . $supplier->alias . '_catagory';
			$sql = "SELECT stb.* FROM `yuemi_sale`.`ext_sku` as ek LEFT JOIN `yuemi_sale`.`ext_spu` as ep " .
					"ON ek.ext_spu_id = ep.id LEFT JOIN `yuemi_sale`." . $tableName . " as stb ON stb.id = ext_cat_id WHERE ek.id ={$res->Data[$k]['id']}";
			$row = $this->MySQL->row($sql);
			$str = $row['name'];
			if (intval($row['parent_id'])) {
				$nn2 = $this->MySQL->row("SELECT name FROM `yuemi_sale`." . $tableName . " WHERE id = {$row['parent_id']}");
				$str = $nn2['name'] . ' - ' . $str;
			} else {
				
			}
			if (empty($row)) {
				$res->Data[$k]['cats'] = '';
			} else {
				$res->Data[$k]['cats'] = $str;
			}
		}

		foreach ($res->Data as $k => $v) {
			$res->Data[$k]['ext_spu_id'] = $v['ext_spu_id'];
			$ext_spu_id = $res->Data[$k]['ext_spu_id'];
			$extspu_spu_id = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`ext_spu` WHERE id = " . $ext_spu_id);
			$res->Data[$k]['extspu_spu_id'] = $extspu_spu_id;
		}

		$supplier = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_main`.`supplier` WHERE `pi_enable` = 1");
		$brand = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_sale`.`brand`");

		return [
			'data' => $res,
			'supplier' => $supplier,
			'brand' => $brand
		];
	}

	public function extsku_picture(int $p = 0, int $ext_sku_id = 0, int $type = 0) {
		$sql = 'SELECT * FROM `yuemi_sale`.`ext_sku_material`';

		$whr = [];
		if ($ext_sku_id) {
			$whr[] = '`ext_sku_id`=' . $ext_sku_id;
		}
		$whr[] = "`type` = $type";
		if (!empty($whr)) {
			$sql .= ' WHERE ' . implode(' and ', $whr);
		}
		$sql .= ' ORDER BY `id` DESC';
		$res = $this->MySQL->paging($sql, 25, $p);
		$spu_title = '';

		if ($ext_sku_id) {
			$sql = 'SELECT `title` FROM `yuemi_sale`.`ext_sku` WHERE `id`=' . $ext_sku_id;
			$row = $this->MySQL->row($sql);
			if (!empty($row)) {
				$spu_title = trim($row['title']);
			}
		}
		return [
			'data' => $res,
			'spu_title' => $spu_title
		];
	}

	
	public function extsku_info(int $ext_sku_id = 0) {

		$sql = "SELECT ek.*,ep.title,ep.price_base,ep.bn,au.name as gysname,br.name as ppname" .
				" FROM `yuemi_sale`.`ext_sku` as `ek` " .
				" LEFT JOIN `yuemi_sale`.`ext_spu` as `ep` ON ek.`ext_spu_id` = `ep`.id " .
				" LEFT JOIN `yuemi_main`.`supplier` as au on ek.supplier_id = au.id" .
				" LEFT JOIN `yuemi_sale`.`brand` as br on br.id = ep.brand_id" .
				" WHERE ek.id = {$ext_sku_id}";
		$ret = $this->MySQL->row($sql);
		return $ret;
	}
	
	

}
