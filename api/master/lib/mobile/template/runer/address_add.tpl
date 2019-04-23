<!DOCTYPE html>
<html>
    <head>
        <title>添加地址</title>
		<meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <!-- 样式 -->
        <link href="{:#URL_RES:}/v1/styles/mui.min.css" rel="stylesheet" />     <!-- 禁止修改：MUI 基本样式 -->
        <link href="{:#URL_RES:}/v1/styles/awesome.css" rel="stylesheet" />     <!-- 禁止修改：字体图标 -->
        <link href="{:#URL_RES:}/v1/styles/yuemi.css" rel="stylesheet" />       <!-- 阅米 公共样式 -->
        <link href="{:#URL_RES:}/v1/styles/ziima.css" rel="stylesheet" />
		<link href="{:#URL_RES:}/v1/styles/mui.picker.min.css" rel="stylesheet" /> 
		<link href="{:#URL_RES:}/v1/styles/mui.poppicker.css" rel="stylesheet" /> 
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
		 
        <script src="{:#URL_RES:}/v1/scripts/mui.min.js"></script>             <!-- 禁止修改：MUI脚本库 -->
		<script src="{:#URL_RES:}/v1/scripts/jquery.js"></script>
		<script src="{:#URL_RES:}/v1/scripts/ziima.js"></script>
		<script src="{:#URL_RES:}/v1/scripts/page.js"></script>
		<script src="{:#URL_RES:}/v1/scripts/city.data-3.js"></script> 
		<script src="{:#URL_RES:}/v1/scripts/mui.picker.min.js"></script> 
		<script src="{:#URL_RES:}/v1/scripts/mui.poppicker.js"></script> 
		<script src="{:#URL_RES:}/v1/scripts/dropload.js"></script> 
		<script src="{:#URL_RES:}/v1/scripts/address_add.js"></script> 
		<script src="{:#URL_RES:}/v1/scripts/egCommen.js"></script> 
		<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/api.js"></script>
        <script type="text/javascript">
            /* 本页面临时/初始化专用JS */
        </script>
<style>
/*头部 公共样式*/
.head_info
{
	clear:both;
	overflow:hidden;
	background:#ffffff;
	padding:4% 2%;
	color:#333333;
	font-size: 14px;
	font-weight: bold;
	font-family:PingFang-SC-Medium;
}
.head_info a.head_info_return
{  
	float:left;
	width:3%;
}
.head_info span
{   
	float:left;
	display:block;
	text-align:center;
	font-size:120%;
	margin-left:33%;
}

.head_info a.head_info_other
{    
	float:right;
	color:#Fff;
	font-size:100%;
}
/*头部*/
/*公共样式*/
.mui-icon-arrowleft{
	float: left;
	color: #666666;
}
.mui-icon-search{
	float: right;
	color: #666666;
	margin-right: 15px;
}
.mui-bj{
	color: #333333;
	font-size: 14px;
	float: right; 
	margin-right: 13px;
}
.mui-content{
	background: #FAFAFA; 
	
}
.mui-input-row label{
	color: #000000;
	font-size: 16px;
	font-family: PingFang-SC-Medium;
}
.mui-input-clear{
	color: #999999;
	font-size: 14px;
}
.mui-bar-nav{
	box-shadow:0 1px 1px #e1e1e1;
}

.words{
   
	width: 90%;
	height: 40px;
	line-height:10px;
	background: #F2493D;
	border-radius: 8px;

	margin-top:100px;
	margin-left:17px;
}
.words .word{
	padding-top: 15px;
	text-align: center;
}
 #con:after{
			content:none;
		}
		#f:after{
			content: none;
		}
