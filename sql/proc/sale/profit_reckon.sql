/**
 * 功能 : 佣金计算返现
 * 作者：王少宏
 * 日期：2018-05-29
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `yuemi_sale`.`profit_reckon` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `profit_reckon`(
	IN OrderId			VARCHAR(16),    /*返回主订单ID */
	IN ClientIp			BIGINT UNSIGNED,/*创建IP*/

	OUT ReturnValue		VARCHAR(32),    /*返回状态值 */
	OUT ReturnMessage	VARCHAR(1024) 	/*返回提示信息 */
)
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT '佣金计算返现'
Main : BEGIN		/*主体内容开始*/
	/*定义状态*/
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	/*局部变量*/
	/*局部变量*/
	DECLARE RebateN				INT UNSIGNED		DEFAULT 0; -- 该订单下的rebate数据个数
	DECLARE I					INT UNSIGNED		DEFAULT 0; -- 循环参数
	DECLARE TempItemId			INT UNSIGNED		DEFAULT 0; -- item id
	DECLARE OwnerId				INT UNSIGNED		DEFAULT 0; -- 自买佣金归属
	DECLARE ShareUserId			INT UNSIGNED		DEFAULT 0; -- 分享人
	DECLARE SelfProfit			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE ShareProfit			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Profit_Old			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE Profit_New			NUMERIC(16,4)		DEFAULT 0.0;

	/* 声明异常处理（定义系统错误），必须放在变量定义之后*/
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	/* 初始化变量*/
	SET ReturnValue = '';
	SET ReturnMessage = '返佣';
	/* 检查参数 */

	SELECT COUNT(*) INTO RebateN FROM yuemi_sale.rebate WHERE `order_id` = OrderId AND `status` = 2;
	IF SYSEmpty = 1 OR RebateN = 0 THEN 
		SET ReturnValue = 'E_REBATE';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无记录');
		LEAVE Main;
	END IF;

	START TRANSACTION;
		SET I = 0 ;
		WHILE I < RebateN DO 
			SELECT `owner_id`,`share_user_id`,`self_profit`,`share_profit`,`item_id` 
			INTO OwnerId,ShareUserId,SelfProfit,ShareProfit,TempItemId
			FROM yuemi_sale.rebate 
			WHERE `order_id` = OrderId AND `status` = 2 ORDER BY item_id ASC LIMIT I,1 ;
			
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
				SET Profit_New = Profit_Old + SelfProfit;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新' , Profit_New);
				UPDATE `yuemi_main`.`user_finance` SET `profit_self` = Profit_New WHERE `user_id` = OwnerId;
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_FINANCE_1';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错');
					LEAVE Main;
				END IF;
				INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (OwnerId,'SELF','BUY',OrderId,Profit_Old,SelfProfit,Profit_New,'自买省',UNIX_TIMESTAMP(NOW()),ClientIp);
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
				SET Profit_New = Profit_Old + ShareProfit;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新' , Profit_New);
				UPDATE `yuemi_main`.`user_finance` SET `profit_share` = Profit_New WHERE `user_id` = ShareUserId;
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_FINANCE_2';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错');
					LEAVE Main;
				END IF;
				INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
				VALUES (UserId,'SHARE','BUY',OrderId,Profit_Old,ShareProfit,Profit_New,'分享赚',UNIX_TIMESTAMP(NOW()),ClientIp);
				IF SYSError = 1 THEN
					ROLLBACK;
					SET ReturnValue = 'E_TALLY';
					SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
					LEAVE Main;
				END IF;
			END IF;
			
			UPDATE `yuemi_sale`.`rebate` SET `status` = 3 WHERE `item_id` = TempItemId;
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_REBATE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
				LEAVE Main;
			END IF;

			SET I = I+ 1 ;
		END WHILE;
	COMMIT;

	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
DELIMITER ;