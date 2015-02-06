<?php
// application/models/AnnouncementsMapper.php
     
    class Application_Model_AnnouncementsMapper
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
                $this->setDbTable('Application_Model_DbTable_Announcements');
            }

            return $this->_dbTable;
        }
     


        public function save(Application_Model_Announcements $cat)
        {
            $data = array(
                'RecordID'  => $cat->getId(),
                'Date'  => $cat->getDate(),
                'Xdate'   => $cat->getXdate(),
                'Time'   => $cat->getTime(),
                'Place'   => $cat->getPlace(),
                'Contact'   => $cat->getContact(),
                'Title' => $cat->getTitle(),
                'Description'   => $cat->getDescription(),
                'Link'   => $cat->getLink(),
                'LinkText'   => $cat->getLinktext(),
                'Owner'   => $cat->getOwner(),
                'Type'   => $cat->getType(),
                'Status'   => $cat->getStatus(),
                'Timestamp' => $cat->getTimestamp(),
            );
     
            if (null === ($number = $cat->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
//                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Date'] = isset($data['Date']) ? $data['Date'] : '';
                $data['Xdate'] = isset($data['Xdate']) ? $data['Xdate'] : '';
                $data['Time'] = isset($data['Time']) ? $data['Time'] : '';
                $data['Place'] = isset($data['Place']) ? $data['Place'] : '';
                $data['Contact'] = isset($data['Contact']) ? $data['Contact'] : '';
                $data['Title'] = isset($data['Title']) ? $data['Title'] : '';
                $data['Description'] = isset($data['Description']) ? $data['Description'] : '';
                $data['Link'] = isset($data['Link']) ? $data['Link'] : '';
                $data['LinkText'] = isset($data['LinkText']) ? $data['LinkText'] : '';
                $data['Owner'] = isset($data['Owner']) ? $data['Owner'] : '0000-00-00';
                $data['Type'] = isset($data['Type']) ? $data['Type'] : '';
                $data['Status'] = isset($data['Status']) ? $data['Status'] : '';
                $number = $this->getDbTable()->insert($data);
                $cat->setId($number);
            } else {
                $this->getDbTable()->update($data, array('RecordID = ?' => $number));
            }
        }
     
     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('RecordID = ?', $id);
            return $this->getDbTable()->delete($where);        
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
            $rowarray['date'] = $row->Date;
            $rowarray['xdate'] = $row->Xdate;
            $rowarray['time'] = $row->Time;
            $rowarray['place'] = $row->Place;
            $rowarray['contact'] = $row->Contact;
            $rowarray['title'] = $row->Title;
            $rowarray['description'] = $row->Description;
            $rowarray['link'] = $row->Link;
            $rowarray['linktext'] = $row->LinkText;
            $rowarray['owner'] = $row->Owner;
            $rowarray['type'] = $row->Type;
            $rowarray['status'] = $row->Status;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
     
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Announcements();
                $entry->setId($row->RecordID);
                $entry->setDate($row->Date);
                $entry->setXdate($row->Xdate);
                $entry->setTime($row->Time);
                $entry->setPlace($row->Place);
                $entry->setContact($row->Contact);
                $entry->setTitle($row->Title);
                $entry->setDescription($row->Description);
                $entry->setLink($row->Link);
                $entry->setLinkText($row->LinkText);
                $entry->setOwner($row->Owner);
                $entry->setType($row->Type);
                $entry->setStatus($row->Status);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }



        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['date'] = 'Date';
            $map['xdate'] = 'Xdate';
            $map['time'] = 'Time';
            $map['place'] = 'Place';
            $map['contact'] = 'Contact';
            $map['title'] = 'Title';
            $map['description'] = 'Description';
            $map['link'] = 'Link';
            $map['linktext'] = 'LinkText';
            $map['owner'] = 'RecordID';
            $map['type'] = 'Type';
            $map['status'] = 'Status';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("Date"));
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



    }
?>