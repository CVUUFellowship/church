<?php
// application/models/ConnectionsMapper.php
     
    class Application_Model_ConnectionsMapper
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
                $this->setDbTable('Application_Model_DbTable_Connections');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Connections $connection)
        {
            $data = array(
                'ConnectID'  => $connection->getID(),
                'PeopleID'  => $connection->getPeopleid(),
                'Comments'   => $connection->getComments(),
                'AngelID'   => $connection->getAngelid(),
                'Inducted'   => $connection->getInducted(),
                'InductionDontAsk'   => $connection->getInductiondontask(),
                'FriendDate'   => $connection->getFrienddate(),
                'UU_date'   => $connection->getClassdate(),
                'TimeStamp' => $connection->getTimestamp(),
            );
//var_dump($data);
     
            if (null === ($id = $connection->getId())) {
//echo "NEW CONNECTION in save()<br>";
                unset($data['ConnectID']);
                /* handle non-null requirements */
//                $data['ConnectID'] = isset($data['ConnectID']) ? $data['ConnectID'] : 0;
                $data['PeopleID'] = isset($data['PeopleID']) ? $data['PeopleID'] : 0;
                $data['Comments'] = isset($data['Comments']) ? $data['Comments'] : '';
                $data['AngelID'] = isset($data['AngelID']) ? $data['AngelID'] : 0;
                $data['Inducted'] = isset($data['Inducted']) ? $data['Inducted'] : 'no';
                $data['InductionDontAsk'] = isset($data['InductionDontAsk']) ? $data['InductionDontAsk'] : 'no';
                $data['FriendDate'] = isset($data['FriendDate']) ? $data['FriendDate'] : '';
                $data['UU_date'] = isset($data['UU_date']) ? $data['UU_date'] : '';
//var_dump($data);
//exit;

                $id = $this->getDbTable()->insert($data);
//echo "new id is $id<br>";
                $connection->setId($id);
            } else {
//var_dump($data);
//exit;
                $this->getDbTable()->update($data, array('ConnectID = ?' => $id));
            }
//exit;
        }
     
        public function find($id)
        {
            $result = $this->getDbTable()->find($id);
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
            $rowarray = array();
            $rowarray['id'] = $row->ConnectID;
            $rowarray['peopleid'] = $row->PeopleID;
            $rowarray['comments'] = $row->Comments;
            $rowarray['angelid'] = $row->AngelID;
                $inducted = $row->Inducted;
                if ($inducted == 'X')
                    $inducted = 'Yes';
            $rowarray['inducted'] = $inducted;
            $rowarray['inductiondontask'] = $row->InductionDontAsk;
            $rowarray['frienddate'] = $row->FriendDate;
            $rowarray['classdate'] = $row->UU_date;
            $rowarray['timestamp'] = $row->TimeStamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Connections();
                $entry->setId($row->ConnectID);
                $entry->setPeopleid($row->PeopleID);
                $entry->setComments($row->Comments);
                $entry->setAngelid($row->AngelID);
                $inducted = $row->Inducted;
                if ($inducted == 'X')
                    $inducted = 'Yes';
                $entry->setInducted($inducted);
                $entry->setInductiondontask($row->InductionDontAsk);
                $entry->setFrienddate($row->FriendDate);
                $entry->setClassdate($row->UU_date);
                $entry->setTimestamp($row->TimeStamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'ConnectID';
            $map['peopleid'] = 'PeopleID';
            $map['comments'] = 'Comments';
            $map['angelid'] = 'AngelID';
            $map['inducted'] = 'Inducted';
            $map['inductiondontask'] = 'Inductiondontask';
            $map['frienddate'] = 'FriendDate';
            $map['classdate'] = 'UU_date';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("PeopleID"));
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