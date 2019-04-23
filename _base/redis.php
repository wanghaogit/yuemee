<?php

/**
 * 全局Redis使用
 */
final class CacheManager {

	/**
	 * 数据库连接
	 * @var \Ziima\Data\MySQLConnection
	 */
	private $MySQL;

	/**
	 * Redis连接
	 * @var \Redis
	 */
	private $Redis;

	function __construct(\Ziima\Data\MySQLConnection $mysql, \Redis $redis) {
		$this->MySQL = $mysql;
		$this->Redis = $redis;
	}

	/**
	 * 加载地区数据
	 * @return array
	 */
	public function loadRegion() {
		$this->Redis->select(0);
		$rgn = $this->Redis->hGetAll('dict_region');
		if (empty($rgn)) {
			$this->region = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`region`");
			foreach ($this->region as $row) {
				$this->Redis->hSet('dict_region', $row['id'], "{$row['province']},{$row['city']},{$row['country']}");
			}
		} else {
			$this->region = [];
			foreach ($rgn as $k => $v) {
				$t = explode(',', $v);
				$this->region[] = [
					'id' => $k,
					'province' => $t[0],
					'city' => $t[1],
					'country' => $t[2]
				];
			}
		}
	}

	/**
	 * 地区数据
	 * @var array
	 */
	public $region = null;

	/**
	 * 加载银行数据
	 */
	public function loadBank() {
		$this->Redis->select(0);
		$rgn = $this->Redis->hGetAll('dict_bank');
		if (empty($rgn)) {
			$this->bank = $this->MySQL->grid("SELECT `id`,`name`,`icon` FROM `yuemi_main`.`bank`");
			foreach ($this->bank as $row) {
				$this->Redis->hSet('dict_bank', $row['id'], "{$row['name']},{$row['icon']}");
			}
		} else {
			$this->bank = [];
			foreach ($rgn as $k => $v) {
				$t = explode(',', $v);
				$this->bank[] = [
					'id' => $k,
					'name' => $t[0],
					'icon' => $t[1]
				];
			}
		}
	}

	/**
	 * 银行数据
	 * @var array
	 */
	public $bank = null;

	/**
	 * 加载手机品牌数据
	 */
	public function loadDeviceVender() {
		$this->Redis->select(0);
		$this->device_vender = $this->Redis->hGetAll('dict_mobile_vender');
		if (empty($this->device_vender)) {
			$this->device_vender = $this->MySQL->map("SELECT `id`,`name` FROM `yuemi_main`.`device_vender`", 'id', 'name');
			foreach ($this->device_vender as $k => $v) {
				$this->Redis->hSet('dict_mobile_vender', $k, $v);
			}
		}
	}

	/**
	 * MAP:手机品牌数据
	 * @var array
	 */
	public $device_vender = null;

	/**
	 * 加载手机型号数据
	 */
	public function loadDeviceModel() {
		$this->Redis->select(0);
		$this->device_model = $this->Redis->hGetAll('dict_mobile_model');
		if (empty($this->device_model)) {
			$this->device_model = $this->MySQL->map("SELECT `id`,`name` FROM `yuemi_main`.`device_model`", 'id', 'name');
			foreach ($this->device_model as $k => $v) {
				$this->Redis->hSet('dict_mobile_model', $k, $v);
			}
		}
	}

	/**
	 * MAP:手机品牌数据
	 * @var array
	 */
	public $device_model = null;

	/**
	 * 加载品类信息
	 */
	public function loadCatagory() {
		$this->Redis->select(0);
		$this->cataogry = $this->Redis->hGetAll('dict_catagory');
		if (empty($this->cataogry)) {
			$this->cataogry = $this->MySQL->hash("SELECT `id`,`parent_id`,`name`,`icon` FROM `yuemi_sale`.`catagory`", 'id');
			foreach ($this->cataogry as &$c) {
				if ($c['parent_id'] > 0) {
					$c['fullname'] = $this->cataogry[$c['parent_id']]['name'] . '/' . $c['name'];
				} else {
					$c['fullname'] = $c['name'];
				}
			}
			foreach ($this->cataogry as $k => $v) {
				$this->Redis->hSet('dict_catagory', $k, serialize($v));
			}
		} else {
			foreach ($this->cataogry as $id => &$u) {
				$u = unserialize($u);
			}
		}
	}