</style>  
    </head>
    <body style="background: #FAFAFA;">
    	<!-- 头部 -->
	    <header class="mui-bar mui-bar-nav" style="background-color: white;">
	        <a class="backpage mui-icon mui-icon-left-nav mui-pull-left" style="color: #666666;"></a>
	        <h1 class="mui-title" style="font-size: 16px;">添加新地址</h1>
	        <a id="preservation" class="mui-action-back" style="color: #666666;float: right;margin-top: 12px">保存</a>
	    </header>
	    <!-- 中部 -->
		<div class="mui-card-content" style="margin-top: 60px">
			<div class="mui-input-row" style="background: white; font-size:16px;width: 94%;margin-left: 3%;margin-right: 3%;">
							<label style="margin-top:5px;">收货人</label>
							<input id="name" type="text" class="mui-input-clear" placeholder="请输入收货人姓名" style="font-size:14px;margin-top:5px;">
			</div>
			<div class="line" style="width: 94%;margin-left: 3%;margin-top: 1px;    background: #fafafa;"></div>
			<div class="mui-input-row" style="background: white; font-size:16px;width: 94%;margin-left: 3%;margin-right: 3%;">
							<label style="margin-top:5px;">所在地区</label>
							<input readonly="readonly" id="liveProvince" type="text" class="mui-input-clear" placeholder="请选择所在地区" style="font-size:14px;margin-top:5px;">
			</div>
			<div class="line" style="width: 94%;margin-left: 3%;margin-top: 1px;    background: #fafafa;"></div>
			<div class="mui-input-row" style="background: white; font-size:16px;width: 94%;margin-left: 3%;margin-right: 3%;">
							<label style="margin-top:5px;">手机号</label>
							<input id="mobile" type="text" class="mui-input-clear" placeholder="请输入收货人的手机号" style="font-size:14px;margin-top:5px;">
			</div>
			<div class="line" style="width: 94%;margin-left: 3%;margin-top: 10px;    background: #fafafa;"></div>
			<div class="mui-input-row" style="background: white; font-size:16px;width: 94%;margin-left: 3%;margin-right: 3%;height: 80px;">
							<label style="margin-top:5px;">详细地址</label>
							<textarea id="addre"    class="mui-input-clear" placeholder="请输入详细地址" style="font-size:14px;margin-top:5px;"></textarea>
			</div>
			
		</div>
		<script type="text/javascript">
            /* 本页面临时/初始化专用JS */
			//居住地
			mui.init();
			var addrType,did;
			var city3 = new mui.PopPicker({
				layer: 3
			});
			city3.setData(cityData3);
			$("#liveProvince").on("tap", function() {
				document.activeElement.blur();
				var $this = $(this);
				city3.show(function(items) {
					var qu = (items[2] || {}).text;
					var qu1 = (items[2] || {}).value;
					if(!qu) {
						qu = "";
					}
					$this.val((items[0] || {}).text + " " + (items[1] || {}).text + " " + qu);
					$this.attr("data-key",qu1);
				});
			})
			
			//点击保存
			$("#preservation").on("tap",function(){
				var name = $("#name").val();
				var mobile = $("#mobile").val();
				var liveProvince = $("#liveProvince").val();
				var liveProvince1 = $("#liveProvince").attr("data-key");

				var addre = $("#addre").val();
				//parseInt()
				if (eg.isNullVal(name))
				{
					mui.toast("收货人姓名不能为空");
					return false;
				}
				if (eg.isNullVal(mobile))
				{
					mui.toast("手机号不能为空");
					return false;
				}

				if (eg.isNullVal(liveProvince))
				{
					mui.toast("所在地区不能为空");
					return false;
				}
				if (eg.isNullVal(addre))
				{
					mui.toast("详细地址不能为空");
					return false;
				}
				if(addrType){
					edit_address(parseInt(liveProvince1),addre,name,mobile);

				}else{
					add_address(parseInt(liveProvince1),addre,name,mobile);
				}
			})
			//新增收获地址
			function add_address(region_id,address,contacts,mobile){

				var addressnew = {
				    __access_token : '{:$User->token:}',
					//__access_token :'Nyfp9oicVSrrGVp7',
					region_id : region_id,		
					address : address,			
					contacts :contacts,
					mobile : mobile
				}
               
				YueMi.API.invoke('profile','address_new',addressnew,function(target, request, response){
						console.log(JSON.stringify(response));
						//分析数据
						mui.toast('新增收获地址成功！');
						backpage();
					},function(target, request, response){
						console.log("target:"+JSON.stringify(target));
						console.log("request:"+JSON.stringify(request));
						console.log("response:"+JSON.stringify(response));
						mui.toast('新增收获地址失败');
					});
			}
			//返回
			
			function backpage(){
				window.location.href = '/mobile.php?call=runer.address';
			}
		
        </script>
    </body>
</html>
