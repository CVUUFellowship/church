<?php
// application/models/LibCatalogMapper.php
     
    class Application_Model_LibCatalogMapper
    {
        protected $_dbTable;
     
        public function setDbTable($dbTable)
        {
            if (is_string($dbTable)) {
                $dbTable = new $dbTable();
            }
            if (!$dbTable instanceof Zend_Db_Table_Abstract) {
                throw new Exception('Invalnumber table data gateway provnumbered');
            }
            $this->_dbTable = $dbTable;
            return $this;
        }
     
        public function getDbTable()
        {
            if (null === $this->_dbTable) {
                $this->setDbTable('Application_Model_DbTable_LibCatalog');
            }

            return $this->_dbTable;
        }
     

        public function delete($id)
        {
            $where = $this->getDbTable()->getAdapter()->quoteInto('Number = ?', $id);
            return $this->getDbTable()->delete($where);        
        }
     


        public function save(Application_Model_LibCatalog $cat)
        {
            $data = array(
                'Number'  => $cat->getNumber(),
                'Title'  => $cat->getTitle(),
                'Author'   => $cat->getAuthor(),
                'CallNumber'   => $cat->getCallnumber(),
                'Subject1'   => $cat->getSubject1(),
                'Subject2'   => $cat->getSubject2(),
                'Subject3' => $cat->getSubject3(),
                'Subject4'   => $cat->getSubject4(),
                'Publisher'   => $cat->getPublisher(),
                'Date'   => $cat->getDate(),
                'Price'   => $cat->getPrice(),
                'CreateDate'   => $cat->getCreatedate(),
                'Timestamp' => $cat->getTimestamp(),
            );

            if (!isset($data['Timestamp'])) {
                $data['Timestamp'] = date('Y-m-d H:i:s', time());
            }
     
            if (null === ($number = $cat->getNumber())) {
                unset($data['Number']);
                /* handle non-null requirements */
//                $data['Number'] = isset($data['Number']) ? $data['Number'] : 0;
                $data['Title'] = isset($data['Title']) ? $data['Title'] : '';
                $data['Author'] = isset($data['Author']) ? $data['Author'] : '';
                $data['CallNumber'] = isset($data['CallNumber']) ? $data['CallNumber'] : '';
                $data['Subject1'] = isset($data['Subject1']) ? $data['Subject1'] : '';
                $data['Subject2'] = isset($data['Subject2']) ? $data['Subject2'] : '';
                $data['Subject3'] = isset($data['Subject3']) ? $data['Subject3'] : '';
                $data['Subject4'] = isset($data['Subject4']) ? $data['Subject4'] : '';
                $data['Publisher'] = isset($data['Publisher']) ? $data['Publisher'] : '';
                $data['Date'] = isset($data['Date']) ? $data['Date'] : '';
                $data['Price'] = isset($data['Price']) ? $data['Price'] : '';
                $data['CreateDate'] = isset($data['CreateDate']) ? $data['CreateDate'] : '0000-00-00';
                $number = $this->getDbTable()->insert($data);
                $cat->setNumber($number);
            } else {
                $this->getDbTable()->update($data, array('Number = ?' => $number));
            }
        }
     

      
     
        public function find($number)
        {
            $result = $this->getDbTable()->find($number);
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
            $rowarray = array();
            $rowarray['number'] = $row->Number;
            $rowarray['title'] = $row->Title;
            $rowarray['author'] = $row->Author;
            $rowarray['callnumber'] = $row->CallNumber;
            $rowarray['subject1'] = $row->Subject1;
            $rowarray['subject2'] = $row->Subject2;
            $rowarray['subject3'] = $row->Subject3;
            $rowarray['subject4'] = $row->Subject4;
            $rowarray['publisher'] = $row->Publisher;
            $rowarray['date'] = $row->Date;
            $rowarray['price'] = $row->Price;
            $rowarray['createdate'] = $row->CreateDate;
            $rowarray['timestamp'] = $row->Timestamp;

            return ($rowarray);
        }
     
     
        public function fetchAll($sql = null)
        {
//if ($sql <> null) {echo($sql->__toString()); exit;}
            $resultSet = $this->getDbTable()->fetchAll($sql);
            $entries   = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_LibCatalog();
                $entry->setNumber($row->Number);
                $entry->setTitle($row->Title);
                $entry->setAuthor($row->Author);
                $entry->setCallnumber($row->CallNumber);
                $entry->setSubject1($row->Subject1);
                $entry->setSubject2($row->Subject2);
                $entry->setSubject3($row->Subject3);
                $entry->setSubject4($row->Subject4);
                $entry->setPublisher($row->Publisher);
                $entry->setDate($row->Date);
                $entry->setPrice($row->Price);
                $entry->setCreatedate($row->CreateDate);
                $entry->setTimestamp($row->Timestamp);
                $entries[] = $entry;
            }

            return $entries;
        }



        public function fetchWhere(array $where, array $order=null)
        {
            $map = array();
            $map['number'] = 'Number';
            $map['title'] = 'Title';
            $map['author'] = 'Author';
            $map['callnumber'] = 'CallNumber';
            $map['subject1'] = 'Subject1';
            $map['subject2'] = 'Subject2';
            $map['subject3'] = 'Subject3';
            $map['subject4'] = 'Subject4';
            $map['publisher'] = 'Publisher';
            $map['date'] = 'Date';
            $map['price'] = 'Price';
            $map['createdate'] = 'CreateDate';
            $map['timestamp'] = 'Timestamp';
            
            $sql = $this->getDbTable()->select();
//var_dump($order);
            if (null === $order)
                $sql->order(array("Title"));
            elseif ($order[0] == 'create')
                $sql->order(array('CreateDate DESC', 'Title'));
            elseif ($order[0] == 'number')
                $sql->order(array('Number DESC'));
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




        public function fetchOrWhere(array $where)
        {
            $map = array();
            $map['title'] = 'Title';
            $map['author'] = 'Author';
            $map['callnumber'] = 'CallNumber';
            $map['subject1'] = 'Subject1';
            $map['subject2'] = 'Subject2';
            $map['subject3'] = 'Subject3';
            $map['subject4'] = 'Subject4';
            $map['publisher'] = 'Publisher';
            $map['date'] = 'Date';
            $map['price'] = 'Price';
            $map['createdate'] = 'Number';
            
            $sql = $this->getDbTable()->select();
            $sql->order(array("Title"));
            foreach ($where as $cond) 
            {
                $str = $map[$cond[0]].$cond[1]."\"".$cond[2]."\"";
                $sql->orwhere("$str");
            }
            return $this->fetchAll($sql);       
        }



    }
?>
