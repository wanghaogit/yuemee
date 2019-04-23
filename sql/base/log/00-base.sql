/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */
DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '配置项ID',
	`group`			VARCHAR(32)				NOT NULL								COMMENT '分组',
	`name`			VARCHAR(64)				NOT NULL								COMMENT '条目',
	`type`			TINYINT					NOT NULL DEFAULT 0						COMMENT '配置类型',
	`value`			VARCHAR(1024)			NOT NULL								COMMENT '配置值',

	`title`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '配置项名称',
	`help`			TEXT															COMMENT '配置项帮助',
	PRIMARY KEY (`id`),
	KEY `group` (`group`),
	KEY `name` (`name`),
	UNIQUE KEY `group_name` (`group` , `name`)
)	Engine=MyISAM
	DEFAULT CHARACTER SET=utf8 
	COLLATE=utf8_general_ci
	COMMENT='系统配置表 @CONFIG';
