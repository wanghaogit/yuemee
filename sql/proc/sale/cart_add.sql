/**
 * 功能 : 添加到购物车
 * 存储过程调用示例 CALL cart_add(1,20,2,111,@CartId,@ReturnValue,@ReturnMessage);
 *	SELECT @CartId,@ReturnValue,@ReturnMessage;
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `cart_add` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `cart_add`(
	IN UserId		INT UNSIGNED,					/* 用户ID */
	IN ShareId		INT UNSIGNED,					/* 分享ID */
	IN SkuId 		INT UNSIGNED,					/* 商品ID */
	IN QtyAdd		SMALLINT UNSIGNED,				/* 购买数量 */
	IN ClientIp		BIGINT UNSIGNED,				/* 创建地址 */

	OUT CartId INT UNSIGNED,
	OUT ReturnValue	VARCHAR(32),    	/*返回状态值 */
	OUT ReturnMessage VARCHAR(1024) 	/*返回提示信息 */
)
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT '加购物车'
Main : BEGIN		/*主体内容开始*/
	/*定义状态*/
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;
	
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

	DECLARE ShareUserId			INT UNSIGNED		DEFAULT 0;
	DECLARE ShareDirectorId		INT UNSIGNED		DEFAULT 0;
	DECLARE ShareTeamId			INT UNSIGNED		DEFAULT 0;
	DECLARE ShareMemberId		INT UNSIGNED		DEFAULT 0;
	DECLARE ShareSkuId			INT UNSIGNED		DEFAULT 0;

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


	DECLARE TmpId			INT UNSIGNED	DEFAULT 0;
	DECLARE TmpVal			INT UNSIGNED	DEFAULT 0;

	DECLARE QtyOld			INT UNSIGNED	DEFAULT 0;
	DECLARE QtyNew			INT UNSIGNED	DEFAULT 0;

	/* 声明异常处理（定义系统错误），必须放在变量定义之后*/
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	/* 初始化变量*/
	SET CartId = 0;
	SET ReturnValue = '';
	SET ReturnMessage = '加购物车';

	-- DELETE FROM `cart` WHERE `qty` <= 0;

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
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无SKU');
		LEAVE Main;
	END IF;
	IF SkuStatus != 2 THEN
		SET ReturnValue = 'E_STATUS';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','未上架');
		LEAVE Main;
	END IF;
	IF SkuDepot < 1 THEN
		SET ReturnValue = 'E_DEPOT';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','已售罄');
		LEAVE Main;
	END IF;
	IF SkuDepot < QtyAdd THEN
		SET ReturnValue = 'E_DEPOT';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','缺货');
		LEAVE Main;
	END IF;
	
	-- 检查限购
	IF SkuLimitStyle = 1 THEN		-- 按人头限购
		-- 检查我的 order_item 中 SUM(qty) > 
		SELECT SUM(qty)
		INTO TmpVal
		FROM `order_item`
		WHERE `sku_id` = SkuId
		AND `order_id` IN (
			SELECT `id` FROM `order` WHERE
				`user_id` = UserId AND `status` IN (2,4,5,6,7,8,21,22,23,31,32,33)
		);
		IF SYSEmpty = 1 THEN
			SET TmpVal = 0;
			SET SYSEmpty = 0;
		END IF;
		IF TmpVal + QtyAdd >=  SkuLimitCount THEN
			SET ReturnValue = 'E_LIMIT';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','超限购');
			LEAVE Main;
		END IF;
	END IF;

	-- 开始选价格
	IF CartPrice <= 0 AND BuyerLevelVip > 0 AND BuyerInviterId > 0 AND SkuPriceInv > 0 THEN
		SET CartPrice = SkuPriceInv;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','受邀价(',SkuPriceInv,')');
	END IF;
	IF CartPrice <= 0 THEN
		SET CartPrice = SkuPriceSale;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','平台价(',SkuPriceSale,')');
	END IF;
	IF CartPrice <= 0 THEN
		SET ReturnValue = 'E_PRICE';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','价格错');
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
	
	-- 查找分享
	IF ShareId  > 0 THEN
		SET ReturnMessage = CONCAT(ReturnMessage,'->','是分享');
		SELECT user_id,director_id,team_id,member_id,sku_id
		INTO ShareUserId,ShareDirectorId,ShareTeamId,ShareMemberId,ShareSkuId
		FROM `share` WHERE `id` = ShareId;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_SHARE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','分享错');
			LEAVE Main;
		END IF;
		IF ShareSkuId != SkuId THEN
			SET ReturnValue = 'E_SHARE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','分享冲突');
			LEAVE Main;
		END IF;
	END IF;

	START TRANSACTION;
		SET SYSEmpty = 0;
		SET QtyOld = 0;
		SET TmpId = 0;
		SELECT `qty`,`id`
		INTO  QtyOld,TmpId 
		FROM `cart`
		WHERE `user_id` = UserId AND `sku_id` = SkuId LIMIT 1 FOR UPDATE;
		
		IF SYSEmpty = 1 OR TmpId <= 0 OR QtyOld <= 0 THEN 
			-- 新数据
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录');
			INSERT INTO `cart`(
				`user_id`,`sku_id`,`share_id`,
				`spu_id`,`catagory_id`,`brand_id`,`supplier_id`,`ext_sku_id`,`ext_spu_id`,`ext_supplier_id`,
				`sku_title`,`sku_price`,`sku_thumb`,`sku_spec`,
				`qty`,`is_checked`,`create_time`,`create_from`)
			VALUES (UserId,SkuId,ShareId,
				SpuId,CatagoryId,BrandId,SupplierId,ExtSkuId,ExtSpuId,ExtSupplierId,
				SkuTitle,CartPrice,CartThumb,SkuSpecs,
				QtyAdd,1,UNIX_TIMESTAMP(),ClientIp);
			SET CartId = LAST_INSERT_ID();
			IF SYSError = 1 THEN 
				SET ReturnValue = 'E_INCART';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','插入错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		ELSE
			-- 老数据
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老记录');
			SET CartId = TmpId;
			SET QtyNew = QtyOld + QtyAdd;

			IF SkuDepot < QtyNew THEN 
				SET ReturnValue = 'E_DEPOT';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','库存不足');
				ROLLBACK;
				LEAVE Main;
			END IF;

			UPDATE `cart` SET `qty` = QtyNew,`sku_price` = CartPrice,`create_time` = UNIX_TIMESTAMP()  WHERE `id` = CartId;

			IF SYSError = 1 THEN 
				SET ReturnValue = 'E_UPCART';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新失败');
				ROLLBACK;
				LEAVE Main;
			END IF;

		END IF;		
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
DELIMITER ;