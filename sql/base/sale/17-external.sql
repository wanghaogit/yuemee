/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-3-14
 */

-- 上游供应商接口数据表

DROP TABLE IF EXISTS `ext_gongyun_catagory`;
CREATE TABLE `ext_gongyun_catagory` (
	`id`				INT UNSIGNED 			NOT NULL		AUTO_INCREMENT			COMMENT '分类ID',
	`parent_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '上级ID',
	`name`				VARCHAR(32)				NOT NULL								COMMENT '分类名称',

	`map_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '映射阅米内部分类ID',

	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`),
	KEY `map_id` (`map_id`)
)	Engine=MyISAM
    AUTO_INCREMENT=1
	DEFAULT CHARACTER SET=utf8 
	COLLATE=utf8_general_ci
	COMMENT='贡云商品分类';

DROP TABLE IF EXISTS `ext_neigou_catagory`;
CREATE TABLE `ext_neigou_catagory` (
	`id`				INT UNSIGNED 			NOT NULL			AUTO_INCREMENT		COMMENT '分类ID',
	`parent_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '上级ID',
	`name`				VARCHAR(32)				NOT NULL								COMMENT '分类名称',

	`map_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '映射阅米内部分类ID',

	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`),
	KEY `map_id` (`map_id`)
)	Engine=MyISAM
    AUTO_INCREMENT=1
	DEFAULT CHARACTER SET=utf8 
	COLLATE=utf8_general_ci
	COMMENT='内购商品分类';

