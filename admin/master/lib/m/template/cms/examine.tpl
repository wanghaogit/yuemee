{:include file="_g/header.tpl" Title="资讯栏目管理":}
<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
<script type="text/javascript" src="/scripts/editor.js"></script>
<form name="form1" action="/index.php?call=cms.examine" method="post">

	<ul class="Form">
		<input type="hidden" value="{:$res.id:}" name="id" id="id"/>
		<li>
			<label>栏目标题：</label>{:$res.title:}
		</li>
		<li>
			<label>咨询分类：</label>{:$cate:}
		</li>
		<li>
			栏目内容：<div id="e_content" name="e_content" style="width:670px;"></div>
			<script type="text/javascript">
				var e = new window.wangEditor('#e_content');
				e.create();
				e.txt.html('{:$res.content:}');
			</script>
			<input type="hidden" id="content" name="content"/>
		</li>
		<li>
			<input type="radio" name="examine" value="1" {:if $res.status == 1 :}checked="checked"{:/if:}/>拒绝&nbsp;&nbsp;
			<input type="radio" name="examine" value="3" {:if $res.status == 3 :}checked="checked"{:/if:}/>批准&nbsp;&nbsp;
			<input type="radio" name="examine" value="4" {:if $res.status == 4 :}checked="checked"{:/if:}/>排队&nbsp;&nbsp;
			<input type="radio" name="examine" value="5" {:if $res.status == 5 :}checked="checked"{:/if:}/>通过&nbsp;&nbsp;
			<input type="radio" name="examine" value="6" {:if $res.status == 6 :}checked="checked"{:/if:}/>下架&nbsp;&nbsp;
		</li>
        <li>
			<input type="button" value="提交" onclick="javascript:check1();" />
		</li>


	</ul>

</form>
<script type="text/javascript">

	function check1()
	{
		var html = e.txt.html();
		var infoobj = document.getElementById('content');
		YueMi.API.Admin.invoke('cms', 'cms_examine', {
			__access_token: '{:$User->token:}',
			id	: $('#id').val().trim(),
			examine : $('[name=examine]:checked').val().trim()
		}, function (t, r, q) {
			location.href = '/index.php?call=cms.index';
		}, function (t, r, q) {
			alert(q.__message);
		});
	}
</script>

{:include file="_g/footer.tpl":}