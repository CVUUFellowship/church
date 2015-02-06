<?php
    // application/models/CalAccessUser.php
     
    class Application_Model_CalAccessUser
    {
        protected $_login;
        protected $_otheruser;
        protected $_canview;
        protected $_canedit;
        protected $_canapprove;
        protected $_caninvite;
        protected $_canemail;
        protected $_seetimeonly;
     
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
                throw new Exception('Invalid CalAccessUser property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid CalAccessUser property');
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
     
        public function setLogin($id)
        {
            $this->_login = (string) $id;
            return $this;
        }
     
        public function getLogin()
        {
            return $this->_login;
        }
     
        public function setOtheruser($otheruser)
        {
            $this->_otheruser = (string) $otheruser;
            return $this;
        }
     
        public function getOtheruser()
        {
            return $this->_otheruser;
        }
     
        public function setCanview($text)
        {
            $this->_canview = (string) $text;
            return $this;
        }
     
        public function getCanview()
        {
            return $this->_canview;
        }
     
  
        public function setCanedit($ts)
        {
            $this->_canedit = $ts;
            return $this;
        }
     
        public function getCanedit()
        {
            return $this->_canedit;
        }
     
  
        public function setCanapprove($ts)
        {
            $this->_canapprove = $ts;
            return $this;
        }
     
        public function getCanapprove()
        {
            return $this->_canapprove;
        }
     
  
  
        public function setCaninvite($ts)
        {
            $this->_caninvite = $ts;
            return $this;
        }
     
        public function getCaninvite()
        {
            return $this->_caninvite;
        }
     
  
        public function setCanemail($ts)
        {
            $this->_canemail = $ts;
            return $this;
        }
     
        public function getCanemail()
        {
            return $this->_canemail;
        }
        
        
        public function setSeetimeonly($ts)
        {
            $this->_seetimeonly = $ts;
            return $this;
        }
     
        public function getSeetimeonly()
        {
            return $this->_seetimeonly;
        }
     

    }
?>