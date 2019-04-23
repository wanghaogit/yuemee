/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */
DROP TABLE `logistics`

DROP TABLE IF EXISTS `logistics`;
CREATE TABLE `logistics` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '物流ID',
	`order_id` 		CHAR(24)				NOT NULL								COMMENT '关联订单ID',

	`time_id`		CHAR(14)				NOT NULL								COMMENT '时间戳',
	`message`		VARCHAR(64)				NOT NULL								COMMENT '物流消息',
	
	`is_final`		TINYING UNSIGNED		NOT NULL DEFAULT 0						COMMENT '是否签收记录',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`update_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-UPDATE',

	PRIMARY KEY (`id`),
	KEY `order_id` (`order_id`),
	KEY `time_id` (`time_id`),
	KEY `is_final` (`is_final`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='商品分享';