	/**
	 * MAP:品类
	 * @var array
	 */
	public $cataogry = null;

	/**
	 * 加载内购品类信息
	 */
	public function loadNeigouCatagory() {
		$this->Redis->select(0);
		$this->neigouCataogry = $this->Redis->hGetAll('dict_neigou_catagory');
		if (empty($this->neigouCataogry)) {
			$this->neigouCataogry = $this->MySQL->hash("SELECT `id`,`parent_id`,`name`,`map_id` FROM `yuemi_sale`.`ext_neigou_catagory`", 'id');

			foreach ($this->neigouCataogry as $k => $v) {
				$this->Redis->hSet('dict_neigou_catagory', $k, serialize($v));
			}
		} else {
			foreach ($this->neigouCataogry as $id => &$u) {
				$u = unserialize($u);
			}
		}
	}

	/**
	 * MAP:内购品类
	 * @var array
	 */
	public $neigouCataogry = null;

	/**
	 * 加载品牌信息
	 */
	public function loadBrand() {
		$this->Redis->select(0);
		$this->brand = $this->Redis->hGetAll('dict_brand');
		if (empty($this->brand)) {
			$this->brand = $this->MySQL->hash("SELECT `id`,`name`,`alias`,`logo` FROM `yuemi_sale`.`brand`", 'id');
			foreach ($this->brand as $k => $v) {
				$this->Redis->hSet('dict_brand', $k, serialize($v));
			}
		} else {
			foreach ($this->brand as $id => &$u) {
				$u = unserialize($u);
			}
		}
	}

	/**
	 * MAP:品牌
	 * @var array
	 */
	public $brand = null;

	/**
	 * 加载供应商
	 */
	public function loadSupplier() {
		$this->Redis->select(0);
		$this->supplier = $this->Redis->hGetAll('dict_supplier');
		if (empty($this->supplier)) {
			$this->supplier = $this->MySQL->hash("SELECT `id`,`user_id`,`name`,`pi_enable` FROM `yuemi_main`.`supplier`", 'id');
			foreach ($this->supplier as $k => $v) {
				$this->Redis->hSet('dict_supplier', $k, serialize($v));
			}
		} else {
			foreach ($this->supplier as $id => &$u) {
				$u = unserialize($u);
			}
		}
	}

	/**
	 * MAP:供应商
	 * @var array
	 */
	public $supplier;

	/**
	 * 加载管理员
	 */
	public function loadAdmin() {
		$this->Redis->select(0);
		$this->admin = $this->Redis->hGetAll('dict_admin');
		if (empty($this->admin)) {
			$this->admin = $this->MySQL->hash("SELECT `id`,`user_id`,`name`,`role_id`,`status` FROM `yuemi_main`.`rbac_admin`", 'id');
			foreach ($this->admin as $k => $v) {
				$this->Redis->hSet('dict_admin', $k, serialize($v));
			}
		} else {
			foreach ($this->admin as $id => &$u) {
				$u = unserialize($u);
			}
		}
	}

	/**
	 * MAP:管理员
	 * @var array
	 */
	public $admin;

	/**
	 * 加载管理角色
	 */
	public function loadRbacRole() {
		$this->Redis->select(0);
		$this->rbac_role = $this->Redis->hGetAll('dict_role');
		if (empty($this->rbac_role)) {
			$this->rbac_role = $this->MySQL->hash("SELECT `id`,`parent_id`,`name` FROM `yuemi_main`.`rbac_role`", 'id');
			foreach ($this->rbac_role as $k => $v) {
				$this->Redis->hSet('dict_role', $k, serialize($v));
			}
		} else {
			foreach ($this->rbac_role as $id => &$u) {
				$u = unserialize($u);
			}
		}
	}

	/**
	 * MAP:管理角色
	 * @var array
	 */
	public $rbac_role;

	/**
	 * 加载管理目标
	 */
	public function loadRbacTarget() {
		$this->Redis->select(0);
		$this->target = $this->Redis->hGetAll('dict_target');
		if (empty($this->target)) {
			$this->target = $this->MySQL->hash("SELECT * FROM `yuemi_main`.`rbac_target`", 'id');
			foreach ($this->target as $k => $v) {
				$this->Redis->hSet('dict_target', $k, serialize($v));
			}
		} else {
			foreach ($this->target as $id => &$u) {
				$u = unserialize($u);
			}
		}
	}

