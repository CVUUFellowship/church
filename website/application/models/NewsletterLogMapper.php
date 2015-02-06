<?php
// application/models/NewsletterLogMapper.php
     
    class Application_Model_NewsletterLogMapper
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
                $this->setDbTable('Application_Model_DbTable_NewsletterLog');
            }

            return $this->_dbTable;
        }

     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('RecordID = ?', $id);
            return $this->getDbTable()->delete($where);        
        }

     
        public function save(Application_Model_NewsletterLog $NewsletterLog)
        {
            $data = array(
                'RecordID'  => $NewsletterLog->getId(),
                'IP'  => $NewsletterLog->getip(),
                'Edition'   => $NewsletterLog->getedition(),
            );
     
            if (null === ($id = $NewsletterLog->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['Edition'] = isset($data['Edition']) ? $data['Edition'] : 0;
                $data['IP'] = isset($data['IP']) ? $data['IP'] : 0;
                $id = $this->getDbTable()->insert($data);
                $NewsletterLog->setId($id);
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
            $rowarray['ip'] = $row->IP;
            $rowarray['edition'] = $row->Edition;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_NewsletterLog();
                $entry->setId($row->RecordID);
                $entry->setIp($row->IP);
                $entry->setEdition($row->Edition);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['ip'] = 'IP';
            $map['edition'] = 'Edition';
            
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