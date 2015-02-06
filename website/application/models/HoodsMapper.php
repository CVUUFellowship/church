<?php
// application/models/HoodsMapper.php
     
    class Application_Model_HoodsMapper
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
                $this->setDbTable('Application_Model_DbTable_Hoods');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Hoods $hood)
        {
            $data = array(
                'RecordID'  => $hood->getID(),
                'HoodName'  => $hood->getHoodName(),
                'Dot'   => $hood->getDot(),
                'Timestamp' => $hood->getTimestamp(),
            );
     
            if (null === ($id = $household->getId())) {
                unset($data['ConnectID']);
                /* handle non-null requirements */
                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Dot'] = isset($data['Dot']) ? $data['Dot'] : 'no';
                $data['HoodName'] = isset($data['HoodName']) ? $data['HoodName'] : '';
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
            $rowarray['hoodname'] = $row->HoodName;
            $rowarray['dot'] = $row->Dot;
            $rowarray['Timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Hoods();
                $entry->setId($row->RecordID);
                $entry->setHoodname($row->HoodName);
                $entry->setDot($row->Dot);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['hoodname'] = 'HoodName';
            $map['dot'] = 'Dot';
            $map['Timestamp'] = 'Timestamp';
            
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