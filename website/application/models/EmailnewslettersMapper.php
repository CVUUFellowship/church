<?php
// application/models/EmailnewslettersMapper.php
     
    class Application_Model_EmailnewslettersMapper
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
                $this->setDbTable('Application_Model_DbTable_Emailnewsletters');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Emailnewsletters $emailnewsletters)
        {
            $data = array(
                'PersonID'  => $emailnewsletters->getID(),
            );
     
            if (null === ($id = $emailnewsletters->getId())) {
                unset($data['PersonID']);
                /* handle non-null requirements */
                $id = $this->getDbTable()->insert($data);
                $households->setId($id);
            } else {
                $this->getDbTable()->update($data, array('PersonID = ?' => $id));
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
            $rowarray['id'] = $row->PersonID;
            return ($rowarray);
        }
     
    }
?>