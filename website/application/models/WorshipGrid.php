<?php
    // application/models/WorshipGrid.php
     
    class Application_Model_WorshipGrid
    {
        protected $_id;
        protected $_sunday;
        protected $_servicedate;
        protected $_servicetime;
        protected $_presenter;
        protected $_topic;
        protected $_music;
        protected $_specialmusic;
        protected $_hymns;
        protected $_early;
        protected $_late;
        protected $_organizer;
        protected $_worshipassoc;
        protected $_otherinfo;
        protected $_attearly;
        protected $_attlate;
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
                throw new Exception('Invalid worshipgrid property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid worshipgrid property');
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
     
        public function setSunday($sunday)
        {
            $this->_sunday = (string) $sunday;
            return $this;
        }
     
        public function getSunday()
        {
            return $this->_sunday;
        }
     
        public function setServicedate($text)
        {
            $this->_servicedate = (string) $text;
            return $this;
        }
     
        public function getServicedate()
        {
            return $this->_servicedate;
        }
     
        public function setServicetime($text)
        {
            $this->_servicetime = (string) $text;
            return $this;
        }
     
        public function getServicetime()
        {
            return $this->_servicetime;
        }
     
        public function setPresenter($text)
        {
            $this->_presenter = (string) $text;
            return $this;
        }
     
        public function getPresenter()
        {
            return $this->_presenter;
        }
     
        public function setTopic($text)
        {
            $this->_topic = (string) $text;
            return $this;
        }
     
        public function getTopic()
        {
            return $this->_topic;
        }
     
        public function setMusic($text)
        {
            $this->_music = (string) $text;
            return $this;
        }
     
        public function getMusic()
        {
            return $this->_music;
        }
     
        public function setSpecialmusic($text)
        {
            $this->_specialmusic = (string) $text;
            return $this;
        }
     
        public function getSpecialmusic()
        {
            return $this->_specialmusic;
        }
     
        public function setHymns($text)
        {
            $this->_hymns = (string) $text;
            return $this;
        }
     
        public function getHymns()
        {
            return $this->_hymns;
        }
     
        public function setEarly($text)
        {
            $this->_early = (string) $text;
            return $this;
        }
     
        public function getEarly()
        {
            return $this->_early;
        }
     
        public function setLate($text)
        {
            $this->_late = (string) $text;
            return $this;
        }
     
        public function getLate()
        {
            return $this->_late;
        }
     
        public function setOrganizer($text)
        {
            $this->_organizer = (string) $text;
            return $this;
        }
     
        public function getOrganizer()
        {
            return $this->_organizer;
        }
     
        public function setWorshipassoc($text)
        {
            $this->_worshipassoc = (string) $text;
            return $this;
        }
     
        public function getWorshipassoc()
        {
            return $this->_worshipassoc;
        }
     
        public function setOtherinfo($text)
        {
            $this->_otherinfo = (string) $text;
            return $this;
        }
     
        public function getOtherinfo()
        {
            return $this->_otherinfo;
        }
     
        public function setAttearly($text)
        {
            $this->_attearly = (string) $text;
            return $this;
        }
     
        public function getAttearly()
        {
            return $this->_attearly;
        }
     
        public function setAttlate($text)
        {
            $this->_attlate = (string) $text;
            return $this;
        }
     
        public function getAttlate()
        {
            return $this->_attlate;
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