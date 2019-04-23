/**
 * 功能 : 快捷下单
 * 作者：殷非非
 * 日期：2018-4-22
 * 修订@2018-04-22	: 
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `yuemi_sale`.`fast_purchase` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `fast_purchase`(
	IN UserId			INT UNSIGNED,		/* 用户ID */
	IN ShareId			INT UNSIGNED,		/* 分享ID */
	IN SkuId			INT UNSIGNED,		/* 商品ID */
	IN BuyQty			SMALLINT UNSIGNED,	/* 购买数量 */
	IN UserAddressId 	INT,				/*收货地址*/
	IN ClientIp			BIGINT UNSIGNED,	/*创建IP*/
	IN SelUseMoney		INT,			/*选择使用余额*/
	IN SelUseProfit		INT,			/*选择使用销售佣金*/
	IN SelUseRecruit	INT,			/*选择使用礼包佣金*/
	IN SelUseTicket		INT,			/*选择使用优惠券*/
	IN TicketId			VARCHAR(32),	/*选择使用优惠券*/
	IN CommentUser		VARCHAR(64), 	/*用户留言 */

	OUT OrderId			VARCHAR(16),    /*返回主订单ID */
	OUT ReturnValue		VARCHAR(32),    /*返回状态值 */
	OUT ReturnMessage	VARCHAR(1024) 	/*返回提示信息 */
)
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT '快捷下单'
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
	DECLARE InvitorLevelVip		TINYINT UNSIGNED	DEFAULT 0;
	DECLARE ShareUserId			INT UNSIGNED		DEFAULT 0;
	DECLARE ShareDirectorId		INT UNSIGNED		DEFAULT 0;
	DECLARE ShareTeamId			INT UNSIGNED		DEFAULT 0;
	DECLARE ShareMemberId		INT UNSIGNED		DEFAULT 0;
	DECLARE ShareSkuId			INT UNSIGNED		DEFAULT 0;
	DECLARE OrderAddrRegion		INT UNSIGNED		DEFAULT 0;
	DECLARE OrderAddrDetail		VARCHAR(256)		DEFAULT '';
	DECLARE OrderAddrName		VARCHAR(16)			DEFAULT '';
	DECLARE OrderMobile			VARCHAR(16)			DEFAULT '';
	DECLARE CartPrice			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE CartThumb			VARCHAR(128)		DEFAULT '';
	DECLARE SpuId				INT UNSIGNED		DEFAULT 0;
	DECLARE SpuStatus			INT UNSIGNED		DEFAULT 0;
	DECLARE ExtSkuId			INT UNSIGNED		DEFAULT 0;
	DECLARE ExtSpuId			INT UNSIGNED		DEFAULT 0;
	DECLARE ExtSupplierId		INT UNSIGNED		DEFAULT 0;
	DECLARE BrandId				INT UNSIGNED		DEFAULT 0;
	DECLARE CatagoryId			INT UNSIGNED		DEFAULT 0;
	DECLARE SupplierId			INT UNSIGNED		DEFAULT 0;
	DECLARE TmpId				INT UNSIGNED		DEFAULT 0;
	DECLARE TmpVal				INT UNSIGNED		DEFAULT 0;
	DECLARE UserMoney			NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE UserProfitSelf		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE UserProfitShare		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE UserRecruitDir		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE ThewStatus			INT UNSIGNED		DEFAULT 0;
	DECLARE Total				NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE UpMoney				NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE UpProfitSelf		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE UpProfitShare		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE UpRecruitDir		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE PayOnline			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE ProId				INT UNSIGNED		DEFAULT 0;
	DECLARE ProValue			VARCHAR(32)			DEFAULT '';
	DECLARE ProMessage			VARCHAR(1024)		DEFAULT '';
	DECLARE Money_Old			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Money_New			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Profit_Old			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Profit_New			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Profit_Old_1		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Profit_New_1		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Recruit_Old			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Recruit_New			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TransSpuId			INT UNSIGNED		DEFAULT 0;
	DECLARE Sid					INT UNSIGNED		DEFAULT 0;
	DECLARE TransExtSpuId		INT UNSIGNED		DEFAULT 0;
	DECLARE TransShopCode		VARCHAR(16)			DEFAULT '';
	DECLARE TransMoney			NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE TemTransMoneyO		NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE TemTransMoneyA		NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE RebateUser			INT UNSIGNED		DEFAULT 0; -- 返佣账户
	DECLARE RebateUserV			INT UNSIGNED		DEFAULT 0; -- 返佣账户
	DECLARE RebateCheif			INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateDirector		INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateItemId		INT	UNSIGNED		DEFAULT 0;
	DECLARE RebatePa			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebatePb			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebatePc			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebateIsSelf		INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateIsShare		INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateSkuId			INT	UNSIGNED		DEFAULT 0;
	DECLARE TallyCoin			INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateQty			INT	UNSIGNED		DEFAULT 0;
	DECLARE TempCoin			NUMERIC(16,8)		DEFAULT 0.0;
	DECLARE Coin_Old			NUMERIC(16,8)		DEFAULT 0.0;
	DECLARE Coin_New			NUMERIC(16,8)		DEFAULT 0.0;
	-- 成为VIP定义变量
	DECLARE UCoin				NUMERIC(16,8)		DEFAULT 0.0;
	DECLARE UCoin_New			NUMERIC(16,8)		DEFAULT 0.0;
	DECLARE OStatus				INT					DEFAULT 0;
	DECLARE CheifId				INT					DEFAULT 0;
	DECLARE DirectorId			INT					DEFAULT 0;
	DECLARE VipId				INT UNSIGNED		DEFAULT 0;
	DECLARE VipCode				VARCHAR(8)			DEFAULT '';
	DECLARE VipStatus			TINYINT UNSIGNED	DEFAULT 0;
	DECLARE VipExpire			BIGINT UNSIGNED		DEFAULT 0;
	DECLARE StatusId			INT UNSIGNED		DEFAULT 0;
	DECLARE StatusExpire		BIGINT UNSIGNED		DEFAULT 0;
	DECLARE TallyId				INT					DEFAULT 0;
	DECLARE OwnerId				INT					DEFAULT 0; -- 佣金归属人
	DECLARE IsGiftSet			INT					DEFAULT 0; -- 佣金归属人
	DECLARE SkuType				INT					DEFAULT 0; -- 佣金归属人
	-- 优惠券相关变量
	DECLARE TicketSpuId			INT					DEFAULT 0; -- 优惠券对应SPUID
	DECLARE TicketStatus		INT					DEFAULT 0; -- 优惠券使用状态
	DECLARE TicketValue			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE PriceSmall			NUMERIC(16,4)		DEFAULT 0.0;
	/* 声明异常处理（定义系统错误），必须放在变量定义之后*/
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	/* 初始化变量*/
	SET OrderId = '';
	SET ReturnValue = '';
	SET ReturnMessage = '快捷下单';
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
	
	IF BuyerInviterId > 0 THEN 
		SELECT level_v INTO InvitorLevelVip FROM `yuemi_main`.`user` WHERE `id` = BuyerInviterId;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无用户');
			LEAVE Main;
		END IF;
	END IF;

	IF BuyQty <= 0 THEN 
		SET ReturnValue = 'E_QTY';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','下单数量错误');
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
		SET ReturnMessage = CONCAT(ReturnMessage,'->','商品已经售罄');
		LEAVE Main;
	END IF;
	IF SkuStatus != 2 THEN
		SET ReturnValue = 'E_STATUS';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','商品已经售罄');
		LEAVE Main;
	END IF;
	IF SkuDepot < 1 THEN
		SET ReturnValue = 'E_DEPOT';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','商品已经售罄');
		LEAVE Main;
	END IF;
	IF SkuDepot < BuyQty THEN
		SET ReturnValue = 'E_DEPOT';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','缺货');
		LEAVE Main;
	END IF;

	-- 检查限购
	IF SkuLimitStyle = 1 THEN		-- 按人头限购
		-- 检查我的 order_item 中 SUM(qty) > 
		SET ReturnMessage = CONCAT(ReturnMessage,'->','限购商品');	
		SELECT SUM(qty)
		INTO TmpVal 
		FROM `order_item`
		WHERE `sku_id` = SkuId
		AND `order_id` IN (
			SELECT `id` FROM `order` WHERE
				`user_id` = UserId AND `status` IN (1,2,4,5,6,7,8)
		);
		IF SYSEmpty = 1 OR TmpVal IS NULL THEN 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无购买记录');
			SET TmpVal = 0;
			SET SYSEmpty = 0;
		END IF;
		IF TmpVal + BuyQty >  SkuLimitCount THEN
			SET ReturnValue = 'E_LIMIT';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','超限购');
			LEAVE Main;
		END IF;
	END IF;

	-- 开始选价格
	IF CartPrice <= 0 AND BuyerInviterId > 0 AND SkuPriceInv > 0 THEN
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
	
	-- 查找分享
	IF ShareId  > 0 THEN 
		SET SYSEmpty = 0;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','是分享');
		SELECT user_id,director_id,team_id,member_id,sku_id
		INTO ShareUserId,ShareDirectorId,ShareTeamId,ShareMemberId,ShareSkuId
		FROM `yuemi_sale`.`share` WHERE `id` = ShareId;
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
		SELECT `level_v` INTO RebateUserV FROM `yuemi_main`.`user` WHERE `id` = ShareUserId;
	END IF;

	IF BuyerLevelVip > 0 THEN 
		SELECT `cheif_id`,`director_id` INTO RebateCheif,RebateDirector FROM `yuemi_main`.`vip` WHERE `user_id` = UserId;
		SET OwnerId = UserId;
	ELSEIF BuyerInviterId > 0 THEN 
		SELECT `cheif_id`,`director_id` INTO RebateCheif,RebateDirector FROM `yuemi_main`.`vip` WHERE `user_id` = BuyerInviterId;
		IF InvitorLevelVip > 0 THEN 
			SET OwnerId = BuyerInviterId;
		END IF;
	END IF;
	
	SELECT spu.is_gift_set INTO IsGiftSet 
	FROM sku 
	LEFT JOIN spu on sku.spu_id = spu.id
	WHERE sku.id = SkuId;

	IF SelUseTicket != 0 THEN 
            -- TODO  查询优惠券是否被用过,是否是一个商品的优惠券，是不是本人的优惠券
            SET SYSEmpty = 0;
            SET ReturnMessage = CONCAT(ReturnMessage,'->','使用优惠券');
            IF TicketId = '' THEN 
                SET ReturnValue = 'E_TICKET';
                SET ReturnMessage = CONCAT(ReturnMessage,'->','无优惠券');
            ELSE 
                SELECT spu_id,`value`,price_small,`status` INTO TicketSpuId,TicketValue,PriceSmall,TicketStatus 
                FROM `yuemi_sale`.`discount_coupon` WHERE `id` = TicketId;
                IF SYSEmpty = 1 THEN 
                        SET ReturnValue = 'E_TICKET';
                        SET ReturnMessage = CONCAT(ReturnMessage,'->','无优惠券');
                        SET TicketValue = 0;
                END IF;
                IF TicketStatus = 1 OR TicketStatus = 2 THEN 
                        SET ReturnValue = 'E_TICKET';
                        SET ReturnMessage = CONCAT(ReturnMessage,'->','优惠券已使用');
                        SET TicketValue = 0;
                END IF;
                IF TicketSpuId IS NOT NULL AND TicketSpuId != SpuId THEN 
                        SET ReturnValue = 'E_TICKET';
                        SET ReturnMessage = CONCAT(ReturnMessage,'->','购买非指定商品');
                        SET TicketValue = 0;
                END IF;
                IF PriceSmall IS NOT NULL AND PriceSmall > CartPrice * BuyQty THEN 
                        SET ReturnValue = 'E_TICKET';
                        SET ReturnMessage = CONCAT(ReturnMessage,'->','订单未到指定额度');
                        SET TicketValue = 0;
                END IF;
            END IF;
	END IF;

	IF IsGiftSet = 0 THEN 
		-- 检查返佣ID 
		IF BuyerLevelVip > 0 THEN 
			SET RebateUser = UserId;
			SET RebateIsSelf = 1;
			SET RebateIsShare = 0;
		ELSEIF ShareId > 0 AND RebateUserV > 0 THEN 
			SET RebateUser = RebateUserId;
			SET RebateIsSelf = 0;
			SET RebateIsShare = 1;
		ELSEIF BuyerInviterId > 0 THEN 
			SET RebateUser = BuyerInviterId;
		ELSE 
			SET RebateUser = 0;
		END IF;
		SET SkuType = 0;
	ELSE 
		SET SkuType = 1;
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
	ELSE 
		SET OrderAddrRegion = 0;
		SET OrderAddrDetail = '';
		SET OrderAddrName = '';
		SET OrderMobile = '';
	END IF;
	

	SET SYSEmpty = 0;
	START TRANSACTION;
		SET OrderId = NEW_ORDER_ID('P','');

		-- 计算运费
		SELECT `supplier_id` INTO Sid FROM `sku` WHERE `id` = SkuId;
		IF Sid = 2 THEN 
			-- 查出ext_spu_id
			SELECT `ext_spu_id` INTO TransExtSpuId FROM ext_sku WHERE `sku_id` = SkuId;
			-- 查出供货商
			SELECT `ext_shop_code` INTO TransShopCode FROM `yuemi_sale`.`ext_spu` WHERE `id` = TransExtSpuId;

			IF TransShopCode = 'JD' THEN 
				SET TemTransMoneyA = BuyQty * CartPrice;
			END IF;
			IF TemTransMoneyA < 99 AND TransShopCode = 'JD' THEN 
				SET TransMoney = 10.0;
			END IF;
			SET PayOnline = TransMoney;
		END IF;

		SET PayOnline = PayOnline + BuyQty * CartPrice;
		SET Total = PayOnline;

		-- 插入order_item表
		SET SYSError = 0;
		SET SYSEmpty = 0;
		INSERT INTO `yuemi_sale`.`order_item`(`rebate_user`,`price_base`,`rebate_vip`,`order_id`,`share_id`,`sku_id`,`spu_id`,`catagory_id`,`supplier_id`,`qty`,`price`,`money`,`title`,`picture`)
		SELECT UserId,K.`price_base`,K.`rebate_vip` * BuyQty,OrderId,ShareId,SkuId,K.`spu_id`,K.`catagory_id`,K.`supplier_id`,BuyQty,CartPrice,BuyQty * CartPrice,K.`title`,CartThumb
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
		
		SET RebateItemId = LAST_INSERT_ID();
		
		-- 插入order 表
		INSERT INTO `yuemi_sale`.`order`(`t_trans`,`id`,`user_id`,`is_primary`,`depend_id`,`supplier_id`,`qty`,`t_amount`,`t_online`,`c_online`,`c_amount`,`pay_serial`,`pay_time`,`address_id`,`addr_region`,`addr_detail`,`addr_name`,`addr_mobile`,`create_time`,`trans_id`,`status`,comment_user,`type`,discount_coupon_id)
		SELECT TransMoney,OrderId,UserId,1,OrderId,K.`supplier_id`,BuyQty,PayOnline,PayOnline,PayOnline,BuyQty*CartPrice,'',UNIX_TIMESTAMP(NOW()),UserAddressId,OrderAddrRegion,OrderAddrDetail,OrderAddrName,OrderMobile,UNIX_TIMESTAMP(NOW()),'',0,CommentUser,SkuType,TicketId
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
		-- 加入优惠券
		SET PayOnline = PayOnline - TicketValue;
		IF PayOnline < 0 THEN 
			SET PayOnline = 0;
		END IF;

		IF PayOnline > 0 THEN 

		-- 检查账户以及赋值
			SELECT `money`,`profit_self`,`profit_share`,`recruit_dir`,`thew_status` 
			INTO UserMoney,UserProfitSelf,UserProfitShare,UserRecruitDir,ThewStatus
			FROM `yuemi_main`.`user_finance` 
			WHERE `user_id` = UserId;

			IF SYSEmpty = 1 THEN
				SET ReturnValue = 'E_F_P';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错误');
				ROLLBACK;
				LEAVE Main;
			END IF;

			--  检查余额
			IF SelUseMoney > 0 AND UserMoney > 0 THEN
				IF UserMoney > PayOnline THEN 
					SET UpMoney = PayOnline;
					SET PayOnline = 0;
				ELSE 
					SET UpMoney = UserMoney;
					SET PayOnline = PayOnline-UserMoney;
				END IF;
			END IF;

			IF SelUseProfit > 0 AND UserProfitSelf > 0 AND PayOnline > 0 THEN
				IF UserProfitSelf > PayOnline THEN
					SET UpProfitSelf = PayOnline;
					SET PayOnline = 0;
				ELSE 
					SET UpProfitSelf = UserProfitSelf;
					SET PayOnline = PayOnline-UserProfitSelf;
				END IF;
			END IF;

			IF SelUseProfit > 0 AND UserProfitShare > 0 AND PayOnline > 0 THEN 
				IF UserProfitShare > PayOnline THEN 
					SET UpProfitShare = PayOnline;
					SET PayOnline = 0;
				ELSE 
					SET UpProfitShare = UserProfitShare;
					SET PayOnline = PayOnline - UserProfitShare;
				END IF;
			END IF;

			IF SelUseRecruit > 0 AND ThewStatus > 0 AND UserRecruitDir > 0 AND PayOnline > 0 THEN 
				IF UserRecruitDir > PayOnline THEN 
					SET UpRecruitDir = PayOnline;
					SET PayOnline = 0;
				ELSE 
					SET UpRecruitDir = UserRecruitDir;
					SET PayOnline = PayOnline - UserRecruitDir;
				END IF;
			END IF;

			IF Total != UpMoney + UpProfitSelf + UpProfitShare + UpRecruitDir + PayOnline + TicketValue THEN 
				SET ReturnValue = 'E_TOTLE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','金额错误');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;

		-- 更新账户 余额
		
		IF UpMoney > 0 THEN
			INSERT INTO `yuemi_main`.`tally_money` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'BUY',OrderId,UserMoney,-UpMoney,UserMoney-UpMoney,'',NOW(),ClientIp);
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
				LEAVE Main;
			END IF;

			UPDATE `yuemi_main`.`user_finance` 
			SET `money` = UserMoney-UpMoney
			WHERE `user_id` = UserId;

			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_F';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新错');
				LEAVE Main;
			END IF;
		END IF;
		-- 更新账户 自省佣金

		IF UpProfitSelf > 0 THEN 
			INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'SELF','BUY',OrderId,UserProfitSelf,-UpProfitSelf,UserProfitSelf-UpProfitSelf,'',NOW(),ClientIp);
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
				LEAVE Main;
			END IF;

			UPDATE `yuemi_main`.`user_finance` 
			SET `profit_self` = UserProfitSelf-UpProfitSelf  
			WHERE `user_id` = UserId;

			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_F';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新错');
				LEAVE Main;
			END IF;
		END IF;
		-- 更新分享佣金
		
		IF UpProfitShare > 0 THEN 

			INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'SHARE','BUY',OrderId,UserProfitShare,-UpProfitShare,UserProfitShare - UpProfitShare,'',NOW(),ClientIp);
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
				LEAVE Main;
			END IF;

			UPDATE `yuemi_main`.`user_finance` 
			SET `profit_share` = UserProfitShare - UpProfitShare  
			WHERE `user_id` = UserId;

			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_F';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新错');
				LEAVE Main;
			END IF;
		END IF;
		-- 更新佣金礼包
		IF UpRecruitDir > 0 THEN 
			INSERT INTO `yuemi_main`.`tally_recruit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'DIR','BUY',OrderId,UserRecruitDir,-UpRecruitDir,UserRecruitDir - UpRecruitDir,'',NOW(),ClientIp);
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
				LEAVE Main;
			END IF;

			UPDATE `yuemi_main`.`user_finance` 
			SET `recruit_dir` = UserRecruitDir - UpRecruitDir  
			WHERE `user_id` = UserId;

			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_F';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新错');
				LEAVE Main;
			END IF;
		END IF;
		-- 更新order表
		IF PayOnline = 0 THEN 
			UPDATE `yuemi_sale`.`order` 
			SET `t_money` = UpMoney,`t_profit` = UpProfitSelf + UpProfitShare,`t_recruit` = UpRecruitDir , `status` = 2 , `t_online` = 0,
				`c_money` = UpMoney,`c_profit` = UpProfitSelf + UpProfitShare,`c_recruit` = UpRecruitDir , `c_online` = 0 
			WHERE `id` = OrderId;
		ELSEIF PayOnline < 0 THEN 
			ROLLBACK;
			SET ReturnValue = 'E_F_M';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','扣除佣金礼包失败');
			LEAVE Main;
		ELSEIF PayOnline > 0 THEN 
			UPDATE `yuemi_sale`.`order` 
			SET `t_money` = UpMoney,`t_profit` = UpProfitSelf + UpProfitShare,`t_recruit` = UpRecruitDir , `status` = 1 , `t_online` = PayOnline,
				`c_money` = UpMoney,`c_profit` = UpProfitSelf + UpProfitShare,`c_recruit` = UpRecruitDir , `c_online` = PayOnline 
			WHERE `id` = OrderId;
		END IF;
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_U_OR';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更新order失败');
			LEAVE Main;
		END IF;
		
		IF IsGiftSet = 1 THEN 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','大礼包商品无佣金');
		ELSEIF ShareId = 0 AND BuyerLevelVip = 0 THEN 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','不是分享商品');
		ELSE 
			SELECT `id`,`sku_id`,`price`,`qty` INTO RebateItemId,RebateSkuId,RebatePa,RebateQty FROM `yuemi_sale`.`order_item` WHERE `order_id` = OrderId;
			SELECT `price_base` INTO RebatePb FROM `yuemi_sale`.`sku` WHERE `id` = RebateSkuId;
			-- 计算毛利
			SET RebatePc = (RebatePa - RebatePb) * RebateQty;
			IF RebatePc < 0 THEN 
				SET RebatePc = 0;
			END IF;

			-- 写入rebate表中
			IF RebatePc > 0 THEN
				INSERT INTO `yuemi_sale`.`rebate`(`item_id`,`order_id`,`buyer_id`,`buyer_vip`,`share_id`,`share_user_id`,`cheif_id`,`director_id`,`sku_id`,`time_create`,`spu_id`,`pay_count`,`pay_total`,`pay_money`,`pay_profit`,`pay_ticket`,`pay_online`,`total_profit`,`system_profit`,`self_profit`,`share_profit`,`cheif_ratio`,`cheif_profit`,`director_ratio`,`director_profit`,`status`,`create_time`,`invitor_id`,`invitor_vip`,`owner_id`)
				SELECT RebateItemId,OrderId,UserId,BuyerLevelVip,ShareId,ShareUserId ,RebateCheif,RebateDirector,`sku_id`,UNIX_TIMESTAMP(),`spu_id`,`qty`,`money`, UpMoney , UpProfitSelf + UpProfitShare ,0, PayOnline ,RebatePc ,RebatePc * 0.2 , RebatePc * 0.8 * 0.7 * RebateIsSelf ,RebatePc * 0.8 * 0.7 * RebateIsShare , 0.12 , RebatePc * 0.12 , 0.08 , RebatePc * 0.08 , 0 ,UNIX_TIMESTAMP(),BuyerInviterId,InvitorLevelVip,OwnerId
				FROM `yuemi_sale`.`order_item` WHERE `id` = RebateItemId;

				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_REBATE';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','更新rebate失败');
					LEAVE Main;
				END IF;
			END IF;
		END IF;

		-- 赠送阅币
		IF PayOnline = 0 THEN 
			SELECT `coin_buyer` INTO TempCoin 
			FROM `yuemi_sale`.`sku` AS K 
			WHERE `id` = SkuId;

			IF IsGiftSet = 1 THEN 
				SET TempCoin = 1000; 
			END IF;

			IF TempCoin > 0 THEN 
				-- 检查原阅币
				SELECT `coin` INTO Coin_Old FROM `yuemi_main`.`user_finance` WHERE `user_id` = UserId FOR UPDATE;
				IF SYSEmpty = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_FINANCE';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','无账户');
					LEAVE Main;
				END IF;
				SET Coin_New = Coin_Old + TempCoin;
				-- 写入流水
				INSERT INTO `yuemi_main`.`tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'GIVE',OrderId ,Coin_Old,TempCoin,Coin_New,'',UNIX_TIMESTAMP(NOW()),ClientIp);

				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY_C';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				-- 更改账户
				UPDATE `yuemi_main`.`user_finance` 
				SET `coin` = `coin` + TempCoin
				WHERE `user_id` = UserId;
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_SKU';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','阅币赠送失败');
					LEAVE Main;
				END IF;
			END IF;
		END IF;

		-- 更改库存
		UPDATE `yuemi_sale`.`sku` 
		SET `depot` = `depot` - BuyQty 
		WHERE `id` = SkuId;
		
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_SKU';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更新库存失败');
			LEAVE Main;
		END IF;
		
		IF SelUseTicket > 0 THEN 
			UPDATE yuemi_sale.discount_coupon 
			SET `status` = 1 , user_id = UserId , update_time = UNIX_TIMESTAMP() 
			WHERE `id` = TicketId;
		END IF;
