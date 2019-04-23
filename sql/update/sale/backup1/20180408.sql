/**
 * 订单调整
 * 素材清理，排序，默认
 */

ALTER TABLE `order` ADD COLUMN `ext_order_id` VARCHAR(32)	NOT NULL DEFAULT '' COMMENT '外部订单ID' AFTER `create_from`;
ALTER TABLE `order` ADD COLUMN `ext_status` TINYINT UNSIGNED	NOT NULL DEFAULT 0 COMMENT '外部订单状态：0未创建,1失败,2成功,3...参见文档' AFTER `ext_order_id`;

ALTER TABLE `order_afs` ADD COLUMN `ext_order_id` VARCHAR(32)	NOT NULL DEFAULT '' COMMENT '外部订单ID' AFTER `create_from`;
ALTER TABLE `order_afs` ADD COLUMN `ext_status` TINYINT UNSIGNED	NOT NULL DEFAULT 0 COMMENT '外部订单状态：0未创建,1失败,2成功,3...参见文档' AFTER `ext_order_id`;

