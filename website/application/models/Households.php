<?php
    // application/models/Households.php
     
    class Application_Model_Households
    {
        protected $_id;
        protected $_inactive;
        protected $_householdname;
        protected $_creationdate;
        protected $_street;
        protected $_city;
        protected $_state;
        protected $_zip;
        protected $_phone;
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
                throw new Exception('Invalid households property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid households property');
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
     
        public function setInactive($inactive)
        {
            $this->_inactive = (string) $inactive;
            return $this;
        }
     
        public function getInactive()
        {
            return $this->_inactive;
        }

        public function setHouseholdname($text)
        {
            $this->_householdname = (string) $text;
            return $this;
        }
     
        public function getHouseholdname()
        {
            return $this->_householdname;
        }
     
        public function setCreationdate($text)
        {
            $this->_creationdate = (string) $text;
            return $this;
        }
     
        public function getCreationdate()
        {
            return $this->_creationdate;
        }
     
        public function setStreet($text)
        {
            $this->_street = (string) $text;
            return $this;
        }
     
        public function getStreet()
        {
            return $this->_street;
        }
     
        public function setCity($stat)
        {
            $this->_city = $stat;
            return $this;
        }
     
        public function getCity()
        {
            return $this->_city;
        }
     
        public function setZip($text)
        {
            $this->_zip = (string) $text;
            return $this;
        }
     
        public function getZip()
        {
            return $this->_zip;
        }
     
        public function setPhone($text)
        {
            $this->_phone = (string) $text;
            return $this;
        }
     
        public function getPhone()
        {
            return $this->_phone;
        }
     
        public function setState($text)
        {
            $this->_state = (string) $text;
            return $this;
        }
     
        public function getState()
        {
            return $this->_state;
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