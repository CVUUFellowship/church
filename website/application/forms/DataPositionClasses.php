<?php
    class Application_Form_DataPositionClasses extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('datapositionclassesForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('datapositionclassesForm');

          $id = new Zend_Form_Element_Text('id');
          $id->setLabel('Class ID');
          $id->setAttrib('size', '4');

          $hid = new Zend_Form_Element_Hidden('hid');

          $sequence = new Zend_Form_Element_Text('sequence');
          $sequence->setLabel('Sequence');
          $sequence->setAttrib('size', '4');

          $title = new Zend_Form_Element_Text('title');
          $title->setLabel('Title');
          $title->setAttrib('size', '50');

          $sbutton = new Zend_Form_Element_Submit('sbutton');
          $sbutton->setLabel('SUBMIT');
             
          $dbutton = new Zend_Form_Element_Submit('dbutton');
          $dbutton->setLabel('REMOVE');

          $dummy = new Zend_Form_Element_Hidden('dummy');

          $this->addElements(array($id, $hid, $sequence, $title, 
              $hidden, $sbutton, $dummy, $dbutton,
              ));
              
          $this->setMethod('post');
          $this->setAction('/data/positionclasses');
       }
    }

?>
