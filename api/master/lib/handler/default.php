<?php
include_once 'lib/ApiHandler.php';
include_once Z_ROOT . '/Cloud/Aliyun.php';
include_once Z_ROOT . '/Cloud/YunTongXun.php';

/**
 * 杂项API接口
 * 系统功能
 * 首页功能
 */
class default_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 测试连接接口，检查更新/检查公告/检查私信
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @noauth
	 */
	public function ping(\Ziima\MVC\REST\Request $request) {
		if ($this->Device === null) {
			throw new \Ziima\MVC\REST\Exception('E_DEVICE', '未注册设备 ' . $request->__udid);
		}
		$dat = [
			'__code' => 'OK',
			'__message' => '',
			'Notify' => [
				'Public' => null,
				'Private' => null
			],
			'DeviceId' => 0,
			'User' => null,
			'Wechat' => null
		];
		if ($this->Device) {
			$dat['DeviceId'] = $this->Device->id;
		}
		if ($this->User) {
			$dat['User'] = [
				'Id' => $this->User->id,
				'Name' => $this->User->name,
				'Mobile' => $this->User->mobile,
				'Token' => $this->User->token
			];
		}
		if ($this->Wechat) {
			$this->Wechat = [
				'Id' => $this->Wechat->id,
				'UnionId' => $this->Wechat->union_id
			];
		}
		if ($this->User === null) {
			return $dat;
		}

		return $dat;
	}

	/**
	 * 系统更新接口
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		sys_type	int			本机系统类型
	 * @request		app_id		string		本机APP包名
	 * @request		app_version	string		本机APP版本
	 *
	 * @response	version			int		安卓最新版本号
	 * @response	apkUrl			string	苹果最新版本号
	 * @noauth
	 */
	public function update(\Ziima\MVC\REST\Request $request) {
		$SysType = $request->body->sys_type;
		$AppId = $request->body->app_id;
		$AppVersion = $this->version2long($request->body->app_version);
		$sql = "SELECT `version`,`apk_url` "
				. "FROM `yuemi_main.release` "
				. "WHERE `platform_id` = {$SysType} AND `app_id` = '{$AppId}' "
				. "ORDER BY `version` DESC "
				. "LIMIT 0,1";
		$re = $this->MySQL->row($sql);
		if ($AppVersion < $re['version']) {
			return [
				'version' => $re['version'],
				'apkUrl' => $re['apk_url']
			];
		}
		return [
			'version' => $re['version'],
			'apkUrl' => ''
		];
	}

	/**
	 * 设备登记接口
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		hw_udid		string		手机序列号
	 * @request		hw_imei		string		手机序列号
	 * @request		hw_imsi		string		手机序列号
	 * @request		hw_vendor	string		手机品牌
	 * @request		hw_model	string		手机型号
	 * @request		hw_width	int			屏幕分辨率：宽度
	 * @request		hw_height	int			屏幕分辨率：高度
	 * @request		sys_type	int			系统类型：1安卓，2苹果
	 * @request		sys_version	string		系统版本：7.0.0
	 * @request		app_version	string		APP版本：1.0.0
	 * @request		oa_version	string		OA版本：1.0.0
	 * @request		gps_lng		float		GPS坐标，经度
	 * @request		gps_lat		float		GPS坐标，纬度
	 * @request		gps_region	int			高德识别出来的地区ID
	 *
	 * @noauth
	 */
	public function register(\Ziima\MVC\REST\Request $request) {
		if (empty($request->body->hw_udid))
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '缺少参数 hw_udid');
		if ($this->Device !== null && $this->Device->udid != $request->body->hw_udid) {
			throw new \Ziima\MVC\REST\Exception('E_DEVICE', '设备UDID错误');
		}
		if (mt_rand(1,1000) < 10){
			$rst = \yuemi_main\ProcedureInvoker::Instance()->device_register(
					$request->body->hw_udid, $request->body->hw_imei, $request->body->hw_imsi,
					$request->body->hw_vendor, $request->body->hw_model,
					$request->body->hw_width, $request->body->hw_height,
					$request->body->sys_type, $request->body->sys_version,
					$this->version2long($request->body->app_version),
					$this->version2long($request->body->oa_version),
					$request->body->gps_lng, $request->body->gps_lat, $request->body->gps_region,
					$request->__access_token, $this->Context->Runtime->ticket->ip);
			return [
				'__code' => 'OK',
				'__message' => '',
				'DeviceId' => $rst->DeviceId
			];
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'DeviceId' => ''
		];

	}

	/**
	 * 验证码短信接口
	 * @param \Ziima\MVC\REST\Request $request
	 * @request	style	int		验证码类型
	 * @request	mobile	string	目标手机号码
	 * @noauth
	 */
	public function sms(\Ziima\MVC\REST\Request $request) 
	{		
		$Style = $request->body->style;
		$Mobile = $request->body->mobile;
		$Style = intval($Style);
		$Mobile = trim($Mobile);
		if (!in_array($Style, [0, 1, 2, 3])) throw new \Ziima\MVC\REST\Exception('E_PARAM', '参数 style 格式错误');
		if (empty($Mobile)) throw new \Ziima\MVC\REST\Exception('E_PARAM', '缺少参数 mobile');
		if (!preg_match('/^1\d{10}$/', $Mobile)) throw new \Ziima\MVC\REST\Exception('E_PARAM', '参数 mobile 格式错误');
		// 验证是否有发送权限
		if (!$this->Cacher->sms_vnum($Mobile, 60, 6)) {
			 return ['__code' => 'E_BUSY', '__message' => '每小时发送超过6条'];
		}
		if (!$this->Cacher->sms_vnum($Mobile, 1440, 16)) {
			return ['__code' => 'E_BUSY', '__message' => '24小时内发送超过16条'];
		}
		// 存储到Redis
		$u = \yuemi_main\UserFactory::Instance()->loadOneByMobile($Mobile);
		$user_id = $u ? $u->id : 0; // 用户Id
		$vcode = \Ziima\Zid::Default()->sms(); // 随机验证码
		$SetStatus = $this->Cacher->sms_set($Mobile, $vcode, $user_id, $Style);
		if ($SetStatus['__code'] != 'OK') {
			return ['__code' => 'E_BUSY', '__message' => $SetStatus['__message']];
		}
		// 语音验证码
		//if ($Style == 1)
		//{
		//	$YunTongXun = new \Cloud\YunTongXun(YUNTONGXUN_AccountSid, YUNTONGXUN_AuthToken);
		//	$Re = $YunTongXun->vcode_send_sound(YUNTONGXUN_AppIdMain, $Mobile, $vcode);
		//	if ($Re) {
		//		return ['__code' => 'OK', '__message' => ''];
		//	} else {
		//		return ['__code' => 'E_YunTongXun', '__message' => '发送失败'];
		//	}
		//}
		// 文字验证码
		if (rand(0, 1) == 0)
		{
			$YunTongXun = new \Cloud\YunTongXun(YUNTONGXUN_AccountSid, YUNTONGXUN_AuthToken);
			if ($YunTongXun->sms_sned($Mobile, $vcode) != true) {
				return ['__code' => 'E_YunTongXun', '__message' => "短信发送失败"];
			}
		} else {
			$sms = new \Cloud\Aliyun\Notify();
			$ret = false;
			try {
				$ret = $sms->send($Mobile, 'SMS_126890099', ['code' => $vcode]);
			} catch (\Exception $e) {
				return ['__code' => 'E_ALIYUN', '__message' => $e->getMessage()];
			}
			if ($ret->Code != 'OK') {
				return ['__code' => 'E_ALIYUN', '__message' => $ret->Code . '/' . $ret->Message];
			}
		}
		return ['__code' => 'OK', '__message' => ''];
	}

	private function version2long(string $version): int {
		if (empty($version))
			return 0;
		$a = explode('.', $version);
		$v = intval($a[0]) * 100000 + intval($a[1] ?? '0') * 1000 + intval($a[2] ?? '0');
		return $v;
	}

}
