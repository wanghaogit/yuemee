{:include file="_g/header.tpl" Title="系统":}
<ul styel="height:300px;">
	<li>手机号</li>
	<li><input id="mobile"/></li>
	<li><input type="button" onclick="get_code();" value="查询"/></li>
	<li><div id="vinfo" style="height:50px;"></div></li>
</ul>
<script>
	function get_code(){
		var mobile = document.getElementById('mobile').value;
		mobile = mobile.replace(/(^\s*)|(\s*$)/g, ""); 
		var str = '<ul>';
		str += '<li>时间 | 验证码</li>';
		YueMi.API.Admin.invoke('system', 'get_code', {
			mobile:mobile
		}, function (t, q, r) {
			console.log(r.data);
			var str = '<ul>';
			str += '<li>时间 | 验证码</li>';
			$.each(r.data,function(key,val){
				console.log(val);
				str += '<li>'+val['time']+'|'+val['vcode']+'</li>';
			});
			str += '</ul>';
			document.getElementById('vinfo').innerHTML = '';
			document.getElementById('vinfo').innerHTML = str;
			//成功
		}, function (t, q, r) {
			//失败
		});
	}
</script>
{:include file="_g/footer.tpl":}
