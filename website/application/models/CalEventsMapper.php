<?php
// application/models/CalEventsMapper.php
     
    class Application_Model_CalEventsMapper
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
                $this->setDbTable('Application_Model_DbTable_CalEvents');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalEvents $event)
        {
            $data = array(
                'RecordID'  => $event->getID(),
                'EventID'  => $event->getEventid(),
                'RequestTime'   => $event->getRequesttime(),
                'RequesterID'   => $event->getRequesterid(),
                'resultEmail'   => $event->getResultemail(),
                'Timestamp' => $event->getTimestamp(),
            );
     
            if (null === ($id = $event->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['EventID'] = isset($data['EventID']) ? $data['EventID'] : 0;
                $data['RequestTime'] = isset($data['RequestTime']) ? $data['RequestTime'] : '';
                $data['RequesterID'] = isset($data['RequesterID']) ? $data['RequesterID'] : 0;
                $data['resultEmail'] = isset($data['resultEmail']) ? $data['resultEmail'] : 'no';
                $id = $this->getDbTable()->insert($data);
                $event->setId($id);
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
            $rowarray['eventid'] = $row->EventID;
            $rowarray['requesttime'] = $row->RequestTime;
            $rowarray['requesterid'] = $row->RequesterID;
            $rowarray['resultemail'] = $row->resultEmail;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_CalEvents();
                $entry->setId($row->RecordID);
                $entry->setEventid($row->EventID);
                $entry->setRequestTime($row->RequestTime);
                $entry->setRequesterid($row->RequesterID);
                $entry->setResultemail($row->resultEmail);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['eventid'] = 'EventID';
            $map['requesttime'] = 'RequestTime';
            $map['requesterid'] = 'RequesterID';
            $map['resultemail'] = 'resultEmail';
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