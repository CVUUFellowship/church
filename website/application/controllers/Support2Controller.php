<?php

class Support2Controller extends Zend_Controller_Action
{

        
        public function fullList($peoplemap, $plus = '')
        {

            $types = "'NewFriend', 'Member', 'Affiliate', 'Staff', 'Friend', 'Visitor'";
            $where = array(
                array('inactive', ' <> ', 'yes'),
//array('lastname', ' = ', 'talvola'),
                array('email', ' <> ', ''),
                array('status', ' IN ', "($types)"),
                  );
            $activepeeps = $peoplemap->fetchWhere($where);
            if ($plus <> 'plus')
                return ($activepeeps);

            $Tdate = getdate(mktime(0,0,0, date('m'), date('d'), date('Y') - 2));              
            $twoyears = mktime(0,0,0, $Tdate['mon'], $Tdate['mday'] - ($Tdate['wday']), $Tdate['year']);
            $TwoYrs = date("Y-m-d", $twoyears);
            $types = "'NewFriend', 'Member', 'Affiliate', 'Staff', 'Friend', 'Visitor', 'Guest', 'Resigned'";
            $where = array(
                array('inactive', ' = ', 'yes'),
                array('email', ' <> ', ''),
                array('creationdate', ' > ', $TwoYrs),
                array('status', ' IN ', "($types)"),
                );
            $inactivepeeps = $peoplemap->fetchWhere($where);
            $peeps = array_merge($activepeeps, $inactivepeeps);
            return $peeps;
        }
    

      public function lastSunday()
      {
          $Tdate = getdate(mktime(0,0,0, date('m'), date('d'),date('Y')));
          $today = $Tdate['wday'];
          $Month = $Tdate['mon'];
          $Year = $Tdate['year'];

          $TDay = $Tdate['mday'];
          $MDay = $TDay - $today;
          if ($MDay <= 0) 
          {
              $lastMonth = $Month - 1;
            	$lastday = mktime(0, 0, 0, $Month, 0, $Year);
            	$lastday = strftime("%d", $lastday);
            	$MDay = $lastday + $MDay;
            	$Month = $lastMonth;
          }
          $lastsunday = mktime(0, 0, 0, $Month, $MDay, $Year);
          $lastdate = date("Y-m-d", $lastsunday);
          return($lastdate);      
      }
      
      
        public function getPeople($list)
        {
            $positionsmap = new Application_Model_PositionsMapper();
            $peoplemap = new Application_Model_PeopleMapper();
            $people = array();
            foreach ($list as $position)
            {
                $where = array(
                    array('title', ' = ', $position),
                        );            
                $members = $positionsmap->fetchWhere($where);
                foreach ($members as $member)
                    $people[] = $peoplemap->find($member->contact1);
            }
            return $people;
        }



    public function init()
    {
        $this->view->theme = 'data';        /* Initialize action controller here */
        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth('');
        $this->auth = $auth;
        if (!isset($auth))
            $this->_redirect('/auth/index');
        $this->view->level = $auth->level;
    }



    public function indexAction()
    {
        // action body
    }





    public function nnuploadAction()
    {
        $functions = new Cvuuf_functions();
        $request = $this->getRequest();
        $publicdir = $_SERVER["DOCUMENT_ROOT"];
        $filedir = $publicdir . '/media/newsandnotes/';

        if ($this->getRequest()->isPost())
        {
            $postvalues = $request->getParams();
            if (isset($postvalues))
            {
                if (isset($postvalues['todo']))
                {
                    $action = $postvalues['todo'];

                  	if ($action=='upload') 
                    {
                    		$this->view->message = array();
                    		$theName = $postvalues['theName'];
                    		$files = $_FILES['THEPDF'];
                    	 	$path_to_file = $filedir;
                    		if ($files['size']) {
                      			$location = $path_to_file . $theName;
                            $tmpfile = $files['tmp_name'];
                      			copy($tmpfile, $location);
                      			unlink($tmpfile);
                      			$this->view->message[] = "Successfully uploaded News and Notes file '".$theName."'";
                    		}
                    		else
                    		    $this->view->message[] = "There is a problem with file '".$theName."'";

                        $this->view->style = 'zform.css';
                        return $this->render('nnresults');

                    }
                    
                }



                if ($postvalues['newName'] <> '')
                    $filename = $postvalues['newName'];
                else
                    $filename = $postvalues['filename'];
                
                $this->view->filename = $filename;
                $this->view->style = 'zform.css';
                return $this->render('nnchoose');

            }
        }

        $filename = 'nn' . $functions->nextSunday() . '.pdf';
        $this->view->filename = $filename;
        $this->view->style = 'zform.css';
    }




