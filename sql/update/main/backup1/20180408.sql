/**
 * 时间戳修复
 */

ALTER TABLE `vip_card` DROP COLUMN `coin`;
ALTER TABLE `vip_card` DROP COLUMN `rcv_wechat_id`;
ALTER TABLE `vip_card` DROP COLUMN `recive_time`;
ALTER TABLE `vip_card` CHANGE COLUMN `status` `status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'VIP卡片状态：0 新卡,1使用';

ALTER TABLE `cheif_card` DROP COLUMN `rcv_wechat_id`;
ALTER TABLE `cheif_card` DROP COLUMN `rcv_buff_id`;
ALTER TABLE `cheif_card` DROP COLUMN `recive_time`;
ALTER TABLE `cheif_card` CHANGE COLUMN `status` `status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'VIP卡片状态：0 新卡,1使用';



