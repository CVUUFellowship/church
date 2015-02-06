<?php
// application/models/LibAudioCatalogMapper.php
     
    class Application_Model_LibAudioCatalogMapper
    {
        protected $_dbTable;
     
        public function setDbTable($dbTable)
        {
            if (is_string($dbTable)) {
                $dbTable = new $dbTable();
            }
            if (!$dbTable instanceof Zend_Db_Table_Abstract) {
                throw new Exception('Invalnumber table data gateway provnumbered');
            }
            $this->_dbTable = $dbTable;
            return $this;
        }
     
        public function getDbTable()
        {
            if (null === $this->_dbTable) {
                $this->setDbTable('Application_Model_DbTable_LibAudioCatalog');
            }

            return $this->_dbTable;
        }
     


        public function save(Application_Model_People $people)
        {
            $data = array(
                'RecordID'  => $people->getRecordID(),
                'Date'   => $people->getDate(),
                'Title'  => $people->getTitle(),
                'Presenter'   => $people->getPresenter(),
                'Timestamp' => $people->getTimestamp(),
            );
     
            if (null === ($number = $people->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Title'] = isset($data['Title']) ? $data['Title'] : 'no';
                $data['Presenter'] = isset($data['Presenter']) ? $data['Presenter'] : '';
                $data['Date'] = isset($data['Date']) ? $data['Date'] : '';
                
                $number = $this->getDbTable()->insert($data);
                $people->setId($number);
            } else {
                $this->getDbTable()->update($data, array('RecordID = ?' => $number));
            }
        }
     

      
     
        public function find($number)
        {
            $result = $this->getDbTable()->find($number);
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
            $rowarray = array();
            $rowarray['id'] = $row->RecordID;
            $rowarray['title'] = $row->Title;
            $rowarray['presenter'] = $row->Presenter;
            $rowarray['date'] = $row->Date;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
     
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_LibAudioCatalog();
                $entry->setId($row->RecordID);
                $entry->setTitle($row->Title);
                $entry->setPresenter($row->Presenter);
                $entry->setDate($row->Date);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }



        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['title'] = 'Title';
            $map['presenter'] = 'Presenter';
            $map['date'] = 'Date';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Date DESC"));
            else
            {
                $mapped = array();
                foreach ($order as $item)
                {
                    $mapped[] = $map[$item];
                }
                $sql->order($mapped);                 
            }
            foreach ($where as $cond) 
            {
//var_dump($cond);
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
            $map['title'] = 'Title';
            $map['presenter'] = 'Presenter';
            $map['date'] = 'Date';
            
            $sql = $this->getDbTable()->select();
            $sql->order(array("Title"));
            foreach ($where as $cond) 
            {
                $str = $map[$cond[0]].$cond[1]."\"".$cond[2]."\"";
                $sql->orwhere("$str");
            }
            return $this->fetchAll($sql);       
        }



    }
?>