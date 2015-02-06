<?PHP
    // application/models/Menus.php
     
    class Application_Model_Menus
    {
        protected $_page;
        protected $_name;
        protected $_position;
        protected $_level;
        protected $_text;
        protected $_type;
        protected $_item;
        protected $_timestamp;
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
                throw new Exception('Invalid menus property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid menus property');
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
     
        public function setPage($page)
        {
            $this->_page = (string) $page;
            return $this;
        }
     
        public function getPage()
        {
            return $this->_page;
        }
     
        public function setName($name)
        {
            $this->_name = (string) $name;
            return $this;
        }
     
        public function getName()
        {
            return $this->_name;
        }
     
        public function setPosition($position)
        {
            $this->_position = (string) $position;
            return $this;
        }
     
        public function getPosition()
        {
            return $this->_position;
        }
     
        public function setLevel($level)
        {
            $this->_level = (string) $level;
            return $this;
        }
     
        public function getLevel()
        {
            return $this->_level;
        }
     
        public function setText($text)
        {
            $this->_text = (string) $text;
            return $this;
        }
     
        public function getText()
        {
            return $this->_text;
        }
     
        public function setType($type)
        {
            $this->_type = (string) $type;
            return $this;
        }
     
        public function getType()
        {
            return $this->_type;
        }
     
        public function setItem($item)
        {
            $this->_item = (string) $item;
            return $this;
        }
     
        public function getItem()
        {
            return $this->_item;
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