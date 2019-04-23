<?php

/**
 * 后台素材上传接口
 * 1、SPU素材
 * 2、SKU素材
 * 3、邀请模板
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

if ($request->Admin === null) {
	_E('E_AUTH', '没有权限');
}

$schema = $_POST['schema'] ?? '';
switch ($schema) {
	case 'sku':
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
		$request->execute(new UTSku(), function(UploadTask $task, \yuemi_sale\SkuEntity $sku) {
			$mat = new yuemi_sale\SkuMaterialEntity();
			$mat->sku_id = $sku->id;
			$mat->file_url = $task->Uri;
			$mat->file_size = filesize($task->Target);
			$mat->image_height = $task->ImageHeight;
			$mat->image_width = $task->ImageWidth;
			$mat->thumb_height = $task->_get_range_thumb()->height;
			$mat->thumb_width = $task->_get_range_thumb()->width;
			$mat->thumb_url = $task->ThumbUri;
			$mat->thumb_size = filesize($task->ThumbTarget);
			$mat->is_default = 1;
			$mat->status = 1;
			$mat->create_user = $this->User->id;
			$mat->create_from = ip2long($_SERVER['REMOTE_ADDR']);
			$mat->create_time = Z_NOW;
			\yuemi_sale\SkuMaterialFactory::Instance()->insert($mat);
			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'sku',
				'Id' => $mat->id,
				'Url' => URL_RES . '/upload' . $task->ThumbUri
					], JSON_UNESCAPED_UNICODE);
		}, $sku);
		break;
	case 'sku-b':
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
		$request->execute(new UTSkuContent(), function(UploadTask $task, \yuemi_sale\SkuEntity $sku) {
			$mat = new yuemi_sale\SkuMaterialEntity();
			$mat->type = 2;
			$mat->sku_id = $sku->id;
			$mat->file_url = $task->Uri;
			$mat->file_size = filesize($task->Target);
			$mat->image_height = $task->ImageHeight;
			$mat->image_width = $task->ImageWidth;
			$mat->is_default = 1;
			$mat->status = 1;
			$mat->create_user = $this->User->id;
			$mat->create_from = ip2long($_SERVER['REMOTE_ADDR']);
			$mat->create_time = Z_NOW;
			\yuemi_sale\SkuMaterialFactory::Instance()->insert($mat);
			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'sku-b',
				'Id' => $mat->id,
				'Url' => URL_RES . '/upload' . $task->ThumbUri
					], JSON_UNESCAPED_UNICODE);
		}, $sku);
		break;
	case 'sku-p':
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
		$request->execute(new UTSkuContent(), function(UploadTask $task, \yuemi_sale\SkuEntity $sku) {
			$mat = new yuemi_sale\SkuMaterialEntity();
			$mat->sku_id = $sku->id;
			$mat->type = 1;
			$mat->file_url = $task->Uri;
			$mat->file_size = filesize($task->Target);
			$mat->image_height = $task->ImageHeight;
			$mat->image_width = $task->ImageWidth;
			$mat->is_default = 1;
			$mat->status = 1;
			$mat->create_user = $this->User->id;
			$mat->create_from = ip2long($_SERVER['REMOTE_ADDR']);
			$mat->create_time = Z_NOW;
			\yuemi_sale\SkuMaterialFactory::Instance()->insert($mat);
			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'sku-p',
				'Id' => $mat->id,
				'Url' => URL_RES . '/upload' . $task->Uri
					], JSON_UNESCAPED_UNICODE);
		}, $sku);
		break;
	case 'spu':
		$spu_id = $_POST['spu_id'] ?? '';
		if (empty($spu_id) || !is_numeric($spu_id)) {
			_E('E_PARAM', '缺少参数 spu_id');
			return;
		}
		$spu = \yuemi_sale\SpuFactory::Instance()->load($spu_id);
		if ($spu === null) {
			_E('E_PARAM', '参数 spu_id 错误');
			return;
		}
		$request->execute(new UTSku(), function(UploadTask $task, \yuemi_sale\SpuEntity $spu) {
			$mat = new yuemi_sale\SpuMaterialEntity();
			$mat->spu_id = $spu->id;
			$mat->type = 0;
			$mat->file_url = $task->Uri;
			$mat->file_size = filesize($task->Target);
			$mat->image_height = $task->ImageHeight;
			$mat->image_width = $task->ImageWidth;
			$mat->thumb_height = $task->_get_range_thumb()->height;
			$mat->thumb_width = $task->_get_range_thumb()->width;
			$mat->thumb_url = $task->ThumbUri;
			$mat->thumb_size = filesize($task->ThumbTarget);
			$mat->is_default = 0;
			$mat->status = 1;
			$mat->create_user = $this->User->id;
			$mat->create_from = ip2long($_SERVER['REMOTE_ADDR']);
			$mat->create_time = Z_NOW;
			\yuemi_sale\SpuMaterialFactory::Instance()->insert($mat);
			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'spu',
				'Id' => $mat->id,
				'Url' => URL_RES . '/upload' . $task->ThumbUri
					], JSON_UNESCAPED_UNICODE);
		}, $spu);
		break;
	case 'spu-p':
		$spu_id = $_POST['spu_id'] ?? '';
		if (empty($spu_id) || !is_numeric($spu_id)) {
			_E('E_PARAM', '缺少参数 spu_id');
			return;
		}
		$spu = \yuemi_sale\SpuFactory::Instance()->load($spu_id);
		if ($spu === null) {
			_E('E_PARAM', '参数 spu_id 错误');
			return;
		}
		$request->execute(new UTSku(), function(UploadTask $task, \yuemi_sale\SpuEntity $spu) {
			$mat = new yuemi_sale\SpuMaterialEntity();
			$mat->spu_id = $spu->id;
			$mat->type = 1;
			$mat->file_url = $task->Uri;
			$mat->file_size = filesize($task->Target);
			$mat->image_height = $task->ImageHeight;
			$mat->image_width = $task->ImageWidth;
			$mat->is_default = 0;
			$mat->status = 1;
			$mat->create_user = $this->User->id;
			$mat->create_from = ip2long($_SERVER['REMOTE_ADDR']);
			$mat->create_time = Z_NOW;
			\yuemi_sale\SpuMaterialFactory::Instance()->insert($mat);
			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'spu-p',
				'Id' => $mat->id,
				'Url' => URL_RES . '/upload' . $task->Uri
					], JSON_UNESCAPED_UNICODE);
		}, $spu);
		break;
	case 'spu-b':
		$spu_id = $_POST['spu_id'] ?? '';
		if (empty($spu_id) || !is_numeric($spu_id)) {
			_E('E_PARAM', '缺少参数 spu_id');
			return;
		}
		$spu = \yuemi_sale\SpuFactory::Instance()->load($spu_id);
		if ($spu === null) {
			_E('E_PARAM', '参数 spu_id 错误');
			return;
		}
		$request->execute(new UTSku(), function(UploadTask $task, \yuemi_sale\SpuEntity $spu) {
			$mat = new yuemi_sale\SpuMaterialEntity();
			$mat->spu_id = $spu->id;
			$mat->type = 2;
			$mat->file_url = $task->Uri;
			$mat->file_size = filesize($task->Target);
			$mat->image_height = $task->ImageHeight;
			$mat->image_width = $task->ImageWidth;
			$mat->is_default = 0;
			$mat->status = 1;
			$mat->create_user = $this->User->id;
			$mat->create_from = ip2long($_SERVER['REMOTE_ADDR']);
			$mat->create_time = Z_NOW;
			\yuemi_sale\SpuMaterialFactory::Instance()->insert($mat);
			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'spu-b',
				'Id' => $mat->id,
				'Url' => URL_RES . '/upload' . $task->Uri
					], JSON_UNESCAPED_UNICODE);
		}, $spu);
		break;
	case 'invite':
		$template_id = $_POST['template_id'] ?? '';
		if (empty($template_id) || !is_numeric($template_id)) {
			_E('E_PARAM', '缺少参数 template_id');
			return;
		}
		$tpl = \yuemi_main\InviteTemplateFactory::Instance()->load($template_id);
		if ($tpl === null) {
			_E('E_PARAM', '参数 template_id 错误');
			return;
		}
		$request->execute(new UTInviteTemplate(), function(UploadTask $task, \yuemi_main\InviteTemplateEntity $tpl) {
			$tpl->body_width = $task->ImageWidth;
			$tpl->body_height = $task->ImageHeight;
			$tpl->body_path = $task->Uri;
			$tpl->body_url = $task->Uri;
			$tpl->create_time = Z_NOW;
			$tpl->update_time = Z_NOW;
			\yuemi_main\InviteTemplateFactory::Instance()->update($tpl);

			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'invite',
				'Id' => $tpl->id,
				'Url' => URL_RES . '/upload' . $task->ThumbUri
					], JSON_UNESCAPED_UNICODE);
		}, $tpl);
		break;
	case 'share':
		$template_id = $_POST['template_id'] ?? '';
		if (empty($template_id) || !is_numeric($template_id)) {
			_E('E_PARAM', '缺少参数 template_id');
			return;
		}
		$tpl = \yuemi_sale\ShareTemplateFactory::Instance()->load($template_id);
		if ($tpl === null) {
			_E('E_PARAM', '参数 template_id 错误');
			return;
		}
		$request->execute(new UTShareTemplate(), function(UploadTask $task, \yuemi_sale\ShareTemplateEntity $tpl) {
			$tpl->body_width = $task->ImageWidth;
			$tpl->body_height = $task->ImageHeight;
			$tpl->body_path = $task->Uri;
			$tpl->body_url = $task->Uri;
			$tpl->create_time = Z_NOW;
			$tpl->update_time = Z_NOW;
			\yuemi_sale\ShareTemplateFactory::Instance()->update($tpl);

			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'share',
				'Id' => $tpl->id,
				'Url' => URL_RES . '/upload' . $task->ThumbUri
					], JSON_UNESCAPED_UNICODE);
		}, $tpl);
		break;
	case 'cms':
		$column_id = $_POST['column_id'] ?? '0';
		$article_id = $_POST['article_id'] ?? '0';
		if (!is_numeric($column_id)) {
			_E('E_PARAM', '参数 column_id 格式错误');
			return;
		} else {
			$column_id = intval($column_id);
		}
		if (!is_numeric($article_id)) {
			_E('E_PARAM', '参数 article_id 格式错误');
			return;
		} else {
			$article_id = intval($article_id);
		}
		$column = null;
		if ($column_id > 0) {
			$column = \yuemi_main\CmsColumnFactory::Instance()->load($column_id);
			if ($column === null) {
				_E('E_PARAM', '参数 column_id 错误，没找到指定栏目');
				return;
			}
		}
		$article = null;
		if ($article_id > 0) {
			$article = \yuemi_main\CmsArticleFactory::Instance()->load($article_id);
			if ($article === null) {
				_E('E_PARAM', '参数 article_id 错误，没找到指定内容');
				return;
			}
		}
		if ($article === null && $column === null) {
			_E('E_PARAM', '参数 article_id 和 column_id 至少要传一个');
			return;
		}

		$request->execute(new UTCMS(), function(UploadTask $task, array $par) {
			$column = $par[0];
			$article = $par[1];
			$mat = new \yuemi_main\CmsMaterialEntity();
			$mat->column_id = $column === null ? 0 : $column->id;
			$mat->article_id = $article === null ? 0 : $article->id;
			$mat->file_url = $task->Uri;
			$mat->file_size = filesize($task->Target);
			$mat->image_height = $task->ImageHeight;
			$mat->image_width = $task->ImageWidth;
			$mat->thumb_height = $task->_get_range_thumb()->height;
			$mat->thumb_width = $task->_get_range_thumb()->width;
			$mat->thumb_url = $task->ThumbUri;
			$mat->thumb_size = filesize($task->ThumbTarget);
			$mat->is_default = 0;
			$mat->status = 1;
			$mat->create_user = $this->User->id;
			$mat->create_from = ip2long($_SERVER['REMOTE_ADDR']);
			$mat->create_time = Z_NOW;
			\yuemi_main\CmsMaterialFactory::Instance()->insert($mat);

			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'cms',
				'Id' => $mat->id,
				'ColumnId' => $mat->column_id,
				'ArticleId' => $mat->article_id,
				'Url' => URL_RES . '/upload' . $task->ThumbUri
					], JSON_UNESCAPED_UNICODE);
		}, [
			$column, $article
		]);
		break;
	case 'page':
		$page_id = $_POST['page_id'] ?? '';
		if (empty($page_id) || !is_numeric($page_id)) {
			_E('E_PARAM', '缺少参数 page_id');
			return;
		}
		$page = \yuemi_main\RunPageFactory::Instance()->load($page_id);
		if ($page === null) {
			_E('E_PARAM', '参数 page_id 错误');
			return;
		}
		$request->execute(new UTRuner(), function(UploadTask $task, \yuemi_main\RunPageEntity $page) {
			$mat = new \yuemi_main\RunMaterialEntity();
			$mat->page_id = $page->id;
			$mat->file_url = $task->Uri;
			$mat->file_size = filesize($task->Target);
			$mat->image_height = $task->ImageHeight;
			$mat->image_width = $task->ImageWidth;
			$mat->status = 1;
			$mat->create_user = $this->User->id;
			$mat->create_from = ip2long($_SERVER['REMOTE_ADDR']);
			$mat->create_time = Z_NOW;
			$mat->audit_user = $this->User->id;
			$mat->audit_from = ip2long($_SERVER['REMOTE_ADDR']);
			$mat->audit_time = Z_NOW;
			\yuemi_main\RunMaterialFactory::Instance()->insert($mat);

			echo json_encode([
				'__code' => 'OK',
				'__message' => '',
				'Schema' => 'page',
				'Id' => $mat->id,
				'Url' => URL_RES . '/upload' . $task->ThumbUri
					], JSON_UNESCAPED_UNICODE);
		}, $page);
		break;
	default:
		_E('E_PARAM', '缺少参数 schema [sku|sku-p|sku-b|spu|spu-p|spu-b|share|invite|cms|page]');
		break;
}
