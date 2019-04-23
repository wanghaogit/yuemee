/**
 * 功能 : 取消订单 -- 供应商取消
 * 作者：王少宏
 * 日期：2018-4-25
 * 修订@2018-04-25	: 
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `yuemi_sale`.`supplier_close_order` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `supplier_close_order`(
	IN	OrderId			VARCHAR(16),	 /*主订单ID */
	IN	UserId			INT,			 /*用户ID */
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

	DECLARE OStatus				INT DEFAULT 0;
	DECLARE Ont					INT DEFAULT 0;
	DECLARE I					INT DEFAULT 0;
	DECLARE TempOrderId			VARCHAR(16)		DEFAULT 0;
	DECLARE TempCMoney			NUMERIC(16,4)	DEFAULT 0.0;	
	DECLARE TempCProfitSelf		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE TempCProfitShare	NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE TempCRecruitDir		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE TempCPayOnline		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OldCMoney			NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OldCMoneyM			NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OldCProfitSelf		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OldCProfitShare		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OldCRecruitDir		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE RebateN				INT DEFAULT 0;
	DECLARE N					INT DEFAULT 0;

	/* 声明异常处理（定义系统错误），必须放在变量定义之后*/
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	/* 初始化变量*/
	SET ReturnValue = '';
	SET ReturnMessage = '快捷下单';
	
	-- 检查订单
	SELECT `status` INTO OStatus FROM `yuemi_sale`.`order` WHERE `id` = OrderId AND `user_id` = UserId LIMIT 0,1;
	IF SYSEmpty = 1 THEN 
		SET ReturnValue = 'E_ORDER';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无订单');
		LEAVE Main;
	END IF;
	
	IF OStatus != 4 THEN 
		SET ReturnValue = 'E_ORDER';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','状态不为4');
		LEAVE Main;
	END IF;
	SELECT count(*) INTO N FROM `yuemi_main`.`tally_coin` WHERE `order_id` = OrderId;
	IF SYSEmpty = 0 AND N > 0 THEN 
		SET ReturnValue = 'E_ORDER';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','有阅币赠送');
		LEAVE Main;
	END IF;
	SET SYSEmpty = 0;
	-- 检查账户
	SELECT `money`,`profit_self`,`profit_share`,`recruit_dir` INTO OldCMoney,OldCProfitSelf,OldCProfitShare,OldCRecruitDir FROM `yuemi_main`.`user_finance`WHERE `user_id` = UserId;
	IF SYSEmpty = 1 THEN
		SET ReturnValue = 'E_F_P';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错误');
		LEAVE Main;
	END IF;

	SELECT COUNT(`id`) INTO Ont FROM `yuemi_sale`.`order` WHERE `depend_id` = OrderId AND `user_id` = UserId;
	START TRANSACTION;
		-- 对所有的订单进行循环
		WHILE I < Ont DO 
			-- 查到订单的ID
			SELECT `id`,`c_online` INTO TempOrderId,TempCPayOnline FROM `yuemi_sale`.`order` WHERE `depend_id` = OrderId AND `user_id` = UserId ORDER BY `id` ASC LIMIT I,1;
			-- 从流水中取出应该退的钱
			SELECT `val_delta` INTO TempCMoney FROM	`yuemi_main`.`tally_money` WHERE `order_id` = TempOrderId AND `source` = 'BUY';
			IF SYSEmpty = 1 THEN 
				SET SYSEmpty = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无余额信息');
			END IF;

			SELECT `val_delta` INTO TempCProfitSelf FROM `yuemi_main`.`tally_profit` WHERE `order_id` = TempOrderId AND `source` = 'BUY' AND `target` = 'SELF';
			IF SYSEmpty = 1 THEN 
				SET SYSEmpty = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无自省佣金信息');
			END IF;
			
			SELECT `val_delta` INTO TempCProfitShare FROM `yuemi_main`.`tally_profit` WHERE `order_id` = TempOrderId AND `source` = 'BUY' AND `target` = 'SHARE';
			IF SYSEmpty = 1 THEN 
				SET SYSEmpty = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无分享佣金信息');
			END IF;

			SELECT `val_delta` INTO TempCRecruitDir FROM `yuemi_main`.`tally_recruit` WHERE `order_id` = TempOrderId AND `source` = 'BUY' AND `target` = 'DIR';
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

			-- 将c_online 加到账户
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

			-- 更改状态 供应商关闭
			
			UPDATE `yuemi_sale`.`order` 
			SET `status` = 15 
			WHERE `id` = TempOrderId;

			SELECT COUNT(item_id) INTO RebateN FROM `yuemi_sale`.`rebate` WHERE `order_id` = TempOrderId;
			IF SYSEmpty = 1 OR RebateN = 0 THEN 
				SET SYSEmpty = 0;
			ELSEIF RebateN > 0 THEN 
				DELETE FROM `yuemi_sale`.`rebate` WHERE `order_id` = TempOrderId;
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_REBATE';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','删除rebate失败');
					LEAVE Main;
				END IF;
			END IF;
			SET I = I + 1 ;
		END WHILE ;

	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
DELIMITER ;