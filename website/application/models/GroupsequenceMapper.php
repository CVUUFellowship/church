<?php
// application/models/GroupsequenceMapper.php
     
    class Application_Model_GroupsequenceMapper
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
                $this->setDbTable('Application_Model_DbTable_Groupsequence');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Groupsequence $sequence)
        {
            $data = array(
                'id'  => $sequence->getID(),
                'headingid'  => $sequence->getHeadingID(),
                'type'   => $sequence->getType(),
                'groupid'   => $sequence->getTitleID(),
                'nextgroup'   => $sequence->getNextGroup(),
                'sequence'   => $sequence->getSequence(),
                'timestamp' => $sequence->getTimestamp(),
            );
     
            if (null === ($id = $household->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['HeadingID'] = isset($data['HeadingID']) ? $data['HeadingID'] : 0;
                $data['Type'] = isset($data['Type']) ? $data['Type'] : '';
                $data['TitleID'] = isset($data['TitleID']) ? $data['TitleID'] : 0;
                $data['NextGroup'] = isset($data['NextGroup']) ? $data['NextGroup'] : 0;
                $data['Sequence'] = isset($data['Sequence']) ? $data['Sequence'] : 0;
                $id = $this->getDbTable()->insert($data);
                $sequence->setId($id);
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
            $rowarray['headingid'] = $row->HeadingID;
            $rowarray['type'] = $row->Type;
            $rowarray['groupid'] = $row->TitleID;
            $rowarray['nextgroup'] = $row->NextGroup;
            $rowarray['sequence'] = $row->Sequence;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Groupsequence();
                $entry->setId($row->RecordID);
                $entry->setHeadingid($row->HeadingID);
                $entry->setType($row->Type);
                $entry->setGroupid($row->TitleID);
                $entry->setNextgroup($row->NextGroup);
                $entry->setSequence($row->Sequence);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['headingid'] = 'HeadingID';
            $map['type'] = 'Type';
            $map['groupid'] = 'TitleID';
            $map['nextgroup'] = 'NextGroup';
            $map['sequence'] = 'Sequence';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Sequence"));
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