<?php
// application/models/AuthMapper.php
     
    class Application_Model_AuthMapper
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
                $this->setDbTable('Application_Model_DbTable_Auth');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Auth $auth)
        {
            $data = array(
                'id'   => $auth->getId(),
                'memberid'   => $auth->getMemberid(),
                'passwd'   => $auth->getPasswd(),
                'team'   => $auth->getTeam(),
                'level'   => $auth->getLevel(),
                'status'   => $auth->getStatus(),
                'lastlogin'   => new Zend_Db_Expr('NOW()'),
                'logincount'   => new Zend_Db_Expr('logincount + 1'),
            );
     
            if (null === ($id = $auth->getId())) {

                unset($data['ConnectID']);
                /* handle non-null requirements */
                $data['memberid'] = isset($data['memberid']) ? $data['memberid'] : 0;
                $data['passwd'] = isset($data['passwd']) ? $data['passwd'] : '';
                $data['team'] = isset($data['team']) ? $data['team'] : '';
                $data['level'] = isset($data['level']) ? $data['level'] : 0;
                $data['status'] = isset($data['status']) ? $data['status'] : '';
                $data['lastlogin'] = isset($data['lastlogin']) ? $data['lastlogin'] : '0000-00-00 00:00:00';
                $data['logincount'] = isset($data['logincount']) ? $data['logincount'] : 0;
                $id = $this->getDbTable()->insert($data);
                $auth->setId($id);


                unset($data['id']);
                $this->getDbTable()->insert($data);
            } else {
                $this->getDbTable()->update($data, array('id = ?' => $id));
            }
        }
     
        public function find($id, Application_Model_Auth $auth)
        {
            $result = $this->getDbTable()->find($id);
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
            $auth->setId($row->id);
            $auth->setMemberid($row->memberid);
            $auth->setPasswd($row->passwd);
            $auth->setTeam($row->team);
            $auth->setLevel($row->level);
            $auth->setStatus($row->status);
            $auth->setLastlogin($row->lastlogin);
            $auth->setLogincount($row->logincount);
        }
     
        public function findMatch($memberid, $password, Application_Model_Auth $auth)
        {
            $sql = $this->getDbTable()->select()
                ->where ("status = 'active'")
                ->where("passwd = MD5('$password')")
                ->where("memberid = '$memberid'");
            $result = $this->getDbTable()->fetchAll($sql);
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
            $auth->setId($row->id);
            $auth->setMemberid($row->memberid);
            $auth->setPasswd($row->passwd);
            $auth->setTeam($row->team);
            $auth->setLevel($row->level);
            $auth->setStatus($row->status);
            $auth->setLastlogin($row->lastlogin);
            $auth->setLogincount($row->logincount);
        }
     
        public function findUser($memberid, Application_Model_Auth $auth)
        {
            $sql = $this->getDbTable()->select()
                ->where("memberid = '$memberid'");
            $result = $this->getDbTable()->fetchAll($sql);
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
            $auth->setId($row->id);
            $auth->setMemberid($row->memberid);
            $auth->setPasswd($row->passwd);
            $auth->setTeam($row->team);
            $auth->setLevel($row->level);
            $auth->setStatus($row->status);
            $auth->setLastlogin($row->lastlogin);
            $auth->setLogincount($row->logincount);
        }
     
        public function fetchAll($sql)
        {
        
//$stmt = $sql->__toString();
//echo "$stmt\n"; exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Auth();
                $entry->setMemberid($row->memberid);
                $entry->setPasswd($row->passwd);
                $entry->setTeam($row->team);
                $entry->setLevel($row->level);
                $entry->setStatus($row->status);
                $entry->setLastlogin($row->lastlogin);
                $entry->setLogincount($row->logincount);
                $entry->setId($row->id);
                $entries[] = $entry;
            }
            return $entries;
        }
        

        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['memberid'] = 'memberid';
            $map['team'] = 'team';
            $map['level'] = 'level';
            $map['status'] = 'status';
            $map['lastlogin'] = 'lastlogin';
            $map['logincount'] = 'logincount';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("memberid"));
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
        

        public function fetchInvalids()
        {
            $sql = $this->getDbTable()->select();
            $sql->from(array('authuser', 'memberid'));
            $sql->from(array('people', 'RecordID'));
            $sql->where("authuser.memberid = people.RecordID");  
            $sql->where("people.Inactive='yes' OR people.status NOT IN ('Member', 'Affiliate', 'NewFriend', 'Staff')");
            $sql->where("authuser.status<>'removed'");
            $sql->setIntegrityCheck(false);
            return $this->fetchAll($sql);       
        }


    }
?>