/**
 * 功能 : 购物车
 * 将货品ID等信息写入order order_item
 * 作者 : 王少宏 2018.04.18
 *	10_99.2_6,78_66.66_9
 * CALL `yuemi_sale`.cart(1,2,'10|6,78|9',0,0,@OrderId,@ReturnValue,@ReturnMessage);
 * SELECT @OrderId,@ReturnValue,@ReturnMessage;
 */
DELIMITER ||| /*定义结束符*/

DROP PROCEDURE IF EXISTS `order_add` ||| /*删除存储过程如果存在*/

CREATE PROCEDURE `order_add`(
	IN UserId 	INT,					/*用户ID*/
	IN UserAddressId 	INT,			/*收货地址*/
	IN UserBalance		TINYINT,		/*是否使用余额*/
	IN UserCommission	TINYINT,		/*是否使用佣金*/
	
	OUT PrimaryOrderId	VARCHAR(12),    /*返回主订单ID */
	OUT ReturnValue		VARCHAR(32),    /*返回状态值 */
	OUT ReturnMessage	VARCHAR(1024) 	/*返回提示信息 */
)

    MODIFIES SQL DATA
    SQL SECURITY INVOKER
    COMMENT '购物车'
Main : BEGIN		/*主体内容开始*/
	
	/*定义状态*/
	DECLARE SYSError INT DEFAULT 0;
	DECLARE SYSEmpty INT DEFAULT 0;

	DECLARE Quantity	INT								/*货物数量*/;
	DECLARE SkuId		INT;
	DECLARE SpuId		INT;
	DECLARE CatagoryId		INT;
	DECLARE SupplierId		INT;
	DECLARE ItemPrice		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE ItemTotal		NUMERIC(16,4);
	DECLARE Picture			VARCHAR(256)	DEFAULT '';
	DECLARE OrderId			VARCHAR(12)		DEFAULT '';
	DECLARE Title			VARCHAR(128)	DEFAULT '';
	DECLARE OrderPrice		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE OrderNum		INT	DEFAULT 0;

	DECLARE ShelfId			INT;
	
	DECLARE ShelfDepot		INT UNSIGNED;
	DECLARE ShelfStatus		TINYINT UNSIGNED;
	DECLARE ShelfATime		BIGINT UNSIGNED	DEFAULT 0;
	DECLARE ShelfBTime		BIGINT UNSIGNED	DEFAULT 0;
	DECLARE ShelfPriceS		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE ShelfPriceU		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE ShelfPriceV		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE ShelfPriceI		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE ShelfPriceR		NUMERIC(16,4)	DEFAULT 0.0;
	DECLARE ShelfCheckV		TINYINT UNSIGNED DEFAULT 0;
	DECLARE ShelfCheckI		TINYINT UNSIGNED DEFAULT 0;
	DECLARE ShelfCheckC		TINYINT UNSIGNED DEFAULT 0;
	DECLARE ShelfCheckD		TINYINT UNSIGNED DEFAULT 0;
	DECLARE ShelfLimitS		TINYINT UNSIGNED DEFAULT 0;
	DECLARE ShelfLimitC		TINYINT UNSIGNED DEFAULT 0;
	DECLARE ShelfLimitD		INT UNSIGNED	DEFAULT 0;

	DECLARE UserInvId		INT UNSIGNED	 DEFAULT 0;
	DECLARE UserLevelU		TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelV		TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelC		TINYINT UNSIGNED DEFAULT 0;
	DECLARE UserLevelD		TINYINT UNSIGNED DEFAULT 0;

	DECLARE TmpId			INT UNSIGNED	DEFAULT 0;
	DECLARE TmpVal			INT UNSIGNED	DEFAULT 0;

	DECLARE AddrRegion		INT UNSIGNED	DEFAULT 0;
	DECLARE AddrDetail		VARCHAR(256)	DEFAULT '';
	DECLARE AddrName		VARCHAR(16)		DEFAULT '';
	DECLARE AddrMobile		VARCHAR(16)		DEFAULT '';
	DECLARE DependId		VARCHAR(12)		DEFAULT '';
	DECLARE I				INT UNSIGNED DEFAULT 0;
	DECLARE J				INT UNSIGNED DEFAULT 0;
	DECLARE Cnt				INT UNSIGNED DEFAULT 0;
	DECLARE Sid				INT UNSIGNED DEFAULT 0;
	DECLARE CntCart			INT UNSIGNED DEFAULT 0;
	DECLARE CartId			INT UNSIGNED DEFAULT 0;
	DECLARE IsPrimary		TINYINT UNSIGNED DEFAULT 0;

	/*定义临时变量*/
		/*用户表*/
	DECLARE	PriceTotal	DECIMAL(16,4)	DEFAULT 0.0000	/*订单总价格*/;
	DECLARE	PayMoney	DECIMAL(16,4)					/*余额支付部分*/;
	DECLARE	PayTicket	DECIMAL(16,4)					/*购物券支付部分*/;
	DECLARE	PayOnline	DECIMAL(16,4)					/*在线支付部分*/;
	DECLARE	CreateTime	DateTime						/*创建时间*/;
	DECLARE	CreateFrom	BIGINT(20)						/*创建IP*/;



	/* 声明异常处理（定义系统错误），必须放在变量定义之后*/
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET SYSError = 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET SYSEmpty = 1;
	
	SELECT invitor_id,level_u,level_v,level_c,level_d
	INTO UserInvId,UserLevelU,UserLevelV,UserLevelC,UserLevelD
	FROM `yuemi_main`.`user`
	WHERE `id` = UserId;

	IF SYSEmpty = 1 THEN
		SET ReturnValue = 'E_USER';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','无用户');
		LEAVE Main;
	END IF;
	IF UserLevelU = 0 THEN
		SET ReturnValue = 'E_USER';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','被禁闭');
		LEAVE Main;
	END IF;

	UPDATE `yuemi_sale`.cart , `yuemi_sale`.shelf , `yuemi_sale`.sku,`yuemi_sale`.spu
	SET `yuemi_sale`.cart.supplier_id = `yuemi_sale`.spu.supplier_id
	WHERE `yuemi_sale`.cart.user_id = UserId 
	AND `yuemi_sale`.shelf.id = `yuemi_sale`.cart.shelf_id
	AND `yuemi_sale`.sku.id = `yuemi_sale`.shelf.sku_id
	AND `yuemi_sale`.spu.id = `yuemi_sale`.sku.spu_id;

	SELECT COUNT(distinct supplier_id)  INTO Cnt FROM `yuemi_sale`.cart WHERE user_id = UserId AND `yuemi_sale`.`cart`.`is_checked` > 0 ;
	-- 检查空车
	IF SYSEmpty = 1 OR Cnt < 1 THEN
		SET ReturnValue = 'E_EMPTY';
		SET ReturnMessage = CONCAT(ReturnMessage,'->','空车');
		LEAVE Main;

	END IF;

	-- TODO VIPS
	IF UserLevelV > 0 AND UserInvId > 0 THEN
		UPDATE `yuemi_sale`.`cart`,`yuemi_sale`.`shelf`
		SET `yuemi_sale`.`cart`.`shelf_price` = `yuemi_sale`.`shelf`.`price_inv`
		WHERE `yuemi_sale`.`cart`.`shelf_id` = `yuemi_sale`.`shelf`.`id` 
		 AND `yuemi_sale`.`shelf`.`price_inv` > 0;
	END IF;

	-- USER
	IF UserLevelV = 0 AND UserLevelU > 0 THEN
		UPDATE `yuemi_sale`.`cart`,`yuemi_sale`.`shelf`
		SET `yuemi_sale`.`cart`.`shelf_price` = `yuemi_sale`.`shelf`.`price_sale`
		WHERE `yuemi_sale`.`cart`.`shelf_id` = `yuemi_sale`.`shelf`.`id` 
		 AND `yuemi_sale`.`shelf`.`price_sale` > 0;
	END IF;
	-- 开始循环
	SET I = 0;

	START TRANSACTION;
	
	WHILE I < Cnt DO 
		-- 生成订单ID，并检查主订单ID
		SET OrderId = `yuemi_main`.NEW_ORDER_ID('P','');
		IF I = 0 THEN
			SET IsPrimary = 1;
			SET PrimaryOrderId = OrderId;
			IF Cnt > 1 THEN
				SET DependId = OrderId;
			ELSE
				SET DependId = '';
			END IF;
		ELSE
			SET IsPrimary = 0;
		END IF;
		
		-- 开始按供应商分组
		SELECT distinct supplier_id INTO Sid FROM `yuemi_sale`.`cart` WHERE user_id = UserId ORDER BY supplier_id ASC LIMIT I,1;
		IF SYSEmpty = 1 THEN
			SET ReturnValue = 'E_SID';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无记录');
			ROLLBACK;
			LEAVE Main;
		END IF;


		-- 插入订单
		INSERT INTO `yuemi_sale`.`order`(`id`,`depend_id`,`user_id`,`inviter_id`,`inviter_feed`,`qty`,`money`,`pay_money`,`pay_profit`,`pay_online`,pay_serial,`create_time`,`address_id`,`addr_region`,`addr_detail`,`addr_name`,`addr_mobile`,`trans_id`,`is_primary`,`status`)
		SELECT PrimaryOrderId,DependId,UserId,`yuemi_sale`.`cart`.`inviter_id`,`yuemi_sale`.`cart`.`inviter_feed`,`yuemi_sale`.`cart`.`qty`,`yuemi_sale`.`cart`.`shelf_price`,0,0,0,0,CURRENT_DATE,UserAddressId, IF(UserAddressId > 0,`yuemi_main`.`user_address`.`region_id`,0),IF (UserAddressId > 0,`yuemi_main`.`user_address`.`address`,''),IF(UserAddressId > 0,`yuemi_main`.`user_address`.`contacts`,''),IF (UserAddressId > 0,`yuemi_main`.`user_address`.`mobile`,''),'',IsPrimary,1
		FROM `yuemi_sale`.`cart` 
		LEFT JOIN `yuemi_main`.`user_address` ON `yuemi_main`.`user_address`.`id` = UserAddressId
		WHERE `yuemi_sale`.`cart`.`user_id` = UserId AND `yuemi_sale`.`cart`.`supplier_id` = Sid AND `yuemi_sale`.`cart`.`is_checked` > 0 
		ORDER BY `yuemi_sale`.`cart`.`id` ASC 
		LIMIT 1;
		
		IF SYSError = 1 THEN
			SET ReturnValue = 'E_I_OR';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','插入order失败');
			ROLLBACK;
			LEAVE Main;
		END IF;

		IF SYSEmpty = 1 THEN
			-- 空订单
			SET ReturnValue = 'E_SID_1';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无记录');
			ROLLBACK;
			LEAVE Main;
		END IF;

		-- 插入详情
		INSERT INTO `yuemi_sale`.`order_item` (`order_id`,`shelf_id`,`sku_id`,`spu_id`,`catagory_id`,`supplier_id`,`qty`,`price`,`money`,`title`,`picture`)
		SELECT OrderId,`cart`.`shelf_id`,`cart`.`sku_id`,`cart`.`spu_id`,`cart`.`catagory_id`,`cart`.`supplier_id`,`cart`.`qty`,IF(`shelf_price` > 0 ,`cart`.`shelf_price`,shelf.price_sale),`cart`.`qty` * `cart`.`shelf_price`,`cart`.`shelf_title`,`cart`.`shelf_thumb`
		FROM `yuemi_sale`.`cart`
		LEFT JOIN `yuemi_sale`.shelf ON `yuemi_sale`.shelf.id = `yuemi_sale`.cart.shelf_id
		WHERE `yuemi_sale`.`cart`.`user_id` = UserId AND `yuemi_sale`.`cart`.`supplier_id` = Sid AND `yuemi_sale`.`cart`.`is_checked` > 0 AND `yuemi_sale`.shelf.qty_left > `yuemi_sale`.cart.qty;

		IF SYSEmpty = 1 THEN
			-- 空订单
			SET ReturnValue = 'E_SID';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','无记录');
			ROLLBACK;
			LEAVE Main;
		END IF;

		UPDATE `yuemi_sale`.`shelf`,`yuemi_sale`.`sku`,`yuemi_sale`.`order_item`
		SET `shelf`.`qty_left` = `shelf`.`qty_left` - `order_item`.`qty`,`sku`.`quantity` = `sku`.`quantity` - `order_item`.`qty`
		WHERE `shelf`.`id` = `order_item`.`shelf_id`
		AND `sku`.`id` = `order_item`.`sku_id`
		AND `order_item`.`order_id` = OrderId;

		IF SYSError = 1 THEN
			SET ReturnValue =  'E_UP';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','插入order_item失败');
			ROLLBACK;
			LEAVE Main;
		END IF;

		-- 回去更新订单总价
		SELECT COUNT(`qty`),COUNT(`money`) INTO OrderNum,OrderPrice FROM `yuemi_sale`.`order_item` WHERE `order_id` = OrderId;
		UPDATE `yuemi_sale`.`order` 
		SET `qty` = OrderNum , money = OrderPrice
		WHERE `id` = OrderId;
		IF IsPrimary = 0 THEN 
			UPDATE `yuemi_sale`.`order` 
			SET `qty` = `qty` + OrderNum , `money` = `money` + OrderPrice
			WHERE `id` = PrimaryOrderId;
		END IF;
		-- 回去更新 InviterId 和InviterFeed


		-- 删除购物车
		DELETE FROM `yuemi_sale`.`cart` 
		WHERE `user_id` = UserId AND `supplier_id` = Sid AND `is_checked` > 0 ;
		IF SYSError = 1 THEN
			SET ReturnValue =  'E_D_C';
			SET ReturnMessage = CONCAT(ReturnMessage,'->','删除失败');
			ROLLBACK;
			LEAVE Main;
		END IF;
		
		SET I = I + 1;
	END WHILE;

	
	COMMIT;
	SET ReturnValue = 'OK';
	SET ReturnMessage = CONCAT(ReturnMessage,'->','完毕');

END |||
DELIMITER ; /*定义结束符*/


