<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }


    
    public function contactAction()
    {
        $this->view->style = 'zformnodt.css';
        return $this->render('contact');
    }
    
    public function formthanksAction()
    {
        return $this->render('formthanks');
    }



    public function testAction()
    {
        $functions = new Cvuuf_functions();
        $file = $functions->findPrivateMaxFile('newsletters');
        $functions->showPrivatePDFFile('newsletters', $file);
exit;
        
    }
    
    
}

