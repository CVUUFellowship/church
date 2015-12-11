<?php
// application/models/LogLoginMapper.php
     
    class Application_Model_LogLoginMapper
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
                $this->setDbTable('Application_Model_DbTable_LogLogin');
            }

            return $this->_dbTable;
        }
     


        public function save(Application_Model_LogLogin $log)
        {
            $data = array(
                'id'  => $log->getid(),
                'ip'  => $log->getip(),
                'memberid'  => $log->getmemberid(),
                'timestamp' => $log->gettimestamp(),
            );

            if (!isset($data['timestamp'])) {
                $data['timestamp'] = date('Y-m-d H:i:s', time());
            }
     
            if (null === ($id = $log->getid())) {
                unset($data['id']);
                /* handle non-null requirements */
//                $data['id'] = isset($data['id']) ? $data['id'] : 0;
                $id = $this->getDbTable()->insert($data);
                $log->setid($id);
            } else {
                $this->getDbTable()->update($data, array('id = ?' => $id));
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
            $rowarray['id'] = $row->id;
            $rowarray['ip'] = $row->ip;
            $rowarray['memberid'] = $row->memberid;
            $rowarray['timestamp'] = $row->timestamp;

            return ($rowarray);
        }
     
     
        public function fetchAll($sql = null)
        {
//if ($sql <> null) {echo($sql->__toString()); exit;}
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_LogLogin();
                $entry->setid($row->id);
                $entry->setmemberid($row->memberid);
                $entry->settimestamp($row->timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }



        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'id';
            $map['ip'] = 'ip';
            $map['memberid'] = 'memberid';
            $map['timestamp'] = 'timestamp';
            
            $sql = $this->getDbTable()->select();
//var_dump($order);
            if (null === $order)
                $sql->order(array("timestamp"));
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
