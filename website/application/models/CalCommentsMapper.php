<?php
// application/models/CalCommentsMapper.php
     
    class Application_Model_CalCommentsMapper
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
                $this->setDbTable('Application_Model_DbTable_CalComments');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalComments $room)
        {
            $data = array(
                'EventID'  => $room->getID(),
                'Comments'   => $room->getComments(),
                'Timestamp' => $room->getTimestamp(),
            );

            if (!isset($data['Timestamp'])) {
                $data['Timestamp'] = date('Y-m-d H:i:s', time());
            }
     
            if (null === ($id = $room->getId())) {
                unset($data['EventID']);
                /* handle non-null requirements */
                $data['EventID'] = isset($data['EventID']) ? $data['EventID'] : 0;
                $data['Comments'] = isset($data['Comments']) ? $data['Comments'] : '';
                $id = $this->getDbTable()->insert($data);
                $connection->setId($id);
            } else {
                $this->getDbTable()->update($data, array('EventID = ?' => $id));
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
            $rowarray['id'] = $row->EventID;
            $rowarray['comments'] = $row->Comments;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_CalComments();
                $entry->setId($row->EventID);
                $entry->setComments($row->Comments);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'EventID';
            $map['comments'] = 'Comments';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("EventID"));
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
