/**
 * 功能 : 购物车多选下单
 * 作者：殷非非
 * 日期：2018-4-22
 * 修订@2018-04-22	: 
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `yuemi_sale`.`cart_purchase` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `cart_purchase`(
	IN UserId			INT UNSIGNED,	/* 用户ID */
	IN UserAddressId 	INT,			/*收货地址*/
	IN ClientIp			BIGINT UNSIGNED,	/*创建IP*/
	IN SelUseMoney		INT,			/*选择使用余额*/
	IN SelUseProfit		INT,			/*选择使用销售佣金*/
	IN SelUseRecruit	INT,			/*选择使用礼包佣金*/
	IN SelUseTicket		INT,			/*选择使用优惠券*/
	IN CommentUser		VARCHAR(64), 	/*用户留言 */

	OUT PrimaryOrderId	VARCHAR(16),    /*返回主订单ID */
	OUT ReturnValue		VARCHAR(32),    /*返回状态值 */
	OUT ReturnMessage	VARCHAR(1024) 	/*返回提示信息 */
)
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT '购物车下单'
Main : BEGIN		/*主体内容开始*/
	/*定义状态*/
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	/*局部变量*/
	DECLARE SkuTitle			VARCHAR(128)		DEFAULT '';
	DECLARE SkuDepot			INT UNSIGNED		DEFAULT 0;
	DECLARE SkuStatus			TINYINT UNSIGNED	DEFAULT 0;
	DECLARE SkuATime			BIGINT UNSIGNED		DEFAULT 0;
	DECLARE SkuBTime			BIGINT UNSIGNED		DEFAULT 0;
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
	DECLARE CUpMoney			NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE CUpProfit			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE CUpRecruitDir		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TempUpMoney			NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE TempUpProfitSelf	NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TempUpProfitShare	NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TempUpRecruitDir	NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TempCMoney			NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE TempCProfitSelf		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TempCProfitShare	NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TempCRecruitDir		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TPayOnline			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE CPayOnline			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TAmount				NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE CAmount				NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Cnt					INT UNSIGNED		DEFAULT 0;
	DECLARE OrderId				VARCHAR(16)			DEFAULT '';
	DECLARE IsPrimary			TINYINT UNSIGNED	DEFAULT 0;
	DECLARE DependId			VARCHAR(16)			DEFAULT '';
	DECLARE Sid					TINYINT UNSIGNED	DEFAULT 0;
	DECLARE I					TINYINT UNSIGNED	DEFAULT 0;
	DECLARE OrderNum			INT UNSIGNED		DEFAULT 0;
	DECLARE OrderPrice			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Ont					INT UNSIGNED		DEFAULT 0;
	DECLARE J					TINYINT UNSIGNED	DEFAULT 0;
	DECLARE TempOrderId			VARCHAR(16)			DEFAULT '';
	DECLARE TUpMoney			NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE TUpProfitSelf		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TUpProfitShare		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TUpRecruitDir		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Percentage			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TPercentage			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE TempPayOnline		NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE TQ					INT UNSIGNED		DEFAULT 0;
	DECLARE TempAmount			INT UNSIGNED		DEFAULT 0;
	DECLARE N					TINYINT UNSIGNED	DEFAULT 0;
	DECLARE M					TINYINT UNSIGNED	DEFAULT 0;
	DECLARE CoinNt				INT UNSIGNED		DEFAULT 0;
	DECLARE CkuId				INT UNSIGNED		DEFAULT 0;
	DECLARE TempCoin			NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE Coin_Old			NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE Coin_New			NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE UCoin				NUMERIC(8,8)		DEFAULT 0.0;	
	DECLARE TransSpuId			INT UNSIGNED		DEFAULT 0;
	DECLARE TransExtSpuId		INT UNSIGNED		DEFAULT 0;
	DECLARE SN					INT UNSIGNED		DEFAULT 0;
	DECLARE TransShopCode		VARCHAR(16)			DEFAULT '';
	DECLARE TransMoney			NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE TemTransMoneyO		NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE TemTransMoneyA		NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE H					INT UNSIGNED		DEFAULT 0;
	DECLARE RebateUser			INT UNSIGNED		DEFAULT 0; -- 返佣账户
	DECLARE RebateMoney			NUMERIC(16,4)		DEFAULT 0.0;	
	DECLARE RebateProfitSelf	NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebateOnline		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebateRecruitDir	NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebateRatio			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebateCheif			INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateDirector		INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateOMoney		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebateN				INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateOn			INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateRatioA		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebateItemId		INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateSkuId			INT	UNSIGNED		DEFAULT 0;
	DECLARE RebatePa			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebatePb			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebatePc			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebateIsSelf		INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateIsShare		INT	UNSIGNED		DEFAULT 0;
	DECLARE RebateTonline		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE RebateQty			INT	UNSIGNED		DEFAULT 0;
	DECLARE CartBigN			INT	UNSIGNED		DEFAULT 0;
	-- 成为VIP定义变量
	DECLARE VUCoin				NUMERIC(16,8)		DEFAULT 0.0;
	DECLARE VUCoin_New			NUMERIC(16,8)		DEFAULT 0.0;
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
	DECLARE TallyCoin			NUMERIC(16,8)		DEFAULT 0.0;
	DECLARE OwnerId				INT					DEFAULT 0; -- 佣金归属人
	DECLARE LimitCnt			INT					DEFAULT 0; -- 限购循环总数
	DECLARE LimitI				INT					DEFAULT 0; -- 限购循环数
	DECLARE LimitSku			INT					DEFAULT 0; -- 限购循环数

	/* 声明异常处理（定义系统错误），必须放在变量定义之后*/
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;

	/* 初始化变量*/
	SET PrimaryOrderId = '';
	SET ReturnValue = '';
	SET ReturnMessage = '购物车下单';
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

	IF BuyerLevelVip > 0 THEN 
		SELECT `cheif_id`,`director_id` INTO RebateCheif,RebateDirector FROM `yuemi_main`.`vip` WHERE `user_id` = UserId;
		SET OwnerId = UserId;
	ELSEIF BuyerInviterId > 0 THEN 
		SELECT `cheif_id`,`director_id` INTO RebateCheif,RebateDirector FROM `yuemi_main`.`vip` WHERE `user_id` = BuyerInviterId;
		IF InvitorLevelVip > 0 THEN 
			SET OwnerId = BuyerInviterId;
		END IF;
	END IF;

	-- 检查购物车中是否存在大礼包商品
	SELECT COUNT(id) INTO CartBigN FROM `yuemi_sale`.`cart` WHERE `user_id` = UserId AND catagory_id = 701;
	IF CartBigN > 0 THEN 
		ROLLBACK;
		SET ReturnValue = 'E_BIG';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','大礼包商品不可加入购物车');
		LEAVE Main;
	ELSE 
		SET SYSEmpty = 0;
	END IF ;

	-- 检查返佣ID 
	IF BuyerLevelVip > 0 THEN 
		SET RebateUser = UserId;
		SET RebateIsSelf = 1;
		SET RebateIsShare = 0;
	ELSEIF BuyerInviterId > 0 THEN 
		SET RebateUser = BuyerInviterId;
	ELSE 
		SET RebateUser = 0;
	END IF;
	

	-- 检查地址并写入变量
	SET SYSEmpty = 0;
	SELECT `region_id`,`address`,`contacts`,`mobile` 
	INTO	OrderAddrRegion,OrderAddrDetail,OrderAddrName,OrderMobile 
	FROM `yuemi_main`.`user_address` 
	WHERE `id` = UserAddressId AND `user_id` = UserId;

	IF SYSEmpty = 1 THEN
		SET ReturnValue = 'E_ADDRESS';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','地址错误');
		LEAVE Main;
	END IF;


	--  检查限购
	SELECT COUNT(*) INTO LimitCnt FROM `yuemi_sale`.cart WHERE user_id = UserId AND `yuemi_sale`.`cart`.`is_checked` > 0 ;
	-- 检查空车
	IF SYSEmpty = 1 OR LimitCnt < 1 THEN
		SET ReturnValue = 'E_EMPTY';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','空车');
		LEAVE Main;
	END IF;

	SET LimitI = 0;
	WHILE LimitI < LimitCnt DO	
		SELECT `sku_id` INTO LimitSku FROM `yuemi_sale`.cart WHERE user_id = UserId AND `yuemi_sale`.`cart`.`is_checked` > 0 ORDER BY `id` ASC LIMIT LimitI , 1;
		
		SELECT limit_style,limit_size
		INTO SkuLimitStyle,SkuLimitCount
		FROM `sku` WHERE `id` = LimitSku;

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
		SET LimitI = LimitI + 1 ;
	END WHILE;

	SET I = 0;
	START TRANSACTION;
	-- 更新购物车货物的供应商ID以及数量
		SET SYSError = 0;

		UPDATE `yuemi_sale`.cart , `yuemi_sale`.sku,`yuemi_sale`.spu
		SET `yuemi_sale`.cart.supplier_id = `yuemi_sale`.spu.supplier_id
		WHERE `yuemi_sale`.cart.user_id = UserId AND `yuemi_sale`.cart.`is_checked` > 0 
		AND `yuemi_sale`.sku.id = `yuemi_sale`.cart.sku_id
		AND `yuemi_sale`.spu.id = `yuemi_sale`.sku.spu_id;
		IF SYSError = 1 THEN 
			SET ReturnValue = 'E_UP_1';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','购物车货物更新错误1',UserId);
			ROLLBACK;
			LEAVE Main;
		END IF;

		SET SYSError = 0;

		UPDATE `yuemi_sale`.cart , `yuemi_sale`.sku,`yuemi_sale`.spu
		SET `yuemi_sale`.cart.qty = `yuemi_sale`.sku.depot 
		WHERE `yuemi_sale`.cart.user_id = UserId AND `yuemi_sale`.cart.`is_checked` > 0 
		AND `yuemi_sale`.sku.id = `yuemi_sale`.cart.sku_id
		AND `yuemi_sale`.spu.id = `yuemi_sale`.sku.spu_id
		AND `yuemi_sale`.cart.qty > `yuemi_sale`.sku.depot;

		IF SYSError = 1 THEN 
			SET ReturnValue = 'E_UP_1';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','购物车货物更新错误',UserId);
			ROLLBACK;
			LEAVE Main;
		END IF;
	
		SELECT COUNT(distinct supplier_id)  INTO Cnt FROM `yuemi_sale`.cart WHERE user_id = UserId AND `yuemi_sale`.`cart`.`is_checked` > 0 ;


		-- 重新同步价格
		SET SYSError = 0;
		IF  BuyerInviterId > 0 THEN
			UPDATE `yuemi_sale`.`cart`,`yuemi_sale`.`sku`
			SET `yuemi_sale`.`cart`.`sku_price` = `yuemi_sale`.`sku`.`price_inv`
			WHERE `yuemi_sale`.`cart`.`sku_id` = `yuemi_sale`.`sku`.`id` 
			 AND `yuemi_sale`.`sku`.`price_inv` > 0;
		END IF;
		IF BuyerLevelVip = 0 AND BuyerLevelUser > 0 THEN
			UPDATE `yuemi_sale`.`cart`,`yuemi_sale`.`sku`
			SET `yuemi_sale`.`cart`.`sku_price` = `yuemi_sale`.`sku`.`price_sale`
			WHERE `yuemi_sale`.`cart`.`sku_id` = `yuemi_sale`.`sku`.`id` 
			 AND `yuemi_sale`.`sku`.`price_sale` > 0;
		END IF;

		IF SYSError = 1 THEN 
			SET ReturnValue = 'E_UP_2';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','价格更新错误');
			ROLLBACK;
			LEAVE Main;
		END IF;

		SET SYSEmpty = 0;
		-- 购物车下单
		WHILE I < Cnt DO 
		-- 生成订单ID，并检查主订单ID
			SET OrderId = NEW_ORDER_ID('P','');
			IF I = 0 THEN
				SET IsPrimary = 1;
				SET PrimaryOrderId = OrderId;
				IF Cnt > 1 THEN
					SET DependId = OrderId;
				ELSE
					SET DependId = '';
				END IF;
			ELSE
				SET IsPrimary = 0;
			END IF;

			-- 开始按供应商分组
			SELECT distinct supplier_id INTO Sid FROM `yuemi_sale`.`cart` WHERE user_id = UserId AND `is_checked` > 0 ORDER BY supplier_id ASC LIMIT I,1;
			IF SYSEmpty = 1 THEN
				SET ReturnValue = 'E_SID';
				SET ReturnMessage = CONCAT(ReturnMessage,'->',Sid);
				ROLLBACK;
				LEAVE Main;
			END IF;
			-- 插入订单
			INSERT INTO `yuemi_sale`.`order`(`id`,`user_id`,`is_primary`,`depend_id`,`supplier_id`,`qty`,`t_amount`,`c_amount`,`pay_serial`,`pay_time`,`address_id`,`addr_region`,`addr_detail`,`addr_name`,`addr_mobile`,`create_time`,`trans_id`,`status`,comment_user)
			SELECT OrderId,UserId,IsPrimary,PrimaryOrderId,Sid,C.`qty`,0,C.`qty` * C.`sku_price`,'',UNIX_TIMESTAMP(NOW()),UserAddressId,OrderAddrRegion,OrderAddrDetail,OrderAddrName,OrderMobile,UNIX_TIMESTAMP(NOW()),'',0 ,CommentUser
			FROM `yuemi_sale`.`cart` AS C 
			WHERE C.`user_id` = UserId AND C.`supplier_id` = Sid AND C.`is_checked` > 0 
			ORDER BY C.`id` ASC 
			LIMIT 1;

			IF SYSError = 1 THEN
				SET ReturnValue = 'E_I_OR';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','插入order失败');
				ROLLBACK;
				LEAVE Main;
			END IF;

			IF SYSEmpty = 1 THEN
				-- 空订单
				SET ReturnValue = 'E_SID_1';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无记录');
				ROLLBACK;
				LEAVE Main;
			END IF;


			-- 插入order_item表
			INSERT INTO `yuemi_sale`.`order_item`(`rebate_user`,`price_base`,`rebate_vip`,`order_id`,`share_id`,`sku_id`,`spu_id`,`catagory_id`,`supplier_id`,`qty`,`price`,`money`,`title`,`picture`)
			SELECT RebateUser,C.`sku_price`,K.`rebate_vip` * C.`qty`,OrderId,C.`share_id`,C.`sku_id`,C.`spu_id`,C.`catagory_id`,Sid,C.`qty`,C.`sku_price`,C.`sku_price` * C.`qty`,C.`sku_title`,C.`sku_thumb`
			FROM `yuemi_sale`.`cart` AS C 
			LEFT JOIN `yuemi_sale`.`sku` AS K ON C.`sku_id` = K.`id` 
			WHERE C.`user_id` = UserId AND C.`supplier_id` = Sid AND C.`is_checked` > 0 AND K.`depot` >= C.`qty`;

			IF SYSEmpty = 1 THEN
				-- 空订单
				SET ReturnValue = 'E_SID';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无记录');
				ROLLBACK;
				LEAVE Main;
			END IF;

			-- 回去更新订单总价
			SELECT SUM(`qty`),SUM(`money`) INTO OrderNum,OrderPrice FROM `yuemi_sale`.`order_item` WHERE `order_id` = OrderId;
			UPDATE `yuemi_sale`.`order` 
			SET `qty` = OrderNum , `c_amount` = OrderPrice
			WHERE `id` = OrderId;

			SET TAmount = TAmount + OrderPrice;

			IF SYSError = 1 THEN
				SET ReturnValue =  'E_U';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新失败');
				ROLLBACK;
				LEAVE Main;
			END IF;

			UPDATE `yuemi_sale`.`order` 
			SET `t_amount` = TAmount
			WHERE `depend_id` = PrimaryOrderId;

			IF SYSError = 1 THEN
				SET ReturnValue =  'E_U';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新失败');
				ROLLBACK;
				LEAVE Main;
			END IF;


			-- 计算运费
			IF Sid = 2 THEN 
				SELECT COUNT(C.id) INTO SN 
				FROM `yuemi_sale`.`cart` AS C 
				LEFT JOIN `yuemi_sale`.`sku` AS K ON C.`sku_id` = K.`id` 
				WHERE C.`user_id` = UserId AND C.`supplier_id` = Sid AND C.`is_checked` > 0 AND K.`depot` >= C.`qty`;

				SET H = 0;
				WHILE H < SN DO
					-- 查出每一个商品的ext_spu_id
					SELECT C.ext_spu_id,C.`sku_price` * C.`qty` INTO TransExtSpuId,TemTransMoneyO 
					FROM `yuemi_sale`.`cart` AS C 
					LEFT JOIN `yuemi_sale`.`sku` AS K ON C.`sku_id` = K.`id` 
					WHERE C.`user_id` = UserId AND C.`supplier_id` = Sid AND C.`is_checked` > 0 AND K.`depot` >= C.`qty` 
					ORDER BY C.`id` DESC LIMIT H,1;

					-- 查出供货商
					SELECT `ext_shop_code` INTO TransShopCode FROM `yuemi_sale`.`ext_spu` WHERE `id` = TransExtSpuId;

					IF TransShopCode = 'JD' THEN 
						SET TemTransMoneyA = TemTransMoneyA + TemTransMoneyO;
						IF TemTransMoneyA < 99 THEN 
							SET TransMoney = 10.0;
						ELSE 
							SET TransMoney = 0;
						END IF;
					END IF;

					SET H = H + 1;
				END WHILE;


				UPDATE `yuemi_sale`.`order` 
				SET t_trans = TransMoney ,c_amount = c_amount + TransMoney 
				WHERE id = OrderId;

				UPDATE `yuemi_sale`.`order` 
				SET t_amount = t_amount + TransMoney 
				WHERE depend_id = PrimaryOrderId; 
			END IF;
			SET SYSError = 0;

			SET I = I + 1;
		END WHILE;

		-- ---------------------------------------------------------------------	计算佣金并扣除

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
			SELECT COUNT(`id`),`t_amount` INTO Ont,Total FROM `yuemi_sale`.`order` WHERE `depend_id` = PrimaryOrderId;
			SET TPayOnline = Total;
			--  检查余额
			IF SelUseMoney > 0 AND UserMoney > 0 THEN
				IF UserMoney > TPayOnline THEN 
					SET TUpMoney = TPayOnline;
					SET TPayOnline = 0;
				ELSE 
					SET TUpMoney = UserMoney;
					SET TPayOnline = TPayOnline-UserMoney;
				END IF;
				SET TempUpMoney = UserMoney;
			END IF;

			IF SelUseProfit > 0 AND UserProfitSelf > 0 AND TPayOnline > 0 THEN
				IF UserProfitSelf > TPayOnline THEN
					SET TUpProfitSelf = TPayOnline;
					SET TPayOnline = 0;
				ELSE 
					SET TUpProfitSelf = UserProfitSelf;
					SET TPayOnline = TPayOnline-UserProfitSelf;
				END IF;
				SET TempUpProfitSelf = UserProfitSelf;
			END IF;

			IF SelUseProfit > 0 AND UserProfitShare > 0 AND TPayOnline > 0 THEN 
				IF UserProfitShare > TPayOnline THEN 
					SET TUpProfitShare = TPayOnline;
					SET TPayOnline = 0;
				ELSE 
					SET TUpProfitShare = UserProfitShare;
					SET TPayOnline = TPayOnline - UserProfitShare;
				END IF;
				SET TempUpProfitShare = UserProfitShare;
			END IF;
			IF SelUseRecruit > 0 AND ThewStatus > 0 AND UserRecruitDir > 0 AND TPayOnline > 0 THEN 
				IF UserRecruitDir > TPayOnline THEN 
					SET TUpRecruitDir = TPayOnline;
					SET TPayOnline = 0;
				ELSE 
					SET TUpRecruitDir = UserRecruitDir;
					SET TPayOnline = TPayOnline - UserRecruitDir;
				END IF;
				SET TempUpRecruitDir = UserRecruitDir;
			END IF;

			IF Total != TUpMoney + TUpProfitSelf + TUpProfitShare + TUpRecruitDir + TPayOnline THEN 
				SET ReturnValue = 'E_TOTLE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','金额错误');
				ROLLBACK;
				LEAVE Main;
			END IF;

			-- 更新总的订单余额，佣金，礼包
			-- 更新账户 余额

			IF TUpMoney > 0 THEN
				UPDATE `yuemi_main`.`user_finance` 
				SET `money` = UserMoney-TUpMoney
				WHERE `user_id` = UserId;

				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_F';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','更新错');
					LEAVE Main;
				END IF;
			END IF;
			-- 更新账户 自省佣金

			IF TUpProfitSelf > 0 THEN 
				UPDATE `yuemi_main`.`user_finance` 
				SET `profit_self` = UserProfitSelf-TUpProfitSelf  
				WHERE `user_id` = UserId;

				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_F';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','更新错');
					LEAVE Main;
				END IF;
			END IF;
			-- 更新分享佣金

			IF TUpProfitShare > 0 THEN 
				UPDATE `yuemi_main`.`user_finance` 
				SET `profit_share` = UserProfitShare - TUpProfitShare  
				WHERE `user_id` = UserId;

				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_F';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','更新错');
					LEAVE Main;
				END IF;
			END IF;
			-- 更新佣金礼包
			IF TUpRecruitDir > 0 THEN 
				UPDATE `yuemi_main`.`user_finance` 
				SET `recruit_dir` = UserRecruitDir - TUpRecruitDir  
				WHERE `user_id` = UserId;

				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_F';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','更新错');
					LEAVE Main;
				END IF;
			END IF;
			-- 更新order表
			IF TPayOnline = 0 THEN 
				UPDATE `yuemi_sale`.`order` 
				SET `t_money` = TUpMoney,`t_profit` = TUpProfitSelf + TUpProfitShare,`t_recruit` = TUpRecruitDir , `status` = 2 , `t_online` = 0
				WHERE `depend_id` = PrimaryOrderId;
			ELSEIF TPayOnline < 0 THEN 
				ROLLBACK;
				SET ReturnValue = 'E_F_M';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','扣除佣金礼包失败');
				LEAVE Main;
			ELSEIF TPayOnline > 0 THEN 
				UPDATE `yuemi_sale`.`order` 
				SET `t_money` = TUpMoney,`t_profit` = TUpProfitSelf + TUpProfitShare,`t_recruit` = TUpRecruitDir , `status` = 1 , `t_online` = TPayOnline
				WHERE `depend_id` = PrimaryOrderId;
			END IF;

			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_U_OR';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新order失败');
				LEAVE Main;
			END IF;

	-- ---------------------------------------------------------------------- 更新order以及order_item
			SET J = 0;
			WHILE J < Ont DO 
				IF J = Ont - 1 THEN 
					SET Percentage = 1 - TPercentage;
					SELECT `id`,`c_amount` INTO TempOrderId,CAmount FROM `yuemi_sale`.`order` WHERE `depend_id` = PrimaryOrderId ORDER BY `id` ASC LIMIT J,1;
				ELSEIF J < Ont - 1 THEN 
					SELECT `id`,`c_amount` INTO TempOrderId,CAmount FROM `yuemi_sale`.`order` WHERE `depend_id` = PrimaryOrderId ORDER BY `id` ASC LIMIT J,1;
					SELECT FORMAT(CAmount/Total,4) INTO Percentage;

					SET TPercentage = TPercentage + Percentage;
				END IF;
				-- 更新order表
				SET RebateMoney = Percentage * TUpMoney ;
				SET RebateProfitSelf = TUpProfitSelf * Percentage + TUpProfitShare * Percentage;
				SET RebateRecruitDir = TUpRecruitDir * Percentage;
				SET RebateOnline = IF( TPayOnline = 0 ,0, CAmount - Percentage * TUpMoney - TUpProfitSelf * Percentage - TUpProfitShare * Percentage -TUpRecruitDir * Percentage);
				UPDATE `yuemi_sale`.`order` 
				SET `c_money` =  RebateMoney , 
					`c_profit` = RebateProfitSelf , 
					`c_recruit` = RebateRecruitDir , 
					`c_online` = `c_online` + RebateOnline 
				WHERE `id` = TempOrderId;

				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;

				SELECT COUNT(`id`) INTO RebateOn FROM `yuemi_sale`.`order_item` WHERE `order_id` = TempOrderId;
				SET RebateN = 0;
				SET RebateRatioA = 0;
				WHILE RebateN < RebateOn DO 
					SELECT `id`,`money`,`sku_id`,`price`,`qty` INTO RebateItemId,RebateOMoney,RebateSkuId,RebatePa,RebateQty FROM `yuemi_sale`.`order_item` WHERE `order_id` = TempOrderId ORDER BY `id` DESC LIMIT RebateN,1;
					SELECT `price_base` INTO RebatePb FROM `yuemi_sale`.`sku` WHERE `id` = RebateSkuId;
					-- 计算毛利
					SET RebatePc = (RebatePa - RebatePb) * RebateQty;
					IF RebatePc < 0 THEN 
						SET RebatePc = 0;
					END IF;

					-- 计算比例
					IF RebateN = RebateOn - 1 THEN 
						SET RebateRatio = 1 - RebateRatioA;
					ELSE 
						SELECT FORMAT(RebateOMoney/CAmount,4) INTO RebateRatio;
						SET RebateRatioA = RebateRatioA + RebateRatio;
					END IF;
					IF RebatePc > 0 THEN 

						-- 写入rebate表中
						INSERT INTO `yuemi_sale`.`rebate`(`item_id`,`order_id`,`buyer_id`,`buyer_vip`,`cheif_id`,`director_id`,`sku_id`,`time_create`,`spu_id`,`pay_count`,`pay_total`,`pay_money`,`pay_profit`,`pay_ticket`,`pay_online`,`total_profit`,`system_profit`,`self_profit`,`share_profit`,`cheif_ratio`,`cheif_profit`,`director_ratio`,`director_profit`,`status`,`create_time`,`invitor_id`,`invitor_vip`,`owner_id`)
						SELECT RebateItemId,TempOrderId,UserId,BuyerLevelVip,RebateCheif,RebateDirector,`sku_id`,UNIX_TIMESTAMP(),`spu_id`,`qty`,`money`,RebateMoney * RebateRatio,RebateProfitSelf * RebateRatio,0,RebateOnline * RebateRatio,RebatePc , RebatePc * 0.2 , RebatePc * 0.8 * 0.7 * RebateIsSelf ,RebatePc * 0.8 * 0.7 * RebateIsShare , 0.12 , RebatePc * 0.12 , 0.08 , RebatePc * 0.08 , 0 ,UNIX_TIMESTAMP(),BuyerInviterId,InvitorLevelVip,OwnerId
						FROM `yuemi_sale`.`order_item` WHERE `id` = RebateItemId;

						IF SYSError = 1 THEN
							ROLLBACK;
							SET ReturnValue = 'E_REBATE';
							SET ReturnMessage = CONCAT(ReturnMessage,'->','更新rebate失败');
							LEAVE Main;
						END IF;
					END IF;

					SET RebateN = RebateN + 1;
				END WHILE ;

				SET J = J + 1;
			END WHILE;
