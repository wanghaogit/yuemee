/**
 * 功能 : 外部SKU数据变化同步
 *		 同步导入 SPU/SKU素材,SPU/SPU素材
 * 作者：殷非非
 * 日期：2018-4-22
 * 修订@2018-04-22	: 
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `resync_price` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `resync_price`(
	IN SkuId			INT UNSIGNED,		/* SKU Id */

	OUT ReturnValue		VARCHAR(32),		/*返回状态值 */
	OUT ReturnMessage	VARCHAR(1024)		/*返回提示信息 */
)	MODIFIES SQL DATA SQL SECURITY INVOKER COMMENT '价格适配'
Main : BEGIN		/*主体内容开始*/
	/*定义状态*/
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE SupplierId		INT UNSIGNED DEFAULT 0;
	DECLARE CatagoryId		INT UNSIGNED DEFAULT 0;
	DECLARE ExtSkuId		INT UNSIGNED DEFAULT 0;
	DECLARE ExtSpuId		INT UNSIGNED DEFAULT 0;
	DECLARE ExtShopCode		VARCHAR(32) DEFAULT '';

	DECLARE SkuStatus		TINYINT UNSIGNED DEFAULT 0;

	DECLARE PriceBase		NUMERIC(16,4) DEFAULT 0;
	DECLARE PriceSale		NUMERIC(16,4) DEFAULT 0;
	DECLARE PriceInv		NUMERIC(16,4) DEFAULT 0;
	DECLARE PriceRef		NUMERIC(16,4) DEFAULT 0;
	DECLARE PriceMarket		NUMERIC(16,4) DEFAULT 0;
	DECLARE RebateVip		NUMERIC(16,4) DEFAULT 0;

	DECLARE ExtPriceBase	NUMERIC(16,4) DEFAULT 0;
	DECLARE ExtPriceRef		NUMERIC(16,4) DEFAULT 0;

	DECLARE Ratio			DOUBLE DEFAULT 0;

	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1; 
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	/* 初始化变量*/
	SET ReturnValue = '';
	SET ReturnMessage = '价格适配';

	START TRANSACTION;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','取现场');
		SELECT `supplier_id`,`catagory_id`,`price_base`,`price_sale`,`price_inv`,`price_ref`,`price_market`,`rebate_vip`,`status`
		INTO SupplierId,CatagoryId,PriceBase,PriceSale,PriceInv,PriceRef,PriceMarket,RebateVip,SkuStatus
		FROM sku WHERE `id` = SkuId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_EMPTY';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无数据');
			ROLLBACK;
			LEAVE Main;
		END IF;

		SET ReturnMessage = CONCAT(ReturnMessage,'->','取基准');
		SELECT `ext_spu_id`,`price_base`,`price_ref`
		INTO ExtSpuId,ExtPriceBase,ExtPriceRef
		FROM ext_sku WHERE sku_id = SkuId;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_NOESKU';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','非外部SKU');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查来源');
		SELECT ext_shop_code INTO ExtShopCode FROM ext_spu WHERE `id` = ExtSpuId;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_NOESPU';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','非外部SKU');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		SET ReturnMessage = CONCAT(ReturnMessage,'->','查毛利');
		SET Ratio = (ExtPriceRef - ExtPriceBase) / ExtPriceRef;
		IF Ratio < 0.05 THEN
			-- 打回
			UPDATE sku SET status = 0 WHERE `id` = SkuId;
			SET ReturnValue = 'E_RATIO';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','坏毛利');
			COMMIT;
			LEAVE Main;
		END IF;
		IF Ratio < 0.1 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','低毛利');
			SET PriceBase = ExtPriceBase * 1.02;
		ELSEIF Ratio >= 0.2 AND SupplierId = 2 THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','内购高毛利');
			SET PriceBase = ExtPriceBase * 1.01;
		ELSE
			SET ReturnMessage = CONCAT(ReturnMessage,'->','正常情况');
		END IF;
		SET PriceMarket = ExtPriceRef * 1.1;
		SET RebateVip = (ExtPriceRef - ExtPriceBase) * 0.56;
		SET PriceSale = ExtPriceRef - RebateVip * 0.1;
		SET RebateVip = RebateVip * 0.9;
		SET PriceInv = PriceSale;
		SET ReturnMessage = CONCAT(ReturnMessage,'->','写回');
		UPDATE sku 
		SET price_base = PriceBase,
			price_ref = ExtPriceRef,
			price_sale = PriceSale,
			price_inv = PriceSale,
			price_market = PriceMarket,
			rebate_vip = RebateVip
		WHERE `id` = SkuId;
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_DATABASE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','写回错');
			ROLLBACK;
			LEAVE Main;
		END IF;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
DELIMITER ;