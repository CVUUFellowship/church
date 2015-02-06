<?php
// application/models/CalApproversMapper.php
     
    class Application_Model_CalApproversMapper
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
                $this->setDbTable('Application_Model_DbTable_CalApprovers');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalApprovers $user, $new = 'old')
        {
            $data = array(
                'RecordID'  => $user->getId(),
                'Title'  => $user->getTitle(),
            );
     
            if (null === ($id = $user->getId())) {
                unset($data['RecordID']);
                $id = $this->getDbTable()->insert($data);
                $user->setId($id);
            } else {
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
            $rowarray['title'] = $row->Title;
            return ($rowarray);
        }
        
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
//var_dump($resultSet); exit;
            foreach ($resultSet as $row) {
                $entry = new Application_Model_CalApprovers();
                $entry->setId($row->RecordID);
                $entry->setTitle($row->Title);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['title'] = 'Title';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Title"));
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