/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */
DROP TABLE IF EXISTS `spu`;
CREATE TABLE `spu` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT 'SPUID',
	`catagory_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '分类ID',
	`supplier_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '供应商ID',
	`brand_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '品牌ID',

	`title`			VARCHAR(128)			NOT NULL								COMMENT '商品标题',
	`barcode`		VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '条码',
	`serial`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '货号',

	`quantity`		INT UNSIGNED			NOT NULL								COMMENT '实时库存数量',
	`price_base`	NUMERIC(16,4)			NOT NULL								COMMENT '成本价',
	`price_market`	NUMERIC(16,4)			NOT NULL								COMMENT '市场价',
	`price_sale`	NUMERIC(16,4)			NOT NULL								COMMENT '平台价',
	`price_rebate`	NUMERIC(16,4)			NOT NULL								COMMENT '佣金额度',
	`weight`		NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '单位重量（克）',
	`unit`			varchar(32)				NOT NULL DEFAULT ''						COMMENT '单位',

	`is_virtual`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否虚拟商品',
	`is_gift`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否赠品',
	`is_bind`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否捆绑（非主）',
	`is_zhiti`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否自提',
	`p_order`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '分类内部排序，DESC',

	`video`			VARCHAR(1024)			NOT NULL DEFAULT ''						COMMENT '视频URL',
	`intro`			TEXT					NULL									COMMENT '描述内容',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT 'SPU状态 0下架,1上架',

	`online_time`	DATETIME				NULL									COMMENT '预定上架时间',
	`offline_time`	DATETIME				NULL									COMMENT '预定下架时间',

	`create_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`	DATETIME				NULL									COMMENT '创建时间',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`update_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '更新人',
	`update_time`	DATETIME				NULL									COMMENT '更新时间',
	`update_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '更新IP',

	`audit_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`	DATETIME				NULL									COMMENT '审核时间',
	`audit_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',

	PRIMARY KEY (`id`),
	KEY `catagory_id` (`catagory_id`),
	KEY `supplier_id` (`supplier_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='SPU';

DROP TABLE IF EXISTS `sku`;
CREATE TABLE `sku` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT 'SKUID',
	`spu_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT 'SPUID',
	`catagory_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '分类ID',
	`supplier_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '供应商ID',

	`name`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '货品名称',
	`title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '标题',
	`barcode`		VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '条码',
	`serial`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '货号',
	`weight`		NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '单位重量（克）',
	`unit`			varchar(32)				NOT NULL DEFAULT ''						COMMENT '单位',
	`quantity`		INT UNSIGNED			NOT NULL								COMMENT '实时库存数量',

	`price_base`	NUMERIC(16,4)			NOT NULL								COMMENT '成本价',
	`price_sale`	NUMERIC(16,4)			NOT NULL								COMMENT '平台价',
	`price_ref`		NUMERIC(16,4)			NOT NULL								COMMENT '对标价',
	`price_market`	NUMERIC(16,4)			NOT NULL								COMMENT '市场价',
	`price_rebate`	NUMERIC(16,4)			NOT NULL								COMMENT '佣金额度',

	`video`			VARCHAR(1024)			NOT NULL DEFAULT ''						COMMENT '视频URL',
	`intro`			TEXT					NULL									COMMENT '描述内容',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT 'SKU状态 0下架,1上架',

	`create_user`	INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`	DATETIME				NULL									COMMENT '创建时间',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`update_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '更新人',
	`update_time`	DATETIME				NULL									COMMENT '更新时间',
	`update_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '更新IP',

	`audit_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`	DATETIME				NULL									COMMENT '审核时间',
	`audit_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',
	PRIMARY KEY (`id`),
	KEY `spu_id` (`spu_id`),
	KEY `catagory_id` (`catagory_id`),
	KEY `supplier_id` (`supplier_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='SKU';
