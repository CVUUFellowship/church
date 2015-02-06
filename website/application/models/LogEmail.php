<?php
    // application/models/LogEmail.php
     
    class Application_Model_LogEmail
    {
        protected $_id;
        protected $_controller;
        protected $_action;
        protected $_listcount;
        protected $_sentcount;
        protected $_invalid;
        protected $_unsub;
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
                throw new Exception('Invalid LogEmail property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid LogEmail property');
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
     
        public function setcontroller($controller)
        {
            $this->_controller = (string) $controller;
            return $this;
        }
     
        public function getcontroller()
        {
            return $this->_controller;
        }

        public function setaction($text)
        {
            $this->_action = (string) $text;
            return $this;
        }
     
        public function getaction()
        {
            return $this->_action;
        }
     
        public function setlistcount($text)
        {
            $this->_listcount = (string) $text;
            return $this;
        }
     
        public function getlistcount()
        {
            return $this->_listcount;
        }
     
        public function setsentcount($text)
        {
            $this->_sentcount = (string) $text;
            return $this;
        }
     
        public function getsentcount()
        {
            return $this->_sentcount;
        }
     
        public function setinvalid($text)
        {
            $this->_invalid = (string) $text;
            return $this;
        }
     
        public function getinvalid()
        {
            return $this->_invalid;
        }
     
        public function setunsub($text)
        {
            $this->_unsub = $text;
            return $this;
        }
     
        public function getunsub()
        {
            return $this->_unsub;
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