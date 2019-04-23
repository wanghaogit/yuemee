/**
 * 品类调整
 */
INSERT INTO `catagory` (`id`,`parent_id`,`name`,`create_user`,`create_time`,`create_from`)
VALUES (7,0,'礼包专区',1,UNIX_TIMESTAMP(),2130706433),
(701,7,'大礼包',1,UNIX_TIMESTAMP(),2130706433);

UPDATE sku SET catagory_id = 701 WHERE catagory_id = 7;

ALTER TABLE `order` ADD COLUMN `shop_code` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '子店铺代码' AFTER `supplier_id`;
ALTER TABLE `order` ADD COLUMN `t_trans` NUMERIC(16,4) NOT NULL DEFAULT 0 COMMENT '运费' AFTER `t_online`;