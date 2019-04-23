{:include file="_g/header.tpl" Title="用户":}
<style>
	#show-img{
		width:600px;
		height:400px;
		position:absolute;
		left:300px;
		top:100px;
		z-index:100;
		display:none;
	}
</style>
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>实名认证列表</caption>
	<div id="show-img">
		<img id="imgbig" src="" style="width:100%;height:100%;"/>
	</div>
	<tr>
		<td>搜索</td>
		<td colspan="6">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				姓名：<input type="text"  name="n" value="{:$_PARAMS.n:}" />
				身份证：<input type="text"  name="c" value="{:$_PARAMS.c:}" />
				状态：
				<select name="s">
					<option value="0">--请选择--</option>
					<option value="1" {:if $_PARAMS.s == 1:}selected="selected"{:else:}{:/if:}>--待审--</option>
					<option value="2" {:if $_PARAMS.s == 2:}selected="selected"{:else:}{:/if:}>--通过--</option>
					<option value="3" {:if $_PARAMS.s == 3:}selected="selected"{:else:}{:/if:}>--拒绝--</option>
				</select>
				<input type="submit" value="搜索" />
			</form>
		</td>
	</tr>
	<tr>
		<th>用户ID</th>
		<th>姓名</th>
		<th>正面</th>
		<th>反面</th>
		<th>身份证号码</th>
		<th>认证状态</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=v:}
	<tr>
		<td class="zid">{:$v.user_id:}</td>
		<td align="right">{:$v.card_name | string.key_highlight $_PARAMS.n:}</td>
		<td><img src="{:#URL_RES:}/upload{:$v.card_pic1:}" style="width:50px;height:50px" class="img-sm" /></td>
		<td><img src="{:#URL_RES:}/upload{:$v.card_pic2:}" style="width:50px;height:50px" class="img-sm" /></td>
		<td>{:$v.card_no | string.key_highlight $_PARAMS.c:}</td>
		<td>
			{:if $v.status == 1 :}<span style="color:#333;">待审核</span>
			{:elseif $v.status == 2 :}<span style="color:green;">通过</span>
			{:elseif $v.status == 3 :}<span style="color:red;">拒绝</span>
			{:elseif $v.status == 0 :}<span style="color:#333;">草稿</span>
			{:else:}<span style="color:green;">通过</span>
			{:/if:}
		</td>
		<td>
			{:if $v.status == 1 :}<span style="color:#333;"><a href="javascript:void(0);" onclick="javascript:_do_examine({:$v.user_id:});">审核</a></span>
			{:elseif $v.status == 2 :}
			{:elseif $v.status == 3 :}
			{:elseif $v.status == 0 :}
			{:else:}
			{:/if:}
		</td>
	</tr>
	{:/foreach:}
	<!--tr class="summary">
		<td>小计</td>
		<td>￥123.45</td>
		<td colspan="3"></td>
	</tr-->
	<tr class="pager">
		<td colspan="7">
			{:include file="_g/pager.tpl" Result=$data:}
		</td>
	</tr>
</table>
<script>
	$('.img-sm').click(function () {
		var url = $(this).attr('src');
		$('#imgbig').attr('src', url);
		var X = $(this).offset().left;
		var Y = $(this).offset().top;
		$('#show-img').css('left', X - 80 + 'px').css('top', Y + 'px').toggle();
	});
	$('#show-img').click(function () {
		$(this).hide();
	});</script>
<script type="text/javascript">
	var API_ADMIN = new Invoker({
		udid: '000000000000000000000000',
		url: 'http://z.ym.cn/api.php',
		applet_token: 'b31ed652c66e11b41b6f7378',
		access_token: function () {
			var m = /\buser\_token\=([a-z0-9]+)\b/i.exec(document.cookie);
			if (m && m.length > 0) {
				return m[1];
			}
			return '';
		}
	});
	//用户删除
	function _do_remove(id) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fa fa-shield',
			title: '删除公告',
			content: '删除吗？',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '删除',
					action: function () {
						API_ADMIN.invoke('user', 'del_usercert', {
							__access_token: '{:$User->token:}',
							id: id
						}, function (t, r, q) {
							if (q.__code === 'OK')
							{
								location.reload();
							} else {
								alert(q.__message);
							}
						}, function (t, r, q) {

						});
					}
				},
				cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
			}
		});
	}
	//用户审核
	function _do_examine(id)
	{
		$.confirm
				({
					useBootstrap: false,
					type: 'blue',
					boxWidth: '300px',
					escapeKey: 'cancel',
					backgroundDismiss: false,
					backgroundDismissAnimation: 'glow',
					icon: 'fa fa-shield',
					title: '审核',
					content: '通过审核？',
					buttons:
							{
								accept:
										{
											btnClass: 'btn-red',
											text: '通过',
											action: function () {
												YueMi.API.Admin.invoke('user', 'cert_check_id_pass', {
													__access_token: '{:$User->token:}',
													userid: id,
													status: 2
												}, function (t, q, r) {
													alert('设置成功');
													location.reload();
												}, function (t, q, r) {
													alert('设置失败');
												});
											}
										},
								cancel:
										{
											text: '拒绝',
											btnClass: 'btn-blue',
											action: function ()
											{
												YueMi.API.Admin.invoke
														(
																'user', 'cert_check_id_pass',
																{
																	__access_token: '{:$User->token:}',
																	userid: id,
																	status: 3
																}, function (t, q, r) {
															alert('设置成功');
															location.reload();
														}, function (t, q, r) {
															alert('设置失败');
														}
														);
											}
										}
							}
				});
	}
</script>
{:include file="_g/footer.tpl":}
