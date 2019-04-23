/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */

DROP TABLE IF EXISTS `share_template`;
CREATE TABLE `share_template` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '模板ID',

	`name`				VARCHAR(32)				NOT NULL								COMMENT '模板名称',
	
	`body_path`			VARCHAR(256)			NOT NULL								COMMENT '底图路径',
	`body_url`			VARCHAR(256)			NOT NULL								COMMENT '底图预览URL',
	`body_width`		SMALLINT UNSIGNED		NOT NULL								COMMENT '底图宽度',
	`body_height`		SMALLINT UNSIGNED		NOT NULL								COMMENT '底图高度',

	`is_multiple`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否支持多商品',

	`tpl_path`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT 'HTML模板路径',
	`tpl_content`		TEXT					NULL									COMMENT 'HTML模板代码',
	
	`title_config`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '商品文案配置：x,y,w,h,length,size,color',
	`material_config`	VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '商品素材配置：count,x,y,w,h,padding',
	`name_config`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '个人昵称配置：open,x,y,size,color',
	`avatar_config`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '个人头像配置：open,x,y,w,h',
	`price_config`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '平台价格配置：open,x,y,size,color',
	`market_config`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '参考价格配置：open,x,y,size,color',

	`status`			TINYINT	UNSIGNED		NOT NULL DEFAULT 1						COMMENT '模板状态：0停用,1草稿,2启用',

	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`)
)	Engine=MyISAM
	DEFAULT CHARACTER SET=utf8
	COLLATE=utf8_general_ci
	COMMENT='分享模板';

DROP TABLE IF EXISTS `share`;
CREATE TABLE `share` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '分享ID',
	`user_id` 		INT UNSIGNED 			NOT NULL								COMMENT '用户ID',
	`template_id`	INT UNSIGNED 			NOT NULL								COMMENT '使用模板ID',

	`shelf_id`		INT UNSIGNED			NOT NULL								COMMENT '货架ID，单品存这里，多品存share_item',	

	`title`			VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '分享文案',
	`materials`		VARCHAR(512)			NOT NULL DEFAULT ''						COMMENT '分享素材，区域:Id,...',

	`page_url`		VARCHAR(1024)			NOT NULL DEFAULT 0						COMMENT '页面URL',
	`image_url`		VARCHAR(1024)			NOT NULL DEFAULT 0						COMMENT '图片URL',

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `shelf_id` (`shelf_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='商品分享';

DROP TABLE IF EXISTS `share_item`;
CREATE TABLE `share_item` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`share_id` 		INT UNSIGNED 			NOT NULL								COMMENT '分享ID',
	`shelf_id`		INT UNSIGNED			NOT NULL								COMMENT '货架ID',	
	PRIMARY KEY (`id`),
	KEY `share_id` (`share_id`),
	KEY `shelf_id` (`shelf_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='分享商品列表';
