DELIMITER |||

/*
 微信登陆
*/
DROP PROCEDURE IF EXISTS `login_wechat_ex` |||
CREATE PROCEDURE `login_wechat_ex` (
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
	OUT UserToken CHAR(16),					/* 登陆令牌 */
	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
)
LANGUAGE SQL 
NOT DETERMINISTIC SQL 
SECURITY INVOKER 
CONTAINS SQL READS SQL DATA MODIFIES SQL DATA 
COMMENT '公众号微信登陆'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;
	-- 定义局部变量
	DECLARE InvId INT UNSIGNED DEFAULT 0;
	DECLARE InvSeed INT UNSIGNED DEFAULT 0;
	DECLARE InvParam VARCHAR(64) DEFAULT '';
	DECLARE UserMobile VARCHAR(11) DEFAULT '';

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
		SELECT `id`,`user_id`,`invitor_id`,`tag_seed`,`tag_param`,`mobile`
		INTO WechatId,UserId,InvId,InvSeed,InvParam,UserMobile
		FROM `user_wechat`
		WHERE `union_id` = WxUnionId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET SYSEmpty = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新');
			IF InvitorId > 0 THEN
				SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请(',InvitorId,',',InvitorSeed,',',InvitorParam,')');
			END IF;
			INSERT INTO `user_wechat` (`user_id`,`invitor_id`,`web_open_id`,`union_id`,`name`,`avatar`,`gender`,`create_time`,`create_from`,`tag_seed`,`tag_param`,`mobile`)
			VALUES (0,InvitorId,WxOpenId,WxUnionId,WxName,WxAvatar,WxGender,UNIX_TIMESTAMP(),ClientIp,InvitorSeed,InvitorParam,UserMobile);
			IF SYSError = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_INSERT';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','插入失败');
				LEAVE Main;
			END IF;
			SET WechatId = LAST_INSERT_ID();
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老');
			-- IMPORTANT : 修复邀请关系
			IF InvId <= 0 AND InvitorId > 0 THEN
				SET InvId = InvitorId;
				SET InvSeed = InvitorSeed;
				SET InvParam = InvitorParam;
				SET ReturnMessage = CONCAT(ReturnMessage,'->','邀请(',InvId,',',InvSeed,',',InvParam,')');
			END IF;
			UPDATE `user_wechat` 
			SET `update_time` = UNIX_TIMESTAMP(),
				`update_from` = ClientIp,
				`invitor_id` = InvId,
				`tag_seed` = InvSeed,
				`tag_param` = InvParam,
				`mobile`	= UserMobile
			 WHERE `id` = WechatId;

			IF UserId = 0 THEN	-- 未绑定
				COMMIT;
				SET ReturnValue = 'OK';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
				LEAVE Main;
			END IF;
			SELECT `token` INTO UserToken FROM `user` WHERE `id` = UserId;
			IF SYSEmpty = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_USER';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','用户错');
				LEAVE Main;
			END IF;
			SET UserToken = RAND_STRING(16);
			UPDATE `user` SET `token` = UserToken WHERE `id` = UserId;
			IF SYSEmpty = 1 THEN
				ROLLBACK;
				SET ReturnValue = 'E_TOKEN';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','令牌错');
				LEAVE Main;
			END IF;
		END IF;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
