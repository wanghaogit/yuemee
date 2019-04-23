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
<div id="show-img">
	<img id="imgbig" src="" style="width:100%;height:100%;"/>
</div>
<table cellspacing="0" cellpadding="0" class="Grid" style="width:1000px;">
	<tr>
		<th>
			查询
		</th>
		<th colspan="8">
			<form action="/index.php?call=depot.spu_picture" method="post">
				<select name="status" style="float:left;">
					<option value="-1">请选择</option>
					<option value="1">已审核</option>
					<option value="0">未审核</option>
				</select>
				<input type="submit" value="搜索" style="float:left;" />
			</form>
		</th>
	</tr>
	<tr>
		<th>id</th>
		<th>素材</th>
		<th>文件名</th>
		<th>文件大小（字节）</th>
		<th>图片宽度</th>
		<th>图片高度</th>
		<th>状态</th>
		<th>创建时间</th>	
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data item=S:}
	<tr>
		<td>{:$S.id:}</td>
		<td><img src="{:#URL_RES:}/upload{:$S.file_url:}" style="width:50px;height:50px" class="img-sm"/></td>
		<td>{:$S.file_name:}</td>
		<td>{:$S.file_size:}</td>
		<td>{:$S.image_width:}</td>
		<td>{:$S.image_height:}</td>
		<td>{:if $S.status == 0:}待审{:elseif $S.status == 1:}已审{:else:}删除{:/if:}</td>
		<td>{:$S.create_time:}</td>
		<td>
			{:if $S.status == 0:}
			<a href="/index.php?call=depot.pass_spupic&id={:$S.id:}&go=1" style="color:green;">通过</a>
			{:elseif $S.status == 1:}
			<a href="/index.php?call=depot.pass_spupic&id={:$S.id:}&go=0" style="color:red;">驳回</a>
			{:else:}
			位置
			{:/if:}
		</td>
	</tr>
	{:/foreach:}
	<tr class="pager">
		<td colspan="9">
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
<script>
	YueMi.Upload.Admin.create('compTest', {
		schema: '{:if $_PARAMS.type == 0:}spu{:else:}spu-p{:/if:}',
				spu_id: {:$spu_id:}
			}, function (t, r, q) {
				location.reload();
			}, function (t, r, q) {
				alert(q.__message);
			});
</script>{:include file="_g/footer.tpl":}