    public function nluploadAction()
    {
        $functions = new Cvuuf_functions();
        $file = $functions->findPublicMaxFile('newsletters');
      	$month = substr($file, 6, 2);
      	$year = substr($file, 2, 4);
      	if ($month == '12') 
        {
      		  $month = '01';
      		  $year++;
      	}
      	else
      		  $month++;
      	if ($month < 10) 
            $month = '0' . $month;	
      	$filename = 'nl' . $year . $month . '.pdf';

        $publicdir = $_SERVER["DOCUMENT_ROOT"];
        $filedir = $publicdir . '/media/newsletters/';

        $request = $this->getRequest();
        if ($this->getRequest()->isPost())
        {
            $postvalues = $request->getParams();
            if (isset($postvalues))
            {
                if (isset($postvalues['todo']))
                {
                    $action = $postvalues['todo'];

                  	if ($action=='upload') 
                    {
                    		$this->view->message = array();
                    		$theName = $postvalues['theName'];
                    		$files = $_FILES['THEPDF'];
                    	 	$path_to_file = $filedir;
                    		if ($files['size']) {
                      			$location = $path_to_file . $theName;
                            $tmpfile = $files['tmp_name'];
                      			copy($tmpfile, $location);
                      			unlink($tmpfile);
                      			$this->view->message[] = "Successfully uploaded Newsletter file '".$theName."'";
                    		}
                    		else
                    		    $this->view->message[] = "There is a problem with file '".$theName."' (".print_r($files, TRUE).")";

                        $this->view->style = 'zform.css';
                        return $this->render('nlresults');

                    }
                    
                }
                if ($postvalues['newName'] <> '')
                    $filename = $postvalues['newName'];
                else
                    $filename = $postvalues['filename'];
                
                $this->view->filename = $filename;
                $this->view->style = 'zform.css';
                return $this->render('nnchoose');
            }
        }
        $this->view->filename = $filename;
        $this->view->style = 'zform.css';
    }

    


    public function nlannounceAction()
    {
$SEND='YES';
        $functions = new Cvuuf_functions();
        $efunctions = new Cvuuf_emailfunctions();
        $peoplemap = new Application_Model_PeopleMapper();
        $peeps = $this->fullList($peoplemap, 'plus');
//var_dump($peeps); exit;
        $emailCount = count($peeps);            

        $unsmap = new Application_Model_UnsubMapper();
        $etext = '';

        $request = $this->getRequest();    
        if ($this->getRequest()->isPost())    
        {
            $postvalues = $request->getParams();
            if (isset($postvalues))
            {
                $todo = $postvalues['todo'];
                if ($todo == 'SEND')
                {

                    $etext = "The monthly CVUUF newsletter is now available on our website for downloading.<br><br>";
                    $etext=$etext . "There is a link on our home page, or you may go directly to <br>";
                    $etext=$etext . "<a href='/public/newsletter'>/public/newsletter</a>";
                    $etext=$etext . "<br><br><small>To unsubscribe from newsletter announcements DO NOT REPLY, please click ";
                    $etext=$etext . "<a href='mailto:unsubscribe@cvuuf.org?subject=newsletter'>here.</a><br>";
                    $etext=$etext . "Or, to unsubscribe from ALL CVUUF announcements, please click ";
                    $etext=$etext . "<a href='mailto:unsubscribe@cvuuf.org?subject=all'>here.</a></small><br>";

                    $result = $efunctions->sendBatchEmail($peeps, 'newsletter', 'CVUUF Newsletter', $etext, $this, array('webmail@cvuuf.org' => "CVUUF"));

                    $log = $result['log'];
                    $this->view->sent = $log->sentcount;
                    $this->view->invalid = $result['invalid'];
                    $this->view->unsub = $result['unsub'];
                    $this->view->results = $result['results'];
                    $this->view->theme = 'data';

                    return $this->render('nlancsent');         
                }
            }
        }
        $this->view->count = $emailCount;
        $this->view->theme = 'data';
        $this->view->style = 'zform.css';
    }



    


