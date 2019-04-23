<?php

include_once Z_ROOT . '/Cloud/Kuaidi.php';

/**
 * 物流
 */
class WuLiu
{
	/**
	 * 构造函数
	 */
	public function __construct() {

	}

	/**
	 * 通过订单号返回物流公司列表（将比较有可能的放到前面来）
	 * @param type $KuaiDiKey
	 * @param type $KuaiDiToken
	 * @param type $Num
	 */
	public function get_list_by_sn($Num, $KuaiDiKey = null, $KuaiDiToken = null)
	{
		$Num = trim($Num);
		if (empty($KuaiDiKey)) $KuaiDiKey = KUAIDI_KEY;
		if (empty($KuaiDiToken)) $KuaiDiToken = KUAIDI_TOKEN;

		// 订单号 -> 物流公司列表
		$Kuaidi = new \Cloud\Kuaidi\Platofrm($KuaiDiKey, $KuaiDiToken);
		$ComCodeList = $Kuaidi->info($Num); // 订单号查询快递公司
		$ComCodeList = $ComCodeList==null ? array() : $ComCodeList;
		
		// 物流公司列表
		$WlComList = $this->get_list(1);

		// 将最有可能的公司列表放到前面来
		$WlComList1 = array(); // 可能的
		$WlComList2 = array(); // 不太可能的
		foreach ($WlComList AS $key => $val)
		{
			if (in_array($key, $ComCodeList)) {
				$WlComList1[$key] = $val;
			} else {
				$WlComList2[$key] = $val;
			}
		}

		// 合并、返回
		$WlComList = array_merge($WlComList1, $WlComList2);
		return $WlComList;
	}

	/**
	 * 物流公司编码转为名称
	 * @param type $code
	 */
	public function code_to_name($code)
	{
		$list = $this->get_list(1);
		return isset($list[$code]) ? $list[$code] : null;
	}

	/**
	 * 名称转编码
	 * @param type $name
	 */
	public function name_to_code($name)
	{
		$list = $this->get_list();
		return isset($list[$name]) ? $list[$name] : null;
	}

