/*
	成为总监相关存储过程
*/
DELIMITER |||

/*
	开卡送10个VIP
*/
DROP PROCEDURE IF EXISTS `make_card_cheif` |||
CREATE PROCEDURE `make_card_cheif` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN CardSerial VARCHAR(10),		-- 总监卡号
	IN ClientIp BIGINT UNSIGNED,	-- 操作IP

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '卡充总监'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	-- 用户信息
	DECLARE UserMobile		VARCHAR(16) DEFAULT '';
	DECLARE UserLevelU		TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV		TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelC		TINYINT UNSIGNED DEFAULT 0;

	-- 总经理状态
	DECLARE DirectorId			INT UNSIGNED DEFAULT 0;
	DECLARE DirectorStatus		TINYINT UNSIGNED DEFAULT 0;

	DECLARE CardId			INT UNSIGNED DEFAULT 0;
	DECLARE CardOwnerId		INT UNSIGNED DEFAULT 0;
	DECLARE CardStatus		TINYINT UNSIGNED DEFAULT 0;
	DECLARE CardMoney		NUMERIC(16,4) DEFAULT 0;
	DECLARE VipId			INT UNSIGNED DEFAULT 0;

	-- VIP
	DECLARE VipCode		VARCHAR(8) DEFAULT '';
	DECLARE VipStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE VipExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE VipStatusId		INT UNSIGNED DEFAULT 0;
	DECLARE VipStatusExpire	BIGINT UNSIGNED DEFAULT 0;

	-- 总监状态
	DECLARE CheifId			INT UNSIGNED DEFAULT 0;
	DECLARE CheifStatus		TINYINT UNSIGNED DEFAULT 0;

	DECLARE J				INT DEFAULT 2;
	DECLARE M				INT DEFAULT 2;
	DECLARE sTemp			TEXT;	
	DECLARE sTempChd		TEXT;	
	DECLARE TempStr			TEXT;	
	DECLARE TempUserId		INT UNSIGNED DEFAULT 0;
	DECLARE InvitorN		INT DEFAULT 0;

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;

	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '卡充总监';
	-- 检查参数
	IF LENGTH(CardSerial) != 10 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','空');
		LEAVE Main;
	END IF;
	-- 开启事务
	START TRANSACTION;
		SELECT `level_u`,`level_v`,`level_c`,`mobile` INTO UserLevelU,UserLevelV,UserLevelC,UserMobile FROM `user` WHERE `id` = UserId FOR UPDATE;
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
		IF UserLevelC > 0 THEN
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','已是总监');
			ROLLBACK;
			LEAVE Main;
		END IF;

		-- 检查总监卡
		SELECT id,owner_id,status,money INTO CardId,CardOwnerId,CardStatus,CardMoney FROM `cheif_card` WHERE `serial` = CardSerial FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_CARD';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无卡');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF CardStatus != 0 THEN
			SET ReturnValue = 'E_CARD';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','已用');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF CardOwnerId < 1 THEN
			SET ReturnValue = 'E_CARD';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无效');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		-- 检查总经理
		SELECT `status` INTO DirectorStatus FROM `director` WHERE `user_id` = CardOwnerId;
		IF SYSEmpty = 1 OR DirectorStatus <= 0 THEN
			SET ReturnValue = 'E_DIRECTOR';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无效经理');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `cheif_card` SET `rcv_user_id` = UserId,rcv_mobile=UserMobile,status=1,used_time=UNIX_TIMESTAMP() WHERE `id` = CardId;
		SET SYSEmpty = 0;
		-- ------------------------------------------------------------------总监
		SELECT `user_id`,`status`
		INTO CheifId,CheifStatus
		FROM `cheif` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新总监');
			SET SYSEmpty = 0;
			SET CheifStatus = 0;
			INSERT INTO `cheif` (`user_id`,`director_id`,`status`,`create_time`,`update_time`,`expire_time`)
			VALUES (UserId,CardOwnerId,1,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP());
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_CHEIF';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SET CheifId = LAST_INSERT_ID();
		ELSE
			SET ReturnValue = 'E_BUFF';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','已是总监');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		-- 新纪录总监
		INSERT INTO `cheif_buff` (`user_id`,`type`,`pay_channel`,`pay_status`,`pay_time`,`order_id`,`money`,`start_time`,`expire_time`,`create_time`,`create_from`)
		VALUES (UserId,1,1,2,UNIX_TIMESTAMP(),CardSerial,0,UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000,UNIX_TIMESTAMP(),ClientIp);
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新状态错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `cheif` SET `expire_time` = UNIX_TIMESTAMP() + 31536000,`update_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','总监记录错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `user` SET `invitor_id` = CardOwnerId,`level_c` = 1 WHERE `id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_STATUS';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更改用户级别错');
			ROLLBACK;
			LEAVE Main;
		END IF;


		--  -----------------------------------------------------------------VIP
		SET SYSEmpty = 0;
		SELECT `invite_code`,`status`,`expire_time`
		INTO VipCode,VipStatus,VipExpire
		FROM `vip` WHERE `user_id` = UserId;

		IF SYSEmpty = 1 THEN 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新VIP');
			SET SYSEmpty = 0;
			SET VipCode = RAND_STRING(8);
			SET VipStatus = 0;
			SET VipExpire = UNIX_TIMESTAMP();
			INSERT INTO `vip` (`user_id`,`cheif_id`,`invite_code`,`status`,`create_time`,`update_time`,`expire_time`)
			VALUES (UserId,0,VipCode,1,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_VIP';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		ELSE 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老VIP');

			UPDATE `vip` SET `cheif_id` = 0 WHERE `user_id` = UserId;
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
					IF SYSError = 1 THEN 
						SET ReturnValue = 'E_BUFF';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','同步出错');
						ROLLBACK;
						LEAVE Main;
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
		SET SYSError = 0;

		-- 新纪录
		INSERT INTO `vip_buff` (`user_id`,`type`,`order_id`,`tally_id`,`coin`,`start_time`,`expire_time`,`create_time`)
		VALUES (UserId,2,CardSerial,0,0.0,VipExpire,VipExpire + 31536000,UNIX_TIMESTAMP());
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP新状态错');
			ROLLBACK;
			LEAVE Main;
		END IF;

		UPDATE `vip` SET `status` = 2,`expire_time` = VipExpire + 31536000,`update_time` = UNIX_TIMESTAMP() , `director_id` = CardOwnerId WHERE `user_id` = UserId;
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

		-- 赠送10张VIP卡
		SET ReturnMessage = CONCAT(ReturnMessage,'->','送VIP卡');
		SET CardId = 0;
		WHILE CardId < 10 DO
			SET CardSerial = RAND_STRING(10);
			INSERT INTO `vip_card` (`owner_id`,`serial`,`money`,`status`,`create_time`)
			VALUES (UserId,CardSerial,399.0,0,UNIX_TIMESTAMP());
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','送卡错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SET CardId = CardId + 1;
		END WHILE;

		SET SYSError = 0;
		-- 更改激活卡的状态
		UPDATE `yuemi_main`.`cheif_card` 
		SET `status` = 1 
		WHERE `serial` = CardSerial;

		IF SYSError = 1 THEN
			SET ReturnValue = 'E_CARD';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更改卡号状态错');
			ROLLBACK;
			LEAVE Main;
		END IF;


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
				SET `vip`.`cheif_id` = UserId ,director_id = CardOwnerId 
				WHERE `vip`.`user_id` = TempUserId AND `user`.`level_v` > 0; 
			END IF;
		END WHILE;

	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||

