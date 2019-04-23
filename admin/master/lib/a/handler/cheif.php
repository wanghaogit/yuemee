<?php

include "lib/ApiHandler.php";
include_once Z_ROOT . '/Cloud/Aliyun.php';

/**
 * 总监管理接口
 */
class cheif_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 后台直接制造总监
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		order_id			string		订单号
	 * @request		mobile				string		手机号
	 * @request		name				string		姓名
	 * @request		pin					string		身份证
	 * @request		region				int			地区ID
	 * @request		address				string		地址
	 * @request		bank_id				int			银行
	 * @request		card				string		卡号
	 * @request		give_card			int			是否赠送VIP卡
	 */
	public function helicopter(\Ziima\MVC\REST\Request $request) {
		if (!empty($request->body->order_id) && !preg_match('/^KC\d{6}[A-Z0-9]+$/', $request->body->order_id)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '订单号格式错误');
		}
		if (!preg_match('/^\d{17}[0-9x]$/i', $request->body->pin)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '身份证号码格式错误');
		}
		if (!preg_match('/^1\d{10}$/i', $request->body->mobile)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '手机号[' . $request->body->mobile . ']格式错误');
		}
		$ret = \yuemi_main\ProcedureInvoker::Instance()->make_money_cheif_ex(
				$request->body->mobile,
				$request->body->order_id,
				$request->body->name,
				$request->body->pin,
				$request->body->region,
				$request->body->address,
				$request->body->bank_id,
				$request->body->card,
				$request->body->give_card,
				$this->Context->Runtime->ticket->ip);
		if ($ret === null) {
			return 'E_DATABASE';
		}
		if ($ret->ReturnValue != 'OK') {
			return [
				'__code' => $ret->ReturnValue,
				'__message' => $ret->ReturnMessage
			];
		}
		\yuemi_main\ProcedureInvoker::Instance()->check_user_role($ret->UserId);
		//发送短信
		try {
			$sms = new \Cloud\Aliyun\Notify();
			if ($request->body->give_card) {
				$sms->send($request->body->mobile, 'SMS_134317308', ['name' => $request->body->name]);
			} else {
				$sms->send($request->body->mobile, 'SMS_134312552', ['name' => $request->body->name]);
			}
		} catch (\Exception $e) {
			
		}
		
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

}
