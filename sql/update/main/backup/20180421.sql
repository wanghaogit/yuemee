/**
 * VIP 系统
 * Author:  eglic
 * Created: 2018-4-17
 */

ALTER TABLE `vip_status` ADD COLUMN `type` TINYINT NOT NULL DEFAULT 0 COMMENT '状态来源，0=NONE,1=TEST,2=FREE,3=CARD,4=COIN,5=MONEY' AFTER `user_id`;
UPDATE `vip_status` SET `type` = 4;