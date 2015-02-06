<?php
// application/models/UnsubLogMapper.php
     
    class Application_Model_UnsubLogMapper
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
                $this->setDbTable('Application_Model_DbTable_UnsubLog');
            }

            return $this->_dbTable;
        }
     


        public function save(Application_Model_UnsubLog $log)
        {
            $data = array(
                'RecordID'  => $log->getId(),
                'Email'  => $log->getEmail(),
                'PersonID'   => $log->getPersonId(),
                'UnsubType'   => $log->getUnsubtype(),
                'Timestamp' => $log->getTimestamp(),
            );
     
            if (null === ($number = $log->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Email'] = isset($data['Email']) ? $data['Email'] : '';
                $data['PersonID'] = isset($data['PersonID']) ? $data['PersonID'] : 0;
                $data['UnsubType'] = isset($data['UnsubType']) ? $data['UnsubType'] : '';
                $number = $this->getDbTable()->insert($data);
                $log->setId($number);
            } else {
                $this->getDbTable()->update($data, array('RecordID = ?' => $number));
            }
        }
     
     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('RecordID = ?', $id);
            return $this->getDbTable()->delete($where);        
        }

      
     
        public function find($number)
        {
            $result = $this->getDbTable()->find($number);
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
            $rowarray = array();
            $rowarray['id'] = $row->RecordID;
            $rowarray['email'] = $row->Email;
            $rowarray['personid'] = $row->PersonID;
            $rowarray['unsubtype'] = $row->UnsubType;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
     
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_UnsubLog();
                $entry->setId($row->RecordID);
                $entry->setEmail($row->Email);
                $entry->setPersonid($row->PersonID);
                $entry->setUnsubtType($row->UnsubType);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }



        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['email'] = 'Email';
            $map['personid'] = 'PersonID';
            $map['unsubtype'] = 'UnsubType';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Email"));
            else
            {
                $mapped = array();
                foreach ($order as $item)
                {
                    $mapped[] = $map[$item];
                }
                $sql->order($mapped);                 
            }
            foreach ($where as $cond) 
            {
//var_dump($cond);
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