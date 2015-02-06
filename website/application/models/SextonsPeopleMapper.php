<?php
// application/models/SextonsPeopleMapper.php
     
    class Application_Model_SextonsPeopleMapper
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
                $this->setDbTable('Application_Model_DbTable_SextonsPeople');
            }

            return $this->_dbTable;
        }
     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('PeopleID = ?', $id);
            return $this->getDbTable()->delete($where);        
        }
     
        public function save(Application_Model_SextonsPeople $Sextonsp, $new = 'old')
        {
            $data = array(
                'PeopleID'  => $Sextonsp->getID(),
            );
     
            if ($new == 'new') {
                $id = $Sextonsp->getID();
                $this->getDbTable()->insert($data);
            } else {
                $id = $Sextonsp->getID();
//echo "existing entry $id"; 
//var_dump($data);
                $this->getDbTable()->update($data, array('PeopleID = ?' => $id));
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
            $rowarray['id'] = $row->PeopleID;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_SextonsPeople();
                $entry->setId($row->PeopleID);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'PeopleID';
            
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