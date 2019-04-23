<?php
/**
 * 贡云数据接口
 */
include "../../_base/config.php";
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include Z_SITE . '/../../_base/entity/yuemi_main.php';
include Z_SITE . '/../../_base/entity/yuemi_sale.php';

//Z_ROOT    D:\/Work\/ziima\/framework\/PZiima
function getImg($url = 'http://img.test.jioao.cn/images/uploadImgForm/201711141436468308.jpg',$path='spu')
{
	$arr = explode('.',$url);
	$ext = substr($arr[count($arr)-1],0,3);

	try{
		$ImgStr = file_get_contents($url);
	} catch (Exception $e) {
		return '';
	}
	if (empty($ImgStr))
		return '';

	while ($filename = getRandStr())
	{
		$dir = UPLOAD_ROOT.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.substr($filename,0,2).DIRECTORY_SEPARATOR.substr($filename,2,2).DIRECTORY_SEPARATOR.substr($filename,4,2);
		$dir1 = DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.substr($filename,0,2).DIRECTORY_SEPARATOR.substr($filename,2,2).DIRECTORY_SEPARATOR.substr($filename,4,2);
		if (is_dir($dir))
		{
			if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename.'.'.$ext))
			{
				$path = $dir.DIRECTORY_SEPARATOR.$filename.'.'.$ext;
				break;
			}
		}
		else
		{
			mkdir($dir,0777,true);
			$path = $dir.DIRECTORY_SEPARATOR.$filename.'.'.$ext;
			break;
		}
	}

	file_put_contents($path, $ImgStr);
	return [
		$dir1,
		$filename.'.'.$ext,
		$ext
	];
}

