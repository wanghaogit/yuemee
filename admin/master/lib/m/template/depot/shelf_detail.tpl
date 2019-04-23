{:include file="_g/header.tpl" Title="货架":}
<script type="text/javascript" src="/scripts/editor.js"></script>
    {:foreach from=$data value=SKU:}
		<label style="float: left;">货架详情：</label><br>
		<div id="sku_info" name="sku_info"></div>
		<script type="text/javascript">
			var e = new window.wangEditor('#sku_info');
			e.create();
			e.txt.html('{:$SKU.shelf_id:}'+'<br>'+'{:$SKU.url_video:}'+'<br>'+'{:$SKU.intro:}'+'<br>'+'{:$SKU.name_1:}'+'<br>'+'{:$SKU.create_time:}'+'{:$SKU.create_from:}'+'<br>'+'{:$SKU.name_2:}'+'<br>'+'{:$SKU.update_time:}'+'<br>'+'{:$SKU.update_from:}'+'<br>'+'{:$SKU.name_3:}'+'<br>'+'{:$SKU.audit_time:}'+'<br>'+'{:$SKU.audit_from:}');
			
		</script>
	{:/foreach:}
{:include file="_g/footer.tpl":}