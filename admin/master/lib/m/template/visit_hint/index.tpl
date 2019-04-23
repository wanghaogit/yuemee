<!DOCTYPE html>
<html>
<head>
	<title>阅米总后台浏览器要求</title>
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport' />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="/favicon.ico" rel="icon" type="image/x-icon" />
	<style type="text/css">
		::-webkit-scrollbar { display:none } /* 移动端隐藏滚轮 */
		html { width:100%; height:100%; overflow-y:hidden; }
		body { width:100%; height:100%; }
		.PageBody { z-index:1; height:100%; width:100%; font-family:'苹方'; text-align:center; }
	</style>
</head>
<body>
	<div class='PageBody'>
		<div style="clera:both; height:100px">&nbsp;</div>
		
		<B style="font-size:24px; color:red">
			{:if $_PARAMS.type == 1:}
				Google浏览器版本过低!
			{:else:}
				请使用Google浏览器访问!
			{:/if:}
		</B>

		<br /><br />
		<B style="font-size:16px; color:green">如无法确定自己浏览器的版本，请从下面的链接中下载最新版本Google浏览器</B>
		
		<br /><br />
		<a href="http://r.yuemee.com/tools/66.0.3359.139_chrome_installer_x64.exe" target="_blank">Google浏览器64位版本下载（建议使用）</a>
		
		<br /><br />
		<a href="http://r.yuemee.com/tools/66.0.3359.117_chrome_installer_32.exe" target="_blank">Google浏览器32位版本（如果64位版本无法安装，请下载该版本）</a>
	</div>
</body>
</html>
