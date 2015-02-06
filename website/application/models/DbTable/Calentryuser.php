<?php

class Application_Model_DbTable_CalEntryUser extends Zend_Db_Table_Abstract
{

    protected $_name = 'webcal_entry_user';
    protected $_primary = 'cal_id';
    protected $_schema = 'local_cal';

}

