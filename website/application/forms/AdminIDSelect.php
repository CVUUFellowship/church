<?php
    class Application_Form_AdminIDSelect extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('adminidselectForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('adminidselectForm');
          
          $id = new Zend_Form_Element_Text('id');
          $id->setAttrib('size', '4');
          $id->setLabel('Person ID');
           
          $findid = new Zend_Form_Element_Submit('findid');
          $findid->setLabel('FIND');
          
          $this->addElements(array(
              $id, $hidden, $findid));
              
          $this->setMethod('post');

       }
    }

?>
