<?php

include_once 'lib/ApiHandler.php';

/**
 * 用户资料API接口
 */
class profile_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 收货地址列表
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 *
	 */
	public function address(\Ziima\MVC\REST\Request $request) {
		$row = $this->MySQL->grid(
				"SELECT `id`,`region_id`,`address`,`contacts`,`mobile`,`create_time`,`is_default` " .
				"FROM `yuemi_main`.`user_address` " .
				"WHERE `user_id` = %d " .
				"AND `status` = 1 " .
				"ORDER BY `id` DESC ", $this->User->id);
		if (empty($row)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				'Addresses' => ''
			];
		} else {
			foreach ($row as $arr) {
				$list['id'] = $arr['id'];
				$list['rig'] = $arr['region_id'];
				$list['address'] = $arr['address'];
				$list['tel'] = $arr['mobile'];
				$list['name'] = $arr['contacts'];
				$list['default'] = $arr['is_default'];
				$res[] = $list;
			}
           
			return [
				'__code' => 'OK',
				'__message' => '',
				'Addresses' => $res
			];
		}
	}

	/**
	 * 新增收货地址
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		region_id		int		归属地区
	 * @request		address			string	详细地址
	 * @request		contacts		string	姓名
	 * @request		mobile			string	手机号码
	 */
	public function address_new(\Ziima\MVC\REST\Request $request) {


		if (empty($request->body->region_id)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '需要填写归属地区');
		}

		if (empty($request->body->address)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '需要填写详细地址');
		}

		if (empty($request->body->address)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '需要填写您的姓名');
		}

		if (empty($request->body->mobile)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '需要填写您的手机号');
		}

		$Address = new yuemi_main\UserAddressEntity();
		$Address->address = $request->body->address;
		$Address->contacts = $request->body->contacts;
		$Address->create_from = $this->Context->Runtime->ticket->ip;
		$Address->create_time = Z_NOW;
		$Address->mobile = $request->body->mobile;
		$Address->region_id = $request->body->region_id;
		$Address->status = 1;
		$Address->user_id = $this->User->id;
		$Re = \yuemi_main\UserAddressFactory::Instance()->insert($Address);
		if ($Re == null){
			return [
				'addres_id'	=> 0,
				'__code'	=> 'ERR',
				'__message'	=> ''
			];
		}
		return [
			'addres_id'	=> $Address->id,
			'__code'	=> 'OK',
			'__message'	=> ''
		];
	}

	/**
	 * 删除收货地址
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id			int		地址ID
	 */
	public function address_del(\Ziima\MVC\REST\Request $request) {
		$this->MySQL->execute("DELETE FROM `yuemi_main`.`user_address` WHERE id = %d", $request->body->id);
		return 'OK';
	}

	/**
	 * 设置默认收货地址
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		id			int		地址ID
	 */
	public function address_default(\Ziima\MVC\REST\Request $request) {

		$add = $this->MySQL->scalar(
				"SELECT id " .
				"FROM `yuemi_main`.`user_address` " .
				"WHERE is_default = 1 " .
				"AND user_id = %d",
				$this->User->id
		);
	
		if (empty($add)) {
			$this->MySQL->execute(
					"UPDATE `yuemi_main`.`user_address` SET `is_default` = '1' WHERE `id` = %d",
					$request->body->id
			);
			return 'OK';
		} else {
			$this->MySQL->execute(
					"UPDATE `yuemi_main`.`user_address` SET `is_default` = '0' WHERE `id` = %d",
					$add
			);
			$this->MySQL->execute(
					"UPDATE `yuemi_main`.`user_address` SET `is_default` = '1' WHERE `id` = %d",
					$request->body->id
			);
			return 'OK';
		}
	}

	/**
	 * 绑定银行卡列表
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 */
	public function bankcard(\Ziima\MVC\REST\Request $request) {

		$row = $this->MySQL->grid(
				"SELECT u.*,b.id AS bankid,b.icon AS icon " .
				"FROM `yuemi_main`.`user_bank` AS u " .
				"LEFT JOIN `yuemi_main`.`bank` AS b ON u.`bank_id` = b.`id` " .
				"WHERE `user_id` = %d " .
				"ORDER BY u.`id` DESC ", $this->User->id);

		if (empty($row)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				'Cards' => ''
			];
		} else {
			foreach ($row as $arr) {
				$list['Id'] = $arr['id'];
				$list['BankId'] = $arr['bankid'];
				$list['Serial'] = "P".$arr['card_no'];
				$list['BankName'] = $arr['bank_name'];
				$list['icon']=$arr['icon'];
				$res[] = $list;
			}

			return [
				'__code' => 'OK',
				'__message' => '',
				'Cards' => $res
			];
		}
	}

	/**
	 * 新增银行卡
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		bank_id			int		银行ID
	 * @request		serial			string	卡号
	 * @request		bank_name		string	开户行名称
	 */
	public function bankcard_new(\Ziima\MVC\REST\Request $request) {
		if (empty($request->body->bank_id)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '需要填写银行ID');
		}

		if (empty($request->body->serial)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '需要填写卡号');
		}

		if (empty($request->body->bank_name)) {
			throw new \Ziima\MVC\REST\Exception('E_PARAM', '需要填写开户行名称');
		}


		$this->MySQL->execute(
				"INSERT INTO `yuemi_main`.`user_bank`(`user_id`,`bank_id`,`bank_name`,`card_no`) VALUES (%d,%d,'%s',%d)",
				$this->User->id,
				$request->body->bank_id,
				$this->MySQL->encode($request->body->bank_name),
				$this->MySQL->encode($request->body->serial)
		);
		return 'OK';
	}

	/**
	 * 删除银行卡
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		id			int		卡片ID
	 */
	public function bankcard_del(\Ziima\MVC\REST\Request $request) {
		$this->MySQL->execute("DELETE FROM `yuemi_main`.`user_bank` WHERE id = %d", $request->body->id);
		return 'OK';
	}

	/**
	 * 进入编辑收货地址页面
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		id		int		地址ID
	 */

	public function edit_adderss(\Ziima\MVC\REST\Request $request) {
		$row = $this->MySQL->grid(
				"SELECT * FROM " .
				"`yuemi_main`.`user_address` " .
				"WHERE id = %s " .
				"AND user_id = %s",
				$request->body->id, $this->User->id
		);

		foreach ($row as $arr) {
			$list['id'] = $arr['id'];
			$list['uid'] = $arr['user_id'];
			$list['rig'] = $arr['region_id'];
			$list['address'] = $arr['address'];
			$list['mobile	'] = $arr['mobile'];
			$list['name'] = $arr['contacts'];
			$res[] = $list;
		}

		return [
			'__code' => 'OK',
			'__message' => '',
			'Profile' => $res
		];
	}

	/**
	 * 修改地址
	 * @param \Ziima\MVC\REST\Request $request
	 *
	 * @request		id			int			地址ID
	 * @request		region_id	int			地区ID
	 * @request		address		string		地址信息
	 * @request		name		string		姓名
	 * @request		mobile		int			手机号
	 */

	public function	update_adderss(\Ziima\MVC\REST\Request $request){
		
		$this->MySQL->execute(
				"UPDATE `yuemi_main`.`user_address` SET `region_id` = %d, `address` = '%s', `contacts` = '%s', `mobile` = %d WHERE id = %d",
				$request->body->region_id,
				$this->MySQL->encode($request->body->address),
				$this->MySQL->encode($request->body->name),
				$request->body->mobile,
				$request->body->id
		);
	
		return 'OK';
	}
}