	/**
	 * MAP:管理目标
	 * @var array
	 */
	public $rbac_target;
	
	/**
	 * 加载管理目标
	 */
	public function loadRbacRule() {
		$this->Redis->select(0);
		$this->rbac_rule = $this->Redis->hGetAll('dict_rule');
		if (empty($this->rbac_rule)) {
			$this->rbac_rule = $this->MySQL->hash("SELECT * FROM `yuemi_main`.`rbac_rule`", 'id');
			foreach ($this->rbac_rule as $k => $v) {
				$this->Redis->hSet('dict_rule', $k, serialize($v));
			}
		} else {
			foreach ($this->rbac_rule as $id => &$u) {
				$u = unserialize($u);
			}
		}
	}

	/**
	 * MAP:管理目标
	 * @var array
	 */
	public $rbac_rule;

	/**
	 * 设置短信验证码
	 * @param strin	g	$mobile		手机号
	 * @param string	$vcode		验证码
	 * @parem int		$user_id	用户Id
	 * @parem int		$type		类型：0文字，1语音
	 * @return bool
	 */
	public function sms_set($mobile, $vcode, $user_id = 0, $type = 0)
	{
		$this->Redis->select(7);
		$data = $this->sms_get($mobile);
		// 每10秒只能发送一条 // 不同服务器时间差可能导致发送有问题，因此暂时不开启
		//if (is_array($data) && count($data) > 0) {
		//	foreach ($data AS $val) {
		//		if (($val['time']+10) > Z_NOW) {
		//			return ['__code' => 'E_BUSY', '__message' => '发送太频繁'];
		//		}
		//	}
		//}
		// 组合、存储
		$info['type'] = $type; // 类型：0文字验证码，1语音验证码
		$info['user_id'] = $user_id; // 用户Id
		$info['time'] = Z_NOW; // 有效期
		$info['vcode'] = $vcode; // 验证码
		$data[] = $info;
		$status = $this->Redis->setex($mobile, 86400*7, json_encode($data));
		if ($status) {
			return ['__code' => 'OK', '__message' => ''];
		} else {
			return ['__code' => 'E_SAVE', '__message' => '存储失败'];
		}
	}
	
	/**
	 * 验证码发送数量
	 * @param string	$mobile	手机号
	 * @param int $time		多少分钟内
	 * @param int $max		允许发送的最大数量
	 * @return bool => true允许继续发送，false不允许继续发送
	 */
	public function sms_vnum($mobile, $time = 60, $max = 6)
	{
		$this->Redis->select(7);
		$data = $this->sms_get($mobile);
		$num = 0;
		if (is_array($data) && count($data) > 0) {
			foreach ($data AS $val) {
				if (($val['time'] + $time*60) > Z_NOW) {
					$num ++;
				}
			}
		}
		return $num < $max ? true : false;
	}

	/**
	 * 读取短信验证码
	 * @param string	$mobile	手机号
	 * @return array
	 */
	public function sms_get($mobile)
	{
		$this->Redis->select(7);
		$data = $this->Redis->get($mobile);
		if (empty($data)) {
			return array();
		}
		$data = json_decode($data, true);
		// 清理过期数据
		foreach ($data AS $key => $val) {
			if (($val['time'] + 86400*7) < Z_NOW) {
				unset($data[$key]);
			}
		}
		// 存储、返回
		$this->Redis->setex($mobile, 86400*7, json_encode($data));
		return $data;
	}

	/**
	 * 检验验证码
	 * @param type $mobile
	 * @param type $vcode
	 * @return bool
	 */
	public function sms_vcode($mobile, $vcode)
	{
		$SmsList = $this->sms_get($mobile);
		if (empty($SmsList)) {
			return false;
		}
		$data = array();
		foreach ($SmsList AS $val) {
			if (($val['time'] + 1200) > Z_NOW) {
				$data[$val['vcode']] = $val;
			}
		}
		return isset($data[$vcode]);
	}

}
