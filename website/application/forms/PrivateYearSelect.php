<?php
    class Application_Form_PrivateYearSelect extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('privateyearselectForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('privateyearselectForm');

          $year = new Zend_Form_Element_Text('year');
          $year->setLabel('year');
          $year->setAttrib('size', '4');
            
          $sbutton = new Zend_Form_Element_Submit('submit');
          $sbutton->setLabel('YEAR');
          
          $this->addElements(array($hidden,
              $year, $sbutton,
              ));
              
          $this->setMethod('post');

       }
    }

?>
