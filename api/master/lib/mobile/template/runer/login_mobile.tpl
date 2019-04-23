<!DOCTYPE html>
<html>
	<head>
		<title>登录-手机号登录</title>
		<meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <!-- 样式 -->
        <link href="{:#URL_RES:}/v1/styles/mui.min.css" rel="stylesheet" />     <!-- 禁止修改：MUI 基本样式 -->
        <link href="{:#URL_RES:}/v1/styles/awesome.css" rel="stylesheet" />     <!-- 禁止修改：字体图标 -->
        <link href="{:#URL_RES:}/v1/styles/yuemi.css" rel="stylesheet" />       <!-- 阅米 公共样式 -->
        <link href="{:#URL_RES:}/v1/styles/ziima.css" rel="stylesheet" />
		<link href="{:#URL_RES:}/v1/styles/mui.picker.min.css" rel="stylesheet" /> 
		<link href="{:#URL_RES:}/v1/styles/mui.poppicker.css" rel="stylesheet" /> 
        <style type="text/css">
             /* 本页面临时样式表 */
            .grounding-img4 {
			    width: 13.4%;
			    float: right;
			}
			.mui-bar-nav~.mui-content {
			    padding-top: 30px;
			}
        </style>
        <!-- 描述：脚本 -->
		 
        <script src="{:#URL_RES:}/v1/scripts/mui.min.js"></script>             <!-- 禁止修改：MUI脚本库 -->
		<script src="{:#URL_RES:}/v1/scripts/jquery.js"></script>
		<script src="{:#URL_RES:}/v1/scripts/ziima.js"></script>
		<script src="{:#URL_RES:}/v1/scripts/page.js"></script>
		<script src="{:#URL_RES:}/v1/scripts/city.data-3.js"></script> 
		<script src="{:#URL_RES:}/v1/scripts/mui.picker.min.js"></script> 
		<script src="{:#URL_RES:}/v1/scripts/mui.poppicker.js"></script> 
		<script src="{:#URL_RES:}/v1/scripts/dropload.js"></script> 
		<script src="{:#URL_RES:}/v1/scripts/address_add.js"></script> 
		<script src="{:#URL_RES:}/v1/scripts/egCommen.js"></script> 
		<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/api.js"></script>
        <script type="text/javascript">
            /* 本页面临时/初始化专用JS */
        </script>
<style>
.mui-ym-view{
    width: 100%; background: white;
} 
.mui-table-ym{
    text-align: center;padding-top: 17%;padding-bottom: 18%;
}
.mui-ym-frist img{
    width:4%;position: absolute;left:10;z-index:5;padding-top: 10px;margin-left: 5px;
}
.mui-ym-frist input{
    border-style: none; border-bottom-style: solid;width: 89%;font-size:14px;padding-left: 30px;margin-bottom: 6%;
}
.mui-ym-sceond img{
    width:4%;position: absolute;left:10;z-index:5;padding-top: 10px;margin-left: 5px;
}
.mui-ym-sceond input{
    border-style: none; border-bottom-style: solid; width: 53%;font-size:14px;padding-left: 30px;margin-bottom: 6%;
}
.mui-ym-third{
    text-align: center;padding-top: 35%;
}
.mui-ym-third div{
    text-align: center;font-size: 14px;color: #e1e1e1;padding-bottom: 8%;
}


