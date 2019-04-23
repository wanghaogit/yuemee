/**
 * 订单ID升位
 * 素材清理，排序，默认
 */
ALTER TABLE `order` CHANGE COLUMN `id` `id` VARCHAR(16) NOT NULL COMMENT '订单ID';
ALTER TABLE `order_afs` CHANGE COLUMN `order_id` `order_id` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '订单ID';
ALTER TABLE `order_item` CHANGE COLUMN `order_id` `order_id` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '订单ID';
ALTER TABLE `order_ticket` CHANGE COLUMN `order_id` `order_id` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '订单ID';
ALTER TABLE `rebate` CHANGE COLUMN `order_id` `order_id` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '订单ID';
ALTER TABLE `catagory` CHANGE COLUMN `create_time` `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间 @TIMESTAMP-CREATE';

ALTER TABLE ext_spu_material DROP COLUMN `file_path`;
ALTER TABLE ext_spu_material DROP COLUMN `thumb_path`;
ALTER TABLE ext_sku_material DROP COLUMN `file_path`;
ALTER TABLE ext_sku_material DROP COLUMN `thumb_path`;
ALTER TABLE sku_material DROP COLUMN `file_name`;
ALTER TABLE spu_material DROP COLUMN `file_name`;
ALTER TABLE `sku_material` CHANGE COLUMN `create_time` `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间 @TIMESTAMP-CREATE';
ALTER TABLE `sku_material` CHANGE COLUMN `audit_time` `audit_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核时间';
ALTER TABLE `spu_material` CHANGE COLUMN `create_time` `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间 @TIMESTAMP-CREATE';
ALTER TABLE `spu_material` CHANGE COLUMN `audit_time` `audit_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核时间';

ALTER TABLE sku_material ADD COLUMN `p_order` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '内部排序' AFTER `is_default`;
ALTER TABLE spu_material ADD COLUMN `p_order` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '内部排序' AFTER `is_default`;
ALTER TABLE ext_sku_material ADD COLUMN `p_order` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '内部排序' AFTER `is_default`;
ALTER TABLE ext_spu_material ADD COLUMN `p_order` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '内部排序' AFTER `is_default`;
