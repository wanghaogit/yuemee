/**
 * SKU 调整
 */

ALTER TABLE `sku` ADD COLUMN `subtitle` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '副标题' AFTER `title`;
ALTER TABLE `sku` ADD COLUMN `att_shipping` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '包邮属性：0无邮费,1智能运费' AFTER `att_refund`;
ALTER TABLE `sku` ADD COLUMN `att_default` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否默认SKU' AFTER `att_shipping`;

ALTER TABLE `sku` DROP COLUMN `price_vip`;
ALTER TABLE `sku` DROP COLUMN `video`;
ALTER TABLE `spu` DROP COLUMN `video`;
ALTER TABLE `ext_sku` DROP COLUMN `video`;
ALTER TABLE `ext_spu` DROP COLUMN `video`;

ALTER TABLE `order_item` ADD COLUMN `shop_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '店铺ID' AFTER `supplier_id`;
ALTER TABLE `order_item` ADD COLUMN `share_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享用户ID' AFTER `share_id`;
ALTER TABLE `order_item` ADD COLUMN `share_user_vip` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享者是否VIP' AFTER `share_user_id`;
ALTER TABLE `order_item` ADD COLUMN `share_cheif_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享者归属总监' AFTER `share_user_vip`;
ALTER TABLE `order_item` ADD COLUMN `share_director_id` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享者归属总经理' AFTER `share_user_vip`;
