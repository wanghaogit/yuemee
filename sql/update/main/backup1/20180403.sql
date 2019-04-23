/**
 * 任务系统
 * Author:  eglic
 * Created: 2018-4-17
	1、VIP续费任务
	5、总监卡位续费任务
	6、总经理卡位续费任务

	2、会员礼包佣金解锁任务
	3、总监礼包佣金解锁任务
	4、总经理礼包佣金解锁任务
 */
DELETE FROM `setting` WHERE `group` IN ('sale','rebate','layer','recruit','coin','promote');
 INSERT INTO `setting` (`group`,`name`,`type`,`value`,`title`,`help`) VALUES
('sale','gross_lowest'			,3,'0.05','毛利下限','允许上架的最低毛利比例'),
('sale','gross_danger'			,3,'0.10','低毛利比例','要特别处理的毛利比例'),
('sale','gross_danger_add'		,3,'0.02','低毛利附加成本','低于此毛利时成本额外加成'),

('layer','price_cheif'			,3,'3999.00','总监卡位费','总监卡位费'),
('layer','price_director'		,3,'120000.00','总经理卡位费','总经理卡位费'),

('rebate','yuemi'				,3,'0.20','系统佣金比例','低于此毛利时成本额外加成'),
('rebate','vip'					,3,'0.70','VIP佣金比例','低于此毛利时成本额外加成'),
('rebate','chief_lor'			,3,'0.12','总监低分佣',''),
('rebate','chief_lom'			,2,'50000','总监低分佣金额',''),
('rebate','chief_md'			,3,'0.16','总监中分佣','低于此毛利时成本额外加成'),
('rebate','chief_mdm'			,2,'100000','总监低分佣金额',''),
('rebate','chief_hi'			,3,'0.20','总监高分佣','低于此毛利时成本额外加成'),
('rebate','director_lo'			,3,'0.08','总经理低分佣金额','低于此毛利时成本额外加成'),
('rebate','director_lom'		,2,'1500000','总经理低分佣','低于此毛利时成本额外加成'),
('rebate','director_md'			,3,'0.10','总经理中分佣金额','低于此毛利时成本额外加成'),
('rebate','director_mdm'		,2,'3000000','总经理中分佣','低于此毛利时成本额外加成'),
('rebate','director_hi'			,3,'0.12','总经理高分佣','低于此毛利时成本额外加成'),

('recruit','vip_unlock_profit'		,3,'100.00','VIP解锁礼包佣金金额',''),
('recruit','vip_unlock_expire'		,2,'365','VIP解锁礼包佣金周期',''),
('recruit','chief_unlock_profit'	,3,'300.00','总监解锁礼包佣金金额',''),
('recruit','chief_unlock_expire'	,2,'365','总监解锁礼包佣金周期',''),
('recruit','director_unlock_profit'	,3,'1000.00','总监解锁礼包佣金金额',''),
('recruit','director_unlock_expire'	,2,'365','总监解锁礼包佣金周期',''),
('recruit','dir_gift_loop'			,2,'2','直接招聘佣金循环长度',''),
('recruit','dir_gift_money'			,18,'80.0,160.0','直接招聘佣金循环金额',''),
('recruit','alt_gift_money'			,3,'50.0','间接招聘佣金金额',''),

('premote','cheif_dir'				,2,'10','晋升总监直接招聘人数',''),
('premote','cheif_alt'				,2,'90','晋升总监间接招聘人数',''),

('premote','director_all'			,2,'3000','晋升总经理需要的团队人数',''),
('premote','director_cheif'			,2,'10','晋升总经理需要的总监人数','');



ALTER TABLE `user_finance` DROP COLUMN `thew_status`;
ALTER TABLE `user_finance` DROP COLUMN `thew_time`;

ALTER TABLE `user_finance` ADD COLUMN `thew_status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '直接礼包佣金解锁任务状态：0未解锁,1已解锁';
ALTER TABLE `user_finance` ADD COLUMN `thew_launch` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '直接礼包佣金解锁任务开始时间';
ALTER TABLE `user_finance` ADD COLUMN `thew_target` NUMERIC(16,4) NOT NULL DEFAULT 0.0 COMMENT '直接礼包佣金解锁目标佣金';
ALTER TABLE `user_finance` ADD COLUMN `thew_money` NUMERIC(16,4) NOT NULL DEFAULT 0.0 COMMENT '直接礼包佣金解锁累积消费佣金';
ALTER TABLE `user_finance` ADD COLUMN `thew_start` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '直接礼包佣金解锁开始时间';
ALTER TABLE `user_finance` ADD COLUMN `thew_expire` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '直接礼包佣金解锁到期时间';

ALTER TABLE `user_finance` ADD COLUMN `cheif_status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总监任务状态：0未解锁,1已解锁';
ALTER TABLE `user_finance` ADD COLUMN `cheif_start` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总监任务开始时间（等于注册日期）';
ALTER TABLE `user_finance` ADD COLUMN `cheif_expire` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总监任务开始时间结束时间（3个月后）';
ALTER TABLE `user_finance` ADD COLUMN `cheif_target_dir` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总监需要直招人数';
ALTER TABLE `user_finance` ADD COLUMN `cheif_target_alt` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总监需要间招人数';
ALTER TABLE `user_finance` ADD COLUMN `cheif_value_dir` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总监需要直招人数';
ALTER TABLE `user_finance` ADD COLUMN `cheif_value_alt` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总监需要间招人数';

ALTER TABLE `user_finance` ADD COLUMN `director_status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总经理任务状态：0未解锁,1已解锁';
ALTER TABLE `user_finance` ADD COLUMN `director_start` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总经理任务开始时间（等于注册日期）';
ALTER TABLE `user_finance` ADD COLUMN `director_expire` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总经理任务开始时间结束时间（3个月后）';
ALTER TABLE `user_finance` ADD COLUMN `director_target_team` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总经理需要团队人数';
ALTER TABLE `user_finance` ADD COLUMN `director_target_cheif` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总经理需要总监人数';
ALTER TABLE `user_finance` ADD COLUMN `director_value_team` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总经理需要团队人数';
ALTER TABLE `user_finance` ADD COLUMN `director_value_cheif` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '晋升总经理需要总监人数';

ALTER TABLE `cheif` ADD COLUMN `expire_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '年费到期时间';
ALTER TABLE `director` ADD COLUMN `expire_time` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '年费到期时间';