	/**
	 * 获取列表
	 * @parem int $KeyType	Key类型：0以物流公司名称为Kye，1以物流公司编码为Key
	 */
	public function get_list($KeyType = 0)
	{
		$DataStr = '金岸物流,jinan
海带宝,haidaibao
澳通华人物流,cllexpress
斑马物流,banma
信丰物流,xinfengwuliu
德国(Deutsche Post),deutschepost
苏宁订单,suningorder
宜送物流,yiex
AOL澳通速递,aolau
TRAKPAK,trakpak
GTS快递,gts
通达兴物流,tongdaxing
中国香港(HongKong Post)英文,hkposten
骏丰国际速递,junfengguoji
俄罗斯邮政(Russian Post),pochta
云达通,ydglobe
EU-EXPRESS,euexpress
广州海关,gzcustoms
杭州海关,hzcustoms
南京海关,njcustoms
北京海关,bjcustoms
美西快递,meixi
一站通快递,zgyzt
易联通达,el56
驿扬国际速运,iyoungspeed
途鲜物流,ibenben
豌豆物流,wandougongzhu
哥士传奇速递,gscq365
心怡物流,alog
ME物流,macroexpressco
疯狂快递,crazyexpress
韩国邮政韩文,koreapostkr
全速物流,quansu
新杰物流,sunjex
鲁通快运,lutong
安的快递,gda
八达通,bdatong
美国申通,stoexpress
法国小包（colissimo）,colissimo
泛捷国际速递,epanex
中远e环球,cosco
顺达快递,sundarexpress
捷记方舟,ajexpress
方舟速递,arkexpress
明大快递,adaexpress
长江国际速递,changjiang
PCA Express,pcaexpress
洋包裹,yangbaoguo
优联吉运,uluckex
德豪驿,dehaoyi
堡昕德速递,bosind
阿根廷(Correo Argentina),correoargentino
秘鲁(SERPOST),peru
哈萨克斯坦(Kazpost),kazpost
广通速递,gtongsudi
东瀚物流,donghanwl
rpx,rpx
黑猫雅玛多,yamato
华通快运,htongexpress
吉尔吉斯斯坦(Kyrgyz Post),kyrgyzpost
拉脱维亚(Latvijas Pasts),latvia
黎巴嫩(Liban Post),libanpost
立陶宛（Lietuvos pa?tas）,lithuania
马尔代夫(Maldives Post),maldives
马耳他（Malta Post）,malta
马其顿(Macedonian Post),macedonia
新西兰（New Zealand Post）,newzealand
摩尔多瓦(Posta Moldovei),moldova
塞尔维亚(PE Post of Serbia),serbia
塞浦路斯(Cyprus Post),cypruspost
突尼斯EMS(Rapid-Poste),tunisia
乌兹别克斯坦(Post of Uzbekistan),uzbekistan
新喀里多尼亚[法国](New Caledonia),caledonia
叙利亚(Syrian Post),republic
亚美尼亚(Haypost-Armenian Postal),haypost
也门(Yemen Post),yemen
印度(India Post),india
"英国(大包,EMS)",england
约旦(Jordan Post),jordan
越南小包(Vietnam Posts),vietnam
黑山(Po?ta Crne Gore),montenegro
哥斯达黎加(Correos de Costa Rica),correos
EFS Post（平安快递）,efs
TNT Post,tntpostcn
立白宝凯物流,lbbk
匈牙利（Magyar Posta）,hungary
中国澳门(Macau Post),macao
西安喜来快递,xilaikd
韩润,hanrun
格陵兰[丹麦]（TELE Greenland A/S）,greenland
菲律宾（Philippine Postal）,phlpost
厄瓜多尔(Correos del Ecuador),ecuador
冰岛(Iceland Post),iceland
波兰小包(Poczta Polska),emonitoring
阿尔巴尼亚(Posta shqipatre),albania
埃及（Egypt Post）,egypt
爱沙尼亚(Eesti Post),omniva
云豹国际货运,leopard
中外运空运,sinoairinex
上海昊宏国际货物,hyk
城晓国际快递,ckeex
中铁快运,ztky
出口易,chukou1
跨畅（直邮易）,kuachangwuliu
WTD海外通,wtdex
CHS中环国际快递,chszhonghuanguoji
汉邦国际速递,handboy
银河物流,milkyway
荷兰速递(Nederland Post),nederlandpost
澳州顺风快递,emms
环东物流,huandonglg
迅达速递,xdexpress
中邮速递,wondersyd
布谷鸟速递,cuckooexpess
万庚国际速递,vangenexpress
FedRoad 联邦转运,fedroad
Landmark Global,landmarkglobal
佳成快递,jiacheng
诺尔国际物流,nuoer
加运美速递,jym56
新时速物流,csxss
中宇天地,zytdscm
翔腾物流,xiangteng
恒瑞物流,hengrui56
中国翼,cnws
邦工快运,bgky100
上海无疆for买卖宝,shanghaiwujiangmmb
新加坡小包(Singapore Post),singpost
中俄速通（淼信）,mxe56
海派通,hipito
源安达,yuananda
赛澳递for买卖宝,saiaodimmb
ECMS Express,ecmsglobal
英脉物流,gml
佳家通货运,jiajiatong56
吉日优派,jrypex
西安胜峰,xaetc
logen路坚,ilogen
amazon-国际订单,amusorder
CJ物流,doortodoor
转运四方,zhuanyunsifang
成都东骏物流,dongjun
日本郵便,japanpost
猴急送,hjs
全信通快递,quanxintong
信天捷快递,xintianjie
泰国138国际物流,sd138
荷兰包裹(PostNL International Parcels),postnlpacle
乐天速递,ltexp
智通物流,ztong
全速通,quansutong
中技物流,zhongjiwuliu
九曳供应链,jiuyescm
当当,dangdang
美龙快递,mjexp
唯品会(vip),vipshop
1号店,yhdshop
皇家物流,pfcexpress
百千诚物流,bqcwl
法国(La Poste),csuivi
DHL-全球件,dhlen
运通中港,yuntongkuaidi
苏宁物流,suning
荷兰Sky Post,skypost
瑞达国际速递,ruidaex
丰程物流,sccod
德中快递,decnlh
全时速运,runhengfeng
云邮跨境快递,hkems
亚风速递,yafengsudi
快淘快递,kuaitao
鑫通宝物流,xtb
USPS,usps
加拿大邮政,canpostfr
汇通天下物流,httx56
台湾（中华邮政）,postserv
好又快物流,haoyoukuai
永旺达快递,yongwangda
木春货运,mchy
程光快递,flyway
百事亨通,bsht
万家通快递,timedg
全之鑫物流,qzx56
美快国际物流,meiquick
ILYANG,ilyang
先锋快递,xianfeng
亿顺航,yishunhang
尚橙物流,shangcheng
OnTrac,ontrac
TNT-全球件,tnten
顺丰-美国件,shunfengen
共速达,gongsuda
源伟丰,yuanweifeng
祥龙运通物流,xianglongyuntong
偌亚奥国际快递,nuoyaao
陪行物流,peixingwuliu
天天快递,tiantian
CCES/国通快递,cces
彪记快递,biaojikuaidi
安信达,anxindakuaixi
配思货运,peisihuoyunkuaidi
大田物流,datianwuliu
邮政快递包裹,youzhengguonei
文捷航空,wenjiesudi
BHT,bht
北青小红帽,xiaohongmao
GSM,gsm
汇强快递,huiqiangkuaidi
昊盛物流,haoshengwuliu
联邦快递-英文,lianbangkuaidien
伍圆速递,wuyuansudi
南京100,nanjing
全通快运,quantwl
宅急便,zhaijibian
加拿大(Canada Post),canpost
COE,coe
百通物流,buytong
友家速递,youjia
新元快递,xingyuankuaidi
中澳速递,cnausu
联合快递,gslhkd
河南次晨达,ccd
奔腾物流,benteng
今枫国际快运,mapleexpress
中运全速,topspeedex
中欧快运,otobv
宜家行,yjxlm
金马甲,jmjss
一号仓,onehcang
论道国际物流,lundao
顺通快递,stkd
globaltracktrace,globaltracktrace
德方物流,ahdf
速递中国,sendtochina
NLE,nle
亚欧专线,nlebv
信联通,sinatone
澳德物流,auod
微转运,wzhaunyun
iExpress,iexpress
远成快运,ycgky
高考通知书,emsluqu
安鲜达,exfresh
BCWELT,bcwelt
欧亚专线,euasia
乐递供应链,ledii
万通快递,gswtkd
特急送,lntjs
增速海淘,zyzoom
金大物流,jindawuliu
民航快递,minghangkuaidi
红马甲物流,sxhongmajia
amazon-国内订单,amcnorder
ABF,abf
小米,xiaomi
新元国际,xynyc
小C海淘,xiaocex
航空快递,airgtc
叮咚澳洲转运,dindon
环球通达,hqtd
新西兰中通,nzzto
良藤国际速递,lmfex
速达通,sdto
速品快递,supinexpress
海龟国际快递,turtle
韩国邮政,koreapostcn
韵丰物流,yunfeng56
易达通快递,qexpress
一运全成物流,yyqc56
泛远国际物流,farlogistis
达速物流,dasu
恒通快递,lqht
壹品速递,ypsd
鹰运国际速递,vipexpress
南方传媒物流,ndwl
速呈宅配,sucheng
云南滇驿物流,dianyi
四川星程快递,scxingcheng
运通中港快递,ytkd
Gati-英文,gatien
jcex,jcex
凯信达,kxda
安达信,advancing
亿翔,yxexpress
加运美,jiayunmeiwuliu
赛澳递,saiaodi
康力物流,kangliwuliu
鑫飞鸿,xinhongyukuaidi
全一快递,quanyikuaidi
华企快运,huaqikuaiyun
青岛安捷快递,anjiekuaidi
递四方,disifang
三态速递,santaisudi
成都立即送,lijisong
河北建华,hebeijianhua
风行天下,fengxingtianxia
一统飞鸿,yitongfeihong
海外环球,haiwaihuanqiu
DHL-中国件,dhl
西安城联速递,xianchengliansudi
一柒国际物流,yiqiguojiwuliu
广东通路,guangdongtonglu
中国香港骏辉物流,chunfai
三三国际物流,zenzen
比利时国际(Bpost international),bpostinter
海红for买卖宝,haihongmmb
FedEx-英国件（FedEx UK),fedexuk
FedEx-英国件,fedexukcn
叮咚快递,dingdong
MRW,mrw
Chronopost Portugal,chronopostport
西班牙(Correos de Espa?a),correosdees
丹麦(Post Denmark),postdanmarken
Purolator,purolator
法国大包、EMS-法文（Chronopost France）,chronopostfra
Selektvracht,selektvracht
蓝弧快递,lanhukuaidi
比利时(Belgium Post),belgiumpost
晟邦物流,nanjingshengbang
UPS Mail Innovations,upsmailinno
挪威（Posten Norge）,postennorge
瑞士(Swiss Post),swisspost
英国邮政小包,royalmailcn
英国小包（Royal Mail）,royalmail
DHL Benelux,dhlbenelux
DHL-荷兰（DHL Netherlands）,dhlnetherlands
OPEK,opek
Italy SDA,italysad
Fastway Ireland,fastway
DHL-波兰（DHL Poland）,dhlpoland
DPD,dpd
速通物流,sutongwuliu
荷兰邮政-中文(PostNL international registered mail),postnlcn
荷兰邮政(PostNL international registered mail),postnl
乌克兰EMS(EMS Ukraine),emsukraine
乌克兰邮政包裹,ukrpostcn
英国大包、EMS（Parcel Force）,parcelforce
YODEL,yodel
UBI Australia,gotoubi
红马速递,nedahm
云南诚中物流,czwlyn
万博快递,wanboex
腾达速递,nntengda
郑州速捷,sujievip
中睿速递,zhongruisudi
中天万运,zhongtianwanyun
新蛋奥硕,neweggozzo
七天连锁,sevendays
UPS-全球件,upsen
跨越速运,kuayue
全际通,quanjitong
UPS,ups
一邦速递,yibangwuliu
上海快通,shanghaikuaitong
品速心达快递,pinsuxinda
PostNord(Posten AB),postenab
城际速递,chengjisudi
户通物流,hutongwuliu
飞康达,feikangda
星晨急便,xingchengjibian
全日通,quanritongkuaidi
凤凰快递,fenghuangkuaidi
广东邮政,guangdongyouzhengwuliu
长宇物流,changyuwuliu
万家物流,wanjiawuliu
EMS-国际件-英文,emsinten
飞远配送,feiyuanvipshop
国美,gome
能达速递,ganzhongnengda
急先达,jixianda
凡宇快递,fanyukuaidi
希优特,xiyoutekuaidi
中通（带电话）,zhongtongphone
蓝镖快递,lanbiaokuaidi
佳吉快运,jiajiwuliu
宏品物流,hongpinwuliu
GLS,gls
原飞航,yuanfeihangwuliu
海红网送,haihongwangsong
TNT,tnt
元智捷诚,yuanzhijiecheng
国际包裹,youzhengguoji
城市100,city100
DPEX,dpex
芝麻开门,zhimakaimen
EMS-国际件,emsguoji
晋越快递,jinyuekuaidi
乐捷递,lejiedi
飞力士物流,flysman
百腾物流,baitengwuliu
品骏快递,pjbest
瓦努阿图(Vanuatu Post),vanuatu
巴巴多斯(Barbados Post),barbados
萨摩亚(Samoa Post),samoa
斐济(Fiji Post),fiji
英超物流,yingchao
TNY物流,tny
美通,valueway
新速航,sunspeedy
速方(Sufast),bphchina
华航快递,hzpl
Gati-KWE,gatikwe
Red Express,redexpress
Toll Priority(Toll Online),tollpriority
Estafeta,estafeta
港快速递,gdkd
墨西哥（Correos de Mexico）,mexico
罗马尼亚（Posta Romanian）,romanian
DPD Poland,dpdpoland
阿联酋(Emirates Post),emirates
新顺丰（NSF）,nsf
巴基斯坦(Pakistan Post),pakistan
Asendia USA,asendiausa
法国大包、EMS-英文(Chronopost France),chronopostfren
意大利(Poste Italiane),italiane
世运快递,shiyunkuaidi
新干线快递,anlexpress
飞洋快递,shipgce
贝海国际速递,xlobo
黄马甲,huangmajia
Toll,dpexen
如风达,rufengda
EC-Firstclass,ecfirstclass
DTDC India,dtdcindia
Safexpress,safexpress
泰国（Thailand Thai Post）,thailand
SkyNet Malaysia,skynetmalaysia
TNT Australia,tntau
马来西亚小包（Malaysia Post(Registered)）,malaysiapost
"马来西亚大包、EMS（Malaysia Post(parcel,EMS)）",malaysiaems
沙特阿拉伯(Saudi Post),saudipost
南非（South African Post Office）,southafrican
Mexico Senda Express,mexicodenda
MyHermes,myhermes
DPD Germany,dpdgermany
Nova Poshta,novaposhta
Estes,estes
TNT UK,tntuk
Deltec Courier,deltec
UPS Freight,upsfreight
TNT Italy,tntitaly
Mexico Multipack,multipack
葡萄牙（Portugal CTT）,portugalctt
Interlink Express,interlink
DPD UK,dpduk
乌克兰EMS-中文(EMS Ukraine),emsukrainecn
乌克兰小包、大包(UkrPost),ukrpost
TCI XPS,tcixps
高铁速递,hre
新加坡EMS、大包(Singapore Speedpost),speedpost
LaserShip,lasership
英国邮政大包EMS,parcelforcecn
同舟行物流,chinatzx
秦邦快运,qbexpress
skynet,skynet
忠信达,zhongxinda
门对门,menduimen
微特派,weitepai
海盟速递,haimengsudi
圣安物流,shenganwuliu
联邦快递,lianbangkuaidi
飞快达,feikuaida
EMS,ems
天地华宇,tiandihuayu
煜嘉物流,yujiawuliu
郑州建华,zhengzhoujianhua
大洋物流,dayangwuliu
递达速运,didasuyun
易通达,yitongda
邮必佳,youbijia
EMS-英文,emsen
闽盛快递,minshengkuaidi
佳惠尔,syjiahuier
KCS,kcs
ADP国际快递,adp
颿达国际快递,fardarww
颿达国际快递-英文,fandaguoji
林道国际快递,shlindao
中外运速递-中文,sinoex
中外运速递,zhongwaiyun
深圳德创物流,dechuangwuliu
林道国际快递-英文,ldxpres
中国香港(HongKong Post),hkpost
邦送物流,bangsongwuliu
华赫物流,nmhuahe
顺捷丰达,shunjiefengda
天马迅达,tianma
恒宇运通,hyytes
考拉国际速递,kaolaexpress
BlueDart,bluedart
日日顺快线,rrskx
运东西,yundx
黑狗物流,higo
鹏远国际速递,pengyuanexpress
安捷物流,anjie88
骏达快递,jdexpressusa
C&C国际速递,cncexp
北京EMS,bjemstckj
airpak expresss,airpak
荷兰邮政-中国件,postnlchina
大达物流,idada
益递物流,edlogistics
中外运,esinotrans
速派快递(FastGo),fastgo
易客满,ecmscn
美国云达,yundaexus
Toll,toll
深圳DPEX,szdpex
俄顺达,eshunda
广东速腾物流,suteng
新鹏快递,gdxp
平安达腾飞,pingandatengfei
穗佳物流,suijiawuliu
传喜物流,chuanxiwuliu
捷特快递,jietekuaidi
隆浪快递,longlangkuaidi
佳吉快递,jiajikuaidi
快达物流,kuaidawuliu
飞狐快递,feihukuaidi
潇湘晨报,xiaoxiangchenbao
巴伦支,balunzhi
安能物流,annengwuliu
申通快递,shentong
亿领速运,yilingsuyun
店通快递,diantongkuaidi
OCA Argentina,ocaargen
尼日利亚(Nigerian Postal),nigerianpost
智利(Correos Chile),chile
以色列(Israel Post),israelpost
京东物流,jd
奥地利(Austrian Post),austria
乌克兰小包、大包(UkrPoshta),ukraine
乌干达(Posta Uganda),uganda
阿塞拜疆EMS(EMS AzerExpressPost),azerbaijan
芬兰(Itella Posti Oy),finland
斯洛伐克(Slovenská Posta),slovak
阿鲁巴[荷兰]（Post Aruba）,aruba
爱尔兰(An Post),ireland
印度尼西亚EMS(Pos Indonesia-EMS),indonesia
易优包裹,eupackage
威时沛运货运,wtdchina
行必达,speeda
中通国际,zhongtongguoji
千顺快递,qskdyxgs
西邮寄,xipost
顺捷达,shunjieda
CE易欧通国际速递,cloudexpress
和丰同城,hfwuxi
天联快运,tlky
优速物流,youshuwuliu
埃塞俄比亚(Ethiopian postal),ethiopia
卢森堡(Luxembourg Post),luxembourg
毛里求斯(Mauritius Post),mauritius
文莱(Brunei Postal),brunei
Quantium,quantium
中铁物流,zhongtiewuliu
宇鑫物流,yuxinwuliu
巴林(Bahrain Post),bahrain
纳米比亚(NamPost),namibia
卢旺达(Rwanda i-posita),rwanda
莱索托(Lesotho Post),lesotho
肯尼亚(POSTA KENYA),kenya
喀麦隆(CAMPOST),cameroon
伯利兹(Belize Postal),belize
巴拉圭(Correo Paraguayo),paraguay
波黑(JP BH Posta),bohei
玻利维亚,bolivia
柬埔寨(Cambodia Post),cambodia
兰州伙伴物流,huoban
天纵物流,tianzong
坦桑尼亚(Tanzania Posts),tanzania
阿曼(Oman Post),oman
直布罗陀[英国]( Royal Gibraltar Post),gibraltar
博源恒通,byht
越南EMS(VNPost Express),vnpost
安迅物流,anxl
达方物流,dfpost
十方通物流,sfift
飞鹰物流,hnfy
UPS i-parcel,iparcel
鑫锐达,bjxsrd
孟加拉国(EMS),bangladesh
快捷速递,kuaijiesudi
日本（Japan Post）,japanposten
众辉达物流,zhdwl
秦远物流,qinyuan
澳邮中国快运,auexpress
日益通速递,rytsd
航宇快递,hangyu
急顺通,pzhjst
优速通达,yousutongda
飞邦快递,fbkd
华达快运,huada
FOX国际快递,fox
佳怡物流,jiayiwuliu
鹏程快递,pengcheng
冠庭国际物流,guanting
美国快递,meiguokuaidi
通和天下,tonghetianxia
音素快运,yinsu
创一快递,chuangyi
重庆星程快递,cqxingcheng
贵州星程快递,gzxingcheng
河南全速通,hnqst
快速递,ksudi
北极星快运,polarisexpress
6LS EXPRESS,lsexpress
ANTS EXPRESS,qdants
S2C,s2c
Hi淘易快递,hitaoe
CNAIR,cnair
易欧洲国际物流,yiouzhou
阳光快递,shiningexpress
北京丰越供应链,beijingfengyue
华中快递,cpsair
青旅物流,zqlwl
易航物流,yihangmall
城铁速递,cex
千里速递,qianli
急递,jdpplus
佳捷翔物流,jjx888
洋口岸,ykouan
考拉速递,koalaexp
天越物流,surpassgo
邮政标准快递,youzhengbk
运通快运,ytky168
卢森堡航空,cargolux
优优速递,youyou
全川物流,quanchuan56
SYNSHIP快递,synship
仓鼠快递,cangspeed
递五方云仓,di5pll
卓志速运,chinaicip
闪电兔,shandiantu
新宁物流,xinning
春风物流,spring56
首达速运,sdsy888
丽狮物流,lishi
雅澳物流,yourscm
直德邮,zdepost
日昱物流,riyuwuliu
Gati-中文,gaticn
派尔快递,peex
汇文,huiwen
东红物流,donghong
增益速递,zengyisudi
好运来,hlyex
顺丰速运,shunfeng
城际快递,chengji
程光快递,chengguangkuaidi
天翼快递,tykd
京东订单,jdorder
蓝天快递,lantiankuaidi
永昌物流,yongchangwuliu
笨鸟海淘,birdex
一正达速运,yizhengdasuyun
德意思,dabei
佐川急便,sagawa
优配速运,sdyoupei
速必达,subida
景光物流,jgwl
御风速运,yufeng
至诚通达快递,zhichengtongda
特急便物流,sucmj
亚马逊中国,yamaxunwuliu
货运皇,kingfreight
锦程物流,jinchengwuliu
澳货通,auex
澳速物流,aosu
澳世速递,aus
环球速运,huanqiu
麦力快递,mailikuaidi
瑞丰速递,rfsd
美联快递,letseml
CNPEX中邮快递,cnpex
鑫世锐达,xsrd
顺丰优选,sfbest
全峰快递,quanfengkuaidi
克罗地亚（Hrvatska Posta）,hrvatska
保加利亚（Bulgarian Posts）,bulgarian
Portugal Seur,portugalseur
International Seur,seur
久易快递,jiuyicn
Direct Link,directlink
希腊EMS（ELTA Courier）,eltahell
捷克（?eská po?ta）,ceskaposta
Siodemka,siodemka
爱尔兰(An Post),anposten
渥途国际速运,wotu
一号线,lineone
四海快递,sihaiet
德坤物流,dekuncn
准实快运,zsky123
宏捷国际物流,hongjie
鸿讯物流,hongxun
卡邦配送,ahkbps
凡客配送（作废）,vancl
瑞士邮政,swisspostcn
辉联物流,huilian
A2U速递,a2u
UEQ快递,ueq
中加国际快递,scic
易达通,yidatong
宜送,yisong
全球快运,abcglobal
芒果速递,mangguo
金海淘,goldhaitao
极光转运,jiguang
富腾达国际货运,ftd
DCS,dcs
捷网俄全通,ruexp
华通务达物流,htwd
申必达,speedoex
联运快递,lianyun
捷安达,jieanda
SHL畅灵国际物流,shlexp
EWE全球快递,ewe
顺邦国际物流,shunbang
成达国际速递,chengda
中环快递,zhonghuan
启辰国际速递,qichen
合众速递(UCS）,ucs
阿富汗(Afghan Post),afghan
白俄罗斯(Belpochta),belpost
冠捷物流,gjwl
钏博物流,cbo56
西翼物流,westwing
优邦速运,ubonex
首通快运,staky
马珂博逻,cnmcpl
小熊物流,littlebearbear
玥玛速运,yue777
上海航瑞货运,hangrui
星云速递,nebuex
环创物流,ghl
林安物流,lasy56
笨鸟国际,benniao
全速快递,fsexp
法翔速运,ftlexpress
易转运,ezhuanyuan
Superb Grace,superb
蓝天国际快递,ltx
圣飞捷快递,sfjhd
淘韩国际快递,krtao
容智快运,gdrz58
锦程快递,hrex
顺时达物流,hnssd56
骏绅物流,jsexpress
德国雄鹰速递,adlerlogi
远为快递,ywexpress
嗖一下同城快递,sofast56
开心快递,happylink
五六快运,wuliuky
卓烨快递,hrbzykd
ZTE中兴物流,zteexpress
尼尔快递,nell
高铁快运,gaotieex
万家康物流,wjkwl
国晶物流,xdshipping
德国云快递,yunexpress
宏递快运,hd
一起送,yiqisong
迈隆递运,mailongdy
新亚物流,nalexpress
艾瑞斯远,ariesfar
澳多多国际速递,adodoxm
CNUP 中联邮,cnup
UEX国际物流,uex
Hermes,hermes
PostElbe,postelbe
维普恩物流,vps
明辉物流,zsmhwl
联运通物流,szuem
龙象国际物流,edragon
永邦国际物流,yongbangwuliu
51跨境通,wykjt
速配欧翼,superoz
嘉里大荣物流,kerrytj
中国香港环球快运,huanqiuabc
CL日中速运,clsp
SQK国际速递,chinasqk
家家通快递,newsway
邮客全球速递,yyox
华瀚快递,hhair56
顺士达速运,shunshid
天翔东捷运,djy56
卓实快运,zhuoshikuaiyun
吉祥邮（澳洲）,jixiangyouau
蓝天快递,blueskyexpress
天天快物流,guoeryue
纵通速运,ynztsy
中通快运,zhongtongkuaiyun
CNE,cnexps
希腊包裹（ELTA Hellenic Post）,elta
星速递,starex
土耳其,ptt
哥伦比亚(4-72 La Red Postal de Colombia),colombia
加州猫速递,jiazhoumao
捷邦物流,jieborne
邮政国内,yzguonei
Canpar,canpar
海硕高铁速递,hsgtsd
日日通国际,rrthk
天翼物流,tywl99
啪啪供应链,papascm
万达美,wdm
安得物流,annto
广东诚通物流,gdct56
安达速递,adapost
易达国际速递,eta100
西游寄,xiyoug
光线速递,gxwl
易邮国际,euguoji
深圳邮政,szyouzheng
粤中国际货运代理（上海）有限公司,yuezhongsh
城通物流,chengtong
GE2D跨境物流,ge2d
败欧洲,europe8
飛斯特,bester
蒙古国(Mongol Post),mongolpost
乌拉圭（Correo Uruguayo）,correo
牙买加（Jamaica Post）,jamaicapost
格鲁吉亚(Georgian Pos）,georgianpost
美达快递,meidaexpress
驭丰速运,yfsuyun
无忧物流,aliexpress
邮鸽速运,ugoexpress
澳洲新干线快递,expressplus
标杆物流,bmlchina
长风物流,longvast
邮来速递,youlai
魔速达,mosuda
商桥物流,shangqiao56
AUV国际快递,auvexpress
Newgistics,newgistics
FQ狂派速递,freakyquick
泽西岛,jerseypost
威盛快递,wherexpess
运通速运,yuntong
老挝(Lao Express),lao
巴布亚新几内亚(PNG Post),postpng
EASY EXPRESS,easyexpress
壹米滴答,yimidida
飞云快递系统,fyex
跨跃国际,kyue
EMS包裹,emsbg
珠峰速运,zf365
甘肃安的快递,gansuandi
一辉物流,yatfai
e直运,edtexpress
wish邮,shpostwish
顶世国际物流,topshey
龙枫国际快递,lfexpress
安能快递,ane66
圆通快运,yuantongkuaiyun
宝通快递,baotongkd
美国汉邦快递,aplus100
易普递,sixroad
速呈,sczpds
海淘物流,ht22
海米派物流,haimibuy
天翔快递,tianxiang
易境达国际物流,uscbexpress
大韩通运,cjkoreaexpress
澳世速递,ausexpress
未来明天快递,weilaimingtian
科捷物流,kejie
大道物流,dadaoex
全联速运,guexp
可可树美中速运,excocotree
邮邦国际,youban
西安运逸快递,yyexp
Aplus物流,aplusex
锋鸟物流,beebird
青云物流,bjqywl
万邑通,winit
中翼国际物流,chnexp
亚洲顺物流,yzswuliu
E跨通,ecallturn
递四方美国,disifangus
星空国际,wlwex
极地快递,polarexpress
到了港,camekong
斯里兰卡(Sri Lanka Post),slpost
斯洛文尼亚(Slovenia Post),slovenia
多米尼加（INPOSDOM – Instituto Postal Dominicano）,inposdom
星运快递,staryvr
狮爱高铁物流,sycawl
爱拜物流,ibuy8
商海德物流,shd56
九宫物流,jiugong
缔惠盛合,twkd56
快服务,kfwnet
dhl小包,dhlecommerce
宇佳物流,yujiawl
湘达物流,xiangdawuliu
远盾物流,yuandun
黑猫宅急便,tcat
韵达快运,yundakuaiyun
速派快递,fastgoexpress
中集冷云,cccc58
久久物流,jiujiuwl
德国八易转运,deguo8elog
UTAO优到,utaoscm
乾坤物流,yatexpress
摩洛哥 ( Morocco Post ),morocco
尼泊尔（Nepal Postal Services）,nepalpost
伊朗（Iran Post）,iran
坦桑尼亚（Tanzania Posts Corporation）,posta
莫桑比克（Correios de Mo?ambique）,correios
聚中大,juzhongda
中邮电商,chinapostcb
鸿泰物流,hnht56
南非EMS,emssouthafrica
申通国际,stosolution
皮牙子快递,bazirim
联众国际,epspost
丰通快运,ftky365
BorderGuru,borderguru
艾姆勒,imlb2c
中欧国际物流,eucnrail
递四方澳洲,disifangau
艺凡快递,yifankd
宏观国际快递,gvpexpress
博茨瓦纳,botspost
塞内加尔,laposte
卡塔尔（Qatar Post）,qpost
苏丹（Sudapost）,sudapost
Sureline冠泰,sureline
海沧无忧,hivewms
安世通快递,astexpress
集先锋快递,jxfex
丰客物流,fecobv
同城快寄,shpost
海联快递,hltop
中联速递,auvanda
三象速递,sxexpress
神马快递,shenma
互联快运,hlkytj
温通物流,wto56kj
四海捷运,sihiexpress
苏通快运,zjstky
邦通国际,comexpress
劲通快递,jintongkd
凡仕特物流,wlfast
红背心,hongbeixin
居家通,homexpress
上大物流,shangda
中邮物流,zhongyouwuliu
Fedex-国际件-中文,fedexcn
韩国（Korea Post）,koreapost
中通快递,zhongtong
京广速递,jinguangsudikuaijian
FedEx-国际件,fedex
日日顺物流,rrs
微店,weidianorder
当当,dangdangorder
国送快运,guosong
考拉订单,kaolaorder
AAE-中国件,aae
四川快优达速递,kuaiyouda
百福东方,baifudongfang
TST速运通,tstexp
YUN TRACK,yuntrack
Aramex,aramex
蘑菇街,mogujieorder
嘉里大通,jialidatong
万象物流,wanxiangwuliu
澳大利亚(Australia Post),auspost
国通快递,guotongkuaidi
全晨快递,quanchenkuaidi
飞豹快递,feibaokuaidi
中速快递,zhongsukuaidi
优能物流,mantoo
国美,gomeorder
亚马逊中国订单,amazoncnorder
蜜芽订单,miaorder
顺丰订单,sfexpressorder
申通快运,stoe56
City-Link,citylink
德邦物流,debangwuliu
银捷速递,yinjiesudi
D速快递,dsukuaidi
民邦速递,minbangsudi
百世物流,baishiwuliu
DHL-德国件（DHL Deutschland）,dhlde
能装能送,canhold
聚美优品,jumeiyoupinorder
诚一物流,parcelchina
网易严选,wangyiyxorder
龙邦速递,longbanwuliu
明亮物流,mingliangwuliu
速尔快递,suer
盛辉物流,shenghuiwuliu
越丰物流,yuefengwuliu
比利时（Bpost）,bpost
韵达快递,yunda
唯品会,vipshoporder
美丽说,meilishuoorder
顺丰优选,sfbestorder
驼峰国际,humpline
小米订单,xiaomiorder
一智通,1ziton
TransRush,transrush
百世快递,huitongkuaidi
联昊通,lianhaowuliu
远成物流,yuanchengwuliu
FedEx-美国件,fedexus
OCS,ocs
巴西(Brazil Post/Correios),brazilposten
孔夫子,kongfzorder
一号店,yhdshoporder
卷皮,juanpiorder
淘宝订单,taobaoorder
盛丰物流,shengfengwuliu
瑞典（Sweden Post）,ruidianyouzheng
圆通速递,yuantong
宅急送,zhaijisong
新邦物流,xinbangwuliu
恒路物流,hengluwuliu
华夏龙,huaxialongwuliu
';
		$ReData = array();
		$DataStr = str_replace("\r\n", "\n", $DataStr);
		$DataStr = str_replace("\r", "\n", $DataStr);
		$DataStr = str_replace("\n\n", "\n", $DataStr);
		$DataArr = explode("\n", $DataStr);
		foreach ($DataArr AS $DataRow)
		{
			$DataRow = trim($DataRow);
			$arr = explode(",", $DataRow);
			if (count($arr) >= 2)
			{
				if ($KeyType == 0) {
					$ReData[trim($arr[0])] = trim($arr[1]);
				} else {
					$ReData[trim($arr[1])] = trim($arr[0]);
				}
			}
		}
		return $ReData;
	}
	
	

}
