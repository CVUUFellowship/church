<?php
// application/models/PoliciesMapper.php
     
    class Application_Model_PoliciesMapper
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
                $this->setDbTable('Application_Model_DbTable_Policies');
            }

            return $this->_dbTable;
        }
     


        public function save(Application_Model_Policies $people)
        {
            $data = array(
                'RecordID'  => $people->getID(),
                'Number'  => $people->getNumber(),
                'Status'  => $people->getStatus(),
                'BelowPolicy'   => $people->getBelowpolicy(),
                'PolicyType'   => $people->getPolicytype(),
                'Level'   => $people->getLevel(),
                'Name'   => $people->getName(),
                'Description'   => $people->getDescription(),
                'Revision' => $people->getRevision(),
                'SubmitDate'   => $people->getSubmitDate(),
                'ApprovalDate'   => $people->getApprovaldate(),
                'PDFFile'   => $people->getPDFFile(),
                'RTFFile'   => $people->getRTFFile(),
                'Times' => $people->getTimestamp(),
            );

            if (!isset($data['Times'])) {
                $data['Times'] = date('Y-m-d H:i:s', time());
            }
     
            if (null === ($id = $people->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Number'] = isset($data['Number']) ? $data['Number'] : 0;
                $data['Status'] = isset($data['Status']) ? $data['Status'] : '';
                $data['BelowPolicy'] = isset($data['BelowPolicy']) ? $data['BelowPolicy'] : 0;
                $data['PolicyType'] = isset($data['PolicyType']) ? $data['PolicyType'] : '';
                $data['Level'] = isset($data['Level']) ? $data['Level'] : 0;
                $data['Name'] = isset($data['Name']) ? $data['Name'] : '';
                $data['Description'] = isset($data['Description']) ? $data['Description'] : '';
                $data['SubmitDate'] = isset($data['SubmitDate']) ? $data['SubmitDate'] : '';
                $data['Revision'] = isset($data['Revision']) ? $data['Revision'] : 0;
                $data['SubmitDate'] = isset($data['SubmitDate']) ? $data['SubmitDate'] : '';
                $data['PDFFile'] = isset($data['PDFFile']) ? $data['PDFFile'] : '';
                $data['RTFFile'] = isset($data['RTFFile']) ? $data['RTFFile'] : '';
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
            $rowarray['number'] = $row->Number;
            $rowarray['status'] = $row->Status;
            $rowarray['belowpolicy'] = $row->BelowPolicy;
            $rowarray['policytype'] = $row->PolicyType;
            $rowarray['level'] = $row->Level;
            $rowarray['name'] = $row->Name;
            $rowarray['description'] = $row->Description;
            $rowarray['submitdate'] = $row->SubmitDate;
            $rowarray['approvaldate'] = $row->ApprovalDate;
            $rowarray['revision'] = $row->Revision;
            $rowarray['pdffile'] = $row->PDFFile;
            $rowarray['rtffile'] = $row->RTFFile;
            $rowarray['timestamp'] = $row->Times;

            return ($rowarray);
        }
     
     
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Policies();
                $entry->setId($row->RecordID);
                $entry->setNumber($row->Number);
                $entry->setStatus($row->Status);
                $entry->setBelowpolicy($row->BelowPolicy);
                $entry->setPolicytype($row->PolicyType);
                $entry->setLevel($row->Level);
                $entry->setName($row->Name);
                $entry->setDescription($row->Description);
                $entry->setSubmitdate($row->SubmitDate);
                $entry->setRevision($row->Revision);
                $entry->setSubmitdate($row->SubmitDate);
                $entry->setPdffile($row->PDFFile);
                $entry->setRtffile($row->RTFFile);
                $entry->setApprovaldate($row->ApprovalDate);
                $entry->setTimestamp($row->Times);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['number'] = 'Number';
            $map['status'] = 'Status';
            $map['belowpolicy'] = 'BelowPolicy';
            $map['policytype'] = 'PolicyType';
            $map['level'] = 'Level';
            $map['name'] = 'Name';
            $map['description'] = 'Description';
            $map['revision'] = 'Revision';
            $map['submitdate'] = 'SubmitDate';
            $map['approvaldate'] = 'ApprovalDate';
            $map['pdffile'] = 'PDFFile';
            $map['rtffile'] = 'RTFFile';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();

            if (null === $order)
                $sql->order(array("Number", "Revision"));
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
