<?php
    // application/models/CalRooms.php
     
    class Application_Model_CalRooms
    {
        protected $_id;
        protected $_roomname;
        protected $_roomcode;
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
                throw new Exception('Invalid calRooms property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid calRooms property');
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

        public function setRoomname($text)
        {
            $this->_roomname = (string) $text;
            return $this;
        }
     
        public function getRoomname()
        {
            return $this->_roomname;
        }
     
        public function setRoomcode($text)
        {
            $this->_roomcode = (string) $text;
            return $this;
        }
     
        public function getRoomcode()
        {
            return $this->_roomcode;
        }
     


        public function setTimestamp($text)
        {
            $this->_timestamp = (string) $text;
            return $this;
        }
     
        public function getTimestamp()
        {
            return $this->_timestamp;
        }
    }
?>