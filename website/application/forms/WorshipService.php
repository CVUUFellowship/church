<?php
    class Application_Form_WorshipService extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('WorshipServiceForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('WorshipServiceForm');

          $servicedate = new Zend_Form_Element_Text('servicedate');
          $servicedate->setLabel('Date');
          $servicedate->setAttrib('size', '10');

          $title = new Zend_Form_Element_Text('title');
          $title->setLabel('Title');
          $title->setAttrib('size', '50');

          $presenter = new Zend_Form_Element_Text('presenter');
          $presenter->setLabel('Presenter');
          $presenter->setAttrib('size', '30');

          $summary = new Zend_Form_Element_Textarea('summary');
          $summary->setLabel('Summary');
          $summary->setAttrib('cols', '60')
              ->setAttrib('rows', '10');             

          $sbutton = new Zend_Form_Element_Submit('submit');
          $sbutton->setLabel('SUBMIT');

          
          $this->addElements(array($servicedate, $title, $presenter, $summary,
              $sbutton));
              
          $this->setMethod('post');

       }
    }

?>