DELIMITER |||

/*
 手机号码直接登陆
*/
DROP PROCEDURE IF EXISTS `oa_login_mobile` |||
CREATE PROCEDURE `oa_login_mobile` (
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
COMMENT '手机登陆OA'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;
	-- 定义局部变量
	DECLARE VerifyId INT UNSIGNED DEFAULT 0;
	DECLARE UserLevelU TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelC TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelD TINYINT UNSIGNED DEFAULT 0;
	DECLARE ExTime	   BIGINT  UNSIGNED DEFAULT 0;

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
	SET ReturnMessage = '手机登陆OA';

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

	SET SYSError = 0;
	-- 开始事务
	START TRANSACTION;
		SELECT `id`,`level_u`,`level_c`,`level_d` INTO UserId,UserLevelU,UserLevelC,UserLevelD FROM `user` WHERE `mobile` = UserMobile ORDER BY `id` ASC LIMIT 0,1;
		IF SYSEmpty = 1 THEN -- 没有这个用户
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','非用户');
			ROLLBACK;
			LEAVE Main;
		ELSEIF UserLevelU = 0 THEN
			SET ReturnValue = 'E_FOBIDDEN';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','黑名单');
			ROLLBACK;
			LEAVE Main;
		-- 检查用户权限
		ELSEIF UserLevelC = 0 AND UserLevelD = 0 THEN 
			SET ReturnValue = 'E_LEVEL';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','权限不足');
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
				SET SYSEmpty = 0;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','无微信');
			END IF;
		END IF;
		
		IF UserLevelD = 0 AND UserLevelC != 0 THEN 
			SELECT `expire_time` INTO ExTime FROM `cheif` WHERE `user_id` = UserId;
			IF SYSEmpty = 1 THEN -- 没有这个用户
				SET ReturnValue = 'E_CHEIF';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','非总监');
				ROLLBACK;
				LEAVE Main;
			END IF;
			IF ExTime < UNIX_TIMESTAMP() THEN 
				SET ReturnValue = 'E_EXPIRE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','总监过期');
				ROLLBACK;
				LEAVE Main;
			END IF;
		ELSEIF UserLevelD != 0 AND UserLevelC = 0  THEN 
			SELECT `expire_time` INTO ExTime FROM `director` WHERE `user_id` = UserId;
			IF SYSEmpty = 1 THEN -- 没有这个用户
				SET ReturnValue = 'E_DIRECTOR';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','非总经理');
				ROLLBACK;
				LEAVE Main;
			END IF;
			IF ExTime < UNIX_TIMESTAMP() THEN 
				SET ReturnValue = 'E_EXPIRE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','总经理过期');
				ROLLBACK;
				LEAVE Main;
			END IF;
		ELSE
			SET ReturnValue = 'E_DUPLICATE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','重复身份');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
