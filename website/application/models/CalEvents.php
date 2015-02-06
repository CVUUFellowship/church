<?php
    // application/models/CalEvents.php
     
    class Application_Model_CalEvents
    {
        protected $_id;
        protected $_eventid;
        protected $_requesttime;
        protected $_requesterid;
        protected $_resultemail;
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
                throw new Exception('Invalid CalEvents property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid CalEvents property');
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
     
        public function setEventid($eventid)
        {
            $this->_eventid = (string) $eventid;
            return $this;
        }
     
        public function getEventid()
        {
            return $this->_eventid;
        }

        public function setRequesttime($text)
        {
            $this->_requesttime = (string) $text;
            return $this;
        }
     
        public function getRequesttime()
        {
            return $this->_requesttime;
        }
     
        public function setRequesterid($text)
        {
            $this->_requesterid = (string) $text;
            return $this;
        }
     
        public function getRequesterid()
        {
            return $this->_requesterid;
        }
     
        public function setResultemail($text)
        {
            $this->_resultemail = (string) $text;
            return $this;
        }
     
        public function getResultemail()
        {
            return $this->_resultemail;
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