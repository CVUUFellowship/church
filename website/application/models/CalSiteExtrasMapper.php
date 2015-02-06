<?php
// application/models/CalSiteExtrasMapper.php
     
    class Application_Model_CalSiteExtrasMapper
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
                $this->setDbTable('Application_Model_DbTable_CalSiteExtras');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalSiteExtras $entry, $new = 'old')
        {
            $data = array(
                'cal_id'  => $entry->getID(),
                'cal_name'   => $entry->getName(),
                'cal_type'   => $entry->getType(),
                'cal_date'   => $entry->getDate(),
                'cal_remind'  => $entry->getRemind(),
                'cal_data'   => $entry->getData(),
            );

            if ($new == 'new') {
                $id = $entry->getID();
//echo "new entry $id"; 
                /* handle non-null requirements */
                $data['cal_type'] = isset($data['cal_type']) ? $data['cal_type'] : 0;
                $data['cal_name'] = isset($data['cal_name']) ? $data['cal_name'] : '';
                $this->getDbTable()->insert($data);

            } else {
                $id = $entry->getID();
//echo "existing entry $id"; 
//var_dump($data);
                $this->getDbTable()->update($data, array('cal_id = ?' => $id));
            }
        }
        
        
            
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_CalSiteExtras();
                $entry->setId($row->cal_id);
                $entry->setName($row->cal_name);
                $entry->setType($row->cal_type);
                $entry->setDate($row->cal_date);
                $entry->setRemind($row->cal_remind);
                $entry->setData($row->cal_data);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'cal_id';
            $map['name'] = 'cal_name';
            $map['type'] = 'cal_type';
            $map['date'] = 'cal_date';
            $map['remind'] = 'cal_remind';
            $map['data'] = 'cal_data';
           
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("cal_id"));
            else
                $sql->order($order);                 
            foreach ($where as $cond) 
            {
                if ($cond[1] == ' IN ')
                    $str = $map[$cond[0]].$cond[1].$cond[2];
                else
                    $str = $map[$cond[0]].$cond[1]."\"".$cond[2]."\"";
                $sql->where("$str");
            }
            return $this->fetchAll($sql);       
        }


    }
?>