function getRandStr ()
{
	$str = '1234567890abcdefghijklmnopqrstuvwxyz';
	return substr(str_shuffle($str),0,24);
}
header('HTTP/1.1 200 OK');
header('Content-Type: application/json; charset=utf-8');
//echo json_encode([
//	'__code' => 'OK',
//	'__message' => '',
//	'Id' => Z_ROOT,
//	], JSON_UNESCAPED_UNICODE);

		$Json_data = '[{"apiGoodsId":0,"ascription":1,"brandId":616,"brandNameCn":"耐克","brandNameEn":"NIKE","categoryId":732,"categoryName":"运动鞋","categoryParentId":209,"categoryParentName":"女性潮鞋","channel":1,"discount":100,"goodsCode":"OB1441148947880","goodsDetailList":[{"goodId":9036,"id":86820,"imageHeight":316,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141436468308.jpg","imageWidth":750,"orderNo":1},{"goodId":9036,"id":86821,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141437039564.png","imageWidth":750,"orderNo":2},{"goodId":9036,"id":86822,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141437044833.png","imageWidth":750,"orderNo":3},{"goodId":9036,"id":86823,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141437044229.png","imageWidth":750,"orderNo":4},{"goodId":9036,"id":86824,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141437096716.png","imageWidth":750,"orderNo":5},{"goodId":9036,"id":86825,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141437159344.png","imageWidth":750,"orderNo":6},{"goodId":9036,"id":86826,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141437166411.png","imageWidth":750,"orderNo":7},{"goodId":9036,"id":86827,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141437197924.png","imageWidth":750,"orderNo":8}],"goodsNavigateList":[{"goodId":9036,"id":33904,"navigateImage":"http://img.test.jioao.cn/images/uploadImgForm/201711141435479289.png","orderNo":1},{"goodId":9036,"id":33905,"navigateImage":"http://img.test.jioao.cn/images/uploadImgForm/201711141436015332.png","orderNo":2},{"goodId":9036,"id":33906,"navigateImage":"http://img.test.jioao.cn/images/uploadImgForm/201711141436065739.png","orderNo":3},{"goodId":9036,"id":33907,"navigateImage":"http://img.test.jioao.cn/images/uploadImgForm/201711141436126482.png","orderNo":4},{"goodId":9036,"id":33908,"navigateImage":"http://img.test.jioao.cn/images/uploadImgForm/201711141436168315.png","orderNo":5}],"id":9036,"labelName":"","lowestPrice":0,"name":"耐克Nike女鞋 运动休闲气垫跑步鞋 724981-007","offlineTime":"2018-12-31 23:55:00.0","onlineTime":"2017-11-14 08:00:00.0","operateCosting":0,"products":[{"barCode":"","goodsAppRecordId":57396,"goodsId":9036,"hasTax":0,"id":17246,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141435264848.png","inventory":{"apiInventoryId":0,"billType":0,"canUseMembershipCard":0,"cooperatePrice":32861,"disabledCooperate":0,"goodsId":9036,"hasTax":0,"id":35592,"inventoryNum":5,"isBill":0,"isCooperate":1,"isTransfer":0,"lockInventoryNum":0,"productId":17246,"salePrice":38300,"settlementPrice":0,"status":1,"storeId":90,"taxCooperatePrice":32861},"isDel":0,"isStockWarn":1,"productBn":"724981-007","specDesc":"[{\"specificationDetailId\":4330,\"detailName\":\"724981-007\"},{\"specificationDetailId\":4296,\"detailName\":\"36\"},{\"specificationDetailId\":4297,\"detailName\":\"36.5\"},{\"specificationDetailId\":4298,\"detailName\":\"37.5\"},{\"specificationDetailId\":4299,\"detailName\":\"38\"}]","specInfo":"4330,4296","specName":"724981-007 36","status":1},{"barCode":"","goodsAppRecordId":57397,"goodsId":9036,"hasTax":0,"id":17247,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141435264848.png","inventory":{"apiInventoryId":0,"billType":0,"canUseMembershipCard":0,"cooperatePrice":32861,"disabledCooperate":0,"goodsId":9036,"hasTax":0,"id":35593,"inventoryNum":4,"isBill":0,"isCooperate":1,"isTransfer":0,"lockInventoryNum":0,"productId":17247,"salePrice":38300,"settlementPrice":0,"status":1,"storeId":90,"taxCooperatePrice":32861},"isDel":0,"isStockWarn":1,"productBn":"724981-007","specDesc":"[{\"specificationDetailId\":4330,\"detailName\":\"724981-007\"},{\"specificationDetailId\":4296,\"detailName\":\"36\"},{\"specificationDetailId\":4297,\"detailName\":\"36.5\"},{\"specificationDetailId\":4298,\"detailName\":\"37.5\"},{\"specificationDetailId\":4299,\"detailName\":\"38\"}]","specInfo":"4330,4297","specName":"724981-007 36.5","status":1},{"barCode":"","goodsAppRecordId":57398,"goodsId":9036,"hasTax":0,"id":17248,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141435264848.png","inventory":{"apiInventoryId":0,"billType":0,"canUseMembershipCard":0,"cooperatePrice":32861,"disabledCooperate":0,"goodsId":9036,"hasTax":0,"id":35594,"inventoryNum":1,"isBill":0,"isCooperate":1,"isTransfer":0,"lockInventoryNum":0,"productId":17248,"salePrice":38300,"settlementPrice":0,"status":1,"storeId":90,"taxCooperatePrice":32861},"isDel":0,"isStockWarn":1,"productBn":"724981-007","specDesc":"[{\"specificationDetailId\":4330,\"detailName\":\"724981-007\"},{\"specificationDetailId\":4296,\"detailName\":\"36\"},{\"specificationDetailId\":4297,\"detailName\":\"36.5\"},{\"specificationDetailId\":4298,\"detailName\":\"37.5\"},{\"specificationDetailId\":4299,\"detailName\":\"38\"}]","specInfo":"4330,4298","specName":"724981-007 37.5","status":1},{"barCode":"","goodsAppRecordId":57399,"goodsId":9036,"hasTax":0,"id":17249,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711141435264848.png","inventory":{"apiInventoryId":0,"billType":0,"canUseMembershipCard":0,"cooperatePrice":32861,"disabledCooperate":0,"goodsId":9036,"hasTax":0,"id":35595,"inventoryNum":1,"isBill":0,"isCooperate":1,"isTransfer":0,"lockInventoryNum":0,"productId":17249,"salePrice":38300,"settlementPrice":0,"status":1,"storeId":90,"taxCooperatePrice":32861},"isDel":0,"isStockWarn":1,"productBn":"724981-007","specDesc":"[{\"specificationDetailId\":4330,\"detailName\":\"724981-007\"},{\"specificationDetailId\":4296,\"detailName\":\"36\"},{\"specificationDetailId\":4297,\"detailName\":\"36.5\"},{\"specificationDetailId\":4298,\"detailName\":\"37.5\"},{\"specificationDetailId\":4299,\"detailName\":\"38\"}]","specInfo":"4330,4299","specName":"724981-007 38","status":1}],"restrictLimit":0,"saleCount":0,"salePrice":0,"scoreRate":0,"selfSupport":0,"sendUpdateGoods":"0","sendUpdateGoodsDetail":"0","sendUpdateSalePrice":"0","serviceDesc":"","specDesc":"[{\"name\":\"货号\",\"specificationId\":788,\"value\":[{\"specificationDetailId\":4330,\"detailName\":\"724981-007\",\"specificationDetailImage\":\"images/uploadImgForm/201711141435264848.png\"}]},{\"name\":\"尺码\",\"specificationId\":789,\"value\":[{\"specificationDetailId\":4296,\"detailName\":\"36\",\"specificationDetailImage\":\"\"},{\"specificationDetailId\":4297,\"detailName\":\"36.5\",\"specificationDetailImage\":\"\"},{\"specificationDetailId\":4298,\"detailName\":\"37.5\",\"specificationDetailImage\":\"\"},{\"specificationDetailId\":4299,\"detailName\":\"38\",\"specificationDetailImage\":\"\"}]}]","suggestedPrice":89900,"talentDisplay":0,"talentLimit":0,"threshold":0,"unit":"","wxSmallImgpath":""},{"apiGoodsId":0,"ascription":1,"brandId":616,"brandNameCn":"耐克","brandNameEn":"NIKE","categoryId":731,"categoryName":"运动鞋","categoryParentId":218,"categoryParentName":"男士鞋履","channel":1,"discount":100,"goodsCode":"PB0393063450985","goodsDetailList":[{"goodId":8318,"id":76571,"imageHeight":313,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031513136435.jpg","imageWidth":750,"orderNo":1},{"goodId":8318,"id":76572,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031513196051.jpg","imageWidth":750,"orderNo":2},{"goodId":8318,"id":76573,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031513191202.jpg","imageWidth":750,"orderNo":3},{"goodId":8318,"id":76574,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031513207961.jpg","imageWidth":750,"orderNo":4},{"goodId":8318,"id":76575,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031513207407.jpg","imageWidth":750,"orderNo":5},{"goodId":8318,"id":76576,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031513203076.jpg","imageWidth":750,"orderNo":6},{"goodId":8318,"id":76577,"imageHeight":750,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031513202891.jpg","imageWidth":750,"orderNo":7}],"goodsNavigateList":[{"goodId":8318,"id":29949,"navigateImage":"http://img.test.jioao.cn/images/uploadImgForm/201711031512469563.jpg","orderNo":1},{"goodId":8318,"id":29950,"navigateImage":"http://img.test.jioao.cn/images/uploadImgForm/201711031512491062.jpg","orderNo":2},{"goodId":8318,"id":29951,"navigateImage":"http://img.test.jioao.cn/images/uploadImgForm/201711031512551455.jpg","orderNo":3},{"goodId":8318,"id":29952,"navigateImage":"http://img.test.jioao.cn/images/uploadImgForm/201711031513007057.jpg","orderNo":4},{"goodId":8318,"id":29953,"navigateImage":"http://img.test.jioao.cn/images/uploadImgForm/201711031513064721.jpg","orderNo":5}],"id":8318,"labelName":"","lowestPrice":0,"name":"耐克Nike男鞋 新款Jordan Horizon Low PRM乔丹实战篮球鞋 850678-401","offlineTime":"2018-12-31 23:55:00.0","onlineTime":"2017-11-03 08:00:00.0","operateCosting":0,"products":[{"barCode":"","goodsAppRecordId":57400,"goodsId":8318,"hasTax":0,"id":15921,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031512391072.jpg","inventory":{"apiInventoryId":0,"billType":0,"canUseMembershipCard":0,"cooperatePrice":77675,"disabledCooperate":0,"goodsId":8318,"hasTax":0,"id":32714,"inventoryNum":1,"isBill":0,"isCooperate":1,"isTransfer":0,"lockInventoryNum":0,"productId":15921,"salePrice":90500,"settlementPrice":0,"status":1,"storeId":90,"taxCooperatePrice":77675},"isDel":0,"isStockWarn":1,"productBn":"850678-401","specDesc":"[{\"specificationDetailId\":7264,\"detailName\":\"850678-401\"},{\"specificationDetailId\":4303,\"detailName\":\"40.5\"},{\"specificationDetailId\":4305,\"detailName\":\"42\"},{\"specificationDetailId\":4306,\"detailName\":\"42.5\"},{\"specificationDetailId\":4307,\"detailName\":\"43\"},{\"specificationDetailId\":4308,\"detailName\":\"44\"},{\"specificationDetailId\":4309,\"detailName\":\"44.5\"},{\"specificationDetailId\":4310,\"detailName\":\"45\"}]","specInfo":"7264,4303","specName":"850678-401 40.5","status":1},{"barCode":"","goodsAppRecordId":57401,"goodsId":8318,"hasTax":0,"id":15922,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031512391072.jpg","inventory":{"apiInventoryId":0,"billType":0,"canUseMembershipCard":0,"cooperatePrice":77675,"disabledCooperate":0,"goodsId":8318,"hasTax":0,"id":32713,"inventoryNum":5,"isBill":0,"isCooperate":1,"isTransfer":0,"lockInventoryNum":0,"productId":15922,"salePrice":90500,"settlementPrice":0,"status":1,"storeId":90,"taxCooperatePrice":77675},"isDel":0,"isStockWarn":1,"productBn":"850678-401","specDesc":"[{\"specificationDetailId\":7264,\"detailName\":\"850678-401\"},{\"specificationDetailId\":4303,\"detailName\":\"40.5\"},{\"specificationDetailId\":4305,\"detailName\":\"42\"},{\"specificationDetailId\":4306,\"detailName\":\"42.5\"},{\"specificationDetailId\":4307,\"detailName\":\"43\"},{\"specificationDetailId\":4308,\"detailName\":\"44\"},{\"specificationDetailId\":4309,\"detailName\":\"44.5\"},{\"specificationDetailId\":4310,\"detailName\":\"45\"}]","specInfo":"7264,4305","specName":"850678-401 42","status":1},{"barCode":"","goodsAppRecordId":57402,"goodsId":8318,"hasTax":0,"id":15923,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031512391072.jpg","inventory":{"apiInventoryId":0,"billType":0,"canUseMembershipCard":0,"cooperatePrice":77675,"disabledCooperate":0,"goodsId":8318,"hasTax":0,"id":32712,"inventoryNum":6,"isBill":0,"isCooperate":1,"isTransfer":0,"lockInventoryNum":0,"productId":15923,"salePrice":90500,"settlementPrice":0,"status":1,"storeId":90,"taxCooperatePrice":77675},"isDel":0,"isStockWarn":1,"productBn":"850678-401","specDesc":"[{\"specificationDetailId\":7264,\"detailName\":\"850678-401\"},{\"specificationDetailId\":4303,\"detailName\":\"40.5\"},{\"specificationDetailId\":4305,\"detailName\":\"42\"},{\"specificationDetailId\":4306,\"detailName\":\"42.5\"},{\"specificationDetailId\":4307,\"detailName\":\"43\"},{\"specificationDetailId\":4308,\"detailName\":\"44\"},{\"specificationDetailId\":4309,\"detailName\":\"44.5\"},{\"specificationDetailId\":4310,\"detailName\":\"45\"}]","specInfo":"7264,4306","specName":"850678-401 42.5","status":1},{"barCode":"","goodsAppRecordId":57403,"goodsId":8318,"hasTax":0,"id":15924,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031512391072.jpg","inventory":{"apiInventoryId":0,"billType":0,"canUseMembershipCard":0,"cooperatePrice":77675,"disabledCooperate":0,"goodsId":8318,"hasTax":0,"id":32711,"inventoryNum":20,"isBill":0,"isCooperate":1,"isTransfer":0,"lockInventoryNum":0,"productId":15924,"salePrice":90500,"settlementPrice":0,"status":1,"storeId":90,"taxCooperatePrice":77675},"isDel":0,"isStockWarn":1,"productBn":"850678-401","specDesc":"[{\"specificationDetailId\":7264,\"detailName\":\"850678-401\"},{\"specificationDetailId\":4303,\"detailName\":\"40.5\"},{\"specificationDetailId\":4305,\"detailName\":\"42\"},{\"specificationDetailId\":4306,\"detailName\":\"42.5\"},{\"specificationDetailId\":4307,\"detailName\":\"43\"},{\"specificationDetailId\":4308,\"detailName\":\"44\"},{\"specificationDetailId\":4309,\"detailName\":\"44.5\"},{\"specificationDetailId\":4310,\"detailName\":\"45\"}]","specInfo":"7264,4307","specName":"850678-401 43","status":1},{"barCode":"","goodsAppRecordId":57404,"goodsId":8318,"hasTax":0,"id":15925,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031512391072.jpg","inventory":{"apiInventoryId":0,"billType":0,"canUseMembershipCard":0,"cooperatePrice":77675,"disabledCooperate":0,"goodsId":8318,"hasTax":0,"id":32710,"inventoryNum":13,"isBill":0,"isCooperate":1,"isTransfer":0,"lockInventoryNum":0,"productId":15925,"salePrice":90500,"settlementPrice":0,"status":1,"storeId":90,"taxCooperatePrice":77675},"isDel":0,"isStockWarn":1,"productBn":"850678-401","specDesc":"[{\"specificationDetailId\":7264,\"detailName\":\"850678-401\"},{\"specificationDetailId\":4303,\"detailName\":\"40.5\"},{\"specificationDetailId\":4305,\"detailName\":\"42\"},{\"specificationDetailId\":4306,\"detailName\":\"42.5\"},{\"specificationDetailId\":4307,\"detailName\":\"43\"},{\"specificationDetailId\":4308,\"detailName\":\"44\"},{\"specificationDetailId\":4309,\"detailName\":\"44.5\"},{\"specificationDetailId\":4310,\"detailName\":\"45\"}]","specInfo":"7264,4308","specName":"850678-401 44","status":1},{"barCode":"","goodsAppRecordId":57405,"goodsId":8318,"hasTax":0,"id":15926,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031512391072.jpg","inventory":{"apiInventoryId":0,"billType":0,"canUseMembershipCard":0,"cooperatePrice":77675,"disabledCooperate":0,"goodsId":8318,"hasTax":0,"id":32709,"inventoryNum":6,"isBill":0,"isCooperate":1,"isTransfer":0,"lockInventoryNum":0,"productId":15926,"salePrice":90500,"settlementPrice":0,"status":1,"storeId":90,"taxCooperatePrice":77675},"isDel":0,"isStockWarn":1,"productBn":"850678-401","specDesc":"[{\"specificationDetailId\":7264,\"detailName\":\"850678-401\"},{\"specificationDetailId\":4303,\"detailName\":\"40.5\"},{\"specificationDetailId\":4305,\"detailName\":\"42\"},{\"specificationDetailId\":4306,\"detailName\":\"42.5\"},{\"specificationDetailId\":4307,\"detailName\":\"43\"},{\"specificationDetailId\":4308,\"detailName\":\"44\"},{\"specificationDetailId\":4309,\"detailName\":\"44.5\"},{\"specificationDetailId\":4310,\"detailName\":\"45\"}]","specInfo":"7264,4309","specName":"850678-401 44.5","status":1},{"barCode":"","goodsAppRecordId":57406,"goodsId":8318,"hasTax":0,"id":15927,"imagePath":"http://img.test.jioao.cn/images/uploadImgForm/201711031512391072.jpg","inventory":{"apiInventoryId":0,"billType":0,"canUseMembershipCard":0,"cooperatePrice":77675,"disabledCooperate":0,"goodsId":8318,"hasTax":0,"id":32708,"inventoryNum":1,"isBill":0,"isCooperate":1,"isTransfer":0,"lockInventoryNum":0,"productId":15927,"salePrice":90500,"settlementPrice":0,"status":1,"storeId":90,"taxCooperatePrice":77675},"isDel":0,"isStockWarn":1,"productBn":"850678-401","specDesc":"[{\"specificationDetailId\":7264,\"detailName\":\"850678-401\"},{\"specificationDetailId\":4303,\"detailName\":\"40.5\"},{\"specificationDetailId\":4305,\"detailName\":\"42\"},{\"specificationDetailId\":4306,\"detailName\":\"42.5\"},{\"specificationDetailId\":4307,\"detailName\":\"43\"},{\"specificationDetailId\":4308,\"detailName\":\"44\"},{\"specificationDetailId\":4309,\"detailName\":\"44.5\"},{\"specificationDetailId\":4310,\"detailName\":\"45\"}]","specInfo":"7264,4310","specName":"850678-401 45","status":1}],"restrictLimit":0,"saleCount":0,"salePrice":0,"scoreRate":0,"selfSupport":0,"sendUpdateGoods":"0","sendUpdateGoodsDetail":"0","sendUpdateSalePrice":"0","serviceDesc":"","specDesc":"[{\"name\":\"货号\",\"specificationId\":788,\"value\":[{\"specificationDetailId\":7264,\"detailName\":\"850678-401\",\"specificationDetailImage\":\"images/uploadImgForm/201711031512391072.jpg\"}]},{\"name\":\"尺码\",\"specificationId\":789,\"value\":[{\"specificationDetailId\":4303,\"detailName\":\"40.5\",\"specificationDetailImage\":\"\"},{\"specificationDetailId\":4305,\"detailName\":\"42\",\"specificationDetailImage\":\"\"},{\"specificationDetailId\":4306,\"detailName\":\"42.5\",\"specificationDetailImage\":\"\"},{\"specificationDetailId\":4307,\"detailName\":\"43\",\"specificationDetailImage\":\"\"},{\"specificationDetailId\":4308,\"detailName\":\"44\",\"specificationDetailImage\":\"\"},{\"specificationDetailId\":4309,\"detailName\":\"44.5\",\"specificationDetailImage\":\"\"},{\"specificationDetailId\":4310,\"detailName\":\"45\",\"specificationDetailImage\":\"\"}]}]","suggestedPrice":139900,"talentDisplay":0,"talentLimit":0,"threshold":0,"unit":"","wxSmallImgpath":""}]';
		$arr = json_decode($Json_data,true);
		