/**
 * VIP晋升总监
 */
DROP PROCEDURE IF EXISTS `make_premote_cheif` |||
CREATE PROCEDURE `make_premote_cheif` (
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

	-- VIP
	DECLARE VipCode		VARCHAR(8) DEFAULT '';
	DECLARE VipStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE VipExpire	BIGINT UNSIGNED DEFAULT 0;

	-- 邀请人数
	DECLARE VipInvQty		TINYINT UNSIGNED DEFAULT 0; -- 直接邀请人数
	DECLARE VipIndirectQty	TINYINT UNSIGNED DEFAULT 0; -- 间接邀请人数

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '晋升总监';
	-- 开启事务
	START TRANSACTION;
		SELECT `level_u`,`level_v`,`level_c` INTO UserLevelU,UserLevelV,UserLevelC FROM `user` WHERE `id` = UserId FOR UPDATE;
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
		IF UserLevelV < 0 THEN 
			SET ReturnValue = 'E_VIP';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无法晋升');
			ROLLBACK;
			LEAVE Main;
		END IF ;
		
		SELECT `invite_code`,`status`,`expire_time`
		INTO VipCode,VipStatus,VipExpire
		FROM `vip` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无用户');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF VipExpire < UNIX_TIMESTAMP() THEN 
			SET ReturnValue = 'E_TIME';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP过期');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
