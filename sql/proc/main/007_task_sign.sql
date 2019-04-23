/*
	签到相关存储过程
*/
DELIMITER |||


DROP PROCEDURE IF EXISTS `task_sign_exec` |||
CREATE PROCEDURE `task_sign_exec` (
	IN UserId INT UNSIGNED,		-- 用户ID
	IN ClientIp BIGINT UNSIGNED,-- IP

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '执行签到'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	-- 声明局部变量
	DECLARE MonthId INT UNSIGNED;
	DECLARE DayId INT UNSIGNED;
	DECLARE SignId INT UNSIGNED;
	DECLARE UserLevelU TINYINT UNSIGNED;
	DECLARE Coin_Old NUMERIC(16,8);
	DECLARE Coin_New NUMERIC(16,8);
	DECLARE F_Exec TINYINT UNSIGNED DEFAULT 0;
	DECLARE F1 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F2 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F3 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F4 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F5 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F6 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F7 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F8 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F9 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F10 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F11 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F12 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F13 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F14 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F15 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F16 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F17 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F18 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F19 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F20 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F21 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F22 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F23 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F24 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F25 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F26 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F27 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F28 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F29 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F30 TINYINT UNSIGNED DEFAULT 0;
	DECLARE F31 TINYINT UNSIGNED DEFAULT 0;

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '执行签到';
	SET MonthId = YEAR(NOW()) * 100 + MONTH(NOW());
	SET DayId = DAY(NOW());

	IF UserId < 1 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','UserId错');
		LEAVE Main;
	END IF;
	
	-- 开启事务
	START TRANSACTION;
		SELECT `level_u`
		INTO UserLevelU
		FROM `user`
		WHERE `id` = UserId;
		IF SYSEmpty = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无用户');
			LEAVE Main;
		END IF;
		IF UserLevelU = 0 THEN
			ROLLBACK;
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无效用户');
			LEAVE Main;
		END IF;
		SELECT `id`,
				`day_1`,`day_2`,`day_3`,`day_4`,`day_5`,`day_6`,`day_7`,`day_8`,`day_9`,
				`day_10`,`day_11`,`day_12`,`day_13`,`day_14`,`day_15`,`day_16`,`day_17`,`day_18`,`day_19`,
				`day_20`,`day_21`,`day_22`,`day_23`,`day_24`,`day_25`,`day_26`,`day_27`,`day_28`,`day_29`,
				`day_30`,`day_31`
		INTO SignId,
				F1,F2,F3,F4,F5,F6,F7,F8,F9,
				F10,F11,F12,F13,F14,F15,F16,F17,F18,F19,
				F20,F21,F22,F23,F24,F25,F26,F27,F28,F29,
				F30,F31
		FROM `task_sign` WHERE `user_id` = UserId AND `month_id` = MonthId;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录');
			INSERT INTO `task_sign` (`user_id`,`month_id`) VALUES (UserId,MonthId);
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','插记录错');
				LEAVE Main;
			END IF;
			SET SignId = LAST_INSERT_ID();
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老记录');
		END IF;
		IF DayId = 1 AND F1 = 0 THEN
			UPDATE `task_sign` SET `day_1` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 2 AND F2 = 0 THEN
			UPDATE `task_sign` SET `day_2` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 3 AND F3 = 0 THEN
			UPDATE `task_sign` SET `day_3` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 4 AND F4 = 0 THEN
			UPDATE `task_sign` SET `day_4` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 5 AND F5 = 0 THEN
			UPDATE `task_sign` SET `day_5` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 6 AND F6 = 0 THEN
			UPDATE `task_sign` SET `day_6` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 7 AND F7 = 0 THEN
			UPDATE `task_sign` SET `day_7` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 8 AND F8 = 0 THEN
			UPDATE `task_sign` SET `day_8` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 9 AND F9 = 0 THEN
			UPDATE `task_sign` SET `day_9` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 10 AND F10 = 0 THEN
			UPDATE `task_sign` SET `day_10` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 11 AND F11 = 0 THEN
			UPDATE `task_sign` SET `day_11` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 12 AND F12 = 0 THEN
			UPDATE `task_sign` SET `day_12` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 13 AND F13 = 0 THEN
			UPDATE `task_sign` SET `day_13` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 14 AND F14 = 0 THEN
			UPDATE `task_sign` SET `day_14` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 15 AND F15 = 0 THEN
			UPDATE `task_sign` SET `day_15` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 16 AND F16 = 0 THEN
			UPDATE `task_sign` SET `day_16` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 17 AND F17 = 0 THEN
			UPDATE `task_sign` SET `day_17` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 18 AND F18 = 0 THEN
			UPDATE `task_sign` SET `day_18` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 19 AND F19 = 0 THEN
			UPDATE `task_sign` SET `day_19` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 20 AND F20 = 0 THEN
			UPDATE `task_sign` SET `day_20` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 21 AND F21 = 0 THEN
			UPDATE `task_sign` SET `day_21` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 22 AND F22 = 0 THEN
			UPDATE `task_sign` SET `day_22` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 23 AND F23 = 0 THEN
			UPDATE `task_sign` SET `day_23` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 24 AND F24 = 0 THEN
			UPDATE `task_sign` SET `day_24` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 25 AND F25 = 0 THEN
			UPDATE `task_sign` SET `day_25` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 26 AND F26 = 0 THEN
			UPDATE `task_sign` SET `day_26` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 27 AND F27 = 0 THEN
			UPDATE `task_sign` SET `day_27` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 28 AND F28 = 0 THEN
			UPDATE `task_sign` SET `day_28` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 29 AND F29 = 0 THEN
			UPDATE `task_sign` SET `day_29` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 30 AND F30 = 0 THEN
			UPDATE `task_sign` SET `day_30` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSEIF DayId = 31 AND F31 = 0 THEN
			UPDATE `task_sign` SET `day_31` = 1 WHERE `id` = SignId;
			SET F_Exec = 1;
		ELSE
			COMMIT;
			SET ReturnValue = 'OK';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','已签到');
			LEAVE Main;
		END IF;
		IF F_Exec = 0 THEN
			COMMIT;
			SET ReturnValue = 'OK';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','重复签到');
			LEAVE Main;
		END IF;

 		SET ReturnMessage = CONCAT(ReturnMessage,'->','给奖励');

		SELECT `coin` INTO Coin_Old FROM `user_finance` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新账户');
			INSERT INTO `user_finance` (`user_id`) VALUES (UserId);
			SET Coin_Old = 0;
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_FINANCE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无账户');
				LEAVE Main;
			END IF;
		END IF;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','原' , Coin_Old);
		SET Coin_New = Coin_Old + 0.01;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','新' , Coin_New);
		UPDATE `user_finance` SET `coin` = Coin_New WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错');
			LEAVE Main;
		END IF;
		INSERT INTO `tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
		VALUES (UserId,'SIGN',SignId,Coin_Old,0.01,Coin_New,'每日签到奖励',NOW(),ClientIp);
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_TALLY';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
			LEAVE Main;
		END IF;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
