/**
 * 时间戳修复
 */


ALTER TABLE `user_wechat` CHANGE COLUMN `create_time` `create_time_old` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间 @CREATE-TIMESTAMP';
ALTER TABLE `user_wechat` ADD COLUMN `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间 @CREATE-TIMESTAMP' AFTER `create_time_old`;
UPDATE `user_wechat` SET create_time = FROM_UNIXTIME(create_time_old);
ALTER TABLE `user_wechat` DROP COLUMN `create_time_old`;

ALTER TABLE `user_wechat` CHANGE COLUMN `update_time` `update_time_old` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间 @CREATE-TIMESTAMP';
ALTER TABLE `user_wechat` ADD COLUMN `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间 @CREATE-TIMESTAMP' AFTER `update_time_old`;
UPDATE `user_wechat` SET update_time = FROM_UNIXTIME(update_time_old);
ALTER TABLE `user_wechat` DROP COLUMN `update_time_old`;

UPDATE `user_wechat` SET create_time = UNIX_TIMESTAMP(),update_time = UNIX_TIMESTAMP();

TRUNCATE TABLE `device_user`;
ALTER TABLE `device_user` CHANGE COLUMN `create_time` `create_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间 @CREATE-TIMESTAMP' ;
ALTER TABLE `device_user` CHANGE COLUMN `update_time` `update_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间 @CREATE-TIMESTAMP' ;
