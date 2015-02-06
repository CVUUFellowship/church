<?php
    class Application_Form_REEnrollnext extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('reenrollnextForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('reenrollnextForm');
             
          $nbutton = new Zend_Form_Element_Submit('nbutton');
          $nbutton->setLabel('NEXT');
//             
//          $rbutton = new Zend_Form_Element_Submit('rbutton');
//          $rbutton->setLabel('REVIEW');

          $keys = new Zend_Form_Element_hidden('keys');
          $values = new Zend_Form_Element_hidden('values');

          $this->addElements(array(
              $nbutton, $hidden, 
//              $rbutton,
              $keys, $values,
              ));

          $this->setMethod('post');

       }
    }

?>
