/**
 * 运营系统
 * Author:  eglic
 * Created: 2018-4-17
 */
DROP TABLE IF EXISTS `run_material`;
CREATE TABLE `run_material` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '素材ID',
	`page_id`		INT UNSIGNED			NOT NULL								COMMENT '页面ID',

	`file_name`		VARCHAR(64)				NOT NULL								COMMENT '文件名',
	`file_size`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '文件大小：字节',
	`file_url`		VARCHAR(512)			NOT NULL								COMMENT '访问路径',

	`image_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片宽度',
	`image_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片高度',

	`thumb_url`		VARCHAR(512)			NOT NULL								COMMENT '缩略图路径',
	`thumb_size`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '缩略图大小：字节',
	`thumb_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图宽度',
	`thumb_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图高度',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '素材状态 0待审,1已审,2删除',

	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`audit_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核时间',
	`audit_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',
	PRIMARY KEY (`id`),
	KEY `page_id` (`page_id`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='用户素材';