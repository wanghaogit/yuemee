/*
	成为总监相关存储过程
*/
DELIMITER |||

/*
	购买总监
*/
DROP PROCEDURE IF EXISTS `make_money_cheif` |||
CREATE PROCEDURE `make_money_cheif` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN ClientIp BIGINT UNSIGNED,	-- 操作IP


	OUT CheifBuffId		INT UNSIGNED,
	OUT OrderId			VARCHAR(16),			
	OUT ReturnValue		VARCHAR(32),
	OUT ReturnMessage	VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '花钱总监'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE UserLevelU	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelC	TINYINT UNSIGNED DEFAULT 0;

	DECLARE CheifStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE CheifExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE CheifStatusId		INT UNSIGNED DEFAULT 0;
	DECLARE CheifStatusExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE CardSerial	VARCHAR(10)	DEFAULT '';		-- 总监卡号
	DECLARE I			INT UNSIGNED DEFAULT 0;
	DECLARE InvitoriId	INT UNSIGNED DEFAULT 0;
	DECLARE DirectorId	INT UNSIGNED DEFAULT 0;

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '购买总监';
	-- 开启事务
	START TRANSACTION;
		SELECT level_u,level_v,level_c,invitor_id INTO UserLevelU,UserLevelV,UserLevelC,InvitoriId FROM `user` WHERE `id` = UserId FOR UPDATE;
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


		-- 获取 directorid
		GetPrev : BEGIN 
			IF InvitoriId <= 0 THEN 
				SET DirectorId = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无邀请人');
				LEAVE GetPrev;
			ELSE 
				WHILE InvitoriId > 0 AND DirectorId <= 0 DO 
					SELECT director_id INTO DirectorId FROM `yuemi_main`.`vip` WHERE `user_id` = InvitoriId;
					IF SYSEmpty = 1 THEN 
						SET ReturnValue = 'E_IN';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无效邀请人');
						ROLLBACK;
						LEAVE Main;
					END IF;
					IF DirectorId = 0 THEN 
						SELECT user_id INTO DirectorId FROM	`yuemi_main`.`director` WHERE `user_id` = InvitoriId AND status != 0;
						IF SYSEmpty = 1 THEN
							SET SYSEmpty = 0;
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人不是总经理');
						ELSE 
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人是总经理');
							LEAVE GetPrev;
						END IF;
					END IF;
					SELECT invitor_id INTO InvitoriId FROM `user` WHERE `id` = InvitoriId;
					IF SYSEmpty = 1 THEN 
						SET ReturnValue = 'E_USER';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人出错');
						ROLLBACK;
						LEAVE Main;
					END IF;
				END WHILE;
			END IF;
		END ;

		
		-- -----------------------------------------------------------------创建总监
		SELECT `status`,`expire_time`
		INTO CheifStatus,CheifExpire
		FROM `cheif` WHERE `user_id` = UserId FOR UPDATE;

		IF SYSEmpty = 1 THEN 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新总监');
			SET SYSEmpty = 0;
			SET CheifStatus = 0;
			SET CheifExpire = UNIX_TIMESTAMP();
			INSERT INTO `cheif` (`user_id`,`director_id`,`status`,`create_time`,`update_time`,`expire_time`)
			VALUES (UserId,DirectorId,0,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_CHIEF';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','CHEIF新纪录错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;

		-- 新纪录
		INSERT INTO `cheif_buff` (`user_id`,`type`,`pay_channel`,`pay_status`,`pay_time`,`order_id`,`expire_time`,`start_time`,`money`,`create_time`,`create_from`)
		VALUES (UserId,3,1,1,0,OrderId,UNIX_TIMESTAMP()+ 31536000,UNIX_TIMESTAMP(),3999,UNIX_TIMESTAMP(),ClientIp);
		SET CheifBuffId = LAST_INSERT_ID();
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','CHEIF新状态错');
			ROLLBACK;
			LEAVE Main;
		END IF;

	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||



/*
 * 确认总监
 */
DROP PROCEDURE IF EXISTS `make_money_cheif_accept` |||
CREATE PROCEDURE `make_money_cheif_accept` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN ClientIp BIGINT UNSIGNED,	-- 操作IP
	IN OrderId			VARCHAR(16),			
	IN CheifBuffId		INT UNSIGNED,

	OUT ReturnValue		VARCHAR(32),
	OUT ReturnMessage	VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '卡位总监'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE UserLevelU	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelC	TINYINT UNSIGNED DEFAULT 0;

	DECLARE VipCode		VARCHAR(8) DEFAULT '';
	DECLARE VipStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE VipExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE VipStatusId		INT UNSIGNED DEFAULT 0;
	DECLARE VipStatusExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE CheifStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE CheifExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE CheifStatusId		INT UNSIGNED DEFAULT 0;
	DECLARE CheifStatusExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE CardSerial	VARCHAR(10)	DEFAULT '';		-- 总监卡号
	DECLARE I			INT UNSIGNED DEFAULT 0;
	DECLARE DirectorId		INT DEFAULT 0;

	DECLARE InvitorN		INT DEFAULT 0;
	DECLARE J				INT DEFAULT 2;
	DECLARE M				INT DEFAULT 2;
	DECLARE sTemp			TEXT;	
	DECLARE sTempChd		TEXT;	
	DECLARE TempStr			TEXT;	
	DECLARE TempUserId	INT UNSIGNED DEFAULT 0;
	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '卡位总监';
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
		-- -----------------------------------------------------------------续充总监
		SELECT `status`,`expire_time`,`director_id`
		INTO CheifStatus,CheifExpire,DirectorId
		FROM `cheif` WHERE `user_id` = UserId FOR UPDATE;

		IF SYSEmpty = 1 THEN 
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','总监账户错误');
			ROLLBACK;
			LEAVE Main;
		END IF;
		-- 检查Status
		SELECT `id`,`expire_time` INTO CheifStatusId,CheifStatusExpire FROM `cheif_buff` WHERE `id` = CheifBuffId ;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_CBUFF';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','总监BUFF错误');
			ROLLBACK;
			LEAVE Main;
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','有状态');
			IF CheifStatusExpire != CheifExpire THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','重新同步');
				IF CheifStatusExpire > UNIX_TIMESTAMP() THEN
					SET ReturnMessage = CONCAT(ReturnMessage,'->','还有效');
					UPDATE `cheif` SET `status` = 1,`update_time` = UNIX_TIMESTAMP(),`expire_time` = CheifStatusExpire WHERE `user_id` = UserId;
					SET CheifExpire = CheifStatusExpire;
				ELSE
					SET ReturnMessage = CONCAT(ReturnMessage,'->','过期了');
					UPDATE `cheif` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = CheifStatusExpire WHERE `user_id` = UserId;
					SET CheifExpire = UNIX_TIMESTAMP();
				END IF;
			ELSE
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无需同步');
				IF CheifStatusExpire > UNIX_TIMESTAMP() THEN
					SET ReturnMessage = CONCAT(ReturnMessage,'->','还有效');
					SET CheifExpire = CheifStatusExpire;
				ELSE
					SET ReturnMessage = CONCAT(ReturnMessage,'->','过期了');
					SET CheifExpire = UNIX_TIMESTAMP();
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
		
		UPDATE `cheif` SET `status` = 3,`expire_time` = CheifExpire + 31536000,`update_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','CHEIF记录错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `user` SET `level_c` = 3 WHERE `id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错误');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		UPDATE `yuemi_main`.`cheif_buff` 
		SET `pay_status` = 2 ,`pay_time` = UNIX_TIMESTAMP() 
		WHERE `id` = CheifBuffId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_CBUFF1';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','改BUFF错');
			ROLLBACK;
			LEAVE Main;
		END IF;

		-- -----------------------------------------------------------------续充VIP
		SELECT `invite_code`,`status`,`expire_time`
		INTO VipCode,VipStatus,VipExpire
		FROM `vip` WHERE `user_id` = UserId;

		IF SYSEmpty = 1 THEN 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新VIP');
			SET SYSEmpty = 0;
			SET VipCode = RAND_STRING(8);
			SET VipStatus = 0;
			SET VipExpire = UNIX_TIMESTAMP();
			INSERT INTO `vip` (`user_id`,`cheif_id`,`director_id`,`invite_code`,`status`,`create_time`,`update_time`,`expire_time`)
			VALUES (UserId,0,DirectorId,VipCode,1,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_VIP';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		ELSE 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老VIP');

			UPDATE `vip` SET `cheif_id` = 0 , `director_id` = DirectorId WHERE `user_id` = UserId;
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

		
		-- -----------------------------------------------------------------赠送VIP卡
		SET ReturnMessage = CONCAT(ReturnMessage,'->','送VIP卡');
		SET I = 0;
		WHILE I < 10 DO
			SET CardSerial = RAND_STRING(10);
			INSERT INTO `vip_card` (`owner_id`,`serial`,`money`,`status`,`create_time`)
			VALUES (UserId,CardSerial,399.0,0,UNIX_TIMESTAMP());
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
			SELECT GROUP_CONCAT(id) INTO sTempChd FROM `user` WHERE FIND_IN_SET(invitor_id,sTempChd)>0 AND level_c = 0 AND level_d = 0 AND level_u > 0;
		END WHILE;
		SELECT LENGTH(sTemp)-LENGTH(replace(sTemp,',','')) INTO InvitorN;
		SET InvitorN = InvitorN + 1;
		WHILE J <=  InvitorN DO 
			SELECT SUBSTRING_INDEX(sTemp, ',', J) INTO TempStr;
			SELECT SUBSTRING(TempStr,M+1) INTO TempUserId;
			SET M = LENGTH(TempStr) + 1;
			SET J = J +1 ;
			IF TempUserId != UserId THEN 
				UPDATE `yuemi_main`.`vip`,`yuemi_main`.`user` 
				SET `vip`.`cheif_id` = UserId ,`vip`.`director_id` = DirectorId 
				WHERE `vip`.`user_id` = TempUserId AND `user`.`level_v` > 0;
			END IF;
		END WHILE;

	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||

DROP PROCEDURE IF EXISTS `testdigui` |||

