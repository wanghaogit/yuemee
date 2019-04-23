/**
 * 功能 : 取消订单
 * 作者：王少宏
 * 日期：2018-4-25
 * 修订@2018-04-25	: 
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `yuemi_sale`.`close_order` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `close_order`(
	IN	OrderId			VARCHAR(16),	 /*主订单ID */
	IN	ClientIp		BIGINT UNSIGNED,	/*创建IP*/

	OUT ReturnValue		VARCHAR(32),    /*返回状态值 */
	OUT ReturnMessage	VARCHAR(1024) 	/*返回提示信息 */
)
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT '取消订单'
Main : BEGIN		/*主体内容开始*/
	/*定义状态*/
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE UserId				INT DEFAULT 0;
	DECLARE OStatus				INT DEFAULT 0;
	DECLARE Ont					INT DEFAULT 0;
	DECLARE Oint				INT DEFAULT 0;
	DECLARE I					INT DEFAULT 0;
	DECLARE M					INT DEFAULT 0;
	DECLARE N					NUMERIC(16,8)	DEFAULT 0.0;	
	DECLARE ItemQty				INT DEFAULT 0;
	DECLARE TempSkuId			INT DEFAULT 0;
	DECLARE TempOrderId			VARCHAR(16)		DEFAULT 0;
	DECLARE TempCMoney			NUMERIC(16,4)	DEFAULT 0.0;	
	DECLARE TempCProfitSelf		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE TempCProfitShare	NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE TempCRecruitDir		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE TempCCoin			NUMERIC(16,8)	DEFAULT 0.0;
	DECLARE TempCPayOnline		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OldCMoney			NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OldCMoneyM			NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OldCProfitSelf		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OldCProfitShare		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OldCRecruitDir		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OldCCoin			NUMERIC(16,8)	DEFAULT 0.0;
	DECLARE RebateStatus		INT				DEFAULT 0; -- rebate 中的状态

	DECLARE TempItemId			INT UNSIGNED	DEFAULT 0; -- item id
	DECLARE OwnerId				INT UNSIGNED	DEFAULT 0; -- 自买佣金归属
	DECLARE ShareUserId			INT UNSIGNED	DEFAULT 0; -- 分享人
	DECLARE SelfProfit			NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE ShareProfit			NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE Profit_Old			NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE Profit_New			NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE RebateN				INT UNSIGNED	DEFAULT 0; -- 该订单下的rebate数据个数
	DECLARE J					INT UNSIGNED	DEFAULT 0; -- 循环参数
	DECLARE DiscountCouponId	VARCHAR(32)		DEFAULT ''; -- 循环参数
	/* 声明异常处理（定义系统错误），必须放在变量定义之后*/
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	/* 初始化变量*/
	SET ReturnValue = '';
	SET ReturnMessage = '取消订单';
	
	-- 检查订单
	SELECT `status`,`user_id`,`discount_coupon_id` INTO OStatus,UserId,DiscountCouponId FROM `yuemi_sale`.`order` WHERE `id` = OrderId LIMIT 0,1;
	IF SYSEmpty = 1 THEN 
		SET ReturnValue = 'E_ORDER';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无订单');
		LEAVE Main;
	END IF;
	
	IF OStatus = 1 THEN 
		SET ReturnValue = 'E_STATUS';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','订单不可取消');
		LEAVE Main;
	END IF;

	IF LENGTH(DiscountCouponId) > 1 THEN 
		SET ReturnValue = 'E_ORDER';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','优惠券商品不予退款');
		LEAVE Main;
	END IF;

	IF OStatus != 1 AND OStatus != 2 AND OStatus != 0 AND OStatus != 4  AND OStatus != 6 AND OStatus != 7 AND OStatus != 8 THEN 
		SET ReturnValue = 'E_STATUS';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','订单不可取消');
		LEAVE Main;
	END IF;

	IF OStatus = 2  OR OStatus = 4 OR OStatus = 6 OR OStatus = 7 OR OStatus = 8 THEN 
		SELECT sum(val_delta) INTO N FROM `yuemi_main`.`tally_coin` WHERE `order_id` = OrderId;
		IF N > 500 THEN 
			SET ReturnValue = 'E_COIN';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','阅币赠送超过500');
			LEAVE Main;
		ELSE 
			SET SYSEmpty = 0;
		END IF;
	END IF;


	-- 检查账户
	SET SYSEmpty = 0;
	SELECT `money`,`profit_self`,`profit_share`,`recruit_dir`,`coin` INTO OldCMoney,OldCProfitSelf,OldCProfitShare,OldCRecruitDir,OldCCoin FROM `yuemi_main`.`user_finance`WHERE `user_id` = UserId;
	IF SYSEmpty = 1 THEN
		SET ReturnValue = 'E_F_P';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错误',UserId);
		LEAVE Main;
	END IF;

	SELECT COUNT(`id`) INTO Ont FROM `yuemi_sale`.`order` WHERE `depend_id` = OrderId ;
	START TRANSACTION;
		-- 对所有的订单进行循环
		WHILE I < Ont DO 
			-- 查到订单的ID
			SELECT `id`,`c_online` INTO TempOrderId,TempCPayOnline FROM `yuemi_sale`.`order` WHERE `depend_id` = OrderId AND `user_id` = UserId ORDER BY `id` ASC LIMIT I,1;
