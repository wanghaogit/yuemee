<?php

include "lib/AdminHandler.php";

/**
 * 售卖管理
 * @auth
 */
class extspu_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0, int $sid = 0, int $bid = 0, int $sku = 0, string $bn_serch = '', string $key_serch = '', string $brand_serch = '',
			string $supplier_serch = '', string $status_serch = '') {
		$whr = [];
		//$whe = [];
		$sql = "SELECT * FROM `yuemi_sale`.`ext_spu` ";
		if (!empty($bn_serch)) {
			//array_push($whr, "`bn` = '{$bn_serch}'");
			$whr[] = "`bn` = '{$bn_serch}'";
		}
		if (!empty($key_serch)) {
			//array_push($whr,"`title` LIKE '%{$key_serch}%'");
			$whr[] = "`title` LIKE '%{$key_serch}%' ";
		}
		if (!empty($status_serch)) {
			//array_push($whr, "`status` = '{$status_serch}'");
			if ($status_serch == 2) {
				$status = $status_serch - 2;
			}
			$whr[] = " `status` = '{$status}' ";
		}
		if (!empty($brand_serch)) {
			//array_push($whr, "`brand_id` = '{$brand_serch}'");
			$whr[] = "`brand_id` = '{$brand_serch}' ";
		}
		if (!empty($supplier_serch)) {
			//array_push($whr, "`supplier_id` = '{$supplier_serch}'");
			$whr[] = "`supplier_id` = '{$supplier_serch}'";
		}

		if ($sid > 0) {
			$whr[] = "`supplier_id` = $sid";
		}
		if ($bid > 0) {
			$whr[] = "`brand_id` = $bid";
		}
		if ($sku > 0) {
			$whr[] = "`id` = $sku";
		}

		if (!empty($whr)) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}

		$sql .= ' ORDER BY `id` DESC';
		$res = $this->MySQL->paging($sql, 35, $p);
		foreach ($res->Data as $k => $v) {
			$sql = 'select * from `yuemi_sale`.`ext_supplier` where `id`=' . $v['supplier_id'];
			$row = $this->MySQL->row($sql);
			if (empty($row)) {
				$res->Data[$k]['supplier'] = '';
			} else {
				$res->Data[$k]['supplier'] = $row['name'];
			}
			$res->Data[$k]['ext_cat'] = $this->get_neigou_catagory($v['ext_cat_id']);

			$sql = 'select * from `yuemi_sale`.`brand` where `id`=' . $v['brand_id'];
			$row = $this->MySQL->row($sql);
			if (empty($row)) {
				$res->Data[$k]['brand'] = '';
			} else {
				$res->Data[$k]['brand'] = $row['name'];
			}
			switch ($v['status']) {
				case '1':
					$res->Data[$k]['status'] = '上架';
					$res->Data[$k]['canuse'] = '有效';
					break;
				case '0':
					$res->Data[$k]['status'] = '下架';
					$res->Data[$k]['canuse'] = '失效';
					break;
				default:
					$res->Data[$k]['status'] = '';
					$res->Data[$k]['canuse'] = '';
			}
		}
		foreach ($res->Data as $k => $v) {
			$sql = "SELECT name FROM `yuemi_main`.`supplier` WHERE id = {$res->Data[$k]['supplier_id']}";
			$row = $this->MySQL->row($sql);
			if (empty($row)) {
				$res->Data[$k]['supplier'] = '';
			} else {
				$res->Data[$k]['supplier'] = $row['name'];
			}
		}

		$supplier = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_main`.`supplier` WHERE `pi_enable` = 1");
		$brand = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_sale`.`brand`");
		return [
			'data' => $res,
			'supplier' => $supplier,
			'brand' => $brand
		];
	}

	
	
	private function get_neigou_catagory($cid) {
		$sql = 'select * from `yuemi_sale`.`ext_neigou_catagory` where `id`=' . $cid;
		$row = $this->MySQL->row($sql);
		if (empty($row)) {
			return '';
		}
		if (intval($row['parent_id'])) {
			return $this->get_neigou_catagory($row['parent_id']) . '-' . $row['name'];
		} else {
			return $row['name'];
		}
	}
	
	public function extspu_picture(int $p = 0, int $ext_spu_id = 0, int $type = 0) {
		$sql = 'select * from `yuemi_sale`.`ext_spu_material`';

		$whr = [];
		if ($ext_spu_id) {
			$whr[] = '`ext_spu_id`=' . $ext_spu_id;
		}
		$whr[] = "`type` = $type";
		if (!empty($whr)) {
			$sql .= ' where ' . implode(' and ', $whr);
		}
		$sql .= ' order by `id` DESC';

		$res = $this->MySQL->paging($sql, 25, $p);
		$spu_title = '';

		if ($ext_spu_id) {
			$sql = 'SELECT `title` FROM `yuemi_sale`.`ext_spu` WHERE `id`=' . $ext_spu_id;
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
	
	public function extspu_info(int $spuid = 0) {

		$cid = "SELECT `supplier_id` FROM `yuemi_sale`.`ext_spu` WHERE id = " . $spuid;
		$id = $this->MySQL->scalar($cid);

		$supplier = \yuemi_main\SupplierFactory::Instance()->load($id);
		$tableName = '`yuemi_sale`.`ext_' . $supplier->alias . '_catagory`';

		$sql = "SELECT ep.*,et.name AS c_name, spu.title AS spu_name, cate.name AS category_name, me.file_url AS img_url, b.name AS brand_name " .
				" FROM  `yuemi_sale`.`ext_spu` AS ep " .
				" LEFT JOIN {$tableName} AS `et` ON ep.ext_cat_id = et.id " .
				" LEFT JOIN `yuemi_sale`.`spu` AS `spu` ON ep.spu_id = spu.id " .
				" LEFT JOIN `yuemi_sale`.`catagory` AS `cate` ON ep.catagory_id = cate.id " .
				" LEFT JOIN `yuemi_sale`.`ext_spu_material` AS `me` ON ep.id = me.ext_spu_id " .
				" LEFT JOIN `yuemi_sale`.`brand` AS `b` ON ep.brand_id = b.id " .
				" where ep.id = {$spuid}";

		$row = $this->MySQL->row($sql);

		switch ($id) {
			case 2:
				$row['supplier'] = "内购";
				break;
			case 3:
				$row['supplier'] = "贡云";
				break;
		}
		return [
			'res' => $row
		];
	}

}
