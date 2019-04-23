/**
 * 阅米数据库初始化升级
 * Author:  eglic
 * Created: 2018-4-16
 */
DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '直营团队ID',
	`director_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '绑定总经理ID',

	`name`				VARCHAR(16)				NOT NULL								COMMENT '团队名称',

	`create_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	UNIQUE KEY `director_id` (`director_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='团队';

DROP TABLE IF EXISTS `team_group`;
CREATE TABLE `team_group` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '小组ID',
	`team_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '团队ID',
	`level`				TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '身份等级：0无效,1一线,2二线,3三线,4四线',
	`manager_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '小组管理员ID',

	`name`				VARCHAR(16)				NOT NULL								COMMENT '小组名称',
	`code`				VARCHAR(2)				NOT NULL DEFAULT ''						COMMENT '身份代码',

	`create_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `team_id` (`team_id`),
	KEY `level` (`level`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='小组';

DROP TABLE IF EXISTS `team_member`;
CREATE TABLE `team_member` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '成员ID',
	`user_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',
	`team_id`			INT UNSIGNED			NOT NULL								COMMENT '团队ID',
	`group_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '小组ID',

	`name`				VARCHAR(16)				NOT NULL								COMMENT '姓名',

	`level`				TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '身份等级：0无效,1一线,2二线,3三线,4四线',
	`code`				VARCHAR(3)				NOT NULL DEFAULT ''						COMMENT '身份代码',
	`password`			CHAR(40)				NOT NULL DEFAULT ''						COMMENT '工作平台密码',

	`status`			TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '员工状态：0离职,1在职',

	`create_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `team_id` (`team_id`),
	UNIQUE KEY `user_id` (`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='团队';

DROP TABLE IF EXISTS `cheif`;
CREATE TABLE `cheif` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '总监ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',
	`director_id`	INT UNSIGNED			NOT NULL								COMMENT '归属经理ID',

	`invite_code`	VARCHAR(8)				NOT NULL								COMMENT '一级邀请码 @UNIQUE',

	`status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT 'VIP状态：0 非VIP，1是VIP',

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间，首次成为VIP时间 @TIMESTAMP-CREATE',
	`update_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '变更时间，最后一次续费时间',
	`expire_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '过期时间，最后一次过期时间',

	PRIMARY KEY (`id`),
	UNIQUE KEY `user_id` (`user_id`),
	KEY `director_id` (`director_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='三层之二总监';


DROP TABLE IF EXISTS `cheif_status`;
CREATE TABLE `cheif_status` (
	`id`				INT UNSIGNED			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
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
	KEY `cheif_id` (`cheif_id`),
	KEY `pay_status` (`pay_status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='总监身份状态';


DROP TABLE IF EXISTS `director`;
CREATE TABLE `director` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '经理ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',

	`invite_code`	VARCHAR(8)				NOT NULL								COMMENT '一级邀请码 @UNIQUE',

	`status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT 'VIP状态：0 非VIP，1是VIP',

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间，首次成为VIP时间 @TIMESTAMP-CREATE',
	`update_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '变更时间，最后一次续费时间',
	`expire_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '过期时间，最后一次过期时间',

	PRIMARY KEY (`id`),
	UNIQUE KEY `user_id` (`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='三层之三经理';

DROP TABLE IF EXISTS `director_status`;
CREATE TABLE `director_status` (
	`id`				INT UNSIGNED			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
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
	KEY `director_id` (`director_id`),
	KEY `pay_status` (`pay_status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='经理身份状态';
