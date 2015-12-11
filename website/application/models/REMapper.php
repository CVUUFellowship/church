<?php
// application/models/REMapper.php
     
    class Application_Model_REMapper
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
                $this->setDbTable('Application_Model_DbTable_RE');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_RE $re)
        {
            $data = array(
                'RecordID'  => $re->getID(),
                'Inactive'  => $re->getInactive(),
                'Registered'   => $re->getRegistered(),
                'Status'   => $re->getStatus(),
                'Class'   => $re->getClass(),
                'ChildID' => $re->getChildid(),
                'Birth'   => $re->getBirth(),
                'Grade'   => $re->getGrade(),
                'Gender'   => $re->getGender(),
                'PPersonID'   => $re->getPpersonid(),
                'APersonID'   => $re->getApersonid(),
                'Allergies'   => $re->getAllergies(),
                'FoodAllergies'   => $re->getFoodallergies(),
                'AllergyMeds'   => $re->getAllergymeds(),
                'Health'   => $re->getHealth(),
                'BehavIssues'   => $re->getBehavissues(),
                'DevelIssues'   => $re->getDevelissues(),
                'LangIssues'   => $re->getLangissues(),
                'OtherIssues'   => $re->getOtherissues(),
                'Medications'   => $re->getMedications(),
                'Characteristics'   => $re->getCharacteristics(),
                'OtherText'   => $re->getOthertext(),
                'Receive'   => $re->getReceive(),
                'Discuss'   => $re->getDiscuss(),
                'Insurance'   => $re->getInsurance(),
                'InsNumber'   => $re->getInsnumber(),
                'SigName'   => $re->getSigname(),
            );
     
            if (null === ($id = $re->getId())) {
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Inactive'] = isset($data['Inactive']) ? $data['Inactive'] : 'no';
                $data['Registered'] = isset($data['Registered']) ? $data['Registered'] : '';
                $data['Status'] = isset($data['Status']) ? $data['Status'] : '';
                $data['Class'] = isset($data['Class']) ? $data['Class'] : '';
                $data['ChildID'] = isset($data['ChildID']) ? $data['ChildID'] : 0;
                $data['Birth'] = isset($data['Birth']) ? $data['Birth'] : '';
                $data['Grade'] = isset($data['Grade']) ? $data['Grade'] : '';
                $data['Gender'] = isset($data['Gender']) ? $data['Gender'] : '';
                $data['PPersonID'] = isset($data['PPersonID']) ? $data['PPersonID'] : 0;
                $data['APersonID'] = isset($data['APersonID']) ? $data['APersonID'] : 0;
                $data['Allergies'] = isset($data['Allergies']) ? $data['Allergies'] : '';
                $data['FoodAllergies'] = isset($data['FoodAllergies']) ? $data['FoodAllergies'] : '';
                $data['AllergyMeds'] = isset($data['AllergyMeds']) ? $data['AllergyMeds'] : '';
                $data['Health'] = isset($data['Health']) ? $data['Health'] : '';
                $data['BehavIssues'] = isset($data['BehavIssues']) ? $data['BehavIssues'] : '';
                $data['DevelIssues'] = isset($data['DevelIssues']) ? $data['DevelIssues'] : '';
                $data['LangIssues'] = isset($data['LangIssues']) ? $data['LangIssues'] : '';
                $data['OtherIssues'] = isset($data['OtherIssues']) ? $data['OtherIssues'] : '';
                $data['Medications'] = isset($data['Medications']) ? $data['Medications'] : '';
                $data['Characteristics'] = isset($data['Characteristics']) ? $data['Characteristics'] : '';
                $data['OtherText'] = isset($data['OtherText']) ? $data['OtherText'] : '';
                $data['Receive'] = isset($data['Receive']) ? $data['Receive'] : '';
                $data['Discuss'] = isset($data['Discuss']) ? $data['Discuss'] : '';
                $data['Insurance'] = isset($data['Insurance']) ? $data['Insurance'] : '';
                $data['InsNumber'] = isset($data['InsNumber']) ? $data['InsNumber'] : '';
                $data['SigName'] = isset($data['SigName']) ? $data['SigName'] : '';
                $id = $this->getDbTable()->insert($data);
                $re->setId($id);
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
            $rowarray['registered'] = $row->Registered;
            $rowarray['status'] = $row->Status;
            $rowarray['class'] = $row->Class;
            $rowarray['childid'] = $row->ChildID;
            $rowarray['birth'] = $row->Birth;
            $rowarray['grade'] = $row->Grade;
            $rowarray['gender'] = $row->Gender;
            $rowarray['ppersonid'] = $row->PPersonID;
            $rowarray['apersonid'] = $row->APersonID;
            $rowarray['allergies'] = $row->Allergies;
            $rowarray['foodallergies'] = $row->FoodAllergies;
            $rowarray['allergymeds'] = $row->AllergyMeds;
            $rowarray['health'] = $row->Health;
            $rowarray['behavissues'] = $row->BehavIssues;
            $rowarray['develissues'] = $row->DevelIssues;
            $rowarray['langissues'] = $row->LangIssues;
            $rowarray['otherissues'] = $row->OtherIssues;
            $rowarray['medications'] = $row->Medications;
            $rowarray['characteristics'] = $row->Characteristics;
            $rowarray['othertext'] = $row->OtherText;
            $rowarray['receive'] = $row->Receive;
            $rowarray['discuss'] = $row->Discuss;
            $rowarray['insurance'] = $row->Insurance;
            $rowarray['insnumber'] = $row->InsNumber;
            $rowarray['signame'] = $row->SigName;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
     
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_RE();
                $entry->setId($row->RecordID);
                $entry->setInactive($row->Inactive);
                $entry->setRegistered($row->Registered);
                $entry->setClass($row->Class);
                $entry->setChildID($row->ChildID);
                $entry->setBirth($row->Birth);
                $entry->setGrade($row->Grade);
                $entry->setGender($row->Gender);
                $entry->setPpersonid($row->PPersonID);
                $entry->setApersonid($row->APersonID);
                $entry->setAllergies($row->Allergies);
                $entry->setFoodallergies($row->FoodAllergies);
                $entry->setAllergymeds($row->AllergyMeds);
                $entry->setHealth($row->Health);
                $entry->setBehavissues($row->BehavIssues);
                $entry->setDevelissues($row->DevelIssues);
                $entry->setLangissues($row->LangIssues);
                $entry->setOtherissues($row->OtherIssues);
                $entry->setMedications($row->Medications);
                $entry->setCharacteristics($row->Characteristics);
                $entry->setOthertext($row->OtherText);
                $entry->setReceive($row->Receive);
                $entry->setDiscuss($row->Discuss);
                $entry->setInsurance($row->Insurance);
                $entry->setInsnumber($row->InsNumber);
                $entry->setSigname($row->SigName);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['inactive'] = 'Inactive';
            $map['registered'] = 'Registered';
            $map['status'] = 'Status';
            $map['class'] = 'Class';
            $map['childid'] = 'ChildID';
            $map['birth'] = 'Birth';
            $map['grade'] = 'Grade';
            $map['gender'] = 'Gender';
            $map['ppersonid'] = 'PPersonID';
            $map['apersonid'] = 'APersonID';
            $map['allergies'] = 'Allergies';
            $map['foodallergies'] = 'FoodAllergies';
            $map['allergymeds'] = 'AllergyMeds';
            $map['health'] = 'Health';
            $map['behavissues'] = 'BehavIssues';
            $map['develissues'] = 'DevelIssues';
            $map['langissues'] = 'LangIssues';
            $map['otherissues'] = 'OtherIssues';
            $map['medications'] = 'Medications';
            $map['characteristics'] = 'Characteristics';
            $map['othertext'] = 'OtherText';
            $map['receive'] = 'Receive';
            $map['discuss'] = 'Discuss';
            $map['insurance'] = 'Insurance';
            $map['insnumber'] = 'InsNumber';
            $map['signame'] = 'SigName';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("RecordID"));
            else
                $sql->order($order);                 
            foreach ($where as $cond) 
            {
                if ($cond[1] == ' IN ')
                    $str = $map[$cond[0]].$cond[1].$cond[2];
                else
                    $str = $map[$cond[0]].$cond[1]."'".$cond[2]."'";
                $sql->where("$str");
            }
            return $this->fetchAll($sql);       
        }



        public function fetchOrWhere(array $where)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['inactive'] = 'Inactive';
            $map['registered'] = 'Registered';
            $map['status'] = 'Status';
            $map['class'] = 'Class';
            $map['childid'] = 'ChildID';
            $map['birth'] = 'Birth';
            $map['grade'] = 'Grade';
            $map['gender'] = 'Gender';
            $map['ppersonid'] = 'PPersonID';
            $map['apersonid'] = 'APersonID';
            $map['allergies'] = 'Allergies';
            $map['foodallergies'] = 'FoodAllergies';
            $map['allergymeds'] = 'AllergyMeds';
            $map['health'] = 'Health';
            $map['behavissues'] = 'BehavIssues';
            $map['develissues'] = 'DevelIssues';
            $map['langissues'] = 'LangIssues';
            $map['otherissues'] = 'OtherIssues';
            $map['medications'] = 'Medications';
            $map['characteristics'] = 'Characteristics';
            $map['othertext'] = 'OtherText';
            $map['receive'] = 'Receive';
            $map['discuss'] = 'Discuss';
            $map['insurance'] = 'Insurance';
            $map['insnumber'] = 'InsNumber';
            $map['signame'] = 'SigName';
            $map['timestamp'] = 'Timestamp';
              
            $sql = $this->getDbTable()->select();
            $sql->order(array("RecordID"));
            foreach ($where as $cond) 
            {
                $str = $map[$cond[0]].$cond[1]."'".$cond[2]."'";
                $sql->orwhere("$str");
            }
            return $this->fetchAll($sql);       
        }


    }
?>
