
ALTER TABLE `spread_userinfo` ADD COLUMN `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0未购买,1已购买' AFTER `update_time`;
