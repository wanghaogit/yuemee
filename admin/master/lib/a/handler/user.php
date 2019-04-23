<?php

include "lib/ApiHandler.php";

/**
 * 用户管理接口
 */
class user_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 保存模板布局
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id				int		模板ID
	 * @request		name_enable		int		是否显示昵称
	 * @request		name_x			int		昵称显示位置X
	 * @request		name_y			int		昵称显示位置Y
	 * @request		name_size		int		昵称显示字号
	 * @request		name_color		string	昵称显示颜色
	 * @request		code_enable		int		是否显示邀请码
	 * @request		code_x			int		邀请码显示位置X
	 * @request		code_y			int		邀请码显示位置Y
	 * @request		code_size		int		邀请码显示字号
	 * @request		code_color		string	邀请码显示颜色
	 * @request		avatar_enable	int		是否显示头像
	 * @request		avatar_x		int		头像显示位置X
	 * @request		avatar_y		int		头像显示位置Y
	 * @request		avatar_w		int		头像显示宽度
	 * @request		avatar_h		int		头像显示高度
	 * @request		qr_x			int		二维码显示位置X
	 * @request		qr_y			int		二维码显示位置Y
	 * @request		qr_w			int		二维码显示宽度
	 * @request		qr_h			int		二维码显示高度
	 *
	 */
	public function layout_save(\Ziima\MVC\REST\Request $request) {
		$tpl = \yuemi_main\InviteTemplateFactory::Instance()->load($request->body->id);
		if ($tpl === null) {
			throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', '没找到指定ID的模板');
		}
		$tpl->name_enable = $request->body->name_enable;
		$tpl->name_x = $request->body->name_x;
		$tpl->name_y = $request->body->name_y;
		$tpl->name_size = $request->body->name_size;
		$tpl->name_color = $request->body->name_color;
		$tpl->code_enable = $request->body->name_enable;
		$tpl->code_x = $request->body->code_x;
		$tpl->code_y = $request->body->code_y;
		$tpl->code_size = $request->body->code_size;
		$tpl->code_color = $request->body->code_color;
		$tpl->avatar_enable = $request->body->avatar_enable;
		$tpl->avatar_x = $request->body->avatar_x;
		$tpl->avatar_y = $request->body->avatar_y;
		$tpl->avatar_w = $request->body->avatar_w;
		$tpl->avatar_h = $request->body->avatar_h;
		$tpl->qr_x = $request->body->qr_x;
		$tpl->qr_y = $request->body->qr_y;
		$tpl->qr_w = $request->body->qr_w;
		$tpl->qr_h = $request->body->qr_h;
		if (!\yuemi_main\InviteTemplateFactory::Instance()->update($tpl)) {
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '保存模板失败');
		}
		return "OK";
	}

	/**
	 * 允许用户登录
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		用户ID
	 */
	public function user_enable(\Ziima\MVC\REST\Request $request) {
		$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `level_u` = 1 WHERE `id` = %d", $request->body->id);
		return 'OK';
	}

	/**
	 * 禁止用户登录
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		用户ID
	 */
	public function user_disable(\Ziima\MVC\REST\Request $request) {
		if ($request->body->id <= 1) {
			throw new \Ziima\MVC\REST\Exception('E_INTERNAL', '系统初始用户不可禁用');
		}
		$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `level_u` = 0 WHERE `id` = %d", $request->body->id);
		return 'OK';
	}

	/**
	 * 认证 - 审核 - 身份证 - 通过
	 * @param \Ziima\MVC\REST\Request $request
	 * @param	userid	int		用户Id
	 * @param	status	int		认证状态 0=草稿,1=待审,2=通过,3=拒绝
	 */
	public function cert_check_id_pass(\Ziima\MVC\REST\Request $request) {
		$userid = $request->body->userid;
		$status = $request->body->status;
		$this->MySQL->execute("UPDATE user_cert SET `status` = '{$status}' WHERE `user_id` = '{$userid}'");
		return "OK";
	}

	/**
	 * 城市级联2级
	 * by wanghao 2018/4/6
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    tt    char     省份id
	 * @slient
	 */
	public function user_cityInfo(\Ziima\MVC\REST\Request $request) {
		$t = $request->body->tt;
		$sql = "SELECT * FROM `yuemi_main`.`region` WHERE `id` LIKE '" . substr($t, 0, 2) . "__00' AND `id` NOT LIKE '__0000'";
		//固定调用 paging
		$result = $this->MySQL->grid($sql);
		return [
			'__code' => 'OK',
			'__arr' => $result
		];
	}

	/**
	 * 城市级联3级
	 * by wanghao 2018/4/6
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    tt    char     城市id
	 * @slient
	 */
	public function user_cityInfo2(\Ziima\MVC\REST\Request $request) {
		$t = $request->body->tt;
		$sql = "SELECT * FROM `yuemi_main`.`region` WHERE `id` LIKE '" . substr($t, 0, 4) . "__' AND `id` NOT LIKE '____00'";
		//固定调用 paging
		$result = $this->MySQL->grid($sql);
		return [
			'__code' => 'OK',
			'__arr' => $result
		];
	}

	/**
	 * 强制踢人（退出登陆）
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     int     用户user_id
	 * @slient
	 */
	public function kick(\Ziima\MVC\REST\Request $request) {
		if ($request->body->id == $this->User->id) {
			throw new \Ziima\MVC\REST\Exception('E_HELPER', '别踢你自己');
		}
		$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `token` = '' WHERE `id` = %d", $request->body->id);
		return "OK";
	}

	/**
	 * 强制修改昵称
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     int		用户user_id
	 * @request    name   string     用户新昵称
	 * @slient
	 */
	public function rename(\Ziima\MVC\REST\Request $request) {
		$u = \yuemi_main\UserFactory::Instance()->load($request->body->id);
		if ($u === null) {
			throw new \Ziima\MVC\REST\Exception('E_USER', '用户不存在');
		}
		$request->body->name = trim($request->body->name);
		$request->body->name = preg_replace('/\s+/', '', $request->body->name);
		if (mb_strlen($request->body->name) <= 2) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '昵称太短');
		}
		if (mb_strlen($request->body->name) > 32) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '昵称太长');
		}
		foreach (['\'', '"', '\\', '\/', '\0', "\n", "\r", "\t"] as $c) {
			if (mb_strpos($request->body->name, $c) !== false) {
				throw new \Ziima\MVC\REST\Exception('E_PARAM', '昵称中有不合适的字符');
			}
		}
		if (!$this->MySQL->execute("UPDATE `user` SET `name` = '%s' WHERE `id` = %d", $this->MySQL->encode($request->body->name), $u->id)) {
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '数据库错误');
		}
		return 'OK';
	}

	/**
	 * 设置微信手机号
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    wxid		  int		 微信ID
	 * @request    mobile     string     手机号码
	 * @slient
	 */
	public function set_wechat_mobile(\Ziima\MVC\REST\Request $request) {
		if (!preg_match('/^1\d{10}$/', $request->body->mobile)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '手机号码格式错误');
		}
		$t = $this->MySQL->row("SELECT * FROM `yuemi_main`.`user_wechat` WHERE `id` != %d AND `mobile` = '%s'",
				$request->body->mobile,
				$request->body->wxid);
		if ($t) {
			throw new \Ziima\MVC\REST\Exception('E_MOBILE', '此手机号码已被登记，请确认。');
		}
		$this->MySQL->execute("UPDATE `yuemi_main`.`user_wechat` SET `mobile` = '%s' WHERE `id` = %d",
				$request->body->mobile,
				$request->body->wxid);
		return "OK";
	}

	/**
	 * 设置微信账号
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    wxid		  int		 微信ID
	 * @request    account     string     手机号码
	 * @slient
	 */
	public function set_wechat_account(\Ziima\MVC\REST\Request $request) {
		if (!preg_match('/^[a-z09-\_]{5,64}$/i', $request->body->account)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '微信账号格式错误');
		}
		$t = $this->MySQL->row("SELECT * FROM `yuemi_main`.`user_wechat` WHERE `id` != %d AND `account` = '%s'",
				$request->body->account,
				$request->body->wxid);
		if ($t) {
			throw new \Ziima\MVC\REST\Exception('E_MOBILE', '此微信账号已被登记，请确认。');
		}
		$this->MySQL->execute("UPDATE `yuemi_main`.`user_wechat` SET `account` = '%s' WHERE `id` = %d",
				$request->body->account,
				$request->body->wxid);
		return "OK";
	}

	/**
	 * 获取权限select
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function get_role(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$list = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`rbac_role` WHERE `parent_id` = {$id}");
		return [
			'__code' => 'OK',
			'__arr' => $list
		];
	}
	
	/**
	 * 获取用户的基本信息
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    user_id     int     用户ID
	 */
	public function reissue_info(\Ziima\MVC\REST\Request $request){
		//地址
		$User = \yuemi_main\UserFactory::Instance()->load($request->body->user_id);
		$address = $this->MySQL->row("SELECT * FROM `yuemi_main`.`user_address` WHERE `user_id` = {$request->body->user_id} AND `is_default` = 1");
		if (!$address){
			return [
				'id' => 0,
				'info' => '',
				'mobile' => $User->mobile,
				'name'	 => $User->name
			];
		}
		$region_id = $address['region_id'];
		$r1 = substr($region_id, 0, 2)*10000;
		$r2 = substr($region_id, 0, 4)*100;
		$province = $this->MySQL->scalar("SELECT `province` FROM `yuemi_main`.`region` WHERE `id` = {$r1}");
		$city = $this->MySQL->scalar("SELECT `city` FROM `yuemi_main`.`region` WHERE `id` = {$r2}");
		$county = $this->MySQL->scalar("SELECT `county` FROM `yuemi_main`.`region` WHERE `id` = {$region_id}");
		return[
			'id' => $address['id'],
			'info' => $province.$city.$county.$address['address'],
			'mobile' => $User->mobile,
			'name'	 => $User->name
		];
	}
	/**
	 * 生成ORDERID
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function order_id(\Ziima\MVC\REST\Request $request){
		return ['id'=>	\Ziima\Zid::Default()->order('K', 'C')];
	}

	/**
	 * 生成ORDERID
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    order_id			string  订单ID
	 * @request    sku_id			int     商品ID
	 * @request    old_region_id   int     原来的收货地址ID
	 * @request    region_id		int     新增地区ID
	 * @request    address			string  新增详细地区
	 * @request    user_id			int     用户ID
	 * @request    name				string  收货姓名
	 * @request    mobile			string  收货手机号
	 */
	public function reissue(\Ziima\MVC\REST\Request $request){
		if($request->body->region_id !== 0){
			$Address = new \yuemi_main\UserAddressEntity();
			$Address->address = $request->body->address;
			$Address->contacts =  $request->body->name;
			$Address->create_from = $this->Context->Runtime->ticket->ip;
			$Address->create_time = Z_NOW;
			$Address->is_default = 0;
			$Address->mobile =  $request->body->mobile;
			$Address->region_id = $request->body->region_id;
			$Address->status = 1;
			$Address->user_id = $request->body->user_id;
			\yuemi_main\UserAddressFactory::Instance()->insert($Address);
			$address_id = $Address->id;
		}  else {
			$address_id = $request->body->old_region_id;
		}
		$str = '补单人：'.$this->User->name.'&nbsp;&nbsp;补单原因：'.$request->body->why;
		$Re = \yuemi_sale\ProcedureInvoker::Instance()->vip_order($request->body->user_id, $request->body->sku_id, $request->body->order_id, $address_id,$str, $this->Context->Runtime->ticket->ip);
		if ($Re->ReturnValue == 'OK'){
			return ['id'=>	$request->body->order_id];
		} else {
			return ['__code'=>$Re->ReturnValue,'__message'=>$Re->ReturnMessage];
		}
	}
	
	
	
	
}
