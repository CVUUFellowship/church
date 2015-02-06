<?php
    // application/models/ChartersGroups.php
     
    class Application_Model_ChartersGroups
    {
        protected $_id;
        protected $_groupname;
        protected $_grouptype;
        protected $_purpose;
        protected $_inclusivepolicy;
        protected $_confidentialitypolicy;
        protected $_numbermembers;
        protected $_meetinglocation;
        protected $_meetings;
        protected $_noncvuufpolicy;
        protected $_approvaldate;
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
                throw new Exception('Invalid CharterGroups property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid CharterGroups property');
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
     
        public function setGroupname($groupname)
        {
            $this->_groupname = (string) $groupname;
            return $this;
        }
     
        public function getGroupname()
        {
            return $this->_groupname;
        }

        public function setGrouptype($text)
        {
            $this->_grouptype = (string) $text;
            return $this;
        }
     
        public function getGrouptype()
        {
            return $this->_grouptype;
        }
     
        public function setPurpose($text)
        {
            $this->_purpose = (string) $text;
            return $this;
        }
     
        public function getPurpose()
        {
            return $this->_purpose;
        }
     
        public function setInclusivepolicy($text)
        {
            $this->_inclusivepolicy = (string) $text;
            return $this;
        }
     
        public function getInclusivepolicy()
        {
            return $this->_inclusivepolicy;
        }
     
        public function setConfidentialitypolicy($text)
        {
            $this->_confidentialitypolicy = (string) $text;
            return $this;
        }
     
        public function getConfidentialitypolicy()
        {
            return $this->_confidentialitypolicy;
        }
     
        public function setNumbermembers($stat)
        {
            $this->_numbermembers = $stat;
            return $this;
        }
     
        public function getNumbermembers()
        {
            return $this->_numbermembers;
        }
     
        public function setMeetinglocation($text)
        {
            $this->_meetinglocation = (string) $text;
            return $this;
        }
     
        public function getMeetinglocation()
        {
            return $this->_meetinglocation;
        }
     
        public function setMeetings($text)
        {
            $this->_meetings = (string) $text;
            return $this;
        }
     
        public function getMeetings()
        {
            return $this->_meetings;
        }
     
        public function setNoncvuufpolicy($text)
        {
            $this->_noncvuufpolicy = (string) $text;
            return $this;
        }
     
        public function getNoncvuufpolicy()
        {
            return $this->_noncvuufpolicy;
        }
     
        public function setApprovaldate($text)
        {
            $this->_approvaldate = (string) $text;
            return $this;
        }
     
        public function getApprovaldate()
        {
            return $this->_approvaldate;
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