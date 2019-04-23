{:include file="_g/header.tpl" Title="SKU审核":}
<link rel="stylesheet" type="text/css" href="/styles/material_manager.css" />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">

	<tr>
		<td>
			查询 
		</td>
		<td colspan="5">
			<form action="/index.php?call=mall.verify" method="GET" name="form1">
				<input type="hidden" name="call" value="{:$_RUNTIME->ticket->handler:}.{:$_RUNTIME->ticket->action:}" />
				供应商:<select id="supplier_serch" name="sid">
					<option value="0">请选择</option>
					{:foreach from=$Supplier value=v:}
					<option value="{:$v.id:}" {:if $_PARAMS.sid == $v.id:}selected="selected"{:/if:}>{:$v.name:}</option>
					{:/foreach:}
				</select>
				关键字：<input type="text" value="{:$_PARAMS.q:}" name="q">
			</form>
		</td>
		<td>
			<input type="button" onclick="subsearch()" value="搜索" style="width:100%;height:100%;"/>
		</td>
	</tr>
	<tr>
		<th>ID</th>
		<th>商品名称</th>
		<th>供应商</th>
		<th>变更项目</th>
		<th>状态</th>
		<th>时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=SKU:}
	<tr>
		<td>
			{:$SKU.id:}
		</td>
		<td><a href="/index.php?call=sku.sku_detail&id={:$SKU.sku_id:}">{:$SKU.sku_id | array.find $Sku,'id','title','':}</a></td>
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

				<input type="button" id="{:$SKU.id:}" value="查看佣金" class="select"/>
			</ul>
			{:/if:}

			{:if $SKU.chg_price_sale == 1:}
			<ul>
				<li>旧阅米价：<span style="float:right;">{:$SKU.old_price_sale | number.currency:}</span></li>
				<li>新阅米价：<span style="float:right;">{:$SKU.new_price_sale | number.currency:}</span></li>
				<input type="button" id="{:$SKU.id:}" value="查看佣金" class="select"/>
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
			{:$SKU.status | boolean.iconic:}
		</td>
		<td>
			{:$SKU.create_time | number.datetime:}
		</td>
		<td>
			{:if $SKU.status == 0:}
			<a style="color:green;"  onclick="yes({:$SKU.id:})">通过</a>
			<a style="color:red;" onclick="no({:$SKU.id:})">驳回</a>

			{:elseif $SKU.status == 2:}
			已拒绝供应商修改
			{:else:}

			{:/if:}
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="7">
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
	
	//查看佣金
	$('.select').click(function () {
		YueMi.API.Admin.invoke('sku', 'jisuan', {
			id: $(this).attr('id')
		}, function (t, q, r) {
			var html = ''
			html += "旧佣金："+r.old_rebate+"\r\n"+"新佣金："+r.yj;
			alert(html);
			//location.reload();
		}, function (t, q, r) {
			//失败
		});

	});

</script>
{:include file="_g/footer.tpl":}