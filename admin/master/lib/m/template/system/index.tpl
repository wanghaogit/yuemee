{:include file="_g/header.tpl" Title="系统":}
<ul class="TaskPanel">
	<li>字典缓存管理</li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_region');">重载地区数据</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_bank');">重载银行列表</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_mobile_vender');">重载手机品牌</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_mobile_model');">重载手机型号</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_catagory');">重载商品分类</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_brand');">重载品牌列表</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_supplier');">重载供应商列表</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_neigou_catagory');">重载内购品类表</a></li>
</ul>
<ul class="TaskPanel">
	<li>权限缓存管理</li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_admin');">重载管理员列表</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_role');">重载角色列表</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_target');">重载目标列表</a></li>
	<li><a href="javascript:void(0);" onclick="javascript:_do_redis_reset(0, 'dict_rule');">重载规则列表</a></li>
</ul>
<ul class="TaskPanel">
	<li>用户缓存管理</li>

</ul>
<ul class="TaskPanel">
	<li>售卖缓存管理</li>

</ul>
<ul class="TaskPanel">
	<li>分销缓存管理</li>

</ul>
<ul class="TaskPanel">
	<li>内容缓存管理</li>
</ul>
<ul class="TaskPanel">
	<li>订单缓存管理</li>
</ul>

<br style="clear:both" />

<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>yuemi_main</caption>
	{:foreach from=$DataCountMain item=Data:}
		<tr>
			<td>{:$Data.TABLE_NAME:}</td>
			<td>{:$Data.TABLE_COMMENT:}</td>
			<td>{:$Data.TABLE_ROWS:}</td>
		</tr>
	{:/foreach:}
</table>

<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>yuemi_sale</caption>
	{:foreach from=$DataCountSale item=Data:}
		<tr>
			<td>{:$Data.TABLE_NAME:}</td>
			<td>{:$Data.TABLE_COMMENT:}</td>
			<td>{:$Data.TABLE_ROWS:}</td>
		</tr>
	{:/foreach:}
</table>

<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>yuemi_log</caption>
	{:foreach from=$DataCountLog item=Data:}
		<tr>
			<td>{:$Data.TABLE_NAME:}</td>
			<td>{:$Data.TABLE_COMMENT:}</td>
			<td>{:$Data.TABLE_ROWS:}</td>
		</tr>
	{:/foreach:}
</table>

<script>
	function _do_redis_reset(db, key) {
		YueMi.API.Admin.invoke('system', 'redis_reset', {
			db: db,
			key: key
		}, function (t, q, r) {
			$.alert({
				icon: 'fas fa-trash',
				title: '刷新缓存',
				content: '缓存已刷新。',
			});
		}, function (t, q, r) {
			$.alert({
				type: 'red',
				icon: 'fas fa-trash',
				title: '刷新缓存',
				content: 'Redis中没找到指定的Key或者刷新失败。',
			});
		})
	}
</script>
{:include file="_g/footer.tpl":}
