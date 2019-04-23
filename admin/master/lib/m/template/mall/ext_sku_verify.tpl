{:include file="_g/header.tpl" Title="SKU审核":}
<link rel="stylesheet" type="text/css" href="/styles/material_manager.css" />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">

	<tr>
		<th>ID</th>
		<th>商品名称</th>
		<th>供应商</th>
		<th>变更项目</th>
		<th>备注信息</th>
		<th>时间</th>
		
	</tr>
	{:foreach from=$data->Data value=SKU:}
	<tr>
		<td>
			{:$SKU.id:}
		</td>
		<td><a href="/index.php?call=extsku.extsku_info&ext_sku_id={:$SKU.id:}">{:$SKU.id | array.find $Sku,'id','name','':}</a></td>
		<td>{:$SKU.supplier_id | array.find $Supplier,'id','name','':}</td>
		<td>
			{:if $SKU.chg_title == 1:}
			<ul>
				<li>旧标题：<span style="float:right;">{:$SKU.old_title | string.key_highlight $_PARAMS.q:}</span></li>
				<li>新标题：<span style="float:right;">{:$SKU.new_title | string.key_highlight $_PARAMS.q:}</span></li>
			</ul>
			{:/if:}
			{:if $SKU.chg_catagory == 1:}
			<ul>
				<li>旧分类：<span style="float:right;">{:$SKU.old_catagory | array.find $Catagory,'id','name','':}</span></li>
				<li>新分类：<span style="float:right;">{:$SKU.new_catagory | array.find $Catagory,'id','name','':}</span></li>
			</ul>
			{:/if:}
			
			{:if $SKU.chg_price_base == 1:}
			<ul>
				<li>旧成本价：<span style="float:right;">{:$SKU.old_price_base | number.currency:}</span></li>
				<li>新成本价：<span style="float:right;">{:$SKU.new_price_base | number.currency:}</span></li>
			</ul>
			{:/if:}
			
			{:if $SKU.chg_price_ref == 1:}
			<ul>
				<li>旧参考价：<span style="float:right;">{:$SKU.old_price_ref | number.currency:}</span></li>
				<li>新参考价：<span style="float:right;">{:$SKU.new_price_ref | number.currency:}</span></li>
			</ul>
			{:/if:}
			
			{:if $SKU.chg_ratio == 1:}
			<ul>
				<li>旧毛利：<span style="float:right;">{:$SKU.old_ratio | number.percent 2,1:}</span></li>
				<li>新毛利：<span style="float:right;">{:$SKU.new_ratio | number.percent 2,1:}</span></li>
			</ul>
			{:/if:}
			
			{:if $SKU.chg_depot == 1:}
			<ul>
				<li>旧库存：<span style="float:right;">{:$SKU.old_depot:}</span></li>
				<li>新库存：<span style="float:right;">{:$SKU.new_depot:}</span></li>
			</ul>
			{:/if:}
			
			
		</td>
		
		<td>
			{:$SKU.message:}
		</td>
		<td>
			{:$SKU.create_time | number.datetime:}
		</td>

	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="6">
			{:include file="_g/pager.tpl" Result=$data:}
		</td>
	</tr>
</table>


<script>
	

	function subsearch() {
		$("#tag").val('');
		document.form1.submit();
	}
	function dosubsearch() {
		$("#tag").val('tag');
		document.form1.submit();
	}
	function allsearch() {
		$("#tag").val('all');
		document.form1.submit();
	}


	//同意修改
	function yes(id) {
		YueMi.API.Admin.invoke('mall', 'adopt', {
			id: id
		}, function (t, q, r) {
			alert(r.__message);
			location.reload();
		}, function (t, q, r) {
			//失败
		});
	}

	//拒绝修改
	function no(id)
	{
		YueMi.API.Admin.invoke('mall', 'refase', {
			id: id
		}, function (t, q, r) {
			alert(r.__message);
			location.reload();
		}, function (t, q, r) {
			//失败
		});
	}
</script>
{:include file="_g/footer.tpl":}