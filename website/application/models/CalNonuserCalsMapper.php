<?php
// application/models/CalNonuserCalsMapper.php
     
    class Application_Model_CalNonuserCalsMapper
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
                $this->setDbTable('Application_Model_DbTable_CalNonuserCals');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalNonuserCals $user, $new = 'old')
        {
            $data = array(
                'cal_login'  => $user->getLogin(),
                'cal_lastname'   => $user->getLastname(),
                'cal_firstname'   => $user->getFirstname(),
                'cal_is_public'  => $user->getIspublic(),
                'cal_url'  => $user->getUrl(),
            );
     
            if ($new == 'new') {
                /* handle non-null requirements */
                $data['cal_url'] = isset($data['cal_url']) ? $data['cal_url'] : '';
                $data['cal_lastname'] = isset($data['cal_lastname']) ? $data['cal_lastname'] : '';
                $data['cal_firstname'] = isset($data['cal_firstname']) ? $data['cal_firstname'] : '';
                $data['cal_is_public'] = isset($data['cal_is_public']) ? $data['cal_is_public'] : '';
                $this->getDbTable()->insert($data);
            } else {
                $this->getDbTable()->update($data);
            }
        }
        
     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('cal_login = ?', $id);
            return $this->getDbTable()->delete($where);        
        }
        
    
        public function fetchAll($sql)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_CalNonuserCals();
                $entry->setUrl($row->cal_url);
                $entry->setLogin($row->cal_login);
                $entry->setLastname($row->cal_lastname);
                $entry->setFirstname($row->cal_firstname);
                $entry->setIspublic($row->cal_is_public);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['url'] = 'cal_url';
            $map['login'] = 'cal_login';
            $map['lastname'] = 'cal_lastname';
            $map['firstname'] = 'cal_firstname';
            $map['ispublic'] = 'cal_is_public';
            
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
        

    }
?>