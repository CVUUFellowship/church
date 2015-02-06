<?php
// application/models/HouseholdsMapper.php
     
    class Application_Model_HouseholdsMapper
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
                $this->setDbTable('Application_Model_DbTable_Households');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Households $household)
        {
            $data = array(
                'RecordID'  => $household->getID(),
                'Inactive'  => $household->getInactive(),
                'HouseholdName'   => $household->getHouseholdname(),
                'CreationDate'   => $household->getCreationdate(),
                'Street'   => $household->getStreet(),
                'City' => $household->getCity(),
                'State'   => $household->getState(),
                'Zip'   => $household->getZip(),
                'Phone'   => $household->getPhone(),
                'TimeStamp' => $household->getTimestamp(),
            );
     
            if (null === ($id = $household->getId())) {
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Inactive'] = isset($data['Inactive']) ? $data['Inactive'] : 'no';
                $data['HouseholdName'] = isset($data['HouseholdName']) ? $data['HouseholdName'] : '';
                $data['CreationDate'] = isset($data['CreationDate']) ? $data['CreationDate'] : '';
                $data['Street'] = isset($data['Street']) ? $data['Street'] : '';
                $data['City'] = isset($data['City']) ? $data['City'] : '';
                $data['State'] = isset($data['State']) ? $data['State'] : '';
                $data['Zip'] = isset($data['Zip']) ? $data['Zip'] : '';
                $data['Phone'] = isset($data['Phone']) ? $data['Phone'] : '';
                $id = $this->getDbTable()->insert($data);
                $household->setId($id);
            } else {
                $this->getDbTable()->update($data, array('RecordID = ?' => $id));
            }
        }
     
        public function savearray($household)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['inactive'] = 'Inactive';
            $map['householdName'] = 'HouseholdName';
            $map['creationdate'] = 'CreationDate';
            $map['street'] = 'Street';
            $map['city'] = 'City';
            $map['state'] = 'State';
            $map['zip'] = 'Zip';
            $map['phone'] = 'Phone';
            $map['timestamp'] = 'Timestamp';

            $data = array(
                'RecordID'  => $household['id'],
                'Inactive'  => $household['inactive'],
                'HouseholdName'   => $household['householdname'],
                'CreationDate'   => $household['creationdate'],
                'Street'   => $household['street'],
                'City' => $household['city'],
                'State'   => $household['state'],
                'Zip'   => $household['zip'],
                'Phone'   => $household['phone'],
                'TimeStamp' => $household['timestamp'],
            );
     
            if (null === ($id = $household['id'])) {
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Inactive'] = isset($data['Inactive']) ? $data['Inactive'] : 'no';
                $data['HouseholdName'] = isset($data['HouseholdName']) ? $data['HouseholdName'] : '';
                $data['CreationDate'] = isset($data['CreationDate']) ? $data['CreationDate'] : '';
                $data['Street'] = isset($data['Street']) ? $data['Street'] : '';
                $data['City'] = isset($data['City']) ? $data['City'] : '';
                $data['State'] = isset($data['State']) ? $data['State'] : '';
                $data['Zip'] = isset($data['Zip']) ? $data['Zip'] : '';
                $data['Phone'] = isset($data['Phone']) ? $data['Phone'] : '';
                $id = $this->getDbTable()->insert($data);
                $household->setId($id);
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
            $rowarray['inactive'] = $row->Inactive;
            $rowarray['householdname'] = $row->HouseholdName;
            $rowarray['creationdate'] = $row->CreationDate;
            $rowarray['street'] = $row->Street;
            $rowarray['city'] = $row->City;
            $rowarray['state'] = $row->State;
            $rowarray['zip'] = $row->Zip;
            $rowarray['phone'] = $row->Phone;
            $rowarray['timestamp'] = $row->TimeStamp;

            return ($rowarray);
        }
     
        public function findEmail($email)
        {
            $sql = $this->getDbTable()->select()
                ->where("Email = '$email'");
            $result = $this->getDbTable()->fetchAll($sql);
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
            $rowarray = array();
            $rowarray['id'] = $row->RecordID;
            $rowarray['inactive'] = $row->Inactive;
            $rowarray['householdName'] = $row->HouseholdName;
            $rowarray['creationdate'] = $row->CreationDate;
            $rowarray['street'] = $row->Street;
            $rowarray['city'] = $row->City;
            $rowarray['state'] = $row->State;
            $rowarray['zip'] = $row->Zip;
            $rowarray['phone'] = $row->Phone;
            $rowarray['timestamp'] = $row->TimeStamp;

            return ($rowarray);
        }
     
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Households();
                $entry->setId($row->RecordID);
                $entry->setInactive($row->Inactive);
                $entry->setHouseholdname($row->HouseholdName);
                $entry->setStreet($row->Street);
                $entry->setCity($row->City);
                $entry->setState($row->State);
                $entry->setZip($row->Zip);
                $entry->setPhone($row->Phone);
                $entry->setTimestamp($row->TimeStamp);
                $entries[] = $entry;
            }

            return $entries;
        }

        public function fetchEmail($email)
        {
            $sql = $this->getDbTable()->select();
            $sql->where("Email = '$email'");
            return $this->fetchAll($sql);

        }

        public function fetchId($id)
        {
            $sql = $this->getDbTable()->select()
                ->where("RecordID = '$id'");
            return $this->fetchAll($sql);

        }

        public function fetchName($householdName)
        {
            $sql = $this->getDbTable()->select()
                ->where("HouseholdName = '$householdName'")
                ;
            return $this->fetchAll($sql);

        }

        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['inactive'] = 'Inactive';
            $map['householdName'] = 'HouseholdName';
            $map['creationdate'] = 'CreationDate';
            $map['street'] = 'Street';
            $map['city'] = 'City';
            $map['state'] = 'State';
            $map['zip'] = 'Zip';
            $map['phone'] = 'Phone';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Inactive DESC", "HouseholdName"));
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