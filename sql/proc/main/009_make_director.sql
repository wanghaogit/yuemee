/*
	成为总监相关存储过程
*/
DELIMITER |||

DROP PROCEDURE IF EXISTS `make_premote_director` |||
CREATE PROCEDURE `make_premote_director` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN ClientIp BIGINT UNSIGNED,	-- 操作IP

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '晋升总监'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE UserLevelU	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelC	TINYINT UNSIGNED DEFAULT 0;

	DECLARE PayChannel			TINYINT UNSIGNED DEFAULT 0;
	DECLARE OrderPayChannel		TINYINT UNSIGNED DEFAULT 0;
	DECLARE PayStatus			TINYINT UNSIGNED DEFAULT 0;
	DECLARE PayTime				BIGINT	UNSIGNED DEFAULT 0;
	DECLARE PayOnline			NUMERIC(16,4) DEFAULT 0;
	DECLARE OStatus				TINYINT UNSIGNED DEFAULT 0;
	
	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '晋升总监';
	-- 开启事务
	START TRANSACTION;
		
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||

/*
	购买总经理
*/
DROP PROCEDURE IF EXISTS `make_money_director` |||
CREATE PROCEDURE `make_money_director` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN ClientIp BIGINT UNSIGNED,	-- 操作IP

	OUT DirectorBuffId		INT UNSIGNED,
	OUT OrderId			VARCHAR(16),			
	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '购买总经理'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE UserLevelU	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelC	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelD	TINYINT UNSIGNED DEFAULT 0;

	DECLARE DirectorStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE DirectorExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE DirectorStatusId		INT UNSIGNED DEFAULT 0;
	DECLARE DirectorStatusExpire	BIGINT UNSIGNED DEFAULT 0;

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '购买总经理';
	-- 开启事务
	START TRANSACTION;
		SELECT level_u,level_v,level_c,level_d INTO UserLevelU,UserLevelV,UserLevelC,UserLevelD FROM `user` WHERE `id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无用户');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF UserLevelU = 0 THEN
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无效用户');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		SET OrderId = NEW_ORDER_ID('K','C');
		SET SYSError = 0 ;

		-- -----------------------------------------------------------------总经理
		SELECT `status`,`expire_time`
		INTO DirectorStatus,DirectorExpire
		FROM `director` WHERE `user_id` = UserId FOR UPDATE;

		IF SYSEmpty = 1 THEN 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新总经理');
			SET SYSEmpty = 0;
			SET DirectorStatus = 0;
			SET DirectorExpire = UNIX_TIMESTAMP();
			INSERT INTO `director` (`user_id`,`status`,`create_time`,`update_time`,`expire_time`)
			VALUES (UserId,0,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP()+ 31536000);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DIRECTOR';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;

		-- 新纪录
		INSERT INTO `director_buff` (`user_id`,`type`,`pay_channel`,`pay_status`,`pay_time`,`order_id`,`expire_time`,`start_time`,`money`,`create_time`,`create_from`)
		VALUES (UserId,3,2,1,0,OrderId,UNIX_TIMESTAMP()+ 31536000,UNIX_TIMESTAMP(),3999,UNIX_TIMESTAMP(),ClientIp);
		SET DirectorBuffId = LAST_INSERT_ID();
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_BUFF';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','DBUFF新状态错');
			ROLLBACK;
			LEAVE Main;
		END IF;

	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||



/*
 * 确认总经理
 */
DROP PROCEDURE IF EXISTS `make_money_director_accept` |||
CREATE PROCEDURE `make_money_director_accept` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN ClientIp BIGINT UNSIGNED,	-- 操作IP
	IN OrderId			VARCHAR(16),			
	IN DirectorBuffId	INT UNSIGNED,

	OUT ReturnValue		VARCHAR(32),
	OUT ReturnMessage	VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '卡位总监'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE UserLevelU	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelC	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelD	TINYINT UNSIGNED DEFAULT 0;

	DECLARE VipCode		VARCHAR(8) DEFAULT '';
	DECLARE VipStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE VipExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE VipStatusId		INT UNSIGNED DEFAULT 0;
	DECLARE VipStatusExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE DirectorStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE DirectorExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE DirectorStatusId		INT UNSIGNED DEFAULT 0;
	DECLARE DirectorStatusExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE CardSerial	VARCHAR(10)	DEFAULT '';		-- 总监卡号
	DECLARE I			INT UNSIGNED DEFAULT 0;
	DECLARE TEMPID			TINYINT UNSIGNED DEFAULT 0;

	DECLARE InvitorN		INT DEFAULT 0;
	DECLARE J				INT DEFAULT 2;
	DECLARE M				INT DEFAULT 2;
	DECLARE sTemp		VARCHAR(65532)	DEFAULT '';	
	DECLARE sTempChd	VARCHAR(65532)	DEFAULT '';	
	DECLARE TempStr		VARCHAR(65532)	DEFAULT '';
	DECLARE TempStr1	VARCHAR(65532)	DEFAULT '';
	DECLARE TempUserId	INT UNSIGNED DEFAULT 0;
	DECLARE TempUserLevelV	TINYINT UNSIGNED DEFAULT 0;
	DECLARE TempUserLevelC	TINYINT UNSIGNED DEFAULT 0;
	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '卡位总经理';
	-- 开启事务
	START TRANSACTION;
		SELECT level_u,level_v,level_c INTO UserLevelU,UserLevelV,UserLevelC FROM `user` WHERE `id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无用户');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF UserLevelU = 0 THEN
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无效用户');
			ROLLBACK;
			LEAVE Main;
		END IF;

		-- -----------------------------------------------------------------续充VIP
		SELECT `invite_code`,`status`,`expire_time`
		INTO VipCode,VipStatus,VipExpire
		FROM `vip` WHERE `user_id` = UserId FOR UPDATE;

		IF SYSEmpty = 1 THEN 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新VIP');
			SET SYSEmpty = 0;
			SET VipCode = RAND_STRING(8);
			SET VipStatus = 0;
			SET VipExpire = UNIX_TIMESTAMP();
			INSERT INTO `vip` (`user_id`,`cheif_id`,`director_id`,`invite_code`,`status`,`create_time`,`update_time`,`expire_time`)
			VALUES (UserId,0,0,VipCode,1,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_VIP';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		ELSE 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老VIP');

			-- 更改原VIP中的关系
			UPDATE `vip` SET `cheif_id` = 0,director_id = 0 WHERE `user_id` = UserId;
			SET SYSError = 0;

			-- 检查Status
			SELECT `id`,`expire_time` INTO VipStatusId,VipStatusExpire FROM `vip_buff` WHERE `user_id` = UserId ORDER BY `expire_time` DESC LIMIT 1;
			IF SYSEmpty = 1 THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无状态');
				SET SYSEmpty = 0;
				UPDATE `vip` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
				SET SYSError = 0;
				SET VipStatus = 0;
				SET VipExpire = UNIX_TIMESTAMP();
				SET VipStatusId = 0;
				SET VipStatusExpire = VipExpire;
			ELSE
				SET ReturnMessage = CONCAT(ReturnMessage,'->','有状态');
				IF VipStatusExpire != VipExpire THEN
					SET ReturnMessage = CONCAT(ReturnMessage,'->','重新同步');
					IF VipStatusExpire > UNIX_TIMESTAMP() THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','还有效');
						UPDATE `vip` SET `status` = 1,`update_time` = UNIX_TIMESTAMP(),`expire_time` = VipStatusExpire WHERE `user_id` = UserId;
						SET VipExpire = VipStatusExpire;
					ELSE
						SET ReturnMessage = CONCAT(ReturnMessage,'->','过期了');
						UPDATE `vip` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = VipStatusExpire WHERE `user_id` = UserId;
						SET VipExpire = UNIX_TIMESTAMP();
					END IF;
				ELSE
					SET ReturnMessage = CONCAT(ReturnMessage,'->','无需同步');
					IF StatusExpire > UNIX_TIMESTAMP() THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','还有效');
						SET VipExpire = StatusExpire;
					ELSE
						SET ReturnMessage = CONCAT(ReturnMessage,'->','过期了');
						SET VipExpire = UNIX_TIMESTAMP();
					END IF;
				END IF;
			END IF;
		END IF;

		IF SYSError = 1 THEN 
			SET ReturnValue = 'E_BUFF';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','同步出错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		-- 新纪录
		INSERT INTO `vip_buff` (`user_id`,`type`,`order_id`,`tally_id`,`coin`,`start_time`,`expire_time`,`create_time`)
		VALUES (UserId,3,OrderId,'',0.0,VipExpire,VipExpire + 31536000,UNIX_TIMESTAMP());
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP新状态错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `vip` SET `status` = 1,`expire_time` = VipExpire + 31536000,`update_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP记录错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `user` SET `level_v` = 1 WHERE `id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP账户错误');
			ROLLBACK;
			LEAVE Main;
		END IF;

		
		-- ----------------------------------------------------------------TODO吧总监下了
		SELECT id INTO TEMPID FROM `yuemi_main`.`cheif` WHERE `user_id` = UserId;
		IF SYSEmpty = 1 THEN 
			SET SYSEmpty = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','原来不是总监');
		ELSE 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','原来是总监');
			UPDATE `yuemi_main`.`cheif` 
			SET `status` = 0
			WHERE `user_id` = UserId;
		END IF;
		SET SYSError = 0;
		-- 将受邀者的总监总经理改了
		

		-- -----------------------------------------------------------------续充总经理
		SELECT `status`,`expire_time`
		INTO DirectorStatus,DirectorExpire
		FROM `director` WHERE `user_id` = UserId FOR UPDATE;

		IF SYSEmpty = 1 THEN 
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','总经理账户错误');
			ROLLBACK;
			LEAVE Main;
		END IF;
		-- 检查Status
		SELECT `id`,`expire_time` INTO DirectorStatusId,DirectorStatusExpire FROM `director_buff` WHERE `id` = DirectorBuffId ;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_CBUFF';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','总经理BUFF错误');
			ROLLBACK;
			LEAVE Main;
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','有状态');
			IF DirectorStatusExpire != DirectorExpire THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','重新同步');
				IF DirectorStatusExpire > UNIX_TIMESTAMP() THEN
					SET ReturnMessage = CONCAT(ReturnMessage,'->','还有效');
					UPDATE `director` SET `status` = 1,`update_time` = UNIX_TIMESTAMP(),`expire_time` = DirectorStatusExpire WHERE `user_id` = UserId;
					SET DirectorExpire = DirectorStatusExpire;
				ELSE
					SET ReturnMessage = CONCAT(ReturnMessage,'->','过期了');
					UPDATE `director` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = DirectorStatusExpire WHERE `user_id` = UserId;
					SET DirectorExpire = UNIX_TIMESTAMP();
				END IF;
			ELSE
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无需同步');
				IF DirectorStatusExpire > UNIX_TIMESTAMP() THEN
					SET ReturnMessage = CONCAT(ReturnMessage,'->','还有效');
					SET DirectorExpire = DirectorStatusExpire;
				ELSE
					SET ReturnMessage = CONCAT(ReturnMessage,'->','过期了');
					SET DirectorExpire = UNIX_TIMESTAMP();
				END IF;
			END IF;
		END IF;

		-- 新纪录
		
		UPDATE `director` SET `status` = 3,`expire_time` = DirectorExpire + 31536000,`update_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DBUFF';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','BUFF记录错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `user` SET `level_c` = 0, `level_d` = 2 WHERE `id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_U';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错误');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		UPDATE `yuemi_main`.`director_buff` 
		SET `pay_status` = 2 ,`pay_time` = UNIX_TIMESTAMP() 
		WHERE `id` = DirectorBuffId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_CBUFF1';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','改BUFF错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		-- -----------------------------------------------------------------赠送总监卡
		SET ReturnMessage = CONCAT(ReturnMessage,'->','送总监卡');
		SET I = 0;
		WHILE I < 30 DO
			SET CardSerial = RAND_STRING(10);
			INSERT INTO `cheif_card` (`owner_id`,`serial`,`money`,`status`,`create_time`)
			VALUES (UserId,CardSerial,3999.0,0,UNIX_TIMESTAMP());
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','送卡错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SET I = I + 1;
		END WHILE;

		-- -----------------------------------------------------------------迁移邀请关系
		SET sTemp='$';
		SET sTempChd = CAST(UserId AS CHAR);

		WHILE sTempChd IS NOT NULL DO 
			SET sTemp= CONCAT(sTemp,',',sTempChd);
			SELECT GROUP_CONCAT(id) INTO sTempChd FROM `user` WHERE FIND_IN_SET(invitor_id,sTempChd)>0 AND level_d = 0 AND level_u > 0;
		END WHILE;
		SELECT LENGTH(sTemp)-LENGTH(replace(sTemp,',','')) INTO InvitorN;
		SET InvitorN = InvitorN + 1;
		WHILE J <=  InvitorN DO 
			SELECT SUBSTRING_INDEX(sTemp, ',', J) INTO TempStr;
			SELECT SUBSTRING(TempStr,M+1) INTO TempUserId;
			SET M = LENGTH(TempStr) + 1;
			SET J = J +1 ;
			IF TempUserId != UserId THEN 
				-- 确定身份是VIP还是总监
				SELECT level_v,level_c INTO TempUserLevelV,TempUserLevelC FROM `user` WHERE `id` = TempUserId ;
				SET SYSEmpty = 0;
				IF TempUserLevelV > 0 AND TempUserLevelC > 0 THEN -- 是总监
					UPDATE `yuemi_main`.`vip` 
					SET `director_id` = UserId 
					WHERE `user_id` = TempUserId;
					
					UPDATE `yuemi_main`.`cheif`
					SET `director_id` = UserId 
					WHERE `user_id` = TempUserId;
				END IF;
				IF TempUserLevelV > 0 AND TempUserLevelC <= 0 THEN -- 是VIP 
					UPDATE `yuemi_main`.`vip` 
					SET `director_id` = UserId ,`cheif_id` = 0 
					WHERE `user_id` = TempUserId;
				END IF ;
				SET SYSError = 0;
			END IF;
		END WHILE;

	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
