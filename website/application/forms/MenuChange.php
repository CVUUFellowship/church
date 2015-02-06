<?php
    class Application_Form_MenuChange extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('menuchangeForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('menuchangeForm');

          $page = new Zend_Form_Element_Text('page');
          $page->setLabel('Page');

          $name = new Zend_Form_Element_Text('name');
          $name->setLabel('Name');

          $old = new Zend_Form_Element_hidden('old');

          $position = new Zend_Form_Element_Text('position');
          $position->setLabel('Position');
          $position->setAttrib('size', '4');

          $level = new Zend_Form_Element_Multiselect('level');
          $level->setLabel('Permission');
          $level->addMultiOptions(array())
              ->setSeparator('');                 

          $text = new Zend_Form_Element_Text('text');
          $text->setLabel('Text');
          $text->setAttrib('size', '50');

          $type = new Zend_Form_Element_Select('type');
          $type->setLabel('Status');
          $type->addMultiOptions(array(
                    'url' => 'url',
                    'menu' => 'menu', 
                    ))
              ->setSeparator('');                 

          $item = new Zend_Form_Element_Text('item');
          $item->setLabel('Item');
          $item->setAttrib('size', '60');

          $cbutton = new Zend_Form_Element_Submit('cancel');
          $cbutton->setLabel('CANCEL');
             
          $sbutton = new Zend_Form_Element_Submit('submit');
          $sbutton->setLabel('SUBMIT');

          $id = new Zend_Form_Element_hidden('id');
          
          $this->addElements(array($hidden,
              $page, $name, $position, $level, $text, $type,
              $item, $hidden, $id, $old, $sbutton, $cbutton,
              ));
              
          $this->setMethod('post');

       }
    }

?>
