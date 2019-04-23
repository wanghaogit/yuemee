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

DROP TABLE IF EXISTS `region`;
CREATE TABLE `region` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '模板ID',
	`province` 		VARCHAR(32) 			NOT NULL								COMMENT '省|市',	
	`city`			VARCHAR(32) 			NOT NULL DEFAULT ''						COMMENT '市|辖',
	`country` 		VARCHAR(32) 			NOT NULL DEFAULT ''						COMMENT '县|区',
	`district` 		VARCHAR(32) 			NOT NULL DEFAULT ''						COMMENT '区|镇',

	PRIMARY KEY (`id`)
)	Engine=MyISAM
	DEFAULT CHARACTER SET=utf8 
	COLLATE=utf8_general_ci
	COMMENT='地区数据 @NOENTITY';


DROP TABLE IF EXISTS `bank`;
CREATE TABLE `bank` (
	`id` 			SMALLINT UNSIGNED 		NOT NULL AUTO_INCREMENT					COMMENT '银行ID',
	`type`			TINYINT UNSIGNED		NOT NULL								COMMENT '类型ID',
	`name`			VARCHAR(32)				NOT NULL								COMMENT '银行名称',
	`icon`			TEXT															COMMENT '银行图标',

	PRIMARY KEY (`id`),
	KEY `type` (`type`)
)	Engine=MyISAM
	DEFAULT CHARACTER SET=utf8 
	COLLATE=utf8_general_ci
	COMMENT='银行数据 @NOENTITY';

DROP TABLE IF EXISTS `device_vender`;
CREATE TABLE `device_vender` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '厂商ID',
	`name`			VARCHAR(32)				NOT NULL								COMMENT '厂商名称',

	PRIMARY KEY (`id`),
	KEY `name` (`name`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='手机生产厂商 @NOENTITY';

DROP TABLE IF EXISTS `device_model`;
CREATE TABLE `device_model` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '型号ID',
	`vendor_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '设备品牌',
	`name`			VARCHAR(32)				NOT NULL								COMMENT '型号名称',

	PRIMARY KEY (`id`),
	KEY `vendor_id` (`vendor_id`),
	KEY `name` (`name`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='手机型号 @NOENTITY';


DROP TABLE IF EXISTS `device`;
CREATE TABLE `device` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '设备ID',
	`udid`			VARCHAR(40)				NOT NULL								COMMENT '设备序列号',
	`imei`			VARCHAR(40)				NOT NULL DEFAULT ''						COMMENT '设备IMEI',
	`imsi`			VARCHAR(40)				NOT NULL DEFAULT ''						COMMENT '设备IMEI',

	`type`			TINYINT UNSIGNED		NOT NULL								COMMENT '设备类型：0未知，1安卓，2苹果',
	`vendor_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '设备品牌',
	`model_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '设备型号',

	`version_sys`	VARCHAR(16) 			NOT NULL DEFAULT 0						COMMENT '系统版本',
	`version_app`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT 'APP版本',
	`version_oa`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT 'OA版本',

	`screen_x`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '屏幕宽度',
	`screen_y`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '屏幕高度',
	
	`gps`			POINT					NULL									COMMENT 'GPS坐标',
	`region_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '计算出来的位置',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	`update_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-UPDATE',
	`update_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	UNIQUE KEY `udid` (`udid`),
	KEY `version_app` (`version_app`),
	KEY `version_oa` (`version_oa`),
	KEY `region_id` (`region_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='终端设备';


DROP TABLE IF EXISTS `device_user`;
CREATE TABLE `device_user` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`device_id`		INT UNSIGNED			NOT NULL								COMMENT '设备ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',
	
	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`update_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-UPDATE',
	`update_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	KEY `device_id` (`device_id`),
	KEY `user_id` (`user_id`),
	UNIQUE KEY `device_user_id`(`device_id`,`user_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='设备和用户的绑定关系 @NOENTITY';

DROP TABLE IF EXISTS `release`;
CREATE TABLE `release` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '版本ID',
	`app_id`		SMALLINT UNSIGNED		NOT NULL								COMMENT 'APP类型，1用户端,2OA端',
	`platform_id`	TINYINT UNSIGNED		NOT NULL								COMMENT '平台类型，1安卓,2苹果',

	`version`		INT UNSIGNED			NOT NULL								COMMENT '版本号，格式0P0M00S',

	`apk_size`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '安卓包大小，KB',
	`apk_url`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '安卓包下载地址',

	`is_force`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否强制更新',
	
	`create_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发布人',
	`create_time`	DATETIME				NULL									COMMENT '发布时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发布IP',

	PRIMARY KEY (`id`),
	KEY `app_id` (`app_id`),
	KEY `platform_id` (`platform_id`),
	KEY `version` (`version`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='软件版本发布记录';

DROP TABLE IF EXISTS `contract_template`;
CREATE TABLE `contract_template` (
	`id` 			SMALLINT UNSIGNED 		NOT NULL AUTO_INCREMENT					COMMENT '合同模板ID',
	`type`			TINYINT UNSIGNED		NOT NULL								COMMENT '合同类型：0=EULA,1=VIP,2=总监,3=经理',
	`title`			VARCHAR(64)				NOT NULL								COMMENT '合同标题',
	`content`		TEXT															COMMENT '合同内容',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '模板状态 0=草稿,1=发布,',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`update_time`	DATETIME				NULL									COMMENT '更新时间 @TIMESTAMP-UPDATE',
	`create_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`),
	KEY `type` (`type`)
)	Engine=MyISAM
	DEFAULT CHARACTER SET=utf8 
	COLLATE=utf8_general_ci
	COMMENT='合同模板 @NOENTITY';