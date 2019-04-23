/**
 * 购物车、订单系统、分享系统
 * Author:  eglic
 * Created: 2018-4-17
 */

ALTER TABLE `share` ADD COLUMN `director_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享归属总经理ID' AFTER `user_id`;
ALTER TABLE `share` ADD COLUMN `team_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享所属团队ID' AFTER `director_id`;
ALTER TABLE `share` ADD COLUMN `member_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享人的员工ID' AFTER `team_id`;
ALTER TABLE `share` ADD COLUMN `share_code` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '分享代码' AFTER `member_id`;

ALTER TABLE `cart` ADD COLUMN `share_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源分享ID' AFTER `sku_id`;
ALTER TABLE `cart` DROP COLUMN `inviter_id`;
ALTER TABLE `cart` DROP COLUMN `inviter_feed`;

ALTER TABLE `order` DROP COLUMN `inviter_id`;
ALTER TABLE `order` DROP COLUMN `inviter_feed`;

ALTER TABLE `order_item` ADD COLUMN `share_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源分享ID' AFTER `order_id`;

ALTER TABLE `sku` ADD COLUMN `rebate_vip` NUMERIC(16,4) NOT NULL DEFAULT 0.0 COMMENT 'VIP返佣' AFTER `price_ref`;
ALTER TABLE `sku` ADD COLUMN `specs` TEXT NULL COMMENT '规格' AFTER `title`;

ALTER TABLE `spu` ADD COLUMN `specs` TEXT NULL COMMENT '规格' AFTER `title`;
