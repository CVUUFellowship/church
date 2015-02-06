<?php
    // application/models/LogLogin.php
     
    class Application_Model_LogLogin
    {
        protected $_id;
        protected $_ip;
        protected $_memberid;
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
                throw new Exception('Invalid LogLogin property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid LogLogin property');
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
     
        public function setid($id)
        {
            $this->_id = (string) $id;
            return $this;
        }
     
        public function getid()
        {
            return $this->_id;
        }
     
        public function setip($ip)
        {
            $this->_ip = (string) $ip;
            return $this;
        }
     
        public function getip()
        {
            return $this->_ip;
        }
     
        public function setmemberid($memberid)
        {
            $this->_memberid = (string) $memberid;
            return $this;
        }
     
        public function getmemberid()
        {
            return $this->_memberid;
        }

    
        public function settimestamp($ts)
        {
            $this->_timestamp = $ts;
            return $this;
        }
     
        public function gettimestamp()
        {
            return $this->_timestamp;
        }
    }
?>