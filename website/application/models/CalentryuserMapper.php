<?php
// application/models/CalentryuserMapper.php
     
    class Application_Model_CalentryuserMapper
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
                $this->setDbTable('Application_Model_DbTable_Calentryuser');
            }

            return $this->_dbTable;
        }
     
        public function save(Application_Model_Calentryuser $heading)
        {
            $data = array(
                'cal_id'  => $heading->getID(),
                'cal_login'  => $heading->getCal_login(),
                'cal_status'   => $heading->getCal_status(),
                'Category'   => $heading->getCategory(),
            );
     
            if (null === ($id = $household->getId())) {
                unset($data['ConnectID']);
                /* handle non-null requirements */
                $data['cal_id'] = isset($data['cal_id']) ? $data['cal_id'] : 0;
                $data['cal_login'] = isset($data['cal_login']) ? $data['cal_login'] : '';
                $data['cal_status'] = isset($data['cal_status']) ? $data['cal_status'] : '';
                $data['cal_category'] = isset($data['cal_category']) ? $data['cal_category'] : 0;
                $data['cal_percent'] = isset($data['cal_percent']) ? $data['cal_percent'] : 0;
                $id = $this->getDbTable()->insert($data);
                $connection->setId($id);
            } else {
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
            $rowarray['type'] = $row->cal_login;
            $rowarray['heading'] = $row->cal_status;
            $rowarray['category'] = $row->cal_category;
            $rowarray['percent'] = $row->cal_percent;

            return ($rowarray);
        }
     
    
        public function fetchAll($sql = null)
        {
//echo($sql->__toString()); exit;
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Calentryuser();
                $entry->setId($row->cal_id);
                $entry->setCal_login($row->cal_login);
                $entry->setCal_status($row->cal_status);
                $entry->setCal_category($row->cal_category);
                $entry->setCal_percent($row->cal_percent);
                $entries[] = $entry;
            }

            return $entries;
        }


        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['id'] = 'cal_id';
            $map['cal_login'] = 'cal_login';
            $map['cal_status'] = 'cal_status';
            $map['category'] = 'category';
            $map['percent'] = 'percent';
            
            $sql = $this->getDbTable()->select();
            if (null === $order)
                $sql->order(array("cal_idy"));
            else
                $sql->order($order);                 
            foreach ($where as $cond) 
            {
                if ($cond[1] == ' IN ')
                    $str = $map[$cond[0]].$cond[1].$cond[2];
                else
                    $str = $map[$cond[0]].$cond[1]."'".$cond[2]."'";
                $sql->where("$str");
            }
            return $this->fetchAll($sql);       
        }



    }
?>