/*
	VIP相关存储过程
 */
DELIMITER |||

DROP PROCEDURE IF EXISTS `make_money_vip` |||
CREATE PROCEDURE `make_money_vip` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN OrderId	VARCHAR(16),
	IN ClientIp BIGINT UNSIGNED,	-- 操作IP

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '购买VIP'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE OStatus		INT DEFAULT 0;
	DECLARE UCoin		NUMERIC(16,8);
	DECLARE UCoin_New	NUMERIC(16,8);
	DECLARE VipId		INT UNSIGNED DEFAULT 0;
	DECLARE VipCode		VARCHAR(8) DEFAULT '';
	DECLARE VipStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE VipExpire	BIGINT UNSIGNED DEFAULT 0;
	DECLARE StatusId		INT UNSIGNED DEFAULT 0;
	DECLARE StatusExpire	BIGINT UNSIGNED DEFAULT 0;
	DECLARE TallyId		INT DEFAULT 0;

	-- 获取cheif ID
	DECLARE InvitoriId		INT DEFAULT 0;
	DECLARE TemInvitoriId	INT DEFAULT 0;
	DECLARE CheifId			INT DEFAULT 0;
	DECLARE DirectorId		INT DEFAULT 0;

	DECLARE UserLevelU		TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV		TINYINT UNSIGNED DEFAULT 0;
	DECLARE Str				TEXT;	
	DECLARE IdIndex			INT DEFAULT 0;
	DECLARE TemStr			VARCHAR(16) DEFAULT '';


	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '购买VIP';
	SET Str = '临时字符串';
	SET TemStr = '';

	START TRANSACTION;
		SELECT level_u,level_v,invitor_id  INTO UserLevelU,UserLevelV,InvitoriId FROM `user` WHERE `id` = UserId FOR UPDATE; 
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

		IF InvitoriId = UserId THEN 
			SET InvitoriId = 0;
			UPDATE `user` SET invitor_id = InvitoriId WHERE `id` = UserId;
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_INV';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新关系错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;

		SELECT `status` INTO OStatus FROM `yuemi_sale`.`order` WHERE `id` = OrderId;
		IF SYSEmpty = 1 OR OStatus = 1 OR OStatus = 11 OR OStatus = 12 OR OStatus = 13 OR OStatus = 14 THEN
			SET ReturnValue = 'E_ORDER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无订单');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SELECT `coin` INTO UCoin FROM `yuemi_main`.`user_finance` WHERE `user_id` = UserId;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_F';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无账户');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF UCoin < 1000 THEN 
			SET ReturnValue = 'E_COIN';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','阅币不足');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SET UCoin_New = UCoin - 1000.0;

		INSERT INTO `tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
		VALUES (UserId,'VIP',OrderId,UCoin,1000.0,UCoin_New,'充值VIP',UNIX_TIMESTAMP(),ClientIp);
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_TALLY';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
			LEAVE Main;
		END IF;
		SET TallyId = LAST_INSERT_ID();
		UPDATE `user_finance` SET `coin` = UCoin_New WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更新账户错误');
			LEAVE Main;
		END IF;
		-- 获取 cheifid
		GetPrev : BEGIN 
			IF InvitoriId <= 0 THEN 
				SET CheifId = 0;
				SET DirectorId = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无邀请人');
				LEAVE GetPrev;
			ELSE 
				WHILE InvitoriId > 0 AND CheifId <= 0 DO 
					SET Str = CONCAT(Str,'#',InvitoriId,'#,');
					SELECT `cheif_id`,director_id INTO CheifId,DirectorId FROM `yuemi_main`.`vip` WHERE `user_id` = InvitoriId;
					IF SYSEmpty = 1 THEN 
						SET ReturnValue = 'E_IN';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无效邀请人');
						ROLLBACK;
						LEAVE Main;
					END IF;

					IF CheifId = 0 AND DirectorId = 0 THEN -- 邀请人是总经理或者野生VIP
						SELECT user_id INTO DirectorId FROM	`yuemi_main`.`director` WHERE `user_id` = InvitoriId AND status != 0;
						IF SYSEmpty = 1 THEN
							SET SYSEmpty = 0;
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人不是总经理');
						ELSE 
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人是总经理');
							LEAVE GetPrev;
						END IF;
					ELSEIF CheifId = 0 AND DirectorId != 0 THEN -- 总经理直招或总监
						SELECT director_id INTO DirectorId FROM	`yuemi_main`.`cheif` WHERE `user_id` = InvitoriId AND status != 0;
						IF SYSEmpty = 1 THEN -- 邀请人是直招，同为直招
							SET SYSEmpty = 0;
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人不是总监');
						ELSE -- 邀请人为总监
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人是总监');
							SET CheifId = InvitoriId;
							LEAVE GetPrev;
						END IF;
					ELSEIF CheifId != 0 AND DirectorId = 0 THEN -- 不存在
						SET ReturnMessage = CONCAT(ReturnMessage,'->','不存在');
					ELSE -- 全部都有
						SET ReturnMessage = CONCAT(ReturnMessage,'->','全部都有');
						LEAVE GetPrev;
					END IF;

					SELECT invitor_id INTO TemInvitoriId FROM `user` WHERE `id` = InvitoriId;
					IF SYSEmpty = 1 THEN 
						SET ReturnValue = 'E_USER';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人出错');
						ROLLBACK;
						LEAVE Main;
					END IF;
					IF TemInvitoriId = InvitoriId THEN 
						UPDATE `yuemi_main`.`user` SET invitor_id = 0 WHERE `id` = InvitoriId;
						SET CheifId = 0;
						SET DirectorId = 0;
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无邀请人');
						LEAVE GetPrev;
					END IF;
					-- 判断邀请人的邀请人ID是否正确
					SET TemStr = CONCAT('#',TemInvitoriId,'#');
					SELECT find_in_set(TemInvitoriId,Str) INTO IdIndex;
					IF IdIndex > 0 THEN 
						UPDATE `yuemi_main`.`user` SET invitor_id = 0 WHERE `id` = InvitoriId;
						SET CheifId = 0;
						SET DirectorId = 0;
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无邀请人');
						LEAVE GetPrev;
					END IF;
					SET InvitoriId = TemInvitoriId;
				END WHILE;
			END IF;
		END ;

		SET SYSError = 0;
		-- 检查 VIP
		SELECT `user_id`,`invite_code`,`status`,`expire_time` 
		INTO VipId,VipCode,VipStatus,VipExpire 
		FROM `vip` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新VIP');
			SET SYSEmpty = 0;
			SET VipCode = RAND_STRING(8);
			SET VipStatus = 0;
			SET VipExpire = UNIX_TIMESTAMP();
			INSERT INTO `vip` (`user_id`,`cheif_id`,`director_id`,`invite_code`,`status`,`create_time`,`update_time`,`expire_time`)
			VALUES (UserId,CheifId,DirectorId,VipCode,1,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_VIP';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SET VipId = LAST_INSERT_ID();

		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老VIP');
			-- 检查Status
			SELECT `id`,`expire_time` INTO StatusId,StatusExpire FROM `vip_buff` WHERE `user_id` = UserId ORDER BY `expire_time` DESC LIMIT 1;
			IF SYSEmpty = 1 THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无状态');
				SET SYSEmpty = 0;
				UPDATE `vip` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
				SET VipStatus = 0;
				SET VipExpire = UNIX_TIMESTAMP();
				SET StatusId = 0;
				SET StatusExpire = VipExpire;
			ELSE
				SET ReturnMessage = CONCAT(ReturnMessage,'->','有状态');
				IF StatusExpire != VipExpire THEN
					SET ReturnMessage = CONCAT(ReturnMessage,'->','重新同步');
					IF StatusExpire > UNIX_TIMESTAMP() THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','还有效');
						UPDATE `vip` SET `status` = 1,`update_time` = UNIX_TIMESTAMP(),`expire_time` = StatusExpire WHERE `user_id` = UserId;
						SET VipExpire = StatusExpire;
					ELSE
						SET ReturnMessage = CONCAT(ReturnMessage,'->','过期了');
						UPDATE `vip` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = StatusExpire WHERE `user_id` = UserId;
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
		VALUES (UserId,5,'',TallyId,1000.0,VipExpire,VipExpire + 31536000,UNIX_TIMESTAMP());
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新状态错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `vip` SET `status` = 5,`expire_time` = VipExpire + 31536000,`update_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP记录错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `user` SET `level_v` = 5 WHERE `id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错误');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||


DROP PROCEDURE IF EXISTS `make_test_vip` |||
CREATE PROCEDURE `make_test_vip` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN TestDays SMALLINT UNSIGNED,	-- 有效天数
	IN ClientIp BIGINT UNSIGNED,	-- 操作IP

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '测试VIP'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE UserLevelU	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV	TINYINT UNSIGNED DEFAULT 0;
	DECLARE VipId		INT UNSIGNED DEFAULT 0;
	DECLARE VipCode		VARCHAR(8) DEFAULT '';
	DECLARE VipStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE VipExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE StatusId		INT UNSIGNED DEFAULT 0;
	DECLARE StatusExpire	BIGINT UNSIGNED DEFAULT 0;
	-- 获取cheif ID
	DECLARE InvitoriId		INT DEFAULT 0;
	DECLARE TemInvitoriId	INT DEFAULT 0;
	DECLARE CheifId			INT DEFAULT 0;
	DECLARE DirectorId		INT DEFAULT 0;

	DECLARE Str				TEXT;	
	DECLARE IdIndex			INT DEFAULT 0;
	DECLARE TemStr			VARCHAR(16) DEFAULT '';
	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '测试VIP';
	SET Str = '临时字符串';
	SET TemStr = '';
	
	IF TestDays <= 0 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','空');
		LEAVE Main;
	END IF;
	-- 开启事务
	START TRANSACTION;
		SELECT level_u,level_v,invitor_id  INTO UserLevelU,UserLevelV,InvitoriId FROM `user` WHERE `id` = UserId FOR UPDATE;
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

		IF InvitoriId = UserId THEN 
			SET InvitoriId = 0;
			UPDATE `user` SET invitor_id = InvitoriId WHERE `id` = UserId;
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_INV';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新关系错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;

		GetPrev : BEGIN 
			IF InvitoriId <= 0 THEN 
				SET CheifId = 0;
				SET DirectorId = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无邀请人');
				LEAVE GetPrev;
			ELSE 
				WHILE InvitoriId > 0 AND CheifId <= 0 DO 
					SET Str = CONCAT(Str,'#',InvitoriId,'#,');
					SELECT `cheif_id`,director_id INTO CheifId,DirectorId FROM `yuemi_main`.`vip` WHERE `user_id` = InvitoriId;
					IF SYSEmpty = 1 THEN 
						SET ReturnValue = 'E_IN';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无效邀请人');
						ROLLBACK;
						LEAVE Main;
					END IF;

					IF CheifId = 0 AND DirectorId = 0 THEN -- 邀请人是总经理或者野生VIP
						SELECT user_id INTO DirectorId FROM	`yuemi_main`.`director` WHERE `user_id` = InvitoriId AND status != 0;
						IF SYSEmpty = 1 THEN
							SET SYSEmpty = 0;
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人不是总经理');
						ELSE 
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人是总经理');
							LEAVE GetPrev;
						END IF;
					ELSEIF CheifId = 0 AND DirectorId != 0 THEN -- 总经理直招或总监
						SELECT director_id INTO DirectorId FROM	`yuemi_main`.`cheif` WHERE `user_id` = InvitoriId AND status != 0;
						IF SYSEmpty = 1 THEN -- 邀请人是直招，同为直招
							SET SYSEmpty = 0;
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人不是总监');
						ELSE -- 邀请人为总监
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人是总监');
							SET CheifId = InvitoriId;
							LEAVE GetPrev;
						END IF;
					ELSEIF CheifId != 0 AND DirectorId = 0 THEN -- 不存在
						SET ReturnMessage = CONCAT(ReturnMessage,'->','不存在');
					ELSE -- 全部都有
						SET ReturnMessage = CONCAT(ReturnMessage,'->','全部都有');
						LEAVE GetPrev;
					END IF;

					SELECT invitor_id INTO TemInvitoriId FROM `user` WHERE `id` = InvitoriId;
					IF SYSEmpty = 1 THEN 
						SET ReturnValue = 'E_USER';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人出错');
						ROLLBACK;
						LEAVE Main;
					END IF;
					IF TemInvitoriId = InvitoriId THEN 
						UPDATE `yuemi_main`.`user` SET invitor_id = 0 WHERE `id` = InvitoriId;
						SET CheifId = 0;
						SET DirectorId = 0;
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无邀请人');
						LEAVE GetPrev;
					END IF;
					-- 判断邀请人的邀请人ID是否正确
					SET TemStr = CONCAT('#',TemInvitoriId,'#');
					SELECT find_in_set(TemInvitoriId,Str) INTO IdIndex;
					IF IdIndex > 0 THEN 
						UPDATE `yuemi_main`.`user` SET invitor_id = 0 WHERE `id` = InvitoriId;
						SET CheifId = 0;
						SET DirectorId = 0;
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无邀请人');
						LEAVE GetPrev;
					END IF;
					SET InvitoriId = TemInvitoriId;
				END WHILE;
			END IF;
		END ;

		-- 检查 VIP
		SELECT `user_id`,`invite_code`,`status`,`expire_time`
		INTO VipId,VipCode,VipStatus,VipExpire
		FROM `vip` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新VIP');
			SET SYSEmpty = 0;
			SET VipCode = RAND_STRING(8);
			SET VipStatus = 0;
			SET VipExpire = UNIX_TIMESTAMP();
			INSERT INTO `vip` (`user_id`,`cheif_id`,`director_id`,`invite_code`,`status`,`create_time`,`update_time`,`expire_time`)
			VALUES (UserId,CheifId,DirectorId,VipCode,0,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP());
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_VIP';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SET VipId = LAST_INSERT_ID();
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老VIP');
			-- 检查Status
			SELECT `id`,`expire_time` INTO StatusId,StatusExpire FROM `vip_buff` WHERE `user_id` = UserId ORDER BY `expire_time` DESC LIMIT 1;
			IF SYSEmpty = 1 THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无状态');
				SET SYSEmpty = 0;
				UPDATE `vip` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
				SET VipStatus = 0;
				SET VipExpire = UNIX_TIMESTAMP();
				SET StatusId = 0;
				SET StatusExpire = VipExpire;
			ELSE
				SET ReturnMessage = CONCAT(ReturnMessage,'->','有状态');
				IF StatusExpire != VipExpire THEN
					SET ReturnMessage = CONCAT(ReturnMessage,'->','重新同步');
					IF StatusExpire > UNIX_TIMESTAMP() THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','还有效');
						UPDATE `vip` SET `status` = 1,`update_time` = UNIX_TIMESTAMP(),`expire_time` = StatusExpire WHERE `user_id` = UserId;
						SET VipExpire = StatusExpire;
					ELSE
						SET ReturnMessage = CONCAT(ReturnMessage,'->','过期了');
						UPDATE `vip` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = StatusExpire WHERE `user_id` = UserId;
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
		SET ReturnMessage = CONCAT(ReturnMessage,'->','新BUFF');
		INSERT INTO `vip_buff` (`user_id`,`type`,`order_id`,`tally_id`,`coin`,`start_time`,`expire_time`,`create_time`)
		VALUES (UserId,1,'',0,0.0,VipExpire,VipExpire + TestDays * 86400,UNIX_TIMESTAMP());
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新状态错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','写状态');
		UPDATE `vip` SET `status` = 1,`expire_time` = VipExpire + TestDays * 86400,`update_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP记录错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','写等级');
		UPDATE `user` SET `level_v` = 1 WHERE `id` = UserId;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||

