/**
 * 修复拼写错误
 */

ALTER TABLE `vip` ADD COLUMN `director_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '归属总经理ID' AFTER `cheif_id`;
