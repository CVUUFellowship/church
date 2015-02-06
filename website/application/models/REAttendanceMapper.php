<?php
// application/models/REAttendanceMapper.php
     
    class Application_Model_REAttendanceMapper
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
                $this->setDbTable('Application_Model_DbTable_REAttendance');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_REAttendance $visit)
        {
            $data = array(
                'RecordID'  => $visit->getId(),
                'ChildID'  => $visit->getChildid(),
                'Date'   => $visit->getDate(),
            );
     
            if (null === ($id = $visit->getId())) {
//echo "new record <br>";
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Date'] = isset($data['Date']) ? $data['Date'] : '';
                $data['ChildID'] = isset($data['ChildID']) ? $data['ChildID'] : 0;
                $id = $this->getDbTable()->insert($data);
//echo "new record id is $id <br>";
                $visit->setId($id);
            } else {
//echo "existing record <br>";
                $this->getDbTable()->update($data, array('RecordID = ?' => $id));
            }
        }
     
     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('RecordID = ?', $id);
            return $this->getDbTable()->delete($where);        
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
            $rowarray['childid'] = $row->ChildID;
            $rowarray['date'] = $row->Date;
            return ($rowarray);
        }
     
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_REAttendance();
                $entry->setId($row->RecordID);
                $entry->setDate($row->Date);
                $entry->setChildid($row->ChildID);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['date'] = 'Date';
            $map['childid'] = 'ChildID';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("ChildID"));
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