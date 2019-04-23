/**
 * 商品排期
 */
ALTER TABLE `sku` DROP COLUMN `online_time`;
ALTER TABLE `sku` DROP COLUMN `offline_time`;

ALTER TABLE `sku` ADD COLUMN `price_ratio` NUMERIC(8,6) NOT NULL DEFAULT 0.0 COMMENT '毛利' AFTER `price_market`;
ALTER TABLE `sku` DROP INDEX `supplier_id`;
ALTER TABLE `sku` ADD INDEX `status` (`status`);
