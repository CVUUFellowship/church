<?php
    // application/models/Policies.php
     
    class Application_Model_Policies
    {
        protected $_id;
        protected $_number;
        protected $_status;
        protected $_belowpolicy;
        protected $_policytype;
        protected $_level;
        protected $_name;
        protected $_description;
        protected $_revision;
        protected $_submitdate;
        protected $_approvaldate;
        protected $_pdffile;
        protected $_rtffile;
        protected $_timestamp;
     
        public function __construct(array $options = null)
        {
            if (is_array($options)) {
                $this->setOptions($options);
            }
        }
     
        public function __set($belowpolicy, $value)
        {
            $method = 'set' . $belowpolicy;
            if (('mapper' == $belowpolicy) || !method_exists($this, $method)) {
                throw new Exception('Invalid Policies property');
            }
            $this->$method($value);
        }
     
        public function __get($belowpolicy)
        {
            $method = 'get' . $belowpolicy;
            if (('mapper' == $belowpolicy) || !method_exists($this, $method)) {
                throw new Exception('Invalid Policies property');
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
     
        public function setNumber($number)
        {
            $this->_number = (string) $number;
            return $this;
        }
     
        public function getNumber()
        {
            return $this->_number;
        }
     
        public function setBelowpolicy($status)
        {
            $this->_status = (string) $status;
            return $this;
        }
     
        public function getBelowpolicy()
        {
            return $this->_status;
        }

        public function setStatus($text)
        {
            $this->_belowpolicy = (string) $text;
            return $this;
        }
     
        public function getStatus()
        {
            return $this->_belowpolicy;
        }
     
        public function setPolicytype($text)
        {
            $this->_policytype = (string) $text;
            return $this;
        }
     
        public function getPolicytype()
        {
            return $this->_policytype;
        }
     
        public function setLevel($text)
        {
            $this->_level = (string) $text;
            return $this;
        }
     
        public function getLevel()
        {
            return $this->_level;
        }
     
        public function setName($text)
        {
            $this->_name = (string) $text;
            return $this;
        }
     
        public function getName()
        {
            return $this->_name;
        }
     
        public function setDescription($text)
        {
            $this->_description = (string) $text;
            return $this;
        }
     
        public function getDescription()
        {
            return $this->_description;
        }
     
        public function setRevision($stat)
        {
            $this->_revision = $stat;
            return $this;
        }
     
        public function getRevision()
        {
            return $this->_revision;
        }
     
        public function setSubmitdate($text)
        {
            $this->_submitdate = (string) $text;
            return $this;
        }
     
        public function getSubmitdate()
        {
            return $this->_submitdate;
        }
     
        public function setRtffile($text)
        {
            $this->_rtffile = (string) $text;
            return $this;
        }
     
        public function getRtffile()
        {
            return $this->_rtffile;
        }
     
        public function setPdffile($text)
        {
            $this->_pdffile = (string) $text;
            return $this;
        }
     
        public function getPdffile()
        {
            return $this->_pdffile;
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