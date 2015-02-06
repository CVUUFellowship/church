<?php
    // application/models/CalGroups.php
     
    class Application_Model_CalGroups
    {
        protected $_id;
        protected $_groupid;
        protected $_groupname;
        protected $_groupcode;
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
                throw new Exception('Invalid CalGroups property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid CalGroups property');
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
     
        public function setGroupid($groupid)
        {
            $this->_groupid = (string) $groupid;
            return $this;
        }
     
        public function getGroupid()
        {
            return $this->_groupid;
        }

        public function setGroupname($text)
        {
            $this->_groupname = (string) $text;
            return $this;
        }
     
        public function getGroupname()
        {
            return $this->_groupname;
        }
     
        public function setGroupcode($text)
        {
            $this->_groupcode = (string) $text;
            return $this;
        }
     
        public function getGroupcode()
        {
            return $this->_groupcode;
        }
     


        public function setTimestamp($text)
        {
            $this->_timestamp = (string) $text;
            return $this;
        }
     
        public function getTimestamp()
        {
            return $this->_timestamp;
        }
    }
?>