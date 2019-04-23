{:include file="_g/header.tpl" Title="团队":}
<table cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		员工管理
		<a class="button button-blue" style="float:left;" onclick="javascript:_do_create();"> <i class="fas fa-plus"></i> 新员工 </a>
	</caption>
	<tr>
		<th rowspan="2">编号</th>
		<th rowspan="2">名称</th>
		<th colspan="3">总经理</th>
		<th rowspan="2">创建</th>
		<th rowspan="2">操作</th>
	</tr>
	<tr>
		<th>会员</th>
		<th>VIP</th>
		<th>经理</th>
	</tr>
	{:foreach from=$Result->Data item=S:}
		<tr>
			<td>{:$S.id:}</td>
			<td><a href="/index.php?call=team.group&tid={:$S.id:}"{:$S.name:}<a></td>
			<td>
				uid
			</td>
			<td>
				vipid
			</td>
			<td>
				did
			</td>
			<td>
				{:$S.create_user:}
			</td>
			<td>
				<a href="javascript:void(0);" onclick="javascript:_do_drop({:$S.id:});">删除</a>
			</td>
		</tr>
	{:/foreach:}
		<tr>
			<td colspan="10">{:include file="_g/pager.tpl" Result=$Result:}</td>
		</tr>
</table>
<script type="text/javasctipt">
	function _do_create(id){
		
	};
	function _do_drop(id){
		
	};
</script>
{:include file="_g/footer.tpl":}
