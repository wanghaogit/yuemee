<?php
include Z_SITE . "/lib/MobileHandler.php";

/**
 * 用户中心（个人资料、修改密码、修改收货地址...）
 */
class user_center_handler extends MobileHandler
{
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 首页
	 * http://a.ym.cn/mobile.php?call=user_center.index
	 */
	public function index()
	{

	}


    public function login_mobile(int $share_id=0)
	{
		$union_id=$this->Wechat->union_id;
		return[
			'share_id'=>$share_id,
			'union_id'=>$union_id
		];
	}

	/**
	 * 绑定手机号
	 * http://a.ym.cn/mobile.php?call=user_center.binding_mobile
	 */
	public function binding_mobile(string $code='',string $mobile='',int $share_id=0,string $union_id='')
	{
		$Mobile = trim($mobile);
		$Vcode = trim($code);
		if (!$this->Cacher->sms_vcode($Mobile, $Vcode)) {
			return ['__code' => "E_Vcode", '__message' => '验证码错误'];
		}
		$ret = \yuemi_main\ProcedureInvoker::Instance()->bind_mobile(
				$union_id,
				$mobile,
				$code,
				$this->Context->Runtime->ticket->ip);
		if ($ret === null) {
			return "E_DATABASE";
		}
		if ($ret->ReturnValue != 'OK') {
			echo "<script>alert('$ret->ReturnMessage');</script>";
			//header("/mobile.php?call=runer.bind_mobile");
			header('location:/mobile.php?call=user_center.binding_mobile');
		}
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
	    header('location:/mobile.php?call=user_center.order&share_id=' . $share_id);
	}

	/**
	 * 订单列表
	 */
	public function order(int $share_id=0,int $res_id=0)
	{
		
	}

	/**
	 * 收货地址列表
	 * @param int $share_id
	 */
	public function address()
	{
		return ["ReUrl" => $_SESSION['ReUrl']];
	}

	/**
	 * 设置默认收货地址
	 * @param int $id
	 */
	public function address_set_default(int $id)
	{
		if ($id < 1) {
			$this->MySQL->execute("UPDATE yuemi_main.user_address SET `is_default` = 1 WHERE id = {$id}");
		}
		throw new \Ziima\MVC\Redirector("/mobile.php?call=user_center.address", 301);
	}
	
	/**
	 * 添加收货地址
	 */
	public function address_add(int $share_id=0)
	{
      return[
		  'share_id'=>$share_id
		];
	}

	/**
	 * 修改收货地址
	 */
	public function address_edit(int $id=0,int $share_id=0)
	{
      $sql="SELECT ua.*,r.province,r.city,r.country "
				."FROM `yuemi_main`.`user_address` AS `ua` "
				."LEFT JOIN `yuemi_main`.`region` AS `r` ON r.id = ua.region_id ";
		if ($id > 0) {
			$whr[] = " `ua`.`id` = {$id} ";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr );
		}
		$data=$this->MySQL->row($sql);

        return[
			'data'=>$data,
			'id'=>$id,
			'share_id'=>$share_id
		];
	}

	public function success()
	{

	}

}
