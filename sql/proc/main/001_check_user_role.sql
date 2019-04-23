DELIMITER |||

/*
 检查用户身份
*/
DROP PROCEDURE IF EXISTS `check_user_role` |||
CREATE PROCEDURE `check_user_role` (
	IN UserId INT UNSIGNED,					/* 用户ID */
	
	OUT LevelUser		TINYINT UNSIGNED,
	OUT LevelVip		TINYINT UNSIGNED,
	OUT LevelCheif		TINYINT UNSIGNED,
	OUT LevelDirector	TINYINT UNSIGNED,
	OUT LevelSupplier	TINYINT UNSIGNED,
	OUT LevelTeam		TINYINT UNSIGNED,
	OUT LevelAdmin		TINYINT UNSIGNED,

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
)
LANGUAGE SQL 
NOT DETERMINISTIC SQL 
SECURITY INVOKER 
CONTAINS SQL READS SQL DATA MODIFIES SQL DATA 
COMMENT '检查身份'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	-- 定义局部变量
	DECLARE Z_NOW BIGINT UNSIGNED DEFAULT 0;

	DECLARE TmpId INT UNSIGNED DEFAULT 0;
	DECLARE TmpStatus TINYINT UNSIGNED DEFAULT 0;
	DECLARE TmpExpire BIGINT UNSIGNED DEFAULT 0;

	DECLARE StuType TINYINT UNSIGNED DEFAULT 0;
	DECLARE StuStart BIGINT UNSIGNED DEFAULT 0;
	DECLARE StuExpire BIGINT UNSIGNED DEFAULT 0;

	DECLARE T INT UNSIGNED DEFAULT 0;

	-- 游标
	DECLARE CurVipStatus CURSOR FOR 
		SELECT `type`,`start_time`,`expire_time` 
		FROM `vip_buff` 
		WHERE `user_id` = UserId 
		ORDER BY `start_time` ASC;
	DECLARE CurCheifStatus CURSOR FOR 
		SELECT `type`,`start_time`,`expire_time` 
		FROM `cheif_buff` 
		WHERE `user_id` = UserId  AND `pay_status` = 2
		ORDER BY `start_time` ASC;
	DECLARE CurDirectorStatus CURSOR FOR 
		SELECT `type`,`start_time`,`expire_time` 
		FROM `director_buff` 
		WHERE `user_id` = UserId  AND `pay_status` = 2
		ORDER BY `start_time` ASC;
	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;

	-- 初始化变量
	SET Z_NOW = UNIX_TIMESTAMP();
	SET LevelUser = 0;
	SET LevelVip = 0;
	SET LevelCheif = 0;
	SET LevelDirector = 0;
	SET LevelSupplier = 0;
	SET LevelAdmin = 0;
	SET LevelTeam = 0;
	
	SET ReturnValue = '';
	SET ReturnMessage = '检查身份';

	-- 检查参数

	-- 开始事务
	START TRANSACTION;
		SELECT `level_u`,`level_v`,`level_c`,`level_d`,`level_t`,`level_a`,`level_s`
		INTO LevelUser,LevelVip,LevelCheif,LevelDirector,LevelTeam,LevelAdmin,LevelSupplier
		FROM `user` WHERE `id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = '无用户';
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF LevelUser = 0 THEN
			SET ReturnValue = 'OK';
			SET LevelVip = 0;
			SET LevelCheif = 0;
			SET LevelDirector = 0;
			SET LevelSupplier = 0;
			SET LevelAdmin = 0;
			SET LevelTeam = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','被禁闭');
			ROLLBACK;
			LEAVE Main;
		END IF;
		-- 检查VIP
		SET SYSEmpty = 0;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查VIP');
		SELECT `user_id`,`status`,`expire_time` 
		INTO TmpId,TmpStatus,TmpExpire
		FROM `vip` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
			SET LevelVip = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','非VIP');
		ELSE
			SELECT COUNT(*) INTO TmpId FROM `vip_buff` WHERE `user_id` = UserId;
			IF SYSEmpty = 1 OR TmpId < 1 THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无VBUF');
				SET SYSEmpty = 0;
				SET TmpId = 0;
				SET LevelVip = 0;
				SET TmpExpire = 0;
			ELSE
				SET ReturnMessage = CONCAT(ReturnMessage,'->','查VBUF');
				SET LevelVip = 0;
				OPEN CurVipStatus;
				FETCH NEXT FROM CurVipStatus INTO StuType,StuStart,StuExpire;
				LOOPVip : WHILE SYSEmpty = 0 DO
					IF StuStart <= Z_NOW AND StuExpire >= Z_NOW AND LevelVip != StuType THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP(',LevelVip,'=>',StuType,')');
						SET LevelVip = StuType;
					END IF;
					IF StuExpire > TmpExpire THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP延期');
						SET TmpExpire = StuExpire;
					END IF;
					FETCH NEXT FROM CurVipStatus INTO StuType,StuStart,StuExpire;
				END WHILE;
				CLOSE CurVipStatus;
			
			END IF;
		END IF;
		UPDATE `vip` SET `status` = LevelVip,`expire_time` = TmpExpire WHERE `user_id` = UserId;

		-- 检查总监
		SET SYSEmpty = 0;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查总监');
		SELECT `user_id`,`status`,`expire_time` 
		INTO TmpId,TmpStatus,TmpExpire
		FROM `cheif` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
			SET LevelCheif = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','非总监');
		ELSE
			SELECT COUNT(*) INTO TmpId FROM `cheif_buff` WHERE `user_id` = UserId AND `pay_status` = 2;
			IF SYSEmpty = 1 OR TmpId < 1 THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无CBUF');
				SET SYSEmpty = 0;
				SET TmpId = 0;
				SET LevelCheif = 0;
				SET TmpExpire = 0;
			ELSE
				SET T = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','查CBUF');
				SET LevelCheif = 0;
				OPEN CurCheifStatus;
				FETCH NEXT FROM CurCheifStatus INTO StuType,StuStart,StuExpire;
				LOOPCheif : WHILE SYSEmpty = 0 DO
					IF StuStart <= Z_NOW AND StuExpire >= Z_NOW AND LevelCheif != StuType THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','总监(',LevelCheif,'=>',StuType,')');
						SET LevelCheif = StuType;
					END IF;
					IF StuExpire > TmpExpire THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','总监延期');
						SET TmpExpire = StuExpire;
					END IF;
					SET T = T + 1;
					IF T > TmpId THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','超时');
						LEAVE LOOPCheif;
					END IF;
					FETCH NEXT FROM CurCheifStatus INTO StuType,StuStart,StuExpire;
					SET ReturnMessage = CONCAT(ReturnMessage,'->',StuType);
				END WHILE;
				CLOSE CurCheifStatus;
			END IF;
		END IF;
		UPDATE `cheif` SET `status` = LevelCheif,`expire_time` = TmpExpire WHERE `user_id` = UserId;

		-- 检查经理
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查经理');
		SET SYSEmpty = 0;
		SELECT `user_id`,`status`,`expire_time` 
		INTO TmpId,TmpStatus,TmpExpire
		FROM `director` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
			SET LevelDirector = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','非经理');
		ELSE
			SELECT COUNT(*) INTO TmpId FROM `director_buff` WHERE `user_id` = UserId AND `pay_status` = 2;
			IF SYSEmpty = 1 OR TmpId < 1 THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无DBUF');
				SET SYSEmpty = 0;
				SET TmpId = 0;
				SET LevelDirector = 0;
				SET TmpExpire = 0;
			ELSE
				SET T = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','查DBUF');
				SET LevelDirector = 0;
				OPEN CurDirectorStatus;
				FETCH NEXT FROM CurDirectorStatus INTO StuType,StuStart,StuExpire;
				LOOPDirector : WHILE SYSEmpty = 0 DO
					IF StuStart <= Z_NOW AND StuExpire >= Z_NOW AND LevelDirector != StuType THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','经理(',LevelDirector,'=>',StuType,')');
						SET LevelDirector = StuType;
					END IF;
					IF StuExpire > TmpExpire THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','经理延期');
						SET TmpExpire = StuExpire;
					END IF;
					SET T = T + 1;
					IF T > TmpId THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','超时');
						LEAVE LOOPDirector;
					END IF;
					FETCH NEXT FROM CurDirectorStatus INTO StuType,StuStart,StuExpire;
				END WHILE;
				CLOSE CurDirectorStatus;
			END IF;
		END IF;
		UPDATE `director` SET `status` = LevelDirector,`expire_time` = TmpExpire WHERE `user_id` = UserId;
		
		SET TmpId = 0;
		-- 检查供应商
		SET SYSEmpty = 0;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查供应商');
		SELECT `id`,`pi_enable` INTO TmpId,TmpStatus FROM `supplier` WHERE `user_id` = UserId AND `status` = 1;
		IF SYSEmpty = 1 OR TmpId = 0 THEN
			SET SYSEmpty = 0;
			SET LevelSupplier = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','否');
		ELSE
			SET LevelSupplier = 1;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','是');
		END IF;
		-- 继续检查供应商子帐号
		IF LevelSupplier = 0 THEN
			SELECT `id` INTO TmpId FROM `supplier_user` WHERE `user_id` = UserId AND `status` = 1;
			IF SYSEmpty = 1 OR TmpId = 0 THEN
				SET SYSEmpty = 0;
				SET LevelSupplier = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','再否');
			ELSE
				SET LevelSupplier = 1;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','再是');
			END IF;
		END IF;

		-- 检查管理员
		SET SYSEmpty = 0;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查管理员');
		SELECT `id` INTO TmpId FROM `rbac_admin` WHERE `user_id` = UserId AND `status` = 1;
		IF SYSEmpty = 1 OR TmpId = 0 THEN
			SET SYSEmpty = 0;
			SET LevelAdmin = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','否');
		ELSE
			SET LevelAdmin = 1;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','是');
		END IF;

		-- 检查团队
		SET SYSEmpty = 0;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查团队');
		SELECT `id` INTO TmpId FROM `team_member` WHERE `user_id` = UserId AND `status` = 1;
		IF SYSEmpty = 1 OR TmpId = 0 THEN
			SET SYSEmpty = 0;
			SET LevelTeam = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','否');
		ELSE
			SET LevelTeam = 1;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','是');
		END IF;
		
		-- 最后更新User
		UPDATE `user` SET 
			`level_v` = LevelVip,
			`level_c` = LevelCheif,
			`level_d` = LevelDirector,
			`level_s` = LevelSupplier,
			`level_t` = LevelTeam,
			`level_a` = LevelAdmin
		WHERE `id` = UserId;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
