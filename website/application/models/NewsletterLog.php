<?php
    // application/models/NewsletterLog.php
     
    class Application_Model_NewsletterLog
    {
        protected $_id;
        protected $_ip;
        protected $_edition;
     
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
                throw new Exception('Invalid NewsletterLog property');
            }
            $this->$method($value);
        }
     
        public function __get($name)
        {
            $method = 'get' . $name;
            if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception('Invalid NewsletterLog property');
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
     
        public function setIp($ip)
        {
            $this->_ip = (string) $ip;
            return $this;
        }
     
        public function getIp()
        {
            return $this->_ip;
        }
     
        public function setEdition($text)
        {
            $this->_edition = (string) $text;
            return $this;
        }
     
        public function getEdition()
        {
            return $this->_edition;
        }

    }
?>