    public function nlsendAction()
    {
        $functions = new Cvuuf_functions();
        $efunctions = new Cvuuf_emailfunctions();
        $peoplemap = new Application_Model_PeopleMapper();
        $emailsmap = new Application_Model_NewsletterEmailsMapper();
        $peepids = $emailsmap->fetchAll();
        $peeps = array();
        foreach ($peepids as $peep)
        {
            $where = array(
                array('id', ' = ', $peep->id),
                  );
            $peeps[] = current($peoplemap->fetchWhere($where));
        }
        $count = count($peeps);
        $filename = $functions->findPublicMaxFile('newsletters');
        $publicdir = $_SERVER["DOCUMENT_ROOT"];
        $filedir = $publicdir . '/media/newsletters';
    		$fullPath = $filedir . '/' . $filename;

        $request = $this->getRequest();    
        if ($this->getRequest()->isPost())    
        {
            $postvalues = $request->getParams();
            if (isset($postvalues))
            {
                $todo = $postvalues['todo'];
                if ($todo == 'SEND')
                {

                    $etext =  "Per your request, here is the CVUUF newsletter.";;
                    $etext=$etext . "<br><br><small>To unsubscribe from newsletter announcements DO NOT REPLY, please click ";
                    $etext=$etext . "<a href='mailto:unsubscribe@cvuuf.org?subject=newsletter'>here.</a><br>";
                    $etext=$etext . "Or, to unsubscribe from ALL CVUUF announcements, please click ";
                    $etext=$etext . "<a href='mailto:unsubscribe@cvuuf.org?subject=all'>here.</a></small><br>";

                    $result = $efunctions->sendBatchEmail($peeps, 'newsletter', 'CVUUF Newsletter', $etext, $this, array('webmail@cvuuf.org' => "CVUUF"), null, $fullPath);

                    $log = $result['log'];
                    $this->view->sent = $log->sentcount;
                    $this->view->invalid = $result['invalid'];
                    $this->view->unsub = $result['unsub'];
                    $this->view->results = $result['results'];
                    $this->view->theme = 'data';

                    return $this->render('nlemailsent');         
                }
            }
        }
        
        $this->view->count = $count;
        $this->view->file = $filename;
        $this->view->theme = 'data';
        $this->view->style = 'zform.css';
    }






