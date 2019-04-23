/**
 * 商品排期
 */
ALTER TABLE `sku` ADD COLUMN `att_newbie` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '新手专享属性：新手限购一件' AFTER `att_refund`;