-- -------------------------------------------成为VIP
		SELECT `id` INTO TallyCoin FROM `yuemi_main`.`tally_coin` WHERE `user_id` = UserId AND `source` = 'GIVE' LIMIT 1;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
		ELSE 
			SELECT `coin` INTO UCoin FROM `yuemi_main`.`user_finance` WHERE `user_id` = UserId ;
			IF SYSEmpty = 1 OR UCoin < 1000.0000 THEN 
				SET ReturnMessage = CONCAT(ReturnMessage,'->','不可升级','->',UCoin);
			ELSE 
				-- 可以升级
				SET ReturnMessage = CONCAT(ReturnMessage,'->','可升级');
				SET UCoin_New = UCoin - 1000.0;
				-- 写入流水
				INSERT INTO `yuemi_main`.`tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'VIP',OrderId,UCoin,1000.0,UCoin_New,'充值VIP',UNIX_TIMESTAMP(),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				SET TallyId = LAST_INSERT_ID();
				-- 更新账户
				UPDATE `yuemi_main`.`user_finance` SET `coin` = UCoin_New WHERE `user_id` = UserId;
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_FINANCE';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','更新账户错误');
					LEAVE Main;
				END IF;
				

				-- 获取 cheifid
				GetPrev : BEGIN 
					IF BuyerInviterId <= 0 THEN 
						SET CheifId = 0;
						SET DirectorId = 0;
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无邀请人');
						LEAVE GetPrev;
					ELSE 
						WHILE BuyerInviterId > 0 AND CheifId <= 0 DO 
							SELECT `cheif_id`,director_id INTO CheifId,DirectorId FROM `yuemi_main`.`vip` WHERE `user_id` = BuyerInviterId;
							IF SYSEmpty = 1 THEN 
								SET ReturnValue = 'E_IN';
								SET ReturnMessage = CONCAT(ReturnMessage,'->','无效邀请人');
								ROLLBACK;
								LEAVE Main;
							END IF;

							IF CheifId = 0 AND DirectorId = 0 THEN -- 邀请人是总经理或者野生VIP
								SELECT user_id INTO DirectorId FROM	`yuemi_main`.`director` WHERE `user_id` = BuyerInviterId AND status != 0;
								IF SYSEmpty = 1 THEN
									SET SYSEmpty = 0;
									SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人不是总经理');
								ELSE 
									SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人是总经理');
									LEAVE GetPrev;
								END IF;
							ELSEIF CheifId = 0 AND DirectorId != 0 THEN -- 总经理直招或总监
								SELECT director_id INTO DirectorId FROM	`yuemi_main`.`cheif` WHERE `user_id` = BuyerInviterId AND status != 0;
								IF SYSEmpty = 1 THEN -- 邀请人是直招，同为直招
									SET SYSEmpty = 0;
									SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人不是总监');
								ELSE -- 邀请人为总监
									SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人是总监');
									SET CheifId = BuyerInviterId;
									LEAVE GetPrev;
								END IF;
							ELSEIF CheifId != 0 AND DirectorId = 0 THEN -- 不存在
								SET ReturnMessage = CONCAT(ReturnMessage,'->','不存在');
							ELSE -- 全部都有
								SET ReturnMessage = CONCAT(ReturnMessage,'->','全部都有');
								LEAVE GetPrev;
							END IF;

							SELECT invitor_id INTO BuyerInviterId FROM `yuemi_main`.`user` WHERE `id` = BuyerInviterId;
							IF SYSEmpty = 1 THEN 
								SET ReturnValue = 'E_USER';
								SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人出错');
								ROLLBACK;
								LEAVE Main;
							END IF;
						END WHILE;
					END IF;
				END ;

				SET SYSError = 0;
				-- 检查 VIP
				SELECT `user_id`,`invite_code`,`status`,`expire_time` 
				INTO VipId,VipCode,VipStatus,VipExpire 
				FROM `yuemi_main`.`vip` WHERE `user_id` = UserId;
				IF SYSEmpty = 1 THEN
					SET ReturnMessage = CONCAT(ReturnMessage,'->','新VIP');
					SET SYSEmpty = 0;
					SET VipCode = RAND_STRING(8);
					SET VipStatus = 0;
					SET VipExpire = UNIX_TIMESTAMP();
					INSERT INTO `yuemi_main`.`vip` (`user_id`,`cheif_id`,`director_id`,`invite_code`,`status`,`create_time`,`update_time`,`expire_time`)
					VALUES (UserId,CheifId,DirectorId,VipCode,1,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000);
					IF SYSError = 1 THEN
						SET ReturnValue = 'E_VIP';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录错');
						ROLLBACK;
						LEAVE Main;
					END IF;
					SET VipId = LAST_INSERT_ID();

				ELSE
					SET ReturnMessage = CONCAT(ReturnMessage,'->','老VIP');
					-- 检查Status
					SELECT `id`,`expire_time` INTO StatusId,StatusExpire FROM `yuemi_main`.`vip_buff` WHERE `user_id` = UserId ORDER BY `expire_time` DESC LIMIT 1;
					IF SYSEmpty = 1 THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无状态');
						SET SYSEmpty = 0;
						UPDATE `yuemi_main`.`vip` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
						SET VipStatus = 0;
						SET VipExpire = UNIX_TIMESTAMP();
						SET StatusId = 0;
						SET StatusExpire = VipExpire;
					ELSE
						SET ReturnMessage = CONCAT(ReturnMessage,'->','有状态');
						IF StatusExpire != VipExpire THEN
							SET ReturnMessage = CONCAT(ReturnMessage,'->','重新同步');
							IF StatusExpire > UNIX_TIMESTAMP() THEN
								SET ReturnMessage = CONCAT(ReturnMessage,'->','还有效');
								UPDATE `yuemi_main`.`vip` SET `status` = 1,`update_time` = UNIX_TIMESTAMP(),`expire_time` = StatusExpire WHERE `user_id` = UserId;
								SET VipExpire = StatusExpire;
							ELSE
								SET ReturnMessage = CONCAT(ReturnMessage,'->','过期了');
								UPDATE `yuemi_main`.`vip` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = StatusExpire WHERE `user_id` = UserId;
								SET VipExpire = UNIX_TIMESTAMP();
							END IF;
						ELSE
							SET ReturnMessage = CONCAT(ReturnMessage,'->','无需同步');
							IF StatusExpire > UNIX_TIMESTAMP() THEN
								SET ReturnMessage = CONCAT(ReturnMessage,'->','还有效');
								SET VipExpire = StatusExpire;
							ELSE
								SET ReturnMessage = CONCAT(ReturnMessage,'->','过期了');
								SET VipExpire = UNIX_TIMESTAMP();
							END IF;
						END IF;
					END IF;
				END IF;
				IF SYSError = 1 THEN 
					SET ReturnValue = 'E_BUFF';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','同步出错');
					ROLLBACK;
					LEAVE Main;
				END IF;
				-- 新纪录
				INSERT INTO `yuemi_main`.`vip_buff` (`user_id`,`type`,`order_id`,`tally_id`,`coin`,`start_time`,`expire_time`,`create_time`)
				VALUES (UserId,5,'',TallyId,1000.0,VipExpire,VipExpire + 31536000,UNIX_TIMESTAMP());
				IF SYSError = 1 THEN
					SET ReturnValue = 'E_DATABASE';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','新状态错');
					ROLLBACK;
					LEAVE Main;
				END IF;
				UPDATE `yuemi_main`.`vip` SET `status` = 5,`expire_time` = VipExpire + 31536000,`update_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
				IF SYSError = 1 THEN
					SET ReturnValue = 'E_DATABASE';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP记录错');
					ROLLBACK;
					LEAVE Main;
				END IF;
				UPDATE `yuemi_main`.`user` SET `level_v` = 5 WHERE `id` = UserId;
				IF SYSError = 1 THEN
					SET ReturnValue = 'E_DATABASE';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错误');
					ROLLBACK;
					LEAVE Main;
				END IF;
			END IF;
		END IF;
	COMMIT;

	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
DELIMITER ;



