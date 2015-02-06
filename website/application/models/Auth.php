<?PHP
    // application/models/Auth.php
     
    class Application_Model_Auth
    {
        protected $_memberid;
        protected $_passwd;
        protected $_team;
        protected $_level;
        protected $_status;
        protected $_lastlogin;
        protected $_logincount;
        protected $_id;
     
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
                throw new Exception('Invalid auth property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid auth property');
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
     
        public function setMemberid($text)
        {
            $this->_memberid = (string) $text;
            return $this;
        }
     
        public function getMemberid()
        {
            return $this->_memberid;
        }
     
        public function setPasswd($text)
        {
            $this->_passwd = (string) $text;
            return $this;
        }
     
        public function getPasswd()
        {
            return $this->_passwd;
        }
     
        public function setTeam($text)
        {
            $this->_team = (string) $text;
            return $this;
        }
     
        public function getTeam()
        {
            return $this->_team;
        }
     
        public function setLevel($level)
        {
            $this->_level = (int) $level;
            return $this;
        }
     
        public function getLevel()
        {
            return $this->_level;
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
     
        public function setLastlogin($text)
        {
            $this->_lastlogin = (string) $text;
            return $this;
        }
     
        public function getLastlogin()
        {
            return $this->_lastlogin;
        }
     
        public function setLogincount($text)
        {
            $this->_logincount = (string) $text;
            return $this;
        }
     
        public function getLogincount()
        {
            return $this->_logincount;
        }
     
     }
?>