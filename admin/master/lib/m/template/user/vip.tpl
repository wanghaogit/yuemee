{:include file="_g/header.tpl" Title="VIP":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption style="text-align: left;">
		VIP管理
	</caption>
	<tr>
		<td>
			搜索
		</td>
		<td colspan="12">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				被邀请人：<input type="text"  name="n" value="{:$_PARAMS.n:}" />
				手机号码：<input type="text"  name="m" value="{:$_PARAMS.m:}" />
				邀请人：<input type="text"  name="i" value="{:$_PARAMS.i:}" />
				邀请人手机：<input type="text"  name="im" value="{:$_PARAMS.im:}" />
				到期时间：
				<input type="text" class="input-date" id="search_time_start" name="search_time_start" readonly="readonly" value="{:$search_time_start | number.datetime:}" />
				-
				<input type="text" class="input-date" id="search_time_end" name="search_time_end" readonly="readonly" value="{:$search_time_end | number.datetime:}" />
				<br /><br />
				激活时间：
				<input type="text" class="input-date" id="search_time_start" name="search_time_start_c" readonly="readonly" value="{:$search_time_start_c | number.datetime:}" />
				-
				<input type="text" class="input-date" id="search_time_end" name="search_time_end_c" readonly="readonly" value="{:$search_time_end_c | number.datetime:}" />

				状态：
				<select name="s">
					<option value="0">--请选择--</option>
					<option value="1">--测试--</option>
					<option value="2">--免费--</option>
					<option value="3">--卡邀--</option>
					<option value="4">--兑换--</option>
					<option value="5">--付费--</option>
				</select>
				<input type="submit" value="搜索" id="subit" />
			</form>
		</td>
	</tr>
	<tr>
		<th>用户ID</th>
		<th>用户名</th>
                <th>头像</th>
		<th>实名</th>
		<th>手机号</th>
		<th>邀请人</th>
		<th>邀请人手机</th>
		<th>VIP状态</th>
		<th>总监身份</th>
		<th>总经理身份</th>
		<!--<th>创建时间</th>-->
		<th>VIP到期时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data value=B:}
	<tr>
		<td>{: $B.user_id :}</td>
		<td><a href="/index.php?call=user.vip_money&uid={: $B.user_id :}">{:$B.uname  | string.key_highlight $_PARAMS.n:}</a></td>
                <td>{:if $B.upic == 'https://r.yuemee.com/upload' :}无{:else:}<img src="{:$B.upic:}" style="width:50px;">{:/if:}</td>
		<td>{:$B.cname:}</td>
		<td>{:$B.um | string.key_highlight $_PARAMS.m:}</td>
		<td>{:$B.gname | string.key_highlight $_PARAMS.i:}</td>
		<td>{:$B.im | string.key_highlight $_PARAMS.im:}</td>
		<td align="center">{:$B.status | array.enum STATUS_NAMES.User.LevelVip:}</td>
		<td>{:if $B.level_c > 0:}<span style="color:green;font-weight:bold;">√</span>{:else:}{:/if:}</td>
		<td>{:if $B.level_d > 0:}<span style="color:green;font-weight:bold;">√</span>{:else:}{:/if:}</td>
		<!--<td>{: $B.create_time | number.datetime :}</td>-->
		<td>{: $B.expire_time | number.datetime :}</td>
		<td>
			<a href="/index.php?call=user.vipinv_pic&uid={:$B.user_id:}">查看族谱</a>
			|
			<a href="/index.php?call=cheif.share_order&uid={:$B.user_id:}">分享订单</a>
			|
			<a href="/index.php?call=cheif.share_good&uid={:$B.user_id:}">分享商品</a>
			|
			<a href="/index.php?call=user.share_money&uid={:$B.user_id:}">销售佣金</a>
			{:if $B.isreissue == 1:}|
			<a onclick="reissue({: $B.user_id :});">补单</a>
			{:/if:}
		</td>
		</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
			<b style='float:right;line-height:30px;margin-right:30px;'>总经理：{:$director_num:}&nbsp;&nbsp;总监：{:$cheif_num:}&nbsp;&nbsp;<b style="color:red;">VIP共：{:$vip_num:}</b>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;共计：{:$sum['sum']:}&nbsp;&nbsp;&nbsp;</b>
		</td>
	</tr>
