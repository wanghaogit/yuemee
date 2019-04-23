{:include file="_g/header.tpl" Title="运营/APP":}
<script type="text/javascript" src="/scripts/editor.js"></script>
<a href="/index.php?call=runer.dpage&parent_id={:$Result.parent_id:}">
	返回上级
</a>
<form name="form1" action="/index.php?call=runer.dpage_update&id={:$id:}" method="post">
	<ul class="Form">
		<li>
			<label>配置名称：</label>
			<input type="text" id="name" name="name"  style="width:200px;" value="{:$Result.name:}"/>
		</li>
		<li>
			<label>页面代号：</label>
			<input type="text" id="alias" name="alias"  style="width:200px;" value="{:$Result.alias:}"/>
		</li>
		<li style="height:80px;line-height: 80px;">
			模块素材：
			{:foreach from=$img value=v:}
				<img src="https://r.yuemee.com/upload{:$v.file_url:}" style="width:50px;height:50px;margin:10px;" onclick="javascript:__insert_pic('{:#URL_RES:}/upload{:$v.file_url:}');"/>
			{:/foreach:}
			<div id="compTest"></div>
		</li>
		<li>
			<label>模块代码：</label>
		</li>
		<li>
			<div id="template"></div>
			<input type="hidden" id="tem" name="template"/>
		</li>
		<li>
			<input type="button" value="保存" onclick="javascript:check1();"  style="width: 100px;margin-top: 20px;"/>
		</li>
	</ul>
</form>
<script>
	function __insert_pic(url) {
		e.cmd.do('insertHTML', '<img src="' + url + '" />');
	}
	
	function check1() {
		if ($('#name').val() == '') {
			alert('请输入品类名称');
			return;
		}
		var html = e.txt.html();
		var infoobj = document.getElementById('tem');
		infoobj.value = html;
		document.form1.submit();
	}

	YueMi.Upload.Admin.create('compTest', {
		__access_token :'{:$User->token:}',
		__width: 64,
		__height: 64,
		__css: 'border-radius:5px;border: solid 1px black;',
		schema: 'page',
		page_id: {:$Result.id:}
	}, function (t, r, q) {
		location.reload();
	}, function (t, r, q) {
		alert(q.__message);
	});
	var e = new window.wangEditor('#template');
	e.create({
		material: {
			page_id: {:$Result.id:},
			network: true,
			invoker: YueMi.API.Open
		}
	});
	e.txt.html('{:$Result.template:}');

</script>
{:include file="_g/footer.tpl":}
