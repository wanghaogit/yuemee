/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */

DROP TABLE IF EXISTS `user_charge`;
CREATE TABLE `user_charge` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',

	`channel_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '支付渠道ID：参见文档',
	`order_id`		VARCHAR(12)				NOT NULL								COMMENT '内部订单ID',
	`payment_id`	VARCHAR(32)				NOT NULL								COMMENT '充值订单ID',
	`account`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '充值账户特征',
	`money`			NUMERIC(16,4)			NOT NULL								COMMENT '充值金额',
	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '充值订单状态',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	`accept_time`	DATETIME				NULL									COMMENT '支付时间',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `order_id` (`order_id`),
	KEY `status` (`status`),
	KEY `create_time` (`create_time`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='充值记录';

DROP TABLE IF EXISTS `user_withdraw`;
CREATE TABLE `user_withdraw` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',
	`order_id`			VARCHAR(12)				NOT NULL								COMMENT '内部订单ID',

	`total`				NUMERIC(16,4)			NOT NULL								COMMENT '兑现总金额',
	`money`				NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '从余额部分提取金额',
	`profit_self`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金余额,自买省的',
	`profit_share`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金余额,分享赚的',
	`profit_team`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '佣金余额,团队管理佣金',
	`recruit_dir`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '礼包佣金,直接招聘佣金',
	`recruit_alt`		NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '礼包佣金,间接招聘佣金',

	`userbank_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '用户银行账号ID',
	`bank_id`			SMALLINT UNSIGNED		NOT NULL								COMMENT '银行ID',
	`region_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '开户地区ID',
	`bank_name`			VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '开户人名称',
	`card_no`			VARCHAR(128)			NOT NULL								COMMENT '卡号',

	`status`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '提现请求状态，0提交,1审核,2打款,3完成,4拒绝,5关闭',

	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	`audit_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核时间',
	`process_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '处理时间',
	`finish_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '完成时间',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `order_id` (`order_id`),
	KEY `status` (`status`),
	KEY `create_time` (`create_time`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='提现记录';
