/**
 * 功能 : 外部SKU数据变化同步
 *		 同步导入 SPU/SKU素材,SPU/SPU素材
 * 作者：殷非非
 * 日期：2018-4-22
 * 修订@2018-04-22	: 
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `resync_sku` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `resync_sku`(
	IN ExtSkuId			INT UNSIGNED,		/* 外部SKU Id */
	IN NewPriceBase		NUMERIC(16,4),		/* 新价格 */
	IN NewPriceRef		NUMERIC(16,4),		/* 新价格 */
	IN NewDepot			INT,				/* 新库存 */

	OUT ReturnValue		VARCHAR(32),		/*返回状态值 */
	OUT ReturnMessage	VARCHAR(1024)		/*返回提示信息 */
)	MODIFIES SQL DATA SQL SECURITY INVOKER COMMENT '同步SKU'
Main : BEGIN		/*主体内容开始*/
	/*定义状态*/
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE SupplierId		INT UNSIGNED DEFAULT 0;
	DECLARE ExtSpuId		INT UNSIGNED DEFAULT 0;
	DECLARE SpuId			INT UNSIGNED DEFAULT 0;
	DECLARE SkuId			INT UNSIGNED DEFAULT 0;
	DECLARE OldPriceBase	NUMERIC(16,4) DEFAULT 0;
	DECLARE OldPriceRef		NUMERIC(16,4) DEFAULT 0;
	DECLARE OldDepot		INT UNSIGNED DEFAULT 0;

	DECLARE OldRatio		DOUBLE DEFAULT 0;
	DECLARE NewRatio		DOUBLE DEFAULT 0;

	DECLARE EP_Status		TINYINT UNSIGNED DEFAULT 0;
	DECLARE EK_Status		TINYINT UNSIGNED DEFAULT 0;
	DECLARE IP_Status		TINYINT UNSIGNED DEFAULT 0;
	DECLARE IK_Status		TINYINT UNSIGNED DEFAULT 0;

	DECLARE EK_Ratio		DOUBLE DEFAULT 0.0;
	DECLARE EK_PriceBase	NUMERIC(16,4) DEFAULT 0;
	DECLARE EK_PriceRef		NUMERIC(16,4) DEFAULT 0;
	DECLARE EK_Stock		NUMERIC(16,4) DEFAULT 0;


	DECLARE ESpuMaterialCount		INT UNSIGNED DEFAULT 0;
	DECLARE ESkuMaterialCount		INT UNSIGNED DEFAULT 0;
	DECLARE ISpuMaterialCount		INT UNSIGNED DEFAULT 0;
	DECLARE ISkuMaterialCount		INT UNSIGNED DEFAULT 0;
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1; 
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	/* 初始化变量*/
	SET ReturnValue = '';
	SET ReturnMessage = '同步SKU';
	SET SYSEmpty = 0;

	SELECT supplier_id,ext_spu_id,sku_id,price_base,price_ref,stock,status
	INTO SupplierId,ExtSpuId,SkuId,OldPriceBase,OldPriceRef,OldDepot,EK_Status
	FROM ext_sku
	WHERE `id` = ExtSkuId;
	IF SYSEmpty = 1 THEN
		SET ReturnValue = 'E_EMPTY';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无数据');
		LEAVE Main;
	END IF;
	IF SkuId > 0 THEN		-- 有内部SKU，重新获取实时库存
		SELECT spu_id,depot,status
		INTO SpuId,OldDepot,IK_Status
		FROM sku
		WHERE `id` = SkuId;
		IF SYSEmpty = 1 THEN	-- 没有SKU，更新外部SKU后退出
			SET ReturnValue = 'E_RELATION';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无SKU');
			UPDATE `ext_sku` 
			SET price_base = NewPriceBase,price_ref = NewPriceRef,stock = NewDepot,`update_time` = UNIX_TIMESTAMP()
			WHERE `id` = ExtSkuId;
			LEAVE Main;
		END IF;
		IF IK_Status = 4 THEN
			SET ReturnValue = 'E_DISCARD';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','已废弃');
			LEAVE Main;
		END IF;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查素材');
		SELECT COUNT(*) INTO ESpuMaterialCount FROM ext_spu_material WHERE `ext_spu_id` = ExtSpuId AND `type` = 0 AND `file_url` != '';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','EPM(',ESpuMaterialCount,')');
		SELECT COUNT(*) INTO ESkuMaterialCount FROM ext_sku_material WHERE `ext_sku_id` = ExtSkuId AND `type` = 0 AND `file_url` != '';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','EKM(',ESkuMaterialCount,')');
		SELECT COUNT(*) INTO ISpuMaterialCount FROM spu_material WHERE `spu_id` = SpuId AND `type` = 0 AND `file_url` != '';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','IPM(',ESpuMaterialCount,')');
		SELECT COUNT(*) INTO ISkuMaterialCount FROM sku_material WHERE `sku_id` = SkuId AND `type` = 0 AND `file_url` != '';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','IKM(',ISkuMaterialCount,')');
		IF ESpuMaterialCount + ESkuMaterialCount + ISpuMaterialCount + ISkuMaterialCount < 1 THEN
			SET ReturnValue = 'E_MATERIAL';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无图');
			IF IK_Status = 2 OR IK_Status = 3 THEN
				UPDATE sku SET status = 0 WHERE `id` = SkuId;
			END IF;
			LEAVE Main;
		END IF;
	END IF;

	START TRANSACTION;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','成本价');
		IF NewPriceBase <= 0 THEN
			SET ReturnValue = 'OK';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','成本价错');
			-- 同步下架，E=0,I=0
			UPDATE ext_sku SET price_base = 0,status = 0,update_time = UNIX_TIMESTAMP() WHERE `id` = ExtSkuId;
			IF SkuId > 0 THEN
				UPDATE sku SET price_base = 0,status = 0,audit_time = UNIX_TIMESTAMP(),audit_user = 1,audit_from = 2130706433 WHERE `id` = SkuId;
			END IF;
			-- 记录变更，成本价归零
			INSERT INTO `ext_sku_changes` (`ext_sku_id`,`supplier_id`,`chg_price_base`,`old_price_base`,`new_price_base`,`message`,`create_time`)
			VALUES (ExtSkuId,SupplierId,1,OldPriceBase,0,'成本价归零或者格式错误，外部/内部SKU同步下架。',UNIX_TIMESTAMP());
			COMMIT;
			LEAVE Main;
		END IF;

		SET ReturnMessage = CONCAT(ReturnMessage,'->','对标价');
		IF NewPriceRef <= 0 THEN
			SET ReturnValue = 'OK';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','对标价错');
			-- 同步下架，E=0,I=0
			UPDATE ext_sku SET price_ref = 0,status = 0,update_time = UNIX_TIMESTAMP() WHERE `id` = ExtSkuId;
			IF SkuId > 0 THEN
				UPDATE sku SET price_ref = 0,status = 0,audit_time = UNIX_TIMESTAMP(),audit_user = 1,audit_from = 2130706433 WHERE `id` = SkuId;
			END IF;
			-- 记录变更，成本价归零
			INSERT INTO `ext_sku_changes` (`ext_sku_id`,`supplier_id`,`chg_price_ref`,`old_price_ref`,`new_price_ref`,`message`,`create_time`)
			VALUES (ExtSkuId,SupplierId,1,OldPriceRef,0,'对标价归零或者格式错误，外部/内部SKU同步下架。',UNIX_TIMESTAMP());
			COMMIT;
			LEAVE Main;
		END IF;

		SET ReturnMessage = CONCAT(ReturnMessage,'->','库存');
		IF NewDepot <= 0 THEN
			SET ReturnValue = 'OK';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','外部售罄');
			-- 同步下架，E=0,I=3
			UPDATE ext_sku SET stock = 0, status = 0,update_time = UNIX_TIMESTAMP() WHERE `id` = ExtSkuId;
			IF SkuId > 0 THEN
				IF IK_Status = 2 OR IK_Status = 3 THEN
					UPDATE sku SET depot = 0,status = 3,audit_time = UNIX_TIMESTAMP(),audit_user = 1,audit_from = 2130706433 WHERE `id` = SkuId;
				ELSE
					UPDATE sku SET depot = 0,audit_time = UNIX_TIMESTAMP(),audit_user = 1,audit_from = 2130706433 WHERE `id` = SkuId;
				END IF;
			END IF;
			-- 记录变更
			INSERT INTO `ext_sku_changes` (`ext_sku_id`,`supplier_id`,`chg_depot`,`old_depot`,`new_depot`,`message`,`create_time`)
			VALUES (ExtSkuId,SupplierId,1,OldDepot,0,'外部售罄，外部/内部SKU同步下架。',UNIX_TIMESTAMP());
			COMMIT;
			LEAVE Main;
		END IF;

		SET ReturnMessage = CONCAT(ReturnMessage,'->','毛利');
		SET OldRatio = (OldPriceRef - OldPriceBase) / OldPriceRef;
		SET NewRatio = (NewPriceRef - NewPriceBase) / NewPriceRef;
		IF NewRatio < 0.05 THEN
			SET ReturnValue = 'OK';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','毛利降低');
			-- 同步下架，E=0,I=0
			UPDATE ext_sku SET stock = 0, status = 0,update_time = UNIX_TIMESTAMP() WHERE `id` = ExtSkuId;
			-- 内部SKU打回重审
			IF SkuId > 0 THEN
				UPDATE sku SET status = 0,audit_time = UNIX_TIMESTAMP(),audit_user = 1,audit_from = 2130706433 WHERE `id` = SkuId;
			END IF;
			-- 记录变更
			INSERT INTO `ext_sku_changes` (`ext_sku_id`,`supplier_id`,`chg_ratio`,`old_ratio`,`new_ratio`,`message`,`create_time`)
			VALUES (ExtSkuId,SupplierId,1,OldRatio,NewRatio,'外部毛利降到5%以下，外部/内部SKU同步下架。',UNIX_TIMESTAMP());
			COMMIT;
			LEAVE Main;
		END IF;
		-- 毛利已经大于 0.05，重新上架，不退出，继续
		IF SkuId > 0 AND (IK_Status = 0 OR IK_Status = 1) THEN
			UPDATE sku SET status = 2,audit_time = UNIX_TIMESTAMP(),audit_user = 1,audit_from = 2130706433 WHERE `id` = SkuId;
			INSERT INTO `ext_sku_changes` (`ext_sku_id`,`supplier_id`,`chg_ratio`,`old_ratio`,`new_ratio`,`message`,`create_time`)
			VALUES (ExtSkuId,SupplierId,1,OldRatio,NewRatio,'外部毛利回升到5%以上，外部SKU和内部SKU同步上架。',UNIX_TIMESTAMP());
		ELSEIF OldRatio != NewRatio THEN
			INSERT INTO `ext_sku_changes` (`ext_sku_id`,`supplier_id`,`chg_ratio`,`old_ratio`,`new_ratio`,`message`,`create_time`)
			VALUES (ExtSkuId,SupplierId,1,OldRatio,NewRatio,'外部毛利变化',UNIX_TIMESTAMP());
		END IF;
		
		-- 最后更新数据，价格部分单独存储过程处理
		UPDATE ext_sku SET price_base = NewPriceBase,price_ref = NewPriceRef,stock = NewDepot, status = 1,update_time = UNIX_TIMESTAMP() WHERE `id` = ExtSkuId;
		UPDATE sku
		SET price_base = NewPriceBase,price_ref = NewPriceRef,depot = NewDepot,
			audit_time = UNIX_TIMESTAMP(),audit_user = 1,audit_from = 2130706433
		WHERE `id` = SkuId;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
DELIMITER ;