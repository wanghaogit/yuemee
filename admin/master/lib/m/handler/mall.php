<?php

include "lib/AdminHandler.php";

/**
 * 售卖管理
 * @auth
 */
class mall_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0) {
		
	}

	public function catagory_create(int $clid = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$parent_id = $_POST['parent_id'];
			$name = $this->MySQL->encode($_POST['name']);
			$manager_id = (int) $_POST['manager_id'];
			$supplier_id = (int) $_POST['supplier_id'];
			$is_hidden = (int) $_POST['is_hidden'];
			$is_internal = (int) $_POST['is_internal'];
			$is_private = (int) $_POST['is_private'];
			$p_order = intval($_POST['p_order']);
			$gratio_dead = floatval($_POST['gratio_dead']) / 100;
			$gratio_warn = floatval($_POST['gratio_warn']) / 100;
			$rratio_system = floatval($_POST['rratio_system']) / 100;

			$CatagoryEntity = new \yuemi_sale\CatagoryEntity();
			$CatagoryEntity->parent_id = $parent_id;
			$CatagoryEntity->name = $name;
			$CatagoryEntity->manager_id = $manager_id;
			$CatagoryEntity->supplier_id = $supplier_id;
			$CatagoryEntity->is_hidden = $is_hidden;
			$CatagoryEntity->is_internal = $is_internal;
			$CatagoryEntity->is_private = $is_private;
			$CatagoryEntity->p_order = $p_order;
			$CatagoryEntity->gratio_dead = $gratio_dead;
			$CatagoryEntity->gratio_warn = $gratio_warn;
			$CatagoryEntity->rratio_system = $rratio_system;
			$CatagoryFactory = new \yuemi_sale\CatagoryFactory(MYSQL_WRITER, MYSQL_READER);
			if (!$CatagoryFactory->insert($CatagoryEntity)) {
				throw new \Exception('插入表Catagory失败！');
			}

			throw new \Ziima\MVC\Redirector('/index.php?call=mall.catagory&clid=' . $parent_id);
		}

		$sql = 'SELECT a.`user_id` as `id`,u.`name` FROM `yuemi_main`.`rbac_admin` a LEFT JOIN `yuemi_main`.`user` u ON a.`user_id` = u.`id` ORDER BY a.`id`';
		$UserList = $this->MySQL->grid($sql);
