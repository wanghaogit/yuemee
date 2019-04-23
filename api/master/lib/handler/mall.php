<?php

include_once 'lib/ApiHandler.php';

/**
 * 商城接口
 */
class mall_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 商品按分类检索列表
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		catagory_id			int			分类ID
	 * @request		brand_id			int			品牌ID
	 * @request		supplier_id			int			供应商ID
	 * @request		keyword				string		查询关键字
	 * @request		sort				string		排序方式 x=销量 p=价格 z=综合(sort字段）
	 * @request		page				int			第几页(固定每页10个，从0页开始)
	 */
	public function list(\Ziima\MVC\REST\Request $request) {
		$cid = $request->body->catagory_id;
		$sql = "SELECT sku.* " .
				"FROM `yuemi_sale`.`sku` AS sku " .
				"LEFT JOIN `yuemi_sale`.`catagory` AS c ON sku.`catagory_id` = c.`id` " .
				"LEFT JOIN `yuemi_sale`.`spu` AS spu ON sku.`spu_id` = spu.id";
		$whr = [];
		// 分类
		if ($cid > 0) {
			$cids = null;
			$data = $this->MySQL->grid("SELECT * FROM yuemi_sale.`catagory` WHERE id = {$cid} OR parent_id = {$cid}");
			foreach ($data AS $val) {
				$cids .= "{$val['id']},";
			}
			$cids = trim($cids, ',');
			$whr[] = " sku.`catagory_id` IN ({$cids}) ";
			$cname = $this->MySQL->scalar("SELECT name FROM `yuemi_sale`.`catagory` WHERE id = " . $request->body->catagory_id);
		} else {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '分类ID为空');
		}
		// 品牌
		if ($request->body->brand_id > 0) {
			$whr[] = " spu.`brand_id` ='" . $request->body->brand_id . "'";
			$band_name = $this->MySQL->scalar("SELECT name FROM `yuemi_sale`.`brand` WHERE id = " . $request->body->brand_id);
		} else {
			$band_name = '';
		}
		// 供应商
		if ($request->body->supplier_id > 0) {
			$whr[] = " sku.`supplier_id` ='" . $request->body->supplier_id . "'";
			$supplier_name = $this->MySQL->scalar("SELECT name FROM `yuemi_main`.`supplier` WHERE id = " . $request->body->supplier_id);
		} else {
			$supplier_name = '';
		}
		// 关键词
		if (!empty($request->body->keyword)) {
			$whr[] = " sku.`title` LIKE '%" . $this->MySQL->encode($request->body->keyword) . "%'";
			$key = $this->MySQL->encode($request->body->keyword);
		} else {
			$key = '';
		}
		// 组合Where条件
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		// 最后 ORDER BY
		$sql .= ' AND sku.status = 2 ';
		if ($request->body->sort == 'p1') {
			$sql .= ' ORDER BY sku.price_sale ASC ';
		} elseif ($request->body->sort == 'p2') {
			$sql .= ' ORDER BY sku.price_sale DESC ';
		} else
			$sql .= ' ORDER BY `id` DESC ';

		// $request->body->sort
		$re = $this->MySQL->paging($sql, 10, $request->body->page);

		if (empty($re)) {
			$arr = '';
			return [
				'__code' => 'OK',
				'__message' => '',
				'List' => $arr
			];
		} else {
			$arr = array();
			foreach ($re->Data as $res) {
				$specs = $res['specs'];
				$aa = array_filter(explode("\n", $specs));
				$list['Specs'] = $aa; //规格
				$sku_id = $res['id'];
				$big = $this->get_IsBig($sku_id);
				$list['Big'] = $big;
				$list['Id'] = $res['id'];   //ID
				$list['Spu'] = $res['spu_id']; //spuid
				$list['Catagory_id'] = $res['catagory_id']; //分类ID
				$list['Att_newbie'] = $res['att_newbie']; //是否是新人专享
				$list['Supplier_id'] = $res['supplier_id']; //供应商ID

				if ($res['weight'] > 0) {
					if ($res['unit'] == "克") {
						$weight = round($res['weight'], 2);
					} else {
						$weight = intval($res['weight']);
					}
					$list['Title'] = $res['title']; //标题
				} else {
					if ($res['unit'] == "克") {
						$weight = round($res['weight'], 2);
					} else {
						$weight = intval($res['weight']);
					}
					$list['Title'] = $res['title']; //标题
				}
				//$list['Title'] = $res['title']; //标题
				$list['Barcode'] = $res['barcode']; //条码
				$list['Serial'] = $res['serial']; //货号
				$list['Weight'] = $res['weight']; //重量
				$list['Unit'] = $res['unit']; //单位
				$list['Att_refund'] = $res['att_refund']; //是否支持退换货
				if ($big > 0) {
					$list['Rebate'] = 0; //vip返佣
				} else {
					$list['Rebate'] = $res['rebate_vip']; //vip返佣
				}
				$list['Only_app'] = $res['att_only_app']; //是否仅支持APP
				$imgs = [];
				// SKU 素材
				if (empty($imgs)) {
					$lis = $this->MySQL->grid("SELECT `thumb_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$sku_id} AND `type` = 0");
					$imgs = array_merge($imgs, $lis);
				}
				// SPU 素材
				if (empty($imgs)) {
					$lis = $this->MySQL->grid("SELECT `spu`.`thumb_url` FROM `yuemi_sale`.`spu_material` AS spu " .
							"LEFT JOIN `yuemi_sale`.`sku` AS sku ON sku.spu_id = spu.spu_id " .
							"WHERE `sku`.`id` = {$sku_id} AND `spu`.`type` = 0 ");
					$imgs = array_merge($imgs, $lis);
				}
//				// ext_sku 素材
//				if (empty($imgs)) {
//					$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = {$res['id']}");
//					$lis = $this->MySQL->grid(
//							"SELECT thumb_url FROM `yuemi_sale`.`ext_sku_mateial` WHERE ext_sku_id = {$ext_sku_id} AND `type` = 0"
//					);
//					$imgs = array_merge($imgs, $lis);
//				}
				// ext_spu 素材
//				if (empty($imgs)) {
//
//					$spu_id = $res['spu_id'];
//
//					$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");
//
//					$lis = $this->MySQL->grid(
//							"SELECT thumb_url FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = {$ext_spu_id} AND `type` = 0"
//					);
//					$imgs = array_merge($imgs, $lis);
//				}

				if (!empty($imgs)) {
					$list['Thumnb'] = URL_RES . '/upload' . $imgs[0]['thumb_url'];
				} else {
					$list['Thumnb'] = '';
				}
				$list['Qty_left'] = $res['depot']; //实时库存
				$list['Price']['Sale'] = $res['price_sale']; //售卖价
				//$list['Price']['Ratio'] = $res['price_ratio']; //售卖价
				$list['Price']['Inv'] = $res['price_inv']; //邀请普通会员售价
				$list['Price']['Vip'] = $res['price_sale']; //TODO:已废弃
				$list['Price']['Ref'] = $res['price_ref']; //对标价
				$list['Price']['Market'] = $res['price_market']; //显示零售价
				$list['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
				$list['Coin']['Buyer'] = $res['coin_buyer']; //购买者赠送阅币
				$list['Coin']['Inviter'] = $res['coin_inviter']; //邀请人奖励阅币
				$list['Limit']['Style'] = $res['limit_style']; //限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购
				$list['Limit']['Size'] = $res['limit_size']; //限购数量
				$arr[] = $list;
			}
			return [
				'Catagory' => [
					'Id' => $request->body->catagory_id,
					'Name' => $cname
				],
				'Search' => [
					'Brand' => [
						'Id' => $request->body->brand_id,
						'Name' => $band_name
					],
					'Supplier' => [
						'Id' => $request->body->supplier_id,
						'Name' => $supplier_name
					],
					'KeyWord' => $key,
					'Sort' => $request->body->sort
				],
				'__code' => 'OK',
				'__message' => '',
				'List' => [
					'DataCount' => $re->DataCount,
					'PageSize' => $re->PageSize,
					'PageCount' => $re->PageCount,
					'PageIndex' => $re->PageIndex,
					'List' => $arr
				],
			];
		}
	}

	/**
	 * 获取是否大礼包 1是 0不是
	 * @param int $kid sku_id
	 */
	private function get_IsBig(int $kid) {
		$wli = $this->MySQL->row("SELECT `catagory_id`,`coin_buyer`,`coin_inviter` FROM `yuemi_sale`.`sku` WHERE `id` = {$kid}");
		if (!empty($wli)) {
			if ($wli['catagory_id'] == 701 && $wli['coin_buyer'] >= 1000) {
				$big = 1;
			} else {
				$big = 0;
			}
		} else {
			$big = 0;
		}
		return $big;
	}

	/**
	 * 商品详情
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id			int			商品ID
	 */
	public function item(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$big = $this->get_IsBig($id);
		$extspuid = $this->MySQL->scalar("SELECT `ext_spu_id` FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = {$id}");
		if ($extspuid) {
			$extshopcode = $this->MySQL->row("SELECT `ext_shop_code` FROM `yuemi_sale`.`ext_spu` WHERE `id` = {$extspuid}");
		} else {
			$extshopcode = [];
		}
		$res = $this->MySQL->row(
				"SELECT * FROM `yuemi_sale`.`sku` WHERE id = {$id}"
		);
		$list['Code'] = empty($extshopcode) ? '' : $extshopcode['ext_shop_code'];
		$list['Id'] = $res['id'];   //ID
		$list['Spu'] = $res['spu_id']; //spuid
		$list['Catagory_id'] = $res['catagory_id']; //分类ID
		$list['Supplier_id'] = $res['supplier_id']; //供应商ID
//		$list['Title'] = $res['title']; //标题

		if ($res['weight'] > 0) {
			if ($res['unit'] == "克") {
				$weight = round($res['weight'], 2);
			} else {
				$weight = intval($res['weight']);
			}
			$list['Title'] = $res['title']; //标题
		} else {
			if ($res['unit'] == "克") {
				$weight = round($res['weight'], 2);
			} else {
				$weight = intval($res['weight']);
			}
			$list['Title'] = $res['title']; //标题
		}
		$list['Barcode'] = $res['barcode']; //条码
		$list['Serial'] = $res['serial']; //货号
		$list['Att_newbie'] = $res['att_newbie']; //是否是新人专享
		$list['Weight'] = $res['weight']; //重量
		$list['Unit'] = $res['unit']; //单位
		$list['Content'] = $res['intro']; //内容
		$list['Qty_left'] = $res['depot']; //实时库存
		if ($big > 0) {
			$list['Rebate'] = 0; //vip返佣
		} else {
			$list['Rebate'] = $res['rebate_vip']; //vip返佣
		}
		$list['Status'] = $res['status']; //  商品状态：0待审,1打回,2通过,3下架,4删除
		$list['Att_refund'] = $res['att_refund']; //是否支持退换货
		$specs = $res['specs'];
		$aa = array_filter(explode("\n", $specs));
		$list['Specs'] = $aa; //规格

		$arr['Price']['Sale'] = $res['price_sale']; //售卖价
		//$arr['Price']['Ratio'] = $res['price_ratio']; //售卖价
		$arr['Price']['Inv'] = $res['price_inv']; //有邀请码会员的价格
		$arr['Price']['Vip'] = $res['price_inv']; //TODO:已废弃
		$arr['Price']['Ref'] = $res['price_ref']; //对标价
		$arr['Price']['Market'] = $res['price_market']; //显示零售价
		$arr['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
		$arr['Coin']['Buyer'] = $res['coin_buyer']; //用户阅币
		$arr['Coin']['Inviter'] = $res['coin_inviter']; //邀请人奖励阅币

		$arr['Limit']['Style'] = $res['limit_style']; //限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购
		$arr['Limit']['Size'] = $res['limit_size']; //限购数量

		$imgs = [];
		//SKU素材
		$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`sku_material` WHERE sku_id = " . $id . " AND `type` = 0 AND `status` != 2");
		if (!empty($shm)) {
			foreach ($shm as $sh) {
				$imgs['Type'] = 1;
				$imgs['Id'] = $sh['id'];
				$imgs['Size'] = $sh['thumb_size'];
				$imgs['Thumb'] = URL_RES . '/upload' . $sh['thumb_url'];
				$img[] = $imgs;
			}
		} else {
			//查spu
			$spuid = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = " . $id);

			$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`spu_material` WHERE spu_id = " . $spuid . " AND `type` = 0 AND `status` != 2 ");
			if (!empty($shm)) {
				foreach ($shm as $sh) {
					$imgs['Type'] = 2;
					$imgs['Id'] = $sh['id'];
					$imgs['Size'] = $sh['thumb_size'];
					$imgs['Thumb'] = URL_RES . '/upload' . $sh['thumb_url'];
					$img[] = $imgs;
				}
			}
//			else {
//				//查外部sku
//				$spu_id = $res['spu_id'];
//				$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");
//				$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE ext_spu_id = {$ext_spu_id}");
//				$shm = $this->MySQL->grid(
//						"SELECT type,id,thumb_size,size,thumb_url FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = {$ext_sku_id} AND `status` != 2" 
//				);
//
//				if (!empty($shm)) {
//					foreach ($shm as $sh) {
//						$imgs['Type'] = 3;
//						$imgs['Id'] = $sh['id'];
//						$imgs['Size'] = $sh['thumb_size'];
//						$imgs['Thumb'] = URL_RES . '/upload' . $sh['thumb_url'];
//						$img[] = $imgs;
//					}
//				} else {
//					//外部spu
//					$spu_id = $res['spu_id'];
//					$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");
//					$shm = $this->MySQL->grid(
//							"SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = {$ext_spu_id} AND `status` != 2"
//					);
//
//					if (!empty($shm)) {
//						foreach ($shm as $sh) {
//							$imgs['Type'] = 4;
//							$imgs['Id'] = $sh['id'];
//							$imgs['Size'] = $sh['thumb_size'];
//							$imgs['Thumb'] = URL_RES . '/upload' . $sh['thumb_url'];
//							$img[] = $imgs;
//						}
//					} else {
//						$img = '';
//					}
//				}
//			}
		}

		$pics = [];
		//SKU素材
		$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`sku_material` WHERE sku_id = " . $id . " AND `type` = 1 AND `status` != 2");
		if (!empty($myshm)) {
			foreach ($myshm as $sh) {
				$pics['Type'] = 1;
				$pics['Id'] = $sh['id'];
				$pics['File_url'] = URL_RES . '/upload' . $sh['file_url'];
				$pics['File_size'] = $sh['file_size'];
				$pic[] = $pics;
			}
		} else {
			//查spu
			$spuid = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = " . $id);

			$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`spu_material` WHERE spu_id = " . $spuid . " AND `type` = 1 AND `status` != 2");
			if (!empty($myshm)) {
				foreach ($myshm as $mysh) {
					$pics['Type'] = 2;
					$pics['Id'] = $mysh['id'];
					$pics['File_url'] = URL_RES . '/upload' . $mysh['file_url'];
					$pics['File_size'] = $mysh['file_size'];
					$pic[] = $pics;
				}
			}
//			else {
//				//查外部sku
//				$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = " . $id);
//				$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = " . $ext_sku_id . " AND `type` = 1 AND `status` != 2");
//				if (!empty($myshm)) {
//					foreach ($myshm as $mysh) {
//						$pics['Type'] = 3;
//						$pics['Id'] = $mysh['id'];
//						$pics['File_url'] = URL_RES . '/upload' . $mysh['file_url'];
//						$pics['File_size'] = $mysh['file_size'];
//						$pic[] = $pics;
//					}
//				} else {
//					//外部spu
//					$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = " . $spuid);
//					$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = " . $ext_spu_id . " AND `type` = 1 AND `status` != 2");
//					if (!empty($myshm)) {
//						foreach ($myshm as $mysh) {
//							$pics['Type'] = 4;
//							$pics['Id'] = $mysh['id'];
//							$pics['File_url'] = URL_RES . '/upload' . $mysh['file_url'];
//							$pics['File_size'] = $mysh['file_size'];
//							$pic[] = $pics;
//						}
//					} else {
//						$pic = '';
//					}
//				}
//			}
		}

		return [
			'__code' => 'OK',
			'__message' => '',
			'Item' => $list,
			'Big' => $big,
			'Images' => $img,
			'Pic' => $pic,
			'Attr' => $arr
		];
	}

	/**
	 * 分类
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function catagory(\Ziima\MVC\REST\Request $request) {
		$arr = $this->MySQL->grid("SELECT `id` AS Id,`name` AS Name FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0 AND `is_hidden` = 0 AND `id` NOT IN (7)  ORDER BY `p_order` DESC");
		return [
			'__code' => 'OK',
			'__message' => '',
			'Catagory' => $arr
		];
	}

	/**
	 * 商品按分类检索列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		keyword				string		查询关键字
	 * @request		sort				string		排序方式 x=销量 p=价格 z=综合(sort字段）
	 * @request		page				int			第几页(固定每页10个，从0页开始)
	 */
	public function search(\Ziima\MVC\REST\Request $request) {

		$sql = "SELECT sku.* " .
				"FROM `yuemi_sale`.`sku` AS sku " .
				"LEFT JOIN `yuemi_sale`.`catagory` AS c ON sku.`catagory_id` = sku.`id` " .
				"LEFT JOIN `yuemi_sale`.`spu` AS spu ON sku.`spu_id` = spu.id";
		$whr = [];

		if (!empty($request->body->keyword)) {
			$whr[] = " sku.`title` LIKE '%" . $this->MySQL->encode($request->body->keyword) . "%'";
			$key = $this->MySQL->encode($request->body->keyword);
		} else {
			$key = '';
		}

		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}

		//最后ORDER BY
		$sql .= ' AND sku.status = 2  AND spu.status = 1 ';
		$sql .= ' ORDER BY `id` DESC ';

		$re = $this->MySQL->paging($sql, 10, $request->body->page);

		if (empty($re)) {
			$arr = '';
			return [
				'__code' => 'OK',
				'__message' => '',
				'List' => $arr
			];
		} else {
//			$arr = array();
			foreach ($re->Data as $res) {
				$sku_id = $res['id'];
				$list['Big'] = $this->get_IsBig($sku_id);
				$list['Id'] = $res['id'];   //ID
				$list['Spu'] = $res['spu_id']; //spuid
				$list['Catagory_id'] = $res['catagory_id']; //分类ID
				$list['Supplier_id'] = $res['supplier_id']; //供应商ID
				$list['Att_newbie'] = $res['att_newbie']; //是否是新人专享
				//$list['Title'] = $res['title']; //标题

				if ($res['weight'] > 0) {
					if ($res['unit'] == "克") {
						$weight = round($res['weight'], 2);
					} else {
						$weight = intval($res['weight']);
					}
					$list['Title'] = $res['title']; //标题
				} else {
					if ($res['unit'] == "克") {
						$weight = round($res['weight'], 2);
					} else {
						$weight = intval($res['weight']);
					}
					$list['Title'] = $res['title']; //标题
				}
				$list['Barcode'] = $res['barcode']; //条码
				$list['Serial'] = $res['serial']; //货号
				$list['Weight'] = $res['weight']; //重量
				$list['Unit'] = $res['unit']; //单位
				if ($this->get_IsBig($sku_id) > 0) {
					$list['Rebate'] = 0; //vip返佣
				} else {
					$list['Rebate'] = $res['rebate_vip']; //vip返佣
				}
				$specs = $res['specs'];
				$aa = array_filter(explode("\n", $specs));
				$list['Specs'] = $aa; //规格
				$imgs = [];
				//SKU素材
				if (empty($imgs)) {
					$lis = $this->MySQL->grid("SELECT `thumb_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$sku_id} AND `type` = 0");
					$imgs = array_merge($imgs, $lis);
				}
				//SPU素材
				if (empty($imgs)) {
					$lis = $this->MySQL->grid("SELECT `spu`.`thumb_url` FROM `yuemi_sale`.`spu_material` AS spu " .
							"LEFT JOIN `yuemi_sale`.`sku` AS sku ON sku.spu_id = spu.spu_id " .
							"WHERE `sku`.`id` = {$sku_id} AND `spu`.`type` = 0 and `spu_material`.`status` != 2");
					$imgs = array_merge($imgs, $lis);
				}

				//ext_sku素材
				if (empty($imgs)) {
					//ext_sku 的ext_sku_id
					$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = {$sku_id}");
					$lis = $this->MySQL->grid(
							"SELECT thumb_url FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = {$ext_sku_id} AND `type` = 0 AND `status` != 2"
					);
					$imgs = array_merge($imgs, $lis);
				}
				//ext_spu素材
				if (empty($imgs)) {
					$spu_id = $res['spu_id'];

					$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");

					$lis = $this->MySQL->grid(
							"SELECT thumb_url FROM `yuemi_sale`.`ext_spu_material` WHERE `ext_spu_id` = {$ext_spu_id} AND `type` = 0 AND `status` != 2"
					);
					$imgs = array_merge($imgs, $lis);
				}

				if (!empty($imgs)) {
					$list['Thumnb'] = URL_RES . '/upload' . $imgs[0]['thumb_url'];
				} else {
					$list['Thumnb'] = '';
				}

				$list['Qty_left'] = $res['depot']; //实时库存
				$list['Price']['Sale'] = $res['price_sale']; //售卖价
				//$list['Price']['Ratio'] = $res['price_ratio']; //售卖价
				$list['Price']['Inv'] = $res['price_inv']; //邀请普通会员售价
				$list['Price']['Vip'] = $res['price_inv']; //对当前VIP的售卖价
				$list['Price']['Ref'] = $res['price_ref']; //对标价
				$list['Price']['Market'] = $res['price_market']; //显示零售价
				$list['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
				$list['Coin']['Buyer'] = $res['coin_buyer']; //购买者赠送阅币
				$list['Coin']['Inviter'] = $res['coin_inviter']; //邀请人奖励阅币
				$list['Limit']['Style'] = $res['limit_style']; //限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购
				$list['Limit']['Size'] = $res['limit_size']; //限购数量

				$arr[] = $list;
			}

			return [
				'Catagory' => [
					'Id' => 0,
					'Name' => ''
				],
				'Search' => [
					'Brand' => [
						'Id' => 0,
						'Name' => ''
					],
					'Supplier' => [
						'Id' => 0,
						'Name' => ''
					],
					'KeyWord' => $key,
					'Sort' => $request->body->sort
				],
				'__code' => 'OK',
				'__message' => '',
				'List' => [
					'DataCount' => $re->DataCount,
					'PageSize' => $re->PageSize,
					'PageCount' => $re->PageCount,
					'PageIndex' => $re->PageIndex,
					'List' => $arr
				],
			];
		}
	}

	/**
	 * 删除严选图片接口
	 * 
	 */
	public function delyanxuan(\Ziima\MVC\REST\Request $request) {
		$this->Redis->del('EXTERNAL_MATERIAL_MAPPER');
		return [
			'__code' => 'OK',
			'__message' => '',
		];
	}

	/**
	 * 新商品按分类检索列表
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		catagory_id			int			分类ID
	 * @request		brand_id			int			品牌ID
	 * @request		supplier_id			int			供应商ID
	 * @request		keyword				string		查询关键字
	 * @request		sort				string		排序方式 x=销量 p=价格 z=综合(sort字段）
	 * @request		page				int			第几页(固定每页10个，从0页开始)
	 */
	public function newlist(\Ziima\MVC\REST\Request $request) {
		$cid = $request->body->catagory_id;
		$sql = "SELECT sku.* " .
				"FROM `yuemi_sale`.`sku` AS sku " .
				"LEFT JOIN `yuemi_sale`.`catagory` AS c ON sku.`catagory_id` = c.id " .
				"LEFT JOIN `yuemi_sale`.`spu` AS spu ON sku.`spu_id` = spu.id";
		$whr = [];
		// 分类
		if ($cid > 0) {
			$cids = "";
			$data = $this->MySQL->grid("SELECT * FROM yuemi_sale.`catagory` WHERE id = {$cid} OR parent_id = {$cid}");
			foreach ($data AS $val) {
				$cids .= "{$val['id']},";
			}
			$cids = trim($cids, ',');
			$whr[] = " sku.`catagory_id` IN ({$cids}) ";
			$cname = $this->MySQL->scalar("SELECT name FROM `yuemi_sale`.`catagory` WHERE id = " . $cid);
		} else {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '分类ID为空');
		}
		// 品牌
		if ($request->body->brand_id > 0) {
			$whr[] = " spu.`brand_id` ='" . $request->body->brand_id . "'";
			$band_name = $this->MySQL->scalar("SELECT name FROM `yuemi_sale`.`brand` WHERE id = " . $request->body->brand_id);
		} else {
			$band_name = '';
		}
		// 供应商
		if ($request->body->supplier_id > 0) {
			$whr[] = " sku.`supplier_id` ='" . $request->body->supplier_id . "'";
			$supplier_name = $this->MySQL->scalar("SELECT name FROM `yuemi_main`.`supplier` WHERE id = " . $request->body->supplier_id);
		} else {
			$supplier_name = '';
		}
		// 关键词
		if (!empty($request->body->keyword)) {
			$whr[] = " sku.`title` LIKE '%" . $this->MySQL->encode($request->body->keyword) . "%'";
			$key = $this->MySQL->encode($request->body->keyword);
		} else {
			$key = '';
		}
		// 组合Where条件
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		// 最后 ORDER BY
		$sql .= ' AND sku.status = 2 AND spu.status = 1';
		if ($request->body->sort == 'p1') {
			$sql .= ' ORDER BY sku.price_sale ASC ';
		} elseif ($request->body->sort == 'p2') {
			$sql .= ' ORDER BY sku.price_sale DESC ';
		} else
			$sql .= ' ORDER BY `id` DESC ';

		// $request->body->sort
		if ($cid == 6) {
			$re = $this->MySQL->paging($sql, 40, $request->body->page);
		} else {
			$re = $this->MySQL->paging($sql, 20, $request->body->page);
		}

		if (empty($re)) {
			$arr = '';
			return [
				'__code' => 'OK',
				'__message' => '',
				'List' => $arr
			];
		} else {
			$va = array();
			$arr = array();
			//$re->Data spu的东西
			$allspecs = [];
			foreach ($re->Data as $res) {
				if (in_array($res['spu_id'], $va)) {
					//如果存在，判断是否是默认
					if ($res['att_default'] == 1) {
						$sku_id = $res['id'];
						$big = $this->get_IsBig($res['id']);
						$list['Big'] = $big;  //是否是大礼包
						$list['Id'] = $res['id'];   //ID
						$list['Spu'] = $res['spu_id']; //spuid
						$list['Catagory_id'] = $res['catagory_id']; //分类ID
						$list['Supplier_id'] = $res['supplier_id']; //供应商ID
						$list['Att_newbie'] = $res['att_newbie']; //是否是新人专享

						if ($res['weight'] > 0) {
							if ($res['unit'] == "克") {
								$weight = round($res['weight'], 2);
							} else {
								$weight = intval($res['weight']);
							}
							$list['Title'] = $res['title']; //标题
						} else {
							if ($res['unit'] == "克") {
								$weight = round($res['weight'], 2);
							} else {
								$weight = intval($res['weight']);
							}
							$list['Title'] = $res['title']; //标题
						}
						$list['Barcode'] = $res['barcode']; //条码
						$list['Serial'] = $res['serial']; //货号
						$list['Weight'] = $res['weight']; //重量
						$list['Unit'] = $res['unit']; //单位
						$list['Att_refund'] = $res['att_refund']; //是否支持退换货
						if ($big > 0) {
							$list['Rebate'] = 0; //vip返佣
						} else {
							$list['Rebate'] = $res['rebate_vip']; //vip返佣
						}
						$list['Only_app'] = $res['att_only_app']; //是否仅支持APP
						$imgs = [];
						// SKU 素材
						if (empty($imgs)) {
							$lis = $this->MySQL->grid("SELECT `thumb_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$sku_id} AND `type` = 0  AND `status` != 2");
							$imgs = array_merge($imgs, $lis);
						}
						if (empty($imgs)) {
							$lis = $this->MySQL->grid("SELECT `spu`.`thumb_url` FROM `yuemi_sale`.`spu_material` AS spu " .
									"LEFT JOIN `yuemi_sale`.`sku` AS sku ON sku.spu_id = spu.spu_id " .
									"WHERE `sku`.`id` = {$sku_id} AND `spu`.`type` = 0  AND `spu_material`.`status` != 2");
							$imgs = array_merge($imgs, $lis);
						}
						if (empty($imgs)) {
							$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = {$res['id']}");
							$lis = $this->MySQL->grid(
									"SELECT thumb_url FROM `yuemi_sale`.`ext_sku_mateial` WHERE ext_sku_id = {$ext_sku_id} AND `type` = 0 AND `status` != 2"
							);
							$imgs = array_merge($imgs, $lis);
						}
						if (empty($imgs)) {

							$spu_id = $res['spu_id'];

							$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");

							$lis = $this->MySQL->grid(
									"SELECT thumb_url FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = {$ext_spu_id} AND `type` = 0 AND `status` != 2"
							);
							$imgs = array_merge($imgs, $lis);
						}
						if (!empty($imgs)) {
							$list['Thumnb'] = URL_RES . '/upload' . $imgs[0]['thumb_url'];
						} else {
							$list['Thumnb'] = '';
						}
						$speces = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`sku` WHERE spu_id = {$res['spu_id']}");

						foreach ($speces as $allsku) {
							$mylist['Sku_id'] = $allsku['id'];
							$bb = $allsku['specs'];
							if (empty($bb)) {
								$aa = '';
							} else {
								$aa = array_filter(explode("\n", $bb));
							}

							$mylist['Sku_specs'] = $aa; //规格
							$mylist['Qty_left'] = $allsku['depot']; //实时库存
							if ($this->get_IsBig($allsku['id']) > 0) {
								$mylist['Rebate'] = 0; //vip返佣
							} else {
								$mylist['Rebate'] = $allsku['rebate_vip']; //vip返佣
							}

							$mylist['Status'] = $allsku['status']; //  商品状态：0待审,1打回,2通过,3下架,4删除
							$mylist['Price']['Sale'] = $allsku['price_sale']; //售卖价
							//$mylist['Price']['Ratio'] = $allsku['price_ratio']; //售卖价
							$mylist['Price']['Inv'] = $allsku['price_inv']; //有邀请码会员的价格
							$mylist['Price']['Ref'] = $allsku['price_ref']; //对标价
							$mylist['Price']['Market'] = $allsku['price_market']; //显示零售价
							$allspecs[] = $mylist;
						}
						$list['SpecsList'] = $aa; //规格
						$list['Qty_left'] = $res['depot']; //实时库存
						$list['Price']['Sale'] = $res['price_sale']; //售卖价
						//$list['Price']['Ratio'] = $res['price_ratio']; //售卖价
						$list['Price']['Inv'] = $res['price_inv']; //邀请普通会员售价
						$list['Price']['Ref'] = $res['price_ref']; //对标价
						$list['Price']['Market'] = $res['price_market']; //显示零售价
						$list['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
						$list['Coin']['Buyer'] = $res['coin_buyer']; //购买者赠送阅币
						$list['Coin']['Inviter'] = $res['coin_inviter']; //邀请人奖励阅币
						$list['Limit']['Style'] = $res['limit_style']; //限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购
						$list['Limit']['Size'] = $res['limit_size']; //限购数量

						$arr[$res['spu_id']] = $list;
					}
				} else {
					//如果不存在，就把所需要信息放到$arr数组中
					array_push($va, $res['spu_id']);
					$sku_id = $res['id'];
					$list['Id'] = $res['id'];   //ID
					$big = $this->get_IsBig($res['id']);
					$list['Big'] = $big;  //是否是大礼包
					$list['Spu'] = $res['spu_id']; //spuid
					$list['Catagory_id'] = $res['catagory_id']; //分类ID
					$list['Supplier_id'] = $res['supplier_id']; //供应商ID

					if ($res['weight'] > 0) {
						if ($res['unit'] == "克") {
							$weight = round($res['weight'], 2);
						} else {
							$weight = intval($res['weight']);
						}
						$list['Title'] = $res['title']; //标题
					} else {
						if ($res['unit'] == "克") {
							$weight = round($res['weight'], 2);
						} else {
							$weight = intval($res['weight']);
						}
						$list['Title'] = $res['title']; //标题
					}
					$list['Barcode'] = $res['barcode']; //条码
					$list['Serial'] = $res['serial']; //货号
					$list['Weight'] = $res['weight']; //重量
					$list['Unit'] = $res['unit']; //单位
					$list['Att_refund'] = $res['att_refund']; //是否支持退换货
					if ($big > 0) {
						$list['Rebate'] = 0; //vip返佣
					} else {
						$list['Rebate'] = $res['rebate_vip']; //vip返佣
					}
					$list['Only_app'] = $res['att_only_app']; //是否仅支持APP
					$imgs = [];
					// SKU 素材
					if (empty($imgs)) {
						$lis = $this->MySQL->grid("SELECT `thumb_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$sku_id} AND `type` = 0 AND `status` != 2");
						$imgs = array_merge($imgs, $lis);
					}
					// SPU 素材
					if (empty($imgs)) {
						$lis = $this->MySQL->grid("SELECT `spu`.`thumb_url` FROM `yuemi_sale`.`spu_material` AS spu " .
								"LEFT JOIN `yuemi_sale`.`sku` AS sku ON sku.spu_id = spu.spu_id " .
								"WHERE `sku`.`id` = {$sku_id} AND `spu`.`type` = 0 AND `spu`.`status` != 2");
						$imgs = array_merge($imgs, $lis);
					}
					// ext_sku 素材
					if (empty($imgs)) {
						$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = {$res['id']}");
						$lis = $this->MySQL->grid(
								"SELECT thumb_url FROM `yuemi_sale`.`ext_sku_mateial` WHERE ext_sku_id = {$ext_sku_id} AND `type` = 0 AND `status` != 2"
						);
						$imgs = array_merge($imgs, $lis);
					}
					// ext_spu 素材
					if (empty($imgs)) {

						$spu_id = $res['spu_id'];

						$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");

						$lis = $this->MySQL->grid(
								"SELECT thumb_url FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = {$ext_spu_id} AND `type` = 0 AND `status` != 2"
						);
						$imgs = array_merge($imgs, $lis);
					}

					if (!empty($imgs)) {
						$list['Thumnb'] = URL_RES . '/upload' . $imgs[0]['thumb_url'];
					} else {
						$list['Thumnb'] = '';
					}

					$speces = $this->MySQL->grid("SELECT *  FROM `yuemi_sale`.`sku` WHERE spu_id = {$res['spu_id']}");

					foreach ($speces as $allsku) {
						$mylist['Sku_id'] = $allsku['id'];
						$bb = $allsku['specs'];
						if (empty($bb)) {
							$aa = '';
						} else {
							$aa = array_filter(explode("\n", $bb));
						}
						$mylist['Sku_specs'] = $aa; //规格
						$mylist['Qty_left'] = $allsku['depot']; //实时库存
						if ($this->get_IsBig($allsku['id']) > 0) {
							$mylist['Rebate'] = 0; //vip返佣
						} else {
							$mylist['Rebate'] = $allsku['rebate_vip']; //vip返佣
						}
						$mylist['Status'] = $allsku['status']; //  商品状态：0待审,1打回,2通过,3下架,4删除
						$mylist['Price']['Sale'] = $allsku['price_sale']; //售卖价
						//$mylist['Price']['Ratio'] = $allsku['price_ratio']; //售卖价
						$mylist['Price']['Inv'] = $allsku['price_inv']; //有邀请码会员的价格
						$mylist['Price']['Ref'] = $allsku['price_ref']; //对标价
						$mylist['Price']['Market'] = $allsku['price_market']; //显示零售价
						$allspecs[] = $mylist;
					}

					$list['SpecsList'] = $mylist; //规格
					$list['Qty_left'] = $res['depot']; //实时库存
					$list['Price']['Sale'] = $res['price_sale']; //售卖价
					//$list['Price']['Ratio'] = $res['price_ratio']; //售卖价
					$list['Price']['Inv'] = $res['price_inv']; //邀请普通会员售价
					$list['Price']['Ref'] = $res['price_ref']; //对标价
					$list['Price']['Market'] = $res['price_market']; //显示零售价
					$list['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
					$list['Coin']['Buyer'] = $res['coin_buyer']; //购买者赠送阅币
					$list['Coin']['Inviter'] = $res['coin_inviter']; //邀请人奖励阅币
					$list['Limit']['Style'] = $res['limit_style']; //限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购
					$list['Limit']['Size'] = $res['limit_size']; //限购数量
					$arr[$res['spu_id']] = $list;
				}
			}
			$myarr = [];
			foreach ($arr as $v) {
				$myarr[] = $v;
			}

			return [
				'Catagory' => [
					'Id' => $request->body->catagory_id,
					'Name' => $cname
				],
				'Search' => [
					'Brand' => [
						'Id' => $request->body->brand_id,
						'Name' => $band_name
					],
					'Supplier' => [
						'Id' => $request->body->supplier_id,
						'Name' => $supplier_name
					],
					'KeyWord' => $key,
					'Sort' => $request->body->sort
				],
				'__code' => 'OK',
				'__message' => '',
				'List' => [
					'DataCount' => $re->DataCount,
					'PageSize' => $re->PageSize,
					'PageCount' => $re->PageCount,
					'PageIndex' => $re->PageIndex,
					'List' => $myarr
				],
			];
		}
	}

	/**
	 * 新商品详情
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id			int			商品ID
	 */
	public function newitem(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;

		$extspuid = $this->MySQL->scalar("SELECT `ext_spu_id` FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = {$id}");
		if ($extspuid) {
			$extshopcode = $this->MySQL->row("SELECT `ext_shop_code` FROM `yuemi_sale`.`ext_spu` WHERE `id` = {$extspuid}");
		} else {
			$extshopcode = [];
		}
		$res = $this->MySQL->row(
				"SELECT * FROM `yuemi_sale`.`sku` WHERE id = {$id}"
		);
		$big = $this->get_IsBig($res['id']);  //是否是大礼包	
		$list['Code'] = empty($extshopcode) ? '' : $extshopcode['ext_shop_code'];
		$list['Id'] = $res['id'];   //ID
		$list['Big'] = $this->get_IsBig($res['id']);  //是否是大礼包
		$list['Spu'] = $res['spu_id']; //spuid
		$list['Catagory_id'] = $res['catagory_id']; //分类ID
		$list['Supplier_id'] = $res['supplier_id']; //供应商ID
		$list['Att_newbie'] = $res['att_newbie']; //是否是新人专享

		if ($res['weight'] > 0) {
			if ($res['unit'] == "克") {
				$weight = round($res['weight'], 2);
			} else {
				$weight = intval($res['weight']);
			}
			$list['Title'] = $res['title']; //标题
		} else {
			if ($res['unit'] == "克") {
				$weight = round($res['weight'], 2);
			} else {
				$weight = intval($res['weight']);
			}
			$list['Title'] = $res['title']; //标题
		}
		$list['Barcode'] = $res['barcode']; //条码
		$list['Serial'] = $res['serial']; //货号
		$list['Weight'] = $res['weight']; //重量
		$list['Unit'] = $res['unit']; //单位
		$list['Content'] = $res['intro']; //内容
		$list['Qty_left'] = $res['depot']; //实时库存
		if ($big > 0) {
			$list['Rebate'] = 0; //vip返佣
		} else {
			$list['Rebate'] = $res['rebate_vip']; //vip返佣
		}
		$list['Status'] = $res['status']; //  商品状态：0待审,1打回,2通过,3下架,4删除
		$list['Att_refund'] = $res['att_refund']; //是否支持退换货
		$specs = $res['specs'];
		$aa = array_filter(explode("\n", $specs));
		$list['Specs'] = $aa; //规格
		$arr['Price']['Sale'] = $res['price_sale']; //售卖价
		//$arr['Price']['Ratio'] = $res['price_ratio']; //售卖价
		$arr['Price']['Inv'] = $res['price_inv']; //有邀请码会员的价格
		$arr['Price']['Ref'] = $res['price_ref']; //对标价
		$arr['Price']['Market'] = $res['price_market']; //显示零售价
		$arr['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
		$arr['Coin']['Buyer'] = $res['coin_buyer']; //用户阅币
		$arr['Coin']['Inviter'] = $res['coin_inviter']; //邀请人奖励阅币
		$arr['Limit']['Style'] = $res['limit_style']; //限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购
		$arr['Limit']['Size'] = $res['limit_size']; //限购数量

		$imgs = [];
		$img = [];
		//SKU素材
		$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`sku_material` WHERE sku_id = " . $id . " AND `type` = 0 AND `status` != 2");
		if (!empty($shm)) {
			foreach ($shm as $sh) {
				$imgs['Type'] = 1;
				$imgs['Id'] = $sh['id'];
				$imgs['Size'] = $sh['thumb_size'];
				$imgs['Thumb'] = URL_RES . '/upload' . $sh['thumb_url'];
				$img[] = $imgs;
			}
		} else {
			//查spu
			$spuid = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = " . $id);

			$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`spu_material` WHERE spu_id = " . $spuid . " AND `type` = 0 AND `status` != 2");
			if (!empty($shm)) {
				foreach ($shm as $sh) {
					$imgs['Type'] = 2;
					$imgs['Id'] = $sh['id'];
					$imgs['Size'] = $sh['thumb_size'];
					$imgs['Thumb'] = URL_RES . '/upload' . $sh['thumb_url'];
					$img[] = $imgs;
				}
			} else {
				//查外部sku
				$spu_id = $res['spu_id'];
				$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");
				$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE ext_spu_id = {$ext_spu_id}");
				$shm = $this->MySQL->grid(
						"SELECT type,id,thumb_size,size,thumb_url FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = {$ext_sku_id}"
				);

				if (!empty($shm)) {
					foreach ($shm as $sh) {
						$imgs['Type'] = 3;
						$imgs['Id'] = $sh['id'];
						$imgs['Size'] = $sh['thumb_size'];
						$imgs['Thumb'] = URL_RES . '/upload' . $sh['thumb_url'];
						$img[] = $imgs;
					}
				} else {
					//外部spu
					$spu_id = $res['spu_id'];
					$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");
					$shm = $this->MySQL->grid(
							"SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = {$ext_spu_id}"
					);

					if (!empty($shm)) {
						foreach ($shm as $sh) {
							$imgs['Type'] = 4;
							$imgs['Id'] = $sh['id'];
							$imgs['Size'] = $sh['thumb_size'];
							$imgs['Thumb'] = URL_RES . '/upload' . $sh['thumb_url'];
							$img[] = $imgs;
						}
					} else {
						$img = '';
					}
				}
			}
		}
		$pics = [];
		//SKU素材
		$pic = [];
		$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`sku_material` WHERE sku_id = " . $id . " AND `type` = 1 AND `status` != 2");
		if (!empty($myshm)) {
			foreach ($myshm as $sh) {
				$pics['Type'] = 1;
				$pics['Id'] = $sh['id'];
				$pics['File_url'] = URL_RES . '/upload' . $sh['file_url'];
				$pics['File_size'] = $sh['file_size'];
				$pic[] = $pics;
			}
		} else {
			//查spu
			$spuid = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = " . $id);

			$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`spu_material` WHERE spu_id = " . $spuid . " AND `type` = 1 AND `status` != 2");
			if (!empty($myshm)) {
				foreach ($myshm as $mysh) {
					$pics['Type'] = 2;
					$pics['Id'] = $mysh['id'];
					$pics['File_url'] = URL_RES . '/upload' . $mysh['file_url'];
					$pics['File_size'] = $mysh['file_size'];
					$pic[] = $pics;
				}
			} else {
				//查外部sku
				$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = " . $id);
				$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = " . $ext_sku_id . " AND `type` = 1 AND `status` != 2");
				if (!empty($myshm)) {
					foreach ($myshm as $mysh) {
						$pics['Type'] = 3;
						$pics['Id'] = $mysh['id'];
						$pics['File_url'] = URL_RES . '/upload' . $mysh['file_url'];
						$pics['File_size'] = $mysh['file_size'];
						$pic[] = $pics;
					}
				} else {
					//外部spu
					$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = " . $spuid);
					$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = " . $ext_spu_id . " AND `type` = 1 AND `status` != 2");
					if (!empty($myshm)) {
						foreach ($myshm as $mysh) {
							$pics['Type'] = 4;
							$pics['Id'] = $mysh['id'];
							$pics['File_url'] = URL_RES . '/upload' . $mysh['file_url'];
							$pics['File_size'] = $mysh['file_size'];
							$pic[] = $pics;
						}
					} else {
						$pic = '';
					}
				}
			}
		}

		//所有的spu相同的sku
		$skus = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`sku` WHERE spu_id = {$res['spu_id']} AND status = 2");
		$allspecs = [];
		foreach ($skus as $allsku) {
			$mylist['Sku_id'] = $allsku['id'];
			$bb = $allsku['specs'];
			if (empty($bb)) {
				$aa = '';
			} else {
				$aa = array_filter(explode("\n", $bb));
			}

			$mylist['Sku_specs'] = $aa; //规格
			$mylist['Qty_left'] = $allsku['depot']; //实时库存
			if ($this->get_IsBig($allsku['id']) > 0) {
				$mylist['Rebate'] = 0; //vip返佣
			} else {
				$mylist['Rebate'] = $allsku['rebate_vip']; //vip返佣
			}
			$mylist['Status'] = $allsku['status']; //  商品状态：0待审,1打回,2通过,3下架,4删除
			$mylist['Price']['Sale'] = $allsku['price_sale']; //售卖价
			$mylist['Price']['Inv'] = $allsku['price_inv']; //有邀请码会员的价格
			$mylist['Price']['Ref'] = $allsku['price_ref']; //对标价
			$mylist['Price']['Market'] = $allsku['price_market']; //显示零售价
			$allspecs[] = $mylist;
		}

		return [
			'__code' => 'OK',
			'__message' => '',
			'Item' => $list,
			'Big' => $big,
			'Images' => $img,
			'Pic' => $pic,
			'Attr' => $arr,
			'AllSpecs' => $allspecs
		];
	}

	/**
	 * 新商品搜索列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		keyword				string		查询关键字
	 * @request		sort				string		排序方式 x=销量 p=价格 z=综合(sort字段）
	 * @request		page				int			第几页(固定每页10个，从0页开始)
	 */
	public function newsearch(\Ziima\MVC\REST\Request $request) {

		$sql = "SELECT sku.* " .
				"FROM `yuemi_sale`.`sku` AS sku " .
				"LEFT JOIN `yuemi_sale`.`catagory` AS c ON sku.`catagory_id` = sku.`id` " .
				"LEFT JOIN `yuemi_sale`.`spu` AS spu ON sku.`spu_id` = spu.id";
		$whr = [];

		if (!empty($request->body->keyword)) {
			$whr[] = " sku.`title` LIKE '%" . $this->MySQL->encode($request->body->keyword) . "%'";
			$key = $this->MySQL->encode($request->body->keyword);
		} else {
			$key = '';
		}

		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}

		//最后ORDER BY
		$sql .= ' AND sku.status = 2 AND spu.status = 1 ';
		$sql .= ' ORDER BY `id` DESC ';

		$re = $this->MySQL->paging($sql, 30, $request->body->page);

		if (empty($re)) {
			$arr = '';
			return [
				'__code' => 'OK',
				'__message' => '',
				'List' => $arr
			];
		} else {
			$va = array();
			$arr = array();
			foreach ($re->Data as $res) {
				if ($res['att_default'] == 1) {
					$sku_id = $res['id'];
					$list['Id'] = $res['id'];   //ID
					$list['Big'] = $this->get_IsBig($res['id']);  //是否是大礼包
					$list['Spu'] = $res['spu_id']; //spuid
					$list['Catagory_id'] = $res['catagory_id']; //分类ID
					$list['Supplier_id'] = $res['supplier_id']; //供应商ID
					$list['Att_newbie'] = $res['att_newbie']; //是否是新人专享

					if ($res['weight'] > 0) {
						if ($res['unit'] == "克") {
							$weight = round($res['weight'], 2);
						} else {
							$weight = intval($res['weight']);
						}
						$list['Title'] = $res['title']; //标题
					} else {
						if ($res['unit'] == "克") {
							$weight = round($res['weight'], 2);
						} else {
							$weight = intval($res['weight']);
						}
						$list['Title'] = $res['title']; //标题
					}
					$list['Barcode'] = $res['barcode']; //条码
					$list['Serial'] = $res['serial']; //货号
					$list['Weight'] = $res['weight']; //重量
					$list['Unit'] = $res['unit']; //单位
					if ($this->get_IsBig($res['id']) > 0) {
						$list['Rebate'] = 0; //vip返佣
					} else {
						$list['Rebate'] = $res['rebate_vip']; //vip返佣
					}
					//$specs = $res['specs'];
					//$aa = array_filter(explode("\n", $specs));
					//$list['Specs'] = $aa; //规格
					$speces = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`sku` WHERE spu_id = {$res['spu_id']}");
					foreach ($speces as $allsku) {
						$mylist['Sku_id'] = $allsku['id'];
						$bb = $allsku['specs'];
						if (empty($bb)) {
							$aa = '';
						} else {
							$aa = array_filter(explode("\n", $bb));
						}
					}
					$list['Sku_specs'] = $aa; //规格
					$imgs = [];
					//SKU素材
					if (empty($imgs)) {
						$lis = $this->MySQL->grid("SELECT `thumb_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$sku_id} AND `type` = 0  AND `status` != 2");
						$imgs = array_merge($imgs, $lis);
					}
					//SPU素材
					if (empty($imgs)) {
						$lis = $this->MySQL->grid("SELECT `spu`.`thumb_url` FROM `yuemi_sale`.`spu_material` AS spu " .
								"LEFT JOIN `yuemi_sale`.`sku` AS sku ON sku.spu_id = spu.spu_id " .
								"WHERE `sku`.`id` = {$sku_id} AND `spu`.`type` = 0  AND `spu`.`status` != 2 ");
						$imgs = array_merge($imgs, $lis);
					}

					//ext_sku素材
					if (empty($imgs)) {
						//ext_sku 的ext_sku_id
						$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = {$sku_id}");
						$lis = $this->MySQL->grid(
								"SELECT thumb_url FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = {$ext_sku_id} AND `type` = 0  AND `status` != 2"
						);
						$imgs = array_merge($imgs, $lis);
					}
					//ext_spu素材
					if (empty($imgs)) {
						$spu_id = $res['spu_id'];

						$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");

						$lis = $this->MySQL->grid(
								"SELECT thumb_url FROM `yuemi_sale`.`ext_spu_material` WHERE `ext_spu_id` = {$ext_spu_id} AND `type` = 0  AND `status` != 2"
						);
						$imgs = array_merge($imgs, $lis);
					}

					if (!empty($imgs)) {
						$list['Thumnb'] = URL_RES . '/upload' . $imgs[0]['thumb_url'];
					} else {
						$list['Thumnb'] = '';
					}

					$list['Qty_left'] = $res['depot']; //实时库存
					$list['Price']['Sale'] = $res['price_sale']; //售卖价
					//$list['Price']['Ratio'] = $res['price_ratio']; //售卖价
					$list['Price']['Inv'] = $res['price_inv']; //邀请普通会员售价
					$list['Price']['Ref'] = $res['price_ref']; //对标价
					$list['Price']['Market'] = $res['price_market']; //显示零售价
					$list['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
					$list['Coin']['Buyer'] = $res['coin_buyer']; //购买者赠送阅币
					$list['Coin']['Inviter'] = $res['coin_inviter']; //邀请人奖励阅币
					$list['Limit']['Style'] = $res['limit_style']; //限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购
					$list['Limit']['Size'] = $res['limit_size']; //限购数量
					$arr[$res['spu_id']] = $list;
				} else {
					array_push($va, $res['spu_id']);
					$sku_id = $res['id'];
					$list['Id'] = $res['id'];   //ID
					$list['Big'] = $this->get_IsBig($res['id']);  //是否是大礼包
					$list['Spu'] = $res['spu_id']; //spuid
					$list['Catagory_id'] = $res['catagory_id']; //分类ID
					$list['Supplier_id'] = $res['supplier_id']; //供应商ID


					if ($res['weight'] > 0) {
						if ($res['unit'] == "克") {
							$weight = round($res['weight'], 2);
						} else {
							$weight = intval($res['weight']);
						}
						$list['Title'] = $res['title']; //标题
					} else {
						if ($res['unit'] == "克") {
							$weight = round($res['weight'], 2);
						} else {
							$weight = intval($res['weight']);
						}
						$list['Title'] = $res['title']; //标题
					}

					$list['Barcode'] = $res['barcode']; //条码
					$list['Serial'] = $res['serial']; //货号
					$list['Weight'] = $res['weight']; //重量
					$list['Unit'] = $res['unit']; //单位
					if ($this->get_IsBig($res['id']) > 0) {
						$list['Rebate'] = 0;
					} else {
						$list['Rebate'] = $res['rebate_vip'];
					}
					//vip返佣
					//$specs = $res['specs'];
					//$aa = array_filter(explode("\n", $specs));
					//$list['Specs'] = $aa; //规格
					$speces = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`sku` WHERE spu_id = {$res['spu_id']}");
					foreach ($speces as $allsku) {
						$mylist['Sku_id'] = $allsku['id'];
						$bb = $allsku['specs'];
						if (empty($bb)) {
							$aa = '';
						} else {
							$aa = array_filter(explode("\n", $bb));
						}
					}
					$list['Sku_specs'] = $aa; //规格
					$imgs = [];
					//SKU素材
					if (empty($imgs)) {
						$lis = $this->MySQL->grid("SELECT `thumb_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$sku_id} AND `type` = 0  AND `status` != 2");
						$imgs = array_merge($imgs, $lis);
					}
					//SPU素材
					if (empty($imgs)) {
						$lis = $this->MySQL->grid("SELECT `spu`.`thumb_url` FROM `yuemi_sale`.`spu_material` AS spu " .
								"LEFT JOIN `yuemi_sale`.`sku` AS sku ON sku.spu_id = spu.spu_id " .
								"WHERE `sku`.`id` = {$sku_id} AND `spu`.`type` = 0  AND `spu`.`status` != 2");
						$imgs = array_merge($imgs, $lis);
					}

					//ext_sku素材
					if (empty($imgs)) {
						//ext_sku 的ext_sku_id
						$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = {$sku_id}");
						$lis = $this->MySQL->grid(
								"SELECT thumb_url FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = {$ext_sku_id} AND `type` = 0  AND `status` != 2"
						);
						$imgs = array_merge($imgs, $lis);
					}
					//ext_spu素材
					if (empty($imgs)) {
						$spu_id = $res['spu_id'];

						$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");

						$lis = $this->MySQL->grid(
								"SELECT thumb_url FROM `yuemi_sale`.`ext_spu_material` WHERE `ext_spu_id` = {$ext_spu_id} AND `type` = 0  AND `status` != 2"
						);
						$imgs = array_merge($imgs, $lis);
					}

					if (!empty($imgs)) {
						$list['Thumnb'] = URL_RES . '/upload' . $imgs[0]['thumb_url'];
					} else {
						$list['Thumnb'] = '';
					}

					$list['Qty_left'] = $res['depot']; //实时库存
					$list['Price']['Sale'] = $res['price_sale']; //售卖价
					//$list['Price']['Ratio'] = $res['price_ratio']; //售卖价
					$list['Price']['Inv'] = $res['price_inv']; //邀请普通会员售价
					$list['Price']['Ref'] = $res['price_ref']; //对标价
					$list['Price']['Market'] = $res['price_market']; //显示零售价
					$list['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
					$list['Coin']['Buyer'] = $res['coin_buyer']; //购买者赠送阅币
					$list['Coin']['Inviter'] = $res['coin_inviter']; //邀请人奖励阅币
					$list['Limit']['Style'] = $res['limit_style']; //限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购
					$list['Limit']['Size'] = $res['limit_size']; //限购数量
					$arr[$res['spu_id']] = $list;
				}
			}

			$myarr = [];
			foreach ($arr as $v) {
				$myarr[] = $v;
			}
			return [
				'Catagory' => [
					'Id' => 0,
					'Name' => ''
				],
				'Search' => [
					'Brand' => [
						'Id' => 0,
						'Name' => ''
					],
					'Supplier' => [
						'Id' => 0,
						'Name' => ''
					],
					'KeyWord' => $key,
					'Sort' => $request->body->sort
				],
				'__code' => 'OK',
				'__message' => '',
				'List' => [
					'DataCount' => $re->DataCount,
					'PageSize' => $re->PageSize,
					'PageCount' => $re->PageCount,
					'PageIndex' => $re->PageIndex,
					'List' => $myarr
				],
			];
		}
	}

	/**
	 * 查看一个spu下面所有sku商品
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		spu_id				int		spuid
	 */
	public function selectspu(\Ziima\MVC\REST\Request $request) {
		$list = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`sku` WHERE spu_id = {$request->body->spu_id}");
		return[
			'List' => $list
		];
	}

	/**
	 * 查看spu
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		spu_id				int		spuid
	 */
	public function selectonespu(\Ziima\MVC\REST\Request $request) {
		$list = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`spu` WHERE id = {$request->body->spu_id}");
		return[
			'List' => $list
		];
	}

	/**
	 * 查看sku
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		sku_id				int		skuid
	 */
	public function selectsku(\Ziima\MVC\REST\Request $request) {
		$list = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`sku` WHERE id = {$request->body->sku_id}");
		return[
			'List' => $list
		];
	}

	/**
	 * 清空spu内容
	 *  @param \Ziima\MVC\REST\Request $request
	 *  @request		spu_id				int		spuid
	 */
	public function delspu_intro(\Ziima\MVC\REST\Request $request) {
		$list = $this->MySQL->execute("UPDATE `yuemi_sale`.`spu` SET intro='' WHERE id = {$request->body->spu_id}");
		return[
			'__code' => 'OK',
			'__message' => '成功'
		];
	}

	/**
	 * 清空sku内容
	 *  @param \Ziima\MVC\REST\Request $request
	 *  @request		sku_id				int		skuid
	 */
	public function delsku_intro(\Ziima\MVC\REST\Request $request) {
		$this->MySQL->execute("UPDATE `yuemi_sale`.`sku` SET intro= '' WHERE id = {$request->body->sku_id}");
		return[
			'__code' => 'OK',
			'__message' => '成功'
		];
	}

	/**
	 * 新列表页
	 * @request		catagory_id			int			分类ID 1 1
	 * @request		brand_id			int			品牌ID 0 0
	 * @request		supplier_id			int			供应商ID 0  0
	 * @request		keyword				string		查询关键字 
	 * @request		sort				string		排序方式	x=销量p=价格z=综合(sort字段) x
	 * @request		page				int			第几页(固定每页10个，从0页开始) 0 0 
	 */
	public function spulist(\Ziima\MVC\REST\Request $request) {
		$cid = $request->body->catagory_id;
		$sql = "SELECT sku.* ,spu.title as spu_title " .
				" FROM `yuemi_sale`.`spu` as spu " .
				"LEFT JOIN `yuemi_sale`.`catagory` as c ON spu.catagory_id = c.id " .
				"INNER JOIN `yuemi_sale`.`sku` as sku ON spu.id = sku.spu_id "
		;
		$whr = [];
		// 分类
		if ($cid > 0) {
			$cids = "";
			$data = $this->MySQL->grid("SELECT * FROM yuemi_sale.`catagory` WHERE id = {$cid} OR parent_id = {$cid}");
			foreach ($data AS $val) {
				$cids .= "{$val['id']},";
			}
			$cids = trim($cids, ',');
			$whr[] = " sku.`catagory_id` IN ({$cids}) ";
			$cname = $this->MySQL->scalar("SELECT name FROM `yuemi_sale`.`catagory` WHERE id = " . $cid);
		} else {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '分类ID为空');
		}
		// 品牌
		if ($request->body->brand_id > 0) {
			$whr[] = " spu.`brand_id` ='" . $request->body->brand_id . "'";
			$band_name = $this->MySQL->scalar("SELECT name FROM `yuemi_sale`.`brand` WHERE id = " . $request->body->brand_id);
		} else {
			$band_name = '';
		}
		// 供应商
		if ($request->body->supplier_id > 0) {
			$whr[] = " sku.`supplier_id` ='" . $request->body->supplier_id . "'";
			$supplier_name = $this->MySQL->scalar("SELECT name FROM `yuemi_main`.`supplier` WHERE id = " . $request->body->supplier_id);
		} else {
			$supplier_name = '';
		}
		// 关键词
		if (!empty($request->body->keyword)) {
			$whr[] = " sku.`title` LIKE '%" . $this->MySQL->encode($request->body->keyword) . "%'";
			$key = $this->MySQL->encode($request->body->keyword);
		} else {
			$key = '';
		}
		// 组合Where条件
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		// 最后 ORDER BY
		$sql .= ' AND sku.status = 2 AND spu.status = 1';
		if ($request->body->sort == 'p1') {
			$sql .= ' group by spu.id ORDER BY sku.price_sale ASC ';
		} elseif ($request->body->sort == 'p2') {
			$sql .= ' group by spu.id ORDER BY sku.price_sale DESC ';
		} else
			$sql .= ' group by spu.id ORDER BY `id` DESC ';

		$re = $this->MySQL->paging($sql, 3, $request->body->page);
		if (empty($re)) {
			$arr = '';
			return [
				'__code' => 'OK',
				'__message' => '',
				'List' => $arr
			];
		} else {
			foreach ($re->Data as $res) {
				$sku_id = $res['id'];
				$list['Id'] = $res['id'];   //SKUID
				$list['Big'] = $this->get_IsBig($res['id']);  //是否是大礼包
				$list['Spu'] = $res['spu_id']; //spuid
				$list['Catagory_id'] = $res['catagory_id']; //分类ID
				$list['Att_newbie'] = $res['att_newbie']; //是否是新人专享
//				if ($res['weight'] > 0) {
//					if ($res['unit'] == "克") {
//						$weight = round($res['weight'], 2);
//					} else {
//						$weight = intval($res['weight']);
//					}
//					$list['Title'] = $res['title']; //标题
//				} else {
//					if ($res['unit'] == "克") {
//						$weight = round($res['weight'], 2);
//					} else {
//						$weight = intval($res['weight']);
//					}
//					$list['Title'] = $res['title']; //标题
//				}

				$list['Title'] = $res['title']; //标题
				if ($this->get_IsBig($res['id']) > 0) {
					$list['Rebate'] = 0; //vip返佣
				} else {
					$list['Rebate'] = $res['rebate_vip']; //vip返佣
				}
				$imgs = [];
				//SKU素材
				if (empty($imgs)) {
					$lis = $this->MySQL->grid("SELECT `thumb_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$sku_id} AND `type` = 0  AND `status` != 2");
					$imgs = array_merge($imgs, $lis);
				}
				//SPU素材
				if (empty($imgs)) {
					$lis = $this->MySQL->grid("SELECT `spu`.`thumb_url` FROM `yuemi_sale`.`spu_material` AS spu " .
							"LEFT JOIN `yuemi_sale`.`sku` AS sku ON sku.spu_id = spu.spu_id " .
							"WHERE `sku`.`id` = {$sku_id} AND `spu`.`type` = 0  AND `spu`.`status` != 2 ");
					$imgs = array_merge($imgs, $lis);
				}

				//ext_sku素材
				if (empty($imgs)) {
					//ext_sku 的ext_sku_id
					$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = {$sku_id}");
					$lis = $this->MySQL->grid(
							"SELECT thumb_url FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = {$ext_sku_id} AND `type` = 0  AND `status` != 2"
					);
					$imgs = array_merge($imgs, $lis);
				}
				//ext_spu素材
				if (empty($imgs)) {
					$spu_id = $res['spu_id'];

					$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");

					$lis = $this->MySQL->grid(
							"SELECT thumb_url FROM `yuemi_sale`.`ext_spu_material` WHERE `ext_spu_id` = {$ext_spu_id} AND `type` = 0  AND `status` != 2"
					);
					$imgs = array_merge($imgs, $lis);
				}

				if (!empty($imgs)) {
					$list['Thumnb'] = URL_RES . '/upload' . $imgs[0]['thumb_url'];
				} else {
					$list['Thumnb'] = '';
				}

				$list['Qty_left'] = $res['depot']; //实时库存
				$list['Price']['Sale'] = $res['price_sale']; //售卖价
				//$list['Price']['Ratio'] = $res['price_ratio']; //售卖价
				$list['Price']['Inv'] = $res['price_inv']; //邀请普通会员售价
				$list['Price']['Ref'] = $res['price_ref']; //对标价
				$list['Price']['Market'] = $res['price_market']; //显示零售价
				$list['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
				$list['Coin']['Buyer'] = $res['coin_buyer']; //购买者赠送阅币
				$list['Coin']['Inviter'] = $res['coin_inviter']; //邀请人奖励阅币
				$list['Limit']['Style'] = $res['limit_style']; //限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购
				$list['Limit']['Size'] = $res['limit_size']; //限购数量
				$arr[] = $list;
			}
			return [
				'Catagory' => [
					'Id' => 0,
					'Name' => ''
				],
				'Search' => [
					'Brand' => [
						'Id' => 0,
						'Name' => ''
					],
					'Supplier' => [
						'Id' => 0,
						'Name' => ''
					],
					'KeyWord' => $key,
					'Sort' => $request->body->sort
				],
				'__code' => 'OK',
				'__message' => '',
				'List' => [
					'DataCount' => $re->DataCount,
					'PageSize' => $re->PageSize,
					'PageCount' => $re->PageCount,
					'PageIndex' => $re->PageIndex,
					'List' => $arr
				],
			];
		}
	}

	/**
	 * 最新商品搜索列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		keyword				string		查询关键字  
	 * @request		sort				string		排序方式	x=销量p=价格z=综合(sort字段) x
	 * @request		page				int			第几页(固定每页10个，从0页开始) 0 0 
	 */
	public function spusearch(\Ziima\MVC\REST\Request $request) {

		$sql = "SELECT sku.* ,spu.title as spu_title " .
				" FROM `yuemi_sale`.`spu` as spu " .
				"LEFT JOIN `yuemi_sale`.`catagory` as c ON spu.catagory_id = c.id " .
				"INNER JOIN `yuemi_sale`.`sku` as sku ON spu.id = sku.spu_id "
		;
		$whr = [];

		if (!empty($request->body->keyword)) {
			$whr[] = " spu.`title` LIKE '%" . $this->MySQL->encode($request->body->keyword) . "%'";
			$key = $this->MySQL->encode($request->body->keyword);
		} else {
			$key = '';
		}

		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}

		//最后ORDER BY
		$sql .= ' AND sku.status = 2 AND spu.status = 1 ';
		$sql .= '  group by spu.id  ORDER BY `id` DESC ';

		$re = $this->MySQL->paging($sql, 20, $request->body->page);
		if (empty($re)) {
			$arr = '';
			return [
				'__code' => 'OK',
				'__message' => '',
				'List' => $arr
			];
		} else {
			foreach ($re->Data as $res) {
				$sku_id = $res['id'];
				$list['Id'] = $res['id'];   //SKUID
				$list['Big'] = $this->get_IsBig($res['id']);  //是否是大礼包
				$list['Spu'] = $res['spu_id']; //spuid
				$list['Catagory_id'] = $res['catagory_id']; //分类ID
				$list['Att_newbie'] = $res['att_newbie']; //是否是新人专享
//				if ($res['weight'] > 0) {
//					if ($res['unit'] == "克") {
//						$weight = round($res['weight'], 2);
//					} else {
//						$weight = intval($res['weight']);
//					}
//					$list['Title'] = $res['title']; //标题
//				} else {
//					if ($res['unit'] == "克") {
//						$weight = round($res['weight'], 2);
//					} else {
//						$weight = intval($res['weight']);
//					}
//					$list['Title'] = $res['title']; //标题
//				}

				$list['Title'] = $res['title']; //标题
				if ($this->get_IsBig($res['id']) > 0) {
					$list['Rebate'] = 0; //vip返佣
				} else {
					$list['Rebate'] = $res['rebate_vip']; //vip返佣
				}
				$imgs = [];
				//SKU素材
				if (empty($imgs)) {
					$lis = $this->MySQL->grid("SELECT `thumb_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$sku_id} AND `type` = 0  AND `status` != 2");
					$imgs = array_merge($imgs, $lis);
				}
				//SPU素材
				if (empty($imgs)) {
					$lis = $this->MySQL->grid("SELECT `spu`.`thumb_url` FROM `yuemi_sale`.`spu_material` AS spu " .
							"LEFT JOIN `yuemi_sale`.`sku` AS sku ON sku.spu_id = spu.spu_id " .
							"WHERE `sku`.`id` = {$sku_id} AND `spu`.`type` = 0  AND `spu`.`status` != 2 ");
					$imgs = array_merge($imgs, $lis);
				}

				//ext_sku素材
				if (empty($imgs)) {
					//ext_sku 的ext_sku_id
					$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = {$sku_id}");
					$lis = $this->MySQL->grid(
							"SELECT thumb_url FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = {$ext_sku_id} AND `type` = 0  AND `status` != 2"
					);
					$imgs = array_merge($imgs, $lis);
				}
				//ext_spu素材
				if (empty($imgs)) {
					$spu_id = $res['spu_id'];

					$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = {$spu_id}");

					$lis = $this->MySQL->grid(
							"SELECT thumb_url FROM `yuemi_sale`.`ext_spu_material` WHERE `ext_spu_id` = {$ext_spu_id} AND `type` = 0  AND `status` != 2"
					);
					$imgs = array_merge($imgs, $lis);
				}

				if (!empty($imgs)) {
					$list['Thumnb'] = URL_RES . '/upload' . $imgs[0]['thumb_url'];
				} else {
					$list['Thumnb'] = '';
				}
				$list['Qty_left'] = $res['depot']; //实时库存
				$list['Price']['Sale'] = $res['price_sale']; //售卖价
				//$list['Price']['Ratio'] = $res['price_ratio']; //售卖价
				$list['Price']['Inv'] = $res['price_inv']; //邀请普通会员售价
				$list['Price']['Ref'] = $res['price_ref']; //对标价
				$list['Price']['Market'] = $res['price_market']; //显示零售价
				$list['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
				$list['Coin']['Buyer'] = $res['coin_buyer']; //购买者赠送阅币
				$list['Coin']['Inviter'] = $res['coin_inviter']; //邀请人奖励阅币
				$list['Limit']['Style'] = $res['limit_style']; //限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购
				$list['Limit']['Size'] = $res['limit_size']; //限购数量
				$arr[] = $list;
			}
			return [
				'Catagory' => [
					'Id' => 0,
					'Name' => ''
				],
				'Search' => [
					'Brand' => [
						'Id' => 0,
						'Name' => ''
					],
					'Supplier' => [
						'Id' => 0,
						'Name' => ''
					],
					'KeyWord' => $key,
					'Sort' => $request->body->sort
				],
				'__code' => 'OK',
				'__message' => '',
				'List' => [
					'DataCount' => $re->DataCount,
					'PageSize' => $re->PageSize,
					'PageCount' => $re->PageCount,
					'PageIndex' => $re->PageIndex,
					'List' => $arr
				],
			];
		}
	}
}