/**
 * 品类调整
 */

DROP TABLE IF EXISTS `ext_sku_changes`;
CREATE TABLE `ext_sku_changes` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`ext_sku_id`		INT UNSIGNED			NOT NULL								COMMENT 'SKUID',
	`supplier_id`		INT UNSIGNED			NOT NULL								COMMENT '供应商ID',

	`chg_title`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否变更标题',
	`old_title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '旧标题',
	`new_title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '新标题',

	`chg_catagory`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否变更品类',
	`old_catagory`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '旧分类',
	`new_catagory`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '新分类',

	`chg_price_base`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否变更成本价',
	`old_price_base`	NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '旧价格',
	`new_price_base`	NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '新价格',

	`chg_price_ref`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否变更参考价',
	`old_price_ref`		NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '旧价格',
	`new_price_ref`		NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '新价格',

	`chg_ratio`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否变更毛利',
	`old_ratio`			NUMERIC(8,6)			NOT NULL DEFAULT 0						COMMENT '旧毛利',
	`new_ratio`			NUMERIC(8,6)			NOT NULL DEFAULT 0						COMMENT '新毛利',

	`chg_depot`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否变更库存',
	`old_depot`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '旧库存',
	`new_depot`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '新库存',

	`message`			VARCHAR(512)			NOT NULL DEFAULT ''						COMMENT '备注消息',

	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	PRIMARY KEY (`id`),
	KEY `ext_sku_id` (`ext_sku_id`),
	KEY `supplier_id` (`supplier_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='外部SKU变化通知';
