<?php
    class Application_Form_AdminPersonSelect extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('adminpersonselectForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('adminpersonselectForm');
          
          $first = new Zend_Form_Element_Text('first');
          $first->setAttrib('size', '20');
          $first->setLabel('Start of first name');
          
          $last = new Zend_Form_Element_Text('last');
          $last->setAttrib('size', '20');
          $last->setLabel('Start of last name');
             
          $find = new Zend_Form_Element_Submit('find');
          $find->setLabel('FIND');
          
          $this->addElements(array(
              $first, $last, $hidden, $find));
              
          $this->setMethod('post');

       }
    }

?>
