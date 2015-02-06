<?php

class LibraryController extends Zend_Controller_Action
{


    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }


          function SearchAlpha($LT, $catmap) 
          {
            	$letter=$LT.'%';
              $where = array(
                      array('title', ' LIKE ', $letter),
                      );            
            	$titles = $catmap->fetchWhere($where, array("title"));
            	return($titles);
          }


          function Search($Category, $Searchterm, $fields, $catmap)
          {
//var_dump($Category);
//var_dump($Searchterm);
//var_dump($fields); exit;


            	if ($Searchterm == '*' || $Searchterm == '')
            		  return(NULL);
            	else
            		  $searchfor = '%' . strtolower($Searchterm) . '%';
              
            	if ($Category <> 'All') 
              {
                  $where = array(
                    array(strtolower($Category), ' LIKE ', $searchfor),
                    );            
                	$titles = $catmap->fetchWhere($where);
            	}
            	else 
              {
                  $orwhere = array();
                  foreach($fields as $field)
                      if ($field <> 'Number')
                          $orwhere[] = array(strtolower($field), ' LIKE ', $searchfor);
                  $titles = $catmap->fetchOrWhere($orwhere);     
            	}
          	   return($titles);
              
          }


          function AlphaLinks()
          {
              $font = "'Verdana, Helvetica, Arial,sans-serif'";
              $string=$_SERVER['REQUEST_URI'];
              $MyURL = substr($string, 0, strlen($string)-strlen (strstr ($string,"?")));
              
          // Generate the alpha link heading line from alphabet
              $local_alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            	unset($letters);
            	for ($i = 0; $i < strlen($local_alphabet); $i += 1)
            		$letters[$i] = substr($local_alphabet, $i, 1);
            	$list = "<center><hr size=.5><font size=2 face=$font>Browse library by title:<br>";
            	foreach ($letters as $let)
            		$list = $list."[<a href='".$MyURL."?LT=$let'>".$let."</a>] ";
            	$list = $list."</font></center><br><br>";
              
              return($list);
          }



    public function catalogAction()
    {
        
        $catmap = new Application_Model_LibCatalogMapper();
        
        $Headline[0] = "<img src='/media/graphics/libraryquote.gif' alt='quote'> ";
        $Headline[1] = "<br><img src='/media/graphics/librarybooks.gif' alt='books'> <img src='/media/graphics/librarybooks2.gif' alt='books2'>";
        $Headline[2] = "<br></center><span style='font-size: 100%; font-weight:normal;'>The majority of Library items are found in the CVUUF Board Room and
        may be checked out there.  New and featured items are on display at the Library Bookcase in Social Hall.";
        $Headline[3] = "</span>";
        $this->view->headlines = $Headline;
        
        $request = $this->getRequest();
        $getvalues = $request->getParams();
        if (isset($getvalues))
        {
            if (isset($getvalues['LT']))
                $LT = $getvalues['LT'];
            else
                $LT = '';
            
            if (isset($getvalues['Searchterm']))
                $Searchterm = $getvalues['Searchterm'];
            else
                $Searchterm = '';
        }

      	$this->view->AlphaList = $this->AlphaLinks();
  
        // Generate the search form line
        $fields = array(
         'Title',
         'Author',
         'CallNumber',
         'Subject1',
         'Subject2',
         'Subject3',
         'Subject4',
         'Publisher',
         'Date',
         'Price',
         'Number',
        );
        
        
        $this->view->fields = $fields;
//echo $SearchForm; exit;

        if ($LT <> '')
        {
            $this->view->titles = $this->SearchAlpha($LT, $catmap);
        }
        if ($Searchterm <> '')
            $this->view->titles = $this->Search('All', $Searchterm, $fields, $catmap);
   
        $this->view->style = 'zform.css';
    }



    public function audiocatalogAction()
    {
        
        $catmap = new Application_Model_LibAudioCatalogMapper();
        
        $Headline[0]="<br></center><span style='font-size: 100%; font-weight:normal;'>
        Sunday Sermons on CD may be ordered for a $3.00 donation at the Library bookcase in Social Hall.";
        $this->view->headlines = $Headline;
        
        $request = $this->getRequest();
        $getvalues = $request->getParams();
        if (isset($getvalues))
        {
            if (isset($getvalues['LT']))
                $LT = $getvalues['LT'];
            else
                $LT = '';
            
            if (isset($getvalues['Searchterm']))
                $Searchterm = $getvalues['Searchterm'];
            else
                $Searchterm = '';
            if (isset($getvalues['Category']))
                $Category = $getvalues['Category'];
            else
                $Category = '';
        }

      	$this->view->AlphaList = $this->AlphaLinks();
  
        // Generate the search form line
        $fields = array(
         'Date',
         'Presenter',
         'Title',
        );
        
        $this->view->fields = $fields;
//echo "SEARCHTERM $Searchterm   LT $LT <br>"; 

        if ($LT <> '')
        {
            $this->view->titles = $this->SearchAlpha($LT, $catmap);
        }
        elseif ($Searchterm <> '')
            $this->view->titles = $this->Search($Category, $Searchterm, $fields, $catmap);
        $this->view->style = 'zform.css';
    }

   
    public function wishlistAction()
    {
          $this->view->filename = 'librarywishlist.htm';
         
          return $this->render('showhtml');
        
    }   

   
    public function newbooksAction()
    {
        $functions = new Cvuuf_functions();
        $catmap = new Application_Model_LibCatalogMapper();
        $where = array(
            array('title', ' <> ', ''),
            array('createdate', ' > ', $functions->lastYear()),
            );            
        $catalog = $catmap->fetchWhere($where, array('create'));
        
        $this->view->catalog = $catalog;
        
//var_dump($catalog);
//exit;

    }   
   
   
}

