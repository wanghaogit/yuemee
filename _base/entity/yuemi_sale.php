<?php
/*
 *
 */
namespace yuemi_sale;
/**
 * 商品品牌
 * @table brand
 * @engine innodb
 */
final class BrandEntity extends \Ziima\Data\Entity {
	/**
	 * 品牌ID
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
	 * 品牌名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 品牌英文名
	 * @var string
	 */
	public $alias = null;

	/**
	 * 品牌LOGO
	 * @var string
	 */
	public $logo = null;
}
/**
 * BrandEntity Factory<br>
 * 商品品牌
 */
final class BrandFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var BrandFactory
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
	public static function Instance() : BrandFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new BrandFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new BrandFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`brand`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`brand` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : BrandEntity {
		$obj = new BrandEntity();$obj->id = $row['id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->name = $row['name'];
		$obj->alias = $row['alias'];
		$obj->logo = $row['logo'];
		return $obj;
	}

	private function _object_to_insert(BrandEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`brand` %s(`id`,`supplier_id`,`name`,`alias`,`logo`) VALUES (NULL,%d,'%s','%s','%s')";
		return sprintf($sql,'',$obj->supplier_id,self::_encode_string($obj->name,64)
			,self::_encode_string($obj->alias,64)
			,self::_encode_string($obj->logo,65535)
			);
	}
	private function _object_to_update(BrandEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`brand` %s SET `supplier_id` = %d,`name` = '%s',`alias` = '%s',`logo` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->supplier_id,self::_encode_string($obj->name,64)
			,self::_encode_string($obj->alias,64)
			,self::_encode_string($obj->logo,65535)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns BrandEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`brand`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`brand` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..品牌ID
	 * @returns BrandEntity
	 * @returns null
	 */
	public function load(int $id) : ?BrandEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`brand` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..品牌ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`brand` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 name 加载
	 * @param	string	$name	..品牌名称
	 * @returns BrandEntity
	 * @returns null
	 */
	public function loadByName (string $name) : ?BrandEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`brand` WHERE `name` = '%s'",
			parent::$reader->escape_string($name)
		));
		
	}
	
	/**
	 * 根据唯一索引 "name" 删除一条
	 * @param	string	$name	..品牌名称
	 * @returns bool
	 */
	public function deleteByName(string $name) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`brand` WHERE `name` = '%s'",
			parent::$reader->escape_string($name)
		));
		
	}
	
	/**
	 * 根据普通索引 supplier_id 加载一条
	 * @param	int  $supplier_id  ..供应商ID
	 * @returns BrandEntity
	 * @returns null
	 */
	public function loadOneBySupplierId (int $supplier_id) : ?BrandEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`brand` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	/**
	 * 根据普通索引 supplier_id 加载全部
	 * @param	int	$supplier_id	..供应商ID
	 * @returns BrandEntity
	 * @returns null
	 */
	public function loadAllBySupplierId (int $supplier_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`brand` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.brand 插入一条新纪录
	 * @param	BrandEntity    $obj    ..商品品牌
	 * @returns bool
	 */
	public function insert(BrandEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.brand 回写一条记录<br>
	 * 更新依据： yuemi_sale.brand.id
	 * @param	BrandEntity	  $obj    ..商品品牌
	 * @returns bool
	 */
	 public function update(BrandEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 购物车
 * @table cart
 * @engine innodb
 */
final class CartEntity extends \Ziima\Data\Entity {
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
	 * SKUID
	 * @var int
	 * @default	0
	 */
	public $sku_id = 0;

	/**
	 * 来源分享ID
	 * @var int
	 * @default	0
	 */
	public $share_id = 0;

	/**
	 * SPUID
	 * @var int
	 * @default	0
	 */
	public $spu_id = 0;

	/**
	 * 分类ID
	 * @var int
	 * @default	0
	 */
	public $catagory_id = 0;

	/**
	 * 品牌ID
	 * @var int
	 * @default	0
	 */
	public $brand_id = 0;

	/**
	 * 供应商ID
	 * @var int
	 * @default	0
	 */
	public $supplier_id = 0;

	/**
	 * 外部SKUID
	 * @var int
	 * @default	0
	 */
	public $ext_sku_id = 0;

	/**
	 * 外部SKUBN
	 * @var string
	 */
	public $ext_sku_bn = null;

	/**
	 * 外部SPUID
	 * @var int
	 * @default	0
	 */
	public $ext_spu_id = 0;

	/**
	 * 外部SPUBN
	 * @var string
	 */
	public $ext_spu_bn = null;

	/**
	 * 外部供应商ID
	 * @var int
	 * @default	0
	 */
	public $ext_supplier_id = 0;

	/**
	 * 加入购物车时的商品标题
	 * @var string
	 */
	public $sku_title = null;

	/**
	 * 加入购物车时的商品价格
	 * @var float
	 * @default	0.0000
	 */
	public $sku_price = 0.0000;

	/**
	 * 加入购物车时的商品缩略图
	 * @var string
	 */
	public $sku_thumb = null;

	/**
	 * 加入购物车时的商品规格
	 * @var string
	 */
	public $sku_spec = null;

	/**
	 * 下单数量
	 * @var int
	 */
	public $qty = null;

	/**
	 * 选中状态
	 * @var int
	 * @default	1
	 */
	public $is_checked = 1;

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
 * CartEntity Factory<br>
 * 购物车
 */
final class CartFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var CartFactory
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
	public static function Instance() : CartFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new CartFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new CartFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`cart`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`cart` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : CartEntity {
		$obj = new CartEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->sku_id = $row['sku_id'];
		$obj->share_id = $row['share_id'];
		$obj->spu_id = $row['spu_id'];
		$obj->catagory_id = $row['catagory_id'];
		$obj->brand_id = $row['brand_id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->ext_sku_id = $row['ext_sku_id'];
		$obj->ext_sku_bn = $row['ext_sku_bn'];
		$obj->ext_spu_id = $row['ext_spu_id'];
		$obj->ext_spu_bn = $row['ext_spu_bn'];
		$obj->ext_supplier_id = $row['ext_supplier_id'];
		$obj->sku_title = $row['sku_title'];
		$obj->sku_price = $row['sku_price'];
		$obj->sku_thumb = $row['sku_thumb'];
		$obj->sku_spec = $row['sku_spec'];
		$obj->qty = $row['qty'];
		$obj->is_checked = $row['is_checked'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(CartEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`cart` %s(`id`,`user_id`,`sku_id`,`share_id`,`spu_id`,`catagory_id`,`brand_id`,`supplier_id`,`ext_sku_id`,`ext_sku_bn`,`ext_spu_id`,`ext_spu_bn`,`ext_supplier_id`,`sku_title`,`sku_price`,`sku_thumb`,`sku_spec`,`qty`,`is_checked`,`create_time`,`create_from`) VALUES (NULL,%d,%d,%d,%d,%d,%d,%d,%d,'%s',%d,'%s',%d,'%s',%f,'%s','%s',%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->user_id,$obj->sku_id,$obj->share_id,$obj->spu_id,$obj->catagory_id,$obj->brand_id,$obj->supplier_id,$obj->ext_sku_id,self::_encode_string($obj->ext_sku_bn,24)
			,$obj->ext_spu_id,self::_encode_string($obj->ext_spu_bn,24)
			,$obj->ext_supplier_id,self::_encode_string($obj->sku_title,128)
			,$obj->sku_price,self::_encode_string($obj->sku_thumb,256)
			,self::_encode_string($obj->sku_spec,65535)
			,$obj->qty,$obj->is_checked,$obj->create_time,$obj->create_from);
	}
	private function _object_to_update(CartEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`cart` %s SET `user_id` = %d,`sku_id` = %d,`share_id` = %d,`spu_id` = %d,`catagory_id` = %d,`brand_id` = %d,`supplier_id` = %d,`ext_sku_id` = %d,`ext_sku_bn` = '%s',`ext_spu_id` = %d,`ext_spu_bn` = '%s',`ext_supplier_id` = %d,`sku_title` = '%s',`sku_price` = %f,`sku_thumb` = '%s',`sku_spec` = '%s',`qty` = %d,`is_checked` = %d,`create_time` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->sku_id,$obj->share_id,$obj->spu_id,$obj->catagory_id,$obj->brand_id,$obj->supplier_id,$obj->ext_sku_id,self::_encode_string($obj->ext_sku_bn,24)
			,$obj->ext_spu_id,self::_encode_string($obj->ext_spu_bn,24)
			,$obj->ext_supplier_id,self::_encode_string($obj->sku_title,128)
			,$obj->sku_price,self::_encode_string($obj->sku_thumb,256)
			,self::_encode_string($obj->sku_spec,65535)
			,$obj->qty,$obj->is_checked,$obj->create_time,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns CartEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`cart`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`cart` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns CartEntity
	 * @returns null
	 */
	public function load(int $id) : ?CartEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`cart` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_sale`.`cart` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns CartEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?CartEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`cart` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns CartEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`cart` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 sku_id 加载一条
	 * @param	int  $sku_id  ..SKUID
	 * @returns CartEntity
	 * @returns null
	 */
	public function loadOneBySkuId (int $sku_id) : ?CartEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`cart` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}
	/**
	 * 根据普通索引 sku_id 加载全部
	 * @param	int	$sku_id	..SKUID
	 * @returns CartEntity
	 * @returns null
	 */
	public function loadAllBySkuId (int $sku_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`cart` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}

	/**
	 * 根据普通索引 supplier_id 加载一条
	 * @param	int  $supplier_id  ..供应商ID
	 * @returns CartEntity
	 * @returns null
	 */
	public function loadOneBySupplierId (int $supplier_id) : ?CartEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`cart` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	/**
	 * 根据普通索引 supplier_id 加载全部
	 * @param	int	$supplier_id	..供应商ID
	 * @returns CartEntity
	 * @returns null
	 */
	public function loadAllBySupplierId (int $supplier_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`cart` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}

	/**
	 * 根据普通索引 is_checked 加载一条
	 * @param	int  $is_checked  ..选中状态
	 * @returns CartEntity
	 * @returns null
	 */
	public function loadOneByIsChecked (int $is_checked) : ?CartEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`cart` WHERE `is_checked` = '%d'",
			$is_checked
		));
		
	}
	/**
	 * 根据普通索引 is_checked 加载全部
	 * @param	int	$is_checked	..选中状态
	 * @returns CartEntity
	 * @returns null
	 */
	public function loadAllByIsChecked (int $is_checked) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`cart` WHERE `is_checked` = '%d'",
			$is_checked
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.cart 插入一条新纪录
	 * @param	CartEntity    $obj    ..购物车
	 * @returns bool
	 */
	public function insert(CartEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.cart 回写一条记录<br>
	 * 更新依据： yuemi_sale.cart.id
	 * @param	CartEntity	  $obj    ..购物车
	 * @returns bool
	 */
	 public function update(CartEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 商品分类
 * @table catagory
 * @engine innodb
 */
final class CatagoryEntity extends \Ziima\Data\Entity {
	/**
	 * 行业ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 上级ID
	 * @var int
	 * @default	0
	 */
	public $parent_id = 0;

	/**
	 * 行业名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 管理员，一级审批权限
	 * @var int
	 * @default	0
	 */
	public $manager_id = 0;

	/**
	 * 是否隐藏类目
	 * @var int
	 * @default	0
	 */
	public $is_hidden = 0;

	/**
	 * 普通用户可见
	 * @var int
	 * @default	1
	 */
	public $lv_user = 1;

	/**
	 * VIP可见
	 * @var int
	 * @default	1
	 */
	public $lv_vip = 1;

	/**
	 * 总监可见
	 * @var int
	 * @default	1
	 */
	public $lv_cheif = 1;

	/**
	 * 总经理可见
	 * @var int
	 * @default	1
	 */
	public $lv_director = 1;

	/**
	 * 排序
	 * @var int
	 * @default	0
	 */
	public $p_order = 0;

	/**
	 * 图标
	 * @var string
	 */
	public $icon = null;

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
 * CatagoryEntity Factory<br>
 * 商品分类
 */
final class CatagoryFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var CatagoryFactory
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
	public static function Instance() : CatagoryFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new CatagoryFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new CatagoryFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`catagory`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`catagory` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : CatagoryEntity {
		$obj = new CatagoryEntity();$obj->id = $row['id'];
		$obj->parent_id = $row['parent_id'];
		$obj->name = $row['name'];
		$obj->manager_id = $row['manager_id'];
		$obj->is_hidden = $row['is_hidden'];
		$obj->lv_user = $row['lv_user'];
		$obj->lv_vip = $row['lv_vip'];
		$obj->lv_cheif = $row['lv_cheif'];
		$obj->lv_director = $row['lv_director'];
		$obj->p_order = $row['p_order'];
		$obj->icon = $row['icon'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(CatagoryEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`catagory` %s(`id`,`parent_id`,`name`,`manager_id`,`is_hidden`,`lv_user`,`lv_vip`,`lv_cheif`,`lv_director`,`p_order`,`icon`,`create_user`,`create_time`,`create_from`) VALUES (NULL,%d,'%s',%d,%d,%d,%d,%d,%d,%d,'%s',%d,UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			,$obj->manager_id,$obj->is_hidden,$obj->lv_user,$obj->lv_vip,$obj->lv_cheif,$obj->lv_director,$obj->p_order,self::_encode_string($obj->icon,65535)
			,$obj->create_user,$obj->create_from);
	}
	private function _object_to_update(CatagoryEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`catagory` %s SET `parent_id` = %d,`name` = '%s',`manager_id` = %d,`is_hidden` = %d,`lv_user` = %d,`lv_vip` = %d,`lv_cheif` = %d,`lv_director` = %d,`p_order` = %d,`icon` = '%s',`create_user` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			,$obj->manager_id,$obj->is_hidden,$obj->lv_user,$obj->lv_vip,$obj->lv_cheif,$obj->lv_director,$obj->p_order,self::_encode_string($obj->icon,65535)
			,$obj->create_user,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns CatagoryEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`catagory`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`catagory` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..行业ID
	 * @returns CatagoryEntity
	 * @returns null
	 */
	public function load(int $id) : ?CatagoryEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`catagory` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..行业ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`catagory` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 parent_id 加载一条
	 * @param	int  $parent_id  ..上级ID
	 * @returns CatagoryEntity
	 * @returns null
	 */
	public function loadOneByParentId (int $parent_id) : ?CatagoryEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}
	/**
	 * 根据普通索引 parent_id 加载全部
	 * @param	int	$parent_id	..上级ID
	 * @returns CatagoryEntity
	 * @returns null
	 */
	public function loadAllByParentId (int $parent_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.catagory 插入一条新纪录
	 * @param	CatagoryEntity    $obj    ..商品分类
	 * @returns bool
	 */
	public function insert(CatagoryEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.catagory 回写一条记录<br>
	 * 更新依据： yuemi_sale.catagory.id
	 * @param	CatagoryEntity	  $obj    ..商品分类
	 * @returns bool
	 */
	 public function update(CatagoryEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 优惠券
 * @table discount_coupon
 * @engine innodb
 */
final class DiscountCouponEntity extends \Ziima\Data\Entity {
	/**
	 * 
	 * @var string
	 */
	public $id = null;

	/**
	 * 类型：0未知，1商品券，2商家券，3品类券
	 * @var int
	 */
	public $type = null;

	/**
	 * 商品spu_id
	 * @var int
	 */
	public $spu_id = null;

	/**
	 * 优惠券价值
	 * @var float
	 */
	public $value = null;

	/**
	 * 可用最小订单价（等于/高于此价格可用）
	 * @var float
	 */
	public $price_small = null;

	/**
	 * 有效期（时间截）
	 * @var int
	 */
	public $expiry_date = null;

	/**
	 * 使用者id
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 创建者id
	 * @var int
	 */
	public $creator_id = null;

	/**
	 * 创建时间
	 * @var int
	 */
	public $create_time = null;

	/**
	 * 更新时间
	 * @var int
	 */
	public $update_time = null;

	/**
	 * 状态：0初始创建，1已使用，2关闭
	 * @var int
	 */
	public $status = null;
}
/**
 * DiscountCouponEntity Factory<br>
 * 优惠券
 */
final class DiscountCouponFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var DiscountCouponFactory
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
	public static function Instance() : DiscountCouponFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new DiscountCouponFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new DiscountCouponFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`discount_coupon`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`discount_coupon` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : DiscountCouponEntity {
		$obj = new DiscountCouponEntity();$obj->id = $row['id'];
		$obj->type = $row['type'];
		$obj->spu_id = $row['spu_id'];
		$obj->value = $row['value'];
		$obj->price_small = $row['price_small'];
		$obj->expiry_date = $row['expiry_date'];
		$obj->user_id = $row['user_id'];
		$obj->creator_id = $row['creator_id'];
		$obj->create_time = $row['create_time'];
		$obj->update_time = $row['update_time'];
		$obj->status = $row['status'];
		return $obj;
	}

	private function _object_to_insert(DiscountCouponEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`discount_coupon` %s(`id`,`type`,`spu_id`,`value`,`price_small`,`expiry_date`,`user_id`,`creator_id`,`create_time`,`update_time`,`status`) VALUES ('%s',%d,%d,%f,%f,%d,%d,%d,%d,%d,%d)";
		return sprintf($sql,'',self::_encode_string($obj->id,32)
			,$obj->type,$obj->spu_id,$obj->value,$obj->price_small,$obj->expiry_date,$obj->user_id,$obj->creator_id,$obj->create_time,$obj->update_time,$obj->status);
	}
	private function _object_to_update(DiscountCouponEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`discount_coupon` %s SET `type` = %d,`spu_id` = %d,`value` = %f,`price_small` = %f,`expiry_date` = %d,`user_id` = %d,`creator_id` = %d,`create_time` = %d,`update_time` = %d,`status` = %d WHERE `id` = '%s'";
		
		return sprintf($sql,'',$obj->type,$obj->spu_id,$obj->value,$obj->price_small,$obj->expiry_date,$obj->user_id,$obj->creator_id,$obj->create_time,$obj->update_time,$obj->status,parent::$reader->escape_string($obj->id)
			);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns DiscountCouponEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`discount_coupon`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`discount_coupon` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "id" 加载一条
	 * @param	string	$id	..
	 * @returns DiscountCouponEntity
	 * @returns null
	 */
	public function load(string $id) : ?DiscountCouponEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`discount_coupon` WHERE `id` = '%s'",
			parent::$reader->escape_string($id)
		));
		
	}
	
	/**
	 * 根据主键 "id" 删除一条
	 * @param	string	$id	..
	 * @returns bool
	 */
	public function delete(string $id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`discount_coupon` WHERE `id` = '%s'",
			parent::$reader->escape_string($id)
		));
		
	}
	
	/**
	 * 根据普通索引 type 加载一条
	 * @param	int  $type  ..类型：0未知，1商品券，2商家券，3品类券
	 * @returns DiscountCouponEntity
	 * @returns null
	 */
	public function loadOneByType (int $type) : ?DiscountCouponEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`discount_coupon` WHERE `type` = '%d'",
			$type
		));
		
	}
	/**
	 * 根据普通索引 type 加载全部
	 * @param	int	$type	..类型：0未知，1商品券，2商家券，3品类券
	 * @returns DiscountCouponEntity
	 * @returns null
	 */
	public function loadAllByType (int $type) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`discount_coupon` WHERE `type` = '%d'",
			$type
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..状态：0初始创建，1已使用，2关闭
	 * @returns DiscountCouponEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?DiscountCouponEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`discount_coupon` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..状态：0初始创建，1已使用，2关闭
	 * @returns DiscountCouponEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`discount_coupon` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.discount_coupon 插入一条新纪录
	 * @param	DiscountCouponEntity    $obj    ..优惠券
	 * @returns bool
	 */
	public function insert(DiscountCouponEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_sale.discount_coupon 回写一条记录<br>
	 * 更新依据： yuemi_sale.discount_coupon.id
	 * @param	DiscountCouponEntity	  $obj    ..优惠券
	 * @returns bool
	 */
	 public function update(DiscountCouponEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 贡云商品分类
 * @table ext_gongyun_catagory
 * @engine innodb
 */
final class ExtGongyunCatagoryEntity extends \Ziima\Data\Entity {
	/**
	 * 分类ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 上级ID
	 * @var int
	 * @default	0
	 */
	public $parent_id = 0;

	/**
	 * 分类名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 映射阅米内部分类ID
	 * @var int
	 * @default	0
	 */
	public $map_id = 0;
}
/**
 * ExtGongyunCatagoryEntity Factory<br>
 * 贡云商品分类
 */
final class ExtGongyunCatagoryFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ExtGongyunCatagoryFactory
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
	public static function Instance() : ExtGongyunCatagoryFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ExtGongyunCatagoryFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ExtGongyunCatagoryFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_gongyun_catagory`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_gongyun_catagory` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ExtGongyunCatagoryEntity {
		$obj = new ExtGongyunCatagoryEntity();$obj->id = $row['id'];
		$obj->parent_id = $row['parent_id'];
		$obj->name = $row['name'];
		$obj->map_id = $row['map_id'];
		return $obj;
	}

	private function _object_to_insert(ExtGongyunCatagoryEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`ext_gongyun_catagory` %s(`id`,`parent_id`,`name`,`map_id`) VALUES (NULL,%d,'%s',%d)";
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			,$obj->map_id);
	}
	private function _object_to_update(ExtGongyunCatagoryEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`ext_gongyun_catagory` %s SET `parent_id` = %d,`name` = '%s',`map_id` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			,$obj->map_id,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ExtGongyunCatagoryEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`ext_gongyun_catagory`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`ext_gongyun_catagory` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..分类ID
	 * @returns ExtGongyunCatagoryEntity
	 * @returns null
	 */
	public function load(int $id) : ?ExtGongyunCatagoryEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_gongyun_catagory` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..分类ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`ext_gongyun_catagory` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 parent_id 加载一条
	 * @param	int  $parent_id  ..上级ID
	 * @returns ExtGongyunCatagoryEntity
	 * @returns null
	 */
	public function loadOneByParentId (int $parent_id) : ?ExtGongyunCatagoryEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_gongyun_catagory` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}
	/**
	 * 根据普通索引 parent_id 加载全部
	 * @param	int	$parent_id	..上级ID
	 * @returns ExtGongyunCatagoryEntity
	 * @returns null
	 */
	public function loadAllByParentId (int $parent_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_gongyun_catagory` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}

	/**
	 * 根据普通索引 map_id 加载一条
	 * @param	int  $map_id  ..映射阅米内部分类ID
	 * @returns ExtGongyunCatagoryEntity
	 * @returns null
	 */
	public function loadOneByMapId (int $map_id) : ?ExtGongyunCatagoryEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_gongyun_catagory` WHERE `map_id` = '%d'",
			$map_id
		));
		
	}
	/**
	 * 根据普通索引 map_id 加载全部
	 * @param	int	$map_id	..映射阅米内部分类ID
	 * @returns ExtGongyunCatagoryEntity
	 * @returns null
	 */
	public function loadAllByMapId (int $map_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_gongyun_catagory` WHERE `map_id` = '%d'",
			$map_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.ext_gongyun_catagory 插入一条新纪录
	 * @param	ExtGongyunCatagoryEntity    $obj    ..贡云商品分类
	 * @returns bool
	 */
	public function insert(ExtGongyunCatagoryEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.ext_gongyun_catagory 回写一条记录<br>
	 * 更新依据： yuemi_sale.ext_gongyun_catagory.id
	 * @param	ExtGongyunCatagoryEntity	  $obj    ..贡云商品分类
	 * @returns bool
	 */
	 public function update(ExtGongyunCatagoryEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 内购商品分类
 * @table ext_neigou_catagory
 * @engine innodb
 */
final class ExtNeigouCatagoryEntity extends \Ziima\Data\Entity {
	/**
	 * 分类ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 上级ID
	 * @var int
	 * @default	0
	 */
	public $parent_id = 0;

	/**
	 * 分类名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 映射阅米内部分类ID
	 * @var int
	 * @default	0
	 */
	public $map_id = 0;
}
/**
 * ExtNeigouCatagoryEntity Factory<br>
 * 内购商品分类
 */
final class ExtNeigouCatagoryFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ExtNeigouCatagoryFactory
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
	public static function Instance() : ExtNeigouCatagoryFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ExtNeigouCatagoryFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ExtNeigouCatagoryFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_neigou_catagory`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_neigou_catagory` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ExtNeigouCatagoryEntity {
		$obj = new ExtNeigouCatagoryEntity();$obj->id = $row['id'];
		$obj->parent_id = $row['parent_id'];
		$obj->name = $row['name'];
		$obj->map_id = $row['map_id'];
		return $obj;
	}

	private function _object_to_insert(ExtNeigouCatagoryEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`ext_neigou_catagory` %s(`id`,`parent_id`,`name`,`map_id`) VALUES (NULL,%d,'%s',%d)";
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			,$obj->map_id);
	}
	private function _object_to_update(ExtNeigouCatagoryEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`ext_neigou_catagory` %s SET `parent_id` = %d,`name` = '%s',`map_id` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->parent_id,self::_encode_string($obj->name,32)
			,$obj->map_id,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ExtNeigouCatagoryEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`ext_neigou_catagory`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`ext_neigou_catagory` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..分类ID
	 * @returns ExtNeigouCatagoryEntity
	 * @returns null
	 */
	public function load(int $id) : ?ExtNeigouCatagoryEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_neigou_catagory` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..分类ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`ext_neigou_catagory` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 parent_id 加载一条
	 * @param	int  $parent_id  ..上级ID
	 * @returns ExtNeigouCatagoryEntity
	 * @returns null
	 */
	public function loadOneByParentId (int $parent_id) : ?ExtNeigouCatagoryEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_neigou_catagory` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}
	/**
	 * 根据普通索引 parent_id 加载全部
	 * @param	int	$parent_id	..上级ID
	 * @returns ExtNeigouCatagoryEntity
	 * @returns null
	 */
	public function loadAllByParentId (int $parent_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_neigou_catagory` WHERE `parent_id` = '%d'",
			$parent_id
		));
		
	}

	/**
	 * 根据普通索引 map_id 加载一条
	 * @param	int  $map_id  ..映射阅米内部分类ID
	 * @returns ExtNeigouCatagoryEntity
	 * @returns null
	 */
	public function loadOneByMapId (int $map_id) : ?ExtNeigouCatagoryEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_neigou_catagory` WHERE `map_id` = '%d'",
			$map_id
		));
		
	}
	/**
	 * 根据普通索引 map_id 加载全部
	 * @param	int	$map_id	..映射阅米内部分类ID
	 * @returns ExtNeigouCatagoryEntity
	 * @returns null
	 */
	public function loadAllByMapId (int $map_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_neigou_catagory` WHERE `map_id` = '%d'",
			$map_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.ext_neigou_catagory 插入一条新纪录
	 * @param	ExtNeigouCatagoryEntity    $obj    ..内购商品分类
	 * @returns bool
	 */
	public function insert(ExtNeigouCatagoryEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.ext_neigou_catagory 回写一条记录<br>
	 * 更新依据： yuemi_sale.ext_neigou_catagory.id
	 * @param	ExtNeigouCatagoryEntity	  $obj    ..内购商品分类
	 * @returns bool
	 */
	 public function update(ExtNeigouCatagoryEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 外部SKU
 * @table ext_sku
 * @engine innodb
 */
final class ExtSkuEntity extends \Ziima\Data\Entity {
	/**
	 * 外部SPUID
	 * @var int
	 */
	public $id = null;

	/**
	 * 外部供应商ID，2=内购，3=贡云
	 * @var int
	 */
	public $supplier_id = null;

	/**
	 * 商品bn
	 * @var string
	 */
	public $bn = null;

	/**
	 * 关联外部SPUID
	 * @var int
	 */
	public $ext_spu_id = null;

	/**
	 * 内部SKUID
	 * @var int
	 * @default	0
	 */
	public $sku_id = 0;

	/**
	 * 货品名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 货品重量
	 * @var float
	 * @default	0.0000
	 */
	public $weight = 0.0000;

	/**
	 * 货品条形码
	 * @var string
	 */
	public $barcode = null;

	/**
	 * 成本价
	 * @var float
	 * @default	0.0000
	 */
	public $price_base = 0.0000;

	/**
	 * 货品市场价
	 * @var float
	 * @default	0.0000
	 */
	public $price_ref = 0.0000;

	/**
	 * 实时库存数量
	 * @var int
	 * @default	0
	 */
	public $stock = 0;

	/**
	 * 描述内容
	 * @var string
	 */
	public $intro = null;

	/**
	 * 内容本地化状态：0待处理,1失败,2成功
	 * @var int
	 * @default	0
	 */
	public $lo_status = 0;

	/**
	 * 内容本地化错误次数
	 * @var int
	 * @default	0
	 */
	public $lo_error = 0;

	/**
	 * 内容本地化处理时间
	 * @var int
	 * @default	0
	 */
	public $lo_time = 0;

	/**
	 * 货品规格
	 * @var string
	 */
	public $spec = null;

	/**
	 * 外部SKU状态，0无效,1有效
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
}
/**
 * ExtSkuEntity Factory<br>
 * 外部SKU
 */
final class ExtSkuFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ExtSkuFactory
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
	public static function Instance() : ExtSkuFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ExtSkuFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ExtSkuFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_sku`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_sku` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ExtSkuEntity {
		$obj = new ExtSkuEntity();$obj->id = $row['id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->bn = $row['bn'];
		$obj->ext_spu_id = $row['ext_spu_id'];
		$obj->sku_id = $row['sku_id'];
		$obj->name = $row['name'];
		$obj->weight = $row['weight'];
		$obj->barcode = $row['barcode'];
		$obj->price_base = $row['price_base'];
		$obj->price_ref = $row['price_ref'];
		$obj->stock = $row['stock'];
		$obj->intro = $row['intro'];
		$obj->lo_status = $row['lo_status'];
		$obj->lo_error = $row['lo_error'];
		$obj->lo_time = $row['lo_time'];
		$obj->spec = $row['spec'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->update_time = $row['update_time'];
		return $obj;
	}

	private function _object_to_insert(ExtSkuEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`ext_sku` %s(`id`,`supplier_id`,`bn`,`ext_spu_id`,`sku_id`,`name`,`weight`,`barcode`,`price_base`,`price_ref`,`stock`,`intro`,`lo_status`,`lo_error`,`lo_time`,`spec`,`status`,`create_time`,`update_time`) VALUES (NULL,%d,'%s',%d,%d,'%s',%f,'%s',%f,%f,%d,'%s',%d,%d,%d,'%s',%d,%d,%d)";
		return sprintf($sql,'',$obj->supplier_id,self::_encode_string($obj->bn,24)
			,$obj->ext_spu_id,$obj->sku_id,self::_encode_string($obj->name,128)
			,$obj->weight,self::_encode_string($obj->barcode,128)
			,$obj->price_base,$obj->price_ref,$obj->stock,self::_encode_string($obj->intro,65535)
			,$obj->lo_status,$obj->lo_error,$obj->lo_time,self::_encode_string($obj->spec,65535)
			,$obj->status,$obj->create_time,$obj->update_time);
	}
	private function _object_to_update(ExtSkuEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`ext_sku` %s SET `supplier_id` = %d,`bn` = '%s',`ext_spu_id` = %d,`sku_id` = %d,`name` = '%s',`weight` = %f,`barcode` = '%s',`price_base` = %f,`price_ref` = %f,`stock` = %d,`intro` = '%s',`lo_status` = %d,`lo_error` = %d,`lo_time` = %d,`spec` = '%s',`status` = %d,`create_time` = %d,`update_time` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->supplier_id,self::_encode_string($obj->bn,24)
			,$obj->ext_spu_id,$obj->sku_id,self::_encode_string($obj->name,128)
			,$obj->weight,self::_encode_string($obj->barcode,128)
			,$obj->price_base,$obj->price_ref,$obj->stock,self::_encode_string($obj->intro,65535)
			,$obj->lo_status,$obj->lo_error,$obj->lo_time,self::_encode_string($obj->spec,65535)
			,$obj->status,$obj->create_time,$obj->update_time,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ExtSkuEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`ext_sku`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`ext_sku` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..外部SPUID
	 * @returns ExtSkuEntity
	 * @returns null
	 */
	public function load(int $id) : ?ExtSkuEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..外部SPUID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`ext_sku` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 supplier_id 加载一条
	 * @param	int  $supplier_id  ..外部供应商ID，2=内购，3=贡云
	 * @returns ExtSkuEntity
	 * @returns null
	 */
	public function loadOneBySupplierId (int $supplier_id) : ?ExtSkuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	/**
	 * 根据普通索引 supplier_id 加载全部
	 * @param	int	$supplier_id	..外部供应商ID，2=内购，3=贡云
	 * @returns ExtSkuEntity
	 * @returns null
	 */
	public function loadAllBySupplierId (int $supplier_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}

	/**
	 * 根据普通索引 bn 加载一条
	 * @param	string  $bn  ..商品bn
	 * @returns ExtSkuEntity
	 * @returns null
	 */
	public function loadOneByBn (string $bn) : ?ExtSkuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku` WHERE `bn` = '%s'",
			parent::$reader->escape_string($bn)
		));
		
	}
	/**
	 * 根据普通索引 bn 加载全部
	 * @param	string	$bn	..商品bn
	 * @returns ExtSkuEntity
	 * @returns null
	 */
	public function loadAllByBn (string $bn) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku` WHERE `bn` = '%s'",
			parent::$reader->escape_string($bn)
		));
		
	}

	/**
	 * 根据普通索引 ext_spu_id 加载一条
	 * @param	int  $ext_spu_id  ..关联外部SPUID
	 * @returns ExtSkuEntity
	 * @returns null
	 */
	public function loadOneByExtSpuId (int $ext_spu_id) : ?ExtSkuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku` WHERE `ext_spu_id` = '%d'",
			$ext_spu_id
		));
		
	}
	/**
	 * 根据普通索引 ext_spu_id 加载全部
	 * @param	int	$ext_spu_id	..关联外部SPUID
	 * @returns ExtSkuEntity
	 * @returns null
	 */
	public function loadAllByExtSpuId (int $ext_spu_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku` WHERE `ext_spu_id` = '%d'",
			$ext_spu_id
		));
		
	}

	/**
	 * 根据普通索引 sku_id 加载一条
	 * @param	int  $sku_id  ..内部SKUID
	 * @returns ExtSkuEntity
	 * @returns null
	 */
	public function loadOneBySkuId (int $sku_id) : ?ExtSkuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}
	/**
	 * 根据普通索引 sku_id 加载全部
	 * @param	int	$sku_id	..内部SKUID
	 * @returns ExtSkuEntity
	 * @returns null
	 */
	public function loadAllBySkuId (int $sku_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.ext_sku 插入一条新纪录
	 * @param	ExtSkuEntity    $obj    ..外部SKU
	 * @returns bool
	 */
	public function insert(ExtSkuEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.ext_sku 回写一条记录<br>
	 * 更新依据： yuemi_sale.ext_sku.id
	 * @param	ExtSkuEntity	  $obj    ..外部SKU
	 * @returns bool
	 */
	 public function update(ExtSkuEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 外部SKU变化通知
 * @table ext_sku_changes
 * @engine innodb
 */
final class ExtSkuChangesEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * SKUID
	 * @var int
	 */
	public $ext_sku_id = null;

	/**
	 * 供应商ID
	 * @var int
	 */
	public $supplier_id = null;

	/**
	 * 是否变更标题
	 * @var int
	 * @default	0
	 */
	public $chg_title = 0;

	/**
	 * 旧标题
	 * @var string
	 */
	public $old_title = null;

	/**
	 * 新标题
	 * @var string
	 */
	public $new_title = null;

	/**
	 * 是否变更品类
	 * @var int
	 * @default	0
	 */
	public $chg_catagory = 0;

	/**
	 * 旧分类
	 * @var int
	 * @default	0
	 */
	public $old_catagory = 0;

	/**
	 * 新分类
	 * @var int
	 * @default	0
	 */
	public $new_catagory = 0;

	/**
	 * 是否变更成本价
	 * @var int
	 * @default	0
	 */
	public $chg_price_base = 0;

	/**
	 * 旧价格
	 * @var float
	 * @default	0.0000
	 */
	public $old_price_base = 0.0000;

	/**
	 * 新价格
	 * @var float
	 * @default	0.0000
	 */
	public $new_price_base = 0.0000;

	/**
	 * 是否变更参考价
	 * @var int
	 * @default	0
	 */
	public $chg_price_ref = 0;

	/**
	 * 旧价格
	 * @var float
	 * @default	0.0000
	 */
	public $old_price_ref = 0.0000;

	/**
	 * 新价格
	 * @var float
	 * @default	0.0000
	 */
	public $new_price_ref = 0.0000;

	/**
	 * 是否变更毛利
	 * @var int
	 * @default	0
	 */
	public $chg_ratio = 0;

	/**
	 * 旧毛利
	 * @var float
	 * @default	0.000000
	 */
	public $old_ratio = 0.000000;

	/**
	 * 新毛利
	 * @var float
	 * @default	0.000000
	 */
	public $new_ratio = 0.000000;

	/**
	 * 是否变更库存
	 * @var int
	 * @default	0
	 */
	public $chg_depot = 0;

	/**
	 * 旧库存
	 * @var int
	 * @default	0
	 */
	public $old_depot = 0;

	/**
	 * 新库存
	 * @var int
	 * @default	0
	 */
	public $new_depot = 0;

	/**
	 * 备注消息
	 * @var string
	 */
	public $message = null;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;
}
/**
 * ExtSkuChangesEntity Factory<br>
 * 外部SKU变化通知
 */
final class ExtSkuChangesFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ExtSkuChangesFactory
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
	public static function Instance() : ExtSkuChangesFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ExtSkuChangesFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ExtSkuChangesFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_sku_changes`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_sku_changes` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ExtSkuChangesEntity {
		$obj = new ExtSkuChangesEntity();$obj->id = $row['id'];
		$obj->ext_sku_id = $row['ext_sku_id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->chg_title = $row['chg_title'];
		$obj->old_title = $row['old_title'];
		$obj->new_title = $row['new_title'];
		$obj->chg_catagory = $row['chg_catagory'];
		$obj->old_catagory = $row['old_catagory'];
		$obj->new_catagory = $row['new_catagory'];
		$obj->chg_price_base = $row['chg_price_base'];
		$obj->old_price_base = $row['old_price_base'];
		$obj->new_price_base = $row['new_price_base'];
		$obj->chg_price_ref = $row['chg_price_ref'];
		$obj->old_price_ref = $row['old_price_ref'];
		$obj->new_price_ref = $row['new_price_ref'];
		$obj->chg_ratio = $row['chg_ratio'];
		$obj->old_ratio = $row['old_ratio'];
		$obj->new_ratio = $row['new_ratio'];
		$obj->chg_depot = $row['chg_depot'];
		$obj->old_depot = $row['old_depot'];
		$obj->new_depot = $row['new_depot'];
		$obj->message = $row['message'];
		$obj->create_time = $row['create_time'];
		return $obj;
	}

	private function _object_to_insert(ExtSkuChangesEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`ext_sku_changes` %s(`id`,`ext_sku_id`,`supplier_id`,`chg_title`,`old_title`,`new_title`,`chg_catagory`,`old_catagory`,`new_catagory`,`chg_price_base`,`old_price_base`,`new_price_base`,`chg_price_ref`,`old_price_ref`,`new_price_ref`,`chg_ratio`,`old_ratio`,`new_ratio`,`chg_depot`,`old_depot`,`new_depot`,`message`,`create_time`) VALUES (NULL,%d,%d,%d,'%s','%s',%d,%d,%d,%d,%f,%f,%d,%f,%f,%d,%f,%f,%d,%d,%d,'%s',UNIX_TIMESTAMP())";
		return sprintf($sql,'',$obj->ext_sku_id,$obj->supplier_id,$obj->chg_title,self::_encode_string($obj->old_title,128)
			,self::_encode_string($obj->new_title,128)
			,$obj->chg_catagory,$obj->old_catagory,$obj->new_catagory,$obj->chg_price_base,$obj->old_price_base,$obj->new_price_base,$obj->chg_price_ref,$obj->old_price_ref,$obj->new_price_ref,$obj->chg_ratio,$obj->old_ratio,$obj->new_ratio,$obj->chg_depot,$obj->old_depot,$obj->new_depot,self::_encode_string($obj->message,512)
			);
	}
	private function _object_to_update(ExtSkuChangesEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`ext_sku_changes` %s SET `ext_sku_id` = %d,`supplier_id` = %d,`chg_title` = %d,`old_title` = '%s',`new_title` = '%s',`chg_catagory` = %d,`old_catagory` = %d,`new_catagory` = %d,`chg_price_base` = %d,`old_price_base` = %f,`new_price_base` = %f,`chg_price_ref` = %d,`old_price_ref` = %f,`new_price_ref` = %f,`chg_ratio` = %d,`old_ratio` = %f,`new_ratio` = %f,`chg_depot` = %d,`old_depot` = %d,`new_depot` = %d,`message` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->ext_sku_id,$obj->supplier_id,$obj->chg_title,self::_encode_string($obj->old_title,128)
			,self::_encode_string($obj->new_title,128)
			,$obj->chg_catagory,$obj->old_catagory,$obj->new_catagory,$obj->chg_price_base,$obj->old_price_base,$obj->new_price_base,$obj->chg_price_ref,$obj->old_price_ref,$obj->new_price_ref,$obj->chg_ratio,$obj->old_ratio,$obj->new_ratio,$obj->chg_depot,$obj->old_depot,$obj->new_depot,self::_encode_string($obj->message,512)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ExtSkuChangesEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`ext_sku_changes`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`ext_sku_changes` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns ExtSkuChangesEntity
	 * @returns null
	 */
	public function load(int $id) : ?ExtSkuChangesEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_changes` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_sale`.`ext_sku_changes` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 ext_sku_id 加载一条
	 * @param	int  $ext_sku_id  ..SKUID
	 * @returns ExtSkuChangesEntity
	 * @returns null
	 */
	public function loadOneByExtSkuId (int $ext_sku_id) : ?ExtSkuChangesEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_changes` WHERE `ext_sku_id` = '%d'",
			$ext_sku_id
		));
		
	}
	/**
	 * 根据普通索引 ext_sku_id 加载全部
	 * @param	int	$ext_sku_id	..SKUID
	 * @returns ExtSkuChangesEntity
	 * @returns null
	 */
	public function loadAllByExtSkuId (int $ext_sku_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_changes` WHERE `ext_sku_id` = '%d'",
			$ext_sku_id
		));
		
	}

	/**
	 * 根据普通索引 supplier_id 加载一条
	 * @param	int  $supplier_id  ..供应商ID
	 * @returns ExtSkuChangesEntity
	 * @returns null
	 */
	public function loadOneBySupplierId (int $supplier_id) : ?ExtSkuChangesEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_changes` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	/**
	 * 根据普通索引 supplier_id 加载全部
	 * @param	int	$supplier_id	..供应商ID
	 * @returns ExtSkuChangesEntity
	 * @returns null
	 */
	public function loadAllBySupplierId (int $supplier_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_changes` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.ext_sku_changes 插入一条新纪录
	 * @param	ExtSkuChangesEntity    $obj    ..外部SKU变化通知
	 * @returns bool
	 */
	public function insert(ExtSkuChangesEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.ext_sku_changes 回写一条记录<br>
	 * 更新依据： yuemi_sale.ext_sku_changes.id
	 * @param	ExtSkuChangesEntity	  $obj    ..外部SKU变化通知
	 * @returns bool
	 */
	 public function update(ExtSkuChangesEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 外部SKU素材
 * @table ext_sku_material
 * @engine innodb
 */
final class ExtSkuMaterialEntity extends \Ziima\Data\Entity {
	/**
	 * 素材ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 外部SKUID
	 * @var int
	 */
	public $ext_sku_id = null;

	/**
	 * 图片类型：0商品图,1内容图
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 源图路径
	 * @var string
	 */
	public $source_url = null;

	/**
	 * 原图HASH值
	 * @var string
	 */
	public $source_hash = null;

	/**
	 * 文件格式：0JPG,1PNG
	 * @var int
	 * @default	0
	 */
	public $file_fmt = 0;

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
	 * 是否默认素材
	 * @var int
	 * @default	0
	 */
	public $is_default = 0;

	/**
	 * 内部排序
	 * @var int
	 * @default	0
	 */
	public $p_order = 0;

	/**
	 * 素材状态 0待下载,1下载失败,2下载成功
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
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $update_time = 0;

	/**
	 * 审核时间
	 * @var int
	 * @default	0
	 */
	public $audit_time = 0;
}
/**
 * ExtSkuMaterialEntity Factory<br>
 * 外部SKU素材
 */
final class ExtSkuMaterialFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ExtSkuMaterialFactory
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
	public static function Instance() : ExtSkuMaterialFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ExtSkuMaterialFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ExtSkuMaterialFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_sku_material`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_sku_material` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ExtSkuMaterialEntity {
		$obj = new ExtSkuMaterialEntity();$obj->id = $row['id'];
		$obj->ext_sku_id = $row['ext_sku_id'];
		$obj->type = $row['type'];
		$obj->source_url = $row['source_url'];
		$obj->source_hash = $row['source_hash'];
		$obj->file_fmt = $row['file_fmt'];
		$obj->file_name = $row['file_name'];
		$obj->file_size = $row['file_size'];
		$obj->file_url = $row['file_url'];
		$obj->image_width = $row['image_width'];
		$obj->image_height = $row['image_height'];
		$obj->thumb_url = $row['thumb_url'];
		$obj->thumb_size = $row['thumb_size'];
		$obj->thumb_width = $row['thumb_width'];
		$obj->thumb_height = $row['thumb_height'];
		$obj->is_default = $row['is_default'];
		$obj->p_order = $row['p_order'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->update_time = $row['update_time'];
		$obj->audit_time = $row['audit_time'];
		return $obj;
	}

	private function _object_to_insert(ExtSkuMaterialEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`ext_sku_material` %s(`id`,`ext_sku_id`,`type`,`source_url`,`source_hash`,`file_fmt`,`file_name`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`is_default`,`p_order`,`status`,`create_time`,`update_time`,`audit_time`) VALUES (NULL,%d,%d,'%s','%s',%d,'%s',%d,'%s',%d,%d,'%s',%d,%d,%d,%d,%d,%d,UNIX_TIMESTAMP(),%d,%d)";
		return sprintf($sql,'',$obj->ext_sku_id,$obj->type,self::_encode_string($obj->source_url,1024)
			,self::_encode_string($obj->source_hash,32)
			,$obj->file_fmt,self::_encode_string($obj->file_name,64)
			,$obj->file_size,self::_encode_string($obj->file_url,128)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,128)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->is_default,$obj->p_order,$obj->status,$obj->update_time,$obj->audit_time);
	}
	private function _object_to_update(ExtSkuMaterialEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`ext_sku_material` %s SET `ext_sku_id` = %d,`type` = %d,`source_url` = '%s',`source_hash` = '%s',`file_fmt` = %d,`file_name` = '%s',`file_size` = %d,`file_url` = '%s',`image_width` = %d,`image_height` = %d,`thumb_url` = '%s',`thumb_size` = %d,`thumb_width` = %d,`thumb_height` = %d,`is_default` = %d,`p_order` = %d,`status` = %d,`update_time` = %d,`audit_time` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->ext_sku_id,$obj->type,self::_encode_string($obj->source_url,1024)
			,self::_encode_string($obj->source_hash,32)
			,$obj->file_fmt,self::_encode_string($obj->file_name,64)
			,$obj->file_size,self::_encode_string($obj->file_url,128)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,128)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->is_default,$obj->p_order,$obj->status,$obj->update_time,$obj->audit_time,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ExtSkuMaterialEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`ext_sku_material`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..素材ID
	 * @returns ExtSkuMaterialEntity
	 * @returns null
	 */
	public function load(int $id) : ?ExtSkuMaterialEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_sale`.`ext_sku_material` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 source_hash 加载
	 * @param	string	$source_hash	..原图HASH值
	 * @returns ExtSkuMaterialEntity
	 * @returns null
	 */
	public function loadBySourceHash (string $source_hash) : ?ExtSkuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `source_hash` = '%s'",
			parent::$reader->escape_string($source_hash)
		));
		
	}
	
	/**
	 * 根据唯一索引 "source_hash" 删除一条
	 * @param	string	$source_hash	..原图HASH值
	 * @returns bool
	 */
	public function deleteBySourceHash(string $source_hash) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`ext_sku_material` WHERE `source_hash` = '%s'",
			parent::$reader->escape_string($source_hash)
		));
		
	}
	
	/**
	 * 根据普通索引 ext_sku_id 加载一条
	 * @param	int  $ext_sku_id  ..外部SKUID
	 * @returns ExtSkuMaterialEntity
	 * @returns null
	 */
	public function loadOneByExtSkuId (int $ext_sku_id) : ?ExtSkuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `ext_sku_id` = '%d'",
			$ext_sku_id
		));
		
	}
	/**
	 * 根据普通索引 ext_sku_id 加载全部
	 * @param	int	$ext_sku_id	..外部SKUID
	 * @returns ExtSkuMaterialEntity
	 * @returns null
	 */
	public function loadAllByExtSkuId (int $ext_sku_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `ext_sku_id` = '%d'",
			$ext_sku_id
		));
		
	}

	/**
	 * 根据普通索引 type 加载一条
	 * @param	int  $type  ..图片类型：0商品图,1内容图
	 * @returns ExtSkuMaterialEntity
	 * @returns null
	 */
	public function loadOneByType (int $type) : ?ExtSkuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `type` = '%d'",
			$type
		));
		
	}
	/**
	 * 根据普通索引 type 加载全部
	 * @param	int	$type	..图片类型：0商品图,1内容图
	 * @returns ExtSkuMaterialEntity
	 * @returns null
	 */
	public function loadAllByType (int $type) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `type` = '%d'",
			$type
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..素材状态 0待下载,1下载失败,2下载成功
	 * @returns ExtSkuMaterialEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?ExtSkuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..素材状态 0待下载,1下载失败,2下载成功
	 * @returns ExtSkuMaterialEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_sku_material` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.ext_sku_material 插入一条新纪录
	 * @param	ExtSkuMaterialEntity    $obj    ..外部SKU素材
	 * @returns bool
	 */
	public function insert(ExtSkuMaterialEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.ext_sku_material 回写一条记录<br>
	 * 更新依据： yuemi_sale.ext_sku_material.id
	 * @param	ExtSkuMaterialEntity	  $obj    ..外部SKU素材
	 * @returns bool
	 */
	 public function update(ExtSkuMaterialEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 外部SPU
 * @table ext_spu
 * @engine innodb
 */
final class ExtSpuEntity extends \Ziima\Data\Entity {
	/**
	 * 外部SPUID
	 * @var int
	 */
	public $id = null;

	/**
	 * 外部供应商ID，2=内购，3=贡云
	 * @var int
	 */
	public $supplier_id = null;

	/**
	 * 外部店铺标识
	 * @var string
	 */
	public $ext_shop_code = null;

	/**
	 * 商品bn
	 * @var string
	 */
	public $bn = null;

	/**
	 * 关联分类ID
	 * @var int
	 */
	public $ext_cat_id = null;

	/**
	 * 品牌ID
	 * @var int
	 * @default	0
	 */
	public $brand_id = 0;

	/**
	 * 内部SPUID
	 * @var int
	 * @default	0
	 */
	public $spu_id = 0;

	/**
	 * 内部分类ID
	 * @var int
	 * @default	0
	 */
	public $catagory_id = 0;

	/**
	 * 商品标题
	 * @var string
	 */
	public $title = null;

	/**
	 * 成本价
	 * @var float
	 */
	public $price_base = null;

	/**
	 * 描述内容
	 * @var string
	 */
	public $intro = null;

	/**
	 * 内容本地化状态：0待处理,1失败,2成功
	 * @var int
	 * @default	0
	 */
	public $lo_status = 0;

	/**
	 * 内容本地化错误次数
	 * @var int
	 * @default	0
	 */
	public $lo_error = 0;

	/**
	 * 内容本地化处理时间
	 * @var int
	 * @default	0
	 */
	public $lo_time = 0;

	/**
	 * 规格定义
	 * @var string
	 */
	public $specs = null;

	/**
	 * 外部SPU状态，0无效,1有效
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
}
/**
 * ExtSpuEntity Factory<br>
 * 外部SPU
 */
final class ExtSpuFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ExtSpuFactory
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
	public static function Instance() : ExtSpuFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ExtSpuFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ExtSpuFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_spu`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_spu` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ExtSpuEntity {
		$obj = new ExtSpuEntity();$obj->id = $row['id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->ext_shop_code = $row['ext_shop_code'];
		$obj->bn = $row['bn'];
		$obj->ext_cat_id = $row['ext_cat_id'];
		$obj->brand_id = $row['brand_id'];
		$obj->spu_id = $row['spu_id'];
		$obj->catagory_id = $row['catagory_id'];
		$obj->title = $row['title'];
		$obj->price_base = $row['price_base'];
		$obj->intro = $row['intro'];
		$obj->lo_status = $row['lo_status'];
		$obj->lo_error = $row['lo_error'];
		$obj->lo_time = $row['lo_time'];
		$obj->specs = $row['specs'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->update_time = $row['update_time'];
		return $obj;
	}

	private function _object_to_insert(ExtSpuEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`ext_spu` %s(`id`,`supplier_id`,`ext_shop_code`,`bn`,`ext_cat_id`,`brand_id`,`spu_id`,`catagory_id`,`title`,`price_base`,`intro`,`lo_status`,`lo_error`,`lo_time`,`specs`,`status`,`create_time`,`update_time`) VALUES (NULL,%d,'%s','%s',%d,%d,%d,%d,'%s',%f,'%s',%d,%d,%d,'%s',%d,%d,%d)";
		return sprintf($sql,'',$obj->supplier_id,self::_encode_string($obj->ext_shop_code,32)
			,self::_encode_string($obj->bn,24)
			,$obj->ext_cat_id,$obj->brand_id,$obj->spu_id,$obj->catagory_id,self::_encode_string($obj->title,128)
			,$obj->price_base,self::_encode_string($obj->intro,65535)
			,$obj->lo_status,$obj->lo_error,$obj->lo_time,self::_encode_string($obj->specs,65535)
			,$obj->status,$obj->create_time,$obj->update_time);
	}
	private function _object_to_update(ExtSpuEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`ext_spu` %s SET `supplier_id` = %d,`ext_shop_code` = '%s',`bn` = '%s',`ext_cat_id` = %d,`brand_id` = %d,`spu_id` = %d,`catagory_id` = %d,`title` = '%s',`price_base` = %f,`intro` = '%s',`lo_status` = %d,`lo_error` = %d,`lo_time` = %d,`specs` = '%s',`status` = %d,`create_time` = %d,`update_time` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->supplier_id,self::_encode_string($obj->ext_shop_code,32)
			,self::_encode_string($obj->bn,24)
			,$obj->ext_cat_id,$obj->brand_id,$obj->spu_id,$obj->catagory_id,self::_encode_string($obj->title,128)
			,$obj->price_base,self::_encode_string($obj->intro,65535)
			,$obj->lo_status,$obj->lo_error,$obj->lo_time,self::_encode_string($obj->specs,65535)
			,$obj->status,$obj->create_time,$obj->update_time,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ExtSpuEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`ext_spu`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`ext_spu` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..外部SPUID
	 * @returns ExtSpuEntity
	 * @returns null
	 */
	public function load(int $id) : ?ExtSpuEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..外部SPUID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`ext_spu` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 supplier_id 加载一条
	 * @param	int  $supplier_id  ..外部供应商ID，2=内购，3=贡云
	 * @returns ExtSpuEntity
	 * @returns null
	 */
	public function loadOneBySupplierId (int $supplier_id) : ?ExtSpuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	/**
	 * 根据普通索引 supplier_id 加载全部
	 * @param	int	$supplier_id	..外部供应商ID，2=内购，3=贡云
	 * @returns ExtSpuEntity
	 * @returns null
	 */
	public function loadAllBySupplierId (int $supplier_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}

	/**
	 * 根据普通索引 bn 加载一条
	 * @param	string  $bn  ..商品bn
	 * @returns ExtSpuEntity
	 * @returns null
	 */
	public function loadOneByBn (string $bn) : ?ExtSpuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu` WHERE `bn` = '%s'",
			parent::$reader->escape_string($bn)
		));
		
	}
	/**
	 * 根据普通索引 bn 加载全部
	 * @param	string	$bn	..商品bn
	 * @returns ExtSpuEntity
	 * @returns null
	 */
	public function loadAllByBn (string $bn) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu` WHERE `bn` = '%s'",
			parent::$reader->escape_string($bn)
		));
		
	}

	/**
	 * 根据普通索引 ext_cat_id 加载一条
	 * @param	int  $ext_cat_id  ..关联分类ID
	 * @returns ExtSpuEntity
	 * @returns null
	 */
	public function loadOneByExtCatId (int $ext_cat_id) : ?ExtSpuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu` WHERE `ext_cat_id` = '%d'",
			$ext_cat_id
		));
		
	}
	/**
	 * 根据普通索引 ext_cat_id 加载全部
	 * @param	int	$ext_cat_id	..关联分类ID
	 * @returns ExtSpuEntity
	 * @returns null
	 */
	public function loadAllByExtCatId (int $ext_cat_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu` WHERE `ext_cat_id` = '%d'",
			$ext_cat_id
		));
		
	}

	/**
	 * 根据普通索引 spu_id 加载一条
	 * @param	int  $spu_id  ..内部SPUID
	 * @returns ExtSpuEntity
	 * @returns null
	 */
	public function loadOneBySpuId (int $spu_id) : ?ExtSpuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu` WHERE `spu_id` = '%d'",
			$spu_id
		));
		
	}
	/**
	 * 根据普通索引 spu_id 加载全部
	 * @param	int	$spu_id	..内部SPUID
	 * @returns ExtSpuEntity
	 * @returns null
	 */
	public function loadAllBySpuId (int $spu_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu` WHERE `spu_id` = '%d'",
			$spu_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.ext_spu 插入一条新纪录
	 * @param	ExtSpuEntity    $obj    ..外部SPU
	 * @returns bool
	 */
	public function insert(ExtSpuEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.ext_spu 回写一条记录<br>
	 * 更新依据： yuemi_sale.ext_spu.id
	 * @param	ExtSpuEntity	  $obj    ..外部SPU
	 * @returns bool
	 */
	 public function update(ExtSpuEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 外部SPU素材
 * @table ext_spu_material
 * @engine innodb
 */
final class ExtSpuMaterialEntity extends \Ziima\Data\Entity {
	/**
	 * 素材ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 外部SPUID
	 * @var int
	 */
	public $ext_spu_id = null;

	/**
	 * 图片类型：0商品图,1内容图
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 源图路径
	 * @var string
	 */
	public $source_url = null;

	/**
	 * 原图HASH值
	 * @var string
	 */
	public $source_hash = null;

	/**
	 * 文件格式：0JPG,1PNG
	 * @var int
	 * @default	0
	 */
	public $file_fmt = 0;

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
	 * 是否默认素材
	 * @var int
	 * @default	0
	 */
	public $is_default = 0;

	/**
	 * 内部排序
	 * @var int
	 * @default	0
	 */
	public $p_order = 0;

	/**
	 * 素材状态 0待下载,1下载失败,2下载成功
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
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $update_time = 0;

	/**
	 * 审核时间
	 * @var int
	 * @default	0
	 */
	public $audit_time = 0;
}
/**
 * ExtSpuMaterialEntity Factory<br>
 * 外部SPU素材
 */
final class ExtSpuMaterialFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ExtSpuMaterialFactory
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
	public static function Instance() : ExtSpuMaterialFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ExtSpuMaterialFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ExtSpuMaterialFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_spu_material`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_spu_material` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ExtSpuMaterialEntity {
		$obj = new ExtSpuMaterialEntity();$obj->id = $row['id'];
		$obj->ext_spu_id = $row['ext_spu_id'];
		$obj->type = $row['type'];
		$obj->source_url = $row['source_url'];
		$obj->source_hash = $row['source_hash'];
		$obj->file_fmt = $row['file_fmt'];
		$obj->file_name = $row['file_name'];
		$obj->file_size = $row['file_size'];
		$obj->file_url = $row['file_url'];
		$obj->image_width = $row['image_width'];
		$obj->image_height = $row['image_height'];
		$obj->thumb_url = $row['thumb_url'];
		$obj->thumb_size = $row['thumb_size'];
		$obj->thumb_width = $row['thumb_width'];
		$obj->thumb_height = $row['thumb_height'];
		$obj->is_default = $row['is_default'];
		$obj->p_order = $row['p_order'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->update_time = $row['update_time'];
		$obj->audit_time = $row['audit_time'];
		return $obj;
	}

	private function _object_to_insert(ExtSpuMaterialEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`ext_spu_material` %s(`id`,`ext_spu_id`,`type`,`source_url`,`source_hash`,`file_fmt`,`file_name`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`is_default`,`p_order`,`status`,`create_time`,`update_time`,`audit_time`) VALUES (NULL,%d,%d,'%s','%s',%d,'%s',%d,'%s',%d,%d,'%s',%d,%d,%d,%d,%d,%d,UNIX_TIMESTAMP(),%d,%d)";
		return sprintf($sql,'',$obj->ext_spu_id,$obj->type,self::_encode_string($obj->source_url,1024)
			,self::_encode_string($obj->source_hash,32)
			,$obj->file_fmt,self::_encode_string($obj->file_name,64)
			,$obj->file_size,self::_encode_string($obj->file_url,128)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,128)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->is_default,$obj->p_order,$obj->status,$obj->update_time,$obj->audit_time);
	}
	private function _object_to_update(ExtSpuMaterialEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`ext_spu_material` %s SET `ext_spu_id` = %d,`type` = %d,`source_url` = '%s',`source_hash` = '%s',`file_fmt` = %d,`file_name` = '%s',`file_size` = %d,`file_url` = '%s',`image_width` = %d,`image_height` = %d,`thumb_url` = '%s',`thumb_size` = %d,`thumb_width` = %d,`thumb_height` = %d,`is_default` = %d,`p_order` = %d,`status` = %d,`update_time` = %d,`audit_time` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->ext_spu_id,$obj->type,self::_encode_string($obj->source_url,1024)
			,self::_encode_string($obj->source_hash,32)
			,$obj->file_fmt,self::_encode_string($obj->file_name,64)
			,$obj->file_size,self::_encode_string($obj->file_url,128)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,128)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->is_default,$obj->p_order,$obj->status,$obj->update_time,$obj->audit_time,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ExtSpuMaterialEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`ext_spu_material`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..素材ID
	 * @returns ExtSpuMaterialEntity
	 * @returns null
	 */
	public function load(int $id) : ?ExtSpuMaterialEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_sale`.`ext_spu_material` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 source_hash 加载
	 * @param	string	$source_hash	..原图HASH值
	 * @returns ExtSpuMaterialEntity
	 * @returns null
	 */
	public function loadBySourceHash (string $source_hash) : ?ExtSpuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `source_hash` = '%s'",
			parent::$reader->escape_string($source_hash)
		));
		
	}
	
	/**
	 * 根据唯一索引 "source_hash" 删除一条
	 * @param	string	$source_hash	..原图HASH值
	 * @returns bool
	 */
	public function deleteBySourceHash(string $source_hash) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`ext_spu_material` WHERE `source_hash` = '%s'",
			parent::$reader->escape_string($source_hash)
		));
		
	}
	
	/**
	 * 根据普通索引 ext_spu_id 加载一条
	 * @param	int  $ext_spu_id  ..外部SPUID
	 * @returns ExtSpuMaterialEntity
	 * @returns null
	 */
	public function loadOneByExtSpuId (int $ext_spu_id) : ?ExtSpuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `ext_spu_id` = '%d'",
			$ext_spu_id
		));
		
	}
	/**
	 * 根据普通索引 ext_spu_id 加载全部
	 * @param	int	$ext_spu_id	..外部SPUID
	 * @returns ExtSpuMaterialEntity
	 * @returns null
	 */
	public function loadAllByExtSpuId (int $ext_spu_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `ext_spu_id` = '%d'",
			$ext_spu_id
		));
		
	}

	/**
	 * 根据普通索引 type 加载一条
	 * @param	int  $type  ..图片类型：0商品图,1内容图
	 * @returns ExtSpuMaterialEntity
	 * @returns null
	 */
	public function loadOneByType (int $type) : ?ExtSpuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `type` = '%d'",
			$type
		));
		
	}
	/**
	 * 根据普通索引 type 加载全部
	 * @param	int	$type	..图片类型：0商品图,1内容图
	 * @returns ExtSpuMaterialEntity
	 * @returns null
	 */
	public function loadAllByType (int $type) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `type` = '%d'",
			$type
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..素材状态 0待下载,1下载失败,2下载成功
	 * @returns ExtSpuMaterialEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?ExtSpuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..素材状态 0待下载,1下载失败,2下载成功
	 * @returns ExtSpuMaterialEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_spu_material` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.ext_spu_material 插入一条新纪录
	 * @param	ExtSpuMaterialEntity    $obj    ..外部SPU素材
	 * @returns bool
	 */
	public function insert(ExtSpuMaterialEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.ext_spu_material 回写一条记录<br>
	 * 更新依据： yuemi_sale.ext_spu_material.id
	 * @param	ExtSpuMaterialEntity	  $obj    ..外部SPU素材
	 * @returns bool
	 */
	 public function update(ExtSpuMaterialEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 外部供应商
 * @table ext_supplier
 * @engine innodb
 */
final class ExtSupplierEntity extends \Ziima\Data\Entity {
	/**
	 * 供应商ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 外部供应商ID，2=内购，3=贡云
	 * @var int
	 */
	public $supplier_id = null;

	/**
	 * 供应商名称
	 * @var string
	 */
	public $name = null;

	/**
	 * 映射阅米内部供应商ID
	 * @var int
	 * @default	0
	 */
	public $map_id = 0;
}
/**
 * ExtSupplierEntity Factory<br>
 * 外部供应商
 */
final class ExtSupplierFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ExtSupplierFactory
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
	public static function Instance() : ExtSupplierFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ExtSupplierFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ExtSupplierFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_supplier`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`ext_supplier` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ExtSupplierEntity {
		$obj = new ExtSupplierEntity();$obj->id = $row['id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->name = $row['name'];
		$obj->map_id = $row['map_id'];
		return $obj;
	}

	private function _object_to_insert(ExtSupplierEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`ext_supplier` %s(`id`,`supplier_id`,`name`,`map_id`) VALUES (NULL,%d,'%s',%d)";
		return sprintf($sql,'',$obj->supplier_id,self::_encode_string($obj->name,32)
			,$obj->map_id);
	}
	private function _object_to_update(ExtSupplierEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`ext_supplier` %s SET `supplier_id` = %d,`name` = '%s',`map_id` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->supplier_id,self::_encode_string($obj->name,32)
			,$obj->map_id,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ExtSupplierEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`ext_supplier`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`ext_supplier` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..供应商ID
	 * @returns ExtSupplierEntity
	 * @returns null
	 */
	public function load(int $id) : ?ExtSupplierEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_supplier` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..供应商ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`ext_supplier` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 supplier_id 加载一条
	 * @param	int  $supplier_id  ..外部供应商ID，2=内购，3=贡云
	 * @returns ExtSupplierEntity
	 * @returns null
	 */
	public function loadOneBySupplierId (int $supplier_id) : ?ExtSupplierEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_supplier` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	/**
	 * 根据普通索引 supplier_id 加载全部
	 * @param	int	$supplier_id	..外部供应商ID，2=内购，3=贡云
	 * @returns ExtSupplierEntity
	 * @returns null
	 */
	public function loadAllBySupplierId (int $supplier_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_supplier` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}

	/**
	 * 根据普通索引 map_id 加载一条
	 * @param	int  $map_id  ..映射阅米内部供应商ID
	 * @returns ExtSupplierEntity
	 * @returns null
	 */
	public function loadOneByMapId (int $map_id) : ?ExtSupplierEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_supplier` WHERE `map_id` = '%d'",
			$map_id
		));
		
	}
	/**
	 * 根据普通索引 map_id 加载全部
	 * @param	int	$map_id	..映射阅米内部供应商ID
	 * @returns ExtSupplierEntity
	 * @returns null
	 */
	public function loadAllByMapId (int $map_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`ext_supplier` WHERE `map_id` = '%d'",
			$map_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.ext_supplier 插入一条新纪录
	 * @param	ExtSupplierEntity    $obj    ..外部供应商
	 * @returns bool
	 */
	public function insert(ExtSupplierEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.ext_supplier 回写一条记录<br>
	 * 更新依据： yuemi_sale.ext_supplier.id
	 * @param	ExtSupplierEntity	  $obj    ..外部供应商
	 * @returns bool
	 */
	 public function update(ExtSupplierEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 订单
 * @table order
 * @engine innodb
 */
final class OrderEntity extends \Ziima\Data\Entity {
	/**
	 * 订单ID
	 * @var string
	 */
	public $id = null;

	/**
	 * 订单类型：0普通订单，1大礼包订单，2系统补偿订单，3后台代下单
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 是主订单
	 * @var int
	 * @default	0
	 */
	public $is_primary = 0;

	/**
	 * 主订单ID
	 * @var string
	 */
	public $depend_id = null;

	/**
	 * 拆分供应商ID
	 * @var int
	 * @default	0
	 */
	public $supplier_id = 0;

	/**
	 * 子店铺代码
	 * @var string
	 */
	public $shop_code = null;

	/**
	 * 货品总数量(包括子订单)
	 * @var int
	 */
	public $qty = null;

	/**
	 * 当前订单群总价格
	 * @var float
	 * @default	0.0000
	 */
	public $t_amount = 0.0000;

	/**
	 * 当前订单群总余额支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $t_money = 0.0000;

	/**
	 * 当前订单群总佣金支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $t_profit = 0.0000;

	/**
	 * 当前订单群总招聘支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $t_recruit = 0.0000;

	/**
	 * 当前订单群总优惠券支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $t_ticket = 0.0000;

	/**
	 * 当前订单群总卡片支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $t_card = 0.0000;

	/**
	 * 当前订单群总在线支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $t_online = 0.0000;

	/**
	 * 运费
	 * @var float
	 * @default	0.0000
	 */
	public $t_trans = 0.0000;

	/**
	 * 当前订单在线支付方式：1微信,2支付宝,3银联,4保留,5保留,6保留,7线下支付
	 * @var int
	 * @default	0
	 */
	public $t_chanel = 0;

	/**
	 * 当前订单在线支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $c_online = 0.0000;

	/**
	 * 当前订单优惠券支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $c_ticket = 0.0000;

	/**
	 * 当前订单卡片支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $c_card = 0.0000;

	/**
	 * 当前订单招聘支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $c_recruit = 0.0000;

	/**
	 * 当前订单佣金支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $c_profit = 0.0000;

	/**
	 * 当前订单余额支付部分
	 * @var float
	 * @default	0.0000
	 */
	public $c_money = 0.0000;

	/**
	 * 当前订单价格
	 * @var float
	 * @default	0.0000
	 */
	public $c_amount = 0.0000;

	/**
	 * 当前订单支付卡序列号
	 * @var string
	 */
	public $pay_card = null;

	/**
	 * 支付回单号
	 * @var string
	 */
	public $pay_serial = null;

	/**
	 * 支付时间
	 * @var int
	 * @default	0
	 */
	public $pay_time = 0;

	/**
	 * 订单收货地址ID
	 * @var int
	 * @default	0
	 */
	public $address_id = 0;

	/**
	 * 地区ID
	 * @var int
	 * @default	0
	 */
	public $addr_region = 0;

	/**
	 * 详细地址
	 * @var string
	 */
	public $addr_detail = null;

	/**
	 * 联系人
	 * @var string
	 */
	public $addr_name = null;

	/**
	 * 联系电话
	 * @var string
	 */
	public $addr_mobile = null;

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
	 * 外部订单ID
	 * @var string
	 */
	public $ext_order_id = null;

	/**
	 * 外部订单状态：0未创建,1失败,2成功,3...参见文档
	 * @var int
	 * @default	0
	 */
	public $ext_status = 0;

	/**
	 * 物流公司代码
	 * @var string
	 */
	public $trans_com = null;

	/**
	 * 物流单号
	 * @var string
	 */
	public $trans_id = null;

	/**
	 * 物流详情
	 * @var string
	 */
	public $trans_trace = null;

	/**
	 * 用户备注
	 * @var string
	 */
	public $comment_user = null;

	/**
	 * 管理员备注
	 * @var string
	 */
	public $comment_admin = null;

	/**
	 * 是否已确认
	 * @var int
	 * @default	0
	 */
	public $trans_fin = 0;

	/**
	 * 确认收货时间
	 * @var int
	 * @default	0
	 */
	public $trans_time = 0;

	/**
	 * 使用的优惠券Id
	 * @var string
	 */
	public $discount_coupon_id = null;

	/**
	 * 订单状态：参见文档
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 订单更新时间
	 * @var int
	 * @default	0
	 */
	public $update_time = 0;
}
/**
 * OrderEntity Factory<br>
 * 订单
 */
final class OrderFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var OrderFactory
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
	public static function Instance() : OrderFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new OrderFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new OrderFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`order`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`order` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : OrderEntity {
		$obj = new OrderEntity();$obj->id = $row['id'];
		$obj->type = $row['type'];
		$obj->user_id = $row['user_id'];
		$obj->is_primary = $row['is_primary'];
		$obj->depend_id = $row['depend_id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->shop_code = $row['shop_code'];
		$obj->qty = $row['qty'];
		$obj->t_amount = $row['t_amount'];
		$obj->t_money = $row['t_money'];
		$obj->t_profit = $row['t_profit'];
		$obj->t_recruit = $row['t_recruit'];
		$obj->t_ticket = $row['t_ticket'];
		$obj->t_card = $row['t_card'];
		$obj->t_online = $row['t_online'];
		$obj->t_trans = $row['t_trans'];
		$obj->t_chanel = $row['t_chanel'];
		$obj->c_online = $row['c_online'];
		$obj->c_ticket = $row['c_ticket'];
		$obj->c_card = $row['c_card'];
		$obj->c_recruit = $row['c_recruit'];
		$obj->c_profit = $row['c_profit'];
		$obj->c_money = $row['c_money'];
		$obj->c_amount = $row['c_amount'];
		$obj->pay_card = $row['pay_card'];
		$obj->pay_serial = $row['pay_serial'];
		$obj->pay_time = $row['pay_time'];
		$obj->address_id = $row['address_id'];
		$obj->addr_region = $row['addr_region'];
		$obj->addr_detail = $row['addr_detail'];
		$obj->addr_name = $row['addr_name'];
		$obj->addr_mobile = $row['addr_mobile'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->ext_order_id = $row['ext_order_id'];
		$obj->ext_status = $row['ext_status'];
		$obj->trans_com = $row['trans_com'];
		$obj->trans_id = $row['trans_id'];
		$obj->trans_trace = $row['trans_trace'];
		$obj->comment_user = $row['comment_user'];
		$obj->comment_admin = $row['comment_admin'];
		$obj->trans_fin = $row['trans_fin'];
		$obj->trans_time = $row['trans_time'];
		$obj->discount_coupon_id = $row['discount_coupon_id'];
		$obj->status = $row['status'];
		$obj->update_time = $row['update_time'];
		return $obj;
	}

	private function _object_to_insert(OrderEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`order` %s(`id`,`type`,`user_id`,`is_primary`,`depend_id`,`supplier_id`,`shop_code`,`qty`,`t_amount`,`t_money`,`t_profit`,`t_recruit`,`t_ticket`,`t_card`,`t_online`,`t_trans`,`t_chanel`,`c_online`,`c_ticket`,`c_card`,`c_recruit`,`c_profit`,`c_money`,`c_amount`,`pay_card`,`pay_serial`,`pay_time`,`address_id`,`addr_region`,`addr_detail`,`addr_name`,`addr_mobile`,`create_time`,`create_from`,`ext_order_id`,`ext_status`,`trans_com`,`trans_id`,`trans_trace`,`comment_user`,`comment_admin`,`trans_fin`,`trans_time`,`discount_coupon_id`,`status`,`update_time`) VALUES ('%s',%d,%d,%d,'%s',%d,'%s',%d,%f,%f,%f,%f,%f,%f,%f,%f,%d,%f,%f,%f,%f,%f,%f,%f,'%s','%s',%d,%d,%d,'%s','%s','%s',%d,%d,'%s',%d,'%s','%s','%s','%s','%s',%d,%d,'%s',%d,%d)";
		return sprintf($sql,'',self::_encode_string($obj->id,16)
			,$obj->type,$obj->user_id,$obj->is_primary,self::_encode_string($obj->depend_id,16)
			,$obj->supplier_id,self::_encode_string($obj->shop_code,32)
			,$obj->qty,$obj->t_amount,$obj->t_money,$obj->t_profit,$obj->t_recruit,$obj->t_ticket,$obj->t_card,$obj->t_online,$obj->t_trans,$obj->t_chanel,$obj->c_online,$obj->c_ticket,$obj->c_card,$obj->c_recruit,$obj->c_profit,$obj->c_money,$obj->c_amount,self::_encode_string($obj->pay_card,32)
			,self::_encode_string($obj->pay_serial,32)
			,$obj->pay_time,$obj->address_id,$obj->addr_region,self::_encode_string($obj->addr_detail,256)
			,self::_encode_string($obj->addr_name,16)
			,self::_encode_string($obj->addr_mobile,16)
			,$obj->create_time,$obj->create_from,self::_encode_string($obj->ext_order_id,32)
			,$obj->ext_status,self::_encode_string($obj->trans_com,32)
			,self::_encode_string($obj->trans_id,16)
			,self::_encode_string($obj->trans_trace,65535)
			,self::_encode_string($obj->comment_user,64)
			,self::_encode_string($obj->comment_admin,64)
			,$obj->trans_fin,$obj->trans_time,self::_encode_string($obj->discount_coupon_id,32)
			,$obj->status,$obj->update_time);
	}
	private function _object_to_update(OrderEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`order` %s SET `type` = %d,`user_id` = %d,`is_primary` = %d,`depend_id` = '%s',`supplier_id` = %d,`shop_code` = '%s',`qty` = %d,`t_amount` = %f,`t_money` = %f,`t_profit` = %f,`t_recruit` = %f,`t_ticket` = %f,`t_card` = %f,`t_online` = %f,`t_trans` = %f,`t_chanel` = %d,`c_online` = %f,`c_ticket` = %f,`c_card` = %f,`c_recruit` = %f,`c_profit` = %f,`c_money` = %f,`c_amount` = %f,`pay_card` = '%s',`pay_serial` = '%s',`pay_time` = %d,`address_id` = %d,`addr_region` = %d,`addr_detail` = '%s',`addr_name` = '%s',`addr_mobile` = '%s',`create_time` = %d,`create_from` = %d,`ext_order_id` = '%s',`ext_status` = %d,`trans_com` = '%s',`trans_id` = '%s',`trans_trace` = '%s',`comment_user` = '%s',`comment_admin` = '%s',`trans_fin` = %d,`trans_time` = %d,`discount_coupon_id` = '%s',`status` = %d,`update_time` = %d WHERE `id` = '%s'";
		
		return sprintf($sql,'',$obj->type,$obj->user_id,$obj->is_primary,self::_encode_string($obj->depend_id,16)
			,$obj->supplier_id,self::_encode_string($obj->shop_code,32)
			,$obj->qty,$obj->t_amount,$obj->t_money,$obj->t_profit,$obj->t_recruit,$obj->t_ticket,$obj->t_card,$obj->t_online,$obj->t_trans,$obj->t_chanel,$obj->c_online,$obj->c_ticket,$obj->c_card,$obj->c_recruit,$obj->c_profit,$obj->c_money,$obj->c_amount,self::_encode_string($obj->pay_card,32)
			,self::_encode_string($obj->pay_serial,32)
			,$obj->pay_time,$obj->address_id,$obj->addr_region,self::_encode_string($obj->addr_detail,256)
			,self::_encode_string($obj->addr_name,16)
			,self::_encode_string($obj->addr_mobile,16)
			,$obj->create_time,$obj->create_from,self::_encode_string($obj->ext_order_id,32)
			,$obj->ext_status,self::_encode_string($obj->trans_com,32)
			,self::_encode_string($obj->trans_id,16)
			,self::_encode_string($obj->trans_trace,65535)
			,self::_encode_string($obj->comment_user,64)
			,self::_encode_string($obj->comment_admin,64)
			,$obj->trans_fin,$obj->trans_time,self::_encode_string($obj->discount_coupon_id,32)
			,$obj->status,$obj->update_time,parent::$reader->escape_string($obj->id)
			);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns OrderEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`order`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`order` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "id" 加载一条
	 * @param	string	$id	..订单ID
	 * @returns OrderEntity
	 * @returns null
	 */
	public function load(string $id) : ?OrderEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order` WHERE `id` = '%s'",
			parent::$reader->escape_string($id)
		));
		
	}
	
	/**
	 * 根据主键 "id" 删除一条
	 * @param	string	$id	..订单ID
	 * @returns bool
	 */
	public function delete(string $id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`order` WHERE `id` = '%s'",
			parent::$reader->escape_string($id)
		));
		
	}
	
	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns OrderEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?OrderEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns OrderEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 depend_id 加载一条
	 * @param	string  $depend_id  ..主订单ID
	 * @returns OrderEntity
	 * @returns null
	 */
	public function loadOneByDependId (string $depend_id) : ?OrderEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order` WHERE `depend_id` = '%s'",
			parent::$reader->escape_string($depend_id)
		));
		
	}
	/**
	 * 根据普通索引 depend_id 加载全部
	 * @param	string	$depend_id	..主订单ID
	 * @returns OrderEntity
	 * @returns null
	 */
	public function loadAllByDependId (string $depend_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order` WHERE `depend_id` = '%s'",
			parent::$reader->escape_string($depend_id)
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..订单状态：参见文档
	 * @returns OrderEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?OrderEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..订单状态：参见文档
	 * @returns OrderEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.order 插入一条新纪录
	 * @param	OrderEntity    $obj    ..订单
	 * @returns bool
	 */
	public function insert(OrderEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_sale.order 回写一条记录<br>
	 * 更新依据： yuemi_sale.order.id
	 * @param	OrderEntity	  $obj    ..订单
	 * @returns bool
	 */
	 public function update(OrderEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 订单售后
 * @table order_afs
 * @engine innodb
 */
final class OrderAfsEntity extends \Ziima\Data\Entity {
	/**
	 * 售后ID
	 * @var string
	 */
	public $id = null;

	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 订单详情ID
	 * @var int
	 */
	public $item_id = null;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * SKUID
	 * @var int
	 * @default	0
	 */
	public $sku_id = 0;

	/**
	 * SKUID
	 * @var int
	 * @default	0
	 */
	public $spu_id = 0;

	/**
	 * 供应商ID
	 * @var int
	 * @default	0
	 */
	public $supplier_id = 0;

	/**
	 * 退货数量
	 * @var int
	 * @default	0
	 */
	public $qty = 0;

	/**
	 * 退货价格
	 * @var float
	 * @default	0.0000
	 */
	public $price = 0.0000;

	/**
	 * 退货总价
	 * @var float
	 * @default	0.0000
	 */
	public $total = 0.0000;

	/**
	 * 商品标题
	 * @var string
	 */
	public $title = null;

	/**
	 * 商品小图
	 * @var string
	 */
	public $picture = null;

	/**
	 * 申请方式：1退货退款,2补发,3部分退款,4全额退款
	 * @var int
	 * @default	0
	 */
	public $req_type = 0;

	/**
	 * 申请理由：参见文档
	 * @var int
	 * @default	0
	 */
	public $req_reason = 0;

	/**
	 * 申请退款金额
	 * @var float
	 * @default	0.0000
	 */
	public $req_money = 0.0000;

	/**
	 * 申请消息
	 * @var string
	 */
	public $req_message = null;

	/**
	 * 物流公司代码
	 * @var string
	 */
	public $req_trans_com = null;

	/**
	 * 退货物流单号
	 * @var string
	 */
	public $req_trans = null;

	/**
	 * 订单收货地址ID
	 * @var int
	 * @default	0
	 */
	public $req_addr_id = 0;

	/**
	 * 物流详情
	 * @var string
	 */
	public $req_trans_trace = null;

	/**
	 * 地区ID
	 * @var int
	 * @default	0
	 */
	public $req_addr_rgn = 0;

	/**
	 * 详细地址
	 * @var string
	 */
	public $req_addr = null;

	/**
	 * 联系人
	 * @var string
	 */
	public $req_name = null;

	/**
	 * 联系电话
	 * @var string
	 */
	public $req_mobile = null;

	/**
	 * 补发理由：参见文档
	 * @var int
	 * @default	0
	 */
	public $fix_id = 0;

	/**
	 * 补发消息
	 * @var string
	 */
	public $fix_message = null;

	/**
	 * 补发物流单号
	 * @var string
	 */
	public $fix_trans = null;

	/**
	 * 实际退款金额
	 * @var float
	 * @default	0.0000
	 */
	public $bak_money = 0.0000;

	/**
	 * 退款流水记录号
	 * @var int
	 * @default	0
	 */
	public $bak_tally = 0;

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
	 * 外部订单ID
	 * @var string
	 */
	public $ext_order_id = null;

	/**
	 * 外部订单状态：0未创建,1失败,2成功,3...参见文档
	 * @var int
	 * @default	0
	 */
	public $ext_status = 0;

	/**
	 * 售后订单状态：0申请,1拒绝,2通过,3完成
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 订单更新时间
	 * @var int
	 * @default	0
	 */
	public $update_time = 0;
}
/**
 * OrderAfsEntity Factory<br>
 * 订单售后
 */
final class OrderAfsFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var OrderAfsFactory
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
	public static function Instance() : OrderAfsFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new OrderAfsFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new OrderAfsFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`order_afs`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`order_afs` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : OrderAfsEntity {
		$obj = new OrderAfsEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->item_id = $row['item_id'];
		$obj->order_id = $row['order_id'];
		$obj->sku_id = $row['sku_id'];
		$obj->spu_id = $row['spu_id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->qty = $row['qty'];
		$obj->price = $row['price'];
		$obj->total = $row['total'];
		$obj->title = $row['title'];
		$obj->picture = $row['picture'];
		$obj->req_type = $row['req_type'];
		$obj->req_reason = $row['req_reason'];
		$obj->req_money = $row['req_money'];
		$obj->req_message = $row['req_message'];
		$obj->req_trans_com = $row['req_trans_com'];
		$obj->req_trans = $row['req_trans'];
		$obj->req_addr_id = $row['req_addr_id'];
		$obj->req_trans_trace = $row['req_trans_trace'];
		$obj->req_addr_rgn = $row['req_addr_rgn'];
		$obj->req_addr = $row['req_addr'];
		$obj->req_name = $row['req_name'];
		$obj->req_mobile = $row['req_mobile'];
		$obj->fix_id = $row['fix_id'];
		$obj->fix_message = $row['fix_message'];
		$obj->fix_trans = $row['fix_trans'];
		$obj->bak_money = $row['bak_money'];
		$obj->bak_tally = $row['bak_tally'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->ext_order_id = $row['ext_order_id'];
		$obj->ext_status = $row['ext_status'];
		$obj->status = $row['status'];
		$obj->update_time = $row['update_time'];
		return $obj;
	}

	private function _object_to_insert(OrderAfsEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`order_afs` %s(`id`,`user_id`,`item_id`,`order_id`,`sku_id`,`spu_id`,`supplier_id`,`qty`,`price`,`total`,`title`,`picture`,`req_type`,`req_reason`,`req_money`,`req_message`,`req_trans_com`,`req_trans`,`req_addr_id`,`req_trans_trace`,`req_addr_rgn`,`req_addr`,`req_name`,`req_mobile`,`fix_id`,`fix_message`,`fix_trans`,`bak_money`,`bak_tally`,`create_time`,`create_from`,`ext_order_id`,`ext_status`,`status`,`update_time`) VALUES ('%s',%d,%d,'%s',%d,%d,%d,%d,%f,%f,'%s','%s',%d,%d,%f,'%s','%s','%s',%d,'%s',%d,'%s','%s','%s',%d,'%s','%s',%f,%d,%d,%d,'%s',%d,%d,%d)";
		return sprintf($sql,'',self::_encode_string($obj->id,12)
			,$obj->user_id,$obj->item_id,self::_encode_string($obj->order_id,16)
			,$obj->sku_id,$obj->spu_id,$obj->supplier_id,$obj->qty,$obj->price,$obj->total,self::_encode_string($obj->title,128)
			,self::_encode_string($obj->picture,256)
			,$obj->req_type,$obj->req_reason,$obj->req_money,self::_encode_string($obj->req_message,512)
			,self::_encode_string($obj->req_trans_com,32)
			,self::_encode_string($obj->req_trans,16)
			,$obj->req_addr_id,self::_encode_string($obj->req_trans_trace,65535)
			,$obj->req_addr_rgn,self::_encode_string($obj->req_addr,256)
			,self::_encode_string($obj->req_name,16)
			,self::_encode_string($obj->req_mobile,16)
			,$obj->fix_id,self::_encode_string($obj->fix_message,512)
			,self::_encode_string($obj->fix_trans,16)
			,$obj->bak_money,$obj->bak_tally,$obj->create_time,$obj->create_from,self::_encode_string($obj->ext_order_id,32)
			,$obj->ext_status,$obj->status,$obj->update_time);
	}
	private function _object_to_update(OrderAfsEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`order_afs` %s SET `user_id` = %d,`item_id` = %d,`order_id` = '%s',`sku_id` = %d,`spu_id` = %d,`supplier_id` = %d,`qty` = %d,`price` = %f,`total` = %f,`title` = '%s',`picture` = '%s',`req_type` = %d,`req_reason` = %d,`req_money` = %f,`req_message` = '%s',`req_trans_com` = '%s',`req_trans` = '%s',`req_addr_id` = %d,`req_trans_trace` = '%s',`req_addr_rgn` = %d,`req_addr` = '%s',`req_name` = '%s',`req_mobile` = '%s',`fix_id` = %d,`fix_message` = '%s',`fix_trans` = '%s',`bak_money` = %f,`bak_tally` = %d,`create_time` = %d,`create_from` = %d,`ext_order_id` = '%s',`ext_status` = %d,`status` = %d,`update_time` = %d WHERE `id` = '%s'";
		
		return sprintf($sql,'',$obj->user_id,$obj->item_id,self::_encode_string($obj->order_id,16)
			,$obj->sku_id,$obj->spu_id,$obj->supplier_id,$obj->qty,$obj->price,$obj->total,self::_encode_string($obj->title,128)
			,self::_encode_string($obj->picture,256)
			,$obj->req_type,$obj->req_reason,$obj->req_money,self::_encode_string($obj->req_message,512)
			,self::_encode_string($obj->req_trans_com,32)
			,self::_encode_string($obj->req_trans,16)
			,$obj->req_addr_id,self::_encode_string($obj->req_trans_trace,65535)
			,$obj->req_addr_rgn,self::_encode_string($obj->req_addr,256)
			,self::_encode_string($obj->req_name,16)
			,self::_encode_string($obj->req_mobile,16)
			,$obj->fix_id,self::_encode_string($obj->fix_message,512)
			,self::_encode_string($obj->fix_trans,16)
			,$obj->bak_money,$obj->bak_tally,$obj->create_time,$obj->create_from,self::_encode_string($obj->ext_order_id,32)
			,$obj->ext_status,$obj->status,$obj->update_time,parent::$reader->escape_string($obj->id)
			);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns OrderAfsEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`order_afs`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`order_afs` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "id" 加载一条
	 * @param	string	$id	..售后ID
	 * @returns OrderAfsEntity
	 * @returns null
	 */
	public function load(string $id) : ?OrderAfsEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_afs` WHERE `id` = '%s'",
			parent::$reader->escape_string($id)
		));
		
	}
	
	/**
	 * 根据主键 "id" 删除一条
	 * @param	string	$id	..售后ID
	 * @returns bool
	 */
	public function delete(string $id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`order_afs` WHERE `id` = '%s'",
			parent::$reader->escape_string($id)
		));
		
	}
	
	/**
	 * 根据唯一索引 item_id 加载
	 * @param	int	$item_id	..订单详情ID
	 * @returns OrderAfsEntity
	 * @returns null
	 */
	public function loadByItemId (int $item_id) : ?OrderAfsEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_afs` WHERE `item_id` = '%d'",
			$item_id
		));
		
	}
	
	/**
	 * 根据唯一索引 "item_id" 删除一条
	 * @param	int	$item_id	..订单详情ID
	 * @returns bool
	 */
	public function deleteByItemId(int $item_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`order_afs` WHERE `item_id` = '%d'",
			$item_id
		));
		
	}
	
	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns OrderAfsEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?OrderAfsEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_afs` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns OrderAfsEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_afs` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 order_id 加载一条
	 * @param	string  $order_id  ..订单ID
	 * @returns OrderAfsEntity
	 * @returns null
	 */
	public function loadOneByOrderId (string $order_id) : ?OrderAfsEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_afs` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}
	/**
	 * 根据普通索引 order_id 加载全部
	 * @param	string	$order_id	..订单ID
	 * @returns OrderAfsEntity
	 * @returns null
	 */
	public function loadAllByOrderId (string $order_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_afs` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..售后订单状态：0申请,1拒绝,2通过,3完成
	 * @returns OrderAfsEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?OrderAfsEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_afs` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..售后订单状态：0申请,1拒绝,2通过,3完成
	 * @returns OrderAfsEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_afs` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.order_afs 插入一条新纪录
	 * @param	OrderAfsEntity    $obj    ..订单售后
	 * @returns bool
	 */
	public function insert(OrderAfsEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_sale.order_afs 回写一条记录<br>
	 * 更新依据： yuemi_sale.order_afs.id
	 * @param	OrderAfsEntity	  $obj    ..订单售后
	 * @returns bool
	 */
	 public function update(OrderAfsEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 订单详情
 * @table order_item
 * @engine innodb
 */
final class OrderItemEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 来源分享ID
	 * @var int
	 * @default	0
	 */
	public $share_id = 0;

	/**
	 * 分享用户ID
	 * @var int
	 * @default	0
	 */
	public $share_user_id = 0;

	/**
	 * 分享者是否VIP
	 * @var int
	 * @default	0
	 */
	public $share_user_vip = 0;

	/**
	 * 分享者归属总经理
	 * @var int
	 * @default	0
	 */
	public $share_director_id = 0;

	/**
	 * 分享者归属总监
	 * @var int
	 * @default	0
	 */
	public $share_cheif_id = 0;

	/**
	 * SKUID
	 * @var int
	 * @default	0
	 */
	public $sku_id = 0;

	/**
	 * SKUID
	 * @var int
	 * @default	0
	 */
	public $spu_id = 0;

	/**
	 * 分类ID
	 * @var int
	 * @default	0
	 */
	public $catagory_id = 0;

	/**
	 * 供应商ID
	 * @var int
	 * @default	0
	 */
	public $supplier_id = 0;

	/**
	 * 店铺ID
	 * @var int
	 * @default	0
	 */
	public $shop_id = 0;

	/**
	 * 货品总数量
	 * @var int
	 */
	public $qty = null;

	/**
	 * 结算单价
	 * @var float
	 */
	public $price = null;

	/**
	 * 结算总价
	 * @var float
	 */
	public $money = null;

	/**
	 * 下单时的基准价
	 * @var float
	 * @default	0.0000
	 */
	public $price_base = 0.0000;

	/**
	 * 本单返佣目标用户
	 * @var int
	 * @default	0
	 */
	public $rebate_user = 0;

	/**
	 * 本单返佣总金额
	 * @var float
	 * @default	0.0000
	 */
	public $rebate_vip = 0.0000;

	/**
	 * 商品标题
	 * @var string
	 */
	public $title = null;

	/**
	 * 商品小图
	 * @var string
	 */
	public $picture = null;
}
/**
 * OrderItemEntity Factory<br>
 * 订单详情
 */
final class OrderItemFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var OrderItemFactory
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
	public static function Instance() : OrderItemFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new OrderItemFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new OrderItemFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`order_item`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`order_item` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : OrderItemEntity {
		$obj = new OrderItemEntity();$obj->id = $row['id'];
		$obj->order_id = $row['order_id'];
		$obj->share_id = $row['share_id'];
		$obj->share_user_id = $row['share_user_id'];
		$obj->share_user_vip = $row['share_user_vip'];
		$obj->share_director_id = $row['share_director_id'];
		$obj->share_cheif_id = $row['share_cheif_id'];
		$obj->sku_id = $row['sku_id'];
		$obj->spu_id = $row['spu_id'];
		$obj->catagory_id = $row['catagory_id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->shop_id = $row['shop_id'];
		$obj->qty = $row['qty'];
		$obj->price = $row['price'];
		$obj->money = $row['money'];
		$obj->price_base = $row['price_base'];
		$obj->rebate_user = $row['rebate_user'];
		$obj->rebate_vip = $row['rebate_vip'];
		$obj->title = $row['title'];
		$obj->picture = $row['picture'];
		return $obj;
	}

	private function _object_to_insert(OrderItemEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`order_item` %s(`id`,`order_id`,`share_id`,`share_user_id`,`share_user_vip`,`share_director_id`,`share_cheif_id`,`sku_id`,`spu_id`,`catagory_id`,`supplier_id`,`shop_id`,`qty`,`price`,`money`,`price_base`,`rebate_user`,`rebate_vip`,`title`,`picture`) VALUES (NULL,'%s',%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%f,%f,%f,%d,%f,'%s','%s')";
		return sprintf($sql,'',self::_encode_string($obj->order_id,16)
			,$obj->share_id,$obj->share_user_id,$obj->share_user_vip,$obj->share_director_id,$obj->share_cheif_id,$obj->sku_id,$obj->spu_id,$obj->catagory_id,$obj->supplier_id,$obj->shop_id,$obj->qty,$obj->price,$obj->money,$obj->price_base,$obj->rebate_user,$obj->rebate_vip,self::_encode_string($obj->title,128)
			,self::_encode_string($obj->picture,256)
			);
	}
	private function _object_to_update(OrderItemEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`order_item` %s SET `order_id` = '%s',`share_id` = %d,`share_user_id` = %d,`share_user_vip` = %d,`share_director_id` = %d,`share_cheif_id` = %d,`sku_id` = %d,`spu_id` = %d,`catagory_id` = %d,`supplier_id` = %d,`shop_id` = %d,`qty` = %d,`price` = %f,`money` = %f,`price_base` = %f,`rebate_user` = %d,`rebate_vip` = %f,`title` = '%s',`picture` = '%s' WHERE `id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->order_id,16)
			,$obj->share_id,$obj->share_user_id,$obj->share_user_vip,$obj->share_director_id,$obj->share_cheif_id,$obj->sku_id,$obj->spu_id,$obj->catagory_id,$obj->supplier_id,$obj->shop_id,$obj->qty,$obj->price,$obj->money,$obj->price_base,$obj->rebate_user,$obj->rebate_vip,self::_encode_string($obj->title,128)
			,self::_encode_string($obj->picture,256)
			,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns OrderItemEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`order_item`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`order_item` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns OrderItemEntity
	 * @returns null
	 */
	public function load(int $id) : ?OrderItemEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_item` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_sale`.`order_item` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 order_id 加载一条
	 * @param	string  $order_id  ..订单ID
	 * @returns OrderItemEntity
	 * @returns null
	 */
	public function loadOneByOrderId (string $order_id) : ?OrderItemEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_item` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}
	/**
	 * 根据普通索引 order_id 加载全部
	 * @param	string	$order_id	..订单ID
	 * @returns OrderItemEntity
	 * @returns null
	 */
	public function loadAllByOrderId (string $order_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_item` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}

	/**
	 * 根据普通索引 sku_id 加载一条
	 * @param	int  $sku_id  ..SKUID
	 * @returns OrderItemEntity
	 * @returns null
	 */
	public function loadOneBySkuId (int $sku_id) : ?OrderItemEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_item` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}
	/**
	 * 根据普通索引 sku_id 加载全部
	 * @param	int	$sku_id	..SKUID
	 * @returns OrderItemEntity
	 * @returns null
	 */
	public function loadAllBySkuId (int $sku_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_item` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.order_item 插入一条新纪录
	 * @param	OrderItemEntity    $obj    ..订单详情
	 * @returns bool
	 */
	public function insert(OrderItemEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.order_item 回写一条记录<br>
	 * 更新依据： yuemi_sale.order_item.id
	 * @param	OrderItemEntity	  $obj    ..订单详情
	 * @returns bool
	 */
	 public function update(OrderItemEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 订单用券
 * @table order_ticket
 * @engine innodb
 */
final class OrderTicketEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 购物券ID
	 * @var int
	 */
	public $ticket_id = null;

	/**
	 * 购物券面额
	 * @var float
	 */
	public $price = null;
}
/**
 * OrderTicketEntity Factory<br>
 * 订单用券
 */
final class OrderTicketFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var OrderTicketFactory
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
	public static function Instance() : OrderTicketFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new OrderTicketFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new OrderTicketFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`order_ticket`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`order_ticket` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : OrderTicketEntity {
		$obj = new OrderTicketEntity();$obj->id = $row['id'];
		$obj->order_id = $row['order_id'];
		$obj->ticket_id = $row['ticket_id'];
		$obj->price = $row['price'];
		return $obj;
	}

	private function _object_to_insert(OrderTicketEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`order_ticket` %s(`id`,`order_id`,`ticket_id`,`price`) VALUES (NULL,'%s',%d,%f)";
		return sprintf($sql,'',self::_encode_string($obj->order_id,16)
			,$obj->ticket_id,$obj->price);
	}
	private function _object_to_update(OrderTicketEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`order_ticket` %s SET `order_id` = '%s',`ticket_id` = %d,`price` = %f WHERE `id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->order_id,16)
			,$obj->ticket_id,$obj->price,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns OrderTicketEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`order_ticket`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`order_ticket` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns OrderTicketEntity
	 * @returns null
	 */
	public function load(int $id) : ?OrderTicketEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_ticket` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_sale`.`order_ticket` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 order_id 加载一条
	 * @param	string  $order_id  ..订单ID
	 * @returns OrderTicketEntity
	 * @returns null
	 */
	public function loadOneByOrderId (string $order_id) : ?OrderTicketEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_ticket` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}
	/**
	 * 根据普通索引 order_id 加载全部
	 * @param	string	$order_id	..订单ID
	 * @returns OrderTicketEntity
	 * @returns null
	 */
	public function loadAllByOrderId (string $order_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_ticket` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}

	/**
	 * 根据普通索引 ticket_id 加载一条
	 * @param	int  $ticket_id  ..购物券ID
	 * @returns OrderTicketEntity
	 * @returns null
	 */
	public function loadOneByTicketId (int $ticket_id) : ?OrderTicketEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_ticket` WHERE `ticket_id` = '%d'",
			$ticket_id
		));
		
	}
	/**
	 * 根据普通索引 ticket_id 加载全部
	 * @param	int	$ticket_id	..购物券ID
	 * @returns OrderTicketEntity
	 * @returns null
	 */
	public function loadAllByTicketId (int $ticket_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`order_ticket` WHERE `ticket_id` = '%d'",
			$ticket_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.order_ticket 插入一条新纪录
	 * @param	OrderTicketEntity    $obj    ..订单用券
	 * @returns bool
	 */
	public function insert(OrderTicketEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.order_ticket 回写一条记录<br>
	 * 更新依据： yuemi_sale.order_ticket.id
	 * @param	OrderTicketEntity	  $obj    ..订单用券
	 * @returns bool
	 */
	 public function update(OrderTicketEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 返利
 * @table rebate
 * @engine innodb
 */
final class RebateEntity extends \Ziima\Data\Entity {
	/**
	 * 明细ID
	 * @var int
	 */
	public $item_id = null;

	/**
	 * 订单ID
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 本条佣金记录的归属人
	 * @var int
	 * @default	0
	 */
	public $owner_id = 0;

	/**
	 * 购买者ID
	 * @var int
	 * @default	0
	 */
	public $buyer_id = 0;

	/**
	 * 购买者VIP状态
	 * @var int
	 * @default	0
	 */
	public $buyer_vip = 0;

	/**
	 * 下单时的邀请人
	 * @var int
	 * @default	0
	 */
	public $invitor_id = 0;

	/**
	 * 邀请人的VIP状态
	 * @var int
	 * @default	0
	 */
	public $invitor_vip = 0;

	/**
	 * 分享ID
	 * @var int
	 * @default	0
	 */
	public $share_id = 0;

	/**
	 * 分享会员ID
	 * @var int
	 * @default	0
	 */
	public $share_user_id = 0;

	/**
	 * 当时的总监ID
	 * @var int
	 * @default	0
	 */
	public $cheif_id = 0;

	/**
	 * 当时的总经理ID
	 * @var int
	 * @default	0
	 */
	public $director_id = 0;

	/**
	 * SKUID
	 * @var int
	 * @default	0
	 */
	public $sku_id = 0;

	/**
	 * 创建时间
	 * @var int
	 * @default	0
	 */
	public $time_create = 0;

	/**
	 * 订单完成时间(T)
	 * @var int
	 * @default	0
	 */
	public $time_finish = 0;

	/**
	 * SPUID
	 * @var int
	 * @default	0
	 */
	public $spu_id = 0;

	/**
	 * 购买数量
	 * @var int
	 * @default	0
	 */
	public $pay_count = 0;

	/**
	 * 支付总额
	 * @var float
	 * @default	0.000000
	 */
	public $pay_total = 0.000000;

	/**
	 * 支付余额
	 * @var float
	 * @default	0.000000
	 */
	public $pay_money = 0.000000;

	/**
	 * 支付佣金
	 * @var float
	 * @default	0.000000
	 */
	public $pay_profit = 0.000000;

	/**
	 * 支付券
	 * @var float
	 * @default	0.000000
	 */
	public $pay_ticket = 0.000000;

	/**
	 * 在线支付
	 * @var float
	 * @default	0.000000
	 */
	public $pay_online = 0.000000;

	/**
	 * 总可支配佣金
	 * @var float
	 * @default	0.000000
	 */
	public $total_profit = 0.000000;

	/**
	 * 平台返利金额
	 * @var float
	 * @default	0.000000
	 */
	public $system_profit = 0.000000;

	/**
	 * 自己返利金额
	 * @var float
	 * @default	0.000000
	 */
	public $self_profit = 0.000000;

	/**
	 * 分享返利金额
	 * @var float
	 * @default	0.000000
	 */
	public $share_profit = 0.000000;

	/**
	 * 经理返利比例
	 * @var float
	 * @default	0.000000
	 */
	public $cheif_ratio = 0.000000;

	/**
	 * 总监返利金额
	 * @var float
	 * @default	0.000000
	 */
	public $cheif_profit = 0.000000;

	/**
	 * 经理返利比例
	 * @var float
	 * @default	0.000000
	 */
	public $director_ratio = 0.000000;

	/**
	 * 经理返利金额
	 * @var float
	 * @default	0.000000
	 */
	public $director_profit = 0.000000;

	/**
	 * 状态：0待确认,1已取消,2已确认,3已结算
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
}
/**
 * RebateEntity Factory<br>
 * 返利
 */
final class RebateFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var RebateFactory
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
	public static function Instance() : RebateFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new RebateFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new RebateFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`rebate`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`rebate` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : RebateEntity {
		$obj = new RebateEntity();$obj->item_id = $row['item_id'];
		$obj->order_id = $row['order_id'];
		$obj->owner_id = $row['owner_id'];
		$obj->buyer_id = $row['buyer_id'];
		$obj->buyer_vip = $row['buyer_vip'];
		$obj->invitor_id = $row['invitor_id'];
		$obj->invitor_vip = $row['invitor_vip'];
		$obj->share_id = $row['share_id'];
		$obj->share_user_id = $row['share_user_id'];
		$obj->cheif_id = $row['cheif_id'];
		$obj->director_id = $row['director_id'];
		$obj->sku_id = $row['sku_id'];
		$obj->time_create = $row['time_create'];
		$obj->time_finish = $row['time_finish'];
		$obj->spu_id = $row['spu_id'];
		$obj->pay_count = $row['pay_count'];
		$obj->pay_total = $row['pay_total'];
		$obj->pay_money = $row['pay_money'];
		$obj->pay_profit = $row['pay_profit'];
		$obj->pay_ticket = $row['pay_ticket'];
		$obj->pay_online = $row['pay_online'];
		$obj->total_profit = $row['total_profit'];
		$obj->system_profit = $row['system_profit'];
		$obj->self_profit = $row['self_profit'];
		$obj->share_profit = $row['share_profit'];
		$obj->cheif_ratio = $row['cheif_ratio'];
		$obj->cheif_profit = $row['cheif_profit'];
		$obj->director_ratio = $row['director_ratio'];
		$obj->director_profit = $row['director_profit'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->update_time = $row['update_time'];
		return $obj;
	}

	private function _object_to_insert(RebateEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`rebate` %s(`item_id`,`order_id`,`owner_id`,`buyer_id`,`buyer_vip`,`invitor_id`,`invitor_vip`,`share_id`,`share_user_id`,`cheif_id`,`director_id`,`sku_id`,`time_create`,`time_finish`,`spu_id`,`pay_count`,`pay_total`,`pay_money`,`pay_profit`,`pay_ticket`,`pay_online`,`total_profit`,`system_profit`,`self_profit`,`share_profit`,`cheif_ratio`,`cheif_profit`,`director_ratio`,`director_profit`,`status`,`create_time`,`update_time`) VALUES (%d,'%s',%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%d,UNIX_TIMESTAMP(),UNIX_TIMESTAMP())";
		return sprintf($sql,'',$obj->item_id,self::_encode_string($obj->order_id,16)
			,$obj->owner_id,$obj->buyer_id,$obj->buyer_vip,$obj->invitor_id,$obj->invitor_vip,$obj->share_id,$obj->share_user_id,$obj->cheif_id,$obj->director_id,$obj->sku_id,$obj->time_create,$obj->time_finish,$obj->spu_id,$obj->pay_count,$obj->pay_total,$obj->pay_money,$obj->pay_profit,$obj->pay_ticket,$obj->pay_online,$obj->total_profit,$obj->system_profit,$obj->self_profit,$obj->share_profit,$obj->cheif_ratio,$obj->cheif_profit,$obj->director_ratio,$obj->director_profit,$obj->status);
	}
	private function _object_to_update(RebateEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`rebate` %s SET `order_id` = '%s',`owner_id` = %d,`buyer_id` = %d,`buyer_vip` = %d,`invitor_id` = %d,`invitor_vip` = %d,`share_id` = %d,`share_user_id` = %d,`cheif_id` = %d,`director_id` = %d,`sku_id` = %d,`time_create` = %d,`time_finish` = %d,`spu_id` = %d,`pay_count` = %d,`pay_total` = %f,`pay_money` = %f,`pay_profit` = %f,`pay_ticket` = %f,`pay_online` = %f,`total_profit` = %f,`system_profit` = %f,`self_profit` = %f,`share_profit` = %f,`cheif_ratio` = %f,`cheif_profit` = %f,`director_ratio` = %f,`director_profit` = %f,`status` = %d,`update_time` = UNIX_TIMESTAMP() WHERE `item_id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->order_id,16)
			,$obj->owner_id,$obj->buyer_id,$obj->buyer_vip,$obj->invitor_id,$obj->invitor_vip,$obj->share_id,$obj->share_user_id,$obj->cheif_id,$obj->director_id,$obj->sku_id,$obj->time_create,$obj->time_finish,$obj->spu_id,$obj->pay_count,$obj->pay_total,$obj->pay_money,$obj->pay_profit,$obj->pay_ticket,$obj->pay_online,$obj->total_profit,$obj->system_profit,$obj->self_profit,$obj->share_profit,$obj->cheif_ratio,$obj->cheif_profit,$obj->director_ratio,$obj->director_profit,$obj->status,$obj->item_id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns RebateEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`rebate`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`rebate` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据主键 "item_id" 加载一条
	 * @param	int	$item_id	..明细ID
	 * @returns RebateEntity
	 * @returns null
	 */
	public function load(int $item_id) : ?RebateEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`rebate` WHERE `item_id` = '%d'",
			$item_id
		));
		
	}
	
	/**
	 * 根据主键 "item_id" 删除一条
	 * @param	int	$item_id	..明细ID
	 * @returns bool
	 */
	public function delete(int $item_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`rebate` WHERE `item_id` = '%d'",
			$item_id
		));
		
	}
	
	/**
	 * 根据普通索引 buyer_id 加载一条
	 * @param	int  $buyer_id  ..购买者ID
	 * @returns RebateEntity
	 * @returns null
	 */
	public function loadOneByBuyerId (int $buyer_id) : ?RebateEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`rebate` WHERE `buyer_id` = '%d'",
			$buyer_id
		));
		
	}
	/**
	 * 根据普通索引 buyer_id 加载全部
	 * @param	int	$buyer_id	..购买者ID
	 * @returns RebateEntity
	 * @returns null
	 */
	public function loadAllByBuyerId (int $buyer_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`rebate` WHERE `buyer_id` = '%d'",
			$buyer_id
		));
		
	}

	/**
	 * 根据普通索引 cheif_id 加载一条
	 * @param	int  $cheif_id  ..当时的总监ID
	 * @returns RebateEntity
	 * @returns null
	 */
	public function loadOneByCheifId (int $cheif_id) : ?RebateEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`rebate` WHERE `cheif_id` = '%d'",
			$cheif_id
		));
		
	}
	/**
	 * 根据普通索引 cheif_id 加载全部
	 * @param	int	$cheif_id	..当时的总监ID
	 * @returns RebateEntity
	 * @returns null
	 */
	public function loadAllByCheifId (int $cheif_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`rebate` WHERE `cheif_id` = '%d'",
			$cheif_id
		));
		
	}

	/**
	 * 根据普通索引 director_id 加载一条
	 * @param	int  $director_id  ..当时的总经理ID
	 * @returns RebateEntity
	 * @returns null
	 */
	public function loadOneByDirectorId (int $director_id) : ?RebateEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`rebate` WHERE `director_id` = '%d'",
			$director_id
		));
		
	}
	/**
	 * 根据普通索引 director_id 加载全部
	 * @param	int	$director_id	..当时的总经理ID
	 * @returns RebateEntity
	 * @returns null
	 */
	public function loadAllByDirectorId (int $director_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`rebate` WHERE `director_id` = '%d'",
			$director_id
		));
		
	}

	/**
	 * 根据普通索引 time_finish 加载一条
	 * @param	int  $time_finish  ..订单完成时间(T)
	 * @returns RebateEntity
	 * @returns null
	 */
	public function loadOneByTimeFinish (int $time_finish) : ?RebateEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`rebate` WHERE `time_finish` = '%d'",
			$time_finish
		));
		
	}
	/**
	 * 根据普通索引 time_finish 加载全部
	 * @param	int	$time_finish	..订单完成时间(T)
	 * @returns RebateEntity
	 * @returns null
	 */
	public function loadAllByTimeFinish (int $time_finish) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`rebate` WHERE `time_finish` = '%d'",
			$time_finish
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..状态：0待确认,1已取消,2已确认,3已结算
	 * @returns RebateEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?RebateEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`rebate` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..状态：0待确认,1已取消,2已确认,3已结算
	 * @returns RebateEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`rebate` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.rebate 插入一条新纪录
	 * @param	RebateEntity    $obj    ..返利
	 * @returns bool
	 */
	public function insert(RebateEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_insert($obj));
		if($ret === false)
			return false;
		
		return true;
	}
	
	/**
	 * 向数据表 yuemi_sale.rebate 回写一条记录<br>
	 * 更新依据： yuemi_sale.rebate.item_id
	 * @param	RebateEntity	  $obj    ..返利
	 * @returns bool
	 */
	 public function update(RebateEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 商品分享
 * @table share
 * @engine innodb
 */
final class ShareEntity extends \Ziima\Data\Entity {
	/**
	 * 分享ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 用户ID
	 * @var int
	 */
	public $user_id = null;

	/**
	 * 分享归属总经理ID
	 * @var int
	 * @default	0
	 */
	public $director_id = 0;

	/**
	 * 分享所属团队ID
	 * @var int
	 * @default	0
	 */
	public $team_id = 0;

	/**
	 * 分享人的员工ID
	 * @var int
	 * @default	0
	 */
	public $member_id = 0;

	/**
	 * 分享代码
	 * @var string
	 */
	public $share_code = null;

	/**
	 * 使用模板ID
	 * @var int
	 */
	public $template_id = null;

	/**
	 * 货架ID
	 * @var int
	 */
	public $sku_id = null;

	/**
	 * 分享文案
	 * @var string
	 */
	public $title = null;

	/**
	 * 生成的页面URL
	 * @var string
	 */
	public $page_url = null;

	/**
	 * 生成的图片URL
	 * @var string
	 */
	public $image_url = null;

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
 * ShareEntity Factory<br>
 * 商品分享
 */
final class ShareFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ShareFactory
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
	public static function Instance() : ShareFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ShareFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ShareFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`share`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`share` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ShareEntity {
		$obj = new ShareEntity();$obj->id = $row['id'];
		$obj->user_id = $row['user_id'];
		$obj->director_id = $row['director_id'];
		$obj->team_id = $row['team_id'];
		$obj->member_id = $row['member_id'];
		$obj->share_code = $row['share_code'];
		$obj->template_id = $row['template_id'];
		$obj->sku_id = $row['sku_id'];
		$obj->title = $row['title'];
		$obj->page_url = $row['page_url'];
		$obj->image_url = $row['image_url'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(ShareEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`share` %s(`id`,`user_id`,`director_id`,`team_id`,`member_id`,`share_code`,`template_id`,`sku_id`,`title`,`page_url`,`image_url`,`create_time`,`create_from`) VALUES (NULL,%d,%d,%d,%d,'%s',%d,%d,'%s','%s','%s',UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',$obj->user_id,$obj->director_id,$obj->team_id,$obj->member_id,self::_encode_string($obj->share_code,16)
			,$obj->template_id,$obj->sku_id,self::_encode_string($obj->title,256)
			,self::_encode_string($obj->page_url,1024)
			,self::_encode_string($obj->image_url,1024)
			,$obj->create_from);
	}
	private function _object_to_update(ShareEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`share` %s SET `user_id` = %d,`director_id` = %d,`team_id` = %d,`member_id` = %d,`share_code` = '%s',`template_id` = %d,`sku_id` = %d,`title` = '%s',`page_url` = '%s',`image_url` = '%s',`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->user_id,$obj->director_id,$obj->team_id,$obj->member_id,self::_encode_string($obj->share_code,16)
			,$obj->template_id,$obj->sku_id,self::_encode_string($obj->title,256)
			,self::_encode_string($obj->page_url,1024)
			,self::_encode_string($obj->image_url,1024)
			,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ShareEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`share`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`share` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..分享ID
	 * @returns ShareEntity
	 * @returns null
	 */
	public function load(int $id) : ?ShareEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`share` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..分享ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`share` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 user_id 加载一条
	 * @param	int  $user_id  ..用户ID
	 * @returns ShareEntity
	 * @returns null
	 */
	public function loadOneByUserId (int $user_id) : ?ShareEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`share` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}
	/**
	 * 根据普通索引 user_id 加载全部
	 * @param	int	$user_id	..用户ID
	 * @returns ShareEntity
	 * @returns null
	 */
	public function loadAllByUserId (int $user_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`share` WHERE `user_id` = '%d'",
			$user_id
		));
		
	}

	/**
	 * 根据普通索引 sku_id 加载一条
	 * @param	int  $sku_id  ..货架ID
	 * @returns ShareEntity
	 * @returns null
	 */
	public function loadOneBySkuId (int $sku_id) : ?ShareEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`share` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}
	/**
	 * 根据普通索引 sku_id 加载全部
	 * @param	int	$sku_id	..货架ID
	 * @returns ShareEntity
	 * @returns null
	 */
	public function loadAllBySkuId (int $sku_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`share` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.share 插入一条新纪录
	 * @param	ShareEntity    $obj    ..商品分享
	 * @returns bool
	 */
	public function insert(ShareEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.share 回写一条记录<br>
	 * 更新依据： yuemi_sale.share.id
	 * @param	ShareEntity	  $obj    ..商品分享
	 * @returns bool
	 */
	 public function update(ShareEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 分享商品列表
 * @table share_icon
 * @engine innodb
 */
final class ShareIconEntity extends \Ziima\Data\Entity {
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
	 * 类型：1SKU素材，2SPU素材，3用户素材
	 * @var int
	 * @default	0
	 */
	public $type = 0;

	/**
	 * 素材ID
	 * @var int
	 * @default	0
	 */
	public $mat_id = 0;

	/**
	 * 素材URL
	 * @var string
	 */
	public $mat_url = null;

	/**
	 * 素材位置
	 * @var int
	 * @default	0
	 */
	public $p_order = 0;
}
/**
 * ShareIconEntity Factory<br>
 * 分享商品列表
 */
final class ShareIconFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ShareIconFactory
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
	public static function Instance() : ShareIconFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ShareIconFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ShareIconFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`share_icon`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`share_icon` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ShareIconEntity {
		$obj = new ShareIconEntity();$obj->id = $row['id'];
		$obj->share_id = $row['share_id'];
		$obj->type = $row['type'];
		$obj->mat_id = $row['mat_id'];
		$obj->mat_url = $row['mat_url'];
		$obj->p_order = $row['p_order'];
		return $obj;
	}

	private function _object_to_insert(ShareIconEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`share_icon` %s(`id`,`share_id`,`type`,`mat_id`,`mat_url`,`p_order`) VALUES (NULL,%d,%d,%d,'%s',%d)";
		return sprintf($sql,'',$obj->share_id,$obj->type,$obj->mat_id,self::_encode_string($obj->mat_url,256)
			,$obj->p_order);
	}
	private function _object_to_update(ShareIconEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`share_icon` %s SET `share_id` = %d,`type` = %d,`mat_id` = %d,`mat_url` = '%s',`p_order` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->share_id,$obj->type,$obj->mat_id,self::_encode_string($obj->mat_url,256)
			,$obj->p_order,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ShareIconEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`share_icon`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`share_icon` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns ShareIconEntity
	 * @returns null
	 */
	public function load(int $id) : ?ShareIconEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`share_icon` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_sale`.`share_icon` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 share_id 加载一条
	 * @param	int  $share_id  ..分享ID
	 * @returns ShareIconEntity
	 * @returns null
	 */
	public function loadOneByShareId (int $share_id) : ?ShareIconEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`share_icon` WHERE `share_id` = '%d'",
			$share_id
		));
		
	}
	/**
	 * 根据普通索引 share_id 加载全部
	 * @param	int	$share_id	..分享ID
	 * @returns ShareIconEntity
	 * @returns null
	 */
	public function loadAllByShareId (int $share_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`share_icon` WHERE `share_id` = '%d'",
			$share_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.share_icon 插入一条新纪录
	 * @param	ShareIconEntity    $obj    ..分享商品列表
	 * @returns bool
	 */
	public function insert(ShareIconEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.share_icon 回写一条记录<br>
	 * 更新依据： yuemi_sale.share_icon.id
	 * @param	ShareIconEntity	  $obj    ..分享商品列表
	 * @returns bool
	 */
	 public function update(ShareIconEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 分享模板
 * @table share_template
 * @engine innodb
 */
final class ShareTemplateEntity extends \Ziima\Data\Entity {
	/**
	 * 模板ID
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
	 * 是否支持多商品
	 * @var int
	 * @default	0
	 */
	public $is_multiple = 0;

	/**
	 * HTML模板路径
	 * @var string
	 */
	public $tpl_path = null;

	/**
	 * HTML模板代码
	 * @var string
	 */
	public $tpl_content = null;

	/**
	 * 商品文案配置：x,y,w,h,length,size,color
	 * @var string
	 */
	public $title_config = null;

	/**
	 * 商品素材配置：count,x,y,w,h,padding
	 * @var string
	 */
	public $material_config = null;

	/**
	 * 个人昵称配置：open,x,y,size,color
	 * @var string
	 */
	public $name_config = null;

	/**
	 * 个人头像配置：open,x,y,w,h
	 * @var string
	 */
	public $avatar_config = null;

	/**
	 * 平台价格配置：open,x,y,size,color
	 * @var string
	 */
	public $price_config = null;

	/**
	 * 参考价格配置：open,x,y,size,color
	 * @var string
	 */
	public $market_config = null;

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
 * ShareTemplateEntity Factory<br>
 * 分享模板
 */
final class ShareTemplateFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var ShareTemplateFactory
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
	public static function Instance() : ShareTemplateFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new ShareTemplateFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new ShareTemplateFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`share_template`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`share_template` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : ShareTemplateEntity {
		$obj = new ShareTemplateEntity();$obj->id = $row['id'];
		$obj->name = $row['name'];
		$obj->body_path = $row['body_path'];
		$obj->body_url = $row['body_url'];
		$obj->body_width = $row['body_width'];
		$obj->body_height = $row['body_height'];
		$obj->is_multiple = $row['is_multiple'];
		$obj->tpl_path = $row['tpl_path'];
		$obj->tpl_content = $row['tpl_content'];
		$obj->title_config = $row['title_config'];
		$obj->material_config = $row['material_config'];
		$obj->name_config = $row['name_config'];
		$obj->avatar_config = $row['avatar_config'];
		$obj->price_config = $row['price_config'];
		$obj->market_config = $row['market_config'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_user = $row['create_user'];
		$obj->create_from = $row['create_from'];
		return $obj;
	}

	private function _object_to_insert(ShareTemplateEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`share_template` %s(`id`,`name`,`body_path`,`body_url`,`body_width`,`body_height`,`is_multiple`,`tpl_path`,`tpl_content`,`title_config`,`material_config`,`name_config`,`avatar_config`,`price_config`,`market_config`,`status`,`create_time`,`create_user`,`create_from`) VALUES (NULL,'%s','%s','%s',%d,%d,%d,'%s','%s','%s','%s','%s','%s','%s','%s',%d,UNIX_TIMESTAMP(),%d,%d)";
		return sprintf($sql,'',self::_encode_string($obj->name,32)
			,self::_encode_string($obj->body_path,256)
			,self::_encode_string($obj->body_url,256)
			,$obj->body_width,$obj->body_height,$obj->is_multiple,self::_encode_string($obj->tpl_path,128)
			,self::_encode_string($obj->tpl_content,65535)
			,self::_encode_string($obj->title_config,256)
			,self::_encode_string($obj->material_config,256)
			,self::_encode_string($obj->name_config,256)
			,self::_encode_string($obj->avatar_config,256)
			,self::_encode_string($obj->price_config,256)
			,self::_encode_string($obj->market_config,256)
			,$obj->status,$obj->create_user,$obj->create_from);
	}
	private function _object_to_update(ShareTemplateEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`share_template` %s SET `name` = '%s',`body_path` = '%s',`body_url` = '%s',`body_width` = %d,`body_height` = %d,`is_multiple` = %d,`tpl_path` = '%s',`tpl_content` = '%s',`title_config` = '%s',`material_config` = '%s',`name_config` = '%s',`avatar_config` = '%s',`price_config` = '%s',`market_config` = '%s',`status` = %d,`create_user` = %d,`create_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->name,32)
			,self::_encode_string($obj->body_path,256)
			,self::_encode_string($obj->body_url,256)
			,$obj->body_width,$obj->body_height,$obj->is_multiple,self::_encode_string($obj->tpl_path,128)
			,self::_encode_string($obj->tpl_content,65535)
			,self::_encode_string($obj->title_config,256)
			,self::_encode_string($obj->material_config,256)
			,self::_encode_string($obj->name_config,256)
			,self::_encode_string($obj->avatar_config,256)
			,self::_encode_string($obj->price_config,256)
			,self::_encode_string($obj->market_config,256)
			,$obj->status,$obj->create_user,$obj->create_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns ShareTemplateEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`share_template`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`share_template` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..模板ID
	 * @returns ShareTemplateEntity
	 * @returns null
	 */
	public function load(int $id) : ?ShareTemplateEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`share_template` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..模板ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`share_template` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 向数据表 yuemi_sale.share_template 插入一条新纪录
	 * @param	ShareTemplateEntity    $obj    ..分享模板
	 * @returns bool
	 */
	public function insert(ShareTemplateEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.share_template 回写一条记录<br>
	 * 更新依据： yuemi_sale.share_template.id
	 * @param	ShareTemplateEntity	  $obj    ..分享模板
	 * @returns bool
	 */
	 public function update(ShareTemplateEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * SKU
 * @table sku
 * @engine innodb
 */
final class SkuEntity extends \Ziima\Data\Entity {
	/**
	 * SKUID
	 * @var int
	 */
	public $id = null;

	/**
	 * SPUID
	 * @var int
	 * @default	0
	 */
	public $spu_id = 0;

	/**
	 * 分类ID
	 * @var int
	 * @default	0
	 */
	public $catagory_id = 0;

	/**
	 * 供应商ID
	 * @var int
	 * @default	0
	 */
	public $supplier_id = 0;

	/**
	 * 标题
	 * @var string
	 */
	public $title = null;

	/**
	 * 副标题
	 * @var string
	 */
	public $subtitle = null;

	/**
	 * 规格
	 * @var string
	 */
	public $specs = null;

	/**
	 * 规格ID
	 * @var int
	 * @default	0
	 */
	public $spec_id = 0;

	/**
	 * 条码
	 * @var string
	 */
	public $barcode = null;

	/**
	 * 货号
	 * @var string
	 */
	public $serial = null;

	/**
	 * 单位重量（克）
	 * @var float
	 * @default	0.0000
	 */
	public $weight = 0.0000;

	/**
	 * 单位
	 * @var string
	 */
	public $unit = null;

	/**
	 * 实时库存
	 * @var int
	 * @default	0
	 */
	public $depot = 0;

	/**
	 * 成本价
	 * @var float
	 */
	public $price_base = null;

	/**
	 * 平台价
	 * @var float
	 */
	public $price_sale = null;

	/**
	 * 有邀请码会员的价格
	 * @var float
	 * @default	0.0000
	 */
	public $price_inv = 0.0000;

	/**
	 * 对标价
	 * @var float
	 */
	public $price_ref = null;

	/**
	 * 显示用的市场价
	 * @var float
	 * @default	0.0000
	 */
	public $price_market = 0.0000;

	/**
	 * 毛利
	 * @var float
	 * @default	0.000000
	 */
	public $price_ratio = 0.000000;

	/**
	 * VIP返佣
	 * @var float
	 * @default	0.0000
	 */
	public $rebate_vip = 0.0000;

	/**
	 * 赠送阅币方式：0不赠送,1按次,2按件
	 * @var int
	 * @default	0
	 */
	public $coin_style = 0;

	/**
	 * 购买者赠送阅币
	 * @var float
	 * @default	0.00000000
	 */
	public $coin_buyer = 0.00000000;

	/**
	 * 分享者赠送阅币
	 * @var float
	 * @default	0.00000000
	 */
	public $coin_inviter = 0.00000000;

	/**
	 * 限购类型：0不限购,1按人头限购,2按地址限购
	 * @var int
	 * @default	0
	 */
	public $limit_style = 0;

	/**
	 * 限购数量
	 * @var int
	 * @default	0
	 */
	public $limit_size = 0;

	/**
	 * 描述内容
	 * @var string
	 */
	public $intro = null;

	/**
	 * 是否支持退换货
	 * @var int
	 * @default	1
	 */
	public $att_refund = 1;

	/**
	 * 新手专享属性：新手限购一件
	 * @var int
	 * @default	0
	 */
	public $att_newbie = 0;

	/**
	 * 包邮属性：0无邮费,1智能运费
	 * @var int
	 * @default	0
	 */
	public $att_shipping = 0;

	/**
	 * 是否默认SKU
	 * @var int
	 * @default	0
	 */
	public $att_default = 0;

	/**
	 * 是否仅支持APP购买
	 * @var int
	 * @default	1
	 */
	public $att_only_app = 1;

	/**
	 * 商品状态：0待审,1打回,2通过,3下架,4删除
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
 * SkuEntity Factory<br>
 * SKU
 */
final class SkuFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SkuFactory
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
	public static function Instance() : SkuFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SkuFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SkuFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`sku`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`sku` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SkuEntity {
		$obj = new SkuEntity();$obj->id = $row['id'];
		$obj->spu_id = $row['spu_id'];
		$obj->catagory_id = $row['catagory_id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->title = $row['title'];
		$obj->subtitle = $row['subtitle'];
		$obj->specs = $row['specs'];
		$obj->spec_id = $row['spec_id'];
		$obj->barcode = $row['barcode'];
		$obj->serial = $row['serial'];
		$obj->weight = $row['weight'];
		$obj->unit = $row['unit'];
		$obj->depot = $row['depot'];
		$obj->price_base = $row['price_base'];
		$obj->price_sale = $row['price_sale'];
		$obj->price_inv = $row['price_inv'];
		$obj->price_ref = $row['price_ref'];
		$obj->price_market = $row['price_market'];
		$obj->price_ratio = $row['price_ratio'];
		$obj->rebate_vip = $row['rebate_vip'];
		$obj->coin_style = $row['coin_style'];
		$obj->coin_buyer = $row['coin_buyer'];
		$obj->coin_inviter = $row['coin_inviter'];
		$obj->limit_style = $row['limit_style'];
		$obj->limit_size = $row['limit_size'];
		$obj->intro = $row['intro'];
		$obj->att_refund = $row['att_refund'];
		$obj->att_newbie = $row['att_newbie'];
		$obj->att_shipping = $row['att_shipping'];
		$obj->att_default = $row['att_default'];
		$obj->att_only_app = $row['att_only_app'];
		$obj->status = $row['status'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(SkuEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`sku` %s(`id`,`spu_id`,`catagory_id`,`supplier_id`,`title`,`subtitle`,`specs`,`spec_id`,`barcode`,`serial`,`weight`,`unit`,`depot`,`price_base`,`price_sale`,`price_inv`,`price_ref`,`price_market`,`price_ratio`,`rebate_vip`,`coin_style`,`coin_buyer`,`coin_inviter`,`limit_style`,`limit_size`,`intro`,`att_refund`,`att_newbie`,`att_shipping`,`att_default`,`att_only_app`,`status`,`create_user`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`) VALUES (NULL,%d,%d,%d,'%s','%s','%s',%d,'%s','%s',%f,'%s',%d,%f,%f,%f,%f,%f,%f,%f,%d,%f,%f,%d,%d,'%s',%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->spu_id,$obj->catagory_id,$obj->supplier_id,self::_encode_string($obj->title,128)
			,self::_encode_string($obj->subtitle,128)
			,self::_encode_string($obj->specs,65535)
			,$obj->spec_id,self::_encode_string($obj->barcode,32)
			,self::_encode_string($obj->serial,64)
			,$obj->weight,self::_encode_string($obj->unit,32)
			,$obj->depot,$obj->price_base,$obj->price_sale,$obj->price_inv,$obj->price_ref,$obj->price_market,$obj->price_ratio,$obj->rebate_vip,$obj->coin_style,$obj->coin_buyer,$obj->coin_inviter,$obj->limit_style,$obj->limit_size,self::_encode_string($obj->intro,65535)
			,$obj->att_refund,$obj->att_newbie,$obj->att_shipping,$obj->att_default,$obj->att_only_app,$obj->status,$obj->create_user,$obj->create_time,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(SkuEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`sku` %s SET `spu_id` = %d,`catagory_id` = %d,`supplier_id` = %d,`title` = '%s',`subtitle` = '%s',`specs` = '%s',`spec_id` = %d,`barcode` = '%s',`serial` = '%s',`weight` = %f,`unit` = '%s',`depot` = %d,`price_base` = %f,`price_sale` = %f,`price_inv` = %f,`price_ref` = %f,`price_market` = %f,`price_ratio` = %f,`rebate_vip` = %f,`coin_style` = %d,`coin_buyer` = %f,`coin_inviter` = %f,`limit_style` = %d,`limit_size` = %d,`intro` = '%s',`att_refund` = %d,`att_newbie` = %d,`att_shipping` = %d,`att_default` = %d,`att_only_app` = %d,`status` = %d,`create_user` = %d,`create_time` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->spu_id,$obj->catagory_id,$obj->supplier_id,self::_encode_string($obj->title,128)
			,self::_encode_string($obj->subtitle,128)
			,self::_encode_string($obj->specs,65535)
			,$obj->spec_id,self::_encode_string($obj->barcode,32)
			,self::_encode_string($obj->serial,64)
			,$obj->weight,self::_encode_string($obj->unit,32)
			,$obj->depot,$obj->price_base,$obj->price_sale,$obj->price_inv,$obj->price_ref,$obj->price_market,$obj->price_ratio,$obj->rebate_vip,$obj->coin_style,$obj->coin_buyer,$obj->coin_inviter,$obj->limit_style,$obj->limit_size,self::_encode_string($obj->intro,65535)
			,$obj->att_refund,$obj->att_newbie,$obj->att_shipping,$obj->att_default,$obj->att_only_app,$obj->status,$obj->create_user,$obj->create_time,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SkuEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`sku`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`sku` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..SKUID
	 * @returns SkuEntity
	 * @returns null
	 */
	public function load(int $id) : ?SkuEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..SKUID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`sku` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 spu_id 加载一条
	 * @param	int  $spu_id  ..SPUID
	 * @returns SkuEntity
	 * @returns null
	 */
	public function loadOneBySpuId (int $spu_id) : ?SkuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku` WHERE `spu_id` = '%d'",
			$spu_id
		));
		
	}
	/**
	 * 根据普通索引 spu_id 加载全部
	 * @param	int	$spu_id	..SPUID
	 * @returns SkuEntity
	 * @returns null
	 */
	public function loadAllBySpuId (int $spu_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku` WHERE `spu_id` = '%d'",
			$spu_id
		));
		
	}

	/**
	 * 根据普通索引 catagory_id 加载一条
	 * @param	int  $catagory_id  ..分类ID
	 * @returns SkuEntity
	 * @returns null
	 */
	public function loadOneByCatagoryId (int $catagory_id) : ?SkuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku` WHERE `catagory_id` = '%d'",
			$catagory_id
		));
		
	}
	/**
	 * 根据普通索引 catagory_id 加载全部
	 * @param	int	$catagory_id	..分类ID
	 * @returns SkuEntity
	 * @returns null
	 */
	public function loadAllByCatagoryId (int $catagory_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku` WHERE `catagory_id` = '%d'",
			$catagory_id
		));
		
	}

	/**
	 * 根据普通索引 title 加载一条
	 * @param	string  $title  ..标题
	 * @returns SkuEntity
	 * @returns null
	 */
	public function loadOneByTitle (string $title) : ?SkuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku` WHERE `title` = '%s'",
			parent::$reader->escape_string($title)
		));
		
	}
	/**
	 * 根据普通索引 title 加载全部
	 * @param	string	$title	..标题
	 * @returns SkuEntity
	 * @returns null
	 */
	public function loadAllByTitle (string $title) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku` WHERE `title` = '%s'",
			parent::$reader->escape_string($title)
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..商品状态：0待审,1打回,2通过,3下架,4删除
	 * @returns SkuEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?SkuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..商品状态：0待审,1打回,2通过,3下架,4删除
	 * @returns SkuEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.sku 插入一条新纪录
	 * @param	SkuEntity    $obj    ..SKU
	 * @returns bool
	 */
	public function insert(SkuEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.sku 回写一条记录<br>
	 * 更新依据： yuemi_sale.sku.id
	 * @param	SkuEntity	  $obj    ..SKU
	 * @returns bool
	 */
	 public function update(SkuEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * SKU数据变化工单
 * @table sku_changes
 * @engine innodb
 */
final class SkuChangesEntity extends \Ziima\Data\Entity {
	/**
	 * 记录ID
	 * @var int
	 */
	public $id = null;

	/**
	 * SKUID
	 * @var int
	 */
	public $sku_id = null;

	/**
	 * 供应商ID
	 * @var int
	 */
	public $supplier_id = null;

	/**
	 * 是否变更标题
	 * @var int
	 * @default	0
	 */
	public $chg_title = 0;

	/**
	 * 旧标题
	 * @var string
	 */
	public $old_title = null;

	/**
	 * 新标题
	 * @var string
	 */
	public $new_title = null;

	/**
	 * 是否变更品类
	 * @var int
	 * @default	0
	 */
	public $chg_catagory = 0;

	/**
	 * 旧分类
	 * @var int
	 * @default	0
	 */
	public $old_catagory = 0;

	/**
	 * 新分类
	 * @var int
	 * @default	0
	 */
	public $new_catagory = 0;

	/**
	 * 是否变更成本价
	 * @var int
	 * @default	0
	 */
	public $chg_price_base = 0;

	/**
	 * 旧价格
	 * @var float
	 * @default	0.0000
	 */
	public $old_price_base = 0.0000;

	/**
	 * 新价格
	 * @var float
	 * @default	0.0000
	 */
	public $new_price_base = 0.0000;

	/**
	 * 是否变更阅米价
	 * @var int
	 * @default	0
	 */
	public $chg_price_sale = 0;

	/**
	 * 旧价格
	 * @var float
	 * @default	0.0000
	 */
	public $old_price_sale = 0.0000;

	/**
	 * 新价格
	 * @var float
	 * @default	0.0000
	 */
	public $new_price_sale = 0.0000;

	/**
	 * 是否变更库存
	 * @var int
	 * @default	0
	 */
	public $chg_depot = 0;

	/**
	 * 旧库存
	 * @var int
	 * @default	0
	 */
	public $old_depot = 0;

	/**
	 * 新库存
	 * @var int
	 * @default	0
	 */
	public $new_depot = 0;

	/**
	 * 状态：0待审,1已审,2拒绝
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
 * SkuChangesEntity Factory<br>
 * SKU数据变化工单
 */
final class SkuChangesFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SkuChangesFactory
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
	public static function Instance() : SkuChangesFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SkuChangesFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SkuChangesFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`sku_changes`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`sku_changes` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SkuChangesEntity {
		$obj = new SkuChangesEntity();$obj->id = $row['id'];
		$obj->sku_id = $row['sku_id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->chg_title = $row['chg_title'];
		$obj->old_title = $row['old_title'];
		$obj->new_title = $row['new_title'];
		$obj->chg_catagory = $row['chg_catagory'];
		$obj->old_catagory = $row['old_catagory'];
		$obj->new_catagory = $row['new_catagory'];
		$obj->chg_price_base = $row['chg_price_base'];
		$obj->old_price_base = $row['old_price_base'];
		$obj->new_price_base = $row['new_price_base'];
		$obj->chg_price_sale = $row['chg_price_sale'];
		$obj->old_price_sale = $row['old_price_sale'];
		$obj->new_price_sale = $row['new_price_sale'];
		$obj->chg_depot = $row['chg_depot'];
		$obj->old_depot = $row['old_depot'];
		$obj->new_depot = $row['new_depot'];
		$obj->status = $row['status'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(SkuChangesEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`sku_changes` %s(`id`,`sku_id`,`supplier_id`,`chg_title`,`old_title`,`new_title`,`chg_catagory`,`old_catagory`,`new_catagory`,`chg_price_base`,`old_price_base`,`new_price_base`,`chg_price_sale`,`old_price_sale`,`new_price_sale`,`chg_depot`,`old_depot`,`new_depot`,`status`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`) VALUES (NULL,%d,%d,%d,'%s','%s',%d,%d,%d,%d,%f,%f,%d,%f,%f,%d,%d,%d,%d,UNIX_TIMESTAMP(),%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->sku_id,$obj->supplier_id,$obj->chg_title,self::_encode_string($obj->old_title,128)
			,self::_encode_string($obj->new_title,128)
			,$obj->chg_catagory,$obj->old_catagory,$obj->new_catagory,$obj->chg_price_base,$obj->old_price_base,$obj->new_price_base,$obj->chg_price_sale,$obj->old_price_sale,$obj->new_price_sale,$obj->chg_depot,$obj->old_depot,$obj->new_depot,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(SkuChangesEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`sku_changes` %s SET `sku_id` = %d,`supplier_id` = %d,`chg_title` = %d,`old_title` = '%s',`new_title` = '%s',`chg_catagory` = %d,`old_catagory` = %d,`new_catagory` = %d,`chg_price_base` = %d,`old_price_base` = %f,`new_price_base` = %f,`chg_price_sale` = %d,`old_price_sale` = %f,`new_price_sale` = %f,`chg_depot` = %d,`old_depot` = %d,`new_depot` = %d,`status` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->sku_id,$obj->supplier_id,$obj->chg_title,self::_encode_string($obj->old_title,128)
			,self::_encode_string($obj->new_title,128)
			,$obj->chg_catagory,$obj->old_catagory,$obj->new_catagory,$obj->chg_price_base,$obj->old_price_base,$obj->new_price_base,$obj->chg_price_sale,$obj->old_price_sale,$obj->new_price_sale,$obj->chg_depot,$obj->old_depot,$obj->new_depot,$obj->status,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SkuChangesEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`sku_changes`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`sku_changes` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录ID
	 * @returns SkuChangesEntity
	 * @returns null
	 */
	public function load(int $id) : ?SkuChangesEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_changes` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_sale`.`sku_changes` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 sku_id 加载一条
	 * @param	int  $sku_id  ..SKUID
	 * @returns SkuChangesEntity
	 * @returns null
	 */
	public function loadOneBySkuId (int $sku_id) : ?SkuChangesEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_changes` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}
	/**
	 * 根据普通索引 sku_id 加载全部
	 * @param	int	$sku_id	..SKUID
	 * @returns SkuChangesEntity
	 * @returns null
	 */
	public function loadAllBySkuId (int $sku_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_changes` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}

	/**
	 * 根据普通索引 supplier_id 加载一条
	 * @param	int  $supplier_id  ..供应商ID
	 * @returns SkuChangesEntity
	 * @returns null
	 */
	public function loadOneBySupplierId (int $supplier_id) : ?SkuChangesEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_changes` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	/**
	 * 根据普通索引 supplier_id 加载全部
	 * @param	int	$supplier_id	..供应商ID
	 * @returns SkuChangesEntity
	 * @returns null
	 */
	public function loadAllBySupplierId (int $supplier_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_changes` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..状态：0待审,1已审,2拒绝
	 * @returns SkuChangesEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?SkuChangesEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_changes` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..状态：0待审,1已审,2拒绝
	 * @returns SkuChangesEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_changes` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.sku_changes 插入一条新纪录
	 * @param	SkuChangesEntity    $obj    ..SKU数据变化工单
	 * @returns bool
	 */
	public function insert(SkuChangesEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.sku_changes 回写一条记录<br>
	 * 更新依据： yuemi_sale.sku_changes.id
	 * @param	SkuChangesEntity	  $obj    ..SKU数据变化工单
	 * @returns bool
	 */
	 public function update(SkuChangesEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * SKU素材
 * @table sku_material
 * @engine innodb
 */
final class SkuMaterialEntity extends \Ziima\Data\Entity {
	/**
	 * 素材ID
	 * @var int
	 */
	public $id = null;

	/**
	 * SKUID
	 * @var int
	 */
	public $sku_id = null;

	/**
	 * 素材类型：0主图,1内容,2活动
	 * @var int
	 */
	public $type = null;

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
	 * 是否默认素材
	 * @var int
	 * @default	0
	 */
	public $is_default = 0;

	/**
	 * 内部排序
	 * @var int
	 * @default	0
	 */
	public $p_order = 0;

	/**
	 * 素材状态 0待审,1已审,2删除
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建人
	 * @var int
	 */
	public $create_user = null;

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
 * SkuMaterialEntity Factory<br>
 * SKU素材
 */
final class SkuMaterialFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SkuMaterialFactory
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
	public static function Instance() : SkuMaterialFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SkuMaterialFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SkuMaterialFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`sku_material`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`sku_material` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SkuMaterialEntity {
		$obj = new SkuMaterialEntity();$obj->id = $row['id'];
		$obj->sku_id = $row['sku_id'];
		$obj->type = $row['type'];
		$obj->file_size = $row['file_size'];
		$obj->file_url = $row['file_url'];
		$obj->image_width = $row['image_width'];
		$obj->image_height = $row['image_height'];
		$obj->thumb_url = $row['thumb_url'];
		$obj->thumb_size = $row['thumb_size'];
		$obj->thumb_width = $row['thumb_width'];
		$obj->thumb_height = $row['thumb_height'];
		$obj->is_default = $row['is_default'];
		$obj->p_order = $row['p_order'];
		$obj->status = $row['status'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(SkuMaterialEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`sku_material` %s(`id`,`sku_id`,`type`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`is_default`,`p_order`,`status`,`create_user`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`) VALUES (NULL,%d,%d,%d,'%s',%d,%d,'%s',%d,%d,%d,%d,%d,%d,%d,UNIX_TIMESTAMP(),%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->sku_id,$obj->type,$obj->file_size,self::_encode_string($obj->file_url,512)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,512)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->is_default,$obj->p_order,$obj->status,$obj->create_user,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(SkuMaterialEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`sku_material` %s SET `sku_id` = %d,`type` = %d,`file_size` = %d,`file_url` = '%s',`image_width` = %d,`image_height` = %d,`thumb_url` = '%s',`thumb_size` = %d,`thumb_width` = %d,`thumb_height` = %d,`is_default` = %d,`p_order` = %d,`status` = %d,`create_user` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->sku_id,$obj->type,$obj->file_size,self::_encode_string($obj->file_url,512)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,512)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->is_default,$obj->p_order,$obj->status,$obj->create_user,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SkuMaterialEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`sku_material`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`sku_material` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..素材ID
	 * @returns SkuMaterialEntity
	 * @returns null
	 */
	public function load(int $id) : ?SkuMaterialEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_material` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_sale`.`sku_material` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 sku_id 加载一条
	 * @param	int  $sku_id  ..SKUID
	 * @returns SkuMaterialEntity
	 * @returns null
	 */
	public function loadOneBySkuId (int $sku_id) : ?SkuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}
	/**
	 * 根据普通索引 sku_id 加载全部
	 * @param	int	$sku_id	..SKUID
	 * @returns SkuMaterialEntity
	 * @returns null
	 */
	public function loadAllBySkuId (int $sku_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..素材状态 0待审,1已审,2删除
	 * @returns SkuMaterialEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?SkuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_material` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..素材状态 0待审,1已审,2删除
	 * @returns SkuMaterialEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_material` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.sku_material 插入一条新纪录
	 * @param	SkuMaterialEntity    $obj    ..SKU素材
	 * @returns bool
	 */
	public function insert(SkuMaterialEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.sku_material 回写一条记录<br>
	 * 更新依据： yuemi_sale.sku_material.id
	 * @param	SkuMaterialEntity	  $obj    ..SKU素材
	 * @returns bool
	 */
	 public function update(SkuMaterialEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * SKU定时任务
 * @table sku_task
 * @engine innodb
 */
final class SkuTaskEntity extends \Ziima\Data\Entity {
	/**
	 * 任务ID
	 * @var int
	 */
	public $id = null;

	/**
	 * 上架ID
	 * @var int
	 */
	public $sku_id = null;

	/**
	 * 任务影响标志：标题
	 * @var int
	 * @default	0
	 */
	public $uf_title = 0;

	/**
	 * 任务影响标志：子标题
	 * @var int
	 * @default	0
	 */
	public $uf_subtitle = 0;

	/**
	 * 任务影响标志：平台价格
	 * @var int
	 * @default	0
	 */
	public $uf_price = 0;

	/**
	 * 任务影响标志：库存
	 * @var int
	 * @default	0
	 */
	public $uf_qty = 0;

	/**
	 * 任务影响标志：限购
	 * @var int
	 * @default	0
	 */
	public $uf_limit = 0;

	/**
	 * 任务影响标志：用户返利
	 * @var int
	 * @default	0
	 */
	public $uf_rebate = 0;

	/**
	 * 状态0:标题
	 * @var string
	 */
	public $s0_title = null;

	/**
	 * 状态0:子标题
	 * @var string
	 */
	public $s0_subtitle = null;

	/**
	 * 状态0:售卖价
	 * @var float
	 * @default	0.0000
	 */
	public $s0_price = 0.0000;

	/**
	 * 状态0:库存
	 * @var int
	 * @default	0
	 */
	public $s0_qty = 0;

	/**
	 * 状态0:限购
	 * @var int
	 * @default	0
	 */
	public $s0_limit = 0;

	/**
	 * 状态0:用户返利
	 * @var float
	 * @default	0.0000
	 */
	public $s0_rebate = 0.0000;

	/**
	 * 启动S1时间
	 * @var int
	 * @default	0
	 */
	public $s1_time = 0;

	/**
	 * 状态1:标题
	 * @var string
	 */
	public $s1_title = null;

	/**
	 * 状态1:子标题
	 * @var string
	 */
	public $s1_subtitle = null;

	/**
	 * 状态1:售卖价
	 * @var float
	 * @default	0.0000
	 */
	public $s1_price = 0.0000;

	/**
	 * 状态1:库存
	 * @var int
	 * @default	0
	 */
	public $s1_qty = 0;

	/**
	 * 状态1:限购
	 * @var int
	 * @default	0
	 */
	public $s1_limit = 0;

	/**
	 * 状态1:用户返利
	 * @var float
	 * @default	0.0000
	 */
	public $s1_rebate = 0.0000;

	/**
	 * 启动S2时间
	 * @var int
	 * @default	0
	 */
	public $s2_time = 0;

	/**
	 * 状态2操作：0恢复S0,1使用S2,2下架
	 * @var int
	 * @default	0
	 */
	public $s2_method = 0;

	/**
	 * 状态2:标题
	 * @var string
	 */
	public $s2_title = null;

	/**
	 * 状态2:子标题
	 * @var string
	 */
	public $s2_subtitle = null;

	/**
	 * 状态2:售卖价
	 * @var float
	 * @default	0.0000
	 */
	public $s2_price = 0.0000;

	/**
	 * 状态2:库存
	 * @var int
	 * @default	0
	 */
	public $s2_qty = 0;

	/**
	 * 状态2:限购
	 * @var int
	 * @default	0
	 */
	public $s2_limit = 0;

	/**
	 * 状态2:用户返利
	 * @var float
	 * @default	0.0000
	 */
	public $s2_rebate = 0.0000;

	/**
	 * 任务状态：0待审,1拒绝,2删除,3批准,4排队,5启动,6结束,7过期
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

	/**
	 * 复核人
	 * @var int
	 * @default	0
	 */
	public $review_user = 0;

	/**
	 * 复核时间
	 * @var int
	 * @default	0
	 */
	public $review_time = 0;

	/**
	 * 复核IP
	 * @var int
	 * @default	0
	 */
	public $review_from = 0;
}
/**
 * SkuTaskEntity Factory<br>
 * SKU定时任务
 */
final class SkuTaskFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SkuTaskFactory
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
	public static function Instance() : SkuTaskFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SkuTaskFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SkuTaskFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`sku_task`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`sku_task` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SkuTaskEntity {
		$obj = new SkuTaskEntity();$obj->id = $row['id'];
		$obj->sku_id = $row['sku_id'];
		$obj->uf_title = $row['uf_title'];
		$obj->uf_subtitle = $row['uf_subtitle'];
		$obj->uf_price = $row['uf_price'];
		$obj->uf_qty = $row['uf_qty'];
		$obj->uf_limit = $row['uf_limit'];
		$obj->uf_rebate = $row['uf_rebate'];
		$obj->s0_title = $row['s0_title'];
		$obj->s0_subtitle = $row['s0_subtitle'];
		$obj->s0_price = $row['s0_price'];
		$obj->s0_qty = $row['s0_qty'];
		$obj->s0_limit = $row['s0_limit'];
		$obj->s0_rebate = $row['s0_rebate'];
		$obj->s1_time = $row['s1_time'];
		$obj->s1_title = $row['s1_title'];
		$obj->s1_subtitle = $row['s1_subtitle'];
		$obj->s1_price = $row['s1_price'];
		$obj->s1_qty = $row['s1_qty'];
		$obj->s1_limit = $row['s1_limit'];
		$obj->s1_rebate = $row['s1_rebate'];
		$obj->s2_time = $row['s2_time'];
		$obj->s2_method = $row['s2_method'];
		$obj->s2_title = $row['s2_title'];
		$obj->s2_subtitle = $row['s2_subtitle'];
		$obj->s2_price = $row['s2_price'];
		$obj->s2_qty = $row['s2_qty'];
		$obj->s2_limit = $row['s2_limit'];
		$obj->s2_rebate = $row['s2_rebate'];
		$obj->status = $row['status'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		$obj->review_user = $row['review_user'];
		$obj->review_time = $row['review_time'];
		$obj->review_from = $row['review_from'];
		return $obj;
	}

	private function _object_to_insert(SkuTaskEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`sku_task` %s(`id`,`sku_id`,`uf_title`,`uf_subtitle`,`uf_price`,`uf_qty`,`uf_limit`,`uf_rebate`,`s0_title`,`s0_subtitle`,`s0_price`,`s0_qty`,`s0_limit`,`s0_rebate`,`s1_time`,`s1_title`,`s1_subtitle`,`s1_price`,`s1_qty`,`s1_limit`,`s1_rebate`,`s2_time`,`s2_method`,`s2_title`,`s2_subtitle`,`s2_price`,`s2_qty`,`s2_limit`,`s2_rebate`,`status`,`create_user`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`,`review_user`,`review_time`,`review_from`) VALUES (NULL,%d,%d,%d,%d,%d,%d,%d,'%s','%s',%f,%d,%d,%f,%d,'%s','%s',%f,%d,%d,%f,%d,%d,'%s','%s',%f,%d,%d,%f,%d,%d,UNIX_TIMESTAMP(),%d,%d,%d,%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->sku_id,$obj->uf_title,$obj->uf_subtitle,$obj->uf_price,$obj->uf_qty,$obj->uf_limit,$obj->uf_rebate,self::_encode_string($obj->s0_title,128)
			,self::_encode_string($obj->s0_subtitle,128)
			,$obj->s0_price,$obj->s0_qty,$obj->s0_limit,$obj->s0_rebate,$obj->s1_time,self::_encode_string($obj->s1_title,128)
			,self::_encode_string($obj->s1_subtitle,128)
			,$obj->s1_price,$obj->s1_qty,$obj->s1_limit,$obj->s1_rebate,$obj->s2_time,$obj->s2_method,self::_encode_string($obj->s2_title,128)
			,self::_encode_string($obj->s2_subtitle,128)
			,$obj->s2_price,$obj->s2_qty,$obj->s2_limit,$obj->s2_rebate,$obj->status,$obj->create_user,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->review_user,$obj->review_time,$obj->review_from);
	}
	private function _object_to_update(SkuTaskEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`sku_task` %s SET `sku_id` = %d,`uf_title` = %d,`uf_subtitle` = %d,`uf_price` = %d,`uf_qty` = %d,`uf_limit` = %d,`uf_rebate` = %d,`s0_title` = '%s',`s0_subtitle` = '%s',`s0_price` = %f,`s0_qty` = %d,`s0_limit` = %d,`s0_rebate` = %f,`s1_time` = %d,`s1_title` = '%s',`s1_subtitle` = '%s',`s1_price` = %f,`s1_qty` = %d,`s1_limit` = %d,`s1_rebate` = %f,`s2_time` = %d,`s2_method` = %d,`s2_title` = '%s',`s2_subtitle` = '%s',`s2_price` = %f,`s2_qty` = %d,`s2_limit` = %d,`s2_rebate` = %f,`status` = %d,`create_user` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d,`review_user` = %d,`review_time` = %d,`review_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->sku_id,$obj->uf_title,$obj->uf_subtitle,$obj->uf_price,$obj->uf_qty,$obj->uf_limit,$obj->uf_rebate,self::_encode_string($obj->s0_title,128)
			,self::_encode_string($obj->s0_subtitle,128)
			,$obj->s0_price,$obj->s0_qty,$obj->s0_limit,$obj->s0_rebate,$obj->s1_time,self::_encode_string($obj->s1_title,128)
			,self::_encode_string($obj->s1_subtitle,128)
			,$obj->s1_price,$obj->s1_qty,$obj->s1_limit,$obj->s1_rebate,$obj->s2_time,$obj->s2_method,self::_encode_string($obj->s2_title,128)
			,self::_encode_string($obj->s2_subtitle,128)
			,$obj->s2_price,$obj->s2_qty,$obj->s2_limit,$obj->s2_rebate,$obj->status,$obj->create_user,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->review_user,$obj->review_time,$obj->review_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SkuTaskEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`sku_task`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`sku_task` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..任务ID
	 * @returns SkuTaskEntity
	 * @returns null
	 */
	public function load(int $id) : ?SkuTaskEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_task` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..任务ID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`sku_task` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 sku_id 加载一条
	 * @param	int  $sku_id  ..上架ID
	 * @returns SkuTaskEntity
	 * @returns null
	 */
	public function loadOneBySkuId (int $sku_id) : ?SkuTaskEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_task` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}
	/**
	 * 根据普通索引 sku_id 加载全部
	 * @param	int	$sku_id	..上架ID
	 * @returns SkuTaskEntity
	 * @returns null
	 */
	public function loadAllBySkuId (int $sku_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_task` WHERE `sku_id` = '%d'",
			$sku_id
		));
		
	}

	/**
	 * 根据普通索引 s1_time 加载一条
	 * @param	int  $s1_time  ..启动S1时间
	 * @returns SkuTaskEntity
	 * @returns null
	 */
	public function loadOneByS1Time (int $s1_time) : ?SkuTaskEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_task` WHERE `s1_time` = '%d'",
			$s1_time
		));
		
	}
	/**
	 * 根据普通索引 s1_time 加载全部
	 * @param	int	$s1_time	..启动S1时间
	 * @returns SkuTaskEntity
	 * @returns null
	 */
	public function loadAllByS1Time (int $s1_time) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_task` WHERE `s1_time` = '%d'",
			$s1_time
		));
		
	}

	/**
	 * 根据普通索引 s2_time 加载一条
	 * @param	int  $s2_time  ..启动S2时间
	 * @returns SkuTaskEntity
	 * @returns null
	 */
	public function loadOneByS2Time (int $s2_time) : ?SkuTaskEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_task` WHERE `s2_time` = '%d'",
			$s2_time
		));
		
	}
	/**
	 * 根据普通索引 s2_time 加载全部
	 * @param	int	$s2_time	..启动S2时间
	 * @returns SkuTaskEntity
	 * @returns null
	 */
	public function loadAllByS2Time (int $s2_time) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_task` WHERE `s2_time` = '%d'",
			$s2_time
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..任务状态：0待审,1拒绝,2删除,3批准,4排队,5启动,6结束,7过期
	 * @returns SkuTaskEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?SkuTaskEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_task` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..任务状态：0待审,1拒绝,2删除,3批准,4排队,5启动,6结束,7过期
	 * @returns SkuTaskEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`sku_task` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.sku_task 插入一条新纪录
	 * @param	SkuTaskEntity    $obj    ..SKU定时任务
	 * @returns bool
	 */
	public function insert(SkuTaskEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.sku_task 回写一条记录<br>
	 * 更新依据： yuemi_sale.sku_task.id
	 * @param	SkuTaskEntity	  $obj    ..SKU定时任务
	 * @returns bool
	 */
	 public function update(SkuTaskEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * 推广传播 - 用户信息记录
 * @table spread_userinfo
 * @engine innodb
 */
final class SpreadUserinfoEntity extends \Ziima\Data\Entity {
	/**
	 * 记录Id
	 * @var int
	 */
	public $id = null;

	/**
	 * 来源
	 * @var string
	 */
	public $source = null;

	/**
	 * 手机号码
	 * @var string
	 */
	public $mobile = null;

	/**
	 * 姓名
	 * @var string
	 */
	public $name = null;

	/**
	 * 微信号
	 * @var string
	 */
	public $weixin = null;

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
	 * 订单Id
	 * @var string
	 */
	public $order_id = null;

	/**
	 * 记录时间
	 * @var int
	 * @default	0
	 */
	public $create_time = 0;

	/**
	 * 注册IP
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
	 * 状态：0未购买,1已购买
	 * @var int
	 * @default	0
	 */
	public $status = 0;
}
/**
 * SpreadUserinfoEntity Factory<br>
 * 推广传播 - 用户信息记录
 */
final class SpreadUserinfoFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SpreadUserinfoFactory
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
	public static function Instance() : SpreadUserinfoFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SpreadUserinfoFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SpreadUserinfoFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`spread_userinfo`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`spread_userinfo` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SpreadUserinfoEntity {
		$obj = new SpreadUserinfoEntity();$obj->id = $row['id'];
		$obj->source = $row['source'];
		$obj->mobile = $row['mobile'];
		$obj->name = $row['name'];
		$obj->weixin = $row['weixin'];
		$obj->region_id = $row['region_id'];
		$obj->address = $row['address'];
		$obj->order_id = $row['order_id'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->update_time = $row['update_time'];
		$obj->status = $row['status'];
		return $obj;
	}

	private function _object_to_insert(SpreadUserinfoEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`spread_userinfo` %s(`id`,`source`,`mobile`,`name`,`weixin`,`region_id`,`address`,`order_id`,`create_time`,`create_from`,`update_time`,`status`) VALUES (NULL,'%s','%s','%s','%s',%d,'%s','%s',UNIX_TIMESTAMP(),%d,UNIX_TIMESTAMP(),%d)";
		return sprintf($sql,'',self::_encode_string($obj->source,32)
			,self::_encode_string($obj->mobile,11)
			,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->weixin,64)
			,$obj->region_id,self::_encode_string($obj->address,256)
			,self::_encode_string($obj->order_id,16)
			,$obj->create_from,$obj->status);
	}
	private function _object_to_update(SpreadUserinfoEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`spread_userinfo` %s SET `source` = '%s',`mobile` = '%s',`name` = '%s',`weixin` = '%s',`region_id` = %d,`address` = '%s',`order_id` = '%s',`create_from` = %d,`status` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',self::_encode_string($obj->source,32)
			,self::_encode_string($obj->mobile,11)
			,self::_encode_string($obj->name,32)
			,self::_encode_string($obj->weixin,64)
			,$obj->region_id,self::_encode_string($obj->address,256)
			,self::_encode_string($obj->order_id,16)
			,$obj->create_from,$obj->status,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SpreadUserinfoEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`spread_userinfo`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`spread_userinfo` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..记录Id
	 * @returns SpreadUserinfoEntity
	 * @returns null
	 */
	public function load(int $id) : ?SpreadUserinfoEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spread_userinfo` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..记录Id
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`spread_userinfo` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据唯一索引 mobile 加载
	 * @param	string	$mobile	..手机号码
	 * @returns SpreadUserinfoEntity
	 * @returns null
	 */
	public function loadByMobile (string $mobile) : ?SpreadUserinfoEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spread_userinfo` WHERE `mobile` = '%s'",
			parent::$reader->escape_string($mobile)
		));
		
	}
	
	/**
	 * 根据唯一索引 "mobile" 删除一条
	 * @param	string	$mobile	..手机号码
	 * @returns bool
	 */
	public function deleteByMobile(string $mobile) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`spread_userinfo` WHERE `mobile` = '%s'",
			parent::$reader->escape_string($mobile)
		));
		
	}
	
	/**
	 * 根据唯一索引 order_id 加载
	 * @param	string	$order_id	..订单Id
	 * @returns SpreadUserinfoEntity
	 * @returns null
	 */
	public function loadByOrderId (string $order_id) : ?SpreadUserinfoEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spread_userinfo` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}
	
	/**
	 * 根据唯一索引 "order_id" 删除一条
	 * @param	string	$order_id	..订单Id
	 * @returns bool
	 */
	public function deleteByOrderId(string $order_id) : bool {
		
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`spread_userinfo` WHERE `order_id` = '%s'",
			parent::$reader->escape_string($order_id)
		));
		
	}
	
	/**
	 * 根据普通索引 source 加载一条
	 * @param	string  $source  ..来源
	 * @returns SpreadUserinfoEntity
	 * @returns null
	 */
	public function loadOneBySource (string $source) : ?SpreadUserinfoEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spread_userinfo` WHERE `source` = '%s'",
			parent::$reader->escape_string($source)
		));
		
	}
	/**
	 * 根据普通索引 source 加载全部
	 * @param	string	$source	..来源
	 * @returns SpreadUserinfoEntity
	 * @returns null
	 */
	public function loadAllBySource (string $source) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spread_userinfo` WHERE `source` = '%s'",
			parent::$reader->escape_string($source)
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.spread_userinfo 插入一条新纪录
	 * @param	SpreadUserinfoEntity    $obj    ..推广传播 - 用户信息记录
	 * @returns bool
	 */
	public function insert(SpreadUserinfoEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.spread_userinfo 回写一条记录<br>
	 * 更新依据： yuemi_sale.spread_userinfo.id
	 * @param	SpreadUserinfoEntity	  $obj    ..推广传播 - 用户信息记录
	 * @returns bool
	 */
	 public function update(SpreadUserinfoEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * SPU
 * @table spu
 * @engine innodb
 */
final class SpuEntity extends \Ziima\Data\Entity {
	/**
	 * SPUID
	 * @var int
	 */
	public $id = null;

	/**
	 * 分类ID
	 * @var int
	 * @default	0
	 */
	public $catagory_id = 0;

	/**
	 * 供应商ID
	 * @var int
	 * @default	0
	 */
	public $supplier_id = 0;

	/**
	 * 品牌ID
	 * @var int
	 * @default	0
	 */
	public $brand_id = 0;

	/**
	 * 商品标题
	 * @var string
	 */
	public $title = null;

	/**
	 * 规格
	 * @var string
	 */
	public $specs = null;

	/**
	 * 规格ID
	 * @var int
	 * @default	0
	 */
	public $spec_id = 0;

	/**
	 * 条码
	 * @var string
	 */
	public $barcode = null;

	/**
	 * 货号
	 * @var string
	 */
	public $serial = null;

	/**
	 * 单位重量（克）
	 * @var float
	 * @default	0.0000
	 */
	public $weight = 0.0000;

	/**
	 * 单位
	 * @var string
	 */
	public $unit = null;

	/**
	 * 分类内部排序，DESC
	 * @var int
	 * @default	0
	 */
	public $p_order = 0;

	/**
	 * 描述内容
	 * @var string
	 */
	public $intro = null;

	/**
	 * 是否大礼包：0否，1是
	 * @var int
	 * @default	0
	 */
	public $is_gift_set = 0;

	/**
	 * 是否显示在列表页：0否，1是
	 * @var int
	 * @default	1
	 */
	public $show_on_list = 1;

	/**
	 * 是否支持发货到海外：0否，1是
	 * @var int
	 * @default	0
	 */
	public $att_overseas = 0;

	/**
	 * 是否支持发货到偏远：0否，1是
	 * @var int
	 * @default	0
	 */
	public $att_special_region = 0;

	/**
	 * 是否支持退换货
	 * @var int
	 * @default	1
	 */
	public $att_refund = 1;

	/**
	 * SPU状态 0下架,1上架
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 预定上架时间
	 * @var int
	 * @default	0
	 */
	public $online_time = 0;

	/**
	 * 预定下架时间
	 * @var int
	 * @default	0
	 */
	public $offline_time = 0;

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
	 * 更新人
	 * @var int
	 * @default	0
	 */
	public $update_user = 0;

	/**
	 * 更新时间
	 * @var int
	 * @default	0
	 */
	public $update_time = 0;

	/**
	 * 更新IP
	 * @var int
	 * @default	0
	 */
	public $update_from = 0;

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
 * SpuEntity Factory<br>
 * SPU
 */
final class SpuFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SpuFactory
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
	public static function Instance() : SpuFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SpuFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SpuFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`spu`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`spu` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SpuEntity {
		$obj = new SpuEntity();$obj->id = $row['id'];
		$obj->catagory_id = $row['catagory_id'];
		$obj->supplier_id = $row['supplier_id'];
		$obj->brand_id = $row['brand_id'];
		$obj->title = $row['title'];
		$obj->specs = $row['specs'];
		$obj->spec_id = $row['spec_id'];
		$obj->barcode = $row['barcode'];
		$obj->serial = $row['serial'];
		$obj->weight = $row['weight'];
		$obj->unit = $row['unit'];
		$obj->p_order = $row['p_order'];
		$obj->intro = $row['intro'];
		$obj->is_gift_set = $row['is_gift_set'];
		$obj->show_on_list = $row['show_on_list'];
		$obj->att_overseas = $row['att_overseas'];
		$obj->att_special_region = $row['att_special_region'];
		$obj->att_refund = $row['att_refund'];
		$obj->status = $row['status'];
		$obj->online_time = $row['online_time'];
		$obj->offline_time = $row['offline_time'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->update_user = $row['update_user'];
		$obj->update_time = $row['update_time'];
		$obj->update_from = $row['update_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(SpuEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`spu` %s(`id`,`catagory_id`,`supplier_id`,`brand_id`,`title`,`specs`,`spec_id`,`barcode`,`serial`,`weight`,`unit`,`p_order`,`intro`,`is_gift_set`,`show_on_list`,`att_overseas`,`att_special_region`,`att_refund`,`status`,`online_time`,`offline_time`,`create_user`,`create_time`,`create_from`,`update_user`,`update_time`,`update_from`,`audit_user`,`audit_time`,`audit_from`) VALUES (NULL,%d,%d,%d,'%s','%s',%d,'%s','%s',%f,'%s',%d,'%s',%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->catagory_id,$obj->supplier_id,$obj->brand_id,self::_encode_string($obj->title,128)
			,self::_encode_string($obj->specs,65535)
			,$obj->spec_id,self::_encode_string($obj->barcode,32)
			,self::_encode_string($obj->serial,64)
			,$obj->weight,self::_encode_string($obj->unit,32)
			,$obj->p_order,self::_encode_string($obj->intro,65535)
			,$obj->is_gift_set,$obj->show_on_list,$obj->att_overseas,$obj->att_special_region,$obj->att_refund,$obj->status,$obj->online_time,$obj->offline_time,$obj->create_user,$obj->create_time,$obj->create_from,$obj->update_user,$obj->update_time,$obj->update_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(SpuEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`spu` %s SET `catagory_id` = %d,`supplier_id` = %d,`brand_id` = %d,`title` = '%s',`specs` = '%s',`spec_id` = %d,`barcode` = '%s',`serial` = '%s',`weight` = %f,`unit` = '%s',`p_order` = %d,`intro` = '%s',`is_gift_set` = %d,`show_on_list` = %d,`att_overseas` = %d,`att_special_region` = %d,`att_refund` = %d,`status` = %d,`online_time` = %d,`offline_time` = %d,`create_user` = %d,`create_time` = %d,`create_from` = %d,`update_user` = %d,`update_time` = %d,`update_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->catagory_id,$obj->supplier_id,$obj->brand_id,self::_encode_string($obj->title,128)
			,self::_encode_string($obj->specs,65535)
			,$obj->spec_id,self::_encode_string($obj->barcode,32)
			,self::_encode_string($obj->serial,64)
			,$obj->weight,self::_encode_string($obj->unit,32)
			,$obj->p_order,self::_encode_string($obj->intro,65535)
			,$obj->is_gift_set,$obj->show_on_list,$obj->att_overseas,$obj->att_special_region,$obj->att_refund,$obj->status,$obj->online_time,$obj->offline_time,$obj->create_user,$obj->create_time,$obj->create_from,$obj->update_user,$obj->update_time,$obj->update_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SpuEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`spu`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`spu` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..SPUID
	 * @returns SpuEntity
	 * @returns null
	 */
	public function load(int $id) : ?SpuEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spu` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据自增ID "id" 删除一条
	 * @param	int		$id		..SPUID
	 * @returns bool
	 */
	public function delete(int $id) : bool {
		return $this->_execute_none(sprintf(
			"DELETE FROM `yuemi_sale`.`spu` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 catagory_id 加载一条
	 * @param	int  $catagory_id  ..分类ID
	 * @returns SpuEntity
	 * @returns null
	 */
	public function loadOneByCatagoryId (int $catagory_id) : ?SpuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spu` WHERE `catagory_id` = '%d'",
			$catagory_id
		));
		
	}
	/**
	 * 根据普通索引 catagory_id 加载全部
	 * @param	int	$catagory_id	..分类ID
	 * @returns SpuEntity
	 * @returns null
	 */
	public function loadAllByCatagoryId (int $catagory_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spu` WHERE `catagory_id` = '%d'",
			$catagory_id
		));
		
	}

	/**
	 * 根据普通索引 supplier_id 加载一条
	 * @param	int  $supplier_id  ..供应商ID
	 * @returns SpuEntity
	 * @returns null
	 */
	public function loadOneBySupplierId (int $supplier_id) : ?SpuEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spu` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}
	/**
	 * 根据普通索引 supplier_id 加载全部
	 * @param	int	$supplier_id	..供应商ID
	 * @returns SpuEntity
	 * @returns null
	 */
	public function loadAllBySupplierId (int $supplier_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spu` WHERE `supplier_id` = '%d'",
			$supplier_id
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.spu 插入一条新纪录
	 * @param	SpuEntity    $obj    ..SPU
	 * @returns bool
	 */
	public function insert(SpuEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.spu 回写一条记录<br>
	 * 更新依据： yuemi_sale.spu.id
	 * @param	SpuEntity	  $obj    ..SPU
	 * @returns bool
	 */
	 public function update(SpuEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * SPU素材
 * @table spu_material
 * @engine innodb
 */
final class SpuMaterialEntity extends \Ziima\Data\Entity {
	/**
	 * 素材ID
	 * @var int
	 */
	public $id = null;

	/**
	 * SPUID
	 * @var int
	 */
	public $spu_id = null;

	/**
	 * 素材类型：0主图,1内容,2活动
	 * @var int
	 */
	public $type = null;

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
	 * 是否默认素材
	 * @var int
	 * @default	0
	 */
	public $is_default = 0;

	/**
	 * 内部排序
	 * @var int
	 * @default	0
	 */
	public $p_order = 0;

	/**
	 * 素材状态 0待审,1已审,2删除
	 * @var int
	 * @default	0
	 */
	public $status = 0;

	/**
	 * 创建人
	 * @var int
	 */
	public $create_user = null;

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
 * SpuMaterialEntity Factory<br>
 * SPU素材
 */
final class SpuMaterialFactory extends \Ziima\Data\MySQLFactory {
	/**
	 * @var SpuMaterialFactory
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
	public static function Instance() : SpuMaterialFactory {
		if(! defined('MYSQL_WRITER'))
			throw new \Exception('缺少 MYSQL_WRITER 常量，无法使用Factory的单例');
		if(self::$_instance)
			return self::$_instance;
		if(defined('MYSQL_READER')){
			self::$_instance = new SpuMaterialFactory(MYSQL_WRITER,MYSQL_READER);
		}else{
			self::$_instance = new SpuMaterialFactory(MYSQL_WRITER,MYSQL_WRITER);
		}
		return self::$_instance;
	}
	
	/**
	 * 统计当前表中记录数量
	 * @returns int
	 */
	public function count() : int {
		return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`spu_material`");
	}
	
	/**
	 * 按条件统计当前表中记录数量
	 * @returns int
	 */
	public function countWith(string $whr) : int {
		if(! empty($whr))
			return $this->_execute_scalar("SELECT COUNT(*) FROM `yuemi_sale`.`spu_material` WHERE $whr");
		else
			return $this->count();
	}
	
	protected function _row_to_object(array $row) : SpuMaterialEntity {
		$obj = new SpuMaterialEntity();$obj->id = $row['id'];
		$obj->spu_id = $row['spu_id'];
		$obj->type = $row['type'];
		$obj->file_size = $row['file_size'];
		$obj->file_url = $row['file_url'];
		$obj->image_width = $row['image_width'];
		$obj->image_height = $row['image_height'];
		$obj->thumb_url = $row['thumb_url'];
		$obj->thumb_size = $row['thumb_size'];
		$obj->thumb_width = $row['thumb_width'];
		$obj->thumb_height = $row['thumb_height'];
		$obj->is_default = $row['is_default'];
		$obj->p_order = $row['p_order'];
		$obj->status = $row['status'];
		$obj->create_user = $row['create_user'];
		$obj->create_time = $row['create_time'];
		$obj->create_from = $row['create_from'];
		$obj->audit_user = $row['audit_user'];
		$obj->audit_time = $row['audit_time'];
		$obj->audit_from = $row['audit_from'];
		return $obj;
	}

	private function _object_to_insert(SpuMaterialEntity $obj) : string{
		
		$sql = "INSERT INTO `yuemi_sale`.`spu_material` %s(`id`,`spu_id`,`type`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`is_default`,`p_order`,`status`,`create_user`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`) VALUES (NULL,%d,%d,%d,'%s',%d,%d,'%s',%d,%d,%d,%d,%d,%d,%d,UNIX_TIMESTAMP(),%d,%d,%d,%d)";
		return sprintf($sql,'',$obj->spu_id,$obj->type,$obj->file_size,self::_encode_string($obj->file_url,512)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,512)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->is_default,$obj->p_order,$obj->status,$obj->create_user,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from);
	}
	private function _object_to_update(SpuMaterialEntity $obj) : string{
		
		$sql = "UPDATE `yuemi_sale`.`spu_material` %s SET `spu_id` = %d,`type` = %d,`file_size` = %d,`file_url` = '%s',`image_width` = %d,`image_height` = %d,`thumb_url` = '%s',`thumb_size` = %d,`thumb_width` = %d,`thumb_height` = %d,`is_default` = %d,`p_order` = %d,`status` = %d,`create_user` = %d,`create_from` = %d,`audit_user` = %d,`audit_time` = %d,`audit_from` = %d WHERE `id` = %d";
		
		return sprintf($sql,'',$obj->spu_id,$obj->type,$obj->file_size,self::_encode_string($obj->file_url,512)
			,$obj->image_width,$obj->image_height,self::_encode_string($obj->thumb_url,512)
			,$obj->thumb_size,$obj->thumb_width,$obj->thumb_height,$obj->is_default,$obj->p_order,$obj->status,$obj->create_user,$obj->create_from,$obj->audit_user,$obj->audit_time,$obj->audit_from,$obj->id);
	}
	
	/**
	 * 任意查询
	 * @param	string		$whr		查询条件和排序
	 * @param	int			$skip		分页
	 * @param	int			$limit		分页
	 * @returns SpuMaterialEntity[]
	 */
	public function queryWith(string $whr,int $skip = 0,int $limit = 0) : array{
		if(empty($whr)){
			$sql = "SELECT * FROM `yuemi_sale`.`spu_material`";
		}else{
			$sql = "SELECT * FROM `yuemi_sale`.`spu_material` WHERE $whr";
		}
		if($limit > 0){
			$sql .= " LIMIT $skip,$limit";
		}
		return $this->_fetch_multi_object($sql);
	}
	

	/**
	 * 根据自增ID "id" 加载一条
	 * @param	int		$id		..素材ID
	 * @returns SpuMaterialEntity
	 * @returns null
	 */
	public function load(int $id) : ?SpuMaterialEntity{
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spu_material` WHERE `id` = %d",
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
			"DELETE FROM `yuemi_sale`.`spu_material` WHERE `id` = %d",
			$id
		));
	}

	/**
	 * 根据普通索引 spu_id 加载一条
	 * @param	int  $spu_id  ..SPUID
	 * @returns SpuMaterialEntity
	 * @returns null
	 */
	public function loadOneBySpuId (int $spu_id) : ?SpuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spu_material` WHERE `spu_id` = '%d'",
			$spu_id
		));
		
	}
	/**
	 * 根据普通索引 spu_id 加载全部
	 * @param	int	$spu_id	..SPUID
	 * @returns SpuMaterialEntity
	 * @returns null
	 */
	public function loadAllBySpuId (int $spu_id) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spu_material` WHERE `spu_id` = '%d'",
			$spu_id
		));
		
	}

	/**
	 * 根据普通索引 status 加载一条
	 * @param	int  $status  ..素材状态 0待审,1已审,2删除
	 * @returns SpuMaterialEntity
	 * @returns null
	 */
	public function loadOneByStatus (int $status) : ?SpuMaterialEntity{
		
		return $this->_fetch_single_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spu_material` WHERE `status` = '%d'",
			$status
		));
		
	}
	/**
	 * 根据普通索引 status 加载全部
	 * @param	int	$status	..素材状态 0待审,1已审,2删除
	 * @returns SpuMaterialEntity
	 * @returns null
	 */
	public function loadAllByStatus (int $status) : array{
		
		return $this->_fetch_multi_object(sprintf(
			"SELECT * FROM `yuemi_sale`.`spu_material` WHERE `status` = '%d'",
			$status
		));
		
	}

	/**
	 * 向数据表 yuemi_sale.spu_material 插入一条新纪录
	 * @param	SpuMaterialEntity    $obj    ..SPU素材
	 * @returns bool
	 */
	public function insert(SpuMaterialEntity $obj) : bool {
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
	 * 向数据表 yuemi_sale.spu_material 回写一条记录<br>
	 * 更新依据： yuemi_sale.spu_material.id
	 * @param	SpuMaterialEntity	  $obj    ..SPU素材
	 * @returns bool
	 */
	 public function update(SpuMaterialEntity $obj) : bool {
		$this->__open_writer();
		$ret = parent::$writer->query($this->_object_to_update($obj));
		if($ret === false)
			return false;
		return true;
	 }
	
}
/**
 * yuemi_sale 存储过程调用器
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
		$this->Tracer = new \Ziima\Tracer('proc.yuemi_sale');
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
	 * 取消订单 <br>
	 * 调用存储过程 cancle_order ()
	 * @var	string	$OrderId		第1个参数
	 * @var	int	$UserId		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerCancleOrderOutput
	 */
	public function cancle_order(string $OrderId,int $UserId,int $ClientIp,bool $useReader = false) : InvokerCancleOrderOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`cancle_order` ('%s',%d,%d,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($OrderId)
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
			throw new \Exception("运行存储过程 cancle_order 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 cancle_order 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 cancle_order 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCancleOrderOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 加购物车 <br>
	 * 调用存储过程 cart_add ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$ShareId		第2个参数
	 * @var	int	$SkuId		第3个参数
	 * @var	int	$QtyAdd		第4个参数
	 * @var	int	$ClientIp		第5个参数
	 * @returns InvokerCartAddOutput
	 */
	public function cart_add(int $UserId,int $ShareId,int $SkuId,int $QtyAdd,int $ClientIp,bool $useReader = false) : InvokerCartAddOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`cart_add` (%d,%d,%d,%d,%d,@CartId,@ReturnValue,@ReturnMessage)"
			,$UserId,$ShareId,$SkuId,$QtyAdd,$ClientIp
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
			throw new \Exception("运行存储过程 cart_add 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @CartId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @CartId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 cart_add 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 cart_add 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCartAddOutput();
		$obj->CartId = $dat['@CartId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 减购物车 <br>
	 * 调用存储过程 cart_dec ()
	 * @var	int	$CartId		第1个参数
	 * @var	int	$QtyDec		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerCartDecOutput
	 */
	public function cart_dec(int $CartId,int $QtyDec,int $ClientIp,bool $useReader = false) : InvokerCartDecOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`cart_dec` (%d,%d,%d,@ReturnValue,@ReturnMessage)"
			,$CartId,$QtyDec,$ClientIp
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
			throw new \Exception("运行存储过程 cart_dec 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 cart_dec 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 cart_dec 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCartDecOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 购物车下单 <br>
	 * 调用存储过程 cart_purchase ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$UserAddressId		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @var	int	$SelUseMoney		第4个参数
	 * @var	int	$SelUseProfit		第5个参数
	 * @var	int	$SelUseRecruit		第6个参数
	 * @var	int	$SelUseTicket		第7个参数
	 * @var	string	$CommentUser		第8个参数
	 * @returns InvokerCartPurchaseOutput
	 */
	public function cart_purchase(int $UserId,int $UserAddressId,int $ClientIp,int $SelUseMoney,int $SelUseProfit,int $SelUseRecruit,int $SelUseTicket,string $CommentUser,bool $useReader = false) : InvokerCartPurchaseOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`cart_purchase` (%d,%d,%d,%d,%d,%d,%d,'%s',@PrimaryOrderId,@ReturnValue,@ReturnMessage)"
			,$UserId,$UserAddressId,$ClientIp,$SelUseMoney,$SelUseProfit,$SelUseRecruit,$SelUseTicket,parent::$writer->escape_string($CommentUser)
				
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
			throw new \Exception("运行存储过程 cart_purchase 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @PrimaryOrderId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @PrimaryOrderId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 cart_purchase 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 cart_purchase 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCartPurchaseOutput();
		$obj->PrimaryOrderId = $dat['@PrimaryOrderId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 取消订单 <br>
	 * 调用存储过程 close_order ()
	 * @var	string	$OrderId		第1个参数
	 * @var	int	$ClientIp		第2个参数
	 * @returns InvokerCloseOrderOutput
	 */
	public function close_order(string $OrderId,int $ClientIp,bool $useReader = false) : InvokerCloseOrderOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`close_order` ('%s',%d,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($OrderId)
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
			throw new \Exception("运行存储过程 close_order 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 close_order 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 close_order 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCloseOrderOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * SPU素材拷贝 <br>
	 * 调用存储过程 copy_esku_to_sku ()
	 * @var	int	$SkuId		第1个参数
	 * @var	int	$ExtSkuId		第2个参数
	 * @var	int	$UserId		第3个参数
	 * @var	int	$MType		第4个参数
	 * @var	int	$ClientIp		第5个参数
	 * @returns InvokerCopyEskuToSkuOutput
	 */
	public function copy_esku_to_sku(int $SkuId,int $ExtSkuId,int $UserId,int $MType,int $ClientIp,bool $useReader = false) : InvokerCopyEskuToSkuOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`copy_esku_to_sku` (%d,%d,%d,%d,%d,@ReturnValue,@ReturnMessage)"
			,$SkuId,$ExtSkuId,$UserId,$MType,$ClientIp
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
			throw new \Exception("运行存储过程 copy_esku_to_sku 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 copy_esku_to_sku 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 copy_esku_to_sku 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCopyEskuToSkuOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * SPU素材拷贝 <br>
	 * 调用存储过程 copy_espu_to_spu ()
	 * @var	int	$SpuId		第1个参数
	 * @var	int	$ExtSpuId		第2个参数
	 * @var	int	$UserId		第3个参数
	 * @var	int	$MType		第4个参数
	 * @var	int	$ClientIp		第5个参数
	 * @returns InvokerCopyEspuToSpuOutput
	 */
	public function copy_espu_to_spu(int $SpuId,int $ExtSpuId,int $UserId,int $MType,int $ClientIp,bool $useReader = false) : InvokerCopyEspuToSpuOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`copy_espu_to_spu` (%d,%d,%d,%d,%d,@ReturnValue,@ReturnMessage)"
			,$SpuId,$ExtSpuId,$UserId,$MType,$ClientIp
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
			throw new \Exception("运行存储过程 copy_espu_to_spu 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 copy_espu_to_spu 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 copy_espu_to_spu 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCopyEspuToSpuOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * SKU素材拷贝 <br>
	 * 调用存储过程 copy_to_sku ()
	 * @var	int	$SkuId		第1个参数
	 * @var	int	$UserId		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerCopyToSkuOutput
	 */
	public function copy_to_sku(int $SkuId,int $UserId,int $ClientIp,bool $useReader = false) : InvokerCopyToSkuOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`copy_to_sku` (%d,%d,%d,@ReturnValue,@ReturnMessage)"
			,$SkuId,$UserId,$ClientIp
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
			throw new \Exception("运行存储过程 copy_to_sku 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 copy_to_sku 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 copy_to_sku 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCopyToSkuOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * SPU素材拷贝 <br>
	 * 调用存储过程 copy_to_spu ()
	 * @var	int	$SpuId		第1个参数
	 * @var	int	$UserId		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerCopyToSpuOutput
	 */
	public function copy_to_spu(int $SpuId,int $UserId,int $ClientIp,bool $useReader = false) : InvokerCopyToSpuOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`copy_to_spu` (%d,%d,%d,@ReturnValue,@ReturnMessage)"
			,$SpuId,$UserId,$ClientIp
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
			throw new \Exception("运行存储过程 copy_to_spu 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 copy_to_spu 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 copy_to_spu 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerCopyToSpuOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 快捷下单 <br>
	 * 调用存储过程 fast_purchase ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$ShareId		第2个参数
	 * @var	int	$SkuId		第3个参数
	 * @var	int	$BuyQty		第4个参数
	 * @var	int	$UserAddressId		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @var	int	$SelUseMoney		第7个参数
	 * @var	int	$SelUseProfit		第8个参数
	 * @var	int	$SelUseRecruit		第9个参数
	 * @var	int	$SelUseTicket		第10个参数
	 * @var	string	$TicketId		第11个参数
	 * @var	string	$CommentUser		第12个参数
	 * @returns InvokerFastPurchaseOutput
	 */
	public function fast_purchase(int $UserId,int $ShareId,int $SkuId,int $BuyQty,int $UserAddressId,int $ClientIp,int $SelUseMoney,int $SelUseProfit,int $SelUseRecruit,int $SelUseTicket,string $TicketId,string $CommentUser,bool $useReader = false) : InvokerFastPurchaseOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`fast_purchase` (%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,'%s','%s',@OrderId,@ReturnValue,@ReturnMessage)"
			,$UserId,$ShareId,$SkuId,$BuyQty,$UserAddressId,$ClientIp,$SelUseMoney,$SelUseProfit,$SelUseRecruit,$SelUseTicket,parent::$writer->escape_string($TicketId)
				,parent::$writer->escape_string($CommentUser)
				
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
			throw new \Exception("运行存储过程 fast_purchase 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @OrderId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @OrderId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 fast_purchase 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 fast_purchase 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerFastPurchaseOutput();
		$obj->OrderId = $dat['@OrderId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 导SKU <br>
	 * 调用存储过程 import_sku ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$ExtSkuId		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerImportSkuOutput
	 */
	public function import_sku(int $UserId,int $ExtSkuId,int $ClientIp,bool $useReader = false) : InvokerImportSkuOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`import_sku` (%d,%d,%d,@SkuId,@ReturnValue,@ReturnMessage)"
			,$UserId,$ExtSkuId,$ClientIp
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
			throw new \Exception("运行存储过程 import_sku 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @SkuId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @SkuId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 import_sku 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 import_sku 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerImportSkuOutput();
		$obj->SkuId = $dat['@SkuId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 确认订单 <br>
	 * 调用存储过程 order_accept ()
	 * @var	string	$OrderId		第1个参数
	 * @var	int	$ClientIp		第2个参数
	 * @returns InvokerOrderAcceptOutput
	 */
	public function order_accept(string $OrderId,int $ClientIp,bool $useReader = false) : InvokerOrderAcceptOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`order_accept` ('%s',%d,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($OrderId)
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
			throw new \Exception("运行存储过程 order_accept 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 order_accept 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 order_accept 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerOrderAcceptOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 购物车 <br>
	 * 调用存储过程 order_add ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$UserAddressId		第2个参数
	 * @var	int	$UserBalance		第3个参数
	 * @var	int	$UserCommission		第4个参数
	 * @returns InvokerOrderAddOutput
	 */
	public function order_add(int $UserId,int $UserAddressId,int $UserBalance,int $UserCommission,bool $useReader = false) : InvokerOrderAddOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`order_add` (%d,%d,%d,%d,@PrimaryOrderId,@ReturnValue,@ReturnMessage)"
			,$UserId,$UserAddressId,$UserBalance,$UserCommission
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
			throw new \Exception("运行存储过程 order_add 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @PrimaryOrderId,@ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @PrimaryOrderId,@ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 order_add 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 order_add 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerOrderAddOutput();
		$obj->PrimaryOrderId = $dat['@PrimaryOrderId'];
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 佣金计算返现 <br>
	 * 调用存储过程 profit_reckon ()
	 * @var	string	$OrderId		第1个参数
	 * @var	int	$ClientIp		第2个参数
	 * @returns InvokerProfitReckonOutput
	 */
	public function profit_reckon(string $OrderId,int $ClientIp,bool $useReader = false) : InvokerProfitReckonOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`profit_reckon` ('%s',%d,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($OrderId)
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
			throw new \Exception("运行存储过程 profit_reckon 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 profit_reckon 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 profit_reckon 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerProfitReckonOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 价格适配 <br>
	 * 调用存储过程 resync_price ()
	 * @var	int	$SkuId		第1个参数
	 * @returns InvokerResyncPriceOutput
	 */
	public function resync_price(int $SkuId,bool $useReader = false) : InvokerResyncPriceOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`resync_price` (%d,@ReturnValue,@ReturnMessage)"
			,$SkuId
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
			throw new \Exception("运行存储过程 resync_price 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 resync_price 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 resync_price 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerResyncPriceOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 同步SKU <br>
	 * 调用存储过程 resync_sku ()
	 * @var	int	$ExtSkuId		第1个参数
	 * @var	float	$NewPriceBase		第2个参数
	 * @var	float	$NewPriceRef		第3个参数
	 * @var	int	$NewDepot		第4个参数
	 * @returns InvokerResyncSkuOutput
	 */
	public function resync_sku(int $ExtSkuId,float $NewPriceBase,float $NewPriceRef,int $NewDepot,bool $useReader = false) : InvokerResyncSkuOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`resync_sku` (%d,%f,%f,%d,@ReturnValue,@ReturnMessage)"
			,$ExtSkuId,$NewPriceBase,$NewPriceRef,$NewDepot
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
			throw new \Exception("运行存储过程 resync_sku 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 resync_sku 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 resync_sku 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerResyncSkuOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 取消订单 <br>
	 * 调用存储过程 supplier_close_order ()
	 * @var	string	$OrderId		第1个参数
	 * @var	int	$UserId		第2个参数
	 * @var	int	$ClientIp		第3个参数
	 * @returns InvokerSupplierCloseOrderOutput
	 */
	public function supplier_close_order(string $OrderId,int $UserId,int $ClientIp,bool $useReader = false) : InvokerSupplierCloseOrderOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`supplier_close_order` ('%s',%d,%d,@ReturnValue,@ReturnMessage)"
			,parent::$writer->escape_string($OrderId)
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
			throw new \Exception("运行存储过程 supplier_close_order 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 supplier_close_order 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 supplier_close_order 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerSupplierCloseOrderOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

	/**
	 * 卡充VIP补单 <br>
	 * 调用存储过程 vip_order ()
	 * @var	int	$UserId		第1个参数
	 * @var	int	$SkuId		第2个参数
	 * @var	string	$OrderId		第3个参数
	 * @var	int	$UserAddressId		第4个参数
	 * @var	string	$CommentAdmin		第5个参数
	 * @var	int	$ClientIp		第6个参数
	 * @returns InvokerVipOrderOutput
	 */
	public function vip_order(int $UserId,int $SkuId,string $OrderId,int $UserAddressId,string $CommentAdmin,int $ClientIp,bool $useReader = false) : InvokerVipOrderOutput {
		
		$ret = null;
		$invoke_sql = sprintf(
			"CALL `yuemi_sale`.`vip_order` (%d,%d,'%s',%d,'%s',%d,@ReturnValue,@ReturnMessage)"
			,$UserId,$SkuId,parent::$writer->escape_string($OrderId)
				,$UserAddressId,parent::$writer->escape_string($CommentAdmin)
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
			throw new \Exception("运行存储过程 vip_order 失败，数据库系统没有响应。");
		}
		if($useReader){
			$ret = parent::$reader->query("SELECT @ReturnValue,@ReturnMessage");
		}else{
			$ret = parent::$writer->query("SELECT @ReturnValue,@ReturnMessage");
		}
		if($ret === null || $ret === false || ! ($ret instanceof \mysqli_result)){
			$this->Tracer->error("\tNO OUTPUT");
			throw new \Exception("运行存储过程 vip_order 失败，获取返回值失败。");
		}
		$dat = $ret->fetch_array(MYSQLI_ASSOC);
		$ret->close();
		if($dat === null || $dat === false || empty($dat)){
			$this->Tracer->error("\tNO DATA");
			throw new \Exception("运行存储过程 vip_order 失败，数据库系统没有返回值。");
		}
		foreach($dat as $k => $v){
			$this->Tracer->debug("\t$k = $v");
		}
		$obj = new InvokerVipOrderOutput();
		$obj->ReturnValue = $dat['@ReturnValue'];
		$obj->ReturnMessage = $dat['@ReturnMessage'];
		return $obj;
	}

}


final class InvokerCancleOrderOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerCartAddOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $CartId;
	
}
	
final class InvokerCartDecOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerCartPurchaseOutput extends \Ziima\Data\Output {
	
	/**
	 * @var string
	 */
	public $PrimaryOrderId;
	
}
	
final class InvokerCloseOrderOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerCopyEskuToSkuOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerCopyEspuToSpuOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerCopyToSkuOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerCopyToSpuOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerFastPurchaseOutput extends \Ziima\Data\Output {
	
	/**
	 * @var string
	 */
	public $OrderId;
	
}
	
final class InvokerImportSkuOutput extends \Ziima\Data\Output {
	
	/**
	 * @var int
	 */
	public $SkuId;
	
}
	
final class InvokerOrderAcceptOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerOrderAddOutput extends \Ziima\Data\Output {
	
	/**
	 * @var string
	 */
	public $PrimaryOrderId;
	
}
	
final class InvokerProfitReckonOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerResyncPriceOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerResyncSkuOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerSupplierCloseOrderOutput extends \Ziima\Data\Output {
	
}
	
final class InvokerVipOrderOutput extends \Ziima\Data\Output {
	
}
	