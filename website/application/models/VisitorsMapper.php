<?php
// application/models/VisitorsMapper.php
     
    class Application_Model_VisitorsMapper
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
                $this->setDbTable('Application_Model_DbTable_Visitors');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Visitors $visitor, $new = 'old')
        {
            $data = array(
                'PersonID'  => $visitor->getID(),
                'Inactive'  => $visitor->getInactive(),
                'Comment'   => $visitor->getComment(),
                'SignedDate'   => $visitor->getSigneddate(),
                'ResignedDate'   => $visitor->getResigneddate(),
                'Reference'   => $visitor->getReference(),
                'PriorUU'   => $visitor->getPrioruu(),
            );
     
            if ($new == 'new') {
                $id = $visitor->getID();
                /* handle non-null requirements */
//                $data['PersonID'] = isset($data['PersonID']) ? $data['PersonID'] : 0;
                $data['Comment'] = isset($data['Comment']) ? $data['Comment'] : '';
                $data['Inactive'] = isset($data['Inactive']) ? $data['Inactive'] : 'no';
                $data['SignedDate'] = isset($data['SignedDate']) ? $data['SignedDate'] : '';
                $data['ResignedDate'] = isset($data['ResignedDate']) ? $data['ResignedDate'] : '';
                $data['PriorUU'] = isset($data['PriorUU']) ? $data['PriorUU'] : 'no';
                $this->getDbTable()->insert($data);
            } 
            else {
                $id = $visitor->getID();
//echo "existing entry $id"; 
//var_dump($data);
                $this->getDbTable()->update($data, array('PersonID = ?' => $id));
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
            $rowarray['id'] = $row->PersonID;
            $rowarray['inactive'] = $row->Inactive;
            $rowarray['comment'] = $row->Comment;
            $rowarray['signeddate'] = $row->SignedDate;
            $rowarray['resigneddate'] = $row->ResignedDate;
            $rowarray['reference'] = $row->Reference;
            $rowarray['prioruu'] = $row->PriorUU;
            $rowarray['timestamp'] = $row->TimeStamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Visitors();
                $entry->setId($row->PersonID);
                $entry->setComment($row->Comment);
                $entry->setSigneddate($row->SignedDate);
                $entry->setResigneddate($row->ResignedDate);
                $entry->setReference($row->Reference);
                $entry->setPrioruu($row->PriorUU);
                $entry->setTimestamp($row->TimeStamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'PersonID';
            $map['comment'] = 'Comment';
            $map['signeddate'] = 'SignedDate';
            $map['resigneddate'] = 'ResignedDate';
            $map['reference'] = 'Reference';
            $map['prioruu'] = 'PriorUU';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("PersonID"));
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
