/**
 * 订单售后
 * Author:  eglic
 * Created: 2018-4-17
 */

DROP TABLE IF EXISTS `order_afs`;
CREATE TABLE `order_afs` (
	`id` 			CHAR(12)				NOT NULL								COMMENT '售后ID',
	`user_id` 		INT UNSIGNED 			NOT NULL								COMMENT '用户ID',
	`item_id` 		INT UNSIGNED			NOT NULL								COMMENT '订单详情ID',
	`order_id` 		CHAR(12)				NOT NULL DEFAULT ''						COMMENT '订单ID',

	`shelf_id` 		INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '上架ID',
	`sku_id` 		INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT 'SKUID',
	`spu_id` 		INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT 'SKUID',
	`supplier_id`	INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '供应商ID',

	`qty`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '退货数量',
	`price`			NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '退货价格',
	`total`			NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '退货总价',

	`title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '商品标题',
	`picture`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '商品小图',

	-- 申请信息
	`req_type`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '申请方式：1退货退款,2补发,3部分退款,4全额退款',
	`req_reason`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '申请理由：参见文档',
	`req_money`		NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '申请退款金额',
	`req_message`	VARCHAR(512)			NOT NULL DEFAULT ''						COMMENT '申请消息',
	`req_trans`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '退货物流单号',
	`req_addr_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '订单收货地址ID',
	`req_addr_rgn`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '地区ID',
	`req_addr`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '详细地址',
	`req_name`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '联系人',
	`req_mobile`	VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '联系电话',

	-- 补发信息
	`fix_id`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '补发理由：参见文档',
	`fix_message`	VARCHAR(512)			NOT NULL DEFAULT ''						COMMENT '补发消息',
	`fix_trans`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '补发物流单号',
	
	-- 退款信息
	`bak_money`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '实际退款金额',
	`bak_tally`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '退款流水记录号',


	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '售后订单状态：0申请,1拒绝,2通过,3完成',
	
	`update_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '订单更新时间',
	PRIMARY KEY (`id`),
	UNIQUE KEY `item_id` (`item_id`),
	KEY `order_id` (`order_id`),
	KEY `user_id` (`user_id`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='订单售后';
