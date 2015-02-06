<?php
    class Application_Form_REEnroll2ad extends Application_Form_REEnroll2d 
    {
       public function __construct($options = null) 
       {
            parent::__construct($options);
  
            $this->setName('reenroll2adForm');
            
            $hidden = new Zend_Form_Element_Hidden('hiddenName');
            $hidden->setValue('reenroll2adForm');
              
            $aldbutton = new Zend_Form_Element_Submit('aldbutton');
            $aldbutton->setLabel('CHANGE');

            $this->addElements(array(
                $hidden, $aldbutton, 
                ));

          $this->setMethod('post');
        }
    }

?>
