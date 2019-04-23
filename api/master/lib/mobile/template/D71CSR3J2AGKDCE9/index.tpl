<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang=zh-cn xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>查询自己用户信息</title>
	<meta http-equiv=Content-Type content="text/html; charset=UTF-8" />
</head>
<style type="text/css">
	body	{ line-height:100%; font-size:13px; font-family:Tahoma, Arial; background-color:#FFF; text-align:left; line-height:200%; }
	strong	{ font-size:13px; color:red; line-height:200%; }
	b		{ font-size:24px; color:#FF0000; line-height:200%; }
	a		{ COLOR:#4f6371; TEXT-DECORATION:none; }
	a:hover	{ COLOR:#63b4cd; TEXT-DECORATION:none; }

	.code			{ padding:10px; BORDER-RIGHT:#00a0c6 1px dashed; BORDER-TOP:#00a0c6 1px dashed; MARGIN:5px 5px 0px; BORDER-LEFT:#00a0c6 1px dashed; BORDER-BOTTOM:#00a0c6 1px dashed; BACKGROUND-COLOR:#ffffff; }
	.quote			{ border-left:0px dashed #D6C094; margin:0px; border:1px dashed #00a0c6; }
	.quote-title	{ background-color:#edf4f6; border-bottom:1px dashed #00a0c6 !important; border-bottom:1px dotted #00a0c6; padding:5px; font-weight:bold; color:#4c9bb0; cursor:pointer; }
	.quote-content	{ word-wrap:break-all; color:#000000; padding:10px; background-color:#ffffff; border:1px dashed #edf4f6; border-top:0px; overflow:hidden }
</style>
<body>
<div style="padding:10px; WORD-WRAP:break-word;">

	<div class="quote">
		<div class="quote-title">
			<strong>阅米 user_wechat 表信息：</strong>
		</div>
		<div class="quote-content">
			{:foreach from=$Wechat key=key value=val:}
				{:$key:}：{:$val:}<br />
			{:/foreach:}
		</div>
	</div>
	<div style="clera:both;height:10px"></div>

	<div class="quote">
		<div class="quote-title">
			<strong>阅米 user 表信息：</strong>
		</div>
		<div class="quote-content">
			{:if isset($User->id):}
				{:foreach from=$User key=key value=val:}
					{:$key:}：{:$val:}<br />
				{:/foreach:}
			{:/if:}
		</div>
	</div>
	<div style="clera:both;height:10px"></div>

</div>
</body>
</html>
