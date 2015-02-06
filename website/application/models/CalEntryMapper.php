<?php
// application/models/CalEntryMapper.php
     
    class Application_Model_CalEntryMapper
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
                $this->setDbTable('Application_Model_DbTable_CalEntry');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalEntry $entry)
        {
            $data = array(
                'cal_id'  => $entry->getID(),
                'cal_group_id'  => $entry->getGroupid(),
                'cal_ext_for_id'   => $entry->getExtforid(),
                'cal_create_by'   => $entry->getCreateby(),
                'cal_date'   => $entry->getDate(),
                'cal_time'   => $entry->getTime(),
                'cal_mod_date'   => $entry->getModdate(),
                'cal_mod_time'   => $entry->getModtime(),
                'cal_duration'   => $entry->getDuration(),
                'cal_due_date'   => $entry->getDuedate(),
                'cal_due_time'   => $entry->getDuetime(),
                'cal_priority'   => $entry->getPriority(),
                'cal_type'   => $entry->getType(),
                'cal_access'   => $entry->getAccess(),
                'cal_name'   => $entry->getName(),
                'cal_location'   => $entry->getLocation(),
                'cal_url'   => $entry->getUrl(),
                'cal_completed'   => $entry->getCompleted(),
                'cal_description'   => $entry->getDescription(),

            );

            if (null === $entry->getCreateby()) {
                $id = $entry->getID();
//echo "a new entry of $id <br>"; 
                /* handle non-null requirements */
                $data['cal_create_by'] = isset($data['cal_create_by']) ? $data['cal_create_by'] : '';
                $data['cal_date'] = isset($data['cal_date']) ? $data['cal_date'] : 0;
                $data['cal_duration'] = isset($data['cal_duration']) ? $data['cal_duration'] : 0;
                $data['cal_priority'] = isset($data['cal_priority']) ? $data['cal_priority'] : 5;
                $data['cal_type'] = isset($data['cal_type']) ? $data['cal_type'] : 'E';
                $data['cal_access'] = isset($data['cal_access']) ? $data['cal_access'] : 'P';
                $data['cal_name'] = isset($data['cal_name']) ? $data['cal_name'] : '';
                $this->getDbTable()->insert($data);
//var_dump($data);

            } else {
                $id = $entry->getID();
//echo "existing entry $id <br>"; 
//var_dump($data);
                $this->getDbTable()->update($data, array('cal_id = ?' => $id));
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
            $rowarray['id'] = $row->cal_id;
            $rowarray['groupid'] = $row->cal_group_id;
            $rowarray['extforid'] = $row->cal_ext_for_id;
            $rowarray['createby'] = $row->cal_create_by;
            $rowarray['date'] = $row->cal_date;
            $rowarray['time'] = $row->cal_time;
            $rowarray['moddate'] = $row->cal_mod_date;
            $rowarray['modtime'] = $row->cal_mod_time;
            $rowarray['duration'] = $row->cal_duration;
            $rowarray['duedate'] = $row->cal_due_date;
            $rowarray['duetime'] = $row->cal_due_time;
            $rowarray['priority'] = $row->cal_priority;
            $rowarray['type'] = $row->cal_type;
            $rowarray['access'] = $row->cal_access;
            $rowarray['name'] = $row->cal_name;
            $rowarray['location'] = $row->cal_location;
            $rowarray['url'] = $row->cal_url;
            $rowarray['completed'] = $row->cal_completed;
            $rowarray['description'] = $row->cal_description;
            return ($rowarray);
        }


        public function max()
        {
            $sql = $this->getDbTable()->select();
            $sql->from(array('webcal_entry'),
                        'MAX(cal_id) AS id'
                        );
            $sql->setIntegrityCheck(false);
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $resultRow = current($resultSet);
            $result = $resultRow[0]['id'];
            return($result);
        }


    }
?>