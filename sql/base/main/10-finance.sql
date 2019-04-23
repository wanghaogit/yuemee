/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 * 招聘佣金，二级招聘佣金，三级招聘佣金，四级招聘佣金
 * 销售佣金，二级销售佣金，三级销售佣金，四级销售佣金
 * 底薪
 * 积分
 * 购物券
 * 现金
 */
DROP TABLE IF EXISTS `user_finance`;
CREATE TABLE `user_finance` (
	`user_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',

	`money`				NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '现金余额,全账户',
	`coin`				NUMERIC(16,8)			NOT NULL DEFAULT 0.0					COMMENT '阅币余额,区块链货币',
	`profit_self`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金余额,自买省的',
	`profit_share`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金余额,分享赚的',
	`profit_team`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金余额,团队管理佣金',
	`recruit_dir`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '礼包佣金,直接招聘佣金',
	`recruit_alt`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '礼包佣金,间接招聘佣金',

	`thew_status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '礼包佣金是否解冻',
	`thew_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '礼包佣金解冻时间',

	PRIMARY KEY (`user_id`),
	KEY `money` (`money`),
	KEY `coin` (`coin`),
	KEY `thew_status` (`thew_status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='账户';

DROP TABLE IF EXISTS `user_bonus`;
CREATE TABLE `user_bonus` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '优惠券ID',
	`user_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',
	`batch_id`			CHAR(24)				NOT NULL DEFAULT ''						COMMENT '批次号',
	`type`				SMALLINT UNSIGNED		NOT NULL								COMMENT '奖金类型：0测试,1伯乐奖,2推荐奖',
	
	`money`				NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '奖金金额',
	`status`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '奖金状态：0草稿,1发布,2领取',
	
	`create_user`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发布人',
	`create_time`		DATETIME				NULL									COMMENT '发布时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发布IP',

	`draw_time`			DATETIME				NULL									COMMENT '领取时间',
	`draw_from`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '领取IP',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `batch_id` (`batch_id`),
	KEY `type` (`type`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='奖金';

DROP TABLE IF EXISTS `user_ticket`;
CREATE TABLE `user_ticket` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '优惠券ID',
	`user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',
	`ticket_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '优惠券ID',

	`money`			NUMERIC(16,4)			NOT NULL								COMMENT '面额',
	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '优惠券状态',

	`create_time`	DATETIME				NULL									COMMENT '领取时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`use_time`		DATETIME				NULL									COMMENT '使用时间',
	`use_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '使用IP',
	`use_order`		CHAR(24)				NOT NULL DEFAULT ''						COMMENT '关联订单',
	`use_item`		CHAR(24)				NOT NULL DEFAULT ''						COMMENT '关联商品',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `use_order` (`use_order`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='用户领券';

DROP TABLE IF EXISTS `tally_money`;
CREATE TABLE `tally_money` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',

	`source`		VARCHAR(16)				NOT NULL								COMMENT '资金来源/去向',
	`order_id`		VARCHAR(12)				NOT NULL								COMMENT '关联订单ID',

	`val_before`	NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '现金',
	`val_delta`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '现金',
	`val_after`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '现金',

	`message`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '变化原因',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `source` (`source`),
	KEY `create_time` (`create_time`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='现金流水账';

DROP TABLE IF EXISTS `tally_coin`;
CREATE TABLE `tally_coin` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',

	`source`		VARCHAR(16)				NOT NULL								COMMENT '资金来源/去向',
	`order_id`		VARCHAR(12)				NOT NULL								COMMENT '关联订单ID',

	`val_before`	NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '阅币',
	`val_delta`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '阅币',
	`val_after`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '阅币',

	`message`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '变化原因',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `source` (`source`),
	KEY `create_time` (`create_time`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='阅币流水账';

DROP TABLE IF EXISTS `tally_profit`;
CREATE TABLE `tally_profit` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',

	`target`		VARCHAR(8)				NOT NULL								COMMENT '目标子账户:SELF,SHARE,TEAM',
	`source`		VARCHAR(16)				NOT NULL								COMMENT '资金来源/去向',
	`order_id`		VARCHAR(12)				NOT NULL								COMMENT '关联订单ID',

	`val_before`	NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金',
	`val_delta`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金',
	`val_after`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金',

	`message`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '变化原因',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `target` (`target`),
	KEY `source` (`source`),
	KEY `create_time` (`create_time`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='销售佣金流水账';

DROP TABLE IF EXISTS `tally_recruit`;
CREATE TABLE `tally_recruit` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',

	`target`		VARCHAR(8)				NOT NULL								COMMENT '目标子账户:DIR,ALT',
	`source`		VARCHAR(16)				NOT NULL								COMMENT '资金来源/去向',
	`order_id`		VARCHAR(12)				NOT NULL								COMMENT '关联订单ID',

	`val_before`	NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金',
	`val_delta`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金',
	`val_after`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金',

	`message`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '变化原因',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `target` (`target`),
	KEY `source` (`source`),
	KEY `create_time` (`create_time`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='招聘佣金流水账';

DROP TABLE IF EXISTS `rebate`;
CREATE TABLE `rebate` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`order_id`			CHAR(12)				NOT NULL								COMMENT '订单ID',
	`item_id`			INT UNSIGNED			NOT NULL								COMMENT '明细ID',

	-- 记录现场
	`spu_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT 'SPUID',
	`sku_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT 'SKUID',
	`shelf_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '售卖ID',
	`buyer_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '购买者ID',

	-- 记录支付
	`pay_count`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '购买数量',
	`pay_total`			NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '支付总额',
	`pay_money`			NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '支付现金',
	`pay_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '支付佣金',
	`pay_ticket`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '支付券',
	
	-- 折合返利
	`total_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '总可支配佣金',

	-- 平台返利
	`system_ratio`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '平台返利比例',
	`system_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '平台返利金额',
	
	-- 自己返利
	`self_ratio`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '自己返利比例',
	`self_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '自己返利金额',

	-- 分享返利
	`share_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '分享ID',
	`share_user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '分享会员ID',
	`share_ratio`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '分享返利比例',
	`share_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '分享返利金额',

	-- 总监返利
	`chief_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '总监ID',
	`chief_user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '总监会员ID',
	`chief_ratio`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '总监返利比例',
	`chief_profit`		NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '总监返利金额',

	-- 经理返利
	`director_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '经理ID',
	`director_user_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '经理会员ID',
	`director_ratio`	NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '经理返利比例',
	`director_profit`	NUMERIC(10,6)			NOT NULL DEFAULT 0.0					COMMENT '经理返利金额',

	
	PRIMARY KEY (`id`),
	KEY `order_id` (`order_id`),
	UNIQUE KEY `item_id` (`item_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='返利';