{:include file="_g/header.tpl" Title="首页":}
<script type="text/javascript" src="/scripts/editor.js"></script>
<div id="editor1"></div>
<script type="text/javascript">
	var e = new window.wangEditor('#editor1');
	e.create({
		material : {
			ext_spu_id : 1,
			ext_sku_id : 1,
			spu_id	: 1,
			sku_id	: 1,
			page_id	: 1,
			network : true
		}
	});
	e.txt.html('测试');
</script>
{:include file="_g/footer.tpl":}
