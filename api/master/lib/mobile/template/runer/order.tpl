<!DOCTYPE html>
<html>
	<head>
       <title>确认订单</title>
       <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <!-- 样式 -->
        <link href="{:#URL_RES:}/v1/styles/mui.min.css" rel="stylesheet" />     <!-- 禁止修改：MUI 基本样式 -->
        <link href="{:#URL_RES:}/v1/styles/awesome.css" rel="stylesheet" />     <!-- 禁止修改：字体图标 -->
        <link href="{:#URL_RES:}/v1/styles/yuemi.css" rel="stylesheet" />       <!-- 阅米 公共样式 -->
        <link href="{:#URL_RES:}/v1/styles/ziima.css" rel="stylesheet" />
        <style type="text/css">
             /* 本页面临时样式表 */
            .grounding-img4 {
			    width: 13.4%;
			    float: right;
			}
			.mui-bar-nav~.mui-content {
			    padding-top: 30px;
			}
        </style>
        <!-- 描述：脚本 -->
		<script src="{:#URL_RES:}/v1/scripts/jquery.js"></script> 
		<script src="{:#URL_RES:}/v1/scripts/ziima.js"></script>
        <script src="{:#URL_RES:}/v1/scripts/mui.min.js"></script>             <!-- 禁止修改：MUI脚本库 -->
		<script src="{:#URL_RES:}/v1/scripts/page.js"></script>
		<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/api.js"></script>
        <script type="text/javascript">
            /* 本页面临时/初始化专用JS */
        </script>
<style>
.mui-table-view-cell:after{
	content: none;
}	
.mui-bar-nav~.mui-content{
	padding-top: 30px;
}
.mui-table-view{
	position: static;
}
.mui-bar{
	background: white;
}
.mui-media-body{
	color: #333333;
}
.money span:nth-child(1){
	line-height: 30px;
	color: red;
	font-size: 20px;
}
.money span:nth-child(2){
	text-decoration:line-through
}
.title{
	word-break:break-all;
	padding: 0;
	margin: 0;
	
}
.mui-table-view-cell>a:not(.mui-btn){
	white-space:inherit;
}
.mui-media-body{
	color: #333333;
	font-size: 15px;
}
.mui-bar-nav{
	box-shadow:0 1px 1px #e1e1e1;
}
.mui-navigate-right{
	font-size: 14px;
	color: #999999;
	
}
.m-wl{
	margin-top: 4px;
}
.mui-input-row input{
	background: white;
}
.mui-input-row label{
	font-size: 14px;	
}
.m-je{
	text-align: center;
}
.m-je span:nth-child(1){
	font-size: 14px;
}
.m-je span:nth-child(2){
	font-size: 15px;
}
.mui-numbox{
	width: 100px;
	height: 30px;
}
.mui-numbox [class*=btn-numbox], .mui-numbox [class*=numbox-btn]{
	background: white;
}

.mui-table-view-cell{
	position: static;
}
.mui-yunfei{
	border-top:0px solid #e1e1e1;
}
.mui-table-view:after{
	position: static;
}
.mui-content{
	background-color: #FAFAFA;
}
.mui-table-view-cell{
	padding: 8px 15px;
}

