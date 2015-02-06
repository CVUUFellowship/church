<?php
    // application/models/ChartersOrg.php
     
    class Application_Model_ChartersOrg
    {
        protected $_id;
        protected $_type;
        protected $_name;
        protected $_purpose;
        protected $_organization;
        protected $_leaderselection;
        protected $_leaderterm;
        protected $_memberterm;
        protected $_numbermembers;
        protected $_reportto;
        protected $_meetings;
        protected $_duties;
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
     
        public function setName($type)
        {
            $this->_type = (string) $type;
            return $this;
        }
     
        public function getName()
        {
            return $this->_type;
        }

        public function setType($text)
        {
            $this->_name = (string) $text;
            return $this;
        }
     
        public function getType()
        {
            return $this->_name;
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
     
        public function setOrganization($text)
        {
            $this->_organization = (string) $text;
            return $this;
        }
     
        public function getOrganization()
        {
            return $this->_organization;
        }
     
        public function setLeaderselection($text)
        {
            $this->_leaderselection = (string) $text;
            return $this;
        }
     
        public function getLeaderselection()
        {
            return $this->_leaderselection;
        }
     
        public function setLeaderterm($text)
        {
            $this->_leaderterm = (string) $text;
            return $this;
        }
     
        public function getLeaderterm()
        {
            return $this->_leaderterm;
        }
     
        public function setMemberterm($stat)
        {
            $this->_memberterm = $stat;
            return $this;
        }
     
        public function getMemberterm()
        {
            return $this->_memberterm;
        }
     
        public function setNumbermembers($text)
        {
            $this->_numbermembers = (string) $text;
            return $this;
        }
     
        public function getNumbermembers()
        {
            return $this->_numbermembers;
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
     
        public function setReportto($text)
        {
            $this->_reportto = (string) $text;
            return $this;
        }
     
        public function getReportto()
        {
            return $this->_reportto;
        }
     
        public function setDuties($text)
        {
            $this->_duties = (string) $text;
            return $this;
        }
     
        public function getDuties()
        {
            return $this->_duties;
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