DROP PROCEDURE IF EXISTS `make_card_vip` |||
CREATE PROCEDURE `make_card_vip` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN CardSerial VARCHAR(10),		-- VIP 卡号
	IN ClientIp BIGINT UNSIGNED,	-- 操作IP

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '卡充VIP'
Main : BEGIN
	DECLARE SYSError		INT DEFAULT 0;
	DECLARE SYSEmpty		INT DEFAULT 0;

	DECLARE UserMobile		VARCHAR(16) DEFAULT '';
	DECLARE UserLevelU		TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV		TINYINT UNSIGNED DEFAULT 0;
	DECLARE VipId			INT UNSIGNED DEFAULT 0;
	DECLARE VipCode			VARCHAR(8) DEFAULT '';
	DECLARE VipStatus		TINYINT UNSIGNED DEFAULT 0;

	DECLARE CardId			INT UNSIGNED DEFAULT 0;
	DECLARE CardOwnerId		INT UNSIGNED DEFAULT 0;
	DECLARE CardStatus		TINYINT UNSIGNED DEFAULT 0;
	DECLARE CardMoney		NUMERIC(16,4) DEFAULT 0.0;

	DECLARE CheifStatus		TINYINT UNSIGNED DEFAULT 0;
	DECLARE DirectorId		INT UNSIGNED DEFAULT 0;

	DECLARE StatusId		INT UNSIGNED DEFAULT 0;
	DECLARE StatusExpire	BIGINT UNSIGNED DEFAULT 0;
	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '卡充VIP';
	
	IF LENGTH(CardSerial) != 10 THEN 
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','空');
		LEAVE Main;
	END IF;
	-- 开启事务
	START TRANSACTION;
		SELECT `level_u`,`level_v`,`mobile` INTO UserLevelU,UserLevelV,UserMobile FROM `user` WHERE `id` = UserId FOR UPDATE;
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
		IF UserLevelV > 0 THEN
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','已是VIP');
			ROLLBACK;
			LEAVE Main;
		END IF;

		-- 检查VIP卡
		SET SYSEmpty = 0;
		SELECT id,owner_id,`status` INTO CardId,CardOwnerId,CardStatus FROM `vip_card` WHERE `serial` = CardSerial FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_CARD';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无卡',CardSerial);
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
		SELECT `status`,`director_id` INTO CheifStatus,DirectorId FROM `cheif` WHERE `user_id` = CardOwnerId;
		IF SYSEmpty = 1 OR CheifStatus = 0 THEN
			SET ReturnValue = 'E_CHEIF';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无效总监');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SET SYSError = 0;
		UPDATE `vip_card` SET `rcv_user_id` = UserId,rcv_mobile=UserMobile,status=1,used_time=UNIX_TIMESTAMP() WHERE `id` = CardId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_VIP1';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更改失败');
			ROLLBACK;
			LEAVE Main;
		END IF;

		-- 检查 VIP
		SELECT `user_id`,`invite_code`,`status`
		INTO VipId,VipCode,VipStatus
		FROM `vip` WHERE `user_id` = UserId FOR UPDATE;

		IF SYSEmpty = 1 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新VIP');
			SET SYSEmpty = 0;
			SET VipCode = RAND_STRING(8);
			SET VipStatus = 0;
			INSERT INTO `vip` (`user_id`,`cheif_id`,`director_id`,`invite_code`,`status`,`create_time`,`update_time`,`expire_time`)
			VALUES (UserId,CardOwnerId,DirectorId,VipCode,3,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP());
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_VIP2';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SET VipId = LAST_INSERT_ID();
		ELSE
			SET ReturnValue = 'E_BUFF';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','已是VIP');
			ROLLBACK;
			LEAVE Main;
		END IF;

		-- 新纪录
		INSERT INTO `vip_buff` (`user_id`,`type`,`order_id`,`tally_id`,`coin`,`start_time`,`expire_time`,`create_time`)
		VALUES (UserId,3,CardSerial,0,0.0,UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000,UNIX_TIMESTAMP());
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新状态错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `vip` SET `expire_time` = UNIX_TIMESTAMP() + 31536000,`update_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP记录错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `user` SET `invitor_id` = CardOwnerId,`level_v` = 3 WHERE `id` = UserId;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||

DROP PROCEDURE IF EXISTS `make_coin_vip` |||
CREATE PROCEDURE `make_coin_vip` (
	IN UserId INT UNSIGNED,			-- 用户ID
	IN ClientIp BIGINT UNSIGNED,	-- 操作IP

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '兑换VIP'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE UserCoin		NUMERIC(16,8) DEFAULT 0.0;
	DECLARE UserCoin_New	NUMERIC(16,8);

	DECLARE UserLevelU	TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV	TINYINT UNSIGNED DEFAULT 0;
	DECLARE VipId		INT UNSIGNED DEFAULT 0;
	DECLARE VipCode		VARCHAR(8) DEFAULT '';
	DECLARE VipStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE VipExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE StatusId		INT UNSIGNED DEFAULT 0;
	DECLARE StatusExpire	BIGINT UNSIGNED DEFAULT 0;

	DECLARE TallyId			INT UNSIGNED DEFAULT 0;

	-- 获取cheif ID
	DECLARE InvitoriId		INT DEFAULT 0;
	DECLARE TemInvitoriId	INT DEFAULT 0;
	DECLARE CheifId			INT DEFAULT 0;
	DECLARE DirectorId		INT DEFAULT 0;
	DECLARE Str				TEXT;	
	DECLARE IdIndex			INT DEFAULT 0;
	DECLARE TemStr			VARCHAR(16) DEFAULT '';

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '兑换VIP';
	SET Str = '临时字符串';
	SET TemStr = '';
	
	-- 开启事务
	START TRANSACTION;
		SELECT level_u,level_v,invitor_id  INTO UserLevelU,UserLevelV,InvitoriId FROM `user` WHERE `id` = UserId FOR UPDATE;
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

		IF InvitoriId = UserId THEN 
			SET InvitoriId = 0;
			UPDATE `user` SET invitor_id = InvitoriId WHERE `id` = UserId;
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_INV';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新关系错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;

		-- 检查账务
		SELECT `coin` INTO UserCoin FROM `user_finance` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_FINANCE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无账户');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF UserCoin < 1000.0 THEN
			SET ReturnValue = 'E_COIN';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','阅币不足');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SET UserCoin_New = UserCoin - 1000.0;
		INSERT INTO `tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
		VALUES (UserId,'VIP',UserId,UserCoin,1000.0,UserCoin_New,'兑换VIP',UNIX_TIMESTAMP(),ClientIp);
		IF SYSError = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_TALLY';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
			LEAVE Main;
		END IF;
		SET TallyId = LAST_INSERT_ID();
		UPDATE `user_finance` SET `coin` = UserCoin_New WHERE `user_id` = UserId;

		-- 获取 cheifid
		GetPrev : BEGIN 
			IF InvitoriId <= 0 THEN 
				SET CheifId = 0;
				SET DirectorId = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无邀请人');
				LEAVE GetPrev;
			ELSE 
				WHILE InvitoriId > 0 AND CheifId <= 0 DO 
					SET Str = CONCAT(Str,'#',InvitoriId,'#,');
					SELECT `cheif_id`,director_id INTO CheifId,DirectorId FROM `yuemi_main`.`vip` WHERE `user_id` = InvitoriId;
					IF SYSEmpty = 1 THEN 
						SET ReturnValue = 'E_IN';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无效邀请人');
						ROLLBACK;
						LEAVE Main;
					END IF;
					IF CheifId = 0 AND DirectorId = 0 THEN -- 邀请人是总经理或者野生VIP
						SELECT user_id INTO DirectorId FROM	`yuemi_main`.`director` WHERE `user_id` = InvitoriId AND status != 0;
						IF SYSEmpty = 1 THEN
							SET SYSEmpty = 0;
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人不是总经理');
						ELSE 
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人是总经理');
							LEAVE GetPrev;
						END IF;
					ELSEIF CheifId = 0 AND DirectorId != 0 THEN -- 邀请人是总经理直招或总监
						SELECT director_id INTO DirectorId FROM	`yuemi_main`.`cheif` WHERE `user_id` = InvitoriId AND status != 0;
						IF SYSEmpty = 1 THEN -- 邀请人是直招，同为直招
							SET SYSEmpty = 0;
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人不是总监');
						ELSE -- 邀请人为总监
							SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人是总监');
							SET CheifId = InvitoriId;
							LEAVE GetPrev;
						END IF;
					ELSEIF CheifId != 0 AND DirectorId = 0 THEN -- 不存在
						SET ReturnMessage = CONCAT(ReturnMessage,'->','不存在');
					ELSE -- 全部都有
						SET ReturnMessage = CONCAT(ReturnMessage,'->','全部都有');
						LEAVE GetPrev;
					END IF;
					SELECT invitor_id INTO TemInvitoriId FROM `user` WHERE `id` = InvitoriId;
					IF SYSEmpty = 1 THEN 
						SET ReturnValue = 'E_USER';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请人出错');
						ROLLBACK;
						LEAVE Main;
					END IF;
					IF TemInvitoriId = InvitoriId THEN 
						UPDATE `yuemi_main`.`user` SET invitor_id = 0 WHERE `id` = InvitoriId;
						SET CheifId = 0;
						SET DirectorId = 0;
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无邀请人');
						LEAVE GetPrev;
					END IF;
					-- 判断邀请人的邀请人ID是否正确
					SET TemStr = CONCAT('#',TemInvitoriId,'#');
					SELECT find_in_set(TemInvitoriId,Str) INTO IdIndex;
					IF IdIndex > 0 THEN 
						UPDATE `yuemi_main`.`user` SET invitor_id = 0 WHERE `id` = InvitoriId;
						SET CheifId = 0;
						SET DirectorId = 0;
						SET ReturnMessage = CONCAT(ReturnMessage,'->','无邀请人');
						LEAVE GetPrev;
					END IF;
					SET InvitoriId = TemInvitoriId;
				END WHILE;
			END IF;
		END ;

		-- 检查 VIP
		SELECT `user_id`,`invite_code`,`status`,`expire_time`
		INTO VipId,VipCode,VipStatus,VipExpire
		FROM `vip` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新VIP');
			SET SYSEmpty = 0;
			SET VipCode = RAND_STRING(8);
			SET VipStatus = 0;
			SET VipExpire = UNIX_TIMESTAMP();
			INSERT INTO `vip` (`user_id`,`cheif_id`,`director_id`,`invite_code`,`status`,`create_time`,`update_time`,`expire_time`)
			VALUES (UserId,CheifId,DirectorId,VipCode,4,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP());
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_VIP';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新纪录错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SET VipId = LAST_INSERT_ID();
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老VIP');
			-- 检查Status
			SELECT `id`,`expire_time` INTO StatusId,StatusExpire FROM `vip_buff` WHERE `user_id` = UserId ORDER BY `expire_time` DESC LIMIT 1;
			IF SYSEmpty = 1 THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无状态');
				SET SYSEmpty = 0;
				UPDATE `vip` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
				SET VipStatus = 0;
				SET VipExpire = UNIX_TIMESTAMP();
				SET StatusId = 0;
				SET StatusExpire = VipExpire;
			ELSE
				SET ReturnMessage = CONCAT(ReturnMessage,'->','有状态');
				IF StatusExpire != VipExpire THEN
					SET ReturnMessage = CONCAT(ReturnMessage,'->','重新同步');
					IF StatusExpire > UNIX_TIMESTAMP() THEN
						SET ReturnMessage = CONCAT(ReturnMessage,'->','还有效');
						UPDATE `vip` SET `status` = 1,`update_time` = UNIX_TIMESTAMP(),`expire_time` = StatusExpire WHERE `user_id` = UserId;
						SET VipExpire = StatusExpire;
					ELSE
						SET ReturnMessage = CONCAT(ReturnMessage,'->','过期了');
						UPDATE `vip` SET `status` = 0,`update_time` = UNIX_TIMESTAMP(),`expire_time` = StatusExpire WHERE `user_id` = UserId;
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
		VALUES (UserId,4,'',TallyId,1000.0,VipExpire,VipExpire + 31536000,UNIX_TIMESTAMP());
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新状态错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `vip` SET `status` = 4,`expire_time` = VipExpire + 31536000,`update_time` = UNIX_TIMESTAMP() WHERE `user_id` = UserId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','VIP记录错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		UPDATE `user` SET `level_v` = 4 WHERE `id` = UserId;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||

DELIMITER ;