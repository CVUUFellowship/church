<?php

class Application_Model_DbTable_CalEntryRepeats extends Zend_Db_Table_Abstract
{

    protected $_name = 'webcal_entry_repeats';
    protected $_primary = array('cal_id', 'cal_name');


}