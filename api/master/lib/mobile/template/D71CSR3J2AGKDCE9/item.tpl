<!DOCTYPE html>
<html>
    <head>
        <title>商品详情</title>
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
		<script src="{:#URL_RES:}/v1/scripts/item.js"></script>
		<script src="{:#URL_RES:}/v1/scripts/page.js"></script>
        <script type="text/javascript">
            /* 本页面临时/初始化专用JS */
        </script>
    </head>
    <body style="background-color: #FAFAFA;">
        <div class="mui-content ziima">
        	<div id="cartIcon0" class="grounding-img4" style="position: fixed; bottom: 12rem;right: 0.5rem; z-index: 999999;">
		        <div class=""><a href="#"><img src="{:#URL_RES:}/v1/images/mobile/cartIcon0.png" style="width: 100%;"/></a></div>
		    </div>
            <header class="mui-bar mui-bar-nav" style="background: #fff;box-shadow: 0 0px 1px #e1e1e1;">
		        <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left" style="color: #333" onclick="javascript:history.back(-1);"></a>
		        <span style="width:20%;font-size: 18px;color:#000;float: left;text-align: center;display:block;margin-top:10px;margin-left:35%;">商品</span>
		        <!--<span class="item1" style="width:19%;font-size: 18px;color:#999;float: right;text-align: center;display:block;margin-top:10px;margin-right:31%;">素材</span>-->
	        </header>
            <!-- 中间主体内容 -->
            <div class="mui-content">
               <div class="mui-card">
					<!--内容区-->
					<div class="mui-card-content" style="margin: 0;padding: 0;">
						<div id="slider" class="mui-slider" >
							<div id="queryAdlist" class="mui-slider-group mui-slider-loop" style="">
							    {:foreach from=$Images value=v:}	
									<div class="mui-slider-item mui-slider-item-duplicate">
										<a href="javascript:;">
											<img src="{:$v.Thumb:}" style="width:100%;">
										</a>
									</div>
	                           {:/foreach:}
							</div>
						</div>
					</div>
					<!--页脚，放置补充信息或支持的操作-->
					<div  class="mui-table-p2" >
			        	<!--<span>满减</span>-->
			        	<span>包邮</span>
			        	<!--<span>优惠卷爆款</span>-->
						
			        	<p class="mui-money" style="margin-bottom: 0;margin-right:0;margin-top: 4%;margin-left: -1%;">
							{:$Attr.Price.Sale | number.currency:}
			        		<span style="border: none;">{:$Attr.Price.Ref | number.currency:}</span>
							{:if $Big > 0:}
							{:else:}
								<!-- <img src="{:#URL_RES:}/v1/images/mobile/zhuan.png" style="width: 14px;height: 14px;border: none;margin-left: 5%;margin-top: 2%;"/><span style="border: none;padding: 0;margin-left: -4%;">{:$Attr.Rebate.Vip | number.currency:}</span> -->
							{:/if:}
						
					</div>
		          
			       <p style="clear: both;"></p>
				   <div id="ItemTitle" class="mui-card-footer" style="font-size: 14px;"><!--伊芙丽2018春装新款韩版宽松chic圆领纯色原宿风落肩袖卫衣女套头--></div>
				   <ul class="mui-table-view">
					    <li class="mui-table-view-cell kuaid" style="font-size: 11px;">快递： 免运费 
					    	<span>0人购买</span>
					    	<span>北京市</span>
					    </li>
					    <li style="height: 1px;width: 94%;margin-left: 3%;background-color: #e1e1e1;"></li>
					    <li class="returngoods mui-table-view-cell">
					        <a class="mui-navigate-right" style="color: #999999;font-size: 11px;">7天无理由退货(特殊商品除外) · 24小时发货 .交易保障</a>
					    </li> 
				   </ul>
				   
				</div>
				<div style="margin-top: 3%;">
					<ul class="mui-table-view">
						
					    <li class="mui-table-view-cell" style="width:72.9%;height:12.8%;background-image:url(../images/gouwuxq.png);margin: 0 auto;color: #ffffff;text-align: center;"></li>    
				        <li id="ItemContent" class="mui-table-view-cell" style="font-size: 12px;color: #333333; margin-top: -45px;">
				        	<!--<p>aDEAed</p>-->
				        </li>
					</ul>
				</div>
				<div class="mui-card-content" style="margin-top: 10px;">
					{:$Item.Content:}
					<!--填充详情-->
					<!-- <div class="mui-card-header mui-card-media" style="height:350vw;background-image:url(../images/mall/js.png);"></div> -->
				</div>
            </div>
            <div  style="height: 50px;"></div>
            <nav class="mui-bar mui-bar-tab footer">	
			    <a id="BuyButton" class="mui-tab-item" onclick="javascript:check1({:$wechat_userid:});" style="width:28.57142857142857%;background-color: #FB342A;" style="width:100%;">
			        <span class="mui-tab-label item" style="color: white;">
			                             购买
			        </span>
			    </a>
			</nav>
        </div>
        <div  class="box none" style="position:relative; overflow: hidden;">
        <div class="top none"></div>
        <div class="bottom none">
        		
			<div class="like" >
				<div class="fa">
					<i class="fa fa-check-circle" style="font-size:15px;color: red;"></i>
				</div>
				<span style="margin-left: 2%;font-size: 14px;color: #000;">
					包邮<br/>
				</span>

				<span style="    margin-left:8%;font-size: 11px;color: #999;">
					该商品支持全国包邮
				</span>
			</div>

			<div class="like" >
				<div class="fa">
					<i class="fa fa-check-circle" style="font-size:15px;color: red;"></i>
				</div>
				<span style="margin-left: 2%;font-size: 14px;color: #000;">
					7天无理由退货(特殊商品除外)<br/>
				</span>

				<div style="    margin-left: 8%;font-size: 11px;color: #999;">
					该商品支持7天无理由退货，自商品签收之日7天内可申请无理由
				</div>
				<span style="    margin-left: 8%;font-size: 11px;color: #999;">
					退货。
				</span>
			</div>

			<div class="like" >
				<div class="fa">
					<i class="fa fa-check-circle" style="font-size:15px;color: red;"></i>
				</div>
				<span style="margin-left:2%;font-size: 14px;color: #000;">
					24小时发货<br/>
				</span>

				<span style="    margin-left: 8%;font-size: 11px;color: #999;">
					买家承诺24小时内发货
				</span>
			</div>

			<div class="like" >
				<div class="fa">
					<i class="fa fa-check-circle" style="font-size:15px;color: red;"></i>
				</div>
				<span style="margin-left: 2%;font-size: 14px;color: #000;">
					假一赔十<br/>
				</span>

				<span style="    margin-left: 8%;font-size: 11px;color: #999;">
				100%正品保障，若收到的商品是假冒品牌，可获得加倍赔偿
				</span>
			</div>
        </div>
        <input class="mybutton" type="button" value="我知道了" style="background: red;color: #fff;width: 100%;height: 40px;position: fixed;bottom: 0;left: 0;z-index: 999;border: none;"/>
        </div>
    </body>
</html>
<script>
	function check1(wechat_userid)
	{  
		 window.location.href = '/mobile.php?call=D71CSR3J2AGKDCE9.order&share_id={:$share_id:}&{:#Z_NOW:}';
	}
</script>
