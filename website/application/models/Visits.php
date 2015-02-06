<?php
    // application/models/Visits.php
     
    class Application_Model_Visits
    {
        protected $_id;
        protected $_personid;
        protected $_visitdate;
        protected $_service;
     
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
                throw new Exception('Invalid visits property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid visits property');
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
     
        public function setPersonid($personid)
        {
            $this->_personid = (string) $personid;
            return $this;
        }
     
        public function getPersonid()
        {
            return $this->_personid;
        }

        public function setService($text)
        {
            $this->_service = (string) $text;
            return $this;
        }
     
        public function getService()
        {
            return $this->_service;
        }

        public function setVisitdate($text)
        {
            $this->_visitdate = (string) $text;
            return $this;
        }
     
        public function getVisitdate()
        {
            return $this->_visitdate;
        }

    }
?>