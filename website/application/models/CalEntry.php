<?php
    // application/models/CalEntry.php
     
    class Application_Model_CalEntry
    {
        protected $_id;
        protected $_groupid;
        protected $_extforid;
        protected $_createby;
        protected $_date;
        protected $_time;
        protected $_moddate;
        protected $_modtime;
        protected $_duration;
        protected $_duedate;
        protected $_duetime;
        protected $_priority;
        protected $_type;
        protected $_access;
        protected $_name;
        protected $_location;
        protected $_url;
        protected $_completed;
        protected $_description;
     
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
                throw new Exception('Invalid CalEntry property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid CalEntry property');
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
     
        public function set_Groupid($groupid)
        {
            $this->_groupid = (string) $groupid;
            return $this;
        }
     
        public function getGroupid()
        {
            return $this->_groupid;
        }
     
        public function set_Extforid($text)
        {
            $this->_extforid = (string) $text;
            return $this;
        }
     
        public function getExtforid()
        {
            return $this->_extforid;
        }
     
        public function setCreateby($text)
        {
            $this->_createby = (string) $text;
            return $this;
        }
     
        public function getCreateby()
        {
            return $this->_createby;
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
     
  
        public function setModdate($ts)
        {
            $this->_moddate = $ts;
            return $this;
        }
     
        public function getModdate()
        {
            return $this->_moddate;
        }
     
  
        public function setModtime($ts)
        {
            $this->_modtime = $ts;
            return $this;
        }
     
        public function getModtime()
        {
            return $this->_modtime;
        }
     
  
        public function setDuration($ts)
        {
            $this->_duration = $ts;
            return $this;
        }
     
        public function getDuration()
        {
            return $this->_duration;
        }
     
  
        public function setDuedate($ts)
        {
            $this->_duedate = $ts;
            return $this;
        }
     
        public function getDuedate()
        {
            return $this->_duedate;
        }
     
  
        public function setDuetime($ts)
        {
            $this->_duetime = $ts;
            return $this;
        }
     
        public function getDuetime()
        {
            return $this->_duetime;
        }
     
  
        public function setPriority($ts)
        {
            $this->_priority = $ts;
            return $this;
        }
     
        public function getPriority()
        {
            return $this->_priority;
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
     
  
        public function setAccess($ts)
        {
            $this->_access = $ts;
            return $this;
        }
     
        public function getAccess()
        {
            return $this->_access;
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
     
  
        public function setLocation($ts)
        {
            $this->_location = $ts;
            return $this;
        }
     
        public function getLocation()
        {
            return $this->_location;
        }
     
  
        public function setUrl($ts)
        {
            $this->_url = $ts;
            return $this;
        }
     
        public function getUrl()
        {
            return $this->_url;
        }
     
  
        public function setCompleted($ts)
        {
            $this->_completed = $ts;
            return $this;
        }
     
        public function getCompleted()
        {
            return $this->_completed;
        }
          
        public function setDescription($ts)
        {
            $this->_description = $ts;
            return $this;
        }
     
        public function getDescription()
        {
            return $this->_description;
        }
     
  

    }
?>