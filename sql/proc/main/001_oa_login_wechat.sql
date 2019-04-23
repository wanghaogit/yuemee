DELIMITER |||

/*
 微信登陆
*/
DROP PROCEDURE IF EXISTS `oa_login_wechat` |||
CREATE PROCEDURE `oa_login_wechat` (
	IN WxOpenId VARCHAR(64),				/* 微信OpenId */
	IN WxUnionId VARCHAR(64),				/* 微信UnionId */
	IN WxName VARCHAR(32),					/* 微信昵称 */
	IN WxAvatar VARCHAR(256),				/* 微信头像 */
	IN WxGender TINYINT,					/* 微信性别 */
	IN InvitorId INT UNSIGNED,				/* 邀请人ID */
	IN InvitorSeed INT UNSIGNED,			/* 邀请人种子 */
	IN InvitorParam VARCHAR(64),			/* 邀请人参数 */
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
COMMENT '微信登陆'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;
	-- 定义局部变量
	DECLARE InvId INT UNSIGNED DEFAULT 0;
	DECLARE InvSeed INT UNSIGNED DEFAULT 0;
	DECLARE InvParam VARCHAR(64) DEFAULT '';
	DECLARE ExTime	   BIGINT  UNSIGNED DEFAULT 0;

	DECLARE UserLevelU TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelC TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelD TINYINT UNSIGNED DEFAULT 0;

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET UserId = 0;
	SET WechatId = 0;
	SET UserToken = '';
	SET ReturnValue = '';
	SET ReturnMessage = '微信登陆';

	-- 检查参数

	-- 开始事务
	START TRANSACTION;
		SELECT `id`,`user_id`,`invitor_id`,`tag_seed`,`tag_param`
		INTO WechatId,UserId,InvId,InvSeed,InvParam
		FROM `user_wechat`
		WHERE `union_id` = WxUnionId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','非用户');
			ROLLBACK;
			LEAVE Main;
		END IF;

		IF UserId = 0 THEN	-- 未绑定
			ROLLBACK;
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','非用户');
			LEAVE Main;
		END IF;

		
		SELECT `level_c`,`level_d`,`token` INTO UserLevelC,UserLevelD,UserToken FROM `user` WHERE `id` = UserId FOR UPDATE;
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
		END IF;
		
		-- IMPORTANT : 修复邀请关系
		IF InvId <= 0 AND InvitorId > 0 THEN
			SET InvId = InvitorId;
			SET InvSeed = InvitorSeed;
			SET InvParam = InvitorParam;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请(',InvId,',',InvSeed,',',InvParam,')');
		END IF;
		UPDATE `user_wechat` 
		SET `app_open_id` = WxOpenId,
			`update_time` = UNIX_TIMESTAMP(),
			`update_from` = ClientIp,
			`invitor_id` = InvId,
			`tag_seed` = InvSeed,
			`tag_param` = InvParam
		WHERE `id` = WechatId;
		 
		SET UserToken = RAND_STRING(16);
		UPDATE `user` SET `token` = UserToken WHERE `id` = UserId;
		IF SYSEmpty = 1 THEN
			ROLLBACK;
			SET ReturnValue = 'E_TOKEN';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','令牌错');
			LEAVE Main;
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
