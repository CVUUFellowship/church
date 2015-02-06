<?php

class Application_Model_DbTable_Headings extends Zend_Db_Table_Abstract
{

    protected $_name = 'list_headings';
    protected $_primary = array('RecordID', 'Position');
}