<?php
    // application/models/ProgTable.php
     
    class Application_Model_ProgTable
    {
        protected $_id;
        protected $_reservedate;
        protected $_person;
        protected $_program;
        protected $_timestamp;
     
        public function __construct(array $options = null)
        {
            if (is_array($options)) {
                $this->setOptions($options);
            }
        }
     
        public function __set($name, $value)
        {
            $method = 'set' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid prograble property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid progtable property');
            }
            return $this->$method();
        }
     
        public function setOptions(array $options)
        {
            $methods = get_class_methods($this);
            foreach ($options as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (in_array($method, $methods)) {
                    $this->$method($value);
                }
            }
            return $this;
        }
     
        public function setId($id)
        {
            $this->_id = (string) $id;
            return $this;
        }
     
        public function getId()
        {
            return $this->_id;
        }

        public function setReservedate($text)
        {
            $this->_reservedate = (string) $text;
            return $this;
        }
     
        public function getReservedate()
        {
            return $this->_reservedate;
        }
     
        public function setPerson($stat)
        {
            $this->_person = $stat;
            return $this;
        }
     
        public function getPerson()
        {
            return $this->_person;
        }
     
        public function setProgram($text)
        {
            $this->_program = (string) $text;
            return $this;
        }
     
        public function getProgram()
        {
            return $this->_program;
        }
   
   
        public function setTimestamp($ts)
        {
            $this->_timestamp = $ts;
            return $this;
        }
     
        public function getTimestamp()
        {
            return $this->_timestamp;
        }
    }
?>