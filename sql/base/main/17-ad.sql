/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-3-14
 */

DROP TABLE IF EXISTS `run_page`;
CREATE TABLE `run_page` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '页面ID',
	`parent_id`		INT UNSIGNED			NOT NULL DEFAULT 0 						COMMENT '上级页面ID',
	`name`			VARCHAR(32)				NOT NULL								COMMENT '页面名称',
	`alias`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '页面代号',
	`style`			TINYINT UNSIGNED		NOT NULL								COMMENT '页面类型：0静态,1动态',

	`template`		TEXT					NULL									COMMENT '模块代码，静态页面没有模板',

	-- 审计信息
	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`),
	KEY `alias` (`alias`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='运营页面配置';

DROP TABLE IF EXISTS `run_block`;
CREATE TABLE `run_block` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '模块ID',
	`page_id`		INT UNSIGNED			NOT NULL DEFAULT 0 						COMMENT '页面ID',
	`name`			VARCHAR(32)				NOT NULL								COMMENT '模块名称',
	`alias`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '模块代号',
	`source_type`	TINYINT UNSIGNED		NOT NULL								COMMENT '组件数据格式：0自定义,1单品,2多品',

	`sizer`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '尺寸模式：0自适应,1指定像素,2百分比',
	`width`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '模块宽度',
	`height`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '模块高度',
	`capacity`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '数据容量',

	`widget_id`		INT UNSIGNED			NOT NULL DEFAULT 0 						COMMENT '默认组件ID',
	`source_id`		INT UNSIGNED			NOT NULL DEFAULT 0 						COMMENT '默认数据源ID',

	`preview`		TEXT					NULL									COMMENT '模块预览图,BASE64',

	-- 审计信息
	PRIMARY KEY (`id`),
	KEY `page_id` (`page_id`),
	KEY `alias` (`alias`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='运营模块配置';

DROP TABLE IF EXISTS `run_source`;
CREATE TABLE `run_source` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '数据源ID',
	`name`			VARCHAR(32)				NOT NULL								COMMENT '数据源名称',
	`alias`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '数据源代号',

	`style`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '数据源类型：0=SQL,1=PHP,2=缓存',
	`type`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '数据源格式：0自定义,1单品,2多品',

	`driver`		TEXT					NULL									COMMENT '驱动代码',

	PRIMARY KEY (`id`),
	KEY `alias` (`alias`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='运营数据源配置';

DROP TABLE IF EXISTS `run_widget`;
CREATE TABLE `run_widget` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '组件ID',
	`name`			VARCHAR(32)				NOT NULL								COMMENT '组件名称',
	`alias`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '组件代号',

	`source_type`	TINYINT UNSIGNED		NOT NULL								COMMENT '组件数据格式：0自定义,1单品,2多品',

	`sizer`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '尺寸模式：0自适应,1指定像素,2百分比',
	`width`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '组件宽度',
	`height`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '组件高度',
	`capacity`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '数据容量',

	`preview`		TEXT					NULL									COMMENT '组件预览图,BASE64',
	`template`		TEXT					NULL									COMMENT '组件的UI代码',


	PRIMARY KEY (`id`),
	KEY `alias` (`alias`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='运营数据组件';

DROP TABLE IF EXISTS `run_usage`;
CREATE TABLE `run_usage` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '引用ID',
	`page_id`		INT UNSIGNED			NOT NULL DEFAULT 0 						COMMENT '页面ID',
	`block_id`		INT UNSIGNED			NOT NULL DEFAULT 0 						COMMENT '模块ID',

	-- 组件信息
	`widget_id`		INT UNSIGNED			NOT NULL DEFAULT 0 						COMMENT '组件ID',

	-- 数据信息
	`source_id`		INT UNSIGNED			NOT NULL DEFAULT 0 						COMMENT '数据源ID',
	`param_1`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '第一参数',			
	`param_2`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '第二参数',			
	`param_3`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '第三参数',			
	`param_4`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '第四参数',			
	`param_5`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '第五参数',			
	`param_6`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '第六参数',			
	`param_7`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '第七参数',			
	`param_8`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '第八参数',			
	`param_9`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '第九参数',			
	
	-- 排期信息
	`schedule_start`	BIGINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '排期开始',
	`schedule_end`		BIGINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '排期结束',

	-- 审计信息
	PRIMARY KEY (`id`),
	KEY `page_id` (`page_id`),
	KEY `block_id` (`block_id`),
	KEY `schedule_start` (`schedule_start`),
	KEY `schedule_end` (`schedule_end`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='运营数据排期';

DROP TABLE IF EXISTS `run_release`;
CREATE TABLE `run_release` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '引用ID',
	`page_id`		INT UNSIGNED			NOT NULL DEFAULT 0 						COMMENT '页面ID',

	`html`			TEXT					NULL									COMMENT '生成页面代码',

	PRIMARY KEY (`id`),
	KEY `page_id` (`page_id`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='运营页面发布';