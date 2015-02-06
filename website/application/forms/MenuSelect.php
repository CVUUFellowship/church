<?php
    class Application_Form_MenuSelect extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('menuselectForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('menuselectForm');
             
          $build = new Zend_Form_Element_Submit('build');
          $build->setLabel('BUILD');
          $build->setDecorators(array(
                     'ViewHelper',
                     array('Description', array('tag' => '', 'class' => 'description')),
                     array('HtmlTag', array('tag' => 'p')),
                 ));
             
          $main = new Zend_Form_Element_Submit('main');
          $main->setLabel('MAIN');
          $main->setDecorators(array(
                     'ViewHelper',
                     array('Description', array('tag' => '', 'class' => 'description')),
                     array('HtmlTag', array('tag' => 'p')),
                 ));
             
          $back = new Zend_Form_Element_Submit('back');
          $back->setLabel('BACK');
          $back->setDecorators(array(
                     'ViewHelper',
                     array('Description', array('tag' => '', 'class' => 'description')),
                     array('HtmlTag', array('tag' => 'p')),
                 ));
             
          $add = new Zend_Form_Element_Submit('add');
          $add->setLabel('ADD');
          $add->setDecorators(array(
                     'ViewHelper',
                     array('Description', array('tag' => '', 'class' => 'description')),
                     array('HtmlTag', array('tag' => 'p')),
                 ));

          $name = new Zend_Form_Element_Hidden('name');
          $page = new Zend_Form_Element_Hidden('page');
          $old = new Zend_Form_Element_Hidden('old');
             
          $this->addElements(array(
              $build, $main, $back, $add, $name, $page, $old, $hidden));
              
          $this->setMethod('post');

       }
    }

?>
