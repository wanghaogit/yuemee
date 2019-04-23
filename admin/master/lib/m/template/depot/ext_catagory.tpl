{:include file="_g/header.tpl" Title="库存/品类":}

<table cellspacing="0" cellpadding="0" class="Grid">
    <caption>
        品类管理
    </caption>
    <tr>
        <th>编号</th>
        <th>名称</th>
        <th>映射分类</th>
        <th>操作</th>
    </tr>
    {:foreach from=$Result->Data item=S:}
		<tr>
			<td>{:$S.id:}</td>
			<td><a href="/index.php?call=depot.ext_catagory&suid={:$_PARAMS.suid:}&clid={:$S.id:}">{:$S.name:}</a></td>
			<td align="center">{:$S.map_id:}</td>
			<td class="operator">
				<a href="/index.php?call=depot.recat_change&id={:$S.id:}&tabid={:$suid:}">重新映射</a>
			</td>
		</tr>
    {:/foreach:}
    <tr class="paging">
        <td colspan="12">{:include file="_g/pager.tpl" Result=$Result:}</td>
    </tr>
</table>
<script type="text/javascript">

</script>
{:include file="_g/footer.tpl":}
