<?php
    class Application_Form_DataMaintVisits extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('dataMaintVisitsForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('dataMaintVisits');


          $visitdate = new Zend_Form_Element_Text('visitdate');
          $visitdate->setLabel('Visit date');
          $visitdate->setAttrib('size', '10');


          $personid = new Zend_Form_Element_Text('personid');
          $personid->setLabel('Person ID');
          $personid->setAttrib('size', '5');


          $ebutton = new Zend_Form_Element_Submit('ebutton');
          $ebutton->setLabel('ENTER');

          $dbutton = new Zend_Form_Element_Submit('dbutton');
          $dbutton->setLabel('DELETE');

          $this->addElements(array(
              $personid, $visitdate,
              $hidden, $ebutton, $dbutton, 
              ));

          $this->setMethod('post');

       }
    }

?>
