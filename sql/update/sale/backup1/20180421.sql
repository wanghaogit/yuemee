/**
 * 商品排期
 */
DROP TABLE IF EXISTS `sku_task`;
CREATE TABLE `sku_task` (
	`id`				INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '任务ID',
	`sku_id` 			INT UNSIGNED 			NOT NULL								COMMENT '上架ID',

	-- 任务影响标志
	`uf_title`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：标题',
	`uf_subtitle`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：子标题',
	`uf_price`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：平台价格',
	`uf_qty`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：库存',
	`uf_limit`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：限购',
	`uf_rebate`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务影响标志：用户返利',

	-- S0	任务启动前的状态缓存
	`s0_title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '状态0:标题',
	`s0_subtitle`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '状态0:子标题',
	`s0_price`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态0:售卖价',
	`s0_qty`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '状态0:库存',
	`s0_limit`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '状态0:限购',
	`s0_rebate`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态0:用户返利',

	-- S1	任务启动后替换值
	`s1_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '启动S1时间',

	`s1_title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '状态1:标题',
	`s1_subtitle`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '状态1:子标题',
	`s1_price`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态1:售卖价',
	`s1_qty`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '状态1:库存',
	`s1_limit`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '状态1:限购',
	`s1_rebate`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态1:用户返利',

	-- S2	任务结束后替换值
	`s2_time`			BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '启动S2时间',
	`s2_method`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '状态2操作：0恢复S0,1使用S2,2下架',

	`s2_title`			VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '状态2:标题',
	`s2_subtitle`		VARCHAR(128)			NOT NULL DEFAULT ''						COMMENT '状态2:子标题',
	`s2_price`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态2:售卖价',
	`s2_qty`			INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '状态2:库存',
	`s2_limit`			SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '状态2:限购',
	`s2_rebate`			NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '状态2:用户返利',
	
	`status`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '任务状态：0待审,1拒绝,2删除,3批准,4排队,5启动,6结束,7过期',

	`create_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建人',
	`create_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	`audit_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核人',
	`audit_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核时间',
	`audit_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',

	`review_user`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '复核人',
	`review_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '复核时间',
	`review_from`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '复核IP',
	PRIMARY KEY (`id`),
	KEY `sku_id` (`sku_id`),
	KEY `s1_time` (`s1_time`),
	KEY `s2_time` (`s2_time`),
	KEY `status` (`status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='SKU定时任务';