<?php
// application/models/CalEntryRepeatsMapper.php
     
    class Application_Model_CalEntryRepeatsMapper
    {
        protected $_dbTable;
     
        public function setDbTable($dbTable)
        {
            if (is_string($dbTable)) {
                $dbTable = new $dbTable();
            }
            if (!$dbTable instanceof Zend_Db_Table_Abstract) {
                throw new Exception('Invalid table data gateway provided');
            }
            $this->_dbTable = $dbTable;
            return $this;
        }
     
        public function getDbTable()
        {
            if (null === $this->_dbTable) {
                $this->setDbTable('Application_Model_DbTable_CalEntryRepeats');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalEntryRepeats $entry, $new = 'old')
        {
            $data = array(
                'cal_id'  => $entry->getID(),
                'cal_type'   => $entry->getType(),
                'cal_end'  => $entry->getEnd(),
                'cal_endtime'   => $entry->getEndtime(),
                'cal_frequency'   => $entry->getFrequency(),
                'cal_days'   => $entry->getDays(),
                'cal_bymonth'   => $entry->getBymonth(),
                'cal_bymonthday'   => $entry->getBymonthday(),
                'cal_byday'   => $entry->getByday(),
                'cal_bysetpos'   => $entry->getBysetpos(),
                'cal_byweekno'   => $entry->getByweekno(),
                'cal_byyearday'   => $entry->getByyearday(),
                'cal_wkst'   => $entry->getWkst(),
                'cal_count'   => $entry->getCount(),
            );

            if ($new == 'new') {
                $id = $entry->getID();
echo "a new entry of $id"; 
                /* handle non-null requirements */
                $data['cal_frequency'] = isset($data['cal_frequency']) ? $data['cal_frequency'] : 1;
                $data['cal_wkst'] = isset($data['cal_wkst']) ? $data['cal_wkst'] : 'MO';
                $this->getDbTable()->insert($data);
var_dump($data);

            } else {
                $id = $entry->getID();
echo "existing entry $id"; 
var_dump($data);
                $this->getDbTable()->update($data, array('cal_id = ?' => $id));
            }
        }

    }

?>