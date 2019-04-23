/*
 *	拷贝素材到spu
 *	作者：王少宏
 *	日期：2018-05-15
 */
DELIMITER |||
DROP PROCEDURE IF EXISTS `copy_to_spu` |||
CREATE PROCEDURE `copy_to_spu` (
	IN SpuId	INT		UNSIGNED,		
	IN UserId	INT		UNSIGNED,	-- 操作ID
	IN ClientIp BIGINT	UNSIGNED,	-- 操作IP

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT 'SPU素材拷贝'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;
	
	DECLARE SkuId	 INT DEFAULT 0;
	DECLARE ExtSkuId INT DEFAULT 0;
	DECLARE ExtSpuId INT DEFAULT 0;
	

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	SELECT id INTO  SpuId FROM `spu` WHERE `id` = SpuId;
	IF SYSEmpty = 1 THEN 
		SET ReturnValue = 'E_SPU';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','SPU错误');
		LEAVE Main;
	END IF;

	SELECT EP.`id`,EK.`id` 
	INTO ExtSpuId,ExtSkuId 
	FROM `ext_spu` AS EP 
	LEFT JOIN `ext_sku` AS EK ON EK.`ext_spu_id` = EP.`id` 
	WHERE EP.`spu_id` = SpuId LIMIT 1;
	

	IF ExtSpuId > 0 THEN 
		SET ReturnMessage = CONCAT(ReturnMessage,'->','有ext_spu');
		INSERT INTO `spu_material`(`spu_id`,`type`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`is_default`,`p_order`,`status`,`create_user`,`create_time`,`create_from`)
		SELECT SpuId,`type`,`file_size`,EPM.`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,0,`p_order`,`status`,UserId,UNIX_TIMESTAMP(),ClientIp
		FROM `ext_spu_material` AS EPM 
		LEFT JOIN ( SELECT `file_url` FROM `spu_material` WHERE `spu_id` = SpuId ) AS PM ON PM.`file_url` = EPM.`file_url`
		WHERE `ext_spu_id` = ExtSpuId  AND PM.`file_url` IS NULL;
	ELSE 
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无ext_spu');
	END IF;
	
	IF ExtSkuId > 0 THEN 
		SET ReturnMessage = CONCAT(ReturnMessage,'->','有ext_sku');
		INSERT INTO `spu_material`(`spu_id`,`type`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`is_default`,`p_order`,`status`,`create_user`,`create_time`,`create_from`)
		SELECT SpuId,`type`,`file_size`,EKM.`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,0,`p_order`,`status`,UserId,UNIX_TIMESTAMP(),ClientIp
		FROM `ext_sku_material` AS EKM 
		LEFT JOIN ( SELECT `file_url` FROM `spu_material` WHERE `spu_id` = SpuId ) AS PM ON PM.`file_url` = EKM.`file_url`
		WHERE `ext_sku_id` = ExtSkuId  AND PM.`file_url` IS NULL;
	ELSE 
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无ext_sku');
	END IF;

	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||

/**
 * 拷贝至sku
 */
DROP PROCEDURE IF EXISTS `copy_to_sku` |||
CREATE PROCEDURE `copy_to_sku` (
	IN SkuId	INT		UNSIGNED,		
	IN UserId	INT		UNSIGNED,	-- 操作ID
	IN ClientIp BIGINT	UNSIGNED,	-- 操作IP

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT 'SKU素材拷贝'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;
	
	DECLARE SpuId	 INT DEFAULT 0;
	DECLARE ExtSkuId INT DEFAULT 0;
	DECLARE ExtSpuId INT DEFAULT 0;
	

	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	SELECT id INTO  SkuId FROM `sku` WHERE `id` = SkuId;
	IF SYSEmpty = 1 THEN 
		SET ReturnValue = 'E_SKU';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','SKU错误');
		LEAVE Main;
	END IF;

	SELECT EP.`id`,EK.`id`,K.`spu_id`
	INTO ExtSpuId,ExtSkuId,SpuId
	FROM sku AS K 
	LEFT JOIN `ext_spu` AS EP ON EP.`spu_id` = K.`spu_id` 
	LEFT JOIN `ext_sku` AS EK ON EK.`ext_spu_id` = EP.`id` 
	WHERE K.`id` = SkuId LIMIT 1;
	
	
	IF SpuId > 0 THEN 
		SET ReturnMessage = CONCAT(ReturnMessage,'->','有spu');
		SELECT `spu_id` INTO SpuId FROM `spu_material` WHERE spu_id = SpuId LIMIT 1;
		IF SYSEmpty = 0 THEN 
			INSERT INTO `sku_material`(`sku_id`,`type`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`is_default`,`p_order`,`status`,`create_user`,`create_time`,`create_from`)
			SELECT SkuId,`type`,`file_size`,PM.`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,0,`p_order`,`status`,UserId,UNIX_TIMESTAMP(),ClientIp
			FROM `spu_material` AS PM 
			LEFT JOIN ( SELECT `file_url` FROM `sku_material` WHERE `sku_id` = SkuId ) AS KM ON KM.`file_url` = PM.`file_url`
			WHERE `spu_id` = SpuId  AND KM.`file_url` IS NULL;
		ELSE 
			SET SYSEmpty = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无spu素材');
		END IF;
		
	ELSE 
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无spu');
	END IF;

	IF ExtSpuId > 0 THEN 
		SET ReturnMessage = CONCAT(ReturnMessage,'->','有ext_spu');
		SELECT `ext_spu_id` INTO ExtSpuId FROM `ext_spu_material` WHERE ext_spu_id = ExtSpuId LIMIT 1;
		IF SYSEmpty = 0 THEN 
			INSERT INTO `sku_material`(`sku_id`,`type`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`is_default`,`p_order`,`status`,`create_user`,`create_time`,`create_from`)
			SELECT SkuId,`type`,`file_size`,EPM.`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,0,`p_order`,`status`,UserId,UNIX_TIMESTAMP(),ClientIp
			FROM `ext_spu_material` AS EPM 
			LEFT JOIN ( SELECT `file_url` FROM `sku_material` WHERE `sku_id` = SkuId ) AS PM ON PM.`file_url` = EPM.`file_url`
			WHERE `ext_spu_id` = ExtSpuId  AND PM.`file_url` IS NULL;
		ELSE 
			SET SYSEmpty = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无extspu素材');
		END IF;
	ELSE 
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无ext_spu');
	END IF;
	
	IF ExtSkuId > 0 THEN 
		SET ReturnMessage = CONCAT(ReturnMessage,'->','有ext_sku');
		SELECT `ext_sku_id` INTO ExtSkuId FROM `ext_sku_material` WHERE ext_sku_id = ExtSkuId LIMIT 1;
		IF SYSEmpty = 0 THEN 
			INSERT INTO `sku_material`(`sku_id`,`type`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`is_default`,`p_order`,`status`,`create_user`,`create_time`,`create_from`)
			SELECT SkuId,`type`,`file_size`,EKM.`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,0,`p_order`,`status`,UserId,UNIX_TIMESTAMP(),ClientIp
			FROM `ext_sku_material` AS EKM 
			LEFT JOIN ( SELECT `file_url` FROM `sku_material` WHERE `sku_id` = SkuId ) AS PM ON PM.`file_url` = EKM.`file_url`
			WHERE `ext_sku_id` = ExtSkuId  AND PM.`file_url` IS NULL;
		ELSE 
			SET SYSEmpty = 0;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无extsku素材');
		END IF;
	ELSE 
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无ext_sku');
	END IF;

	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
