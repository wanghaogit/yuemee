/*
 邀请入驻功能
 */
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
               window.location.href = '/mobile.php?call=download.index';
			} else {
			   alert(q.__message);
			}
		}, function (t, r, q) {
			weui.topTips(q.__message);
		});
	})
});
