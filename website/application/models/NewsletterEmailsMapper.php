<?php
// application/models/NewsletterEmailsMapper.php
     
    class Application_Model_NewsletterEmailsMapper
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
                $this->setDbTable('Application_Model_DbTable_NewsletterEmails');
            }

            return $this->_dbTable;
        }
     
     

        public function delete($key)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('PersonID = ?', $key);
            return $this->getDbTable()->delete($where);        
        }


        public function save(Application_Model_NewsletterEmails $e)
        {
            $id = $e->getID();
//echo "new entry $id";
            $data['PersonID'] = $id; 
            $this->getDbTable()->insert($data);
        }

     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_NewsletterEmails();
                $entry->setId($row->PersonID);
                $entries[] = $entry;
            }

            return $entries;
        }

     
        public function find($id)
        {
            $sql = $this->getDbTable()->select()
                ->where("PersonID = '$id'");
            $result = $this->getDbTable()->fetchAll($sql);
//var_dump($result); exit;
            if (0 == count($result)) {
                return 'no';
            }
            return 'yes';
        }
    }
?>
