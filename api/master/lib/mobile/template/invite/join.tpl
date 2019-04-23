{:include file="_g/header.tpl" Title="邀请入驻":}
<style type="text/css">
	::-webkit-scrollbar { display:none } /* 移动端隐藏滚轮 */
	html { width:100%; height:100%; overflow-y:hidden; }
	body { width:100%; height:100%; background:#bd6200; }
	.PageBody { z-index:1; height:100%; width:100%; font-family:'苹方'; background:url('{:#URL_RES:}/v1/images/mobile/scrolla1.png')no-repeat; background-size:100% 100%; /* 必须放在background的后面才能生效 */ }
	.Page2Bg { position:absolute; z-index:10; background:#000; opacity:0.1;  }
	.Page2Body { position:absolute; top:100%; z-index:11; background:#FFF; color:#000; }
	.weui-cell:before { border:0 }
	.weui-cell:after { border:0 }
	.weui-cells:before { border:0 }
	.weui-cells:after { border:0 }
	.SendCode1 { background-color: #E64340 }
	.SendCode2 { background-color: #CCCCCC }
</style>
<script type="text/javascript" src="/scripts/join.js?v=1"></script>

<!-- ------------------------------ 第1页 start ------------------------------ -->
<div id="Page1" class='PageBody'>
	<img src="{:#URL_RES:}/v1/images/mobile/scrolla2.png"  style="width:100%; position:absolute; top:0px; z-index:2;" />
	<img src="{:#URL_RES:}/v1/images/mobile/scrrolla3.png" style="width:100%; position:absolute; bottom:0px; z-index:2;" />
	<div style="position:absolute; z-index:3; top:25%; width:100%; text-align:center; color:#FFFFFF; z-index:10">
		<img src="{:#URL_RES:}/v1/images/mobile/app-logo.png" style="width:89px" />
		<p style="font-size:28px; margin-top:30px">
			大众创业平台
		</p>
		<p style="font-size:15px">
			无需囤货、不需压货、动动手指、月入过万
		</p>
		<input id="JoinInput" type="button" style="color:#f2493d; background-color:#fff; border:1px solid #fff; width:42%; height:46px; border-radius:18px; margin-top:8%; font-size:22px;" value="立即加入" />
	</div>
</div>
<!-- ------------------------------ 第1页 end ------------------------------ -->

<!-- ------------------------------ 第2页 start ------------------------------ -->
<div id="Page2" class="PageBody" style="display:none">
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
				<div style="padding:10px"><a class="weui-btn weui-btn_warn">注册</a></div>
			</div>
		</div>
	</div>
</div>
<!-- ------------------------------ 第2页 end ------------------------------ -->

<script type="text/javascript">

	var Page2Interval = null;
	$('#JoinInput').click(function (ev) {
		$('#Page2').show();
		Page2Interval = window.setInterval("Page2UP()","1");
	});
	// 第1页向上移动
	var Page2i = 0;
	function Page2UP() {
		Page2i ++;
		n = 100 - Page2i;
		document.getElementById('Page2Body').style.top = n + "%";
		if (n < 1) {
			window.clearInterval(Page2Interval);
		}
	}

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

</script>
{:include file="_g/footer.tpl":}
