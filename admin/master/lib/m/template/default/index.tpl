{:include file="_g/header.tpl" Title="首页":}
<script>
	function item() {
		YueMi.API.Local.invoke('mall', 'item', {
			__access_token: '{:$User->token:}',
			id: 7160
		}, function (t, q, r) {
			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function income() {
		YueMi.API.Local.invoke('oa', 'income', {

		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function hot_search() {
		YueMi.API.Local.invoke('runer', 'hotsearch', {

		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function cancle_older() {
		YueMi.API.Local.invoke('order', 'cancle', {
			access_token: '{:$User->token:}',
			order_id: 'P18042664R7F9QNS'
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function make_order_weixin() {
		YueMi.API.Open.invoke('order', 'make_order_weixin', {
			order_id: 'P180508LU5Z8VGED',
			is_merge_pay: 1
		}, function (t, q, r) {
			console.log(r);
			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function goods_info() {
		YueMi.API.Local.invoke('mall', 'list', {
			id: 129,
			catagory_id: 701,
			brand_id: 0,
			supplier_id: 0,
			keyword: '',
			sort: 'x',
			page: 0
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function delredis() {
		YueMi.API.Local.invoke('mall', 'delyanxuan', {

		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function my_pic() {
		YueMi.API.Local.invoke('vip', 'mypicture', {
			keyword: '',
			catagory: 1,
			page: 0
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function card() {
		YueMi.API.Local.invoke('oa_user', 'card', {

		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function chunv() {
		YueMi.API.Local.invoke('user', 'new_person', {

		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function today_get() {
		YueMi.API.Local.invoke('vip', 'today_get', {

		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function get_dpage() {
		YueMi.API.Local.invoke('runer', 'get_dpage', {
			id: 7
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}

	function userinfo() {
		YueMi.API.Local.invoke('user', 'info', {

		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function oa_team() {
		YueMi.API.Local.invoke('oa_user', 'team_info', {

		}, function (t, q, r) {
			
			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function wuliu_info() {
		YueMi.API.Local.invoke('order', 'load_info', {
			order_id: 'P1805311ZF0ERAV8  ',
			access_token: '{:$User->token:}'
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function my_invite_user() {
		YueMi.API.Local.invoke('vip', 'userlist', {
			access_token: '{:$User->token:}',
			begin : 0,
			end : 0,
			isapp : 2,
			page : 0
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}

	function make_card_cheif() {
		YueMi.API.Open.invoke('oa', 'make_cheif_card', {
			mobile: '18866668888',
			code: '0000',
			card_code: 'asda7777qq',
			number: '147896325123654789',
			name: '王'
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}

	function get_block_data() {
		YueMi.API.Local.invoke('runer', 'get_block_data', {
			
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	
	function vip_buyer() {
		YueMi.API.Local.invoke('vip', 'userbuy', {
			access_token: '{:$User->token:}',
			begin : 0,
			end : 0,
			keyword : '',
			page : 0
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function sell_info() {
		YueMi.API.Local.invoke('vip', 'sell_info', {
			access_token: '{:$User->token:}',
			begin : 0,
			end : 0,
			keyword : '',
			catagory_id : 1,
			page : 0,
			order : 2
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function select_vip() {
		YueMi.API.Local.invoke('vip', 'select_vip', {
			access_token: '{:$User->token:}',
			page : 0
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function sell_infobuyer() {
		YueMi.API.Local.invoke('vip', 'sell_info_buyer', {
			access_token: '{:$User->token:}',
			begin : 9999999,
			end : 0,
			keyword : '',
			catagory_id : 0,
			page : 0,
			order : 0,
			share : 1544
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function vip_wuliu() {
		YueMi.API.Local.invoke('vip', 'wuliu', {
			access_token: '{:$User->token:}',
			order_id :  'KC180531EESSSH2S'
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	
	function share_vip_order() {
		YueMi.API.Local.invoke('vip', 'inviteorder', {
			access_token: '{:$User->token:}',
			begin : 999999,
			end : 9999999999,
			status : 1,
			keyword : '',
			page : 0
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}

	function get_mall_item() {
		YueMi.API.Local.invoke('mall', 'item', {
			id: 34905
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function set_order() {
		YueMi.API.Local.invoke('order', 'create', {
			user_address_id: 28,
			sel_use_money: 0,
			sel_use_profit: 0,
			sel_use_recruit: 0,
			sel_use_ticket: 0
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function get_time() {
		YueMi.API.Local.invoke('runer', 'get_block_data', {
			access_token: '{:$User->token:}',
			block_id: 8
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function make_card_vip() {
		YueMi.API.Local.invoke('user', 'make_card_vip', {
			access_token: '{:$User->token:}',
			serial: 'tW7BuvVTzZ'
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function make_card() {
		YueMi.API.Local.invoke('user', 'make_card_vip', {
			serial:'tW7BuvVTzZ'
		}, function (t, q, r) {

			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function get_ip() {
		var ip = document.getElementById('IP').value;
		YueMi.API.Admin.invoke('default', 'get_ip', {
			ip : ip
		}, function (t, q, r) {
			document.getElementById('ipString').innerHTML = r.ip;
			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function get_date(){
		var timeStamp = document.getElementById('dateStamp').value;
		var date = new Date();  
		// 这里要注意了php时间戳是10位，js里是13位，注意自己转换。
		date.setTime(timeStamp * 1000); 
		var y = date.getFullYear();      
		var m = date.getMonth() + 1;      
		m = m < 10 ? ('0' + m) : m;      
		var d = date.getDate();      
		d = d < 10 ? ('0' + d) : d;      
		var h = date.getHours();    
		h = h < 10 ? ('0' + h) : h;    
		var minute = date.getMinutes();    
		var second = date.getSeconds();    
		minute = minute < 10 ? ('0' + minute) : minute;      
		second = second < 10 ? ('0' + second) : second;     
		var time = y + '-' + m + '-' + d+' '+h+':'+minute+':'+second;
		document.getElementById('timeString').innerHTML = time;
	}
</script>
<ul class="TaskPanel" style="width:400px;">
	<li>重要勿删</li>
	<li>
		主机名：{:#Z_HOSTNAME:}
	</li>
	<li>
		IP地址：{:#Z_IP:}
	</li>
	<li>
		MySQL读：{:#MYSQL_READER:}
	</li>
	<li>
		MySQL写：{:#MYSQL_WRITER:}
	</li>
	<li>
		Redis：{:#REDIS_HOST:}
	</li>
	<li>
		MongoDB：{:#MONGODB_HOST:}
	</li>
</ul>

<ul class="TaskPanel">
	<li>小工具</li>
	<li>时间戳转化：</li>
	<li><input id="dateStamp"/></li>
	<li><input type="button" onclick="get_date();" value="转化"/></li>
	<li><div id="timeString"></div></li>
	<li>ip转化：</li>
	<li><input id="IP"/></li>
	<li><input type="button" onclick="get_ip();" value="转化"/></li>
	<li><div id="ipString"></div></li>
</ul>

<ul class="TaskPanel">
	<li>OA</li>
	<li><a href="javascript:void(0);" onclick="javascript:income();">收入</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:card();">激活卡</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:today_get();">今日收入</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:goods_info();">商品详情1</a></li>
</ul>
<ul class="TaskPanel">
	<li>mall</li>
	<li><a href="javascript:void(0);" onclick="javascript:get_mall_item();">详情</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:get_mall_item();">商品检索列表</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:my_pic();">我的素材</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:chunv();">新手用户</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:userinfo();">userinfo</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:get_block_data();">get_block</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:get_dpage();">get_page1</a></li>
</ul>
<ul class="TaskPanel">
	<li>order</li>
	<li><a href="javascript:void(0);" onclick="javascript:set_order();">下单</a></li>
</ul>
<ul class="TaskPanel">
	<li>杂项测试</li>
	<li><a href="javascript:void(0);" onclick="javascript:get_time();">运营时间轴数据</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:make_card_vip();">卡充VIP</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:wuliu_info();">物流详情</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:make_card();">ka</a></li>
</ul>
<ul class="TaskPanel">
	<li>OA</li>
	<li><a href="javascript:void(0);" onclick="javascript:oa_team();">团队管理</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:share_vip_order();">分享订单</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:my_invite_user();">浏览用户</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:vip_buyer();">买家管理</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:sell_info();">销售明细</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:sell_infobuyer();">销售明细-买家</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:vip_wuliu();">VIP物流</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:select_vip();">看手底下的人</a></li>
</ul>

	<script>
		/**
		 * 价格计算器
		 * @param {object} cfg
		 * @returns {PriceCalculator}
		 */
		function PriceCalculator(cfg) {
			this.config = {
				input: {
					base: null,
					sale: null,
					vip: null,
					inv: null,
					ref: null,
					market: null,
					rebate: null
				}
			};
			this.target = {
				base: null,
				sale: null,
				vip: null,
				inv: null,
				ref: null,
				market: null,
				rebate: null
			};

			//读取参数
			if (cfg === undefined || cfg.input === undefined) {
				throw new Error('缺少配置');
			}
			this.config.input.base = cfg.input.base === undefined ? 'price_base' : cfg.input.base;
			this.config.input.sale = cfg.input.sale === undefined ? 'price_sale' : cfg.input.sale;
			this.config.input.vip = cfg.input.vip === undefined ? 'price_vip' : cfg.input.vip;
			this.config.input.inv = cfg.input.inv === undefined ? 'price_inv' : cfg.input.inv;
			this.config.input.ref = cfg.input.ref === undefined ? 'price_ref' : cfg.input.ref;
			this.config.input.market = cfg.input.market === undefined ? 'price_market' : cfg.input.market;
			this.config.input.rebate = cfg.input.rebate === undefined ? 'price_rebate' : cfg.input.rebate;
			this.target.base = document.getElementById(this.config.input.base);
			this.target.sale = document.getElementById(this.config.input.sale);
			this.target.vip = document.getElementById(this.config.input.vip);
			this.target.inv = document.getElementById(this.config.input.inv);
			this.target.ref = document.getElementById(this.config.input.ref);
			this.target.market = document.getElementById(this.config.input.market);
			this.target.rebate = document.getElementById(this.config.input.rebate);

			//传递 this
			var _self = this;

			//规则实现
			this.check = function () {

			};

			//绑定事件
			if (this.target.base === null) {
				throw new Error('缺少成本价配置');
			}
			this.target.base.addEventListener('change', function () {

			});
		}

		var calc = new PriceCalculator({
			input: {
				base: 'price_base',
				sale: 'price_sale',
				vip: 'price_vip',
				inv: 'price_inv',
				ref: 'price_ref',
				market: 'price_market',
				rebate: 'rebate_vip'
			}
		});

	</script>
	{:include file="_g/footer.tpl":}
