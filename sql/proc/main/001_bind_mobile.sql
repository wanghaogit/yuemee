
DELIMITER |||
/*
 微信登陆后的手机号码绑定
*/
DROP PROCEDURE IF EXISTS `bind_mobile` |||
CREATE PROCEDURE `bind_mobile` (
	IN WxUnionId VARCHAR(40),				/* 微信UnionId */
	IN UserMobile VARCHAR(16),				/* 用户手机号码 */
	IN VerifyCode VARCHAR(8),				/* 手机验证码 */
	IN ClientIp BIGINT UNSIGNED,			/* 登陆IP */

	OUT WechatId INT UNSIGNED,				/* 用户ID */
	OUT UserId INT UNSIGNED,				/* 用户ID */
	OUT UserToken VARCHAR(16),					/* 登陆令牌 */
	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
)
LANGUAGE SQL 
NOT DETERMINISTIC SQL 
SECURITY INVOKER 
CONTAINS SQL READS SQL DATA MODIFIES SQL DATA 
COMMENT '手机绑定'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;
	-- 定义局部变量
	DECLARE VerifyId INT UNSIGNED DEFAULT 0;
	DECLARE UserInvitorId INT UNSIGNED DEFAULT 0;
	DECLARE UserLevelU TINYINT UNSIGNED DEFAULT 0;
	DECLARE TmpId INT UNSIGNED DEFAULT 0;

	DECLARE InvId INT UNSIGNED DEFAULT 0;
	DECLARE InvSeed INT UNSIGNED DEFAULT 0;
	DECLARE InvParam VARCHAR(64) DEFAULT '';

	DECLARE Coin_Old NUMERIC(16,8);
	DECLARE Coin_New NUMERIC(16,8);

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET UserId = 0;
	SET WechatId = 0;
	SET UserToken = '';
	SET ReturnValue = '';
	SET ReturnMessage = '手机绑定';

	-- 检查参数
	-- IF UserMobile = '18601122863' OR VerifyCode = '1342' THEN
	-- 	SET ReturnMessage = CONCAT(ReturnMessage,'->','特权号码');
	-- ELSE
	-- 	SELECT `id` 
	-- 	INTO VerifyId 
	-- 	FROM `sms` 
	-- 	WHERE `mobile` = UserMobile 
	-- 	  AND `code` = VerifyCode
	-- 	  AND `expire_time` > UNIX_TIMESTAMP();
	-- 	IF SYSEmpty = 1 THEN
	-- 		SET ReturnValue = 'E_VERIFY';
	-- 		SET ReturnMessage = CONCAT(ReturnMessage,'->','验证码错');
	-- 		LEAVE Main;
	-- 	END IF;
	-- END IF;

	-- 检查绑定
	SELECT `id`,`user_id`,`invitor_id`,`tag_seed`,`tag_param`
	INTO WechatId,UserId,InvId,InvSeed,InvParam
	FROM `user_wechat` 
	WHERE `union_id` = WxUnionId;

	IF SYSEmpty = 1 OR SYSError = 1 THEN
		SET ReturnValue = 'E_UNIONID';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','微信错');
		LEAVE Main;
	END IF;
	
	IF UserId > 0 THEN
		SET ReturnMessage = CONCAT(ReturnMessage,'->','已绑定');
		SELECT `token`,`level_u` 
		INTO UserToken,UserLevelU
		FROM `user` 
		WHERE `id` = UserId;
		IF SYSEmpty = 1 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','用户已删');
			SET SYSEmpty = 0;
			SET UserId = 0;
			UPDATE `user_wechat` SET `user_id` = 0 WHERE `id` = WechatId;
		END IF;
	ELSE
		SET ReturnMessage = CONCAT(ReturnMessage,'->','未绑定');
	END IF;
	IF UserId > 0 THEN
		SET ReturnMessage = CONCAT(ReturnMessage,'->','老用户');
		IF UserLevelU = 0 THEN
			SET ReturnValue = 'E_FOBIDDEN';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','被禁闭');
			LEAVE Main;
		ELSE
			SET ReturnValue = 'OK';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','已绑定');
			LEAVE Main;
		END IF;
	END IF;

	-- 开始事务
	START TRANSACTION;
		UPDATE `user_wechat` SET `update_time` = NOW(),`update_from` = ClientIp WHERE `id` = WechatId;
		SET SYSEmpty = 0;
		SELECT `id`,`invitor_id`,`token` INTO UserId,UserInvitorId,UserToken FROM `user` WHERE `mobile` = UserMobile ORDER BY `id` ASC LIMIT 0,1;
		IF SYSEmpty = 1 OR UserId < 1 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新手机');
			SET UserToken = RAND_STRING(16);
			-- 全新用户
			INSERT INTO `user` (invitor_id,mobile,password,token,`name`,level_u,level_v,level_c,level_d,level_t,level_a,`avatar`,`gender`,`reg_time`,`reg_from`,`reg_seed`,`reg_param`)
			SELECT invitor_id,UserMobile,'',UserToken,`name`,1,0,0,0,0,0,'',`gender`,`create_time`,`create_from`,`tag_seed`,`tag_param`
			FROM `user_wechat` WHERE `id` = WechatId;
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_USER';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','插用户错');
				LEAVE Main;
			END IF;
			SET UserId = LAST_INSERT_ID();
			INSERT INTO `user_finance` (user_id) VALUES (UserId);
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_FINANCE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','插账户错');
				LEAVE Main;
			END IF;
			UPDATE `user_wechat` SET `mobile` = UserMobile, `user_id` = UserId WHERE `id` = WechatId;
			
			-- 新用户注册给 0.05 阅币
			SET ReturnMessage = CONCAT(ReturnMessage,'->','给奖励');
			SET Coin_Old = 0;
			SET Coin_New = Coin_Old + 0.05;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新' , Coin_New);
			UPDATE `user_finance` SET `coin` = Coin_New WHERE `user_id` = UserId;
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_FINANCE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','账户错');
				LEAVE Main;
			END IF;
			INSERT INTO `tally_coin` (`user_id`,`source`,`order_id`,`val_before`,`val_delta`,`val_after`,`message`,`create_time`,`create_from`)
			VALUES (UserId,'REGISTER','',Coin_Old,0.05,Coin_New,'注册奖励',UNIX_TIMESTAMP(),ClientIp);
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_TALLY';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','流水错');
				LEAVE Main;
			END IF;
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老手机');
			-- 检查重复绑定
			SELECT `id` INTO TmpId FROM `user_wechat` WHERE `user_id` > 0 AND `user_id` = UserId ORDER BY `id` ASC LIMIT 0,1;
			IF SYSEmpty = 1 THEN
				SET SYSEmpty = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','没绑过');
			ELSEIF TmpId > 0 THEN
				ROLLBACK;
				SET ReturnValue = 'E_DUPLICATE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','重复绑定');
				SET ReturnMessage = CONCAT(ReturnMessage,'->','UID(',UserId,')');
				SET ReturnMessage = CONCAT(ReturnMessage,'->','WID(',TmpId,')');
				LEAVE Main;
			END IF;
			IF UserToken = '' THEN
				SET UserToken = RAND_STRING(16);
				SET ReturnMessage = CONCAT(ReturnMessage,'->','新Token');
				UPDATE `user` SET `token` = UserToken WHERE `id` = UserId;
			END IF;

			-- 重新同步邀请关系
			IF UserInvitorId <= 0 AND InvId > 0 THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请关系');
				UPDATE `user` SET `invitor_id` = InvId WHERE `id` = UserId;
				UPDATE `user` SET `reg_seed` = InvSeed,`reg_param` = InvParam WHERE `id` = UserId;
			END IF;
			
			-- 绑定
			UPDATE `user_wechat` SET `mobile` = UserMobile, `user_id` = UserId WHERE `id` = WechatId;
		END IF;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
