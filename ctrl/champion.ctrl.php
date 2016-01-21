<?php

if(!defined('IN_WP')) {
	exit('Access Denied');
}

class championControl extends baseModel
{
    protected $roleList = array(
        'Top' => 'TOP',
        'Middle' => 'MIDDLE',
        'Support' => 'DUO_SUPPORT',
        'ADC' => 'DUO_CARRY',
        'Jungle' => 'JUNGLE',
        'top' => 'TOP',
        'middle' => 'MIDDLE',
        'support' => 'DUO_SUPPORT',
        'adc' => 'DUO_CARRY',
        'jungle' => 'JUNGLE',
        'adcsupport' => 'ADCSUPPORT',
        'synergy' => 'SYNERGY'
    );

    protected $roleKey = array(
        'TOP' => 'Top',
        'MIDDLE' => 'Middle',
        'DUO_SUPPORT' => 'Support',
        'DUO_CARRY' => 'ADC',
        'JUNGLE' => 'Jungle',
        'ADCSUPPORT' => 'adcsupport',
        'SYNERGY' => 'synergy'
    );

    public function __construct() 
    {
        $this->homeControl();
    }

    public function homeControl() 
    {
		parent::__construct();
        $this->loadModel('champion');
        $this->tpl->assign('appName', 'championPage');
    }

    public function onDefault() 
    {
        $champKey = $_GET['champKey'];
        $model = $_ENV['championModel'];

        $champion = $model->getChampionRoles($champKey);

        if(isset($_GET['role']) && $_GET['role']) {
            $champRole = $this->roleList[$_GET['role']];
            $champion['role'] = $champRole;
            $champion['roleTitle'] = $this->roleKey[$champRole];

        } else {
            $champion['role'] = $champion['roles']['0']['role'];
        }
        
        $champPage = $model->getChampionPage($champKey, $champion['role']);
        $generalRole = $model->getOverallRoleData($champion['role']);
        $overallStats = $model->getOverallStats();

        $this->tpl->assign('championData', $champPage);
        $this->tpl->assign('generalRole', $generalRole);
        $this->tpl->assign('champion', $champion);
        $this->tpl->assign('overallStats', $overallStats);
        
        $this->tpl->assign('masteryOrder', array('Offense','Defense','Utility'));
        $this->tpl->display('champion');
    }

    protected function getChampion($champKey)
    {
    }

    protected function getChampionRole($champKey, $role)
    {
    }

}
