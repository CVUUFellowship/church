<?php
    class Application_Form_PrivateHoodSelect extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('PrivatehoodselectForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('privatehoodselectForm');
             
          $select = new Zend_Form_Element_Submit('select');
          $select->setLabel('SELECT');
             
          $hood = new Zend_Form_Element_Select('hood');
          $hood->setLabel('Neighborhood');

          $name = new Zend_Form_Element_Hidden('name');
          $page = new Zend_Form_Element_Hidden('page');
          $old = new Zend_Form_Element_Hidden('old');
          
          $this->addElements(array(
              $hood, $select, $hidden, $name, $page, $old));
              
          $this->setMethod('post');

       }
    }

?>