.mui-btn{
    background: #F2493D;
    color: white;
    width: 89%;
    border: 0;
    font-size: 18px;
    border-radius: 4px;
    text-align: center;
    line-height: 31px;
    font-size: 15px;
}
#yzm{
    background: #F2493D;
    color: white;
    width: 32%;
    border: 0;
    font-size: 18px;
    border-radius: 4px;
    text-align: center;
    line-height: 31px;
    font-size: 15px;
}
.mui-content>.mui-table-view:first-child{
   margin-top: 0;
}
.mui-table-view{
    position: static;
}
.footer-title a{
    color: #CCCCCC;
}
/*.XXXXXXXXXXXXXXX:before
{
    background-color:#FFFFFF
}*/
.mui-table-view:after{
    position: static;
}
</style>
	</head>
	<body style="background: white;">
		<form action="/mobile.php?call" method="get">
		<input type="hidden" name="call" value="{:$_RUNTIME->ticket->handler:}.bind_mobile" />
		<div class="mui-content ziima">
			<div class="mui-content">
				<div class="mui-table-view mui-ym-view">
					<div class="mui-table-ym">
						<img src="{:#URL_RES:}/v1/images/mobile/app-logo.png"  style="width: 5rem;"/>
					</div>
					<div class="mui-ym-content">
						<div class="mui-ym-frist" style="text-align: center;margin: 0 auto;width: 100%;">
							<img src="{:#URL_RES:}/v1/images/mobile/sj.png" style="width: 1rem;" />
							<input id="mobile" type="text" name="mobile" placeholder="手机号"/>
						</div>
						<div class="mui-ym-sceond" style="text-align: center;">
							<img src="{:#URL_RES:}/v1/images/mobile/dxm.png" style="width: 1rem;"/>
							<span><input id="code" name="code" type="text" placeholder="短信验证码"/></span>
							<span style="margin-left: 10px;"><button type="button" class="mui-btn mui-btn-primary mui-btn-outlined" id="yzm">获取验证码</button></span>
						</div>
						<div class="mui-button-row">
							<input type="hidden" value="{:$share_id:}" name="share_id" />
							<input type="hidden"   value="{:$union_id:}" name="union_id"/>
							<input  type="submit" class="mui-btn mui-btn-primary mui-btn-outlined" style="font-size: 16px;background: red;border:1px solid red;"  value="登录" />
							<div class="mui-ym-third" style="padding-top: 7.5rem;">
								<img class="wechatLogin" src="{:#URL_RES:}/v1/images/mobile/vip_1.png"  style="width: 2.5rem;"/>
								<div class="footer-title"><a class="wechatLogin" href="">微信快捷登录</a></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<script>
     $("#yzm").on("tap", function () {
		var mobile = $("#mobile").val();
		if (eg.isNullVal(mobile))
		{
			mui.toast("手机号不能为空");
			return false;
		}
		if (mobile && !eg.phone.test(mobile)) {
			mui.toast("手机号格式不正确");
			return  false;
		}
		eg.countDown("yzm");
		var short_message = {
			style: 1,
			mobile: mobile
		}
		YueMi.API.invoke('default', 'sms', short_message, function (d) {
			mui.toast("发送成功");
		}, function (target, request, response) {
			if (response.__message) {
				return mui.toast(response.__message);
			}
			mui.toast("发送失败，原因不请，请联系管理员!");
			console.log("target:" + JSON.stringify(target));
			console.log("request:" + JSON.stringify(request));
			console.log("response:" + JSON.stringify(response));
		});
	});

	/*$("#loginBtn").on("tap", function () {
		var mobile = $("#mobile").val();
		var code = $("#code").val();
		if (eg.isNullVal(mobile))
		{
			mui.toast("手机号不能为空");
			return false;
		}
		if (eg.isNullVal(code))
		{
			mui.toast("验证码不能为空");
			return false;
		}
		if (mobile && !eg.phone.test(mobile)) {
			mui.toast("手机号格式不正确");
			return  false;
		}
		/*plus.nativeUI.showWaiting('正在登陆', {
			modal: true,
			textalign: 'center'
		});*/
		
		/*YueMi.API.invoke('user', 'bind_mobile', {
			mobile: mobile,
			code: code,
			unionid:'oeG6I1bGFNr-8oxKyRAvLwMZh3-k'
		}, function (target, request, response) {
			//console.log(response);
			window.location.href = '/mobile.php?call=runer.order_confirm&share_id={:$share_id:}';
			console.log(response);
			return;
		}, function (target, request, response) {
			console.log(response);
			return;
		});
	});*/

	
	</script>
	</body>
</html>
