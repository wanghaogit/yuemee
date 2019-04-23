<?php
include Z_SITE . "/lib/MobileHandler.php";

/**
 * 商城
 */
class mall_handler extends MobileHandler
{
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
		parent::__init();
	}

	/**
	 * 首页
	 * https://a.yuemee.com/mobile.php?call=mall.index
	 */
	public function index() {
		
	}

	/**
	 * 列表页
	 */
	public function list() {
		
	}

	/**
	 * 商品详情页
	 */
	public function item(int $share_id = 0, string $v = '') 
	{
		if (strlen($v) > 8) {
			$invite_code = substr($v, 0, 8);
			$invite_feed = substr($v, 8);
			$invite_user = \yuemi_main\VipFactory::Instance()->loadOneByInviteCode($invite_code);
		} else {
			$invite_code = $v;
			$invite_feed = '';
			$invite_user = null;
		}
       
		$share = "select *  from `yuemi_sale`.`share` where id={$share_id}";
		$result = $this->MySQL->row($share);


		if (!$share) {
			throw new \Ziima\MVC\REST\Exception('E_SHARE', '无分享');
		}
		$res = $this->MySQL->row(
				"SELECT * FROM `yuemi_sale`.`sku` WHERE id = {$result['sku_id']}"
		);

		// 扫码获取union_id
		$Wechat_sql = "select user_id from `yuemi_main`.`user_wechat` where union_id='{$this->Wechat->union_id}'";
		$Wechat = $this->MySQL->row($Wechat_sql);

		if (empty($Wechat) && $Wechat['user_id'] <= 0) {
			$wechat_userid = 0;
		} else {
			$wechat_userid = $Wechat['user_id'];
		}
		$big = $this->get_IsBig($res['id']);
		$list['Id'] = $res['id'];   //ID
		$list['Spu'] = $res['spu_id']; //spuid
		$list['Catagory_id'] = $res['catagory_id']; //分类ID
		$list['Supplier_id'] = $res['supplier_id']; //供应商ID
		$list['Title'] = $res['title']; //标题
		$list['Barcode'] = $res['barcode']; //条码
		$list['Serial'] = $res['serial']; //货号
		$list['Weight'] = $res['weight']; //重量
		$list['Unit'] = $res['unit']; //单位
		$list['Content'] = str_replace(array('\r\n', 'r', '\n'), "", $res['intro']); //内容
		//$list['Video'] = $res['video']; //视频
		$list['Qty_left'] = $res['depot']; //实时库存
		$list['Rebate'] = $res['rebate_vip']; //vip返佣
		$list['specs'] = $res['specs']; //规格
		$arr['Price']['Sale'] = $res['price_sale']; //售卖价
		$arr['Price']['Inv'] = $res['price_inv']; //有邀请码会员的价格
		//检查VIP表中有没有此用户的记录	price_vips
		if ($this->User !== null && $this->User->level_v > 0) {
			//用VIP价格
			$arr['Price']['Vips'] = $res['price_inv'];
		} else if ($invite_user !== null) {
			//用邀请价格
			$arr['Price']['Vips'] = $res['price_inv'];
		} else {
			//用普通价格
			$arr['Price']['Vips'] = $res['price_inv'];
		}
		$arr['Price']['Ref'] = $res['price_ref']; //对标价
		if($big > 0)
		{
			$arr['Rebate']['Vip'] = 0; //vip返利
		} else {
			$arr['Rebate']['Vip'] = $res['rebate_vip']; //vip返利
		}
		
		$arr['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
		$arr['Coin']['User'] = $res['coin_buyer']; //购买者阅币
		$arr['Coin']['coin_inviter'] = $res['coin_inviter']; //分享者阅币
		if ($this->User !== null && $this->User->level_v > 0) {
			//用VIP阅币
			$arr['Coin']['check_inviter'] = $res['price_sale'];
		} else if ($invite_user !== null) {
			//用邀请阅币
			$arr['Coin']['coin_inviter'] = $res['coin_inviter'];
		} else {
			//用普通阅币
			$arr['Coin']['coin_user'] = $res['coin_buyer'];
		}
		$arr['Price']['Ref'] = $res['price_ref']; //对标价
		$arr['Coin']['Style'] = $res['coin_style']; //赠送阅币方式：0不送，1，按次，2按件
		$arr['Coin']['Buyer'] = $res['coin_buyer']; //用户阅币
		$arr['Coin']['Inviter'] = $res['coin_inviter']; //邀请人奖励阅币
		$arr['Limit']['Style'] = $res['limit_style']; //限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购
		$arr['Limit']['Size'] = $res['limit_size']; //限购数量
		$imgs = [];
		//SKU素材

		$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`sku_material` WHERE sku_id = " . $res['id'] . " AND `type` = 0");

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
			$spuid = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = " . $res['id']);
			$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`spu_material` WHERE spu_id = " . $spuid . " AND `type` = 0");
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
				$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = " . $res['id']);
				$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = " . $ext_sku_id . " AND `type` = 0");

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
					$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = " . $spuid);

					$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = " . $ext_spu_id . " AND `type` = 0");

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
		$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`sku_material` WHERE sku_id = " . $res['id'] . " AND `type` = 1");

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
			$spuid = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = " . $res['id']);

			$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`spu_material` WHERE spu_id = " . $spuid . " AND `type` = 1");

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
				$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = " . $res['id']);

				$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = " . $ext_sku_id . " AND `type` = 1");

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
					$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = " . $ext_spu_id . " AND `type` = 1");
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
		
		// 如果用户信息不全，则设置登录URL
		$RegUrl = null;
		if (!isset($this->User->id)) {
			$RegUrl = "/mobile.php?call=auth.reg&type=1&Parms={$share_id}";
		}
		
		return [
			'RegUrl' => $RegUrl,
			'Item' => $list,
			'Images' => $img,
			'Big' => $big,
			'Pic' => $pic,
			'Attr' => $arr,
			'wechat_userid' => $wechat_userid,
			'share_id' => $share_id
				//'address_id'=>$address_id
		];
	}

	/**
	 * 下单页
	 */
	public function order(int $share_id = 0)
	{	
		$sql="select * from `yuemi_sale`.`share` where id={$share_id} ";
		$data=$this->MySQL->row($sql);

		if(!empty($data))
		{
		  	$sku_id=$data['sku_id'];
		} else {
			//退出
		}
		//sku详情
		$sqls="select * from `yuemi_sale`.`sku` where id={$sku_id}";
		$result=$this->MySQL->row($sqls);
		$imgs = [];
		//SKU素材

		$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`sku_material` WHERE sku_id = " . $result['id'] . " AND `type` = 0");

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
			$spuid = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = " . $result['id']);
			$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`spu_material` WHERE spu_id = " . $spuid . " AND `type` = 0");
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
				$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = " . $result['id']);
				$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = " . $ext_sku_id . " AND `type` = 0");

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
					$ext_spu_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_spu` WHERE spu_id = " . $spuid);

					$shm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = " . $ext_spu_id . " AND `type` = 0");

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
		$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`sku_material` WHERE sku_id = " . $result['id'] . " AND `type` = 1");

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
			$spuid = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = " . $result['id']);

			$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`spu_material` WHERE spu_id = " . $spuid . " AND `type` = 1");

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
				$ext_sku_id = $this->MySQL->scalar("SELECT id FROM `yuemi_sale`.`ext_sku` WHERE sku_id = " . $result['id']);

				$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_sku_material` WHERE ext_sku_id = " . $ext_sku_id . " AND `type` = 1");

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
					$myshm = $this->MySQL->grid("SELECT `type`,`id`,`thumb_size`,`thumb_url`,`file_url`,`file_size` FROM `yuemi_sale`.`ext_spu_material` WHERE ext_spu_id = " . $ext_spu_id . " AND `type` = 1");
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
		//sku素材
		//$sq="select * from  ale`.`sku_material` where sku_id={$result['id']}";
		$thumb_url=$img[0]['Thumb'];

		// 钱包余额与佣金
		$money_sql="select * from `yuemi_main`.`user_finance` where user_id={$this->User->id}";
		$money=$this->MySQL->row($money_sql);

		$qianbao=$money['money'];//钱包余e
		$yongjin=$money['profit_self']+$money['profit_share']+$money['profit_team'];//佣金

		// 收货地址
		$sql3 = "SELECT * FROM `yuemi_main`.`user_address` WHERE `user_id` ={$this->User->id} ORDER BY is_default DESC, id DESC";
		$address = $this->MySQL->row($sql3);
		if (isset($address['id'])) 
		{
			$whr = [];
			$sql = "select u.*,r.province,r.city,r.country  "
					. "from `yuemi_main`.`user_address` AS `u` "
					. "LEFT JOIN `yuemi_main`.`region` AS `r` ON r.id = u.region_id "
					. "WHERE u.`id` = {$address['id']}";
			$data = $this->MySQL->row($sql);
			$this->Context->Response->assign('data', $data);
			$this->Context->Response->assign('res_id', $address['id']);
		} else {
			$this->Context->Response->assign('res_id', 0);
		}

		return[
			'result'=>$result,
			'sku_url'=>$thumb_url,
			'qianbao'=>$qianbao,
			'yongjin'=>$yongjin,
			'share_id'=>$share_id,
			'nonceStr'=> date("YmdHis") . rand(10000000, 99999999)
		];
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
}
