/**
 * 功能 : 确认订单
 * 作者 : 王少宏 2018.05.30
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `order_accept` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `order_accept`(
	IN OrderId			VARCHAR(16),    /*返回主订单ID */
	IN ClientIp			BIGINT UNSIGNED,/*创建IP*/
	
	OUT ReturnValue		VARCHAR(32),    /*返回状态值 */
	OUT ReturnMessage	VARCHAR(1024) 	/*返回提示信息 */
)

    MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT '确认订单'
Main : BEGIN		/*主体内容开始*/
	
	/*定义状态*/
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE OStatus			TINYINT UNSIGNED	DEFAULT 0; -- 订单状态
	DECLARE RebateN			INT UNSIGNED		DEFAULT 0; -- 该订单下的rebate数据个数

	/* 声明异常处理（定义系统错误），必须放在变量定义之后*/
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;

	SET ReturnValue = '';
	SET ReturnMessage = '确认订单';
	
	START TRANSACTION;
		SELECT `status` INTO OStatus FROM `order` WHERE `id` = OrderId LIMIT 1;
		IF OStatus != 6 OR SYSEmpty = 1 THEN 
			SET ReturnValue = 'E_STATUS';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','未签收');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `order` SET `status` = 7 WHERE `id` = OrderId;
		IF SYSError = 1 THEN 
			SET ReturnValue = 'E_UP_1';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更改失败');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SELECT COUNT(*) INTO RebateN FROM `rebate` WHERE `order_id` = OrderId;
		IF RebateN = 6 OR SYSEmpty = 1 THEN 
			SET ReturnValue = 'E_REBATE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无返佣记录');
			LEAVE Main;
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','有返佣记录');
			UPDATE `rebate` SET  `status` = 2 ,`time_finish` = UNIX_TIMESTAMP(NOW()) WHERE `order_id` = OrderId;
			IF SYSError = 1 THEN 
				SET ReturnValue = 'E_UP_2';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更改失败');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');

END |||
DELIMITER ; /*定义结束符*/


