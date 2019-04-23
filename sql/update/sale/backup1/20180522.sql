
DROP TABLE IF EXISTS `spread_userinfo`;
CREATE TABLE `spread_userinfo`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '记录Id',
  `source` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '来源',
  `mobile` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号码 @UNIQUE',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `weixin` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信号',
  `region_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '地区ID',
  `address` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '详细地址',
  `create_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '记录时间 @TIMESTAMP-CREATE',
  `create_from` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册IP',
  `update_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间 @TIMESTAMP-CREATE',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `source`(`source`) USING BTREE,
  UNIQUE INDEX `mobile`(`mobile`) USING BTREE
) 
ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '推广传播 - 用户信息记录' ROW_FORMAT = Dynamic;
