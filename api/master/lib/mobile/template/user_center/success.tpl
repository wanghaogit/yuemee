<!DOCTYPE html>
<html lang="en">
	<head>
		<title>支付成功</title>
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
			.boss{
				width: 100%;
				height: 700px;
				background: #F7F7F7;
			}
			.pay_success{
				width:100%;
				height:180px;

				/*background:orange;*/
			}
			.see_mail{
				width:100%;
				background:white;
			}
			.guuess_like{
				width:100%;
				height:140px;
				/*background:yellow;*/
			}
			.success{
				margin-top:-120px;
				margin-left:0px;
				width:6px;
				height: 70px;
				background: white;

			}
			.back_img div{
				width:100%;
				height:180px;
				background:url(./FuKuan/css/img/1.png) no-repeat;
				background-size: 100%;

			}
			.money{
				margin-top:-160px;
				margin-left:0px;
				width: 6px;
				height: 30px;
			}
			.word{
				margin-top:-130px;
				margin-left:0px;
				width: 5px;
				height: 10px;
				background: white;
			}
			.see_mail .back_index{
				margin:0 auto;
				/*margin-top: 20px;*/
				width: 90%;
				height: 25%;
				background: #F2493D;
			}
			.see_info{
				/*	margin:0 auto;*/
				margin-top: 20px;
				/*margin-left:10% ;*/
				margin-left: auto;
				margin-right: auto;
				width: 90%;
				height: 25%;

				/*background: #D6D7DC;*/
			}
			.see_mail .back_index .index{
				text-align: center;
				line-height: 55px;
			}
			.see_mail .see_info .info{
				text-align: center;
				line-height: 55px;
			}
			.mui-col-xs-6 {
				width: 25%;
			}
			.mui-bar-nav{
				box-shadow:0 1px 1px #e1e1e1;
			}
			.mui-table-view{
				position: static;
			}

		</style>
	</head>
	<body>
		<div class="mui-content ziima">
			<div class="mui-content">
				<div class="boss">
					<header class="mui-bar mui-bar-nav" style="background-color: white;   box-shadow: 0 1px 0px #e1e1e1;">
						<span class="mui-action-back mui-icon mui-icon-arrowleft"></span>
						<h1 class="mui-title">支付成功</h1>
					</header>

					<div class="pay_success" style="width: 100%;">
						<div class="back_img">
							<img src="../images/1.png" style="width: 100%;height: 180px;margin-top: 40px;"/>
							<div class="success" style="text-align: center;margin-left: -28%;">
								&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
								<span style="color: #EE645D;font-size: 16px;">支</span>
								<span style="color: #FFAB00;font-size: 16px;">付</span>
								<span style="color: #EE645D;font-size: 16px;">成</span>
								<span style="color: #FF0000;font-size: 16px;">功</span>
								<span style="color: #EE645D;font-size: 16px;">~</span>
							</div>
							<div class="money" style="text-align: center;margin-left: -26%;">
								&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
								<span style="color: rgba(0,0,0,0);font-size: 16px;">微</span>
								<span style="color: rgba(0,0,0,0);font-size: 16px;">信</span>
								<span style="color: rgba(0,0,0,0);font-size: 16px;">支</span>
								<span style="color: rgba(0,0,0,0);font-size: 16px;">付</span>
								<span style="color: rgba(0,0,0,0);font-size: 16px;">:</span>
								<span  class="num"  style="color:  rgba(0,0,0,0);font-size: 16px;">0元</span>
							</div>
							<div class="word"  style="margin-top: -155px;text-align: center;letter-spacing: 1px;font-size:12px;color:#d6d7dc;">
								我们会尽快发出您喜爱的商品哦！请耐心等待一小会~
							</div>
						</div>
					</div>
					<div class="see_mail" style="border: 1px solid #ffffff;">
						<div class="back_index" style="margin-top: 14.6%;height:40px;border-radius: 4px;"><p class="index" style="    line-height: 42px;"><font size="4" color="white">返回首页</font></p></div>
						<div class="see_info" style="border-radius: 4px;"><p class="info" style="border:1px solid; color: #e1e1e1;height:40px;line-height:40px;border-radius: 4px;"><font size="4" style="color: #666;">查看订单</font></p></div>
					</div>
					<div style="height: 30px;background-color: #fafafa;"></div>
					<script type="text/javascript">
						mui('.mui-bar-tab').on('tap', 'a', function (e) {
							top.location = this.getAttribute('href');
						});
					</script>

				</div>
			</div>
		</div>

    </body>
</html>
