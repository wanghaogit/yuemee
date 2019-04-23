{:include file="_g/header.tpl" Title="资讯栏目管理":}
<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
<script type="text/javascript" src="/scripts/editor.js"></script>
<form name="form1" action="/index.php?call=cms.update_cms" method="post">

	<ul class="Form">
		<input type="hidden" value="{:$res.id:}" name="id" id="id"/>
		<li>
			<label>标题：</label>
			<input type="text" id="title" name="title" class="input-text" size="40" maxlength="64" value="{:$res.title:}" />
		</li>
		<li>
			<label>分类：</label>
			<select id="scope_id" name="scope_id"/>
		<option value="0">-- 请选择 --</option>
		{:foreach from=$cate value=v:}
			<option value="{:$v.id:}" {: if $res.column_id == $v.id :}selected = "selected"{:else:}{:/if:}>{:$v.name:}</option>
		{:/foreach:}
		</select>
		</li>
		<li>
			素材：
			<div id="compTest"></div>
			{:foreach from=$Materials.Column item=P:}
				<img src="{:#URL_RES:}/upload{:$P.thumb_url:}" data-id="{:$P.id:}" style="border:solid 1px #ddd;" onclick="javascript:__insert_pic('{:#URL_RES:}/upload{:$P.file_url:}');" />
			{:/foreach:}
			{:foreach from=$Materials.Article item=P:}
				<img src="{:#URL_RES:}/upload{:$P.thumb_url:}" data-id="{:$P.id:}" style="border:solid 1px #ddd;" onclick="javascript:__insert_pic('{:#URL_RES:}/upload{:$P.file_url:}');" />
			{:/foreach:}
		</li>
		<li>
			<div id="e_content" name="e_content" style="width:670px;"></div>
			<script type="text/javascript">
				var e = new window.wangEditor('#e_content');
				e.create();
				e.txt.html('{:$res.content | string.js_format:}');
			</script>
			<input type="hidden" id="content" name="content"/>
		</li>
        <li>
			<input type="button" value="发布" onclick="javascript:check1();" />
		</li>
	</ul>
</form>
<script type="text/javascript">

	function __insert_pic(url) {
		
		e.cmd.do('insertHTML', '<img src="' + url + '" />');
	}
	
	function check1()
	{
		if ($('#scope_id').val() == 0) {
			alert("请选择分类");
			return;
		}

		if ($('#title').val() == '')
		{
			alert('请输入公告标题');
			return;
		}
		var html = e.txt.html();
		YueMi.API.Admin.invoke('cms', 'cms_update', {
			__access_token: '{:$User->token:}',
			content : html,
			scope_id : $('#scope_id').val().trim(),
			title	: $('#title').val().trim(),
			id	: $('#id').val().trim()
		}, function (t, r, q) {
			location.href = '/index.php?call=cms.index';
		}, function (t, r, q) {
			alert(q.__message);
		});
	}
	YueMi.Upload.Admin.create('compTest', {
		__access_token: '{:$User->token:}',
		__width: 64,
		__height: 64,
		__css: 'border-radius:5px;border: solid 1px black;',
		schema: 'cms',
		article_id: {:$res.id:}
	}, function (t, r, q) {
		//location.reload();
	}, function (t, r, q) {
		alert(q.__message);
	});
</script>

{:include file="_g/footer.tpl":}