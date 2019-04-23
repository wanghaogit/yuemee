
ALTER TABLE `spread_userinfo` ADD COLUMN `order_id` varchar(16) NULL DEFAULT NULL COMMENT '订单Id'	AFTER `address`;

ALTER TABLE `spread_userinfo` ADD UNIQUE (`order_id`);
