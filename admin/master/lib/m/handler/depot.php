<?php

include "lib/AdminHandler.php";

/**
 * 库存管理
 * @auth
 */
class depot_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0) {
		
	}

	public function sub_shop(int $p = 0, int $zid = 0) {
		$sql = "SELECT * "
				. "FROM ext_supplier";
		$result = $this->MySQL->paging($sql, 30, $p, $clid);
	}

	public function catagory(int $p = 0, int $clid = 0) {
		$clobj = null;
		if ($clid > 0) {
			$clobj = \yuemi_sale\CatagoryFactory::Instance()->load($clid);
			if ($clobj === null) {
				throw new \Ziima\MVC\Redirector('/index.php?call=depot.catagory');
			}
		}
		$sql = "SELECT `c`.*,`s`.`name` AS `supplier_name`,`u`.`name` AS `manager_name` " .
				"FROM `yuemi_sale`.`catagory` AS `c` " .
				"LEFT JOIN `yuemi_main`.`supplier` AS `s` ON `s`.`id` = `c`.`supplier_id` " .
				"LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `c`.`manager_id` ";
		$whr = [];
		if ($clid > 0) {
			$whr[] = "`parent_id` = $clid";
		} else {
			$whr[] = "`parent_id` = 0";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$sql .= ' ORDER BY `c`.`p_order` DESC';
		$result = $this->MySQL->paging($sql, 30, $p, $clid);

		return [
			'ParentCatagory' => $clobj,
			'Result' => $result
		];
	}

	public function changehidden(int $id = 0, int $val = 0) {
		if ($val > 0) {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`catagory` SET `is_hidden` = 0 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.catagory');
		} else {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`catagory` SET `is_hidden` = 1 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.catagory');
		}
	}

	public function changeinternal(int $id = 0, int $val = 0) {
		if ($val > 0) {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`catagory` SET `is_internal` = 0 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.catagory');
		} else {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`catagory` SET `is_internal` = 1 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.catagory');
		}
	}

	public function changeprivate(int $id = 0, int $val = 0) {
		if ($val > 0) {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`catagory` SET `is_private` = 0 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.catagory');
		} else {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`catagory` SET `is_private` = 1 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.catagory');
		}
	}

	/**
	 * 供应商子店铺
	 * @param int $clid
	 */
	public function ext_child_shop(int $p = 0, int $clid = 0) {
		$res = $this->MySQL->paging("SELECT * " .
				"FROM `yuemi_sale`.`ext_supplier` " .
				"WHERE `supplier_id` = {$clid}", 30, $p);
		return [
			'shop' => $res
		];
	}

	/**
	 * 新增供货商
	 * @throws \Ziima\MVC\Redirector
	 */
	public function supplier_create() {
		if ($this->Context->Runtime->ticket->postback) {
			$mob = (int) $_POST['user_mobile'];
			$row = $this->MySQL->row("SELECT `id` " .
					"FROM `yuemi_main`.`user` " .
					"WHERE `mobile` = {$mob}");
			if (empty($row)) {
				$name = $this->MySQL->encode($_POST['corp_name']);
				$UserEntity = new \yuemi_main\UserEntity();
				$UserEntity->name = $name;
				$UserEntity->password = sha1(SECURITY_SALT_USER . '/' . $_POST['user_password']);
				$UserEntity->mobile = $mob;
				$UserFactory = new \yuemi_main\UserFactory(MYSQL_WRITER, MYSQL_READER);
				$UserFactory->insert($UserEntity);
			}
			$row = $this->MySQL->row("SELECT `id` " .
					"FROM `yuemi_main`.`user` " .
					"WHERE `mobile` = {$mob}");
			if (!empty($row['id'])) {
				$user_id = $row['id'];
				$pass = sha1(SECURITY_SALT_USER . '/' . $_POST['user_password']);
				$corp_name = $this->MySQL->encode($_POST['corp_name']);
				$corp_alias = $this->MySQL->encode($_POST['corp_alias']);
				$pi_enable = 0;
				$po_enable = 0;
				if (isset($_POST['pi_enable'])) {
					$pi_enable = 1;
				}
				if (isset($_POST['po_enable'])) {
					$po_enable = 1;
				}
				$SupplierEntity = new \yuemi_main\SupplierEntity();
				$SupplierEntity->user_id = $user_id;
				$SupplierEntity->name = $corp_name;
				$SupplierEntity->alias = $corp_alias;
				$SupplierEntity->password = $pass;
				$SupplierEntity->status = 1;
				$SupplierEntity->pi_enable = $pi_enable;
				$SupplierEntity->po_enable = $po_enable;
				$SupplierFactory = new \yuemi_main\SupplierFactory(MYSQL_WRITER, MYSQL_READER);
				$SupplierFactory->insert($SupplierEntity);
				$sup_id = $SupplierEntity->id;
				$SupplierCertEntity = new \yuemi_main\SupplierCertEntity();
				$SupplierCertEntity->supplier_id = $sup_id;
				$SupplierCertEntity->corp_name = $this->MySQL->encode($_POST['corp_name']);
				$SupplierCertEntity->corp_serial = $this->MySQL->encode($_POST['corp_serial']);
				$SupplierCertEntity->corp_law = $this->MySQL->encode($_POST['corp_law']);
				$SupplierCertEntity->corp_status = 0;
				$SupplierCertEntity->bank_status = 0;
				$SupplierCertEntity->bond_status = 0;
				$SupplierCertFactory = new \yuemi_main\SupplierCertFactory(MYSQL_WRITER, MYSQL_READER);
				$SupplierCertFactory->insert($SupplierCertEntity);
				$this->MySQL->execute("UPDATE `yuemi_main`.`user` " .
						"SET `level_s` = 2, " .
						"`password` = '{$pass}' " .
						"WHERE `id` = {$user_id}");
			} 

			throw new \Ziima\MVC\Redirector('/index.php?call=depot.supplier');
		}
	}

	public function brand_create() {
		if ($this->Context->Runtime->ticket->postback) {
			$supplier_id = intval($_POST['supplier_id']);
			$name = $this->MySQL->encode($_POST['name']);
			$rol = $this->MySQL->row("SELECT `id`,`name` FROM `yuemi_sale`.`brand` WHERE `name` = '{$name}' ");
			if(!empty($rol)){
				throw new \Ziima\MVC\Redirector('/index.php?call=depot.brand_create&msg=1');
			}
			$BrandEntity = new \yuemi_sale\BrandEntity();
			$BrandEntity->supplier_id = $supplier_id;
			$BrandEntity->name = $name;
			$BrandEntity->alias = $this->MySQL->encode($_POST['alias']);
			
			$BrandFactory = new \yuemi_sale\BrandFactory(MYSQL_WRITER, MYSQL_READER);
			if (!$BrandFactory->insert($BrandEntity)) {
				throw new \Exception('插入表Brand失败！');
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=depot.brand');
		}

		$sql = "SELECT `id`,`name` FROM `yuemi_main`.`supplier` WHERE `status` = 1";
		$result = $this->MySQL->grid($sql);

		return [
			'Result' => $result
		];
	}

	public function supplier(int $p = 0, string $m = '', string $n = '') {
		$sql = "SELECT `s`.*,`uw`.`name` AS `wname`,`uw`.`id` AS `wid` FROM `yuemi_main`.`supplier` AS `s` LEFT JOIN `yuemi_main`.`user_wechat` AS `uw` ON `uw`.`user_id` = `s`.`user_id` ";
		$whr = [];
		if ($m != '') {
			$whr[] = " `uw`.`mobile` = '{$m}' ";
		}
		if ($n != '') {
			$whr[] = " `uw`.`name` LIKE '%{$n}%' ";
		}
		if (!empty($whr)) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$sql .= " ORDER BY `id` DESC";
		$result = $this->MySQL->paging($sql, 30, $p);
		if ($result->Data) {
			$uids = [];
			foreach ($result->Data as &$sup) {
				if ($sup['user_id'] > 0 && !in_array($sup['user_id'], $uids))
					$uids[] = $sup['user_id'];
				$sup['user_mobile'] = '';
				$sup['user_name'] = '';
				$sup['user_wechat'] = 0;
			}
			if (!empty($uids)) {
				$u = $this->MySQL->hash("SELECT `id`,`mobile`,`name` FROM `yuemi_main`.`user` WHERE `id` IN (%s)", 'id', implode(',', $uids));
				if ($u) {
					foreach ($result->Data as &$sup) {
						$uid = $sup['user_id'];
						if (!array_key_exists($uid, $u))
							continue;
						$sup['user_mobile'] = $u[$uid]['mobile'];
						$sup['user_name'] = $u[$uid]['name'];
					}
				}
				$w = $this->MySQL->map("SELECT `id`,`user_id`,`name` FROM `yuemi_main`.`user_wechat` WHERE `user_id` IN (%s)", 'user_id', 'id', implode(',', $uids));
				if ($w) {
					foreach ($result->Data as &$sup) {
						$sup['user_wechat'] = $w[$sup['user_id']] ?? 0;
					}
				}
			}
		}
		return [
			'Result' => $result
		];
	}

	public function brand(int $p = 0, int $sid = 0,string $e = '',string $na = '',string $sn = '') {
		$sql = "SELECT `b`.*,`s`.`name` AS `sname` FROM `yuemi_sale`.`brand` AS `b` LEFT JOIN `yuemi_main`.`supplier` AS `s` ON `s`.`id` = `b`.`supplier_id` ";
		$whr = [];
		
		if ($e != '') {
			$whr[] = " `b`.`alias` LIKE '%{$e}%' ";
		}
                if ($na != '') {
			$whr[] = " `b`.`name` LIKE '%{$na}%' ";
		}
                 if ($sn != '') {
			$whr[] = " `s`.`name` LIKE '%{$sn}%' ";
		}
		if (!empty($whr)) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
//                echo $sql;die;
		$sql .= " ORDER BY `id` DESC";
		$result = $this->MySQL->paging($sql, 30, $p);
		
		return [
			'Result' => $result
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

	/**
	 * 供应商密码重置
	 * by wanghao 2018/4/4
	 * @param int $id
	 * @return type
	 * @throws \Ziima\MVC\Redirector
	 */
//	public function reset_password() {
//		if ($this->Context->Runtime->ticket->postback) {
//			$new_password = $_POST['new_password'];
//			$id = $_POST['id'];
//			$sql = "UPDATE `yuemi_main`.`supplier` "
//					. "SET  password = '" . $new_password . "' "
//					. "WHERE  id = '" . $id . "'";
//			if ($this->MySQL->execute($sql)) {
//				throw new \Ziima\MVC\Redirector('/index.php?call=depot.supplier');
//			}
//		} else {
//			$id = $_GET['id'];
//			$sql = "SELECT * FROM `yuemi_main`.`supplier` "
//					. "WHERE id = '" . $id . "' ";
//			$res = $this->MySQL->row($sql);
//
//			return [
//				'res' => $res
//			];
//		}
//	}

	/**
	 * 外部spu向内部spu上架
	 * by wanghao 2018/4/8
	 * @param int $pid
	 * @param int $id
	 * @throws \Ziima\MVC\Redirector
	 */
	public function change_extspu(int $pid = 0, int $id = 0) {
		$sql1 = "select id " .
				"from `yuemi_sale`.`spu` " .
				"where id = $pid";
		$res1 = $this->MySQL->row($sql1);
		if (!empty($res1['id'])) {
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.spu');
		} else {
			$sql2 = "select * " .
					"from `yuemi_sale`.`ext_spu`" .
					" where id = $id";
			//查mapid
			$res2 = $this->MySQL->row($sql2);
			$supid = $res2['supplier_id'];
			$catoldid = $res2['ext_cat_id'];
			$supplier = \yuemi_main\SupplierFactory::Instance()->load($supid);
			$tableName = 'ext_' . $supplier->alias . '_catagory';
			$mapid = $this->MySQL->row("SELECT `map_id` FROM `yuemi_sale`." . $tableName . " WHERE id = {$catoldid}");
			if (isset($mapid['map_id'])) {
				$catid = $mapid['map_id'];
			} else {
				$catid = 0;
			}

			$SkuEntity = new \yuemi_sale\SkuEntity();
			$SkuEntity->supplier_id = (int) $res2['supplier_id'];
			$SkuEntity->brand_id = (int) $res2['brand_id'];
			$SkuEntity->specs = $this->MySQL->encode($res2['specs']);
			$SkuEntity->title = $this->MySQL->encode($res2['title']);
			$SkuEntity->create_time = (int) time();
			$SkuEntity->video = $this->MySQL->encode($res2['video']);
			$SkuEntity->intro = $this->MySQL->encode($res2['intro']);
			$SkuEntity->create_user = (int) $this->User->id;
			$SkuEntity->status = (int) 1;
			$SkuEntity->catagory_id = (int) $catid;
			$SkuFactory = new \yuemi_sale\SkuFactory(MYSQL_WRITER, MYSQL_READER);
			$SkuFactory->insert($SkuEntity);

			$lastid = $SkuEntity->id;
			$sql4 = "UPDATE `yuemi_sale`.`ext_spu`" .
					" SET `spu_id` = $lastid" .
					" WHERE `id` = $id";
			$this->MySQL->execute($sql4);

			$sql5 = "SELECT * " .
					"FROM `yuemi_sale`.`ext_sku`" .
					" where `ext_spu_id` = $id";
			$ressku = $this->MySQL->grid($sql5);
			$num = count($ressku);
			for ($i = 0; $i < $num; $i++) {
				$rst = \yuemi_sale\ProcedureInvoker::Instance()->import_sku(
						$this->User->id,
						$ressku[$i]['id'],
						(float) $_SERVER["REMOTE_ADDR"]);
				if (!empty($rst) || $rst->ReturnValue == 'OK') {
					$this->MySQL->execute("UPDATE `yuemi_sale`.`ext_sku` SET `sku_id` = {$rst->SkuId} WHERE `id` = {$ressku[$i]['id']} ");
					$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `catagory_id` = {$catid} WHERE `id` = {$rst->SkuId}");
				}
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.spu');
		}
	}

	/**
	 * 外部sku向内部sku上架
	 * by wanghao 2018/4/9
	 * @param int $ext_spu_id
	 * @param int $id
	 * @throws \Ziima\MVC\Redirector
	 */
	public function change_extsku(int $ext_spu_id = 0, int $id = 0, int $sku_id = 0) {
		$rst = \yuemi_sale\ProcedureInvoker::Instance()->import_sku(
				$this->User->id,
				$id,
				(float) $_SERVER["REMOTE_ADDR"]);
		if (!empty($rst) || $rst->ReturnValue == 'OK') {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`ext_sku` SET `sku_id` = {$rst->SkuId} WHERE `id` = {$id} ");
			$skuid = $rst->SkuId;
			$ll = $this->MySQL->row("SELECT `ext_spu_id`,`supplier_id` FROM `yuemi_sale`.`ext_sku` WHERE `id` = {$id} ");
			$catidold = $this->MySQL->row("SELECT `ext_cat_id` FROM `yuemi_sale`.`ext_spu` WHERE `id` = {$ll['ext_spu_id']}");

			$supplier = \yuemi_main\SupplierFactory::Instance()->load($ll['supplier_id']);
			$tableName = 'ext_' . $supplier->alias . '_catagory';
			$mapid = $this->MySQL->row("SELECT `map_id` FROM `yuemi_sale`." . $tableName . " WHERE id = {$catidold['ext_cat_id']}");
			if (isset($mapid['map_id'])) {
				$catid = $mapid['map_id'];
			} else {
				$catid = 0;
			}
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `catagory_id` = {$catid} WHERE `id` = {$skuid} ");
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.sku');
		}
	}

	/**
	 * 外到内改分类
	 * @param int $nid
	 */
	public function updateCat(int $nid = 0) {
		
	}

	public function sku_detail(int $p = 0, int $id = 0) {

		$sql = "SELECT sk.*,bb.name as ppname " .
				"FROM `yuemi_sale`.`sku` as sk " .
				"left join `yuemi_sale`.`spu` as sp on sk.spu_id = sp.id " .
				"left join `yuemi_sale`.brand as bb on bb.id = sp.brand_id " .
				"WHERE sk.id = $id";

		$res = $this->MySQL->row($sql);
		$supplier = \yuemi_main\SupplierFactory::Instance()->load($res['supplier_id']);
		$tableName = 'ext_' . $supplier->alias . '_catagory';
		$sql = "SELECT stb.* FROM `yuemi_sale`.`sku` as ek " .
				"LEFT JOIN `yuemi_sale`." . $tableName . " as `stb` ON `stb`.`id` = `ek`.`catagory_id` WHERE ek.id ={$res['id']}";
		$row = $this->MySQL->row($sql);
		$str = '';
		if (isset($row['name'])) {
			$str = $row['name'];
		}
		if (isset($row['parent_id'])) {
			$nn2 = $this->MySQL->row("SELECT name FROM `yuemi_sale`." . $tableName . " WHERE id = {$row['parent_id']}");
			$str = $nn2['name'] . ' - ' . $str;
		} else {
			
		}
		if (empty($row)) {
			$res['cats'] = '';
		} else {
			$res['cats'] = $str;
		}
		return[
			'data' => $res
		];
	}

	public function shelf_deetail(int $p = 0, int $id = 0) {

		$sql = "SELECT S.*,U1.name AS name_1,U2.name AS name_2,U3.name AS name_3 "
				. "FROM `yuemi_sale`.`shelf_info` AS `S` "
				. "LEFT JOIN `yuemi_main`.`user` AS `U1`  ON S.create_user = U1.id "
				. "LEFT JOIN `yuemi_main`.`user` AS `U2`  ON S.update_user = U2.id "
				. "LEFT JOIN `yuemi_main`.`user` AS `U3`  ON S.audit_user = U3.id "
				. "WHERE `shelf_id`='" . $id . "'";
		$row = $this->MySQL->grid($sql);
		return[
			'data' => $row
		];
	}

	public function spu_detail(int $p = 0, int $spuid = 0) {
		$sql = "SELECT * FROM `yuemi_sale`.`spu` AS `S` "
				. "WHERE `id`='" . $spuid . "'";

		$row = $this->MySQL->grid($sql);
		return[
			'data' => $row[0]
		];
	}

	public function ext_catagory(int $suid, int $p = 0, int $clid = 0) {
		$supplier = \yuemi_main\SupplierFactory::Instance()->load($suid);
		if ($supplier->pi_enable == 0) {
			throw new \Ziima\MVC\Redirector('/index.php?call=depot.supplier');
		}
		$tableName = $supplier->pi_catagory;
		$sql = "SELECT `c`.* " .
				"FROM `yuemi_sale`.`$tableName` AS `c` ";
		$whr = [];
		if ($clid > 0) {
			$whr[] = "`parent_id` = $clid";
		} else {
			$whr[] = "`parent_id` = 0";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$result = $this->MySQL->paging($sql, 30, $p, $clid);
		return [
			'Result' => $result,
			'suid' => $suid
		];
	}

	/**
	 * 供应商下品类更改map_id
	 * @param int $id
	 * @param int $tabid
	 * @return type
	 * @throws \Ziima\MVC\Redirector
	 */
	public function recat_change(int $id = 0, int $tabid = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$id = (int) $_POST['id'];
			$val = (int) $_POST['catagory_id'];
			$supplier = \yuemi_main\SupplierFactory::Instance()->load($_POST['tabid']);
			$tableName = 'ext_' . $supplier->alias . '_catagory';
			if ($this->MySQL->execute("UPDATE `yuemi_sale`." . $tableName . " SET `map_id` = {$val} WHERE `id` = {$id}")) {
				throw new \Ziima\MVC\Redirector('/index.php?call=depot.supplier');
			}
		}
		$sql = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0";
		$res = $this->MySQL->grid($sql);
		$res['id'] = $id;
		$res['tabid'] = $tabid;
		return [
			'res' => $res
		];
	}

	public function ext_sku_info($sku_id) {
		if ($this->Context->Runtime->ticket->postback) {
			$id = (int) $_POST['ext_sku_id'];
			$video = $this->MySQL->encode($_POST['url_video']);
			$intro = $this->MySQL->encode($_POST['intro']);
			$sql2 = "UPDATE `yuemi_sale`.`ext_sku` " .
					"SET `video` = '$video',`intro` = '$intro'" .
					" WHERE `id` = $id";
			$ext_sku_id = (int) $_POST['ext_sku_id'];
			$this->MySQL->execute($sql2);
			header('location:\index.php?call=depot.update_extsku&ext_sku_id=' . $ext_sku_id);
		}
		$sql = 'select `video`,`intro` from `yuemi_sale`.`ext_sku` where `id`=' . $sku_id;
		$row = $this->MySQL->row($sql);
		if (empty($row)) {
			$ret = [
				'intro' => '',
				'url_video' => '',
				'ext_sku_id' => $sku_id
			];
		} else {
			$ret = [
				'intro' => $row['intro'],
				'url_video' => $row['video'],
				'ext_sku_id' => $sku_id
			];
		}

		return $ret;
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
	 * 根据分类子ID，拼接已选中select字符串
	 * @param int $id
	 */
	private function selto_str(int $id = 0, int $suid = 0) {
		$suid = 2;
		$supplier = \yuemi_main\SupplierFactory::Instance()->load($suid);
		$tableName = 'ext_' . $supplier->alias . '_catagory';
	}

	/**
	 * 供应商密码修改
	 * @param int $id
	 * @return type
	 */
	public function supplier_chapass(int $id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$pass = $_POST['pass'];
			$id = (int) $_POST['id'];
			$passw = $pass = sha1(SECURITY_SALT_SUPPLIER . '/' . $pass);
			$this->MySQL->execute("UPDATE `yuemi_main`.`supplier`" .
					" SET `password` = '{$passw}'" .
					" WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector("/index.php?call=depot.supplier");
		}
		$sql = "SELECT * " .
				"FROM `yuemi_main`.`supplier`" .
				" WHERE `id` = {$id}";
		$row = $this->MySQL->row($sql);
		return[
			'res' => $row
		];
	}

	public function supplier_change(int $id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$id = $_POST['id'];
			$name = $this->MySQL->encode($_POST['name']);
			$alias = $this->MySQL->encode($_POST['alias']);
			$corp_name = $this->MySQL->encode($_POST['corp_name']);
			$corp_serial = $this->MySQL->encode($_POST['corp_serial']);
			$corp_law = $this->MySQL->encode($_POST['corp_law']);
			if (isset($_POST['pi_enable'])) {
				$pi_enable = 1;
			} else {
				$pi_enable = 0;
			}
			if (isset($_POST['po_enable'])) {
				$po_enable = 1;
			} else {
				$po_enable = 0;
			}
			$this->MySQL->execute("UPDATE `yuemi_main`.`supplier` SET" .
					" `name` = '{$name}',`alias` = '{$alias}',pi_enable = {$pi_enable},po_enable = {$po_enable} WHERE `id` = {$id}");
			$this->MySQL->execute("UPDATE `yuemi_main`.`supplier_cert`" .
					" SET `corp_name` = '{$corp_name}',`corp_serial` " .
					"= '{$corp_serial}',`corp_law` = '{$corp_law}' WHERE `supplier_id` = {$id}");
			throw new \Ziima\MVC\Redirector("/index.php?call=depot.supplier");
		}
		$res = $this->MySQL->row("SELECT su.*,sc.corp_name,sc.corp_serial,sc.corp_law " .
				"FROM `yuemi_main`.`supplier` AS `su` " .
				"LEFT JOIN `yuemi_main`.`supplier_cert` AS `sc` ON su.id = sc.supplier_id " .
				"WHERE su.id = {$id}");
		return[
			'res' => $res
		];
	}

	/**
	 * spu素材审核
	 * @param int $id
	 * @param int $go
	 */
	public function pass_spupic(int $id = 0, int $go = 0) {
		$this->MySQL->execute("UPDATE `yuemi_sale`.`spu_material` SET `status` = {$go} WHERE `id` = {$id}");
		throw new \Ziima\MVC\Redirector("/index.php?call=depot.spu_picture");
	}

	public function pass_skupic(int $id = 0, int $go = 0) {
		$this->MySQL->execute("UPDATE `yuemi_sale`.`sku_material` SET `status` = {$go} WHERE `id` = {$id}");
		throw new \Ziima\MVC\Redirector("/index.php?call=depot.sku_picture");
	}

	public function update_brand(int $id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`brand` SET `name` = '{$this->MySQL->encode($_POST['name'])}',`alias` = '{$this->MySQL->encode($_POST['alias'])}' WHERE id = {$_POST['id']}");
			throw new \Ziima\MVC\Redirector("/index.php?call=depot.brand");
		}
		$res = $this->MySQL->row("SELECT * " .
				"FROM `yuemi_sale`.`brand` " .
				"WHERE `id` = {$id}");
		return [
			'res' => $res
		];
	}

}
