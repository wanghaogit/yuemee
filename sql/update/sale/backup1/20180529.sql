
ALTER TABLE `order` ADD COLUMN `type`			tinyint(2) NOT NULL DEFAULT '0' COMMENT '订单类型：0普通订单，1大礼包订单，2系统补偿订单，3后台代下单'	AFTER `id`;

ALTER TABLE `order` ADD COLUMN `comment_user`	varchar(64) NULL DEFAULT NULL COMMENT '用户备注'	AFTER `trans_trace`;
ALTER TABLE `order` ADD COLUMN `comment_admin`	varchar(64) NULL DEFAULT NULL COMMENT '管理员备注'	AFTER `comment_user`;
