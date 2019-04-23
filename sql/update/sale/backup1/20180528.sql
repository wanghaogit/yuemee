
ALTER TABLE `spu` ADD COLUMN `is_gift_set`			tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否大礼包：0否，1是'			AFTER `intro`;
ALTER TABLE `spu` ADD COLUMN `show_on_list`			tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示在列表页：0否，1是'	AFTER `is_gift_set`;
ALTER TABLE `spu` ADD COLUMN `att_overseas`			tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否支持发货到海外：0否，1是'	AFTER `show_on_list`;
ALTER TABLE `spu` ADD COLUMN `att_special_region`	tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否支持发货到偏远：0否，1是'	AFTER `att_overseas`;
