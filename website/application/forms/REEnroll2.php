<?php
    class Application_Form_REEnroll2 extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $dummy = new Zend_Form_Element_hidden('dummy');
          $dummy2 = new Zend_Form_Element_hidden('dummy2');

          $firstname = new Zend_Form_Element_Text('firstname');
          $firstname->setLabel('First name begins');
          $firstname->setAttrib('size', '15');

          $lastname = new Zend_Form_Element_Text('lastname');
          $lastname->setLabel('Last name begins');
          $lastname->setAttrib('size', '30');

          $status = new Zend_Form_Element_Text('status');
          $status->setLabel('Status');
          $status->setAttrib('size', '10');
             
          $keys = new Zend_Form_Element_hidden('keys');
          $values = new Zend_Form_Element_hidden('values');

          $this->addElements(array(
              $firstname, $lastname, $status,
              ));

          $this->setMethod('post');

       }
    }

?>
