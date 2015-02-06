<?php
    class Application_Form_menusbuild extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('menusbuildForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('menusbuildForm');
             
          $build = new Zend_Form_Element_Submit('build');
          $build->setLabel('BUILD');
          $build->setDecorators(array(
                     'ViewHelper',
                     array('Description', array('tag' => '', 'class' => 'description')),
                     array('HtmlTag', array('tag' => 'div')),
                 ));
             
          
          $this->addElements(array(
              $hidden, $build));
              
          $this->setMethod('post');

       }
    }

?>
