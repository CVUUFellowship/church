<?php
// application/models/EMStatusMapper.php
     
    class Application_Model_EMStatusMapper
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
                $this->setDbTable('Application_Model_DbTable_EMStatus');
            }

            return $this->_dbTable;
        }

     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('RecordID = ?', $id);
            return $this->getDbTable()->delete($where);        
        }

     
        public function save(Application_Model_EMStatus $emstatus)
        {
            $data = array(
                'RecordID'  => $emstatus->getId(),
                'PeopleID'  => $emstatus->getPeopleid(),
                'DateID'   => $emstatus->getDateid(),
                'Early'   => $emstatus->getEarly(),
                'Late'   => $emstatus->getLate(),
//                'Timestamp' => $emstatus->getTimestamp(),
            );
     
            if (null === ($id = $emstatus->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['DateID'] = isset($data['DateID']) ? $data['DateID'] : 0;
                $data['PeopleID'] = isset($data['PeopleID']) ? $data['PeopleID'] : 0;
                $data['Early'] = isset($data['Early']) ? $data['Early'] : '';
                $data['Late'] = isset($data['Late']) ? $data['Late'] : '';
                $id = $this->getDbTable()->insert($data);
                $emstatus->setId($id);
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
            $rowarray['early'] = $row->Early;
            $rowarray['late'] = $row->Late;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_EMStatus();
                $entry->setId($row->RecordID);
                $entry->setPeopleid($row->PeopleID);
                $entry->setDateid($row->DateID);
                $entry->setEarly($row->Early);
                $entry->setLate($row->Late);
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
            $map['early'] = 'Early';
            $map['late'] = 'Late';
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
