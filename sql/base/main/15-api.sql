/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-3-14
 */
DROP TABLE IF EXISTS `applet`;
CREATE TABLE `applet` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '应用ID',
	`user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '归属用户ID',
	`type`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '应用类型：0系统,1VIP，2总监,3经理,4供应商',

	`name`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '名称',

	`token`			CHAR(16)				NOT NULL DEFAULT ''						COMMENT '调用Token',
	`secret`		CHAR(16)				NOT NULL DEFAULT ''						COMMENT '调用密钥',

	`callback`		VARCHAR(1024)			NOT NULL DEFAULT ''						COMMENT '回调地址',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '应用状态，0未提交,1待审核,2已审核,3已关闭',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	UNIQUE KEY `token` (`token`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='应用';

DROP TABLE IF EXISTS `applet_acl`;
CREATE TABLE `applet_acl` (
	`applet_id`			INT UNSIGNED			NOT NULL		 						COMMENT '应用ID',

	`is_admin`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否后台（操作别人数据）',

	`upload`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否允许上传',
	`upload_avatar`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否允许头像',
	`upload_base_mat`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否允许基础素材',
	`upload_spu_mat`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否允许SPU素材',
	`upload_sku_mat`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否允许SKU素材',
	`upload_item_mat`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否允许商品素材',
	`upload_inv_mat`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否允许邀请素材',
	`upload_tuan_mat`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否允许团购素材',
	
	PRIMARY KEY (`applet_id`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='应用';