<?php
// application/models/UnsubMapper.php
     
    class Application_Model_UnsubMapper
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
                $this->setDbTable('Application_Model_DbTable_Unsub');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Unsub $unsub, $new = 'old')
        {
            $data = array(
                'PersonID'  => $unsub->getID(),
                'all'  => $unsub->getAll(),
                'weekly'   => $unsub->getWeekly(),
                'newsletter'   => $unsub->getNewsletter(),
                'neighborhood'   => $unsub->getNeighborhood(),
                'individual'   => $unsub->getindividual(),
            );
     
            if ($new == 'new') {
                $id = $unsub->getID();
                /* handle non-null requirements */
//                $data['PersonID'] = isset($data['PersonID']) ? $data['PersonID'] : 0;
                $data['all'] = isset($data['all']) ? $data['all'] : '';
                $data['weekly'] = isset($data['weekly']) ? $data['weekly'] : '';
                $data['newsletter'] = isset($data['newsletter']) ? $data['newsletter'] : '';
                $data['neighborhood'] = isset($data['neighborhood']) ? $data['neighborhood'] : '';
                $data['individual'] = isset($data['individual']) ? $data['individual'] : '';
                $this->getDbTable()->insert($data);
            } 
            else {
                $id = $unsub->getID();
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
            $rowarray['all'] = $row->all;
            $rowarray['weekly'] = $row->weekly;
            $rowarray['newsletter'] = $row->newsletter;
            $rowarray['neighborhood'] = $row->neighborhood;
            $rowarray['individual'] = $row->individual;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Unsub();
                $entry->setId($row->PersonID);
                $entry->setAll($row->all);
                $entry->setWeekly($row->weekly);
                $entry->setNewsletter($row->newsletter);
                $entry->setNeighborhood($row->neighborhood);
                $entry->setIndividual($row->individual);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'PersonID';
            $map['all'] = 'all';
            $map['weekly'] = 'weekly';
            $map['newsletter'] = 'newsletter';
            $map['neighborhood'] = 'neighborhood';
            $map['individual'] = 'individual';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("PersonID DESC"));
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



        public function fetchOrWhere(array $where)
        {
            $map = array();
            $map['id'] = 'PersonID';
            $map['`all`'] = '`all`';
            $map['weekly'] = 'weekly';
            $map['newsletter'] = 'newsletter';
            $map['neighborhood'] = 'neighborhood';
            $map['individual'] = 'individual';
            
            $sql = $this->getDbTable()->select();
            $sql->order(array("PersonID"));
            foreach ($where as $cond) 
            {
                $str = $map[$cond[0]].$cond[1]."\"".$cond[2]."\"";
                $sql->orwhere("$str");
            }
            return $this->fetchAll($sql);       
        }


    }
?>
