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
        <link href="{:#URL_RES:}/v1/styles/item.css" rel="stylesheet" /> <!-- 首页私有样式 -->
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
		<script src="{:#URL_RES:}/v1/scripts/address.js"></script> 
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
	margin-left:37%;
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
.mui-table-view-ce1{
	color: #000000;
	
	font-size: 15px;
}
.mui-table-view-ce2{
	font-size: 15px;
	float: right;
}
.mui-table-view-ce3{
	float: left;
	line-height: 26px;
	font-size: 12px;
	color:#333;
}
.mui-icon-trash{
	float: right;
	font-size: 16px;
}
.mui-icon-compose{
	float: right;
	font-size: 16px;
}
.mui-but{
	width: 94%;
	background: #F2493D;
	color: white;
	font-size: 18px;
	margin-left: 3%;
	border-radius: 4PX;
	text-align: center;
	height: 40px;
	line-height: 40px;
	position:fixed;bottom:40%;
}
input[type="checkbox"].Toggle {
	width:18px;
}
input[type="checkbox"].Toggle:after {
	content: ' ';
	width:18px;
	height:20px;
	line-height: 18px;margin-top: -3px;
	display: inline-block;
	background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo4OTNBQkI5NzJDQUUxMUU4QUE5ODg5NEEyRTVCMDk0NyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo4OTNBQkI5ODJDQUUxMUU4QUE5ODg5NEEyRTVCMDk0NyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjg5M0FCQjk1MkNBRTExRThBQTk4ODk0QTJFNUIwOTQ3IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjg5M0FCQjk2MkNBRTExRThBQTk4ODk0QTJFNUIwOTQ3Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+56aSBgAAAERJREFUeNpivHb9zgcGBgZ+BsrARyYqGAIC/CwwlqaGMlkmXL9xF0wzMVAJjBo0atCoQfQxiAU9F1Pioo9UcNBHgAADAEKYDC0s4EfcAAAAAElFTkSuQmCCiVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDRTdEQ0JDOTJDQTYxMUU4QjI3QkQ0NDRCN0E4QkM1MCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDRTdEQ0JDQTJDQTYxMUU4QjI3QkQ0NDRCN0E4QkM1MCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNFN0RDQkM3MkNBNjExRThCMjdCRDQ0NEI3QThCQzUwIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkNFN0RDQkM4MkNBNjExRThCMjdCRDQ0NEI3QThCQzUwIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ie7cRQAAAFlJREFUeNpivHb9jicDA8NcIJZkIA88B+JkRqBBzygwBG4YE5IhjKRiTQ1lRqheSSYGKoFRg0YNGjWIfgY9h7L/k4qv37j7H6r3BcigFBCDAsc8BRVsAAEGANGWHvkMvTfoAAAAAElFTkSuQmCCiVBORw0KGgoAAAANSUhEUgAAACIAAAAiCAMAAAANmfvwAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QTYxNDRFMTMyQzMwMTFFOEE5MUE5MjM3QjgzQTc5RUQiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QTYxNDRFMTIyQzMwMTFFOEE5MUE5MjM3QjgzQTc5RUQiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjJERjg0M0Y3MkJFRTExRThCM0ZBQjg5MkI1QTBGNTBDIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjJERjg0M0Y4MkJFRTExRThCM0ZBQjg5MkI1QTBGNTBDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+ME5ceAAAAAlQTFRF1tfc////AAAAdGCt9gAAAAN0Uk5T//8A18oNQQAAACRJREFUeNpiYGIgAAgqgABGvGBUyaiSUSUjUwkDxSUME0CAAQBDggONLnhhZgAAAABJRU5ErkJggg==')  !important;
	background-repeat: no-repeat;
	cursor:pointer;
}
input[type="checkbox"].Toggle:checked:after {
	content: ' ';
	width:18px;
	height:20px;
	line-height: 18px;margin-top: -3px;
	display: inline-block;
	background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDRjEyMTA4MTJDQTYxMUU4ODRFQ0RDRDM3NDc2RDVCOSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDRjEyMTA4MjJDQTYxMUU4ODRFQ0RDRDM3NDc2RDVCOSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNGMTIxMDdGMkNBNjExRTg4NEVDRENEMzc0NzZENUI5IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkNGMTIxMDgwMkNBNjExRTg4NEVDRENEMzc0NzZENUI5Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+4HM/6QAAAYVJREFUeNqclMFOwkAQ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') !important;
	background-repeat: no-repeat;
	cursor:pointer;
}
.mui-bar-nav{
	box-shadow:0 1px 1px #e1e1e1;
}

