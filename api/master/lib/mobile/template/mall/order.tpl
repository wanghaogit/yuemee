<!DOCTYPE html>
<html>
<head>
	<title>确认订单</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
	<!-- 样式 -->
	<link href="{:#URL_RES:}/v1/styles/mui.min.css?1" rel="stylesheet" />     <!-- 禁止修改：MUI 基本样式 -->
	<link href="{:#URL_RES:}/v1/styles/awesome.css?1" rel="stylesheet" />     <!-- 禁止修改：字体图标 -->
	<link href="{:#URL_RES:}/v1/styles/yuemi.css?1" rel="stylesheet" />       <!-- 阅米 公共样式 -->
	<link href="{:#URL_RES:}/v1/styles/ziima.css?1" rel="stylesheet" />
	<link href="{:#URL_RES:}/v1/styles/item.css?1" rel="stylesheet" /> <!-- 首页私有样式 -->
	<style type="text/css">
		 /* 本页面临时样式表 */
		.grounding-img4 {
			width: 13.4%;
			float: right;
		}
		.mui-bar-nav .mui-content {
			padding-top: 30px;
		}
	</style>
	<!-- 描述：脚本 -->
	<script src="{:#URL_RES:}/v1/scripts/jquery.js?1"></script> 
	<script src="{:#URL_RES:}/v1/scripts/ziima.js?1"></script>
	<script src="{:#URL_RES:}/v1/scripts/mui.min.js?1"></script>             <!-- 禁止修改：MUI脚本库 -->
	<script src="{:#URL_RES:}/v1/scripts/item.js?1"></script>
	<script src="{:#URL_RES:}/v1/scripts/page.js?1"></script>
	<script type="text/javascript" src="/scripts/api.js?07"></script>
	<script type="text/javascript">
		/* 本页面临时/初始化专用JS */
	</script>
<style>
	.mui-but{
		width: 100%;
		background: #FF6E59;
		color: white;
		font-size: 18px;
		text-align: center;
		padding: 4% 0 4% 0;
		position:fixed;bottom:0;
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
		width: 120px;
		height: 30px;
	}
	.mui-numbox [class*=btn-numbox], .mui-numbox [class*=numbox-btn]{
		background: white;
	}

	.mui-navigate-right:after, .mui-push-left:after, .mui-push-right:after{
		color: white;font-size: 14px;
	}
	.m-yhj{
		border-top: 1px solid #E1E1E1;
		border-bottom: 1px solid #E1E1E1;
	}
	.mui-table-view-cell{
		position: static;
	}
	input[type="checkbox"].Toggle {
		width:18px;
	}
	input[type="checkbox"].Toggle:after {
		content: ' ';
		width:18px;
		height:18px;
		line-height: 18px;margin-top: -3px;
		display: inline-block;
		background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4OTNBQkI5NzJDQUUxMUU4QUE5ODg5NEEyRTVCMDk0NyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4OTNBQkI5ODJDQUUxMUU4QUE5ODg5NEEyRTVCMDk0NyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjg5M0FCQjk1MkNBRTExRThBQTk4ODk0QTJFNUIwOTQ3IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjg5M0FCQjk2MkNBRTExRThBQTk4ODk0QTJFNUIwOTQ3Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+56aSBgAAAERJREFUeNpivHb9zgcGBgZ+BsrARyYqGAIC/CwwlqaGMlkmXL9xF0wzMVAJjBo0atCoQfQxiAU9F1Pioo9UcNBHgAADAEKYDC0s4EfcAAAAAElFTkSuQmCCiVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDRTdEQ0JDOTJDQTYxMUU4QjI3QkQ0NDRCN0E4QkM1MCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDRTdEQ0JDQTJDQTYxMUU4QjI3QkQ0NDRCN0E4QkM1MCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNFN0RDQkM3MkNBNjExRThCMjdCRDQ0NEI3QThCQzUwIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkNFN0RDQkM4MkNBNjExRThCMjdCRDQ0NEI3QThCQzUwIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ie7cRQAAAFlJREFUeNpivHb9jicDA8NcIJZkIA88B+JkRqBBzygwBG4YE5IhjKRiTQ1lRqheSSYGKoFRg0YNGjWIfgY9h7L/k4qv37j7H6r3BcigFBCDAsc8BRVsAAEGANGWHvkMvTfoAAAAAElFTkSuQmCCiVBORw0KGgoAAAANSUhEUgAAACIAAAAiCAMAAAANmfvwAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QTYxNDRFMTMyQzMwMTFFOEE5MUE5MjM3QjgzQTc5RUQiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QTYxNDRFMTIyQzMwMTFFOEE5MUE5MjM3QjgzQTc5RUQiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJERjg0M0Y3MkJFRTExRThCM0ZBQjg5MkI1QTBGNTBDIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJERjg0M0Y4MkJFRTExRThCM0ZBQjg5MkI1QTBGNTBDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ME5ceAAAAAlQTFRF1tfc////AAAAdGCt9gAAAAN0Uk5T//8A18oNQQAAACRJREFUeNpiYGIgAAgqgABGvGBUyaiSUSUjUwkDxSUME0CAAQBDggONLnhhZgAAAABJRU5ErkJggg==')  !important;
		background-repeat: no-repeat;
		cursor:pointer;
	}
	input[type="checkbox"].Toggle:checked:after {
		content: ' ';
		width:18px;
		height:18px;
		line-height: 18px;margin-top: -3px;
		display: inline-block;
		background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDRjEyMTA4MTJDQTYxMUU4ODRFQ0RDRDM3NDc2RDVCOSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDRjEyMTA4MjJDQTYxMUU4ODRFQ0RDRDM3NDc2RDVCOSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNGMTIxMDdGMkNBNjExRTg4NEVDRENEMzc0NzZENUI5IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkNGMTIxMDgwMkNBNjExRTg4NEVDRENEMzc0NzZENUI5Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+4HM/6QAAAYVJREFUeNqclMFOwkAQ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') !important;
		background-repeat: no-repeat;
		cursor:pointer;
	}
	.mui-numbox [class*=btn-numbox], .mui-numbox [class*=numbox-btn]{
		width:25px;
	}
	.mui-numbox{
		padding: 0 25px;
	}
