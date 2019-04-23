{:include file="_g/header_teach.tpl" Title="API测试工具":}
<div style="padding:10px; WORD-WRAP:break-word;">

	{:foreach from=$ClassList value=ClassInfo:}
		<div class="quote">
			<div class="quote-title" onclick="$('#ClassName-{:$ClassInfo->name:}').toggle()"><strong>{:$ClassInfo->action:}（{:$ClassInfo->name:}）</strong></div>
			<div class="quote-content" id="ClassName-{:$ClassInfo->name:}" style="display:nonex">
				{:foreach from=$ClassInfo->methods_list key=Aname value=Ainfo:}
					<div style="width:380px;float:left">
						<a href="/index.php?call=test_api.post&class={:$ClassInfo->name:}&action={:$Aname:}" target="_blank">{:$ClassInfo->name:}.{:$Aname:}：{:$Ainfo->action:}</a>
					</div>
				{:/foreach:}
			</div>
		</div>
		<div style="clear:both;height:10px"></div>
	{:/foreach:}

</div>
</body>
</html>
