/**
 * SKU/SHELF系统
 * Author:  eglic
 * Created: 2018-4-17
 */

ALTER TABLE `spu` ADD COLUMN `att_refund` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否支持退换货' AFTER `intro`;
ALTER TABLE `sku` ADD COLUMN `att_refund` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否支持退换货' AFTER `intro`;
ALTER TABLE `sku` ADD COLUMN `att_only_app` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否仅支持APP购买' AFTER `att_refund`;
ALTER TABLE `sku` DROP COLUMN `name`;
ALTER TABLE `sku` DROP COLUMN `update_user`;
ALTER TABLE `sku` DROP COLUMN `update_time`;
ALTER TABLE `sku` DROP COLUMN `update_from`;
ALTER TABLE `sku` DROP COLUMN `price_rebate`;
ALTER TABLE `sku` CHANGE COLUMN `quantity` `depot` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '实时库存';
ALTER TABLE `sku` ADD COLUMN `price_inv` NUMERIC(16,4) NOT NULL DEFAULT 0.0 COMMENT '有邀请码会员的价格' AFTER `price_sale`;
ALTER TABLE `sku` ADD COLUMN `price_vip` NUMERIC(16,4) NOT NULL DEFAULT 0.0 COMMENT '无邀请码会员的价格' AFTER `price_inv`;
ALTER TABLE `sku` ADD COLUMN `coin_style` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '赠送阅币方式：0不赠送,1按次,2按件' AFTER `price_ref`;
ALTER TABLE `sku` ADD COLUMN `coin_buyer` NUMERIC(16,8) NOT NULL DEFAULT 0.0 COMMENT '购买者赠送阅币' AFTER `coin_style`;
ALTER TABLE `sku` ADD COLUMN `coin_inviter` NUMERIC(16,8) NOT NULL DEFAULT 0.0 COMMENT '分享者赠送阅币' AFTER `coin_buyer`;
ALTER TABLE `sku` ADD COLUMN `limit_style` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '限购类型：0不限购,1按人头限购,2按地址限购' AFTER `coin_inviter`;
ALTER TABLE `sku` ADD COLUMN `limit_size` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '限购数量' AFTER `limit_style`;
ALTER TABLE `sku` ADD COLUMN `online_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '预定上架时间' AFTER `intro`;
ALTER TABLE `sku` ADD COLUMN `offline_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '预定下架时间' AFTER `online_time`;
ALTER TABLE `sku` CHANGE COLUMN `status` `status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品状态：0待审,1打回,2通过,3下架,4删除';

ALTER TABLE `order` ADD COLUMN `trans_detail` TEXT NULL COMMENT '物流信息列表' AFTER `trans_time`;
DROP TABLE IF EXISTS `shelf`;
DROP TABLE IF EXISTS `shelf_material`;

DROP TABLE IF EXISTS `rebate`;
CREATE TABLE `rebate` (
	`item_id`			INT UNSIGNED			NOT NULL								COMMENT '明细ID',
	`order_id`			CHAR(12)				NOT NULL								COMMENT '订单ID',
	`buyer_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '购买者ID',
	`buyer_vip`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '购买者VIP状态',
	`share_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '分享ID',
	`share_user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '分享会员ID',
	`cheif_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '当时的总监ID',
	`director_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '当时的总经理ID',
	`sku_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT 'SKUID',

	-- 记录现场
	`time_create`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`time_finish`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '订单完成时间(T)',
	`spu_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT 'SPUID',

	-- 记录支付
	`pay_count`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '购买数量',
	`pay_total`			NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '支付总额',
	`pay_money`			NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '支付余额',
	`pay_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '支付佣金',
	`pay_ticket`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '支付券',
	`pay_online`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '在线支付',
	
	-- 折合返利
	`total_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '总可支配佣金',
	`system_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '平台返利金额',
	`self_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '自己返利金额',
	`share_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '分享返利金额',

	-- 总监返利
	`cheif_ratio`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '经理返利比例',
	`cheif_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '总监返利金额',

	-- 经理返利
	`director_ratio`	NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '经理返利比例',
	`director_profit`	NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '经理返利金额',

	
	UNIQUE KEY `item_id` (`item_id`),
	KEY `buyer_id` (`buyer_id`),
	KEY `cheif_id` (`cheif_id`),
	KEY `director_id` (`director_id`),
	KEY `time_finish` (`time_finish`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='返利';


DROP TABLE IF EXISTS `shelf_task`;
DROP TABLE IF EXISTS `sku_task`;
CREATE TABLE `sku_task` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '任务ID',
	`sku_id` 			INT UNSIGNED 			NOT NULL								COMMENT '上架ID',

	-- 任务影响标志
	`uf_title`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：标题',
	`uf_price`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：价格',
	`uf_qty`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：库存',
	`uf_limit`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：限购',
	`uf_rbtu`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：用户返利',
	`uf_rbtv`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：VIP返利',
	`uf_coinu`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：用户阅币',
	`uf_coinv`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：VIP阅币',

	-- S0	任务启动前的状态缓存
	`s0_title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '状态0:标题',
	`s0_price`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态0:售卖价',
	`s0_qty`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '状态0:库存',
	`s0_limit`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '状态0:限购',
	`s0_rbtu`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态0:用户返利',
	`s0_rbtv`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态0:VIP返利',
	`s0_coinu`			NUMERIC(16,8)			NOT NULL DEFAULT 0						COMMENT '状态0:用户阅币',
	`s0_coinv`			NUMERIC(16,8)			NOT NULL DEFAULT 0						COMMENT '状态0:VIP阅币',

	-- S1	任务启动后替换值
	`s1_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '启动S1时间',
	`s1_title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '状态1:标题',
	`s1_price`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态1:售卖价',
	`s1_qty`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '状态1:库存',
	`s1_limit`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '状态1:限购',
	`s1_rbtu`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态1:用户返利',
	`s1_rbtv`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态1:VIP返利',
	`s1_coinu`			NUMERIC(16,8)			NOT NULL DEFAULT 0						COMMENT '状态1:用户阅币',
	`s1_coinv`			NUMERIC(16,8)			NOT NULL DEFAULT 0						COMMENT '状态1:VIP阅币',

	-- S2	任务结束后替换值
	`s2_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '启动S2时间',
	`s2_method`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '状态2操作：0恢复S0,1使用S2,2下架',
	`s2_title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '状态2:标题',
	`s2_price`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态2:售卖价',
	`s2_qty`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '状态2:库存',
	`s2_limit`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '状态2:限购',
	`s2_rbtu`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态2:用户返利',
	`s2_rbtv`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态2:VIP返利',
	`s2_coinu`			NUMERIC(16,8)			NOT NULL DEFAULT 0						COMMENT '状态2:用户阅币',
	`s2_coinv`			NUMERIC(16,8)			NOT NULL DEFAULT 0						COMMENT '状态2:VIP阅币',
	
	`status`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务状态：0待审,1拒绝,2删除,3批准,4排队,5启动,6结束,7过期',

	`create_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`audit_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核时间',
	`audit_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',

	`review_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '复核人',
	`review_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '复核时间',
	`review_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '复核IP',
	PRIMARY KEY (`id`),
	KEY `sku_id` (`sku_id`),
	KEY `s1_time` (`s1_time`),
	KEY `s2_time` (`s2_time`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='SKU定时任务';


DROP TABLE IF EXISTS `order_afs`;
CREATE TABLE `order_afs` (
	`id` 			CHAR(12)				NOT NULL								COMMENT '售后ID',
	`user_id` 		INT UNSIGNED 			NOT NULL								COMMENT '用户ID',
	`item_id` 		INT UNSIGNED			NOT NULL								COMMENT '订单详情ID',
	`order_id` 		CHAR(12)				NOT NULL DEFAULT ''						COMMENT '订单ID',

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


DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
	`id` 				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id` 			INT UNSIGNED 			NOT NULL								COMMENT '用户ID',
	`sku_id` 			INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT 'SKUID',

	-- 邀请信息
	`inviter_id` 		INT UNSIGNED 			NOT NULL								COMMENT '邀请人ID',
	`inviter_feed`	 	INT UNSIGNED 			NOT NULL								COMMENT '裂变种子号',

	-- 冗余ID信息，必填
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

	-- sku 相关信息，Titile，图片，价格
	`sku_title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '加入购物车时的商品标题',
	`sku_price`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '加入购物车时的商品价格',
	`sku_thumb`			VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '加入购物车时的商品缩略图',
	`sku_spec`			TEXT					NULL									COMMENT '加入购物车时的商品规格',
	
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


DROP TABLE IF EXISTS `share`;
CREATE TABLE `share` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '分享ID',
	`user_id` 		INT UNSIGNED 			NOT NULL								COMMENT '用户ID',
	`template_id`	INT UNSIGNED 			NOT NULL								COMMENT '使用模板ID',
	`sku_id`		INT UNSIGNED			NOT NULL								COMMENT '货架ID',	

	`title`			VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '分享文案',

	`page_url`		VARCHAR(1024)			NOT NULL DEFAULT ''						COMMENT '生成的页面URL',
	`image_url`		VARCHAR(1024)			NOT NULL DEFAULT ''						COMMENT '生成的图片URL',

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `sku_id` (`sku_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='商品分享';

DROP TABLE IF EXISTS `share_item`;
DROP TABLE IF EXISTS `share_icon`;
CREATE TABLE `share_icon` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`share_id` 		INT UNSIGNED 			NOT NULL								COMMENT '分享ID',
	`type`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '类型：1SKU素材，2SPU素材，3用户素材',
	`mat_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '素材ID',
	`mat_url`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '素材URL',
	`p_order`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '素材位置',

	PRIMARY KEY (`id`),
	KEY `share_id` (`share_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='分享商品列表';
