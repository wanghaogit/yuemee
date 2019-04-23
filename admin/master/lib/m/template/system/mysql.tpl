{:include file="_g/header.tpl" Title="系统":}

<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>打开文件</caption>
	<tr>
		<th>已打开<br />show global status like 'open_files'</th>
		<th>最大允许<br />show variables like 'open_files_limit'</th>
	</tr>
	<tr>
		<td>{:$DataOpenFiles.open:}</td>
		<td>{:$DataOpenFiles.max:}</td>
	</tr>
</table>

<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>表锁情况</caption>
	<tr><th colspan="100">show global status like 'table_locks%'</th></tr>
	{:foreach from=$DataTableLocks item=Data:}
		<tr>
			<td>{:$Data.Variable_name:}：{:$Data.Value:}</td>
		</tr>
	{:/foreach:}
</table>

<!------------------------------------------------------------------------------------------------------------------------------------------------------------->

<br />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>正在进行的进程</caption>
	<tr><th colspan="100">SHOW FULL PROCESSLIST</th></tr>
	<tr>
		{:foreach from=$DataProcessList[0] key=key item=item:}
			<td>{:$key:}</td>
		{:/foreach:}
	</tr>
	{:foreach from=$DataProcessList item=Data:}
		<tr>
			{:foreach from=$Data key=key item=item:}
				<td>{:$item:}</td>
			{:/foreach:}
		</tr>
	{:/foreach:}
</table>

<br />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>当前被打开的表列表</caption> 
	<tr><th colspan="100">SHOW OPEN TABLES</th></tr>
	<tr>
		{:foreach from=$DataOpenTables[0] key=key item=item:}
			<td>{:$key:}</td>
		{:/foreach:}
	</tr>
	{:foreach from=$DataOpenTables item=Data:}
		<tr>
			{:foreach from=$Data key=key item=item:}
				<td>{:$item:}</td>
			{:/foreach:}
		</tr>
	{:/foreach:}
</table>

<br />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>InnoDB 状态</caption>
	<tr><th colspan="100">SHOW ENGINE INNODB STATUS</th></tr>
	<tr>
		<td>{:$DataInnoDBStatus.Status:}</td>
	</tr>
</table>

<br />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>运行状态</caption>
	<tr><th colspan="100">SHOW STATUS</th></tr>
	{:foreach from=$DataStatus item=Data:}
		<tr>
			<td>{:$Data.Variable_name:}：{:$Data.Value:}</td>
		</tr>
	{:/foreach:}
</table>

<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>运行状态值</caption>
	<tr><th colspan="100">show global status</th></tr>
	<tr>
		{:foreach from=$DataGlobalStatus[0] key=key item=item:}
			<td>{:$key:}</td>
		{:/foreach:}
	</tr>
	{:foreach from=$DataGlobalStatus item=Data:}
		<tr>
			{:foreach from=$Data key=key item=item:}
				<td>{:$item:}</td>
			{:/foreach:}
		</tr>
	{:/foreach:}
</table>

<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>配置参数</caption>
	<tr><th colspan="100">SHOW VARIABLES</th></tr>
	{:foreach from=$DataVariables item=Data:}
		<tr>
			<td>{:$Data.Variable_name:}：{:$Data.Value:}</td>
		</tr>
	{:/foreach:}
</table>

{:include file="_g/footer.tpl":}
