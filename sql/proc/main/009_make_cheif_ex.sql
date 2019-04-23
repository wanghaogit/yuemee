/*
	成为总监相关存储过程
*/
DELIMITER |||

/*
	后台卡位总监
*/
DROP PROCEDURE IF EXISTS `make_money_cheif_ex` |||
CREATE PROCEDURE `make_money_cheif_ex` (
	IN UserMobile VARCHAR(16),			-- 手机号
--	IN SubordId	  INT,					-- 从属经理
	IN OrderId VARCHAR(16),				-- 订单号
	IN CertName VARCHAR(32),			-- 姓名
	IN CertNumber VARCHAR(18),			-- 身份证
	IN AddrRegion INT UNSIGNED,			-- 地区
	IN AddrDetail VARCHAR(128),			-- 地址
	IN BankTypeId INT UNSIGNED,			-- 银行
	IN BankNumber VARCHAR(32),			-- 卡号
	IN IsGiveVipCard TINYINT UNSIGNED,	-- 是否给10张VIP卡
	IN ClientIp BIGINT UNSIGNED,	-- 操作IP

	OUT UserId INT,
	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT '直升总监'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	-- 用户信息
	DECLARE UserLevelU		TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV		TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelC		TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelD		TINYINT UNSIGNED DEFAULT 0;

	-- 总经理状态
	DECLARE BankName		VARCHAR(32) DEFAULT '';
	
	DECLARE InviteCode		VARCHAR(8) DEFAULT '';
	DECLARE CardId			INT DEFAULT 0;
	DECLARE CardSerial		VARCHAR(32) DEFAULT '';

	DECLARE VipId		INT UNSIGNED DEFAULT 0;
	DECLARE VipCode		VARCHAR(8) DEFAULT '';
	DECLARE VipStatus	TINYINT UNSIGNED DEFAULT 0;
	DECLARE VipExpire	BIGINT UNSIGNED DEFAULT 0;
	DECLARE StatusId		INT UNSIGNED DEFAULT 0;
	DECLARE StatusExpire	BIGINT UNSIGNED DEFAULT 0;

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
	SET ReturnMessage = '直升总监';
	-- 检查参数
	IF LENGTH(UserMobile) != 11 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','手机错');
		LEAVE Main;
	END IF;
	IF LENGTH(OrderId) != 16 THEN
		SET OrderId = NEW_ORDER_ID('K','C');
	END IF;
	IF LENGTH(CertName) < 1 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','姓名错');
		LEAVE Main;
	END IF;
	IF LENGTH(CertNumber) != 18 THEN
		SET ReturnValue = 'E_PARAM';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','身份证错');
		LEAVE Main;
	END IF;
	IF BankTypeId > 0 THEN
		SELECT `name` INTO BankName FROM `bank` WHERE `id` = BankTypeId;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_PARAM';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','银行错');
			LEAVE Main;
		END IF;
	END IF;
	
	-- 开启事务
	START TRANSACTION;
		SELECT `id`,`level_u`,`level_v`,`level_c`,`level_d` INTO UserId,UserLevelU,UserLevelV,UserLevelC,UserLevelD FROM `user` WHERE `mobile` = UserMobile FOR UPDATE;
		IF SYSEmpty = 1 THEN
			-- 创建新用户
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新用户');
			SET SYSEmpty = 0;
			INSERT INTO `user` (`mobile`,`password`,`name`,`reg_time`,`reg_from`,`level_u`)
			VALUES (UserMobile,SHA1(CONCAT('user.yuemee.com',UserMobile)),CertName,UNIX_TIMESTAMP(),ClientIp,1);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新用户错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SET UserId = LAST_INSERT_ID();
			-- 创建账户
			INSERT INTO `user_finance` (`user_id`,`thew_launch`,`thew_target`)
			VALUES (UserId,UNIX_TIMESTAMP(),300.0);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新账户错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SET UserLevelU = 1;
			SET UserLevelV = 0;
			SET UserLevelC = 0;
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老用户');
			IF UserLevelD > 0 THEN
				SET ReturnValue = 'E_ROLE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','是总经理');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		
		-- 实名
		SET ReturnMessage = CONCAT(ReturnMessage,'->','实名');
		INSERT INTO `user_cert` (`user_id`,`card_pic1`,`card_pic2`,`card_no`,`card_name`,`card_exp`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`,`status`)
		VALUES (UserId,'','',CertNumber,CertName,'0000-00-00',UNIX_TIMESTAMP(),ClientIp,1,UNIX_TIMESTAMP(),ClientIp,2);
		IF SYSError = 1 THEN
			SET SYSError = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','已实名');
		END IF;
		
		-- 银行卡
		IF BankTypeId > 0 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','插银行卡');
			INSERT INTO `user_bank` (`user_id`,`bank_id`,`bank_name`,`card_no`,`status`,`create_time`,`create_from`,`audit_user`,`audit_time`,`audit_from`)
			VALUES (UserId,BankTypeId,BankName,BankNumber,2,UNIX_TIMESTAMP(),ClientIp,1,UNIX_TIMESTAMP(),ClientIp);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','银行卡错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		
		-- 地址
		IF AddrRegion > 0 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','插地址');
			INSERT INTO `user_address` (`user_id`,`region_id`,`address`,`contacts`,`mobile`,`is_default`,`status`,`create_time`,`create_from`)
			VALUES (UserId,AddrRegion,AddrDetail,CertName,UserMobile,1,1,UNIX_TIMESTAMP(),ClientIp);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','地址错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		
		-- 先检查VIP
		SELECT `user_id`,`invite_code`,`status`,`expire_time`
		INTO VipId,VipCode,VipStatus,VipExpire
		FROM `vip` WHERE `user_id` = UserId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','非VIP');
			SET SYSEmpty = 0;
			SET InviteCode = RAND_STRING(8);
			-- 立即给一年VIP
			SET ReturnMessage = CONCAT(ReturnMessage,'->','捆绑VIP');
			INSERT INTO `vip` (`user_id`,`cheif_id`,`invite_code`,`status`,`create_time`,`update_time`,`expire_time`)
			VALUES (UserId,0,InviteCode,2,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_VIP';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','捆绑失败');
				ROLLBACK;
				LEAVE Main;
			END IF;
			INSERT INTO `vip_buff` (`user_id`,`type`,`order_id`,`tally_id`,`coin`,`start_time`,`expire_time`,`create_time`)
			VALUES (UserId,2,OrderId,0,0.0,UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000,UNIX_TIMESTAMP());
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_VIP';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','VBUF失败');
				ROLLBACK;
				LEAVE Main;
			END IF;
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','是VIP');
			
			UPDATE `vip` SET `cheif_id` = 0 WHERE `user_id` = UserId;
			SET SYSError = 0;
			-- 继承原 VIP 邀请码
			SET InviteCode = VipCode;
			-- 赠送一年
			SET ReturnMessage = CONCAT(ReturnMessage,'->','赠送一年');
			INSERT INTO `vip_buff` (`user_id`,`type`,`order_id`,`tally_id`,`coin`,`start_time`,`expire_time`,`create_time`)
			VALUES (UserId,2,OrderId,0,0.0,VipExpire,VipExpire + 31536000,UNIX_TIMESTAMP());
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_VIP';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','VBUF失败');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		-- 总监
		SET ReturnMessage = CONCAT(ReturnMessage,'->','插总监');
		INSERT INTO `cheif` (`user_id`,`director_id`,`status`,`create_time`,`update_time`,`expire_time`)
		VALUES (UserId,0,3,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000);
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','总监错',UserId);
			ROLLBACK;
			LEAVE Main;
		END IF;

		-- BUFF
		SET ReturnMessage = CONCAT(ReturnMessage,'->','插BUFF');
		INSERT INTO `cheif_buff` (`user_id`,`type`,`order_id`,`pay_channel`,`pay_status`,`pay_time`,`money`,`start_time`,`expire_time`,`create_time`,`create_from`)
		VALUES (UserId,3,OrderId,2,2,UNIX_TIMESTAMP(),3999.00,UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000,UNIX_TIMESTAMP(),ClientIp);
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','BUFF错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		-- 更新状态
		SET ReturnMessage = CONCAT(ReturnMessage,'->','更新状态');
		UPDATE `user` SET `level_c` = 3 WHERE `id` = UserId;

		-- 赠送10张VIP卡
		IF IsGiveVipCard > 0 THEN
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
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','不送VIP卡');
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
				SET `vip`.`cheif_id` = UserId ,`vip`.`director_id` = DirectorId 
				WHERE `vip`.`user_id` = TempUserId AND `user`.`level_v` > 0;
			END IF;
		END WHILE;

	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
