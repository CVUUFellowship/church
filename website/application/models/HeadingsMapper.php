<?php
// application/models/HeadingsMapper.php
     
    class Application_Model_HeadingsMapper
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
                $this->setDbTable('Application_Model_DbTable_Headings');
            }

            return $this->_dbTable;
        }
     
     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('RecordID = ?', $id);
            return $this->getDbTable()->delete($where);        
        }


        public function save(Application_Model_Headings $heading)
        {
            $data = array(
                'RecordID'  => $heading->getID(),
                'Type'  => $heading->getType(),
                'HeadingName'   => $heading->getHeading(),
                'Sequence'   => $heading->getSequence(),
            );
     
            if (null === ($id = $heading->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Type'] = isset($data['Type']) ? $data['Type'] : '';
                $data['HeadingName'] = isset($data['HeadingName']) ? $data['HeadingName'] : '';
                $data['Sequence'] = isset($data['Sequence']) ? $data['Sequence'] : 0;
                $rec = $this->getDbTable()->insert($data);
//var_dump($rec); 
                $id = $rec['RecordID'];
                $heading->setId($id);
            } else {
                $this->getDbTable()->update($data, array('RecordID = ?' => $id, 'Type = ?' => $data['Type']));
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
            $rowarray['type'] = $row->Type;
            $rowarray['heading'] = $row->HeadingName;
            $rowarray['sequence'] = $row->Sequence;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Headings();
                $entry->setId($row->RecordID);
                $entry->setType($row->Type);
                $entry->setHeading($row->HeadingName);
                $entry->setSequence($row->Sequence);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['type'] = 'Type';
            $map['heading'] = 'HeadingName';
            $map['sequence'] = 'Sequence';
            
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