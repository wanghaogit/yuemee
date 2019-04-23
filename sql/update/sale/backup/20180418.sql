/**
 * 订单数据表调整
 * Author:  eglic
 * Created: 2018-4-17
 */

DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
	`id` 			CHAR(12)				NOT NULL								COMMENT '订单ID',
	`user_id` 		INT UNSIGNED 			NOT NULL								COMMENT '用户ID',
	`inviter_id` 	INT UNSIGNED 			NOT NULL								COMMENT '邀请人ID',
	`inviter_feed` 	INT UNSIGNED 			NOT NULL								COMMENT '裂变种子号',

	`is_primary`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是主订单',
	`depend_id`		CHAR(12)				NOT NULL DEFAULT ''						COMMENT '主订单ID',
	`supplier_id` 	INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '拆分供应商ID',

	`qty`			INT UNSIGNED			NOT NULL								COMMENT '货品总数量(包括子订单)',
	`money`			NUMERIC(16,4)			NOT NULL								COMMENT '订单总价格(包括子订单)',

	`pay_money`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '余额支付部分(包括子订单)',
	`pay_profit`	NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金支付部分(包括子订单)',
	`pay_ticket`	NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '购物券支付部分(包括子订单)',
	`pay_online`	NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '在线支付部分(包括子订单)',

	`pay_serial`	VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '支付回单号',
	`pay_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '支付时间',

	`address_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '订单收货地址ID',
	`addr_region`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '地区ID',
	`addr_detail`	VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '详细地址',
	`addr_name`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '联系人',
	`addr_mobile`	VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '联系电话',

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`trans_id`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '物流单号',
	`trans_fin`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否已确认',
	`trans_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '确认收货时间',

	`status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '订单状态：参见文档',

	`update_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '订单更新时间',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `depend_id` (`depend_id`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='订单';

DROP TABLE IF EXISTS `order_item`;
CREATE TABLE `order_item` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`order_id` 		CHAR(12)				NOT NULL								COMMENT '订单ID',
	`shelf_id` 		INT UNSIGNED 			NOT NULL								COMMENT '上架ID',
	`sku_id` 		INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT 'SKUID',
	`spu_id` 		INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT 'SKUID',
	`catagory_id`	INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '分类ID',
	`supplier_id`	INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '供应商ID',

	`qty`			INT UNSIGNED			NOT NULL								COMMENT '货品总数量',
	`price`			NUMERIC(16,4)			NOT NULL								COMMENT '结算单价',
	`money`			NUMERIC(16,4)			NOT NULL								COMMENT '结算总价',

	`title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '商品标题',
	`picture`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '商品小图',

	PRIMARY KEY (`id`),
	KEY `order_id` (`order_id`),
	KEY `sku_id` (`sku_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='订单详情';

DROP TABLE IF EXISTS `tmp_neigou_bn`;


DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
	`id` 				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id` 			INT UNSIGNED 			NOT NULL								COMMENT '用户ID',
	`shelf_id` 			INT UNSIGNED 			NOT NULL								COMMENT '货架ID',
	
	-- 邀请信息
	`inviter_id` 		INT UNSIGNED 			NOT NULL								COMMENT '邀请人ID',
	`inviter_feed`	 	INT UNSIGNED 			NOT NULL								COMMENT '裂变种子号',

	-- 冗余ID信息，必填
	`sku_id` 			INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT 'SKUID',
	`spu_id` 			INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT 'SPUID',
	`catagory_id`		INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '分类ID',
	`brand_id`			INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '品牌ID',
	`supplier_id`		INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '供应商ID',

	-- 外部供应商冗余信息
	`ext_sku_id`		INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '外部SKUID',
	`ext_sku_bn`		VARCHAR(24)				NOT NULL DEFAULT ''						COMMENT '外部SKUBN',
	`ext_spu_id`		INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '外部SPUID',
	`ext_spu_bn`		VARCHAR(24)				NOT NULL DEFAULT ''						COMMENT '外部SPUBN',
	`ext_supplier_id`	INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT '外部供应商ID',

	-- Shelf相关信息，Titile，图片，价格
	`shelf_title`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '加入购物车时的商品标题',
	`shelf_price`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '加入购物车时的商品价格',
	`shelf_thumb`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '加入购物车时的商品缩略图',
	
	`qty`				INT UNSIGNED			NOT NULL								COMMENT '下单数量',

	`is_checked`		TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '选中状态',
	
	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `sku_id` (`sku_id`),
	KEY `supplier_id` (`supplier_id`),
	KEY `is_checked` (`is_checked`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='购物车';

TRUNCATE TABLE `share_template`;
INSERT INTO `share_template` VALUES (1, '3图', '/template/share/share_3.png', '/template/share/share_3.png', 750, 549, 1, '', '', '144,28,587,147,50,30,#fff', '3,16,206,717,242,0', '1,645,503,5,#000', '1,26,25,78,78', '1,318,505,12,#ccc', '0,268,144,12,#000', 1, 20180418142153, 1, 2130706433);
INSERT INTO `share_template` VALUES (2, '6图', '/template/share/share_6.png', '/template/share/share_6.png', 750, 819, 1, '', '', '140,37,590,125,12,30,#fff', '6,17,207,715,510,0', '1,595,765,5,#000', '1,26,25,78,78', '1,345,770,12,#ccc', '0,268,144,12,#000', 1, 20180418142420, 1, 2130706433);
INSERT INTO `share_template` VALUES (3, '9图', '/template/share/share_9.png', '/template/share/share_9.png', 750, 1078, 1, '', '', '140,25,589,112,92,30,#fff', '9,16,208,716,750,0', '1,630,1032,5,#000', '1,26,25,78,78', '1,324,1040,12,#ccc', '0,577,102,12,#000', 1, 20180418141735, 1, 2130706433);
