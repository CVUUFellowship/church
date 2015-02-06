<?php
    // application/models/Announcements.php
     
    class Application_Model_Announcements
    {
        protected $_id;
        protected $_date;
        protected $_xdate;
        protected $_time;
        protected $_place;
        protected $_contact;
        protected $_title;
        protected $_description;
        protected $_link;
        protected $_linktext;
        protected $_owner;
        protected $_type;
        protected $_status;
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
                throw new Exception('Invalid announcements property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid announcements property');
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
     
        public function setDate($date)
        {
            $this->_date = (string) $date;
            return $this;
        }
     
        public function getDate()
        {
            return $this->_date;
        }

        public function setXdate($text)
        {
            $this->_xdate = (string) $text;
            return $this;
        }
     
        public function getXdate()
        {
            return $this->_xdate;
        }
     
        public function setTime($text)
        {
            $this->_time = (string) $text;
            return $this;
        }
     
        public function getTime()
        {
            return $this->_time;
        }
     
        public function setPlace($text)
        {
            $this->_place = (string) $text;
            return $this;
        }
     
        public function getPlace()
        {
            return $this->_place;
        }
     
        public function setContact($stat)
        {
            $this->_contact = $stat;
            return $this;
        }
     
        public function getContact()
        {
            return $this->_contact;
        }
     
        public function setDescription($text)
        {
            $this->_description = (string) $text;
            return $this;
        }
     
        public function getDescription()
        {
            return $this->_description;
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
     
        public function setLink($text)
        {
            $this->_link = (string) $text;
            return $this;
        }
     
        public function getLink()
        {
            return $this->_link;
        }
     
        public function setLinktext($text)
        {
            $this->_linktext = (string) $text;
            return $this;
        }
     
        public function getLinktext()
        {
            return $this->_linktext;
        }
     
        public function setOwner($text)
        {
            $this->_owner = (string) $text;
            return $this;
        }
     
        public function getOwner()
        {
            return $this->_owner;
        }
     
        public function setType($text)
        {
            $this->_type = (string) $text;
            return $this;
        }
     
        public function getType()
        {
            return $this->_type;
        }
     
        public function setStatus($text)
        {
            $this->_status = (string) $text;
            return $this;
        }
     
        public function getStatus()
        {
            return $this->_status;
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