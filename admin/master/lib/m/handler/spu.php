<?php

include "lib/AdminHandler.php";

/**
 * 售卖管理
 * @auth
 */
class spu_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0) {
		
	}

	/**
	 * 有效SPU
	 */
	public function spu_a(int $p = 0, int $sid = 0, int $clid = 0, int $bid = 0, string $q = '') {
		$this->Cacher->loadSupplier();
		$this->Cacher->loadBrand();
		$this->Cacher->loadCatagory();
		$whr = [
			'`status` = 1'
		];
		$sql = "SELECT `id`,`catagory_id`,`supplier_id`,`brand_id`,`title`,`serial`,`att_refund` "
				. "FROM `yuemi_sale`.`spu` ";

		if ($clid > 0) {
			$whr[] = "`catagory_id` = $clid";
		}
		if ($bid > 0) {
			$whr[] = "`brand_id` = $bid";
		}
		if ($sid > 0) {
			$whr[] = "`supplier_id` = $sid";
		}
		if (strlen($q) > 1) {
			$whr[] = "`title` LIKE '%" . $this->MySQL->encode($q) . "%'";
		}

		$sql .= ' WHERE ' . implode(' AND ', $whr);
		$sql .= ' ORDER BY `id` DESC';

		$res = $this->MySQL->paging($sql, 30, $p);

		return [
			'data' => $res,
			'Supplier' => $this->Cacher->supplier,
			'Catagory' => $this->Cacher->cataogry,
			'Brand' => $this->Cacher->brand
		];
	}

	/**
	 * 无效SPU
	 */
	public function spu_b(int $p = 0, int $sid = 0, int $clid = 0, int $bid = 0, string $q = '') {
		$this->Cacher->loadSupplier();
		$this->Cacher->loadBrand();
		$this->Cacher->loadCatagory();
		$whr = [
			'`status` = 0'
		];
		$sql = "SELECT `id`,`catagory_id`,`supplier_id`,`brand_id`,`title`,`serial`,`att_refund` "
				. "FROM `yuemi_sale`.`spu` ";

		if ($clid > 0) {
			$whr[] = "`catagory_id` = $clid";
		}
		if ($bid > 0) {
			$whr[] = "`brand_id` = $bid";
		}
		if ($sid > 0) {
			$whr[] = "`supplier_id` = $sid";
		}
		if (strlen($q) > 1) {
			$whr[] = "`title` LIKE '%" . $this->MySQL->encode($q) . "%'";
		}

		$sql .= ' WHERE ' . implode(' AND ', $whr);
		$sql .= ' ORDER BY `id` DESC';

		$res = $this->MySQL->paging($sql, 30, $p);
		$supplier = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_main`.`supplier`");
		$brand = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_sale`.`brand`");

		return [
			'data' => $res,
			'Supplier' => $this->Cacher->supplier,
			'Catagory' => $this->Cacher->cataogry,
			'Brand' => $this->Cacher->brand
		];
	}

	/**
	 * 素材管理
	 * @param int $p		分页
	 * @param int $sku_id		sku_ID
	 * @return type
	 */
	public function material(int $p = 0, int $t = 0, int $sku_id = 0) {
		$spu = null;
		$sku = null;
		if ($t == 0) {
			$sql = 'SELECT * FROM `yuemi_sale`.`spu_material` WHERE `type` = 0';
			if ($sku_id > 0) {
				$sql .= ' AND `spu_id` = ' . $sku_id;
				$spu = \yuemi_sale\SpuFactory::Instance()->load($sku_id);
			}
		} else if ($t == 1) {
			$sql = 'SELECT * FROM `yuemi_sale`.`spu_material` WHERE `type` = 1';
			if ($sku_id > 0) {
				$sql .= ' AND `spu_id` = ' . $sku_id;
				$spu = \yuemi_sale\SpuFactory::Instance()->load($sku_id);
			}
		} else if ($t == 2) {
			$sql = 'SELECT * FROM `yuemi_sale`.`sku_material` WHERE `type` = 0';
			if ($sku_id > 0) {
				$sql .= ' AND `sku_id` = ' . $sku_id;
				$sku = \yuemi_sale\SkuFactory::Instance()->load($sku_id);
			}
		} else if ($t == 3) {
			$sql = 'SELECT * FROM `yuemi_sale`.`sku_material` WHERE `type` = 1';
			if ($sku_id > 0) {
				$sql .= ' AND `sku_id` = ' . $sku_id;
				$sku = \yuemi_sale\SkuFactory::Instance()->load($sku_id);
			}
		} else {
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.material');
		}


		$sql .= ' ORDER BY `id`';
		$res = $this->MySQL->paging($sql, 25, $p);

		return [
			'SPU' => $spu,
			'SKU' => $sku,
			'Data' => $res
		];
	}

	public function spu(int $p = 0, int $sid = 0, int $did = 0, int $clid = 0, int $bid = 0) {
		$whr = [];
		$whe = [];
		if ($this->Context->Runtime->ticket->postback) {
			$whe = [];

			if ($_POST['key_serch'] !== '') {
				array_push($whe, "`p`.`title` LIKE '%{$this->MySQL->encode($_POST['key_serch'])}%'");
			}
			if ($_POST['status_serch'] !== '-1') {
				array_push($whe, "`p`.`status` = '{$this->MySQL->encode($_POST['status_serch'])}'");
			}
			if ($_POST['brand_serch'] !== '0') {
				array_push($whe, "`p`.`brand_id` = '{$this->MySQL->encode($_POST['brand_serch'])}'");
			}
			if ($_POST['supplier_serch'] !== '0') {
				array_push($whe, "`p`.`supplier_id` = '{$this->MySQL->encode($_POST['supplier_serch'])}'");
			}
		}
		$sql = "SELECT p.*,`c`.`name` AS `name_1` "
				. "FROM `yuemi_sale`.`spu` AS `p` "
				. "LEFT JOIN `yuemi_sale`.`catagory` AS `c` ON `c`.`id` = `p`.`catagory_id` ";
		if ($clid > 0) {
			$whr[] = "`catagory_id` = $clid";
		}
		if ($bid > 0) {
			$whr[] = "`brand_id` = $bid";
		}
		if ($sid > 0) {
			$whr[] = "`supplier_id` = $sid";
		}
		if ($whr || $whe) {
			$sql .= 'WHERE ' . implode(' AND ', $whr) . implode(' AND ', $whe);
		}
		$sql .= ' ORDER BY `id` DESC';
		$res = $this->MySQL->paging($sql, 30, $p);
		$supplier = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_main`.`supplier`");
		$brand = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_sale`.`brand`");
		for ($i = 0; $i < count($res->Data); $i++) {
			$res->Data[$i]['online_time'] = date('Y-m-d H:i:s', $res->Data[$i]['online_time']);
			$res->Data[$i]['offline_time'] = date('Y-m-d H:i:s', $res->Data[$i]['offline_time']);
		}
		return [
			'data' => $res,
			'supplier' => $supplier,
			'brand' => $brand
		];
	}

	public function spu_info($spu_id) {
		$sql = "SELECT p.*,c.name AS c_name ,b.name AS b_name ,s.`name` AS s_name "
				. " FROM `yuemi_sale`.`spu` AS p "
				. " LEFT JOIN `yuemi_sale`.`catagory` AS c ON p.`catagory_id` = c.`id` "
				. " LEFT JOIN `yuemi_main`.`supplier` AS s ON p.`supplier_id` = s.`id` "
				. " LEFT JOIN `yuemi_sale`.`brand` AS b ON p.`brand_id` = b.`id` "
				. " WHERE  p.id = {$spu_id}";
		$row = $this->MySQL->row($sql);
		$row['online_time'] = date('Y-m-d H:i:s', $row['online_time']);
		$row['offline_time'] = date('Y-m-d H:i:s', $row['offline_time']);
		$sql1 = "SELECT `is_default` AS IsDefault,`id` AS Id,`file_url` AS Picture , `thumb_url` AS Thumb  FROM `yuemi_sale`.`spu_material` "
				. "  WHERE `spu_id` = {$spu_id} AND `type` = 0";
		$ImgMain = $this->MySQL->grid($sql1);
		$sql2 = "SELECT `is_default` AS IsDefault,`id` AS Id,`file_url` AS Picture , `thumb_url` AS Thumb  FROM `yuemi_sale`.`spu_material` "
				. "  WHERE `spu_id` = {$spu_id} AND `type` = 1";
		$ImgCont = $this->MySQL->grid($sql2);
		$sql3 = "SELECT `is_default` AS IsDefault,`id` AS Id,`file_url` AS Picture , `thumb_url` AS Thumb  FROM `yuemi_sale`.`spu_material` "
				. "  WHERE `spu_id` = {$spu_id} AND `type` = 2";
		$ImgLoop = $this->MySQL->grid($sql3);

		return [
			'res' => $row,
			'ImgMain' => $ImgMain,
			'ImgCont' => $ImgCont,
			'ImgLoop' => $ImgLoop
		];
	}

	/**
	 * spu 详情修改
	 * @param int $id
	 * @return type
	 * @throws \Ziima\MVC\Redirector
	 */
	public function edit_ext_spu(int $id = 0) {
		
		$id = intval($_POST['id']);
		$catagory_id = intval($_POST['catagory_id']);
		$brand_id = intval($_POST['brand_id']);
		$intro = $this->MySQL->encode($_POST['intro']);
		$title = $this->MySQL->encode($_POST['title']);
		$barcode = $this->MySQL->encode($_POST['barcode']);
		$serial = $this->MySQL->encode($_POST['serial']);
		$weight = floatval($_POST['weight']);
		$unit = $this->MySQL->encode($_POST['unit']);
		$online_time = strtotime($_POST['online_time']);
		$offline_time = strtotime($_POST['offline_time']);
		$specs = $_POST['specs'];
		$ss = $this->MySQL->row("SELECT `supplier_id`,`status` FROM `yuemi_sale`.`spu` WHERE `id` = {$id}");
		$supplier_id = $ss['supplier_id'];
		$status = $ss['status'];
		
		$sqlcool = "UPDATE `yuemi_sale`.`spu` SET `catagory_id` = {$catagory_id} ,`brand_id` " .
				"= {$brand_id} , `intro` = '{$intro}', `title` = '{$title}'," .
				" `barcode` = '{$barcode}',`serial` = '{$serial}',`specs` = '{$specs}',`weight` = {$weight},`unit` = '{$unit}',`online_time` = '{$online_time}'," .
				"`offline_time` = '{$offline_time}' WHERE `id` = {$id}";

		$this->MySQL->execute($sqlcool);
		if ($_POST['ty'] == 'a') {
			throw new \Ziima\MVC\Redirector("/index.php?call=spu.spu_a");
		} elseif ($_POST['ty'] == 'b') {
			throw new \Ziima\MVC\Redirector("/index.php?call=spu.spu_b");
		}
	}

	/**
	 * spu 详情修改
	 * @param int $id
	 * @return type
	 * @throws \Ziima\MVC\Redirector
	 */
	public function edit_spu_info(int $id = 0, string $ty = 'a') {

		$cid = "SELECT `supplier_id` FROM `yuemi_sale`.`spu` WHERE id = " . $id;

		$myid = $this->MySQL->scalar($cid);

		$supplier = \yuemi_main\SupplierFactory::Instance()->load($myid);
		$tableName = '`yuemi_sale`.`ext_' . $supplier->alias . '_catagory`';

		$sql = "SELECT p.*,c.name AS c_name, b.name AS b_name" .
				" FROM  `yuemi_sale`.`spu` AS p " .
				" LEFT JOIN `yuemi_sale`.`catagory` AS	c ON p.catagory_id = c.id " .
				" LEFT JOIN `yuemi_sale`.`brand` AS b ON p.brand_id = b.id " .
				" where p.id = {$id}";
		$row = $this->MySQL->row($sql);
		
		$sql2 = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0";
		$res2 = $this->MySQL->grid($sql2);
		$psql = "SELECT `id`,`name` FROM `yuemi_sale`.`brand` ";
		$pp = $this->MySQL->grid($psql);
		$cid = $row['catagory_id'];
		$ifelse = $this->MySQL->row("SELECT ID FROM" . $tableName);
		$sql5 = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0";
		$res5 = $this->MySQL->grid($sql5);

		if (empty($ifelse)) {
			$catagory = '';
		} else {

			$catagory = $this->get_str(($cid == '0') ? 1 : $cid, $row['supplier_id']);
		}
		$sql = "SELECT * FROM `yuemi_sale`.`spu` WHERE `id` = {$row['id']}";
		$re = $this->MySQL->row($sql);
		$sp = explode("\r\n", $re['specs']);

		return [
			'ty' => $ty,
			'res' => $row,
			'res2' => $res2,
			'brand' => $pp,
			'catagory' => $catagory,
			'res5' => $res5,
			'specs' => $sp
		];
	}

	/**
	 *
	 * @param int $cid  分类catagoryId
	 * @param int $tid  supplierId
	 * @return type
	 */
	private function get_str(int $cid, int $tid = 0) {
		$arr = [];
		while ($cid) {
			$arr[] = ($this->get_catagory($cid, $tid))['str'];
			$cid = ($this->get_catagory($cid, $tid))['pid'];
		}
		$count = count($arr);
		$str = '';
		for ($i = $count; $i > 0; $i--) {
			$str .= $arr[$i - 1];
		}
		return $str;
	}

	/**
	 *
	 * @param int $cid  分类catagoryId
	 * @param int $tid  supplierId
	 * @return type
	 */
	private function get_catagory(int $cid, int $tid = 0) {

		$supplier = \yuemi_main\SupplierFactory::Instance()->load($tid);
		$tableName = '`yuemi_sale`.`ext_' . $supplier->alias . '_catagory`';
		$sql = "SELECT * FROM " . $tableName . " WHERE `id` = {$cid}";
		$re = $this->MySQL->row($sql);
		$pid = $re['parent_id'];
		$sql1 = "SELECT * FROM " . $tableName . " WHERE `parent_id` = {$pid}";
		$re1 = $this->MySQL->grid($sql1);
		$str = '<select onchange="get_catagory(this.value,this)" name="catagory_id" id="catagory_id" style="width:100px;background: white;">';

		foreach ($re1 as $val) {
			if ($val['id'] == $cid) {
				$str .= '<option value="' . $val['id'] . '" selected="selected" >' . $val['name'] . '</option>';
			} else {
				$str .= '<option value="' . $val['id'] . '">' . $val['name'] . '</option>';
			}
		}
		$str .= '</select>';
		return [
			'pid' => $pid,
			'str' => $str
		];
	}

	/**
	 * 图片通过审核
	 */
	public function throw_picture(int $id = 0, int $t = 0) {
		if ($t == 0 || $t == 1) {
			$sku_id = $this->MySQL->scalar("SELECT sku_id FROM `yuemi_sale`.`spu_material` WHERE id = {$id}");
			$this->MySQL->execute("UPDATE `yuemi_sale`.`spu_material` SET status = 1 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector("/index.php?call=spu.material&sku_id={$sku_id}&t={$t}");
		} elseif ($t == 2 || $t == 3) {
			$sku_id = $this->MySQL->scalar("SELECT sku_id FROM `yuemi_sale`.`sku_material` WHERE id = {$id}");
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku_material` SET status = 1 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector("/index.php?call=spu.material&sku_id={$sku_id}&t={$t}");
		} else {
			throw new \Ziima\MVC\Redirector("/index.php?call=spu.material&sku_id={$sku_id}&t={$t}");
		}
	}

	public function hit_picture(int $id = 0, int $t = 0) {
		if ($t == 0 || $t == 1) {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`spu_material` SET status = 2 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector("/index.php?call=spu.material&t={$t}");
		} elseif ($t == 2 || $t == 3) {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku_material` SET status = 2 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector("/index.php?call=spu.material&t={$t}");
		} else {
			throw new \Ziima\MVC\Redirector("/index.php?call=spu.material&t={$t}");
		}
	}

}
