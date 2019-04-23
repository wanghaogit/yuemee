/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */

DROP TABLE IF EXISTS `catagory`;
CREATE TABLE `catagory` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '行业ID',
	`parent_id`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '上级ID',
	`name`				VARCHAR(32)				NOT NULL								COMMENT '行业名称',

	`manager_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '管理员，一级审批权限',
	`supplier_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '绑定供应商ID',

	`is_hidden`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否隐藏类目',
	`is_internal`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否内部专区（VIP可见）',
	`is_private`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否供应商专区',

	`p_order`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '排序',

	`icon`				TEXT					NULL									COMMENT '图标',

	`gratio_dead`		NUMERIC(10,6)			NOT NULL DEFAULT 0.03					COMMENT '毛利死线',
	`gratio_warn`		NUMERIC(10,6)			NOT NULL DEFAULT 0.1					COMMENT '毛利报警',

	`rratio_system`		NUMERIC(10,6)			NOT NULL DEFAULT 0.3					COMMENT '平台佣金比例',

	`create_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	KEY `parent_id` (`parent_id`)
)	Engine=InnoDB
	DEFAULT CHARACTER SET=utf8 
	COLLATE=utf8_general_ci
	COMMENT='商品分类';
