<?php
// application/models/NeighborhoodsMapper.php
     
    class Application_Model_NeighborhoodsMapper
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
                $this->setDbTable('Application_Model_DbTable_Neighborhoods');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Neighborhoods $neighborhood)
        {
            $data = array(
                'RecordID'  => $neighborhood->getID(),
                'Active'  => $neighborhood->getActive(),
                'NoCall'   => $neighborhood->getNocall(),
                'HouseholdID'   => $neighborhood->getHouseholdid(),
                'HoodID'   => $neighborhood->getHoodid(),
                'Comments'   => $neighborhood->getComments(),
                'TimeStamp' => $neighborhood->getTimestamp(),
            );
     
            if (!isset($data['TimeStamp'])) {
                $data['TimeStamp'] = date('Y-m-d H:i:s', time());
            }
     
            if (null === ($id = $neighborhood->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['NoCall'] = isset($data['NoCall']) ? $data['NoCall'] : 'no';
                $data['Active'] = isset($data['Active']) ? $data['Active'] : 'yes';
                $data['HouseholdID'] = isset($data['HouseholdID']) ? $data['HouseholdID'] : 0;
                $data['HoodID'] = isset($data['HoodID']) ? $data['HoodID'] : 0;
                $data['Comments'] = isset($data['Comments']) ? $data['Comments'] : '';
                $id = $this->getDbTable()->insert($data);
                $neighborhood->setId($id);
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
            $rowarray['active'] = $row->Active;
            $rowarray['nocall'] = $row->NoCall;
            $rowarray['householdid'] = $row->HouseholdID;
            $rowarray['hoodid'] = $row->HoodID;
            $rowarray['comments'] = $row->Comments;
            $rowarray['timestamp'] = $row->TimeStamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Neighborhoods();
                $entry->setId($row->RecordID);
                $entry->setActive($row->Active);
                $entry->setNocall($row->NoCall);
                $entry->setHouseholdid($row->HouseholdID);
                $entry->setHoodid($row->HoodID);
                $entry->setComments($row->Comments);
                $entry->setTimestamp($row->TimeStamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['active'] = 'Active';
            $map['nocall'] = 'NoCall';
            $map['householdid'] = 'HouseholdID';
            $map['hoodid'] = 'HoodID';
            $map['comments'] = 'Comments';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("RecordID"));
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
