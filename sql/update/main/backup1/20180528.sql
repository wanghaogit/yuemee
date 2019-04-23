
CREATE TABLE `supplier_info`  (
  `supplier_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '供应商Id',
  `logistics_com_ids` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '供应商常用快递公司Id列表，多个用逗号隔开',
  PRIMARY KEY (`supplier_id`) USING BTREE
) 
ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '供应商扩展信息' ROW_FORMAT = Dynamic;
