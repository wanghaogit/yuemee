/**
 * 品类调整
 */

ALTER TABLE `ext_spu` ADD COLUMN `ext_shop_code` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '外部店铺标识' AFTER `supplier_id`;
UPDATE `ext_spu` SET ext_shop_code = 'JD' WHERE `bn` LIKE 'JD-%';
UPDATE `ext_spu` SET ext_shop_code = 'YX' WHERE `bn` LIKE 'YX-%';
