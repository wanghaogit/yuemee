<?php

include "lib/ApiHandler.php";

/**
 * 系统管理接口
 */
class system_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 修改银行图标
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id		int			银行id
	 * @request    icon		string		银行图标的Base64
	 * @slient
	 */
	public function bank_logo(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$icon = $request->body->icon;
		$sql = "UPDATE `yuemi_main`.`bank` SET  icon = '" . $icon . "'  WHERE  id='" . $id . "'";
		if ($this->MySQL->execute($sql)) {
			return [
				'__code' => 'OK',
				'__message' => '更新失败'
			];
		}
	}
	/**
	 * 获取短息验证码
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    mobile		string			银行id
	 */
	public function get_code(\Ziima\MVC\REST\Request $request){
		$this->Redis->select(7);
		$data = $this->Redis->get($request->body->mobile);
		if (!$data){
			return [
				'__code'	=> 'ERROR',
				'__message'	=> '',
				'data'		=> ''			
			];
		}
		$newdata = [];
		$data = json_decode($data);
		foreach ($data as $key=>$val){
			$newdata[$key]['time'] = date('Y-m-d H:i:s',$val->time);
			$newdata[$key]['vcode'] = $val->vcode;
		}
		return [
			'__code'	=> 'OK',
			'__message'	=> '',
			'data'		=> $newdata		
		];
	}
	
	/**
	 * 重置Redis缓存
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		db		int			数据库ID
	 * @request		key		string		RedisKey
	 */
	public function redis_reset(\Ziima\MVC\REST\Request $request) {
		$this->Redis->select($request->body->db);
		if ($this->Redis->exists($request->body->key)) {
			$this->Redis->delete($request->body->key);
			if ($this->Redis->exists($request->body->key)) {
				return 'E_DELETE';
			} else {
				return 'OK';
			}
		} else {
			return 'E_NOKEY';
		}
	}

	/**
	 * 获取要删除的用户信息
	 * @param \Ziima\MVC\REST\Request $request
	 * @request	mobile		string	手机号
	 * @request	unionid		string	微信union_id
	 */
	public function deluser_info(\Ziima\MVC\REST\Request $request)
	{
		$data = null;
		$Uinfo = null;
		$Winfo = null;
		
		// 校验参数
		if (empty($request->body->unionid) && empty($request->body->mobile)) {
			return ['__code' => 'OK', '__message' => '', 'data' => "请输入搜索条件"];
		}
		$request->body->mobile = trim($request->body->mobile);
		$request->body->unionid = trim($request->body->unionid);
		
		// 传入的是微信UnionId
		if (!empty($request->body->unionid))
		{
			$Winfo = \yuemi_main\UserWechatFactory::Instance()->loadByUnionId($request->body->unionid);
			if (!isset($Winfo->user_id)) {
				return ['__code'=>'OK', '__message'=>'', 'data' => "微信UnionId (<b style='color:blue'>{$request->body->unionid}</b>) 查询不到相关信息"];
			}
			$Uinfo = \yuemi_main\UserFactory::Instance()->loadOneByMobile($Winfo->user_id);
		}
		// 默认认为是手机号
		else
		{
			$Uinfo = \yuemi_main\UserFactory::Instance()->loadOneByMobile($request->body->mobile);
			if (!isset($Uinfo->id)) {
				return ['__code'=>'OK', '__message'=>'', 'data' => "手机号 <b style='color:blue'>{$request->body->mobile}</b> 查询不到相关信息"];
			}
			$Winfo = \yuemi_main\UserWechatFactory::Instance()->loadOneByUserId($Uinfo->id);
		}
		
		// 用户订单
		if (!empty($Uinfo)) 
		{
			$OrderList = \yuemi_sale\OrderFactory::Instance()->loadAllByUserId($Uinfo->id);
			if (is_array($OrderList)) {
				$data .= "<B style='color:red'>用户订单列表（" . count($OrderList) . "）：</B><br />";
				foreach ($OrderList AS $OrderInfo) {
					$data .= "{$OrderInfo->id} / ";
				}
				$data .= "<br />";
			}
		}

		// 组合返回的数据
		if (!empty($Winfo)) {
			$data .= "<B style='color:red'>用户 user_wechat 表信息：</B><br />";
			foreach ($Winfo AS $key => $val) {
				$data .= "{$key}：{$val} <br />";
			}
		}
		if (!empty($Uinfo)) {
			$data .= "<B style='color:red'>用户 user 表信息：</B><br />";
			foreach ($Uinfo AS $key => $val) {
				$data .= "{$key}：{$val} <br />";
			}
		}

		return ['__code' => 'OK', '__message' => '', 'data' => $data];
	}
	
	
	
	

}
