<?php
// application/models/CalAccessUserMapper.php
     
    class Application_Model_CalAccessUserMapper
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
                $this->setDbTable('Application_Model_DbTable_CalAccessUser');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalAccessUser $entry, $new = 'old')
        {
            $data = array(
                'cal_login'  => $entry->getID(),
                'cal_other_user'   => $entry->getOtheruser(),
                'cal_can_view'   => $entry->getCanview(),
                'cal_can_edit'   => $entry->getCanedit(),
                'cal_can_approve'  => $entry->getCanapprove(),
                'cal_can_invite'   => $entry->getCaninvite(),
                'cal_can_email'   => $entry->getCanemail(),
                'cal_can_see_time_only'   => $entry->getCanseetimeonly(),
            );

            if ($new == 'new') {
                $id = $entry->getID();
//echo "new entry $id"; 
                /* handle non-null requirements */
                $data['cal_can_view'] = isset($data['cal_can_view']) ? $data['cal_can_view'] : 0;
                $data['cal_can_edit'] = isset($data['cal_edit']) ? $data['cal_edit'] : 0;
                $data['cal_can_approve'] = isset($data['cal_approve']) ? $data['cal_approve'] : 0;
                $data['cal_can_invite'] = isset($data['cal_invite']) ? $data['cal_invite'] : 'Y';
                $data['cal_can_email'] = isset($data['cal_email']) ? $data['cal_email'] : 'Y';
                $data['cal_see_time_only'] = isset($data['cal_see_time_only']) ? $data['cal_see_time_only'] : 'N';
                $this->getDbTable()->insert($data);

            } else {
                $id = $entry->getID();
//echo "existing entry $id"; 
//var_dump($data);
                $this->getDbTable()->update($data, array('cal_login = ?' => $id));
            }
        }
        
                
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); 
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_CalAccessUser();
                $entry->setLogin($row->cal_login);
                $entry->setOtheruser($row->cal_other_user);
                $entry->setCanview($row->cal_can_view);
                $entry->setCanedit($row->cal_can_edit);
                $entry->setCanapprove($row->cal_can_approve);
                $entry->setCaninvite($row->cal_can_invite);
                $entry->setCanemail($row->cal_can_email);
                $entry->setSeetimeonly($row->cal_see_time_only);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['login'] = 'cal_login';
            $map['otheruser'] = 'cal_other_user';
            $map['canview'] = 'cal_can_view';
            $map['canedit'] = 'cal_can_edit';
            $map['canapprove'] = 'cal_can_approve';
            $map['caninvite'] = 'cal_can_invite';
            $map['canemail'] = 'cal_can_email';
            $map['seetimeonly'] = 'cal_see_time_only';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("cal_login"));
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

     
        public function fetchCol($column, $sql)
        {
            $resultSet = $this->getDbTable()->fetchAll($sql);
//var_dump($resultSet); exit;
            $entries   = array();
            foreach ($resultSet as $row) {
                $entries[] = $row;
            }
            return $entries;
        }


        public function fetchColumn(array $where, $column, $distinct = false)
        {
            $map = array();
            $map['login'] = 'cal_login';
            $map['otheruser'] = 'cal_other_user';
            $map['canview'] = 'cal_can_view';
            $map['canedit'] = 'cal_can_edit';
            $map['canapprove'] = 'cal_can_approve';
            $map['caninvite'] = 'cal_can_invite';
            $map['canemail'] = 'cal_can_email';
            $map['seetimeonly'] = 'cal_see_time_only';
            
            $sql = $this->getDbTable()->select();
            if ($distinct)
            {
                $sql->distinct();             
            }
            $sql->from(array('webcal_access_user'),
                        $column
                        );
            foreach ($where as $cond) 
            {
                $str = $map[$cond[0]].$cond[1]."\"".$cond[2]."\"";
                $sql->where("$str");
            }
            return $this->fetchCol($column, $sql);       
        }
     

    }
?>