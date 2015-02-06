<?php
// application/models/NodesMapper.php
     
    class Application_Model_NodesMapper
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
                $this->setDbTable('Application_Model_DbTable_Nodes');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Nodes $node)
        {
            $data = array(
                'RecordID'  => $node->getID(),
                'Nid'  => $node->getNodeid(),
                'Title'   => $node->getTitle(),
                'Content'   => $node->getContent(),
                'Org'   => $node->getOrganization(),
                'Special'   => $node->getSpecial(),
                'Date'   => $node->getDate(),
                'Body'   => $node->getBody(),
                'Preliminary'   => $node->getPreliminary(),
            );
     
            if (null === ($id = $node->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Nid'] = isset($data['Nid']) ? $data['Nid'] : '';
                $data['Title'] = isset($data['Title']) ? $data['Title'] : '';
                $data['Content'] = isset($data['Content']) ? $data['Content'] : '';
                $data['Org'] = isset($data['Org']) ? $data['Org'] : '';
                $data['Special'] = isset($data['Special']) ? $data['Special'] : '';
                $data['Date'] = isset($data['Date']) ? $data['Date'] : '';
                $data['Body'] = isset($data['Body']) ? $data['Body'] : '';
                $data['Preliminary'] = isset($data['Preliminary']) ? $data['Preliminary'] : '';
                $id = $this->getDbTable()->insert($data);
                $node->setId($id);
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
            $rowarray['nodeid'] = $row->Nodeid;
            $rowarray['title'] = $row->Title;
            $rowarray['content'] = $row->Content;
            $rowarray['organization'] = $row->Org;
            $rowarray['special'] = $row->Special;
            $rowarray['date'] = $row->Date;
            $rowarray['body'] = $row->Body;
            $rowarray['preliminary'] = $row->Preliminary;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Nodes();
                $entry->setId($row->RecordID);
                $entry->setNodeid($row->Nid);
                $entry->setTitle($row->Title);
                $entry->setContent($row->Content);
                $entry->setOrganization($row->Org);
                $entry->setSpecial($row->Special);
                $entry->setDate($row->Date);
                $entry->setBody($row->Body);
                $entry->setPreliminary($row->Preliminary);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['nodeid'] = 'Nid';
            $map['title'] = 'Title';
            $map['content'] = 'Content';
            $map['organization'] = 'Org';
            $map['special'] = 'Special';
            $map['date'] = 'Date';
            $map['body'] = 'Body';
            $map['preliminary'] = 'Preliminary';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Date DESC"));
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



        public function max()
        {
            $sql = $this->getDbTable()->select();
            $sql->from(array('nodes'),
                        'MAX(Nid) AS id'
                        );
            $sql->setIntegrityCheck(false);
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $resultRow = current($resultSet);
            $result = $resultRow[0]['id'];
            return($result);
        }


    }
?>