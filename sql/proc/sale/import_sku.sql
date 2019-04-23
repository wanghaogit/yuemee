/**
 * 功能 : 导入外部SKU
 *		 同步导入 SPU/SKU素材,SPU/SPU素材
 * 作者：殷非非
 * 日期：2018-4-22
 * 修订@2018-04-22	: 
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `import_sku` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `import_sku`(
	IN UserId			INT UNSIGNED,		/* 用户ID */
	IN ExtSkuId			INT UNSIGNED,		/* 外部SKUId */
	IN ClientIp			BIGINT UNSIGNED,	/* 创建地址 */

	OUT SkuId			INT UNSIGNED,		/* 导入后的SkuId */
	OUT ReturnValue		VARCHAR(32),		/*返回状态值 */
	OUT ReturnMessage	VARCHAR(1024)		/*返回提示信息 */
)
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT '导SKU'
Main : BEGIN		/*主体内容开始*/
	/*定义状态*/
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE ExtSpuId		INT UNSIGNED DEFAULT 0;
	DECLARE SpuId			INT UNSIGNED DEFAULT 0;

	DECLARE IK_Status		TINYINT UNSIGNED DEFAULT 0;
	DECLARE IP_Status		TINYINT UNSIGNED DEFAULT 0;

	DECLARE EK_Ratio		DOUBLE DEFAULT 0.0;
	DECLARE EK_PriceBase	NUMERIC(16,4) DEFAULT 0;
	DECLARE EK_PriceRef		NUMERIC(16,4) DEFAULT 0;
	DECLARE EK_Stock		NUMERIC(16,4) DEFAULT 0;
	DECLARE EK_Status		TINYINT UNSIGNED DEFAULT 0;

	DECLARE EP_Status		TINYINT UNSIGNED DEFAULT 0;

	DECLARE EP_ExtCatId		INT UNSIGNED DEFAULT 0;
	DECLARE EP_MapCatId		INT UNSIGNED DEFAULT 0;

	DECLARE SpuMaterialCount		INT UNSIGNED DEFAULT 0;
	DECLARE SkuMaterialCount		INT UNSIGNED DEFAULT 0;
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1; 
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	/* 初始化变量*/
	SET SkuId = 0;
	SET ReturnValue = '';
	SET ReturnMessage = '导SKU';

	START TRANSACTION;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查ESKU');
		SELECT ext_spu_id,sku_id,price_base,price_ref,stock,status
		INTO ExtSpuId,SkuId,EK_PriceBase,EK_PriceRef,EK_Stock,EK_Status
		FROM `ext_sku`
		WHERE `id` = ExtSkuId;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_EMPTY';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无数据');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF EK_Status = 0 OR EK_PriceBase <= 0 OR EK_PriceRef <= 0 OR EK_Stock <= 0 THEN
			SET ReturnValue = 'E_STATUS';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','已下架');
			-- 同步下架
			IF SkuId > 0 THEN
				UPDATE `sku` SET `status` = 3,`audit_user` = 1,`audit_time` = UNIX_TIMESTAMP(),audit_from = ClientIp
				WHERE `id` = SkuId AND `status` = 2;
			END IF;
			COMMIT;
			LEAVE Main;
		END IF;
		SET EK_Ratio = (EK_PriceRef - EK_PriceBase) / EK_PriceRef;
		IF EK_Ratio < 0.05 THEN
			SET ReturnValue = 'E_RATIO';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','毛利不够');
			-- 同步下架
			IF SkuId > 0 THEN
				UPDATE `sku` SET `status` = 3,`audit_user` = 1,`audit_time` = UNIX_TIMESTAMP(),audit_from = ClientIp
				WHERE `id` = SkuId AND `status` = 2;
			END IF;
			COMMIT;
			LEAVE Main;
		END IF;
		-- 检查SPU
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查ESPU');
		SELECT `spu_id`,`status`,`ext_cat_id`
		INTO SpuId,EP_Status,EP_ExtCatId
		FROM `ext_spu` WHERE `id` = ExtSpuId;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_NOESPU';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无ESPU');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF EP_Status = 0 THEN
			SET ReturnValue = 'E_STATUS';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','ESPU下架');
			UPDATE `ext_sku` SET `status` = 0 WHERE `ext_spu_id` = ExtSpuId;
			UPDATE `spu` SET `status` = 0,`audit_user` = 1,`audit_time` = UNIX_TIMESTAMP(),audit_from = ClientIp WHERE `id` = SpuId;
			UPDATE `sku` 
			SET `status` = 3,`audit_user` = 1,`audit_time` = UNIX_TIMESTAMP(),audit_from = ClientIp
			WHERE `id` IN (SELECT `sku_id` FROM `ext_sku` WHERE `ext_spu_id` = ExtSpuId);
			COMMIT;
			LEAVE Main;
		END IF;
		IF SpuId > 0 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','查SPU');
			SELECT `status` INTO IP_Status FROM `spu` WHERE `id` = SpuId;
			IF SYSEmpty = 1 THEN
				SET SYSEmpty = 0;
				SET SpuId = 0;
			END IF;
			IF IP_Status = 4 THEN
				SET ReturnValue = 'E_DROPED';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','已废弃');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查分类');
		IF EP_ExtCatId <= 0 THEN
			SET ReturnValue = 'E_NOECAT';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无外部分类');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','EC(',EP_ExtCatId,')');
		SELECT `map_id` INTO EP_MapCatId FROM ext_neigou_catagory WHERE `id` = EP_ExtCatId;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_NOECAT';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','外部分类错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		IF EP_MapCatId <= 0 THEN
			SET ReturnValue = 'E_MAPCAT';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','映射分类错');
			ROLLBACK;
			LEAVE Main;
		END IF;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','IC(',EP_MapCatId,')');
		
		-- 更新或者插入SKU
		IF SkuId > 0 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','查SKU');
			SELECT `status` INTO IK_Status FROM `sku` WHERE `id` = SkuId;
			IF SYSEmpty = 1 THEN
				SET SYSEmpty = 0;
				SET SkuId = 0;
			END IF;
			IF IK_Status = 4 THEN
				SET ReturnValue = 'E_DROPED';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','已废弃');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;

		-- 检查图片素材
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查素材');
		SELECT COUNT(*) INTO SpuMaterialCount FROM ext_spu_material WHERE `ext_spu_id` = ExtSpuId AND `type` = 0 AND `file_url` != '';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','PM(',SpuMaterialCount,')');
		SELECT COUNT(*) INTO SkuMaterialCount FROM ext_sku_material WHERE `ext_sku_id` = ExtSkuId AND `type` = 0 AND `file_url` != '';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','KM(',SkuMaterialCount,')');
		IF SpuMaterialCount + SkuMaterialCount <= 0 THEN
			SET ReturnValue = 'E_MATERIAL';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无图');
			ROLLBACK;
			LEAVE Main;
		END IF;

		-- 处理SPU
		IF SpuId > 0 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更新SPU');
			UPDATE `spu`,`ext_spu`
			SET `spu`.`catagory_id` = EP_MapCatId,
				`spu`.`title` = `ext_spu`.`title`,
				`spu`.`specs` = `ext_spu`.`specs`,
				`spu`.`serial` = `ext_spu`.`bn`,
				`spu`.`intro` = `ext_spu`.`intro`,
				`spu`.`att_refund` = 0,
				`spu`.`update_user` = UserId,
				`spu`.`update_time` = UNIX_TIMESTAMP(),
				`spu`.`update_from` = ClientIp,
				`spu`.`status` = 1
			WHERE `spu`.`id` = `ext_spu`.`spu_id` AND `spu`.`id`= SpuId AND `ext_spu`.`id` = ExtSpuId;
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新SPU错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','插入SPU');
			INSERT INTO `spu` (`supplier_id`,`catagory_id`,`brand_id`,`title`,`specs`,`serial`,`intro`,`status`,`create_user`,`create_time`,`create_from`)
			SELECT supplier_id,EP_MapCatId,brand_id,`title`,`specs`,`bn`,`intro`,1,UserId,UNIX_TIMESTAMP(),ClientIp
			FROM `ext_spu`
			WHERE `id` = ExtSpuId;
			SET SpuId = LAST_INSERT_ID();
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','插入SPU错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			UPDATE `ext_spu` SET `spu_id` = SpuId WHERE `id` = ExtSpuId;
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新ESPU错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
		-- 处理SKU
		IF SkuId > 0 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','更新SKU');
			UPDATE `sku`,`ext_sku`
			SET `sku`.`title` = `ext_sku`.`name` ,
				`sku`.`specs` = `ext_sku`.`spec` ,
				`sku`.`weight` = `ext_sku`.`weight` ,
				`sku`.`unit` = '件' ,
				`sku`.`depot` = `ext_sku`.`stock` ,
				`sku`.`price_base` = `ext_sku`.`price_base` ,
				`sku`.`price_inv` = `ext_sku`.`price_ref` ,
				`sku`.`price_sale` = `ext_sku`.`price_ref` ,
				`sku`.`price_ref` = `ext_sku`.`price_ref` ,
				`sku`.`price_market` = `ext_sku`.`price_ref` * 1.1,
				`sku`.`intro` = `ext_sku`.`intro`,
				`sku`.`status` = 2
			WHERE `sku`.`id` = `ext_sku`.`sku_id` AND `sku_id` = SkuId AND `ext_sku`.`id` = ExtSkuId;
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新SKU错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','插入SKU');
			INSERT INTO `sku` (spu_id,catagory_id,supplier_id,title,specs,`serial`,`weight`,`unit`,`depot`,`price_base`,`price_sale`,`price_inv`,`price_ref`,`price_market`,`intro`,
				`status`,`create_user`,`create_time`,`create_from`)
			SELECT SpuId,0,supplier_id,`name`,`spec`,`bn`,`weight`,'件',`stock`,`price_base`,`price_ref`,`price_ref`,`price_ref`,`price_ref` * 1.1,`intro`,
				2,UserId,UNIX_TIMESTAMP(),ClientIp
			FROM `ext_sku` WHERE `id` = ExtSkuId;
			SET SkuId = LAST_INSERT_ID();
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','插入SKU错');
				ROLLBACK;
				LEAVE Main;
			END IF;
			UPDATE `ext_sku` SET `sku_id` = SkuId WHERE `id` = ExtSkuId;
			IF SYSError = 1 THEN
				SET ReturnValue = 'E_DATABASE';
				SET ReturnMessage = CONCAT(ReturnMessage,'->','更新ESKU错');
				ROLLBACK;
				LEAVE Main;
			END IF;
		END IF;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
DELIMITER ;