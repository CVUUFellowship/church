<?php
    // application/models/People.php
     
    class Application_Model_People
    {
        protected $_id;
        protected $_inactive;
        protected $_firstname;
        protected $_lastname;
        protected $_creationdate;
        protected $_resigndate;
        protected $_membershipdate;
        protected $_birthdate;
        protected $_householdid;
        protected $_gender;
        protected $_status;
        protected $_photolink;
        protected $_pphone;
        protected $_email;
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
                throw new Exception('Invalid people property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid people property');
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
     
        public function setInactive($inactive)
        {
            $this->_inactive = (string) $inactive;
            return $this;
        }
     
        public function getInactive()
        {
            return $this->_inactive;
        }

        public function setFirstname($text)
        {
            $this->_firstname = (string) $text;
            return $this;
        }
     
        public function getFirstname()
        {
            return $this->_firstname;
        }
     
        public function setLastname($text)
        {
            $this->_lastname = (string) $text;
            return $this;
        }
     
        public function getLastname()
        {
            return $this->_lastname;
        }
     
        public function setCreationdate($text)
        {
            $this->_creationdate = (string) $text;
            return $this;
        }
     
        public function getCreationdate()
        {
            return $this->_creationdate;
        }
     
        public function setResigndate($text)
        {
            $this->_resigndate = (string) $text;
            return $this;
        }
     
        public function getResigndate()
        {
            return $this->_resigndate;
        }
     
        public function setMembershipdate($stat)
        {
            $this->_membershipdate = $stat;
            return $this;
        }
     
        public function getMembershipdate()
        {
            return $this->_membershipdate;
        }
     
        public function setBirthdate($text)
        {
            $this->_birthdate = (string) $text;
            return $this;
        }
     
        public function getBirthdate()
        {
            return $this->_birthdate;
        }
     
        public function setHouseholdid($text)
        {
            $this->_householdid = (string) $text;
            return $this;
        }
     
        public function getHouseholdid()
        {
            return $this->_householdid;
        }
     
        public function setGender($text)
        {
            $this->_gender = (string) $text;
            return $this;
        }
     
        public function getGender()
        {
            return $this->_gender;
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
     
        public function setZip($text)
        {
            $this->_photolink = (string) $text;
            return $this;
        }
     
        public function getPhotolink()
        {
            return $this->_photolink;
        }
     
        public function setPhotolink($text)
        {
            $this->_photolink = (string) $text;
            return $this;
        }
     
        public function getPphone()
        {
            return $this->_pphone;
        }
     
        public function setPphone($text)
        {
            $this->_pphone = (string) $text;
            return $this;
        }
     
        public function setEmail($email)
        {
            $this->_email = (string) $email;
            return $this;
        }
     
        public function getEmail()
        {
            return $this->_email;
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