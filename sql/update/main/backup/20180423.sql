/**
 * 总监，经理数据结构调整
 * @FORCE-UPDATE
 * Author:  eglic
 * Created: 2018-4-17
 */

DROP TABLE IF EXISTS `vip_status`;
DROP TABLE IF EXISTS `vip_buff`;
CREATE TABLE `vip_buff` (
	`id`			INT UNSIGNED			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',
	
	`type`			TINYINT					NOT NULL DEFAULT 0						COMMENT '状态来源，0=NONE,1=TEST,2=FREE,3=CARD,4=COIN,5=MONEY',

	`order_id`		VARCHAR(12)				NOT NULL DEFAULT ''						COMMENT '购买订单ID  @UNIQUE',
	`tally_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '钻石流水记录ID',
	`coin`			NUMERIC(16,8)			NOT NULL DEFAULT 0.0					COMMENT '支付钻石',

	`start_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '开始时间',
	`expire_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '过期时间',

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `start_time` (`start_time`),
	KEY `expire_time` (`expire_time`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='VIP缴费状态';

DROP TABLE IF EXISTS `cheif`;
CREATE TABLE `cheif` (
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '总监ID',
	`director_id`	INT UNSIGNED			NOT NULL								COMMENT '归属经理ID',

	`invite_code`	VARCHAR(8)				NOT NULL								COMMENT '一级邀请码 @UNIQUE',

	`status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '总监状态：0非总监,1免费总监,2晋升总监,3卡位总监',

	`create_time`	DATETIME				NULL									COMMENT '创建时间',
	`update_time`	DATETIME				NULL									COMMENT '变更时间',

	PRIMARY KEY (`user_id`),
	KEY `director_id` (`director_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='三层之二总监';

DROP TABLE IF EXISTS `cheif_status`;
DROP TABLE IF EXISTS `cheif_buff`;
CREATE TABLE `cheif_buff` (
	`id`				INT UNSIGNED			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`			INT UNSIGNED			NOT NULL								COMMENT '用户ID',
	
	`type`				TINYINT					NOT NULL DEFAULT 0						COMMENT '状态来源，0=NONE,1=免费,2=晋升,3=卡位',

	`pay_channel`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '支付渠道 0免费,1卡片,2线下,3微信,4支付宝',
	`pay_status`		TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '支付状态 0已关闭,1待支付,2已支付',
	`pay_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '支付时间',
	`order_id`			VARCHAR(12)				NOT NULL DEFAULT ''						COMMENT '购买订单ID  @UNIQUE',
	`money`				NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '支付金额',

	`start_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '开始时间',
	`expire_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '过期时间',

	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `pay_status` (`pay_status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='总监身份状态';

DROP TABLE IF EXISTS `cheif_finance`;
CREATE TABLE `cheif_finance` (
	`user_id`			INT UNSIGNED			NOT NULL								COMMENT '用户ID',

	`recruit_self`		NUMERIC(16,4)			NOT NULL								COMMENT '间接招聘佣金',
	`deduct_self`		NUMERIC(16,4)			NOT NULL								COMMENT '团队管理佣金',
	`recruit_bole`		NUMERIC(16,4)			NOT NULL								COMMENT '伯乐奖/招聘佣金部分',
	`deduct_bole`		NUMERIC(16,4)			NOT NULL								COMMENT '伯乐奖/销售佣金部分',

	`thew_status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '礼包佣金是否解冻',
	`thew_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '礼包佣金解冻时间',
	PRIMARY KEY (`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='总监账户';

DROP TABLE IF EXISTS `cheif_card`;
CREATE TABLE `cheif_card` (
	`id`				INT UNSIGNED			NOT NULL AUTO_INCREMENT					COMMENT '卡片ID',
	`owner_id`			INT UNSIGNED			NOT NULL								COMMENT '拥有者用户ID',

	`serial`			VARCHAR(10)				NOT NULL								COMMENT '卡号',
	`money`				NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '价值金额（冗余）',

	`rcv_user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '接受者用户ID',
	`rcv_mobile`		VARCHAR(12)				NOT NULL DEFAULT ''						COMMENT '接受者手机号码',
	`rcv_wechat_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '接受者微信ID',
	`rcv_buff_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '接受者BUFFID',

	`status`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT 'VIP卡片状态：0 新卡,1领取,2使用',

	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`recive_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '领取时间',
	`used_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '使用时间',

	PRIMARY KEY (`id`),
	UNIQUE KEY `serial` (`serial`),
	KEY `owner_id` (`owner_id`),
	KEY `rcv_user_id` (`rcv_user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='总监激活卡';

DROP TABLE IF EXISTS `cheif_contract`;

DROP TABLE IF EXISTS `director`;
CREATE TABLE `director` (
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',

	`invite_code`	VARCHAR(8)				NOT NULL								COMMENT '一级邀请码 @UNIQUE',

	`status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '总经理状态：0非经理,2晋升经理,3卡位经理',

	`create_time`	DATETIME				NULL									COMMENT '创建时间',
	`update_time`	DATETIME				NULL									COMMENT '变更时间',
	
	PRIMARY KEY (`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='三层之三经理';

DROP TABLE IF EXISTS `director_status`;
DROP TABLE IF EXISTS `director_buff`;
CREATE TABLE `director_buff` (
	`id`				INT UNSIGNED			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`			INT UNSIGNED			NOT NULL								COMMENT '用户ID',

	`type`				TINYINT					NOT NULL DEFAULT 0						COMMENT '状态来源，0=NONE,2=晋升,3=卡位',
	
	`pay_channel`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '支付渠道 0线下,1免费,2微信,3支付宝',
	`pay_status`		TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '支付状态 0已关闭,1待支付,2已支付',
	`pay_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '支付时间',
	`order_id`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '购买订单ID  @UNIQUE',
	`money`				NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '支付金额',

	`start_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '开始时间',
	`expire_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '过期时间',

	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `pay_status` (`pay_status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='经理身份状态';

DROP TABLE IF EXISTS `director_finance`;
CREATE TABLE `director_finance` (
	`user_id`			INT UNSIGNED			NOT NULL								COMMENT '用户ID',

	`recruit_self`		NUMERIC(16,4)			NOT NULL								COMMENT '间接招聘佣金',
	`deduct_self`		NUMERIC(16,4)			NOT NULL								COMMENT '团队管理佣金',
	`recruit_bole`		NUMERIC(16,4)			NOT NULL								COMMENT '伯乐奖/招聘佣金部分',
	`deduct_bole`		NUMERIC(16,4)			NOT NULL								COMMENT '伯乐奖/销售佣金部分',

	`thew_status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '礼包佣金是否解冻',
	`thew_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0							COMMENT '礼包佣金解冻时间',
	PRIMARY KEY (`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='经理账户';

DROP TABLE IF EXISTS `director_contract`;
