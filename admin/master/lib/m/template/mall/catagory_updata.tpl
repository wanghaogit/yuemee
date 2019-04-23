{:include file="_g/header.tpl" Title="品类编辑":}
<br />
<h1>品类编辑</h1>
<br />
<form name="form1" action="/index.php?call=mall.catagory_updata&cid={:$result.id:}"" method="post">
<ul class="uu">
   <li>
	品类名称：<input type="text" name="name" value="{:$result.name:}"  style="width: 200px;">
   </li>	
   <li>
	   排序：<input type="text" name="p_order" value="{:$result.p_order:}"  style="width: 200px;">
   </li>
   <li>
	   <input type="button" value="保存"  onclick="javascript:check1();" style="margin-top: 20px;width: 100px;">
   </li>
</ul>
</form>
<script>
function check1()
{
	document.form1.submit();
}
</script>
{:include file="_g/footer.tpl":}

