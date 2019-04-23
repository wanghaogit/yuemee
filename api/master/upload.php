<?php

/**
 * 用户素材上传接口
 * 1、头像上传
 * 2、实名认证上传
 * 3、分享素材关联到SKU
 */
include "../../_base/config.php";
include "../../_base/upload.php";
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include Z_SITE . '/../../_base/entity/yuemi_main.php';
include Z_SITE . '/../../_base/entity/yuemi_sale.php';

header('HTTP/1.1 200 OK');
header('Content-Type: application/json; charset=utf-8');

$request = new UploadRequest();
$request->init();

$schema = $_POST['schema'] ?? '';
switch ($schema) {
	case 'avatar':
		$request->execute(new UTAvatar(), function($task) {
			$info = \yuemi_main\UserInfoFactory::Instance()->load($this->User->id);
			$info->avatar = $task->Uri;
			\yuemi_main\UserInfoFactory::Instance()->update($info);
			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'avatar',
				'Url' => URL_RES . '/upload' . $task->Uri
					], JSON_UNESCAPED_UNICODE);
			exit;
		});
		break;
	case 'cert-a':
		$cert = \yuemi_main\UserCertFactory::Instance()->load($request->User->id);
		if ($cert !== null && $cert->status == 1) {
			_E('E_STATUS', '实名认证已审核，不允许修改。');
		}
		$request->execute(new UTCert(), function($task) {
			$cert = \yuemi_main\UserCertFactory::Instance()->load($this->User->id);
			if ($cert === null) {
				$cert = new yuemi_main\UserCertEntity();
				$cert->user_id = $this->User->id;
				$cert->card_pic1 = $task->Uri;
				$cert->card_pic2 = '';
				$cert->card_no = '';
				$cert->card_name = '';
				$cert->card_exp = '0000-00-00';
				$cert->create_from = ip2long($_SERVER['REMOTE_ADDR']);
				$cert->audit_time = '0000-00-00';

				if (!\yuemi_main\UserCertFactory::Instance()->insert($cert)) {
					_E('E_DATABASE', '数据库系统错误。');
				}
			} else {
				if ($cert->status == 1) {
					_E('E_STATUS', '实名认证已审核，不允许修改。');
				}
				$cert->card_pic1 = $task->Uri;
				\yuemi_main\UserCertFactory::Instance()->update($cert);
			}
			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'cert-a',
				'Url' => URL_RES . '/upload' . $task->Uri
					], JSON_UNESCAPED_UNICODE);
			exit;
		});
		break;
	case 'cert-b':
		$cert = \yuemi_main\UserCertFactory::Instance()->load($request->User->id);
		if ($cert !== null && $cert->status == 1) {
			_E('E_STATUS', '实名认证已审核，不允许修改。');
		}
		$request->execute(new UTCert(), function($task) {
			$cert = \yuemi_main\UserCertFactory::Instance()->load($this->User->id);
			if ($cert === null) {
				$cert = new yuemi_main\UserCertEntity();
				$cert->user_id = $this->User->id;
				$cert->card_pic2 = $task->Uri;
				$cert->card_pic1 = '';
				$cert->card_no = '';
				$cert->card_name = '';
				$cert->card_exp = '0000-00-00';
				$cert->create_from = ip2long($_SERVER['REMOTE_ADDR']);
				$cert->audit_time = 0;
				if (!\yuemi_main\UserCertFactory::Instance()->insert($cert)) {
					_E('E_DATABASE', '数据库系统错误。');
				}
			} else {
				if ($cert->status == 1) {
					_E('E_STATUS', '实名认证已审核，不允许修改。');
				}
				$cert->card_pic2 = $task->Uri;
				\yuemi_main\UserCertFactory::Instance()->update($cert);
			}
			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'cert-b',
				'Url' => URL_RES . '/upload' . $task->Uri
					], JSON_UNESCAPED_UNICODE);
			exit;
		});
		break;
	case 'share':
		$sku_id = $_POST['sku_id'] ?? '';
		if (empty($sku_id) || !is_numeric($sku_id)) {
			_E('E_PARAM', '缺少参数 sku_id');
			return;
		}
		$sku = \yuemi_sale\SkuFactory::Instance()->load($sku_id);
		if ($sku === null) {
			_E('E_PARAM', '参数 sku_id 错误');
			return;
		}
		$request->execute(new UTShare(), function(UploadTask $task, \yuemi_sale\SkuEntity $sku) {
			$mat = new yuemi_main\UserMaterialEntity();
			$mat->user_id = $this->User->id;
			$mat->sku_id = $sku->id;
			$mat->file_url = $task->Uri;
			$mat->file_size = filesize($task->Target);
			$mat->image_height = $task->ImageHeight;
			$mat->image_width = $task->ImageWidth;
			$mat->thumb_height = $task->_get_range_thumb()->height;
			$mat->thumb_width = $task->_get_range_thumb()->width;
			$mat->thumb_url = $task->ThumbUri;
			$mat->thumb_size = filesize($task->ThumbTarget);
			$mat->status = 1;
			$mat->create_from = ip2long($_SERVER['REMOTE_ADDR']);
			$mat->create_time = Z_NOW;
			\yuemi_main\UserMaterialFactory::Instance()->insert($mat);
			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'cert-b',
				'Id' => $mat->id,
				'Url' => URL_RES . '/upload' . $task->ThumbUri
					], JSON_UNESCAPED_UNICODE);
		}, $sku);
		break;
	default:
		_E('E_PARAM', '缺少参数 schema [avatar|cert-a|cert-b|share]');
		break;
}
