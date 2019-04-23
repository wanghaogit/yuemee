{:include file="_g/header.tpl" Title="库存/SPU素材":}
<style>
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
	li{float:left;margin-left:5px;}
</style>
<div>
	<ul>
		<li>{:if $spu_title != '':}SPU：“{:$spu_title:}”的{:/if:}{:if $type == 0:}商品{:else:}内容{:/if:}素材列表</li>
		<li {:if $_PARAMS.type == 0:}class="selected"{:/if:}><a href="{:$_URL | common.set_url_param 'type',0:}">商品图</a></li>
		<li {:if $_PARAMS.type != 0:}class="selected"{:/if:}><a href="{:$_URL | common.set_url_param 'type',1:}">内容图</a></li>
	</ul>
</div>
<div id="show-img" style="background-color:white;padding:10px;text-align: center;border:solid 1px black; box-shadow: -10px -10px 5px #888888;">
	<img id="imgbig" src="" style="width:100%;height:100%;margin:auto;"/>
</div>
<table cellspacing="0" cellpadding="0" class="Grid">
	<tr>
		<th>id</th>
		<th>素材</th>
		<th>文件格式</th>
		<th>文件大小</th>
		<th>图片宽度</th>
		<th>图片高度</th>
		<th>状态</th>
		<th>创建时间</th>	
		<th>更新时间</th>	
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data item=S:}
		<tr>
			<td align="center">{:$S.id:}</td>
			<td>
				{:if $S.file_url:}
					<img src="https://r.yuemee.com/upload{:$S.file_url:}" style="max-width:140px;max-height:50px;" class="img-sm"/>
				{:else:}
					<img src="{:$S.source_url:}" style="max-width:140px;max-height:50px;" class="img-sm"/>
				{:/if:}
			</td>
			<td align="center">{:if $S.file_fmt == 0:}JPG{:else:}PNG{:/if:}</td>
			<td align="right">{:$S.file_size:}</td>
			<td align="center">{:$S.image_width:}</td>
			<td align="center">{:$S.image_height:}</td>
			<td align="center">{:$S.status | array.enum ['待下载','已下载','下载失败']:}</td>
			<td align="center">{:$S.create_time | number.datetime:}</td>
			<td align="center">{:$S.update_time | number.datetime:}</td>
			<td></td>
		</tr>
	{:/foreach:}
	<tr class="pager" style="word-break: break-all;word-wrap: break-word;">
		<td colspan="10">
			{:include file="_g/pager.tpl" Result=$data:}
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