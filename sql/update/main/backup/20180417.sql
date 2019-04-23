/**
 * 客服系统
 * Author:  eglic
 * Created: 2018-4-17
 */

DROP TABLE IF EXISTS `run_source`;
CREATE TABLE `run_source` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '数据源ID',
	`name`			VARCHAR(32)				NOT NULL								COMMENT '数据源名称',
	`alias`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '数据源代号',

	`style`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '数据源类型：0=SQL,1=PHP,2=选品',
	`type`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '数据源格式：0自定义,1单品,2多品',

	`driver`		TEXT					NULL									COMMENT '驱动代码/选品规则',

	PRIMARY KEY (`id`),
	KEY `alias` (`alias`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='运营数据源配置';

DROP TABLE IF EXISTS `consult`;
CREATE TABLE `consult` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '聊天记录ID',

	`type`				TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '沟通类型：0全局,1商品,2订单',
	`shelf_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '关联商品ID',
	`order_id`			VARCHAR(18)				NOT NULL DEFAULT ''						COMMENT '管理订单ID',

	`sender_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发送人ID（用户）',
	`sender_name`		VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '发送人昵称',

	`reciver_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '接收人ID（后台用户）',
	`reciver_name`		VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '发送人昵称',

	`title`				VARCHAR(64)				NOT NULL								COMMENT '公告标题',
	`content`			TEXT					NULL									COMMENT '公告内容',

	`create_time`		DATETIME				NULL									COMMENT '发布时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发布IP',

	PRIMARY KEY (`id`),
	KEY `sender_id` (`sender_id`),
	KEY `reciver_id` (`reciver_id`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='客服';
