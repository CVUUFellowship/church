<?php
    // application/models/RE.php
     
    class Application_Model_RE
    {
        protected $_id;
        protected $_inactive;
        protected $_registered;
        protected $_status;
        protected $_class;
        protected $_childid;
        protected $_birth;
        protected $_grade;
        protected $_gender;
        protected $_ppersonid;
        protected $_apersonid;
        protected $_allergies;
        protected $_foodallergies;
        protected $_allergymeds;
        protected $_health;
        protected $_behavissues;
        protected $_develissues;
        protected $_langissues;
        protected $_otherissues;
        protected $_medications;
        protected $_characteristics;
        protected $_othertext;
        protected $_receive;
        protected $_discuss;
        protected $_insurance;
        protected $_insnumber;
        protected $_signame;
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
                throw new Exception('Invalid re property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid re property');
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

        public function setRegistered($text)
        {
            $this->_registered = (string) $text;
            return $this;
        }
     
        public function getRegistered()
        {
            return $this->_registered;
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
     
        public function setClass($text)
        {
            $this->_class = (string) $text;
            return $this;
        }
     
        public function getClass()
        {
            return $this->_class;
        }
     
        public function setChildid($stat)
        {
            $this->_childid = $stat;
            return $this;
        }
     
        public function getChildid()
        {
            return $this->_childid;
        }
     
        public function setGrade($text)
        {
            $this->_grade = (string) $text;
            return $this;
        }
     
        public function getGrade()
        {
            return $this->_grade;
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
     
        public function setBirth($text)
        {
            $this->_birth = (string) $text;
            return $this;
        }
     
        public function getBirth()
        {
            return $this->_birth;
        }
     
     
        public function setPpersonid($text)
        {
            $this->_ppersonid = (string) $text;
            return $this;
        }
     
        public function getPpersonid()
        {
            return $this->_ppersonid;
        }
     
        public function setApersonid($text)
        {
            $this->_apersonid = (string) $text;
            return $this;
        }
     
        public function getApersonid()
        {
            return $this->_apersonid;
        }
     
        public function setAllergies($text)
        {
            $this->_allergies = (string) $text;
            return $this;
        }
     
        public function getAllergies()
        {
            return $this->_allergies;
        }
     
        public function setFoodallergies($text)
        {
            $this->_foodallergies = (string) $text;
            return $this;
        }
     
        public function getFoodallergies()
        {
            return $this->_foodallergies;
        }
     
        public function setAllergymeds($text)
        {
            $this->_allergymeds = (string) $text;
            return $this;
        }
     
        public function getAllergymeds()
        {
            return $this->_allergymeds;
        }
     
        public function setHealth($text)
        {
            $this->_health = (string) $text;
            return $this;
        }
     
        public function getHealth()
        {
            return $this->_health;
        }
     
        public function setBehavissues($text)
        {
            $this->_behavissues = (string) $text;
            return $this;
        }
     
        public function getBehavissues()
        {
            return $this->_behavissues;
        }
     
        public function setDevelissues($text)
        {
            $this->_develissues = (string) $text;
            return $this;
        }
     
        public function getDevelissues()
        {
            return $this->_develissues;
        }
     
        public function setLangissues($text)
        {
            $this->_langissues = (string) $text;
            return $this;
        }
     
        public function getLangissues()
        {
            return $this->_langissues;
        }
     
        public function setOtherissues($text)
        {
            $this->_otherissues = (string) $text;
            return $this;
        }
     
        public function getOtherissues()
        {
            return $this->_otherissues;
        }
     
        public function setMedications($text)
        {
            $this->_medications = (string) $text;
            return $this;
        }
     
        public function getMedications()
        {
            return $this->_medications;
        }
     
        public function setCharacteristics($text)
        {
            $this->_characteristics = (string) $text;
            return $this;
        }
     
        public function getCharacteristics()
        {
            return $this->_characteristics;
        }
     
        public function setOthertext($text)
        {
            $this->_othertext = (string) $text;
            return $this;
        }
     
        public function getOthertext()
        {
            return $this->_othertext;
        }
     
        public function setReceive($text)
        {
            $this->_receive = (string) $text;
            return $this;
        }
     
        public function getReceive()
        {
            return $this->_receive;
        }
     
        public function setDiscuss($text)
        {
            $this->_discuss = (string) $text;
            return $this;
        }
     
        public function getDiscuss()
        {
            return $this->_discuss;
        }
     
        public function setInsurance($text)
        {
            $this->_insurance = (string) $text;
            return $this;
        }
     
        public function getInsurance()
        {
            return $this->_insurance;
        }
     
        public function setInsnumber($text)
        {
            $this->_insnumber = (string) $text;
            return $this;
        }
     
        public function getInsnumber()
        {
            return $this->_insnumber;
        }
     
        public function setSigname($text)
        {
            $this->_signame = (string) $text;
            return $this;
        }
     
        public function getSigname()
        {
            return $this->_signame;
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