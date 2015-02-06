<?php
// application/models/PeopleMapper.php
     
    class Application_Model_PeopleMapper
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
                $this->setDbTable('Application_Model_DbTable_People');
            }

            return $this->_dbTable;
        }
     


        public function save(Application_Model_People $people)
        {
            $data = array(
                'RecordID'  => $people->getID(),
                'Inactive'  => $people->getInactive(),
                'FirstName'   => $people->getFirstname(),
                'LastName'   => $people->getLastname(),
                'CreationDate'   => $people->getCreationdate(),
                'ResignDate'   => $people->getResigndate(),
                'MembershipDate' => $people->getMembershipdate(),
                'BirthDate'   => $people->getBirthdate(),
                'HouseholdID'   => $people->getHouseholdid(),
                'Gender'   => $people->getGender(),
                'Status'   => $people->getStatus(),
                'Photolink'   => $people->getPhotolink(),
                'PPhone'   => $people->getPphone(),
                'Email'   => $people->getEmail(),
                'TimeStamp' => $people->getTimestamp(),
            );
     
            if (null === ($id = $people->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Inactive'] = isset($data['Inactive']) ? $data['Inactive'] : 'no';
                $data['FirstName'] = isset($data['FirstName']) ? $data['FirstName'] : '';
                $data['LastName'] = isset($data['LastName']) ? $data['LastName'] : '';
                $data['CreationDate'] = isset($data['CreationDate']) ? $data['CreationDate'] : '';
                $data['ResignDate'] = isset($data['ResignDate']) ? $data['ResignDate'] : '';
                $data['MembershipDate'] = isset($data['MembershipDate']) ? $data['MembershipDate'] : '';
                $data['BirthDate'] = isset($data['BirthDate']) ? $data['BirthDate'] : '';
                $data['HouseholdID'] = isset($data['HouseholdID']) ? $data['HouseholdID'] : '';
                $data['Gender'] = isset($data['Gender']) ? $data['Gender'] : '';
                $data['Status'] = isset($data['Status']) ? $data['Status'] : '';
                $data['Photolink'] = isset($data['Photolink']) ? $data['Photolink'] : '';
                $data['PPhone'] = isset($data['PPhone']) ? $data['PPhone'] : '';
                $data['Email'] = isset($data['Email']) ? $data['Email'] : '';
                
                $id = $this->getDbTable()->insert($data);
                $people->setId($id);
            } else {
                $this->getDbTable()->update($data, array('RecordID = ?' => $id));
            }
        }
     
        public function savearray($people)
        {

            $map = array();
            $map['id'] = 'RecordID';
            $map['inactive'] = 'Inactive';
            $map['firstname'] = 'FirstName';
            $map['lastname'] = 'LastName';
            $map['creationdate'] = 'CreationDate';
            $map['resigndate'] = 'ResignDate';
            $map['membershipdate'] = 'MembershipDate';
            $map['birthdate'] = 'BirthDate';
            $map['householdid'] = 'HouseholdID';
            $map['gender'] = 'Gender';
            $map['status'] = 'Status';
            $map['photolink'] = 'Photolink';
            $map['pphone'] = 'PPhone';
            $map['email'] = 'Email';
            $map['timestamp'] = 'Timestamp';

            $data = array(
                'RecordID'  => $people['id'],
                'Inactive'  => $people['inactive'],
                'FirstName'   => $people['firstname'],
                'LastName'   => $people['lastname'],
                'CreationDate'   => $people['creationdate'],
                'ResignDate'   => $people['resigndate'],
                'MembershipDate' => $people['membershipdate'],
                'BirthDate'   => $people['birthdate'],
                'HouseholdID'   => $people['householdid'],
                'Gender'   => $people['gender'],
                'Status'   => $people['status'],
                'Photolink'   => $people['photolink'],
                'PPhone'   => $people['pphone'],
                'Email'   => $people['email'],
                'TimeStamp' => $people['timestamp'],
            );

     
            if (null === ($id = $people['id'])) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Inactive'] = isset($data['Inactive']) ? $data['Inactive'] : 'no';
                $data['FirstName'] = isset($data['FirstName']) ? $data['FirstName'] : '';
                $data['LastName'] = isset($data['LastName']) ? $data['LastName'] : '';
                $data['CreationDate'] = isset($data['CreationDate']) ? $data['CreationDate'] : '';
                $data['ResignDate'] = isset($data['ResignDate']) ? $data['ResignDate'] : '';
                $data['MembershipDate'] = isset($data['MembershipDate']) ? $data['MembershipDate'] : '';
                $data['BirthDate'] = isset($data['BirthDate']) ? $data['BirthDate'] : '';
                $data['HouseholdID'] = isset($data['HouseholdID']) ? $data['HouseholdID'] : '';
                $data['Gender'] = isset($data['Gender']) ? $data['Gender'] : '';
                $data['Status'] = isset($data['Status']) ? $data['Status'] : '';
                $data['Photolink'] = isset($data['Photolink']) ? $data['Photolink'] : '';
                $data['PPhone'] = isset($data['PPhone']) ? $data['PPhone'] : '';
                $data['Email'] = isset($data['Email']) ? $data['Email'] : '';
                
                $id = $this->getDbTable()->insert($data);
                $people['id'] = $id;
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
            $rowarray['firstname'] = $row->FirstName;
            $rowarray['lastname'] = $row->LastName;
            $rowarray['creationdate'] = $row->CreationDate;
            $rowarray['resigndate'] = $row->ResignDate;
            $rowarray['membershipdate'] = $row->MembershipDate;
            $rowarray['birthdate'] = $row->BirthDate;
            $rowarray['householdid'] = $row->HouseholdID;
            $rowarray['gender'] = $row->Gender;
            $rowarray['status'] = $row->Status;
            $rowarray['photolink'] = $row->Photolink;
            $rowarray['pphone'] = $row->PPhone;
            $rowarray['email'] = $row->Email;
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
            $rowarray['firstname'] = $row->FirstName;
            $rowarray['lastname'] = $row->LastName;
            $rowarray['creationdate'] = $row->CreationDate;
            $rowarray['resigndate'] = $row->ResignDate;
            $rowarray['membershipdate'] = $row->MembershipDate;
            $rowarray['birthdate'] = $row->BirthDate;
            $rowarray['householdid'] = $row->HouseholdID;
            $rowarray['gender'] = $row->Gender;
            $rowarray['status'] = $row->Status;
            $rowarray['photolink'] = $row->Photolink;
            $rowarray['pphone'] = $row->PPhone;
            $rowarray['email'] = $row->Email;
            $rowarray['timestamp'] = $row->TimeStamp;

            return ($rowarray);
        }
     
        public function fetchAll($sql)
        {
//echo($sql->__toString()); 
//exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_People();
                $entry->setId($row->RecordID);
                $entry->setInactive($row->Inactive);
                $entry->setFirstname($row->FirstName);
                $entry->setLastname($row->LastName);
                $entry->setCreationdate($row->CreationDate);
                $entry->setResigndate($row->ResignDate);
                $entry->setMembershipdate($row->MembershipDate);
                $entry->setBirthdate($row->BirthDate);
                $entry->setHouseholdid($row->HouseholdID);
                $entry->setGender($row->Gender);
                $entry->setStatus($row->Status);
                $entry->setPhotolink($row->Photolink);
                $entry->setPphone($row->PPhone);
                $entry->setEmail($row->Email);
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

        public function fetchName($firstname, $lastname)
        {
            $sql = $this->getDbTable()->select()
                ->where("FirstName = '$firstname'")
                ->where("LastName = '$lastname'")
                ;
            return $this->fetchAll($sql);

        }

        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['inactive'] = 'Inactive';
            $map['firstname'] = 'FirstName';
            $map['lastname'] = 'LastName';
            $map['creationdate'] = 'CreationDate';
            $map['resigndate'] = 'ResignDate';
            $map['membershipdate'] = 'MembershipDate';
            $map['birthdate'] = 'BirthDate';
            $map['householdid'] = 'HouseholdID';
            $map['gender'] = 'Gender';
            $map['status'] = 'Status';
            $map['photolink'] = 'Photolink';
            $map['pphone'] = 'PPhone';
            $map['email'] = 'Email';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Inactive DESC", "Status", "Lastname", "FirstName"));
            else
            {
                $mapped = array();
                foreach ($order as $item)
                {
                    if ($item == 'creationdate')
                        $mapped[] = $map['creationdate'] . ' DESC';
                    elseif ($item == 'creationdateA')
                        $mapped[] = $map['creationdate'] . ' ASC';
                    else
                        $mapped[] = $map[$item];
                }
                $sql->order($mapped);                 
            }
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