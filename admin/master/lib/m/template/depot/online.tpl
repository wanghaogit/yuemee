{:include file="_g/header.tpl" Title="库存/上架工具":}
<form name="form1" action="" method="post">
	<ul class="Form">
		<li>
			售卖标题：
			<input type="text" class="input-text" name="title" id="title" maxlength="128" size="64" value="{:if $SKU:}{:$SKU->title:}{:/if:}" />
			<input type="hidden" name="spu_id" value=""/>
		</li>
		<li>
			栏目划分：
			<select onchange="get_catagory(this.value, this)" name="catagory_id" id="catagory_id">
				{:foreach from=$res item=c:}
				<option value="{:$c.id:}">{:$c.name:}</option>
				{:/foreach:}
			</select>
		</li>
		<li>
			释放库存：
			{:if $SKU:}
			<input type="number" class="input-number" id="qty_total" name="qty_total" value="{:$SKU->quantity:}" min="1" max="{:$SKU->quantity:}" />
			{:else:}
			TODO
			{:/if:}
		</li>
		<li>
			平台价格：
			{:if $SKU:}
			<input type="number" class="input-number" id="price_sale" name="price_sale" value="{:$SKU->price_sale:}" min="{:$SKU->price_base:}" max="{:$SKU->price_market:}" style="width:100px;" />

			<!--	<div id="price_bar" style="display:inline-block;width:300px;height:80px;border:solid 1px red;">
				<div style="display:inline;width:40px;height:50%;float:left;border-bottom:dotted 3px black;">
					
				</div>
			</div>-->
			{:else:}
			TODO
			{:/if:}
		</li>
		<li>
			对标价：
			{:if $SKU:}
			<input type="number" class="input-number" id="price_ref" name="price_ref" value="{:$SKU->price_ref:}" min="0" max="{:$SKU->price_ref:}" />
			{:else:}
			TODO
			{:/if:}
		</li>
		<li>
			用户返利：
			{:if $SKU:}
			<input type="number" class="input-number" id="rebate_user" name="rebate_user" value="{:$SKU->price_rebate:}" min="0" max="{:$SKU->price_rebate:}" />
			{:else:}
			TODO
			{:/if:}
		</li>
		<li>
			<input type="button" value="保存" onclick="" />
		</li>
	</ul>
	{:if $SKU:}
	<!--<input type="hidden" name="catagory_id" value="{:$SKU->catagory_id:}" />-->
	<input type="hidden" name="sku_id" value="{:$SKU->id:}" />
	{:/if:}
</form>
{:include file="_g/footer.tpl":}
<script>
	function check1()
	{
		if ($('#name').val() == '')
		{
			alert('请输入品类名称');
			return;
		}
		document.form1.submit();
	}
</script>
<script>
	$(function () {
		var obj = document.getElementsByTagName('select')[0];
		var id = obj.value;
		get_catagory(id, obj);
	})
	function get_catagory(id, obj) {
		console.log(obj.nextSibling);
		if (obj.nextSibling !== null) {
			obj.parentNode.removeChild(obj.nextSibling);
		}
		YueMi.API.Admin.invoke('depot', 'get_catagory', {
			id: id
		}, function (t, q, r) {
			if (r.Re !== '') {
				var newNode = document.createElement('select');
				newNode.setAttribute('onchange', 'get_catagory(this.value,this)');
				newNode.setAttribute('name', 'catagory_id');
				obj.removeAttribute('name');
				var str = '';
				$.each(r.Re, function (key, val) {
					str += '<option value="' + val.id + '">' + val.name + '</option>';
				});
				newNode.innerHTML = str;
				obj.parentNode.insertBefore(newNode, null);
			}
		}, function (t, q, r) {
			//失败
		});
	}
</script>