//		$BrandFactory = new \yuemi_sale\BrandFactory(MYSQL_WRITER, MYSQL_READER);
//		$CatagoryFactory = new \yuemi_sale\CatagoryFactory(MYSQL_WRITER, MYSQL_READER);
//		$SpuFactory = new \yuemi_sale\SpuFactory(MYSQL_WRITER, MYSQL_READER);
//		$SpuMaterialFactory = new \yuemi_sale\SpuMaterialFactory(MYSQL_WRITER, MYSQL_READER);
//		$SkuFactory = new \yuemi_sale\SkuFactory(MYSQL_WRITER, MYSQL_READER);
//		$SkuMaterialFactory = new \yuemi_sale\SkuMaterialFactory(MYSQL_WRITER, MYSQL_READER);
		
		$BrandFactory = \yuemi_sale\BrandFactory::Instance();
		$CatagoryFactory = \yuemi_sale\CatagoryFactory::Instance();
		$SpuFactory = \yuemi_sale\SpuFactory::Instance();
		$SpuMaterialFactory = \yuemi_sale\SpuMaterialFactory::Instance();
		$SkuFactory = \yuemi_sale\SkuFactory::Instance();
		$SkuMaterialFactory = \yuemi_sale\SkuMaterialFactory::Instance();
		
		$MySql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
		//取得供应商ID
		$sql = 'select * from `yuemi_sale`.`supplier` where `name`="内购"';
		$res = $MySql->row($sql);
		if (empty($res))
		{
			$supplier_id = 0;
		}
		else
		{
			$supplier_id = $res['id'];
		}
		foreach ($arr as $v)
		{
			//取得商品品牌ID
			$sql = 'select * from `yuemi_sale`.`brand` where `name`="'.trim($v['brandNameCn']).'"';
			$res = $MySql->row($sql);
			if (empty($res))
			{
				$BrandEntity  = new \yuemi_sale\BrandEntity();
				$BrandEntity->supplier_id = 0;
				$BrandEntity->name = trim($v['brandNameCn']);
				if (!$BrandFactory->insert($BrandEntity))
				{
					error_log('插入数据库失败'."\r\n",3,'d:\a.txt');
					die();
				}
				$brand_id = $BrandEntity->id;
			}
			else
			{
				$brand_id = $res['id'];
			}
			
			//取得商品分类ID
			$sql = 'select * from `yuemi_sale`.`catagory` where `name`="'.trim($v['categoryParentName']).'"';
			$res = $MySql->row($sql);
			if (empty($res))
			{
				$CatagoryEntity = new \yuemi_sale\CatagoryEntity();
				$CatagoryEntity->parent_id = 0;
				$CatagoryEntity->name = trim($v['categoryParentName']);
				$CatagoryEntity->manager_id = 0;
				$CatagoryEntity->supplier_id = 0;
				$CatagoryEntity->is_hidden = 0;
				$CatagoryEntity->is_internal = 0;
				$CatagoryEntity->is_private = 0;
				
				if (!$CatagoryFactory->insert($CatagoryEntity))
				{
					error_log('插入数据库失败'."\r\n",3,'d:\a.txt');
					die();
				}
				$pid = $CatagoryEntity->id;
				$CatagoryEntity = new \yuemi_sale\CatagoryEntity();
				$CatagoryEntity->parent_id = $pid;
				$CatagoryEntity->name = trim($v['categoryName']);
				$CatagoryEntity->manager_id = 0;
				$CatagoryEntity->supplier_id = 0;
				$CatagoryEntity->is_hidden = 0;
				$CatagoryEntity->is_internal = 0;
				$CatagoryEntity->is_private = 0;
				
				if (!$CatagoryFactory->insert($CatagoryEntity))
				{
					error_log('插入数据库失败'."\r\n",3,'d:\a.txt');
					die();
				}
				
				$catagory_id = $CatagoryEntity->id;
			}
			else 
			{
				$pid = $res['id'];
				$sql = 'select * from `yuemi_sale`.`catagory` where `parent_id`='.$pid.' and `name`="'.trim($v['categoryName']).'"';
				$res = $MySql->row($sql);
				if (empty($res))
				{
					$CatagoryEntity = new \yuemi_sale\CatagoryEntity();
					$CatagoryEntity->parent_id = $pid;
					$CatagoryEntity->name = trim($v['categoryName']);
					$CatagoryEntity->manager_id = 0;
					$CatagoryEntity->supplier_id = 0;
					$CatagoryEntity->is_hidden = 0;
					$CatagoryEntity->is_internal = 0;
					$CatagoryEntity->is_private = 0;

					if (!$CatagoryFactory->insert($CatagoryEntity))
					{
						error_log('插入数据库失败'."\r\n",3,'d:\a.txt');
						die();
					}

					$catagory_id = $CatagoryEntity->id;
				}
				else
				{
					$catagory_id = $res['id'];
				}
			}
			
			//插入spu表
			$SpuEntity = new \yuemi_sale\SpuEntity();
			$SpuEntity->catagory_id = $catagory_id;
			$SpuEntity->brand_id = $brand_id;
			$SpuEntity->supplier_id = $supplier_id;
			$SpuEntity->title = $v['name'];
			$SpuEntity->serial = $v['goodsCode'];
			$SpuEntity->price_base = $v['salePrice'];
			$SpuEntity->online_time = $v['onlineTime'];
			$SpuEntity->offline_time = $v['offlineTime'];
			if (!$SpuFactory->insert($SpuEntity))
			{
				error_log('插入数据库失败'."\r\n",3,'d:\a.txt');
				die();
			}
			$spu_id = $SpuEntity->id;
			
			//存储SPU商品详情图片
			foreach ($v['goodsDetailList'] as $v1)
			{
				$file_name = getImg($v1['imagePath'],'spu');
				
				if (empty($file_name)) continue;
				
				switch (strtolower($file_name[2]))
				{
					case 'jpg':
						$file_fmt = 0;
						break;
					case 'png':
						$file_fmt = 1;
						break;
					default :
						$file_fmt = 10;
				}
				$SpuMaterialEntity = new \yuemi_sale\SpuMaterialEntity();
				$SpuMaterialEntity->spu_id = $spu_id;
				$SpuMaterialEntity->file_fmt = $file_fmt;
				$SpuMaterialEntity->file_name = $file_name[1];
				$SpuMaterialEntity->file_path = $file_name[0];
				$SpuMaterialEntity->status = 0;
				if (!$SpuMaterialFactory->insert($SpuMaterialEntity))
				{
					error_log('插入数据库失败'."\r\n",3,'d:\a.txt');
					die();
				}
			}
			
			//存储SPU商品详情图片
			foreach ($v['goodsNavigateList'] as $v1)
			{
				$file_name = getImg($v1['navigateImage'],'spu');
				
				if (empty($file_name)) continue;
				
				switch (strtolower($file_name[2]))
				{
					case 'jpg':
						$file_fmt = 0;
						break;
					case 'png':
						$file_fmt = 1;
						break;
					default :
						$file_fmt = 10;
				}
				$SpuMaterialEntity = new \yuemi_sale\SpuMaterialEntity();
				$SpuMaterialEntity->spu_id = $spu_id;
				$SpuMaterialEntity->file_fmt = $file_fmt;
				$SpuMaterialEntity->file_name = $file_name[1];
				$SpuMaterialEntity->file_path = $file_name[0];
				$SpuMaterialEntity->status = 0;
				if (!$SpuMaterialFactory->insert($SpuMaterialEntity))
				{
					error_log('插入数据库失败'."\r\n",3,'d:\a.txt');
					die();
				}
			}
			
			//插入SKU表
			foreach ($v['products'] as $v1)
			{
				$SkuEntity = new \yuemi_sale\SkuEntity();
				$SkuEntity->spu_id = $spu_id;
				$SkuEntity->catagory_id = $catagory_id;
				$SkuEntity->supplier_id = $supplier_id;
				$SkuEntity->title = $v['name'];
				$SkuEntity->barcode = $v1['barCode'];
				$SkuEntity->serial = $v1['productBn'];
				
				if (!$SkuFactory->insert($SkuEntity))
				{
					error_log('插入数据库失败'."\r\n",3,'d:\a.txt');
					die();
				}
				
				$sku_id = $SkuEntity->id;
				
				//读取和存储SKU规格图
				if (!empty(trim($v1['imagePath'])))
				{
					$file_name = getImg(trim($v1['imagePath']),'sku');
					if (!empty($file_name))
					{
						switch (strtolower($file_name[2]))
						{
							case 'jpg':
								$file_fmt = 0;
								break;
							case 'png':
								$file_fmt = 1;
								break;
							default :
								$file_fmt = 10;
						}
						$SkuMaterialEntity = new \yuemi_sale\SkuMaterialEntity();
						$SkuMaterialEntity->sku_id = $sku_id;
						$SkuMaterialEntity->file_fmt = $file_fmt;
						$SkuMaterialEntity->file_name = $file_name[1];
						$SkuMaterialEntity->file_path = $file_name[0];
						$SkuMaterialEntity->status = 0;
						
						if (!$SkuMaterialFactory->insert($SkuMaterialEntity))
						{
							error_log('插入数据库失败'."\r\n",3,'d:\a.txt');
							die();
						}
					}
				}
			}
		}
echo json_encode([
	'__code' => 'OK',
	'__message' => ''
	], JSON_UNESCAPED_UNICODE);