    public function minutesmanageAction()
    {
        $functions = new Cvuuf_functions();
        $nodesmap = new Application_Model_NodesMapper();
        
        $Months = array('January','February','March','April','May','June','July','August','September','October','November','December');
        $functions = new Cvuuf_functions();



//var_dump($file); exit;
        $request = $this->getRequest();    
        if ($this->getRequest()->isPost())    
        {
            $postvalues = $request->getParams();
            if (isset($postvalues))
            {
                if (isset($postvalues['orgcode']))
                    $orgCode = $postvalues['orgcode'];
                if (isset($postvalues['typebutton']))
                    $orgCode = $postvalues['orgtype'];
                
                if (isset($postvalues['createbutton']))
                {
                    $minutes = new Application_Model_Nodes();
                }
                
                if (isset($postvalues['submitbutton']))
                {
                    $nodeid = $postvalues['nodeid'];
                    if ($nodeid > 0)
                    {
                        $where = array(
                            array('nodeid', ' = ', $nodeid),
                              );
                        $minutes = current($nodesmap->fetchWhere($where));
                    }
                    else
                        $minutes = new Application_Model_Nodes();
                    $theTitle = $postvalues['title'];
                    $minutes->title = $theTitle;
                    $minutes->body = $postvalues['TEXT'];
                    $newnodeid = $nodesmap->max() + 1;                    
                    $minutes->nodeid = $newnodeid;
                    $space = strpos($minutes->title, ' ');
                    $orgname = substr($minutes->title, 0, $space);
                    $minutes->organization = $orgname;
                    $minutes->content = "Minutes";
                    $monNum = 0;
                    foreach ($Months AS $month){
                        $monNum++;
                        $mnPos = strpos($theTitle, $month);
                        if ($mnPos !== false){
                            $theMonth = $monNum;
                            if ($monNum < 10)
                                $theMonth = '0' . $theMonth;
                            break;
                        }
                    }
                    $yearstart = strpos(substr($theTitle, $mnPos), ' ') + 1;
                    $theYear = substr($theTitle, $mnPos + $yearstart, 4);
                    $minutes->date = $theYear . '-' . $theMonth;
//var_dump($minutes); exit;
                    $nodesmap->save($minutes);
                    $this->view->message = "Minutes id $minutes->nodeid filed.";
                    unset($minutes);
                }
                
            }
        }

        if (!isset($orgCode))
            $orgCode = 'B';
        $begin = 'att_' . $orgCode;
        $file = $functions->findPublicMaxFile('atts', $begin);
        $today = $functions->today();
        $curMonth = substr($today, 4, 2);
        $curYear = substr($today, 0, 4);
      	$month = substr($file, 11, 2);
      	$year = substr($file, 6, 4);
      	if ($month < 10)
            $month = '0' . $month;     	
      	if ($file <> '')
        {
            if ($month == $curMonth)
            {
                $num = substr($file, 14, 1)+1;	
                $theName = 'att_' . $orgCode . '_' . $year . '-' . $month . '_' . $num . '.pdf';
            }
            else
                $theName = 'att_' . $orgCode . '_' . $year . '-' . $month . '_1.pdf';
        }
        else
        {
            $month = substr($today, 4, 2);
            $year = substr($today, 0, 4);
            $theName = 'att_' . $orgCode . '_' . $year . '-' . $month . "_1.pdf";
        }
        
        $this->view->name = $theName;

        if ($this->getRequest()->isGet())    
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['select']))
            {    
                $nodeid = $getvalues['select'];
                $where = array(
                    array('nodeid', ' = ', $nodeid),
                      );
                $minutes = current($nodesmap->fetchWhere($where));
            }
        }
        
        switch ($orgCode)
        {
          case 'B': $org = 'Board'; break;
          case 'C': $org = 'Council'; break;
          case 'F': $org = 'Fellowship'; break;
        }
        
        if (isset($minutes))
        {
            if ($minutes->title == '')
                $minutes->title = $org . " Meeting Minutes - " . date("F") . " $curYear";

            $this->view->orgcode = $orgCode;
            $this->view->minutes = $minutes;
            $this->view->theme = 'data';
            $this->view->style = 'zform.css';
            return $this->render('minutesedit');
            
        }
        
        
        $this->view->select = array();
        $this->view->select[$orgCode] = 'selected';

        $begin = 'att_' . $orgCode;
        $file = $functions->findPublicMaxFile('atts', $begin);

        $today = $functions->today();
        $curMonth = substr($today, 4, 2);
      	$month = substr($file, 11, 2);
      	$year = substr($file, 6, 4);
      	if ($month < 10)
            $month = '0' . $month;     	
      	if ($file <> '')
        {
            if ($month == $curMonth)
            {
                $num = substr($file, 14, 1)+1;	
                $theName = 'att_' . $orgCode . '_' . $year . '-' . $month . '_' . $num . '.pdf';
            }
            else
                $theName = 'att_' . $orgCode . '_' . $year . '-' . $month . '_1.pdf';
        }
        else
        {
            $month = substr($today, 5, 2);
            $year = substr($today, 0, 4);
            $theName = 'att_' . $orgCode . '_' . $year . '-' . $month . "_1.pdf";
        }
        
        $this->view->name = $theName;
        $where = array(
            array('content', ' = ', 'Minutes'),
            array('organization', ' = ', $org),
              );
        $minutes = $nodesmap->fetchWhere($where);
        $this->view->minutes = $minutes;
        
        $this->view->orgcode = $orgCode;
        $this->view->theme = 'data';
        $this->view->style = 'zform.css';      
        
    }



    public function emailforwardsAction()
    {
        $authfunctions = new Cvuuf_authfunctions();
        $forwardsmap = new Application_Model_EmailForwardMapper();

        $request = $this->getRequest();    
        if ($this->getRequest()->isPost())    
        {
            $postvalues = $request->getParams();
//var_dump($postvalues);


            if (isset($postvalues['showbutton']))
            {
                $lines = array();
                $forwarders = $forwardsmap->fetchAll();
                $last = '';
                foreach ($forwarders as $forwarder)
                {
                    $thisForwarder = $forwarder->forwarder;
                    if ($thisForwarder <> $last)
                    {
                        $last = $thisForwarder;
                        $lines[] = "<b>$thisForwarder</b>";
                    }
                    $forwardto = $forwarder->forwardto;
                    if ($forwardto[0] == ' ')
                        $start = 1;
                    else
                        $start = 0;
                    $lines[] = "&nbsp;&nbsp;&nbsp;" . substr($forwarder->forwardto, $start);
                }
                $this->view->lines = $lines;
                $this->view->style = 'zform.css';
                return $this->render('emailforwardsshow');
            }


            if (isset($postvalues['filebutton']))
            {
                $this->view->style = 'zform.css';
                return $this->render('emailforwardschoose');
            }
            
            if (isset($postvalues['sendbutton']))
            {
            		$this->view->message = array();
            		$files = $_FILES['THEFILE'];
                $publicdir = $_SERVER["DOCUMENT_ROOT"];
                $filedir = $publicdir . '/reports/';
            	 	$path_to_file = $filedir;
            		if ($files['size']) {
              			$location = $path_to_file . 'fwd.txt';
                    $tmpfile = $files['tmp_name'];
              			copy($tmpfile, $location);
              			unlink($tmpfile);
              			$this->view->message[] = "Successfully uploaded forwarders file.";
            		}
            		else
            		    $this->view->message[] = "There is a problem with upload file.";

                $this->view->message[] = "Loading forwards into database table.";
                $forwardsmap->truncate();
                $forwardName = '';
                $forwardCount = 0;
              
                $lineno = 0;
                $fd = fopen ($location, "r"); 
          		  $buffer = fgets($fd, 4096);
          		  $forwardLine = substr($buffer, 0, strlen($buffer)-1);
// move down to tableform
                while (strpos($forwardLine, '<form') === FALSE){
              		  $buffer = fgets($fd, 4096);
//var_dump($buffer);
              		  $forwardLine = substr($buffer, 0, strlen($buffer)-1);
              		  $lineno++;
          
//        echo "LTLine: ", $lineno, " Starts: ", substr($forwardLine, 8), " line length: ",strlen($buffer), " <br>";
//if ($lineno > 9) exit;
                };
//echo "FOUND &lt;form <br>";   exit;
                while (strpos($forwardLine, 'tableform') === FALSE){
              		  $buffer = fgets($fd, 4096);
              		  $forwardLine = substr($buffer, 0, strlen($buffer)-1);
              		  $lineno++;
          
          //        echo "TFLine: ", $lineno, " Starts: ", substr($forwardLine, 8), " line length: ",strlen($buffer), " <br>";
                };
//echo "FOUND tableform at line $lineno <br>"; 
//exit;

// move to start of forwards
                while (strpos($forwardLine, '</tr') === FALSE){
              		  $buffer = fgets($fd, 4096);
              		  $forwardLine = substr($buffer, 0, strlen($buffer)-1);
              		  $lineno++;
//        echo "Line: ", $lineno, " Starts: ", substr($forwardLine,2), " line length: ",strlen($buffer), " <br>";
                };
//echo "FOUND /tr  <br>";
           		  $buffer = fgets($fd, 4096);
           		  $forwardLine = substr($buffer, 0, strlen($buffer)-1);
                while (strpos($forwardLine, '</tr') === FALSE){
              		  $buffer = fgets($fd, 4096);
              		  $forwardLine = substr($buffer, 0, strlen($buffer)-1);
              		  $lineno++;
//        echo "Line: ", $lineno, " Starts: ", substr($forwardLine,2), " line length: ",strlen($buffer), " <br>";
                };
//echo "FOUND /tr at line $lineno <br>"; 

// process line by line
                while (!feof ($fd)) 
                { 
                  	$buffer = fgets($fd, 4096);
                  	$forwardLine = substr($buffer, 0, strlen($buffer)-1);
                    if (strpos($forwardLine, 'listtitle') !== FALSE)
                        break;
                //echo 'line size is ', strlen($forwardLine), '<br>';
                    $firstGT=strpos($forwardLine, '>');
                    $forwardLine=substr($forwardLine, $firstGT+1);
                  //echo "firstGT is ".htmlentities($forwardLine)." <br>";
                    $strtFwd=strpos($forwardLine, '>')+1;
                  //echo "strtFwd is $strtFwd <br>";
                    $forwardLine=substr($forwardLine, $strtFwd);
                  //echo "AFTER start IS $forwardLine <br>";
                    $endFwd=strpos($forwardLine, '</td');
                  //echo "endFwd is $endFwd <br>";      
                    $forwardName=substr($forwardLine, 0, $endFwd);
                //echo "New forward Name: $forwardName <br>";
                
                
                // find the forwardTo addresses
                    $pointTo=strpos($forwardLine, '->');
                    $forwardLine=substr($forwardLine, $pointTo+10);
                    $startTo=strpos($forwardLine, '>')+1;
                    $toPart=substr($forwardLine, $startTo);
                  //echo "toPart is ".htmlentities($toPart)." <br>";
                    $endTo=strpos($toPart, '<');
                  //echo "End to is $endTo <br>";
                    $forwardTo=substr($toPart, 0, $endTo);
//echo "FORWARD TO '$forwardTo' <br>";    
                    if (strpos ($forwardTo, ',') === FALSE)
                    {
                        $forward = new Application_Model_EmailForward();
                        $forward->forwarder = $forwardName;
                        $forward->forwardto = $forwardTo;
                        $forwardsmap->save($forward);
                        $forwardCount++;
//echo "SAVE DONE <br>"; 
                    }
                    else 
                    {
                        $forwardTo=substr($toPart, 0, $endTo);
                  //echo "&nbsp;&nbsp;&nbsp;Forward To $forwardTo <br>";
                        while (strpos ($forwardTo, ',') > 0)
                        {
                            $forwardActual=substr($toPart, 0, $endTo-1);
                    //echo "FIRSTCHAR is '".$forwardActual[0]."' which is ".ord($forwardActual[0])." <br>";
                            if (ord($forwardActual[0])==32) 
                            {
                              $forwardActual=substr($forwardActual, 1);
                  //echo "MODIFIED FORWARD TO '$forwardActual' <br>";  
                            }
                            $forward = new Application_Model_EmailForward();
                            $forward->forwarder = $forwardName;
                            $forward->forwardto = $forwardActual;
                            $forwardsmap->save($forward);
          
                            $forwardCount++;
                            $toPart=substr($toPart, $endTo+4);
                    //echo "Remaining To Part is $toPart <br>";      
                            $endTo=strpos($toPart, '<');
                            $forwardTo=substr($toPart, 0, $endTo);
                        }
        
                        $forward = new Application_Model_EmailForward();
                        $forward->forwarder = $forwardName;
                        $forward->forwardto = $forwardTo;
                        $forwardsmap->save($forward);
                        $forwardCount++;
                //exit;      
                    }
//exit;
              	} 
              	fclose ($fd); 
              	$this->view->message[] = "Wrote $forwardCount forwards.";

                $this->view->style = 'zform.css';
                return $this->render('emailforwards');
            }
        }
        
        $admin = $authfunctions->hasPermission('admin', $this->auth);
        $this->view->admin = $admin;
        $this->view->style = 'zform.css';      
    }




    public function sermonaudiouploadAction()
    {
            function goodname($name)
            {
                if (substr($name, 0, 6) <> 'sermon')
                    return false;
                if (strpos($name, '.mp3') == false)
                    return false;
                if (!is_numeric(substr($name, 6, 4)))
                    return false;
                
                return true;
            }

        $this->view->theme = 'private';
        $functions = new Cvuuf_functions();
        $onlinefiles = $functions->findPublicFiles('sermons');
        $filecount = count($onlinefiles);
        $this->view->message = array();

        $request = $this->getRequest();    
        if ($this->getRequest()->isPost())
        {
            $postvalues = $request->getParams();
            if (isset($postvalues))
            {
//var_dump($postvalues);
                $publicdir = $_SERVER["DOCUMENT_ROOT"];
                $filedir = $publicdir . '/media/sermons/';
            	 	$path_to_file = $filedir;
                $this->view->message = array();
                if (isset($postvalues['id']))
                {
                    $ids = $postvalues['id'];
                    if (count($ids) == 1)
                    {
                        $oldfile = $ids[0];
                        $oldloc = $path_to_file . $oldfile;
                    }
                    
                }

                if (isset($postvalues['deletebutton']))
                {
                    if (isset($oldloc))
                    {
                        $delresult = unlink($oldloc);
                        if ($delresult)
                        {
                            $this->view->message[] = "Sermon file '$oldfile' deleted.";
                        }
                        else
                            $this->view->message[] = "Sermon file '$oldfile' could not be deleted.";
                    }
                    else
                        $this->view->message[] = "No sermon file selected to delete.";
                }
                		

                elseif (isset($postvalues['sendbutton']))
                {
                    if ($filecount > 12)
                    {
                        $needed = $filecount - 12;
                        $this->view->message[] = "There are more than 10 sermon files online.  Delete $needed sermon files before uploading.";
                    }
                    else
                    {
                    		$files = $_FILES['mp3file'];
                    		$theName = $files['name'];
                        if (goodname($theName))
                        {
                            $size = $files['size'];
                            $error = $files['error'];
                            $erm = " code $error";
                            if ($error == 2)
                                $erm = 'file too large';
                            if ($error <> 0)
                                $this->view->message[] = "There is a problem with '".$theName."' - $erm.";
                        		if ($error == 0) 
                            {
                           			$location = $path_to_file . $theName;
                                $tmpfile = $files['tmp_name'];
                          			copy($tmpfile, $location);
                          			unlink($tmpfile);
                                $this->view->message[] = "Successfully uploaded sermon file '".$theName."'";
                                
                                if (isset($postvalues['id']))
                                {
                                    $ids = $postvalues['id'];
                                    if (count($ids) == 1)
                                    {
                                        $oldfile = $ids[0];
                                        $oldloc = $path_to_file . $oldfile;
                                        $delresult = unlink($oldloc);
                                        if ($delresult)
                                            $this->view->message[] = "Sermon file '$oldfile' deleted.";
                                        else
                                            $this->view->message[] = "Sermon file '$oldfile' could not be deleted.";
                                    }
                                    
                                }
                        		}
                        }
                        else
                        {
                                $this->view->message[] = "File name '".$theName."' is improperly formed.";                   
                        }
                    }
                }
                
                elseif (isset($postvalues['mlbutton']))
                {
                    $ftpdir = substr($publicdir, 0, strlen($publicdir) - 12) . '/public_ftp/';
                    $newxmlfile = $ftpdir . 'sermons.xml';
                    $xmlfile = $path_to_file . 'sermons.xml';
                    copy($newxmlfile, $xmlfile);
                    $newhtmlfile = $ftpdir . 'sermons.html';
                    $htmlfile = $path_to_file . 'sermons.html';
                    copy($newhtmlfile, $htmlfile);
                    $this->view->message[] = "Sermon .xml and .html files successfully copied.";
                }
                
                else
                {
                    $this->view->message[] = "File cannot be uploaded, may be too large.";
                }                  
                
            }
        }
        

        $onlinefiles = $functions->findPublicFiles('sermons');
        sort($onlinefiles);
        $filecount = count($onlinefiles);
        if ($filecount <> 13)
        {
            $filecount -= 3;
            $this->view->message[] = "There are $filecount instead of 10 sermon files online.";
        }

        if (count($this->view->message) == 0)
            unset($this->view->message);
        $this->view->files = $onlinefiles;
        $this->view->style = 'zform.css';      
       
    }




    public function tableAction()
    {
//echo "INTO TABLE"; exit;
        $request = $this->getRequest();
        $getvalues = $request->getParams();

        $ptmap = new Application_Model_ProgTableMapper();
        $gridmap = new Application_Model_WorshipGridMapper();
        $startdate = $this->lastSunday();
        $where = array(
            array('servicedate' , ' >= ', $startdate),
            array('sunday' , ' = ', 'yes'),
        );
        $gridentries = $gridmap->fetchWhere($where);
        if ($this->getRequest()->isPost())
        {
            $councilPositions=array("Operations Group Director", 
              "Membership Group Director", "Ministry Group Director", "Outreach Group Director",
              "Communications Group Director","Education Group Director",  "Finance Group Director", "Council Convenor");
            $council = $this->getPeople($councilPositions);
            $functions = new Cvuuf_authfunctions();
            $auth = $functions->getAuth('');

//$council[1]['id'] = 1085; echo "MIKE IS FORCED IN COUNCIL #3 SLOT!!!";
            
            $found = 0;
            foreach($council as $person)
            {
                if ($auth->memberid == $person['id'])
                {
                    $found = 1;
                    break;
                }
            }
//var_dump($found);                
//var_dump($council); 
//var_dump($this->auth); exit;
            if ($found == 0)
                $this->view->message = "Only Council members are authorized to make reservations.";
            else
            {
    
    
                if (isset($getvalues['clear']))
                {
                    if (isset($getvalues['did']))
                        $dates = $getvalues['did'];
                    else $dates = array();
                    if (count($dates) == 0)
                        $this->view->message = "No dates selected, nothing to clear.";
                    else
                    {
                        foreach($dates as $cdate)
                        {
                            $where = array(
                                array('reservedate' , ' = ', $cdate),
                            );
                            $entries = $ptmap->fetchWhere($where);
                            if (count($entries) > 0)
                            {
                                $ptid = $entries[0]->id;
    //var_dump($ptid); exit; 
                                $ptmap->delete($ptid);
                            }
                            else
                                $this->view->message = "Date selected to clear has no reservation.";
                                                   
                        }
                    }
                    
                }
                
                elseif (isset($getvalues['getdate']))
                {
    
                    if (isset($getvalues['did']))
                        $dates = $getvalues['did'];
                    else $dates = array();
                    if (count($dates) == 0)
                        $this->view->message = "No dates selected, nothing to do.";
                    else
                    {
                        $thedate = $dates[0];
                        $person = '';
                        $program = '';
                        if (isset($getvalues['person']))
                            $person = $getvalues['person'];
                        else
                            $this->view->message = "No person entered.";
    //var_dump($dates);
                        if (isset($getvalues['program']))
                            $program = $getvalues['program'];
                        else
                            $this->view->message = "No program entered.";
    
    //echo "Date: ", $thedate, " Person: ", $person, " Program: ", $program;
    
                        $ptline = new Application_Model_ProgTable();
                        $ptline->reservedate = $thedate;
                        $ptline->person = $person;
                        $ptline->program = $program;
                        $ptmap->save($ptline);
                    }
                }
            }
        }

//var_dump($gridentries);
        $countDates = 0;
        $table = array();
      	foreach($gridentries as $gridentry) 
        {
            if ($countDates++ > 15)
                break;
        		$dateID = $gridentry->id;
            $where = array(
                array('reservedate' , ' = ', $dateID),
            );
            $entries = $ptmap->fetchWhere($where);
//var_dump($dateID, $entries);
            if (count($entries) <> 0)
                $table[] = array($dateID, $gridentry->servicedate, $entries[0]->person, $entries[0]->program);
            else
                $table[] = array($dateID, $gridentry->servicedate, '', '');
        }
//var_dump($table); exit;
        $this->view->entries = $table;
        $this->view->style = 'zform.css';       
        

    }
    
    
}
