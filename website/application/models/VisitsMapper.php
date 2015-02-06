<?php
// application/models/VisitsMapper.php
     
    class Application_Model_VisitsMapper
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
                $this->setDbTable('Application_Model_DbTable_Visits');
            }

            return $this->_dbTable;
        }
     
     

        public function delete($key)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('VisitID = ?', $key);
            return $this->getDbTable()->delete($where);        
        }


        public function save(Application_Model_Visits $visit)
        {
            $data = array(
                'VisitID'  => $visit->getId(),
                'PersonID'  => $visit->getPersonid(),
                'VisitDate'   => $visit->getVisitdate(),
                'Service'   => $visit->getService(),
            );
            if (null === ($id = $visit->getId())) {
                unset($data['VisitID']);
                /* handle non-null requirements */
//                $data['VisitID'] = isset($data['VisitID']) ? $data['VisitID'] : 0;
                $data['VisitDate'] = isset($data['VisitDate']) ? $data['VisitDate'] : '';
                $data['PersonID'] = isset($data['PersonID']) ? $data['PersonID'] : 0;
                $data['Service'] = isset($data['Service']) ? $data['Service'] : '';
                $id = $this->getDbTable()->insert($data);
//echo "new id is $id<br>";
                $visit->setId($id);
            } else {
//var_dump($data);
//exit;
                $this->getDbTable()->update($data, array('VisitID = ?' => $id));
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
            $rowarray['id'] = $row->VisitID;
            $rowarray['personid'] = $row->PersonID;
            $rowarray['visitdate'] = $row->VisitDate;
            $rowarray['service'] = $row->Service;
            return ($rowarray);
        }
     
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Visits();
                $entry->setId($row->VisitID);
                $entry->setVisitdate($row->VisitDate);
                $entry->setService($row->Service);
                $entry->setPersonid($row->PersonID);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'VisitID';
            $map['visitdate'] = 'VisitDate';
            $map['service'] = 'Service';
            $map['personid'] = 'PersonID';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("PersonID"));
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