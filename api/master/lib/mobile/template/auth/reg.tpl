{:include file="_g/header.tpl" Title="手机认证":}
<!-- ------------------------------ start ------------------------------ -->
<div id="Page2" class="PageBody">
	<div id="Page2Bg" class="PageBody Page2Bg"></div>
	<div id="Page2Body" class="PageBody Page2Body">
		<img src="{:#URL_RES:}/v1/images/mobile/bactop.png" style="width:100%; position:absolute; top:0px; z-index:12;" />
		<img src="{:#URL_RES:}/v1/images/mobile/bacbot.png" style="width:100%; position:absolute; bottom:0px; z-index:12;" />
		<div class="weui-cells weui-cells_form" style="position:absolute; z-index:13; top:25%; width:100%">
			<input type="hidden" id="txt_wx_unionid" value="{:$Wechat->union_id:}" />
			<div class="weui-cell">
				<div class="weui-cell__hd" style="width:28px">
					<label class="weui-label">
						<img src="{:#URL_RES:}/v1/images/mobile/sj.png" style="width:20px">
					</label>
				</div>
				<div class="weui-cell__bd">
					<input id="txt_user_mobile" class="weui-input" type="number" pattern="[0-9]*" placeholder="手机号" />
				</div>
			</div>
			<div class="weui-cell weui-cell_vcode">
				<div class="weui-cell__hd" style="width:28px">
					<label class="weui-label">
						<img src="{:#URL_RES:}/v1/images/mobile/dxm.png" style="width:20px" />
					</label>
				</div>
				<div class="weui-cell__bd">
					<input id="txt_user_code" class="weui-input" type="tel" placeholder="短信验证码" />
				</div>
				<div class="weui-cell__ft">
					<div>
						<a id="btn_send_sms" href="javascript:sendCode();" class="weui-btn weui-btn_warn">获取验证码</a>
					</div>
				</div>
				<div class="weui-cell__ft" style="width:10px"></div>
			</div>
			<div id="btn_submit">
				<div style="padding:10px"><a class="weui-btn weui-btn_warn">确定</a></div>
			</div>
		</div>
	</div>
</div>
<!-- ------------------------------ end ------------------------------ -->
{:include file="_g/footer.tpl":}
<script type="text/javascript">

	// 发送验证码
	var SendMsgInterval = '';
	var SendMsgNums = 60;
	function sendCode()
	{
		if ($('#btn_send_sms').hasClass('SendCode2')){
			return;
		}
		SendMsgNums = 60;
		
		var m = $('#txt_user_mobile').val().trim();
		if (m.length < 1) {
			weui.topTips('请输入手机号码');
			$('#txt_user_mobile').focus();
			return;
		}
		if (!/^1\d{10}$/.test(m)) {
			weui.topTips('请输入正确格式的手机号码');
			$('#txt_user_mobile').focus().val('');
			return;
		}
		$('#btn_send_sms').attr('disabled', '1');
		YueMi.API.invoke('default', 'sms', {
			style: 1,
			mobile: m
		}, function (t, r, q) {
			weui.topTips('验证码发送成功，请注意查看短信通知。');
		}, function (t, r, q) {
			weui.topTips('验证码发送失败，请稍候重试。');
		});
		$("#btn_send_sms").removeClass("SendCode1");
		$("#btn_send_sms").addClass("SendCode2");
		document.getElementById("btn_send_sms").innerHTML = SendMsgNums + '秒后重新获取';
		SendMsgInterval = window.setInterval(doLoop,"1000");
	}
	function doLoop()
	{
		SendMsgNums --;
		if (SendMsgNums > 0) {
			document.getElementById("btn_send_sms").innerHTML = SendMsgNums + '秒后重新获取';
		} else {
			SendMsgNums = 60; // 重置时间
			window.clearInterval(SendMsgInterval); //清除js定时器
			$("#btn_send_sms").addClass("SendCode1");
			$("#btn_send_sms").removeClass("SendCode2");
			document.getElementById("btn_send_sms").innerHTML = '重新发送验证码';
		}
	}

	$(document).ready(function () {
		// 提交注册
		$('#btn_submit').click(function () {
			var m = $('#txt_user_mobile').val().trim();
			var c = $('#txt_user_code').val().trim();
			var unid=$('#txt_wx_unionid').val().trim();
			if (m.length < 1) {
				weui.topTips('请输入手机号码');
				$('#txt_user_mobile').focus();
				return;
			}  
			if (!/^1\d{10}$/.test(m)) {
				weui.topTips('请输入正确格式的手机号码');
				$('#txt_user_mobile').focus().val('');
				return;
			}
			if(c.length < 1){
				weui.topTips('请输入您手机收到的短信验证码。');
				$('#txt_user_code').focus();
				return;
			}
			if (!/^1\d{10}$/.test(m)) {
				weui.topTips('请输入正确格式的手机号码');
				$('#txt_user_mobile').focus().val('');
				return;
			}
			// 调用API（绑定手机号）
			YueMi.API.invoke('user', 'bind_mobile', {
				mobile: m,
				code:c,
				unionid:unid
			}, function (t, r, q) {
				console.log(q);
				if (q.__code == 'OK') {
				   window.location.href = '{:$ReturnUrl:}';
				} else {
				   alert(q.__message);
				}
			}, function (t, r, q) {
				weui.topTips(q.__message);
			});
		})
	});
</script>
