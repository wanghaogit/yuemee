{:include file="_g/header_teach.tpl" Title="提交测试 - API测试工具":}
<div style="padding:10px; WORD-WRAP:break-word;">

	<div class="quote">
		<div class="quote-title">
			<strong>
				<a href="/index.php?call=test_api.index">
					<span style="color:red">
						API测试首页
						-> {:$ClassInfo->action:}（{:$ClassInfo->name:}） 
					</span>
				</a> 
				-> {:$ActionInfo->action:}（{:$_PARAMS.action:}）
			</strong>
		</div>
		<div class="quote-content">
			<span style="color:blue">公共参数：</span><br />
				__udid <input type="text" id="__udid" value="1234567890123456789" style="width: 260px" /> 设备Id<br />
				__applet_token <input type="text" id="__applet_token" value="b31ed652c66e11b4" style="width: 260px" /> 应用Id<br />
				__access_token <input type="text" id="__access_token" value="{:$User->token:}" style="width: 260px" /> 当前总后台登录用户的AccessToken<br />
				__timestamp <input type="text" id="__timestamp" value="{:$TimeNow:}" style="width: 260px" /> 当前时间<br />
				__request_id <input type="text" id="__request_id" value="1" style="width: 260px" /> <br />
			<span style="color:blue">接口参数：</span><br />
			{:foreach from=$ActionInfo->params value=Ainfo:}
				{:if $Ainfo[0] == '@request':}
					{:$Ainfo[1]:}，{:$Ainfo[2]:} <input type="text" id="{:$Ainfo[1]:}" value="{:$Ainfo[5]:}" style="width: 260px" /> {:$Ainfo[3]:} <br />
				{:/if:}
			{:/foreach:}
			<div style="clear:both; padding:20px">
				<button type="button" onclick="LoadAPI()">&nbsp;&nbsp; 执行 &nbsp;&nbsp;</button>
			</div>
		</div>
	</div>
	<div style="clear:both;height:10px"></div>
	
	<div id="DirTime"></div>
	<div id="DirRequest"></div>
	<div id="DivReturn"></div>

</div>
</body>
</html>
<script type="text/javascript">

	function LoadAPI()
	{
		var data = 
		{
			__udid: $('#__udid').val(), // 设备Id
			__applet_token: $('#__applet_token').val(), // 应用Id
			__access_token: $('#__access_token').val(), // 登录token
			__timestamp: $('#__timestamp').val(), // 当前时间
			__request_id: $('#__request_id').val(), // ???
			{:foreach from=$ActionInfo->params value=Ainfo:}
				{:if $Ainfo[0] == '@request':}
					{:$Ainfo[1]:} : $('#{:$Ainfo[1]:}').val(),
				{:/if:}
			{:/foreach:}
		};
		$('#DirTime').html("请求时间 => " + new Date().format("yyyy-MM-dd hh:mm:ss"));
		$('#DirRequest').html("请求参数 => " + JSON.stringify(data));
		$.ajax({
			url : "{:#URL_API:}/?call={:$_PARAMS.class:}.{:$_PARAMS.action:}",
			type : 'POST',
			dataType : 'JSON',
			timeout: 30000,
			data: JSON.stringify(data),
			success : function(re) {
				$('#DivReturn').html("返回数据 => " + JSON.stringify(re));
			},
			error : function(re) { $('#DivReturn').html("返回数据 => " + JSON.stringify(re)); }
		});
	}

	Date.prototype.format = function(fmt)
	{
		var year    =   this.getFullYear();
		var month   =   this.getMonth()+1;
		var date    =   this.getDate();
		var hour    =   this.getHours();
		var minute  =   this.getMinutes();
		var second  =   this.getSeconds();
		fmt = fmt.replace("yyyy",year);
		fmt = fmt.replace("yy",year%100);
		fmt = fmt.replace("MM",fix(month));
		fmt = fmt.replace("dd",fix(this.getDate()));
		fmt = fmt.replace("hh",fix(this.getHours()));
		fmt = fmt.replace("mm",fix(this.getMinutes()));
		fmt = fmt.replace("ss",fix(this.getSeconds()));
		return fmt;

		function fix(n) {
			return n<10?"0"+n:n;
		}
	};

</script>