//		die (print_r($UserList));

		$sql = 'SELECT * FROM `yuemi_main`.`supplier` ORDER BY `id`';
		$supplier_id = $this->MySQL->grid($sql);
		$sql = 'SELECT MAX(`p_order`) FROM `yuemi_sale`.`catagory`';
		$p_order = intval($this->MySQL->scalar($sql)) + 1;

		if ($clid) {
			$CatagoryFactory = new \yuemi_sale\CatagoryFactory(MYSQL_WRITER, MYSQL_READER);
			$obj = $CatagoryFactory->load($clid);
			$pname = $obj->name;
		} else {
			$pname = '';
		}

		return [
			'pid' => $clid,
			'userlist' => $UserList,
			'supplier_id' => $supplier_id,
			'p_order' => $p_order,
			'pname' => $pname
		];
	}

	public function cart(int $p = 0) {
		$sql = 'SELECT c.*,U1.name AS name_1,SK.title AS name_2,SK.title AS name_3,SP.title AS name_4,(c.qty * c.sku_price) AS price, SK.price_sale AS money, from_unixtime(1524676716) AS time '
				. 'FROM `yuemi_sale`.`cart` AS `c` '
				. "LEFT JOIN `yuemi_main`.`user` AS `U1` ON c.user_id = U1.id "
				. "LEFT JOIN `yuemi_sale`.`sku` AS `SK` ON c.sku_id = SK.id "
				. "LEFT JOIN `yuemi_sale`.`spu` AS `SP` ON c.spu_id = SP.id ";
		$sql .= " ORDER BY `c`.`create_time` DESC ";
		$res = $this->MySQL->paging($sql, 30, $p);
		return [
			'data' => $res,
		];
	}

	public function rebate(int $p = 0) {
		$sql = "SELECT r.*,S.title AS name_1,P.title AS name_2 "
				. "FROM `yuemi_sale`.`rebate` AS  `r` "
				. "LEFT JOIN `yuemi_sale`.`spu` AS `S` ON r.spu_id = S.id "
				. "LEFT JOIN `yuemi_sale`.`sku` AS `P` ON r.sku_id = P.id ";

		$res = $this->MySQL->paging($sql, 30, $p);
		return [
			'data' => $res,
		];
	}

	//可怕的分割线
	public function catagory(int $p = 0, int $clid = 0) {
		$this->FactoryCatagory = new \yuemi_sale\CatagoryFactory(MYSQL_WRITER, MYSQL_READER);
		$clobj = null;
		if ($clid > 0) {
			$clobj = $this->FactoryCatagory->load($clid);
			if ($clobj === null) {
				throw new \Ziima\MVC\Redirector('/index.php?call=mall.catagory');
			}
		}
		$sql = "SELECT `c`.*,`u`.`name` AS `manager_name` " .
				"FROM `yuemi_sale`.`catagory` AS `c` " .
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

	public function catagory_updata(int $cid = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$CatagoryFactory = new \yuemi_sale\CatagoryFactory(MYSQL_WRITER, MYSQL_READER);
			$CatagoryEntity = $CatagoryFactory->load($cid);
			$CatagoryEntity->name = $this->MySQL->encode($_POST['name']);
			$CatagoryEntity->p_order = (int) $_POST['p_order'];
			$CatagoryFactory->update($CatagoryEntity);
			if (!$CatagoryFactory->update($CatagoryEntity)) {
				throw new \Exception('更新表catagory失败！');
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.catagory');
		}
		$sql = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `id` = {$cid}";
		$result = $this->MySQL->row($sql);
		return [
			'result' => $result
		];
	}

	public function upshelfsku(int $id = 0, int $p = 0) {
		$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 2 WHERE `id` = {$id} ");
		throw new \Ziima\MVC\Redirector("/index.php?call=mall.sku&p={$p}");
	}

	public function throw(int $id = 0) {
		$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET status = 2");
		throw new \Ziima\MVC\Redirector("/index.php?call=mall.sku");
	}

	public function offsku() {
		$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET status = 1");
		throw new \Ziima\MVC\Redirector("/index.php?call=mall.sku");
	}

	public function statuschange(int $id = 0, int $t = 0, int $p = 0) {
		$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET status = {$t} WHERE `id` = {$id}");
		throw new \Ziima\MVC\Redirector("/index.php?call=mall.sku&p={$p}");
	}

	public function downsku(int $id = 0, int $p = 0) {
		$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 3 WHERE `id` = {$id} ");
		throw new \Ziima\MVC\Redirector("/index.php?call=mall.sku&p={$p}");
	}

	/**
	 * 审核SKU
	 */
	public function verify(int $p = 0, int $sid = 0, string $q = '') {
		$this->Cacher->loadSupplier();
//		$this->Cacher->loadBrand();
//		$this->Cacher->loadCatagory();

		$sql = "SELECT * FROM `yuemi_sale`.`sku_changes`";
		//WHERE
		$whr = [];
		if ($sid > 0) {
			$whr[] = "`supplier_id` = '" . $sid . "'";
		}

		if (!empty($q)) {
			$whr[] = "`old_title` like '%" . $this->MySQL->encode($q) . "%'";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		//最后ORDER BY
		$sql .= ' ORDER BY `id` DESC ';

		$res = $this->MySQL->paging($sql, 20, $p);


		$supplier = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_main`.`supplier`");
//		$brand = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_sale`.`brand`");
		$catagory = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`catagory`");
		$sku = $this->MySQL->grid("SELECT `id`,`title` FROM `yuemi_sale`.`sku`");
//		var_dump($catagory);die;
		return [
			'data' => $res,
			'Supplier' => $supplier,
			'Catagory' => $catagory,
			'Sku' => $sku
//			'Brand' => $this->Cacher->brand
		];
	}

	/**
	 * 内购
	 */
	public function ext_sku_verify(int $p = 0, int $sid = 0, string $q = '') {
		$this->Cacher->loadSupplier();
//		$this->Cacher->loadBrand();
//		$this->Cacher->loadCatagory();

		$sql = "SELECT * FROM `yuemi_sale`.`ext_sku_changes`";
		//WHERE
		//最后ORDER BY
		$sql .= ' ORDER BY `id` DESC ';

		$res = $this->MySQL->paging($sql, 20, $p);


		$supplier = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_main`.`supplier`");
//		$brand = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_sale`.`brand`");
		$catagory = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`catagory`");
		$sku = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_sale`.`ext_sku`");
//		var_dump($catagory);die;
		return [
			'data' => $res,
			'Supplier' => $supplier,
			'Catagory' => $catagory,
			'Sku' => $sku
//			'Brand' => $this->Cacher->brand
		];
	}

	/**
	 * 排期
	 * @param int $p
	 * @param string $key_serch
	 * @param string $catagory
	 * @param string $status_serch
	 * @return type
	 */
	public function sku_task(int $p = 0, string $key_serch = '', string $catagory = '', string $status_serch = '') {
		//echo $status_serch;DIE;
		$sql = "SELECT st.*,sku.title AS Title, sku.catagory_id AS Cata FROM `yuemi_sale`.`sku_task` AS st " .
				"LEFT JOIN `yuemi_sale`.`sku` AS sku ON st.sku_id = sku.id "
		;
		// 查询条件，搜索相关参数
		$online_time = strtotime($_GET['online_time'] ?? ""); // 开始时间
		$offline_time = strtotime($_GET['offline_time'] ?? ""); // 结束时间
		//拼装where条件
		$whr = [];
		//时间
		if ($online_time > 0)
			$whr[] = " st.s1_time >= {$online_time} ";
		if ($offline_time > 0)
			$whr[] = " st.s2_time <= {$offline_time} ";
		//关键字
		$key_serch = trim($key_serch); // 订单号
		if (!empty($key_serch))
			$whr[] = " `sku`.`title` LIKE '%{$key_serch}%' ";
		//分类
		if (!empty($catagory))
			$whr[] = " `sku`.`catagory_id` = {$catagory} ";

		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$sql .= "ORDER BY st.`id` DESC";
		$catagory = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`catagory`");
		//echo $sql;die;
		$Re = $this->MySQL->paging($sql,10, $p);

		return[
			'res' => $Re,
			'online_time' => $online_time,
			'catagory' => $catagory,
			'offline_time' => $offline_time
		];
	}

	/**
	 * 查看信息
	 * @param int $id
	 * @return type
	 * @throws \Exception
	 * @throws \Ziima\MVC\Redirector
	 */
	public function info(int $id) {
		if ($this->Context->Runtime->ticket->postback) {

			$SkuTaskEntity = \yuemi_sale\SkuTaskFactory::Instance()->load($id);
//			if ($SkuTaskEntity->status == 0) {
//				throw new \Exception('正在审核中不允许修改');
//			}
			$SkuTaskEntity->uf_title = empty($_POST['title']) ? 0 : 1;
			$SkuTaskEntity->uf_subtitle = empty($_POST['subtitle']) ? 0 : 1;
			$SkuTaskEntity->uf_price = empty($_POST['price']) ? 0 : 1;
			$SkuTaskEntity->uf_qty = empty($_POST['qty']) ? 0 : 1;
			$SkuTaskEntity->uf_limit = empty($_POST['limit']) ? 0 : 1;
			$SkuTaskEntity->uf_rebate = empty($_POST['rebate']) ? 0 : 1;
			$SkuTaskEntity->s1_time = strtotime($_POST['online_time']);
			$SkuTaskEntity->s1_title = $this->MySQL->encode($_POST['start_name']);
			$SkuTaskEntity->s1_subtitle = $this->MySQL->encode($_POST['start_subtitle']);
			$SkuTaskEntity->s1_price = floatval($_POST['start_price']);
			$SkuTaskEntity->s1_qty = intval($_POST['start_qty']);
			$SkuTaskEntity->s1_limit = intval($_POST['start_limit']);
			$SkuTaskEntity->s1_rebate = floatval($_POST['start_rebate']);
			$SkuTaskEntity->s2_time = strtotime($_POST['offline_time']);
			$SkuTaskEntity->s2_method = (int) $_POST['s2_method'];
			$SkuTaskEntity->s2_title = $this->MySQL->encode($_POST['end_name']);
			$SkuTaskEntity->s2_subtitle = $this->MySQL->encode($_POST['end_subtitle']);
			$SkuTaskEntity->s2_price = floatval($_POST['end_price']);
			$SkuTaskEntity->s2_qty = intval($_POST['end_qty']);
			$SkuTaskEntity->s2_limit = intval($_POST['end_limit']);
			$SkuTaskEntity->s2_rebate = floatval($_POST['end_rebate']);
			$SkuTaskEntity->status = 0;
			$SkuTaskEntity->audit_user = $this->User->id;
			$SkuTaskEntity->audit_time = time();
			$SkuTaskEntity->audit_from = $this->Context->Runtime->ticket->ip;
			if (!\yuemi_sale\SkuTaskFactory::Instance()->update($SkuTaskEntity)) {
				return [
					'__code' => 'Err',
					'__message' => '修改失败'
				];
			}

			throw new \Ziima\MVC\Redirector('/index.php?call=mall.sku_task');
		}
		$sql = "SELECT st.*,sku.title FROM `yuemi_sale`.`sku_task` AS st " .
				"LEFT JOIN `yuemi_sale`.`sku` AS sku ON st.sku_id = sku.id " .
				"WHERE st.id = {$id}";
		$res = $this->MySQL->row($sql);
//		var_dump($res);die;
		return[
			'res' => $res
		];
	}

	/**
	 * 添加排期
	 * 
	 */
	public function skutask_create() {
		if ($this->Context->Runtime->ticket->postback) {
//			var_dump($_POST);die;
			$SkuTaskEntity = new \yuemi_sale\SkuTaskEntity();
			$SkuTaskEntity->sku_id = $_POST['sku_id'] ?? 0;
			$SkuTaskEntity->uf_title = $_POST['title'] ?? 0;
			$SkuTaskEntity->uf_subtitle = $_POST['subtitle'] ?? 0;
			$SkuTaskEntity->uf_price = $_POST['price'] ?? 0;
			$SkuTaskEntity->uf_qty = $_POST['qty'] ?? 0;
			$SkuTaskEntity->uf_limit = $_POST['limit'] ?? 0;
			$SkuTaskEntity->uf_rebate = $_POST['rebate'] ?? 0;
			$SkuTaskEntity->s1_time = strtotime($_POST['online_time']);
			$SkuTaskEntity->s1_title = $this->MySQL->encode($_POST['start_name']);
			$SkuTaskEntity->s1_subtitle = $this->MySQL->encode($_POST['start_subtitle']);
			$SkuTaskEntity->s1_price = floatval($_POST['start_price']);
			$SkuTaskEntity->s1_qty = intval($_POST['start_qty']);
			$SkuTaskEntity->s1_limit = intval($_POST['start_limit']);
			$SkuTaskEntity->s1_rebate = floatval($_POST['start_rebate']);
			$SkuTaskEntity->s2_time = strtotime($_POST['offline_time']);
			$SkuTaskEntity->s2_method = (int) $_POST['s2_method'];
			$SkuTaskEntity->s2_title = $this->MySQL->encode($_POST['end_name']);
			$SkuTaskEntity->s2_subtitle = $this->MySQL->encode($_POST['end_subtitle']);
			$SkuTaskEntity->s2_price = floatval($_POST['end_price']);
			$SkuTaskEntity->s2_qty = intval($_POST['end_qty']);
			$SkuTaskEntity->s2_limit = intval($_POST['end_limit']);
			$SkuTaskEntity->s2_rebate = floatval($_POST['end_rebate']);
			$SkuTaskEntity->status = 0;
			$SkuTaskEntity->create_user = $this->User->id;
			$SkuTaskEntity->create_time = time();
			$SkuTaskEntity->create_from = $this->Context->Runtime->ticket->ip;
			$SkuTaskFactory = new \yuemi_sale\SkuTaskFactory(MYSQL_WRITER, MYSQL_READER);
			if (!$SkuTaskFactory->insert($SkuTaskEntity)) {
				throw new \Exception('插入表SkuTask失败！');
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.sku_task');
		}
		$sql = "SELECT id,user_id,name FROM `yuemi_main`.`supplier` WHERE status = 1";
		$list = $this->MySQL->grid($sql);

		return [
			'List' => $list
		];
	}

	/**
	 * 修改启动的排期
	 */
	public function update(int $id) {
		if ($this->Context->Runtime->ticket->postback) {
			$SkuTaskEntity = \yuemi_sale\SkuTaskFactory::Instance()->load($id);
			$sku = $SkuTaskEntity->sku_id;	//商品标题
			$title = empty($_POST['title']) ? 0 : 1;  //是否修改标题
			$subtitle = empty($_POST['subtitle']) ? 0 : 1;//是否修改子标题
			$end_time =  strtotime($_POST['offline_time']);//结束时间
			$status = (int) $_POST['s2_method'];	//结束状态
			if($title == 1)
			{
				//直接修改sku标题
				$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `title` = '{$this->MySQL->encode($_POST['start_name'])}' WHERE id = {$sku}");
				//修改排期表s2name
				$this->MySQL->execute("UPDATE `yuemi_sale`.`sku_task` SET `s2_title` = '{$this->MySQL->encode($_POST['end_name'])}' WHERE id = {$_POST['task_id']}");
			}
			if($subtitle == 1)
			{
				//直接修改sku子标题
				$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `subtitle` = '{$this->MySQL->encode($_POST['start_subtitle'])}' WHERE id = {$sku}");
				//修改排期表s2name
				$this->MySQL->execute("UPDATE `yuemi_sale`.`sku_task` SET `s2_subtitle` = '{$this->MySQL->encode($_POST['end_subtitle'])}' WHERE id = {$_POST['task_id']}");
			
			}
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku_task` SET `s2_time` = $end_time WHERE id = {$_POST['task_id']}");
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku_task` SET `s2_method` = $status WHERE id = {$_POST['task_id']}");
			throw new \Ziima\MVC\Redirector('/index.php?call=mall.sku_task');
		}
		$sql = "SELECT st.*,sku.title FROM `yuemi_sale`.`sku_task` AS st " .
				"LEFT JOIN `yuemi_sale`.`sku` AS sku ON st.sku_id = sku.id " .
				"WHERE st.id = {$id}";
		$res = $this->MySQL->row($sql);
//		var_dump($ress);die;
		return[
			'res' => $res
		];
	}
	
	public function discount(int $p = 0){
		$sql = " SELECT `dc`.*,`p`.`title`,`u`.`name` AS `UseName`,`uu`.`name` AS `CreName` ".
				"FROM `yuemi_sale`.`discount_coupon` AS `dc` ".
				"LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `dc`.`user_id` ".
				"LEFT JOIN `yuemi_sale`.`spu` AS `p` ON `p`.`id` = `dc`.`spu_id` ".
				"LEFT JOIN `yuemi_main`.`user` AS `uu` ON `uu`.`id` = `dc`.`creator_id` ";
		$res = $this->MySQL->paging($sql,25,$p);
		return [
			'res' => $res
		];
	}
}
