/*
	基本财务相关存储过程
*/
DELIMITER |||

DROP PROCEDURE IF EXISTS `profit_team_income` |||
CREATE PROCEDURE `profit_team_income` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN Profit_Dlt NUMERIC(16,4),	-- 增加额度
	IN SrcType VARCHAR(16),			-- 原因
	IN SrcId VARCHAR(24),			-- 关联ID
	IN Message VARCHAR(128),		-- 备注
	IN ClientIp BIGINT UNSIGNED,	-- IP

	OUT TallyId INT UNSIGNED,
	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '团队佣金收入'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	-- 声明局部变量
	DECLARE Profit_Old NUMERIC(16,4);
	DECLARE Profit_New NUMERIC(16,4);

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '佣金收入->团队';
	
	IF Profit_Dlt = 0 THEN
		SET ReturnValue = 'OK';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
		LEAVE Main;
	END IF;
	IF Profit_Dlt < 0 THEN
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
		SELECT `profit_team` INTO Profit_Old FROM `user_finance` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无账户');
			LEAVE Main;
		END IF;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','原' , Profit_Old);
		SET Profit_New = Profit_Old + Profit_Dlt;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','新' , Profit_New);
		UPDATE `user_finance` SET `profit_team` = Profit_New WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错');
			LEAVE Main;
		END IF;
		INSERT INTO `tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
		VALUES (UserId,'TEAM',SrcType,SrcId,Profit_Old,Profit_Dlt,Profit_New,Message,UNIX_TIMESTAMP(NOW()),ClientIp);
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_TALLY';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
			LEAVE Main;
		END IF;
		SET TallyId = LAST_INSERT_ID();
		
		UPDATE `user_finance` 
		SET `profit_team` = Profit_New  
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


DROP PROCEDURE IF EXISTS `profit_team_expend` |||
CREATE PROCEDURE `profit_team_expend` (
	IN UserId INT UNSIGNED,		-- 用户ID
	IN Profit_Dlt NUMERIC(16,4),		-- 增加额度
	IN SrcType VARCHAR(16),		-- 原因
	IN SrcId VARCHAR(24),		-- 关联ID
	IN Message VARCHAR(128),		-- 备注
	IN ClientIp BIGINT UNSIGNED,-- IP

	OUT TallyId INT UNSIGNED,
	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '团队佣金支出'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	-- 声明局部变量
	DECLARE Profit_Old NUMERIC(16,4);
	DECLARE Profit_New NUMERIC(16,4);

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '团队佣金支出';
	
	IF Profit_Dlt = 0 THEN
		SET ReturnValue = 'OK';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
		LEAVE Main;
	END IF;
	IF Profit_Dlt < 0 THEN
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
		SELECT `profit_team` INTO Profit_Old FROM `user_finance` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无账户');
			LEAVE Main;
		END IF;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','原' , Profit_Old);
		SET Profit_New = Profit_Old - Profit_Dlt;
		IF Profit_New < 0 THEN
			ROLLBACK;
			SET ReturnValue = 'E_PROFIT';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','佣金负');
			LEAVE Main;
		END IF;
		INSERT INTO `tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
		VALUES (UserId,'TEAM',SrcType,SrcId,Profit_Old,-Profit_Dlt,Profit_New,Message,UNIX_TIMESTAMP(NOW()),ClientIp);
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_TALLY';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
			LEAVE Main;
		END IF;
		SET TallyId = LAST_INSERT_ID();
		
		UPDATE `user_finance` 
		SET `profit_team` = Profit_New  
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

