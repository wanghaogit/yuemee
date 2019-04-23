{:include file="_g/header.tpl" Title="库存/供应商":}
<table cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		供应商管理
		<a class="button button-blue" style="float:left;" href="/index.php?call=depot.supplier_create"> <i class="fas fa-plus"></i> 新增供应商 </a>
	</caption>
	<tr>
		<th rowspan="2">编号</th>
		<th rowspan="2">名称</th>
		<th colspan="5">会员</th>
		<th rowspan="2">状态</th>
		<th colspan="2">入站接口</th>
		<th colspan="2">出站接口</th>
		<th rowspan="2">入驻日期</th>
		<th rowspan="2">操作</th>
	</tr>
	<tr>
		<th>ID</th>
		<th>手机</th>
		<th>微信</th>
		<th>昵称</th>
		<th>身份</th>
		<th>识别码</th>
		<th>管理</th>
		<th>代码</th>
		<th>管理</th>
	</tr>
	{:foreach from=$Result->Data item=S:}
		<tr>
			<td>
				
			</td>
		</tr>
	{:/foreach:}
		<tr>
			<td colspan="14">{:include file="_g/pager.tpl" Result=$Result:}</td>
		</tr>
</table>
{:include file="_g/footer.tpl":}
