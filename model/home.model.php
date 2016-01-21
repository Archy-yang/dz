<?php

class homeModel
{
    private $base = null;

    public function __construct($base)
    {
        $this->base = $base;
    }

    public function getChampionRoles()
    { 
        $collection = $this->base->db->getCollection('webchampionroles');

        $res = $collection->find()->sort(array('name' => 1));
        return array_values(iterator_to_array($res));
    }

    public function getHomePageSummaries()
    {
        $collection = $this->base->db->getCollection('webhomepagesummaries');

        $res = $collection->findOne(
            array(
                'id' => intval(1),
            )
        );

        return $res['data'];
    }
}