-- ---------------------------------------------------------------------- 写入流水 START
		SET N = 0;
		WHILE N < Ont DO 
			IF TUpMoney > 0 THEN 
				SELECT `id`,`c_money` INTO TempOrderId,TempCMoney 
				FROM `yuemi_sale`.`order` 
				WHERE  `depend_id` = PrimaryOrderId 
				ORDER BY `id` ASC 
				LIMIT N,1;
				INSERT INTO `yuemi_main`.`tally_money` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'BUY',TempOrderId,TempUpMoney , -TempCMoney ,TempUpMoney - TempCMoney ,'',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				SET TempUpMoney = TempUpMoney - TempCMoney;
			END IF;

			IF TUpProfitSelf > 0 AND TUpProfitShare > 0 THEN 
				SELECT `id`,`c_profit` INTO TempOrderId,CUpProfit 
				FROM `yuemi_sale`.`order` 
				WHERE  `depend_id` = PrimaryOrderId 
				ORDER BY `id` ASC 
				LIMIT N,1;
				-- 计算比例
				SELECT FORMAT(TUpProfitSelf/(TUpProfitShare + TUpProfitSelf),4) INTO Percentage;

				INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'SELF','BUY',TempOrderId,TempUpProfitSelf, - (CUpProfit*Percentage),TempUpProfitSelf - (CUpProfit*Percentage),'',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				
				SET TempUpProfitSelf = TempUpProfitSelf - (CUpProfit*Percentage);
				INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'SHARE','BUY',TempOrderId,TempUpProfitShare,-(CUpProfit*(1-Percentage)),TempUpProfitShare-(CUpProfit*(1-Percentage)),'',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				SET TempUpProfitShare = TempUpProfitShare-(CUpProfit*(1-Percentage));
			END IF;

			IF TUpProfitSelf > 0 AND TUpProfitShare = 0 THEN 
				SELECT `id`,`c_profit` INTO TempOrderId,CUpProfit 
				FROM `yuemi_sale`.`order` 
				WHERE  `depend_id` = PrimaryOrderId 
				ORDER BY `id` ASC 
				LIMIT N,1;
				-- 计算比例

				INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'SELF','BUY',TempOrderId,TempUpProfitSelf, - CUpProfit,TempUpProfitSelf - CUpProfit,'',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				
				SET TempUpProfitSelf = TempUpProfitSelf - CUpProfit;
				
			END IF;


			IF TUpRecruitDir > 0 THEN 
				SELECT `id`,`c_recruit` INTO TempOrderId, TempCRecruitDir 
				FROM `yuemi_sale`.`order` 
				WHERE  `depend_id` = PrimaryOrderId 
				ORDER BY `id` ASC 
				LIMIT N,1;
				-- 计算比例

				INSERT INTO `yuemi_main`.`tally_recruit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'DIR','BUY',TempOrderId,TempUpRecruitDir,-TempCRecruitDir,TempUpRecruitDir - TempCRecruitDir,'',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				
				SET TempUpRecruitDir = TempUpRecruitDir - TempCRecruitDir;
				
			END IF;

			SET N = N + 1 ;
		END WHILE;

-- -------------------------------------------- 写入流水 END
		
		-- 更改库存
		UPDATE `yuemi_sale`.`sku` , `yuemi_sale`.`order`, `yuemi_sale`.`order_item` 
		SET `yuemi_sale`.`sku`.`depot` = `yuemi_sale`.`sku`.`depot` - `yuemi_sale`.`order_item`.`qty`  
		WHERE `yuemi_sale`.`sku`.`id` = `yuemi_sale`.`order_item`.`sku_id` 
		AND `yuemi_sale`.`order_item`.`order_id` =  `yuemi_sale`.`order`.`id` 
		AND `yuemi_sale`.`order`.`depend_id` = PrimaryOrderId;
		
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_SKU';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更新库存失败');
			LEAVE Main;
		END IF;

		-- 删除购物车
 		DELETE FROM `yuemi_sale`.`cart` 
		WHERE `user_id` = UserId AND `is_checked` > 0 ;
		IF SYSError = 1 THEN
			SET ReturnValue =  'E_D_C';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','删除失败');
			ROLLBACK;
			LEAVE Main;
		END IF;
-- --------------------------------------------阅币赠送

		IF TPayOnline = 0 THEN -- 状态已经是2
			SELECT COUNT(K.`id`) INTO CoinNt 
			FROM `yuemi_sale`.`sku` AS K 
			WHERE K.`coin_style` != 0 AND K.`coin_buyer` > 0 AND `id` IN (
				SELECT I.`sku_id` FROM `yuemi_sale`.`order` AS O 
				LEFT JOIN `yuemi_sale`.`order_item` AS I ON O.`id` = I.`order_id` 
				WHERE  O.`depend_id` = PrimaryOrderId 
			);
			SET M = 0 ;
			WHILE M < CoinNt DO 
				-- 获取赠送的阅币
				SELECT `coin_buyer` INTO TempCoin 
				FROM `yuemi_sale`.`sku` AS K 
				WHERE K.`coin_style` != 0 AND K.`coin_buyer` > 0 AND `id` IN (
					SELECT I.`sku_id` FROM `yuemi_sale`.`order` AS O 
					LEFT JOIN `yuemi_sale`.`order_item` AS I ON O.`id` = I.`order_id` 
					WHERE  O.`depend_id` = PrimaryOrderId 
				) ORDER BY `id` DESC LIMIT M,1;
				-- 更改账户
				
				IF TempCoin >= 1000 THEN 
					ROLLBACK;
					SET ReturnValue = 'E_BIG';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','大礼包商品不可加入购物车');
					LEAVE Main;
				END IF;
				IF TempCoin > 0 THEN 
					SELECT `coin` INTO Coin_Old FROM `yuemi_main`.`user_finance` WHERE `user_id` = UserId FOR UPDATE;
					IF SYSEmpty = 1 THEN
						ROLLBACK;
						SET ReturnValue = 'E_FINANCE';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无账户');
						LEAVE Main;
					END IF;
					SET Coin_New = Coin_Old + TempCoin;

					INSERT INTO `yuemi_main`.`tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
					VALUES (UserId,'GIVE',PrimaryOrderId ,Coin_Old,TempCoin,Coin_New,'',UNIX_TIMESTAMP(NOW()),ClientIp);

					IF SYSError = 1 THEN
						ROLLBACK;
						SET ReturnValue = 'E_TALLY_C';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
						LEAVE Main;
					END IF;

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
				-- 写入流水
				SET M = M + 1;
			END WHILE;
		END IF;
-- ------------------------------------------------------------------------成为VIP
		SELECT `id` INTO TallyCoin FROM `yuemi_main`.`tally_coin` WHERE `user_id` = UserId LIMIT 1;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
		ELSE 
			SELECT `coin` INTO VUCoin FROM `yuemi_main`.`user_finance` WHERE `user_id` = UserId ;
			IF SYSEmpty = 1 OR VUCoin < 1000.0000 THEN 
				SET ReturnMessage = CONCAT(ReturnMessage,'->','不可升级','->',VUCoin);
			ELSE 
				-- 可以升级
				SET ReturnMessage = CONCAT(ReturnMessage,'->','可升级');
				SET VUCoin_New = VUCoin - 1000.0;
				-- 写入流水
				INSERT INTO `yuemi_main`.`tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'VIP',PrimaryOrderId,VUCoin,1000.0,VUCoin_New,'充值VIP',UNIX_TIMESTAMP(),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				SET TallyId = LAST_INSERT_ID();
				-- 更新账户
				UPDATE `yuemi_main`.`user_finance` SET `coin` = VUCoin_New WHERE `user_id` = UserId;
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