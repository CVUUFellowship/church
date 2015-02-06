<?php
// application/models/EmailForwardMapper.php
     
    class Application_Model_EmailForwardMapper
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
                $this->setDbTable('Application_Model_DbTable_EmailForward');
            }

            return $this->_dbTable;
        }
     
     

        public function delete($key)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('PersonID = ?', $key);
            return $this->getDbTable()->delete($where);        
        }
     

        public function truncate()
        {
            return $this->getDbTable()->delete('1=1');        
        }



        public function save(Application_Model_EmailForward $forward)
        {
            $data = array(
                'RecordID'  => $forward->getID(),
                'Forwarder'  => $forward->getForwarder(),
                'ForwardTo'   => $forward->getForwardto(),
            );
     
            if (null === ($id = $forward->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Forwarder'] = isset($data['Forwarder']) ? $data['Forwarder'] : '';
                $data['ForwardTo'] = isset($data['ForwardTo']) ? $data['ForwardTo'] : '';
                $id = $this->getDbTable()->insert($data);
                $forward->setId($id);
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
            $rowarray['forwarder'] = $row->Forwarder;
            $rowarray['forwardto'] = $row->ForwardTo;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_EmailForward();
                $entry->setId($row->RecordID);
                $entry->setForwarder($row->Forwarder);
                $entry->setForwardto($row->ForwardTo);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['forwarder'] = 'Forwarder';
            $map['forwardto'] = 'ForwardTo';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Forwarder", "ForwardTo"));
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