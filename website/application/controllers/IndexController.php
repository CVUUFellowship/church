<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {

        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $menufunctions = new Cvuuf_menufunctions();
        $menufunctions->buildMenumap();

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


    public function auctionAction()
    {
        $this->_redirect('http://togetherauction.com/cvuuf');
    }


    public function nullpageAction()
    {
        exit;
    }
    
    
    
    public function pageconstructionAction()
    {
        return $this->render('pageconstruction');
    }



    public function testAction()
    {
        $functions = new Cvuuf_functions();
        $file = $functions->findPrivateMaxFile('newsletters');
        $functions->showPrivatePDFFile('newsletters', $file);
exit;
        
    }
    
    
}

