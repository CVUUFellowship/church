<?php
    // application/models/CalEntryUser.php
     
    class Application_Model_CalEntryUser
    {
        protected $_id;
        protected $_login;
        protected $_status;
        protected $_category;
        protected $_percent;
     
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
                throw new Exception('Invalid CalEntryUser property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid CalEntryUser property');
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
     
        public function setLogin($login)
        {
            $this->_login = (string) $login;
            return $this;
        }
     
        public function getLogin()
        {
            return $this->_login;
        }
     
        public function setStatus($text)
        {
            $this->_status = (string) $text;
            return $this;
        }
     
        public function getStatus()
        {
            return $this->_status;
        }
     
        public function setCategory($text)
        {
            $this->_category = (string) $text;
            return $this;
        }
     
        public function getCategory()
        {
            return $this->_category;
        }
     
  
        public function setPercent($ts)
        {
            $this->_percent = $ts;
            return $this;
        }
     
        public function getPercent()
        {
            return $this->_percent;
        }
    }
?>