</style>
</head>
<body style="background: #FAFAFA;">
<div class="mui-content ziima">
	 <!-- 头部 -->
    <header class="mui-bar mui-bar-nav">
        <a class="mui-action-back mui-icon mui-pull-left" style="color: #666666;margin-right:-3px" onclick="javascript:history.back(-1);"><img src="{:#URL_RES:}/v1/images/mobile/itemnext.png" style="width: 50%;"/></a>
        <h1 class="mui-title message" style="font-size: 16px;text-align: center;position: absolute;">确认订单</h1>
    </header>
    <!-- 中间主体内容 -->
    <div class="mui-content">
    	<ul class="mui-table-view" style="background:red;">
			{:if $res_id > 0:}
				<li class="mui-table-view-cell addresssected">
					<input type="hidden" id="AddressId" value="0" />
					<span style="font-size: 14px;color: white;">{:$data.contacts:}</span>
					<a  href="/mobile.php?call=goto.user_center&type=1" style="font-size: 12px;color: white;white-space:nowrap;">{:$data.province:}{:$data.city:}{:$data.country:}<div style="float:right;height: 100%;margin-right:-15px;"><img src="{:#URL_RES:}/v1/images/mobile/itemback2.png" style="width: 40%;"/></div></a>
					
				</li>
			{:else:}
				<li class="mui-table-view-cell addresssected">
					<input type="hidden" id="AddressId" value="0" />
					<span style="font-size: 14px;color: white;">暂无默认收货地址</span>
					<a href="/mobile.php?call=goto.user_center&type=1" class="mui-navigate-right" style="font-size: 12px;color: white;white-space:nowrap;">请添加设置默认收货地址</a>
					<!--<span style="font-size: 14px;color: white;">收货时间不限</span>-->
				</li>
			{:/if:}
		</ul>
    	<ul id="cartlisted" class="mui-table-view ">
		   <li class="mui-table-view-cell mui-media cartli">
		        <a href="javascript:;">
		        	<input type="hidden" id="SkuId" value="0" />
		            <img id="contentThumb" class="mui-media-object mui-pull-left order-img" src="{:$sku_url:}" style="max-width: 23.2%;height: 80px;">
		            <div class="mui-media-body title">
		                <p id="contentTitle" style="font-size:12px">{:$result.title:}</p>
		                <div style="height:17px;"></div>
		                <p class="mui-ellipsis money" style="float: left;">
		                	阅米价<span id="contentSale" data-Unitprice="0.00" style="font-size:16px">{:$result.price_sale | number.currency:}</span>
		                	<span id="contentRef">{:$result.price_ref | number.currency:}</span>
		                </p> 
		             <div class="mui-numbox" data-numbox-step='1' data-numbox-min='1' data-numbox-max='1000' style="float: right; margin-top:5px;width: 90px; height: 26px;">
    	                    <button class="mui-btn mui-btn-numbox-minus" type="button" id="jian">-</button>
    	                    <input  id="contentNum" class="mui-input-numbox" type="number" value="1" style="font-size:11px"/>
    	                    <button class="mui-btn mui-btn-numbox-plus" type="button" id="jia">+</button>
    	                </div>
		            </div>
		        </a>
		    </li>
		</ul>
		<ul class="mui-table-view" style="margin-top: 3px;">
		    <li class="mui-table-view-cell m-je">
		    	<span style="float:left; font-size:14px; color:#333">配送方式</span>
		    	<span style="float:right; font-size:14px; color:#333">快递免邮</span>
		    </li> 
		</ul>
		<ul class="mui-table-view" style="margin-top: 3px;">
		    <li class="mui-table-view-cell m-je">
		    	<span>应付金额:</span>
		        <span><span id="PriceTotal">{:$result.price_sale | number.currency:}</span></span>
		    </li> 
		</ul>
		<ul class="mui-table-view">
			<li class="mui-table-view-cell " style="float: left;color: #999999;"><input id="check1" type="checkbox" class="Toggle"/></li>
		    <li class="mui-table-view-cell" style="float: left;color: #999999;font-size: 14px;margin-left: -20px;">钱包余额</li>
		    <li class="mui-table-view-cell" style="text-align: right;color: #999999;font-size: 14px;"><span id="MyMoney">{:$qianbao | number.currency:}</span></li>
		</ul>
		<ul class="mui-table-view m-yhj">
			<li class="mui-table-view-cell " style="float: left;color: #999999;"><input id="check2" type="checkbox" class="Toggle"/></li>
		    <li class="mui-table-view-cell" style="float: left;color: #999999;font-size: 14px;margin-left: -20px;">佣金</li>
		    <li class="mui-table-view-cell" style="text-align: right;color: #999999;font-size: 14px;"><span id="MyProfit">{:$yongjin | number.currency:}</span></li>
		</ul>
		<ul class="mui-table-view" style="margin-top: 3px;">
			
		    <li class="mui-table-view-cell" style="text-align: right;font-size: 12px;color:#333;"><span>共<span id="ItemNum">1</span>件商品&nbsp;&nbsp;还需支付</span><span style="font-size:16px;color:#FB342A"><span id="PriceResidue">{:$result.price_sale | number.currency:}</span></span>&nbsp;<span id="Pricesss" style="color:#999;">(已优惠0.00元)</span></li>
		</ul>
		
    </div>
    
	<div class="mui-but" style="background-color:#F2493D;height: 40px;line-height: 17px;" id="DoBuy">去支付<span id="pay_money">{:$result.price_sale | number.currency:}</span></div>
    