</table>
<div style="width: 720px;height:580px;position: absolute;top:50%;margin-top: -240px;left:50%;margin-left: -360px;display: none;border:1px solid #333;background-color: #fff" id="reissue_div">
	<div style="text-align:center;font-size:30px;margin-top: 30px;">
		<span>补发货物</span>
	</div>
	<ul style="margin-left: 30px;">
		<li style="margin-top: 20px;">
			订 单 号  : <input id="order_id" name="order_id" readonly="true">
		</li>
		<li style="margin-top: 20px;">
			礼包选择 : 
			<select id="gifts" name="sku" style="background-color:#fff">
				{:foreach from=$gifts value=S:}
				<option value="{:$S.id:}">{:$S.title:}</option>
				{:/foreach:}
			</select>
		</li>
		<li style="margin-top: 20px;">
			礼包价格 : <input id="price" value="399" readonly="true">
		</li>
		<li style="margin-top: 20px;">
			收 货 人 : <input id="name">
		</li>
		<li style="margin-top: 20px;">
			手 机 号 : <input id="mobile">
		</li>
		<li style="margin-top: 20px;">
			送货地址 : <input id="old_address" readonly="true" style="width:396px;">
			<input id="old_region_id" name="address_id" type="hidden">
		</li>
		<li style="margin-top: 20px;">
			新建地址 : <input type="text" class="input-region" id="region" name="region"/>
			<script>
				$('#region').createRegionSelector({
					level: 'country'
				});
			</script>

		</li>
		<li style="margin-top: 20px;">
			详细地址 : <input type="text" class="input-text" id="address" size="60" maxlength="60" name="address"/>
		</li>
		<li style="margin-top: 20px;">
			补单原因 : <input type="text" id="why" name="why" value="未知"/>
		</li>

	</ul>
	<input id="user_hidden" name="user_hidden" type="hidden">
	<input type="button" value="保存" onclick="save_order();" style="position: absolute;left: 40%;width:60px;margin-left: -30px;bottom: 30px;"/>
	<input type="button" value="关闭" onclick="close_div();" style="position: absolute;left: 60%;width:60px;margin-left: -30px;bottom: 30px;"/>
</div>
<script>
	$(".input-date").datetimepicker({
		autoclose: true,
		clearBtn: true,
		todayBtn: true,
		todayHighlight: true,
		fontAwesome: true,
		zIndex: 9999,
		format: 'yyyy-mm-dd hh:ii'
	});


	function reissue(id) {
		var divobj = document.getElementById('reissue_div');
		document.getElementById('user_hidden').value = id;
		divobj.style.display = "";
		get_orderid();
		get_userinfo(id);
		$('#why').val('未知');
	}
	function close_div() {
		var divobj = document.getElementById('reissue_div');
		divobj.style.display = "none";
	}
	function get_userinfo(id) {
		YueMi.API.Admin.invoke('user', 'reissue_info', {
			__access_token: '{:$User->token:}',
			user_id: id
		}, function (t, q, r) {
			console.log(r);
			document.getElementById('old_address').value = r.info;
			document.getElementById('old_region_id').value = r.id;
			document.getElementById('name').value = r.mobile;
			document.getElementById('mobile').value = r.name;
		}, function (t, q, r) {

		});
	}
	function get_orderid() {
		YueMi.API.Admin.invoke('user', 'order_id', {
			__access_token: '{:$User->token:}'
		}, function (t, q, r) {
			document.getElementById('order_id').value = r.id;
		}, function (t, q, r) {

		});
	}
	function save_order() {
		if (document.getElementById('old_region_id').value == 0 && document.getElementById('region').value == 0) {
			alert('请选择收货地址');
			return;
		}
		if($('#why').val() == '未知'){
			alert('请输入补发原因');
			return;
		}
		
		YueMi.API.Admin.invoke('user', 'reissue', {
			__access_token: '{:$User->token:}',
			order_id: document.getElementById('order_id').value,
			sku_id: document.getElementById('gifts').value,
			old_region_id: document.getElementById('old_region_id').value,
			region_id: document.getElementById('region').value,
			address: document.getElementById('address').value,
			user_id: document.getElementById('user_hidden').value,
			name: document.getElementById('name').value,
			mobile: document.getElementById('mobile').value,
			why: document.getElementById('why').value
		}, function (t, q, r) {
			location.reload();
		}, function (t, q, r) {
			alert(r.__message);
		});

	}
	$('#gifts').change(function () {
		var skuid = $(this).val();
		YueMi.API.Admin.invoke('sku', 'get_priceinv', {
			__access_token: '{:$User->token:}',
			skuid: skuid
		}, function (t, q, r) {
			$('#price').val(r.price_inv);
		}, function (t, q, r) {

		});
	});
</script>
{:include file="_g/footer.tpl":}