/**
 * 分尔多数据库初始化脚本
 * Author:  eglic
 * Created: 2018-2-19
 */
TRUNCATE TABLE `setting`;
INSERT INTO `setting` VALUES
	(1,'system','db_version'	,0,'20180412','数据库版本',''),
	(2,'system','name'			,4,'阅米','APP名称','');

TRUNCATE TABLE `user`;
INSERT INTO `user` (`id`,`invitor_id`,`mobile`,`password`,`token`,`name`,`level_u`,`level_v`,`level_c`,`level_d`,`level_t`,`level_a`,`level_s`,`reg_time`,`reg_from`)
VALUES 
	(1,0,'18601122863','ff18f476bac9333292c76e5f229acfd7fcda6666',''		,'是是非非',1,1,0,0,0,2,2,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1')),
	(2,0,'13466624212','ff18f476bac9333292c76e5f229acfd7fcda6666',''		,'徐磊'	,1,0,0,0,0,2,2,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1')),
	(3,0,'15117950065','ff18f476bac9333292c76e5f229acfd7fcda6666',''		,'马梓涵'	,1,0,0,0,0,1,0,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1')),
	(4,0,'13903212117','ff18f476bac9333292c76e5f229acfd7fcda6666',''		,'微畅'	,1,0,0,0,0,0,2,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1')),
	(5,0,'17896083750','ff18f476bac9333292c76e5f229acfd7fcda6666',''		,'小马'	,1,0,0,0,0,1,2,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1')),
	(6,0,'15201080215','ff18f476bac9333292c76e5f229acfd7fcda6666',''		,'海燕'	,1,0,0,0,0,1,2,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1')),
	(7,0,'18713720819','ff18f476bac9333292c76e5f229acfd7fcda6666',''		,'张震'	,1,1,0,0,0,0,0,UNIX_TIMESTAMP(),INET_ATON('127.0.0.1'));


TRUNCATE TABLE `supplier`;
INSERT INTO `supplier` (`id`,`user_id`,`name`,`alias`,`password`,
		`pi_enable`,`pi_token`,`pi_secret`,`pi_catagory`,`pi_supplier`,
		`status`,`create_time`,`create_from`) VALUES
	(1,5,'阅米','yuemi'	,SHA1('supplier.yuemee.com/q1w2e3r4t5')	,0,'','','',''											,1,NOW(),INET_ATON('127.0.0.1')),
	(2,6,'内购','neigou'	,SHA1('supplier.yuemee.com/q1w2e3r4t5')	,1,'242019a522a5173d4368003e8a545c50','3936ba3c3a2e621584ecdf6348f14c48','ext_neigou_catagory','ext_supplier'			,1,NOW(),INET_ATON('127.0.0.1')),
	(3,2,'贡云','gongyun',SHA1('supplier.yuemee.com/q1w2e3r4t5')	,1,'','','ext_gongyun_catagory','ext_supplier'			,1,NOW(),INET_ATON('127.0.0.1')),
	(4,1,'测试','ziima'	,SHA1('supplier.yuemee.com/q1w2e3r4t5')	,0,'','','',''											,1,NOW(),INET_ATON('127.0.0.1'));

TRUNCATE TABLE `user_finance`;
INSERT INTO `user_finance` (`user_id`,`coin`) 
	SELECT `id`,0.05 FROM `user`;

TRUNCATE TABLE `user_bank`;
INSERT INTO `user_bank` VALUES
	(1,1,4,110105,'化信支行','6222080200024694690',1,NOW(),INET_ATON('127.0.0.1'),1,NOW(),INET_ATON('127.0.0.1'));

TRUNCATE TABLE `user_address`;
INSERT INTO `user_address` VALUES
	(1,1,110113,'天博中心B座402','殷非非','18601122863',1,1,NOW(),INET_ATON('127.0.0.1'));


TRUNCATE TABLE `rbac_role`;
INSERT INTO `rbac_role` VALUES
	(1,0,'系统'),
	(2,0,'管理员'),
	(3,0,'财务'),
	(4,0,'采购'),
	(5,0,'运营'),
	(6,0,'物流'),
	(7,0,'售后'),
	(8,0,'客服'),
	(9,5,'商品运营'),
	(10,5,'活动运营');

TRUNCATE TABLE `rbac_admin`;
INSERT INTO `rbac_admin` (`id`,`user_id`,`role_id`,`password`,`status`,`create_time`,`create_from`) VALUES
	(1,1,1,SHA1('admin.yuemee.com/q1w2e3r4t5'),1,NOW(),INET_ATON('127.0.0.1')),
	(2,2,1,SHA1('admin.yuemee.com/q1w2e3r4t5'),1,NOW(),INET_ATON('127.0.0.1')),
	(3,3,2,SHA1('admin.yuemee.com/q1w2e3r4t5'),1,NOW(),INET_ATON('127.0.0.1')),
	(4,5,2,SHA1('admin.yuemee.com/q1w2e3r4t5'),1,NOW(),INET_ATON('127.0.0.1')),
	(5,6,2,SHA1('admin.yuemee.com/q1w2e3r4t5'),1,NOW(),INET_ATON('127.0.0.1'));


TRUNCATE TABLE `cheif`;
INSERT INTO `cheif` VALUES
	(1,1,3,NOW(),NOW());

TRUNCATE TABLE `director`;
INSERT INTO `director` VALUES
	(1,1, NOW(),NOW());

TRUNCATE TABLE `team`;
INSERT INTO `team` VALUES
	(1,1,1,'天博中心',1,NOW(),INET_ATON('127.0.0.1'));

TRUNCATE TABLE `team_group`;
INSERT INTO `team_group` VALUES
	(1,1,0,2,'尚书',0,'S',1,NOW(),INET_ATON('127.0.0.1')),
	(2,1,0,2,'阅米',0,'Y',1,NOW(),INET_ATON('127.0.0.1'));

TRUNCATE TABLE `invite_template`;
INSERT INTO `invite_template` VALUES 
(1,'默认','/template/invite/invite.jpg','/template/invite/invite.jpg',720,1100,1,265,424,28,'#FFFFFF',1,232,503,32,'#FFFFFF',250,722,220,220,1,329,336,64,64,1,20180405220414,1522940249,1,2130706433),
(2,'聊天','/template/invite/invite1.jpg','/template/invite/invite1.jpg',720,1100,1,237,591,28,'#FFFFAA',1,363,641,32,'#FFFF',246,829,220,220,1,128,598,88,88,1,20180405220414,1522939216,1,2130706433),
(3,'黑色','/template/invite/invite2.jpg','/template/invite/invite2.jpg',720,1100,1,225,227,22,'#FFFFFF',1,223,319,24,'#FFFFFF',251,834,220,220,0,55,37,128,128,1,20180405220414,1522938903,1,2130706433);

TRUNCATE TABLE `notice`;
INSERT INTO `notice` VALUES
	('0ba75e3d0dd89da285',0,0,'对全体用户的公告','测试内容'	,NOW(),ADDDATE(NOW() ,INTERVAL 1 YEAR),2,1,NOW(),INET_ATON('127.0.0.1'),1,NOW(),INET_ATON('127.0.0.1')),
	('ab435a1e13e1c3563f',1,0,'对普通会员的公告','测试内容'	,NOW(),ADDDATE(NOW() ,INTERVAL 1 YEAR),2,1,NOW(),INET_ATON('127.0.0.1'),1,NOW(),INET_ATON('127.0.0.1')),
	('d606a020be7bb74108',2,0,'对VIP会员的公告','测试内容'	,NOW(),ADDDATE(NOW() ,INTERVAL 1 YEAR),2,1,NOW(),INET_ATON('127.0.0.1'),1,NOW(),INET_ATON('127.0.0.1')),
	('20b89a4bacb5314e7e',3,0,'对总监的公告','测试内容'		,NOW(),ADDDATE(NOW() ,INTERVAL 1 YEAR),2,1,NOW(),INET_ATON('127.0.0.1'),1,NOW(),INET_ATON('127.0.0.1')),
	('6ba70d421e824f0209',4,0,'对总经理的公告','测试内容'		,NOW(),ADDDATE(NOW() ,INTERVAL 1 YEAR),2,1,NOW(),INET_ATON('127.0.0.1'),1,NOW(),INET_ATON('127.0.0.1')),
	('bbca4c73361c25d3bc',5,0,'对供应商的公告','测试内容'		,NOW(),ADDDATE(NOW() ,INTERVAL 1 YEAR),2,1,NOW(),INET_ATON('127.0.0.1'),1,NOW(),INET_ATON('127.0.0.1')),
	('a81f36dd97a02d51c5',6,1,'对阅米团队的公告','测试内容'	,NOW(),ADDDATE(NOW() ,INTERVAL 1 YEAR),2,1,NOW(),INET_ATON('127.0.0.1'),1,NOW(),INET_ATON('127.0.0.1')),
	('3cc4959e09f841b44a',7,0,'对管理员的公告','测试内容'		,NOW(),ADDDATE(NOW() ,INTERVAL 1 YEAR),2,1,NOW(),INET_ATON('127.0.0.1'),1,NOW(),INET_ATON('127.0.0.1'));

TRUNCATE TABLE `applet`;
INSERT INTO `applet` VALUES
	(1,1,0,'系统后台'		,'9c8df78ed1fb8c55','af40f9d62fb7d658','',2,NOW(),INET_ATON('127.0.0.1')),
	(2,1,0,'供应商后台'	,'1da08b9db54f1c43','b63b00ca70ef78cc','',2,NOW(),INET_ATON('127.0.0.1')),
	(3,1,0,'工作平台'		,'7d35fb429b9e9643','668f98a52cd17e65','',2,NOW(),INET_ATON('127.0.0.1')),
	(4,1,0,'用户端'		,'b31ed652c66e11b4','9d676becf5a2a443','',2,NOW(),INET_ATON('127.0.0.1')),
	(5,1,0,'分销端'		,'b6ee298cd09b799c','6c73141d02b94ab6','',2,NOW(),INET_ATON('127.0.0.1')),
	(6,1,0,'微信端'		,'3d496ab662d08e75','5dc9989e99c8d1d9','',2,NOW(),INET_ATON('127.0.0.1'));

TRUNCATE TABLE `applet_acl`;
INSERT INTO `applet_acl` VALUES
	(1	,1	,1,0,1,1,1,1,1,1),
	(2	,0	,1,1,0,1,1,1,1,1),
	(3	,0	,1,1,1,1,1,1,1,1),
	(4	,0	,1,1,0,0,0,0,0,0),
	(5	,0	,1,1,0,0,0,0,0,0),
	(6	,0	,1,0,0,0,0,0,0,0);

TRUNCATE TABLE `vip`;
INSERT INTO `vip` VALUES
	(1,0,'6c7e85be',1,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000),
	(7,0,'6fa25041',1,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000);

TRUNCATE TABLE `vip_status`;
INSERT INTO `vip_status` VALUES
	(1,1,'34bf6c7e85be',2,1000.00,UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000,UNIX_TIMESTAMP()),
	(2,7,'399196fa2504',3,1000.00,UNIX_TIMESTAMP(),UNIX_TIMESTAMP() + 31536000,UNIX_TIMESTAMP());

TRUNCATE TABLE `tally_coin`;
INSERT INTO `tally_coin`
SELECT NULL,`id`,'REGISTER','',0.0,0.05,0.05,'注册奖励',NOW(),INET_ATON('127.0.0.1')
FROM `user`;
INSERT INTO `tally_coin` VALUES
	(NULL,1,'SYSTEM','',0.05,1000.0,1000.05,'系统赠送',NOW(),INET_ATON('127.0.0.1')),
	(NULL,1,'VIP','1',1000.05,-1000.0,0.05,'兑换VIP',NOW(),INET_ATON('127.0.0.1')),
	(NULL,7,'SYSTEM','',0.05,1000.0,1000.05,'系统赠送',NOW(),INET_ATON('127.0.0.1')),
	(NULL,7,'VIP','2',1000.05,-1000.0,0.05,'兑换VIP',NOW(),INET_ATON('127.0.0.1'));

TRUNCATE TABLE `device_vender`;
INSERT INTO `device_vender` VALUES (1,'Meizu'),(2,'vivo'),(3,'HUAWEI'),(4,'OPPO'),(5,'Xiaomi'),(6,'ZTE');

TRUNCATE TABLE `device_model`;
INSERT INTO `device_model` VALUES (1,1,'PRO 6 Plus'),(2,2,'vivo Y66'),(3,3,'ALP-AL00'),(4,2,'vivo X9'),(5,4,'OPPO A59m'),(6,5,'Redmi 4A'),(7,3,'VTR-AL00'),(8,6,'ZTE BV0730'),(9,5,'Redmi 3'),(10,4,'OPPO R11'),(11,5,'xiaomi-1s'),(12,2,'vivo X7Plus'),(13,5,'MIX 2'),(14,5,'Redmi 5A'),(15,5,'Redmi Note 5A'),(16,5,'MI 6');

TRUNCATE TABLE `device`;
INSERT INTO `device` VALUES (1,'862891031128225','862891031128225','460011120614985',1,1,1,'7.0.0',100004,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-02 19:34:56',1928460139,'2018-04-04 13:22:09',1928460139),(2,'866442030239039','866442030239039','460027136375326',1,2,2,'6.0.1',900002,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-02 20:09:54',1928460139,'2018-04-06 18:04:00',1928460139),(3,'868035031424739','868035031424739','460021169572664',1,3,3,'8.0.0',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-02 20:56:48',3748135910,'2018-04-02 20:57:32',3748135910),(4,'864279032690799','864279032690799','460025112446371',1,2,4,'7.1.2',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-02 21:03:04',1711192250,'2018-04-03 18:55:15',1711192250),(5,'863050033496291','863050033496291','460000480249628',1,4,5,'5.1',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-02 21:08:05',1711192250,'2018-04-03 07:52:29',3748135820),(6,'862110037937988','862110037937988','460025015951600',1,5,6,'6.0.1',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-03 09:50:44',1928471193,'2018-04-04 10:52:32',1928471193),(7,'865334031293381','865334031293381','460077128946061',1,3,7,'8.0.0',100003,0,360,597,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-03 11:14:59',1711192250,'2018-04-03 20:34:26',1711192250),(8,'861913033049034','861913033049034','',1,6,8,'6.0',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-03 12:18:24',1928460139,NULL,0),(9,'860850030869322','860850030869322','',1,5,9,'5.1.1',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-03 12:21:09',1928460139,'2018-04-03 12:37:13',1928460139),(10,'866013035368352','866013035368352','460078010861996',1,4,10,'7.1.1',100006,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-03 17:30:59',1928460139,'2018-04-06 18:06:44',1928460139),(11,'670919929151778','670919929151778','460000801353932',1,5,11,'4.2.2',100003,0,480,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-03 17:31:31',3074241331,NULL,0),(12,'862591037176372','862591037176372','460021011751768',1,2,12,'5.1.1',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-03 18:10:06',1711192250,'2018-04-03 18:10:31',3748135842),(13,'869033026734906','869033026734906','460077266768584',1,5,13,'8.0.0',100004,0,392,738,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-03 18:29:45',1928460139,'2018-04-06 15:45:59',1928460139),(14,'865500036613589','865500036613589','460022010563885',1,5,14,'7.1.2',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-04 10:54:58',1928471193,NULL,0),(15,'865400033398666','865400033398666','460077016840311',1,5,6,'6.0.1',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-04 10:56:44',1928471193,NULL,0),(16,'864150035783123','864150035783123','460028100958452',1,5,6,'6.0.1',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-04 10:56:51',1928471193,NULL,0),(17,'863934039255340','863934039255340','460077266860369',1,5,6,'6.0.1',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-04 10:59:28',1928471193,'2018-04-04 11:01:54',1928471193),(18,'865394035325563','865394035325563','460024014245570',1,5,15,'7.1.2',100003,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-04 11:55:48',1928471193,NULL,0),(19,'380458432874347','380458432874347','460000801350429',1,5,11,'4.2.2',100003,0,480,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-04 13:04:04',3074241340,NULL,0),(20,'868030031196445','868030031196445','460011091669585',1,5,16,'8.0.0',100005,0,360,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-06 14:22:43',1928526563,'2018-04-06 14:23:34',1928526563),(21,'630911084003657','630911084003657','460000801353520',1,5,11,'4.2.2',100006,0,480,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-06 14:38:38',3074241346,NULL,0),(22,'020523038830229','020523038830229','460000801357636',1,5,11,'4.2.2',100006,0,480,640,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',0,'2018-04-06 15:03:09',3074241340,NULL,0);

TRUNCATE TABLE `user_wechat`;
INSERT INTO `user_wechat` VALUES (1,1,0,'oeG6I1bGFNr-8oxKyRAvLwMZh3-k','ob7n40ZBC85VHMcOXIB3Zdn4R8-E','oBlqv0uZXmMNY6-AB0cI7UeB-JVU','',NULL,NULL,'','18601122863','是是非非','http://thirdwx.qlogo.cn/mmopen/vi_32/ajNVdqHZLLBibVbmuaZc96XCqnQAx3icI0s8GAvdZOU9dibibtX6Aiblxia44q1HR64UldrUjhTK7icViboYMAdVGXMgcg/132',1,NULL,0,'2018-04-10 11:59:26',1928462164,'2018-04-10 12:13:34',1928462164,0,'');

TRUNCATE TABLE `run_page`;
INSERT INTO `run_page` VALUES
	(1,0,'首页','index',0,''),
	(2,0,'栏目页','catagory',0,''),
	(3,0,'聚划算','tuan',0,'');
