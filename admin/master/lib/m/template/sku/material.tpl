{:include file="_g/header.tpl" Title="商城/素材":}
<style>
	li{
		float:left;margin-left:5px;
	}
	.img-sm{
		cursor:pointer;
	}
	#show-img{
		width:400px;
		height:400px;
		position:absolute;
		left:300px;
		top:100px;
		z-index:100;
		display:none;
	}
</style>
<div>
	<ul class="SimpleList">
		<li {:if $_PARAMS.t == 2:}class="selected"{:/if:}><a href="{:$_URL | common.set_url_param 't',2:}">SKU主图</a></li>
		<li {:if $_PARAMS.t == 3:}class="selected"{:/if:}><a href="{:$_URL | common.set_url_param 't',3:}">SKU素材</a></li>
	</ul>
</div>
<div id="show-img">
	<img id="imgbig" src="" style="width:100%;height:100%;"/>
</div>
<table cellspacing="0" cellpadding="0" class="Grid" style="width:1000px;">
	<tr>
		<th>id</th>
		<th>素材</th>
		<th>文件大小（字节）</th>
		<th>图片宽度</th>
		<th>图片高度</th>
		<th>状态</th>
		<th>创建时间</th>	
		<th>操作</th>
	</tr>
	{:foreach from=$Data->Data item=S:}
	<tr>
		<td>{:$S.id:}</td>
		<td><img src="{:#URL_RES:}/upload{:$S.file_url:}" style="width:50px;height:50px" class="img-sm"/></td>
		<td>{:$S.file_size:}</td>
		<td>{:$S.image_width:}</td>
		<td>{:$S.image_height:}</td>
		<td>{:if $S.status == 0:}待审{:elseif $S.status == 1:}已审{:else:}<b style="color:red">删除</b>{:/if:}</td>
		<td>{:$S.create_time | number.datetime:}</td>
		<td>
			{:if $S.status == 0:}
			<a href="/index.php?call=sku.throw_picture&id={:$S.id:}&t={:$_PARAMS.t:}">通过</a>
			<a href="/index.php?call=sku.hit_picture&id={:$S.id:}&t={:$_PARAMS.t:}" style="color:red;">驳回</a>
			{:elseif $S.status == 1:}
			
			{:else:}

			{:/if:}
		</td>
	</tr>
	{:/foreach:}
	<tr class="pager">
		<td colspan="9">
			{:include file="_g/pager.tpl" Result=$Data:}
		</td>
	</tr>
</table>
<script>
	$('.img-sm').click(function () {
		var url = $(this).attr('src');
		$('#imgbig').attr('src', url);
		var X = $(this).offset().left;
		var Y = $(this).offset().top;
		$('#show-img').css('left', X - 80 + 'px').css('top', Y + 'px').toggle();
	});
	$('#show-img').click(function () {
		$(this).hide();
	});
</script>
{:include file="_g/footer.tpl":}