DROP TABLE IF EXISTS `ext_supplier`;
CREATE TABLE `ext_supplier` (
	`id`				INT UNSIGNED 			NOT NULL	AUTO_INCREMENT				COMMENT '供应商ID',
	`supplier_id`		INT UNSIGNED			NOT NULL								COMMENT '外部供应商ID，2=内购，3=贡云',

	`name`				VARCHAR(32)				NOT NULL								COMMENT '供应商名称',

	`map_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '映射阅米内部供应商ID',

	PRIMARY KEY (`id`),
	KEY `supplier_id` (`supplier_id`),
	KEY `map_id` (`map_id`)
)	Engine=MyISAM
    AUTO_INCREMENT=1
	DEFAULT CHARACTER SET=utf8 
	COLLATE=utf8_general_ci
	COMMENT='外部供应商';

DROP TABLE IF EXISTS `ext_spu`;
CREATE TABLE `ext_spu` (
	`id`				INT UNSIGNED 			NOT NULL	AUTO_INCREMENT				COMMENT '外部SPUID',
	`supplier_id`		INT UNSIGNED			NOT NULL								COMMENT '外部供应商ID，2=内购，3=贡云',
	`bn`				VARCHAR(24)				NOT NULL								COMMENT '商品bn',
	`ext_cat_id`		INT UNSIGNED			NOT NULL								COMMENT '关联分类ID',
	`brand_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '品牌ID',

	`spu_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '内部SPUID',
	`catagory_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '内部分类ID',

	`title`				VARCHAR(128)			NOT NULL								COMMENT '商品标题',
	`price_base`		NUMERIC(16,4)			NOT NULL								COMMENT '成本价',

	`video`				VARCHAR(1024)			NOT NULL DEFAULT ''						COMMENT '视频URL',
	`intro`				TEXT					NULL									COMMENT '描述内容',
	`specs`				TEXT					NULL									COMMENT '规格定义',

	`status`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '外部SPU状态，0无效,1有效',

	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`update_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '更新时间',
	PRIMARY KEY (`id`),
	KEY `supplier_id` (`supplier_id`),
	KEY `ext_cat_id` (`ext_cat_id`),
	KEY `spu_id` (`spu_id`),
	KEY `bn` (`bn`)
)	Engine=MyISAM
    AUTO_INCREMENT=1
	DEFAULT CHARACTER SET=utf8 
	COLLATE=utf8_general_ci
	COMMENT='外部SPU';

DROP TABLE IF EXISTS `ext_sku`;
CREATE TABLE `ext_sku` (
	`id`				INT UNSIGNED 			NOT NULL	AUTO_INCREMENT				COMMENT '外部SPUID',
	`supplier_id`		INT UNSIGNED			NOT NULL								COMMENT '外部供应商ID，2=内购，3=贡云',
	`bn`				VARCHAR(24)				NOT NULL								COMMENT '商品bn',
	`ext_spu_id`		INT UNSIGNED			NOT NULL								COMMENT '关联外部SPUID',

	`sku_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '内部SKUID',

	`name`				VARCHAR(128)			NOT NULL								COMMENT '货品名称',
	`weight`			NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '货品重量',
	`barcode`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '货品条形码',

	`price_base`		NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '成本价',
	`price_ref`			NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '货品市场价',
	`stock`				INT						NOT NULL DEFAULT 0						COMMENT '实时库存数量',

	`video`				VARCHAR(1024)			NOT NULL DEFAULT ''						COMMENT '视频URL',
	`intro`				TEXT					NULL									COMMENT '描述内容',
	`spec`				TEXT					NULL									COMMENT '货品规格',

	`status`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '外部SKU状态，0无效,1有效',
	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`update_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '更新时间',
	PRIMARY KEY (`id`),
	KEY `supplier_id` (`supplier_id`),
	KEY `ext_spu_id` (`ext_spu_id`),
	KEY `sku_id` (`sku_id`),
	KEY `bn` (`bn`)
)	Engine=MyISAM
    AUTO_INCREMENT=1
	DEFAULT CHARACTER SET=utf8 
	COLLATE=utf8_general_ci
	COMMENT='外部SKU';

DROP TABLE IF EXISTS `ext_spu_material`;
CREATE TABLE `ext_spu_material` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '素材ID',
	`ext_spu_id`	INT UNSIGNED			NOT NULL								COMMENT '外部SPUID',
	`type`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片类型：0商品图,1内容图',

	`source_url`	VARCHAR(1024)			NOT NULL DEFAULT ''						COMMENT '源图路径',
	`source_hash`	VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '原图HASH值',

	`file_fmt`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '文件格式：0JPG,1PNG',
	`file_name`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '文件名',
	`file_size`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '文件大小：字节',
	`file_path`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '保存路径',
	`file_url`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '访问路径',

	`image_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片宽度',
	`image_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片高度',

	`thumb_path`	VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '缩略图路径',
	`thumb_url`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '缩略图路径',
	`thumb_size`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '缩略图大小：字节',
	`thumb_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图宽度',
	`thumb_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图高度',

	`is_default`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否默认素材',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '素材状态 0待下载,1下载失败,2下载成功',

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`update_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`audit_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核时间',

	PRIMARY KEY (`id`),
	UNIQUE KEY `source_hash` (`source_hash`),
	KEY `ext_spu_id` (`ext_spu_id`),
	KEY `type` (`type`),
	KEY `status` (`status`)
) Engine=InnoDB
  AUTO_INCREMENT=1
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='外部SPU素材';

DROP TABLE IF EXISTS `ext_sku_material`;
CREATE TABLE `ext_sku_material` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '素材ID',
	`ext_sku_id`	INT UNSIGNED			NOT NULL								COMMENT '外部SKUID',
	`type`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片类型：0商品图,1内容图',

	`source_url`	VARCHAR(1024)			NOT NULL DEFAULT ''						COMMENT '源图路径',
	`source_hash`	VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '原图HASH值',

	`file_fmt`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '文件格式：0JPG,1PNG',
	`file_name`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '文件名',
	`file_size`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '文件大小：字节',
	`file_path`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '保存路径',
	`file_url`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '访问路径',

	`image_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片宽度',
	`image_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片高度',

	`thumb_path`	VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '缩略图路径',
	`thumb_url`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '缩略图路径',
	`thumb_size`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '缩略图大小：字节',
	`thumb_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图宽度',
	`thumb_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图高度',

	`is_default`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否默认素材',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '素材状态 0待下载,1下载失败,2下载成功',

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`update_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`audit_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核时间',

	PRIMARY KEY (`id`),
	UNIQUE KEY `source_hash` (`source_hash`),
	KEY `ext_sku_id` (`ext_sku_id`),
	KEY `type` (`type`),
	KEY `status` (`status`)
) Engine=InnoDB
  AUTO_INCREMENT=1
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='外部SKU素材';

/*
	严重程度：
	0	标题小范围,内容变化,主图变化,素材变化
	1	市场价上调,成本价下调,市场价小幅下调，成本价小幅上调，标题大范围变化
	2	市场价下调，成本价上调
	3	亏本，缺货，下架
*/
DROP TABLE IF EXISTS `ext_sku_changes`;
CREATE TABLE `ext_sku_changes` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`ext_sku_id`	INT UNSIGNED			NOT NULL								COMMENT '外部SKUID',
	
	`field`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '变化字段:0整个,1标题,2内容,3内容图,4素材图,5规格,6重量,7成本价,8市场价,9库存,10其它',
	`old_value`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '原始值',
	`new_value`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '变更值',

	`change_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '变更时间 @TIMESTAMP-CREATE',
	PRIMARY KEY (`id`),
	KEY `ext_sku_id` (`ext_sku_id`),
	KEY `field` (`field`)
) Engine=InnoDB
  AUTO_INCREMENT=1
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='外部SPU数据变更';