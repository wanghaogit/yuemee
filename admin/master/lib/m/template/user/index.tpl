{:include file="_g/header.tpl" Title="用户":}
<link rel="stylesheet" type="text/css" href="/styles/user/usershow.css" />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		注册用户列表
	</caption>
	<tr>
		<td>查询</td>
		<td colspan="24">
			<form method="GET" action="/index.php">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				手机号码：<input type="text" class="input-mobile" name="m" value="{:$_PARAMS.m:}" />
				邀请人手机：<input type="text" class="input-mobile" name="v" value="{:$_PARAMS.v:}" />
				姓名：<input type="text"  name="n" value="{:$_PARAMS.n:}" />
				身份证：<input type="text"  name="c" value="{:$_PARAMS.c:}" />
				按身份查询: <select name="l" style="background-color: #fff;width:150px;height: 26px;">
					<option value="0" {:if $_PARAMS.l == 0:}selected="selected"{:/if:}>全部</option>
					<option value="1" {:if $_PARAMS.l == 1:}selected="selected"{:/if:}>总经理</option>
					<option value="2" {:if $_PARAMS.l == 2:}selected="selected"{:/if:}>总监</option>
					<option value="3" {:if $_PARAMS.l == 3:}selected="selected"{:/if:}>VIP</option>
					<option value="4" {:if $_PARAMS.l == 4:}selected="selected"{:/if:}>普通</option>
				</select>
				注册时间：
				<input type="text" class="input-date" id="search_time_start" name="search_time_start" readonly="readonly" value="{:$search_time_start | number.datetime:}" />
				-
				<input type="text" class="input-date" id="search_time_end" name="search_time_end" readonly="readonly" value="{:$search_time_end | number.datetime:}" />

				<input type="submit" value="查询" />
				
			</form>
		</td>
	</tr>
	<tr>
		<th rowspan="2">ID</th>
		<th rowspan="2">手机</th>
		<th rowspan="2">昵称</th>
		<th rowspan="2">邀请人</th>
		<th rowspan="2">邀请人电话</th>
		<th colspan="7">身份</th>
		<th colspan="3">实名</th>
		<th colspan="3">微信</th>
		<th colspan="2">头像</th>
		<th colspan="3">资料</th>
		<th rowspan="2">登陆</th>
		<th rowspan="2">操作</th>
	</tr>
	<tr>
		<th>会员</th>
		<th>VIP</th>
		<th>总监</th>
		<th>经理</th>
		<th>员工</th>
		<th>后台</th>
		<th>供应商</th>
		<th>记录</th>
		<th>姓名</th>
		<th>身份证</th>
		<th>APP</th>
		<th>公众号</th>
		<th>昵称</th>
		<th>微信</th>
		<th>本地</th>
		<th>性别</th>
		<th>地区</th>
		<th>注册</th>
	</tr>
	{:foreach from=$data->Data value=v:}
		<tr>
			<td class="zid">{:$v.id:}</td>
			<td align="right">{:$v.mobile | string.key_highlight $_PARAMS.m:}</td>
			<td>
				{:$v.name | string.key_highlight $_PARAMS.n:}
				<span style="float:right;">
					<a href="javascript:void(0);" title="改名"
					   onclick="javascript:_do_rename({:$v.id:},'{:$v.name:}');">
						<i class="fas fa-edit"></i>
					</a>
					<a href="javascript:void(0);" title="重置密码"
					   onclick="javascript:_do_passwd({:$v.id:},'{:$v.name:}');">
						<i class="fas fa-key"></i>
					</a>
				</span>
			</td>
			<td align="center">{:$v.invitor_name:}</td>
			<td align="center">{:$v.invmobile | string.key_highlight $_PARAMS.v :}</td>
			<td align="center">{:$v.level_u | boolean.iconic:}</td>
			<td align="center">{:$v.level_v | array.enum STATUS_NAMES.User.LevelVip:}</td>
			<td align="center">{:$v.level_c | array.enum STATUS_NAMES.User.LevelCheif:}</td>
			<td align="center">{:$v.level_d | array.enum STATUS_NAMES.User.LevelDirector:}</td>
			<td align="center">{:$v.level_t | array.enum STATUS_NAMES.User.LevelTeam:}</td>
			<td align="center">{:$v.level_a | array.enum STATUS_NAMES.User.LevelAdmin:}</td>
			<td align="center">{:$v.level_s | array.enum STATUS_NAMES.User.LevelSupplier:}</td>
			{:if $v.cert_exists:}
				<td align="center">{:$v.cert_exists | boolean.iconic:}</td>
				<td align="center">{:$v.cert_name:}</td>
				<td align="center">{:$v.cert_no | string.key_highlight $_PARAMS.c:}</td>
			{:else:}
				<td colspan="3" align="center"></td>
			{:/if:}
			{:if $v.app_open_id  != '' || $v.web_open_id != '':}
				<td align="center">{:$v.app_open_id | boolean.iconic:}</td>
				<td align="center">{:$v.web_open_id | boolean.iconic:}</td>
				<td align="center">{:$v.wechat_name:}</td>
			{:else:}
				<td colspan="3" align="center"></td>
			{:/if:}
			<td align="center">{:if $v.wechat_avatar:}
				<img src="{:$v.wechat_avatar:}" style="width:32px;height:32px;" />
				{:/if:}
			</td>
			<td align="center">{:if $v.avatar:}
				<img src="{:#URL_RES:}/upload{:$v.avatar:}" style="width:32px;height:32px;" />
				{:/if:}</td>
			<td>{:$v.gender | array.enum ['','男','女']:}</td>
			<td align="center">{:$v.province:}-{:$v.city:}-{:$v.country:}</td>
			<td>{:$v.reg_time | number.datetime:}</td>
			<td align="center">
				{:$v.token | boolean.iconic:}
				{:if $v.token:}
					<a href="javascript:void(0);" onclick="javascript:_do_kick({:$v.id:});">踢</a>
				{:/if:}
			</td>
			<td class="operator">
					{:if $v.level_u == 1:}
						<a href="javascript:void(0);" onclick="javascript:_do_disable({:$v.id:});" style="color:red;">禁用</a>
					{:else:}
						<a href="javascript:void(0);" onclick="javascript:_do_enable({:$v.id:});">启用</a>
					{:/if:}
				<a href="javascript:void(0);" onclick="javascript:_do_test_vip({:$v.id:},1);">测试VIP</a>
				<a href="javascript:void(0);" onclick="javascript:_do_test_vip({:$v.id:},365);">一年VIP</a>
			</td>
		</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="25">
			{:include file="_g/pager.tpl" Result=$data:}
			<b style="float:right;margin-right:30px;">总计：{:$count:}&nbsp;&nbsp;&nbsp;</b>
		</td>
	</tr>
