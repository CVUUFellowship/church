<?php
// application/models/ProgTableMapper.php
     
    class Application_Model_ProgTableMapper
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
                $this->setDbTable('Application_Model_DbTable_ProgTable');
            }

            return $this->_dbTable;
        }
     


        public function save(Application_Model_ProgTable $cat)
        {
            $data = array(
                'RecordID'  => $cat->getId(),
                'Reservedate'   => $cat->getReservedate(),
                'Person'   => $cat->getPerson(),
                'Program' => $cat->getProgram(),
                'Timestamp' => $cat->getTimestamp(),
            );
     
            if (null === ($number = $cat->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Reservedate'] = isset($data['Reservedate']) ? $data['Reservedate'] : '';
                $data['Person'] = isset($data['Person']) ? $data['Person'] : '';
                $data['Program'] = isset($data['Program']) ? $data['Program'] : '';
                $number = $this->getDbTable()->insert($data);
                $cat->setId($number);
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
            $rowarray['reservedate'] = $row->ReserveDate;
            $rowarray['person'] = $row->Person;
            $rowarray['program'] = $row->Program;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
     
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_ProgTable();
                $entry->setId($row->RecordID);
                $entry->setReservedate($row->ReserveDate);
                $entry->setPerson($row->Person);
                $entry->setProgram($row->Program);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }



        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['reservedate'] = 'ReserveDate';
            $map['person'] = 'Person';
            $map['program'] = 'Program';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Reservedate"));
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