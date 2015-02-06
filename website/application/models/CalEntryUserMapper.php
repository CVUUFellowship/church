<?php
// application/models/CalentryuserMapper.php
     
    class Application_Model_CalEntryUserMapper
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
                $this->setDbTable('Application_Model_DbTable_CalEntryUser');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalEntryUser $user, $new = 'old')
        {
            $data = array(
                'cal_id'  => $user->getID(),
                'cal_login'  => $user->getLogin(),
                'cal_status'   => $user->getStatus(),
                'cal_category'   => $user->getCategory(),
                'cal_percent'   => $user->getPercent(),
            );
     
            if ($new == 'new') {
                /* handle non-null requirements */
                $data['cal_status'] = isset($data['cal_status']) ? $data['cal_status'] : 'A';
                $data['cal_percent'] = isset($data['cal_percent']) ? $data['cal_percent'] : 0;
                $this->getDbTable()->insert($data);
            }
            else {
                $id = $user->getID();
                $login = $user->getLogin();
                $this->getDbTable()->update($data, array('cal_id = ?' => $id, 'cal_login = ?' => $login));
            }
        }
        
        
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); 
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_CalEntryUser();
                $entry->setId($row->cal_id);
                $entry->setLogin($row->cal_login);
                $entry->setStatus($row->cal_status);
                $entry->setCategory($row->cal_category);
                $entry->setPercent($row->cal_percent);
                $entries[] = $entry;
            }
            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'cal_id';
            $map['login'] = 'cal_login';
            $map['status'] = 'cal_status';
            $map['category'] = 'cal_category';
            $map['percent'] = 'cal_percent';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("cal_id"));
            else
                $sql->order($order);
            
            if (gettype($where[0]) == "array")
            {
                foreach ($where as $cond) 
                {
                    if ($cond[1] == ' IN ')
                        $str = $map[$cond[0]].$cond[1].$cond[2];
                    else
                        $str = $map[$cond[0]].$cond[1]."\"".$cond[2]."\"";
                    $sql->where("$str");
                }
            }
            else
            {
                if ($where[0] == 'OR')  /* special case used only in CalendarController */
                {
//var_dump($where); 
                    $str1 = $map[$where[1][0]].$where[1][1]."'".$where[1][2]."'";
                    $str2 = $map[$where[2][0]].$where[2][1]."'".$where[2][2]."'";
                    $str = "(" . $str1 . ")" . " OR (" . $str2 . ")";
                    $sql->where("$str");
                    $str = $map[$where[3][0]].$where[3][1]."'".$where[3][2]."'";
                    $sql->where("$str");
                }
                else
                    error();
            }
            return $this->fetchAll($sql);       
        }


     
        public function fetchCol($column, $sql)
        {
//echo($sql->__toString()); 
            $resultSet = $this->getDbTable()->fetchAll($sql);
//var_dump($resultSet); exit;
            $entries   = array();
            foreach ($resultSet as $row) {
                $entries[] = $row->$column;
            }
            return $entries;
        }


        public function fetchColumn(array $where, $column, $distinct = false)
        {
            $map['id'] = 'cal_id';
            $map['login'] = 'cal_login';
            $map['status'] = 'cal_status';
            $map['category'] = 'cal_category';
            $map['percent'] = 'cal_percent';
            
            $sql = $this->getDbTable()->select();
            if ($distinct)
            {
                $sql->distinct();             
            }
            $sql->from(array('webcal_entry_user'),
                        $column
                        );
            foreach ($where as $cond) 
            {
                    $str = $map[$cond[0]].$cond[1]."\"".$cond[2]."\"";
                $sql->where("$str");
            }
            return $this->fetchCol($column[0], $sql);       
        }
         

    }
?>