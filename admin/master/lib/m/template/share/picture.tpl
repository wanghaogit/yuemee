{:include file="_g/header.tpl" Title="分享":}
<table class="Grid" cellspacing="0" cellpadding="0">
	<caption>素材管理</caption>

	<tr>
		<th>ID</th>
		<th>尺寸</th>
		<th>缩略图</th>
		<th>关联商品</th>
	</tr>
	{:foreach from=$Pic->Data value=v:}
		<tr>
			<td>{:$v.id:}</td>
			<td>{:$v.image_width:}*{:$v.image_height:}</td>
			<td><img src="{:#URL_RES:}/upload{:$v.thumb_url:}" /></td>
			<td>{:$v.name:}</td>
		</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="5">
			{:include file="_g/pager.tpl" Result=$Pic:}
		</td>
	</tr>
</table>
{:include file="_g/footer.tpl":}
