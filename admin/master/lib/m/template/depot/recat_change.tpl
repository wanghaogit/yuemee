{:include file="_g/header.tpl" Title="库存/供应商":}
<form action="/index.php?call=depot.recat_change" method="post">
	<br><br><br>
	<input type="submit" value="go">
	<input type="hidden" name="id" value="{:$res.id:}">	<input type="hidden" name="tabid" value="{:$res.tabid:}">

	<select onchange="get_catagory(this.value, this)" name="catagory_id" id="catagory_id">
		{:foreach from=$res item=c:}
		<option value="{:$c.id:}">{:$c.name:}</option>
		{:/foreach:}
	</select>
	
</form>
<script type="text/javascript">
	var API_ADMIN = new Invoker({
		udid: '000000000000000000000000',
		url: 'http://z.ym.cn/api.php',
		applet_token: 'b31ed652c66e11b41b6f7378',
		access_token: function () {
			var m = /\buser\_token\=([a-z0-9]+)\b/i.exec(document.cookie);
			if (m && m.length > 0) {
				return m[1];
			}
			return '';
		}
	});

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
{:include file="_g/footer.tpl":}
