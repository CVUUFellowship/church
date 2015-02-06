<?php
    // application/models/CalUser.php
     
    class Application_Model_CalUser
    {
        protected $_passwd;
        protected $_login;
        protected $_lastname;
        protected $_firstname;
        protected $_isadmin;
        protected $_email;
        protected $_enabled;
        protected $_telephone;
        protected $_address;
        protected $_title;
        protected $_birthday;
        protected $_lastlogin;
     
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
                throw new Exception('Invalpasswd CalUser property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalpasswd CalUser property');
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

        public function setPasswd($passwd)
        {
            $this->_passwd = (string) $passwd;
            return $this;
        }
     
        public function getPasswd()
        {
            return $this->_passwd;
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
     
  
        public function setIsadmin($ts)
        {
            $this->_isadmin = $ts;
            return $this;
        }
     
        public function getIsadmin()
        {
            return $this->_isadmin;
        }
        
     
  
        public function setEmail($ts)
        {
            $this->_email = $ts;
            return $this;
        }
     
        public function getEmail()
        {
            return $this->_email;
        }
        
     
  
        public function setEnabled($ts)
        {
            $this->_enabled = $ts;
            return $this;
        }
     
        public function getEnabled()
        {
            return $this->_enabled;
        }
        
     
  
        public function setTelephone($ts)
        {
            $this->_telephone = $ts;
            return $this;
        }
     
        public function getTelephone()
        {
            return $this->_telephone;
        }
        
     
  
        public function setAddress($ts)
        {
            $this->_address = $ts;
            return $this;
        }
     
        public function getAddress()
        {
            return $this->_address;
        }
        
     
  
        public function setTitle($ts)
        {
            $this->_title = $ts;
            return $this;
        }
     
        public function getTitle()
        {
            return $this->_title;
        }
        
     
  
        public function setBirthday($ts)
        {
            $this->_birthday = $ts;
            return $this;
        }
     
        public function getBirthday()
        {
            return $this->_birthday;
        }
        
     
  
        public function setLastlogin($ts)
        {
            $this->_lastlogin = $ts;
            return $this;
        }
     
        public function getLastlogin()
        {
            return $this->_lastlogin;
        }
        
    }
?>