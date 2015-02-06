<?php
    // application/models/Groupsequence.php
     
    class Application_Model_Groupsequence
    {
        protected $_id;
        protected $_type;
        protected $_headingid;
        protected $_groupid;
        protected $_nextgroup;
        protected $_sequence;
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
                throw new Exception('Invalid groupsequence property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid groupsequence property');
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
     
        public function setType($type)
        {
            $this->_type = (string) $type;
            return $this;
        }
     
        public function getType()
        {
            return $this->_type;
        }

        public function setHeadingid($text)
        {
            $this->_headingid = (string) $text;
            return $this;
        }
     
        public function getHeadingid()
        {
            return $this->_headingid;
        }
     
        public function setGroupid($text)
        {
            $this->_groupid = (string) $text;
            return $this;
        }
     
        public function getGroupid()
        {
            return $this->_groupid;
        }
     
        public function setNextgroup($text)
        {
            $this->_nextgroup = (string) $text;
            return $this;
        }
     
        public function getNextgroup()
        {
            return $this->_nextgroup;
        }
     
        public function setSequence($text)
        {
            $this->_sequence = (string) $text;
            return $this;
        }
     
        public function getSequence()
        {
            return $this->_sequence;
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