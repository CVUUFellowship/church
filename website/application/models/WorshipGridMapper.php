<?php
// application/models/WorshipGridMapper.php
     
    class Application_Model_WorshipGridMapper
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
                $this->setDbTable('Application_Model_DbTable_WorshipGrid');
            }

            return $this->_dbTable;
        }
     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('RecordID = ?', $id);
            return $this->getDbTable()->delete($where);        
        }
     
        public function save(Application_Model_Worshipgrid $grid)
        {
            $data = array(
                'RecordID'  => $grid->getID(),
                'Sunday'  => $grid->getSunday(),
                'ServiceDate'   => $grid->getServicedate(),
                'ServiceTime'   => $grid->getServicetime(),
                'Presenter'   => $grid->getPresenter(),
                'Topic'   => $grid->getTopic(),
                'Music'   => $grid->getMusic(),
                'SpecialMusic'   => $grid->getSpecialmusic(),
                'Hymns'   => $grid->getHymns(),
                'Early'   => $grid->getEarly(),
                'Late'   => $grid->getLate(),
                'Organizer'   => $grid->getOrganizer(),
                'WorshipAssoc'   => $grid->getWorshipAssoc(),
                'OtherInfo'   => $grid->getOtherinfo(),
                'AttEarly'   => $grid->getAttearly(),
                'AttLate'   => $grid->getAttlate(),
             );
            if (null === ($id = $grid->getId())) {
//echo "new record <br>";
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['Sunday'] = isset($data['Sunday']) ? $data['Sunday'] : 'yes';
                $data['ServiceDate'] = isset($data['ServiceDate']) ? $data['ServiceDate'] : '';
                $data['ServiceTime'] = isset($data['ServiceTime']) ? $data['ServiceTime'] : '';
                $data['Presenter'] = isset($data['Presenter']) ? $data['Presenter'] : '';
                $data['Topic'] = isset($data['Topic']) ? $data['Topic'] : '';
                $data['Music'] = isset($data['Music']) ? $data['Music'] : '';
                $data['SpecialMusic'] = isset($data['SpecialMusic']) ? $data['SpecialMusic'] : '';
                $data['Hymns'] = isset($data['Hymns']) ? $data['Hymns'] : '';
                $data['Early'] = isset($data['Early']) ? $data['Early'] : '';
                $data['Late'] = isset($data['Late']) ? $data['Late'] : '';
                $data['Organizer'] = isset($data['Organizer']) ? $data['Organizer'] : '';
                $data['WorshipAssoc'] = isset($data['WorshipAssoc']) ? $data['WorshipAssoc'] : '';
                $data['OtherInfo'] = isset($data['OtherInfo']) ? $data['OtherInfo'] : '';
                $data['AttEarly'] = isset($data['AttEarly']) ? $data['AttEarly'] : 0;
                $data['AttLate'] = isset($data['AttLate']) ? $data['AttLate'] : 0;
                $id = $this->getDbTable()->insert($data);
//echo "new record id is $id <br>";
                $grid->setId($id);
            } else {
//echo "existing record <br>";
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
            $rowarray['servicedate'] = $row->ServiceDate;
            $rowarray['servicetime'] = $row->ServiceTime;
            $rowarray['presenter'] = $row->Presenter;
            $rowarray['topic'] = $row->Topic;
            $rowarray['music'] = $row->Music;
            $rowarray['specialmusic'] = $row->SpecialMusic;
            $rowarray['hymns'] = $row->Hymns;
            $rowarray['early'] = $row->Early;
            $rowarray['late'] = $row->Late;
            $rowarray['organizer'] = $row->Organizer;
            $rowarray['worshipassoc'] = $row->WorshipAssoc;
            $rowarray['otherinfo'] = $row->OtherInfo;
            $rowarray['attearly'] = $row->AttEarly;
            $rowarray['attlate'] = $row->AttLate;
            $rowarray['early'] = $row->Late;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_WorshipGrid();
                $entry->setId($row->RecordID);
                $entry->setSunday($row->Sunday);
                $entry->setServicedate($row->ServiceDate);
                $entry->setServicetime($row->ServiceTime);
                $entry->setPresenter($row->Presenter);
                $entry->setTopic($row->Topic);
                $entry->setMusic($row->Music);
                $entry->setSpecialMusic($row->SpecialMusic);
                $entry->setHymns($row->Hymns);
                $entry->setEarly($row->Early);
                $entry->setLate($row->Late);
                $entry->setOrganizer($row->Organizer);
                $entry->setWorshipassoc($row->WorshipAssoc);
                $entry->setOtherinfo($row->OtherInfo);
                $entry->setAttearly($row->AttEarly);
                $entry->setAttlate($row->AttLate);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['sunday'] = 'Sunday';
            $map['servicedate'] = 'ServiceDate';
            $map['servicetime'] = 'ServiceTime';
            $map['presenter'] = 'Presenter';
            $map['topic'] = 'Topic';
            $map['music'] = 'Music';
            $map['specialmusic'] = 'SpecialMusic';
            $map['hymns'] = 'Hymns';
            $map['early'] = 'Early';
            $map['late'] = 'Late';
            $map['organizer'] = 'Organizer';
            $map['worshipassoc'] = 'WorshipAssoc';
            $map['otherinfo'] = 'Otherinfo';
            $map['attearly'] = 'AttEarly';
            $map['attlate'] = 'AttLate';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("ServiceDate", "ServiceTime"));
            else
                $sql->order($order);                 
            foreach ($where as $cond) 
            {
                if (($cond[1] == ' IN ') || ($cond[1] == ' BETWEEN '))
                    $str = $map[$cond[0]].$cond[1].$cond[2];
                else
                    $str = $map[$cond[0]].$cond[1]."\"".$cond[2]."\"";
                $sql->where("$str");
            }
            return $this->fetchAll($sql);       
        }



    }
?>