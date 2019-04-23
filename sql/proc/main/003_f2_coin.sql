/*
	基本财务相关存储过程
*/
DELIMITER |||

DROP PROCEDURE IF EXISTS `coin_income` |||
CREATE PROCEDURE `coin_income` (
	IN UserId INT UNSIGNED,		-- 用户ID
	IN Coin_Dlt NUMERIC(16,8),		-- 增加额度
	IN SrcType VARCHAR(16),		-- 原因
	IN SrcId VARCHAR(24),		-- 关联ID
	IN Message VARCHAR(128),		-- 备注
	IN ClientIp BIGINT UNSIGNED,-- IP

	OUT TallyId INT UNSIGNED,
	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '阅币收入'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	-- 声明局部变量
	DECLARE Coin_Old NUMERIC(16,8);
	DECLARE Coin_New NUMERIC(16,8);

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '阅币收入';
	
	IF Coin_Dlt = 0 THEN
		SET ReturnValue = 'OK';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
		LEAVE Main;
	END IF;
	IF Coin_Dlt < 0 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','加负数');
		LEAVE Main;
	END IF;
	IF UserId < 1 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','UserId错');
		LEAVE Main;
	END IF;
	IF LENGTH(SrcType) < 1 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','缺少事由');
		LEAVE Main;
	END IF;
	
	-- 开启事务
	START TRANSACTION;
		SELECT `coin` INTO Coin_Old FROM `user_finance` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无账户');
			LEAVE Main;
		END IF;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','原' , Coin_Old);
		SET Coin_New = Coin_Old + Coin_Dlt;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','新' , Coin_New);
		UPDATE `user_finance` SET `coin` = Coin_New WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错');
			LEAVE Main;
		END IF;
		INSERT INTO `tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
		VALUES (UserId,SrcType,SrcId,Coin_Old,Coin_Dlt,Coin_New,Message,UNIX_TIMESTAMP(NOW()),ClientIp);
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_TALLY';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
			LEAVE Main;
		END IF;
		SET TallyId = LAST_INSERT_ID();

		UPDATE `user_finance` 
		SET `coin` = Coin_New  
		WHERE `user_id` = UserId;
		
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_F';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更新错');
			LEAVE Main;
		END IF;

	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||


DROP PROCEDURE IF EXISTS `coin_expend` |||
CREATE PROCEDURE `coin_expend` (
	IN UserId INT UNSIGNED,		-- 用户ID
	IN Coin_Dlt NUMERIC(16,8),		-- 增加额度
	IN SrcType VARCHAR(16),		-- 原因
	IN SrcId VARCHAR(24),		-- 关联ID
	IN Message VARCHAR(128),		-- 备注
	IN ClientIp BIGINT UNSIGNED,-- IP

	OUT TallyId INT UNSIGNED,
	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '阅币支出'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	-- 声明局部变量
	DECLARE Coin_Old NUMERIC(16,8);
	DECLARE Coin_New NUMERIC(16,8);

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '阅币支出';
	
	IF Coin_Dlt = 0 THEN
		SET ReturnValue = 'OK';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
		LEAVE Main;
	END IF;
	IF Coin_Dlt < 0 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','减负数');
		LEAVE Main;
	END IF;
	IF UserId < 1 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','UserId错');
		LEAVE Main;
	END IF;
	IF LENGTH(SrcType) < 1 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','缺少事由');
		LEAVE Main;
	END IF;
	
	-- 开启事务
	START TRANSACTION;
		SELECT `money` INTO Coin_Old FROM `user_finance` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无账户');
			LEAVE Main;
		END IF;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','原' , Coin_Old);
		SET Coin_New = Coin_Old - Coin_Dlt;
		IF Coin_New < 0 THEN
			ROLLBACK;
			SET ReturnValue = 'E_COIN';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','阅币负');
			LEAVE Main;
		END IF;
		INSERT INTO `tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
		VALUES (UserId,SrcType,SrcId,Coin_Old,-Coin_Dlt,Coin_New,Message,UNIX_TIMESTAMP(NOW()),ClientIp);
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_TALLY';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
			LEAVE Main;
		END IF;
		SET TallyId = LAST_INSERT_ID();

		UPDATE `user_finance` 
		SET `coin` = Coin_New  
		WHERE `user_id` = UserId;
		
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_F';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更新错');
			LEAVE Main;
		END IF;

	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||

