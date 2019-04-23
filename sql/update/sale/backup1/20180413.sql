/**
 * 品类调整
 */

ALTER TABLE `ext_spu` ADD COLUMN `lo_status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '内容本地化状态：0待处理,1失败,2成功' AFTER `intro`;
ALTER TABLE `ext_spu` ADD COLUMN `lo_error` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '内容本地化错误次数' AFTER `lo_status`;
ALTER TABLE `ext_spu` ADD COLUMN `lo_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '内容本地化处理时间' AFTER `lo_error`;

ALTER TABLE `spu` ADD COLUMN `spec_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '规格ID' AFTER `specs`;
ALTER TABLE `sku` ADD COLUMN `spec_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '规格ID' AFTER `specs`;

ALTER TABLE `rebate` ADD COLUMN `status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0待确认,1已取消,2已确认,3已结算' AFTER `director_profit`;
ALTER TABLE `rebate` ADD COLUMN `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间 @TIMESTAMP-CREATE' AFTER `status`;
ALTER TABLE `rebate` ADD COLUMN `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间 @TIMESTAMP-UPDATE' AFTER `create_time`;
ALTER TABLE `rebate` ADD KEY `status` (`status`);

DROP TABLE IF EXISTS `sku_changes`;
DROP TABLE IF EXISTS `ext_sku_changes`;

CREATE TABLE `sku_changes` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`sku_id`			INT UNSIGNED			NOT NULL								COMMENT 'SKUID',
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

	`chg_price_sale`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否变更阅米价',
	`old_price_sale`	NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '旧价格',
	`new_price_sale`	NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '新价格',

	`chg_depot`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否变更库存',
	`old_depot`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '旧库存',
	`new_depot`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '新库存',

	`status`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '状态：0待审,1已审,2拒绝',
	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`audit_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核时间',
	`audit_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',
	PRIMARY KEY (`id`),
	KEY `sku_id` (`sku_id`),
	KEY `supplier_id` (`supplier_id`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='SKU数据变化工单';

DROP TABLE IF EXISTS `spec_define`;
CREATE TABLE `spec_define` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '规格项ID',
	`supplier_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '供应商ID',
	`name`				VARCHAR(32)				NOT NULL								COMMENT '规格名称',

	PRIMARY KEY (`id`),
	KEY `supplier_id` (`supplier_id`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='规格定义 @NOENTITY';

DROP TABLE IF EXISTS `spec_value`;
CREATE TABLE `spec_value` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '规格值ID',
	`spec_id`			INT UNSIGNED			NOT NULL								COMMENT '规格项ID',
	`name`				VARCHAR(32)				NOT NULL								COMMENT '规格值',

	PRIMARY KEY (`id`),
	KEY `spec_id` (`spec_id`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='规格值 @NOENTITY';
