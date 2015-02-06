<?php
    // application/models/WelcomingStatus.php
     
    class Application_Model_WelcomingStatus
    {
        protected $_id;
        protected $_peopleid;
        protected $_dateid;
        protected $_early;
        protected $_late;
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
                throw new Exception('Invalid welcomingstatus property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid welcomingstatus property');
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
     
        public function setPeopleid($peopleid)
        {
            $this->_peopleid = (string) $peopleid;
            return $this;
        }
     
        public function getPeopleid()
        {
            return $this->_peopleid;
        }
     
        public function setDateid($text)
        {
            $this->_dateid = (string) $text;
            return $this;
        }
     
        public function getDateid()
        {
            return $this->_dateid;
        }
     
        public function setEarly($text)
        {
            $this->_early = (string) $text;
            return $this;
        }
     
        public function getEarly()
        {
            return $this->_early;
        }
     
        public function setLate($text)
        {
            $this->_late = (string) $text;
            return $this;
        }
     
        public function getLate()
        {
            return $this->_late;
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