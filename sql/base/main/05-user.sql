/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '用户ID',
	`invitor_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '推荐人ID',
	
	`mobile`		VARCHAR(11)				NOT NULL DEFAULT ''						COMMENT '手机号码 @UNIQUE',
	`password`		CHAR(40)				NOT NULL DEFAULT ''						COMMENT '登陆密码',
	`token`			CHAR(16)				NOT NULL DEFAULT ''						COMMENT '登陆令牌，APP用 @UNIQUE',
	
	`name`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '用户昵称',
	`avatar`		VARCHAR(512)			NOT NULL DEFAULT ''						COMMENT '用户头像,URL',
	`gender`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '性别,0未知,1男,2女',
	`birth`			DATE					NOT NULL DEFAULT '0000-00-00'			COMMENT '出生年月日',
	`region_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '所在地区',

	`level_u`		TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '用户级别：0无效,1普通',
	`level_v`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT 'VIP级别：0无,1正式,2过期',
	`level_c`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '总监级别：0无,1正式,2过期',
	`level_d`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '经理级别：0无,1正式,2过期',
	`level_t`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '员工级别：0无,1员工,2组长,3经理,4离职',
	`level_a`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '后台级别：0无,1普通,2超级',
	`level_s`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '供应商级别：0无,1间接,2直接',

	`reg_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '注册时间 @TIMESTAMP-CREATE',
	`reg_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '注册IP',
	`reg_seed`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '注册种子参数',
	`reg_param`		VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '注册其它参数',

	PRIMARY KEY (`id`),
	KEY `invitor_id` (`invitor_id`),
	KEY `mobile` (`mobile`),
	KEY `token` (`token`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='用户';

DROP TABLE IF EXISTS `user_cert`;
CREATE TABLE `user_cert` (
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',
	
	`card_pic1`		VARCHAR(512)			NOT NULL DEFAULT ''						COMMENT '身份证图像，正面JPG',
	`card_pic2`		VARCHAR(512)			NOT NULL DEFAULT ''						COMMENT '身份证图像，反面JPG',

	`card_no`		VARCHAR(18)				NOT NULL DEFAULT ''						COMMENT '身份证号码 @UNIQUE',
	`card_name`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '身份证上的姓名',
	`card_exp`		DATE					NOT NULL DEFAULT '0000-00-00'			COMMENT '识别出来的过期时间',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '认证状态 0=草稿,1=待审,2=通过,3=拒绝',

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`audit_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核时间',
	`audit_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',

	PRIMARY KEY (`user_id`),
	KEY `card_no` (`card_no`),
	KEY `status` (`status`),
	KEY `create_time` (`create_time`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='用户实名认证';

DROP TABLE IF EXISTS `user_bank`;
CREATE TABLE `user_bank` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',

	`bank_id`		SMALLINT UNSIGNED		NOT NULL								COMMENT '银行ID',
	`region_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '开户地区ID',
	`bank_name`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '开户行名称',
	`card_no`		VARCHAR(128)			NOT NULL								COMMENT '卡号',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '用户状态 0删除,1可用,2正确,3错误',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`audit_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`	DATETIME				NULL									COMMENT '审核时间',
	`audit_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='用户绑定银行卡';

DROP TABLE IF EXISTS `user_address`;
CREATE TABLE `user_address` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID',

	`region_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '地区ID',
	`address`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '详细地址',
	`contacts`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '联系人',
	`mobile`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '联系电话',
	`is_default`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否默认',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '状态 0删除,1可用',
	
	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `status` (`status`)
) Engine=MyISAM
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='用户收货地址';

DROP TABLE IF EXISTS `user_wechat`;
CREATE TABLE `user_wechat` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '认证记录ID',
	`user_id`		INT UNSIGNED			NOT NULL								COMMENT '用户ID  @REF iedu_core.user.id',
	`invitor_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '推荐人ID',

	`union_id` 		VARCHAR(64)				NOT NULL								COMMENT '开放平台ID',
	`app_open_id` 	VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT 'APP的OpenId  @UNIQUE',
	`web_open_id` 	VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT 'Web的OpenId  @UNIQUE',

	`auth_code`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '授权密钥',
	`auth_update`	DATETIME				NULL									COMMENT '授权更新时间',
	`auth_expire`	DATETIME				NULL									COMMENT '授权有效期',

	`account`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '微信账号  @UNIQUE',
	`mobile`		VARCHAR(16)				NOT NULL DEFAULT ''						COMMENT '手机号码',

	`name`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '昵称',
	`avatar`		VARCHAR(2048)			NOT NULL DEFAULT ''						COMMENT '用户头像,URL',
	`gender`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '性别,0未知,1男,2女',
	`birth`			DATE					NULL									COMMENT '出生年月日',
	`region_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '所在地区',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`update_time`	DATETIME				NULL									COMMENT '更新时间 @TIMESTAMP-UPDATE',
	`update_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '更新 IP',

	`tag_seed`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '标签：裂变种子号',
	`tag_param`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '标签：裂变其它参数',

	PRIMARY KEY (`id`),
	UNIQUE KEY `union_id` (`union_id`),
	KEY `user_id` (`user_id`),
	KEY `tag_seed` (`tag_seed`)
)	Engine=InnoDB
	DEFAULT CHARACTER SET=utf8
	COLLATE=utf8_general_ci
	COMMENT='用户的微信授权';
