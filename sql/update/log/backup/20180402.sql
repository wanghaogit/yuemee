
DROP TABLE IF EXISTS `shelf_counter`;
DROP TABLE IF EXISTS `sku_counter`;
CREATE TABLE `sku_counter` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`sku_id` 		INT UNSIGNED 			NOT NULL								COMMENT 'SKU_ID',
	`time_id`		INT UNSIGNED			NOT NULL								COMMENT '时间戳,与Z_BASETIME相差小时数',		

	`t_view`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '累积：访问',
	`t_like`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '累积：喜欢次数',
	`t_sale`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '累积：卖出',
	`t_share`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '累积：分享',

	PRIMARY KEY (`id`),
	KEY `sku_id` (`sku_id`),
	KEY `time_id` (`time_id`),
	UNIQUE KEY `sku_time_id` (`sku_id`,`time_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='SKU统计数据';


DROP TABLE IF EXISTS `share_counter`;
CREATE TABLE `share_counter` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '记录ID',
	`share_id` 		INT UNSIGNED 			NOT NULL								COMMENT '分享ID',
	`time_id`		INT UNSIGNED			NOT NULL								COMMENT '时间戳,与Z_BASETIME相差小时数',		

	`t_view`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '累积：访问',
	`t_like`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '累积：喜欢次数',
	`t_sale`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '累积：购买次数',

	PRIMARY KEY (`id`),
	KEY `share_id` (`share_id`),
	KEY `time_id` (`time_id`),
	UNIQUE KEY `share_time_id` (`share_id`,`time_id`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='分享统计数据';
