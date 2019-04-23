/**
 * 功能 : 用户返佣
 * 作者：王少宏
 * 日期：2018-05-17
 * 修订@2018-05-17	: 
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `yuemi_sale`.`commission` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `commission`(0
	IN OrderId			VARCHAR(16),    /*订单ID */
	IN UserId			INT UNSIGNED,	/* 用户ID */
	IN ClientIp			BIGINT UNSIGNED,	/*创建IP*/

	OUT ReturnValue		VARCHAR(32),    /*返回状态值 */
	OUT ReturnMessage	VARCHAR(1024) 	/*返回提示信息 */
)
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT '计算返佣'
Main : BEGIN		/*主体内容开始*/
	/*定义状态*/
	DECLARE SYSError	INT DEFAULT 0;
	DECLARE SYSEmpty	INT DEFAULT 0;

	/*局部变量*/
	DECLARE RebateSelfPro		NUMERIC(16,4)	DEFAULT 0.0; -- 返佣账户
	DECLARE RebateSharePro		NUMERIC(16,4)	DEFAULT 0.0; -- 返佣账户
	DECLARE RebateUserId		INT UNSIGNED	DEFAULT 0; -- 返佣账户
	DECLARE RebateShareUserId	INT UNSIGNED	DEFAULT 0; -- 返佣账户

	DECLARE Profit_Old			NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE Profit_New			NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE UserProfitSelf		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE UserProfitShare		NUMERIC(16,4)	DEFAULT 0.0;

	DECLARE RebatePersonId		INT UNSIGNED	DEFAULT 0; 
	DECLARE RebatePersonPro		NUMERIC(16,4)	DEFAULT 0.0;

	DECLARE RebatePersonProPre	NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE RebateModel			VARCHAR(32)		DEFAULT '';

	/* 声明异常处理（定义系统错误），必须放在变量定义之后*/
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	/* 初始化变量*/
	SET ReturnValue = '';
	SET ReturnMessage = '计算返佣';

	START TRANSACTION;
		-- 查询所有返佣以及返佣对象
		SELECT SUM(`self_profit`),SUM(`share_profit`),`buyer_id`,`share_user_id` INTO RebateSelfPro,RebateSharePro,RebateUserId,RebateShareUserId 
		FROM `yuemi_sale`.`rebate` WHERE `order_id` = OrderId;
		IF RebateSelfPro != 0 THEN -- 自买
			SET RebatePersonId = RebateUserId;
			SET RebatePersonPro = RebateSelfPro;
			SET RebateModel = 'SELF';

			SELECT `profit_self` INTO RebatePersonProPre FROM `yuemi_main`.`user_finance` WHERE `user_id` = RebatePersonId;
			UPDATE `yuemi_main`.`user_finance`  SET `profit_self` = `profit_self` + RebatePersonPro   WHERE `user_id` = RebatePersonId;

			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_F_SELF';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','账号错');
				LEAVE Main;
			END IF;

		ELSEIF RebateSharePro != 0 THEN -- 分享
			SET RebatePersonId = RebateShareUserId;
			SET RebatePersonPro = RebateSharePro;
			SET RebateModel = 'SHARE';
			-- 写入流水以及账户	
			SELECT `profit_share` INTO RebatePersonProPre FROM `yuemi_main`.`user_finance` WHERE `user_id` = RebatePersonId;
			UPDATE `yuemi_main`.`user_finance`  SET `profit_share` = `profit_share` + RebatePersonPro   WHERE `user_id` = RebatePersonId;
			
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_F_SHARE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','账号错');
				LEAVE Main;
			END IF;
		END IF;
		
		INSERT INTO `yuemi_main`.`tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
		VALUES (RebatePersonId,RebateModel,'BUY',OrderId,RebatePersonProPre,RebatePersonPro,RebatePersonProPre + RebatePersonPro,'',NOW(),ClientIp);

		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_TALLY';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
			LEAVE Main;
		END IF;
		UPDATE `yuemi_sale`.`rebate` SET `status` = 3 WHERE `order_id` = OrderId;
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_REBATE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','rebate错');
			LEAVE Main;
		END IF;

	COMMIT;
 	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
DELIMITER ;