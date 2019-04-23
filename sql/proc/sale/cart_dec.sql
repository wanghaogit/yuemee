/**
 * 功能 : 删减/同步购物车信息
 * 作者：殷非非
 * 日期：2018-4-22
 * 修订@2018-04-22	: 
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `yuemi_sale`.`cart_dec` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `cart_dec`(
	IN CartId		INT UNSIGNED,				/* 购物车ID */
	IN QtyDec		SMALLINT UNSIGNED,			/* 减少数量，0=删除 */
	IN ClientIp		BIGINT UNSIGNED,				/* 创建地址 */

	OUT ReturnValue	VARCHAR(32),    	/*返回状态值 */
	OUT ReturnMessage VARCHAR(1024) 	/*返回提示信息 */
)	MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT '减购物车'
Main : BEGIN		/*主体内容开始*/
	/*定义状态*/
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;
	
	/*局部变量*/
	DECLARE UserId				INT UNSIGNED		DEFAULT 0;
	DECLARE SkuId				INT UNSIGNED		DEFAULT 0;
	DECLARE SkuDepot			INT UNSIGNED		DEFAULT 0;
	DECLARE SkuStatus			TINYINT UNSIGNED	DEFAULT 0;
	DECLARE SkuPriceSale		NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE SkuPriceInv			NUMERIC(16,4)		DEFAULT 0.0;
	DECLARE SkuLimitStyle		TINYINT UNSIGNED	DEFAULT 0;
	DECLARE SkuLimitCount		TINYINT UNSIGNED	DEFAULT 0;

	DECLARE BuyerInviterId		INT UNSIGNED		DEFAULT 0;
	DECLARE BuyerLevelUser		TINYINT UNSIGNED	DEFAULT 0;
	DECLARE BuyerLevelVip		TINYINT UNSIGNED	DEFAULT 0;

	DECLARE ShareUserId			INT UNSIGNED		DEFAULT 0;
	DECLARE ShareDirectorId		INT UNSIGNED		DEFAULT 0;
	DECLARE ShareTeamId			INT UNSIGNED		DEFAULT 0;
	DECLARE ShareMemberId		INT UNSIGNED		DEFAULT 0;
	DECLARE ShareSkuId			INT UNSIGNED		DEFAULT 0;

	DECLARE PriceOld		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE PriceNew		NUMERIC(16,4)	DEFAULT 0.0;

	
	DECLARE SpuId			INT UNSIGNED	DEFAULT 0;
	DECLARE SpuStatus		INT UNSIGNED	DEFAULT 0;

	DECLARE QtyOld			INT UNSIGNED	DEFAULT 0;
	DECLARE QtyNew			INT UNSIGNED	DEFAULT 0;

	/* 声明异常处理（定义系统错误），必须放在变量定义之后*/
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	/* 初始化变量*/
	SET CartId = 0;
	SET ReturnValue = '';
	SET ReturnMessage = '减购物车';

	START TRANSACTION;
		SELECT `user_id`,`sku_id`,`spu_id`,`sku_price`,`qty`
		INTO UserId,SkuId,SpuId,PriceOld,QtyOld
		FROM `yuemi_sale`.`cart` WHERE `id` = CartId FOR UPDATE;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_CART';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无记录');
			ROLLBACK;
			LEAVE Main;
		END IF;
		-- 特殊情况：删除
		IF QtyDec >= QtyOld THEN
			SET ReturnValue = 'OK';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','直接删');
			DELETE FROM `yuemi_sale`.`cart` WHERE `id` = CartId;
			COMMIT;
			LEAVE Main;
		END IF;
		-- 检查用户身份
		SELECT `invitor_id`,`level_u`,`level_v`
		INTO BuyerInviterId,BuyerLevelUser,BuyerLevelVip
		FROM `yuemi_main`.`user`
		WHERE `id` = UserId;
		IF SYSEmpty = 1 OR BuyerLevelUser = 0 THEN
			SET ReturnValue = 'E_USER';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无用户');
			DELETE FROM `yuemi_sale`.`cart` WHERE `user_id` = UserId;
			COMMIT;
			LEAVE Main;
		END IF;
		-- 检查SKU
		SELECT `depot`,`price_sale`,`price_inv`,`limit_style`,`limit_size`,`status`
		INTO SkuDepot,SkuPriceSale,SkuPriceInv,SkuLimitStyle,SkuLimitCount,SkuStatus
		FROM `yuemi_sale`.`sku` WHERE `id` = SkuId;
		IF SYSEmtpy =1 THEN
			SET ReturnValue = 'E_SKU';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无SKU');
			DELETE FROM `yuemi_sale`.`cart` WHERE `sku_id` = SkuId;
			COMMIT;
			LEAVE Main;
		END IF;
		IF SkuStatus = 4 THEN
			SET ReturnValue = 'E_DELETED';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无SKU');
			DELETE FROM `yuemi_sale`.`cart` WHERE `sku_id` = SkuId;
			COMMIT;
			LEAVE Main;
		END IF;
		
		-- 未上架，仍然可以减少数量
		IF SkuStatus != 2  THEN
			SET ReturnValue = 'E_SCHEDULE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','未上架');
			UPDATE `yuemi_sale`.`cart` SET `qty` = `qty` - QtyDec  WHERE `id` = CartId;
			COMMIT;
			LEAVE Main;
		END IF;
		
		-- 重新计算价格
		SET PriceNew = 0;
		IF PriceNew <= 0 AND BuyerLevelVip > 0 AND BuyerInviterId > 0 AND SkuPriceInv > 0 THEN
			SET PriceNew = SkuPriceInv;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','受邀价(',SkuPriceInv,')');
		END IF;
		IF PriceNew <= 0 THEN
			SET PriceNew = SkuPriceSale;
			SET ReturnMessage = CONCAT(ReturnMessage,'->','平台价(',SkuPriceSale,')');
		END IF;
		IF PriceNew <= 0 THEN
			SET ReturnValue = 'E_PRICE';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','价格错');
			LEAVE Main;
		END IF;
		IF PriceNew != PriceOld THEN
			SET ReturnMessage = CONCAT(ReturnMessage,'->','价格变(',PriceOld,'=>',PriceNew,')');
			UPDATE `yuemi_sale`.`cart` SET `sku_price` = PriceNew ,`qty` = `qty` - QtyDec  WHERE `id` = CartId;
		ELSE
			UPDATE `yuemi_sale`.`cart` SET `qty` = `qty` - QtyDec  WHERE `id` = CartId;
		END IF;
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');
END |||
DELIMITER ;