</table>
<script type="text/javascript">
	$(".input-date").datetimepicker({
		autoclose: true,
		clearBtn: true,
		todayBtn: true,
		todayHighlight: true,
		fontAwesome: true,
		zIndex: 9999,
		format: 'yyyy-mm-dd hh:ii'
	});
	function _do_disable(id) {
		YueMi.API.Admin.invoke('user', 'user_disable', {
			__access_token: '{:$User->token:}',
			id: id,
		}, function (t, q, r) {
			location.reload();
		}, function (t, q, r) {

		});
	}

	function _do_enable(id) {
		YueMi.API.Admin.invoke('user', 'user_enable', {
			__access_token: '{:$User->token:}',
			id: id,
		}, function (t, q, r) {
			location.reload();
		}, function (t, q, r) {

		});
	}
	function _do_kick(userId) {
		YueMi.API.Admin.invoke('user', 'kick', {
			id: userId
		}, function (t, q, r) {
			location.reload();
		}, function (t, q, r) {
			alert('踢人失败：' + r.__message);
		});
	}
	function _do_test_vip(id,days){
		YueMi.API.Admin.invoke('vip', 'test', {
			user_id : id,
			days	: days,
		}, function (t, q, r) {
			if(confirm('测试VIP给予成功，是否查看VIP状态去？')){
				location.href = '/index.php?call=user.vip';
			}
		}, function (t, q, r) {
			alert('给予测试VIP失败：' + r.__message);
		});
	}
	function _do_rename(id,name){
		$.confirm({
			boxWidth: '400px',
			escapeKey: 'cancel',
			icon: 'fas fa-edit',
			title: '修改昵称',
			content: '新昵称：<input type="text" class="input-text" id="dlg_input_nickname" value="' + name + '" maxlength="32" size="40" />',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '修改',
					action: function () {
						YueMi.API.Admin.invoke('user','rename',{
							__access_token: '{:$User->token:}',
							id : id,
							name : $('#dlg_input_nickname').val().trim()
						},function(t,r,q){
							location.reload();
						},function(t,r,q){
							alert(q.__message);
						});
					}
				},
				cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
			}
		});
	}
	function _do_passwd(id){
		$.confirm({
			boxWidth: '400px',
			escapeKey: 'cancel',
			icon: 'fas fa-key',
			title: '重置密码',
			content: '新密码：<input type="text" class="input-text" id="dlg_input_newpwd" value="" maxlength="32" size="40" />',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '修改',
					action: function () {
						var pwd = $('#dlg_input_newpwd').val().trim();
						if(! /^[a-z0-9]{6,32}$/i.test(pwd)){
							$('#dlg_input_newpwd').focus().select();
						}							
						YueMi.API.Admin.invoke('default','passwd',{
							__access_token: '{:$User->token:}',
							id : id,
							op : '',
							np : pwd
						},function(t,r,q){
							alert('修改密码成功');
						},function(t,r,q){
							alert('修改密码失败。');
						});
					}
				},
				cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
			}
		});
	}
</script>
{:include file="_g/footer.tpl":}
