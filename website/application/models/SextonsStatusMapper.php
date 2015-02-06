<?php
// application/models/SextonsStatusMapper.php
     
    class Application_Model_SextonsStatusMapper
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
                $this->setDbTable('Application_Model_DbTable_SextonsStatus');
            }

            return $this->_dbTable;
        }

     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('RecordID = ?', $id);
            return $this->getDbTable()->delete($where);        
        }

     
        public function save(Application_Model_SextonsStatus $Sextonsstatus)
        {
            $data = array(
                'RecordID'  => $Sextonsstatus->getId(),
                'PeopleID'  => $Sextonsstatus->getPeopleid(),
                'DateID'   => $Sextonsstatus->getDateid(),
                'Status'   => $Sextonsstatus->getStatus(),
                'Timestamp' => $Sextonsstatus->getTimestamp(),
            );
     
            if (null === ($id = $Sextonsstatus->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['DateID'] = isset($data['DateID']) ? $data['DateID'] : 0;
                $data['PeopleID'] = isset($data['PeopleID']) ? $data['PeopleID'] : 0;
                $data['Status'] = isset($data['Status']) ? $data['Status'] : '';
                $id = $this->getDbTable()->insert($data);
                $Sextonsstatus->setId($id);
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
            $rowarray['peopleid'] = $row->PeopleID;
            $rowarray['dateid'] = $row->DateID;
            $rowarray['status'] = $row->Status;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_SextonsStatus();
                $entry->setId($row->RecordID);
                $entry->setPeopleid($row->PeopleID);
                $entry->setDateid($row->DateID);
                $entry->setStatus($row->Status);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['peopleid'] = 'PeopleID';
            $map['dateid'] = 'DateID';
            $map['status'] = 'Status';
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