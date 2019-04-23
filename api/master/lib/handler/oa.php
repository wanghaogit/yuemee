<?php

include_once 'lib/ApiHandler.php';

/**
 * OA接口
 */
class oa_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 假登陆
	 * @param \Ziima\MVC\REST\Request $request
	 * @return type
	 */
	public function login_xx(\Ziima\MVC\REST\Request $request) {
		return [
			'__code' => 'OK',
			'__message' => '',
			'WechatId' => 1,
			'UserId' => 1,
			'Token' => $this->MySQL->scalar("SELECT `token` FROM yuemi_main.`user` WHERE `id` = 1"),
			'Cheif' => [
				'Level' => 1,
				'Id' => 1
			],
			'Director' => [
				'Level' => 0,
				'Id' => 0
			]
		];
	}

	/**
	 * 用户登陆
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		name			char			用户名
	 * @request		openid			char			openid
	 * @request		unionid			char			开放平台
	 * @request		avatar			char			头像url
	 * @request		gender			int			性别 0未知 1男 2女
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
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '数据库错误');
		}
		if ($ret->ReturnValue != 'OK') {
			throw new \Ziima\MVC\REST\Exception($ret->ReturnValue, $ret->ReturnMessage);
		}
		if ($ret->UserId <= 0) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '内部系统，仅供总监以上级别使用');
		}

		$tmp = \yuemi_main\ProcedureInvoker::Instance()->check_user_role($ret->UserId);
		if ($tmp === null || $tmp->ReturnValue !== 'OK') {
			return [
				'__code' => $ret->ReturnValue,
				'__message' => $ret->ReturnMessage
			];
		}
		if ($tmp->LevelUser == 0) {
			throw new \Ziima\MVC\REST\Exception('E_FOBIDDEN', '此账号被禁止登录');
		}
		if ($tmp->LevelCheif <= 0 && $tmp->LevelDirector <= 0) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '身份等级不够');
		}

		return [
			'__code' => 'OK',
			'__message' => '',
			'WechatId' => $ret->WechatId,
			'UserId' => $ret->UserId,
			'Token' => $ret->UserToken,
			'Cheif' => [
				'Level' => $tmp->LevelCheif,
				'Id' => $ret->UserId
			],
			'Director' => [
				'Level' => $tmp->LevelDirector,
				'Id' => $ret->UserId
			]
		];
	}

	/**
	 * 手机号码登陆
	 * @param \Ziima\MVC\REST\Request $request
	 * 
	 * @request		mobile		string		手机号码
	 * @request		code		string		短信验证码
	 * 
	 * @noauth
	 */
	public function login_mobile(\Ziima\MVC\REST\Request $request) 
	{
		$Mobile = trim($request->body->mobile);
		$Vcode = trim($request->body->code);
		if (!$this->Cacher->sms_vcode($Mobile, $Vcode)) {
			return ['__code' => "E_Vcode", '__message' => '验证码错误'];
		}
		$ret = \yuemi_main\ProcedureInvoker::Instance()->oa_login_mobile(
				$request->body->mobile,
				$request->body->code,
				$this->Context->Runtime->ticket->ip);
		if ($ret === null) {
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '数据库错误');
		}
		if ($ret->ReturnValue != 'OK') {
			throw new \Ziima\MVC\REST\Exception($ret->ReturnValue, $ret->ReturnMessage);
		}
		if ($ret->UserId <= 0) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '内部系统，仅供总监以上级别使用');
		}

		$tmp = \yuemi_main\ProcedureInvoker::Instance()->check_user_role($ret->UserId);
		if ($tmp === null || $tmp->ReturnValue !== 'OK') {
			return [
				'__code' => $ret->ReturnValue,
				'__message' => $ret->ReturnMessage
			];
		}
		if ($tmp->LevelUser == 0) {
			throw new \Ziima\MVC\REST\Exception('E_FOBIDDEN', '此账号被禁止登录');
		}
		if ($tmp->LevelCheif <= 0 && $tmp->LevelDirector <= 0) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '身份等级不够');
		}

		return [
			'__code' => 'OK',
			'__message' => '',
			'WechatId' => $ret->WechatId,
			'UserId' => $ret->UserId,
			'Token' => $ret->UserToken,
			'Cheif' => [
				'Level' => $tmp->LevelCheif,
				'Id' => $ret->UserId
			],
			'Director' => [
				'Level' => $tmp->LevelDirector,
				'Id' => $ret->UserId
			]
		];
	}

	/**
	 * 激活总监卡
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		mobile		string		手机号码
	 * @request		code		string		短信验证码
	 * @request		card_code	string		卡号
	 * @request		pinid		string		身份证
	 * @request		name		string		姓名
	 */
	public function make_cheif_card(\Ziima\MVC\REST\Request $request) {
		//调用手机登录存储过程用户不存在创建账户
		$loginRe = \yuemi_main\ProcedureInvoker::Instance()->login_mobile($request->body->mobile, $request->body->code, $this->Context->Runtime->ticket->ip);
		if ($loginRe === null) {
			return "E_DATABASE";
		}
		if ($loginRe->ReturnValue != 'OK') {
			return [
				'__code' => $loginRe->ReturnValue,
				'__message' => $loginRe->ReturnMessage
			];
		}
		if ($loginRe->UserId > 0) {
			$tmp = \yuemi_main\ProcedureInvoker::Instance()->check_user_role($loginRe->UserId);
			if ($tmp === null || $tmp->ReturnValue !== 'OK') {
				return [
					'__code' => $loginRe->ReturnValue,
					'__message' => $loginRe->ReturnMessage
				];
			}
			if ($tmp->LevelUser == 0) {
				return [
					'__code' => 'E_FOBIDDEN',
					'__message' => '此账号被禁止登录'
				];
			}
		}
		$C = \yuemi_main\UserCertFactory::Instance()->load($loginRe->UserId);
		if ($C == null){
			$cert = new \yuemi_main\UserCertEntity();
			$cert->card_no = $request->body->pinid;
			$cert->card_name = $request->body->name;
			$cert->status = 2;
			$cert->create_time = Z_NOW;
			$cert->create_from =  $this->Context->Runtime->ticket->ip;
			$cert->user_id = $loginRe->UserId;
			$re = \yuemi_main\UserCertFactory::Instance()->insert($cert);
			if (!$re){
				return [
					'__code' => 'ERR',
					'__message' => '实名认证失败'
				];
			}
		} 
		$serial = preg_replace("/(\s|\&nbsp\;| | |　|　|\xc2\xa0)/","",$request->body->card_code);

		// 调用激活存储过程
		$makeRe = \yuemi_main\ProcedureInvoker::Instance()->make_card_cheif($loginRe->UserId, $serial, $this->Context->Runtime->ticket->ip);
		if ($makeRe === null) {
			return "E_DATABASE";
		}
		if ($makeRe->ReturnValue != 'OK') {
			return [
				'user_id'=> $loginRe->UserId,
				'__code' => $makeRe->ReturnValue,
				'__message' => $makeRe->ReturnMessage
			];
		}
		return [
			'Token'	=> $loginRe->UserToken,
			'user_id'=> $loginRe->UserId,
			'__code' => $makeRe->ReturnValue,
			'__message' => $makeRe->ReturnMessage
		];
	}
}