</style>
	</head>
	<body style="background: #FAFAFA;">
		<div class="mui-content ziima">
			<!-- 头部 -->
			<header class="mui-bar mui-bar-nav">
				<a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left" style="color: #666666;"></a>
				<h1 class="mui-title message" style="font-size: 18px;text-align: center;position: absolute;">订单详情</h1>
			</header>
			<!-- 中间主体内容 -->
			<div class="mui-content" style="background-image: url(../images/bg.png);">
				<ul class="mui-table-view" style="background-image: url(../images/bg.png);">
					<li class="mui-table-view-cell"  style="float: left;font-size: 14px;color: #FFFFFF;line-height: 55px;">
						<span><img src="../images/whitetime.png" style="width: 1rem;vertical-align: middle;margin-top: -9px;"/>&nbsp;&nbsp;</span><font id="Statustext" style="font-size: 24px;"></font>
					</li>
					<li class="mui-table-view-cell" style="float: right; font-size: 15px;color: rgba(0,0,0,0);">剩余：21小时50分钟</li>
					<li id="TOnline2" class="mui-table-view-cell" style="float: right; font-size: 15px;color: #FFFFFF;">需付款：￥0.00</li>
				</ul>

				<div style="clear: both;"></div>
				<ul class="mui-table-view">
					<li style="width: 100%;height: 10px;background-color: #fafafa;"></li>

					<li class="mui-table-view-cell mui-media">
						<a href="javascript:;">
							<img class="mui-media-object mui-pull-left" src="../images/dizhi.png" style="width: 0.9rem;height: 18px;"/>
							<div id="address" class="mui-media-body">
								&nbsp;&nbsp;&nbsp;
								<p class="mui-ellipsis"></p>
							</div>
						</a>
					</li>
					<li style="width: 100%;height: 10px;background-color: #fafafa;"></li>

				</ul>
				<ul class="mui-table-view mui-yunfei">
					<li class="mui-table-view-cell" style="font-size: 14px;">
						<img src="../images/time.png"  style="width: 0.9rem;vertical-align: middle;margin-top: -3px;"/>&nbsp;&nbsp;收货时间不限
					</li>
					<li style="width: 100%;height: 10px;background-color: #fafafa;"></li>

				</ul>
				<ul id="orderUl" class="mui-table-view">

				</ul>
				<ul class="mui-table-view"> 
					<li class="mui-table-view-cell mui-collapse logisticsBtn none">
						<p style="width: 4.53%;float: left;margin-top: 2px;"><img src="../images/wl.png" style="width: 100%;"/></p>
						<p style="width: 1.8%;float: left;margin-left: 25px;"><img src="../images/wuliu.png" style="width: 100%;"/></p>
						<a class="mui-navigate-right" href="javascript:;" style="font-size: 14px;color: #999999;padding-left: 20px;">物流信息</a>
						<div class="mui-collapse-content" id="wuliucon">
							<div >
								<p class="wuliutime"></p>
								<p class="wuliumessgae">暂无物流信息</p>
							</div>
						</div>
					</li>
				</ul>
				<ul class="mui-table-view">
					<li class="mui-table-view-cell m-je">
						<span><img src="../images/kfs.png"  style="width: 1rem;"/></span>
						<span>联系客服</span>
					</li> 
					<li style="width: 100%;height: 10px;background-color: #fafafa;"></li>
				</ul>
				<ul class="mui-table-view mui-yunfei">
					<li class="mui-table-view-cell" style="float: left;color: #999999;font-size: 14px;width: 40%;">商品总价</li>
					<li id="TAmount" class="mui-table-view-cell" style="text-align: right;color: #999999;font-size: 14px;width: 60%;">￥0.00</li>
				</ul>
				<ul class="mui-table-view mui-yunfei">
					<li class="mui-table-view-cell" style="float: left;color: #999999;font-size: 14px;">实付款 (微信支付)</li>
					<li id="TOnline" class="mui-table-view-cell" style="text-align: right;color: #999999;font-size: 14px;color: red;">￥0.00</li>
					<li style="width: 100%;height: 10px;background-color: #fafafa;"></li>

				</ul>
				<ul class="mui-table-view mui-yunfei">
					<li id="ordid" class="mui-table-view-cell" style="float: left;color: #999999;font-size: 14px;background-color: #FFFFFF;width: 100%;">订单编号：</li>
					<li id="CreateTime" class="mui-table-view-cell" style="float: left;color: #999999;font-size: 14px;background-color: #FFFFFF;width: 100%;">下单时间：</li>
				</ul>

			</div>
			<div id="buttonStatustext" style="width: 50%;float: right;padding-top: 10px;">

				<button type="button" class="mui-btn mui-btn-danger" style="border: 1px solid #D6D7DC; background: white;color:#999;width: 44%;margin: 0 2%;">取消订单</button>
				<button type="button" class="mui-btn mui-btn-royal" style="background: #FE332D;border: 1px solid red;  width: 44%; margin: 0 2%; ">支付 </button>
			</div>

		</div>
		<!-- 弹框 -->
		<div style="height: 30px;"></div>
	</body>
	<script type="text/javascript">
		$(".logisticsBtn").on("tap", function () {
			$(".logisticsMes").toggle();
		});
	</script>
</html>

