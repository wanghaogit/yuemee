{:include file="_g/header.tpl" Title="库存/品类":}

<table cellspacing="0" cellpadding="0" class="Grid">
    <caption>
	{:if $ParentCatagory:}{:$ParentCatagory->name:}{:/if:} 品类管理
	<a class="button button-blue" style="float:left;" href="/index.php?call=mall.catagory_create&clid={:if $ParentCatagory !== null:}{:$ParentCatagory->id:}{:else:}0{:/if:}"> <i class="fas fa-plus"></i> 新增品类 </a>
</caption>
<tr>
	<th rowspan="2">编号</th>
	<th rowspan="2">名称</th>
	<th rowspan="2">负责人</th>
	<th colspan="5">标志位</th>
	<th rowspan="2">排序权重</th>
	<th rowspan="2">操作</th>
</tr>
<tr>
	<th>隐藏</th>
	<th>会员</th>
	<th>VIP</th>
	<th>总监</th>
	<th>经理</th>
</tr>
{:if $ParentCatagory:}
    <tr>
        <td align="center">
            <i class="fas fa-reply"></i>
        </td>
        <td colspan="11">
            <a href="/index.php?call=mall.catagory&clid={:$ParentCatagory->parent_id:}">
                返回上级
            </a>
        </td>
    </tr>
{:/if:}
{:foreach from=$Result->Data item=S:}
    <tr>
        <td>{:$S.id:}</td>
        <td><a href="/index.php?call=mall.catagory&clid={:$S.id:}">{:$S.name:}</a></td>
        <td align="center">{:$S.manager_name:}</td>
        <td align="center">{:$S.is_hidden | boolean.iconic:}</td>
        <td align="center">{:$S.lv_user | boolean.iconic:}</td>
        <td align="center">{:$S.lv_vip | boolean.iconic:}</td>
        <td align="center">{:$S.lv_cheif | boolean.iconic:}</td>
        <td align="center">{:$S.lv_director | boolean.iconic:}</td>
        <td align="center">
			<a href="javascript:void(0);" onclick="" style="float:left;" title="向前一位"><i class="fas fa-arrow-circle-up"></i></a>
			{:$S.p_order:}
			<a href="javascript:void(0);" onclick="" style="float:right;" title="向后一位"><i class="fas fa-arrow-circle-down"></i></a>
		</td>
        <td class="operator">
            <a href="/index.php?call=mall.spu&clid={:$S.id:}">SPU</a>
            <a href="/index.php?call=mall.sku&clid={:$S.id:}">SKU</a>
			<a href="/index.php?call=mall.catagory_updata&cid={:$S.id:}">修改</a>
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
