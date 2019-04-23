/**
 * 系统配置数据调整
 * @FORCE-UPDATE
 * Author:  eglic
 * Created: 2018-4-17
 */
DELETE FROM `setting` WHERE `group` IN ('sale','rebate','layer');
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
('rebate','director_hi'			,3,'0.12','总经理高分佣','低于此毛利时成本额外加成');
