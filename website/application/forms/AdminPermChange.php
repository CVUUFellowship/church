<?php
    class Application_Form_AdminPermChange extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('adminpermchangeForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('adminpermchangeForm');

          $permission = new Zend_Form_Element_Multiselect('permission');
          $permission->setLabel('Permissions');
          $permission->addMultiOptions(array())
              ->setSeparator('');                 

          $cbutton = new Zend_Form_Element_Submit('cancel');
          $cbutton->setLabel('CANCEL');
             
          $sbutton = new Zend_Form_Element_Submit('submit');
          $sbutton->setLabel('SUBMIT');

          $id = new Zend_Form_Element_hidden('id');
          $perm = new Zend_Form_Element_hidden('perm');
          $continue = new Zend_Form_Element_hidden('continue');
          
          $this->addElements(array($hidden,
              $permission, $id, $perm, $sbutton, $cbutton, $continue,
              ));
              
          $this->setMethod('post');

       }
    }

?>
