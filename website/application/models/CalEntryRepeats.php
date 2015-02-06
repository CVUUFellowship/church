<?php
    // application/models/CalEntryRepeats.php
     
    class Application_Model_CalEntryRepeats
    {
        protected $_id;
        protected $_type;
        protected $_end;
        protected $_endtime;
        protected $_endbymonth;
        protected $_frequency;
        protected $_days;
        protected $_bymonth;
        protected $_bymonthday;
        protected $_byday;
        protected $_bysetpos;
        protected $_byweekno;
        protected $_byyearday;
        protected $_wkst;
        protected $_count;
     
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
                throw new Exception('Invalid CalEntryRepeats property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid CalEntryRepeats property');
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
     
        public function setEnd($end)
        {
            $this->_end = (string) $end;
            return $this;
        }
     
        public function getEnd()
        {
            return $this->_end;
        }
     
        public function setEndtime($endtime)
        {
            $this->_endtime = (string) $endtime;
            return $this;
        }
     
        public function getEndtime()
        {
            return $this->_endtime;
        }
     
        public function setEndbymonth($text)
        {
            $this->_endbymonth = (string) $text;
            return $this;
        }
     
        public function getEndbymonth()
        {
            return $this->_endbymonth;
        }
     
        public function setFrequency($text)
        {
            $this->_frequency = (string) $text;
            return $this;
        }
     
        public function getFrequency()
        {
            return $this->_frequency;
        }
     
  
        public function setDays($ts)
        {
            $this->_days = $ts;
            return $this;
        }
     
        public function getDays()
        {
            return $this->_days;
        }
     
  
        public function setBymonth($ts)
        {
            $this->_bymonth = $ts;
            return $this;
        }
     
        public function getBymonth()
        {
            return $this->_bymonth;
        }
     
  
        public function setBymonthday($ts)
        {
            $this->_bymonthday = $ts;
            return $this;
        }
     
        public function getBymonthday()
        {
            return $this->_bymonthday;
        }
     
  
        public function setByday($ts)
        {
            $this->_byday = $ts;
            return $this;
        }
     
        public function getByday()
        {
            return $this->_byday;
        }
     
  
        public function setBysetpos($ts)
        {
            $this->_bysetpos = $ts;
            return $this;
        }
     
        public function getBysetpos()
        {
            return $this->_bysetpos;
        }
     
  
        public function setByweekno($ts)
        {
            $this->_byweekno = $ts;
            return $this;
        }
     
        public function getByweekno()
        {
            return $this->_byweekno;
        }
     
  
        public function setByyearday($ts)
        {
            $this->_byyearday = $ts;
            return $this;
        }
     
        public function getByyearday()
        {
            return $this->_byyearday;
        }
     
  
        public function setWkst($ts)
        {
            $this->_wkst = $ts;
            return $this;
        }
     
        public function getWkst()
        {
            return $this->_wkst;
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
     
  
        public function setCount($ts)
        {
            $this->_count = $ts;
            return $this;
        }
     
        public function getCount()
        {
            return $this->_count;
        }
     

    }
?>