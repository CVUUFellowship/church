<?php
    class Application_Form_LibraryCatalogEntry extends Zend_Form {


       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('librarycatalogentryForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('librarycatalogentryentry');

          $title = new Zend_Form_Element_Textarea('title');
          $title->setLabel('Title');
          $title->setAttrib('cols', '50')
              ->setAttrib('rows', '2');

          $author = new Zend_Form_Element_Text('author');
          $author->setLabel('Author');
          $author->setAttrib('size', '40');

          $dummy = new Zend_Form_Element_hidden('dummy');
          $dummy2 = new Zend_Form_Element_hidden('dummy2');
          $dummy3 = new Zend_Form_Element_hidden('dummy3');
          $dummy4 = new Zend_Form_Element_hidden('dummy4');
          $dummy5 = new Zend_Form_Element_hidden('dummy5');

          $callnumber = new Zend_Form_Element_Text('callnumber');
          $callnumber->setLabel('Call Number');
          $callnumber->setAttrib('size', '15');

          $subject1 = new Zend_Form_Element_Text('subject1');
          $subject1->setLabel('Subject 1');
          $subject1->setAttrib('size', '60');

          $subject2 = new Zend_Form_Element_Text('subject2');
          $subject2->setLabel('Subject 2');
          $subject2->setAttrib('size', '60');

          $subject3 = new Zend_Form_Element_Text('subject3');
          $subject3->setLabel('Subject 3');
          $subject3->setAttrib('size', '60');

          $subject4 = new Zend_Form_Element_Text('subject4');
          $subject4->setLabel('Subject 4');
          $subject4->setAttrib('size', '60');

          $publisher = new Zend_Form_Element_Text('publisher');
          $publisher->setLabel('Publisher');
          $publisher->setAttrib('size', '60');

          $date = new Zend_Form_Element_Text('date');
          $date->setLabel('Date');
          $date->setAttrib('size', '10');

          $price = new Zend_Form_Element_Text('price');
          $price->setLabel('Price');
          $price->setAttrib('size', '6');

          $number = new Zend_Form_Element_Text('number');
          $number->setLabel('Number');
          $number->setAttrib('size', '4');

          $cbutton = new Zend_Form_Element_Submit('cbutton');
          $cbutton->setLabel('COMMIT');

          $xbutton = new Zend_Form_Element_Submit('xbutton');
          $xbutton->setLabel('CLEAR');
             
          $ebutton = new Zend_Form_Element_Submit('ebutton');
          $ebutton->setLabel('ENTER');
             
          $dbutton = new Zend_Form_Element_Submit('dbutton');
          $dbutton->setLabel('DONE');

          $rbutton = new Zend_Form_Element_Submit('rbutton');
          $rbutton->setLabel('REMOVE');


          
          $num = new Zend_Form_Element_hidden('num');
          $keys = new Zend_Form_Element_hidden('keys');
          $values = new Zend_Form_Element_hidden('values');


          $this->addElements(array($hidden,
              $number, $dummy3, $hidden, $title, $dummy2, $author, $callnumber, 
              $subject1, $subject2, $subject3, $subject4, 
              $publisher, $date, $price, 
              $dummy, $ebutton, $xbutton, $cbutton, 
              $dummy4, $dbutton, $dummy5, $rbutton,
              $num, $keys, $values,
              ));

          $this->setMethod('post');
          $this->setAction('/support/librarycatchange');

       }
    }

?>
