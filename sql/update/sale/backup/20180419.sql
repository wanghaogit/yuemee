/**
 * 字段调整
 * Author:  eglic
 * Created: 2018-4-17
 */

ALTER TABLE `sku` DROP COLUMN `price_market`;
ALTER TABLE `sku` CHANGE COLUMN `create_time` `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间 @CREATE-TIMESTAMP';
ALTER TABLE `sku` CHANGE COLUMN `update_time` `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间 @UPDATE-TIMESTAMP';
ALTER TABLE `sku` CHANGE COLUMN `audit_time` `audit_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核时间';

ALTER TABLE `spu` DROP COLUMN `price_base`;
ALTER TABLE `spu` DROP COLUMN `price_market`;
ALTER TABLE `spu` DROP COLUMN `price_sale`;
ALTER TABLE `spu` DROP COLUMN `price_rebate`;
ALTER TABLE `spu` DROP COLUMN `quantity`;
ALTER TABLE `spu` DROP COLUMN `is_gift`;
ALTER TABLE `spu` DROP COLUMN `is_bind`;
ALTER TABLE `spu` DROP COLUMN `is_zhiti`;
ALTER TABLE `spu` DROP COLUMN `is_virtual`;

ALTER TABLE `spu` CHANGE COLUMN `create_time` `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间 @CREATE-TIMESTAMP';
ALTER TABLE `spu` CHANGE COLUMN `update_time` `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间 @UPDATE-TIMESTAMP';
ALTER TABLE `spu` CHANGE COLUMN `audit_time` `audit_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核时间';
ALTER TABLE `spu` CHANGE COLUMN `online_time` `online_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '预定上架时间';
ALTER TABLE `spu` CHANGE COLUMN `offline_time` `offline_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '预定下架时间';

ALTER TABLE `shelf` DROP COLUMN `price_user`;
ALTER TABLE `shelf` CHANGE COLUMN `price_vipi` `price_vip` NUMERIC(16,4) NOT NULL DEFAULT 0.0 COMMENT 'VIP售价';
ALTER TABLE `shelf` CHANGE COLUMN `price_vips` `price_inv` NUMERIC(16,4) NOT NULL DEFAULT 0.0 COMMENT '邀请普通会员售价';
ALTER TABLE `shelf` DROP COLUMN `rebate_user`;
ALTER TABLE `shelf` DROP COLUMN `rebate_chief`;
ALTER TABLE `shelf` DROP COLUMN `rebate_director`;

ALTER TABLE `shelf` DROP COLUMN `coin_vipc`;
ALTER TABLE `shelf` DROP COLUMN `coin_vipd`;
ALTER TABLE `shelf` DROP COLUMN `coin_vipu`;
ALTER TABLE `shelf` DROP COLUMN `coin_vipi`;
ALTER TABLE `shelf` CHANGE COLUMN `coin_vips` `coin_inviter` NUMERIC(16,8) NOT NULL DEFAULT 0.0 COMMENT '邀请人奖励阅币';
ALTER TABLE `shelf` CHANGE COLUMN `check_vipi` `check_inviter` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否要求分析购买';
ALTER TABLE `shelf` DROP COLUMN `check_cheif`;
ALTER TABLE `shelf` DROP COLUMN `check_director`;
