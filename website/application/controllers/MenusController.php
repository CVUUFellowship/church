<?php

class MenusController extends Zend_Controller_Action
{


        function setupmenu($page, $name, $old)
        {
            $menus = new Application_Model_Menus();
            $menusmap = new Application_Model_MenusMapper();
            $where = array(
                array('name', ' = ', $name),
                array('page', ' = ', $page),
                );            
            $menus = $menusmap->fetchWhere($where);
            $this->view->menus = $menus;
            $this->view->page = $page;
            $this->view->name = $name;
            $this->view->old = $old;
            $this->view->style = 'zformnodt.css';

            $selForm = new Application_Form_MenuSelect();
            $selForm->build->setDescription("Build menus from database");        
            $selForm->main->setDescription("Select Main menu");        
            $selForm->back->setDescription("Back to previous menu (only can do once)");        
            $selForm->add->setDescription("Add a new menu item to this menu ");        
            $selForm->setAction('select');

            if (!isset($pageForm))
                {
                $pageForm = new Application_Form_MenuPageSelect();
                $this->view->pageForm = $pageForm;
                
                $where = array(
                    array('page', ' <> ', ' '),
                    );            
                $pages = $menusmap->fetchColumn($where, 'page', true);
                $options = array();
                foreach ($pages as $menupage)
                {
                    $pagename = $menupage->page;
                    $options[$pagename] = $pagename;
                }
                $pageForm->selpage->setMultiOptions($options);
                $pageForm->selpage->setValue($page);
                }
            $pageForm->select->setDescription('Select an existing menu page');        
            $pageForm->page->setValue($pagename);
            $pageForm->name->setValue($name);
            $pageForm->old->setValue($old);
            $pageForm->setAction('select');
            $this->view->pageForm = $pageForm;

            $data['page'] = $page;
            $data['name'] = $name;
            $data['old'] = $old;
            $selForm->populate($data);
            $this->view->selForm = $selForm;
        }

        /* returns people object from init form */
        function fillMenu($param, Application_Model_Menus $menu)
        {
            $menu->page = isset($param['page']) ? $param['page'] : '';
            $menu->name = isset($param['name']) ? $param['name'] : '';
            $menu->position = isset($param['position']) ? $param['position'] : '';
            $menu->level = isset($param['level']) ? $param['level'] : 0;
            $menu->text = isset($param['text']) ? $param['text'] : '';
            $menu->type = isset($param['type']) ? $param['type'] : 'url';
            $menu->item = isset($param['item']) ? $param['item'] : '';
        }
        
        
        function checkAdmin()
        {
            $functions = new Cvuuf_authfunctions();
            $auth = $functions->getAuth('');
            if (!isset($auth))
                $this->_redirect('/auth/index');
            if ($functions->hasPermission('admin', $auth) == false)
            {
                $page = $_SERVER['REQUEST_URI'];
                $this->_redirect("/auth/notauth?from=$page");
            }
        }




    public function init()
    {
        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth('');
        $this->auth = $auth;
        $this->view->level = $auth->level;
        $this->view->theme = 'admin';        /* Initialize action controller here */
    }


    public function indexAction()
    {
        $this->checkAdmin();
    }


