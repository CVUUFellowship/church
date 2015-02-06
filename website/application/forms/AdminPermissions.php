<?php
    class Application_Form_AdminPermissions extends Zend_Form {
       public function __construct($options = null) {
          parent::__construct($options);
          
          $this->setName('adminpermissionsForm');
          
          $hidden = new Zend_Form_Element_Hidden('hiddenName');
          $hidden->setValue('adminpermissionsForm');
             
          $select = new Zend_Form_Element_Submit('select');
          $select->setLabel('SELECT');
             
          $permission = new Zend_Form_Element_Radio('permission');
          $permission->setSeparator('');                 
          
          $this->addElements(array(
              $hidden, $permission, $select));
              
          $this->setMethod('post');

       }
    }

?>
