<?php
// application/models/CalentryuserMapper.php
     
    class Application_Model_CalUserMapper
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
                $this->setDbTable('Application_Model_DbTable_CalUser');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_CalUser $user, $new = 'old')
        {
            $data = array(
                'cal_passwd'  => $user->getPasswd(),
                'cal_login'  => $user->getLogin(),
                'cal_lastname'   => $user->getLastname(),
                'cal_firstname'   => $user->getFirstname(),
                'cal_is_admin'   => $user->getIsadmin(),
                'cal_email'   => $user->getEmail(),
                'cal_enabled'   => $user->getEnabled(),
                'cal_telephone'   => $user->getTelephone(),
                'cal_address'   => $user->getAddress(),
                'cal_title'   => $user->getTitle(),
                'cal_birthday'   => $user->getBirthday(),
                'cal_last_login'   => $user->getLastlogin(),
            );
     
            if ($new == 'new') {
                /* handle non-null requirements */
                $data['cal_is_admin'] = isset($data['cal_is_admin']) ? $data['cal_is_admin'] : 'N';
                $data['cal_enabled'] = isset($data['cal_enabled']) ? $data['enabled'] : 'Y';

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
                $entry = new Application_Model_CalUser();
                $entry->setPasswd($row->cal_passwd);
                $entry->setLogin($row->cal_login);
                $entry->setLastname($row->cal_lastname);
                $entry->setFirstname($row->cal_firstname);
                $entry->setIsadmin($row->cal_is_admin);
                $entry->setEmail($row->cal_email);
                $entry->setEnabled($row->cal_enabled);
                $entry->setTelephone($row->cal_telephone);
                $entry->setAddress($row->cal_address);
                $entry->setTitle($row->cal_title);
                $entry->setBirthday($row->cal_birthday);
                $entry->setLastlogin($row->cal_last_login);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['passwd'] = 'cal_passwd';
            $map['login'] = 'cal_login';
            
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