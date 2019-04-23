/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */

/*
	VIP 职位开放，对用户唯一
*/
DROP TABLE IF EXISTS `vip`;
CREATE TABLE `vip` (
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',
	`chief_id`		INT UNSIGNED			NOT NULL								COMMENT '归属总监ID',

	`invite_code`	VARCHAR(8)				NOT NULL								COMMENT '一级邀请码 @UNIQUE',

	`status`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT 'VIP状态：0 非VIP，1是VIP',

	-- TODO: 统计数据

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间，首次成为VIP时间 @TIMESTAMP-CREATE',
	`update_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '变更时间，最后一次续费时间',
	`expire_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '过期时间，最后一次过期时间',

	PRIMARY KEY (`user_id`),
	KEY `chief_id` (`chief_id`),
	KEY `invite_code` (`invite_code`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='三层之一VIP';

DROP TABLE IF EXISTS `vip_status`;
CREATE TABLE `vip_status` (
	`id`			INT UNSIGNED			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',
	
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

DROP TABLE IF EXISTS `vip_card`;
CREATE TABLE `vip_card` (
	`id`				INT UNSIGNED			NOT NULL AUTO_INCREMENT					COMMENT '卡片ID',
	`owner_id`			INT UNSIGNED			NOT NULL								COMMENT '拥有者用户ID',
	`chief_id`			INT UNSIGNED			NOT NULL								COMMENT '拥有者总监ID',

	`serial`			VARCHAR(10)				NOT NULL								COMMENT '卡号',
	`money`				NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '价值金额（冗余）',
	`coin`				NUMERIC(16,8)			NOT NULL DEFAULT 0.0					COMMENT '价值阅币（冗余）',

	`rcv_user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '接受者用户ID',
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
  COMMENT='VIP激活卡';

DROP TABLE IF EXISTS `invite_template`;
CREATE TABLE `invite_template` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',

	`name`			VARCHAR(32)				NOT NULL								COMMENT '模板名称',

	`body_path`		VARCHAR(256)			NOT NULL								COMMENT '底图路径',
	`body_url`		VARCHAR(256)			NOT NULL								COMMENT '底图预览URL',
	`body_width`	SMALLINT UNSIGNED		NOT NULL								COMMENT '底图宽度',
	`body_height`	SMALLINT UNSIGNED		NOT NULL								COMMENT '底图高度',

	`name_enable`	TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '是否显示姓名',
	`name_x`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '姓名显示位置X',
	`name_y`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '姓名显示位置Y',
	`name_size`		TINYINT UNSIGNED		NOT NULL DEFAULT 16						COMMENT '姓名显示字体大小',
	`name_color`	VARCHAR(8)				NOT NULL DEFAULT '#000000'				COMMENT '姓名显示字体颜色',

	`code_enable`	TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '是否显示邀请码',
	`code_x`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '邀请码显示位置X',
	`code_y`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '邀请码显示位置Y',
	`code_size`		TINYINT UNSIGNED		NOT NULL DEFAULT 24						COMMENT '邀请码显示字体大小',
	`code_color`	VARCHAR(8)				NOT NULL DEFAULT '#000000'				COMMENT '邀请码显示字体颜色',

	`qr_x`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '二维码显示位置X',
	`qr_y`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '二维码显示位置Y',
	`qr_w`			SMALLINT UNSIGNED		NOT NULL DEFAULT 128					COMMENT '二维码显示位置宽度',
	`qr_h`			SMALLINT UNSIGNED		NOT NULL DEFAULT 128					COMMENT '二维码显示位置高度',

	`avatar_enable`	TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '是否显示头像',
	`avatar_x`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '头像显示位置X',
	`avatar_y`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '头像码显示位置Y',
	`avatar_w`		SMALLINT UNSIGNED		NOT NULL DEFAULT 128					COMMENT '头像码显示位置宽度',
	`avatar_h`		SMALLINT UNSIGNED		NOT NULL DEFAULT 128					COMMENT '头像码显示位置高度',

	`status`		TINYINT	UNSIGNED		NOT NULL DEFAULT 1						COMMENT '模板状态：0停用,1草稿,2启用',

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`update_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '更新时间 @TIMESTAMP-UPDATE',
	`create_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`)
)	Engine=MyISAM
	DEFAULT CHARACTER SET=utf8
	COLLATE=utf8_general_ci
	COMMENT='邀请模板';