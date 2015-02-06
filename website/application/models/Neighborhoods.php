<?php
    // application/models/Neighborhoods.php
     
    class Application_Model_Neighborhoods
    {
        protected $_id;
        protected $_active;
        protected $_nocall;
        protected $_householdid;
        protected $_hoodid;
        protected $_comments;
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
                throw new Exception('Invalid neighborhoods property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid neighborhoods property');
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
     
        public function setActive($active)
        {
            $this->_active = (string) $active;
            return $this;
        }
     
        public function getActive()
        {
            return $this->_active;
        }
     
        public function setNocall($text)
        {
            $this->_nocall = (string) $text;
            return $this;
        }
     
        public function getNocall()
        {
            return $this->_nocall;
        }
     
        public function setHouseholdid($text)
        {
            $this->_householdid = (string) $text;
            return $this;
        }
     
        public function getHouseholdid()
        {
            return $this->_householdid;
        }
     
        public function setHoodid($text)
        {
            $this->_hoodid = (string) $text;
            return $this;
        }
     
        public function getHoodid()
        {
            return $this->_hoodid;
        }
     
  
        public function setTimestamp($ts)
        {
            $this->_timestamp = $ts;
            return $this;
        }

        public function setComments($text)
        {
            $this->_comments = (string) $text;
            return $this;
        }
     
        public function getComments()
        {
            return $this->_comments;
        }
     
        public function getTimestamp()
        {
            return $this->_timestamp;
        }
    }
?>