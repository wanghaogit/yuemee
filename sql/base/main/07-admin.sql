/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */
DROP TABLE IF EXISTS `rbac_admin`;
CREATE TABLE `rbac_admin` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '用户ID',
	`user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',
	`role_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '角色ID',

	`password`		CHAR(40)				NOT NULL DEFAULT ''						COMMENT '二次操作密码',
	`token`			CHAR(16)				NOT NULL DEFAULT ''						COMMENT '登陆令牌',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '管理员状态：0=禁止，1=允许',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	UNIQUE KEY `user_id` (`user_id`),
	KEY `token` (`token`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='管理员';

DROP TABLE IF EXISTS `rbac_role`;
CREATE TABLE `rbac_role` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '角色ID',
	`parent_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '上级角色ID',
	`name`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '角色名称',

	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='管理角色';

DROP TABLE IF EXISTS `rbac_target`;
CREATE TABLE `rbac_target` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '目标ID',
	`parent_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '上级目标ID',
	`name`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '目标名称',
	
	`mvc_module`	VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT 'MVC模块',
	`mvc_handler`	VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT 'MVC处理器',
	`mvc_action`	VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT 'MVC动作',
	`mvc_param`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT 'MVC参数',
	`mvc_value`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT 'MVC参数值',

	
	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='管理目标 @NOENTITY';


DROP TABLE IF EXISTS `rbac_rule`;
CREATE TABLE `rbac_rule` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '目标ID',
	`role_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '角色ID',
	`target_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '目标ID',
	
	`acl_view`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '访问权限：0=继承，1=允许，2=拒绝',
	`acl_edit`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '修改权限：0=继承，1=允许，2=拒绝',
	`acl_delete`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '删除权限：0=继承，1=允许，2=拒绝',

	PRIMARY KEY (`id`),
	KEY `role_id` (`role_id`),
	KEY `target_id` (`target_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='管理规则 @NOENTITY';