.mui-table-view-cell:after{
	left: 3%;
	right: 3%;
}
.mui-table-view:before{
	height: 0;
}
.mui-table-view:after{
	height: 0;
}
		</style>
 </head>
    <body style="background: #FAFAFA;">

        <div class="mui-content ziima">
           <!-- 头部 -->
            <header class="mui-bar mui-bar-nav" style="background-color: white;">
                <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left" style="color: #666666;"></a>
                <h1 class="mui-title" style="font-size: 16px;">地址管理</h1>
               
            </header>
            <!-- 中间主体内容 -->
            <div class="mui-content">
                <ul class="mui-table-view" style="margin-top: 10;width: 94%;margin-left: 3%;">
                
                </ul>
            </div>
            <div style="width: 100%; height: 40px; background: #FAFAFA;"></div>
            <div class="mui-but">添加新地址</div>

            <!-- 页尾 -->

        </div>
<script type="text/javascript">
	/* 本页面临时/初始化专用JS */

    $(".mui-but").on("tap", function() {
		
		//plus.webview.currentWebview().loadURL('address_add.html');
		mui.openWindow({
			"url": "/mobile.php?call=runer.address_add&share_id="+{:$share_id:},
			"id": "address_add",
			"styles": {
				"popGesture": "none"
			}
		});
	});

	YueMi.API.invoke('profile', 'address', {
			//__access_token : 'Nyfp9oicVSrrGVp7'
			__access_token : '{:$User->token:}'
		}, function (t, q, r) {
			
			$(".mui-table-view").html("");
			 $.each(r.Addresses,function(i,v){  
					
				var html = '<li class="address_li">'
				html +=             '<div class="mui-table-view-cell">'
				html +=                 '<div class="mui-table-view-ce1">'+ v.name
				html +=                 	 '<span class="mui-table-view-ce2"style="float: right;font-weight: normal;">' + v.tel + '</span>'
				html +=                '</div>'                       
				html +=             '</div>'
				html +=             '<div did="' + v.id + '" dname="'+v.name+'" drig="'+v.rig+'" daddress="'+v.address+'" dtel="'+v.tel+'" class="mui-table-view-cell1">'
				html +=                 '<input readonly="readonly" type="checkbox" class="check1" ' + (v.default === 1 ? 'checked' : '') + ' style="margin-left: 10px;margin-top: 5px;position: absolute;"/>'
				html +=                 '<span style="font-size: 11px;margin-top: -2px;margin-left: 30px;">设为默认</span>'
				html +=                 '<a href="javascript:;" class="mui-icon mui-icon-trash tj sc" style="margin-top:5px;margin-right:15px;color:#333;" sc-id="'+ v.id +'"><n style="font-size: 11px;">删除</n></a>'
				html +=                 '<a style="margin-right: 3px;margin-top:5px;margin-right:15px;color:#333;" href="javascript:;" class="address_edit mui-icon mui-icon-compose" data-id="' + v.id + '"><n style="font-size: 11px;" class="bj" >编辑</n></a>'
				html +=             '</div>'
				html +=				'<div style="width: 100%; height: 10px; background: #FAFAFA;"></div>'
				html +=         '</li>'
                $(".mui-table-view").append(html);
				
				});  
			    $(".address_edit").on("tap", function() {
					var aid=$(this).attr('data-id');
					
					 mui.openWindow({
						 "url": '/mobile.php?call=runer.update_address&share_id='+"{:$share_id:}&"+"id="+aid,
						 "styles": {
							 "popGesture": "none"
						 }
					 });
				 });
				 
			 $(".sc").on("tap", function(){
			    var addid=$(this).attr('sc-id');
				var defaultid = $(this).parent(".mui-table-view-cell1").attr("did");
			    var addressnew = {
					__access_token : '{:$User->token:}',
					//__access_token :'Nyfp9oicVSrrGVp7',
					id:addid
				}
				
				YueMi.API.invoke('profile','address_del',addressnew,function(target, request, response){
					 console.log(JSON.stringify(response));
					 //分析数据
					 mui.toast('删除地址成功！');
					 //location.reload();
					 window.location.href = '/mobile.php?call=runer.address&share_id='+"{:$share_id:}&"+"res_id="+defaultid;
				   },function(target, request, response){
					  
					 //console.log("target:"+JSON.stringify(target));
					 //console.log("request:"+JSON.stringify(request));
					 console.log("response:"+JSON.stringify(response));
					 mui.toast('删除地址失败');
				 })
			 });
			
		}, function (t, q, r) {
			alert(r.__message);
	});
	
	$('.mui-table-view').on("click", "li .mui-table-view-cell1 .check1", function(ev) {
		
		ev.stopPropagation();
		var defaultid = $(this).parent(".mui-table-view-cell1").attr("did");
		
		YueMi.API.invoke('profile', 'address_default', {
			__access_token :'{:$User->token:}',
			id: parseInt(defaultid)
		}, function(target, request, response) {
			mui.toast("设置默认收获地址成功");
			window.location.href = '/mobile.php?call=runer.order_confirm&share_id='+"{:$share_id:}&"+"res_id="+defaultid;
		}, function(target, request, response) {
			console.log(11);
			return;
		});
	});
	 
</script>
 
    </body>

</html>
 

