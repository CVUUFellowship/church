<?php
    // application/models/Nodes.php
     
    class Application_Model_Nodes
    {
        protected $_id;
        protected $_nodeid;
        protected $_title;
        protected $_content;
        protected $_organization;
        protected $_special;
        protected $_date;
        protected $_body;
        protected $_preliminary;
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
                throw new Exception('Invalid nodes property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid nodes property');
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
     
        public function setNodeid($nodeid)
        {
            $this->_nodeid = (string) $nodeid;
            return $this;
        }
     
        public function getNodeid()
        {
            return $this->_nodeid;
        }
     
        public function setTitle($text)
        {
            $this->_title = (string) $text;
            return $this;
        }
     
        public function getTitle()
        {
            return $this->_title;
        }
     
        public function setContent($text)
        {
            $this->_content = (string) $text;
            return $this;
        }
     
        public function getContent()
        {
            return $this->_content;
        }
     
        public function setOrganization($text)
        {
            $this->_organization = (string) $text;
            return $this;
        }
     
        public function getOrganization()
        {
            return $this->_organization;
        }
     
        public function setSpecial($text)
        {
            $this->_special = (string) $text;
            return $this;
        }
     
        public function getSpecial()
        {
            return $this->_special;
        }
     
        public function setDate($text)
        {
            $this->_date = (string) $text;
            return $this;
        }
     
        public function getDate()
        {
            return $this->_date;
        }
     
        public function setBody($text)
        {
            $this->_body = (string) $text;
            return $this;
        }
     
        public function getBody()
        {
            return $this->_body;
        }
     
        public function setPreliminary($text)
        {
            $this->_preliminary = (string) $text;
            return $this;
        }
     
        public function getPreliminary()
        {
            return $this->_preliminary;
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