<?php
    // application/models/LibCatalog.php
     
    class Application_Model_LibCatalog
    {
        protected $_title;
        protected $_author;
        protected $_callnumber;
        protected $_subject1;
        protected $_subject2;
        protected $_subject3;
        protected $_subject4;
        protected $_publisher;
        protected $_date;
        protected $_price;
        protected $_number;
        protected $_createdate;
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
                throw new Exception('Invalid LibCatalog property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid LibCatalog property');
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
     
        public function setTitle($title)
        {
            $this->_title = (string) $title;
            return $this;
        }
     
        public function getTitle()
        {
            return $this->_title;
        }

        public function setAuthor($text)
        {
            $this->_author = (string) $text;
            return $this;
        }
     
        public function getAuthor()
        {
            return $this->_author;
        }
     
        public function setCallnumber($text)
        {
            $this->_callnumber = (string) $text;
            return $this;
        }
     
        public function getCallnumber()
        {
            return $this->_callnumber;
        }
     
        public function setSubject1($text)
        {
            $this->_subject1 = (string) $text;
            return $this;
        }
     
        public function getSubject1()
        {
            return $this->_subject1;
        }
     
        public function setSubject2($text)
        {
            $this->_subject2 = (string) $text;
            return $this;
        }
     
        public function getSubject2()
        {
            return $this->_subject2;
        }
     
        public function setSubject3($text)
        {
            $this->_subject3 = $text;
            return $this;
        }
     
        public function getSubject3()
        {
            return $this->_subject3;
        }
     
        public function setSubject4($text)
        {
            $this->_subject4 = (string) $text;
            return $this;
        }
     
        public function getSubject4()
        {
            return $this->_subject4;
        }
     
        public function setPublisher($text)
        {
            $this->_publisher = (string) $text;
            return $this;
        }
     
        public function getPublisher()
        {
            return $this->_publisher;
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
     
        public function setPrice($text)
        {
            $this->_price = (string) $text;
            return $this;
        }
     
        public function getPrice()
        {
            return $this->_price;
        }
     
        public function setNumber($text)
        {
            $this->_number = (string) $text;
            return $this;
        }
     
        public function getNumber()
        {
            return $this->_number;
        }
     
        public function setCreateDate($text)
        {
            $this->_createdate = (string) $text;
            return $this;
        }
     
        public function getCreateDate()
        {
            return $this->_createdate;
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