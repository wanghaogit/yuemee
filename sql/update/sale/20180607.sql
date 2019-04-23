
ALTER TABLE `order` ADD COLUMN `discount_coupon_id` varchar(32) NULL DEFAULT NULL COMMENT '使用的优惠券Id'	AFTER `trans_time`;

DROP TABLE IF EXISTS `discount_coupon`;
CREATE TABLE `discount_coupon`  (
  `id` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT '类型：0未知，1商品券，2商家券，3品类券',
  `spu_id` int(10) DEFAULT NULL COMMENT '商品spu_id',
  `value` decimal(16, 4) DEFAULT NULL COMMENT '优惠券价值',
  `price_small` decimal(16, 4) DEFAULT NULL COMMENT '可用最小订单价（等于/高于此价格可用）',
  `expiry_date` bigint(20) DEFAULT NULL COMMENT '有效期（时间截）',
  `user_id` int(10) DEFAULT NULL COMMENT '使用者id',
  `creator_id` int(10) DEFAULT NULL COMMENT '创建者id',
  `create_time` bigint(12) DEFAULT NULL COMMENT '创建时间',
  `update_time` bigint(12) DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态：0初始创建，1已使用，2关闭',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `type`(`type`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '优惠券' ROW_FORMAT = Dynamic;
