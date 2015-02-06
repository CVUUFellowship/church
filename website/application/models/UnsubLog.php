<?php
    // application/models/UnsubLog.php
     
    class Application_Model_UnsubLog
    {
        protected $_id;
        protected $_email;
        protected $_personid;
        protected $_unsubtype;
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
                throw new Exception('Invalid unsublog property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid unsublog property');
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
     
        public function setEmail($email)
        {
            $this->_email = (string) $email;
            return $this;
        }
     
        public function getEmail()
        {
            return $this->_email;
        }

        public function setPersonid($text)
        {
            $this->_personid = (string) $text;
            return $this;
        }
     
        public function getPersonid()
        {
            return $this->_personid;
        }
     
        public function setUnsubtype($text)
        {
            $this->_unsubtype = (string) $text;
            return $this;
        }
     
        public function getUnsubtype()
        {
            return $this->_unsubtype;
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