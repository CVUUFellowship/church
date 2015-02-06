<?php
    class Application_Form_REEnroll2pd extends Application_Form_REEnroll2d 
    {
       public function __construct($options = null) 
       {
            parent::__construct($options);
  
            $this->setName('reenroll2pdForm');
            
            $hidden = new Zend_Form_Element_Hidden('hiddenName');
            $hidden->setValue('reenroll2pdForm');
              
            $pdbutton = new Zend_Form_Element_Submit('pdbutton');
            $pdbutton->setLabel('CHANGE');

          $this->addElements(array(
              $hidden, $pdbutton,
              ));

          $this->setMethod('post');
        }
    }

?>
