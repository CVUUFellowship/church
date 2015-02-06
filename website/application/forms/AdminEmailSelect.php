<?php
    class Application_Form_AdminEmailSelect extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('adminemailselectForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('adminemailselectForm');
          
          $email = new Zend_Form_Element_Text('email');
          $email->setAttrib('size', '50');
          $email->setLabel('Start of email');
           
          $findemail = new Zend_Form_Element_Submit('findemail');
          $findemail->setLabel('FIND');
          
          $this->addElements(array(
              $email, $hidden, $findemail));
              
          $this->setMethod('post');

       }
    }

?>
