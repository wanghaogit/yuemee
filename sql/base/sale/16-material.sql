/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-3-14
 */
DROP TABLE IF EXISTS `spu_material`;
CREATE TABLE `spu_material` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '素材ID',
	`spu_id`		INT UNSIGNED			NOT NULL								COMMENT 'SPUID',

	`type`			INT UNSIGNED			NOT NULL								COMMENT '素材类型：0主图,1内容,2活动',

	`file_name`		VARCHAR(64)				NOT NULL								COMMENT '文件名',
	`file_size`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '文件大小：字节',
	`file_url`		VARCHAR(512)			NOT NULL								COMMENT '访问路径',

	`image_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片宽度',
	`image_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片高度',

	`thumb_url`		VARCHAR(512)			NOT NULL								COMMENT '缩略图路径',
	`thumb_size`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '缩略图大小：字节',
	`thumb_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图宽度',
	`thumb_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图高度',

	`is_default`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否默认素材',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '素材状态 0待审,1已审,2删除',

	`create_user`	INT UNSIGNED			NOT NULL								COMMENT '创建人',
	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`audit_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`	DATETIME				NULL									COMMENT '审核时间',
	`audit_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',
	PRIMARY KEY (`id`),
	KEY `spu_id` (`spu_id`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='SPU素材';

DROP TABLE IF EXISTS `sku_material`;
CREATE TABLE `sku_material` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '素材ID',
	`sku_id`		INT UNSIGNED			NOT NULL								COMMENT 'SKUID',

	`type`			INT UNSIGNED			NOT NULL								COMMENT '素材类型：0主图,1内容,2活动',

	`file_name`		VARCHAR(64)				NOT NULL								COMMENT '文件名',
	`file_size`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '文件大小：字节',
	`file_url`		VARCHAR(512)			NOT NULL								COMMENT '访问路径',

	`image_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片宽度',
	`image_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片高度',

	`thumb_url`		VARCHAR(512)			NOT NULL								COMMENT '缩略图路径',
	`thumb_size`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '缩略图大小：字节',
	`thumb_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图宽度',
	`thumb_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图高度',

	`is_default`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否默认素材',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '素材状态 0待审,1已审,2删除',

	`create_user`	INT UNSIGNED			NOT NULL								COMMENT '创建人',
	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`audit_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`	DATETIME				NULL									COMMENT '审核时间',
	`audit_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',
	PRIMARY KEY (`id`),
	KEY `sku_id` (`sku_id`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='SKU素材';

DROP TABLE IF EXISTS `shelf_material`;
CREATE TABLE `shelf_material` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '素材ID',
	`shelf_id`		INT UNSIGNED			NOT NULL								COMMENT '货架ID',

	`type`			INT UNSIGNED			NOT NULL								COMMENT '素材类型：0主图,1内容,2活动',

	`file_name`		VARCHAR(64)				NOT NULL								COMMENT '文件名',
	`file_size`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '文件大小：字节',
	`file_url`		VARCHAR(512)			NOT NULL								COMMENT '访问路径',

	`image_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片宽度',
	`image_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '图片高度',

	`thumb_url`		VARCHAR(512)			NOT NULL								COMMENT '缩略图路径',
	`thumb_size`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '缩略图大小：字节',
	`thumb_width`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图宽度',
	`thumb_height`	SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '缩略图高度',

	`is_default`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否默认素材',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '素材状态 0待审,1已审,2删除',

	`create_user`	INT UNSIGNED			NOT NULL								COMMENT '创建人',
	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`audit_user`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`	DATETIME				NULL									COMMENT '审核时间',
	`audit_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',
	PRIMARY KEY (`id`),
	KEY `shelf_id` (`shelf_id`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='货架素材';
