<?php

class EventsController extends Zend_Controller_Action
{



    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }


    public function newsandnotesAction()
    {
        $functions = new Cvuuf_functions();
        $file = $functions->findPrivateMaxFile('newsandnotes');
        $functions->showPrivatePDFFile('newsandnotes', $file);
    }

    
   
}

