/*
 *	拷贝素材到spu
 *	作者：王少宏
 *	日期：2018-05-15
 */
DELIMITER |||
DROP PROCEDURE IF EXISTS `copy_espu_to_spu` |||
CREATE PROCEDURE `copy_espu_to_spu` (
	IN SpuId	INT		UNSIGNED,		
	IN ExtSpuId	INT		UNSIGNED,		
	IN UserId	INT		UNSIGNED,	-- 操作ID
	IN MType	INT		UNSIGNED,	-- 素材类型 主图0 内容图1 推广图2 所有图3
	IN ClientIp BIGINT	UNSIGNED,	-- 操作IP

	OUT ReturnValue	VARCHAR(32),
	OUT ReturnMessage VARCHAR(1024)
) LANGUAGE SQL NOT DETERMINISTIC SQL  SECURITY INVOKER CONTAINS SQL READS SQL DATA MODIFIES SQL DATA COMMENT 'SPU素材拷贝'
Main : BEGIN
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;
	
	-- 声明异常处理
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	SET ReturnValue = '';
	SET ReturnMessage = '开始复制';
	
	-- 全部复制
	IF MType = 3 THEN 
		INSERT INTO `spu_material`(`spu_id`,`type`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`is_default`,`p_order`,`status`,`create_user`,`create_time`,`create_from`)
		SELECT SpuId,`type`,`file_size`,EPM.`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,0,`p_order`,`status`,UserId,UNIX_TIMESTAMP(),ClientIp
		FROM `ext_spu_material` AS EPM 
		LEFT JOIN ( SELECT `file_url` FROM `spu_material` WHERE `spu_id` = SpuId ) AS PM ON PM.`file_url` = EPM.`file_url`
		WHERE EPM.`ext_spu_id` = ExtSpuId  AND PM.`file_url` IS NULL;
		IF SYSError = 1 THEN 
			SET SYSError = 0;
			SET ReturnValue = 'ERR';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','复制素材失败');
			LEAVE Main;
		ELSE 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','复制素材成功');
		END IF;
	ELSE 	-- 按照主图等类型复制	
		INSERT INTO `spu_material`(`spu_id`,`type`,`file_size`,`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,`is_default`,`p_order`,`status`,`create_user`,`create_time`,`create_from`)
		SELECT SpuId,MType,`file_size`,EPM.`file_url`,`image_width`,`image_height`,`thumb_url`,`thumb_size`,`thumb_width`,`thumb_height`,0,`p_order`,`status`,UserId,UNIX_TIMESTAMP(),ClientIp
		FROM `ext_spu_material` AS EPM 
		LEFT JOIN ( SELECT `file_url` FROM `spu_material` WHERE `spu_id` = SpuId ) AS PM ON PM.`file_url` = EPM.`file_url`
		WHERE EPM.`ext_spu_id` = ExtSpuId AND EPM.`type` = MType AND PM.`file_url` IS NULL;
		IF SYSError = 1 THEN 
			SET SYSError = 0;
			SET ReturnValue = 'ERR';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','复制素材失败');
			LEAVE Main;
		ELSE 
			SET ReturnMessage = CONCAT(ReturnMessage,'->','复制素材成功');
		END IF;
	END IF;

	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
