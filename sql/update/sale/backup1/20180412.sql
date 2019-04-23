/**
 * 品类调整
 */
ALTER TABLE `ext_spu` ADD COLUMN `intro_back` TEXT NULL COMMENT '备份内容' AFTER `intro`;
ALTER TABLE `ext_sku` ADD COLUMN `intro_back` TEXT NULL COMMENT '备份内容' AFTER `intro`;

UPDATE `ext_spu` SET `intro_back` = `intro`;
UPDATE `ext_sku` SET `intro_back` = `intro`;

DROP TABLE IF EXISTS `hot_search`;
CREATE TABLE `hot_search` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '关键词ID',

	`title`			VARCHAR(32)				NOT NULL								COMMENT '关键词',
	`color`			VARCHAR(8)				NOT NULL DEFAULT '#000000'				COMMENT '显示颜色',
	`size`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '显示大小',
	`p_order`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '排序',
	
	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	PRIMARY KEY (`id`)
)	Engine=MyISAM
	DEFAULT CHARACTER SET=utf8 
	COLLATE=utf8_general_ci
	COMMENT='热搜关键词 @NOENTITY';
