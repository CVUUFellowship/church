<?php
    // application/models/Connections.php
     
    class Application_Model_Connections
    {
        protected $_id;
        protected $_peopleid;
        protected $_comments;
        protected $_angelid;
        protected $_inducted;
        protected $_inductiondontask;
        protected $_frienddate;
        protected $_classdate;
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
                throw new Exception('Invalid connections property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid connections property');
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
     
        public function setPeopleid($peopleid)
        {
            $this->_peopleid = (string) $peopleid;
            return $this;
        }
     
        public function getPeopleid()
        {
            return $this->_peopleid;
        }

        public function setComments($text)
        {
            $this->_comments = (string) $text;
            return $this;
        }
     
        public function getComments()
        {
            return $this->_comments;
        }
     
        public function setAngelid($text)
        {
            $this->_angelid = (string) $text;
            return $this;
        }
     
        public function getAngelid()
        {
            return $this->_angelid;
        }
     
        public function setInducted($text)
        {
            $this->_inducted = (string) $text;
            return $this;
        }
     
        public function getInducted()
        {
            return $this->_inducted;
        }
     
     
        public function setInductiondontask($text)
        {
            $this->_inductiondontask = (string) $text;
            return $this;
        }
     
        public function getInductiondontask()
        {
            return $this->_inductiondontask;
        }
     
        public function setClassdate($text)
        {
            $this->_classdate = (string) $text;
            return $this;
        }
     
        public function getClassdate()
        {
            return $this->_classdate;
        }
     
        public function setFrienddate($stat)
        {
            $this->_frienddate = $stat;
            return $this;
        }
     
        public function getFrienddate()
        {
            return $this->_frienddate;
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