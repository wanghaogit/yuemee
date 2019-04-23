{:include file="_g/header_teach.tpl" Title="首页":}
<div style="padding:10px; WORD-WRAP:break-word;">

	<div class="quote">
		<div class="quote-title">
			<strong>存储过程 {:$_PARAMS.DbName:}.{:$_PARAMS.ProcName:}</strong>
		</div>
		<div class="quote-content">
			{:foreach from=$ParamList value=Pinfo:}
				{:if $Pinfo.PARAMETER_MODE == "IN":}
					<div style="clear:both; color:blue">
						<div style="float:left; width:66px">输入参数</div>
						<div style="float:left; width:50px">{:$Pinfo.DATA_TYPE:}</div>
						<div style="float:left; width:120px">{:$Pinfo.PARAMETER_NAME:}</div>
					</div>
				{:else:}
					<div style="clear:both">
						<div style="float:left; width:66px">输出参数</div>
						<div style="float:left; width:50px">{:$Pinfo.DATA_TYPE:}</div>
						<div style="float:left; width:120px">{:$Pinfo.PARAMETER_NAME:}</div>
					</div>
				{:/if:}
			{:/foreach:}
			<br />
			<span style="color:red">调语法：\{:$_PARAMS.DbName:}\ProcedureInvoker::Instance()->{:$_PARAMS.ProcName:}([参数列表]);</span>
		</div>
	</div>

</div>
</body>
</html>
