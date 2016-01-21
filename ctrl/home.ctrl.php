<?php

if(!defined('IN_WP')) {
	exit('Access Denied');
}

class homeControl extends baseModel {

	public function __construct() {
		$this->homeControl();
	}

	public function homeControl() {
		parent::__construct();
		$this->loadModel('home');
	}

    public function onDefault() {
        $model = $_ENV['homeModel'];

        $compData = $model->getChampionRoles();
        $summaries = $model->getHomePageSummaries();

		$this->tpl->assign('summaries', $summaries);
		$this->tpl->assign('data', $compData);
		$this->tpl->display('home');
	}
}

?>
