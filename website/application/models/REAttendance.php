<?php
    // application/models/REAttendance.php
     
    class Application_Model_REAttendance
    {
        protected $_id;
        protected $_childid;
        protected $_date;
     
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
     
        public function setChildid($childid)
        {
            $this->_childid = (string) $childid;
            return $this;
        }
     
        public function getChildid()
        {
            return $this->_childid;
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

    }
?>