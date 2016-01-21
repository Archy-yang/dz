<?php

if(!defined('IN_WP')) {
	exit('Access Denied');
}

class championControl extends baseModel
{
    public function __construct() 
    {
		$this->homeControl();
	}

    public function homeControl() 
    {
		parent::__construct();
		$this->loadModel('champion');
    }

    public function onDefault() 
    {
        if(isset($_GET['role'])) {
            $this->getChampionRole($_GET['champKey'], $_GET['role']);
        } else {
            $this->getChampion($_GET['champKey']);
        }

        $this->tpl->display('champion');
    }

    protected function getChampion($champKey)
    {
        $model = $_ENV['championModel'];

        $champion = $model->getChampionRoles($champKey);
        $champion['role'] = $champion['roles']['0']['role'];
        
        $champPage = $model->getChampionPage($champKey, $champion['role']);
        $generalRole = $model->getOverallRoleData($champion['role']);
        $overallStats = $model->getOverallStats();

        $this->tpl->assign('championData', $champPage);
        $this->tpl->assign('generalRole', $generalRole);
        $this->tpl->assign('champion', $champion);
        $this->tpl->assign('overallStats', $overallStats);
    }

    protected function getChampionRole($champKey, $role)
    {
    }

}
