<?php
// application/models/HoodsMapper.php
     
    class Application_Model_HoodFromZipMapper
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
                $this->setDbTable('Application_Model_DbTable_HoodFromZip');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_HoodFromZip $hood)
        {
            $data = array(
                'RecordID'  => $hood->getID(),
                'HoodID'  => $hood->getHoodID(),
                'Low'   => $hood->getLow(),
                'High'   => $hood->getHigh(),
                'Timestamp' => $hood->getTimestamp(),
            );

            if (!isset($data['Timestamp'])) {
                $data['Timestamp'] = date('Y-m-d H:i:s', time());
            }
     
            if (null === ($id = $household->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Low'] = isset($data['Low']) ? $data['Low'] : '';
                $data['High'] = isset($data['High']) ? $data['High'] : '';
                $data['HoodID'] = isset($data['HoodID']) ? $data['HoodID'] : 0;
                $id = $this->getDbTable()->insert($data);
                $connection->setId($id);
            } else {
                $this->getDbTable()->update($data, array('RecordID = ?' => $id));
            }
        }
     
        public function find($id)
        {
            $result = $this->getDbTable()->find($id);
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
            $rowarray = array();
            $rowarray['id'] = $row->RecordID;
            $rowarray['hoodid'] = $row->HoodID;
            $rowarray['low'] = $row->Low;
            $rowarray['high'] = $row->High;
            $rowarray['Timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_HoodFromZip();
                $entry->setId($row->RecordID);
                $entry->setHoodid($row->HoodID);
                $entry->setLow($row->Low);
                $entry->setHigh($row->High);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['hoodid'] = 'HoodID';
            $map['low'] = 'Low';
            $map['high'] = 'High';
            $map['Timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Low"));
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
