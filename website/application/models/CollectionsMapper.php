<?php
// application/models/CollectionsMapper.php
     
    class Application_Model_CollectionsMapper
    {
        protected $_dbTable;
        
        public function __construct()
        {
            $this->table = 'dummy';
        }
     
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
                $this->setDbTable('Application_Model_DbTable_' . $this->table);
            }
//var_dump($this->_dbTable);    exit;

            return $this->_dbTable;
        }
     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('RecordID = ?', $id);
            return $this->getDbTable()->delete($where);        
        }

     
        public function save(Application_Model_Collections $collection)
        {
            $data = array(
                'RecordID'  => $collection->getID(),
                'Title'  => $collection->getTitle(),
                'Sequence'   => $collection->getSequence(),
                'HeadingID'   => $collection->getHeadingid(),
                'Contact1'   => $collection->getContact1(),
                'Contact2'   => $collection->getContact2(),
                'Contact3'   => $collection->getContact3(),
                'Contact4'   => $collection->getContact4(),
                'PublicPage'   => $collection->getPublicpage(),
                'TimeStamp' => $collection->getTimestamp(),
            );
     
            if (null === ($id = $collection->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['Sequence'] = isset($data['Sequence']) ? $data['Sequence'] : 0;
                $data['HeadingID'] = isset($data['HeadingID']) ? $data['HeadingID'] : 0;
                $data['Contact1'] = isset($data['Contact1']) ? $data['Contact1'] : 0;
                $data['Contact2'] = isset($data['Contact2']) ? $data['Contact2'] : 0;
                $data['Contact3'] = isset($data['Contact3']) ? $data['Contact3'] : 0;
                $data['Contact4'] = isset($data['Contact4']) ? $data['Contact4'] : 0;
                $data['PublicPage'] = isset($data['PublicPage']) ? $data['PublicPage'] : 'no';
                $id = $this->getDbTable()->insert($data);
                $collection->setId($id);
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
//var_dump($row); 
            $rowarray = array();
            $rowarray['id'] = $row->RecordID;
            $rowarray['title'] = $row->Title;
            $rowarray['sequence'] = $row->Sequence;
            $rowarray['headingid'] = $row->HeadingID;
            $rowarray['contact1'] = $row->Contact1;
            $rowarray['contact2'] = $row->Contact2;
            $rowarray['contact3'] = $row->Contact3;
            $rowarray['contact4'] = $row->Contact4;
            $rowarray['publicpage'] = $row->PublicPage;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//var_dump($sql); exit;
//echo($sql->__toString()); exit;
//var_dump($this->_dbTable); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
//var_dump($resultSet); exit;
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Collections();
                $entry->setId($row->RecordID);
                $entry->setTitle($row->Title);
                $entry->setSequence($row->Sequence);
                $entry->setHeadingid($row->HeadingID);
                $entry->setContact1($row->Contact1);
                $entry->setContact2($row->Contact2);
                $entry->setContact3($row->Contact3);
                $entry->setContact4($row->Contact4);
                $entry->setPublicpage($row->PublicPage);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }
//var_dump($entries); exit;
            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['title'] = 'Title';
            $map['sequence'] = 'Sequence';
            $map['headingid'] = 'HeadingID';
            $map['contact1'] = 'Contact1';
            $map['contact2'] = 'Contact2';
            $map['contact3'] = 'Contact3';
            $map['contact4'] = 'Contact4';
            $map['publicpage'] = 'PublicPage';
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