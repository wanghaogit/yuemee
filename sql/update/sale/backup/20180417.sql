/**
 * SKU变化通知
 * Author:  eglic
 * Created: 2018-4-17
 */

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
	
	`field`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '变化字段:1成本价,2市场价,3库存',
	`old_value`		NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '原始值',
	`new_value`		NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '变更值',

	`change_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '变更时间 @TIMESTAMP-CREATE',
	
	`status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '状态：0未读,1已读',

	`audit_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核时间',
	`audit_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',
	PRIMARY KEY (`id`),
	KEY `ext_sku_id` (`ext_sku_id`),
	KEY `field` (`field`)
) Engine=InnoDB
  AUTO_INCREMENT=1
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='外部SPU数据变更';


/*
	严重程度：
	0	标题小范围,内容变化,主图变化,素材变化
	1	市场价上调,成本价下调,市场价小幅下调，成本价小幅上调，标题大范围变化
	2	市场价下调，成本价上调
	3	亏本，缺货，下架
*/
DROP TABLE IF EXISTS `sku_changes`;
CREATE TABLE `sku_changes` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`sku_id`		INT UNSIGNED			NOT NULL								COMMENT '内部SKUID',
	
	`field`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '变化字段:1成本价,2市场价,3库存',
	`old_value`		NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '原始值',
	`new_value`		NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '变更值',

	`change_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '变更时间 @TIMESTAMP-CREATE',
	
	`status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '状态：0未读,1已读',

	`audit_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核时间',
	`audit_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',
	PRIMARY KEY (`id`),
	KEY `sku_id` (`sku_id`),
	KEY `field` (`field`)
) Engine=InnoDB
  AUTO_INCREMENT=1
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='内部SKU数据变更';