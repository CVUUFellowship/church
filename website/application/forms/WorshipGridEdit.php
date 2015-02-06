<?php
    class Application_Form_WorshipGridEdit extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('WorshipgrideditForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('WorshipgrideditForm');

          $servicedate = new Zend_Form_Element_Text('servicedate');
          $servicedate->setLabel('Date');
          $servicedate->setAttrib('size', '10');

          $servicetime = new Zend_Form_Element_Text('servicetime');
          $servicetime->setLabel('Time if special');
          $servicetime->setAttrib('size', '10');

          $speaker = new Zend_Form_Element_Text('presenter');
          $speaker->setLabel('Speaker');
          $speaker->setAttrib('size', '30');

          $topic = new Zend_Form_Element_Text('topic');
          $topic->setLabel('Topic');
          $topic->setAttrib('size', '50');

          $performer = new Zend_Form_Element_Text('music');
          $performer->setLabel('Performer');
          $performer->setAttrib('size', '30');

          $specialmusic = new Zend_Form_Element_Text('specialmusic');
          $specialmusic->setLabel('Special music');
          $specialmusic->setAttrib('size', '50');

          $hymns = new Zend_Form_Element_Text('hymns');
          $hymns->setLabel('Hymns');
          $hymns->setAttrib('size', '16');

          $early = new Zend_Form_Element_Text('early');
          $early->setLabel('Early person');
          $early->setAttrib('size', '3');

          $late = new Zend_Form_Element_Text('late');
          $late->setLabel('Late person');
          $late->setAttrib('size', '3');

          $organizer = new Zend_Form_Element_Text('organizer');
          $organizer->setLabel('Organizer');
          $organizer->setAttrib('size', '3');

          $worshipassoc = new Zend_Form_Element_Text('worshipassoc');
          $worshipassoc->setLabel('Worship Associate');
          $worshipassoc->setAttrib('size', '30');

          $otherinfo = new Zend_Form_Element_Text('otherinfo');
          $otherinfo->setLabel('Other Info');
          $otherinfo->setAttrib('size', '80');

          $attearly = new Zend_Form_Element_Text('attearly');
          $attearly->setLabel('Early Attendance');
          $attearly->setAttrib('size', '3');

          $attlate = new Zend_Form_Element_Text('attlate');
          $attlate->setLabel('Late Attendance');
          $attlate->setAttrib('size', '3');
             
          $sbutton = new Zend_Form_Element_Submit('submit');
          $sbutton->setLabel('WRITE');
          $sbutton->setDecorators(array(
                     'ViewHelper',
                     array('Description', array('tag' => '', 'class' => 'description')),
                     array('HtmlTag', array('tag' => 'dd')),
                 ));

          $dbutton = new Zend_Form_Element_Submit('done');
          $dbutton->setLabel(' DONE ');
          $dbutton->setDecorators(array(
                     'ViewHelper',
                     array('Description', array('tag' => '', 'class' => 'description')),
                     array('HtmlTag', array('tag' => 'dd')),
                 ));

          $rbutton = new Zend_Form_Element_Submit('remove');
          $rbutton->setLabel('REMOVE');
          $rbutton->setDecorators(array(
                     'ViewHelper',
                     array('Description', array('tag' => '', 'class' => 'description')),
                     array('HtmlTag', array('tag' => 'dd')),
                 ));

          $year = new Zend_Form_Element_hidden('year');
          $id = new Zend_Form_Element_hidden('id');
          $dummy1 = new Zend_Form_Element_hidden('dummy1');
          $dummy2 = new Zend_Form_Element_hidden('dummy2');
          $dummy3 = new Zend_Form_Element_hidden('dummy3');
          
          $this->addElements(array($servicedate, $servicetime, $speaker, $topic, $performer,
              $specialmusic, $hymns, $early, $late, $organizer, $worshipassoc,
              $otherinfo, $attearly, $attlate, $hidden, $dummy3, $sbutton, $dummy1, $dbutton, $year, $dummy2, $rbutton,
              $id));
              
          $this->setMethod('post');

       }
    }

?>