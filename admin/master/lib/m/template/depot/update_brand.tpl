{:include file="_g/header.tpl" Title="库存/品牌":}
<form action="/index.php?call=depot.update_brand" method="post">
	<input type="hidden" name="id" value="{:$res.id:}"/>
	姓名：<input type="text" name="name" value="{:$res.name:}"/>
	英文名：<input type="text" name="alias" value="{:$res.alias:}"/>
	<input type="submit" value="改"/>
</form>
{:include file="_g/footer.tpl":}
