<?php

include "lib/ApiHandler.php";

/**
 * 售卖管理接口
 */
class mall_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 获取商品素材
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     int     SPUid
	 * @request    type   int     素材类型
	 */
	public function sku_get_material_img(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT `is_default` AS IsDefault,`id` AS Id,`file_url` AS Picture , `thumb_url` AS Thumb  FROM `yuemi_sale`.`sku_material` "
				. "  WHERE `sku_id` = {$request->body->id} AND `type` = {$request->body->type} AND `status` != 2";
		$Re = $this->MySQL->grid($sql);
		$sql1 = "SELECT `title` AS Title FROM `yuemi_sale`.`sku` WHERE `id` = {$request->body->id}";
		$title = ($this->MySQL->row($sql1))['Title'];
		return [
			'Title' => $title,
			'data' => $Re
		];
	}

	/**
	 * 设为默认
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     int     SPUid
	 * @request    type   int     素材类型
	 * @request    mid    int     素材ID
	 */
	public function sku_set_default(\Ziima\MVC\REST\Request $request) {
		$sql2 = "SELECT COUNT(*) AS N FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$request->body->id} AND `type` = {$request->body->type}";
		$num = ($this->MySQL->row($sql2))['N'];
		if ($num == 0) {
			return[
				'__code' => 'Err',
				'__message' => '错误'
			];
		}
		$sql = "UPDATE `yuemi_sale`.`sku_material` SET `is_default` = 0 WHERE `sku_id` = {$request->body->id} AND `type` = {$request->body->type}";
		$Re = $this->MySQL->execute($sql);
		if (!$Re && ($num != 1)) {
			return[
				'__code' => 'Err',
				'__message' => '错误'
			];
		}
		$sql1 = "UPDATE `yuemi_sale`.`sku_material` SET `is_default` = 1 WHERE `id` = {$request->body->mid}";
		$Re1 = $this->MySQL->execute($sql1);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 删除素材
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     int     SPUid
	 */
	public function sku_remove_material(\Ziima\MVC\REST\Request $request) {
		$this->MySQL->execute("UPDATE `yuemi_sale`.`sku_material` SET `status` = 2 WHERE id = {$request->body->id}");
		return[
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 获取商品素材
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     int     SPUid
	 * @request    type   int     素材类型
	 */
	public function get_material_img(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT `is_default` AS IsDefault,`id` AS Id,`file_url` AS Picture , `thumb_url` AS Thumb  FROM `yuemi_sale`.`spu_material` "
				. "  WHERE `spu_id` = {$request->body->id} AND `type` = {$request->body->type}";
		$Re = $this->MySQL->grid($sql);
		$sql1 = "SELECT `title` AS Title FROM `yuemi_sale`.`spu` WHERE `id` = {$request->body->id}";
		$title = ($this->MySQL->row($sql1))['Title'];
		return [
			'Title' => $title,
			'data' => $Re
		];
	}

	/**
	 * 设为默认
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     int     SPUid
	 * @request    type   int     素材类型
	 * @request    mid    int     素材ID
	 */
	public function set_default(\Ziima\MVC\REST\Request $request) {
		$sql2 = "SELECT COUNT(*) AS N FROM `yuemi_sale`.`spu_material` WHERE `spu_id` = {$request->body->id} AND `type` = {$request->body->type}";
		$num = ($this->MySQL->row($sql2))['N'];
		if ($num == 0) {
			return[
				'__code' => 'Err',
				'__message' => '错误'
			];
		}
		$sql = "UPDATE `yuemi_sale`.`spu_material` SET `is_default` = 0 WHERE `spu_id` = {$request->body->id} AND `type` = {$request->body->type}";
		$Re = $this->MySQL->execute($sql);
		if (!$Re && ($num != 1)) {
			return[
				'__code' => 'Err',
				'__message' => '错误'
			];
		}
		$sql1 = "UPDATE `yuemi_sale`.`spu_material` SET `is_default` = 1 WHERE `id` = {$request->body->mid}";
		$Re1 = $this->MySQL->execute($sql1);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 删除素材
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     int     SPUid
	 */
	public function remove_material(\Ziima\MVC\REST\Request $request) {
		\yuemi_sale\SpuMaterialFactory::Instance()->delete($request->body->id);
		return[
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 购物车删除
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     char     记录id
	 * @slient
	 */
	public function mall_del(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$sql = "DELETE FROM `yuemi_sale`. `cart` WHERE id = '" . $id . "'";
		$this->MySQL->execute($sql);
		return [
			'__code' => 'OK',
			'__message' => '删除成功'
		];
	}

	/**
	 * 优惠卷删除
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     char     优惠卷id
	 * @slient
	 */
	public function ticket_del(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$sql = "DELETE FROM `yuemi_main`. `ticket` WHERE id = '" . $id . "'";
		$this->MySQL->execute($sql);
		return [
			'__code' => 'OK',
			'__message' => '删除成功'
		];
	}

	/*
	 * 计算价格
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    sale     float     平台价
	 * @request	   sku_id	int		  skuid
	 * @request		base	float	  成本价
	 */

	public function suan(\Ziima\MVC\REST\Request $request) {
		$rv = ($request->body->sale - $request->body->base) * 0.56;
		return[
			'__code' => 'OK',
			'__message' => '',
			'rv' => $rv
		];
	}

	/**
	 * 获取分类ID
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function get_catagory(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$sql = "SELECT * FROM `yuemi_sale`.`" . ($this->Supplier->pi_catagory !== '' ? $this->Supplier->pi_catagory : 'catagory') . "` WHERE `parent_id` = {$id}";
		$re = $this->MySQL->grid($sql);
		if (empty($re)) {
			return [
				'Re' => '',
				'__code' => 'OK',
				'__message' => ''
			];
		}
		return [
			'Re' => $re,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * sku下架
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id		  int		 skuID
	 */
	public function downsku(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$res = $this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 3 WHERE `id` = {$id}");
		if ($res) {
			return [
				'__code' => 'OK',
				'__message' => '修改成功'
			];
		} else {
			return [
				'__code' => 'OK',
				'__message' => '修改失败'
			];
		}
	}

	/**
	 * sku上架（通过）
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id		  int		 skuID
	 */
	public function upsku(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$res = $this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 2 WHERE `id` = {$id}");
		if ($res) {
			return [
				'__code' => 'OK',
				'__message' => '修改成功'
			];
		} else {
			return [
				'__code' => 'OK',
				'__message' => '修改失败'
			];
		}
	}

	/**
	 * sku驳回
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id		  int		 skuID
	 */
	public function getoutsku(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$res = $this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `status` = 1 WHERE `id` = {$id}");
		if ($res) {
			return [
				'__code' => 'OK',
				'__message' => '修改成功'
			];
		} else {
			return [
				'__code' => 'OK',
				'__message' => '修改失败'
			];
		}
	}

	/**
	 * SKU修改审核通过
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		id
	 */
	public function adopt(\Ziima\MVC\REST\Request $request) {
		//现在修改阅米价，佣金计算按照修改的阅米价计算，如果修改有邀请码，需要总后台修改一次，佣金才会变
		$id = $request->body->id;
		$res = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`sku_changes` WHERE id = {$id}");
		//修改sku商品名称
		if ($res['chg_title'] == 1) {
			//获得修改的title
			$title = $res['new_title'];
			//进行修改操作
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `title` = '{$title}',status = 2 WHERE id = {$res['sku_id']}");
		}

		//修改阅米价
		if ($res['chg_price_sale'] == 1) {
			//获得修改的阅米价
			$sale = $res['new_price_sale'];
			//获得之前的成本价
			$base = $this->MySQL->scalar("SELECT price_base FROM `yuemi_sale`.`sku` WHERE id = {$res['sku_id']}");
			if ($sale == 0) {
				return[
					'__code' => 'ERROR',
					'__message' => '新修改的阅米价不能为0'
				];
			} else {
				$mll = ($sale - $base) / $sale;
			}

			$yj = ($sale - $base) * 0.56;
			if ($yj < 0) {
				$yj = 0;
			}
			//进行修改操作
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `price_sale` = '{$sale}',status = 2,`price_inv` = {$sale},`price_ratio` = {$mll},`rebate_vip` = {$yj} WHERE id = {$res['sku_id']}");
		}
		//修改库存
		if ($res['chg_depot'] == 1) {
			//获得修改的库存
			$depot = $res['new_depot'];
			//进行修改操作
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `depot` = '{$depot}',status = 2 WHERE id = {$res['sku_id']}");
		}

		//修改成本价
		if ($res['chg_price_base'] == 1) {
			//获得修改的成本价
			$base = $res['new_price_base'];
			//获得之前的有邀请码价格
			$price_inv = $this->MySQL->scalar("SELECT price_inv FROM `yuemi_sale`.`sku` WHERE id = {$res['sku_id']}");
			$yj = ($price_inv - $base) * 0.56;
			if ($price_inv == 0) {
				return[
					'__code' => 'ERROR',
					'__message' => '有邀请码价格不能为0'
				];
			} else {
				$mll = ($price_inv - $base) / $price_inv;
			}
			if ($yj < 0) {
				$yj = 0;
			}
			//进行修改操作
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `price_base` = '{$base}',status = 2,`price_ratio` = {$mll},`rebate_vip` = {$yj}  WHERE id = {$res['sku_id']}");
		}

		//修改分类
		if ($res['chg_catagory'] == 1) {
			//获得修改的分类
			$catagory = $res['new_catagory'];
			//进行修改操作
			$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET `catagory_id` = '{$catagory}',status = 2 WHERE id = {$res['sku_id']}");
		}



		//修改sku_change 状态和数据
		$time = time();
		$Re = $this->MySQL->execute(
				"UPDATE `yuemi_sale`.`sku_changes` SET status = 1, audit_time = {$time}, audit_user = {$this->User->id}, audit_from = {$this->Context->Runtime->ticket->ip} " .
				"WHERE id = {$id}"
		);

//		$SkuChangesEntity = new yuemi_sale\SkuChangesEntity();
//		$SkuChangesEntity->status = 1;
//		$SkuChangesEntity->audit_time = time();
//		$SkuChangesEntity->audit_user = $this->User->id;
//		$SkuChangesEntity->audit_from = $this->Context->Runtime->ticket->ip;
//		$Re = \yuemi_sale\SkuChangesFactory::Instance()->update($SkuChangesEntity);
//		if ($Re){
//			return [
//				'__code' => 'ERR_OR',
//				'__message' => '修改错误'
//			];
//		}
		return[
			'__code' => 'OK',
			'__message' => '审核通过'
		];
	}

	/**
	 * SKU审核拒绝
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		id 
	 */
	public function refase(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		//skucahnge 状态改为拒绝 不对SKU进行操作
		$res = $this->MySQL->execute(
				"UPDATE `yuemi_sale`.`sku_changes` SET status = 2 WHERE id = {$id}"
		);
		return [
			'__code' => 'OK',
			'__message' => '已拒绝供应商修改'
		];
	}

	/**
	 * 拷贝spu素材
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		spu_id		int		id 
	 */
	public function copy_to_spu(\Ziima\MVC\REST\Request $request) {
		$ext_spu_id = $this->MySQL->scalar("SELECT `id` FROM `yuemi_sale`.`ext_spu` WHERE `spu_id` = {$request->body->spu_id} LIMIT 1");
		if (!$ext_spu_id) {
			return [
				'__code' => 'OK',
				'__message' => '无外部素材'
			];
		}
///		$Re = \yuemi_sale\ProcedureInvoker::Instance()->copy_to_spu($request->body->spu_id, $this->User->id, $this->Context->Runtime->ticket->ip);
		$Re = \yuemi_sale\ProcedureInvoker::Instance()->copy_espu_to_spu($request->body->spu_id, $ext_spu_id, $this->User->id, 3, $this->Context->Runtime->ticket->ip);
		var_dump($Re);
		exit;
		return [
			'__code' => 'OK',
			'__message' => '已复制'
		];
	}

	/**
	 * 拷贝sku素材
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		sku_id		int		id 
	 */
	public function copy_to_sku(\Ziima\MVC\REST\Request $request) {
		$ext_sku_id = $this->MySQL->scalar("SELECT `id` FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = {$request->body->sku_id} LIMIT 1");
		if (!$ext_sku_id) {
			return [
				'__code' => 'OK',
				'__message' => '无外部素材'
			];
		}
//		$Re = \yuemi_sale\ProcedureInvoker::Instance()->copy_to_sku($request->body->sku_id,$ext_sku_id ,$this->User->id,3 ,$this->Context->Runtime->ticket->ip);
		$Re = \yuemi_sale\ProcedureInvoker::Instance()->copy_esku_to_sku($request->body->sku_id, $ext_sku_id, $this->User->id, 3, $this->Context->Runtime->ticket->ip);
		return [
			'__code' => 'OK',
			'__message' => '已复制'
		];
	}

	/**
	 * 排期审核通过
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		id 
	 */
	public function ok(\Ziima\MVC\REST\Request $request) {
		$SkuTaskEntity = \yuemi_sale\SkuTaskFactory::Instance()->load($request->body->id);
		$SkuTaskEntity->status = 3;
		$SkuTaskEntity->audit_user = $this->User->id;
		$SkuTaskEntity->audit_time = time();
		$SkuTaskEntity->audit_from = $this->Context->Runtime->ticket->ip;
		if (!\yuemi_sale\SkuTaskFactory::Instance()->update($SkuTaskEntity)) {
			return [
				'__code' => 'Err',
				'__message' => '操作失败，请联系管理员'
			];
		}
		return [
			'__code' => 'OK',
			'__message' => '审核成功'
		];
	}

	/**
	 * 排期审核驳回
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		id 
	 */
	public function no(\Ziima\MVC\REST\Request $request) {
		$SkuTaskEntity = \yuemi_sale\SkuTaskFactory::Instance()->load($request->body->id);
		$SkuTaskEntity->status = 1;
		$SkuTaskEntity->audit_user = $this->User->id;
		$SkuTaskEntity->audit_time = time();
		$SkuTaskEntity->audit_from = $this->Context->Runtime->ticket->ip;
		if (!\yuemi_sale\SkuTaskFactory::Instance()->update($SkuTaskEntity)) {
			return [
				'__code' => 'Err',
				'__message' => '操作失败，请联系管理员'
			];
		}
		return [
			'__code' => 'OK',
			'__message' => '审核成功'
		];
	}

	/**
	 * 排期审核删除
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		id 
	 */
	public function del(\Ziima\MVC\REST\Request $request) {
		$SkuTaskEntity = \yuemi_sale\SkuTaskFactory::Instance()->load($request->body->id);
		$SkuTaskEntity->status = 2;
		$SkuTaskEntity->audit_user = $this->User->id;
		$SkuTaskEntity->audit_time = time();
		$SkuTaskEntity->audit_from = $this->Context->Runtime->ticket->ip;
		if (!\yuemi_sale\SkuTaskFactory::Instance()->update($SkuTaskEntity)) {
			return [
				'__code' => 'Err',
				'__message' => '操作失败，请联系管理员'
			];
		}
		return [
			'__code' => 'OK',
			'__message' => '审核成功'
		];
	}

	/**
	 * 查看排期SKU
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		page		int			页码
	 * @request		supplier	int			供应商ID
	 * @request		search		string		关键字
	 */
	public function select_sku(\Ziima\MVC\REST\Request $request) {
		$sid = $request->body->supplier;
		$sql = "SELECT id,title FROM `yuemi_sale`.`sku`";
		$whr = [];
		$whr[] = " supplier_id = {$sid} ";
		if (strlen($request->body->search) > 0) {
			$whr[] = " `title` LIKE '%" . $this->MySQL->encode($request->body->search) . "%'";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}

//		echo $sql;die;
		$sku = $this->MySQL->paging($sql, 20, $request->body->page);
		$res = array();
		foreach ($sku->Data AS $key => $val) {
			$list['title'] = $val['title'];
			$list['id'] = $val['id'];
			$res[] = $list;
		}
		return [
			'List' => $res
		];
	}

	/**
	 * 查询供应商
	 *  @param \Ziima\MVC\REST\Request $request
	 * @request		name		string			供应商名称
	 */
	public function search_supplier(\Ziima\MVC\REST\Request $request) {
		$name = $this->MySQL->encode($request->body->name);
		$list = $this->MySQL->grid("SELECT id,name FROM `yuemi_main`.`supplier` WHERE name like '%{$name}%'");
		return [
			'List' => $list
		];
	}

	/**
	 * 新增优惠券
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function insert_discount(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$type = $request->body->type;
		$val = $request->body->val;
		$small = $request->body->small;
		$can_use = strtotime($request->body->can_use);
		$spu_id = $request->body->spuid;
		$creator_id = $this->User->id;
		$create_time = time();
		$status = 0;
		$DiscountCouponEntity = new \yuemi_sale\DiscountCouponEntity();
		$DiscountCouponEntity->id = $id;
		$DiscountCouponEntity->type = $type;
		$DiscountCouponEntity->spu_id = $spu_id;
		$DiscountCouponEntity->value = $val;
		$DiscountCouponEntity->price_small = $small;
		$DiscountCouponEntity->expiry_date = $can_use;
		$DiscountCouponEntity->creator_id = $creator_id;
		$DiscountCouponEntity->create_time = $create_time;
		$DiscountCouponEntity->status = 0;
		$DiscountCouponFactory = new \yuemi_sale\DiscountCouponFactory(MYSQL_WRITER, MYSQL_READER);
		if (!$DiscountCouponFactory->insert($DiscountCouponEntity)) {
			throw new \Exception('插入表DiscountCoupon失败！');
		} else {
			return;
		}
	}

	/**
	 * 获取优惠券ID
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function get_discount_id(\Ziima\MVC\REST\Request $request) {
		$XX = date("YmdHis");
		$XX .= "-" . mt_rand(100000, 999999);
		$XX .= "-" . mt_rand(100000, 999999);
		$XX .= "-" . mt_rand(100000, 999999);
		$XX = strtoupper(md5($XX));
		$id = $this->get_only_id($XX);
		return [
			'id' => $id
		];
	}

	/**
	 * 获取唯一id
	 * @param type $id
	 */
	private function get_only_id($id) {
		$row = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`discount_coupon` WHERE `id` = '{$id}'");
		if (!empty($row)) {
			$XX = date("YmdHis");
			$XX .= "-" . mt_rand(100000, 999999);
			$XX .= "-" . mt_rand(100000, 999999);
			$XX .= "-" . mt_rand(100000, 999999);
			$XX = strtoupper(md5($XX));
			return $this->get_only_id($XX);
			
		} else {
			return $id;
		}
	}
	
	
	/**
	 * 关闭优惠券
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function close_card(\Ziima\MVC\REST\Request $request){
		$id = $request->body->id;
		$this->MySQL->execute("UPDATE `yuemi_sale`.`discount_coupon` SET `status` = 2 WHERE `id` = '{$id}'");
		return 'OK';
	}

}
