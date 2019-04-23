{:include file="_g/header.tpl" Title="库存/新增品类":}
<br />
<h1>外部SPU-编辑</h1>
<br />
<form name="form1" action="/index.php?call=depot.edit_ext_spu" method="post">
	<ul class="Form">
		<li>
			<label>SUP名称：</label>
			<input type="text" value="{:$res.title:}" style="width:70%;" readonly="readonly"/>
		</li>
		<li>
			<label>外部供应商：</label>
			<span>{:$res.supplier:}</span>
		</li>
		<li>
			<label>关联分类名称：</label>
			<span>{:$res.c_name:}</span>
		</li>
		<li>
			<label>内部SPU名称：</label>
			<span>{:$res.spu_name:}</span>
		</li>
		<li>
			<label>内部分类名称：</label>
			<span>{:$res.category_name:}</span>
		</li>
		<li>
			<label>品牌名称：</label>
			<span>{:$res.brand_name:}</span>
		</li>
		<li>
			<label>内部分类名称：</label>
			<span>{:$res.c_name:}</span>
		</li>
		<li>
			<label>成本价：</label>
			<span>{:$res.price_base	:}</span>
		</li>
		<li>
			<label>商品bn：</label>
			<span>{:$res.bn:}</span>
		</li>
		<li>
			<label>图片素材：</label>
			<span>
				<img src="{:$res.img_url:}" />
				{:$res.video:}
			</span>
		</li>
		<li>
			<label>视频URL：</label>
			<span>{:$res.video:}</span>
		</li>
		<li>
			<label vertical-align="center">描述内容：</label><br/>
			<div id="intro" name="intro" rows="10" cols="50">{:$res.intro:}</div>
		</li>
		<li>
			<input type="submit" value="保存" /><input type="hidden" name="id" value="{:$res.id:}" />
		</li>
	</ul>
</form>
<br />
<span id="a1"></span>
{:include file="_g/footer.tpl":}
<script>
</script>

