<?php

class Application_Model_DbTable_CalAccessUser extends Zend_Db_Table_Abstract
{

    protected $_name = 'webcal_access_user';
    protected $_primary = array('cal_id', 'cal_other_user');

}