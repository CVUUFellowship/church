<?php
    // application/models/HoodFromZip.php
     
    class Application_Model_HoodFromZip
    {
        protected $_id;
        protected $_hoodid;
        protected $_low;
        protected $_high;
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
                throw new Exception('Invalid HoodFromZip property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid HoodFromZip property');
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
     
        public function setHoodid($hoodid)
        {
            $this->_hoodid = (string) $hoodid;
            return $this;
        }
     
        public function getHoodid()
        {
            return $this->_hoodid;
        }
     
        public function setLow($text)
        {
            $this->_low = (string) $text;
            return $this;
        }
     
        public function getLow()
        {
            return $this->_low;
        }
     
        public function setHigh($text)
        {
            $this->_high = (string) $text;
            return $this;
        }
     
        public function getHigh()
        {
            return $this->_high;
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