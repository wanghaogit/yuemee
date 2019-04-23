<!DOCTYPE html>
<html style="background:#000;">
	<head>
		<title>阅米 - 管理后台</title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport' />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="/favicon.ico" rel="icon" type="image/x-icon" />
		<link rel="stylesheet" type="text/css" href="{:#URL_RES:}/v1/styles/awesome.css"/>
        <link rel="stylesheet" type="text/css" href="{:#URL_RES:}/v1/styles/ziima.css"/>
        <link rel="stylesheet" type="text/css" href="/styles/login.css"/>
		<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/jquery.js"></script>
		<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/ziima.js"></script>
		<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/dialog.js"></script>
		<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/menu.js"></script>
		<style type="text/css">
		</style>
		<script type="text/javascript">
			function check_login_form() {
				var u = $('#username').val();
				var p = $('#password').val();
				if (!/^1\d{10}$/.test(u)) {
					$('#username').focus().select();
					return false;
				}
				if (p.length < 6) {
					$('#password').focus().select();
					return false;
				}
				return true;
			}
		</script>
	</head>
	<body>
		<form method="POST" action="/index.php?call=default.login" onsubmit="javascript:return check_login_form();">
			<div class="login">				
				<div class="login_title">
					<image src="/images/login_logo.png" />
					<p>阅米总后台</p>
				</div>				
				<div class="login-top">					
					<p>手机号</p>					
					<input type="text" name="username" id="username" value="" placeholder="手机号" id="username"/>
					<p>前台密码</p>
					<input type="password" name="password" id="password" value="" placeholder="前台密码" id="password"/>
					{:if $_PARAMS.e > 0:}
					<p style="color:red;">出错啦：{:$_PARAMS.e | array.enum ['登陆成功','无此用户','检查身份失败','用户无效','非管理员','密码错','管理员帐户错','管理员状态错','管理角色错']:}</p>
					{:/if:}
					<div class="forgot">
						<input type="submit" value="登录" />
						<a href="#">忘记密码？</a>
					</div>
				</div>				
			</div>
			<div class="login-bottom">
				北京凯熙科技有限公司版权所有
			</div>
		</form>
	</body>
</html>