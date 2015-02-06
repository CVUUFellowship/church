<?php
    // application/models/CalNonuserCals.php
     
    class Application_Model_CalNonuserCals
    {
        protected $_login;
        protected $_lastname;
        protected $_firstname;
        protected $_admin;
        protected $_ispublic;
        protected $_url;
     
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
                throw new Exception('Invalurl CalNonuserCals property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalurl CalNonuserCals property');
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
     
        public function setId($url)
        {
            $this->_url = (string) $url;
            return $this;
        }
     
        public function getId()
        {
            return $this->_url;
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
     
        public function setLastname($text)
        {
            $this->_lastname = (string) $text;
            return $this;
        }
     
        public function getLastname()
        {
            return $this->_lastname;
        }
     
        public function setFirstname($text)
        {
            $this->_firstname = (string) $text;
            return $this;
        }
     
        public function getFirstname()
        {
            return $this->_firstname;
        }
     
  
        public function setAdmin($ts)
        {
            $this->_admin = $ts;
            return $this;
        }
     
        public function getAdmin()
        {
            return $this->_admin;
        }
  
        public function setIspublic($ts)
        {
            $this->_ispublic = $ts;
            return $this;
        }
     
        public function getIspublic()
        {
            return $this->_ispublic;
        }
  
        public function setUrl($ts)
        {
            $this->_url = $ts;
            return $this;
        }
     
        public function getUrl()
        {
            return $this->_url;
        }
    }
?>