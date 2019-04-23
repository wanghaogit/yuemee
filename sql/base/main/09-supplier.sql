/**
 * 阅米数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '用户ID',
	`user_id`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '前台用户ID',

	`name`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '名称',
	`alias`			VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '内部代码',

	`password`		CHAR(40)				NOT NULL DEFAULT ''						COMMENT '工作平台密码',
	`token`			CHAR(16)				NOT NULL DEFAULT ''						COMMENT '登陆令牌 @UNIQUE',

	`pi_enable`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '数据泵入开关',
	`pi_url`		VARCHAR(1024)			NOT NULL DEFAULT ''						COMMENT '数据泵入接口',
	`pi_token`		VARCHAR(48)				NOT NULL DEFAULT ''						COMMENT '数据泵入令牌',
	`pi_secret`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '数据泵入密钥',
	`pi_interval`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '数据泵入频率 0手动,其它乘以30分钟',
	`pi_catagory`	VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '分类表名',
	`pi_supplier`	VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '供应商表名',

	`po_enable`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '数据泵出开关',
	`po_applet`		INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '数据泵出AppletId',
	`po_url`		VARCHAR(1024)			NOT NULL DEFAULT ''						COMMENT '数据泵出接口',
	`po_token`		VARCHAR(48)				NOT NULL DEFAULT ''						COMMENT '数据泵入令牌',
	`po_secret`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '数据泵入密钥',

	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '供应商状态 0停用,1启用',

	`create_time`	DATETIME				NULL									COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',

	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `token` (`token`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='供应商';

DROP TABLE IF EXISTS `supplier_cert`;
CREATE TABLE `supplier_cert` (
	`supplier_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '供应商ID',
	
	`corp_name`		VARCHAR(64)				NOT NULL DEFAULT ''						COMMENT '公司名',
	`corp_serial`	VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '营业执照号码',
	`corp_law`		VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '公司法人',
	`corp_region`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '注册地',
	`corp_address`	VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '注册地址',
	`corp_money`	NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '注册资金',
	`corp_expire`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '证件有效期',
	`corp_image`	VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '营业执照照片',
	`corp_status`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '实名状态：0未,1待审,2已审,3过期',

	`bank_id`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '开户银行',
	`bank_name`		VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '开户银行详细信息',
	`bank_code`		VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '开户银行代码',
	`bank_card`		VARCHAR(32)				NOT NULL DEFAULT ''						COMMENT '银行账号',
	`bank_image`	VARCHAR(256)			NOT NULL DEFAULT ''						COMMENT '开户许可证图片',
	`bank_status`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '实名状态：0未,1待审,2已审',

	`bond_money`	NUMERIC(16,4)			NOT NULL DEFAULT 0.0					COMMENT '保证金金额',
	`bond_order`	VARCHAR(12)				NOT NULL DEFAULT ''						COMMENT '保证金支付订单',
	`bond_time`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '保证金支付时间',
	`bond_info`		VARCHAR(512)			NOT NULL DEFAULT ''						COMMENT '保证金备注',
	`bond_status`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '保证金状态：0未,1支付,2退还',

	PRIMARY KEY (`supplier_id`),
	KEY `corp_name` (`corp_name`),
	KEY `corp_status` (`corp_status`),
	KEY `bank_status` (`bank_status`),
	KEY `bond_status` (`bond_status`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='供应商认证';

DROP TABLE IF EXISTS `ticket`;
CREATE TABLE `ticket` (
	`id` 			INT UNSIGNED 			NOT NULL AUTO_INCREMENT					COMMENT '优惠券ID',
	`supplier_id`	INT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '供应商ID',

	`type`			TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '类型:0无效,1满减',

	`money`			NUMERIC(16,4)			NOT NULL								COMMENT '面额',
	`qty_all`		INT UNSIGNED			NOT NULL								COMMENT '发行数量',
	`qty_got`		INT UNSIGNED			NOT NULL								COMMENT '已领取数量',
	`qty_use`		INT UNSIGNED			NOT NULL								COMMENT '已使用数量',
	
	-- 领取规则
	`got_style`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '领取方式：0手动领取,1自动领取',
	`got_count`		SMALLINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '领取数量',

	-- 满减规则数据
	`mj_money`		NUMERIC(16,4)			NOT NULL DEFAULT 0						COMMENT '满多少',

	-- 发行数据
	`pub_start`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发行开始时间，0立即开始',
	`pub_end`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '发行结束时间，0领完结束',

	-- 使用数据
	`use_auto`		TINYINT UNSIGNED		NOT NULL DEFAULT 1						COMMENT '是否自动使用',
	`use_multi`		TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '叠加规则：0不叠加,1可叠加',
	`use_exclus`	TINYINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '排他规则：0不排他,1本券排他,2内部排他,3全局排他',
	`use_start`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '使用开始时间，0立即开始',
	`use_end`		BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '使用结束时间，0用不过期',

	-- 状态
	`status`		SMALLINT UNSIGNED		NOT NULL DEFAULT 0						COMMENT '优惠券状态 0草稿,1提交,2审批,3拒绝',
	
	-- 审计数据
	`create_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建时间 @TIMESTAMP-CREATE',
	`create_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '创建IP',
	`audit_time`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核时间',
	`audit_from`	BIGINT UNSIGNED			NOT NULL DEFAULT 0						COMMENT '审核IP',

	PRIMARY KEY (`id`),
	KEY `supplier_id` (`supplier_id`),
	KEY `pub_start` (`pub_start`),
	KEY `pub_end` (`pub_end`)
) Engine=InnoDB
  DEFAULT CHARACTER SET=utf8 
  COLLATE=utf8_general_ci
  COMMENT='优惠券';
