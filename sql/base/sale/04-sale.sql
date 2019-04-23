/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */
DROP TABLE IF EXISTS `shelf`;
CREATE TABLE `shelf` (
	`id`				INT UNSIGNED				NOT NULL AUTO_INCREMENT					COMMENT '货架ID',
	`catagory_id`		INT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '分类ID',
	`sku_id`			INT UNSIGNED				NOT NULL								COMMENT 'SKUID',

	`title`				VARCHAR(128)				NOT NULL DEFAULT ''						COMMENT '标题',

	`qty_total`			INT UNSIGNED				NOT NULL								COMMENT '总上架库存',
	`qty_left`			INT UNSIGNED				NOT NULL								COMMENT '实时库存',

	`price_sale`		NUMERIC(16,4)				NOT NULL								COMMENT '售卖价(默认售价)',
	`price_user`		NUMERIC(16,4)				NOT NULL								COMMENT '售卖价(对非VIP售价)',
	`price_vips`		NUMERIC(16,4)				NOT NULL								COMMENT '售卖价(对孤单VIP售价)',
	`price_vipi`		NUMERIC(16,4)				NOT NULL								COMMENT '售卖价(对受邀VIP售价)',
	`price_ref`			NUMERIC(16,4)				NOT NULL								COMMENT '对标价(京东价/电商价)',

	`rebate_user`		NUMERIC(16,4)				NOT NULL DEFAULT 0.0					COMMENT '用户返利金额',
	`rebate_vip`		NUMERIC(16,4)				NOT NULL DEFAULT 0.0					COMMENT 'VIP返利金额',
	`rebate_system`		NUMERIC(16,4)				NOT NULL DEFAULT 0.0					COMMENT '隐藏：平台返利金额',
	`rebate_chief`		NUMERIC(16,4)				NOT NULL DEFAULT 0.0					COMMENT '隐藏：总监返利金额',
	`rebate_director`	NUMERIC(16,4)				NOT NULL DEFAULT 0.0					COMMENT '隐藏：经理返利金额',

	`coin_style`		TINYINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '赠送阅币方式：0不赠送,1按次,2按件',
	`coin_user`			NUMERIC(16,8)				NOT NULL DEFAULT 0						COMMENT '用户阅币',
	`coin_vips`			NUMERIC(16,8)				NOT NULL DEFAULT 0						COMMENT '孤单VIP阅币',
	`coin_vipi`			NUMERIC(16,8)				NOT NULL DEFAULT 0						COMMENT '受邀VIP阅币',
	`coin_vipu`			NUMERIC(16,8)				NOT NULL DEFAULT 0						COMMENT '邀请人阅币',
	`coin_vipc`			NUMERIC(16,8)				NOT NULL DEFAULT 0						COMMENT '总监阅币',
	`coin_vipd`			NUMERIC(16,8)				NOT NULL DEFAULT 0						COMMENT '经理阅币',

	`check_vip`			TINYINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '是否要求VIP身份（是VIP才可以购买）',
	`check_vipi`		TINYINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '是否检查邀请人（没有邀请人不可购买）',
	`check_cheif`		TINYINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '是否检查总监（必须有总监身份才可购买）',
	`check_director`	TINYINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '是否检查总监（必须有总经理身份才可购买）',

	`limit_style`		TINYINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '限购类型：0不限购,1按人头限购,2按地址限购,3上架期间限购,4指定天数段限购',
	`limit_size`		SMALLINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '限购数量',
	`limit_days`		SMALLINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '限购天数',

	`p_order`			SMALLINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '分类内部排序，DESC',

	`video`				VARCHAR(1024)				NOT NULL DEFAULT ''						COMMENT '视频URL',
	`intro`				TEXT						NULL									COMMENT '描述内容',

	`online_time`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '预定上架时间',
	`offline_time`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '预定下架时间',

	`is_alone`			TINYINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '是否单独下单，有它必须单独下单',

	`status`			TINYINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '上架状态：0待审,1拒绝,2删除,3批准,4排队,5正常,6下架',

	`create_user`		INT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '创建IP',

	`audit_user`		INT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '审核时间',
	`audit_from`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '审核IP',

	`review_user`		INT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '复核人',
	`review_time`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '复核时间',
	`review_from`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '复核IP',
	PRIMARY KEY (`id`),
	KEY `catagory_id` (`catagory_id`),
	KEY `sku_id` (`sku_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='货架';

DROP TABLE IF EXISTS `shelf_task`;
CREATE TABLE `shelf_task` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '品牌ID',
	`shelf_id` 			INT UNSIGNED 			NOT NULL								COMMENT '上架ID',

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
	KEY `shelf_id` (`shelf_id`),
	KEY `s1_time` (`s1_time`),
	KEY `s2_time` (`s2_time`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='货架定时任务';

DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
	`id` 				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id` 			INT UNSIGNED 			NOT NULL								COMMENT '用户ID',
	`shelf_id` 			INT UNSIGNED 			NOT NULL								COMMENT '货架ID',
	`sku_id` 			INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT 'SKUID',
	`spu_id` 			INT UNSIGNED 			NOT NULL DEFAULT 0						COMMENT 'SPUID',
	`inviter_id` 		INT UNSIGNED 			NOT NULL								COMMENT '邀请人ID',

	-- Shelf相关信息，Titile，图片，价格
	`shelf_title`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '加入购物车时的商品标题',
	`shelf_price`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '加入购物车时的商品价格',
	`shelf_thumb`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '加入购物车时的商品缩略图',
		
	`quantity`			INT UNSIGNED			NOT NULL								COMMENT '下单数量',

	`is_checked`		TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '选中状态',
	
	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `sku_id` (`sku_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='购物车';

DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
	`id` 			CHAR(12)				NOT NULL								COMMENT '订单ID',
	`user_id` 		INT UNSIGNED 			NOT NULL								COMMENT '用户ID',
	`inviter_id` 	INT UNSIGNED 			NOT NULL								COMMENT '邀请人ID',

	`quantity`		INT UNSIGNED			NOT NULL								COMMENT '货品总数量',
	`price_total`	NUMERIC(16,4)			NOT NULL								COMMENT '订单总价格',

	`pay_money`		NUMERIC(16,4)			NOT NULL								COMMENT '余额支付部分',
	`pay_ticket`	NUMERIC(16,4)			NOT NULL								COMMENT '购物券支付部分',
	`pay_online`	NUMERIC(16,4)			NOT NULL								COMMENT '在线支付部分',

	`pay_serial`	VARCHAR(32)				NOT NULL								COMMENT '支付回单号',
	`pay_time`		DATETIME				NULL									COMMENT '支付时间',

	`create_time`	DATETIME				NULL									COMMENT '创建时间',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`trans_id`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '物流单号',
	`trans_fin`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否已确认',
	`trans_time`	DATETIME				NULL									COMMENT '确认收货时间',

	`status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '订单状态：参见文档',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
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
	`sku_id` 		INT UNSIGNED 			NOT NULL								COMMENT 'SKUID',
	`spu_id` 		INT UNSIGNED 			NOT NULL								COMMENT 'SKUID',
	`supplier_id`	INT UNSIGNED 			NOT NULL								COMMENT '供应商ID',

	`quantity`		INT UNSIGNED			NOT NULL								COMMENT '货品总数量',
	`price`			NUMERIC(16,4)			NOT NULL								COMMENT '结算单价',
	`total`			NUMERIC(16,4)			NOT NULL								COMMENT '结算总价',

	`title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '商品标题',
	`picture`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '商品小图',

	`trans_id`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '物流单号',
	`trans_fin`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否已确认',
	`trans_time`	DATETIME				NULL									COMMENT '确认收货时间',
	PRIMARY KEY (`id`),
	KEY `order_id` (`order_id`),
	KEY `sku_id` (`sku_id`),
	KEY `supplier_id` (`supplier_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='订单详情';

DROP TABLE IF EXISTS `order_ticket`;
CREATE TABLE `order_ticket` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`order_id` 		CHAR(24)				NOT NULL								COMMENT '订单ID',
	`ticket_id`		INT UNSIGNED 			NOT NULL								COMMENT '购物券ID',
	`price`			NUMERIC(16,4)			NOT NULL								COMMENT '购物券面额',

	PRIMARY KEY (`id`),
	KEY `order_id` (`order_id`),
	KEY `ticket_id` (`ticket_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='订单用券';
