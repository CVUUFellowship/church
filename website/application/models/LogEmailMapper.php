<?php
// application/models/LogEmailMapper.php
     
    class Application_Model_LogEmailMapper
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
                $this->setDbTable('Application_Model_DbTable_LogEmail');
            }

            return $this->_dbTable;
        }
     


        public function save(Application_Model_LogEmail $cat)
        {
            $data = array(
                'id'  => $cat->getid(),
                'controller'  => $cat->getcontroller(),
                'action'   => $cat->getaction(),
                'listcount'   => $cat->getlistcount(),
                'sentcount'   => $cat->getsentcount(),
                'invalid'   => $cat->getinvalid(),
                'unsub' => $cat->getunsub(),
                'timestamp' => $cat->gettimestamp(),
            );
     
            if (null === ($id = $cat->getid())) {
                unset($data['id']);
                /* handle non-null requirements */
//                $data['id'] = isset($data['id']) ? $data['id'] : 0;
                $data['controller'] = isset($data['controller']) ? $data['controller'] : '';
                $data['action'] = isset($data['action']) ? $data['action'] : '';
                $data['listcount'] = isset($data['listcount']) ? $data['listcount'] : '';
                $data['sentcount'] = isset($data['sentcount']) ? $data['sentcount'] : '';
                $data['invalid'] = isset($data['invalid']) ? $data['invalid'] : '';
                $data['unsub'] = isset($data['unsub']) ? $data['unsub'] : '';
                $id = $this->getDbTable()->insert($data);
                $cat->setid($id);
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
            $rowarray['controller'] = $row->controller;
            $rowarray['action'] = $row->action;
            $rowarray['listcount'] = $row->listcount;
            $rowarray['sentcount'] = $row->sentcount;
            $rowarray['invalid'] = $row->invalid;
            $rowarray['unsub'] = $row->unsub;
            $rowarray['timestamp'] = $row->timestamp;

            return ($rowarray);
        }
     
     
        public function fetchAll($sql = null)
        {
//if ($sql <> null) {echo($sql->__toString()); exit;}
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_LogEmail();
                $entry->setid($row->id);
                $entry->setcontroller($row->controller);
                $entry->setaction($row->action);
                $entry->setlistcount($row->listcount);
                $entry->setsentcount($row->sentcount);
                $entry->setinvalid($row->invalid);
                $entry->setunsub($row->unsub);
                $entry->settimestamp($row->timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }



        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'id';
            $map['controller'] = 'controller';
            $map['action'] = 'action';
            $map['listcount'] = 'listcount';
            $map['sentcount'] = 'sentcount';
            $map['invalid'] = 'invalid';
            $map['unsub'] = 'unsub';
            $map['timestamp'] = 'timestamp';
            
            $sql = $this->getDbTable()->select();
//var_dump($order);
            if (null === $order)
                $sql->order(array("controller"));
            elseif ($order[0] == 'create')
                $sql->order(array('CreateDate DESC', 'controller'));
            elseif ($order[0] == 'id')
                $sql->order(array('id DESC'));
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