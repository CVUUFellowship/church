<?php
    // application/models/Collections.php
     
    class Application_Model_Collections
    {
        protected $_id;
        protected $_type;
        protected $_title;
        protected $_sequence;
        protected $_headingid;
        protected $_contact1;
        protected $_contact2;
        protected $_contact3;
        protected $_contact4;
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
                throw new Exception('Invalid collections property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid collections property');
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

        public function setTitle($text)
        {
            $this->_title = (string) $text;
            return $this;
        }
     
        public function getTitle()
        {
            return $this->_title;
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
     
        public function setHeadingid($text)
        {
            $this->_headingid = (string) $text;
            return $this;
        }
     
        public function getHeadingid()
        {
            return $this->_headingid;
        }
     
        public function setContact1($text)
        {
            $this->_contact1 = (string) $text;
            return $this;
        }
     
        public function getContact1()
        {
            return $this->_contact1;
        }


        public function setContact2($text)
        {
            $this->_contact2 = (string) $text;
            return $this;
        }
     
        public function getContact2()
        {
            return $this->_contact2;
        }


        public function setContact3($text)
        {
            $this->_contact3 = (string) $text;
            return $this;
        }
     
        public function getContact3()
        {
            return $this->_contact3;
        }


        public function setContact4($text)
        {
            $this->_contact4 = (string) $text;
            return $this;
        }
     
        public function getContact4()
        {
            return $this->_contact4;
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