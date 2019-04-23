DELIMITER |||

/*
 设备注册
*/
DROP PROCEDURE IF EXISTS `device_register` |||
CREATE PROCEDURE `device_register` (
	IN hw_udid VARCHAR(40),				/* 设备ID */
	IN hw_imei VARCHAR(40),				/* 设备ID */
	IN hw_imsi VARCHAR(40),				/* 设备ID */
	IN hw_vender VARCHAR(32),				/* 设备ID */
	IN hw_model VARCHAR(32),				/* 设备ID */
	IN hw_width SMALLINT UNSIGNED,				/* 设备ID */
	IN hw_height SMALLINT UNSIGNED,				/* 设备ID */
	IN sys_style TINYINT UNSIGNED,				/* 设备ID */
	IN sys_version VARCHAR(16),				/* 设备ID */
	IN app_version INT UNSIGNED,				/* 设备ID */
	IN oa_version INT UNSIGNED,
	IN gps_lng FLOAT,
	IN gps_lat FLOAT,
	IN gps_region INT UNSIGNED,
	IN user_token VARCHAR(16),
	IN user_from BIGINT UNSIGNED,
	
	OUT DeviceId INT UNSIGNED,				/* 设备ID */
	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
)
LANGUAGE SQL 
NOT DETERMINISTIC SQL 
SECURITY INVOKER 
CONTAINS SQL READS SQL DATA MODIFIES SQL DATA 
COMMENT '设备注册'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;
	-- 定义局部变量
	DECLARE VenderId INT UNSIGNED DEFAULT 0;
	DECLARE ModelId INT UNSIGNED DEFAULT 0;
	DECLARE UserId INT UNSIGNED DEFAULT 0;
	DECLARE TempId INT UNSIGNED DEFAULT 0;

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	-- 初始化变量
	SET ReturnValue = '';
	SET ReturnMessage = '注册A';
	SET DeviceId = 0;
	-- 检查参数

	-- 开始事务
	START TRANSACTION;
		SELECT `id` INTO DeviceId FROM `device` WHERE `udid` = hw_udid FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET DeviceId = 0;
			SET SYSEmpty = 0;
		END IF;
		IF DeviceId < 1 THEN	-- 注册新设备
			SET ReturnMessage = CONCAT(ReturnMessage,'->','新设备');
			IF hw_vender != '' AND hw_model != '' THEN
				SELECT `id` INTO VenderId FROM `device_vender` WHERE `name` = hw_vender;
				IF SYSEmpty = 1 OR VenderId < 1 THEN
					SET SYSEmpty = 0;
					INSERT INTO `device_vender` (`name`) VALUES (hw_vender);
					IF SYSError = 1 THEN
						SET ReturnValue = 'ERROR';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','V错');
						ROLLBACK;
						LEAVE Main;
					END IF;
					SET VenderId = LAST_INSERT_ID();
					SET ReturnMessage = CONCAT(ReturnMessage,'->','VID(',VenderId,')');
				END IF;
				SELECT `id` INTO ModelId FROM `device_model` WHERE `vendor_id` = VenderId AND `name` = hw_model;
				IF SYSEmpty = 1 OR ModelId < 1 THEN
					SET SYSEmpty = 0;
					INSERT INTO `device_model` (`vendor_id`,`name`) VALUES (VenderId,hw_model);
					IF SYSError = 1 THEN
						SET ReturnValue = 'ERROR';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','M错');
						ROLLBACK;
						LEAVE Main;
					END IF;
					SET ModelId = LAST_INSERT_ID();
					SET ReturnMessage = CONCAT(ReturnMessage,'->','MID(',ModelId,')');
				END IF;
			END IF;
			INSERT INTO `device` VALUES (
				NULL,
				hw_udid,
				hw_imei,
				hw_imsi,
				sys_style,
				VenderId,
				ModelId,
				sys_version,
				app_version,
				oa_version,
				hw_width,
				hw_height,
				POINT(gps_lng,gps_lat),
				gps_region,
				UNIX_TIMESTAMP(),
				user_from,
				0,
				0
			);
			SET DeviceId = LAST_INSERT_ID(); 
			IF SYSError = 1 THEN
				SET ReturnValue = 'ERROR';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','D错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			SET DeviceId = LAST_INSERT_ID();
			SET ReturnMessage = CONCAT(ReturnMessage,'->','DID(',DeviceId,')');
		ELSE		-- 更新老设备
			SET ReturnMessage = CONCAT(ReturnMessage,'->','老设备');
			SET ReturnMessage = CONCAT(ReturnMessage,'->','DID(',DeviceId,')');
			UPDATE `device` SET 
				`version_sys` = sys_version,
				`version_app` = app_version,
				`version_oa` = oa_version,
				`gps` = POINT(gps_lng,gps_lat),
				`region_id` = gps_region,
				`update_time` = UNIX_TIMESTAMP(),
				`update_from` = user_from
			WHERE `id` = DeviceId;
			IF SYSError = 1 THEN
				SET ReturnValue = 'ERROR';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		-- 检查用户
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查绑定');
		IF user_token != '' THEN
			SELECT `id` INTO UserId FROM `user` WHERE `token` = user_token;
			IF SYSEmpty = 1 THEN
				SET SYSEmpty = 0;
			END IF;
			IF UserId > 0 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','UID(' , UserId , ')');
				SELECT `id` INTO TempId FROM `device_user` WHERE `device_id` = DeviceId AND `user_id` = UserId;
				IF SYSEmpty = 1 OR TempId < 1 THEN
					SET ReturnMessage = CONCAT(ReturnMessage,'->','新绑定');
					SET SYSEmpty = 0;
					INSERT INTO `device_user` VALUES (
						NULL,
						DeviceId,
						UserId,
						UNIX_TIMESTAMP(),
						user_from,
						NULL,
						0);
					IF SYSError = 1 THEN
						SET ReturnValue = 'ERROR';
						SET ReturnMessage = CONCAT(ReturnMessage,'->','绑定错');
						ROLLBACK;
						LEAVE Main;
					END IF;
				ELSE
					SET ReturnMessage = CONCAT(ReturnMessage,'->','已绑定');
					UPDATE `device_user` SET 
						`update_time` = UNIX_TIMESTAMP(),
						`update_from` = user_from
					WHERE `id` = TempId;
				END IF;
			END IF;
		END IF;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
