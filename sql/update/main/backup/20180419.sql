/**
 * CMS系统
 * Author:  eglic
 * Created: 2018-4-17
 */
DROP TABLE IF EXISTS `cms_column`;
CREATE TABLE `cms_column` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '栏目ID',
	`parent_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '上级栏目ID',
	`name`			VARCHAR(32)				NOT NULL								COMMENT '栏目名称',
	`alias`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '栏目代号',
	
	`create_user`		INT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='CMS栏目';

INSERT INTO `cms_column` (`parent_id`,`name`,`alias`,`create_user`,`create_time`,`create_from`)
VALUES 
	(0,'会员公告','notice',1,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1')),
	(0,'规则中心','rules',1,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1')),
	(0,'新手宝典','guide',1,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1')),
	(0,'大咖讲堂','teach',1,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1')),
	(0,'明星采访','star',1,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1'));

DROP TABLE IF EXISTS `cms_article`;
CREATE TABLE `cms_article` (
	`id`				INT UNSIGNED				NOT NULL AUTO_INCREMENT					COMMENT '栏目ID',
	`column_id`			INT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '栏目ID',

	`title`				VARCHAR(128)				NOT NULL								COMMENT '标题',
	`content`			TEXT						NULL									COMMENT '内容',

	`status`			TINYINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '上架状态：0待审,1拒绝,2删除,3批准,4排队,5正常,6下架',

	`create_user`		INT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '创建IP',

	`audit_user`		INT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '审核时间',
	`audit_from`		BIGINT UNSIGNED				NOT NULL DEFAULT 0						COMMENT '审核IP',
	PRIMARY KEY (`id`),
	KEY `column_id` (`column_id`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='CMS内容';

