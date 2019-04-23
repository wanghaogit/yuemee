/**
 * 供应商多帐号
 */


DROP TABLE IF EXISTS `supplier_user`;
CREATE TABLE `supplier_user` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`supplier_id`	INT UNSIGNED			NOT NULL								COMMENT '供应商ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',

	`role_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '角色ID：保留扩展',

	`acl_spu`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否可以管理SPU',
	`acl_sku`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否可以管理SKU',
	`acl_depot`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否可以管理库存',
	`acl_price`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否可以管理价格',
	`acl_order`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否可以管理订单',
	`acl_trans`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否可以管理物流',
	`acl_finance`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否可以管理财务',

	`password`		CHAR(40)				NOT NULL DEFAULT ''						COMMENT '工作平台密码',
	`token`			CHAR(16)				NOT NULL DEFAULT ''						COMMENT '登陆令牌 @UNIQUE',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '用户状态 0无效,1有效',
	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	KEY `supplier_id` (`supplier_id`),
	KEY `user_id` (`user_id`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='供应商子帐号';