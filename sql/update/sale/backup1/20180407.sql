/**
 * 订单调整
 * 素材清理，排序，默认
 */

ALTER TABLE `order` ADD COLUMN `trans_com` VARCHAR(32)	NOT NULL DEFAULT '' COMMENT '物流公司代码' AFTER `create_from`;
ALTER TABLE `order` ADD COLUMN `trans_trace` TEXT NULL COMMENT '物流详情' AFTER `trans_id`;

ALTER TABLE `order_afs` ADD COLUMN `req_trans_com` VARCHAR(32)	NOT NULL DEFAULT '' COMMENT '物流公司代码' AFTER `req_message`;
ALTER TABLE `order_afs` ADD COLUMN `req_trans_trace` TEXT NULL COMMENT '物流详情' AFTER `req_addr_id`;

