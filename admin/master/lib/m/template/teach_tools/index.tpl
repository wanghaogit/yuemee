{:include file="_g/header_teach.tpl" Title="首页":}
<div style="padding:10px; WORD-WRAP:break-word;">

	<div class="quote">
		<div class="quote-title">
			<strong>外链功能：</strong>
		</div>
		<div class="quote-content">
			<div style="width:380px;float:left">
				<a href="/index.php?call=test_api.index" target="_blank"><span style="color:blue">API测试工具</span></a>
			</div>
		</div>
	</div>

	{:foreach from=$ProcList key=DbName value=Plist:}
		<div style="clear:both;height:10px"></div>
		<div class="quote">
			<div class="quote-title" onclick="$('#Proc-{:$DbName:}').toggle()">
				<strong>存储过程 {:$DbName:}</strong>
			</div>
			<div class="quote-content" id="Proc-{:$DbName:}">
				{:foreach from=$Plist value=Pinfo:}
					<div style="float:left; width:250px">
						<a target="_blank" href="/?call=teach_tools.procedure_info&DbName={:$DbName:}&ProcName={:$Pinfo.name:}">{:$Pinfo.name:} - {:$Pinfo.comment:}</a>
					</div>
				{:/foreach:}
			</div>
		</div>
	{:/foreach:}

</div>
</body>
</html>
