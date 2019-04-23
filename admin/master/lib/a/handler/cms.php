<?php

include "lib/ApiHandler.php";

/**
 * 咨询管理接口
 */
class cms_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	
	/**
	 * 审核公共
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    examine		int		审核后状态
	 * @request    id			int		咨询ID
	 * @slient
	 */
	public function cms_examine(\Ziima\MVC\REST\Request $request) {
		$CmsArticleEntity = \yuemi_main\CmsArticleFactory::Instance()->load($request->body->id);
		$CmsArticleEntity->status = $request->body->examine;
		$CmsArticleEntity->audit_user = $this->User->id;
		$CmsArticleEntity->audit_time = Z_NOW;
		$CmsArticleEntity->audit_from = $this->Context->Runtime->ticket->ip;
		if (!\yuemi_main\CmsArticleFactory::Instance()->update($CmsArticleEntity)){
			return [
				'__code' => 'Err',
				'__message' => '修改失败'
			];
		}
		return [
			'__code' => 'OK',
			'__message' => '修改成功'
		];
	}
	/**
	 * 修改公告
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    content		string	 内容
	 * @request    scope_id		int     栏目ID
	 * @request    title		string	咨询标题
	 * @request    id			int		咨询ID
	 * @slient
	 */
	public function cms_update(\Ziima\MVC\REST\Request $request) {
		$CmsArticleEntity = \yuemi_main\CmsArticleFactory::Instance()->load($request->body->id);
		$CmsArticleEntity->column_id = (int)$request->body->scope_id;
		$CmsArticleEntity->title = $request->body->title;
		$CmsArticleEntity->content = $request->body->content;
		if (!\yuemi_main\CmsArticleFactory::Instance()->update($CmsArticleEntity)){
			return [
				'__code' => 'Err',
				'__message' => '修改失败'
			];
		}
		return [
			'__code' => 'OK',
			'__message' => '修改成功'
		];
	}
	/**
	 * 发布公告
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    content		string	 内容
	 * @request    scope_id		int     栏目ID
	 * @request    title		string	咨询标题
	 * @slient
	 */
	public function cms_add(\Ziima\MVC\REST\Request $request) {
		$CmsArticleEntity = new yuemi_main\CmsArticleEntity();
		$CmsArticleEntity->column_id = (int)$request->body->scope_id;
		$CmsArticleEntity->title = $request->body->title;
		$CmsArticleEntity->content = $request->body->content;
		$CmsArticleEntity->status = 5;
		$CmsArticleEntity->create_user = $this->User->id;
		$CmsArticleEntity->create_from = $this->Context->Runtime->ticket->ip;
		$CmsArticleEntity->create_time = time();
		$CmsArticleFactory = new \yuemi_main\CmsArticleFactory(MYSQL_WRITER, MYSQL_READER);
		if (!$CmsArticleFactory->insert($CmsArticleEntity)){
			return [
				'__code' => 'Err',
				'__message' => '添加失败'
			];
		}
		return [
			'__code' => 'OK',
			'__message' => '添加成功'
		];
	}
	/**
	 * 添加栏目
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    cms_id		int		父ID
	 * @request    name			string  名称
	 * @slient
	 */
	public function column_add(\Ziima\MVC\REST\Request $request) {
		$this->MySQL->execute("INSERT INTO `yuemi_main`.`cms_column` (`parent_id`,`name`,`alias`) VALUES (%d,'%s','')",
				$request->body->cms_id,
				$this->MySQL->encode($request->body->name)
		);

		return [
			'__code' => 'OK',
			'__message' => '添加成功'
		];
	}
	/**
	 * 系统公告删除
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     char     公告id
	 * @slient
	 */
	public function del(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$sql = "DELETE FROM `yuemi_main`. `cms_article` WHERE id = '" . $id . "'";
		$this->MySQL->execute($sql);

		return [
			'__code' => 'OK',
			'__message' => '删除成功'
		];
	}

	/**
	 * 系统公告分类删除
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     char     公告id
	 * @slient
	 */
	public function catagory_del(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$sql = "DELETE FROM `yuemi_main`. `cms_column` WHERE id = '" . $id . "'";
		$this->MySQL->execute($sql);
		return [
			'__code' => 'OK',
			'__message' => '删除成功'
		];
	}

}
