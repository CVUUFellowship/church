<?php
    // application/models/Worshipservices.php
     
    class Application_Model_Worshipservices
    {
        protected $_id;
        protected $_sunday;
        protected $_title;
        protected $_presenter;
        protected $_summary;
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
                throw new Exception('Invalid worshipservices property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid worshipservices property');
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
     
        public function setSunday($sunday)
        {
            $this->_sunday = (string) $sunday;
            return $this;
        }
     
        public function getSunday()
        {
            return $this->_sunday;
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
     
        public function setPresenter($text)
        {
            $this->_presenter = (string) $text;
            return $this;
        }
     
        public function getPresenter()
        {
            return $this->_presenter;
        }
     
        public function setSummary($text)
        {
            $this->_summary = (string) $text;
            return $this;
        }
     
        public function getSummary()
        {
            return $this->_summary;
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