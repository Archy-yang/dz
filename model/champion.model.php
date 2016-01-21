<?php

class championModel
{
    private $base = null;

    public function __construct($base)
    {
        $this->base = $base;
    }

    public function getChampionRoles($champKey)
    {
        $collection = $this->base->db->getCollection('webchampionroles');

        $res = $collection->findOne(array('key' => $champKey));

        return $res;
    }

    public function getChampionPage($champKey, $role)
    {
        $collection = $this->base->db->getCollection('webchampionpages');
        
        $res = $collection->findOne(array('key' => $champKey, 'role' => $role));

        return $res;
    }

    public function getOverallRoleData($role)
    {
        $collection = $this->base->db->getCollection('weboverallroledatas');
        
        $res = $collection->findOne(array('role' => $role));

        return $res;
    
    }

    public function getOverallStats()
    {
        $collection = $this->base->db->getCollection('weboverallstats');
        
        $res = $collection->findOne();

        return $res;
    
    }
}
