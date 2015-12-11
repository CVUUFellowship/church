<?php
// application/models/CookiesMapper.php
     
    class Application_Model_CookiesMapper
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
                $this->setDbTable('Application_Model_DbTable_Cookies');
            }

            return $this->_dbTable;
        }
     
     

        public function delete($stamp)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('Timestamp = ?', $stamp);
            return $this->getDbTable()->delete($where);        
        }


        public function save(Application_Model_Cookies $cookie)
        {
            $data = array(
                'SecurityID'   => $cookie->auth,
                'Value'   => $cookie->value,
            );
            if (null === ($id = $cookie->getId())) {
                unset($data['id']);
               $this->getDbTable()->insert($data);  
             
            } else {
                $this->getDbTable()->update($data, array('id = ?' => $id));
            }
        }
     
        public function find($id, Application_Model_Cookies $cookie)
        {
            $sql = $this->getDbTable()->select()
                ->where("Value = '$id'");
            $result = $this->getDbTable()->fetchAll($sql);
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
            $cookie->setId($row->SecurityID);
            $cookie->setValue($row->Value);
            $cookie->setTimestamp($row->Timestamp);
        }
     
        public function fetchAll()
        {
            $resultSet = $this->getDbTable()->fetchAll();
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Cookies();
                $entry->setValue($row->Value);
                $entry->setTimestamp($row->Timestamp);
                $entry->setId($row->SecurityID);
                $entries[] = $entry;
            }
            return $entries;
        }


     

    }
?>
