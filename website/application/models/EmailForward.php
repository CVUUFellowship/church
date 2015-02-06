<?php
    // application/models/EmailForward.php
     
    class Application_Model_EmailForward
    {
        protected $_id;
        protected $_forwarder;
        protected $_forwardto;
     
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
                throw new Exception('Invalid EmailForward property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid EmailForward property');
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
     
        public function setForwarder($forwarder)
        {
            $this->_forwarder = (string) $forwarder;
            return $this;
        }
     
        public function getForwarder()
        {
            return $this->_forwarder;
        }
     
        public function setForwardto($text)
        {
            $this->_forwardto = (string) $text;
            return $this;
        }
     
        public function getForwardto()
        {
            return $this->_forwardto;
        }

    }
?>