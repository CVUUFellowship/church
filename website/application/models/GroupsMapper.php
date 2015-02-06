<?php

class Application_Model_GroupsMapper extends Application_Model_CollectionsMapper
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'Groups';
    }

}

