<?php 

class PublicController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }


        
        
        public function Ymd()
        {
            $unixtime=mktime(date('G'),date('i'),date('s'), date('m'), date('d'),date('Y'));
            return (date("Ymd", $unixtime));
        }
        

        public function showTrans($str)
        {
            $tr = array(
                    "\\'" => "&#8217;",
                    "\n\r" => " ",
                    "\n" => " ",
                    "^" => "<br>",
            );
            return (strtr($str, $tr));
        }

      
      public function fillPerson($person, $line)
      {
          $line[] = $person['firstname'] . ' ' . $person['lastname'];
          $line[] = $person['photolink'];
          return($line);
      }





    public function newsandnotesAction()
    {
        $functions = new Cvuuf_functions();
        $file = $functions->findPublicMaxFile('newsandnotes');
        $functions->showPublicPDFFile('newsandnotes', $file);
    }


    public function announcementsAction()
    {

        $request = $this->getRequest();    
        if ($this->getRequest()->isGet())
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['theme']))
                $this->view->theme = $getvalues['theme'];
        }

        $Ymd = $this->Ymd();
        $annmap = new Application_Model_AnnouncementsMapper();
        $where = array(
            array('status', ' = ', 'approved'),
            array('xdate', ' >= ', $Ymd),
              );
        $anns = $annmap->fetchWhere($where);

        $data = array();
        $row = array();
        foreach ($anns as $anc)
        {
            unset($row);
            $encDate = $anc->date;
            $ut = mktime(0,0,0,substr($encDate, 4, 2), substr($encDate, 6, 2), substr($encDate, 0, 4));
            $row['date'] = date("D F j, Y", $ut);
            $Con = $anc->contact;
            $row['contact'] = $Con <> '' ? " <b>Contact</b> " . $Con . ". " : '';
            $row['location'] = ($anc->place <> '') ? " <b>Location:</b> " . $anc->place . ". " : '';
            
            $time = $anc->time;    
            if ($time <> '0:00PM')
                $row['time'] = " " . $time;
            else
                $row['time'] = '';
                
            if ($anc->link <> '')
            {
                $linkText = ($anc->linktext <> '') ? $anc->linktext : "Web address";
                $row['link'] = " <b>Link:</b> <a href='$anc->link'> $linkText </a>";
            }
            else
                $row['link'] = '';
                
            $row['desc'] = $this->showTrans($anc->description);              
            $row['id'] = $anc->id;
            $row['title'] = $anc->title;
            
            $data[] = $row;
        }

        $this->view->data = $data;
    }


    public function newsletterAction()
    {
        
        $functions = new Cvuuf_functions();
        $file = $functions->findPublicMaxFile('newsletters');
        $edition = substr($file, 6, 2) . '-' . substr($file, 2, 4);
        $ip = $_SERVER['REMOTE_ADDR'];
        $logmap = new Application_Model_NewsletterLogMapper();
        $logentry = new Application_Model_NewsletterLog();
        $logentry->ip = $ip;
        $logentry->edition = $edition;
        $logmap->save($logentry);
        $functions->showPublicPDFFile('newsletters', $file);
    }


    public function welcomeAction()
    {
    
    }

    public function outreachAction()
    {
    
    }




    public function showgroupsAction()
    {
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
        }
        $group = new Application_Model_Groups();
        $groupsmap = new Application_Model_GroupsMapper();
        $headingsmap = new Application_Model_HeadingsMapper();
        $where = array(
            array('type', ' = ', 'group'),
            );            
        $headings = $headingsmap->fetchWhere($where);
        $data = array();
        $heads = array();
//var_dump($headings);
        foreach ($headings as $heading)
        {
            $where = array(
                array('headingid', ' = ', $heading->id),
                array('publicpage', ' = ', 'yes'),
                );            
            $groups = $groupsmap->fetchWhere($where);
            if (count($groups) > 0)
            {
  //var_dump($groups); 
              $lines = array();
              $line = array();
              foreach ($groups as $group)
              {              
                  unset($line);
                  $line[] = $group->title;
                  $peoplemap = new Application_Model_PeopleMapper();
                  $person = $peoplemap->find($group->contact1);
                  $line = $this->fillPerson($person, $line);
                  $lines[] = $line;
                  unset($line);
                  $person = $peoplemap->find($group->contact2);
                  if ($person['id'] > 0)
                  {
                      $line[] = '+';
                      $line = $this->fillPerson($person, $line);
                      $lines[] = $line;
                  }
                  
                  unset($line);
                  $person = $peoplemap->find($group->contact3);
                  if ($person['id'] > 0)
                  {
                      $line[] = '+';
                      $line = $this->fillPerson($person, $line);
                      $lines[] = $line;
                  }
                  
                  unset($line);
                  $person = $peoplemap->find($group->contact4);
                  if ($person['id'] > 0)
                  {
                      $line[] = '+';
                      $line = $this->fillPerson($person, $line);
                      $lines[] = $line;
                  }
              }
              $heads[] = $heading->heading;
              $data[] = array($heading->heading, $lines);
              }          
        }
        $this->view->data = $data;
//var_dump($data);
//exit;
        $this->view->heads = $heads;
        $this->view->style = 'zform.css';
        
        if (isset($getvalues['pdfbutton']))
            return $this->render('pdfgroups');
            
    }

    

    public function webmailAction()
    {
          $now = date("Y-m-d H:i:s", mktime(date('H'), date('i'), 0, date('m'), date('d'),date('Y')));
      
      		$TEXT="Webmail alive and well at ".$now;
          $SUBJECT="Webmail is alive!";
          $TO_array=array('alive@cvuuf.org');           
          $efunctions = new Cvuuf_emailfunctions();
          $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, array('webmail@cvuuf.org' => "CVUUF EMAIL"));
          $log = $efunctions->log_email($this, 1, $totalsent, 0, 0);

    exit();
    }
    



}
?>