    public function showAction()
    {
        $this->checkAdmin();
        $page = 'Public';
        $name = 'Main';
        $old = 'Main';
        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['old']))
            {
                $old = $getvalues['old'];
                $name = $getvalues['name'];
                $page = $getvalues['page'];
            }
        }     
        elseif ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            $name = $getvalues['name'];
            $page = $getvalues['page'];
            $old = $getvalues['old'];
            
            if (isset($getvalues['newpagebutton']))
            {
                $menu = new Application_Model_Menus();
                $menusmap = new Application_Model_MenusMapper();
                $menu->name = "Main";
                $menu->page = $getvalues['newpage'];
                $menu->text = "*** NEW MENU ITEM ***";         
                $menusmap->save($menu);
            }

            
            
            if (isset($getvalues['drop']))
            {
                $ids = $getvalues['id'];
                foreach ($ids as $id)
                {
                    $menusmap = new Application_Model_MenusMapper();          
                    $result = $menusmap->delete($id);
                }
                $this->view->message = count($ids) . " lines deleted";
            }
            
            if (isset($getvalues['edit']))
            {
                $ids = $getvalues['id'];
                $id = $ids[0];
                $changeForm = new Application_Form_MenuChange();
                $this->view->changeForm = $changeForm;
                $menu = new Application_Model_Menus();
                $menumap = new Application_Model_MenusMapper();
                $menuline = $menumap->find($id);
                $menuline['old'] = $old;
                $changeForm->populate($menuline);
                $this->view->style = 'zform.css';
                $changeForm->setAction('change');
                $changeForm->submit->setDescription('Send in the changes');
                $functions = new Cvuuf_authfunctions();
                $codes = $functions->permissionNames();
                $changeForm->level->addMultiOptions(array('')); 
                $changeForm->level->addMultiOptions($codes); 

                $level = $menuline['level'];
                $codes = array();
                $pvalues = $functions->permissionCodes();
                foreach ($pvalues as $key => $value)
                {
                    $thebit = $level & $value;
                    if (($level & $value) <> 0)
                    {
                        $codes[$key] = $value;
                    }
                }
                $changeForm->level->setValue($codes); 
                $this->view->style = 'zform.css';
                return $this->render('change'); 
            }
        }     

        $this->setupmenu($page, $name, $old);
        $this->view->style = 'zform.css';
    }


    public function selectAction()
    {
        $this->checkAdmin();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            $name = $getvalues['name'];
            $page = $getvalues['page'];
            $old = $getvalues['old'];

            if (isset($getvalues['select']))
            {
                $page = $getvalues['selpage'];
            }

            if (isset($getvalues['build']))
            {
                $menufunctions = new Cvuuf_menufunctions();
                $menufunctions->buildmenus();
                $this->view->message = "Menu files built.";
            }
            elseif (isset($getvalues['main']))
            {
                $name = "Main";
            }
            elseif (isset($getvalues['back']))
            {
                $name = $old;
            }
            elseif (isset($getvalues['add']))
            {
                $menu = new Application_Model_Menus();
                $menusmap = new Application_Model_MenusMapper();
                $menu->name = $name;
                $menu->page = $page;
                $menu->text = "*** NEW MENU ITEM ***";         
                $menusmap->save($menu);
            }
        }
        else
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['page']))
                $page = $getvalues['page'];
        }


        if (!isset($name))
        {
            $name = "Main";
            if (!isset($page))
                $page = 'Public';
            $old = 'Main';
        }
        $this->setupmenu($page, $name, $old);
        return $this->render('show');
    }



    public function changeAction()
    {
        $this->checkAdmin();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->_request->getPost();
            $getvalues = $request->getParams();
            $page = $getvalues['page'];
            $name = $getvalues['name'];
            $old = $getvalues['old'];
            if (isset($getvalues['cancel'])) 
            {
                $this->_redirect("/menus/show?name=$name&page=$page&old=$old");
            }
            if (isset($getvalues['submit'])) 
            {
/* get all the form values */
/* save the menus record */
                $menus = new Application_Model_Menus();
                $menusmap = new Application_Model_MenusMapper();
                $formData = $request->getParams();
                $this->fillMenu($formData, $menus);

                $levels = $getvalues['level'];
                $code = 0;
                foreach ($levels as $bit)
                    $code |= $bit;
                $menus->level = $code;

                $id = $getvalues['id'];
                $menus->id = $id;
                $menusmap->save($menus);
                $this->_redirect("/menus/show?name=$name&page=$page&old=$old");
            }
                
        }
    }
    


    public function testAction()
    {
        $menufunctions = new Cvuuf_menufunctions();
        $menufunctions->buildMenumap();
        
    }

}

