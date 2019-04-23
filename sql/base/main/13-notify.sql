/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-3-14
 */
DROP TABLE IF EXISTS `sms`;
CREATE TABLE `sms` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '短信通知ID',

	`user_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '所属用户ID',
	`mobile`			VARCHAR(16)				NOT NULL								COMMENT '目标手机号',

	`code`				VARCHAR(6)				NOT NULL								COMMENT '短信验证码',

	`message`			VARCHAR(256)			NOT NULL								COMMENT '短信内容',
	`biz_id`			VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '回执ID',

	`create_time`		DATETIME				NULL									COMMENT '发送时间 @TIMESTAMP-CREATE',
	`expire_time`		DATETIME				NULL									COMMENT '过期时间',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `mobile` (`mobile`),
	KEY `biz_id` (`biz_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='短信通知';

DROP TABLE IF EXISTS `notice`;
CREATE TABLE `notice` (
	`id`				CHAR(18)				NOT NULL								COMMENT '公告ID',

	`scope`				TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '公告范围：0全体,1用户,2VIP,3总监,4经理,5供应商,6员工,7管理员',
	`scope_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '范围ID',

	`title`				VARCHAR(64)				NOT NULL								COMMENT '公告标题',
	`content`			TEXT					NULL									COMMENT '公告内容',

	`open_time`			DATETIME				NULL									COMMENT '公开时间',
	`close_time`		DATETIME				NULL									COMMENT '关闭时间',

	`status`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '认证状态 0=草稿,1=待审,2=审核,3=关闭',

	`create_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发布人',
	`create_time`		DATETIME				NULL									COMMENT '发布时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发布IP',

	`audit_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`		DATETIME				NULL									COMMENT '审核时间',
	`audit_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',

	PRIMARY KEY (`id`),
	KEY `scope` (`scope`),
	KEY `scope_id` (`scope_id`),
	KEY `status` (`status`),
	KEY `create_time` (`create_time`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='系统公告';


DROP TABLE IF EXISTS `mail`;
CREATE TABLE `mail` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '邮件ID',

	`sender_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发送人ID',
	`sender_name`		VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '发送人昵称',

	`reciver_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '接收人ID',
	`reciver_name`		VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '发送人昵称',

	`title`				VARCHAR(64)				NOT NULL								COMMENT '公告标题',
	`content`			TEXT					NULL									COMMENT '公告内容',

	`status`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '邮件状态 0=草稿,1=发送,2=已读,3=删除',

	`create_time`		DATETIME				NULL									COMMENT '发布时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发布IP',

	`recive_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '阅读时间',
	`recive_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '阅读IP',

	PRIMARY KEY (`id`),
	KEY `sender_id` (`sender_id`),
	KEY `reciver_id` (`reciver_id`),
	KEY `status` (`status`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='私信';
