<?php
    // application/models/Visitors.php
     
    class Application_Model_Visitors
    {
        protected $_id;
        protected $_inactive;
        protected $_signeddate;
        protected $_resigneddate;
        protected $_reference;
        protected $_comment;
        protected $_prioruu;
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
                throw new Exception('Invalid visitors property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid visitors property');
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
     
        public function setInactive($inactive)
        {
            $this->_inactive = (string) $inactive;
            return $this;
        }
     
        public function getInactive()
        {
            return $this->_inactive;
        }

        public function setComment($text)
        {
            $this->_comment = (string) $text;
            return $this;
        }
     
        public function getComment()
        {
            return $this->_comment;
        }

        public function setSigneddate($text)
        {
            $this->_signeddate = (string) $text;
            return $this;
        }
     
        public function getSigneddate()
        {
            return $this->_signeddate;
        }
     
        public function setResigneddate($text)
        {
            $this->_resigneddate = (string) $text;
            return $this;
        }
     
        public function getResigneddate()
        {
            return $this->_resigneddate;
        }
     
        public function setReference($text)
        {
            $this->_reference = (string) $text;
            return $this;
        }
     
        public function getReference()
        {
            return $this->_reference;
        }
     
        public function setPrioruu($text)
        {
            $this->_prioruu = (string) $text;
            return $this;
        }
     
        public function getPrioruu()
        {
            return $this->_prioruu;
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