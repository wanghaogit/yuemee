/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */

DROP TABLE IF EXISTS `cheif`;
CREATE TABLE `cheif` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '总监ID',
	`director_id`	INT UNSIGNED			NOT NULL								COMMENT '归属经理ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',



	`create_time`	DATETIME				NULL									COMMENT '创建时间',
	`update_time`	DATETIME				NULL									COMMENT '变更时间',

	PRIMARY KEY (`id`),
	KEY `director_id` (`director_id`),
	KEY `user_id` (`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='三层之二总监';

DROP TABLE IF EXISTS `cheif_status`;
CREATE TABLE `cheif_status` (
	`id`				INT UNSIGNED			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`			INT UNSIGNED			NOT NULL								COMMENT '用户ID',
	`cheif_id`			INT UNSIGNED			NOT NULL								COMMENT '总监ID',
	
	`pay_channel`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '支付渠道 0免费,1卡片,2线下,3微信,4支付宝',
	`pay_status`		TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '支付状态 0已关闭,1待支付,2已支付',
	`pay_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '支付时间',
	`order_id`			VARCHAR(12)				NOT NULL DEFAULT ''						COMMENT '购买订单ID  @UNIQUE',
	`trans_id`			VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '外部订单ID/激活卡序列号',
	`money`				NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '支付金额',
	`expire_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '过期时间',

	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `cheif_id` (`cheif_id`),
	KEY `pay_status` (`pay_status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='总监身份状态';

DROP TABLE IF EXISTS `cheif_finance`;
CREATE TABLE `cheif_finance` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '账户ID',
	`cheif_id`		INT UNSIGNED			NOT NULL								COMMENT '总监ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',

	`recruit_self`	NUMERIC(16,4)			NOT NULL								COMMENT '间接招聘佣金',
	`deduct_self`	NUMERIC(16,4)			NOT NULL								COMMENT '团队管理佣金',
	`recruit_bole`	NUMERIC(16,4)			NOT NULL								COMMENT '伯乐奖/招聘佣金部分',
	`deduct_bole`	NUMERIC(16,4)			NOT NULL								COMMENT '伯乐奖/销售佣金部分',

	`thew_status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '礼包佣金是否解冻',
	`thew_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '礼包佣金解冻时间',
	PRIMARY KEY (`id`),
	KEY `cheif_id` (`cheif_id`),
	KEY `user_id` (`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='总监账户';

DROP TABLE IF EXISTS `cheif_card`;
CREATE TABLE `cheif_card` (
	`id`				INT UNSIGNED			NOT NULL AUTO_INCREMENT					COMMENT '卡片ID',
	`owner_id`			INT UNSIGNED			NOT NULL								COMMENT '拥有者用户ID',
	`director_id`		INT UNSIGNED			NOT NULL								COMMENT '拥有者经理ID',

	`serial`			VARCHAR(10)				NOT NULL								COMMENT '卡号',
	`money`				NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '价值金额（冗余）',

	`rcv_user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '接受者用户ID',
	`rcv_vip_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '接受者VIPID',
	`rcv_mobile`		VARCHAR(12)				NOT NULL DEFAULT ''						COMMENT '接受者手机号码',
	`rcv_wechat_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '接受者微信ID',

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
CREATE TABLE `cheif_contract` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '合同ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',
	`cheif_id`		INT UNSIGNED			NOT NULL								COMMENT '岗位ID',
	`template_id`	INT UNSIGNED			NOT NULL								COMMENT '模板ID',
	
	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '合同状态 0=草稿,1=已签，2=作废',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`update_time`	DATETIME				NULL									COMMENT '变更时间 @TIMESTAMP-UPDATE',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `cheif_id` (`cheif_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='总监合同';

DROP TABLE IF EXISTS `director`;
CREATE TABLE `director` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '经理ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',



	`create_time`	DATETIME				NULL									COMMENT '创建时间',
	`update_time`	DATETIME				NULL									COMMENT '变更时间',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='三层之三经理';

DROP TABLE IF EXISTS `director_status`;
CREATE TABLE `director_status` (
	`id`				INT UNSIGNED			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`			INT UNSIGNED			NOT NULL								COMMENT '用户ID',
	`director_id`		INT UNSIGNED			NOT NULL								COMMENT '经理ID',
	
	`pay_channel`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '支付渠道 0线下,1免费,2微信,3支付宝',
	`pay_status`		TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '支付状态 0已关闭,1待支付,2已支付',
	`pay_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '支付时间',
	`order_id`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '购买订单ID  @UNIQUE',
	`trans_id`			VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '外部订单ID',
	`money`				NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '支付金额',
	`expire_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '过期时间',

	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `director_id` (`director_id`),
	KEY `pay_status` (`pay_status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='经理身份状态';

DROP TABLE IF EXISTS `director_finance`;
CREATE TABLE `director_finance` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '账户ID',
	`director_id`		INT UNSIGNED			NOT NULL								COMMENT '经理ID',
	`user_id`			INT UNSIGNED			NOT NULL								COMMENT '用户ID',

	`recruit_self`		NUMERIC(16,4)			NOT NULL								COMMENT '间接招聘佣金',
	`deduct_self`		NUMERIC(16,4)			NOT NULL								COMMENT '团队管理佣金',
	`recruit_bole`		NUMERIC(16,4)			NOT NULL								COMMENT '伯乐奖/招聘佣金部分',
	`deduct_bole`		NUMERIC(16,4)			NOT NULL								COMMENT '伯乐奖/销售佣金部分',

	`thew_status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '礼包佣金是否解冻',
	`thew_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0							COMMENT '礼包佣金解冻时间',
	PRIMARY KEY (`id`),
	KEY `director_id` (`director_id`),
	KEY `user_id` (`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='经理账户';

DROP TABLE IF EXISTS `director_contract`;
CREATE TABLE `director_contract` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '合同ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',
	`director_id`	INT UNSIGNED			NOT NULL								COMMENT '岗位ID',
	`template_id`	INT UNSIGNED			NOT NULL								COMMENT '模板ID',
	
	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '合同状态 0=草稿,1=已签，2=作废',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`update_time`	DATETIME				NULL									COMMENT '变更时间 @TIMESTAMP-UPDATE',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `director_id` (`director_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='经理合同';
