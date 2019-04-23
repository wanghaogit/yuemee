/**
 * 订单支付方式 调整
 */

ALTER TABLE `order` ADD COLUMN `t_card` NUMERIC(16,4) NOT NULL DEFAULT 0 COMMENT '当前订单群总卡片支付部分' AFTER `t_ticket`;
ALTER TABLE `order` ADD COLUMN `c_card` NUMERIC(16,4) NOT NULL DEFAULT 0 COMMENT '当前订单卡片支付部分' AFTER `c_ticket`;
ALTER TABLE `order` ADD COLUMN `pay_card` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '当前订单支付卡序列号' AFTER `c_amount`;
