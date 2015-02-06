<?php
    class Application_Form_DataPositions extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('datapositionsForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('datapositionsForm');

          $id = new Zend_Form_Element_Text('id');
          $id->setLabel('Class ID');
          $id->setAttrib('size', '4');

          $pid = new Zend_Form_Element_Hidden('pid');

          $sequence = new Zend_Form_Element_Text('sequence');
          $sequence->setLabel('Sequence');
          $sequence->setAttrib('size', '4');

          $name = new Zend_Form_Element_Text('name');
          $name->setLabel('Name');
          $name->setAttrib('size', '40');

          $title = new Zend_Form_Element_Text('title');
          $title->setLabel('Title');
          $title->setAttrib('size', '50');

          $class = new Zend_Form_Element_Multiselect('class');
          $class->setLabel('Class');
          $class->setAttrib('size', '6');
          $class->addMultiOptions(array())
              ->setSeparator('');                 
             
          $sbutton = new Zend_Form_Element_Submit('sbutton');
          $sbutton->setLabel('SUBMIT');
             
          $dbutton = new Zend_Form_Element_Submit('dbutton');
          $dbutton->setLabel('REMOVE');

          $dummy = new Zend_Form_Element_Hidden('dummy');

          $this->addElements(array($id, $pid, $sequence, $title, 
              $name, $class, $hidden, $sbutton, $dummy, $dbutton,
              ));
              
          $this->setMethod('post');
          $this->setAction('/data/positions');
       }
    }

?>
