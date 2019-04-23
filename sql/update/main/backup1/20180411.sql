/**
 * 修复拼写错误
 */

ALTER TABLE `vip_card` DROP COLUMN `chief_id`;
ALTER TABLE `vip` CHANGE COLUMN `chief_id` `cheif_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '归属总监ID';
