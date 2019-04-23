/**
 * 增加和IM的对接
 */

ALTER TABLE `user` ADD `imuid` VARCHAR(24) NOT NULL DEFAULT '' COMMENT 'IM系统账号，前缀：u_' AFTER `level_s`;
ALTER TABLE `cheif` ADD `imgid` VARCHAR(24) NOT NULL DEFAULT '' COMMENT 'IM系统群号，前缀：c_' AFTER `invite_code`;
ALTER TABLE `director` ADD `imgid` VARCHAR(24) NOT NULL DEFAULT '' COMMENT 'IM系统群号，前缀：d_' AFTER `invite_code`;
ALTER TABLE `supplier` ADD `imuid` VARCHAR(24) NOT NULL DEFAULT '' COMMENT 'IM系统账号，前缀：s_' AFTER `alias`;
ALTER TABLE `supplier_user` ADD `imuid` VARCHAR(24) NOT NULL DEFAULT '' COMMENT 'IM系统账号，前缀：su_' AFTER `role_id`;
ALTER TABLE `rbac_admin` ADD `imuid` VARCHAR(24) NOT NULL DEFAULT '' COMMENT 'IM系统账号，前缀：a_' AFTER `role_id`;

ALTER TABLE `cheif` DROP COLUMN `invite_code`;
ALTER TABLE `director` DROP COLUMN `invite_code`;

ALTER TABLE `user_finance` ADD COLUMN `passwd` VARCHAR(40) NOT NULL DEFAULT '' COMMENT '支付密码' AFTER `recruit_alt`;
