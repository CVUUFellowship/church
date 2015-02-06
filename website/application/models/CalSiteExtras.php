<?php
    // application/models/CalSiteExtras.php
     
    class Application_Model_CalSiteExtras
    {
        protected $_id;
        protected $_name;
        protected $_type;
        protected $_date;
        protected $_remind;
        protected $_data;
     
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
                throw new Exception('Invalid CalSiteExtras property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid CalSiteExtras property');
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
     
        public function setRemind($remind)
        {
            $this->_remind = (string) $remind;
            return $this;
        }
     
        public function getRemind()
        {
            return $this->_remind;
        }
     
        public function setData($text)
        {
            $this->_data = (string) $text;
            return $this;
        }
     
        public function getData()
        {
            return $this->_data;
        }
     
  
        public function setDate($ts)
        {
            $this->_date = $ts;
            return $this;
        }
     
        public function getDate()
        {
            return $this->_date;
        }
     
  
        public function setTime($ts)
        {
            $this->_time = $ts;
            return $this;
        }
     
        public function getTime()
        {
            return $this->_time;
        }
     
  
  
        public function setType($ts)
        {
            $this->_type = $ts;
            return $this;
        }
     
        public function getType()
        {
            return $this->_type;
        }
     
  
        public function setName($ts)
        {
            $this->_name = $ts;
            return $this;
        }
     
        public function getName()
        {
            return $this->_name;
        }
     

    }
?>