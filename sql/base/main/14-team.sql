/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-3-14
 */
DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '团队ID',
	`director_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '绑定总经理ID',
	`user_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '绑定团队ID',

	`name`				VARCHAR(16)				NOT NULL								COMMENT '团队名称',

	`create_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		DATETIME				NULL									COMMENT '创建时间',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	KEY `director_id` (`director_id`),
	KEY `user_id` (`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='团队';

DROP TABLE IF EXISTS `team_group`;
CREATE TABLE `team_group` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '小组ID',
	`team_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '团队ID',
	`parent_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '上级小组ID',
	`level`				TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '身份等级：0无效,1一线,2二线,3三线,4四线',

	`name`				VARCHAR(16)				NOT NULL								COMMENT '小组名称',
	`manager_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '小组管理员ID',
	`code`				VARCHAR(3)				NOT NULL DEFAULT ''						COMMENT '身份代码',

	`create_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		DATETIME				NULL									COMMENT '创建时间',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `team_id` (`team_id`),
	KEY `parent_id` (`parent_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='小组';

DROP TABLE IF EXISTS `team_member`;
CREATE TABLE `team_member` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '成员ID',
	`team_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '团队ID',
	`group_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '小组ID',
	`user_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',

	`name`				VARCHAR(16)				NOT NULL								COMMENT '姓名',

	`level`				TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '身份等级：0无效,1一线,2二线,3三线,4四线',
	`code`				VARCHAR(3)				NOT NULL DEFAULT ''						COMMENT '身份代码',
	`password`			CHAR(40)				NOT NULL DEFAULT ''						COMMENT '工作平台密码',

	`is_manager`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否当前等级的管理员',

	`status`			TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '员工状态：0离职,1在职',

	`create_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		DATETIME				NULL									COMMENT '创建时间',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `team_id` (`team_id`),
	KEY `user_id` (`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='团队';

