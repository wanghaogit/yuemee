<?php

include_once 'lib/ApiHandler.php';
include_once Z_ROOT . '/Chart.php';
include_once Z_ROOT . '/QR.php';
include_once Z_SITE . '/../../_base/entity/yuemi_sale.php';

/**
 * 用户API接口
 * @auth
 */
class user_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 用户微信登陆
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		unionid		string		微信返回来的union_id
	 * @request		openid		string		微信返回来的open_id
	 * @request		name		string		微信昵称
	 * @request		gender		string		性别
	 * @request		avatar		string		头像
	 *
	 * @response	WechatId	int			user_wechat 表的ID
	 * @response	UserId		int			user 表的id
	 * @response	Token		string		user 表的token
	 *
	 * @noauth
	 */
	public function login_wechat(\Ziima\MVC\REST\Request $request) {
		if (!empty($request->body->name)) {
			$request->body->name = json_encode($request->body->name);
			$request->body->name = preg_replace("/(\\\ud[0-9a-f]{3})|(\\\ue[0-9a-f]{3})/i", "", $request->body->name);
			$request->body->name = json_decode($request->body->name);
		}
		$ret = \yuemi_main\ProcedureInvoker::Instance()->login_wechat(
				$request->body->openid,
				$request->body->unionid,
				$request->body->name,
				$request->body->avatar,
				$request->body->gender,
				0, 0, '',
				$this->Context->Runtime->ticket->ip);
		if ($ret === null) {
			return "E_DATABASE";
		}
		if ($ret->ReturnValue != 'OK') {
			return [
				'__code' => $ret->ReturnValue,
				'__message' => $ret->ReturnMessage
			];
		}
		if (mt_rand(1,100) < 30){
			if ($ret->UserId > 0) {
				$tmp = \yuemi_main\ProcedureInvoker::Instance()->check_user_role($ret->UserId);
				if ($tmp === null || $tmp->ReturnValue !== 'OK') {
					return [
						'__code' => $ret->ReturnValue,
						'__message' => $ret->ReturnMessage
					];
				}
				if ($tmp->LevelUser == 0) {
					return [
						'__code' => 'E_FOBIDDEN',
						'__message' => '此账号被禁止登录'
					];
				}
			}
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'WechatId' => $ret->WechatId,
			'UserId' => $ret->UserId,
			'Token' => $ret->UserToken
		];
	}

	/**
	 * 激活VIP卡
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		serial		string			卡号
	 */
	public function make_card_vip(\Ziima\MVC\REST\Request $request) {
		$serial = preg_replace("/(\s|\&nbsp\;| | |　|　|\xc2\xa0)/","",$request->body->serial);
		$serial = ltrim($serial,'.');
		$Re = \yuemi_main\ProcedureInvoker::Instance()->make_card_vip($this->User->id, $serial, $this->Context->Runtime->ticket->ip);
		if ($Re === null) {
			return "E_DATABASE";
		}
		if ($Re->ReturnValue != 'OK') {
			return [
				'__code' => $Re->ReturnValue,
				'__message' => $Re->ReturnMessage
			];
		}
		return [
			'__code' => $Re->ReturnValue,
			'__message' => $Re->ReturnMessage
		];
	}

	/**
	 * 绑定手机号码
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		mobile		string		手机号码
	 * @request		code		string		短信验证码
	 * @request		unionid		string		微信统一ID
	 *
	 * @response	WechatId		int			user_wechat 表的ID
	 * @response	UserId			int			user 表的id
	 * @response	Token			string		user 表的token
	 *
	 * @noauth
	 */
	public function bind_mobile(\Ziima\MVC\REST\Request $request) 
	{
		$Mobile = trim($request->body->mobile);
		$Vcode = trim($request->body->code);
		if (!$this->Cacher->sms_vcode($Mobile, $Vcode)) {
			return ['__code' => "E_Vcode", '__message' => '验证码错误'];
		}
		// 执行绑定逻辑（存储过程）
		$ret = \yuemi_main\ProcedureInvoker::Instance()->bind_mobile(
				$request->body->unionid,
				$request->body->mobile,
				$request->body->code,
				$this->Context->Runtime->ticket->ip);
		if ($ret === null) {
			return "E_DATABASE";
		}
		if ($ret->ReturnValue != 'OK') {
			return [
				'__code' => $ret->ReturnValue,
				'__message' => '绑定错误'
			];
		}
		if ($ret->UserId > 0) {
			$tmp = \yuemi_main\ProcedureInvoker::Instance()->check_user_role($ret->UserId);
			if ($tmp === null || $tmp->ReturnValue !== 'OK') {
				return [
					'__code' => $ret->ReturnValue,
					'__message' => '权限错误'
				];
			}
			if ($tmp->LevelUser == 0) {
				return [
					'__code' => 'E_FOBIDDEN',
					'__message' => '此账号被禁止登录'
				];
			}
		}

		$Coin = \yuemi_main\ProcedureInvoker::Instance()->coin_income($ret->UserId,  1, 'BIND', '', '绑定手机号奖励', $this->Context->Runtime->ticket->ip);
		if ($Coin->ReturnValue != 'OK'){
			return [
				'__code'	=> 'ERR',
				'__message'	=> '阅币赠送错误'
			];
		}

		return [
			'__code' => 'OK',
			'__message' => '',
			'WechatId' => $ret->WechatId,
			'UserId' => $ret->UserId,
			'Token' => $ret->UserToken
		];
	}

	/**
	 * 手机号码登陆
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		mobile		string		手机号码
	 * @request		code		string		短信验证码
	 *
	 * @response	WechatId		int			user_wechat 表的ID
	 * @response	UserId			int			user 表的id
	 * @response	Token			string		user 表的token
	 *
	 * @noauth
	 */
	public function login_mobile(\Ziima\MVC\REST\Request $request) 
	{
		$Mobile = trim($request->body->mobile);
		$Vcode = trim($request->body->code);
		//if ($Vcode != '9277'){
			if (!$this->Cacher->sms_vcode($Mobile, $Vcode)) {
				return ['__code' => "E_Vcode", '__message' => '验证码错误'];
			}
		//}
		$ret = \yuemi_main\ProcedureInvoker::Instance()->login_mobile(
				$request->body->mobile,
				$request->body->code,
				$this->Context->Runtime->ticket->ip);
		if ($ret === null) {
			return "E_DATABASE";
		}
		if ($ret->ReturnValue != 'OK') {
			return [
				'__code' => $ret->ReturnValue,
				'__message' => $ret->ReturnMessage
			];
		}
		if(mt_rand(1,100) < 30){
			if ($ret->UserId > 0) {
				$tmp = \yuemi_main\ProcedureInvoker::Instance()->check_user_role($ret->UserId);
				if ($tmp === null || $tmp->ReturnValue !== 'OK') {
					return [
						'__code' => $ret->ReturnValue,
						'__message' => $ret->ReturnMessage
					];
				}
				if ($tmp->LevelUser == 0) {
					return [
						'__code' => 'E_FOBIDDEN',
						'__message' => '此账号被禁止登录'
					];
				}
			}
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'WechatId' => $ret->WechatId,
			'UserId' => $ret->UserId,
			'Token' => $ret->UserToken
		];
	}

	/**
	 * 用户退出
	 * @param \Ziima\MVC\REST\Request $request
	 * @noauth
	 */
	public function quit(\Ziima\MVC\REST\Request $request) {
		if ($this->User !== null) {
			$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `token` = '' WHERE `id` = %d", $this->User->id);
		}
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 修改昵称
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		name			string	昵称
	 * @noauth
	 */
	public function updata_name(\Ziima\MVC\REST\Request $request) {

		$name = $request->body->name;
		//TODO:检查昵称规则和敏感词
		if ($this->User !== null) {
			$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `name` = '$name' WHERE `id` = %d", $this->User->id);
		}
		//TODO:删除分享素材
		//1，vip才做以下操作
		if ($this->User->level_v > 0) {
			//查vip表中的邀请码
			$code = $this->MySQL->scalar("SELECT invite_code FROM `yuemi_main`.`vip` WHERE user_id = {$this->User->id}");
			$one = substr($code, 0, 1);
			$two = substr($code, 1, 1);
			$url = UPLOAD_ROOT . '/vip/' . $one . '/' . $two . '/';
			$one_code = $url . $code . '_1.png';
			if (file_exists($one_code)) {
				unlink($one_code);
			}
			$two_code = $url . $code . '_2.png';
			if (file_exists($two_code)) {
				unlink($two_code);
			}
			$three_code = $url . $code . '_3.png';
			if (file_exists($three_code)) {
				unlink($three_code);
			}
		}
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 修改性别
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		gender			tinyint	性别
	 * @noauth
	 */
	public function updata_gender(\Ziima\MVC\REST\Request $request) {
		$gender = $request->body->gender;
		if ($this->User !== null) {
			$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `gender` = '$gender' WHERE `id` = %d", $this->User->id);
		}
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 修改性别
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		birth			date	性别
	 * @noauth
	 */
	public function updata_birthday(\Ziima\MVC\REST\Request $request) {
		$birth = $request->body->birth;
		if ($this->User !== null) {
			$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `birth` = '$birth' WHERE `id` = %d", $this->User->id);
		}
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 拉取用户信息
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 */
	public function info(\Ziima\MVC\REST\Request $request) {
		$ret = \yuemi_main\ProcedureInvoker::Instance()->check_user_role($this->User->id);
		if ($ret === null) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '用户身份出现问题');
		}
		$fina = \yuemi_main\UserFinanceFactory::Instance()->load($this->User->id);
		$vip = $this->MySQL->scalar("SELECT expire_time FROM `yuemi_main`.`vip` WHERE user_id = " . $this->User->id);
		if (empty($vip)) {
			$vip = '';
		}
		if (empty($this->User->avatar)) {
			$avatar = '';
		} else {
			$avatar = URL_RES . '/upload' . $this->User->avatar;
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'Base' => [
				'Id' => $this->User->id,
				'Name' => $this->User->name,
				'Mobile' => $this->User->mobile,
				'Viptime' => $vip,
				'Roles' => [
					'Inviter' => $this->User->invitor_id,
					'User' => $ret->LevelUser,
					'Vip' => $ret->LevelVip,
					'Chief' => $ret->LevelCheif,
					'Director' => $ret->LevelDirector,
					'Team' => $ret->LevelTeam,
					'Admin' => $ret->LevelAdmin,
					'Supplier' => $ret->LevelSupplier
				]
			],
			'Info' => [
				'Avatar' => $avatar,
				'Birth' => $this->User->birth,
				'Gender' => $this->User->gender
			],
			'Finance' => [
				'Money' => $fina->money,
				'Coin' => $fina->coin,
				'Profit' => [
					'Self' => $fina->profit_self,
					'Share' => $fina->profit_share,
					'Team' => $fina->profit_team
				],
				'Recruit' => [
					'Dir' => $fina->recruit_dir,
					'Alt' => $fina->recruit_alt
				],
				'Threw' => [
					'Enable' => $fina->thew_status,
					'Time' => $fina->thew_launch,
					'Target' => $fina->thew_target,
					'Money' => $fina->thew_money,
					'Start' => $fina->thew_start,
					'Expire' => $fina->thew_expire
				],
				'Cheif' => [
					'Status' => $fina->cheif_status,
					'Start' => $fina->cheif_start,
					'Expire' => $fina->cheif_expire,
					'Target_dir' => $fina->cheif_target_dir,
					'Target_alt' => $fina->cheif_target_alt,
					'Value_dir' => $fina->cheif_value_dir,
					'Value_alt' => $fina->cheif_value_alt
				],
				'Director' => [
					'Status' => $fina->director_status,
					'Start' => $fina->director_start,
					'Expire' => $fina->director_expire,
					'Target_team' => $fina->director_target_team,
					'Target_cheif' => $fina->director_target_cheif,
					'Value_team' => $fina->director_value_team,
					'Value_cheif' => $fina->director_value_cheif
				]
			]
		];
	}

	/**
	 * 上传用户头像
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		avatar		string		头像的Base64数据
	 * @request		format		string		头像数据的格式，jpg/png/gif
	 */
	public function avatar(\Ziima\MVC\REST\Request $request) {
		if (empty($request->body->avatar))
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '需要头像数据');
		if (empty($request->body->format))
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '请告诉我头像文件格式');
		if (!in_array($request->body->format, ['jpg', 'png', 'gif']))
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '头像文件格式只能是 jpg/png/gif');
		$bin = base64_decode($request->body->avatar);
		if ($bin === false || $bin === null) {
			throw new \Ziima\MVC\REST\Exception('E_BASE64', '头像数据解码失败');
		}

		$sav = '/avatar/';
		$sid = \Ziima\Zid::Default()->serial();
		$sav .= substr($sid, 0, 2) . '/';
		$sav .= substr($sid, 2, 2) . '/';
		if (!file_exists(UPLOAD_ROOT . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . substr($sid, 0, 2) . DIRECTORY_SEPARATOR . substr($sid, 2, 2))) {
			mkdir(UPLOAD_ROOT . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . substr($sid, 0, 2) . DIRECTORY_SEPARATOR . substr($sid, 2, 2), 0755, true);
		}
		$sav .= $sid . '.' . $request->body->format;
		$this->MySQL->execute(
				"UPDATE `yuemi_main`.`user` SET `avatar` = '%s' WHERE `id` = %d",
				$sav,
				$this->User->id
		);
		$path = UPLOAD_ROOT . $sav;
		$path = str_replace(['\\', '/'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $path);
		$dir = dirname($path);
		if (!file_exists($dir)) {
			mkdir($dir, 0755, true);
		}
		file_put_contents($path, $bin);
		//TODO:删除我的邀请素材
		//1，vip才做以下操作
		if ($this->User->level_v > 0) {
			//查vip表中的邀请码
			$code = $this->MySQL->scalar("SELECT invite_code FROM `yuemi_main`.`vip` WHERE user_id = {$this->User->id}");
			$one = substr($code, 0, 1);
			$two = substr($code, 1, 1);
			$url = UPLOAD_ROOT . '/vip/' . $one . '/' . $two . '/';
			$one_code = $url . $code . '_1.png';
			if (file_exists($one_code)) {
				unlink($one_code);
			}
			$two_code = $url . $code . '_2.png';
			if (file_exists($two_code)) {
				unlink($two_code);
			}
			$three_code = $url . $code . '_3.png';
			if (file_exists($three_code)) {
				unlink($three_code);
			}
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'Avatar' => URL_RES . '/upload' . $sav
		];
	}

	/**
	 * 上传素材
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		material		string		素材的Base64数据
	 * @request		format			string		素材数据的格式，jpg/png/gif
	 * @request		sku_id			int			sku_id
	 */
	public function add_material(\Ziima\MVC\REST\Request $request) {
		if (!in_array($request->body->format, ['jpg', 'png', 'gif']))
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '素材文件格式只能是 jpg/png/gif');

		$bin = base64_decode($request->body->material);
		if ($bin === false || $bin === null) {
			throw new \Ziima\MVC\REST\Exception('E_BASE64', '素材数据解码失败');
		}

		$sav = '/share/'; // /share/ab/cd/abcdefghijk.jpg
		// /share/ab/cd/abcdefghijk-thumb.jpg
		$sid = \Ziima\Zid::Default()->serial();
		$sav .= substr($sid, 0, 2) . '/';
		$sav .= substr($sid, 2, 2) . '/';
		if (!file_exists(UPLOAD_ROOT . DIRECTORY_SEPARATOR . $sav)) {
			mkdir(UPLOAD_ROOT . DIRECTORY_SEPARATOR . $sav, 0755, true);
		}
		$thumb_file = $sav . $sid . '-thumb' . '.' . $request->body->format;
		$sav .= $sid . '.' . $request->body->format;

		$path = UPLOAD_ROOT . $sav;
		$thumb_path = UPLOAD_ROOT . $thumb_file;

		$path = str_replace(['\\', '/'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $path);
		$thumb_path = str_replace(['\\', '/'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $thumb_path);
		$dir = dirname($path);
		if (!file_exists($dir)) {
			mkdir($dir, 0755, true);
		}
		$dirthumb = dirname($thumb_path);
		if (!file_exists($dirthumb)) {
			mkdir($dir, 0755, true);
		}
		file_put_contents($path, $bin);
		file_put_contents($thumb_path, $bin);
		$mat = new yuemi_main\UserMaterialEntity();
		$mat->file_name = '';
		$mat->file_size = filesize($path);
		$mat->url = $sav;
		$mat->thumb_url = $thumb_file;
		$mat->image_width = 800;
		$mat->image_height = 800;
		$mat->user_id = $this->User->id;
		$mat->sku_id = $request->body->sku_id;
		$pic = new \Ziima\Drawing\Picture($path);
		$pic->thumbnail(800, 800);
		$pic->saveAs($path);
		unset($pic);
		$pic = null;

		$pic = new \Ziima\Drawing\Picture($path);
		$pic->thumbnail(320, 320);

		$pic->saveAs($thumb_path);
		unset($pic);
		$pic = null;
		$mat->thumb_width = 320;
		$mat->thumb_height = 320;
		$mat->thumb_size = filesize($thumb_path);
		$mat->audit_time = date('Y-m-d H:i:s');

		$mat->create_from = $this->Context->Runtime->ticket->ip;
		$mat->create_time = date('Y-m-d H:i:s');

		\yuemi_main\UserMaterialFactory::Instance()->insert($mat);

		return [
			'__code' => 'OK',
			'__message' => '',
			'Id' => $mat->id,
			'Picture' => URL_RES . '/upload' . $sav
		];
	}

	/**
	 * 用户素材列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		sku_id		int		SKUID
	 */
	public function material(\Ziima\MVC\REST\Request $request) {
		$row = $this->MySQL->grid(
				"SELECT * FROM `yuemi_main`.`user_material` WHERE `user_id`= %d AND sku_id = %d ",
				$this->User->id,
				$request->body->sku_id
		);

		if (!empty($row)) {
			foreach ($row as $arr) {
				$list['Id'] = $arr['id'];
				$list['Sku_id'] = $arr['sku_id'];
				$list['ThumbSize'] = $arr['thumb_size'];
				$list['Thumb_url'] = URL_RES . '/upload/' . $arr['thumb_url'];
				$res[] = $list;
			}
			return [
				'__code' => 'OK',
				'__message' => '',
				'Material' => $res
			];
		} else {
			return [
				'__code' => 'OK',
				'__message' => '',
				'Material' => ''
			];
		}
	}

	/**
	 * 用户分享
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		shelf_id		int		SKUID
	 * @request		title			string	商品文案
	 * @request		pictures		string	素材ID列表（SKU:1,SPU:2,USER:3)
	 * @request		price_sale		float	一般售卖价
	 * @request		price_ref		float	参考价
	 */
	public function share(\Ziima\MVC\REST\Request $request) {
		//判断用户是否在24小时内分享过，规定每个人24小时内每个商品只能分享一次
//		$time = strtotime('-1 days');
//		$history = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`share` WHERE user_id = {$this->User->id} AND sku_id = {$request->body->shelf_id} AND create_time > {$time}");
//		if(!empty($history))
//		{
//			return [
//				'__code' => 'OK',
//				'__message' => '24小时内只能发一次'
//			];
//		}
		$tpl = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`share_template` WHERE id = 4");

		if ($tpl === null) {
			throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', '没找到模板信息');
		}
		$sav = UPLOAD_ROOT . DIRECTORY_SEPARATOR . 'share';
		$url = '/share';
		$time = time();
		$str = $time . $this->User->id . $request->body->shelf_id;
		if (!file_exists($sav))
			mkdir($sav, 0755, true);
		$sav .= DIRECTORY_SEPARATOR . substr($str, 0, 1);
		$url .= '/' . substr($str, 0, 1);
		if (!file_exists($sav))
			mkdir($sav, 0755, true);
		$sav .= DIRECTORY_SEPARATOR . substr($str, 1, 1);
		$url .= '/' . substr($str, 1, 1);
		if (!file_exists($sav))
			mkdir($sav, 0755, true);
		$sav .= DIRECTORY_SEPARATOR . $str . '_' . $tpl['id'] . '.png';
		$url .= '/' . $str . '_' . $tpl['id'] . '.png';

		$src = UPLOAD_ROOT . $tpl['body_path'];
		$src = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $src);
		if (!file_exists($src)) {
			throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', '没找到模板文件 ' . $tpl['body_path']);
		}
		$pic = new \Ziima\Drawing\Picture($src);


		$images = $request->body->pictures;
		$res = array();
		$cata = array();
		foreach ($images as $arr) {
			switch ($arr->type) {
				case 1:
					//sku
					$img = $this->MySQL->scalar("SELECT thumb_url FROM `yuemi_sale`.`sku_material` where id = {$arr->id}");
					break;
				case 2:
					//spu
					$img = $this->MySQL->scalar("SELECT thumb_url FROM `yuemi_sale`.`spu_material` where id = {$arr->id}");
					break;
				case 3:
					//ext_sku
					$img = $this->MySQL->scalar("SELECT thumb_url FROM `yuemi_sale`.`ext_sku_material` where id = {$arr->id}");
					break;
				case 4:
					//ext_spu
					$img = $this->MySQL->scalar("SELECT thumb_url FROM `yuemi_sale`.`ext_spu_material` where id = {$arr->id}");
					break;
				case 5:
					//用户上传素材
					$img = $this->MySQL->scalar("SELECT thumb_url FROM `yuemi_main`.`user_material` where id = {$arr->id}");
					break;
			}
			$res[] = UPLOAD_ROOT . $img;
			$cata[] = $arr->type;
			$coin[] = $arr->id;
		}

		//画头像处图片
		$avatar = explode(',', $tpl['avatar_config']);
		if (!empty($this->User->avatar)) {
			$avf = UPLOAD_ROOT . $this->User->avatar;
			$avf = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $avf);
			if (!file_exists($avf)) {
				throw new \Ziima\MVC\REST\Exception('E_AVATAR', '头像文件丢失');
			}
			$pic->drawIcon($avf, $avatar[1], $avatar[2], $avatar[3], $avatar[4]);
		}
		//画title
		$title = explode(',', $tpl['title_config']);
		$t = new \Ziima\Drawing\Font('noto', $title['5'], $title['6']);

		//换行
		$length = mb_strlen($request->body->title, 'utf-8'); //计算字符串长度
		$new_str = mb_substr($request->body->title, 0, 16, 'utf-8') . "\r\n" . mb_substr($request->body->title, 17, 50, 'utf-8');
		$pic->drawString($new_str, $title[0], $title[1] + $title[5], $t);



		//画name
		$name = explode(',', $tpl['name_config']);
		$n = new \Ziima\Drawing\Font('noto', $name[3], $name[4], true);
		$pic->drawString($this->User->name, $name[1], $name[2], $n);

		//画参考 price_ref
		$ref = explode(',', $tpl['market_config']);
		$m = new \Ziima\Drawing\Font('noto', $ref[3], $ref[4], true);
		$pic->drawString($request->body->price_ref, $ref[1], $ref[2], $m);

		//画平台售卖价 price_sale
		$sale = explode(',', $tpl['price_config']);
		$s = new \Ziima\Drawing\Font('noto', $sale[3], $sale[4]);
		$pic->drawString($request->body->price_sale, $sale[1], $sale[2], $s);

		//画商品
		if ($tpl['id'] == 4) {
			//画商品主图
			$goods = explode(',', $tpl['material_config']);
			$x = $goods[1];
			$y = $goods[2];
			$w = $goods[3];
			$h = $goods[4];
			$res[0] = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $res[0]);
			$pic->drawIcon($res[0], $x, $y, $w, $h);
			$type[] = $cata[0];
			$mat_id[] = $coin[0];
			$mat_url[] = $res[0];
			$p_order[] = 1;
		} else {
			$goods = explode(',', $tpl['material_config']);
			$x = $goods[1];
			$y = $goods[2];
			$w = $goods[3];
			$h = $goods[4];

			$res[0] = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $res[0]);
			$pic->drawIcon($res[0], $x, $y, $w / 3 - 13, $h / 3 - 13);
			$type[] = $cata[0];
			$mat_id[] = $coin[0];
			$mat_url[] = $res[0];
			$p_order[] = 1;

			$res[1] = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $res[1]);
			$pic->drawIcon($res[1], $x + $w / 3, $y, $w / 3 - 13, $h / 3 - 13);
			$type[] = $cata[1];
			$mat_id[] = $coin[1];
			$mat_url[] = $res[1];
			$p_order[] = 2;

			$res[2] = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $res[2]);
			$pic->drawIcon($res[2], $x + $w * 2 / 3, $y, $w / 3 - 13, $h / 3 - 13);
			$type[] = $cata[2];
			$mat_id[] = $coin[2];
			$mat_url[] = $res[2];
			$p_order[] = 3;

			$res[3] = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $res[3]);
			$pic->drawIcon($res[3], $x, $y + $h / 3 + 13, $w / 3 - 13, $h / 3 - 13);
			$type[] = $cata[3];
			$mat_id[] = $coin[3];
			$mat_url[] = $res[3];
			$p_order[] = 4;

			$res[4] = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $res[4]);
			$pic->drawIcon($res[4], $x + $w * 2 / 3 + 13, $y + $h / 3 + 13, $w / 3 - 13, $h / 3 - 13);
			$type[] = $cata[4];
			$mat_id[] = $coin[4];
			$mat_url[] = $res[4];
			$p_order[] = 5;

			$res[5] = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $res[5]);
			$pic->drawIcon($res[5], $x, $y + $h * 2 / 3 + 26, $w / 3 - 13, $h / 3 - 13);
			$type[] = $cata[5];
			$mat_id[] = $coin[5];
			$mat_url[] = $res[5];
			$p_order[] = 6;
