{:include file="_g/header.tpl" Title="系统/银行":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		系统银行
	</caption>
	<tr>
		<th>银行ID</th>
		<th>类型ID</th>
		<th>银行名称</th>
		<th>银行图标</th>	
		<th>操作</th>
	</tr>
	{:foreach from=$Result->Data item=R:}
		<tr>
			<td align="center">{:$R.id:}</td>
			<td align="center">({:$R.type:}){:$R.type | array.enum ['未知','政策性银行','国有控股','全国商业','邮政储蓄','外资银行','','城市商业','农村商业','农村合作','信用合作','村镇银行','信托公司','财务公司','金融租赁','汽车金融','货币经纪','消费金融']:}</td>
			<td align="center">{:$R.name:}</td>
			<td align="center">
				{:if $R.icon:}<img src="data:image/png;base64,{:$R.icon:}" style="width: 50px;height: 50px;"/>{:/if:}
			</td>   
			<td style="width: 80px;text-align: center;"><a class="upload_img" data-id="{:$R.id:}" style="color: green;"> 上传银行图标 </a></td>
		</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="10">
			{:include file="_g/pager.tpl" Result=$Result:}
		</td>
	</tr>
</table>
<script type="text/javascript">
	$(".upload_img").click(function (ev) {
		var id = $(this).attr('data-id');
		var oInput = document.createElement("input");
		oInput.type = "file";
		oInput.click();
		oInput.addEventListener("change", function () {
			var oFile = this.files[0];
			if (!/image\/\w+/.test(oFile.type)) {
				alert("请确保文件为图像类型");
				return false;
			}
			var fileSize = this.files[0].size;
			fileSize = Math.round(fileSize / 1000 * 200) / 100;                       //判断图片大小是否符合规范
			if (fileSize >= 100) {
				alert('照片最大尺寸大于100k，请重新上传!');
				return false;
			}
			var render = new FileReader();
			render.readAsDataURL(oFile);
			render.onload = function (e) {
				var event = this;
				console.log(event.result);
				YueMi.API.Admin.invoke('system', 'bank_logo', {
					id: id,
					icon: event.result,

				}, function (t, r, q) {
					if (q.__code === 'OK') {
						//alert(111);
						location.reload();
					} else {
						alert(q.__message);
					}
					//成功
				}, function (t, q, r) {
					//失败
				});
			}
		});
	});
</script>			
{:include file="_g/footer.tpl":}
