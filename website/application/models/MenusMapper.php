<?php
// application/models/MenusMapper.php
     
    class Application_Model_MenusMapper
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
//var_dump($dbTable);  exit;
            $this->_dbTable = $dbTable;
            return $this;
        }
     
        public function getDbTable()
        {
            if (null === $this->_dbTable) {
                $this->setDbTable('Application_Model_DbTable_Menus');
            }
//var_dump($this->_dbTable);  exit;

            return $this->_dbTable;
        }

     
        public function save(Application_Model_Menus $menu)
        {
            $data = array(
                'RecordID'  => $menu->getId(),
                'Page'  => $menu->getPage(),
                'Name'  => $menu->getName(),
                'Position'  => $menu->getPosition(),
                'Level'  => $menu->getLevel(),
                'Text'  => $menu->getText(),
                'Type'  => $menu->getType(),
                'Item'  => $menu->getItem(),
                'Timestamp' => $menu->getTimestamp(),
            );
     
            if (null === ($id = $menu->getId())) {
                unset($data['RecordID']);
                /* handle non-null requirements */
                $data['RecordID'] = isset($data['RecordID']) ? $data['RecordID'] : 0;
                $data['Page'] = isset($data['Page']) ? $data['Page'] : '';
                $data['Name'] = isset($data['Name']) ? $data['Name'] : '';
                $data['Position'] = isset($data['Position']) ? $data['Position'] : 0;
                $data['Level'] = isset($data['Level']) ? $data['Level'] : 0;
                $data['Text'] = isset($data['Text']) ? $data['Text'] : '';
                $data['Type'] = isset($data['Type']) ? $data['Type'] : '';
                $data['Item'] = isset($data['Item']) ? $data['Item'] : '';
                
                $id = $this->getDbTable()->insert($data);
                $menu->setId($id);
            } else {
                $this->getDbTable()->update($data, array('RecordID = ?' => $id));
            }
        }
     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('RecordID = ?', $id);
            return $this->getDbTable()->delete($where);        
        }
     
     
        public function find($id)
        {
            $result = $this->getDbTable()->find($id);
//var_dump($result); exit;
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
            $rowarray = array();
            $rowarray['id'] = $row->RecordID;
            $rowarray['page'] = $row->Page;
            $rowarray['name'] = $row->Name;
            $rowarray['position'] = $row->Position;
            $rowarray['level'] = $row->Level;
            $rowarray['text'] = $row->Text;
            $rowarray['type'] = $row->Type;
            $rowarray['item'] = $row->Item;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
      }
     
        public function fetchAll($sql)
        {
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Menus();
                $entry->setPage($row->Page);
                $entry->setName($row->Name);
                $entry->setPosition($row->Position);
                $entry->setLevel($row->Level);
                $entry->setText($row->Text);
                $entry->setType($row->Type);
                $entry->setItem($row->Item);
                $entry->setTimestamp($row->Timestamp);
                $entry->setId($row->RecordID);
                $entries[] = $entry;
            }
            return $entries;
        }



        public function fetchWhere(array $where, $distinct = false)
        {
            $map = array();
            $map['id'] = 'RecordID';
            $map['page'] = 'Page';
            $map['name'] = 'Name';
            $map['position'] = 'Position';
            $map['level'] = 'Level';
            $map['text'] = 'Text';
            $map['type'] = 'Type';
            $map['item'] = 'Item';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            $sql->order(array('Page DESC', 'Position'));
            if ($distinct)
                $sql->distinct();             
            foreach ($where as $cond) 
            {
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
            $map['id'] = 'RecordID';
            $map['page'] = 'Page';
            $map['name'] = 'Name';
            $map['position'] = 'Position';
            $map['level'] = 'Level';
            $map['text'] = 'Text';
            $map['type'] = 'Type';
            $map['item'] = 'Item';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
            if ($distinct)
            {
                $sql->distinct();             
            }
            $sql->from(array('menus'),
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