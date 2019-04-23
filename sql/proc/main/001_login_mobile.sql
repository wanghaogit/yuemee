DELIMITER |||

/*
 手机号码直接登陆
*/
DROP PROCEDURE IF EXISTS `login_mobile` |||
CREATE PROCEDURE `login_mobile` (
	IN UserMobile VARCHAR(16),					/* 用户手机号码 */
	IN VerifyCode VARCHAR(8),					/* 手机验证码 */
	IN ClientIp BIGINT UNSIGNED,			/* 登陆IP */

	OUT WechatId INT UNSIGNED,				/* 用户ID */
	OUT UserId INT UNSIGNED,				/* 用户ID */
	OUT UserToken CHAR(24),					/* 登陆令牌 */
	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
)
LANGUAGE SQL 
NOT DETERMINISTIC SQL 
SECURITY INVOKER 
CONTAINS SQL READS SQL DATA MODIFIES SQL DATA 
COMMENT '手机登陆'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;
	-- 定义局部变量
	DECLARE VerifyId INT UNSIGNED DEFAULT 0;
	DECLARE UserLevelU TINYINT UNSIGNED DEFAULT 0;

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
	SET ReturnMessage = '手机登陆';

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

	-- 开始事务
	START TRANSACTION;
		SELECT `id`,`level_u` INTO UserId,UserLevelU FROM `user` WHERE `mobile` = UserMobile ORDER BY `id` ASC LIMIT 0,1 FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新用户');
			SET UserToken = RAND_STRING(16);
			INSERT INTO `user` (`invitor_id`,`mobile`,`password`,`token`,`name`,`level_u`,`reg_time`,`reg_from`)
			VALUES (0,UserMobile,'',UserToken,UserMobile,1,UNIX_TIMESTAMP(),ClientIp);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_USER';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','插用户错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SET UserId = LAST_INSERT_ID();
			INSERT INTO `user_finance` (user_id) VALUES (UserId);
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_FINANCE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','插账户错');
				ROLLBACK;
				LEAVE Main;
			END IF;

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
		ELSEIF UserLevelU = 0 THEN
			SET ReturnValue = 'E_FOBIDDEN';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','黑名单');
			ROLLBACK;
			LEAVE Main;
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老用户');
			SET UserToken = RAND_STRING(16);
			UPDATE `user` SET `token` = UserToken WHERE `id` = UserId;
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_TOKEN';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','令牌错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SELECT `id` INTO WechatId FROM `user_wechat` WHERE `user_id` = UserId;
			IF SYSEmpty = 1 THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无微信');
			END IF;
		END IF;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
