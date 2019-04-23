/*
	放弃提现
*/
DELIMITER |||

DROP PROCEDURE IF EXISTS `withdraw_cancel` |||
CREATE PROCEDURE `withdraw_cancel` (
	IN WithdrawId INT UNSIGNED,			-- 申请号
	IN IsDeny	 TINYINT UNSIGNED,		-- 是否后台拒绝
	IN ClientIp BIGINT UNSIGNED,	-- IP

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '取消提现'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	-- 声明局部变量
	DECLARE UF_Money		NUMERIC(16,4) DEFAULT 0.0;
	DECLARE UF_ProfitTotal	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE UF_ProfitSelf	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE UF_ProfitShare	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE UF_ProfitTeam	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE UF_RecruitTotal	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE UF_RecruitDir	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE UF_RecruitAlt	NUMERIC(16,4) DEFAULT 0.0;

	DECLARE DT_Money		NUMERIC(16,4) DEFAULT 0.0;
	DECLARE DT_ProfitSelf	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE DT_ProfitShare	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE DT_ProfitTeam	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE DT_RecruitDir	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE DT_RecruitAlt	NUMERIC(16,4) DEFAULT 0.0;

	DECLARE AF_Money		NUMERIC(16,4) DEFAULT 0.0;
	DECLARE AF_ProfitSelf	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE AF_ProfitShare	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE AF_ProfitTeam	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE AF_RecruitDir	NUMERIC(16,4) DEFAULT 0.0;
	DECLARE AF_RecruitAlt	NUMERIC(16,4) DEFAULT 0.0;

	DECLARE TmpVal			NUMERIC(16,4) DEFAULT 0.0;

	DECLARE WD_Status		TINYINT UNSIGNED DEFAULT 0;
	DECLARE TID_Money		INT UNSIGNED DEFAULT 0;
	DECLARE TID_ProfitSelf	INT UNSIGNED DEFAULT 0;
	DECLARE TID_ProfitShare	INT UNSIGNED DEFAULT 0;
	DECLARE TID_ProfitTeam	INT UNSIGNED DEFAULT 0;
	DECLARE TID_RecruitDir	INT UNSIGNED DEFAULT 0;
	DECLARE TID_RecruitAlt	INT UNSIGNED DEFAULT 0;
	DECLARE WD_OrderId		VARCHAR(12) DEFAULT '';
	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '取消提现';
	
	SET SYSEmpty = 0;
	SET SYSError = 0;
	-- 开启事务
	START TRANSACTION;
		SELECT `money`,`profit_self`,`profit_share`,`profit_team`,`recruit_dir`,`recruit_alt`
		INTO UF_Money,UF_ProfitSelf,UF_ProfitShare,UF_ProfitTeam,UF_RecruitDir,UF_RecruitAlt
		FROM `user_finance`
		WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SELECT `money`,`profit_self`,`profit_share`,`profit_team`,`recruit_dir`,`recruit_alt`,`status`,`order_id`
		INTO DT_Money,DT_ProfitSelf,DT_ProfitShare,DT_ProfitTeam,DT_RecruitDir,DT_RecruitAlt,WD_Status,WD_OrderId
		FROM `user_withdraw`
		WHERE `id` = WithdrawId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_WITHDRAW';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无记录');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF WD_Status != 0 THEN
			SET ReturnValue = 'E_STATUS';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','状态错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		-- 准备财务更新数据
		SET AF_Money		= UF_Money			+ DT_Money;
		SET AF_ProfitSelf	= UF_ProfitSelf		+ DT_ProfitSelf;
		SET AF_ProfitShare	= UF_ProfitShare	+ DT_ProfitShare;
		SET AF_ProfitTeam	= UF_ProfitTeam		+ DT_ProfitTeam;
		SET AF_RecruitDir	= UF_RecruitDir		+ DT_RecruitDir;
		SET AF_RecruitAlt	= UF_RecruitAlt		+ DT_RecruitAlt;
		
		-- 开始退钱
		SET SYSEmpty = 0;
		IF DT_Money > 0 THEN
			SELECT `id`,`money` INTO TID_Money,TmpVal FROM `tally_money` WHERE `user_id` = UserId AND `order_id` = WD_OrderId;
			IF SYSEmpty = 1 OR TID_Money <= 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无凭条#1');
				ROLLBACK;
				LEAVE Main;
			END IF;
			IF TmpVal + DT_Money != 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','不平衡#1');
				ROLLBACK;
				LEAVE Main;
			END IF;
			INSERT INTO `tally_money` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'WITHDRAW',WD_OrderId,UF_Money,DT_Money,AF_Momey,'提现返还',NOW(),ClientIp);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','返回错#1');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		IF DT_ProfitSelf > 0 THEN
			SELECT `id`,`profit_self` INTO TID_ProfitSelf,TmpVal FROM `tally_profit` WHERE `user_id` = UserId AND `order_id` = WD_OrderId;
			IF SYSEmpty = 1 OR TID_ProfitSelf <= 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无凭条#2');
				ROLLBACK;
				LEAVE Main;
			END IF;
			IF TmpVal + DT_ProfitSelf != 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','不平衡#2');
				ROLLBACK;
				LEAVE Main;
			END IF;
			INSERT INTO `tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'SELF','WITHDRAW',WD_OrderId,UF_Money,DT_ProfitSelf,AF_Momey,'提现返还',NOW(),ClientIp);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','返回错#2');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		IF DT_ProfitShare > 0 THEN
			SELECT `id`,`profit_self` INTO TID_ProfitShare,TmpVal FROM `tally_profit` WHERE `user_id` = UserId AND `order_id` = WD_OrderId;
			IF SYSEmpty = 1 OR TID_ProfitShare <= 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无凭条#3');
				ROLLBACK;
				LEAVE Main;
			END IF;
			IF TmpVal + DT_ProfitShare != 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','不平衡#3');
				ROLLBACK;
				LEAVE Main;
			END IF;
			INSERT INTO `tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'SHARE','WITHDRAW',WD_OrderId,UF_Money,DT_ProfitShare,AF_Momey,'提现返还',NOW(),ClientIp);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','返回错#3');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		IF DT_ProfitTeam > 0 THEN
			SELECT `id`,`profit_self` INTO TID_ProfitTeam,TmpVal FROM `tally_profit` WHERE `user_id` = UserId AND `order_id` = WD_OrderId;
			IF SYSEmpty = 1 OR TID_ProfitTeam <= 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无凭条#4');
				ROLLBACK;
				LEAVE Main;
			END IF;
			IF TmpVal + DT_ProfitTeam != 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','不平衡#4');
				ROLLBACK;
				LEAVE Main;
			END IF;
			INSERT INTO `tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'TEAM','WITHDRAW',WD_OrderId,UF_Money,DT_ProfitTeam,AF_Momey,'提现返还',NOW(),ClientIp);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','返回错#4');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		IF DT_RecruitDir > 0 THEN
			SELECT `id`,`recruit_self` INTO TID_RecruitDir,TmpVal FROM `tally_recruit` WHERE `user_id` = UserId AND `order_id` = WD_OrderId;
			IF SYSEmpty = 1 OR TID_RecruitDir <= 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无凭条#5');
				ROLLBACK;
				LEAVE Main;
			END IF;
			IF TmpVal + DT_RecruitDir != 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','不平衡#5');
				ROLLBACK;
				LEAVE Main;
			END IF;
			INSERT INTO `tally_recruit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'DIR','WITHDRAW',WD_OrderId,UF_Money,DT_RecruitDir,AF_Momey,'提现返还',NOW(),ClientIp);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','返回错#5');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		IF DT_RecruitAlt > 0 THEN
			SELECT `id`,`recruit_self` INTO TID_RecruitAlt,TmpVal FROM `tally_recruit` WHERE `user_id` = UserId AND `order_id` = WD_OrderId;
			IF SYSEmpty = 1 OR TID_RecruitAlt <= 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无凭条#6');
				ROLLBACK;
				LEAVE Main;
			END IF;
			IF TmpVal + DT_RecruitAlt != 0 THEN
				-- TODO: 添加报警日志
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','不平衡#6');
				ROLLBACK;
				LEAVE Main;
			END IF;
			INSERT INTO `tally_recruit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'ALT','WITHDRAW',WD_OrderId,UF_Money,DT_RecruitAlt,AF_Momey,'提现返还',NOW(),ClientIp);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','返回错#6');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;

		UPDATE `user_finance` SET
			`money`			= AF_Money,
			`profit_self`	= AF_ProfitSelf,
			`profit_share`	= AF_ProfitShare,
			`profit_team`	= AF_ProfitTeam,
			`recruit_dir`	= AF_RecruitDir,
			`recruit_alt`	= AF_RecruitAlt
		WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','返还错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF IsDeny > 0 THEN
			UPDATE `user_withdraw` SET `audit_time` = UNIX_TIMESTAMP() , `status` = 4 WHERE `id` = WithdrawId;
		ELSE
			UPDATE `user_withdraw` SET `status` = 5 WHERE `id` = WithdrawId;
		END IF;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','记录错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SET WithdrawId = LAST_INSERT_ID();
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
