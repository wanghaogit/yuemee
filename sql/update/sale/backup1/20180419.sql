/**
 * SKU 调整
 */
ALTER TABLE `ext_sku` ADD COLUMN `lo_status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '内容本地化状态：0待处理,1失败,2成功' AFTER `intro`;
ALTER TABLE `ext_sku` ADD COLUMN `lo_error` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '内容本地化错误次数' AFTER `lo_status`;
ALTER TABLE `ext_sku` ADD COLUMN `lo_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '内容本地化处理时间' AFTER `lo_error`;
