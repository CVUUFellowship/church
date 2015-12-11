<?php
// application/models/CalGroupsMapper.php
     
    class Application_Model_CalGroupsMapper
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
                $this->setDbTable('Application_Model_DbTable_CalGroups');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalGroups $group)
        {
            $data = array(
                'RecordID'  => $group->getID(),
                'GroupID'  => $group->getGroupid(),
                'GroupName'   => $group->getGroupname(),
                'GroupCode'   => $group->getGroupcode(),
                'Timestamp' => $group->getTimestamp(),
            );

            if (!isset($data['Timestamp'])) {
                $data['Timestamp'] = date('Y-m-d H:i:s', time());
            }
     
            if (null === ($id = $group->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['GroupID'] = isset($data['GroupID']) ? $data['GroupID'] : 0;
                $data['GroupName'] = isset($data['GroupName']) ? $data['GroupName'] : '';
                $data['GroupCode'] = isset($data['GroupCode']) ? $data['GroupCode'] : '';
                $id = $this->getDbTable()->insert($data);
                $group->setId($id);
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
            $rowarray['groupid'] = $row->GroupID;
            $rowarray['groupname'] = $row->GroupName;
            $rowarray['groupcode'] = $row->GroupCode;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_CalGroups();
                $entry->setId($row->RecordID);
                $entry->setGroupid($row->GroupID);
                $entry->setGroupname($row->GroupName);
                $entry->setGroupcode($row->GroupCode);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['groupid'] = 'GroupID';
            $map['groupname'] = 'GroupName';
            $map['groupcode'] = 'GroupCode';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("GroupName"));
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



        public function deleteall()
        {
            $where = "1";;            
            return $this->getDbTable()->delete($where);        
        }


    }
?>
