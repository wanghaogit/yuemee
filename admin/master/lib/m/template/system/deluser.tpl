{:include file="_g/header.tpl" Title="用户清理":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		<B>用户清理</B>
	</caption>
	<tr>
		<form action="" method="POST">
			<td colspan="100" style="background-color:#F8F8FF; padding:8px 5px 8px 5px">
				&nbsp; 手机号:&nbsp;<input id="search_mobile" name="search_mobile" value="{:$search_mobile:}" />
				&nbsp; 微信UnionId:&nbsp;<input id="search_unionid" name="search_unionid" style="width:220px" value="{:$search_unionid:}" />
				&nbsp; 操作密码:&nbsp;<input name="password" />
				<input type="button" onclick="GetUserInfo()" value="查询用户信息" />
				<input type="submit" value="确定执行删除" style="background:red" />
				<br />
				<b style='color:blue'>在微信中打开下面的地址即可查询自己用户的相关信息：</b><br />
				https://a.yuemee.com/mobile.php?call=D71CSR3J2AGKDCE9.index&Password=SHXFQE12ACU8CUNU3MKTGO82VRPN0ZFK
			</td>
		</form>
	</tr>
	<tr>
		<td>
			<div id="HintTime"></div>
			<div id="HintBody"></div>
		</td>
	</tr>
</table>
<script type="text/javascript">

	// 弹出提示
	{:if !empty($DelHintStr):}
		alert("{:$DelHintStr:}");
	{:/if:}

	// 查询用户信息
	function GetUserInfo()
	{
		$('#HintTime').html("请求时间 => " + new Date().format("yyyy-MM-dd hh:mm:ss"));
		YueMi.API.Admin.invoke('system', 'deluser_info', {
			mobile: $("#search_mobile").val(),
			unionid: $("#search_unionid").val(),
		}, function (t, q, r) {
			$('#HintBody').html(r.data);
		}, function (t, q, r) {
			if (r.__code == 'E_AUTH') {
				alert("登录超时，请重新登录!");
				window.location.href = '/index.php?call=default.login';
			}
			$('#HintBody').html("错误信息：" + r.__message);
		});
	}

</script>
{:include file="_g/footer.tpl":}