//
			$res[6] = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $res[6]);
			$pic->drawIcon($res[6], $x + $w / 3 + 13, $y + $h * 2 / 3 + 26, $w / 3 - 13, $h / 3 - 13);
			$type[] = $cata[6];
			$mat_id[] = $coin[6];
			$mat_url[] = $res[6];
			$p_order[] = 7;

			$res[7] = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $res[7]);
			$pic->drawIcon($res[7], $x + $w * 2 / 3 + 13, $y + $h * 2 / 3 + 26, $w / 3 - 13, $h / 3 - 13);
			$type[] = $cata[7];
			$mat_id[] = $coin[7];
			$mat_url[] = $res[7];
			$p_order[] = 8;
		}

		//保存数据到share

		$uid = $this->User->id;
		$did = $this->MySQL->scalar("SELECT team.director_id FROM `yuemi_main`.`team` AS team " .
				"LEFT JOIN `yuemi_main`.`team_member` AS tm ON tm.team_id = team.id " .
				"WHERE tm.user_id = " . $this->User->id);
		if (empty($did)) {
			$did = 0;
		}
		$tid = $this->MySQL->scalar("SELECT team_id FROM `team_member` WHERE user_id = " . $this->User->id);
		if (empty($tid)) {
			$tid = 0;
		}
		$mid = $this->MySQL->scalar("SELECT id FROM `team_member` WHERE user_id = " . $this->User->id);
		if (empty($mid)) {
			$mid = 0;
		}
		mt_srand(time());
		$code = (mt_rand());
		$tplid = $tpl['id'];
		$skuid = $request->body->shelf_id;
		$title = $this->MySQL->encode($request->body->title);
		$page_url = '';
		$image_url = URL_RES . '/upload' . $url;
		$create_time = time();
		$create_from = $this->Context->Runtime->ticket->ip;

		$this->MySQL->execute(
				"INSERT INTO `yuemi_sale`.`share`(`user_id`,`director_id`,`team_id`,`member_id`,`share_code`,`template_id`,`sku_id`,`title`,`page_url`,`image_url`,`create_time`,`create_from`) VALUES (%d,%d,%d,%d,%d,%d,%d,'%s','%s','%s',%d,%d)",
				$this->User->id,
				$did, $tid, $mid, $code, $tplid, $skuid, $title, $page_url, $image_url, $create_time, $create_from
		);
		$shareid = $this->MySQL->lastid();
		//保存数据到share_icon
		if ($tpl['id'] != 4) {
			for ($i = 0; $i < 8; $i++) {
				$this->MySQL->execute(
						"INSERT INTO `yuemi_sale`.`share_icon`(`share_id`,`type`,`mat_id`,`mat_url`,`p_order`) VALUES (%d,%d,%d,'%s',%d)",
						$shareid, $type[$i], $mat_id[$i], $res[$i], $p_order[$i]
				);
			}
		} else {
			for ($i = 0; $i < 1; $i++) {
				$this->MySQL->execute(
						"INSERT INTO `yuemi_sale`.`share_icon`(`share_id`,`type`,`mat_id`,`mat_url`,`p_order`) VALUES (%d,%d,%d,'%s',%d)",
						$shareid, $type[$i], $mat_id[$i], $res[$i], $p_order[$i]
				);
			}
		}
		//二维码
		if ($tpl['id'] == 4) {
			//画二维码（一张图）
			$qr = new \Ziima\Drawing\QRencode(QR_ECLEVEL_L, 3, 1);
			$qrp = $qr->buildPicture('https://a.yuemee.com/mobile.php?call=mall.item&share_id=' . $shareid . '&uid=' . $this->User->id);
			if ($qrp === null) {
				throw new \Ziima\MVC\REST\Exception('E_QR', '生成二维码失败');
			}
			$pic->drawCanvas($qrp, 22, 860, 170, 170);
			
			//顺便画时间
			$time = date('Y-m-d H:i:s', time());
			$n = new \Ziima\Drawing\Font('noto', 12, '#aaa', true);
			$pic->drawString($time, $name[1], $name[2] + 30, $n);
			//还有副标题
			$subtitle = $this->MySQL->scalar("SELECT `subtitle` FROM `yuemi_sale`.`sku` WHERE id = {$skuid}"); //1,20,190,22,#999  1,122,72,20,#999
			$s_name = mb_substr($subtitle, 0, 16, 'utf-8');
			$n = new \Ziima\Drawing\Font('noto', 20, '#999', true);
			$titles = explode(',', $tpl['title_config']);
			$pic->drawString($s_name,  $titles[0], $titles[1] + $titles[5] + 40, $n);
		} else {
			//画二维码（九张图）
			$qr = new \Ziima\Drawing\QRencode(QR_ECLEVEL_L, 3, 1);
			$qrp = $qr->buildPicture('https://a.yuemee.com/mobile.php?call=mall.item&share_id=' . $shareid . '&uid=' . $this->User->id);
			if ($qrp === null) {
				throw new \Ziima\MVC\REST\Exception('E_QR', '生成二维码失败');
			}
			$pic->drawCanvas($qrp, $x + $w / 3 + 13, $y + $h / 3 + 13, $w / 3 + 13, $h / 3 + 13);
		}

		//重画，生成图片
		$pic->setFormat(\Ziima\Drawing\Picture::FORMAT_PNG24);
		$pic->saveAs($sav);


		// 分享赠币
		$UserFinance = \yuemi_main\UserFinanceFactory::Instance()->load($this->User->id);
		$CoinOld = $UserFinance->coin;
		$CoinNew = $CoinOld + 0.01;
		$Time = Z_NOW;
		$sql = "INSERT INTO `tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES ({$this->User->id},'SHARE','',{$CoinOld},0.01,{$CoinNew},'分享奖励',{$Time},{$this->Context->Runtime->ticket->ip})";
		$this->MySQL->execute($sql);
		$UserFinance->coin = $CoinNew;
		\yuemi_main\UserFinanceFactory::Instance()->update($UserFinance);
		return [
			'__code' => 'OK',
			'__message' => '',
			'Picture' => URL_RES . '/upload' . $url,
			'shareid' => $shareid
		];
	}

	/**
	 * 用户开通/续费VIP
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function tobe_vip(\Ziima\MVC\REST\Request $request) {
		$uid = $request->body->uid;

		$usermoney = $this->MySQL->row("SELECT `user_id` FROM " .
				"`yuemi_main`.`user_finance` " .
				"WHERE `user_id` = {$uid}");
		if (empty($usermoney['user_id'])) {
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => '用户无充值记录',
				'Order' => ''
			];
		}

		$row = $this->MySQL->row("SELECT * " .
				"FROM `yuemi_main`.`vip` " .
				"WHERE `user_id` = {$uid}");
		$molist = $this->MySQL->row("SELECT `coin` FROM `yuemi_main`.`user_finance` WHERE `user_id` = {$uid}");
		$money = $molist['coin'];
		$newmoney = $money - 1000;
		if ($money < 1000) {
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => '钻石不足',
				'Order' => ''
			];
		}
		if ($row) {
			//有该用户VIP信息
			$expire_time = $row['expire_time'];
			$now = time();

			if ($now < $expire_time) {
				//vip还没到期
				$oldtime = $row['expire_time'];
				$newtime = $oldtime + 31536000;
				$tal = $this->MySQL->row("SELECT `tally_id` FROM `yuemi_main`.`vip_status` ORDER BY `tally_id` DESC");
				$newtally = $tal['tally_id'] + 1;
				//记录流水
				$order = \Ziima\Zid::Default()->order();
				$river = $this->MySQL->execute("INSERT INTO `yuemi_main`.`vip_status` " .
						"(user_id,order_id,tally_id,coin,start_time,expire_time,create_time) " .
						"VALUES('%s','%s','%s','%s','%s','%s','%s')",
						$this->MySQL->encode($uid),
						$this->MySQL->encode($order),
						$this->MySQL->encode($newtally),
						$this->MySQL->encode(1000),
						$this->MySQL->encode(0),
						$this->MySQL->encode(0),
						$this->MySQL->encode(time()));
				//扣钱
				$this->MySQL->execute("UPDATE `yuemi_main`.`user_finance` " .
						"SET `coin` = {$newmoney} " .
						"WHERE `user_id` = {$uid}");
				if ($river) {
					$this->MySQL->execute("UPDATE `yuemi_main`.`vip` " .
							"SET `expire_time` = {$newtime} " .
							"WHERE `user_id` = {$uid}");
				}
				return [
					'__code' => 'OK',
					'__message' => '',
					'Message' => '续费成功',
					'Order' => $order
				];
			} else {
				//vip已经过期
				$tal = $this->MySQL->row("SELECT `tally_id` FROM `yuemi_main`.`vip_status` ORDER BY `tally_id` DESC");
				$newtally = $tal['tally_id'] + 1;
				//记录流水
				$order = \Ziima\Zid::Default()->order();
				$river = $this->MySQL->execute("INSERT INTO `yuemi_main`.`vip_status` " .
						"(user_id,order_id,tally_id,coin,start_time,expire_time,create_time) " .
						"VALUES('%s','%s','%s','%s','%s','%s','%s')",
						$this->MySQL->encode($uid),
						$this->MySQL->encode($order),
						$this->MySQL->encode($newtally),
						$this->MySQL->encode(1000),
						$this->MySQL->encode(0),
						$this->MySQL->encode(0),
						$this->MySQL->encode(time()));

				//扣钱
				$this->MySQL->execute("UPDATE `yuemi_main`.`user_finance` " .
						"SET `coin` = {$newmoney} " .
						"WHERE `user_id` = {$uid}");
				if ($river) {
					$time = time();
					$endtime = strtotime("+1 year");
					$this->MySQL->execute("UPDATE `yuemi_main`.`vip` " .
							"SET `expire_time` = {$endtime},`update_time` = {$time} " .
							"WHERE `user_id` = {$uid}");
					$this->MySQL->execute("UPDATE `yuemi_main`.`user` " .
							"SET `level_v` = 1 " .
							"WHERE `id` = {$uid}");
				}
				return [
					'__code' => 'OK',
					'__message' => '',
					'Message' => '开通成功',
					'Order' => $order
				];
			}
		} else {
			//没有该用户VIP信息
			$row = $this->MySQL->row("SELECT `tally_id` FROM `yuemi_main`.`vip_status` ORDER BY `tally_id` DESC");
			$newtally = $row['tally_id'] + 1;
			//记录流水
			$order = \Ziima\Zid::Default()->order();
			$river = $this->MySQL->execute("INSERT INTO `yuemi_main`.`vip_status` " .
					"(user_id,order_id,tally_id,coin,start_time,expire_time,create_time) " .
					"VALUES('%s','%s','%s','%s','%s','%s','%s')",
					$this->MySQL->encode($uid),
					$this->MySQL->encode($order),
					$this->MySQL->encode($newtally),
					$this->MySQL->encode(1000),
					$this->MySQL->encode(time()),
					$this->MySQL->encode(strtotime("+1 year")),
					$this->MySQL->encode(time()));
			//扣钱
			$this->MySQL->execute("UPDATE `yuemi_main`.`user_finance` " .
					"SET `coin` = {$newmoney} " .
					"WHERE `user_id` = {$uid}");
			//vip增加
			if ($river) {
				$this->MySQL->execute("INSERT INTO `yuemi_main`.`vip`" .
						" (user_id,chief_id,invite_code,status,create_time,update_time,expire_time) " .
						"VALUES('%s','%s','%s','%s','%s','%s','%s')",
						$this->MySQL->encode($uid),
						$this->MySQL->encode(0),
						$this->MySQL->encode(0),
						$this->MySQL->encode(1),
						$this->MySQL->encode(time()),
						$this->MySQL->encode(time()),
						$this->MySQL->encode(strtotime("+1 year")));
				//修改user中vip状态
				$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `level_v` = 1 WHERE `id` = {$uid}");
			}
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => '升级成功',
				'Order' => $order
			];
		}
	}

	/**
	 * 用户开通/续费总监
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function tobe_cheif(\Ziima\MVC\REST\Request $request) {
		$uid = $request->body->uid;
		$viplist = $this->MySQL->row("SELECT * FROM `yuemi_main`.`vip` WHERE `user_id` = {$uid}");
		if ($viplist) {
			$viptime = $viplist['expire_time'];
			if ($viptime < time()) {
				return [
					'__code' => 'OK',
					'__message' => '',
					'Message' => '您的VIP身份已过期',
					'Order' => ''
				];
			}
		} else {
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => '请您先开通VIP身份',
				'Order' => ''
			];
		}

		$cheif = $this->MySQL->row("SELECT * " .
				"FROM `yuemi_main`.`cheif` " .
				"WHERE `user_id` = {$uid}");

		if ($cheif) {
			//有总监记录
			$oldtime = $cheif['expire_time'];
			$now = time();
			$nextyear = strtotime("+1 year");
			if ($now < $oldtime) {
				//总监还没到期
				//改cheif
				$newtime = $oldtime + 31536000;
				$this->MySQL->execute("UPDATE `yuemi_main`.`cheif` " .
						"SET `expire_time` = {$newtime},`update_time` = {$now}" .
						" WHERE `user_id` = {$uid}");
				//插入流水
				$order = \Ziima\Zid::Default()->order();
				$ll = $this->MySQL->row("SELECT `id` " .
						"FROM `yuemi_main`.`cheif` " .
						"WHERE `user_id` = {$uid}");
				$cheif_id = $ll['id'];
				$this->MySQL->execute("INSERT INTO `yuemi_main`.`cheif_status` " .
						"(cheif_id,pay_channel,pay_status,pay_time,order_id,trans_id,money,expire_time,create_time) " .
						"VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
						$this->MySQL->encode($cheif_id),
						$this->MySQL->encode(0),
						$this->MySQL->encode(2),
						$this->MySQL->encode(time()),
						$this->MySQL->encode($order),
						$this->MySQL->encode(123456),
						$this->MySQL->encode(0),
						$this->MySQL->encode($newtime),
						$this->MySQL->encode(time()));
				return [
					'__code' => 'OK',
					'__message' => '',
					'Message' => '总监续费成功',
					'Order' => $order
				];
			} else {
				//总监过期
				//改cheif
				$this->MySQL->execute("UPDATE `yuemi_main`.`cheif` " .
						"SET `expire_time` = {$nextyear} " .
						"WHERE `user_id` = {$uid}");
				$ll = $this->MySQL->row("SELECT `id` " .
						"FROM `yuemi_main`.`cheif` " .
						"WHERE `user_id` = {$uid}");
				$cheif_id = $ll['id'];

				//插入流水
				$order = \Ziima\Zid::Default()->order();
				$this->MySQL->execute("INSERT INTO `yuemi_main`.`cheif_status` " .
						"(cheif_id,pay_channel,pay_status,pay_time,order_id,trans_id,money,expire_time,create_time) " .
						"VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
						$this->MySQL->encode($cheif_id),
						$this->MySQL->encode(0),
						$this->MySQL->encode(2),
						$this->MySQL->encode(time()),
						$this->MySQL->encode(123456),
						$this->MySQL->encode($order),
						$this->MySQL->encode(0),
						$this->MySQL->encode(strtotime("+1 year")),
						$this->MySQL->encode(time()));
				//改user
//				$this->MySQL->execute("UPDATE `yuemi_main`.`user` " .
//						"SET `level_c` = 1 " .
//						"WHERE `id` = {$uid}");
				return [
					'__code' => 'OK',
					'__message' => '',
					'Message' => '总监开通成功',
					'Order' => $order
				];
			}
		} else {
			//没有总监记录
			//插总监表
			$this->MySQL->execute("INSERT INTO `yuemi_main`.`cheif`" .
					" (user_id,director_id,invite_code,status,create_time,expire_time)" .
					" VALUE('%s','%s','%s','%s','%s','%s')",
					$this->MySQL->encode($uid),
					$this->MySQL->encode(0),
					$this->MySQL->encode(0),
					$this->MySQL->encode(0),
					$this->MySQL->encode(time()),
					$this->MySQL->encode(strtotime("+1 year")));
			$getid = $this->MySQL->lastid();
			//插入流水
			$order = \Ziima\Zid::Default()->order();
			$this->MySQL->execute("INSERT INTO `yuemi_main`.`cheif_status` " .
					"(cheif_id,pay_channel,pay_status,pay_time,order_id,trans_id,money,expire_time,create_time) " .
					"VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
					$this->MySQL->encode($getid),
					$this->MySQL->encode(0),
					$this->MySQL->encode(2),
					$this->MySQL->encode(time()),
					$this->MySQL->encode($order),
					$this->MySQL->encode(123456),
					$this->MySQL->encode(0),
					$this->MySQL->encode(strtotime("+1 year")),
					$this->MySQL->encode(time()));
			//修改user表
//			$this->MySQL->execute("UPDATE `yuemi_main`.`user` " .
//					"SET `level_c` = 1 WHERE " .
//					"`id` = {$uid}");
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => '升级总监成功',
				'Order' => $order
			];
		}
	}

	/**
	 * 用户开通/续费总经理
	 * @request		uid		int		用户id
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function tobe_director(\Ziima\MVC\REST\Request $request) {
		$uid = $request->body->uid;
		$viplist = $this->MySQL->row("SELECT * FROM `yuemi_main`.`vip` WHERE `user_id` = {$uid}");
		if ($viplist) {
			$viptime = $viplist['expire_time'];
			if ($viptime < time()) {
				return [
					'__code' => 'OK',
					'__message' => '',
					'Message' => '您的VIP身份已过期',
					'Order' => ''
				];
			}
		} else {
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => '请您先开通VIP身份',
				'Order' => ''
			];
		}
		$row = $this->MySQL->row("SELECT * " .
				"FROM `yuemi_main`.`director` " .
				"WHERE `user_id` = {$uid}");
		if ($row) {
			//有总监记录
			$now = time();
			$oldtime = $row['expire_time'];
			$newtime = $oldtime + 31536000;
			$yeartime = strtotime("+1 year");
			if ($now < $oldtime) {
				//总经理还没过期
				$this->MySQL->execute("UPDATE `yuemi_main`.`director` " .
						"SET `expire_time` = {$newtime} " .
						"WHERE `user_id` = {$uid}");
				$dlist = $this->MySQL->row("SELECT `id` FROM `yuemi_main`.`director` WHERE `user_id` = {$uid}");
				$did = $dlist['id'];
				//插流水
				$order = \Ziima\Zid::Default()->order();
				$this->MySQL->execute("INSERT INTO `yuemi_main`.`director_status` " .
						"(director_id,pay_channel,pay_status,pay_time,order_id,trans_id,money,expire_time,create_time) " .
						"VALUE('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
						$this->MySQL->encode($did),
						$this->MySQL->encode(0),
						$this->MySQL->encode(2),
						$this->MySQL->encode(time()),
						$this->MySQL->encode($order),
						$this->MySQL->encode(123456),
						$this->MySQL->encode(0),
						$this->MySQL->encode($newtime),
						$this->MySQL->encode(time()));
				return [
					'__code' => 'OK',
					'__message' => '',
					'Message' => '续费总经理成功',
					'Order' => $order
				];
			} else {
				//总经理过期了
				$this->MySQL->execute("UPDATE `yuemi_main`.`director` " .
						"SET `expire_time` = {$yeartime} " .
						"WHERE `user_id` = {$uid}");
				$dlist = $this->MySQL->row("SELECT `id` FROM `yuemi_main`.`director` WHERE `user_id` = {$uid}");
				$did = $dlist['id'];
				//插流水
				$order = \Ziima\Zid::Default()->order();
				$this->MySQL->execute("INSERT INTO `yuemi_main`.`director_status` " .
						"(director_id,pay_channel,pay_status,pay_time,order_id,trans_id,money,expire_time,create_time) " .
						"VALUE('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
						$this->MySQL->encode($did),
						$this->MySQL->encode(0),
						$this->MySQL->encode(2),
						$this->MySQL->encode(time()),
						$this->MySQL->encode($order),
						$this->MySQL->encode(123456),
						$this->MySQL->encode(0),
						$this->MySQL->encode(strtotime("+1 year")),
						$this->MySQL->encode(time()));
				//改user
//				$this->MySQL->execute("UPDATE `yuemi_main`.`user` " .
//						"SET `level_d` = 1 " .
//						"WHERE `id` = {$uid}");
				return [
					'__code' => 'OK',
					'__message' => '',
					'Message' => '开通总经理成功',
					'Order' => $order
				];
			}
		} else {
			//没有总监记录
			//插总经理表
			$river = $this->MySQL->execute("INSERT INTO `yuemi_main`.`director` " .
					"(user_id,create_time,invite_code,status,create_from,expire_time) " .
					"VALUES('%s','%s','%s','%s','%s','%s')",
					$this->MySQL->encode($uid),
					$this->MySQL->encode(time()),
					$this->MySQL->encode(0),
					$this->MySQL->encode(0),
					$this->MySQL->encode(0),
					$this->MySQL->encode(strtotime("+1 year")));
			$getid = $this->MySQL->lastid();

			//插流水
			$order = \Ziima\Zid::Default()->order();
			$this->MySQL->execute("INSERT INTO `yuemi_main`.`director_status` " .
					"(director_id,pay_channel,pay_status,pay_time,order_id,trans_id,money,expire_time,create_time) " .
					"VALUE('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
					$this->MySQL->encode($getid),
					$this->MySQL->encode(0),
					$this->MySQL->encode(2),
					$this->MySQL->encode(time()),
					$this->MySQL->encode($order),
					$this->MySQL->encode(123456),
					$this->MySQL->encode(0),
					$this->MySQL->encode(strtotime("+1 year")),
					$this->MySQL->encode(time()));
			//改user
//			$this->MySQL->execute("UPDATE `yuemi_main`.`user` " .
//					"SET `level_d` = 1 " .
//					"WHERE `id` = {$uid}");
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => '升级总经理成功',
				'Order' => $order
			];
		}
	}

	/**
	 * VIP激活（通过卡号）
	 * @param \Ziima\MVC\REST\Request $request
	 * @request	card_num	string	卡号
	 */
	public function activate_card_num(\Ziima\MVC\REST\Request $request) {
		// $CardNum = $request->body->card_num;
		return ['__code' => 'OK', '__message' => ''];
	}

	/**
	 * 我的素材
	 * @param \Ziima\MVC\REST\Request $request
	 * @request			catagory		int		分类ID
	 * @request			title			string	标题
	 * @request			page			int		分页
	 *
	 */
	public function myshare(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id;
		$sql = "SELECT s.id AS id, sku.title AS Sku_name, s.title AS Title, s.create_time AS Time FROM `yuemi_sale`.`share` AS s " .
				"LEFT JOIN `yuemi_sale`.`sku` AS sku ON s.sku_id = sku.id " //sku		商品表
		;
		$whr = [];

		if (!empty($request->body->title)) {
			$whr[] = "s.`title` like  '%" . $this->MySQL->encode($request->body->title) . "%'";
		}

		if ($request->body->catagory > 0) {
			$whr[] = "sku.`catagory_id` = '" . $request->body->catagory . "'";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$sql .= ' ORDER BY s.`id` DESC ';
		$result = $this->MySQL->paging($sql, 20, $request->body->page);
		if (!empty($result)) {
			foreach ($result->Data as $key => $res) {
				$list['Title'] = $res['Title']; //标题
				$list['Sku_name'] = $res['Sku_name']; //商品标题
				$list['Time'] = $res['Time']; //时间
				$list['Id'] = $res['id']; //ID
				$ur = URL_RES;
				$shm = $this->MySQL->grid("SELECT concat('$ur',SUBSTRING(mat_url,10)) AS Url,id FROM `yuemi_sale`.`share_icon` WHERE share_id = " . $res['id']);
				$list['Material'] = $shm;
				$arr[] = $list;
			}


			return [
				'__code' => 'OK',
				'__message' => '',
				'List' => $arr
			];
		} else {
			return [
				'__code' => 'OK',
				'__message' => '',
				'List' => ''
			];
		}
	}

	/**
	 * 移动图片
	 *  @param \Ziima\MVC\REST\Request $request
	 * @request		url			string			新图片路径
	 * @request		id			int				模板ID
	 */
	public function copy(\Ziima\MVC\REST\Request $request) {

		$file = $request->body->url; //上传图片
		header("Content-Type: text/html; charset:UTF-8");
		//获得模板原路径
		$old = $this->MySQL->scalar("SELECT `body_url` FROM `yuemi_main`.`invite_template` WHERE id = {$request->body->id}");

		//处理URL得到新模板路径
		$a = UPLOAD_ROOT;

		$old_url = $a . $old;  //放图片的模板路径

		$old_path = substr($old_url, 0, -11);   //目标 路径
		//获得新图片文件并且重命名
		$new_path = substr($file, strlen(URL_RES) + 7);

		//去掉-thumb
		$b = substr($new_path, 0, -10);

		$new_urls = '/data/nfs/upload' . $b . '.jpg';

		//copy到新路径
		copy($new_urls, $old_url);

		//修改数据库
		//路径处理好
		$u = $b . '.jpg';
		$this->MySQL->execute(
				"UPDATE `yuemi_main`.`invite_template` SET `body_url` = '{$u}',`body_path` = '{$u}' WHERE id = {$request->body->id}"
		);
		return [
			'__code' => 'OK',
			'__message' => '',
		];
	}

//	public function img(\Ziima\MVC\REST\Request $request) {
//
//		$this->MySQL->execute("UPDATE `yuemi_sale`.`share_template` SET `body_path` = '/template/share/share_9.png', `body_url` = '/template/share/share_9.png',`body_width` = 750,`body_height` = 1078 WHERE id = 3");
//		return [
//			'__code' => 'OK',
//			'__message' => '',
//		];
//	}

	/**
	 * 银行列表
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function bank(\Ziima\MVC\REST\Request $request) {
		$bankAll1 = [
			[
				"id" => "4",
				"type" => "2",
				"text" => "中国工商银行"
			],
			[
				"id" => "5",
				"type" => "2",
				"text" => "中国农业银行"
			],
			[
				"id" => "6",
				"type" => "2",
				"text" => "中国银行"
			],
			[
				"id" => "7",
				"type" => "2",
				"text" => "中国建设银行"
			],
			[
				"id" => "8",
				"type" => "2",
				"text" => "交通银行"
			],
			[
				"id" => "9",
				"type" => "3",
				"text" => "中信银行"
			],
			[
				"id" => "10",
				"type" => "3",
				"text" => "中国光大银行"
			],
			[
				"id" => "11",
				"type" => "3",
				"text" => "华夏银行"
			],
			[
				"id" => "12",
				"type" => "3",
				"text" => "中国民生银行"
			],
			[
				"id" => "13",
				"type" => "3",
				"text" => "招商银行"
			],
			[
				"id" => "14",
				"type" => "3",
				"text" => "兴业银行"
			],
			[
				"id" => "17",
				"type" => "3",
				"text" => "上海浦东发展银行"
			],
			[
				"id" => "21",
				"type" => "4",
				"text" => "中国邮政储蓄银行"
			],
			[
				"id" => "9999",
				"type" => "2",
				"text" => "支付宝"
			]
		];
		return ['Bank' => $bankAll1];
	}

	/**
	 * 推广注册接口
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		mobile		string			手机号
	 */
	public function spread(\Ziima\MVC\REST\Request $request) {
		$Re = \yuemi_main\ProcedureInvoker::Instance()->login_mobile($request->body->mobile, '1342', $this->Context->Runtime->ticket->ip);
		return[
			'__code'	=> $Re->ReturnValue,
			'__message' => $Re->ReturnMessage,
			'UserId'	=> $Re->UserId,
			'Token'		=> $Re->UserToken
		];
	}
	
	/**
	 * 推广权限检测
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		sku_id		int			推广商品
	 * @request		region_id	int			地区ID：没有请传入0
	 * @request		address		string		商品邮寄详细地址：当没有请传入空字符串
	 * @request		mobile		string		收货手机号：当没有请传入空字符串
	 */
	public function spread_power(\Ziima\MVC\REST\Request $request) {
		$Sku = \yuemi_sale\SkuFactory::Instance()->load($request->body->sku_id);
		$IsSpread = $Sku->att_newbie;
		if ($IsSpread == 0){
			return ['__code'=>'OK','Isnew'=> 1];
		}
		//判断是否为新用户(24小时内注册)
		$User = \yuemi_main\UserFactory::Instance()->load($this->User->id);
		$RegTime = $User->reg_time;
		if (($RegTime + 86400 ) < Z_NOW){
			return [ '__code'	=> 'E_USER', '__message'	=> '本次活动仅限24小时内注册的新用户购买，感谢关注!','Isnew'=> 0];
		}
		//判断是否为有过购买行为
		$Sql2 = "SELECT COUNT(*) FROM `yuemi_sale`.`order` WHERE `user_id` = {$this->User->id} AND `status` IN (1,2,4,5,6,7) ";
		$Count1 = $this->MySQL->scalar($Sql2);
		if ($Count1 > 0 ) {
			return [ '__code'	=> 'E_QTY', '__message'	=> '本次活动仅限阅米新用户购买，感谢关注!','Isnew'=> 0 ];
		}
		//判断用户收货手机
		$Sql1 = "SELECT COUNT(*) FROM `yuemi_sale`.`order` WHERE `addr_mobile` = '{$User->mobile}' AND `status` IN (1,2,4,5,6,7)";
		if ($this->MySQL->scalar($Sql1) > 0 ){
			return [ '__code'	=> 'E_MOBILE', '__message'	=> '本次活动每个手机号码限购一次，感谢关注!','Isnew'=> 0 ];
		}
		return ['__code'=>'OK'];
	}
	
	/**
	 * 判断是否是新手用户
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function new_person(\Ziima\MVC\REST\Request $request){
		$uid = $this->User->id;
		$row = $this->MySQL->row("SELECT `reg_time` FROM `yuemi_main`.`user` WHERE `id` = {$uid}");
		$diff = time() - $row['reg_time'];
		if($diff > 86400){
			//大于24小时了
			$new = 0;
		}else{
			//小于24小时
			//是否买过商品
			$lis = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`order` WHERE `user_id` = {$uid}");
			if(!empty($lis['id'])){
				//买过
				$new = 0;
			}else{
				$new = 1;
			}
		}
		return [
			'__code'	=> 'OK',
			'__message'	=> '',
			'Isnew' => $new
		];
		
	}
}
