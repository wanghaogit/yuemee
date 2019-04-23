/**
 * 订单冗余
 * 素材清理，排序，默认
 */


ALTER TABLE `order_item` ADD COLUMN `price_base` NUMERIC(16,4) NOT NULL DEFAULT 0 COMMENT '下单时的基准价' AFTER `money`;
ALTER TABLE `order_item` ADD COLUMN `rebate_user` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '本单返佣目标用户' AFTER `price_base`;
ALTER TABLE `order_item` ADD COLUMN `rebate_vip` NUMERIC(16,4) NOT NULL DEFAULT 0 COMMENT '本单返佣总金额' AFTER `rebate_user`;
ALTER TABLE `order` CHANGE COLUMN `depend_id` `depend_id` VARCHAR(16) NOT NULL COMMENT '主订单ID' AFTER `is_primary`; 

TRUNCATE TABLE `order`;
TRUNCATE TABLE `order_afs`;
TRUNCATE TABLE `order_item`;
TRUNCATE TABLE `rebate`;
TRUNCATE TABLE `cart`;
