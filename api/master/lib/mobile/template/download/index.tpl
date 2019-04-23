{:include file="_g/header.tpl" Title="阅米APP下载":}
<style type="text/css">
	::-webkit-scrollbar { display:none } /* 移动端隐藏滚轮 */
	html { width:100%; height:100%; overflow-y:hidden; }
	body { width:100%; height:100%; background:url('{:#URL_RES:}/v1/images/mobile/bg.png')no-repeat; background-size:100% 100%; /* 必须放在background的后面才能生效 */ }
	.PageBody { height:100%; width:100%; font-family:'苹方'; }
	.Button        { text-align:center; margin-left:15%; margin-right:15%; height:44px; line-height:44px; border-radius:4px; font-weight:bold; font-size:15px; border:0.5px solid #333333; }
	.ButtonIos     { border:0.5px solid #333333; }
	.ButtonAndroid { border:0.5px solid #5AC68E; background-color:#5AC68E; color:white; }
</style>
<div class='PageBody'>
	<div style="height:56%; clear:both">&nbsp;</div>
	{:if $OsName == 'iosxx':}
	{:else:}
		<div style="text-align:center">
			<div class="Button ButtonIos" id="DownloadIos">
				<img src="{:#URL_RES:}/v1/images/mobile/wei.png" style="width:1rem" id="IosLogo" />
				&nbsp;
				iphone正式版
			</div>
		</div>
		<div style="height:10px;clear:both;"></div>
		<div style="text-align:center">
			<div class="Button ButtonAndroid" id="DownloadAndroid">
				<img src="{:#URL_RES:}/v1/images/mobile/xuanzhong.png" style="width:1rem" id="AndroidLogo" />
				&nbsp;
				Andriod正式版
			</div>
		</div>
	{:/if:}
</div>
<script type="text/javascript">
	$("#DownloadIos").click(function(){
		 var DownloadAndroid= document.getElementById('DownloadAndroid');
		 var IosLogo = document.getElementById('IosLogo');
		 var AndroidLogo=document.getElementById('AndroidLogo');
		 this.style.background = "#f3483e";
		 this.style.color = "white";
		 IosLogo.style.borderColor = "#f3483e";
		 IosLogo.src = "{:#URL_RES:}/v1/images/mobile/xuan.png";
		 DownloadAndroid.style.background = "white";
		 $("#az").css("border", "1px solid black");
		 DownloadAndroid.style.color = "black";
		 AndroidLogo.src = "{:#URL_RES:}/v1/images/mobile/weixuanzhong.png";
	     window.location.href = '/mobile.php?call=download.ios';
	});
	$("#DownloadAndroid").click(function(){
		var DownloadIos= document.getElementById('DownloadIos');
		var IosLogo = document.getElementById('IosLogo');
        var AndroidLogo= document.getElementById('AndroidLogo');
		IosLogo.style.background = "white";
		IosLogo.style.color = "black";
		DownloadIos.src="{:#URL_RES:}/v1/images/mobile/wei.png";
		this.style.background = "#5AC68E";
		this.style.color = "white";
		AndroidLogo.src = "{:#URL_RES:}/v1/images/mobile/xuanzhong.png";
	    window.location.href = '/mobile.php?call=download.android';
	});
</script>
{:include file="_g/footer.tpl":}
