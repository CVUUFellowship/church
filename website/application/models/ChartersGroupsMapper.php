<?php
// application/models/ChartersGroupsMapper.php
     
    class Application_Model_ChartersGroupsMapper
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
                $this->setDbTable('Application_Model_DbTable_ChartersGroups');
            }

            return $this->_dbTable;
        }
     


        public function save(Application_Model_ChartersGroups $people)
        {
            $data = array(
                'RecordID'  => $people->getID(),
                'GroupName'  => $people->getGroupname(),
                'GroupType'   => $people->getGrouptype(),
                'Purpose'   => $people->getPurpose(),
                'InclusivePolicy'   => $people->getInclusivepolicy(),
                'ConfidentialityPolicy'   => $people->getConfidentialitypolicy(),
                'NumberMembers' => $people->getNumbermembers(),
                'MeetingLocation'   => $people->getMeetinglocation(),
                'Meetings'   => $people->getMeetings(),
                'NonCVUUFPolicy'   => $people->getNoncvuufpolicy(),
                'ApprovalDate'   => $people->getApprovaldate(),
                'Timestamp' => $people->getTimestamp(),
            );
     
            if (null === ($id = $people->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['GroupName'] = isset($data['GroupName']) ? $data['GroupName'] : 'no';
                $data['GroupType'] = isset($data['GroupType']) ? $data['GroupType'] : '';
                $data['Purpose'] = isset($data['Purpose']) ? $data['Purpose'] : '';
                $data['InclusivePolicy'] = isset($data['InclusivePolicy']) ? $data['InclusivePolicy'] : '';
                $data['ConfidentialityPolicy'] = isset($data['ConfidentialityPolicy']) ? $data['ConfidentialityPolicy'] : '';
                $data['NumberMembers'] = isset($data['NumberMembers']) ? $data['NumberMembers'] : '';
                $data['MeetingLocation'] = isset($data['MeetingLocation']) ? $data['MeetingLocation'] : '';
                $data['Meetings'] = isset($data['Meetings']) ? $data['Meetings'] : '';
                $data['NonCVUUFPolicy'] = isset($data['NonCVUUFPolicy']) ? $data['NonCVUUFPolicy'] : '';
                $data['ApprovalDate'] = isset($data['ApprovalDate']) ? $data['ApprovalDate'] : '';
                
                $id = $this->getDbTable()->insert($data);
                $people->setId($id);
            } else {
                $this->getDbTable()->update($data, array('RecordID = ?' => $id));
            }
        }
     
     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('RecordID = ?', $id);
            return $this->getDbTable()->delete($where);        
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
            $rowarray['groupname'] = $row->GroupName;
            $rowarray['grouptype'] = $row->GroupType;
            $rowarray['purpose'] = $row->Purpose;
            $rowarray['inclusivepolicy'] = $row->InclusivePolicy;
            $rowarray['confidentialitypolicy'] = $row->ConfidentialityPolicy;
            $rowarray['numbermembers'] = $row->NumberMembers;
            $rowarray['meetinglocation'] = $row->MeetingLocation;
            $rowarray['meetings'] = $row->Meetings;
            $rowarray['noncvuufpolicy'] = $row->NonCVUUFPolicy;
            $rowarray['approvaldate'] = $row->ApprovalDate;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
     
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_ChartersGroups();
                $entry->setId($row->RecordID);
                $entry->setGroupname($row->GroupName);
                $entry->setGrouptype($row->GroupType);
                $entry->setPurpose($row->Purpose);
                $entry->setInclusivepolicy($row->InclusivePolicy);
                $entry->setConfidentialitypolicy($row->ConfidentialityPolicy);
                $entry->setNumbermembers($row->NumberMembers);
                $entry->setMeetinglocation($row->MeetingLocation);
                $entry->setMeetings($row->Meetings);
                $entry->setNoncvuufpolicy($row->NonCVUUFPolicy);
                $entry->setApprovaldate($row->ApprovalDate);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['groupname'] = 'GroupName';
            $map['grouptype'] = 'GroupType';
            $map['purpose'] = 'Purpose';
            $map['inclusivepolicy'] = 'InclusivePolicy';
            $map['confidentialitypolicy'] = 'ConfidentialityPolicy';
            $map['numbermembers'] = 'NumberMembers';
            $map['meetinglocation'] = 'MeetingLocation';
            $map['meetings'] = 'Meetings';
            $map['noncvuufpolicy'] = 'NonCVUUFPolicy';
            $map['approvaldate'] = 'ApprovalDate';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();

            if (null === $order)
                $sql->order(array("GroupName"));
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