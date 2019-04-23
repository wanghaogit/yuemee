<?php
/*
 *
 */
namespace yuemi_log;
/**
 * 分享统计数据
 * @table share_counter
 * @engine innodb
 */
final class ShareCounterEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 分享ID
	 * @var int
	 */
	public $share_id = null;

	/**
	 * 时间戳,与Z_BASETIME相差小时数
	 * @var int
	 */
	public $time_id = null;

	/**
	 * 累积：访问
	 * @var int
	 * @default	0
	 */
	public $t_view = 0;

	/**
	 * 累积：喜欢次数
	 * @var int
	 * @default	0
	 */
	public $t_like = 0;

	/**
	 * 累积：购买次数
	 * @var int
	 * @default	0
	 */
	public $t_sale = 0;
}
/**
 * ShareCounterEntity Factory<br>
 * 分享统计数据
 */
final class ShareCounterFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ShareCounterFactory
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
	public static function Instance() : ShareCounterFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ShareCounterFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ShareCounterFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_log`.`share_counter`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_log`.`share_counter` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ShareCounterEntity {
		$obj = new ShareCounterEntity();$obj->id = $row['id'];
		$obj->share_id = $row['share_id'];
		$obj->time_id = $row['time_id'];
		$obj->t_view = $row['t_view'];
		$obj->t_like = $row['t_like'];
		$obj->t_sale = $row['t_sale'];
		return $obj;
	}

	private function _object_to_insert(ShareCounterEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_log`.`share_counter` %s(`id`,`share_id`,`time_id`,`t_view`,`t_like`,`t_sale`) VALUES (NULL,%d,%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->share_id,$obj->time_id,$obj->t_view,$obj->t_like,$obj->t_sale);
	}
	private function _object_to_update(ShareCounterEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_log`.`share_counter` %s SET `share_id` = %d,`time_id` = %d,`t_view` = %d,`t_like` = %d,`t_sale` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->share_id,$obj->time_id,$obj->t_view,$obj->t_like,$obj->t_sale,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ShareCounterEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_log`.`share_counter`";
		}else{
			$sql = "SELECT * FROM `yuemi_log`.`share_counter` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns ShareCounterEntity
	 * @returns null
	 */
	public function load(int $id) : ?ShareCounterEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_log`.`share_counter` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_log`.`share_counter` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 share_id 加载一条
	 * @param	int  $share_id  ..分享ID
	 * @returns ShareCounterEntity
	 * @returns null
	 */
	public function loadOneByShareId (int $share_id) : ?ShareCounterEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_log`.`share_counter` WHERE `share_id` = '%d'",
			$share_id
		));
		
	}
	/**
	 * 根据普通索引 share_id 加载全部
	 * @param	int	$share_id	..分享ID
	 * @returns ShareCounterEntity
	 * @returns null
	 */
	public function loadAllByShareId (int $share_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_log`.`share_counter` WHERE `share_id` = '%d'",
			$share_id
		));
		
	}

	/**
	 * 根据普通索引 time_id 加载一条
	 * @param	int  $time_id  ..时间戳,与Z_BASETIME相差小时数
	 * @returns ShareCounterEntity
	 * @returns null
	 */
	public function loadOneByTimeId (int $time_id) : ?ShareCounterEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_log`.`share_counter` WHERE `time_id` = '%d'",
			$time_id
		));
		
	}
	/**
	 * 根据普通索引 time_id 加载全部
	 * @param	int	$time_id	..时间戳,与Z_BASETIME相差小时数
	 * @returns ShareCounterEntity
	 * @returns null
	 */
	public function loadAllByTimeId (int $time_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_log`.`share_counter` WHERE `time_id` = '%d'",
			$time_id
		));
		
	}

	/**
	 * 向数据表 yuemi_log.share_counter 插入一条新纪录
	 * @param	ShareCounterEntity    $obj    ..分享统计数据
	 * @returns bool
	 */
	public function insert(ShareCounterEntity $obj) : bool {
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
	 * 向数据表 yuemi_log.share_counter 回写一条记录<br>
	 * 更新依据： yuemi_log.share_counter.id
	 * @param	ShareCounterEntity	  $obj    ..分享统计数据
	 * @returns bool
	 */
	 public function update(ShareCounterEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * SKU统计数据
 * @table sku_counter
 * @engine innodb
 */
final class SkuCounterEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * SKU_ID
	 * @var int
	 */
	public $sku_id = null;

	/**
	 * 时间戳,与Z_BASETIME相差小时数
	 * @var int
	 */
	public $time_id = null;

	/**
	 * 累积：访问
	 * @var int
	 * @default	0
	 */
	public $t_view = 0;

	/**
	 * 累积：喜欢次数
	 * @var int
	 * @default	0
	 */
	public $t_like = 0;

	/**
	 * 累积：卖出
	 * @var int
	 * @default	0
	 */
	public $t_sale = 0;

	/**
	 * 累积：分享
	 * @var int
	 * @default	0
	 */
	public $t_share = 0;
}
/**
 * SkuCounterEntity Factory<br>
 * SKU统计数据
 */
final class SkuCounterFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SkuCounterFactory
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
	public static function Instance() : SkuCounterFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SkuCounterFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SkuCounterFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_log`.`sku_counter`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_log`.`sku_counter` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SkuCounterEntity {
		$obj = new SkuCounterEntity();$obj->id = $row['id'];
		$obj->sku_id = $row['sku_id'];
		$obj->time_id = $row['time_id'];
		$obj->t_view = $row['t_view'];
		$obj->t_like = $row['t_like'];
		$obj->t_sale = $row['t_sale'];
		$obj->t_share = $row['t_share'];
		return $obj;
	}

	private function _object_to_insert(SkuCounterEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_log`.`sku_counter` %s(`id`,`sku_id`,`time_id`,`t_view`,`t_like`,`t_sale`,`t_share`) VALUES (NULL,%d,%d,%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->sku_id,$obj->time_id,$obj->t_view,$obj->t_like,$obj->t_sale,$obj->t_share);
	}
	private function _object_to_update(SkuCounterEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_log`.`sku_counter` %s SET `sku_id` = %d,`time_id` = %d,`t_view` = %d,`t_like` = %d,`t_sale` = %d,`t_share` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->sku_id,$obj->time_id,$obj->t_view,$obj->t_like,$obj->t_sale,$obj->t_share,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SkuCounterEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_log`.`sku_counter`";
		}else{
			$sql = "SELECT * FROM `yuemi_log`.`sku_counter` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns SkuCounterEntity
	 * @returns null
	 */
	public function load(int $id) : ?SkuCounterEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_log`.`sku_counter` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_log`.`sku_counter` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 sku_id 加载一条
	 * @param	int  $sku_id  ..SKU_ID
	 * @returns SkuCounterEntity
	 * @returns null
	 */
	public function loadOneBySkuId (int $sku_id) : ?SkuCounterEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_log`.`sku_counter` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}
	/**
	 * 根据普通索引 sku_id 加载全部
	 * @param	int	$sku_id	..SKU_ID
	 * @returns SkuCounterEntity
	 * @returns null
	 */
	public function loadAllBySkuId (int $sku_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_log`.`sku_counter` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}

	/**
	 * 根据普通索引 time_id 加载一条
	 * @param	int  $time_id  ..时间戳,与Z_BASETIME相差小时数
	 * @returns SkuCounterEntity
	 * @returns null
	 */
	public function loadOneByTimeId (int $time_id) : ?SkuCounterEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_log`.`sku_counter` WHERE `time_id` = '%d'",
			$time_id
		));
		
	}
	/**
	 * 根据普通索引 time_id 加载全部
	 * @param	int	$time_id	..时间戳,与Z_BASETIME相差小时数
	 * @returns SkuCounterEntity
	 * @returns null
	 */
	public function loadAllByTimeId (int $time_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_log`.`sku_counter` WHERE `time_id` = '%d'",
			$time_id
		));
		
	}

	/**
	 * 向数据表 yuemi_log.sku_counter 插入一条新纪录
	 * @param	SkuCounterEntity    $obj    ..SKU统计数据
	 * @returns bool
	 */
	public function insert(SkuCounterEntity $obj) : bool {
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
	 * 向数据表 yuemi_log.sku_counter 回写一条记录<br>
	 * 更新依据： yuemi_log.sku_counter.id
	 * @param	SkuCounterEntity	  $obj    ..SKU统计数据
	 * @returns bool
	 */
	 public function update(SkuCounterEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * yuemi_log 存储过程调用器
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
		$this->Tracer = new \Ziima\Tracer('proc.yuemi_log');
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

	
}

