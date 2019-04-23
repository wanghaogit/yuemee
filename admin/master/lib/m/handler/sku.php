<?php

include "lib/AdminHandler.php";

/**
 * 售卖管理
 * @auth
 */
class sku_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0) {
		
	}

	public function sku_a(int $p = 0, int $sid = 0, int $bid = 0, int $clid = 0, int $spid = 0, string $q = '', int $catagory_id = 0,int $skuid = 0) {
		$whr = [
			'`k`.`status` = 2'
		];
		$sql = "SELECT `k`.*,`c`.`name` AS `catname`,`s`.`name` AS `sname` FROM `yuemi_sale`.`sku` AS `k` LEFT JOIN `yuemi_sale`.`catagory` AS `c` ON `k`.`catagory_id` = `c`.`id` " .
				"LEFT JOIN `yuemi_main`.`supplier` AS `s` ON `s`.`id` = `k`.`supplier_id`";

		if ($sid > 0) {
			$whr[] = "`k`.`supplier_id` = $sid";
		}
		if ($clid > 0) {
			$whr[] = "`k`.`catagory_id` = $clid";
		}
		if ($catagory_id > 0) {
			$whr[] = "`k`.`catagory_id` = $clid";
		}
		if ($spid > 0) {
			$whr[] = "`k`.`spu_id` = $spid";
		}
		if (strlen($q) > 0) {
			$whr[] = "`k`.`title` LIKE '%" . $this->MySQL->encode($q) . "%'";
		}
		if ($skuid > 0) {
			$whr[] = "`k`.`id` = $skuid";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$sql .= ' ORDER BY `k`.`rebate_vip`';
		$res = $this->MySQL->paging($sql, 10, $p);

		$sql5 = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0";
		$cat = $this->MySQL->grid($sql5);
		$supplier = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`supplier`");
		$brand = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`brand`");
		return [
			'cat' => $cat,
			'data' => $res,
			'supplier' => $supplier,
			'brand' => $brand,
			'GET' => $_GET
		];
	}

	public function sku_b(int $p = 0, int $sid = 0, int $bid = 0, int $clid = 0, int $spid = 0, string $q = '', int $catagory_id = 0,int $skuid = 0) {
		$whr = [
			'`k`.`status` IN (0,1)'
		];
		$sql = "SELECT `k`.*,`c`.`name` AS `catname`,`s`.`name` AS `sname` FROM `yuemi_sale`.`sku` AS `k` LEFT JOIN `yuemi_sale`.`catagory` AS `c` ON `k`.`catagory_id` = `c`.`id` " .
				"LEFT JOIN `yuemi_main`.`supplier` AS `s` ON `s`.`id` = `k`.`supplier_id`";

		if ($sid > 0) {
			$whr[] = "`k`.`supplier_id` = $sid";
		}
		if ($clid > 0) {
			$whr[] = "`k`.`catagory_id` = $clid";
		}
		if ($catagory_id > 0) {
			$whr[] = "`k`.`catagory_id` = $clid";
		}
		if ($spid > 0) {
			$whr[] = "`k`.`spu_id` = $spid";
		}
		if (strlen($q) > 0) {
			$whr[] = "`k`.`title` LIKE '%" . $this->MySQL->encode($q) . "%'";
		}
		if ($skuid > 0) {
			$whr[] = "`k`.`id` = $skuid";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$sql .= ' ORDER BY `k`.`id` DESC';



		$res = $this->MySQL->paging($sql, 10, $p);

		$sql5 = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0";
		$cat = $this->MySQL->grid($sql5);
		$supplier = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`supplier`");
		$brand = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`brand`");

		return [
			'cat' => $cat,
			'data' => $res,
			'supplier' => $supplier,
			'brand' => $brand,
			'GET' => $_GET
		];
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
		if (!empty($row['name'])) {
			$str = $row['name'];
		}
		if (!empty($row['parent_id'])) {
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

	public function sku_c(int $p = 0, int $sid = 0, int $bid = 0, int $clid = 0, int $spid = 0, string $q = '', int $catagory_id = 0,int $skuid = 0) {
		$whr = [
			'`k`.`status` = 3'
		];
		$sql = "SELECT `k`.*,`c`.`name` AS `catname`,`s`.`name` AS `sname` FROM `yuemi_sale`.`sku` AS `k` LEFT JOIN `yuemi_sale`.`catagory` AS `c` ON `k`.`catagory_id` = `c`.`id` " .
				"LEFT JOIN `yuemi_main`.`supplier` AS `s` ON `s`.`id` = `k`.`supplier_id`";


		if ($sid > 0) {
			$whr[] = "`k`.`supplier_id` = $sid";
		}
		if ($clid > 0) {
			$whr[] = "`k`.`catagory_id` = $clid";
		}
		if ($catagory_id > 0) {
			$whr[] = "`k`.`catagory_id` = $clid";
		}
		if ($spid > 0) {
			$whr[] = "`k`.`spu_id` = $spid";
		}
		if (strlen($q) > 0) {
			$whr[] = "`k`.`title` LIKE '%" . $this->MySQL->encode($q) . "%'";
		}
		if ($skuid > 0) {
			$whr[] = "`k`.`id` = $skuid";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$sql .= ' ORDER BY `k`.`id` DESC';


		$res = $this->MySQL->paging($sql, 10, $p);

		$sql5 = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0";
		$cat = $this->MySQL->grid($sql5);
		$supplier = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`supplier`");
		$brand = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`brand`");

		return [
			'cat' => $cat,
			'data' => $res,
			'supplier' => $supplier,
			'brand' => $brand,
			'GET' => $_GET
		];
	}

	public function sku_d(int $p = 0, int $sid = 0, int $bid = 0, int $clid = 0, int $spid = 0, string $q = '', int $catagory_id = 0,int $skuid = 0) {
		$whr = [
			'`k`.`status` = 4'
		];
		$sql = "SELECT `k`.*,`c`.`name` AS `catname`,`s`.`name` AS `sname` FROM `yuemi_sale`.`sku` AS `k` LEFT JOIN `yuemi_sale`.`catagory` AS `c` ON `k`.`catagory_id` = `c`.`id` " .
				"LEFT JOIN `yuemi_main`.`supplier` AS `s` ON `s`.`id` = `k`.`supplier_id`";


		if ($sid > 0) {
			$whr[] = "`k`.`supplier_id` = $sid";
		}
		if ($clid > 0) {
			$whr[] = "`k`.`catagory_id` = $clid";
		}
		if ($catagory_id > 0) {
			$whr[] = "`k`.`catagory_id` = $clid";
		}
		if ($spid > 0) {
			$whr[] = "`k`.`spu_id` = $spid";
		}
		if (strlen($q) > 0) {
			$whr[] = "`k`.`title` LIKE '%" . $this->MySQL->encode($q) . "%'";
		}
		if ($skuid > 0) {
			$whr[] = "`k`.`id` = $skuid";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$sql .= ' ORDER BY `k`.`id` DESC';

		$res = $this->MySQL->paging($sql, 10, $p);

		$sql5 = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0";
		$cat = $this->MySQL->grid($sql5);
		$supplier = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`supplier`");
		$brand = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`brand`");

		return [
			'cat' => $cat,
			'data' => $res,
			'supplier' => $supplier,
			'brand' => $brand,
			'GET' => $_GET
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

	public function update_sku(int $id = 0, int $p = 0, string $ty = 'a') {
		$sql = "SELECT * FROM `yuemi_sale`.`sku` "
				. "WHERE `id`='" . $id . "'";

		$row = $this->MySQL->row($sql);
		$sql = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0";
		$res = $this->MySQL->grid($sql);
		$cid = $row['catagory_id'];
		$supplier = \yuemi_main\SupplierFactory::Instance()->load($row['supplier_id']);
		$tableName = '`yuemi_sale`.`ext_' . $supplier->alias . '_catagory`';
		$ifelse = $this->MySQL->row("SELECT ID FROM" . $tableName);
		if (empty($ifelse)) {
			$catagory = 1;
		} else {
			$catagory = $this->get_str(($cid == '0') ? 1 : $cid, $row['supplier_id']);
		}
		$sql5 = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0";
		$res5 = $this->MySQL->grid($sql5);
		//判断价格类别
		$arr = explode("-", $row['serial']);
		if ($arr[0] == 'JD') {
			$ml = ($row['price_ref'] - $row['price_base']) / $row['price_ref'];
			if ($ml > 0.2) {
				$type = 'JDG';
				$price['cb'] = $row['price_base'] * 1.01;
				$price['db'] = $row['price_ref'];
				$price['sc'] = $row['price_ref'] * 1.1;
				$price['ym'] = $row['price_ref'] - (($row['price_ref'] - $row['price_base'] * 1.01) * 0.56) * 0.1;
				$price['yj'] = ($row['price_ref'] - $row['price_base'] * 1.01) * 0.504;
				$price['sd'] = ($row['price_ref'] * 1.1 - $row['price_ref']) - ((($row['price_ref'] - $row['price_base'] * 1.01) * 0.56) * 0.1);
				$price['zd'] = ($row['price_ref'] - $row['price_base'] * 1.01) * 0.504;
				$price['hy'] = 0;
				$price['yq'] = $row['price_inv'];
				$price['yf'] = 0;
			} else {
				$type = 'JDD';
				$price['cb'] = $row['price_base'];
				$price['db'] = $row['price_ref'];
				$price['sc'] = $row['price_ref'] * 1.1;
				$price['ym'] = $row['price_ref'] - (($row['price_ref'] - $row['price_base']) * 0.56) * 0.1;
				$price['yj'] = ($row['price_ref'] - $row['price_base']) * 0.504;
				$price['sd'] = ($row['price_ref'] * 1.1) - ($row['price_ref'] - (($row['price_ref'] - $row['price_base']) * 0.56) * 0.1);
				$price['zd'] = ($row['price_ref'] - $row['price_base']) * 0.504;
				$price['hy'] = 0;
				$price['yq'] = $row['price_inv'];
				$price['yf'] = 0;
			}
		} elseif ($arr[0] == 'YX') {
			$type = 'YX';
			$price['cb'] = $row['price_base'];
			$price['db'] = $row['price_ref'];
			$price['sc'] = $row['price_ref'] * 1.1;
			$price['ym'] = $row['price_ref'] - (($row['price_ref'] - $row['price_base']) * 0.56) * 0.1;
			$price['yj'] = ($row['price_ref'] - $row['price_base']) * 0.54;
			$price['sd'] = ($row['price_ref'] * 1.1) - ($row['price_ref'] - (($row['price_ref'] - $row['price_base']) * 0.56) * 0.1);
			$price['zd'] = ($row['price_ref'] - $row['price_base']) * 0.54;
			$price['hy'] = 0;
			$price['yq'] = $row['price_inv'];
			$price['yf'] = 0;
		} else {
			$type = '';
			$price['cb'] = $row['price_base'];
			$price['db'] = $row['price_ref'];
			$price['sc'] = $row['price_market'];
			$price['ym'] = $row['price_sale'];
			$price['yj'] = ($row['price_sale'] - $row['price_base']) * 0.56;
			$price['sd'] = $row['price_market'] - $row['price_sale'];
			$price['zd'] = ($row['price_sale'] - $row['price_base']) * 0.56;
			$price['hy'] = 0;
			$price['yq'] = $row['price_inv'];
			$price['yf'] = 0;
		}
		//价格处理结束
		//组分类
		$mycatagory = $row['catagory_id'];
		if ($mycatagory > 0) {
			$catpid = $this->MySQL->row("SELECT `parent_id` FROM `yuemi_sale`.`catagory` WHERE `id` = {$mycatagory} ");
			$catagory_par = $catpid['parent_id'];
			$catagory_chdlist = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = {$catagory_par} ");
		} else {
			$catagory_par = 0;
			$catagory_chdlist = '';
		}

		$spusql = "SELECT * FROM `yuemi_sale`.`spu` WHERE id = {$row['spu_id']}";
		$spudata = $this->MySQL->grid($spusql);
		$spudatas = $this->MySQL->row($spusql);
		if (empty($spudata)) {
			$name = -1;
			$spudata = -1;
		} else {
			//分割成颜色和尺码
			$specs = $spudata[0]['specs'];
			$more_specs = $this->spec($specs); //只返名称，然后通过接口调具体规格
			if (empty($more_specs)) {
				$name = -1;
			} else {
				$name = $more_specs['name'];
			}
		}
		return[
			'ty' => $ty,
			'type' => $type,
			'data' => $row,
			'res' => $res,
			'catagory' => $catagory,
			'res5' => $res5,
			'price' => $price,
			'page' => $p,
			'child_list' => $catagory_chdlist,
			'catagory_pid' => $catagory_par,
			'name' => $name,
			'big_libao' => $spudatas['is_gift_set']
				//'back_url' => $_SERVER['HTTP_REFERER']
		];
	}

	//规格和规格名分开
	public function spec($specs) {
		if (empty($specs)) {
			return '';
		}
		$spec = array_filter(preg_split('/[;\r\n]+/s', $specs));
		//根据逗号细分
		for ($i = 0; $i < count($spec); $i ++) {
			$qian = explode(':', $spec[$i]);
			$name[] = $qian[0];
			$guige[] = explode(',', $qian[1]);
		}
		$ok_specs['name'] = $name;
		$ok_specs['guige'] = $guige;
		return $ok_specs;
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
			throw new \Ziima\MVC\Redirector("/index.php?call=sku.material&sku_id={$sku_id}&t={$t}");
		} elseif ($t == 2 || $t == 3) {
			$sku_id = $this->MySQL->scalar("SELECT sku_id FROM `yuemi_sale`.`sku_material` WHERE id = {$id}");
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku_material` SET status = 1 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector("/index.php?call=sku.material&sku_id={$sku_id}&t={$t}");
		} else {
			throw new \Ziima\MVC\Redirector("/index.php?call=sku.material&sku_id={$sku_id}&t={$t}");
		}
	}

	public function hit_picture(int $id = 0, int $t = 0) {
		if ($t == 0 || $t == 1) {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`spu_material` SET status = 2 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector("/index.php?call=sku.material&t={$t}");
		} elseif ($t == 2 || $t == 3) {
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku_material` SET status = 2 WHERE `id` = {$id}");
			throw new \Ziima\MVC\Redirector("/index.php?call=sku.material&t={$t}");
		} else {
			throw new \Ziima\MVC\Redirector("/index.php?call=sku.material&t={$t}");
		}
	}

	public function edit_sku() {
		
		$id = intval($_POST['id']);
		$intro = $this->MySQL->encode($_POST['intro']);
		$name = $this->MySQL->encode($_POST['name']);
		$barcode = $this->MySQL->encode($_POST['barcode']);
		$serial = $this->MySQL->encode($_POST['serial']);
		$weight = floatval($_POST['weight']);
		$unit = $this->MySQL->encode($_POST['unit']);
		$depot = intval($_POST['quantity']);
		$rebate_vip = floatval($_POST['rebate_vip']);
		$specs = $_POST['sku_specs'];
		if ($rebate_vip < 0) {
			$rebate_vip = 0;
		}

		if (isset($_POST['att_newbie'])) {
			$att_newbie = 1;
		} else {
			$att_newbie = 0;
		}
		if (isset($_POST['att_shipping'])) {
			$att_shipping = 0;
		} else {
			$att_shipping = 1;
		}
		if (isset($_POST['att_refund'])) {
			$att_refund = 1;
		} else {
			$att_refund = 0;
		}
		if (isset($_POST['limit_style'])) {
			$limit_style = 1;
			$limit_size = $_POST['limit_size'];
		} else {
			$limit_style = 0;
			$limit_size = 0;
		}
		$spu_id = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = {$id}");
		if(isset($_POST['big_libao']))
		{
			//修改spu为大礼包
			$this->MySQL->execute("UPDATE `yuemi_sale`.`spu` SET `is_gift_set` = 1 WHERE id = {$spu_id}");
		}else{
			
			//修改spu为大礼包
			$this->MySQL->execute("UPDATE `yuemi_sale`.`spu` SET `is_gift_set` = 0 WHERE id = {$spu_id}");
		}
		
		

		$price_market = floatval($_POST['price_market']);
		$price_ref = floatval($_POST['price_ref']);
		$price_sale = floatval($_POST['price_sale']); //阅米价
		//判断是否负毛利
		$price_base = $this->MySQL->scalar("SELECT price_base FROM `yuemi_sale`.`sku` WHERE id = {$id}");  //成本价
		if ($price_base > $price_sale) {
			$rebate_vip = 0;
		}

		$catagory_id = intval($_POST['catagory_id']);
		$price_inv = floatval($_POST['price_inv']);
		$subtitle = $this->MySQL->encode($_POST['subtitle']);

		$sql2 = "UPDATE `yuemi_sale`.`sku` " .
				"SET `intro` = '{$intro}' ," .
				"`title` = '{$name}',`barcode` = '{$barcode}'," .
				"`serial` = '{$serial}',`weight` = {$weight}," .
				"`unit` = '{$unit}',`depot` " .
				"= {$depot},`specs` = '{$specs}',`att_newbie` = {$att_newbie},`att_shipping` = {$att_shipping},`att_refund` = {$att_refund},`limit_style` = {$limit_style},`limit_size` = {$limit_size}," .
				"`price_sale` = {$price_sale},`rebate_vip` = {$rebate_vip}, `price_market` " .
				"= {$price_market},`price_ref` = {$price_ref},`status` = 2,`catagory_id`" .
				" = {$catagory_id},`subtitle` = '{$subtitle}',`price_inv` = {$price_inv} " .
				" WHERE `id` = {$id}";
		$this->MySQL->execute($sql2);

		if ($_POST['ty'] == 'a') {
			header("location:\index.php?call=sku.sku_a&p={$_POST['page']}");
		} elseif ($_POST['ty'] == 'b') {
			header("location:\index.php?call=sku.sku_b&p={$_POST['page']}");
		} elseif ($_POST['ty'] == 'c') {
			header("location:\index.php?call=sku.sku_c&p={$_POST['page']}");
		} elseif ($_POST['ty'] == 'd') {
			header("location:\index.php?call=sku.sku_d&p={$_POST['page']}");
		} else {
			header("location:\index.php?call=sku.sku_a&p={$_POST['page']}");
		}
	}

}