</div>
<script type="text/javascript">

	function change_money() {
		var status = document.getElementById('check1').checked;
		var money = Number(document.getElementById('MyMoney').innerHTML.substr(1));
		var need_money = Number(document.getElementById('PriceTotal').innerHTML.substr(1));
		var pay = 0;
		if (status){
			if (money > need_money){
				pay = 0;
			} else {
				pay = need_money - money;
			}
		} else {
			pay = need_money;
		}
		return pay.toFixed(2);		
	}
	updata_price();	
	var price_sale = '{:$result.price_sale:}';
	var price_ref = '{:$result.price_ref:}';
	function updata_price()
	{
		// 修改数量
		var testBox=document.getElementById("contentNum");
		testBox.addEventListener('change',function(){
			$("#ItemNum").html(testBox.value);
			console.log(typeof testBox.value);
			Value = Number(testBox.value);
			$("#PriceTotal").html(((price_sale*100*Value)/100).toFixed(4));
			allPrice();
		});
		$(".Toggle").on("change",function(){
			allPrice();
		});
		// 下单请求
		$("#DoBuy").on("tap",function(){
			if("{:$res_id:}" == 0 || "{:$res_id:}" == "") {
				mui.toast("请选择收货地址");
				return false;
			}
			// 购买 - 快速下单
			__order_create();
		});
	}
	function allPrice()
	{
		var pricesss = 0;
		var arrp = $("#MyProfit").html(); 
		var pay = change_money();
		document.getElementById('PriceResidue').innerHTML = '';
		document.getElementById('PriceResidue').innerHTML = pay;
		document.getElementById('pay_money').innerHTML = '';
		document.getElementById('pay_money').innerHTML = pay;
		$("#Pricesss").html('(已优惠'+(pricesss/100)+'元)');
	}

	/** ******************************************** 创建订单 ******************************************** **/

	function __order_create()
	{
		var  qty = $('#contentNum').val();
		var  AddressId = {:$res_id:};
		var  sel_use_money = $('#check1').attr('checked') == true ? 1 : 0;
		var  sel_use_profit = $('#check2').attr('checked') == true ? 1 : 0;

		YueMi.API.invoke('order', 'fast_order', {
				__access_token : '{:$User->token:}',
				share_id: {:$share_id:}, // 分享ID
				sku_id: {:$result.id:}, // SkuId
				qty: qty , // 数量
				user_address_id: AddressId, // 收货地址Id：0表示使用默认值(需要从库里拿出默认值，或是第一条，也可能完全没有...)
				sel_use_money: sel_use_money, // 是否使用余额 1.使用，0不使用
				sel_use_profit: sel_use_profit, // 是否使用佣金 1.使用，0不使用
				sel_use_recruit: 0, // 是否使用佣金礼包 1.使用，0不使用
				sel_use_ticket: 0, // 是否使用卡券 1.使用，0不使用
				message : "",
			}, function(target, request, response) {
				if (response.__code == "OK") {
					PayWeiXin(response.order_id);
				} else {
					mui.toast('下单失败，请联系管理员!');
				}
			}, function(target, request, response) {
				if (response.__message) {
					mui.toast(response.__message);
				} else {
					mui.toast('下单失败，请检查网络是否正常!');
				}
			}
		);
	}

	// 微信支付 - 获取微信订单信息
	var OrderId;
	var WxOrderInfo;
	function PayWeiXin(YmOrderId)
	{
		YueMi.API.invoke('order', 'make_owx_gongzhonghao', {
				__access_token : '{:$User->token:}',
				openid : '{:$Wechat->web_open_id:}',
				order_id: "" + YmOrderId, // 销售商品id
				is_merge_pay: "1", // 是否合并支付：0不是(只支付当前订单)，1是（将该订单和其下的所有子订单合并支付）
			}, function(target, request, response) {
				if (response.__code == 'OK') {
					OrderId = YmOrderId;
					WxOrderInfo = response;
					PayWeiXinSend();
				} else {
					mui.toast('获取支付信息失败，请联系管理员!');
				}
			}, function(target, request, response) {
				mui.toast('获取支付信息失败，请检查网络是否正常!');
			}
		);
	}

	// 微信支付 - 调起微信客户端支付
	function PayWeiXinSend()
	{
		if (typeof WeixinJSBridge == "undefined") {
			if ( document.addEventListener ) {
				document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
			} else if (document.attachEvent) {
				document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
				document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
			}
		} else {
		   onBridgeReady();
		}
	}

	// 调起微信支付
	function onBridgeReady()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest', 
			{
				"appId": "" + WxOrderInfo.appId, // 公众号名称，由商户传入
				"timeStamp": "" + WxOrderInfo.timeStamp, // 时间戳，自1970年以来的秒数
				"nonceStr": "" + WxOrderInfo.nonceStr, // 随机串 
				"package": "prepay_id=" + WxOrderInfo.prepay_id,
				"signType": "MD5", // 微信签名方式：MD5
				"paySign": "" + WxOrderInfo.sign, //微信签名
			},
			function(res)
			{
				if (res.err_msg == "get_brand_wcpay_request:ok") {
					alert("支付成功~");
				} else {
					alert("支付失败~");
				}
			}
		); 
	}

</script>
</body>
</html>
