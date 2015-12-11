<?php
// application/models/ChartersOrgMapper.php
     
    class Application_Model_ChartersOrgMapper
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
                $this->setDbTable('Application_Model_DbTable_ChartersOrg');
            }

            return $this->_dbTable;
        }
     


        public function save(Application_Model_ChartersOrg $people)
        {
            $data = array(
                'RecordID'  => $people->getID(),
                'Name'  => $people->getName(),
                'Type'   => $people->getType(),
                'Purpose'   => $people->getPurpose(),
                'Organization'   => $people->getOrganization(),
                'LeaderSelection'   => $people->getLeaderselection(),
                'LeaderTerm'   => $people->getLeaderterm(),
                'MemberTerm'   => $people->getMemberterm(),
                'NumberMembers' => $people->getNumbermembers(),
                'ReportTo'   => $people->getReportTo(),
                'Meetings'   => $people->getMeetings(),
                'Duties'   => $people->getDuties(),
                'ApprovalDate'   => $people->getApprovaldate(),
                'Timestamp' => $people->getTimestamp(),
            );

            if (!isset($data['Timestamp'])) {
                $data['Timestamp'] = date('Y-m-d H:i:s', time());
            }
     
            if (null === ($id = $people->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Name'] = isset($data['Name']) ? $data['Name'] : 'no';
                $data['Type'] = isset($data['Type']) ? $data['Type'] : '';
                $data['Purpose'] = isset($data['Purpose']) ? $data['Purpose'] : '';
                $data['Organization'] = isset($data['Organization']) ? $data['Organization'] : '';
                $data['LeaderSelection'] = isset($data['LeaderSelection']) ? $data['LeaderSelection'] : '';
                $data['LeaderTerm'] = isset($data['LeaderTerm']) ? $data['LeaderTerm'] : '';
                $data['MemberTerm'] = isset($data['MemberTerm']) ? $data['MemberTerm'] : '';
                $data['NumberMembers'] = isset($data['NumberMembers']) ? $data['NumberMembers'] : '';
                $data['ReportTo'] = isset($data['ReportTo']) ? $data['ReportTo'] : '';
                $data['Meetings'] = isset($data['Meetings']) ? $data['Meetings'] : '';
                $data['Duties'] = isset($data['Duties']) ? $data['Duties'] : '';
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
            $rowarray['name'] = $row->Name;
            $rowarray['type'] = $row->Type;
            $rowarray['purpose'] = $row->Purpose;
            $rowarray['organization'] = $row->Organization;
            $rowarray['leaderselection'] = $row->LeaderSelection;
            $rowarray['leaderterm'] = $row->LeaderTerm;
            $rowarray['memberterm'] = $row->MemberTerm;
            $rowarray['numbermembers'] = $row->NumberMembers;
            $rowarray['reportto'] = $row->ReportTo;
            $rowarray['meetings'] = $row->Meetings;
            $rowarray['duties'] = $row->Duties;
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
                $entry = new Application_Model_ChartersOrg();
                $entry->setId($row->RecordID);
                $entry->setName($row->Name);
                $entry->setType($row->Type);
                $entry->setPurpose($row->Purpose);
                $entry->setOrganization($row->Organization);
                $entry->setLeaderselection($row->LeaderSelection);
                $entry->setLeaderterm($row->LeaderTerm);
                $entry->setMemberterm($row->MemberTerm);
                $entry->setNumbermembers($row->NumberMembers);
                $entry->setReportTo($row->ReportTo);
                $entry->setMeetings($row->Meetings);
                $entry->setDuties($row->Duties);
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
            $map['name'] = 'Name';
            $map['type'] = 'Type';
            $map['purpose'] = 'Purpose';
            $map['organization'] = 'Organization';
            $map['leaderselection'] = 'LeaderSelection';
            $map['leaderterm'] = 'LeaderTerm';
            $map['memberterm'] = 'MemberTerm';
            $map['numbermembers'] = 'NumberMembers';
            $map['reportto'] = 'ReportTo';
            $map['meetings'] = 'Meetings';
            $map['duties'] = 'Duties';
            $map['approvaldate'] = 'ApprovalDate';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();

            if (null === $order)
                $sql->order(array("Type"));
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
