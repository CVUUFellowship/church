<?php
    // application/models/LibAudioCatalog.php
     
    class Application_Model_LibAudioCatalog
    {
        protected $_id;
        protected $_date;
        protected $_presenter;
        protected $_title;
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
                throw new Exception('Invalid LibAudioCatalog property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid LibAudioCatalog property');
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

        public function setPresenter($text)
        {
            $this->_presenter = (string) $text;
            return $this;
        }
     
        public function getPresenter()
        {
            return $this->_presenter;
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
     
        public function getId()
        {
            return $this->_id;
        }
     
        public function setId($text)
        {
            $this->_id = (string) $text;
            return $this;
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