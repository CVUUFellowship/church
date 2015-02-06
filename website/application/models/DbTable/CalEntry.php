<?php

class Application_Model_DbTable_CalEntry extends Zend_Db_Table_Abstract
{

    protected $_name = 'webcal_entry';
    protected $_primary = 'cal_id';
    protected $_sequence = false;
}