{:include file="_g/header.tpl" Title="资讯栏目管理":}
<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
<script type="text/javascript" src="/scripts/editor.js"></script>
<form name="form1">
	<ul class="Form">
		<li>
			<label>标题：</label>
			<input type="text" id="title" name="title" class="input-text" size="40" maxlength="64" />
		</li>
		<li>
			<label>分类：</label>
			<select id="scope_id" name="scope_id"/>
		<option value="0">-- 请选择 --</option>
		{:foreach from=$res value=v:}
			<option value="{:$v.id:}">{:$v.name:}</option>
		{:/foreach:}
		</select>
		</li>
		<li>
			<div id="e_content" name="e_content" style="width:670px;"></div>
			<script type="text/javascript">
				var e = new window.wangEditor('#e_content');
				e.create();
			</script>
			<input type="hidden" id="content" name="content"/>
		</li>
        <li>
			<input type="button" value="发布" onclick="javascript:check1();" />
		</li>


	</ul>

</form>
<script type="text/javascript">

	function check1()
	{
		if ($('#title').val() == '')
		{
			alert('请输入公告标题');
			return;
		}
		var html = e.txt.html();
		var infoobj = document.getElementById('content');
		infoobj.value = html;
		YueMi.API.Admin.invoke('cms', 'cms_add', {
			__access_token: '{:$User->token:}',
			content : html,
			scope_id : $('#scope_id').val().trim(),
			title	: $('#title').val().trim()
		}, function (t, r, q) {
			location.href = '/index.php?call=cms.index';
		}, function (t, r, q) {
			alert(q.__message);
		});
	}
</script>

{:include file="_g/footer.tpl":}