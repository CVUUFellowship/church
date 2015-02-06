<?php
    class Application_Form_MenuPageSelect extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('menupageselectForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('menupageselectForm');
             
          $select = new Zend_Form_Element_Submit('select');
          $select->setLabel('SELECT');
          $select->setDecorators(array(
                     'ViewHelper',
                     array('Description', array('tag' => '', 'class' => 'description')),
                     array('HtmlTag', array('tag' => 'div')),
                 ));
             
          $selpage = new Zend_Form_Element_Radio('selpage');
          $selpage->setSeparator('');                 
          $selpage->setDecorators(array(
                     'ViewHelper',
                     array('HtmlTag', array('tag' => 'div')),
                 ));

          $name = new Zend_Form_Element_Hidden('name');
          $page = new Zend_Form_Element_Hidden('page');
          $old = new Zend_Form_Element_Hidden('old');
          
          $this->addElements(array(
              $selpage, $select, $hidden, $name, $page, $old));
              
          $this->setMethod('post');

       }
    }

?>
