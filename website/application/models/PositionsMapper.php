<?php

class Application_Model_PositionsMapper extends Application_Model_CollectionsMapper
{

    protected $_type = 'positions';

    public function __construct()
    {
        parent::__construct();
        $this->table = 'Positions';
    }

}

