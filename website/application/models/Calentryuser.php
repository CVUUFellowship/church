<?php
    // application/models/Calentryuser.php
     
    class Application_Model_Calentryuser
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
                throw new Exception('Invalid Calentryuser property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid Calentryuser property');
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
     
        public function setCal_login($cal_login)
        {
            $this->_login = (string) $cal_login;
            return $this;
        }
     
        public function getCal_login()
        {
            return $this->_login;
        }
     
        public function setCal_status($text)
        {
            $this->_status = (string) $text;
            return $this;
        }
     
        public function getCal_status()
        {
            return $this->_status;
        }
     
        public function setCal_category($text)
        {
            $this->_category = (string) $text;
            return $this;
        }
     
        public function getCal_category()
        {
            return $this->_category;
        }
     
  
        public function setCal_percent($ts)
        {
            $this->_percent = $ts;
            return $this;
        }
     
        public function getCal_percent()
        {
            return $this->_percent;
        }
    }
?>