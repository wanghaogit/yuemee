<?php

include_once 'lib/ApiHandler.php';

/**
 * 用户认证接口
 * @auth
 */
class cert_handler extends ApiHandler {

	/**
	 * 实名认证记录
	 * @var \yuemi_main\UserCertEntity
	 */
	private $Cert;

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function __auth() {
		parent::__auth();
		$this->Cert = \yuemi_main\UserCertFactory::Instance()->load($this->User->id);
	}

	/**
	 * 我的实名认证信息
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 */
	public function info(\Ziima\MVC\REST\Request $request) {
		if ($this->Cert === null) {
			return [
				'PicA' => '',
				'PicB' => '',
				'Name' => '',
				'Serial' => '',
				'Status' => 0
			];
		}
		return [
			'PicA' => URL_RES . '/upload' . $this->Cert->card_pic1,
			'PicB' => URL_RES . '/upload' . $this->Cert->card_pic2,
			'Name' => $this->Cert->card_name,
			'Serial' => "P".$this->Cert->card_no,
			'Status' => $this->Cert->status
		];
	}

	/**
	 * 上传实名认证照片
	 * @param \Ziima\MVC\REST\Request $request
	 * @request	side	int		图片正背面：1正面，2背面
	 * @request	format	string	图片格式：png、jpg
	 * @request	binary	string	图片Base64数据
	 *
	 */
	public function upload(\Ziima\MVC\REST\Request $request)
	{
		if ($this->Cert === null) {
			$sql = sprintf("INSERT INTO `yuemi_main`.`user_cert` " .
					"(`user_id`,`status`,`create_time`,`create_from`)" .
					" VALUES (%d,0,UNIX_TIMESTAMP(),%d)", $this->User->id, $this->Context->Runtime->ticket->ip);
			\Ziima\Tracer::Default()->debug($sql);
			$this->MySQL->execute($sql);
			$this->Cert = \yuemi_main\UserCertFactory::Instance()->load($this->User->id);
			if ($this->Cert === null) {
				throw new \Ziima\MVC\REST\Exception('E_DATABASE', '数据库错误');
			}
		}
		if ($this->Cert->status == 1 || $this->Cert->status == 2) {
			throw new \Ziima\MVC\REST\Exception('E_STATUS', '已提交审核，禁止修改');
		}
		$bin = base64_decode($request->body->binary);
		if ($bin === null || $bin === false) {
			throw new \Ziima\MVC\REST\Exception('E_REQUEST', '图片数据错误');
		}
		if ($request->body->format != 'jpg' && $request->body->format != 'png') {
			throw new \Ziima\MVC\REST\Exception('E_REQUEST', '图片格式错误');
		}
		if ($request->body->side != 1 && $request->body->side != 2) {
			throw new \Ziima\MVC\REST\Exception('E_REQUEST', '正反面选择错误');
		}

		// Path、URL
		$url = '/cert';
		$path = UPLOAD_ROOT . DIRECTORY_SEPARATOR . 'cert';

		// 生成随机路径（如果已经存在则重复尝试重新生成）
		$serial = \Ziima\Zid::Default()->serial();
		while (file_exists($path . DIRECTORY_SEPARATOR . substr($serial, 0, 2) . DIRECTORY_SEPARATOR . substr($serial, 2, 2) . DIRECTORY_SEPARATOR . $serial . '.' . $request->body->format)) {
			$serial = \Ziima\Zid::Default()->serial();
		}

		// 生成最后目录
		$url .= '/' . substr($serial, 0, 2) . '/' . substr($serial, 2, 2);
		$path .= DIRECTORY_SEPARATOR . substr($serial, 0, 2) . DIRECTORY_SEPARATOR . substr($serial, 2, 2);
		if (!is_dir($path)) {
			mkdir($path, 0755, true);
		}

		// 生成最路径、存储
		$url .= '/' . $serial . '.' . $request->body->format;
		$path .= DIRECTORY_SEPARATOR . $serial . '.' . $request->body->format;
		@file_put_contents($path, $bin);
		if ($request->body->side == 1) {
			$this->MySQL->execute("UPDATE `user_cert` SET `card_pic1` = '%s',`status` = 0 WHERE `user_id` = %d", $url, $this->User->id);
		} else {
			$this->MySQL->execute("UPDATE `user_cert` SET `card_pic2` = '%s',`status` = 0 WHERE `user_id` = %d", $url, $this->User->id);
		}
		return 'OK';
	}

	/**
	 * 提交认证
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		pinid		string		身份证号码
	 * @request		realname	string		真实姓名
	 *
	 * @throws \Ziima\MVC\REST\Exception
	 */
	public function commit(\Ziima\MVC\REST\Request $request) {
		if ($this->Cert === null) {
			throw new \Ziima\MVC\REST\Exception('E_CERT', '没有认证数据');
		}
		if (empty($this->Cert->card_pic1)) {
			throw new \Ziima\MVC\REST\Exception('E_CERT', '缺少身份证正面照片');
		}
		if (empty($this->Cert->card_pic2)) {
			throw new \Ziima\MVC\REST\Exception('E_CERT', '缺少身份证背面照片');
		}
		if (empty($request->body->realname)) {
			throw new \Ziima\MVC\REST\Exception('E_REQUEST', '缺少真实姓名');
		}
		if (empty($request->body->pinid)) {
			throw new \Ziima\MVC\REST\Exception('E_REQUEST', '缺少身份证号码');
		}
		if (!preg_match('/^\d{17}[0-9X]$/i', $request->body->pinid)) {
			throw new \Ziima\MVC\REST\Exception('E_REQUEST', '身份证号码不是二代18位格式');
		}
		if ($this->Cert->status == 0) {
			$this->MySQL->execute(
					"UPDATE `user_cert` SET " .
					"`card_name` = '%s'," .
					"`card_no` = '%s'," .
					"`status` = 1 " .
					"WHERE `user_id` = %d",
					$this->MySQL->encode($request->body->realname),
					$request->body->pinid,
					$this->User->id);
			return 'OK';
		} else if ($this->Cert->status == 1) {
			return 'OK';
		} else if ($this->Cert->status == 2) {
			throw new \Ziima\MVC\REST\Exception('E_STATUS', '实名已审核，禁止提交');
		} else {
			throw new \Ziima\MVC\REST\Exception('E_STATUS', '实名认证状态异常');
		}
	}

	/**
	 * 撤回认证
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @throws \Ziima\MVC\REST\Exception
	 */
	public function rollback(\Ziima\MVC\REST\Request $request) {
		if ($this->Cert->status == 0) {
			return 'OK';
		} else if ($this->Cert->status == 1) {
			$this->MySQL->execute("UPDATE `user_cert` SET `status` = 0 WHERE `user_id` = %d", $this->User->id);
			return 'OK';
		} else if ($this->Cert->status == 2) {
			throw new \Ziima\MVC\REST\Exception('E_STATUS', '实名已审核，禁止撤回');
		} else {
			$this->MySQL->execute("UPDATE `user_cert` SET `status` = 0 WHERE `user_id` = %d", $this->User->id);
			return 'OK';
		}
	}

}
