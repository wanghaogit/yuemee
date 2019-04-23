/**
 * 价格体系调整
 */
ALTER TABLE `sku` ADD COLUMN `price_market` NUMERIC(16,4) NOT NULL DEFAULT 0.0 COMMENT '显示用的市场价' AFTER `price_ref`;
