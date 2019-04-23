<?php
/*
 *
 */
namespace yuemi_main;
/**
 * 应用
 * @table applet
 * @engine innodb
 */
final class AppletEntity extends \Ziima\Data\Entity {
	/**
	 * 应用ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 归属用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 应用类型：0系统,1VIP，2总监,3经理,4供应商
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 调用Token
	 * @var string
	 */
	public $token = null;

	/**
	 * 调用密钥
	 * @var string
	 */
	public $secret = null;

	/**
	 * 回调地址
	 * @var string
	 */
	public $callback = null;

	/**
	 * 应用状态，0未提交,1待审核,2已审核,3已关闭
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var string
	 */
	public $create_time = null;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * AppletEntity Factory<br>
 * 应用
 */
final class AppletFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var AppletFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : AppletFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new AppletFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new AppletFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`applet`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`applet` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : AppletEntity {
		$obj = new AppletEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->type = $row['type'];
		$obj->name = $row['name'];
		$obj->token = $row['token'];
		$obj->secret = $row['secret'];
		$obj->callback = $row['callback'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(AppletEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`applet` %s(`id`,`user_id`,`type`,`name`,`token`,`secret`,`callback`,`status`,`create_time`,`create_from`) VALUES (NULL,%d,%d,'%s','%s','%s','%s',%d,NOW(),%d)";
		return sprintf($sql,'',$obj->user_id,$obj->type,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->token,16)
			,self::_encode_string($obj->secret,16)
			,self::_encode_string($obj->callback,1024)
			,$obj->status,$obj->create_from);
	}
	private function _object_to_update(AppletEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`applet` %s SET `user_id` = %d,`type` = %d,`name` = '%s',`token` = '%s',`secret` = '%s',`callback` = '%s',`status` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->type,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->token,16)
			,self::_encode_string($obj->secret,16)
			,self::_encode_string($obj->callback,1024)
			,$obj->status,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns AppletEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`applet`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`applet` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..应用ID
	 * @returns AppletEntity
	 * @returns null
	 */
	public function load(int $id) : ?AppletEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`applet` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..应用ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`applet` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 token 加载
	 * @param	string	$token	..调用Token
	 * @returns AppletEntity
	 * @returns null
	 */
	public function loadByToken (string $token) : ?AppletEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`applet` WHERE `token` = '%s'",
			parent::$reader->escape_string($token)
		));
		
	}
	
	/**
	 * 根据唯一索引 "token" 删除一条
	 * @param	string	$token	..调用Token
	 * @returns bool
	 */
	public function deleteByToken(string $token) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`applet` WHERE `token` = '%s'",
			parent::$reader->escape_string($token)
		));
		
	}
	
	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..归属用户ID
	 * @returns AppletEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?AppletEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`applet` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..归属用户ID
	 * @returns AppletEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`applet` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.applet 插入一条新纪录
	 * @param	AppletEntity    $obj    ..应用
	 * @returns bool
	 */
	public function insert(AppletEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.applet 回写一条记录<br>
	 * 更新依据： yuemi_main.applet.id
	 * @param	AppletEntity	  $obj    ..应用
	 * @returns bool
	 */
	 public function update(AppletEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 三层之二总监
 * @table cheif
 * @engine innodb
 */
final class CheifEntity extends \Ziima\Data\Entity {
	/**
	 * 总监ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 归属经理ID
	 * @var int
	 */
	public $director_id = null;

	/**
	 * IM系统群号，前缀：c_
	 * @var string
	 */
	public $imgid = null;

	/**
	 * 总监状态：0非总监,1免费总监,2晋升总监,3卡位总监
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 更新时间
	 * @var int
	 * @default	0
	 */
	public $update_time = 0;

	/**
	 * 年费到期时间
	 * @var int
	 * @default	0
	 */
	public $expire_time = 0;
}
/**
 * CheifEntity Factory<br>
 * 三层之二总监
 */
final class CheifFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var CheifFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : CheifFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new CheifFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new CheifFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cheif`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cheif` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : CheifEntity {
		$obj = new CheifEntity();$obj->user_id = $row['user_id'];
		$obj->director_id = $row['director_id'];
		$obj->imgid = $row['imgid'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->update_time = $row['update_time'];
		$obj->expire_time = $row['expire_time'];
		return $obj;
	}

	private function _object_to_insert(CheifEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`cheif` %s(`user_id`,`director_id`,`imgid`,`status`,`create_time`,`update_time`,`expire_time`) VALUES (%d,%d,'%s',%d,UNIX_TIMESTAMP(),%d,%d)";
		return sprintf($sql,'',$obj->user_id,$obj->director_id,self::_encode_string($obj->imgid,24)
			,$obj->status,$obj->update_time,$obj->expire_time);
	}
	private function _object_to_update(CheifEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`cheif` %s SET `director_id` = %d,`imgid` = '%s',`status` = %d,`update_time` = %d,`expire_time` = %d WHERE `user_id` = %d";
		
		return sprintf($sql,'',$obj->director_id,self::_encode_string($obj->imgid,24)
			,$obj->status,$obj->update_time,$obj->expire_time,$obj->user_id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns CheifEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`cheif`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`cheif` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "user_id" 加载一条
	 * @param	int	$user_id	..总监ID
	 * @returns CheifEntity
	 * @returns null
	 */
	public function load(int $user_id) : ?CheifEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据主键 "user_id" 删除一条
	 * @param	int	$user_id	..总监ID
	 * @returns bool
	 */
	public function delete(int $user_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`cheif` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据普通索引 director_id 加载一条
	 * @param	int  $director_id  ..归属经理ID
	 * @returns CheifEntity
	 * @returns null
	 */
	public function loadOneByDirectorId (int $director_id) : ?CheifEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif` WHERE `director_id` = '%d'",
			$director_id
		));
		
	}
	/**
	 * 根据普通索引 director_id 加载全部
	 * @param	int	$director_id	..归属经理ID
	 * @returns CheifEntity
	 * @returns null
	 */
	public function loadAllByDirectorId (int $director_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif` WHERE `director_id` = '%d'",
			$director_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.cheif 插入一条新纪录
	 * @param	CheifEntity    $obj    ..三层之二总监
	 * @returns bool
	 */
	public function insert(CheifEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.cheif 回写一条记录<br>
	 * 更新依据： yuemi_main.cheif.user_id
	 * @param	CheifEntity	  $obj    ..三层之二总监
	 * @returns bool
	 */
	 public function update(CheifEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 总监身份状态
 * @table cheif_buff
 * @engine innodb
 */
final class CheifBuffEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 状态来源，0=NONE,1=免费,2=晋升,3=卡位
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 支付渠道 0免费,1卡片,2线下,3微信,4支付宝
	 * @var int
	 * @default	0
	 */
	public $pay_channel = 0;

	/**
	 * 支付状态 0已关闭,1待支付,2已支付
	 * @var int
	 * @default	1
	 */
	public $pay_status = 1;

	/**
	 * 支付时间
	 * @var int
	 * @default	0
	 */
	public $pay_time = 0;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 支付金额
	 * @var float
	 * @default	0.0000
	 */
	public $money = 0.0000;

	/**
	 * 开始时间
	 * @var int
	 * @default	0
	 */
	public $start_time = 0;

	/**
	 * 过期时间
	 * @var int
	 * @default	0
	 */
	public $expire_time = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * CheifBuffEntity Factory<br>
 * 总监身份状态
 */
final class CheifBuffFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var CheifBuffFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : CheifBuffFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new CheifBuffFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new CheifBuffFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cheif_buff`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cheif_buff` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : CheifBuffEntity {
		$obj = new CheifBuffEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->type = $row['type'];
		$obj->pay_channel = $row['pay_channel'];
		$obj->pay_status = $row['pay_status'];
		$obj->pay_time = $row['pay_time'];
		$obj->order_id = $row['order_id'];
		$obj->money = $row['money'];
		$obj->start_time = $row['start_time'];
		$obj->expire_time = $row['expire_time'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(CheifBuffEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`cheif_buff` %s(`id`,`user_id`,`type`,`pay_channel`,`pay_status`,`pay_time`,`order_id`,`money`,`start_time`,`expire_time`,`create_time`,`create_from`) VALUES (NULL,%d,%d,%d,%d,%d,'%s',%f,%d,%d,UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->user_id,$obj->type,$obj->pay_channel,$obj->pay_status,$obj->pay_time,self::_encode_string($obj->order_id,16)
			,$obj->money,$obj->start_time,$obj->expire_time,$obj->create_from);
	}
	private function _object_to_update(CheifBuffEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`cheif_buff` %s SET `user_id` = %d,`type` = %d,`pay_channel` = %d,`pay_status` = %d,`pay_time` = %d,`order_id` = '%s',`money` = %f,`start_time` = %d,`expire_time` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->type,$obj->pay_channel,$obj->pay_status,$obj->pay_time,self::_encode_string($obj->order_id,16)
			,$obj->money,$obj->start_time,$obj->expire_time,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns CheifBuffEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`cheif_buff`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`cheif_buff` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns CheifBuffEntity
	 * @returns null
	 */
	public function load(int $id) : ?CheifBuffEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_buff` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`cheif_buff` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns CheifBuffEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?CheifBuffEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_buff` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns CheifBuffEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_buff` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 pay_status 加载一条
	 * @param	int  $pay_status  ..支付状态 0已关闭,1待支付,2已支付
	 * @returns CheifBuffEntity
	 * @returns null
	 */
	public function loadOneByPayStatus (int $pay_status) : ?CheifBuffEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_buff` WHERE `pay_status` = '%d'",
			$pay_status
		));
		
	}
	/**
	 * 根据普通索引 pay_status 加载全部
	 * @param	int	$pay_status	..支付状态 0已关闭,1待支付,2已支付
	 * @returns CheifBuffEntity
	 * @returns null
	 */
	public function loadAllByPayStatus (int $pay_status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_buff` WHERE `pay_status` = '%d'",
			$pay_status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.cheif_buff 插入一条新纪录
	 * @param	CheifBuffEntity    $obj    ..总监身份状态
	 * @returns bool
	 */
	public function insert(CheifBuffEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.cheif_buff 回写一条记录<br>
	 * 更新依据： yuemi_main.cheif_buff.id
	 * @param	CheifBuffEntity	  $obj    ..总监身份状态
	 * @returns bool
	 */
	 public function update(CheifBuffEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 总监激活卡
 * @table cheif_card
 * @engine innodb
 */
final class CheifCardEntity extends \Ziima\Data\Entity {
	/**
	 * 卡片ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 拥有者用户ID
	 * @var int
	 */
	public $owner_id = null;

	/**
	 * 卡号
	 * @var string
	 */
	public $serial = null;

	/**
	 * 价值金额（冗余）
	 * @var float
	 * @default	0.0000
	 */
	public $money = 0.0000;

	/**
	 * 接受者用户ID
	 * @var int
	 * @default	0
	 */
	public $rcv_user_id = 0;

	/**
	 * 接受者手机号码
	 * @var string
	 */
	public $rcv_mobile = null;

	/**
	 * VIP卡片状态：0 新卡,1使用
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 使用时间
	 * @var int
	 * @default	0
	 */
	public $used_time = 0;
}
/**
 * CheifCardEntity Factory<br>
 * 总监激活卡
 */
final class CheifCardFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var CheifCardFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : CheifCardFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new CheifCardFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new CheifCardFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cheif_card`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cheif_card` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : CheifCardEntity {
		$obj = new CheifCardEntity();$obj->id = $row['id'];
		$obj->owner_id = $row['owner_id'];
		$obj->serial = $row['serial'];
		$obj->money = $row['money'];
		$obj->rcv_user_id = $row['rcv_user_id'];
		$obj->rcv_mobile = $row['rcv_mobile'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->used_time = $row['used_time'];
		return $obj;
	}

	private function _object_to_insert(CheifCardEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`cheif_card` %s(`id`,`owner_id`,`serial`,`money`,`rcv_user_id`,`rcv_mobile`,`status`,`create_time`,`used_time`) VALUES (NULL,%d,'%s',%f,%d,'%s',%d,UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->owner_id,self::_encode_string($obj->serial,16)
			,$obj->money,$obj->rcv_user_id,self::_encode_string($obj->rcv_mobile,12)
			,$obj->status,$obj->used_time);
	}
	private function _object_to_update(CheifCardEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`cheif_card` %s SET `owner_id` = %d,`serial` = '%s',`money` = %f,`rcv_user_id` = %d,`rcv_mobile` = '%s',`status` = %d,`used_time` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->owner_id,self::_encode_string($obj->serial,16)
			,$obj->money,$obj->rcv_user_id,self::_encode_string($obj->rcv_mobile,12)
			,$obj->status,$obj->used_time,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns CheifCardEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`cheif_card`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`cheif_card` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..卡片ID
	 * @returns CheifCardEntity
	 * @returns null
	 */
	public function load(int $id) : ?CheifCardEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_card` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..卡片ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`cheif_card` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 serial 加载
	 * @param	string	$serial	..卡号
	 * @returns CheifCardEntity
	 * @returns null
	 */
	public function loadBySerial (string $serial) : ?CheifCardEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_card` WHERE `serial` = '%s'",
			parent::$reader->escape_string($serial)
		));
		
	}
	
	/**
	 * 根据唯一索引 "serial" 删除一条
	 * @param	string	$serial	..卡号
	 * @returns bool
	 */
	public function deleteBySerial(string $serial) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`cheif_card` WHERE `serial` = '%s'",
			parent::$reader->escape_string($serial)
		));
		
	}
	
	/**
	 * 根据普通索引 owner_id 加载一条
	 * @param	int  $owner_id  ..拥有者用户ID
	 * @returns CheifCardEntity
	 * @returns null
	 */
	public function loadOneByOwnerId (int $owner_id) : ?CheifCardEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_card` WHERE `owner_id` = '%d'",
			$owner_id
		));
		
	}
	/**
	 * 根据普通索引 owner_id 加载全部
	 * @param	int	$owner_id	..拥有者用户ID
	 * @returns CheifCardEntity
	 * @returns null
	 */
	public function loadAllByOwnerId (int $owner_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_card` WHERE `owner_id` = '%d'",
			$owner_id
		));
		
	}

	/**
	 * 根据普通索引 rcv_user_id 加载一条
	 * @param	int  $rcv_user_id  ..接受者用户ID
	 * @returns CheifCardEntity
	 * @returns null
	 */
	public function loadOneByRcvUserId (int $rcv_user_id) : ?CheifCardEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_card` WHERE `rcv_user_id` = '%d'",
			$rcv_user_id
		));
		
	}
	/**
	 * 根据普通索引 rcv_user_id 加载全部
	 * @param	int	$rcv_user_id	..接受者用户ID
	 * @returns CheifCardEntity
	 * @returns null
	 */
	public function loadAllByRcvUserId (int $rcv_user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_card` WHERE `rcv_user_id` = '%d'",
			$rcv_user_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.cheif_card 插入一条新纪录
	 * @param	CheifCardEntity    $obj    ..总监激活卡
	 * @returns bool
	 */
	public function insert(CheifCardEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.cheif_card 回写一条记录<br>
	 * 更新依据： yuemi_main.cheif_card.id
	 * @param	CheifCardEntity	  $obj    ..总监激活卡
	 * @returns bool
	 */
	 public function update(CheifCardEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 总监账户
 * @table cheif_finance
 * @engine innodb
 */
final class CheifFinanceEntity extends \Ziima\Data\Entity {
	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 间接招聘佣金
	 * @var float
	 */
	public $recruit_self = null;

	/**
	 * 团队管理佣金
	 * @var float
	 */
	public $deduct_self = null;

	/**
	 * 伯乐奖/招聘佣金部分
	 * @var float
	 */
	public $recruit_bole = null;

	/**
	 * 伯乐奖/销售佣金部分
	 * @var float
	 */
	public $deduct_bole = null;

	/**
	 * 礼包佣金是否解冻
	 * @var int
	 * @default	0
	 */
	public $thew_status = 0;

	/**
	 * 礼包佣金解冻时间
	 * @var int
	 * @default	0
	 */
	public $thew_time = 0;
}
/**
 * CheifFinanceEntity Factory<br>
 * 总监账户
 */
final class CheifFinanceFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var CheifFinanceFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : CheifFinanceFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new CheifFinanceFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new CheifFinanceFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cheif_finance`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cheif_finance` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : CheifFinanceEntity {
		$obj = new CheifFinanceEntity();$obj->user_id = $row['user_id'];
		$obj->recruit_self = $row['recruit_self'];
		$obj->deduct_self = $row['deduct_self'];
		$obj->recruit_bole = $row['recruit_bole'];
		$obj->deduct_bole = $row['deduct_bole'];
		$obj->thew_status = $row['thew_status'];
		$obj->thew_time = $row['thew_time'];
		return $obj;
	}

	private function _object_to_insert(CheifFinanceEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`cheif_finance` %s(`user_id`,`recruit_self`,`deduct_self`,`recruit_bole`,`deduct_bole`,`thew_status`,`thew_time`) VALUES (%d,%f,%f,%f,%f,%d,%d)";
		return sprintf($sql,'',$obj->user_id,$obj->recruit_self,$obj->deduct_self,$obj->recruit_bole,$obj->deduct_bole,$obj->thew_status,$obj->thew_time);
	}
	private function _object_to_update(CheifFinanceEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`cheif_finance` %s SET `recruit_self` = %f,`deduct_self` = %f,`recruit_bole` = %f,`deduct_bole` = %f,`thew_status` = %d,`thew_time` = %d WHERE `user_id` = %d";
		
		return sprintf($sql,'',$obj->recruit_self,$obj->deduct_self,$obj->recruit_bole,$obj->deduct_bole,$obj->thew_status,$obj->thew_time,$obj->user_id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns CheifFinanceEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`cheif_finance`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`cheif_finance` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "user_id" 加载一条
	 * @param	int	$user_id	..用户ID
	 * @returns CheifFinanceEntity
	 * @returns null
	 */
	public function load(int $user_id) : ?CheifFinanceEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cheif_finance` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据主键 "user_id" 删除一条
	 * @param	int	$user_id	..用户ID
	 * @returns bool
	 */
	public function delete(int $user_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`cheif_finance` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 向数据表 yuemi_main.cheif_finance 插入一条新纪录
	 * @param	CheifFinanceEntity    $obj    ..总监账户
	 * @returns bool
	 */
	public function insert(CheifFinanceEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.cheif_finance 回写一条记录<br>
	 * 更新依据： yuemi_main.cheif_finance.user_id
	 * @param	CheifFinanceEntity	  $obj    ..总监账户
	 * @returns bool
	 */
	 public function update(CheifFinanceEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * CMS内容
 * @table cms_article
 * @engine innodb
 */
final class CmsArticleEntity extends \Ziima\Data\Entity {
	/**
	 * 栏目ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 栏目ID
	 * @var int
	 * @default	0
	 */
	public $column_id = 0;

	/**
	 * 标题
	 * @var string
	 */
	public $title = null;

	/**
	 * 内容
	 * @var string
	 */
	public $content = null;

	/**
	 * 上架状态：0待审,1拒绝,2删除,3批准,4排队,5正常,6下架
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建人
	 * @var int
	 * @default	0
	 */
	public $create_user = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 审核人
	 * @var int
	 * @default	0
	 */
	public $audit_user = 0;

	/**
	 * 审核时间
	 * @var int
	 * @default	0
	 */
	public $audit_time = 0;

	/**
	 * 审核IP
	 * @var int
	 * @default	0
	 */
	public $audit_from = 0;
}
/**
 * CmsArticleEntity Factory<br>
 * CMS内容
 */
final class CmsArticleFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var CmsArticleFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : CmsArticleFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new CmsArticleFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new CmsArticleFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cms_article`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cms_article` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : CmsArticleEntity {
		$obj = new CmsArticleEntity();$obj->id = $row['id'];
		$obj->column_id = $row['column_id'];
		$obj->title = $row['title'];
		$obj->content = $row['content'];
		$obj->status = $row['status'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(CmsArticleEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`cms_article` %s(`id`,`column_id`,`title`,`content`,`status`,`create_user`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`) VALUES (NULL,%d,'%s','%s',%d,%d,UNIX_TIMESTAMP(),%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->column_id,self::_encode_string($obj->title,128)
			,self::_encode_string($obj->content,65535)
			,$obj->status,$obj->create_user,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(CmsArticleEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`cms_article` %s SET `column_id` = %d,`title` = '%s',`content` = '%s',`status` = %d,`create_user` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->column_id,self::_encode_string($obj->title,128)
			,self::_encode_string($obj->content,65535)
			,$obj->status,$obj->create_user,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns CmsArticleEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`cms_article`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`cms_article` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..栏目ID
	 * @returns CmsArticleEntity
	 * @returns null
	 */
	public function load(int $id) : ?CmsArticleEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_article` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..栏目ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`cms_article` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 column_id 加载一条
	 * @param	int  $column_id  ..栏目ID
	 * @returns CmsArticleEntity
	 * @returns null
	 */
	public function loadOneByColumnId (int $column_id) : ?CmsArticleEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_article` WHERE `column_id` = '%d'",
			$column_id
		));
		
	}
	/**
	 * 根据普通索引 column_id 加载全部
	 * @param	int	$column_id	..栏目ID
	 * @returns CmsArticleEntity
	 * @returns null
	 */
	public function loadAllByColumnId (int $column_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_article` WHERE `column_id` = '%d'",
			$column_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.cms_article 插入一条新纪录
	 * @param	CmsArticleEntity    $obj    ..CMS内容
	 * @returns bool
	 */
	public function insert(CmsArticleEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.cms_article 回写一条记录<br>
	 * 更新依据： yuemi_main.cms_article.id
	 * @param	CmsArticleEntity	  $obj    ..CMS内容
	 * @returns bool
	 */
	 public function update(CmsArticleEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * CMS栏目
 * @table cms_column
 * @engine innodb
 */
final class CmsColumnEntity extends \Ziima\Data\Entity {
	/**
	 * 栏目ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 上级栏目ID
	 * @var int
	 * @default	0
	 */
	public $parent_id = 0;

	/**
	 * 栏目名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 栏目代号
	 * @var string
	 */
	public $alias = null;

	/**
	 * 创建人
	 * @var int
	 * @default	0
	 */
	public $create_user = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * CmsColumnEntity Factory<br>
 * CMS栏目
 */
final class CmsColumnFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var CmsColumnFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : CmsColumnFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new CmsColumnFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new CmsColumnFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cms_column`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cms_column` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : CmsColumnEntity {
		$obj = new CmsColumnEntity();$obj->id = $row['id'];
		$obj->parent_id = $row['parent_id'];
		$obj->name = $row['name'];
		$obj->alias = $row['alias'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(CmsColumnEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`cms_column` %s(`id`,`parent_id`,`name`,`alias`,`create_user`,`create_time`,`create_from`) VALUES (NULL,%d,'%s','%s',%d,UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,$obj->create_user,$obj->create_from);
	}
	private function _object_to_update(CmsColumnEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`cms_column` %s SET `parent_id` = %d,`name` = '%s',`alias` = '%s',`create_user` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,$obj->create_user,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns CmsColumnEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`cms_column`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`cms_column` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..栏目ID
	 * @returns CmsColumnEntity
	 * @returns null
	 */
	public function load(int $id) : ?CmsColumnEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_column` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..栏目ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`cms_column` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 parent_id 加载一条
	 * @param	int  $parent_id  ..上级栏目ID
	 * @returns CmsColumnEntity
	 * @returns null
	 */
	public function loadOneByParentId (int $parent_id) : ?CmsColumnEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_column` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}
	/**
	 * 根据普通索引 parent_id 加载全部
	 * @param	int	$parent_id	..上级栏目ID
	 * @returns CmsColumnEntity
	 * @returns null
	 */
	public function loadAllByParentId (int $parent_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_column` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.cms_column 插入一条新纪录
	 * @param	CmsColumnEntity    $obj    ..CMS栏目
	 * @returns bool
	 */
	public function insert(CmsColumnEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.cms_column 回写一条记录<br>
	 * 更新依据： yuemi_main.cms_column.id
	 * @param	CmsColumnEntity	  $obj    ..CMS栏目
	 * @returns bool
	 */
	 public function update(CmsColumnEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * CMS素材
 * @table cms_material
 * @engine innodb
 */
final class CmsMaterialEntity extends \Ziima\Data\Entity {
	/**
	 * 素材ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 关联栏目ID
	 * @var int
	 * @default	0
	 */
	public $column_id = 0;

	/**
	 * 关联文章ID
	 * @var int
	 * @default	0
	 */
	public $article_id = 0;

	/**
	 * 文件大小：字节
	 * @var int
	 * @default	0
	 */
	public $file_size = 0;

	/**
	 * 访问路径
	 * @var string
	 */
	public $file_url = null;

	/**
	 * 图片宽度
	 * @var int
	 * @default	0
	 */
	public $image_width = 0;

	/**
	 * 图片高度
	 * @var int
	 * @default	0
	 */
	public $image_height = 0;

	/**
	 * 缩略图路径
	 * @var string
	 */
	public $thumb_url = null;

	/**
	 * 缩略图大小：字节
	 * @var int
	 * @default	0
	 */
	public $thumb_size = 0;

	/**
	 * 缩略图宽度
	 * @var int
	 * @default	0
	 */
	public $thumb_width = 0;

	/**
	 * 缩略图高度
	 * @var int
	 * @default	0
	 */
	public $thumb_height = 0;

	/**
	 * 素材状态 0待审,1已审,2删除
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 审核人
	 * @var int
	 * @default	0
	 */
	public $audit_user = 0;

	/**
	 * 审核时间
	 * @var int
	 * @default	0
	 */
	public $audit_time = 0;

	/**
	 * 审核IP
	 * @var int
	 * @default	0
	 */
	public $audit_from = 0;
}
/**
 * CmsMaterialEntity Factory<br>
 * CMS素材
 */
final class CmsMaterialFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var CmsMaterialFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : CmsMaterialFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new CmsMaterialFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new CmsMaterialFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cms_material`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`cms_material` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : CmsMaterialEntity {
		$obj = new CmsMaterialEntity();$obj->id = $row['id'];
		$obj->column_id = $row['column_id'];
		$obj->article_id = $row['article_id'];
		$obj->file_size = $row['file_size'];
		$obj->file_url = $row['file_url'];
		$obj->image_width = $row['image_width'];
		$obj->image_height = $row['image_height'];
		$obj->thumb_url = $row['thumb_url'];
		$obj->thumb_size = $row['thumb_size'];
		$obj->thumb_width = $row['thumb_width'];
		$obj->thumb_height = $row['thumb_height'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(CmsMaterialEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`cms_material` %s(`id`,`column_id`,`article_id`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`status`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`) VALUES (NULL,%d,%d,%d,'%s',%d,%d,'%s',%d,%d,%d,%d,UNIX_TIMESTAMP(),%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->column_id,$obj->article_id,$obj->file_size,self::_encode_string($obj->file_url,512)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,512)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(CmsMaterialEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`cms_material` %s SET `column_id` = %d,`article_id` = %d,`file_size` = %d,`file_url` = '%s',`image_width` = %d,`image_height` = %d,`thumb_url` = '%s',`thumb_size` = %d,`thumb_width` = %d,`thumb_height` = %d,`status` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->column_id,$obj->article_id,$obj->file_size,self::_encode_string($obj->file_url,512)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,512)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns CmsMaterialEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`cms_material`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`cms_material` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..素材ID
	 * @returns CmsMaterialEntity
	 * @returns null
	 */
	public function load(int $id) : ?CmsMaterialEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_material` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..素材ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`cms_material` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 column_id 加载一条
	 * @param	int  $column_id  ..关联栏目ID
	 * @returns CmsMaterialEntity
	 * @returns null
	 */
	public function loadOneByColumnId (int $column_id) : ?CmsMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_material` WHERE `column_id` = '%d'",
			$column_id
		));
		
	}
	/**
	 * 根据普通索引 column_id 加载全部
	 * @param	int	$column_id	..关联栏目ID
	 * @returns CmsMaterialEntity
	 * @returns null
	 */
	public function loadAllByColumnId (int $column_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_material` WHERE `column_id` = '%d'",
			$column_id
		));
		
	}

	/**
	 * 根据普通索引 article_id 加载一条
	 * @param	int  $article_id  ..关联文章ID
	 * @returns CmsMaterialEntity
	 * @returns null
	 */
	public function loadOneByArticleId (int $article_id) : ?CmsMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_material` WHERE `article_id` = '%d'",
			$article_id
		));
		
	}
	/**
	 * 根据普通索引 article_id 加载全部
	 * @param	int	$article_id	..关联文章ID
	 * @returns CmsMaterialEntity
	 * @returns null
	 */
	public function loadAllByArticleId (int $article_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_material` WHERE `article_id` = '%d'",
			$article_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..素材状态 0待审,1已审,2删除
	 * @returns CmsMaterialEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?CmsMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_material` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..素材状态 0待审,1已审,2删除
	 * @returns CmsMaterialEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`cms_material` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.cms_material 插入一条新纪录
	 * @param	CmsMaterialEntity    $obj    ..CMS素材
	 * @returns bool
	 */
	public function insert(CmsMaterialEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.cms_material 回写一条记录<br>
	 * 更新依据： yuemi_main.cms_material.id
	 * @param	CmsMaterialEntity	  $obj    ..CMS素材
	 * @returns bool
	 */
	 public function update(CmsMaterialEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 客服
 * @table consult
 * @engine innodb
 */
final class ConsultEntity extends \Ziima\Data\Entity {
	/**
	 * 聊天记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 沟通类型：0全局,1商品,2订单
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 关联商品ID
	 * @var int
	 * @default	0
	 */
	public $shelf_id = 0;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 发送人ID（用户）
	 * @var int
	 * @default	0
	 */
	public $sender_id = 0;

	/**
	 * 发送人昵称
	 * @var string
	 */
	public $sender_name = null;

	/**
	 * 接收人ID（后台用户）
	 * @var int
	 * @default	0
	 */
	public $reciver_id = 0;

	/**
	 * 发送人昵称
	 * @var string
	 */
	public $reciver_name = null;

	/**
	 * 公告标题
	 * @var string
	 */
	public $title = null;

	/**
	 * 公告内容
	 * @var string
	 */
	public $content = null;

	/**
	 * 发布时间
	 * @var string
	 */
	public $create_time = null;

	/**
	 * 发布IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * ConsultEntity Factory<br>
 * 客服
 */
final class ConsultFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ConsultFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : ConsultFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ConsultFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ConsultFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`consult`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`consult` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ConsultEntity {
		$obj = new ConsultEntity();$obj->id = $row['id'];
		$obj->type = $row['type'];
		$obj->shelf_id = $row['shelf_id'];
		$obj->order_id = $row['order_id'];
		$obj->sender_id = $row['sender_id'];
		$obj->sender_name = $row['sender_name'];
		$obj->reciver_id = $row['reciver_id'];
		$obj->reciver_name = $row['reciver_name'];
		$obj->title = $row['title'];
		$obj->content = $row['content'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(ConsultEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`consult` %s(`id`,`type`,`shelf_id`,`order_id`,`sender_id`,`sender_name`,`reciver_id`,`reciver_name`,`title`,`content`,`create_time`,`create_from`) VALUES (NULL,%d,%d,'%s',%d,'%s',%d,'%s','%s','%s',NOW(),%d)";
		return sprintf($sql,'',$obj->type,$obj->shelf_id,self::_encode_string($obj->order_id,16)
			,$obj->sender_id,self::_encode_string($obj->sender_name,32)
			,$obj->reciver_id,self::_encode_string($obj->reciver_name,32)
			,self::_encode_string($obj->title,64)
			,self::_encode_string($obj->content,65535)
			,$obj->create_from);
	}
	private function _object_to_update(ConsultEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`consult` %s SET `type` = %d,`shelf_id` = %d,`order_id` = '%s',`sender_id` = %d,`sender_name` = '%s',`reciver_id` = %d,`reciver_name` = '%s',`title` = '%s',`content` = '%s',`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->type,$obj->shelf_id,self::_encode_string($obj->order_id,16)
			,$obj->sender_id,self::_encode_string($obj->sender_name,32)
			,$obj->reciver_id,self::_encode_string($obj->reciver_name,32)
			,self::_encode_string($obj->title,64)
			,self::_encode_string($obj->content,65535)
			,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ConsultEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`consult`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`consult` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..聊天记录ID
	 * @returns ConsultEntity
	 * @returns null
	 */
	public function load(int $id) : ?ConsultEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`consult` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..聊天记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`consult` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 sender_id 加载一条
	 * @param	int  $sender_id  ..发送人ID（用户）
	 * @returns ConsultEntity
	 * @returns null
	 */
	public function loadOneBySenderId (int $sender_id) : ?ConsultEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`consult` WHERE `sender_id` = '%d'",
			$sender_id
		));
		
	}
	/**
	 * 根据普通索引 sender_id 加载全部
	 * @param	int	$sender_id	..发送人ID（用户）
	 * @returns ConsultEntity
	 * @returns null
	 */
	public function loadAllBySenderId (int $sender_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`consult` WHERE `sender_id` = '%d'",
			$sender_id
		));
		
	}

	/**
	 * 根据普通索引 reciver_id 加载一条
	 * @param	int  $reciver_id  ..接收人ID（后台用户）
	 * @returns ConsultEntity
	 * @returns null
	 */
	public function loadOneByReciverId (int $reciver_id) : ?ConsultEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`consult` WHERE `reciver_id` = '%d'",
			$reciver_id
		));
		
	}
	/**
	 * 根据普通索引 reciver_id 加载全部
	 * @param	int	$reciver_id	..接收人ID（后台用户）
	 * @returns ConsultEntity
	 * @returns null
	 */
	public function loadAllByReciverId (int $reciver_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`consult` WHERE `reciver_id` = '%d'",
			$reciver_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.consult 插入一条新纪录
	 * @param	ConsultEntity    $obj    ..客服
	 * @returns bool
	 */
	public function insert(ConsultEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.consult 回写一条记录<br>
	 * 更新依据： yuemi_main.consult.id
	 * @param	ConsultEntity	  $obj    ..客服
	 * @returns bool
	 */
	 public function update(ConsultEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 终端设备
 * @table device
 * @engine innodb
 */
final class DeviceEntity extends \Ziima\Data\Entity {
	/**
	 * 设备ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 设备序列号
	 * @var string
	 */
	public $udid = null;

	/**
	 * 设备IMEI
	 * @var string
	 */
	public $imei = null;

	/**
	 * 设备IMEI
	 * @var string
	 */
	public $imsi = null;

	/**
	 * 设备类型：0未知，1安卓，2苹果
	 * @var int
	 */
	public $type = null;

	/**
	 * 设备品牌
	 * @var int
	 * @default	0
	 */
	public $vendor_id = 0;

	/**
	 * 设备型号
	 * @var int
	 * @default	0
	 */
	public $model_id = 0;

	/**
	 * 系统版本
	 * @var string
	 * @default	0
	 */
	public $version_sys = '0';

	/**
	 * APP版本
	 * @var int
	 * @default	0
	 */
	public $version_app = 0;

	/**
	 * OA版本
	 * @var int
	 * @default	0
	 */
	public $version_oa = 0;

	/**
	 * 屏幕宽度
	 * @var int
	 * @default	0
	 */
	public $screen_x = 0;

	/**
	 * 屏幕高度
	 * @var int
	 * @default	0
	 */
	public $screen_y = 0;

	/**
	 * GPS坐标
	 * @var \Ziima\Data\GEO\Point
	 */
	public $gps = null;

	/**
	 * 计算出来的位置
	 * @var int
	 * @default	0
	 */
	public $region_id = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 更新时间
	 * @var int
	 * @default	0
	 */
	public $update_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $update_from = 0;
}
/**
 * DeviceEntity Factory<br>
 * 终端设备
 */
final class DeviceFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var DeviceFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : DeviceFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new DeviceFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new DeviceFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`device`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`device` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : DeviceEntity {
		$obj = new DeviceEntity();$obj->id = $row['id'];
		$obj->udid = $row['udid'];
		$obj->imei = $row['imei'];
		$obj->imsi = $row['imsi'];
		$obj->type = $row['type'];
		$obj->vendor_id = $row['vendor_id'];
		$obj->model_id = $row['model_id'];
		$obj->version_sys = $row['version_sys'];
		$obj->version_app = $row['version_app'];
		$obj->version_oa = $row['version_oa'];
		$obj->screen_x = $row['screen_x'];
		$obj->screen_y = $row['screen_y'];
		$obj->gps = parent::_convert_point_to_geo($row['gps']);
		$obj->region_id = $row['region_id'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->update_time = $row['update_time'];
		$obj->update_from = $row['update_from'];
		return $obj;
	}

	private function _object_to_insert(DeviceEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`device` %s(`id`,`udid`,`imei`,`imsi`,`type`,`vendor_id`,`model_id`,`version_sys`,`version_app`,`version_oa`,`screen_x`,`screen_y`,`gps`,`region_id`,`create_time`,`create_from`,`update_time`,`update_from`) VALUES (NULL,'%s','%s','%s',%d,%d,%d,'%s',%d,%d,%d,%d,POINT(%.8f,%.8f),%d,UNIX_TIMESTAMP(),%d,%d,%d)";
		return sprintf($sql,'',self::_encode_string($obj->udid,40)
			,self::_encode_string($obj->imei,40)
			,self::_encode_string($obj->imsi,40)
			,$obj->type,$obj->vendor_id,$obj->model_id,self::_encode_string($obj->version_sys,16)
			,$obj->version_app,$obj->version_oa,$obj->screen_x,$obj->screen_y,$obj->gps->longitude,$obj->gps->latitude
			,$obj->region_id,$obj->create_from,$obj->update_time,$obj->update_from);
	}
	private function _object_to_update(DeviceEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`device` %s SET `udid` = '%s',`imei` = '%s',`imsi` = '%s',`type` = %d,`vendor_id` = %d,`model_id` = %d,`version_sys` = '%s',`version_app` = %d,`version_oa` = %d,`screen_x` = %d,`screen_y` = %d,`gps` = POINT(%.8f,%.8f),`region_id` = %d,`create_from` = %d,`update_time` = %d,`update_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->udid,40)
			,self::_encode_string($obj->imei,40)
			,self::_encode_string($obj->imsi,40)
			,$obj->type,$obj->vendor_id,$obj->model_id,self::_encode_string($obj->version_sys,16)
			,$obj->version_app,$obj->version_oa,$obj->screen_x,$obj->screen_y,$obj->gps->longitude,$obj->gps->latitude
			,$obj->region_id,$obj->create_from,$obj->update_time,$obj->update_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns DeviceEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`device`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`device` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..设备ID
	 * @returns DeviceEntity
	 * @returns null
	 */
	public function load(int $id) : ?DeviceEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`device` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..设备ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`device` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 udid 加载
	 * @param	string	$udid	..设备序列号
	 * @returns DeviceEntity
	 * @returns null
	 */
	public function loadByUdid (string $udid) : ?DeviceEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`device` WHERE `udid` = '%s'",
			parent::$reader->escape_string($udid)
		));
		
	}
	
	/**
	 * 根据唯一索引 "udid" 删除一条
	 * @param	string	$udid	..设备序列号
	 * @returns bool
	 */
	public function deleteByUdid(string $udid) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`device` WHERE `udid` = '%s'",
			parent::$reader->escape_string($udid)
		));
		
	}
	
	/**
	 * 根据普通索引 version_app 加载一条
	 * @param	int  $version_app  ..APP版本
	 * @returns DeviceEntity
	 * @returns null
	 */
	public function loadOneByVersionApp (int $version_app) : ?DeviceEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`device` WHERE `version_app` = '%d'",
			$version_app
		));
		
	}
	/**
	 * 根据普通索引 version_app 加载全部
	 * @param	int	$version_app	..APP版本
	 * @returns DeviceEntity
	 * @returns null
	 */
	public function loadAllByVersionApp (int $version_app) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`device` WHERE `version_app` = '%d'",
			$version_app
		));
		
	}

	/**
	 * 根据普通索引 version_oa 加载一条
	 * @param	int  $version_oa  ..OA版本
	 * @returns DeviceEntity
	 * @returns null
	 */
	public function loadOneByVersionOa (int $version_oa) : ?DeviceEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`device` WHERE `version_oa` = '%d'",
			$version_oa
		));
		
	}
	/**
	 * 根据普通索引 version_oa 加载全部
	 * @param	int	$version_oa	..OA版本
	 * @returns DeviceEntity
	 * @returns null
	 */
	public function loadAllByVersionOa (int $version_oa) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`device` WHERE `version_oa` = '%d'",
			$version_oa
		));
		
	}

	/**
	 * 根据普通索引 region_id 加载一条
	 * @param	int  $region_id  ..计算出来的位置
	 * @returns DeviceEntity
	 * @returns null
	 */
	public function loadOneByRegionId (int $region_id) : ?DeviceEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`device` WHERE `region_id` = '%d'",
			$region_id
		));
		
	}
	/**
	 * 根据普通索引 region_id 加载全部
	 * @param	int	$region_id	..计算出来的位置
	 * @returns DeviceEntity
	 * @returns null
	 */
	public function loadAllByRegionId (int $region_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`device` WHERE `region_id` = '%d'",
			$region_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.device 插入一条新纪录
	 * @param	DeviceEntity    $obj    ..终端设备
	 * @returns bool
	 */
	public function insert(DeviceEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.device 回写一条记录<br>
	 * 更新依据： yuemi_main.device.id
	 * @param	DeviceEntity	  $obj    ..终端设备
	 * @returns bool
	 */
	 public function update(DeviceEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 三层之三经理
 * @table director
 * @engine innodb
 */
final class DirectorEntity extends \Ziima\Data\Entity {
	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * IM系统群号，前缀：d_
	 * @var string
	 */
	public $imgid = null;

	/**
	 * 总经理状态：0非经理,2晋升经理,3卡位经理
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 更新时间
	 * @var int
	 * @default	0
	 */
	public $update_time = 0;

	/**
	 * 年费到期时间
	 * @var int
	 * @default	0
	 */
	public $expire_time = 0;
}
/**
 * DirectorEntity Factory<br>
 * 三层之三经理
 */
final class DirectorFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var DirectorFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : DirectorFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new DirectorFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new DirectorFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`director`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`director` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : DirectorEntity {
		$obj = new DirectorEntity();$obj->user_id = $row['user_id'];
		$obj->imgid = $row['imgid'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->update_time = $row['update_time'];
		$obj->expire_time = $row['expire_time'];
		return $obj;
	}

	private function _object_to_insert(DirectorEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`director` %s(`user_id`,`imgid`,`status`,`create_time`,`update_time`,`expire_time`) VALUES (%d,'%s',%d,UNIX_TIMESTAMP(),%d,%d)";
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->imgid,24)
			,$obj->status,$obj->update_time,$obj->expire_time);
	}
	private function _object_to_update(DirectorEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`director` %s SET `imgid` = '%s',`status` = %d,`update_time` = %d,`expire_time` = %d WHERE `user_id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->imgid,24)
			,$obj->status,$obj->update_time,$obj->expire_time,$obj->user_id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns DirectorEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`director`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`director` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "user_id" 加载一条
	 * @param	int	$user_id	..用户ID
	 * @returns DirectorEntity
	 * @returns null
	 */
	public function load(int $user_id) : ?DirectorEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`director` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据主键 "user_id" 删除一条
	 * @param	int	$user_id	..用户ID
	 * @returns bool
	 */
	public function delete(int $user_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`director` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 向数据表 yuemi_main.director 插入一条新纪录
	 * @param	DirectorEntity    $obj    ..三层之三经理
	 * @returns bool
	 */
	public function insert(DirectorEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.director 回写一条记录<br>
	 * 更新依据： yuemi_main.director.user_id
	 * @param	DirectorEntity	  $obj    ..三层之三经理
	 * @returns bool
	 */
	 public function update(DirectorEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 经理身份状态
 * @table director_buff
 * @engine innodb
 */
final class DirectorBuffEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 状态来源，0=NONE,2=晋升,3=卡位
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 支付渠道 0线下,1免费,2微信,3支付宝
	 * @var int
	 * @default	0
	 */
	public $pay_channel = 0;

	/**
	 * 支付状态 0已关闭,1待支付,2已支付
	 * @var int
	 * @default	1
	 */
	public $pay_status = 1;

	/**
	 * 支付时间
	 * @var int
	 * @default	0
	 */
	public $pay_time = 0;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 支付金额
	 * @var float
	 * @default	0.0000
	 */
	public $money = 0.0000;

	/**
	 * 开始时间
	 * @var int
	 * @default	0
	 */
	public $start_time = 0;

	/**
	 * 过期时间
	 * @var int
	 * @default	0
	 */
	public $expire_time = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * DirectorBuffEntity Factory<br>
 * 经理身份状态
 */
final class DirectorBuffFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var DirectorBuffFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : DirectorBuffFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new DirectorBuffFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new DirectorBuffFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`director_buff`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`director_buff` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : DirectorBuffEntity {
		$obj = new DirectorBuffEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->type = $row['type'];
		$obj->pay_channel = $row['pay_channel'];
		$obj->pay_status = $row['pay_status'];
		$obj->pay_time = $row['pay_time'];
		$obj->order_id = $row['order_id'];
		$obj->money = $row['money'];
		$obj->start_time = $row['start_time'];
		$obj->expire_time = $row['expire_time'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(DirectorBuffEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`director_buff` %s(`id`,`user_id`,`type`,`pay_channel`,`pay_status`,`pay_time`,`order_id`,`money`,`start_time`,`expire_time`,`create_time`,`create_from`) VALUES (NULL,%d,%d,%d,%d,%d,'%s',%f,%d,%d,UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->user_id,$obj->type,$obj->pay_channel,$obj->pay_status,$obj->pay_time,self::_encode_string($obj->order_id,16)
			,$obj->money,$obj->start_time,$obj->expire_time,$obj->create_from);
	}
	private function _object_to_update(DirectorBuffEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`director_buff` %s SET `user_id` = %d,`type` = %d,`pay_channel` = %d,`pay_status` = %d,`pay_time` = %d,`order_id` = '%s',`money` = %f,`start_time` = %d,`expire_time` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->type,$obj->pay_channel,$obj->pay_status,$obj->pay_time,self::_encode_string($obj->order_id,16)
			,$obj->money,$obj->start_time,$obj->expire_time,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns DirectorBuffEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`director_buff`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`director_buff` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns DirectorBuffEntity
	 * @returns null
	 */
	public function load(int $id) : ?DirectorBuffEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`director_buff` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`director_buff` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns DirectorBuffEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?DirectorBuffEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`director_buff` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns DirectorBuffEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`director_buff` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 pay_status 加载一条
	 * @param	int  $pay_status  ..支付状态 0已关闭,1待支付,2已支付
	 * @returns DirectorBuffEntity
	 * @returns null
	 */
	public function loadOneByPayStatus (int $pay_status) : ?DirectorBuffEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`director_buff` WHERE `pay_status` = '%d'",
			$pay_status
		));
		
	}
	/**
	 * 根据普通索引 pay_status 加载全部
	 * @param	int	$pay_status	..支付状态 0已关闭,1待支付,2已支付
	 * @returns DirectorBuffEntity
	 * @returns null
	 */
	public function loadAllByPayStatus (int $pay_status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`director_buff` WHERE `pay_status` = '%d'",
			$pay_status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.director_buff 插入一条新纪录
	 * @param	DirectorBuffEntity    $obj    ..经理身份状态
	 * @returns bool
	 */
	public function insert(DirectorBuffEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.director_buff 回写一条记录<br>
	 * 更新依据： yuemi_main.director_buff.id
	 * @param	DirectorBuffEntity	  $obj    ..经理身份状态
	 * @returns bool
	 */
	 public function update(DirectorBuffEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 经理账户
 * @table director_finance
 * @engine innodb
 */
final class DirectorFinanceEntity extends \Ziima\Data\Entity {
	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 间接招聘佣金
	 * @var float
	 */
	public $recruit_self = null;

	/**
	 * 团队管理佣金
	 * @var float
	 */
	public $deduct_self = null;

	/**
	 * 伯乐奖/招聘佣金部分
	 * @var float
	 */
	public $recruit_bole = null;

	/**
	 * 伯乐奖/销售佣金部分
	 * @var float
	 */
	public $deduct_bole = null;

	/**
	 * 礼包佣金是否解冻
	 * @var int
	 * @default	0
	 */
	public $thew_status = 0;

	/**
	 * 礼包佣金解冻时间
	 * @var int
	 * @default	0
	 */
	public $thew_time = 0;
}
/**
 * DirectorFinanceEntity Factory<br>
 * 经理账户
 */
final class DirectorFinanceFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var DirectorFinanceFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : DirectorFinanceFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new DirectorFinanceFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new DirectorFinanceFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`director_finance`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`director_finance` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : DirectorFinanceEntity {
		$obj = new DirectorFinanceEntity();$obj->user_id = $row['user_id'];
		$obj->recruit_self = $row['recruit_self'];
		$obj->deduct_self = $row['deduct_self'];
		$obj->recruit_bole = $row['recruit_bole'];
		$obj->deduct_bole = $row['deduct_bole'];
		$obj->thew_status = $row['thew_status'];
		$obj->thew_time = $row['thew_time'];
		return $obj;
	}

	private function _object_to_insert(DirectorFinanceEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`director_finance` %s(`user_id`,`recruit_self`,`deduct_self`,`recruit_bole`,`deduct_bole`,`thew_status`,`thew_time`) VALUES (%d,%f,%f,%f,%f,%d,%d)";
		return sprintf($sql,'',$obj->user_id,$obj->recruit_self,$obj->deduct_self,$obj->recruit_bole,$obj->deduct_bole,$obj->thew_status,$obj->thew_time);
	}
	private function _object_to_update(DirectorFinanceEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`director_finance` %s SET `recruit_self` = %f,`deduct_self` = %f,`recruit_bole` = %f,`deduct_bole` = %f,`thew_status` = %d,`thew_time` = %d WHERE `user_id` = %d";
		
		return sprintf($sql,'',$obj->recruit_self,$obj->deduct_self,$obj->recruit_bole,$obj->deduct_bole,$obj->thew_status,$obj->thew_time,$obj->user_id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns DirectorFinanceEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`director_finance`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`director_finance` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "user_id" 加载一条
	 * @param	int	$user_id	..用户ID
	 * @returns DirectorFinanceEntity
	 * @returns null
	 */
	public function load(int $user_id) : ?DirectorFinanceEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`director_finance` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据主键 "user_id" 删除一条
	 * @param	int	$user_id	..用户ID
	 * @returns bool
	 */
	public function delete(int $user_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`director_finance` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 向数据表 yuemi_main.director_finance 插入一条新纪录
	 * @param	DirectorFinanceEntity    $obj    ..经理账户
	 * @returns bool
	 */
	public function insert(DirectorFinanceEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.director_finance 回写一条记录<br>
	 * 更新依据： yuemi_main.director_finance.user_id
	 * @param	DirectorFinanceEntity	  $obj    ..经理账户
	 * @returns bool
	 */
	 public function update(DirectorFinanceEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 邀请模板
 * @table invite_template
 * @engine innodb
 */
final class InviteTemplateEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 模板名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 底图路径
	 * @var string
	 */
	public $body_path = null;

	/**
	 * 底图预览URL
	 * @var string
	 */
	public $body_url = null;

	/**
	 * 底图宽度
	 * @var int
	 */
	public $body_width = null;

	/**
	 * 底图高度
	 * @var int
	 */
	public $body_height = null;

	/**
	 * 是否显示姓名
	 * @var int
	 * @default	1
	 */
	public $name_enable = 1;

	/**
	 * 姓名显示位置X
	 * @var int
	 * @default	0
	 */
	public $name_x = 0;

	/**
	 * 姓名显示位置Y
	 * @var int
	 * @default	0
	 */
	public $name_y = 0;

	/**
	 * 姓名显示字体大小
	 * @var int
	 * @default	16
	 */
	public $name_size = 16;

	/**
	 * 姓名显示字体颜色
	 * @var string
	 * @default	#000000
	 */
	public $name_color = '#000000';

	/**
	 * 是否显示邀请码
	 * @var int
	 * @default	1
	 */
	public $code_enable = 1;

	/**
	 * 邀请码显示位置X
	 * @var int
	 * @default	0
	 */
	public $code_x = 0;

	/**
	 * 邀请码显示位置Y
	 * @var int
	 * @default	0
	 */
	public $code_y = 0;

	/**
	 * 邀请码显示字体大小
	 * @var int
	 * @default	24
	 */
	public $code_size = 24;

	/**
	 * 邀请码显示字体颜色
	 * @var string
	 * @default	#000000
	 */
	public $code_color = '#000000';

	/**
	 * 二维码显示位置X
	 * @var int
	 * @default	0
	 */
	public $qr_x = 0;

	/**
	 * 二维码显示位置Y
	 * @var int
	 * @default	0
	 */
	public $qr_y = 0;

	/**
	 * 二维码显示位置宽度
	 * @var int
	 * @default	128
	 */
	public $qr_w = 128;

	/**
	 * 二维码显示位置高度
	 * @var int
	 * @default	128
	 */
	public $qr_h = 128;

	/**
	 * 是否显示头像
	 * @var int
	 * @default	1
	 */
	public $avatar_enable = 1;

	/**
	 * 头像显示位置X
	 * @var int
	 * @default	0
	 */
	public $avatar_x = 0;

	/**
	 * 头像码显示位置Y
	 * @var int
	 * @default	0
	 */
	public $avatar_y = 0;

	/**
	 * 头像码显示位置宽度
	 * @var int
	 * @default	128
	 */
	public $avatar_w = 128;

	/**
	 * 头像码显示位置高度
	 * @var int
	 * @default	128
	 */
	public $avatar_h = 128;

	/**
	 * 模板状态：0停用,1草稿,2启用
	 * @var int
	 * @default	1
	 */
	public $status = 1;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 更新时间
	 * @var int
	 * @default	0
	 */
	public $update_time = 0;

	/**
	 * 创建人
	 * @var int
	 * @default	0
	 */
	public $create_user = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * InviteTemplateEntity Factory<br>
 * 邀请模板
 */
final class InviteTemplateFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var InviteTemplateFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : InviteTemplateFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new InviteTemplateFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new InviteTemplateFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`invite_template`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`invite_template` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : InviteTemplateEntity {
		$obj = new InviteTemplateEntity();$obj->id = $row['id'];
		$obj->name = $row['name'];
		$obj->body_path = $row['body_path'];
		$obj->body_url = $row['body_url'];
		$obj->body_width = $row['body_width'];
		$obj->body_height = $row['body_height'];
		$obj->name_enable = $row['name_enable'];
		$obj->name_x = $row['name_x'];
		$obj->name_y = $row['name_y'];
		$obj->name_size = $row['name_size'];
		$obj->name_color = $row['name_color'];
		$obj->code_enable = $row['code_enable'];
		$obj->code_x = $row['code_x'];
		$obj->code_y = $row['code_y'];
		$obj->code_size = $row['code_size'];
		$obj->code_color = $row['code_color'];
		$obj->qr_x = $row['qr_x'];
		$obj->qr_y = $row['qr_y'];
		$obj->qr_w = $row['qr_w'];
		$obj->qr_h = $row['qr_h'];
		$obj->avatar_enable = $row['avatar_enable'];
		$obj->avatar_x = $row['avatar_x'];
		$obj->avatar_y = $row['avatar_y'];
		$obj->avatar_w = $row['avatar_w'];
		$obj->avatar_h = $row['avatar_h'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->update_time = $row['update_time'];
		$obj->create_user = $row['create_user'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(InviteTemplateEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`invite_template` %s(`id`,`name`,`body_path`,`body_url`,`body_width`,`body_height`,`name_enable`,`name_x`,`name_y`,`name_size`,`name_color`,`code_enable`,`code_x`,`code_y`,`code_size`,`code_color`,`qr_x`,`qr_y`,`qr_w`,`qr_h`,`avatar_enable`,`avatar_x`,`avatar_y`,`avatar_w`,`avatar_h`,`status`,`create_time`,`update_time`,`create_user`,`create_from`) VALUES (NULL,'%s','%s','%s',%d,%d,%d,%d,%d,%d,'%s',%d,%d,%d,%d,'%s',%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),%d,%d)";
		return sprintf($sql,'',self::_encode_string($obj->name,32)
			,self::_encode_string($obj->body_path,256)
			,self::_encode_string($obj->body_url,256)
			,$obj->body_width,$obj->body_height,$obj->name_enable,$obj->name_x,$obj->name_y,$obj->name_size,self::_encode_string($obj->name_color,8)
			,$obj->code_enable,$obj->code_x,$obj->code_y,$obj->code_size,self::_encode_string($obj->code_color,8)
			,$obj->qr_x,$obj->qr_y,$obj->qr_w,$obj->qr_h,$obj->avatar_enable,$obj->avatar_x,$obj->avatar_y,$obj->avatar_w,$obj->avatar_h,$obj->status,$obj->create_user,$obj->create_from);
	}
	private function _object_to_update(InviteTemplateEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`invite_template` %s SET `name` = '%s',`body_path` = '%s',`body_url` = '%s',`body_width` = %d,`body_height` = %d,`name_enable` = %d,`name_x` = %d,`name_y` = %d,`name_size` = %d,`name_color` = '%s',`code_enable` = %d,`code_x` = %d,`code_y` = %d,`code_size` = %d,`code_color` = '%s',`qr_x` = %d,`qr_y` = %d,`qr_w` = %d,`qr_h` = %d,`avatar_enable` = %d,`avatar_x` = %d,`avatar_y` = %d,`avatar_w` = %d,`avatar_h` = %d,`status` = %d,`update_time` = UNIX_TIMESTAMP(),`create_user` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->name,32)
			,self::_encode_string($obj->body_path,256)
			,self::_encode_string($obj->body_url,256)
			,$obj->body_width,$obj->body_height,$obj->name_enable,$obj->name_x,$obj->name_y,$obj->name_size,self::_encode_string($obj->name_color,8)
			,$obj->code_enable,$obj->code_x,$obj->code_y,$obj->code_size,self::_encode_string($obj->code_color,8)
			,$obj->qr_x,$obj->qr_y,$obj->qr_w,$obj->qr_h,$obj->avatar_enable,$obj->avatar_x,$obj->avatar_y,$obj->avatar_w,$obj->avatar_h,$obj->status,$obj->create_user,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns InviteTemplateEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`invite_template`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`invite_template` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns InviteTemplateEntity
	 * @returns null
	 */
	public function load(int $id) : ?InviteTemplateEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`invite_template` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`invite_template` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 向数据表 yuemi_main.invite_template 插入一条新纪录
	 * @param	InviteTemplateEntity    $obj    ..邀请模板
	 * @returns bool
	 */
	public function insert(InviteTemplateEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.invite_template 回写一条记录<br>
	 * 更新依据： yuemi_main.invite_template.id
	 * @param	InviteTemplateEntity	  $obj    ..邀请模板
	 * @returns bool
	 */
	 public function update(InviteTemplateEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 私信
 * @table mail
 * @engine innodb
 */
final class MailEntity extends \Ziima\Data\Entity {
	/**
	 * 邮件ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 发送人ID
	 * @var int
	 * @default	0
	 */
	public $sender_id = 0;

	/**
	 * 发送人昵称
	 * @var string
	 */
	public $sender_name = null;

	/**
	 * 接收人ID
	 * @var int
	 * @default	0
	 */
	public $reciver_id = 0;

	/**
	 * 发送人昵称
	 * @var string
	 */
	public $reciver_name = null;

	/**
	 * 公告标题
	 * @var string
	 */
	public $title = null;

	/**
	 * 公告内容
	 * @var string
	 */
	public $content = null;

	/**
	 * 邮件状态 0=草稿,1=发送,2=已读,3=删除
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 发布时间
	 * @var string
	 */
	public $create_time = null;

	/**
	 * 发布IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 阅读时间
	 * @var int
	 * @default	0
	 */
	public $recive_time = 0;

	/**
	 * 阅读IP
	 * @var int
	 * @default	0
	 */
	public $recive_from = 0;
}
/**
 * MailEntity Factory<br>
 * 私信
 */
final class MailFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var MailFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : MailFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new MailFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new MailFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`mail`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`mail` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : MailEntity {
		$obj = new MailEntity();$obj->id = $row['id'];
		$obj->sender_id = $row['sender_id'];
		$obj->sender_name = $row['sender_name'];
		$obj->reciver_id = $row['reciver_id'];
		$obj->reciver_name = $row['reciver_name'];
		$obj->title = $row['title'];
		$obj->content = $row['content'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->recive_time = $row['recive_time'];
		$obj->recive_from = $row['recive_from'];
		return $obj;
	}

	private function _object_to_insert(MailEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`mail` %s(`id`,`sender_id`,`sender_name`,`reciver_id`,`reciver_name`,`title`,`content`,`status`,`create_time`,`create_from`,`recive_time`,`recive_from`) VALUES (NULL,%d,'%s',%d,'%s','%s','%s',%d,NOW(),%d,%d,%d)";
		return sprintf($sql,'',$obj->sender_id,self::_encode_string($obj->sender_name,32)
			,$obj->reciver_id,self::_encode_string($obj->reciver_name,32)
			,self::_encode_string($obj->title,64)
			,self::_encode_string($obj->content,65535)
			,$obj->status,$obj->create_from,$obj->recive_time,$obj->recive_from);
	}
	private function _object_to_update(MailEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`mail` %s SET `sender_id` = %d,`sender_name` = '%s',`reciver_id` = %d,`reciver_name` = '%s',`title` = '%s',`content` = '%s',`status` = %d,`create_from` = %d,`recive_time` = %d,`recive_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->sender_id,self::_encode_string($obj->sender_name,32)
			,$obj->reciver_id,self::_encode_string($obj->reciver_name,32)
			,self::_encode_string($obj->title,64)
			,self::_encode_string($obj->content,65535)
			,$obj->status,$obj->create_from,$obj->recive_time,$obj->recive_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns MailEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`mail`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`mail` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..邮件ID
	 * @returns MailEntity
	 * @returns null
	 */
	public function load(int $id) : ?MailEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`mail` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..邮件ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`mail` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 sender_id 加载一条
	 * @param	int  $sender_id  ..发送人ID
	 * @returns MailEntity
	 * @returns null
	 */
	public function loadOneBySenderId (int $sender_id) : ?MailEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`mail` WHERE `sender_id` = '%d'",
			$sender_id
		));
		
	}
	/**
	 * 根据普通索引 sender_id 加载全部
	 * @param	int	$sender_id	..发送人ID
	 * @returns MailEntity
	 * @returns null
	 */
	public function loadAllBySenderId (int $sender_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`mail` WHERE `sender_id` = '%d'",
			$sender_id
		));
		
	}

	/**
	 * 根据普通索引 reciver_id 加载一条
	 * @param	int  $reciver_id  ..接收人ID
	 * @returns MailEntity
	 * @returns null
	 */
	public function loadOneByReciverId (int $reciver_id) : ?MailEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`mail` WHERE `reciver_id` = '%d'",
			$reciver_id
		));
		
	}
	/**
	 * 根据普通索引 reciver_id 加载全部
	 * @param	int	$reciver_id	..接收人ID
	 * @returns MailEntity
	 * @returns null
	 */
	public function loadAllByReciverId (int $reciver_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`mail` WHERE `reciver_id` = '%d'",
			$reciver_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..邮件状态 0=草稿,1=发送,2=已读,3=删除
	 * @returns MailEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?MailEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`mail` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..邮件状态 0=草稿,1=发送,2=已读,3=删除
	 * @returns MailEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`mail` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.mail 插入一条新纪录
	 * @param	MailEntity    $obj    ..私信
	 * @returns bool
	 */
	public function insert(MailEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.mail 回写一条记录<br>
	 * 更新依据： yuemi_main.mail.id
	 * @param	MailEntity	  $obj    ..私信
	 * @returns bool
	 */
	 public function update(MailEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 系统公告
 * @table notice
 * @engine innodb
 */
final class NoticeEntity extends \Ziima\Data\Entity {
	/**
	 * 公告ID
	 * @var string
	 */
	public $id = null;

	/**
	 * 公告范围：0全体,1用户,2VIP,3总监,4经理,5供应商,6员工,7管理员
	 * @var int
	 * @default	0
	 */
	public $scope = 0;

	/**
	 * 范围ID
	 * @var int
	 * @default	0
	 */
	public $scope_id = 0;

	/**
	 * 公告标题
	 * @var string
	 */
	public $title = null;

	/**
	 * 公告内容
	 * @var string
	 */
	public $content = null;

	/**
	 * 公开时间
	 * @var string
	 */
	public $open_time = null;

	/**
	 * 关闭时间
	 * @var string
	 */
	public $close_time = null;

	/**
	 * 认证状态 0=草稿,1=待审,2=审核,3=关闭
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 发布人
	 * @var int
	 * @default	0
	 */
	public $create_user = 0;

	/**
	 * 发布时间
	 * @var string
	 */
	public $create_time = null;

	/**
	 * 发布IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 审核人
	 * @var int
	 * @default	0
	 */
	public $audit_user = 0;

	/**
	 * 审核时间
	 * @var string
	 */
	public $audit_time = null;

	/**
	 * 审核IP
	 * @var int
	 * @default	0
	 */
	public $audit_from = 0;
}
/**
 * NoticeEntity Factory<br>
 * 系统公告
 */
final class NoticeFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var NoticeFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : NoticeFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new NoticeFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new NoticeFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`notice`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`notice` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : NoticeEntity {
		$obj = new NoticeEntity();$obj->id = $row['id'];
		$obj->scope = $row['scope'];
		$obj->scope_id = $row['scope_id'];
		$obj->title = $row['title'];
		$obj->content = $row['content'];
		$obj->open_time = $row['open_time'];
		$obj->close_time = $row['close_time'];
		$obj->status = $row['status'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(NoticeEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`notice` %s(`id`,`scope`,`scope_id`,`title`,`content`,`open_time`,`close_time`,`status`,`create_user`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`) VALUES ('%s',%d,%d,'%s','%s','%s','%s',%d,%d,NOW(),%d,%d,'%s',%d)";
		return sprintf($sql,'',self::_encode_string($obj->id,18)
			,$obj->scope,$obj->scope_id,self::_encode_string($obj->title,64)
			,self::_encode_string($obj->content,65535)
			,$obj->open_time,$obj->close_time,$obj->status,$obj->create_user,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(NoticeEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`notice` %s SET `scope` = %d,`scope_id` = %d,`title` = '%s',`content` = '%s',`open_time` = '%s',`close_time` = '%s',`status` = %d,`create_user` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = '%s',`audit_from` = %d WHERE `id` = '%s'";
		
		return sprintf($sql,'',$obj->scope,$obj->scope_id,self::_encode_string($obj->title,64)
			,self::_encode_string($obj->content,65535)
			,$obj->open_time,$obj->close_time,$obj->status,$obj->create_user,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,parent::$reader->escape_string($obj->id)
			);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns NoticeEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`notice`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`notice` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "id" 加载一条
	 * @param	string	$id	..公告ID
	 * @returns NoticeEntity
	 * @returns null
	 */
	public function load(string $id) : ?NoticeEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`notice` WHERE `id` = '%s'",
			parent::$reader->escape_string($id)
		));
		
	}
	
	/**
	 * 根据主键 "id" 删除一条
	 * @param	string	$id	..公告ID
	 * @returns bool
	 */
	public function delete(string $id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`notice` WHERE `id` = '%s'",
			parent::$reader->escape_string($id)
		));
		
	}
	
	/**
	 * 根据普通索引 scope 加载一条
	 * @param	int  $scope  ..公告范围：0全体,1用户,2VIP,3总监,4经理,5供应商,6员工,7管理员
	 * @returns NoticeEntity
	 * @returns null
	 */
	public function loadOneByScope (int $scope) : ?NoticeEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`notice` WHERE `scope` = '%d'",
			$scope
		));
		
	}
	/**
	 * 根据普通索引 scope 加载全部
	 * @param	int	$scope	..公告范围：0全体,1用户,2VIP,3总监,4经理,5供应商,6员工,7管理员
	 * @returns NoticeEntity
	 * @returns null
	 */
	public function loadAllByScope (int $scope) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`notice` WHERE `scope` = '%d'",
			$scope
		));
		
	}

	/**
	 * 根据普通索引 scope_id 加载一条
	 * @param	int  $scope_id  ..范围ID
	 * @returns NoticeEntity
	 * @returns null
	 */
	public function loadOneByScopeId (int $scope_id) : ?NoticeEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`notice` WHERE `scope_id` = '%d'",
			$scope_id
		));
		
	}
	/**
	 * 根据普通索引 scope_id 加载全部
	 * @param	int	$scope_id	..范围ID
	 * @returns NoticeEntity
	 * @returns null
	 */
	public function loadAllByScopeId (int $scope_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`notice` WHERE `scope_id` = '%d'",
			$scope_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..认证状态 0=草稿,1=待审,2=审核,3=关闭
	 * @returns NoticeEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?NoticeEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`notice` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..认证状态 0=草稿,1=待审,2=审核,3=关闭
	 * @returns NoticeEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`notice` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.notice 插入一条新纪录
	 * @param	NoticeEntity    $obj    ..系统公告
	 * @returns bool
	 */
	public function insert(NoticeEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.notice 回写一条记录<br>
	 * 更新依据： yuemi_main.notice.id
	 * @param	NoticeEntity	  $obj    ..系统公告
	 * @returns bool
	 */
	 public function update(NoticeEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 管理员
 * @table rbac_admin
 * @engine innodb
 */
final class RbacAdminEntity extends \Ziima\Data\Entity {
	/**
	 * 用户ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 前台用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 角色ID
	 * @var int
	 * @default	0
	 */
	public $role_id = 0;

	/**
	 * IM系统账号，前缀：a_
	 * @var string
	 */
	public $imuid = null;

	/**
	 * 二次操作密码
	 * @var string
	 */
	public $password = null;

	/**
	 * 登陆令牌
	 * @var string
	 */
	public $token = null;

	/**
	 * 管理员状态：0=禁止，1=允许
	 * @var int
	 * @default	1
	 */
	public $status = 1;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * RbacAdminEntity Factory<br>
 * 管理员
 */
final class RbacAdminFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var RbacAdminFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : RbacAdminFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new RbacAdminFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new RbacAdminFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`rbac_admin`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`rbac_admin` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : RbacAdminEntity {
		$obj = new RbacAdminEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->role_id = $row['role_id'];
		$obj->imuid = $row['imuid'];
		$obj->password = $row['password'];
		$obj->token = $row['token'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(RbacAdminEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`rbac_admin` %s(`id`,`user_id`,`role_id`,`imuid`,`password`,`token`,`status`,`create_time`,`create_from`) VALUES (NULL,%d,%d,'%s','%s','%s',%d,UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->user_id,$obj->role_id,self::_encode_string($obj->imuid,24)
			,self::_encode_string($obj->password,40)
			,self::_encode_string($obj->token,16)
			,$obj->status,$obj->create_from);
	}
	private function _object_to_update(RbacAdminEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`rbac_admin` %s SET `user_id` = %d,`role_id` = %d,`imuid` = '%s',`password` = '%s',`token` = '%s',`status` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->role_id,self::_encode_string($obj->imuid,24)
			,self::_encode_string($obj->password,40)
			,self::_encode_string($obj->token,16)
			,$obj->status,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns RbacAdminEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`rbac_admin`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`rbac_admin` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..用户ID
	 * @returns RbacAdminEntity
	 * @returns null
	 */
	public function load(int $id) : ?RbacAdminEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`rbac_admin` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..用户ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`rbac_admin` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 user_id 加载
	 * @param	int	$user_id	..前台用户ID
	 * @returns RbacAdminEntity
	 * @returns null
	 */
	public function loadByUserId (int $user_id) : ?RbacAdminEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`rbac_admin` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据唯一索引 "user_id" 删除一条
	 * @param	int	$user_id	..前台用户ID
	 * @returns bool
	 */
	public function deleteByUserId(int $user_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`rbac_admin` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据普通索引 token 加载一条
	 * @param	string  $token  ..登陆令牌
	 * @returns RbacAdminEntity
	 * @returns null
	 */
	public function loadOneByToken (string $token) : ?RbacAdminEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`rbac_admin` WHERE `token` = '%s'",
			parent::$reader->escape_string($token)
		));
		
	}
	/**
	 * 根据普通索引 token 加载全部
	 * @param	string	$token	..登陆令牌
	 * @returns RbacAdminEntity
	 * @returns null
	 */
	public function loadAllByToken (string $token) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`rbac_admin` WHERE `token` = '%s'",
			parent::$reader->escape_string($token)
		));
		
	}

	/**
	 * 向数据表 yuemi_main.rbac_admin 插入一条新纪录
	 * @param	RbacAdminEntity    $obj    ..管理员
	 * @returns bool
	 */
	public function insert(RbacAdminEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.rbac_admin 回写一条记录<br>
	 * 更新依据： yuemi_main.rbac_admin.id
	 * @param	RbacAdminEntity	  $obj    ..管理员
	 * @returns bool
	 */
	 public function update(RbacAdminEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 管理角色
 * @table rbac_role
 * @engine innodb
 */
final class RbacRoleEntity extends \Ziima\Data\Entity {
	/**
	 * 角色ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 上级角色ID
	 * @var int
	 * @default	0
	 */
	public $parent_id = 0;

	/**
	 * 角色名称
	 * @var string
	 */
	public $name = null;
}
/**
 * RbacRoleEntity Factory<br>
 * 管理角色
 */
final class RbacRoleFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var RbacRoleFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : RbacRoleFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new RbacRoleFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new RbacRoleFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`rbac_role`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`rbac_role` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : RbacRoleEntity {
		$obj = new RbacRoleEntity();$obj->id = $row['id'];
		$obj->parent_id = $row['parent_id'];
		$obj->name = $row['name'];
		return $obj;
	}

	private function _object_to_insert(RbacRoleEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`rbac_role` %s(`id`,`parent_id`,`name`) VALUES (NULL,%d,'%s')";
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			);
	}
	private function _object_to_update(RbacRoleEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`rbac_role` %s SET `parent_id` = %d,`name` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns RbacRoleEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`rbac_role`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`rbac_role` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..角色ID
	 * @returns RbacRoleEntity
	 * @returns null
	 */
	public function load(int $id) : ?RbacRoleEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`rbac_role` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..角色ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`rbac_role` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 parent_id 加载一条
	 * @param	int  $parent_id  ..上级角色ID
	 * @returns RbacRoleEntity
	 * @returns null
	 */
	public function loadOneByParentId (int $parent_id) : ?RbacRoleEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`rbac_role` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}
	/**
	 * 根据普通索引 parent_id 加载全部
	 * @param	int	$parent_id	..上级角色ID
	 * @returns RbacRoleEntity
	 * @returns null
	 */
	public function loadAllByParentId (int $parent_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`rbac_role` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.rbac_role 插入一条新纪录
	 * @param	RbacRoleEntity    $obj    ..管理角色
	 * @returns bool
	 */
	public function insert(RbacRoleEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.rbac_role 回写一条记录<br>
	 * 更新依据： yuemi_main.rbac_role.id
	 * @param	RbacRoleEntity	  $obj    ..管理角色
	 * @returns bool
	 */
	 public function update(RbacRoleEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 运营模块配置
 * @table run_block
 * @engine innodb
 */
final class RunBlockEntity extends \Ziima\Data\Entity {
	/**
	 * 模块ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 页面ID
	 * @var int
	 * @default	0
	 */
	public $page_id = 0;

	/**
	 * 模块名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 模块代号
	 * @var string
	 */
	public $alias = null;

	/**
	 * 组件数据格式：0自定义,1单品,2多品
	 * @var int
	 */
	public $source_type = null;

	/**
	 * 尺寸模式：0自适应,1指定像素,2百分比
	 * @var int
	 * @default	0
	 */
	public $sizer = 0;

	/**
	 * 模块宽度
	 * @var int
	 * @default	0
	 */
	public $width = 0;

	/**
	 * 模块高度
	 * @var int
	 * @default	0
	 */
	public $height = 0;

	/**
	 * 数据容量
	 * @var int
	 * @default	0
	 */
	public $capacity = 0;

	/**
	 * 默认组件ID
	 * @var int
	 * @default	0
	 */
	public $widget_id = 0;

	/**
	 * 默认数据源ID
	 * @var int
	 * @default	0
	 */
	public $source_id = 0;

	/**
	 * 模块预览图,BASE64
	 * @var string
	 */
	public $preview = null;
}
/**
 * RunBlockEntity Factory<br>
 * 运营模块配置
 */
final class RunBlockFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var RunBlockFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : RunBlockFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new RunBlockFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new RunBlockFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_block`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_block` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : RunBlockEntity {
		$obj = new RunBlockEntity();$obj->id = $row['id'];
		$obj->page_id = $row['page_id'];
		$obj->name = $row['name'];
		$obj->alias = $row['alias'];
		$obj->source_type = $row['source_type'];
		$obj->sizer = $row['sizer'];
		$obj->width = $row['width'];
		$obj->height = $row['height'];
		$obj->capacity = $row['capacity'];
		$obj->widget_id = $row['widget_id'];
		$obj->source_id = $row['source_id'];
		$obj->preview = $row['preview'];
		return $obj;
	}

	private function _object_to_insert(RunBlockEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`run_block` %s(`id`,`page_id`,`name`,`alias`,`source_type`,`sizer`,`width`,`height`,`capacity`,`widget_id`,`source_id`,`preview`) VALUES (NULL,%d,'%s','%s',%d,%d,%d,%d,%d,%d,%d,'%s')";
		return sprintf($sql,'',$obj->page_id,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,$obj->source_type,$obj->sizer,$obj->width,$obj->height,$obj->capacity,$obj->widget_id,$obj->source_id,self::_encode_string($obj->preview,65535)
			);
	}
	private function _object_to_update(RunBlockEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`run_block` %s SET `page_id` = %d,`name` = '%s',`alias` = '%s',`source_type` = %d,`sizer` = %d,`width` = %d,`height` = %d,`capacity` = %d,`widget_id` = %d,`source_id` = %d,`preview` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->page_id,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,$obj->source_type,$obj->sizer,$obj->width,$obj->height,$obj->capacity,$obj->widget_id,$obj->source_id,self::_encode_string($obj->preview,65535)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns RunBlockEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`run_block`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`run_block` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..模块ID
	 * @returns RunBlockEntity
	 * @returns null
	 */
	public function load(int $id) : ?RunBlockEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_block` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..模块ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`run_block` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 page_id 加载一条
	 * @param	int  $page_id  ..页面ID
	 * @returns RunBlockEntity
	 * @returns null
	 */
	public function loadOneByPageId (int $page_id) : ?RunBlockEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_block` WHERE `page_id` = '%d'",
			$page_id
		));
		
	}
	/**
	 * 根据普通索引 page_id 加载全部
	 * @param	int	$page_id	..页面ID
	 * @returns RunBlockEntity
	 * @returns null
	 */
	public function loadAllByPageId (int $page_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_block` WHERE `page_id` = '%d'",
			$page_id
		));
		
	}

	/**
	 * 根据普通索引 alias 加载一条
	 * @param	string  $alias  ..模块代号
	 * @returns RunBlockEntity
	 * @returns null
	 */
	public function loadOneByAlias (string $alias) : ?RunBlockEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_block` WHERE `alias` = '%s'",
			parent::$reader->escape_string($alias)
		));
		
	}
	/**
	 * 根据普通索引 alias 加载全部
	 * @param	string	$alias	..模块代号
	 * @returns RunBlockEntity
	 * @returns null
	 */
	public function loadAllByAlias (string $alias) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_block` WHERE `alias` = '%s'",
			parent::$reader->escape_string($alias)
		));
		
	}

	/**
	 * 向数据表 yuemi_main.run_block 插入一条新纪录
	 * @param	RunBlockEntity    $obj    ..运营模块配置
	 * @returns bool
	 */
	public function insert(RunBlockEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.run_block 回写一条记录<br>
	 * 更新依据： yuemi_main.run_block.id
	 * @param	RunBlockEntity	  $obj    ..运营模块配置
	 * @returns bool
	 */
	 public function update(RunBlockEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 用户素材
 * @table run_material
 * @engine innodb
 */
final class RunMaterialEntity extends \Ziima\Data\Entity {
	/**
	 * 素材ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 页面ID
	 * @var int
	 */
	public $page_id = null;

	/**
	 * 文件名
	 * @var string
	 */
	public $file_name = null;

	/**
	 * 文件大小：字节
	 * @var int
	 * @default	0
	 */
	public $file_size = 0;

	/**
	 * 访问路径
	 * @var string
	 */
	public $file_url = null;

	/**
	 * 图片宽度
	 * @var int
	 * @default	0
	 */
	public $image_width = 0;

	/**
	 * 图片高度
	 * @var int
	 * @default	0
	 */
	public $image_height = 0;

	/**
	 * 缩略图路径
	 * @var string
	 */
	public $thumb_url = null;

	/**
	 * 缩略图大小：字节
	 * @var int
	 * @default	0
	 */
	public $thumb_size = 0;

	/**
	 * 缩略图宽度
	 * @var int
	 * @default	0
	 */
	public $thumb_width = 0;

	/**
	 * 缩略图高度
	 * @var int
	 * @default	0
	 */
	public $thumb_height = 0;

	/**
	 * 素材状态 0待审,1已审,2删除
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 审核人
	 * @var int
	 * @default	0
	 */
	public $audit_user = 0;

	/**
	 * 审核时间
	 * @var int
	 * @default	0
	 */
	public $audit_time = 0;

	/**
	 * 审核IP
	 * @var int
	 * @default	0
	 */
	public $audit_from = 0;
}
/**
 * RunMaterialEntity Factory<br>
 * 用户素材
 */
final class RunMaterialFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var RunMaterialFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : RunMaterialFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new RunMaterialFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new RunMaterialFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_material`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_material` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : RunMaterialEntity {
		$obj = new RunMaterialEntity();$obj->id = $row['id'];
		$obj->page_id = $row['page_id'];
		$obj->file_name = $row['file_name'];
		$obj->file_size = $row['file_size'];
		$obj->file_url = $row['file_url'];
		$obj->image_width = $row['image_width'];
		$obj->image_height = $row['image_height'];
		$obj->thumb_url = $row['thumb_url'];
		$obj->thumb_size = $row['thumb_size'];
		$obj->thumb_width = $row['thumb_width'];
		$obj->thumb_height = $row['thumb_height'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(RunMaterialEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`run_material` %s(`id`,`page_id`,`file_name`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`status`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`) VALUES (NULL,%d,'%s',%d,'%s',%d,%d,'%s',%d,%d,%d,%d,UNIX_TIMESTAMP(),%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->page_id,self::_encode_string($obj->file_name,64)
			,$obj->file_size,self::_encode_string($obj->file_url,512)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,512)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(RunMaterialEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`run_material` %s SET `page_id` = %d,`file_name` = '%s',`file_size` = %d,`file_url` = '%s',`image_width` = %d,`image_height` = %d,`thumb_url` = '%s',`thumb_size` = %d,`thumb_width` = %d,`thumb_height` = %d,`status` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->page_id,self::_encode_string($obj->file_name,64)
			,$obj->file_size,self::_encode_string($obj->file_url,512)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,512)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns RunMaterialEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`run_material`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`run_material` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..素材ID
	 * @returns RunMaterialEntity
	 * @returns null
	 */
	public function load(int $id) : ?RunMaterialEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_material` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..素材ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`run_material` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 page_id 加载一条
	 * @param	int  $page_id  ..页面ID
	 * @returns RunMaterialEntity
	 * @returns null
	 */
	public function loadOneByPageId (int $page_id) : ?RunMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_material` WHERE `page_id` = '%d'",
			$page_id
		));
		
	}
	/**
	 * 根据普通索引 page_id 加载全部
	 * @param	int	$page_id	..页面ID
	 * @returns RunMaterialEntity
	 * @returns null
	 */
	public function loadAllByPageId (int $page_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_material` WHERE `page_id` = '%d'",
			$page_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..素材状态 0待审,1已审,2删除
	 * @returns RunMaterialEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?RunMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_material` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..素材状态 0待审,1已审,2删除
	 * @returns RunMaterialEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_material` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.run_material 插入一条新纪录
	 * @param	RunMaterialEntity    $obj    ..用户素材
	 * @returns bool
	 */
	public function insert(RunMaterialEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.run_material 回写一条记录<br>
	 * 更新依据： yuemi_main.run_material.id
	 * @param	RunMaterialEntity	  $obj    ..用户素材
	 * @returns bool
	 */
	 public function update(RunMaterialEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 运营页面配置
 * @table run_page
 * @engine innodb
 */
final class RunPageEntity extends \Ziima\Data\Entity {
	/**
	 * 页面ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 上级页面ID
	 * @var int
	 * @default	0
	 */
	public $parent_id = 0;

	/**
	 * 页面名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 页面代号
	 * @var string
	 */
	public $alias = null;

	/**
	 * 页面类型：0静态,1动态
	 * @var int
	 */
	public $style = null;

	/**
	 * 模块代码，静态页面没有模板
	 * @var string
	 */
	public $template = null;
}
/**
 * RunPageEntity Factory<br>
 * 运营页面配置
 */
final class RunPageFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var RunPageFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : RunPageFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new RunPageFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new RunPageFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_page`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_page` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : RunPageEntity {
		$obj = new RunPageEntity();$obj->id = $row['id'];
		$obj->parent_id = $row['parent_id'];
		$obj->name = $row['name'];
		$obj->alias = $row['alias'];
		$obj->style = $row['style'];
		$obj->template = $row['template'];
		return $obj;
	}

	private function _object_to_insert(RunPageEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`run_page` %s(`id`,`parent_id`,`name`,`alias`,`style`,`template`) VALUES (NULL,%d,'%s','%s',%d,'%s')";
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,$obj->style,self::_encode_string($obj->template,65535)
			);
	}
	private function _object_to_update(RunPageEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`run_page` %s SET `parent_id` = %d,`name` = '%s',`alias` = '%s',`style` = %d,`template` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,$obj->style,self::_encode_string($obj->template,65535)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns RunPageEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`run_page`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`run_page` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..页面ID
	 * @returns RunPageEntity
	 * @returns null
	 */
	public function load(int $id) : ?RunPageEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_page` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..页面ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`run_page` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 parent_id 加载一条
	 * @param	int  $parent_id  ..上级页面ID
	 * @returns RunPageEntity
	 * @returns null
	 */
	public function loadOneByParentId (int $parent_id) : ?RunPageEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_page` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}
	/**
	 * 根据普通索引 parent_id 加载全部
	 * @param	int	$parent_id	..上级页面ID
	 * @returns RunPageEntity
	 * @returns null
	 */
	public function loadAllByParentId (int $parent_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_page` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}

	/**
	 * 根据普通索引 alias 加载一条
	 * @param	string  $alias  ..页面代号
	 * @returns RunPageEntity
	 * @returns null
	 */
	public function loadOneByAlias (string $alias) : ?RunPageEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_page` WHERE `alias` = '%s'",
			parent::$reader->escape_string($alias)
		));
		
	}
	/**
	 * 根据普通索引 alias 加载全部
	 * @param	string	$alias	..页面代号
	 * @returns RunPageEntity
	 * @returns null
	 */
	public function loadAllByAlias (string $alias) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_page` WHERE `alias` = '%s'",
			parent::$reader->escape_string($alias)
		));
		
	}

	/**
	 * 向数据表 yuemi_main.run_page 插入一条新纪录
	 * @param	RunPageEntity    $obj    ..运营页面配置
	 * @returns bool
	 */
	public function insert(RunPageEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.run_page 回写一条记录<br>
	 * 更新依据： yuemi_main.run_page.id
	 * @param	RunPageEntity	  $obj    ..运营页面配置
	 * @returns bool
	 */
	 public function update(RunPageEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 运营页面发布
 * @table run_release
 * @engine innodb
 */
final class RunReleaseEntity extends \Ziima\Data\Entity {
	/**
	 * 引用ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 页面ID
	 * @var int
	 * @default	0
	 */
	public $page_id = 0;

	/**
	 * 生成页面代码
	 * @var string
	 */
	public $html = null;
}
/**
 * RunReleaseEntity Factory<br>
 * 运营页面发布
 */
final class RunReleaseFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var RunReleaseFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : RunReleaseFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new RunReleaseFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new RunReleaseFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_release`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_release` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : RunReleaseEntity {
		$obj = new RunReleaseEntity();$obj->id = $row['id'];
		$obj->page_id = $row['page_id'];
		$obj->html = $row['html'];
		return $obj;
	}

	private function _object_to_insert(RunReleaseEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`run_release` %s(`id`,`page_id`,`html`) VALUES (NULL,%d,'%s')";
		return sprintf($sql,'',$obj->page_id,self::_encode_string($obj->html,65535)
			);
	}
	private function _object_to_update(RunReleaseEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`run_release` %s SET `page_id` = %d,`html` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->page_id,self::_encode_string($obj->html,65535)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns RunReleaseEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`run_release`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`run_release` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..引用ID
	 * @returns RunReleaseEntity
	 * @returns null
	 */
	public function load(int $id) : ?RunReleaseEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_release` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..引用ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`run_release` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 page_id 加载一条
	 * @param	int  $page_id  ..页面ID
	 * @returns RunReleaseEntity
	 * @returns null
	 */
	public function loadOneByPageId (int $page_id) : ?RunReleaseEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_release` WHERE `page_id` = '%d'",
			$page_id
		));
		
	}
	/**
	 * 根据普通索引 page_id 加载全部
	 * @param	int	$page_id	..页面ID
	 * @returns RunReleaseEntity
	 * @returns null
	 */
	public function loadAllByPageId (int $page_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_release` WHERE `page_id` = '%d'",
			$page_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.run_release 插入一条新纪录
	 * @param	RunReleaseEntity    $obj    ..运营页面发布
	 * @returns bool
	 */
	public function insert(RunReleaseEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.run_release 回写一条记录<br>
	 * 更新依据： yuemi_main.run_release.id
	 * @param	RunReleaseEntity	  $obj    ..运营页面发布
	 * @returns bool
	 */
	 public function update(RunReleaseEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 运营数据源配置
 * @table run_source
 * @engine innodb
 */
final class RunSourceEntity extends \Ziima\Data\Entity {
	/**
	 * 数据源ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 数据源名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 数据源代号
	 * @var string
	 */
	public $alias = null;

	/**
	 * 数据源类型：0=SQL,1=PHP,2=选品
	 * @var int
	 * @default	0
	 */
	public $style = 0;

	/**
	 * 数据源格式：0自定义,1单品,2多品
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 驱动代码/选品规则
	 * @var string
	 */
	public $driver = null;
}
/**
 * RunSourceEntity Factory<br>
 * 运营数据源配置
 */
final class RunSourceFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var RunSourceFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : RunSourceFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new RunSourceFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new RunSourceFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_source`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_source` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : RunSourceEntity {
		$obj = new RunSourceEntity();$obj->id = $row['id'];
		$obj->name = $row['name'];
		$obj->alias = $row['alias'];
		$obj->style = $row['style'];
		$obj->type = $row['type'];
		$obj->driver = $row['driver'];
		return $obj;
	}

	private function _object_to_insert(RunSourceEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`run_source` %s(`id`,`name`,`alias`,`style`,`type`,`driver`) VALUES (NULL,'%s','%s',%d,%d,'%s')";
		return sprintf($sql,'',self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,$obj->style,$obj->type,self::_encode_string($obj->driver,65535)
			);
	}
	private function _object_to_update(RunSourceEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`run_source` %s SET `name` = '%s',`alias` = '%s',`style` = %d,`type` = %d,`driver` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,$obj->style,$obj->type,self::_encode_string($obj->driver,65535)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns RunSourceEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`run_source`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`run_source` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..数据源ID
	 * @returns RunSourceEntity
	 * @returns null
	 */
	public function load(int $id) : ?RunSourceEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_source` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..数据源ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`run_source` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 alias 加载一条
	 * @param	string  $alias  ..数据源代号
	 * @returns RunSourceEntity
	 * @returns null
	 */
	public function loadOneByAlias (string $alias) : ?RunSourceEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_source` WHERE `alias` = '%s'",
			parent::$reader->escape_string($alias)
		));
		
	}
	/**
	 * 根据普通索引 alias 加载全部
	 * @param	string	$alias	..数据源代号
	 * @returns RunSourceEntity
	 * @returns null
	 */
	public function loadAllByAlias (string $alias) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_source` WHERE `alias` = '%s'",
			parent::$reader->escape_string($alias)
		));
		
	}

	/**
	 * 向数据表 yuemi_main.run_source 插入一条新纪录
	 * @param	RunSourceEntity    $obj    ..运营数据源配置
	 * @returns bool
	 */
	public function insert(RunSourceEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.run_source 回写一条记录<br>
	 * 更新依据： yuemi_main.run_source.id
	 * @param	RunSourceEntity	  $obj    ..运营数据源配置
	 * @returns bool
	 */
	 public function update(RunSourceEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 运营数据排期
 * @table run_usage
 * @engine innodb
 */
final class RunUsageEntity extends \Ziima\Data\Entity {
	/**
	 * 引用ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 页面ID
	 * @var int
	 * @default	0
	 */
	public $page_id = 0;

	/**
	 * 模块ID
	 * @var int
	 * @default	0
	 */
	public $block_id = 0;

	/**
	 * 组件ID
	 * @var int
	 * @default	0
	 */
	public $widget_id = 0;

	/**
	 * 数据源ID
	 * @var int
	 * @default	0
	 */
	public $source_id = 0;

	/**
	 * 第一参数
	 * @var string
	 */
	public $param_1 = null;

	/**
	 * 第二参数
	 * @var string
	 */
	public $param_2 = null;

	/**
	 * 第三参数
	 * @var string
	 */
	public $param_3 = null;

	/**
	 * 第四参数
	 * @var string
	 */
	public $param_4 = null;

	/**
	 * 第五参数
	 * @var string
	 */
	public $param_5 = null;

	/**
	 * 第六参数
	 * @var string
	 */
	public $param_6 = null;

	/**
	 * 第七参数
	 * @var string
	 */
	public $param_7 = null;

	/**
	 * 第八参数
	 * @var string
	 */
	public $param_8 = null;

	/**
	 * 第九参数
	 * @var string
	 */
	public $param_9 = null;

	/**
	 * 排期开始
	 * @var int
	 * @default	0
	 */
	public $schedule_start = 0;

	/**
	 * 排期结束
	 * @var int
	 * @default	0
	 */
	public $schedule_end = 0;
}
/**
 * RunUsageEntity Factory<br>
 * 运营数据排期
 */
final class RunUsageFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var RunUsageFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : RunUsageFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new RunUsageFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new RunUsageFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_usage`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_usage` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : RunUsageEntity {
		$obj = new RunUsageEntity();$obj->id = $row['id'];
		$obj->page_id = $row['page_id'];
		$obj->block_id = $row['block_id'];
		$obj->widget_id = $row['widget_id'];
		$obj->source_id = $row['source_id'];
		$obj->param_1 = $row['param_1'];
		$obj->param_2 = $row['param_2'];
		$obj->param_3 = $row['param_3'];
		$obj->param_4 = $row['param_4'];
		$obj->param_5 = $row['param_5'];
		$obj->param_6 = $row['param_6'];
		$obj->param_7 = $row['param_7'];
		$obj->param_8 = $row['param_8'];
		$obj->param_9 = $row['param_9'];
		$obj->schedule_start = $row['schedule_start'];
		$obj->schedule_end = $row['schedule_end'];
		return $obj;
	}

	private function _object_to_insert(RunUsageEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`run_usage` %s(`id`,`page_id`,`block_id`,`widget_id`,`source_id`,`param_1`,`param_2`,`param_3`,`param_4`,`param_5`,`param_6`,`param_7`,`param_8`,`param_9`,`schedule_start`,`schedule_end`) VALUES (NULL,%d,%d,%d,%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s',%d,%d)";
		return sprintf($sql,'',$obj->page_id,$obj->block_id,$obj->widget_id,$obj->source_id,self::_encode_string($obj->param_1,16)
			,self::_encode_string($obj->param_2,16)
			,self::_encode_string($obj->param_3,16)
			,self::_encode_string($obj->param_4,16)
			,self::_encode_string($obj->param_5,16)
			,self::_encode_string($obj->param_6,16)
			,self::_encode_string($obj->param_7,16)
			,self::_encode_string($obj->param_8,16)
			,self::_encode_string($obj->param_9,16)
			,$obj->schedule_start,$obj->schedule_end);
	}
	private function _object_to_update(RunUsageEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`run_usage` %s SET `page_id` = %d,`block_id` = %d,`widget_id` = %d,`source_id` = %d,`param_1` = '%s',`param_2` = '%s',`param_3` = '%s',`param_4` = '%s',`param_5` = '%s',`param_6` = '%s',`param_7` = '%s',`param_8` = '%s',`param_9` = '%s',`schedule_start` = %d,`schedule_end` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->page_id,$obj->block_id,$obj->widget_id,$obj->source_id,self::_encode_string($obj->param_1,16)
			,self::_encode_string($obj->param_2,16)
			,self::_encode_string($obj->param_3,16)
			,self::_encode_string($obj->param_4,16)
			,self::_encode_string($obj->param_5,16)
			,self::_encode_string($obj->param_6,16)
			,self::_encode_string($obj->param_7,16)
			,self::_encode_string($obj->param_8,16)
			,self::_encode_string($obj->param_9,16)
			,$obj->schedule_start,$obj->schedule_end,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns RunUsageEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`run_usage`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`run_usage` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..引用ID
	 * @returns RunUsageEntity
	 * @returns null
	 */
	public function load(int $id) : ?RunUsageEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_usage` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..引用ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`run_usage` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 page_id 加载一条
	 * @param	int  $page_id  ..页面ID
	 * @returns RunUsageEntity
	 * @returns null
	 */
	public function loadOneByPageId (int $page_id) : ?RunUsageEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_usage` WHERE `page_id` = '%d'",
			$page_id
		));
		
	}
	/**
	 * 根据普通索引 page_id 加载全部
	 * @param	int	$page_id	..页面ID
	 * @returns RunUsageEntity
	 * @returns null
	 */
	public function loadAllByPageId (int $page_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_usage` WHERE `page_id` = '%d'",
			$page_id
		));
		
	}

	/**
	 * 根据普通索引 block_id 加载一条
	 * @param	int  $block_id  ..模块ID
	 * @returns RunUsageEntity
	 * @returns null
	 */
	public function loadOneByBlockId (int $block_id) : ?RunUsageEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_usage` WHERE `block_id` = '%d'",
			$block_id
		));
		
	}
	/**
	 * 根据普通索引 block_id 加载全部
	 * @param	int	$block_id	..模块ID
	 * @returns RunUsageEntity
	 * @returns null
	 */
	public function loadAllByBlockId (int $block_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_usage` WHERE `block_id` = '%d'",
			$block_id
		));
		
	}

	/**
	 * 根据普通索引 schedule_start 加载一条
	 * @param	int  $schedule_start  ..排期开始
	 * @returns RunUsageEntity
	 * @returns null
	 */
	public function loadOneByScheduleStart (int $schedule_start) : ?RunUsageEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_usage` WHERE `schedule_start` = '%d'",
			$schedule_start
		));
		
	}
	/**
	 * 根据普通索引 schedule_start 加载全部
	 * @param	int	$schedule_start	..排期开始
	 * @returns RunUsageEntity
	 * @returns null
	 */
	public function loadAllByScheduleStart (int $schedule_start) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_usage` WHERE `schedule_start` = '%d'",
			$schedule_start
		));
		
	}

	/**
	 * 根据普通索引 schedule_end 加载一条
	 * @param	int  $schedule_end  ..排期结束
	 * @returns RunUsageEntity
	 * @returns null
	 */
	public function loadOneByScheduleEnd (int $schedule_end) : ?RunUsageEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_usage` WHERE `schedule_end` = '%d'",
			$schedule_end
		));
		
	}
	/**
	 * 根据普通索引 schedule_end 加载全部
	 * @param	int	$schedule_end	..排期结束
	 * @returns RunUsageEntity
	 * @returns null
	 */
	public function loadAllByScheduleEnd (int $schedule_end) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_usage` WHERE `schedule_end` = '%d'",
			$schedule_end
		));
		
	}

	/**
	 * 向数据表 yuemi_main.run_usage 插入一条新纪录
	 * @param	RunUsageEntity    $obj    ..运营数据排期
	 * @returns bool
	 */
	public function insert(RunUsageEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.run_usage 回写一条记录<br>
	 * 更新依据： yuemi_main.run_usage.id
	 * @param	RunUsageEntity	  $obj    ..运营数据排期
	 * @returns bool
	 */
	 public function update(RunUsageEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 运营数据组件
 * @table run_widget
 * @engine innodb
 */
final class RunWidgetEntity extends \Ziima\Data\Entity {
	/**
	 * 组件ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 组件名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 组件代号
	 * @var string
	 */
	public $alias = null;

	/**
	 * 组件数据格式：0自定义,1单品,2多品
	 * @var int
	 */
	public $source_type = null;

	/**
	 * 尺寸模式：0自适应,1指定像素,2百分比
	 * @var int
	 * @default	0
	 */
	public $sizer = 0;

	/**
	 * 组件宽度
	 * @var int
	 * @default	0
	 */
	public $width = 0;

	/**
	 * 组件高度
	 * @var int
	 * @default	0
	 */
	public $height = 0;

	/**
	 * 数据容量
	 * @var int
	 * @default	0
	 */
	public $capacity = 0;

	/**
	 * 组件预览图,BASE64
	 * @var string
	 */
	public $preview = null;

	/**
	 * 组件的UI代码
	 * @var string
	 */
	public $template = null;
}
/**
 * RunWidgetEntity Factory<br>
 * 运营数据组件
 */
final class RunWidgetFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var RunWidgetFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : RunWidgetFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new RunWidgetFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new RunWidgetFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_widget`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`run_widget` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : RunWidgetEntity {
		$obj = new RunWidgetEntity();$obj->id = $row['id'];
		$obj->name = $row['name'];
		$obj->alias = $row['alias'];
		$obj->source_type = $row['source_type'];
		$obj->sizer = $row['sizer'];
		$obj->width = $row['width'];
		$obj->height = $row['height'];
		$obj->capacity = $row['capacity'];
		$obj->preview = $row['preview'];
		$obj->template = $row['template'];
		return $obj;
	}

	private function _object_to_insert(RunWidgetEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`run_widget` %s(`id`,`name`,`alias`,`source_type`,`sizer`,`width`,`height`,`capacity`,`preview`,`template`) VALUES (NULL,'%s','%s',%d,%d,%d,%d,%d,'%s','%s')";
		return sprintf($sql,'',self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,$obj->source_type,$obj->sizer,$obj->width,$obj->height,$obj->capacity,self::_encode_string($obj->preview,65535)
			,self::_encode_string($obj->template,65535)
			);
	}
	private function _object_to_update(RunWidgetEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`run_widget` %s SET `name` = '%s',`alias` = '%s',`source_type` = %d,`sizer` = %d,`width` = %d,`height` = %d,`capacity` = %d,`preview` = '%s',`template` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,$obj->source_type,$obj->sizer,$obj->width,$obj->height,$obj->capacity,self::_encode_string($obj->preview,65535)
			,self::_encode_string($obj->template,65535)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns RunWidgetEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`run_widget`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`run_widget` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..组件ID
	 * @returns RunWidgetEntity
	 * @returns null
	 */
	public function load(int $id) : ?RunWidgetEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_widget` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..组件ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`run_widget` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 alias 加载一条
	 * @param	string  $alias  ..组件代号
	 * @returns RunWidgetEntity
	 * @returns null
	 */
	public function loadOneByAlias (string $alias) : ?RunWidgetEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_widget` WHERE `alias` = '%s'",
			parent::$reader->escape_string($alias)
		));
		
	}
	/**
	 * 根据普通索引 alias 加载全部
	 * @param	string	$alias	..组件代号
	 * @returns RunWidgetEntity
	 * @returns null
	 */
	public function loadAllByAlias (string $alias) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`run_widget` WHERE `alias` = '%s'",
			parent::$reader->escape_string($alias)
		));
		
	}

	/**
	 * 向数据表 yuemi_main.run_widget 插入一条新纪录
	 * @param	RunWidgetEntity    $obj    ..运营数据组件
	 * @returns bool
	 */
	public function insert(RunWidgetEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.run_widget 回写一条记录<br>
	 * 更新依据： yuemi_main.run_widget.id
	 * @param	RunWidgetEntity	  $obj    ..运营数据组件
	 * @returns bool
	 */
	 public function update(RunWidgetEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 短信通知
 * @table sms
 * @engine innodb
 */
final class SmsEntity extends \Ziima\Data\Entity {
	/**
	 * 短信通知ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 验证码类型：0保留，1文字，2语音
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 所属用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 目标手机号
	 * @var string
	 */
	public $mobile = null;

	/**
	 * 短信验证码
	 * @var string
	 */
	public $code = null;

	/**
	 * 短信内容
	 * @var string
	 */
	public $message = null;

	/**
	 * 回执ID
	 * @var string
	 */
	public $biz_id = null;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 过期时间
	 * @var int
	 * @default	0
	 */
	public $expire_time = 0;
}
/**
 * SmsEntity Factory<br>
 * 短信通知
 */
final class SmsFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SmsFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : SmsFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SmsFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SmsFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`sms`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`sms` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SmsEntity {
		$obj = new SmsEntity();$obj->id = $row['id'];
		$obj->type = $row['type'];
		$obj->user_id = $row['user_id'];
		$obj->mobile = $row['mobile'];
		$obj->code = $row['code'];
		$obj->message = $row['message'];
		$obj->biz_id = $row['biz_id'];
		$obj->create_time = $row['create_time'];
		$obj->expire_time = $row['expire_time'];
		return $obj;
	}

	private function _object_to_insert(SmsEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`sms` %s(`id`,`type`,`user_id`,`mobile`,`code`,`message`,`biz_id`,`create_time`,`expire_time`) VALUES (NULL,%d,%d,'%s','%s','%s','%s',UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->type,$obj->user_id,self::_encode_string($obj->mobile,16)
			,self::_encode_string($obj->code,6)
			,self::_encode_string($obj->message,256)
			,self::_encode_string($obj->biz_id,64)
			,$obj->expire_time);
	}
	private function _object_to_update(SmsEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`sms` %s SET `type` = %d,`user_id` = %d,`mobile` = '%s',`code` = '%s',`message` = '%s',`biz_id` = '%s',`expire_time` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->type,$obj->user_id,self::_encode_string($obj->mobile,16)
			,self::_encode_string($obj->code,6)
			,self::_encode_string($obj->message,256)
			,self::_encode_string($obj->biz_id,64)
			,$obj->expire_time,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SmsEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`sms`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`sms` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..短信通知ID
	 * @returns SmsEntity
	 * @returns null
	 */
	public function load(int $id) : ?SmsEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`sms` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..短信通知ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`sms` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..所属用户ID
	 * @returns SmsEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?SmsEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`sms` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..所属用户ID
	 * @returns SmsEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`sms` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 mobile 加载一条
	 * @param	string  $mobile  ..目标手机号
	 * @returns SmsEntity
	 * @returns null
	 */
	public function loadOneByMobile (string $mobile) : ?SmsEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`sms` WHERE `mobile` = '%s'",
			parent::$reader->escape_string($mobile)
		));
		
	}
	/**
	 * 根据普通索引 mobile 加载全部
	 * @param	string	$mobile	..目标手机号
	 * @returns SmsEntity
	 * @returns null
	 */
	public function loadAllByMobile (string $mobile) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`sms` WHERE `mobile` = '%s'",
			parent::$reader->escape_string($mobile)
		));
		
	}

	/**
	 * 根据普通索引 biz_id 加载一条
	 * @param	string  $biz_id  ..回执ID
	 * @returns SmsEntity
	 * @returns null
	 */
	public function loadOneByBizId (string $biz_id) : ?SmsEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`sms` WHERE `biz_id` = '%s'",
			parent::$reader->escape_string($biz_id)
		));
		
	}
	/**
	 * 根据普通索引 biz_id 加载全部
	 * @param	string	$biz_id	..回执ID
	 * @returns SmsEntity
	 * @returns null
	 */
	public function loadAllByBizId (string $biz_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`sms` WHERE `biz_id` = '%s'",
			parent::$reader->escape_string($biz_id)
		));
		
	}

	/**
	 * 向数据表 yuemi_main.sms 插入一条新纪录
	 * @param	SmsEntity    $obj    ..短信通知
	 * @returns bool
	 */
	public function insert(SmsEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.sms 回写一条记录<br>
	 * 更新依据： yuemi_main.sms.id
	 * @param	SmsEntity	  $obj    ..短信通知
	 * @returns bool
	 */
	 public function update(SmsEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 供应商
 * @table supplier
 * @engine innodb
 */
final class SupplierEntity extends \Ziima\Data\Entity {
	/**
	 * 用户ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 前台用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 内部代码
	 * @var string
	 */
	public $alias = null;

	/**
	 * IM系统账号，前缀：s_
	 * @var string
	 */
	public $imuid = null;

	/**
	 * 工作平台密码
	 * @var string
	 */
	public $password = null;

	/**
	 * 登陆令牌
	 * @var string
	 */
	public $token = null;

	/**
	 * 数据泵入开关
	 * @var int
	 * @default	0
	 */
	public $pi_enable = 0;

	/**
	 * 数据泵入接口
	 * @var string
	 */
	public $pi_url = null;

	/**
	 * 数据泵入令牌
	 * @var string
	 */
	public $pi_token = null;

	/**
	 * 数据泵入密钥
	 * @var string
	 */
	public $pi_secret = null;

	/**
	 * 数据泵入频率 0手动,其它乘以30分钟
	 * @var int
	 * @default	0
	 */
	public $pi_interval = 0;

	/**
	 * 分类表名
	 * @var string
	 */
	public $pi_catagory = null;

	/**
	 * 供应商表名
	 * @var string
	 */
	public $pi_supplier = null;

	/**
	 * 数据泵出开关
	 * @var int
	 * @default	0
	 */
	public $po_enable = 0;

	/**
	 * 数据泵出AppletId
	 * @var int
	 * @default	0
	 */
	public $po_applet = 0;

	/**
	 * 数据泵出接口
	 * @var string
	 */
	public $po_url = null;

	/**
	 * 数据泵入令牌
	 * @var string
	 */
	public $po_token = null;

	/**
	 * 数据泵入密钥
	 * @var string
	 */
	public $po_secret = null;

	/**
	 * 供应商状态 0停用,1启用
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * SupplierEntity Factory<br>
 * 供应商
 */
final class SupplierFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SupplierFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : SupplierFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SupplierFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SupplierFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`supplier`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`supplier` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SupplierEntity {
		$obj = new SupplierEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->name = $row['name'];
		$obj->alias = $row['alias'];
		$obj->imuid = $row['imuid'];
		$obj->password = $row['password'];
		$obj->token = $row['token'];
		$obj->pi_enable = $row['pi_enable'];
		$obj->pi_url = $row['pi_url'];
		$obj->pi_token = $row['pi_token'];
		$obj->pi_secret = $row['pi_secret'];
		$obj->pi_interval = $row['pi_interval'];
		$obj->pi_catagory = $row['pi_catagory'];
		$obj->pi_supplier = $row['pi_supplier'];
		$obj->po_enable = $row['po_enable'];
		$obj->po_applet = $row['po_applet'];
		$obj->po_url = $row['po_url'];
		$obj->po_token = $row['po_token'];
		$obj->po_secret = $row['po_secret'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(SupplierEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`supplier` %s(`id`,`user_id`,`name`,`alias`,`imuid`,`password`,`token`,`pi_enable`,`pi_url`,`pi_token`,`pi_secret`,`pi_interval`,`pi_catagory`,`pi_supplier`,`po_enable`,`po_applet`,`po_url`,`po_token`,`po_secret`,`status`,`create_time`,`create_from`) VALUES (NULL,%d,'%s','%s','%s','%s','%s',%d,'%s','%s','%s',%d,'%s','%s',%d,%d,'%s','%s','%s',%d,UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,self::_encode_string($obj->imuid,24)
			,self::_encode_string($obj->password,40)
			,self::_encode_string($obj->token,16)
			,$obj->pi_enable,self::_encode_string($obj->pi_url,1024)
			,self::_encode_string($obj->pi_token,48)
			,self::_encode_string($obj->pi_secret,64)
			,$obj->pi_interval,self::_encode_string($obj->pi_catagory,64)
			,self::_encode_string($obj->pi_supplier,64)
			,$obj->po_enable,$obj->po_applet,self::_encode_string($obj->po_url,1024)
			,self::_encode_string($obj->po_token,48)
			,self::_encode_string($obj->po_secret,64)
			,$obj->status,$obj->create_from);
	}
	private function _object_to_update(SupplierEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`supplier` %s SET `user_id` = %d,`name` = '%s',`alias` = '%s',`imuid` = '%s',`password` = '%s',`token` = '%s',`pi_enable` = %d,`pi_url` = '%s',`pi_token` = '%s',`pi_secret` = '%s',`pi_interval` = %d,`pi_catagory` = '%s',`pi_supplier` = '%s',`po_enable` = %d,`po_applet` = %d,`po_url` = '%s',`po_token` = '%s',`po_secret` = '%s',`status` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->alias,32)
			,self::_encode_string($obj->imuid,24)
			,self::_encode_string($obj->password,40)
			,self::_encode_string($obj->token,16)
			,$obj->pi_enable,self::_encode_string($obj->pi_url,1024)
			,self::_encode_string($obj->pi_token,48)
			,self::_encode_string($obj->pi_secret,64)
			,$obj->pi_interval,self::_encode_string($obj->pi_catagory,64)
			,self::_encode_string($obj->pi_supplier,64)
			,$obj->po_enable,$obj->po_applet,self::_encode_string($obj->po_url,1024)
			,self::_encode_string($obj->po_token,48)
			,self::_encode_string($obj->po_secret,64)
			,$obj->status,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SupplierEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`supplier`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`supplier` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..用户ID
	 * @returns SupplierEntity
	 * @returns null
	 */
	public function load(int $id) : ?SupplierEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..用户ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`supplier` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..前台用户ID
	 * @returns SupplierEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?SupplierEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..前台用户ID
	 * @returns SupplierEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 token 加载一条
	 * @param	string  $token  ..登陆令牌
	 * @returns SupplierEntity
	 * @returns null
	 */
	public function loadOneByToken (string $token) : ?SupplierEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier` WHERE `token` = '%s'",
			parent::$reader->escape_string($token)
		));
		
	}
	/**
	 * 根据普通索引 token 加载全部
	 * @param	string	$token	..登陆令牌
	 * @returns SupplierEntity
	 * @returns null
	 */
	public function loadAllByToken (string $token) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier` WHERE `token` = '%s'",
			parent::$reader->escape_string($token)
		));
		
	}

	/**
	 * 向数据表 yuemi_main.supplier 插入一条新纪录
	 * @param	SupplierEntity    $obj    ..供应商
	 * @returns bool
	 */
	public function insert(SupplierEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.supplier 回写一条记录<br>
	 * 更新依据： yuemi_main.supplier.id
	 * @param	SupplierEntity	  $obj    ..供应商
	 * @returns bool
	 */
	 public function update(SupplierEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 供应商认证
 * @table supplier_cert
 * @engine innodb
 */
final class SupplierCertEntity extends \Ziima\Data\Entity {
	/**
	 * 供应商ID
	 * @var int
	 * @default	0
	 */
	public $supplier_id = 0;

	/**
	 * 公司名
	 * @var string
	 */
	public $corp_name = null;

	/**
	 * 营业执照号码
	 * @var string
	 */
	public $corp_serial = null;

	/**
	 * 公司法人
	 * @var string
	 */
	public $corp_law = null;

	/**
	 * 注册地
	 * @var int
	 * @default	0
	 */
	public $corp_region = 0;

	/**
	 * 注册地址
	 * @var string
	 */
	public $corp_address = null;

	/**
	 * 注册资金
	 * @var float
	 * @default	0.0000
	 */
	public $corp_money = 0.0000;

	/**
	 * 证件有效期
	 * @var int
	 * @default	0
	 */
	public $corp_expire = 0;

	/**
	 * 营业执照照片
	 * @var string
	 */
	public $corp_image = null;

	/**
	 * 实名状态：0未,1待审,2已审,3过期
	 * @var int
	 * @default	0
	 */
	public $corp_status = 0;

	/**
	 * 开户银行
	 * @var int
	 * @default	0
	 */
	public $bank_id = 0;

	/**
	 * 开户银行详细信息
	 * @var string
	 */
	public $bank_name = null;

	/**
	 * 开户银行代码
	 * @var string
	 */
	public $bank_code = null;

	/**
	 * 银行账号
	 * @var string
	 */
	public $bank_card = null;

	/**
	 * 开户许可证图片
	 * @var string
	 */
	public $bank_image = null;

	/**
	 * 实名状态：0未,1待审,2已审
	 * @var int
	 * @default	0
	 */
	public $bank_status = 0;

	/**
	 * 保证金金额
	 * @var float
	 * @default	0.0000
	 */
	public $bond_money = 0.0000;

	/**
	 * 保证金支付订单
	 * @var string
	 */
	public $bond_order = null;

	/**
	 * 保证金支付时间
	 * @var int
	 * @default	0
	 */
	public $bond_time = 0;

	/**
	 * 保证金备注
	 * @var string
	 */
	public $bond_info = null;

	/**
	 * 保证金状态：0未,1支付,2退还
	 * @var int
	 * @default	0
	 */
	public $bond_status = 0;
}
/**
 * SupplierCertEntity Factory<br>
 * 供应商认证
 */
final class SupplierCertFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SupplierCertFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : SupplierCertFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SupplierCertFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SupplierCertFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`supplier_cert`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`supplier_cert` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SupplierCertEntity {
		$obj = new SupplierCertEntity();$obj->supplier_id = $row['supplier_id'];
		$obj->corp_name = $row['corp_name'];
		$obj->corp_serial = $row['corp_serial'];
		$obj->corp_law = $row['corp_law'];
		$obj->corp_region = $row['corp_region'];
		$obj->corp_address = $row['corp_address'];
		$obj->corp_money = $row['corp_money'];
		$obj->corp_expire = $row['corp_expire'];
		$obj->corp_image = $row['corp_image'];
		$obj->corp_status = $row['corp_status'];
		$obj->bank_id = $row['bank_id'];
		$obj->bank_name = $row['bank_name'];
		$obj->bank_code = $row['bank_code'];
		$obj->bank_card = $row['bank_card'];
		$obj->bank_image = $row['bank_image'];
		$obj->bank_status = $row['bank_status'];
		$obj->bond_money = $row['bond_money'];
		$obj->bond_order = $row['bond_order'];
		$obj->bond_time = $row['bond_time'];
		$obj->bond_info = $row['bond_info'];
		$obj->bond_status = $row['bond_status'];
		return $obj;
	}

	private function _object_to_insert(SupplierCertEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`supplier_cert` %s(`supplier_id`,`corp_name`,`corp_serial`,`corp_law`,`corp_region`,`corp_address`,`corp_money`,`corp_expire`,`corp_image`,`corp_status`,`bank_id`,`bank_name`,`bank_code`,`bank_card`,`bank_image`,`bank_status`,`bond_money`,`bond_order`,`bond_time`,`bond_info`,`bond_status`) VALUES (%d,'%s','%s','%s',%d,'%s',%f,%d,'%s',%d,%d,'%s','%s','%s','%s',%d,%f,'%s',%d,'%s',%d)";
		return sprintf($sql,'',$obj->supplier_id,self::_encode_string($obj->corp_name,64)
			,self::_encode_string($obj->corp_serial,32)
			,self::_encode_string($obj->corp_law,32)
			,$obj->corp_region,self::_encode_string($obj->corp_address,256)
			,$obj->corp_money,$obj->corp_expire,self::_encode_string($obj->corp_image,256)
			,$obj->corp_status,$obj->bank_id,self::_encode_string($obj->bank_name,256)
			,self::_encode_string($obj->bank_code,32)
			,self::_encode_string($obj->bank_card,32)
			,self::_encode_string($obj->bank_image,256)
			,$obj->bank_status,$obj->bond_money,self::_encode_string($obj->bond_order,12)
			,$obj->bond_time,self::_encode_string($obj->bond_info,512)
			,$obj->bond_status);
	}
	private function _object_to_update(SupplierCertEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`supplier_cert` %s SET `corp_name` = '%s',`corp_serial` = '%s',`corp_law` = '%s',`corp_region` = %d,`corp_address` = '%s',`corp_money` = %f,`corp_expire` = %d,`corp_image` = '%s',`corp_status` = %d,`bank_id` = %d,`bank_name` = '%s',`bank_code` = '%s',`bank_card` = '%s',`bank_image` = '%s',`bank_status` = %d,`bond_money` = %f,`bond_order` = '%s',`bond_time` = %d,`bond_info` = '%s',`bond_status` = %d WHERE `supplier_id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->corp_name,64)
			,self::_encode_string($obj->corp_serial,32)
			,self::_encode_string($obj->corp_law,32)
			,$obj->corp_region,self::_encode_string($obj->corp_address,256)
			,$obj->corp_money,$obj->corp_expire,self::_encode_string($obj->corp_image,256)
			,$obj->corp_status,$obj->bank_id,self::_encode_string($obj->bank_name,256)
			,self::_encode_string($obj->bank_code,32)
			,self::_encode_string($obj->bank_card,32)
			,self::_encode_string($obj->bank_image,256)
			,$obj->bank_status,$obj->bond_money,self::_encode_string($obj->bond_order,12)
			,$obj->bond_time,self::_encode_string($obj->bond_info,512)
			,$obj->bond_status,$obj->supplier_id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SupplierCertEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`supplier_cert`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`supplier_cert` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "supplier_id" 加载一条
	 * @param	int	$supplier_id	..供应商ID
	 * @returns SupplierCertEntity
	 * @returns null
	 */
	public function load(int $supplier_id) : ?SupplierCertEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_cert` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	
	/**
	 * 根据主键 "supplier_id" 删除一条
	 * @param	int	$supplier_id	..供应商ID
	 * @returns bool
	 */
	public function delete(int $supplier_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`supplier_cert` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	
	/**
	 * 根据普通索引 corp_name 加载一条
	 * @param	string  $corp_name  ..公司名
	 * @returns SupplierCertEntity
	 * @returns null
	 */
	public function loadOneByCorpName (string $corp_name) : ?SupplierCertEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_cert` WHERE `corp_name` = '%s'",
			parent::$reader->escape_string($corp_name)
		));
		
	}
	/**
	 * 根据普通索引 corp_name 加载全部
	 * @param	string	$corp_name	..公司名
	 * @returns SupplierCertEntity
	 * @returns null
	 */
	public function loadAllByCorpName (string $corp_name) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_cert` WHERE `corp_name` = '%s'",
			parent::$reader->escape_string($corp_name)
		));
		
	}

	/**
	 * 根据普通索引 corp_status 加载一条
	 * @param	int  $corp_status  ..实名状态：0未,1待审,2已审,3过期
	 * @returns SupplierCertEntity
	 * @returns null
	 */
	public function loadOneByCorpStatus (int $corp_status) : ?SupplierCertEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_cert` WHERE `corp_status` = '%d'",
			$corp_status
		));
		
	}
	/**
	 * 根据普通索引 corp_status 加载全部
	 * @param	int	$corp_status	..实名状态：0未,1待审,2已审,3过期
	 * @returns SupplierCertEntity
	 * @returns null
	 */
	public function loadAllByCorpStatus (int $corp_status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_cert` WHERE `corp_status` = '%d'",
			$corp_status
		));
		
	}

	/**
	 * 根据普通索引 bank_status 加载一条
	 * @param	int  $bank_status  ..实名状态：0未,1待审,2已审
	 * @returns SupplierCertEntity
	 * @returns null
	 */
	public function loadOneByBankStatus (int $bank_status) : ?SupplierCertEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_cert` WHERE `bank_status` = '%d'",
			$bank_status
		));
		
	}
	/**
	 * 根据普通索引 bank_status 加载全部
	 * @param	int	$bank_status	..实名状态：0未,1待审,2已审
	 * @returns SupplierCertEntity
	 * @returns null
	 */
	public function loadAllByBankStatus (int $bank_status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_cert` WHERE `bank_status` = '%d'",
			$bank_status
		));
		
	}

	/**
	 * 根据普通索引 bond_status 加载一条
	 * @param	int  $bond_status  ..保证金状态：0未,1支付,2退还
	 * @returns SupplierCertEntity
	 * @returns null
	 */
	public function loadOneByBondStatus (int $bond_status) : ?SupplierCertEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_cert` WHERE `bond_status` = '%d'",
			$bond_status
		));
		
	}
	/**
	 * 根据普通索引 bond_status 加载全部
	 * @param	int	$bond_status	..保证金状态：0未,1支付,2退还
	 * @returns SupplierCertEntity
	 * @returns null
	 */
	public function loadAllByBondStatus (int $bond_status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_cert` WHERE `bond_status` = '%d'",
			$bond_status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.supplier_cert 插入一条新纪录
	 * @param	SupplierCertEntity    $obj    ..供应商认证
	 * @returns bool
	 */
	public function insert(SupplierCertEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.supplier_cert 回写一条记录<br>
	 * 更新依据： yuemi_main.supplier_cert.supplier_id
	 * @param	SupplierCertEntity	  $obj    ..供应商认证
	 * @returns bool
	 */
	 public function update(SupplierCertEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 供应商扩展信息
 * @table supplier_info
 * @engine innodb
 */
final class SupplierInfoEntity extends \Ziima\Data\Entity {
	/**
	 * 供应商Id
	 * @var int
	 */
	public $supplier_id = null;

	/**
	 * 供应商常用快递公司Id列表，多个用逗号隔开
	 * @var string
	 */
	public $logistics_com_ids = null;
}
/**
 * SupplierInfoEntity Factory<br>
 * 供应商扩展信息
 */
final class SupplierInfoFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SupplierInfoFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : SupplierInfoFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SupplierInfoFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SupplierInfoFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`supplier_info`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`supplier_info` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SupplierInfoEntity {
		$obj = new SupplierInfoEntity();$obj->supplier_id = $row['supplier_id'];
		$obj->logistics_com_ids = $row['logistics_com_ids'];
		return $obj;
	}

	private function _object_to_insert(SupplierInfoEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`supplier_info` %s(`supplier_id`,`logistics_com_ids`) VALUES (NULL,'%s')";
		return sprintf($sql,'',self::_encode_string($obj->logistics_com_ids,256)
			);
	}
	private function _object_to_update(SupplierInfoEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`supplier_info` %s SET `logistics_com_ids` = '%s' WHERE `supplier_id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->logistics_com_ids,256)
			,$obj->supplier_id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SupplierInfoEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`supplier_info`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`supplier_info` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "supplier_id" 加载一条
	 * @param	int		$supplier_id		..供应商Id
	 * @returns SupplierInfoEntity
	 * @returns null
	 */
	public function load(int $supplier_id) : ?SupplierInfoEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_info` WHERE `supplier_id` = %d",
			$supplier_id
		));
	}

	/**
	 * 根据自增ID "supplier_id" 删除一条
	 * @param	int		$supplier_id		..供应商Id
	 * @returns bool
	 */
	public function delete(int $supplier_id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`supplier_info` WHERE `supplier_id` = %d",
			$supplier_id
		));
	}

	/**
	 * 向数据表 yuemi_main.supplier_info 插入一条新纪录
	 * @param	SupplierInfoEntity    $obj    ..供应商扩展信息
	 * @returns bool
	 */
	public function insert(SupplierInfoEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->supplier_id === NULL || $obj->supplier_id <= 0){
			$obj->supplier_id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.supplier_info 回写一条记录<br>
	 * 更新依据： yuemi_main.supplier_info.supplier_id
	 * @param	SupplierInfoEntity	  $obj    ..供应商扩展信息
	 * @returns bool
	 */
	 public function update(SupplierInfoEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 供应商子帐号
 * @table supplier_user
 * @engine innodb
 */
final class SupplierUserEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 供应商ID
	 * @var int
	 */
	public $supplier_id = null;

	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 角色ID：保留扩展
	 * @var int
	 * @default	0
	 */
	public $role_id = 0;

	/**
	 * IM系统账号，前缀：su_
	 * @var string
	 */
	public $imuid = null;

	/**
	 * 是否可以管理SPU
	 * @var int
	 * @default	0
	 */
	public $acl_spu = 0;

	/**
	 * 是否可以管理SKU
	 * @var int
	 * @default	0
	 */
	public $acl_sku = 0;

	/**
	 * 是否可以管理库存
	 * @var int
	 * @default	0
	 */
	public $acl_depot = 0;

	/**
	 * 是否可以管理价格
	 * @var int
	 * @default	0
	 */
	public $acl_price = 0;

	/**
	 * 是否可以管理订单
	 * @var int
	 * @default	0
	 */
	public $acl_order = 0;

	/**
	 * 是否可以管理物流
	 * @var int
	 * @default	0
	 */
	public $acl_trans = 0;

	/**
	 * 是否可以管理财务
	 * @var int
	 * @default	0
	 */
	public $acl_finance = 0;

	/**
	 * 工作平台密码
	 * @var string
	 */
	public $password = null;

	/**
	 * 登陆令牌
	 * @var string
	 */
	public $token = null;

	/**
	 * 用户状态 0无效,1有效
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * SupplierUserEntity Factory<br>
 * 供应商子帐号
 */
final class SupplierUserFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SupplierUserFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : SupplierUserFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SupplierUserFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SupplierUserFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`supplier_user`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`supplier_user` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SupplierUserEntity {
		$obj = new SupplierUserEntity();$obj->id = $row['id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->user_id = $row['user_id'];
		$obj->role_id = $row['role_id'];
		$obj->imuid = $row['imuid'];
		$obj->acl_spu = $row['acl_spu'];
		$obj->acl_sku = $row['acl_sku'];
		$obj->acl_depot = $row['acl_depot'];
		$obj->acl_price = $row['acl_price'];
		$obj->acl_order = $row['acl_order'];
		$obj->acl_trans = $row['acl_trans'];
		$obj->acl_finance = $row['acl_finance'];
		$obj->password = $row['password'];
		$obj->token = $row['token'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(SupplierUserEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`supplier_user` %s(`id`,`supplier_id`,`user_id`,`role_id`,`imuid`,`acl_spu`,`acl_sku`,`acl_depot`,`acl_price`,`acl_order`,`acl_trans`,`acl_finance`,`password`,`token`,`status`,`create_time`,`create_from`) VALUES (NULL,%d,%d,%d,'%s',%d,%d,%d,%d,%d,%d,%d,'%s','%s',%d,UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->supplier_id,$obj->user_id,$obj->role_id,self::_encode_string($obj->imuid,24)
			,$obj->acl_spu,$obj->acl_sku,$obj->acl_depot,$obj->acl_price,$obj->acl_order,$obj->acl_trans,$obj->acl_finance,self::_encode_string($obj->password,40)
			,self::_encode_string($obj->token,16)
			,$obj->status,$obj->create_from);
	}
	private function _object_to_update(SupplierUserEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`supplier_user` %s SET `supplier_id` = %d,`user_id` = %d,`role_id` = %d,`imuid` = '%s',`acl_spu` = %d,`acl_sku` = %d,`acl_depot` = %d,`acl_price` = %d,`acl_order` = %d,`acl_trans` = %d,`acl_finance` = %d,`password` = '%s',`token` = '%s',`status` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->supplier_id,$obj->user_id,$obj->role_id,self::_encode_string($obj->imuid,24)
			,$obj->acl_spu,$obj->acl_sku,$obj->acl_depot,$obj->acl_price,$obj->acl_order,$obj->acl_trans,$obj->acl_finance,self::_encode_string($obj->password,40)
			,self::_encode_string($obj->token,16)
			,$obj->status,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SupplierUserEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`supplier_user`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`supplier_user` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns SupplierUserEntity
	 * @returns null
	 */
	public function load(int $id) : ?SupplierUserEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_user` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`supplier_user` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 supplier_id 加载一条
	 * @param	int  $supplier_id  ..供应商ID
	 * @returns SupplierUserEntity
	 * @returns null
	 */
	public function loadOneBySupplierId (int $supplier_id) : ?SupplierUserEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_user` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	/**
	 * 根据普通索引 supplier_id 加载全部
	 * @param	int	$supplier_id	..供应商ID
	 * @returns SupplierUserEntity
	 * @returns null
	 */
	public function loadAllBySupplierId (int $supplier_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_user` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns SupplierUserEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?SupplierUserEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_user` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns SupplierUserEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_user` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..用户状态 0无效,1有效
	 * @returns SupplierUserEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?SupplierUserEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_user` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..用户状态 0无效,1有效
	 * @returns SupplierUserEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`supplier_user` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.supplier_user 插入一条新纪录
	 * @param	SupplierUserEntity    $obj    ..供应商子帐号
	 * @returns bool
	 */
	public function insert(SupplierUserEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.supplier_user 回写一条记录<br>
	 * 更新依据： yuemi_main.supplier_user.id
	 * @param	SupplierUserEntity	  $obj    ..供应商子帐号
	 * @returns bool
	 */
	 public function update(SupplierUserEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 阅币流水账
 * @table tally_coin
 * @engine innodb
 */
final class TallyCoinEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 前台用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 资金来源/去向
	 * @var string
	 */
	public $source = null;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 阅币
	 * @var float
	 * @default	0.0000
	 */
	public $val_before = 0.0000;

	/**
	 * 阅币
	 * @var float
	 * @default	0.0000
	 */
	public $val_delta = 0.0000;

	/**
	 * 阅币
	 * @var float
	 * @default	0.0000
	 */
	public $val_after = 0.0000;

	/**
	 * 变化原因
	 * @var string
	 */
	public $message = null;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * TallyCoinEntity Factory<br>
 * 阅币流水账
 */
final class TallyCoinFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var TallyCoinFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : TallyCoinFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new TallyCoinFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new TallyCoinFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`tally_coin`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`tally_coin` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : TallyCoinEntity {
		$obj = new TallyCoinEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->source = $row['source'];
		$obj->order_id = $row['order_id'];
		$obj->val_before = $row['val_before'];
		$obj->val_delta = $row['val_delta'];
		$obj->val_after = $row['val_after'];
		$obj->message = $row['message'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(TallyCoinEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`tally_coin` %s(`id`,`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`) VALUES (NULL,%d,'%s','%s',%f,%f,%f,'%s',UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->source,16)
			,self::_encode_string($obj->order_id,16)
			,$obj->val_before,$obj->val_delta,$obj->val_after,self::_encode_string($obj->message,128)
			,$obj->create_from);
	}
	private function _object_to_update(TallyCoinEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`tally_coin` %s SET `user_id` = %d,`source` = '%s',`order_id` = '%s',`val_before` = %f,`val_delta` = %f,`val_after` = %f,`message` = '%s',`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->source,16)
			,self::_encode_string($obj->order_id,16)
			,$obj->val_before,$obj->val_delta,$obj->val_after,self::_encode_string($obj->message,128)
			,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns TallyCoinEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`tally_coin`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`tally_coin` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns TallyCoinEntity
	 * @returns null
	 */
	public function load(int $id) : ?TallyCoinEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_coin` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`tally_coin` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..前台用户ID
	 * @returns TallyCoinEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?TallyCoinEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_coin` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..前台用户ID
	 * @returns TallyCoinEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_coin` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 source 加载一条
	 * @param	string  $source  ..资金来源/去向
	 * @returns TallyCoinEntity
	 * @returns null
	 */
	public function loadOneBySource (string $source) : ?TallyCoinEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_coin` WHERE `source` = '%s'",
			parent::$reader->escape_string($source)
		));
		
	}
	/**
	 * 根据普通索引 source 加载全部
	 * @param	string	$source	..资金来源/去向
	 * @returns TallyCoinEntity
	 * @returns null
	 */
	public function loadAllBySource (string $source) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_coin` WHERE `source` = '%s'",
			parent::$reader->escape_string($source)
		));
		
	}

	/**
	 * 根据普通索引 create_time 加载一条
	 * @param	int  $create_time  ..创建时间
	 * @returns TallyCoinEntity
	 * @returns null
	 */
	public function loadOneByCreateTime (int $create_time) : ?TallyCoinEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_coin` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}
	/**
	 * 根据普通索引 create_time 加载全部
	 * @param	int	$create_time	..创建时间
	 * @returns TallyCoinEntity
	 * @returns null
	 */
	public function loadAllByCreateTime (int $create_time) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_coin` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}

	/**
	 * 向数据表 yuemi_main.tally_coin 插入一条新纪录
	 * @param	TallyCoinEntity    $obj    ..阅币流水账
	 * @returns bool
	 */
	public function insert(TallyCoinEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.tally_coin 回写一条记录<br>
	 * 更新依据： yuemi_main.tally_coin.id
	 * @param	TallyCoinEntity	  $obj    ..阅币流水账
	 * @returns bool
	 */
	 public function update(TallyCoinEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 现金流水账
 * @table tally_money
 * @engine innodb
 */
final class TallyMoneyEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 前台用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 资金来源/去向
	 * @var string
	 */
	public $source = null;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 现金
	 * @var float
	 * @default	0.0000
	 */
	public $val_before = 0.0000;

	/**
	 * 现金
	 * @var float
	 * @default	0.0000
	 */
	public $val_delta = 0.0000;

	/**
	 * 现金
	 * @var float
	 * @default	0.0000
	 */
	public $val_after = 0.0000;

	/**
	 * 变化原因
	 * @var string
	 */
	public $message = null;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * TallyMoneyEntity Factory<br>
 * 现金流水账
 */
final class TallyMoneyFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var TallyMoneyFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : TallyMoneyFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new TallyMoneyFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new TallyMoneyFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`tally_money`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`tally_money` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : TallyMoneyEntity {
		$obj = new TallyMoneyEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->source = $row['source'];
		$obj->order_id = $row['order_id'];
		$obj->val_before = $row['val_before'];
		$obj->val_delta = $row['val_delta'];
		$obj->val_after = $row['val_after'];
		$obj->message = $row['message'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(TallyMoneyEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`tally_money` %s(`id`,`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`) VALUES (NULL,%d,'%s','%s',%f,%f,%f,'%s',UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->source,16)
			,self::_encode_string($obj->order_id,16)
			,$obj->val_before,$obj->val_delta,$obj->val_after,self::_encode_string($obj->message,128)
			,$obj->create_from);
	}
	private function _object_to_update(TallyMoneyEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`tally_money` %s SET `user_id` = %d,`source` = '%s',`order_id` = '%s',`val_before` = %f,`val_delta` = %f,`val_after` = %f,`message` = '%s',`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->source,16)
			,self::_encode_string($obj->order_id,16)
			,$obj->val_before,$obj->val_delta,$obj->val_after,self::_encode_string($obj->message,128)
			,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns TallyMoneyEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`tally_money`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`tally_money` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns TallyMoneyEntity
	 * @returns null
	 */
	public function load(int $id) : ?TallyMoneyEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_money` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`tally_money` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..前台用户ID
	 * @returns TallyMoneyEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?TallyMoneyEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_money` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..前台用户ID
	 * @returns TallyMoneyEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_money` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 source 加载一条
	 * @param	string  $source  ..资金来源/去向
	 * @returns TallyMoneyEntity
	 * @returns null
	 */
	public function loadOneBySource (string $source) : ?TallyMoneyEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_money` WHERE `source` = '%s'",
			parent::$reader->escape_string($source)
		));
		
	}
	/**
	 * 根据普通索引 source 加载全部
	 * @param	string	$source	..资金来源/去向
	 * @returns TallyMoneyEntity
	 * @returns null
	 */
	public function loadAllBySource (string $source) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_money` WHERE `source` = '%s'",
			parent::$reader->escape_string($source)
		));
		
	}

	/**
	 * 根据普通索引 create_time 加载一条
	 * @param	int  $create_time  ..创建时间
	 * @returns TallyMoneyEntity
	 * @returns null
	 */
	public function loadOneByCreateTime (int $create_time) : ?TallyMoneyEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_money` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}
	/**
	 * 根据普通索引 create_time 加载全部
	 * @param	int	$create_time	..创建时间
	 * @returns TallyMoneyEntity
	 * @returns null
	 */
	public function loadAllByCreateTime (int $create_time) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_money` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}

	/**
	 * 向数据表 yuemi_main.tally_money 插入一条新纪录
	 * @param	TallyMoneyEntity    $obj    ..现金流水账
	 * @returns bool
	 */
	public function insert(TallyMoneyEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.tally_money 回写一条记录<br>
	 * 更新依据： yuemi_main.tally_money.id
	 * @param	TallyMoneyEntity	  $obj    ..现金流水账
	 * @returns bool
	 */
	 public function update(TallyMoneyEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 销售佣金流水账
 * @table tally_profit
 * @engine innodb
 */
final class TallyProfitEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 前台用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 目标子账户:SELF,SHARE,TEAM
	 * @var string
	 */
	public $target = null;

	/**
	 * 资金来源/去向
	 * @var string
	 */
	public $source = null;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 佣金
	 * @var float
	 * @default	0.0000
	 */
	public $val_before = 0.0000;

	/**
	 * 佣金
	 * @var float
	 * @default	0.0000
	 */
	public $val_delta = 0.0000;

	/**
	 * 佣金
	 * @var float
	 * @default	0.0000
	 */
	public $val_after = 0.0000;

	/**
	 * 变化原因
	 * @var string
	 */
	public $message = null;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * TallyProfitEntity Factory<br>
 * 销售佣金流水账
 */
final class TallyProfitFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var TallyProfitFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : TallyProfitFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new TallyProfitFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new TallyProfitFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`tally_profit`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`tally_profit` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : TallyProfitEntity {
		$obj = new TallyProfitEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->target = $row['target'];
		$obj->source = $row['source'];
		$obj->order_id = $row['order_id'];
		$obj->val_before = $row['val_before'];
		$obj->val_delta = $row['val_delta'];
		$obj->val_after = $row['val_after'];
		$obj->message = $row['message'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(TallyProfitEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`tally_profit` %s(`id`,`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`) VALUES (NULL,%d,'%s','%s','%s',%f,%f,%f,'%s',UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->target,8)
			,self::_encode_string($obj->source,16)
			,self::_encode_string($obj->order_id,16)
			,$obj->val_before,$obj->val_delta,$obj->val_after,self::_encode_string($obj->message,128)
			,$obj->create_from);
	}
	private function _object_to_update(TallyProfitEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`tally_profit` %s SET `user_id` = %d,`target` = '%s',`source` = '%s',`order_id` = '%s',`val_before` = %f,`val_delta` = %f,`val_after` = %f,`message` = '%s',`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->target,8)
			,self::_encode_string($obj->source,16)
			,self::_encode_string($obj->order_id,16)
			,$obj->val_before,$obj->val_delta,$obj->val_after,self::_encode_string($obj->message,128)
			,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns TallyProfitEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`tally_profit`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`tally_profit` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns TallyProfitEntity
	 * @returns null
	 */
	public function load(int $id) : ?TallyProfitEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_profit` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`tally_profit` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..前台用户ID
	 * @returns TallyProfitEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?TallyProfitEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_profit` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..前台用户ID
	 * @returns TallyProfitEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_profit` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 target 加载一条
	 * @param	string  $target  ..目标子账户:SELF,SHARE,TEAM
	 * @returns TallyProfitEntity
	 * @returns null
	 */
	public function loadOneByTarget (string $target) : ?TallyProfitEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_profit` WHERE `target` = '%s'",
			parent::$reader->escape_string($target)
		));
		
	}
	/**
	 * 根据普通索引 target 加载全部
	 * @param	string	$target	..目标子账户:SELF,SHARE,TEAM
	 * @returns TallyProfitEntity
	 * @returns null
	 */
	public function loadAllByTarget (string $target) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_profit` WHERE `target` = '%s'",
			parent::$reader->escape_string($target)
		));
		
	}

	/**
	 * 根据普通索引 source 加载一条
	 * @param	string  $source  ..资金来源/去向
	 * @returns TallyProfitEntity
	 * @returns null
	 */
	public function loadOneBySource (string $source) : ?TallyProfitEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_profit` WHERE `source` = '%s'",
			parent::$reader->escape_string($source)
		));
		
	}
	/**
	 * 根据普通索引 source 加载全部
	 * @param	string	$source	..资金来源/去向
	 * @returns TallyProfitEntity
	 * @returns null
	 */
	public function loadAllBySource (string $source) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_profit` WHERE `source` = '%s'",
			parent::$reader->escape_string($source)
		));
		
	}

	/**
	 * 根据普通索引 create_time 加载一条
	 * @param	int  $create_time  ..创建时间
	 * @returns TallyProfitEntity
	 * @returns null
	 */
	public function loadOneByCreateTime (int $create_time) : ?TallyProfitEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_profit` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}
	/**
	 * 根据普通索引 create_time 加载全部
	 * @param	int	$create_time	..创建时间
	 * @returns TallyProfitEntity
	 * @returns null
	 */
	public function loadAllByCreateTime (int $create_time) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_profit` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}

	/**
	 * 向数据表 yuemi_main.tally_profit 插入一条新纪录
	 * @param	TallyProfitEntity    $obj    ..销售佣金流水账
	 * @returns bool
	 */
	public function insert(TallyProfitEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.tally_profit 回写一条记录<br>
	 * 更新依据： yuemi_main.tally_profit.id
	 * @param	TallyProfitEntity	  $obj    ..销售佣金流水账
	 * @returns bool
	 */
	 public function update(TallyProfitEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 招聘佣金流水账
 * @table tally_recruit
 * @engine innodb
 */
final class TallyRecruitEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 前台用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 目标子账户:DIR,ALT
	 * @var string
	 */
	public $target = null;

	/**
	 * 资金来源/去向
	 * @var string
	 */
	public $source = null;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 佣金
	 * @var float
	 * @default	0.0000
	 */
	public $val_before = 0.0000;

	/**
	 * 佣金
	 * @var float
	 * @default	0.0000
	 */
	public $val_delta = 0.0000;

	/**
	 * 佣金
	 * @var float
	 * @default	0.0000
	 */
	public $val_after = 0.0000;

	/**
	 * 变化原因
	 * @var string
	 */
	public $message = null;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * TallyRecruitEntity Factory<br>
 * 招聘佣金流水账
 */
final class TallyRecruitFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var TallyRecruitFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : TallyRecruitFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new TallyRecruitFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new TallyRecruitFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`tally_recruit`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`tally_recruit` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : TallyRecruitEntity {
		$obj = new TallyRecruitEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->target = $row['target'];
		$obj->source = $row['source'];
		$obj->order_id = $row['order_id'];
		$obj->val_before = $row['val_before'];
		$obj->val_delta = $row['val_delta'];
		$obj->val_after = $row['val_after'];
		$obj->message = $row['message'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(TallyRecruitEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`tally_recruit` %s(`id`,`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`) VALUES (NULL,%d,'%s','%s','%s',%f,%f,%f,'%s',UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->target,8)
			,self::_encode_string($obj->source,16)
			,self::_encode_string($obj->order_id,16)
			,$obj->val_before,$obj->val_delta,$obj->val_after,self::_encode_string($obj->message,128)
			,$obj->create_from);
	}
	private function _object_to_update(TallyRecruitEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`tally_recruit` %s SET `user_id` = %d,`target` = '%s',`source` = '%s',`order_id` = '%s',`val_before` = %f,`val_delta` = %f,`val_after` = %f,`message` = '%s',`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->target,8)
			,self::_encode_string($obj->source,16)
			,self::_encode_string($obj->order_id,16)
			,$obj->val_before,$obj->val_delta,$obj->val_after,self::_encode_string($obj->message,128)
			,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns TallyRecruitEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`tally_recruit`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`tally_recruit` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns TallyRecruitEntity
	 * @returns null
	 */
	public function load(int $id) : ?TallyRecruitEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_recruit` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`tally_recruit` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..前台用户ID
	 * @returns TallyRecruitEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?TallyRecruitEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_recruit` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..前台用户ID
	 * @returns TallyRecruitEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_recruit` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 target 加载一条
	 * @param	string  $target  ..目标子账户:DIR,ALT
	 * @returns TallyRecruitEntity
	 * @returns null
	 */
	public function loadOneByTarget (string $target) : ?TallyRecruitEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_recruit` WHERE `target` = '%s'",
			parent::$reader->escape_string($target)
		));
		
	}
	/**
	 * 根据普通索引 target 加载全部
	 * @param	string	$target	..目标子账户:DIR,ALT
	 * @returns TallyRecruitEntity
	 * @returns null
	 */
	public function loadAllByTarget (string $target) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_recruit` WHERE `target` = '%s'",
			parent::$reader->escape_string($target)
		));
		
	}

	/**
	 * 根据普通索引 source 加载一条
	 * @param	string  $source  ..资金来源/去向
	 * @returns TallyRecruitEntity
	 * @returns null
	 */
	public function loadOneBySource (string $source) : ?TallyRecruitEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_recruit` WHERE `source` = '%s'",
			parent::$reader->escape_string($source)
		));
		
	}
	/**
	 * 根据普通索引 source 加载全部
	 * @param	string	$source	..资金来源/去向
	 * @returns TallyRecruitEntity
	 * @returns null
	 */
	public function loadAllBySource (string $source) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_recruit` WHERE `source` = '%s'",
			parent::$reader->escape_string($source)
		));
		
	}

	/**
	 * 根据普通索引 create_time 加载一条
	 * @param	int  $create_time  ..创建时间
	 * @returns TallyRecruitEntity
	 * @returns null
	 */
	public function loadOneByCreateTime (int $create_time) : ?TallyRecruitEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_recruit` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}
	/**
	 * 根据普通索引 create_time 加载全部
	 * @param	int	$create_time	..创建时间
	 * @returns TallyRecruitEntity
	 * @returns null
	 */
	public function loadAllByCreateTime (int $create_time) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`tally_recruit` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}

	/**
	 * 向数据表 yuemi_main.tally_recruit 插入一条新纪录
	 * @param	TallyRecruitEntity    $obj    ..招聘佣金流水账
	 * @returns bool
	 */
	public function insert(TallyRecruitEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.tally_recruit 回写一条记录<br>
	 * 更新依据： yuemi_main.tally_recruit.id
	 * @param	TallyRecruitEntity	  $obj    ..招聘佣金流水账
	 * @returns bool
	 */
	 public function update(TallyRecruitEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 签到任务
 * @table task_sign
 * @engine innodb
 */
final class TaskSignEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 月份ID：YYYYMM
	 * @var int
	 */
	public $month_id = null;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_1 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_2 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_3 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_4 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_5 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_6 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_7 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_8 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_9 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_10 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_11 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_12 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_13 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_14 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_15 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_16 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_17 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_18 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_19 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_20 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_21 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_22 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_23 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_24 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_25 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_26 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_27 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_28 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_29 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_30 = 0;

	/**
	 * 1号
	 * @var int
	 * @default	0
	 */
	public $day_31 = 0;
}
/**
 * TaskSignEntity Factory<br>
 * 签到任务
 */
final class TaskSignFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var TaskSignFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : TaskSignFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new TaskSignFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new TaskSignFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`task_sign`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`task_sign` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : TaskSignEntity {
		$obj = new TaskSignEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->month_id = $row['month_id'];
		$obj->day_1 = $row['day_1'];
		$obj->day_2 = $row['day_2'];
		$obj->day_3 = $row['day_3'];
		$obj->day_4 = $row['day_4'];
		$obj->day_5 = $row['day_5'];
		$obj->day_6 = $row['day_6'];
		$obj->day_7 = $row['day_7'];
		$obj->day_8 = $row['day_8'];
		$obj->day_9 = $row['day_9'];
		$obj->day_10 = $row['day_10'];
		$obj->day_11 = $row['day_11'];
		$obj->day_12 = $row['day_12'];
		$obj->day_13 = $row['day_13'];
		$obj->day_14 = $row['day_14'];
		$obj->day_15 = $row['day_15'];
		$obj->day_16 = $row['day_16'];
		$obj->day_17 = $row['day_17'];
		$obj->day_18 = $row['day_18'];
		$obj->day_19 = $row['day_19'];
		$obj->day_20 = $row['day_20'];
		$obj->day_21 = $row['day_21'];
		$obj->day_22 = $row['day_22'];
		$obj->day_23 = $row['day_23'];
		$obj->day_24 = $row['day_24'];
		$obj->day_25 = $row['day_25'];
		$obj->day_26 = $row['day_26'];
		$obj->day_27 = $row['day_27'];
		$obj->day_28 = $row['day_28'];
		$obj->day_29 = $row['day_29'];
		$obj->day_30 = $row['day_30'];
		$obj->day_31 = $row['day_31'];
		return $obj;
	}

	private function _object_to_insert(TaskSignEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`task_sign` %s(`id`,`user_id`,`month_id`,`day_1`,`day_2`,`day_3`,`day_4`,`day_5`,`day_6`,`day_7`,`day_8`,`day_9`,`day_10`,`day_11`,`day_12`,`day_13`,`day_14`,`day_15`,`day_16`,`day_17`,`day_18`,`day_19`,`day_20`,`day_21`,`day_22`,`day_23`,`day_24`,`day_25`,`day_26`,`day_27`,`day_28`,`day_29`,`day_30`,`day_31`) VALUES (NULL,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->user_id,$obj->month_id,$obj->day_1,$obj->day_2,$obj->day_3,$obj->day_4,$obj->day_5,$obj->day_6,$obj->day_7,$obj->day_8,$obj->day_9,$obj->day_10,$obj->day_11,$obj->day_12,$obj->day_13,$obj->day_14,$obj->day_15,$obj->day_16,$obj->day_17,$obj->day_18,$obj->day_19,$obj->day_20,$obj->day_21,$obj->day_22,$obj->day_23,$obj->day_24,$obj->day_25,$obj->day_26,$obj->day_27,$obj->day_28,$obj->day_29,$obj->day_30,$obj->day_31);
	}
	private function _object_to_update(TaskSignEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`task_sign` %s SET `user_id` = %d,`month_id` = %d,`day_1` = %d,`day_2` = %d,`day_3` = %d,`day_4` = %d,`day_5` = %d,`day_6` = %d,`day_7` = %d,`day_8` = %d,`day_9` = %d,`day_10` = %d,`day_11` = %d,`day_12` = %d,`day_13` = %d,`day_14` = %d,`day_15` = %d,`day_16` = %d,`day_17` = %d,`day_18` = %d,`day_19` = %d,`day_20` = %d,`day_21` = %d,`day_22` = %d,`day_23` = %d,`day_24` = %d,`day_25` = %d,`day_26` = %d,`day_27` = %d,`day_28` = %d,`day_29` = %d,`day_30` = %d,`day_31` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->month_id,$obj->day_1,$obj->day_2,$obj->day_3,$obj->day_4,$obj->day_5,$obj->day_6,$obj->day_7,$obj->day_8,$obj->day_9,$obj->day_10,$obj->day_11,$obj->day_12,$obj->day_13,$obj->day_14,$obj->day_15,$obj->day_16,$obj->day_17,$obj->day_18,$obj->day_19,$obj->day_20,$obj->day_21,$obj->day_22,$obj->day_23,$obj->day_24,$obj->day_25,$obj->day_26,$obj->day_27,$obj->day_28,$obj->day_29,$obj->day_30,$obj->day_31,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns TaskSignEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`task_sign`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`task_sign` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns TaskSignEntity
	 * @returns null
	 */
	public function load(int $id) : ?TaskSignEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`task_sign` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`task_sign` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns TaskSignEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?TaskSignEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`task_sign` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns TaskSignEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`task_sign` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 month_id 加载一条
	 * @param	int  $month_id  ..月份ID：YYYYMM
	 * @returns TaskSignEntity
	 * @returns null
	 */
	public function loadOneByMonthId (int $month_id) : ?TaskSignEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`task_sign` WHERE `month_id` = '%d'",
			$month_id
		));
		
	}
	/**
	 * 根据普通索引 month_id 加载全部
	 * @param	int	$month_id	..月份ID：YYYYMM
	 * @returns TaskSignEntity
	 * @returns null
	 */
	public function loadAllByMonthId (int $month_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`task_sign` WHERE `month_id` = '%d'",
			$month_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.task_sign 插入一条新纪录
	 * @param	TaskSignEntity    $obj    ..签到任务
	 * @returns bool
	 */
	public function insert(TaskSignEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.task_sign 回写一条记录<br>
	 * 更新依据： yuemi_main.task_sign.id
	 * @param	TaskSignEntity	  $obj    ..签到任务
	 * @returns bool
	 */
	 public function update(TaskSignEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 团队
 * @table team
 * @engine innodb
 */
final class TeamEntity extends \Ziima\Data\Entity {
	/**
	 * 直营团队ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 绑定总经理ID
	 * @var int
	 * @default	0
	 */
	public $director_id = 0;

	/**
	 * 团队名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 创建人
	 * @var int
	 * @default	0
	 */
	public $create_user = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * TeamEntity Factory<br>
 * 团队
 */
final class TeamFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var TeamFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : TeamFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new TeamFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new TeamFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`team`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`team` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : TeamEntity {
		$obj = new TeamEntity();$obj->id = $row['id'];
		$obj->director_id = $row['director_id'];
		$obj->name = $row['name'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(TeamEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`team` %s(`id`,`director_id`,`name`,`create_user`,`create_time`,`create_from`) VALUES (NULL,%d,'%s',%d,%d,%d)";
		return sprintf($sql,'',$obj->director_id,self::_encode_string($obj->name,16)
			,$obj->create_user,$obj->create_time,$obj->create_from);
	}
	private function _object_to_update(TeamEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`team` %s SET `director_id` = %d,`name` = '%s',`create_user` = %d,`create_time` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->director_id,self::_encode_string($obj->name,16)
			,$obj->create_user,$obj->create_time,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns TeamEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`team`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`team` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..直营团队ID
	 * @returns TeamEntity
	 * @returns null
	 */
	public function load(int $id) : ?TeamEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`team` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..直营团队ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`team` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 director_id 加载
	 * @param	int	$director_id	..绑定总经理ID
	 * @returns TeamEntity
	 * @returns null
	 */
	public function loadByDirectorId (int $director_id) : ?TeamEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`team` WHERE `director_id` = '%d'",
			$director_id
		));
		
	}
	
	/**
	 * 根据唯一索引 "director_id" 删除一条
	 * @param	int	$director_id	..绑定总经理ID
	 * @returns bool
	 */
	public function deleteByDirectorId(int $director_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`team` WHERE `director_id` = '%d'",
			$director_id
		));
		
	}
	
	/**
	 * 向数据表 yuemi_main.team 插入一条新纪录
	 * @param	TeamEntity    $obj    ..团队
	 * @returns bool
	 */
	public function insert(TeamEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.team 回写一条记录<br>
	 * 更新依据： yuemi_main.team.id
	 * @param	TeamEntity	  $obj    ..团队
	 * @returns bool
	 */
	 public function update(TeamEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 小组
 * @table team_group
 * @engine innodb
 */
final class TeamGroupEntity extends \Ziima\Data\Entity {
	/**
	 * 小组ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 团队ID
	 * @var int
	 * @default	0
	 */
	public $team_id = 0;

	/**
	 * 身份等级：0无效,1一线,2二线,3三线,4四线
	 * @var int
	 * @default	0
	 */
	public $level = 0;

	/**
	 * 小组管理员ID
	 * @var int
	 * @default	0
	 */
	public $manager_id = 0;

	/**
	 * 小组名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 身份代码
	 * @var string
	 */
	public $code = null;

	/**
	 * 创建人
	 * @var int
	 * @default	0
	 */
	public $create_user = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * TeamGroupEntity Factory<br>
 * 小组
 */
final class TeamGroupFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var TeamGroupFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : TeamGroupFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new TeamGroupFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new TeamGroupFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`team_group`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`team_group` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : TeamGroupEntity {
		$obj = new TeamGroupEntity();$obj->id = $row['id'];
		$obj->team_id = $row['team_id'];
		$obj->level = $row['level'];
		$obj->manager_id = $row['manager_id'];
		$obj->name = $row['name'];
		$obj->code = $row['code'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(TeamGroupEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`team_group` %s(`id`,`team_id`,`level`,`manager_id`,`name`,`code`,`create_user`,`create_time`,`create_from`) VALUES (NULL,%d,%d,%d,'%s','%s',%d,%d,%d)";
		return sprintf($sql,'',$obj->team_id,$obj->level,$obj->manager_id,self::_encode_string($obj->name,16)
			,self::_encode_string($obj->code,2)
			,$obj->create_user,$obj->create_time,$obj->create_from);
	}
	private function _object_to_update(TeamGroupEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`team_group` %s SET `team_id` = %d,`level` = %d,`manager_id` = %d,`name` = '%s',`code` = '%s',`create_user` = %d,`create_time` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->team_id,$obj->level,$obj->manager_id,self::_encode_string($obj->name,16)
			,self::_encode_string($obj->code,2)
			,$obj->create_user,$obj->create_time,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns TeamGroupEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`team_group`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`team_group` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..小组ID
	 * @returns TeamGroupEntity
	 * @returns null
	 */
	public function load(int $id) : ?TeamGroupEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`team_group` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..小组ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`team_group` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 team_id 加载一条
	 * @param	int  $team_id  ..团队ID
	 * @returns TeamGroupEntity
	 * @returns null
	 */
	public function loadOneByTeamId (int $team_id) : ?TeamGroupEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`team_group` WHERE `team_id` = '%d'",
			$team_id
		));
		
	}
	/**
	 * 根据普通索引 team_id 加载全部
	 * @param	int	$team_id	..团队ID
	 * @returns TeamGroupEntity
	 * @returns null
	 */
	public function loadAllByTeamId (int $team_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`team_group` WHERE `team_id` = '%d'",
			$team_id
		));
		
	}

	/**
	 * 根据普通索引 level 加载一条
	 * @param	int  $level  ..身份等级：0无效,1一线,2二线,3三线,4四线
	 * @returns TeamGroupEntity
	 * @returns null
	 */
	public function loadOneByLevel (int $level) : ?TeamGroupEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`team_group` WHERE `level` = '%d'",
			$level
		));
		
	}
	/**
	 * 根据普通索引 level 加载全部
	 * @param	int	$level	..身份等级：0无效,1一线,2二线,3三线,4四线
	 * @returns TeamGroupEntity
	 * @returns null
	 */
	public function loadAllByLevel (int $level) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`team_group` WHERE `level` = '%d'",
			$level
		));
		
	}

	/**
	 * 向数据表 yuemi_main.team_group 插入一条新纪录
	 * @param	TeamGroupEntity    $obj    ..小组
	 * @returns bool
	 */
	public function insert(TeamGroupEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.team_group 回写一条记录<br>
	 * 更新依据： yuemi_main.team_group.id
	 * @param	TeamGroupEntity	  $obj    ..小组
	 * @returns bool
	 */
	 public function update(TeamGroupEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 团队
 * @table team_member
 * @engine innodb
 */
final class TeamMemberEntity extends \Ziima\Data\Entity {
	/**
	 * 成员ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 前台用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 团队ID
	 * @var int
	 */
	public $team_id = null;

	/**
	 * 小组ID
	 * @var int
	 * @default	0
	 */
	public $group_id = 0;

	/**
	 * 姓名
	 * @var string
	 */
	public $name = null;

	/**
	 * 身份等级：0无效,1一线,2二线,3三线,4四线
	 * @var int
	 * @default	0
	 */
	public $level = 0;

	/**
	 * 身份代码
	 * @var string
	 */
	public $code = null;

	/**
	 * 工作平台密码
	 * @var string
	 */
	public $password = null;

	/**
	 * 员工状态：0离职,1在职
	 * @var int
	 * @default	1
	 */
	public $status = 1;

	/**
	 * 创建人
	 * @var int
	 * @default	0
	 */
	public $create_user = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * TeamMemberEntity Factory<br>
 * 团队
 */
final class TeamMemberFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var TeamMemberFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : TeamMemberFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new TeamMemberFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new TeamMemberFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`team_member`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`team_member` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : TeamMemberEntity {
		$obj = new TeamMemberEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->team_id = $row['team_id'];
		$obj->group_id = $row['group_id'];
		$obj->name = $row['name'];
		$obj->level = $row['level'];
		$obj->code = $row['code'];
		$obj->password = $row['password'];
		$obj->status = $row['status'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(TeamMemberEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`team_member` %s(`id`,`user_id`,`team_id`,`group_id`,`name`,`level`,`code`,`password`,`status`,`create_user`,`create_time`,`create_from`) VALUES (NULL,%d,%d,%d,'%s',%d,'%s','%s',%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->user_id,$obj->team_id,$obj->group_id,self::_encode_string($obj->name,16)
			,$obj->level,self::_encode_string($obj->code,3)
			,self::_encode_string($obj->password,40)
			,$obj->status,$obj->create_user,$obj->create_time,$obj->create_from);
	}
	private function _object_to_update(TeamMemberEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`team_member` %s SET `user_id` = %d,`team_id` = %d,`group_id` = %d,`name` = '%s',`level` = %d,`code` = '%s',`password` = '%s',`status` = %d,`create_user` = %d,`create_time` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->team_id,$obj->group_id,self::_encode_string($obj->name,16)
			,$obj->level,self::_encode_string($obj->code,3)
			,self::_encode_string($obj->password,40)
			,$obj->status,$obj->create_user,$obj->create_time,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns TeamMemberEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`team_member`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`team_member` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..成员ID
	 * @returns TeamMemberEntity
	 * @returns null
	 */
	public function load(int $id) : ?TeamMemberEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`team_member` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..成员ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`team_member` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 user_id 加载
	 * @param	int	$user_id	..前台用户ID
	 * @returns TeamMemberEntity
	 * @returns null
	 */
	public function loadByUserId (int $user_id) : ?TeamMemberEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`team_member` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据唯一索引 "user_id" 删除一条
	 * @param	int	$user_id	..前台用户ID
	 * @returns bool
	 */
	public function deleteByUserId(int $user_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`team_member` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据普通索引 team_id 加载一条
	 * @param	int  $team_id  ..团队ID
	 * @returns TeamMemberEntity
	 * @returns null
	 */
	public function loadOneByTeamId (int $team_id) : ?TeamMemberEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`team_member` WHERE `team_id` = '%d'",
			$team_id
		));
		
	}
	/**
	 * 根据普通索引 team_id 加载全部
	 * @param	int	$team_id	..团队ID
	 * @returns TeamMemberEntity
	 * @returns null
	 */
	public function loadAllByTeamId (int $team_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`team_member` WHERE `team_id` = '%d'",
			$team_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.team_member 插入一条新纪录
	 * @param	TeamMemberEntity    $obj    ..团队
	 * @returns bool
	 */
	public function insert(TeamMemberEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.team_member 回写一条记录<br>
	 * 更新依据： yuemi_main.team_member.id
	 * @param	TeamMemberEntity	  $obj    ..团队
	 * @returns bool
	 */
	 public function update(TeamMemberEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 优惠券
 * @table ticket
 * @engine innodb
 */
final class TicketEntity extends \Ziima\Data\Entity {
	/**
	 * 优惠券ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 供应商ID
	 * @var int
	 * @default	0
	 */
	public $supplier_id = 0;

	/**
	 * 类型:0无效,1满减
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 面额
	 * @var float
	 */
	public $money = null;

	/**
	 * 发行数量
	 * @var int
	 */
	public $qty_all = null;

	/**
	 * 已领取数量
	 * @var int
	 */
	public $qty_got = null;

	/**
	 * 已使用数量
	 * @var int
	 */
	public $qty_use = null;

	/**
	 * 领取方式：0手动领取,1自动领取
	 * @var int
	 * @default	0
	 */
	public $got_style = 0;

	/**
	 * 领取数量
	 * @var int
	 * @default	1
	 */
	public $got_count = 1;

	/**
	 * 满多少
	 * @var float
	 * @default	0.0000
	 */
	public $mj_money = 0.0000;

	/**
	 * 发行开始时间，0立即开始
	 * @var int
	 * @default	0
	 */
	public $pub_start = 0;

	/**
	 * 发行结束时间，0领完结束
	 * @var int
	 * @default	0
	 */
	public $pub_end = 0;

	/**
	 * 是否自动使用
	 * @var int
	 * @default	1
	 */
	public $use_auto = 1;

	/**
	 * 叠加规则：0不叠加,1可叠加
	 * @var int
	 * @default	0
	 */
	public $use_multi = 0;

	/**
	 * 排他规则：0不排他,1本券排他,2内部排他,3全局排他
	 * @var int
	 * @default	0
	 */
	public $use_exclus = 0;

	/**
	 * 使用开始时间，0立即开始
	 * @var int
	 * @default	0
	 */
	public $use_start = 0;

	/**
	 * 使用结束时间，0用不过期
	 * @var int
	 * @default	0
	 */
	public $use_end = 0;

	/**
	 * 优惠券状态 0草稿,1提交,2审批,3拒绝
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 审核时间
	 * @var int
	 * @default	0
	 */
	public $audit_time = 0;

	/**
	 * 审核IP
	 * @var int
	 * @default	0
	 */
	public $audit_from = 0;
}
/**
 * TicketEntity Factory<br>
 * 优惠券
 */
final class TicketFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var TicketFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : TicketFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new TicketFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new TicketFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`ticket`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`ticket` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : TicketEntity {
		$obj = new TicketEntity();$obj->id = $row['id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->type = $row['type'];
		$obj->money = $row['money'];
		$obj->qty_all = $row['qty_all'];
		$obj->qty_got = $row['qty_got'];
		$obj->qty_use = $row['qty_use'];
		$obj->got_style = $row['got_style'];
		$obj->got_count = $row['got_count'];
		$obj->mj_money = $row['mj_money'];
		$obj->pub_start = $row['pub_start'];
		$obj->pub_end = $row['pub_end'];
		$obj->use_auto = $row['use_auto'];
		$obj->use_multi = $row['use_multi'];
		$obj->use_exclus = $row['use_exclus'];
		$obj->use_start = $row['use_start'];
		$obj->use_end = $row['use_end'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(TicketEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`ticket` %s(`id`,`supplier_id`,`type`,`money`,`qty_all`,`qty_got`,`qty_use`,`got_style`,`got_count`,`mj_money`,`pub_start`,`pub_end`,`use_auto`,`use_multi`,`use_exclus`,`use_start`,`use_end`,`status`,`create_time`,`create_from`,`audit_time`,`audit_from`) VALUES (NULL,%d,%d,%f,%d,%d,%d,%d,%d,%f,%d,%d,%d,%d,%d,%d,%d,%d,UNIX_TIMESTAMP(),%d,%d,%d)";
		return sprintf($sql,'',$obj->supplier_id,$obj->type,$obj->money,$obj->qty_all,$obj->qty_got,$obj->qty_use,$obj->got_style,$obj->got_count,$obj->mj_money,$obj->pub_start,$obj->pub_end,$obj->use_auto,$obj->use_multi,$obj->use_exclus,$obj->use_start,$obj->use_end,$obj->status,$obj->create_from,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(TicketEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`ticket` %s SET `supplier_id` = %d,`type` = %d,`money` = %f,`qty_all` = %d,`qty_got` = %d,`qty_use` = %d,`got_style` = %d,`got_count` = %d,`mj_money` = %f,`pub_start` = %d,`pub_end` = %d,`use_auto` = %d,`use_multi` = %d,`use_exclus` = %d,`use_start` = %d,`use_end` = %d,`status` = %d,`create_from` = %d,`audit_time` = %d,`audit_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->supplier_id,$obj->type,$obj->money,$obj->qty_all,$obj->qty_got,$obj->qty_use,$obj->got_style,$obj->got_count,$obj->mj_money,$obj->pub_start,$obj->pub_end,$obj->use_auto,$obj->use_multi,$obj->use_exclus,$obj->use_start,$obj->use_end,$obj->status,$obj->create_from,$obj->audit_time,$obj->audit_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns TicketEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`ticket`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`ticket` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..优惠券ID
	 * @returns TicketEntity
	 * @returns null
	 */
	public function load(int $id) : ?TicketEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`ticket` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..优惠券ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`ticket` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 supplier_id 加载一条
	 * @param	int  $supplier_id  ..供应商ID
	 * @returns TicketEntity
	 * @returns null
	 */
	public function loadOneBySupplierId (int $supplier_id) : ?TicketEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`ticket` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	/**
	 * 根据普通索引 supplier_id 加载全部
	 * @param	int	$supplier_id	..供应商ID
	 * @returns TicketEntity
	 * @returns null
	 */
	public function loadAllBySupplierId (int $supplier_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`ticket` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}

	/**
	 * 根据普通索引 pub_start 加载一条
	 * @param	int  $pub_start  ..发行开始时间，0立即开始
	 * @returns TicketEntity
	 * @returns null
	 */
	public function loadOneByPubStart (int $pub_start) : ?TicketEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`ticket` WHERE `pub_start` = '%d'",
			$pub_start
		));
		
	}
	/**
	 * 根据普通索引 pub_start 加载全部
	 * @param	int	$pub_start	..发行开始时间，0立即开始
	 * @returns TicketEntity
	 * @returns null
	 */
	public function loadAllByPubStart (int $pub_start) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`ticket` WHERE `pub_start` = '%d'",
			$pub_start
		));
		
	}

	/**
	 * 根据普通索引 pub_end 加载一条
	 * @param	int  $pub_end  ..发行结束时间，0领完结束
	 * @returns TicketEntity
	 * @returns null
	 */
	public function loadOneByPubEnd (int $pub_end) : ?TicketEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`ticket` WHERE `pub_end` = '%d'",
			$pub_end
		));
		
	}
	/**
	 * 根据普通索引 pub_end 加载全部
	 * @param	int	$pub_end	..发行结束时间，0领完结束
	 * @returns TicketEntity
	 * @returns null
	 */
	public function loadAllByPubEnd (int $pub_end) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`ticket` WHERE `pub_end` = '%d'",
			$pub_end
		));
		
	}

	/**
	 * 向数据表 yuemi_main.ticket 插入一条新纪录
	 * @param	TicketEntity    $obj    ..优惠券
	 * @returns bool
	 */
	public function insert(TicketEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.ticket 回写一条记录<br>
	 * 更新依据： yuemi_main.ticket.id
	 * @param	TicketEntity	  $obj    ..优惠券
	 * @returns bool
	 */
	 public function update(TicketEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 用户
 * @table user
 * @engine innodb
 */
final class UserEntity extends \Ziima\Data\Entity {
	/**
	 * 用户ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 推荐人ID
	 * @var int
	 * @default	0
	 */
	public $invitor_id = 0;

	/**
	 * 手机号码
	 * @var string
	 */
	public $mobile = null;

	/**
	 * 登陆密码
	 * @var string
	 */
	public $password = null;

	/**
	 * 登陆令牌，APP用
	 * @var string
	 */
	public $token = null;

	/**
	 * 用户昵称
	 * @var string
	 */
	public $name = null;

	/**
	 * 用户头像,URL
	 * @var string
	 */
	public $avatar = null;

	/**
	 * 性别,0未知,1男,2女
	 * @var int
	 * @default	0
	 */
	public $gender = 0;

	/**
	 * 出生年月日
	 * @var string
	 * @default	0000-00-00
	 */
	public $birth = '0000-00-00';

	/**
	 * 所在地区
	 * @var int
	 * @default	0
	 */
	public $region_id = 0;

	/**
	 * 用户级别：0无效,1普通
	 * @var int
	 * @default	1
	 */
	public $level_u = 1;

	/**
	 * VIP级别
	 * @var int
	 * @default	0
	 */
	public $level_v = 0;

	/**
	 * 总监级别：0无,1正式,2过期
	 * @var int
	 * @default	0
	 */
	public $level_c = 0;

	/**
	 * 经理级别：0无,1正式,2过期
	 * @var int
	 * @default	0
	 */
	public $level_d = 0;

	/**
	 * 员工级别：0无,1员工,2组长,3经理,4离职
	 * @var int
	 * @default	0
	 */
	public $level_t = 0;

	/**
	 * 后台级别：0无,1普通,2超级
	 * @var int
	 * @default	0
	 */
	public $level_a = 0;

	/**
	 * 供应商级别：0无,1间接,2直接
	 * @var int
	 * @default	0
	 */
	public $level_s = 0;

	/**
	 * IM系统账号，前缀：u_
	 * @var string
	 */
	public $imuid = null;

	/**
	 * 注册时间
	 * @var int
	 * @default	0
	 */
	public $reg_time = 0;

	/**
	 * 注册IP
	 * @var int
	 * @default	0
	 */
	public $reg_from = 0;

	/**
	 * 注册种子参数
	 * @var int
	 * @default	0
	 */
	public $reg_seed = 0;

	/**
	 * 注册其它参数
	 * @var string
	 */
	public $reg_param = null;
}
/**
 * UserEntity Factory<br>
 * 用户
 */
final class UserFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var UserFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : UserFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new UserFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new UserFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : UserEntity {
		$obj = new UserEntity();$obj->id = $row['id'];
		$obj->invitor_id = $row['invitor_id'];
		$obj->mobile = $row['mobile'];
		$obj->password = $row['password'];
		$obj->token = $row['token'];
		$obj->name = $row['name'];
		$obj->avatar = $row['avatar'];
		$obj->gender = $row['gender'];
		$obj->birth = $row['birth'];
		$obj->region_id = $row['region_id'];
		$obj->level_u = $row['level_u'];
		$obj->level_v = $row['level_v'];
		$obj->level_c = $row['level_c'];
		$obj->level_d = $row['level_d'];
		$obj->level_t = $row['level_t'];
		$obj->level_a = $row['level_a'];
		$obj->level_s = $row['level_s'];
		$obj->imuid = $row['imuid'];
		$obj->reg_time = $row['reg_time'];
		$obj->reg_from = $row['reg_from'];
		$obj->reg_seed = $row['reg_seed'];
		$obj->reg_param = $row['reg_param'];
		return $obj;
	}

	private function _object_to_insert(UserEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`user` %s(`id`,`invitor_id`,`mobile`,`password`,`token`,`name`,`avatar`,`gender`,`birth`,`region_id`,`level_u`,`level_v`,`level_c`,`level_d`,`level_t`,`level_a`,`level_s`,`imuid`,`reg_time`,`reg_from`,`reg_seed`,`reg_param`) VALUES (NULL,%d,'%s','%s','%s','%s','%s',%d,'%s',%d,%d,%d,%d,%d,%d,%d,%d,'%s',UNIX_TIMESTAMP(),%d,%d,'%s')";
		return sprintf($sql,'',$obj->invitor_id,self::_encode_string($obj->mobile,11)
			,self::_encode_string($obj->password,40)
			,self::_encode_string($obj->token,16)
			,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->avatar,512)
			,$obj->gender,$obj->birth,$obj->region_id,$obj->level_u,$obj->level_v,$obj->level_c,$obj->level_d,$obj->level_t,$obj->level_a,$obj->level_s,self::_encode_string($obj->imuid,24)
			,$obj->reg_from,$obj->reg_seed,self::_encode_string($obj->reg_param,32)
			);
	}
	private function _object_to_update(UserEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`user` %s SET `invitor_id` = %d,`mobile` = '%s',`password` = '%s',`token` = '%s',`name` = '%s',`avatar` = '%s',`gender` = %d,`birth` = '%s',`region_id` = %d,`level_u` = %d,`level_v` = %d,`level_c` = %d,`level_d` = %d,`level_t` = %d,`level_a` = %d,`level_s` = %d,`imuid` = '%s',`reg_from` = %d,`reg_seed` = %d,`reg_param` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->invitor_id,self::_encode_string($obj->mobile,11)
			,self::_encode_string($obj->password,40)
			,self::_encode_string($obj->token,16)
			,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->avatar,512)
			,$obj->gender,$obj->birth,$obj->region_id,$obj->level_u,$obj->level_v,$obj->level_c,$obj->level_d,$obj->level_t,$obj->level_a,$obj->level_s,self::_encode_string($obj->imuid,24)
			,$obj->reg_from,$obj->reg_seed,self::_encode_string($obj->reg_param,32)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns UserEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`user`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`user` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..用户ID
	 * @returns UserEntity
	 * @returns null
	 */
	public function load(int $id) : ?UserEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..用户ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`user` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 invitor_id 加载一条
	 * @param	int  $invitor_id  ..推荐人ID
	 * @returns UserEntity
	 * @returns null
	 */
	public function loadOneByInvitorId (int $invitor_id) : ?UserEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user` WHERE `invitor_id` = '%d'",
			$invitor_id
		));
		
	}
	/**
	 * 根据普通索引 invitor_id 加载全部
	 * @param	int	$invitor_id	..推荐人ID
	 * @returns UserEntity
	 * @returns null
	 */
	public function loadAllByInvitorId (int $invitor_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user` WHERE `invitor_id` = '%d'",
			$invitor_id
		));
		
	}

	/**
	 * 根据普通索引 mobile 加载一条
	 * @param	string  $mobile  ..手机号码
	 * @returns UserEntity
	 * @returns null
	 */
	public function loadOneByMobile (string $mobile) : ?UserEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user` WHERE `mobile` = '%s'",
			parent::$reader->escape_string($mobile)
		));
		
	}
	/**
	 * 根据普通索引 mobile 加载全部
	 * @param	string	$mobile	..手机号码
	 * @returns UserEntity
	 * @returns null
	 */
	public function loadAllByMobile (string $mobile) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user` WHERE `mobile` = '%s'",
			parent::$reader->escape_string($mobile)
		));
		
	}

	/**
	 * 根据普通索引 token 加载一条
	 * @param	string  $token  ..登陆令牌，APP用
	 * @returns UserEntity
	 * @returns null
	 */
	public function loadOneByToken (string $token) : ?UserEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user` WHERE `token` = '%s'",
			parent::$reader->escape_string($token)
		));
		
	}
	/**
	 * 根据普通索引 token 加载全部
	 * @param	string	$token	..登陆令牌，APP用
	 * @returns UserEntity
	 * @returns null
	 */
	public function loadAllByToken (string $token) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user` WHERE `token` = '%s'",
			parent::$reader->escape_string($token)
		));
		
	}

	/**
	 * 向数据表 yuemi_main.user 插入一条新纪录
	 * @param	UserEntity    $obj    ..用户
	 * @returns bool
	 */
	public function insert(UserEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.user 回写一条记录<br>
	 * 更新依据： yuemi_main.user.id
	 * @param	UserEntity	  $obj    ..用户
	 * @returns bool
	 */
	 public function update(UserEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 用户收货地址
 * @table user_address
 * @engine innodb
 */
final class UserAddressEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 地区ID
	 * @var int
	 * @default	0
	 */
	public $region_id = 0;

	/**
	 * 详细地址
	 * @var string
	 */
	public $address = null;

	/**
	 * 联系人
	 * @var string
	 */
	public $contacts = null;

	/**
	 * 联系电话
	 * @var string
	 */
	public $mobile = null;

	/**
	 * 是否默认
	 * @var int
	 * @default	0
	 */
	public $is_default = 0;

	/**
	 * 状态 0删除,1可用
	 * @var int
	 * @default	1
	 */
	public $status = 1;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;
}
/**
 * UserAddressEntity Factory<br>
 * 用户收货地址
 */
final class UserAddressFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var UserAddressFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : UserAddressFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new UserAddressFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new UserAddressFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_address`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_address` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : UserAddressEntity {
		$obj = new UserAddressEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->region_id = $row['region_id'];
		$obj->address = $row['address'];
		$obj->contacts = $row['contacts'];
		$obj->mobile = $row['mobile'];
		$obj->is_default = $row['is_default'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(UserAddressEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`user_address` %s(`id`,`user_id`,`region_id`,`address`,`contacts`,`mobile`,`is_default`,`status`,`create_time`,`create_from`) VALUES (NULL,%d,%d,'%s','%s','%s',%d,%d,UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->user_id,$obj->region_id,self::_encode_string($obj->address,256)
			,self::_encode_string($obj->contacts,16)
			,self::_encode_string($obj->mobile,16)
			,$obj->is_default,$obj->status,$obj->create_from);
	}
	private function _object_to_update(UserAddressEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`user_address` %s SET `user_id` = %d,`region_id` = %d,`address` = '%s',`contacts` = '%s',`mobile` = '%s',`is_default` = %d,`status` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->region_id,self::_encode_string($obj->address,256)
			,self::_encode_string($obj->contacts,16)
			,self::_encode_string($obj->mobile,16)
			,$obj->is_default,$obj->status,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns UserAddressEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`user_address`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`user_address` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns UserAddressEntity
	 * @returns null
	 */
	public function load(int $id) : ?UserAddressEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_address` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`user_address` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns UserAddressEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?UserAddressEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_address` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns UserAddressEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_address` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..状态 0删除,1可用
	 * @returns UserAddressEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?UserAddressEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_address` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..状态 0删除,1可用
	 * @returns UserAddressEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_address` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.user_address 插入一条新纪录
	 * @param	UserAddressEntity    $obj    ..用户收货地址
	 * @returns bool
	 */
	public function insert(UserAddressEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.user_address 回写一条记录<br>
	 * 更新依据： yuemi_main.user_address.id
	 * @param	UserAddressEntity	  $obj    ..用户收货地址
	 * @returns bool
	 */
	 public function update(UserAddressEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 用户绑定银行卡
 * @table user_bank
 * @engine innodb
 */
final class UserBankEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 银行ID
	 * @var int
	 */
	public $bank_id = null;

	/**
	 * 开户地区ID
	 * @var int
	 * @default	0
	 */
	public $region_id = 0;

	/**
	 * 开户行名称
	 * @var string
	 */
	public $bank_name = null;

	/**
	 * 卡号
	 * @var string
	 */
	public $card_no = null;

	/**
	 * 用户状态 0删除,1可用,2正确,3错误
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 审核人
	 * @var int
	 * @default	0
	 */
	public $audit_user = 0;

	/**
	 * 审核时间
	 * @var int
	 * @default	0
	 */
	public $audit_time = 0;

	/**
	 * 审核IP
	 * @var int
	 * @default	0
	 */
	public $audit_from = 0;
}
/**
 * UserBankEntity Factory<br>
 * 用户绑定银行卡
 */
final class UserBankFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var UserBankFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : UserBankFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new UserBankFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new UserBankFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_bank`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_bank` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : UserBankEntity {
		$obj = new UserBankEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->bank_id = $row['bank_id'];
		$obj->region_id = $row['region_id'];
		$obj->bank_name = $row['bank_name'];
		$obj->card_no = $row['card_no'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(UserBankEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`user_bank` %s(`id`,`user_id`,`bank_id`,`region_id`,`bank_name`,`card_no`,`status`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`) VALUES (NULL,%d,%d,%d,'%s','%s',%d,UNIX_TIMESTAMP(),%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->user_id,$obj->bank_id,$obj->region_id,self::_encode_string($obj->bank_name,64)
			,self::_encode_string($obj->card_no,128)
			,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(UserBankEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`user_bank` %s SET `user_id` = %d,`bank_id` = %d,`region_id` = %d,`bank_name` = '%s',`card_no` = '%s',`status` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->bank_id,$obj->region_id,self::_encode_string($obj->bank_name,64)
			,self::_encode_string($obj->card_no,128)
			,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns UserBankEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`user_bank`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`user_bank` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns UserBankEntity
	 * @returns null
	 */
	public function load(int $id) : ?UserBankEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_bank` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`user_bank` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns UserBankEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?UserBankEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_bank` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns UserBankEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_bank` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..用户状态 0删除,1可用,2正确,3错误
	 * @returns UserBankEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?UserBankEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_bank` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..用户状态 0删除,1可用,2正确,3错误
	 * @returns UserBankEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_bank` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.user_bank 插入一条新纪录
	 * @param	UserBankEntity    $obj    ..用户绑定银行卡
	 * @returns bool
	 */
	public function insert(UserBankEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.user_bank 回写一条记录<br>
	 * 更新依据： yuemi_main.user_bank.id
	 * @param	UserBankEntity	  $obj    ..用户绑定银行卡
	 * @returns bool
	 */
	 public function update(UserBankEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 用户实名认证
 * @table user_cert
 * @engine innodb
 */
final class UserCertEntity extends \Ziima\Data\Entity {
	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 身份证图像，正面JPG
	 * @var string
	 */
	public $card_pic1 = null;

	/**
	 * 身份证图像，反面JPG
	 * @var string
	 */
	public $card_pic2 = null;

	/**
	 * 身份证号码
	 * @var string
	 */
	public $card_no = null;

	/**
	 * 身份证上的姓名
	 * @var string
	 */
	public $card_name = null;

	/**
	 * 识别出来的过期时间
	 * @var string
	 * @default	0000-00-00
	 */
	public $card_exp = '0000-00-00';

	/**
	 * 认证状态 0=草稿,1=待审,2=通过,3=拒绝
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 审核人
	 * @var int
	 * @default	0
	 */
	public $audit_user = 0;

	/**
	 * 审核时间
	 * @var int
	 * @default	0
	 */
	public $audit_time = 0;

	/**
	 * 审核IP
	 * @var int
	 * @default	0
	 */
	public $audit_from = 0;
}
/**
 * UserCertEntity Factory<br>
 * 用户实名认证
 */
final class UserCertFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var UserCertFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : UserCertFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new UserCertFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new UserCertFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_cert`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_cert` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : UserCertEntity {
		$obj = new UserCertEntity();$obj->user_id = $row['user_id'];
		$obj->card_pic1 = $row['card_pic1'];
		$obj->card_pic2 = $row['card_pic2'];
		$obj->card_no = $row['card_no'];
		$obj->card_name = $row['card_name'];
		$obj->card_exp = $row['card_exp'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(UserCertEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`user_cert` %s(`user_id`,`card_pic1`,`card_pic2`,`card_no`,`card_name`,`card_exp`,`status`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`) VALUES (%d,'%s','%s','%s','%s','%s',%d,UNIX_TIMESTAMP(),%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->card_pic1,512)
			,self::_encode_string($obj->card_pic2,512)
			,self::_encode_string($obj->card_no,18)
			,self::_encode_string($obj->card_name,16)
			,$obj->card_exp,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(UserCertEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`user_cert` %s SET `card_pic1` = '%s',`card_pic2` = '%s',`card_no` = '%s',`card_name` = '%s',`card_exp` = '%s',`status` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d WHERE `user_id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->card_pic1,512)
			,self::_encode_string($obj->card_pic2,512)
			,self::_encode_string($obj->card_no,18)
			,self::_encode_string($obj->card_name,16)
			,$obj->card_exp,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->user_id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns UserCertEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`user_cert`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`user_cert` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "user_id" 加载一条
	 * @param	int	$user_id	..用户ID
	 * @returns UserCertEntity
	 * @returns null
	 */
	public function load(int $user_id) : ?UserCertEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_cert` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据主键 "user_id" 删除一条
	 * @param	int	$user_id	..用户ID
	 * @returns bool
	 */
	public function delete(int $user_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`user_cert` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据普通索引 card_no 加载一条
	 * @param	string  $card_no  ..身份证号码
	 * @returns UserCertEntity
	 * @returns null
	 */
	public function loadOneByCardNo (string $card_no) : ?UserCertEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_cert` WHERE `card_no` = '%s'",
			parent::$reader->escape_string($card_no)
		));
		
	}
	/**
	 * 根据普通索引 card_no 加载全部
	 * @param	string	$card_no	..身份证号码
	 * @returns UserCertEntity
	 * @returns null
	 */
	public function loadAllByCardNo (string $card_no) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_cert` WHERE `card_no` = '%s'",
			parent::$reader->escape_string($card_no)
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..认证状态 0=草稿,1=待审,2=通过,3=拒绝
	 * @returns UserCertEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?UserCertEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_cert` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..认证状态 0=草稿,1=待审,2=通过,3=拒绝
	 * @returns UserCertEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_cert` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 根据普通索引 create_time 加载一条
	 * @param	int  $create_time  ..创建时间
	 * @returns UserCertEntity
	 * @returns null
	 */
	public function loadOneByCreateTime (int $create_time) : ?UserCertEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_cert` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}
	/**
	 * 根据普通索引 create_time 加载全部
	 * @param	int	$create_time	..创建时间
	 * @returns UserCertEntity
	 * @returns null
	 */
	public function loadAllByCreateTime (int $create_time) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_cert` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}

	/**
	 * 向数据表 yuemi_main.user_cert 插入一条新纪录
	 * @param	UserCertEntity    $obj    ..用户实名认证
	 * @returns bool
	 */
	public function insert(UserCertEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.user_cert 回写一条记录<br>
	 * 更新依据： yuemi_main.user_cert.user_id
	 * @param	UserCertEntity	  $obj    ..用户实名认证
	 * @returns bool
	 */
	 public function update(UserCertEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 充值记录
 * @table user_charge
 * @engine innodb
 */
final class UserChargeEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 前台用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 支付渠道ID：参见文档
	 * @var int
	 * @default	0
	 */
	public $channel_id = 0;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 充值订单ID
	 * @var string
	 */
	public $payment_id = null;

	/**
	 * 充值账户特征
	 * @var string
	 */
	public $account = null;

	/**
	 * 充值金额
	 * @var float
	 */
	public $money = null;

	/**
	 * 充值订单状态
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var string
	 */
	public $create_time = null;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 支付时间
	 * @var string
	 */
	public $accept_time = null;
}
/**
 * UserChargeEntity Factory<br>
 * 充值记录
 */
final class UserChargeFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var UserChargeFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : UserChargeFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new UserChargeFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new UserChargeFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_charge`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_charge` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : UserChargeEntity {
		$obj = new UserChargeEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->channel_id = $row['channel_id'];
		$obj->order_id = $row['order_id'];
		$obj->payment_id = $row['payment_id'];
		$obj->account = $row['account'];
		$obj->money = $row['money'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->accept_time = $row['accept_time'];
		return $obj;
	}

	private function _object_to_insert(UserChargeEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`user_charge` %s(`id`,`user_id`,`channel_id`,`order_id`,`payment_id`,`account`,`money`,`status`,`create_time`,`create_from`,`accept_time`) VALUES (NULL,%d,%d,'%s','%s','%s',%f,%d,NOW(),%d,'%s')";
		return sprintf($sql,'',$obj->user_id,$obj->channel_id,self::_encode_string($obj->order_id,16)
			,self::_encode_string($obj->payment_id,32)
			,self::_encode_string($obj->account,64)
			,$obj->money,$obj->status,$obj->create_from,$obj->accept_time);
	}
	private function _object_to_update(UserChargeEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`user_charge` %s SET `user_id` = %d,`channel_id` = %d,`order_id` = '%s',`payment_id` = '%s',`account` = '%s',`money` = %f,`status` = %d,`create_from` = %d,`accept_time` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->channel_id,self::_encode_string($obj->order_id,16)
			,self::_encode_string($obj->payment_id,32)
			,self::_encode_string($obj->account,64)
			,$obj->money,$obj->status,$obj->create_from,$obj->accept_time,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns UserChargeEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`user_charge`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`user_charge` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns UserChargeEntity
	 * @returns null
	 */
	public function load(int $id) : ?UserChargeEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_charge` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`user_charge` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..前台用户ID
	 * @returns UserChargeEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?UserChargeEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_charge` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..前台用户ID
	 * @returns UserChargeEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_charge` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 order_id 加载一条
	 * @param	string  $order_id  ..订单ID
	 * @returns UserChargeEntity
	 * @returns null
	 */
	public function loadOneByOrderId (string $order_id) : ?UserChargeEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_charge` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}
	/**
	 * 根据普通索引 order_id 加载全部
	 * @param	string	$order_id	..订单ID
	 * @returns UserChargeEntity
	 * @returns null
	 */
	public function loadAllByOrderId (string $order_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_charge` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..充值订单状态
	 * @returns UserChargeEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?UserChargeEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_charge` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..充值订单状态
	 * @returns UserChargeEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_charge` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.user_charge 插入一条新纪录
	 * @param	UserChargeEntity    $obj    ..充值记录
	 * @returns bool
	 */
	public function insert(UserChargeEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.user_charge 回写一条记录<br>
	 * 更新依据： yuemi_main.user_charge.id
	 * @param	UserChargeEntity	  $obj    ..充值记录
	 * @returns bool
	 */
	 public function update(UserChargeEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 账户
 * @table user_finance
 * @engine innodb
 */
final class UserFinanceEntity extends \Ziima\Data\Entity {
	/**
	 * 前台用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 现金余额,全账户
	 * @var float
	 * @default	0.0000
	 */
	public $money = 0.0000;

	/**
	 * 阅币余额,区块链货币
	 * @var float
	 * @default	0.00000000
	 */
	public $coin = 0.00000000;

	/**
	 * 佣金余额,自买省的
	 * @var float
	 * @default	0.0000
	 */
	public $profit_self = 0.0000;

	/**
	 * 佣金余额,分享赚的
	 * @var float
	 * @default	0.0000
	 */
	public $profit_share = 0.0000;

	/**
	 * 佣金余额,团队管理佣金
	 * @var float
	 * @default	0.0000
	 */
	public $profit_team = 0.0000;

	/**
	 * 礼包佣金,直接招聘佣金
	 * @var float
	 * @default	0.0000
	 */
	public $recruit_dir = 0.0000;

	/**
	 * 礼包佣金,间接招聘佣金
	 * @var float
	 * @default	0.0000
	 */
	public $recruit_alt = 0.0000;

	/**
	 * 支付密码
	 * @var string
	 */
	public $passwd = null;

	/**
	 * 直接礼包佣金解锁任务状态：0未解锁,1已解锁
	 * @var int
	 * @default	0
	 */
	public $thew_status = 0;

	/**
	 * 直接礼包佣金解锁任务开始时间
	 * @var int
	 * @default	0
	 */
	public $thew_launch = 0;

	/**
	 * 直接礼包佣金解锁目标佣金
	 * @var float
	 * @default	0.0000
	 */
	public $thew_target = 0.0000;

	/**
	 * 直接礼包佣金解锁累积消费佣金
	 * @var float
	 * @default	0.0000
	 */
	public $thew_money = 0.0000;

	/**
	 * 直接礼包佣金解锁开始时间
	 * @var int
	 * @default	0
	 */
	public $thew_start = 0;

	/**
	 * 直接礼包佣金解锁到期时间
	 * @var int
	 * @default	0
	 */
	public $thew_expire = 0;

	/**
	 * 晋升总监任务状态：0未解锁,1已解锁
	 * @var int
	 * @default	0
	 */
	public $cheif_status = 0;

	/**
	 * 晋升总监任务开始时间（等于注册日期）
	 * @var int
	 * @default	0
	 */
	public $cheif_start = 0;

	/**
	 * 晋升总监任务开始时间结束时间（3个月后）
	 * @var int
	 * @default	0
	 */
	public $cheif_expire = 0;

	/**
	 * 晋升总监需要直招人数
	 * @var int
	 * @default	0
	 */
	public $cheif_target_dir = 0;

	/**
	 * 晋升总监需要间招人数
	 * @var int
	 * @default	0
	 */
	public $cheif_target_alt = 0;

	/**
	 * 晋升总监需要直招人数
	 * @var int
	 * @default	0
	 */
	public $cheif_value_dir = 0;

	/**
	 * 晋升总监需要间招人数
	 * @var int
	 * @default	0
	 */
	public $cheif_value_alt = 0;

	/**
	 * 晋升总经理任务状态：0未解锁,1已解锁
	 * @var int
	 * @default	0
	 */
	public $director_status = 0;

	/**
	 * 晋升总经理任务开始时间（等于注册日期）
	 * @var int
	 * @default	0
	 */
	public $director_start = 0;

	/**
	 * 晋升总经理任务开始时间结束时间（3个月后）
	 * @var int
	 * @default	0
	 */
	public $director_expire = 0;

	/**
	 * 晋升总经理需要团队人数
	 * @var int
	 * @default	0
	 */
	public $director_target_team = 0;

	/**
	 * 晋升总经理需要总监人数
	 * @var int
	 * @default	0
	 */
	public $director_target_cheif = 0;

	/**
	 * 晋升总经理需要团队人数
	 * @var int
	 * @default	0
	 */
	public $director_value_team = 0;

	/**
	 * 晋升总经理需要总监人数
	 * @var int
	 * @default	0
	 */
	public $director_value_cheif = 0;
}
/**
 * UserFinanceEntity Factory<br>
 * 账户
 */
final class UserFinanceFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var UserFinanceFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : UserFinanceFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new UserFinanceFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new UserFinanceFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_finance`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_finance` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : UserFinanceEntity {
		$obj = new UserFinanceEntity();$obj->user_id = $row['user_id'];
		$obj->money = $row['money'];
		$obj->coin = $row['coin'];
		$obj->profit_self = $row['profit_self'];
		$obj->profit_share = $row['profit_share'];
		$obj->profit_team = $row['profit_team'];
		$obj->recruit_dir = $row['recruit_dir'];
		$obj->recruit_alt = $row['recruit_alt'];
		$obj->passwd = $row['passwd'];
		$obj->thew_status = $row['thew_status'];
		$obj->thew_launch = $row['thew_launch'];
		$obj->thew_target = $row['thew_target'];
		$obj->thew_money = $row['thew_money'];
		$obj->thew_start = $row['thew_start'];
		$obj->thew_expire = $row['thew_expire'];
		$obj->cheif_status = $row['cheif_status'];
		$obj->cheif_start = $row['cheif_start'];
		$obj->cheif_expire = $row['cheif_expire'];
		$obj->cheif_target_dir = $row['cheif_target_dir'];
		$obj->cheif_target_alt = $row['cheif_target_alt'];
		$obj->cheif_value_dir = $row['cheif_value_dir'];
		$obj->cheif_value_alt = $row['cheif_value_alt'];
		$obj->director_status = $row['director_status'];
		$obj->director_start = $row['director_start'];
		$obj->director_expire = $row['director_expire'];
		$obj->director_target_team = $row['director_target_team'];
		$obj->director_target_cheif = $row['director_target_cheif'];
		$obj->director_value_team = $row['director_value_team'];
		$obj->director_value_cheif = $row['director_value_cheif'];
		return $obj;
	}

	private function _object_to_insert(UserFinanceEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`user_finance` %s(`user_id`,`money`,`coin`,`profit_self`,`profit_share`,`profit_team`,`recruit_dir`,`recruit_alt`,`passwd`,`thew_status`,`thew_launch`,`thew_target`,`thew_money`,`thew_start`,`thew_expire`,`cheif_status`,`cheif_start`,`cheif_expire`,`cheif_target_dir`,`cheif_target_alt`,`cheif_value_dir`,`cheif_value_alt`,`director_status`,`director_start`,`director_expire`,`director_target_team`,`director_target_cheif`,`director_value_team`,`director_value_cheif`) VALUES (%d,%f,%f,%f,%f,%f,%f,%f,'%s',%d,%d,%f,%f,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->user_id,$obj->money,$obj->coin,$obj->profit_self,$obj->profit_share,$obj->profit_team,$obj->recruit_dir,$obj->recruit_alt,self::_encode_string($obj->passwd,40)
			,$obj->thew_status,$obj->thew_launch,$obj->thew_target,$obj->thew_money,$obj->thew_start,$obj->thew_expire,$obj->cheif_status,$obj->cheif_start,$obj->cheif_expire,$obj->cheif_target_dir,$obj->cheif_target_alt,$obj->cheif_value_dir,$obj->cheif_value_alt,$obj->director_status,$obj->director_start,$obj->director_expire,$obj->director_target_team,$obj->director_target_cheif,$obj->director_value_team,$obj->director_value_cheif);
	}
	private function _object_to_update(UserFinanceEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`user_finance` %s SET `money` = %f,`coin` = %f,`profit_self` = %f,`profit_share` = %f,`profit_team` = %f,`recruit_dir` = %f,`recruit_alt` = %f,`passwd` = '%s',`thew_status` = %d,`thew_launch` = %d,`thew_target` = %f,`thew_money` = %f,`thew_start` = %d,`thew_expire` = %d,`cheif_status` = %d,`cheif_start` = %d,`cheif_expire` = %d,`cheif_target_dir` = %d,`cheif_target_alt` = %d,`cheif_value_dir` = %d,`cheif_value_alt` = %d,`director_status` = %d,`director_start` = %d,`director_expire` = %d,`director_target_team` = %d,`director_target_cheif` = %d,`director_value_team` = %d,`director_value_cheif` = %d WHERE `user_id` = %d";
		
		return sprintf($sql,'',$obj->money,$obj->coin,$obj->profit_self,$obj->profit_share,$obj->profit_team,$obj->recruit_dir,$obj->recruit_alt,self::_encode_string($obj->passwd,40)
			,$obj->thew_status,$obj->thew_launch,$obj->thew_target,$obj->thew_money,$obj->thew_start,$obj->thew_expire,$obj->cheif_status,$obj->cheif_start,$obj->cheif_expire,$obj->cheif_target_dir,$obj->cheif_target_alt,$obj->cheif_value_dir,$obj->cheif_value_alt,$obj->director_status,$obj->director_start,$obj->director_expire,$obj->director_target_team,$obj->director_target_cheif,$obj->director_value_team,$obj->director_value_cheif,$obj->user_id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns UserFinanceEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`user_finance`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`user_finance` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "user_id" 加载一条
	 * @param	int	$user_id	..前台用户ID
	 * @returns UserFinanceEntity
	 * @returns null
	 */
	public function load(int $user_id) : ?UserFinanceEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_finance` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据主键 "user_id" 删除一条
	 * @param	int	$user_id	..前台用户ID
	 * @returns bool
	 */
	public function delete(int $user_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`user_finance` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据普通索引 money 加载一条
	 * @param	float  $money  ..现金余额,全账户
	 * @returns UserFinanceEntity
	 * @returns null
	 */
	public function loadOneByMoney (float $money) : ?UserFinanceEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_finance` WHERE `money` = '%f'",
			$money
		));
		
	}
	/**
	 * 根据普通索引 money 加载全部
	 * @param	float	$money	..现金余额,全账户
	 * @returns UserFinanceEntity
	 * @returns null
	 */
	public function loadAllByMoney (float $money) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_finance` WHERE `money` = '%f'",
			$money
		));
		
	}

	/**
	 * 根据普通索引 coin 加载一条
	 * @param	float  $coin  ..阅币余额,区块链货币
	 * @returns UserFinanceEntity
	 * @returns null
	 */
	public function loadOneByCoin (float $coin) : ?UserFinanceEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_finance` WHERE `coin` = '%f'",
			$coin
		));
		
	}
	/**
	 * 根据普通索引 coin 加载全部
	 * @param	float	$coin	..阅币余额,区块链货币
	 * @returns UserFinanceEntity
	 * @returns null
	 */
	public function loadAllByCoin (float $coin) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_finance` WHERE `coin` = '%f'",
			$coin
		));
		
	}

	/**
	 * 向数据表 yuemi_main.user_finance 插入一条新纪录
	 * @param	UserFinanceEntity    $obj    ..账户
	 * @returns bool
	 */
	public function insert(UserFinanceEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.user_finance 回写一条记录<br>
	 * 更新依据： yuemi_main.user_finance.user_id
	 * @param	UserFinanceEntity	  $obj    ..账户
	 * @returns bool
	 */
	 public function update(UserFinanceEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 用户素材
 * @table user_material
 * @engine innodb
 */
final class UserMaterialEntity extends \Ziima\Data\Entity {
	/**
	 * 素材ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 关联SKUID
	 * @var int
	 * @default	0
	 */
	public $sku_id = 0;

	/**
	 * 文件大小：字节
	 * @var int
	 * @default	0
	 */
	public $file_size = 0;

	/**
	 * 访问路径
	 * @var string
	 */
	public $file_url = null;

	/**
	 * 图片宽度
	 * @var int
	 * @default	0
	 */
	public $image_width = 0;

	/**
	 * 图片高度
	 * @var int
	 * @default	0
	 */
	public $image_height = 0;

	/**
	 * 缩略图路径
	 * @var string
	 */
	public $thumb_url = null;

	/**
	 * 缩略图大小：字节
	 * @var int
	 * @default	0
	 */
	public $thumb_size = 0;

	/**
	 * 缩略图宽度
	 * @var int
	 * @default	0
	 */
	public $thumb_width = 0;

	/**
	 * 缩略图高度
	 * @var int
	 * @default	0
	 */
	public $thumb_height = 0;

	/**
	 * 素材状态 0待审,1已审,2删除
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 审核人
	 * @var int
	 * @default	0
	 */
	public $audit_user = 0;

	/**
	 * 审核时间
	 * @var int
	 * @default	0
	 */
	public $audit_time = 0;

	/**
	 * 审核IP
	 * @var int
	 * @default	0
	 */
	public $audit_from = 0;
}
/**
 * UserMaterialEntity Factory<br>
 * 用户素材
 */
final class UserMaterialFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var UserMaterialFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : UserMaterialFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new UserMaterialFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new UserMaterialFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_material`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_material` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : UserMaterialEntity {
		$obj = new UserMaterialEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->sku_id = $row['sku_id'];
		$obj->file_size = $row['file_size'];
		$obj->file_url = $row['file_url'];
		$obj->image_width = $row['image_width'];
		$obj->image_height = $row['image_height'];
		$obj->thumb_url = $row['thumb_url'];
		$obj->thumb_size = $row['thumb_size'];
		$obj->thumb_width = $row['thumb_width'];
		$obj->thumb_height = $row['thumb_height'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(UserMaterialEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`user_material` %s(`id`,`user_id`,`sku_id`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`status`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`) VALUES (NULL,%d,%d,%d,'%s',%d,%d,'%s',%d,%d,%d,%d,UNIX_TIMESTAMP(),%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->user_id,$obj->sku_id,$obj->file_size,self::_encode_string($obj->file_url,512)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,512)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(UserMaterialEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`user_material` %s SET `user_id` = %d,`sku_id` = %d,`file_size` = %d,`file_url` = '%s',`image_width` = %d,`image_height` = %d,`thumb_url` = '%s',`thumb_size` = %d,`thumb_width` = %d,`thumb_height` = %d,`status` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->sku_id,$obj->file_size,self::_encode_string($obj->file_url,512)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,512)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns UserMaterialEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`user_material`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`user_material` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..素材ID
	 * @returns UserMaterialEntity
	 * @returns null
	 */
	public function load(int $id) : ?UserMaterialEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_material` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..素材ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`user_material` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns UserMaterialEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?UserMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_material` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns UserMaterialEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_material` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 sku_id 加载一条
	 * @param	int  $sku_id  ..关联SKUID
	 * @returns UserMaterialEntity
	 * @returns null
	 */
	public function loadOneBySkuId (int $sku_id) : ?UserMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_material` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}
	/**
	 * 根据普通索引 sku_id 加载全部
	 * @param	int	$sku_id	..关联SKUID
	 * @returns UserMaterialEntity
	 * @returns null
	 */
	public function loadAllBySkuId (int $sku_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_material` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..素材状态 0待审,1已审,2删除
	 * @returns UserMaterialEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?UserMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_material` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..素材状态 0待审,1已审,2删除
	 * @returns UserMaterialEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_material` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_main.user_material 插入一条新纪录
	 * @param	UserMaterialEntity    $obj    ..用户素材
	 * @returns bool
	 */
	public function insert(UserMaterialEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.user_material 回写一条记录<br>
	 * 更新依据： yuemi_main.user_material.id
	 * @param	UserMaterialEntity	  $obj    ..用户素材
	 * @returns bool
	 */
	 public function update(UserMaterialEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 用户领券
 * @table user_ticket
 * @engine innodb
 */
final class UserTicketEntity extends \Ziima\Data\Entity {
	/**
	 * 优惠券ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 前台用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 优惠券ID
	 * @var int
	 * @default	0
	 */
	public $ticket_id = 0;

	/**
	 * 面额
	 * @var float
	 */
	public $money = null;

	/**
	 * 优惠券状态
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 使用时间
	 * @var int
	 * @default	0
	 */
	public $use_time = 0;

	/**
	 * 使用IP
	 * @var int
	 * @default	0
	 */
	public $use_from = 0;

	/**
	 * 关联订单
	 * @var string
	 */
	public $use_order = null;

	/**
	 * 关联商品
	 * @var string
	 */
	public $use_item = null;
}
/**
 * UserTicketEntity Factory<br>
 * 用户领券
 */
final class UserTicketFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var UserTicketFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : UserTicketFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new UserTicketFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new UserTicketFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_ticket`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_ticket` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : UserTicketEntity {
		$obj = new UserTicketEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->ticket_id = $row['ticket_id'];
		$obj->money = $row['money'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->use_time = $row['use_time'];
		$obj->use_from = $row['use_from'];
		$obj->use_order = $row['use_order'];
		$obj->use_item = $row['use_item'];
		return $obj;
	}

	private function _object_to_insert(UserTicketEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`user_ticket` %s(`id`,`user_id`,`ticket_id`,`money`,`status`,`create_time`,`create_from`,`use_time`,`use_from`,`use_order`,`use_item`) VALUES (NULL,%d,%d,%f,%d,UNIX_TIMESTAMP(),%d,%d,%d,'%s','%s')";
		return sprintf($sql,'',$obj->user_id,$obj->ticket_id,$obj->money,$obj->status,$obj->create_from,$obj->use_time,$obj->use_from,self::_encode_string($obj->use_order,24)
			,self::_encode_string($obj->use_item,24)
			);
	}
	private function _object_to_update(UserTicketEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`user_ticket` %s SET `user_id` = %d,`ticket_id` = %d,`money` = %f,`status` = %d,`create_from` = %d,`use_time` = %d,`use_from` = %d,`use_order` = '%s',`use_item` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->ticket_id,$obj->money,$obj->status,$obj->create_from,$obj->use_time,$obj->use_from,self::_encode_string($obj->use_order,24)
			,self::_encode_string($obj->use_item,24)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns UserTicketEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`user_ticket`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`user_ticket` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..优惠券ID
	 * @returns UserTicketEntity
	 * @returns null
	 */
	public function load(int $id) : ?UserTicketEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_ticket` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..优惠券ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`user_ticket` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..前台用户ID
	 * @returns UserTicketEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?UserTicketEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_ticket` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..前台用户ID
	 * @returns UserTicketEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_ticket` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..优惠券状态
	 * @returns UserTicketEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?UserTicketEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_ticket` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..优惠券状态
	 * @returns UserTicketEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_ticket` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 根据普通索引 use_order 加载一条
	 * @param	string  $use_order  ..关联订单
	 * @returns UserTicketEntity
	 * @returns null
	 */
	public function loadOneByUseOrder (string $use_order) : ?UserTicketEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_ticket` WHERE `use_order` = '%s'",
			parent::$reader->escape_string($use_order)
		));
		
	}
	/**
	 * 根据普通索引 use_order 加载全部
	 * @param	string	$use_order	..关联订单
	 * @returns UserTicketEntity
	 * @returns null
	 */
	public function loadAllByUseOrder (string $use_order) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_ticket` WHERE `use_order` = '%s'",
			parent::$reader->escape_string($use_order)
		));
		
	}

	/**
	 * 向数据表 yuemi_main.user_ticket 插入一条新纪录
	 * @param	UserTicketEntity    $obj    ..用户领券
	 * @returns bool
	 */
	public function insert(UserTicketEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.user_ticket 回写一条记录<br>
	 * 更新依据： yuemi_main.user_ticket.id
	 * @param	UserTicketEntity	  $obj    ..用户领券
	 * @returns bool
	 */
	 public function update(UserTicketEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 用户的微信授权
 * @table user_wechat
 * @engine innodb
 */
final class UserWechatEntity extends \Ziima\Data\Entity {
	/**
	 * 认证记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 用户IDiedu_core.user.id
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 推荐人ID
	 * @var int
	 * @default	0
	 */
	public $invitor_id = 0;

	/**
	 * 开放平台ID
	 * @var string
	 */
	public $union_id = null;

	/**
	 * APP的OpenId
	 * @var string
	 */
	public $app_open_id = null;

	/**
	 * Web的OpenId
	 * @var string
	 */
	public $web_open_id = null;

	/**
	 * 授权密钥
	 * @var string
	 */
	public $auth_code = null;

	/**
	 * 授权更新时间
	 * @var string
	 */
	public $auth_update = null;

	/**
	 * 授权有效期
	 * @var string
	 */
	public $auth_expire = null;

	/**
	 * 微信账号
	 * @var string
	 */
	public $account = null;

	/**
	 * 手机号码
	 * @var string
	 */
	public $mobile = null;

	/**
	 * 昵称
	 * @var string
	 */
	public $name = null;

	/**
	 * 用户头像,URL
	 * @var string
	 */
	public $avatar = null;

	/**
	 * 性别,0未知,1男,2女
	 * @var int
	 * @default	0
	 */
	public $gender = 0;

	/**
	 * 出生年月日
	 * @var string
	 */
	public $birth = null;

	/**
	 * 所在地区
	 * @var int
	 * @default	0
	 */
	public $region_id = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $update_time = 0;

	/**
	 * 更新 IP
	 * @var int
	 * @default	0
	 */
	public $update_from = 0;

	/**
	 * 标签：裂变种子号
	 * @var int
	 * @default	0
	 */
	public $tag_seed = 0;

	/**
	 * 标签：裂变其它参数
	 * @var string
	 */
	public $tag_param = null;
}
/**
 * UserWechatEntity Factory<br>
 * 用户的微信授权
 */
final class UserWechatFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var UserWechatFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : UserWechatFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new UserWechatFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new UserWechatFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_wechat`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_wechat` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : UserWechatEntity {
		$obj = new UserWechatEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->invitor_id = $row['invitor_id'];
		$obj->union_id = $row['union_id'];
		$obj->app_open_id = $row['app_open_id'];
		$obj->web_open_id = $row['web_open_id'];
		$obj->auth_code = $row['auth_code'];
		$obj->auth_update = $row['auth_update'];
		$obj->auth_expire = $row['auth_expire'];
		$obj->account = $row['account'];
		$obj->mobile = $row['mobile'];
		$obj->name = $row['name'];
		$obj->avatar = $row['avatar'];
		$obj->gender = $row['gender'];
		$obj->birth = $row['birth'];
		$obj->region_id = $row['region_id'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->update_time = $row['update_time'];
		$obj->update_from = $row['update_from'];
		$obj->tag_seed = $row['tag_seed'];
		$obj->tag_param = $row['tag_param'];
		return $obj;
	}

	private function _object_to_insert(UserWechatEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`user_wechat` %s(`id`,`user_id`,`invitor_id`,`union_id`,`app_open_id`,`web_open_id`,`auth_code`,`auth_update`,`auth_expire`,`account`,`mobile`,`name`,`avatar`,`gender`,`birth`,`region_id`,`create_time`,`create_from`,`update_time`,`update_from`,`tag_seed`,`tag_param`) VALUES (NULL,%d,%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',%d,'%s',%d,%d,%d,%d,%d,%d,'%s')";
		return sprintf($sql,'',$obj->user_id,$obj->invitor_id,self::_encode_string($obj->union_id,64)
			,self::_encode_string($obj->app_open_id,64)
			,self::_encode_string($obj->web_open_id,64)
			,self::_encode_string($obj->auth_code,128)
			,$obj->auth_update,$obj->auth_expire,self::_encode_string($obj->account,64)
			,self::_encode_string($obj->mobile,16)
			,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->avatar,2048)
			,$obj->gender,$obj->birth,$obj->region_id,$obj->create_time,$obj->create_from,$obj->update_time,$obj->update_from,$obj->tag_seed,self::_encode_string($obj->tag_param,64)
			);
	}
	private function _object_to_update(UserWechatEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`user_wechat` %s SET `user_id` = %d,`invitor_id` = %d,`union_id` = '%s',`app_open_id` = '%s',`web_open_id` = '%s',`auth_code` = '%s',`auth_update` = '%s',`auth_expire` = '%s',`account` = '%s',`mobile` = '%s',`name` = '%s',`avatar` = '%s',`gender` = %d,`birth` = '%s',`region_id` = %d,`create_time` = %d,`create_from` = %d,`update_time` = %d,`update_from` = %d,`tag_seed` = %d,`tag_param` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->invitor_id,self::_encode_string($obj->union_id,64)
			,self::_encode_string($obj->app_open_id,64)
			,self::_encode_string($obj->web_open_id,64)
			,self::_encode_string($obj->auth_code,128)
			,$obj->auth_update,$obj->auth_expire,self::_encode_string($obj->account,64)
			,self::_encode_string($obj->mobile,16)
			,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->avatar,2048)
			,$obj->gender,$obj->birth,$obj->region_id,$obj->create_time,$obj->create_from,$obj->update_time,$obj->update_from,$obj->tag_seed,self::_encode_string($obj->tag_param,64)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns UserWechatEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`user_wechat`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`user_wechat` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..认证记录ID
	 * @returns UserWechatEntity
	 * @returns null
	 */
	public function load(int $id) : ?UserWechatEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_wechat` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..认证记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`user_wechat` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 union_id 加载
	 * @param	string	$union_id	..开放平台ID
	 * @returns UserWechatEntity
	 * @returns null
	 */
	public function loadByUnionId (string $union_id) : ?UserWechatEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_wechat` WHERE `union_id` = '%s'",
			parent::$reader->escape_string($union_id)
		));
		
	}
	
	/**
	 * 根据唯一索引 "union_id" 删除一条
	 * @param	string	$union_id	..开放平台ID
	 * @returns bool
	 */
	public function deleteByUnionId(string $union_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`user_wechat` WHERE `union_id` = '%s'",
			parent::$reader->escape_string($union_id)
		));
		
	}
	
	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户IDiedu_core.user.id
	 * @returns UserWechatEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?UserWechatEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_wechat` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户IDiedu_core.user.id
	 * @returns UserWechatEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_wechat` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 tag_seed 加载一条
	 * @param	int  $tag_seed  ..标签：裂变种子号
	 * @returns UserWechatEntity
	 * @returns null
	 */
	public function loadOneByTagSeed (int $tag_seed) : ?UserWechatEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_wechat` WHERE `tag_seed` = '%d'",
			$tag_seed
		));
		
	}
	/**
	 * 根据普通索引 tag_seed 加载全部
	 * @param	int	$tag_seed	..标签：裂变种子号
	 * @returns UserWechatEntity
	 * @returns null
	 */
	public function loadAllByTagSeed (int $tag_seed) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_wechat` WHERE `tag_seed` = '%d'",
			$tag_seed
		));
		
	}

	/**
	 * 向数据表 yuemi_main.user_wechat 插入一条新纪录
	 * @param	UserWechatEntity    $obj    ..用户的微信授权
	 * @returns bool
	 */
	public function insert(UserWechatEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.user_wechat 回写一条记录<br>
	 * 更新依据： yuemi_main.user_wechat.id
	 * @param	UserWechatEntity	  $obj    ..用户的微信授权
	 * @returns bool
	 */
	 public function update(UserWechatEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 提现记录
 * @table user_withdraw
 * @engine innodb
 */
final class UserWithdrawEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 前台用户ID
	 * @var int
	 * @default	0
	 */
	public $user_id = 0;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 兑现总金额
	 * @var float
	 */
	public $total = null;

	/**
	 * 从余额部分提取金额
	 * @var float
	 * @default	0.0000
	 */
	public $money = 0.0000;

	/**
	 * 佣金余额,自买省的
	 * @var float
	 * @default	0.0000
	 */
	public $profit_self = 0.0000;

	/**
	 * 佣金余额,分享赚的
	 * @var float
	 * @default	0.0000
	 */
	public $profit_share = 0.0000;

	/**
	 * 佣金余额,团队管理佣金
	 * @var float
	 * @default	0.0000
	 */
	public $profit_team = 0.0000;

	/**
	 * 礼包佣金,直接招聘佣金
	 * @var float
	 * @default	0.0000
	 */
	public $recruit_dir = 0.0000;

	/**
	 * 礼包佣金,间接招聘佣金
	 * @var float
	 * @default	0.0000
	 */
	public $recruit_alt = 0.0000;

	/**
	 * 用户银行账号ID
	 * @var int
	 * @default	0
	 */
	public $userbank_id = 0;

	/**
	 * 银行ID
	 * @var int
	 */
	public $bank_id = null;

	/**
	 * 开户地区ID
	 * @var int
	 * @default	0
	 */
	public $region_id = 0;

	/**
	 * 开户人名称
	 * @var string
	 */
	public $bank_name = null;

	/**
	 * 卡号
	 * @var string
	 */
	public $card_no = null;

	/**
	 * 提现请求状态，0提交,1审核,2打款,3完成,4拒绝,5关闭
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 创建IP
	 * @var int
	 * @default	0
	 */
	public $create_from = 0;

	/**
	 * 审核时间
	 * @var int
	 * @default	0
	 */
	public $audit_time = 0;

	/**
	 * 处理时间
	 * @var int
	 * @default	0
	 */
	public $process_time = 0;

	/**
	 * 完成时间
	 * @var int
	 * @default	0
	 */
	public $finish_time = 0;
}
/**
 * UserWithdrawEntity Factory<br>
 * 提现记录
 */
final class UserWithdrawFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var UserWithdrawFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : UserWithdrawFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new UserWithdrawFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new UserWithdrawFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_withdraw`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`user_withdraw` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : UserWithdrawEntity {
		$obj = new UserWithdrawEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->order_id = $row['order_id'];
		$obj->total = $row['total'];
		$obj->money = $row['money'];
		$obj->profit_self = $row['profit_self'];
		$obj->profit_share = $row['profit_share'];
		$obj->profit_team = $row['profit_team'];
		$obj->recruit_dir = $row['recruit_dir'];
		$obj->recruit_alt = $row['recruit_alt'];
		$obj->userbank_id = $row['userbank_id'];
		$obj->bank_id = $row['bank_id'];
		$obj->region_id = $row['region_id'];
		$obj->bank_name = $row['bank_name'];
		$obj->card_no = $row['card_no'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_time = $row['audit_time'];
		$obj->process_time = $row['process_time'];
		$obj->finish_time = $row['finish_time'];
		return $obj;
	}

	private function _object_to_insert(UserWithdrawEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`user_withdraw` %s(`id`,`user_id`,`order_id`,`total`,`money`,`profit_self`,`profit_share`,`profit_team`,`recruit_dir`,`recruit_alt`,`userbank_id`,`bank_id`,`region_id`,`bank_name`,`card_no`,`status`,`create_time`,`create_from`,`audit_time`,`process_time`,`finish_time`) VALUES (NULL,%d,'%s',%f,%f,%f,%f,%f,%f,%f,%d,%d,%d,'%s','%s',%d,UNIX_TIMESTAMP(),%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->order_id,16)
			,$obj->total,$obj->money,$obj->profit_self,$obj->profit_share,$obj->profit_team,$obj->recruit_dir,$obj->recruit_alt,$obj->userbank_id,$obj->bank_id,$obj->region_id,self::_encode_string($obj->bank_name,64)
			,self::_encode_string($obj->card_no,128)
			,$obj->status,$obj->create_from,$obj->audit_time,$obj->process_time,$obj->finish_time);
	}
	private function _object_to_update(UserWithdrawEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`user_withdraw` %s SET `user_id` = %d,`order_id` = '%s',`total` = %f,`money` = %f,`profit_self` = %f,`profit_share` = %f,`profit_team` = %f,`recruit_dir` = %f,`recruit_alt` = %f,`userbank_id` = %d,`bank_id` = %d,`region_id` = %d,`bank_name` = '%s',`card_no` = '%s',`status` = %d,`create_from` = %d,`audit_time` = %d,`process_time` = %d,`finish_time` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,self::_encode_string($obj->order_id,16)
			,$obj->total,$obj->money,$obj->profit_self,$obj->profit_share,$obj->profit_team,$obj->recruit_dir,$obj->recruit_alt,$obj->userbank_id,$obj->bank_id,$obj->region_id,self::_encode_string($obj->bank_name,64)
			,self::_encode_string($obj->card_no,128)
			,$obj->status,$obj->create_from,$obj->audit_time,$obj->process_time,$obj->finish_time,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns UserWithdrawEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`user_withdraw`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`user_withdraw` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns UserWithdrawEntity
	 * @returns null
	 */
	public function load(int $id) : ?UserWithdrawEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_withdraw` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`user_withdraw` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..前台用户ID
	 * @returns UserWithdrawEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?UserWithdrawEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_withdraw` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..前台用户ID
	 * @returns UserWithdrawEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_withdraw` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 order_id 加载一条
	 * @param	string  $order_id  ..订单ID
	 * @returns UserWithdrawEntity
	 * @returns null
	 */
	public function loadOneByOrderId (string $order_id) : ?UserWithdrawEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_withdraw` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}
	/**
	 * 根据普通索引 order_id 加载全部
	 * @param	string	$order_id	..订单ID
	 * @returns UserWithdrawEntity
	 * @returns null
	 */
	public function loadAllByOrderId (string $order_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_withdraw` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..提现请求状态，0提交,1审核,2打款,3完成,4拒绝,5关闭
	 * @returns UserWithdrawEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?UserWithdrawEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_withdraw` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..提现请求状态，0提交,1审核,2打款,3完成,4拒绝,5关闭
	 * @returns UserWithdrawEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_withdraw` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 根据普通索引 create_time 加载一条
	 * @param	int  $create_time  ..创建时间
	 * @returns UserWithdrawEntity
	 * @returns null
	 */
	public function loadOneByCreateTime (int $create_time) : ?UserWithdrawEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_withdraw` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}
	/**
	 * 根据普通索引 create_time 加载全部
	 * @param	int	$create_time	..创建时间
	 * @returns UserWithdrawEntity
	 * @returns null
	 */
	public function loadAllByCreateTime (int $create_time) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`user_withdraw` WHERE `create_time` = '%d'",
			$create_time
		));
		
	}

	/**
	 * 向数据表 yuemi_main.user_withdraw 插入一条新纪录
	 * @param	UserWithdrawEntity    $obj    ..提现记录
	 * @returns bool
	 */
	public function insert(UserWithdrawEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.user_withdraw 回写一条记录<br>
	 * 更新依据： yuemi_main.user_withdraw.id
	 * @param	UserWithdrawEntity	  $obj    ..提现记录
	 * @returns bool
	 */
	 public function update(UserWithdrawEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 三层之一VIP
 * @table vip
 * @engine innodb
 */
final class VipEntity extends \Ziima\Data\Entity {
	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 归属总监ID
	 * @var int
	 * @default	0
	 */
	public $cheif_id = 0;

	/**
	 * 归属总经理ID
	 * @var int
	 * @default	0
	 */
	public $director_id = 0;

	/**
	 * 一级邀请码
	 * @var string
	 */
	public $invite_code = null;

	/**
	 * VIP状态：0 非VIP，1是VIP
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 是否领取大礼包
	 * @var int
	 * @default	0
	 */
	public $has_gifts = 0;

	/**
	 * 创建时间，首次成为VIP时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 变更时间，最后一次续费时间
	 * @var int
	 * @default	0
	 */
	public $update_time = 0;

	/**
	 * 过期时间，最后一次过期时间
	 * @var int
	 * @default	0
	 */
	public $expire_time = 0;
}
/**
 * VipEntity Factory<br>
 * 三层之一VIP
 */
final class VipFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var VipFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : VipFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new VipFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new VipFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`vip`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`vip` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : VipEntity {
		$obj = new VipEntity();$obj->user_id = $row['user_id'];
		$obj->cheif_id = $row['cheif_id'];
		$obj->director_id = $row['director_id'];
		$obj->invite_code = $row['invite_code'];
		$obj->status = $row['status'];
		$obj->has_gifts = $row['has_gifts'];
		$obj->create_time = $row['create_time'];
		$obj->update_time = $row['update_time'];
		$obj->expire_time = $row['expire_time'];
		return $obj;
	}

	private function _object_to_insert(VipEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`vip` %s(`user_id`,`cheif_id`,`director_id`,`invite_code`,`status`,`has_gifts`,`create_time`,`update_time`,`expire_time`) VALUES (%d,%d,%d,'%s',%d,%d,UNIX_TIMESTAMP(),%d,%d)";
		return sprintf($sql,'',$obj->user_id,$obj->cheif_id,$obj->director_id,self::_encode_string($obj->invite_code,8)
			,$obj->status,$obj->has_gifts,$obj->update_time,$obj->expire_time);
	}
	private function _object_to_update(VipEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`vip` %s SET `cheif_id` = %d,`director_id` = %d,`invite_code` = '%s',`status` = %d,`has_gifts` = %d,`update_time` = %d,`expire_time` = %d WHERE `user_id` = %d";
		
		return sprintf($sql,'',$obj->cheif_id,$obj->director_id,self::_encode_string($obj->invite_code,8)
			,$obj->status,$obj->has_gifts,$obj->update_time,$obj->expire_time,$obj->user_id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns VipEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`vip`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`vip` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "user_id" 加载一条
	 * @param	int	$user_id	..用户ID
	 * @returns VipEntity
	 * @returns null
	 */
	public function load(int $user_id) : ?VipEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据主键 "user_id" 删除一条
	 * @param	int	$user_id	..用户ID
	 * @returns bool
	 */
	public function delete(int $user_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`vip` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	
	/**
	 * 根据普通索引 cheif_id 加载一条
	 * @param	int  $cheif_id  ..归属总监ID
	 * @returns VipEntity
	 * @returns null
	 */
	public function loadOneByCheifId (int $cheif_id) : ?VipEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip` WHERE `cheif_id` = '%d'",
			$cheif_id
		));
		
	}
	/**
	 * 根据普通索引 cheif_id 加载全部
	 * @param	int	$cheif_id	..归属总监ID
	 * @returns VipEntity
	 * @returns null
	 */
	public function loadAllByCheifId (int $cheif_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip` WHERE `cheif_id` = '%d'",
			$cheif_id
		));
		
	}

	/**
	 * 根据普通索引 invite_code 加载一条
	 * @param	string  $invite_code  ..一级邀请码
	 * @returns VipEntity
	 * @returns null
	 */
	public function loadOneByInviteCode (string $invite_code) : ?VipEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip` WHERE `invite_code` = '%s'",
			parent::$reader->escape_string($invite_code)
		));
		
	}
	/**
	 * 根据普通索引 invite_code 加载全部
	 * @param	string	$invite_code	..一级邀请码
	 * @returns VipEntity
	 * @returns null
	 */
	public function loadAllByInviteCode (string $invite_code) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip` WHERE `invite_code` = '%s'",
			parent::$reader->escape_string($invite_code)
		));
		
	}

	/**
	 * 向数据表 yuemi_main.vip 插入一条新纪录
	 * @param	VipEntity    $obj    ..三层之一VIP
	 * @returns bool
	 */
	public function insert(VipEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.vip 回写一条记录<br>
	 * 更新依据： yuemi_main.vip.user_id
	 * @param	VipEntity	  $obj    ..三层之一VIP
	 * @returns bool
	 */
	 public function update(VipEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * VIP缴费状态
 * @table vip_buff
 * @engine innodb
 */
final class VipBuffEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 状态来源，0=NONE,1=TEST,2=FREE,3=CARD,4=COIN,5=MONEY
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 钻石流水记录ID
	 * @var int
	 * @default	0
	 */
	public $tally_id = 0;

	/**
	 * 支付钻石
	 * @var float
	 * @default	0.00000000
	 */
	public $coin = 0.00000000;

	/**
	 * 开始时间
	 * @var int
	 * @default	0
	 */
	public $start_time = 0;

	/**
	 * 过期时间
	 * @var int
	 * @default	0
	 */
	public $expire_time = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;
}
/**
 * VipBuffEntity Factory<br>
 * VIP缴费状态
 */
final class VipBuffFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var VipBuffFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : VipBuffFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new VipBuffFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new VipBuffFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`vip_buff`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`vip_buff` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : VipBuffEntity {
		$obj = new VipBuffEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->type = $row['type'];
		$obj->order_id = $row['order_id'];
		$obj->tally_id = $row['tally_id'];
		$obj->coin = $row['coin'];
		$obj->start_time = $row['start_time'];
		$obj->expire_time = $row['expire_time'];
		$obj->create_time = $row['create_time'];
		return $obj;
	}

	private function _object_to_insert(VipBuffEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`vip_buff` %s(`id`,`user_id`,`type`,`order_id`,`tally_id`,`coin`,`start_time`,`expire_time`,`create_time`) VALUES (NULL,%d,%d,'%s',%d,%f,%d,%d,UNIX_TIMESTAMP())";
		return sprintf($sql,'',$obj->user_id,$obj->type,self::_encode_string($obj->order_id,16)
			,$obj->tally_id,$obj->coin,$obj->start_time,$obj->expire_time);
	}
	private function _object_to_update(VipBuffEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`vip_buff` %s SET `user_id` = %d,`type` = %d,`order_id` = '%s',`tally_id` = %d,`coin` = %f,`start_time` = %d,`expire_time` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->type,self::_encode_string($obj->order_id,16)
			,$obj->tally_id,$obj->coin,$obj->start_time,$obj->expire_time,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns VipBuffEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`vip_buff`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`vip_buff` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns VipBuffEntity
	 * @returns null
	 */
	public function load(int $id) : ?VipBuffEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_buff` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`vip_buff` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns VipBuffEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?VipBuffEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_buff` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns VipBuffEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_buff` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 start_time 加载一条
	 * @param	int  $start_time  ..开始时间
	 * @returns VipBuffEntity
	 * @returns null
	 */
	public function loadOneByStartTime (int $start_time) : ?VipBuffEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_buff` WHERE `start_time` = '%d'",
			$start_time
		));
		
	}
	/**
	 * 根据普通索引 start_time 加载全部
	 * @param	int	$start_time	..开始时间
	 * @returns VipBuffEntity
	 * @returns null
	 */
	public function loadAllByStartTime (int $start_time) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_buff` WHERE `start_time` = '%d'",
			$start_time
		));
		
	}

	/**
	 * 根据普通索引 expire_time 加载一条
	 * @param	int  $expire_time  ..过期时间
	 * @returns VipBuffEntity
	 * @returns null
	 */
	public function loadOneByExpireTime (int $expire_time) : ?VipBuffEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_buff` WHERE `expire_time` = '%d'",
			$expire_time
		));
		
	}
	/**
	 * 根据普通索引 expire_time 加载全部
	 * @param	int	$expire_time	..过期时间
	 * @returns VipBuffEntity
	 * @returns null
	 */
	public function loadAllByExpireTime (int $expire_time) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_buff` WHERE `expire_time` = '%d'",
			$expire_time
		));
		
	}

	/**
	 * 向数据表 yuemi_main.vip_buff 插入一条新纪录
	 * @param	VipBuffEntity    $obj    ..VIP缴费状态
	 * @returns bool
	 */
	public function insert(VipBuffEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.vip_buff 回写一条记录<br>
	 * 更新依据： yuemi_main.vip_buff.id
	 * @param	VipBuffEntity	  $obj    ..VIP缴费状态
	 * @returns bool
	 */
	 public function update(VipBuffEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * VIP激活卡
 * @table vip_card
 * @engine innodb
 */
final class VipCardEntity extends \Ziima\Data\Entity {
	/**
	 * 卡片ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 拥有者用户ID
	 * @var int
	 */
	public $owner_id = null;

	/**
	 * 卡号
	 * @var string
	 */
	public $serial = null;

	/**
	 * 价值金额（冗余）
	 * @var float
	 * @default	0.0000
	 */
	public $money = 0.0000;

	/**
	 * 接受者用户ID
	 * @var int
	 * @default	0
	 */
	public $rcv_user_id = 0;

	/**
	 * 接受者手机号码
	 * @var string
	 */
	public $rcv_mobile = null;

	/**
	 * VIP卡片状态：0 新卡,1使用
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 使用时间
	 * @var int
	 * @default	0
	 */
	public $used_time = 0;
}
/**
 * VipCardEntity Factory<br>
 * VIP激活卡
 */
final class VipCardFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var VipCardFactory
	 */
	private static $_instance;
	
	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_reader();
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : VipCardFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new VipCardFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new VipCardFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`vip_card`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_main`.`vip_card` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : VipCardEntity {
		$obj = new VipCardEntity();$obj->id = $row['id'];
		$obj->owner_id = $row['owner_id'];
		$obj->serial = $row['serial'];
		$obj->money = $row['money'];
		$obj->rcv_user_id = $row['rcv_user_id'];
		$obj->rcv_mobile = $row['rcv_mobile'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->used_time = $row['used_time'];
		return $obj;
	}

	private function _object_to_insert(VipCardEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_main`.`vip_card` %s(`id`,`owner_id`,`serial`,`money`,`rcv_user_id`,`rcv_mobile`,`status`,`create_time`,`used_time`) VALUES (NULL,%d,'%s',%f,%d,'%s',%d,UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->owner_id,self::_encode_string($obj->serial,16)
			,$obj->money,$obj->rcv_user_id,self::_encode_string($obj->rcv_mobile,12)
			,$obj->status,$obj->used_time);
	}
	private function _object_to_update(VipCardEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_main`.`vip_card` %s SET `owner_id` = %d,`serial` = '%s',`money` = %f,`rcv_user_id` = %d,`rcv_mobile` = '%s',`status` = %d,`used_time` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->owner_id,self::_encode_string($obj->serial,16)
			,$obj->money,$obj->rcv_user_id,self::_encode_string($obj->rcv_mobile,12)
			,$obj->status,$obj->used_time,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns VipCardEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_main`.`vip_card`";
		}else{
			$sql = "SELECT * FROM `yuemi_main`.`vip_card` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..卡片ID
	 * @returns VipCardEntity
	 * @returns null
	 */
	public function load(int $id) : ?VipCardEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_card` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..卡片ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`vip_card` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 serial 加载
	 * @param	string	$serial	..卡号
	 * @returns VipCardEntity
	 * @returns null
	 */
	public function loadBySerial (string $serial) : ?VipCardEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_card` WHERE `serial` = '%s'",
			parent::$reader->escape_string($serial)
		));
		
	}
	
	/**
	 * 根据唯一索引 "serial" 删除一条
	 * @param	string	$serial	..卡号
	 * @returns bool
	 */
	public function deleteBySerial(string $serial) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_main`.`vip_card` WHERE `serial` = '%s'",
			parent::$reader->escape_string($serial)
		));
		
	}
	
	/**
	 * 根据普通索引 owner_id 加载一条
	 * @param	int  $owner_id  ..拥有者用户ID
	 * @returns VipCardEntity
	 * @returns null
	 */
	public function loadOneByOwnerId (int $owner_id) : ?VipCardEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_card` WHERE `owner_id` = '%d'",
			$owner_id
		));
		
	}
	/**
	 * 根据普通索引 owner_id 加载全部
	 * @param	int	$owner_id	..拥有者用户ID
	 * @returns VipCardEntity
	 * @returns null
	 */
	public function loadAllByOwnerId (int $owner_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_card` WHERE `owner_id` = '%d'",
			$owner_id
		));
		
	}

	/**
	 * 根据普通索引 rcv_user_id 加载一条
	 * @param	int  $rcv_user_id  ..接受者用户ID
	 * @returns VipCardEntity
	 * @returns null
	 */
	public function loadOneByRcvUserId (int $rcv_user_id) : ?VipCardEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_card` WHERE `rcv_user_id` = '%d'",
			$rcv_user_id
		));
		
	}
	/**
	 * 根据普通索引 rcv_user_id 加载全部
	 * @param	int	$rcv_user_id	..接受者用户ID
	 * @returns VipCardEntity
	 * @returns null
	 */
	public function loadAllByRcvUserId (int $rcv_user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_main`.`vip_card` WHERE `rcv_user_id` = '%d'",
			$rcv_user_id
		));
		
	}

	/**
	 * 向数据表 yuemi_main.vip_card 插入一条新纪录
	 * @param	VipCardEntity    $obj    ..VIP激活卡
	 * @returns bool
	 */
	public function insert(VipCardEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		if($obj->id === NULL || $obj->id <= 0){
			$obj->id = parent::$writer->insert_id;
		}
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_main.vip_card 回写一条记录<br>
	 * 更新依据： yuemi_main.vip_card.id
	 * @param	VipCardEntity	  $obj    ..VIP激活卡
	 * @returns bool
	 */
	 public function update(VipCardEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * yuemi_main 存储过程调用器
 */
final class ProcedureInvoker extends \Ziima\Data\MySQLInvoker {
	/**
	 * @var ProcedureInvoker
	 */
	private static $_instance;
	/**
	 * @var \Ziima\Tracer
	 */
	private $Tracer;

	function __construct(string $cnn_w,string $cnn_r = null) {
		parent::__construct($cnn_w, $cnn_r);
		$this->__open_writer();
		$this->Tracer = new \Ziima\Tracer('proc.yuemi_main');
	}
	
	/**
	 * 单例。<br />
	 * 需要 MYSQL_WRITER , MYSQL_READER 两个常量支持
	 * @returns int
	 */
	public static function Instance() : ProcedureInvoker {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用ProcedureInvoker的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ProcedureInvoker(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ProcedureInvoker(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}

	
	/**
	 * 手机绑定 <br>
	 * 调用存储过程 bind_mobile ()
	 * @var	string	$WxUnionId		第1个参数
	 * @var	string	$UserMobile		第2个参数
	 * @var	string	$VerifyCode		第3个参数
	 * @var	int	$ClientIp		第4个参数
	 * @returns InvokerBindMobileOutput
	 */
	public function bind_mobile(string $WxUnionId,string $UserMobile,string $VerifyCode,int $ClientIp,bool $useReader = false) : InvokerBindMobileOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`bind_mobile` ('%s','%s','%s',%d,@WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($WxUnionId)
				,parent::$writer->escape_string($UserMobile)
				,parent::$writer->escape_string($VerifyCode)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 bind_mobile 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 bind_mobile 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 bind_mobile 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerBindMobileOutput();
		$obj->WechatId = $dat['@WechatId'];
		$obj->UserId = $dat['@UserId'];
		$obj->UserToken = $dat['@UserToken'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 检查身份 <br>
	 * 调用存储过程 check_user_role ()
	 * @var	int	$UserId		第1个参数
	 * @returns InvokerCheckUserRoleOutput
	 */
	public function check_user_role(int $UserId,bool $useReader = false) : InvokerCheckUserRoleOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`check_user_role` (%d,@LevelUser,@LevelVip,@LevelCheif,@LevelDirector,@LevelSupplier,@LevelTeam,@LevelAdmin,@ReturnValue,@ReturnMessage)"
			,$UserId
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 check_user_role 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @LevelUser,@LevelVip,@LevelCheif,@LevelDirector,@LevelSupplier,@LevelTeam,@LevelAdmin,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @LevelUser,@LevelVip,@LevelCheif,@LevelDirector,@LevelSupplier,@LevelTeam,@LevelAdmin,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 check_user_role 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 check_user_role 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCheckUserRoleOutput();
		$obj->LevelUser = $dat['@LevelUser'];
		$obj->LevelVip = $dat['@LevelVip'];
		$obj->LevelCheif = $dat['@LevelCheif'];
		$obj->LevelDirector = $dat['@LevelDirector'];
		$obj->LevelSupplier = $dat['@LevelSupplier'];
		$obj->LevelTeam = $dat['@LevelTeam'];
		$obj->LevelAdmin = $dat['@LevelAdmin'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 阅币支出 <br>
	 * 调用存储过程 coin_expend ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Coin_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerCoinExpendOutput
	 */
	public function coin_expend(int $UserId,float $Coin_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerCoinExpendOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`coin_expend` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Coin_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 coin_expend 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 coin_expend 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 coin_expend 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCoinExpendOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 阅币收入 <br>
	 * 调用存储过程 coin_income ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Coin_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerCoinIncomeOutput
	 */
	public function coin_income(int $UserId,float $Coin_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerCoinIncomeOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`coin_income` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Coin_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 coin_income 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 coin_income 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 coin_income 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCoinIncomeOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 设备注册 <br>
	 * 调用存储过程 device_register ()
	 * @var	string	$hw_udid		第1个参数
	 * @var	string	$hw_imei		第2个参数
	 * @var	string	$hw_imsi		第3个参数
	 * @var	string	$hw_vender		第4个参数
	 * @var	string	$hw_model		第5个参数
	 * @var	int	$hw_width		第6个参数
	 * @var	int	$hw_height		第7个参数
	 * @var	int	$sys_style		第8个参数
	 * @var	string	$sys_version		第9个参数
	 * @var	int	$app_version		第10个参数
	 * @var	int	$oa_version		第11个参数
	 * @var	float	$gps_lng		第12个参数
	 * @var	float	$gps_lat		第13个参数
	 * @var	int	$gps_region		第14个参数
	 * @var	string	$user_token		第15个参数
	 * @var	int	$user_from		第16个参数
	 * @returns InvokerDeviceRegisterOutput
	 */
	public function device_register(string $hw_udid,string $hw_imei,string $hw_imsi,string $hw_vender,string $hw_model,int $hw_width,int $hw_height,int $sys_style,string $sys_version,int $app_version,int $oa_version,float $gps_lng,float $gps_lat,int $gps_region,string $user_token,int $user_from,bool $useReader = false) : InvokerDeviceRegisterOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`device_register` ('%s','%s','%s','%s','%s',%d,%d,%d,'%s',%d,%d,%f,%f,%d,'%s',%d,@DeviceId,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($hw_udid)
				,parent::$writer->escape_string($hw_imei)
				,parent::$writer->escape_string($hw_imsi)
				,parent::$writer->escape_string($hw_vender)
				,parent::$writer->escape_string($hw_model)
				,$hw_width,$hw_height,$sys_style,parent::$writer->escape_string($sys_version)
				,$app_version,$oa_version,$gps_lng,$gps_lat,$gps_region,parent::$writer->escape_string($user_token)
				,$user_from
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 device_register 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @DeviceId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @DeviceId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 device_register 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 device_register 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerDeviceRegisterOutput();
		$obj->DeviceId = $dat['@DeviceId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 手机登陆 <br>
	 * 调用存储过程 login_mobile ()
	 * @var	string	$UserMobile		第1个参数
	 * @var	string	$VerifyCode		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerLoginMobileOutput
	 */
	public function login_mobile(string $UserMobile,string $VerifyCode,int $ClientIp,bool $useReader = false) : InvokerLoginMobileOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`login_mobile` ('%s','%s',%d,@WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($UserMobile)
				,parent::$writer->escape_string($VerifyCode)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 login_mobile 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 login_mobile 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 login_mobile 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerLoginMobileOutput();
		$obj->WechatId = $dat['@WechatId'];
		$obj->UserId = $dat['@UserId'];
		$obj->UserToken = $dat['@UserToken'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 微信登陆 <br>
	 * 调用存储过程 login_wechat ()
	 * @var	string	$WxOpenId		第1个参数
	 * @var	string	$WxUnionId		第2个参数
	 * @var	string	$WxName		第3个参数
	 * @var	string	$WxAvatar		第4个参数
	 * @var	int	$WxGender		第5个参数
	 * @var	int	$InvitorId		第6个参数
	 * @var	int	$InvitorSeed		第7个参数
	 * @var	string	$InvitorParam		第8个参数
	 * @var	int	$ClientIp		第9个参数
	 * @returns InvokerLoginWechatOutput
	 */
	public function login_wechat(string $WxOpenId,string $WxUnionId,string $WxName,string $WxAvatar,int $WxGender,int $InvitorId,int $InvitorSeed,string $InvitorParam,int $ClientIp,bool $useReader = false) : InvokerLoginWechatOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`login_wechat` ('%s','%s','%s','%s',%d,%d,%d,'%s',%d,@WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($WxOpenId)
				,parent::$writer->escape_string($WxUnionId)
				,parent::$writer->escape_string($WxName)
				,parent::$writer->escape_string($WxAvatar)
				,$WxGender,$InvitorId,$InvitorSeed,parent::$writer->escape_string($InvitorParam)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 login_wechat 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 login_wechat 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 login_wechat 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerLoginWechatOutput();
		$obj->WechatId = $dat['@WechatId'];
		$obj->UserId = $dat['@UserId'];
		$obj->UserToken = $dat['@UserToken'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 公众号微信登陆 <br>
	 * 调用存储过程 login_wechat_ex ()
	 * @var	string	$WxOpenId		第1个参数
	 * @var	string	$WxUnionId		第2个参数
	 * @var	string	$WxName		第3个参数
	 * @var	string	$WxAvatar		第4个参数
	 * @var	int	$WxGender		第5个参数
	 * @var	int	$InvitorId		第6个参数
	 * @var	int	$InvitorSeed		第7个参数
	 * @var	string	$InvitorParam		第8个参数
	 * @var	int	$ClientIp		第9个参数
	 * @returns InvokerLoginWechatExOutput
	 */
	public function login_wechat_ex(string $WxOpenId,string $WxUnionId,string $WxName,string $WxAvatar,int $WxGender,int $InvitorId,int $InvitorSeed,string $InvitorParam,int $ClientIp,bool $useReader = false) : InvokerLoginWechatExOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`login_wechat_ex` ('%s','%s','%s','%s',%d,%d,%d,'%s',%d,@WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($WxOpenId)
				,parent::$writer->escape_string($WxUnionId)
				,parent::$writer->escape_string($WxName)
				,parent::$writer->escape_string($WxAvatar)
				,$WxGender,$InvitorId,$InvitorSeed,parent::$writer->escape_string($InvitorParam)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 login_wechat_ex 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 login_wechat_ex 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 login_wechat_ex 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerLoginWechatExOutput();
		$obj->WechatId = $dat['@WechatId'];
		$obj->UserId = $dat['@UserId'];
		$obj->UserToken = $dat['@UserToken'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 卡充总监 <br>
	 * 调用存储过程 make_card_cheif ()
	 * @var	int	$UserId		第1个参数
	 * @var	string	$CardSerial		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerMakeCardCheifOutput
	 */
	public function make_card_cheif(int $UserId,string $CardSerial,int $ClientIp,bool $useReader = false) : InvokerMakeCardCheifOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_card_cheif` (%d,'%s',%d,@ReturnValue,@ReturnMessage)"
			,$UserId,parent::$writer->escape_string($CardSerial)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_card_cheif 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_card_cheif 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_card_cheif 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakeCardCheifOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 卡充VIP <br>
	 * 调用存储过程 make_card_vip ()
	 * @var	int	$UserId		第1个参数
	 * @var	string	$CardSerial		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerMakeCardVipOutput
	 */
	public function make_card_vip(int $UserId,string $CardSerial,int $ClientIp,bool $useReader = false) : InvokerMakeCardVipOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_card_vip` (%d,'%s',%d,@ReturnValue,@ReturnMessage)"
			,$UserId,parent::$writer->escape_string($CardSerial)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_card_vip 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_card_vip 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_card_vip 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakeCardVipOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 兑换VIP <br>
	 * 调用存储过程 make_coin_vip ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$ClientIp		第2个参数
	 * @returns InvokerMakeCoinVipOutput
	 */
	public function make_coin_vip(int $UserId,int $ClientIp,bool $useReader = false) : InvokerMakeCoinVipOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_coin_vip` (%d,%d,@ReturnValue,@ReturnMessage)"
			,$UserId,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_coin_vip 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_coin_vip 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_coin_vip 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakeCoinVipOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 花钱总监 <br>
	 * 调用存储过程 make_money_cheif ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$ClientIp		第2个参数
	 * @returns InvokerMakeMoneyCheifOutput
	 */
	public function make_money_cheif(int $UserId,int $ClientIp,bool $useReader = false) : InvokerMakeMoneyCheifOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_money_cheif` (%d,%d,@CheifBuffId,@OrderId,@ReturnValue,@ReturnMessage)"
			,$UserId,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_money_cheif 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @CheifBuffId,@OrderId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @CheifBuffId,@OrderId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_money_cheif 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_money_cheif 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakeMoneyCheifOutput();
		$obj->CheifBuffId = $dat['@CheifBuffId'];
		$obj->OrderId = $dat['@OrderId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 卡位总监 <br>
	 * 调用存储过程 make_money_cheif_accept ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$ClientIp		第2个参数
	 * @var	string	$OrderId		第3个参数
	 * @var	int	$CheifBuffId		第4个参数
	 * @returns InvokerMakeMoneyCheifAcceptOutput
	 */
	public function make_money_cheif_accept(int $UserId,int $ClientIp,string $OrderId,int $CheifBuffId,bool $useReader = false) : InvokerMakeMoneyCheifAcceptOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_money_cheif_accept` (%d,%d,'%s',%d,@ReturnValue,@ReturnMessage)"
			,$UserId,$ClientIp,parent::$writer->escape_string($OrderId)
				,$CheifBuffId
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_money_cheif_accept 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_money_cheif_accept 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_money_cheif_accept 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakeMoneyCheifAcceptOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 直升总监 <br>
	 * 调用存储过程 make_money_cheif_ex ()
	 * @var	string	$UserMobile		第1个参数
	 * @var	string	$OrderId		第2个参数
	 * @var	string	$CertName		第3个参数
	 * @var	string	$CertNumber		第4个参数
	 * @var	int	$AddrRegion		第5个参数
	 * @var	string	$AddrDetail		第6个参数
	 * @var	int	$BankTypeId		第7个参数
	 * @var	string	$BankNumber		第8个参数
	 * @var	int	$IsGiveVipCard		第9个参数
	 * @var	int	$ClientIp		第10个参数
	 * @returns InvokerMakeMoneyCheifExOutput
	 */
	public function make_money_cheif_ex(string $UserMobile,string $OrderId,string $CertName,string $CertNumber,int $AddrRegion,string $AddrDetail,int $BankTypeId,string $BankNumber,int $IsGiveVipCard,int $ClientIp,bool $useReader = false) : InvokerMakeMoneyCheifExOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_money_cheif_ex` ('%s','%s','%s','%s',%d,'%s',%d,'%s',%d,%d,@UserId,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($UserMobile)
				,parent::$writer->escape_string($OrderId)
				,parent::$writer->escape_string($CertName)
				,parent::$writer->escape_string($CertNumber)
				,$AddrRegion,parent::$writer->escape_string($AddrDetail)
				,$BankTypeId,parent::$writer->escape_string($BankNumber)
				,$IsGiveVipCard,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_money_cheif_ex 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @UserId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @UserId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_money_cheif_ex 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_money_cheif_ex 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakeMoneyCheifExOutput();
		$obj->UserId = $dat['@UserId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 购买总经理 <br>
	 * 调用存储过程 make_money_director ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$ClientIp		第2个参数
	 * @returns InvokerMakeMoneyDirectorOutput
	 */
	public function make_money_director(int $UserId,int $ClientIp,bool $useReader = false) : InvokerMakeMoneyDirectorOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_money_director` (%d,%d,@DirectorBuffId,@OrderId,@ReturnValue,@ReturnMessage)"
			,$UserId,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_money_director 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @DirectorBuffId,@OrderId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @DirectorBuffId,@OrderId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_money_director 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_money_director 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakeMoneyDirectorOutput();
		$obj->DirectorBuffId = $dat['@DirectorBuffId'];
		$obj->OrderId = $dat['@OrderId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 直升经理 <br>
	 * 调用存储过程 make_money_director_ex ()
	 * @var	string	$UserMobile		第1个参数
	 * @var	string	$OrderId		第2个参数
	 * @var	string	$CertName		第3个参数
	 * @var	string	$CertNumber		第4个参数
	 * @var	int	$AddrRegion		第5个参数
	 * @var	string	$AddrDetail		第6个参数
	 * @var	int	$BankTypeId		第7个参数
	 * @var	string	$BankNumber		第8个参数
	 * @var	int	$IsGiveCheifCard		第9个参数
	 * @var	int	$ClientIp		第10个参数
	 * @returns InvokerMakeMoneyDirectorExOutput
	 */
	public function make_money_director_ex(string $UserMobile,string $OrderId,string $CertName,string $CertNumber,int $AddrRegion,string $AddrDetail,int $BankTypeId,string $BankNumber,int $IsGiveCheifCard,int $ClientIp,bool $useReader = false) : InvokerMakeMoneyDirectorExOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_money_director_ex` ('%s','%s','%s','%s',%d,'%s',%d,'%s',%d,%d,@UserId,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($UserMobile)
				,parent::$writer->escape_string($OrderId)
				,parent::$writer->escape_string($CertName)
				,parent::$writer->escape_string($CertNumber)
				,$AddrRegion,parent::$writer->escape_string($AddrDetail)
				,$BankTypeId,parent::$writer->escape_string($BankNumber)
				,$IsGiveCheifCard,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_money_director_ex 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @UserId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @UserId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_money_director_ex 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_money_director_ex 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakeMoneyDirectorExOutput();
		$obj->UserId = $dat['@UserId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 购买VIP <br>
	 * 调用存储过程 make_money_vip ()
	 * @var	int	$UserId		第1个参数
	 * @var	string	$OrderId		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerMakeMoneyVipOutput
	 */
	public function make_money_vip(int $UserId,string $OrderId,int $ClientIp,bool $useReader = false) : InvokerMakeMoneyVipOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_money_vip` (%d,'%s',%d,@ReturnValue,@ReturnMessage)"
			,$UserId,parent::$writer->escape_string($OrderId)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_money_vip 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_money_vip 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_money_vip 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakeMoneyVipOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 晋升总监 <br>
	 * 调用存储过程 make_premote_cheif ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$ClientIp		第2个参数
	 * @returns InvokerMakePremoteCheifOutput
	 */
	public function make_premote_cheif(int $UserId,int $ClientIp,bool $useReader = false) : InvokerMakePremoteCheifOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_premote_cheif` (%d,%d,@ReturnValue,@ReturnMessage)"
			,$UserId,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_premote_cheif 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_premote_cheif 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_premote_cheif 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakePremoteCheifOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 晋升总监 <br>
	 * 调用存储过程 make_premote_director ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$ClientIp		第2个参数
	 * @returns InvokerMakePremoteDirectorOutput
	 */
	public function make_premote_director(int $UserId,int $ClientIp,bool $useReader = false) : InvokerMakePremoteDirectorOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_premote_director` (%d,%d,@ReturnValue,@ReturnMessage)"
			,$UserId,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_premote_director 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_premote_director 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_premote_director 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakePremoteDirectorOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 测试VIP <br>
	 * 调用存储过程 make_test_vip ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$TestDays		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerMakeTestVipOutput
	 */
	public function make_test_vip(int $UserId,int $TestDays,int $ClientIp,bool $useReader = false) : InvokerMakeTestVipOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`make_test_vip` (%d,%d,%d,@ReturnValue,@ReturnMessage)"
			,$UserId,$TestDays,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 make_test_vip 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 make_test_vip 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 make_test_vip 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMakeTestVipOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 余额支出 <br>
	 * 调用存储过程 money_expend ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Money_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerMoneyExpendOutput
	 */
	public function money_expend(int $UserId,float $Money_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerMoneyExpendOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`money_expend` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Money_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 money_expend 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 money_expend 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 money_expend 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMoneyExpendOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 余额收入 <br>
	 * 调用存储过程 money_income ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Money_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerMoneyIncomeOutput
	 */
	public function money_income(int $UserId,float $Money_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerMoneyIncomeOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`money_income` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Money_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 money_income 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 money_income 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 money_income 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerMoneyIncomeOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 手机登陆OA <br>
	 * 调用存储过程 oa_login_mobile ()
	 * @var	string	$UserMobile		第1个参数
	 * @var	string	$VerifyCode		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerOaLoginMobileOutput
	 */
	public function oa_login_mobile(string $UserMobile,string $VerifyCode,int $ClientIp,bool $useReader = false) : InvokerOaLoginMobileOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`oa_login_mobile` ('%s','%s',%d,@WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($UserMobile)
				,parent::$writer->escape_string($VerifyCode)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 oa_login_mobile 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 oa_login_mobile 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 oa_login_mobile 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerOaLoginMobileOutput();
		$obj->WechatId = $dat['@WechatId'];
		$obj->UserId = $dat['@UserId'];
		$obj->UserToken = $dat['@UserToken'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 微信登陆 <br>
	 * 调用存储过程 oa_login_wechat ()
	 * @var	string	$WxOpenId		第1个参数
	 * @var	string	$WxUnionId		第2个参数
	 * @var	string	$WxName		第3个参数
	 * @var	string	$WxAvatar		第4个参数
	 * @var	int	$WxGender		第5个参数
	 * @var	int	$InvitorId		第6个参数
	 * @var	int	$InvitorSeed		第7个参数
	 * @var	string	$InvitorParam		第8个参数
	 * @var	int	$ClientIp		第9个参数
	 * @returns InvokerOaLoginWechatOutput
	 */
	public function oa_login_wechat(string $WxOpenId,string $WxUnionId,string $WxName,string $WxAvatar,int $WxGender,int $InvitorId,int $InvitorSeed,string $InvitorParam,int $ClientIp,bool $useReader = false) : InvokerOaLoginWechatOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`oa_login_wechat` ('%s','%s','%s','%s',%d,%d,%d,'%s',%d,@WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($WxOpenId)
				,parent::$writer->escape_string($WxUnionId)
				,parent::$writer->escape_string($WxName)
				,parent::$writer->escape_string($WxAvatar)
				,$WxGender,$InvitorId,$InvitorSeed,parent::$writer->escape_string($InvitorParam)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 oa_login_wechat 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @WechatId,@UserId,@UserToken,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 oa_login_wechat 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 oa_login_wechat 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerOaLoginWechatOutput();
		$obj->WechatId = $dat['@WechatId'];
		$obj->UserId = $dat['@UserId'];
		$obj->UserToken = $dat['@UserToken'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 自买佣金支出 <br>
	 * 调用存储过程 profit_self_expend ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Profit_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerProfitSelfExpendOutput
	 */
	public function profit_self_expend(int $UserId,float $Profit_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerProfitSelfExpendOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`profit_self_expend` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Profit_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 profit_self_expend 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 profit_self_expend 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 profit_self_expend 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerProfitSelfExpendOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 自买佣金收入 <br>
	 * 调用存储过程 profit_self_income ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Profit_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerProfitSelfIncomeOutput
	 */
	public function profit_self_income(int $UserId,float $Profit_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerProfitSelfIncomeOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`profit_self_income` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Profit_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 profit_self_income 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 profit_self_income 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 profit_self_income 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerProfitSelfIncomeOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 分享佣金支出 <br>
	 * 调用存储过程 profit_share_expend ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Profit_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerProfitShareExpendOutput
	 */
	public function profit_share_expend(int $UserId,float $Profit_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerProfitShareExpendOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`profit_share_expend` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Profit_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 profit_share_expend 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 profit_share_expend 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 profit_share_expend 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerProfitShareExpendOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 分享佣金收入 <br>
	 * 调用存储过程 profit_share_income ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Profit_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerProfitShareIncomeOutput
	 */
	public function profit_share_income(int $UserId,float $Profit_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerProfitShareIncomeOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`profit_share_income` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Profit_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 profit_share_income 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 profit_share_income 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 profit_share_income 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerProfitShareIncomeOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 团队佣金支出 <br>
	 * 调用存储过程 profit_team_expend ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Profit_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerProfitTeamExpendOutput
	 */
	public function profit_team_expend(int $UserId,float $Profit_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerProfitTeamExpendOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`profit_team_expend` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Profit_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 profit_team_expend 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 profit_team_expend 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 profit_team_expend 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerProfitTeamExpendOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 团队佣金收入 <br>
	 * 调用存储过程 profit_team_income ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Profit_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerProfitTeamIncomeOutput
	 */
	public function profit_team_income(int $UserId,float $Profit_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerProfitTeamIncomeOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`profit_team_income` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Profit_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 profit_team_income 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 profit_team_income 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 profit_team_income 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerProfitTeamIncomeOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 间招佣金支出 <br>
	 * 调用存储过程 recruit_alt_expend ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Recruit_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerRecruitAltExpendOutput
	 */
	public function recruit_alt_expend(int $UserId,float $Recruit_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerRecruitAltExpendOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`recruit_alt_expend` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Recruit_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 recruit_alt_expend 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 recruit_alt_expend 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 recruit_alt_expend 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerRecruitAltExpendOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 间招佣金收入 <br>
	 * 调用存储过程 recruit_alt_income ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Recruit_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerRecruitAltIncomeOutput
	 */
	public function recruit_alt_income(int $UserId,float $Recruit_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerRecruitAltIncomeOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`recruit_alt_income` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Recruit_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 recruit_alt_income 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 recruit_alt_income 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 recruit_alt_income 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerRecruitAltIncomeOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 直招佣金支出 <br>
	 * 调用存储过程 recruit_dir_expend ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Recruit_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerRecruitDirExpendOutput
	 */
	public function recruit_dir_expend(int $UserId,float $Recruit_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerRecruitDirExpendOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`recruit_dir_expend` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Recruit_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 recruit_dir_expend 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 recruit_dir_expend 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 recruit_dir_expend 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerRecruitDirExpendOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 直招佣金收入 <br>
	 * 调用存储过程 recruit_dir_income ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$Recruit_Dlt		第2个参数
	 * @var	string	$SrcType		第3个参数
	 * @var	string	$SrcId		第4个参数
	 * @var	string	$Message		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerRecruitDirIncomeOutput
	 */
	public function recruit_dir_income(int $UserId,float $Recruit_Dlt,string $SrcType,string $SrcId,string $Message,int $ClientIp,bool $useReader = false) : InvokerRecruitDirIncomeOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`recruit_dir_income` (%d,%f,'%s','%s','%s',%d,@TallyId,@ReturnValue,@ReturnMessage)"
			,$UserId,$Recruit_Dlt,parent::$writer->escape_string($SrcType)
				,parent::$writer->escape_string($SrcId)
				,parent::$writer->escape_string($Message)
				,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 recruit_dir_income 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @TallyId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 recruit_dir_income 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 recruit_dir_income 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerRecruitDirIncomeOutput();
		$obj->TallyId = $dat['@TallyId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 执行签到 <br>
	 * 调用存储过程 task_sign_exec ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$ClientIp		第2个参数
	 * @returns InvokerTaskSignExecOutput
	 */
	public function task_sign_exec(int $UserId,int $ClientIp,bool $useReader = false) : InvokerTaskSignExecOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`task_sign_exec` (%d,%d,@ReturnValue,@ReturnMessage)"
			,$UserId,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 task_sign_exec 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 task_sign_exec 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 task_sign_exec 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerTaskSignExecOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 直升经理 <br>
	 * 调用存储过程 test_1 ()
	 * @returns InvokerTest1Output
	 */
	public function test_1(bool $useReader = false) : InvokerTest1Output {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`test_1` (@ReturnValue,@ReturnMessage)"
			
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 test_1 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 test_1 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 test_1 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerTest1Output();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 取消提现 <br>
	 * 调用存储过程 withdraw_cancel ()
	 * @var	int	$WithdrawId		第1个参数
	 * @var	int	$IsDeny		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerWithdrawCancelOutput
	 */
	public function withdraw_cancel(int $WithdrawId,int $IsDeny,int $ClientIp,bool $useReader = false) : InvokerWithdrawCancelOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`withdraw_cancel` (%d,%d,%d,@ReturnValue,@ReturnMessage)"
			,$WithdrawId,$IsDeny,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 withdraw_cancel 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 withdraw_cancel 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 withdraw_cancel 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerWithdrawCancelOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 申请提现 <br>
	 * 调用存储过程 withdraw_request ()
	 * @var	int	$UserId		第1个参数
	 * @var	float	$ReqMoney		第2个参数
	 * @var	float	$ReqProfit		第3个参数
	 * @var	float	$ReqRecruit		第4个参数
	 * @var	int	$ReqBankId		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerWithdrawRequestOutput
	 */
	public function withdraw_request(int $UserId,float $ReqMoney,float $ReqProfit,float $ReqRecruit,int $ReqBankId,int $ClientIp,bool $useReader = false) : InvokerWithdrawRequestOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_main`.`withdraw_request` (%d,%f,%f,%f,%d,%d,@WithdrawId,@ReturnValue,@ReturnMessage)"
			,$UserId,$ReqMoney,$ReqProfit,$ReqRecruit,$ReqBankId,$ClientIp
		);
		$this->Tracer->debug($invoke_sql);
		if($useReader){
			$this->__open_reader();
			$this->Tracer->debug("\t#USE_READER");
			$ret = parent::$reader->query($invoke_sql,MYSQLI_USE_RESULT);
		}else{
			$ret = parent::$writer->query($invoke_sql,MYSQLI_USE_RESULT);
		}
		if($ret === null || $ret === false){
			$this->Tracer->error("\tFAILUR");
			throw new \Exception("运行存储过程 withdraw_request 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @WithdrawId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @WithdrawId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 withdraw_request 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 withdraw_request 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerWithdrawRequestOutput();
		$obj->WithdrawId = $dat['@WithdrawId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

}


final class InvokerBindMobileOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $WechatId;
	
	/**
	 * @var int
	 */
	public $UserId;
	
	/**
	 * @var string
	 */
	public $UserToken;
	
}
	
final class InvokerCheckUserRoleOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $LevelUser;
	
	/**
	 * @var int
	 */
	public $LevelVip;
	
	/**
	 * @var int
	 */
	public $LevelCheif;
	
	/**
	 * @var int
	 */
	public $LevelDirector;
	
	/**
	 * @var int
	 */
	public $LevelSupplier;
	
	/**
	 * @var int
	 */
	public $LevelTeam;
	
	/**
	 * @var int
	 */
	public $LevelAdmin;
	
}
	
final class InvokerCoinExpendOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerCoinIncomeOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerDeviceRegisterOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $DeviceId;
	
}
	
final class InvokerLoginMobileOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $WechatId;
	
	/**
	 * @var int
	 */
	public $UserId;
	
	/**
	 * @var string
	 */
	public $UserToken;
	
}
	
final class InvokerLoginWechatOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $WechatId;
	
	/**
	 * @var int
	 */
	public $UserId;
	
	/**
	 * @var string
	 */
	public $UserToken;
	
}
	
final class InvokerLoginWechatExOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $WechatId;
	
	/**
	 * @var int
	 */
	public $UserId;
	
	/**
	 * @var string
	 */
	public $UserToken;
	
}
	
final class InvokerMakeCardCheifOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerMakeCardVipOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerMakeCoinVipOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerMakeMoneyCheifOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $CheifBuffId;
	
	/**
	 * @var string
	 */
	public $OrderId;
	
}
	
final class InvokerMakeMoneyCheifAcceptOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerMakeMoneyCheifExOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $UserId;
	
}
	
final class InvokerMakeMoneyDirectorOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $DirectorBuffId;
	
	/**
	 * @var string
	 */
	public $OrderId;
	
}
	
final class InvokerMakeMoneyDirectorExOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $UserId;
	
}
	
final class InvokerMakeMoneyVipOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerMakePremoteCheifOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerMakePremoteDirectorOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerMakeTestVipOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerMoneyExpendOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerMoneyIncomeOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerOaLoginMobileOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $WechatId;
	
	/**
	 * @var int
	 */
	public $UserId;
	
	/**
	 * @var string
	 */
	public $UserToken;
	
}
	
final class InvokerOaLoginWechatOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $WechatId;
	
	/**
	 * @var int
	 */
	public $UserId;
	
	/**
	 * @var string
	 */
	public $UserToken;
	
}
	
final class InvokerProfitSelfExpendOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerProfitSelfIncomeOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerProfitShareExpendOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerProfitShareIncomeOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerProfitTeamExpendOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerProfitTeamIncomeOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerRecruitAltExpendOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerRecruitAltIncomeOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerRecruitDirExpendOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerRecruitDirIncomeOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $TallyId;
	
}
	
final class InvokerTaskSignExecOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerTest1Output extends \Ziima\Data\Output {
	
}
	
final class InvokerWithdrawCancelOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerWithdrawRequestOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $WithdrawId;
	
}
	