--     -----------------------------------------------------------------------退给用户，包括使用的余额，佣金
			-- 从流水中取出应该退的钱
			SELECT `val_delta` INTO TempCMoney FROM	`yuemi_main`.`tally_money` WHERE `order_id` = TempOrderId AND `source` = 'BUY' LIMIT 1;
			IF SYSEmpty = 1 THEN 
				SET SYSEmpty = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无余额信息');
			END IF;

			SELECT `val_delta` INTO TempCProfitSelf FROM `yuemi_main`.`tally_profit` WHERE `order_id` = TempOrderId AND `source` = 'BUY' AND `target` = 'SELF' LIMIT 1;
			IF SYSEmpty = 1 THEN 
				SET SYSEmpty = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无自省佣金信息');
			END IF;
			
			SELECT `val_delta` INTO TempCProfitShare FROM `yuemi_main`.`tally_profit` WHERE `order_id` = TempOrderId AND `source` = 'BUY' AND `target` = 'SHARE' LIMIT 1;
			IF SYSEmpty = 1 THEN 
				SET SYSEmpty = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无分享佣金信息');
			END IF;

			SELECT `val_delta` INTO TempCRecruitDir FROM `yuemi_main`.`tally_recruit` WHERE `order_id` = TempOrderId AND `source` = 'BUY' AND `target` = 'DIR' LIMIT 1;
			IF SYSEmpty = 1 THEN 
				SET SYSEmpty = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无领礼包信息');
			END IF;
			
			-- 退回到账户
			IF TempCMoney != 0 OR TempCProfitSelf != 0 OR TempCProfitShare != 0 OR TempCRecruitDir != 0 THEN 
				UPDATE `yuemi_main`.`user_finance` 
				SET `money` = `money` - TempCMoney,
					`profit_self` = `profit_self` - TempCProfitSelf,
					`profit_share` = `profit_share` - TempCProfitShare,
					`recruit_dir` = `recruit_dir` - TempCRecruitDir
				WHERE `user_id` = UserId;
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_FINANCE_1';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','退回账户错');
					LEAVE Main;
				END IF;
			END IF;

			-- 写回到流水	OldCMoney,OldCProfitSelf,OldCProfitShare,OldCRecruitDir
			IF TempCMoney < 0 THEN 
				INSERT INTO `yuemi_main`.`tally_money` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'CANCLE',TempOrderId,OldCMoney , -TempCMoney ,OldCMoney - TempCMoney ,'',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY_M';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				SET OldCMoney =OldCMoney - TempCMoney;
			END IF;
			IF TempCProfitSelf < 0 THEN 
				INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'SELF','CANCLE',TempOrderId,OldCProfitSelf, - TempCProfitSelf,OldCProfitSelf - TempCProfitSelf,'',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY_F';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				SET OldCProfitSelf = OldCProfitSelf - TempCProfitSelf;
			END IF;
			IF OldCProfitShare < 0 THEN 
				INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'SHARE','CANCLE',TempOrderId,OldCProfitShare, - TempCProfitShare,OldCProfitShare - TempCProfitShare,'',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY_E';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				SET OldCProfitShare = OldCProfitShare - TempCProfitShare;
			END IF;
			IF OldCRecruitDir < 0 THEN 
				INSERT INTO `yuemi_main`.`tally_recruit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'DIR','BUY',TempOrderId,OldCRecruitDir,-TempCRecruitDir,OldCRecruitDir - TempCRecruitDir,'',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY_D';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				SET OldCRecruitDir = OldCRecruitDir - TempCRecruitDir;
			END IF;



			-- 当状态为 2 更新账户，将c_online 加到账户
			IF OStatus = 2 OR OStatus = 4 OR OStatus = 6 OR OStatus = 7 OR OStatus = 8 THEN 
				SELECT `money` INTO OldCMoneyM FROM `yuemi_main`.`user_finance` WHERE `user_id` = UserId;  
				SELECT `c_online` INTO TempCPayOnline FROM `yuemi_sale`.`order` WHERE `id` = TempOrderId;

				INSERT INTO `yuemi_main`.`tally_money` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'MONEY',TempOrderId,OldCMoneyM , TempCPayOnline ,OldCMoneyM + TempCPayOnline ,'',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY_M';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;

				UPDATE `yuemi_main`.`user_finance` 
				SET `money` = `money` + TempCPayOnline
				WHERE `user_id` = UserId;
				
				IF SYSError = 1 THEN 
					ROLLBACK;
					SET ReturnValue = 'E_FINANCE_2';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','退回账户错');
					LEAVE Main;
				END IF;
			END IF;


-- -------------------------------------------------------------------------收回佣金，阅币等


			SELECT `val_delta` INTO TempCCoin FROM `yuemi_main`.`tally_coin` WHERE `order_id` = TempOrderId AND `source` = 'GIVE';
			IF SYSEmpty = 1 THEN 
				SET SYSEmpty = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无领赠送阅币信息');
			END IF;
			-- 更改账户
			IF TempCCoin > 0 THEN 
				UPDATE `yuemi_main`.`user_finance` 
				SET `coin` = `coin` - TempCCoin
				WHERE `user_id` = UserId;
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_FINANCE_1';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','退回账户错');
					LEAVE Main;
				END IF;
			END IF;

			IF TempCCoin > 0 THEN 
				INSERT INTO `yuemi_main`.`tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'BACK',TempOrderId ,OldCCoin,- TempCoin,OldCCoin - TempCoin,'',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY_D';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
				SET OldCCoin = OldCCoin - TempCoin;
			END IF;
	
			-- 收回佣金
			SELECT `status` INTO RebateStatus FROM `yuemi_sale`.`rebate` WHERE `order_id` = TempOrderId LIMIT 1 ;
			IF OStatus = 7 OR OStatus = 8 THEN -- 佣金已结算
				IF RebateStatus = 3 THEN 
					SELECT COUNT(*) INTO RebateN FROM `yuemi_sale`.`rebate` WHERE `order_id` = TempOrderId AND `status` = 3 ;
					SET J = 0;
					WHILE J < RebateN DO 
						SELECT `owner_id`,`share_user_id`,`self_profit`,`share_profit`,`item_id` 
						INTO OwnerId,ShareUserId,SelfProfit,ShareProfit,TempItemId
						FROM yuemi_sale.rebate 
						WHERE `order_id` = TempOrderId AND `status` = 3 ORDER BY item_id ASC LIMIT J,1 ;
						
						IF OwnerId <= 0 THEN 
							SET ReturnMessage = CONCAT(ReturnMessage,'->','无自买佣金');
						ELSE 
							SELECT `profit_self` INTO Profit_Old FROM `yuemi_main`.`user_finance` WHERE `user_id` = OwnerId;
							IF SYSEmpty = 1 THEN
								ROLLBACK;
								SET ReturnValue = 'E_FINANCE_1';
								SET ReturnMessage = CONCAT(ReturnMessage,'->','无账户');
								LEAVE Main;
							END IF;
							SET ReturnMessage = CONCAT(ReturnMessage,'->','原' , Profit_Old);
							SET Profit_New = Profit_Old - SelfProfit;
							SET ReturnMessage = CONCAT(ReturnMessage,'->','新' , Profit_New);
							UPDATE `yuemi_main`.`user_finance` SET `profit_self` = Profit_New WHERE `user_id` = OwnerId;
							IF SYSError = 1 THEN
								ROLLBACK;
								SET ReturnValue = 'E_FINANCE_1';
								SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错');
								LEAVE Main;
							END IF;
							INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
							VALUES (OwnerId,'SELF','CLOSE',TempOrderId,Profit_Old,-SelfProfit,Profit_New,'自买省',UNIX_TIMESTAMP(NOW()),ClientIp);
							IF SYSError = 1 THEN
								ROLLBACK;
								SET ReturnValue = 'E_TALLY';
								SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
								LEAVE Main;
							END IF;
						END IF;
						IF ShareUserId <= 0 THEN 
							SET ReturnMessage = CONCAT(ReturnMessage,'->','无分享佣金');
						ELSE 
							SELECT `profit_self` INTO Profit_Old FROM `yuemi_main`.`user_finance` WHERE `user_id` = ShareUserId;
							IF SYSEmpty = 1 THEN
								ROLLBACK;
								SET ReturnValue = 'E_FINANCE_2';
								SET ReturnMessage = CONCAT(ReturnMessage,'->','无账户');
								LEAVE Main;
							END IF;
							SET ReturnMessage = CONCAT(ReturnMessage,'->','原' , Profit_Old);
							SET Profit_New = Profit_Old - ShareProfit;
							SET ReturnMessage = CONCAT(ReturnMessage,'->','新' , Profit_New);
							UPDATE `yuemi_main`.`user_finance` SET `profit_share` = Profit_New WHERE `user_id` = ShareUserId;
							IF SYSError = 1 THEN
								ROLLBACK;
								SET ReturnValue = 'E_FINANCE_2';
								SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错');
								LEAVE Main;
							END IF;
							INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
							VALUES (UserId,'SHARE','BUY',OrderId,Profit_Old,-ShareProfit,Profit_New,'分享赚',UNIX_TIMESTAMP(NOW()),ClientIp);
							IF SYSError = 1 THEN
								ROLLBACK;
								SET ReturnValue = 'E_TALLY';
								SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
								LEAVE Main;
							END IF;
						END IF;


						SET J = J + 1 ;
					END WHILE;
				END IF;
			ELSEIF OStatus = 6 OR OStatus = 4 OR OStatus = 2 THEN 
				IF RebateStatus != 3 AND RebateStatus != 1 THEN -- 佣金未结算
					UPDATE `yuemi_sale`.`rebate` SET `status` = 1 WHERE `order_id` = TempOrderId;
					IF SYSError = 1 THEN
						ROLLBACK;
						SET ReturnValue = 'E_REBATE';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','更新rebate失败');
						LEAVE Main;
					END IF;
				END IF;
			END IF;
			


			-- 更改库存
			SELECT COUNT(`id`) INTO Oint FROM `yuemi_sale`.`order_item` WHERE `order_id` = TempOrderId;
			SET M = 0;
			WHILE M < Oint DO 
				SELECT `qty`,`sku_id` INTO ItemQty,TempSkuId FROM `yuemi_sale`.`order_item` WHERE `order_id` = TempOrderId ORDER BY `id` ASC LIMIT M,1;

				SET SYSError = 0;

				UPDATE `yuemi_sale`.`sku` 
				SET `depot` = `depot` + ItemQty 
				WHERE `id` = TempSkuId;

				IF SYSError = 1 THEN 
					ROLLBACK;
					SET ReturnValue = 'E_SKU';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','更新库存失败');
					LEAVE Main;
				END IF;
				SET M = M + 1;
			END WHILE;

			-- 更改状态
			IF OStatus = 0 OR OStatus = 1 THEN 
				UPDATE `yuemi_sale`.`order` 
				SET `status` = 12 
				WHERE `id` = TempOrderId;
			ELSEIF OStatus = 2 OR OStatus = 4 OR OStatus = 6 OR OStatus = 7 THEN 
				UPDATE `yuemi_sale`.`order` 
				SET `status` = 14  
				WHERE `id` = TempOrderId;
			END IF;
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_UP';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更改状态错');
				LEAVE Main;
			END IF;

			SET I = I + 1 ;
		END WHILE ;

	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
DELIMITER ;