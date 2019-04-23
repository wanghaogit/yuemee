/**
 * 时间戳修复
 */
ALTER TABLE `user_material` CHANGE COLUMN `sku_id` `sku_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联SKUID';

DROP TABLE IF EXISTS `cms_material`;
CREATE TABLE `cms_material` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '素材ID',
	`column_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '关联栏目ID',
	`article_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '关联文章ID',

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
	KEY `column_id` (`column_id`),
	KEY `article_id` (`article_id`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='CMS素材';