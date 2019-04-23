/**
 * 功能 : 快捷下单
 * 作者：殷非非
 * 日期：2018-4-22
 * 修订@2018-04-22	: 
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `yuemi_sale`.`vip_order` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `vip_order`(
	IN UserId			INT UNSIGNED,		/* 用户ID */
	IN SkuId			INT UNSIGNED,		/* 商品ID */
	IN OrderId			VARCHAR(16),    /*返回主订单ID */
	IN UserAddressId 	INT,				/*收货地址*/
	IN CommentAdmin		VARCHAR(128),    /*返回主订单ID */
	IN ClientIp			BIGINT UNSIGNED,	/*创建IP*/

	OUT ReturnValue		VARCHAR(32),    /*返回状态值 */
	OUT ReturnMessage	VARCHAR(1024) 	/*返回提示信息 */
)
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT '卡充VIP补单'
Main : BEGIN		/*主体内容开始*/
	/*定义状态*/
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	/*局部变量*/
	/*局部变量*/
	DECLARE SkuTitle			VARCHAR(128)		DEFAULT '';
	DECLARE SkuDepot			INT UNSIGNED		DEFAULT 0;
	DECLARE SkuStatus			TINYINT UNSIGNED	DEFAULT 0;
	DECLARE SkuPriceSale		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE SkuPriceInv			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE SkuLimitStyle		TINYINT UNSIGNED	DEFAULT 0;
	DECLARE SkuLimitCount		TINYINT UNSIGNED	DEFAULT 0;
	DECLARE SkuSpecs			TEXT				DEFAULT '';

	DECLARE BuyerInviterId		INT UNSIGNED		DEFAULT 0;
	DECLARE BuyerLevelUser		TINYINT UNSIGNED	DEFAULT 0;
	DECLARE BuyerLevelVip		TINYINT UNSIGNED	DEFAULT 0;

	DECLARE OrderAddrRegion		INT UNSIGNED		DEFAULT 0;
	DECLARE OrderAddrDetail		VARCHAR(256)		DEFAULT '';
	DECLARE OrderAddrName		VARCHAR(16)			DEFAULT '';
	DECLARE OrderMobile			VARCHAR(16)			DEFAULT '';
	

	DECLARE CartPrice		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE CartThumb		VARCHAR(128)	DEFAULT '';

	
	DECLARE SpuId			INT UNSIGNED	DEFAULT 0;
	DECLARE SpuStatus		INT UNSIGNED	DEFAULT 0;

	DECLARE ExtSkuId		INT UNSIGNED	DEFAULT 0;
	DECLARE ExtSpuId		INT UNSIGNED	DEFAULT 0;
	DECLARE ExtSupplierId	INT UNSIGNED	DEFAULT 0;

	DECLARE BrandId			INT UNSIGNED	DEFAULT 0;
	DECLARE CatagoryId		INT UNSIGNED	DEFAULT 0;
	DECLARE SupplierId		INT UNSIGNED	DEFAULT 0;

	DECLARE PayOnline		NUMERIC(16,4)	DEFAULT 0.0;


	/* 声明异常处理（定义系统错误），必须放在变量定义之后*/
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	/* 初始化变量*/
	SET ReturnValue = '';
	SET ReturnMessage = '卡充VIP补单';
	/* 检查参数 */
	SELECT invitor_id,level_u,level_v
	INTO BuyerInviterId,BuyerLevelUser,BuyerLevelVip
	FROM `yuemi_main`.`user`
	WHERE `id` = UserId;
	IF SYSEmpty = 1 THEN
		SET ReturnValue = 'E_USER';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无用户');
		LEAVE Main;
	END IF;
	IF BuyerLevelUser = 0 THEN
		SET ReturnValue = 'E_USER';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','被禁闭');
		LEAVE Main;
	END IF;
	IF BuyerLevelVip <= 0 THEN
		SET ReturnValue = 'E_USER';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','不是VIP');
		LEAVE Main;
	END IF;


	/*查询 sku_id 并且对变量赋值*/
	SELECT spu_id,title,depot,status,
			price_sale,price_inv,
			limit_style,limit_size,catagory_id,specs
	INTO SpuId,SkuTitle,SkuDepot,SkuStatus,
		SkuPriceSale,SkuPriceInv,
		SkuLimitStyle,SkuLimitCount,CatagoryId,SkuSpecs
	FROM `sku` WHERE `id` = SkuId;
	IF SYSEmpty = 1 THEN
		SET ReturnValue = 'E_NOSKU';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无商品');
		LEAVE Main;
	END IF;
	IF SkuStatus != 2 THEN
		SET ReturnValue = 'E_STATUS';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','已下架');
		LEAVE Main;
	END IF;
	IF SkuDepot < 1 THEN
		SET ReturnValue = 'E_DEPOT';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','商品已经售罄');
		LEAVE Main;
	END IF;
	-- 第一次找缩略图
	IF LENGTH(CartThumb) < 1 THEN
		SELECT `thumb_url` INTO CartThumb FROM `sku_material` WHERE `sku_id` = SkuId AND `type` = 0 ORDER BY `is_default` DESC LIMIT 1;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
			SET CartThumb = '';
		END IF;
	END IF;
	-- 第二次找缩略图
	IF LENGTH(CartThumb) < 1 THEN
		SELECT `thumb_url` INTO CartThumb FROM `spu_material` WHERE `spu_id` = SpuId AND `type` = 0 ORDER BY `is_default` DESC LIMIT 1;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
			SET CartThumb = '';
		END IF;
	END IF;	
	-- 查SPU status，读 供应商，分类
	SELECT `status`,`supplier_id`,`brand_id` INTO SpuStatus,SupplierId,BrandId FROM `spu` WHERE `id` = SpuId;
	IF SYSEmpty = 1 THEN
		SET ReturnValue = 'E_SPU';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无SPU');
	END IF;
	IF SpuStatus = 0 THEN
		SET ReturnValue = 'E_SPUSTATUS';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','SPU下架');
		LEAVE Main;
	END IF;

	-- 查ext_sku，查 ext供应商
	SET SYSEmpty = 0;
	SELECT `id`,`ext_spu_id`,`supplier_id` INTO ExtSkuId,ExtSpuId,ExtSupplierId FROM `ext_sku` WHERE `sku_id` = SkuId;
	-- 第三次找缩略图
	IF SYSEmpty = 0 AND LENGTH(CartThumb) < 1 THEN
		SELECT `thumb_url` INTO CartThumb FROM `ext_sku_material` WHERE `ext_sku_id` = ExtSkuId AND `type` = 0 ORDER BY `is_default` DESC LIMIT 1;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
			SET CartThumb = '';
		END IF;
	END IF;
	
	-- 第四次找缩略图
	IF ExtSkuId > 0 AND LENGTH(CartThumb) < 1 THEN
		SELECT `thumb_url` INTO CartThumb FROM `ext_spu_material` WHERE `ext_spu_id` = ExtSpuId AND `type` = 0 ORDER BY `is_default` DESC LIMIT 1;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
			SET CartThumb = '';
		END IF;
	END IF;
	
	-- 检查地址并写入变量
	IF UserAddressId > 0 THEN 
		SET SYSEmpty = 0;
		SELECT `region_id`,`address`,`contacts`,`mobile` 
		INTO	OrderAddrRegion,OrderAddrDetail,OrderAddrName,OrderMobile 
		FROM `yuemi_main`.`user_address` 
		WHERE `id` = UserAddressId AND `user_id` = UserId;

		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_ADDRESS';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','请选择正确地址','->',UserId);
			LEAVE Main;
		END IF;
	END IF;
	

	SET SYSEmpty = 0;
	SET SYSError = 0;
	START TRANSACTION;

		SET PayOnline = 0;

		-- 插入order_item表
		INSERT INTO `yuemi_sale`.`order_item`(`price_base`,`order_id`,`sku_id`,`spu_id`,`catagory_id`,`supplier_id`,`qty`,`price`,`money`,`title`,`picture`)
		SELECT K.`price_base`,OrderId,SkuId,K.`spu_id`,K.`catagory_id`,K.`supplier_id`,1,0,0,K.`title`,CartThumb
		FROM `yuemi_sale`.`sku` AS K
		WHERE K.`id` = SkuId;
		
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_SKU';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','SKU错误');
			ROLLBACK;
			LEAVE Main;
		END IF;

		IF SYSError = 1 THEN
			SET ReturnValue = 'E_OR';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','插入order_item失败');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		-- 插入order 表
		INSERT INTO `yuemi_sale`.`order`(`id`,`user_id`,`is_primary`,`depend_id`,`supplier_id`,`qty`,`t_amount`,`t_online`,`c_online`,`c_amount`,`pay_serial`,`pay_time`,`address_id`,`addr_region`,`addr_detail`,`addr_name`,`addr_mobile`,`create_time`,`trans_id`,`status`,`comment_admin`,`type`)
		SELECT OrderId,UserId,1,OrderId,K.`supplier_id`,1,SkuPriceInv,0,0,SkuPriceInv,'',UNIX_TIMESTAMP(NOW()),UserAddressId,OrderAddrRegion,OrderAddrDetail,OrderAddrName,OrderMobile,UNIX_TIMESTAMP(NOW()),'',4,CommentAdmin,1
		FROM `yuemi_sale`.`sku` AS K
		WHERE K.`id` = SkuId;

		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_SKU';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','SKU错误');
			ROLLBACK;
			LEAVE Main;
		END IF;

		IF SYSError = 1 THEN
			SET ReturnValue = 'E_OR';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','插入order失败');
			ROLLBACK;
			LEAVE Main;
		END IF;

		-- 更改库存
		UPDATE `yuemi_sale`.`sku` 
		SET `depot` = `depot` - 1 
		WHERE `id` = SkuId;
		
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_SKU';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更新库存失败');
			LEAVE Main;
		END IF;

		-- 更改大礼包领取状态
		UPDATE `yuemi_main`.`vip` 
		SET `has_gifts` = 1 
		WHERE user_id = UserId;
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_VIP';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更改领取状态失败');
			LEAVE Main;
		END IF;

	COMMIT;

	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
DELIMITER ;