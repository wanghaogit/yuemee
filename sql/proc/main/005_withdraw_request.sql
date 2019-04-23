/*
	提现申请
*/
DELIMITER |||

DROP PROCEDURE IF EXISTS `withdraw_request` |||
CREATE PROCEDURE `withdraw_request` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN ReqMoney NUMERIC(16,4),		-- 余额
	IN ReqProfit NUMERIC(16,4),		-- 销售佣金
	IN ReqRecruit NUMERIC(16,4),	-- 招聘佣金
	IN ReqBankId INT UNSIGNED,		-- 银行ID
	IN ClientIp BIGINT UNSIGNED,	-- IP

	OUT WithdrawId INT UNSIGNED,
	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '申请提现'
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

	DECLARE UF_ThewFlag		TINYINT UNSIGNED DEFAULT 0;
	DECLARE UF_ThewTime		BIGINT UNSIGNED DEFAULT 0;

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

	DECLARE BK_Bank			SMALLINT UNSIGNED DEFAULT 0;
	DECLARE BK_Name			VARCHAR(32) DEFAULT '';
	DECLARE BK_Serial		VARCHAR(256) DEFAULT '';
	DECLARE BK_Region		INT UNSIGNED DEFAULT 0;

	DECLARE TID_Money		INT UNSIGNED DEFAULT 0;
	DECLARE TID_ProfitSelf	INT UNSIGNED DEFAULT 0;
	DECLARE TID_ProfitShare	INT UNSIGNED DEFAULT 0;
	DECLARE TID_ProfitTeam	INT UNSIGNED DEFAULT 0;
	DECLARE TID_RecruitDir	INT UNSIGNED DEFAULT 0;
	DECLARE TID_RecruitAlt	INT UNSIGNED DEFAULT 0;
	DECLARE WD_OrderId		VARCHAR(12) DEFAULT '';
	DECLARE TmpId			INT UNSIGNED DEFAULT 0;
	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET WithdrawId = 0;
	SET ReturnValue = '';
	SET ReturnMessage = '申请提现';
	
	-- 检查参数
	IF ReqMoney < 0 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','参数错#1');
		LEAVE Main;
	END IF;
	IF ReqProfit < 0 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','参数错#2');
		LEAVE Main;
	END IF;
	IF ReqRecruit < 0 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','参数错#3');
		LEAVE Main;
	END IF;
	IF ReqMoney = 0 AND ReqProfit = 0 AND ReqRecruit = 0 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无操作');
		LEAVE Main;
	END IF;
	-- 检查实名
	SET SYSEmpty = 0;
	SET SYSError = 0;
	SELECT `card_name` INTO BK_Name FROM `user_cert` WHERE `user_id` = UserId AND `status` = 2;
	IF SYSEmpty = 1 THEN
		SET ReturnValue = 'E_CERT';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','未实名');
		LEAVE Main;
	END IF;
	SELECT `bank_id`,`card_no`,`region_id`
	INTO BK_Bank,BK_Serial,BK_Region
	FROM `user_bank`
	WHERE `user_id` = UserId AND (`status` = 1 OR `status` = 2);
	IF SYSEmpty = 1 THEN
		SET ReturnValue = 'E_CERT';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','卡片错');
		LEAVE Main;
	END IF;
	SELECT `id` INTO TmpId FROM `user_withdraw` WHERE `user_id` = UserId AND `status` IN (0,1,2);
	IF SYSEmpty = 1 THEN
		SET SYSEmpty = 0;
	ELSE
		SET ReturnValue = 'E_BUSY';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','重复提交');
		LEAVE Main;
	END IF;
	-- 开启事务
	START TRANSACTION;
		SELECT `money`,`profit_self`,`profit_share`,`profit_team`,`recruit_dir`,`recruit_alt`,`thew_status`,`thew_time`
		INTO UF_Money,UF_ProfitSelf,UF_ProfitShare,UF_ProfitTeam,UF_RecruitDir,UF_RecruitAlt,UF_ThewFlag,UF_ThewTime
		FROM `user_finance`
		WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF ReqRecruit > 0 AND UF_ThewFlag = 0 THEN
			SET ReturnValue = 'E_LOCK';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','未解锁');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF ReqRecruit > 0 AND UF_ThewTime < UNIX_TIMESTAMP() - 31536000 THEN
			UPDATE `user_finance` SET `thew_status` = 0 WHERE `user_id` = UserId;
			SET ReturnValue = 'E_LOCK';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','未解锁');
			COMMIT;
			LEAVE Main;
		END IF;
		SET UF_ProfitTotal = UF_ProfitSelf + UF_ProfitShare + UF_ProfitTeam;
		SET UF_RecruitTotal = UF_RecruitDir + UF_RecruitAlt;
		IF ReqMoney > 0 AND ReqMoney > UF_Money THEN
			SET ReturnValue = 'E_MONEY';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','余额不足');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF ReqProfit > 0 AND ReqProfit > UF_ProfitTotal THEN
			SET ReturnValue = 'E_PROFIT';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','佣金不足');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF ReqRecruit > 0 AND ReqRecruit > UF_RecruitTotal THEN
			SET ReturnValue = 'E_RECRUIT';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','招聘费不足');
			ROLLBACK;
			LEAVE Main;
		END IF;
		-- 准备财务更新数据
		SET AF_Money = UF_Money;
		SET AF_ProfitSelf = UF_ProfitSelf;
		SET AF_ProfitShare = UF_ProfitShare;
		SET AF_ProfitTeam = UF_ProfitTeam;
		SET AF_RecruitDir = UF_RecruitDir;
		SET AF_RecruitAlt = UF_RecruitAlt;

		-- 处理余额
		IF ReqMoney > 0 THEN
			SET DT_Money = ReqMoney;
			SET AF_Money = UF_Money - DT_Money;
		END IF;
		--  处理团队销售佣金
		IF ReqProfit > 0 AND UF_ProfitTeam > 0 THEN
			IF UF_ProfitTeam >= ReqProfit THEN
				SET DT_ProfitTeam = ReqProfit;
				SET AF_ProfitTeam = UF_ProfitTeam - DT_ProfitTeam;
				SET ReqProfit = 0;
			ELSE
				SET DT_ProfitTeam = UF_ProfitTeam;
				SET AF_ProfitTeam = 0;
				SET ReqProfit = ReqProfit - DT_ProfitTeam;
			END IF;
		END IF;
		-- 处理分享佣金
		IF ReqProfit > 0 AND UF_ProfitShare > 0 THEN
			IF UF_ProfitShare >= ReqProfit THEN
				SET DT_ProfitShare = ReqProfit;
				SET AF_ProfitShare = UF_ProfitShare - DT_ProfitShare;
				SET ReqProfit = 0;
			ELSE
				SET DT_ProfitShare = UF_ProfitShare;
				SET AF_ProfitShare = 0;
				SET ReqProfit = ReqProfit - DT_ProfitShare;
			END IF;
		END IF;
		-- 处理自买佣金
		IF ReqProfit > 0 AND UF_ProfitSelf > 0 THEN
			IF UF_ProfitSelf >= ReqProfit THEN
				SET DT_ProfitSelf = ReqProfit;
				SET AF_ProfitSelf = UF_ProfitSelf - DT_ProfitSelf;
				SET ReqProfit = 0;
			ELSE
				SET DT_ProfitSelf = UF_ProfitSelf;
				SET AF_ProfitSelf = 0;
				SET ReqProfit = ReqProfit - DT_ProfitSelf;
			END IF;
		END IF;
		-- 处理间接招聘佣金
		IF ReqRecruit > 0 AND UF_RecruitAlt > 0 THEN
			IF UF_RecruitAlt >= ReqRecruit THEN
				SET DT_RecruitAlt = ReqRecruit;
				SET AF_RecruitAlt = UF_RecruitAlt - DT_RecruitAlt;
				SET ReqRecruit = 0;
			ELSE
				SET DT_RecruitAlt = UF_RecruitAlt;
				SET AF_RecruitAlt = 0;
				SET ReqRecruit = ReqRecruit - DT_RecruitAlt;
			END IF;
		END IF;
		--  处理直接招聘佣金
		IF ReqRecruit > 0 AND UF_RecruitDir > 0 THEN
			IF UF_RecruitDir >= ReqRecruit THEN
				SET DT_RecruitDir = ReqRecruit;
				SET AF_RecruitDir = UF_RecruitDir - DT_RecruitDir;
				SET ReqRecruit = 0;
			ELSE
				SET DT_RecruitDir = UF_RecruitDir;
				SET AF_RecruitDir = 0;
				SET ReqRecruit = ReqRecruit - DT_RecruitDir;
			END IF;
		END IF;
		
		-- 开始扣钱
		IF DT_Money > 0 THEN
			INSERT INTO `tally_money` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'WITHDRAW','',UF_Money,-DT_Money,AF_Money,'提现冻结',NOW(),ClientIp);
			SET TID_Money = LAST_INSERT_ID();
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','冻结错#1');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		IF DT_ProfitSelf > 0 THEN
			INSERT INTO `tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'SELF','WITHDRAW','',UF_ProfitSelf,-DT_ProfitSelf,AF_ProfitSelf,'提现冻结',NOW(),ClientIp);
			SET TID_ProfitSelf = LAST_INSERT_ID();
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','冻结错#1');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		IF DT_ProfitShare > 0 THEN
			INSERT INTO `tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'SHARE','WITHDRAW','',UF_ProfitShare,-DT_ProfitShare,AF_ProfitShare,'提现冻结',NOW(),ClientIp);
			SET TID_ProfitShare = LAST_INSERT_ID();
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','冻结错#2');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		IF DT_ProfitTeam > 0 THEN
			INSERT INTO `tally_profit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'TEAM','WITHDRAW','',UF_ProfitTeam,-DT_ProfitTeam,AF_ProfitTeam,'提现冻结',NOW(),ClientIp);
			SET TID_ProfitTeam = LAST_INSERT_ID();
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','冻结错#3');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		IF DT_RecruitDir > 0 THEN
			INSERT INTO `tally_recruit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'DIR','WITHDRAW','',UF_RecruitDir,-DT_RecruitDir,AF_RecruitDir,'提现冻结',NOW(),ClientIp);
			SET TID_RecruitDir = LAST_INSERT_ID();
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','冻结错#4');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		IF DT_RecruitAlt > 0 THEN
			INSERT INTO `tally_recruit` (`user_id`,`target`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'ALT','WITHDRAW','',UF_RecruitAlt,-DT_RecruitAlt,AF_RecruitAlt,'提现冻结',NOW(),ClientIp);
			SET TID_RecruitAlt = LAST_INSERT_ID();
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','冻结错#5');
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
			SET ReturnMessage = CONCAT(ReturnMessage,'->','扣减错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		-- 定义OrderId
		SET WD_OrderId =CONCAT('TX',RIGHT(CONCAT('00',YEAR(NOW()) - 2000),2),RIGHT(CONCAT('00',MONTH(NOW())),2),RIGHT(CONCAT('00',DAY(NOW())),2),UPPER(RAND_STRING(4)));
		INSERT INTO `user_withdraw` 
			(`user_id`,`order_id`,`total`,`money`,`profit_self`,`profit_share`,`profit_team`,`recruit_dir`,`recruit_alt`,
							`userbank_id`,`bank_id`,`region_id`,`bank_name`,`card_no`,`status`,`create_time`,`create_from`)
		VALUES (UserId,WD_OrderId,ReqMoney + ReqProfit + ReqRecruit,
					DT_Money,DT_ProfitSelf,DT_ProfitShare,DT_ProfitTeam,DT_RecruitDir,DT_RecruitAlt,
					ReqBankId,BK_Bank,BK_Region,BK_Name,BK_Serial,0,UNIX_TIMESTAMP(),ClientIp);
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','记录错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SET WithdrawId = LAST_INSERT_ID();
		IF TID_Money > 0 THEN
			UPDATE `tally_money` SET `order_id` = WD_OrderId WHERE `id` = TID_Money;
		END IF;
		IF TID_ProfitSelf > 0 THEN
			UPDATE `tally_profit` SET `order_id` = WD_OrderId WHERE `id` = TID_ProfitSelf;
		END IF;
		IF TID_ProfitShare> 0 THEN
			UPDATE `tally_profit` SET `order_id` = WD_OrderId WHERE `id` = TID_ProfitSelf;
		END IF;
		IF TID_ProfitTeam > 0 THEN
			UPDATE `tally_profit` SET `order_id` = WD_OrderId WHERE `id` = TID_ProfitSelf;
		END IF;
		IF TID_RecruitDir > 0 THEN
			UPDATE `tally_recruit` SET `order_id` = WD_OrderId WHERE `id` = TID_RecruitDir;
		END IF;
		IF TID_RecruitAlt > 0 THEN
			UPDATE `tally_recruit` SET `order_id` = WD_OrderId WHERE `id` = TID_RecruitAlt;
		END IF;
		IF SYSError = 1 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','同步错');
		END IF;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
