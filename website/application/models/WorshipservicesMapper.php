<?php
// application/models/WorshipservicesMapper.php
     
    class Application_Model_WorshipservicesMapper
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
                $this->setDbTable('Application_Model_DbTable_Worshipservices');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Worshipservices $service)
        {
            $data = array(
                'RecordID'  => $service->getID(),
                'Sunday'  => $service->getSunday(),
                'SermonTitle'   => $service->getTitle(),
                'Presenter'   => $service->getPresenter(),
                'Summary'   => $service->getSummary(),
            );
     
            if (null === ($id = $service->getId())) {
                unset($data['ConnectID']);
                /* handle non-null requirements */
                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Sunday'] = isset($data['Sunday']) ? $data['Sunday'] : '';
                $data['SermonTitle'] = isset($data['SermonTitle']) ? $data['SermonTitle'] : '';
                $data['Presenter'] = isset($data['Presenter']) ? $data['Presenter'] : '';
                $data['Summary'] = isset($data['Summary']) ? $data['Summary'] : '';
                $id = $this->getDbTable()->insert($data);
                $service->setId($id);
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
            $rowarray['sunday'] = $row->Sunday;
            $rowarray['title'] = $row->SermonTitle;
            $rowarray['presenter'] = $row->Presenter;
            $rowarray['summary'] = $row->Summary;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Worshipservices();
                $entry->setId($row->RecordID);
                $entry->setSunday($row->Sunday);
                $entry->setTitle($row->SermonTitle);
                $entry->setPresenter($row->Presenter);
                $entry->setSummary($row->Summary);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['sunday'] = 'Sunday';
            $map['title'] = 'SermonTitle';
            $map['presenter'] = 'Presenter';
            $map['summary'] = 'Summary';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Sunday DESC"));
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
            $sql->from(array('oos_services'),
                        'MAX(Sunday) AS sunday'
                        );
            $sql->setIntegrityCheck(false);
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $resultRow = current($resultSet);
            $result = $resultRow[0]['sunday'];
            return($result);
        }


    }
?>