<?php
    class Application_Form_DataGroups extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('datagroupsForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('datagroupsForm');

          $id = new Zend_Form_Element_Text('id');
          $id->setLabel('Class ID');
          $id->setAttrib('size', '4');

          $pid = new Zend_Form_Element_Hidden('pid');

          $sequence = new Zend_Form_Element_Text('sequence');
          $sequence->setLabel('Sequence');
          $sequence->setAttrib('size', '4');

          $contact1 = new Zend_Form_Element_Text('contact1');
          $contact1->setLabel('Contact1');
          $contact1->setAttrib('size', '40');

          $contact2 = new Zend_Form_Element_Text('contact2');
          $contact2->setLabel('Contact2');
          $contact2->setAttrib('size', '40');

          $contact3 = new Zend_Form_Element_Text('contact3');
          $contact3->setLabel('Contact3');
          $contact3->setAttrib('size', '40');

          $contact4 = new Zend_Form_Element_Text('contact4');
          $contact4->setLabel('Contact4');
          $contact4->setAttrib('size', '40');

          $pub = new Zend_Form_Element_Radio('publicpage');
          $pub->setLabel('Public');
          $pub->addMultiOptions(array(
                    'yes' => 'yes',
                    'no' => 'no', 
                    ))
              ->setSeparator('');                 

          $title = new Zend_Form_Element_Text('title');
          $title->setLabel('Title');
          $title->setAttrib('size', '80');

          $class = new Zend_Form_Element_Multiselect('class');
          $class->setLabel('Class');
          $class->setAttrib('size', '6');
          $class->addMultiOptions(array())
              ->setSeparator('');                 
             
          $sbutton = new Zend_Form_Element_Submit('sbutton');
          $sbutton->setLabel('SUBMIT');
             
          $cbutton = new Zend_Form_Element_Submit('cbutton');
          $cbutton->setLabel('CLONE');
             
          $dbutton = new Zend_Form_Element_Submit('dbutton');
          $dbutton->setLabel('REMOVE');

          $dummy = new Zend_Form_Element_Hidden('dummy');

          $this->addElements(array($id, $pid, $sequence, $title, 
              $contact1, $contact2, $contact3, $contact4,
              $class, $pub, $hidden, $sbutton, $cbutton, $dummy, $dbutton,
              ));
              
          $this->setMethod('post');
          $this->setAction('/data/groups');
       }
    }

?>
