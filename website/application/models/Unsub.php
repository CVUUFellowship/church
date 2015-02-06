<?php
    // application/models/Unsub.php
     
    class Application_Model_Unsub
    {
        protected $_id;
        protected $_all;
        protected $_weekly;
        protected $_newsletter;
        protected $_neighborhood;
        protected $_individual;
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
                throw new Exception('Invalid unsub property');
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
     
        public function setAll($all)
        {
            $this->_all = (string) $all;
            return $this;
        }
     
        public function getAll()
        {
            return $this->_all;
        }
     
        public function setWeekly($text)
        {
            $this->_weekly = (string) $text;
            return $this;
        }
     
        public function getWeekly()
        {
            return $this->_weekly;
        }
     
        public function setNewsletter($text)
        {
            $this->_newsletter = (string) $text;
            return $this;
        }
     
        public function getNewsletter()
        {
            return $this->_newsletter;
        }
     
        public function setNeighborhood($text)
        {
            $this->_neighborhood = (string) $text;
            return $this;
        }
     
        public function getNeighborhood()
        {
            return $this->_neighborhood;
        }
     
        public function setIndividual($text)
        {
            $this->_individual = (string) $text;
            return $this;
        }
     
        public function getIndividual()
        {
            return $this->_individual;
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