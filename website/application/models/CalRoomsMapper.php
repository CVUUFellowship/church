<?php
// application/models/CalRoomsMapper.php
     
    class Application_Model_CalRoomsMapper
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
                $this->setDbTable('Application_Model_DbTable_CalRooms');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalRooms $room)
        {
            $data = array(
                'RecordID'  => $room->getID(),
                'RoomName'   => $room->getRoomname(),
                'RoomCode'   => $room->getRoomcode(),
                'Timestamp' => $room->getTimestamp(),
            );
     
            if (null === ($id = $room->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['RoomName'] = isset($data['RoomName']) ? $data['RoomName'] : '';
                $data['RoomCode'] = isset($data['RoomCode']) ? $data['RoomCode'] : '';
                $id = $this->getDbTable()->insert($data);
                $room->setId($id);
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
            $rowarray['roomname'] = $row->RoomName;
            $rowarray['roomcode'] = $row->RoomCode;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_CalRooms();
                $entry->setId($row->RecordID);
                $entry->setRoomname($row->RoomName);
                $entry->setRoomcode($row->RoomCode);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['roomname'] = 'RoomName';
            $map['roomcode'] = 'RoomCode';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("RoomName"));
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