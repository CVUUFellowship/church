<?php

class WelcomingController extends Zend_Controller_Action
{



      public function lastThreemonths($format = 'mysql')
      {
          $Tdate = getdate(mktime(0,0,0, date('m')-3, date('d')+8,date('Y')));
          $today = $Tdate['wday'];
          $TDay = $Tdate['mday'];
          $Month = $Tdate['mon'];
          $Year = $Tdate['year'];
          $MDay = $TDay - $today;
          $lastthree = mktime(0, 0, 0, $Month, $MDay, $Year);
          if ($format == 'mysql')
          {
              $lastdate = date("Y-m-d", $lastthree);
              return($lastdate);
          }
          else
              return($lastthree);      
      }


      public function lastFourmonths($format = 'mysql')
      {
          $Tdate = getdate(mktime(0,0,0, date('m')-4, date('d')+8,date('Y')));
          $today = $Tdate['wday'];
          $TDay = $Tdate['mday'];
          $Month = $Tdate['mon'];
          $Year = $Tdate['year'];
          $MDay = $TDay - $today;
          $lastfour = mktime(0, 0, 0, $Month, $MDay, $Year);
          if ($format == 'mysql')
          {
              $lastdate = date("Y-m-d", $lastfour);
              return($lastdate);
          }
          else
              return($lastfour);      
      }

      public function lastSixmonths($format = 'mysql')
      {
          $Tdate = getdate(mktime(0,0,0, date('m')-6, date('d')+8,date('Y')));
          $today = $Tdate['wday'];
          $TDay = $Tdate['mday'];
          $Month = $Tdate['mon'];
          $Year = $Tdate['year'];
          $MDay = $TDay - $today;
          $lastsix = mktime(0, 0, 0, $Month, $MDay, $Year);
          if ($format == 'mysql')
          {
              $lastdate = date("Y-m-d", $lastsix);
              return($lastdate);
          }
          else
              return($lastsix);      
      }

      public function nextSunday($format = 'mysql')
      {
          $Tdate = getdate(mktime(0,0,0, date('m'), date('d')+7,date('Y')));
          $today = $Tdate['wday'];
          $TDay = $Tdate['mday'];
          $Month = $Tdate['mon'];
          $Year = $Tdate['year'];
          $MDay = $TDay - $today;

          if ($MDay <= 0) {
          	$lastMonth = $Month-1;
          	$lastday = mktime(0, 0, 0, $Month, 0, $Year);
          	$lastday = strftime("%d", $lastday);
          	$MDay = $lastday + $MDay;
          	$Month = $lastMonth;
          }

          $nextdate = mktime(0, 0, 0, $Month, $MDay, $Year);
          if ($format == 'mysql')
          {
              $lastdate = date("Y-m-d", $nextdate);
              return($lastdate);
          }
          else
              return($nextdate);      
      }


      
      public function today()
      {
          return(date("Y-m-d"));    
      }
      


      public function formatPhoneNumber($strPhone, $format="") 
      {
          $strPhone = preg_replace("/[^0-9]/",'', $strPhone);
          if (strlen($strPhone) != 10) {
            return $strPhone;
          }
          
          $strArea = substr($strPhone, 0, 3);
          $strPrefix = substr($strPhone, 3, 3);
          $strNumber = substr($strPhone, 6, 4);
          
          if ($format=='-')
            $strPhone = $strArea."-".$strPrefix."-".$strNumber;
          
          return ($strPhone);
      }



    public function init()
    {
        $this->view->theme = 'data';        /* Initialize action controller here */
        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth();
        if ($auth === null)
            $this->_redirect('/auth/index');
        $this->view->level = $auth->level;
        $this->userid = $auth->memberid;    
    }

    public function indexAction()
    {
        // action body
    }


    public function createcalendarAction()
    {
        $functions = new Cvuuf_functions();
        $lastsunday = $functions->lastsunday('raw');
        $lasttime = date("m/d", $lastsunday);
        $lastdate = date("Y-m-d", $lastsunday);
        $nextsunday = $this->nextSunday('raw');
        $date = date("M d", $nextsunday);
        $caldate = strtoupper($date);
        $ThreeMos = $this->lastThreemonths();
//echo "three months $ThreeMos <br>"; 
        $FourMos = $this->lastFourmonths();
        $SixMos = $this->lastSixmonths();
        $LastYear = $functions->lastYear();
        $Status = 'Visitor';
        $Format = 'HTML';

        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['Status']))
                    $Status = $getvalues['Status'];
                if (isset($getvalues['Format']))
                    $Format = $getvalues['Format'];
            }
        }

        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
        }
        
        if (isset($getvalues['Action']))
        if ($getvalues['Action'] == 'Log') 
        {
            $ids = array('id1', 'id2');
            foreach ($ids as $id)
            {
            		if (isset($getvalues[$id]))	
                {
              			$vid = $getvalues[$id];
//var_dump($id, $vid);
              			$n = count($vid);
              			for($i = 0; $i < $n;) 
                    {
              				  $theID = $vid[$i++];
              				  if ($theID) 
                        {
                            $visitsmap = new Application_Model_VisitsMapper();
                            $visit = new Application_Model_Visits();
                            $visit->personid = $theID;
                            $visit->visitdate = $lastdate;
                            $visit->service = substr($id, 2, 1);
                            $visitsmap->save($visit, 'new');
//var_dump($visit);
              				  }
            			 }
    		        }
            }		
        }

        if (isset($getvalues['Format']))
            $Format = $getvalues['Format'];
        
        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', $Status),
            );            
        $persons = $peoplemap->fetchWhere($where);
        
        if ($Format <> 'HTML')
        {
            require_once 'EzPDF/class.ezpdf.php';
            $pdf = new Cezpdf('LETTER', 'portrait');
            $pdf->selectFont('./fonts/Helvetica.afm');
        }
        $number = 0; 
        foreach ($persons as $person) 
        {
            $missing = '';
            $hid = $person->householdid;
//var_dump($hid);
            $housemap = new Application_Model_HouseholdsMapper();
            $where = array(
                array('id', ' = ', $hid),
                );            
            $house = current($housemap->fetchWhere($where));
//var_dump($house);
            $phone = $house->phone;
            if (strlen($phone) == 0)
                $missing = $missing . 'P';
//var_dump($phone);
            $email = $person->email;
            if (strlen($email) == 0)
                $missing = $missing . 'E';
//var_dump($email);
            $addr = $house->street;
            if (strlen($addr) == 0)
                $missing = $missing . 'A';
//var_dump($addr);
//var_dump($missing);
//exit;


            if ($Status == 'Visitor') 
            {
                
                $PriorUU = '';
                $Comment = '';
                $visitsmap = new Application_Model_VisitsMapper();
                $where = array(
                    array('personid', ' = ', $person->id),
                    );            
/* NOTE violation here - using actual row names  */
                $visits = $visitsmap->fetchWhere($where, array('VisitDate DESC'));
              	$list = '';
            		if (isset($visits)) 
                {
              			$nvisits = 0;
              			$first = true;
              			$countvisits = count($visits) - 1;
              //echo "Count is ", $countvisits, "<br>";
              			foreach ($visits as $visit) {
              				  if ($first) 
                            $first = false;
                        else 
                            $list = $list . ' ';
            			
                				if ($countvisits > $nvisits && $nvisits < 5) 
                        {
                  					$visitdate = substr($visit->VisitDate, 5, 2) . "/" . substr($visit->VisitDate, 8, 2);
                  					if ($visit->VisitDate < $LastYear)
                  						  $list = $list . "<i>$visitdate</i>";
                  					elseif ($lasttime == $visitdate) 
                  						$list = $list . "<b>$visitdate</b>";
                  					else 
                  						$list = $list . $visitdate;
                  			}
    
                				if (++$nvisits == 5) 
                					$list = $list . " more";
    
                				$firsttime = substr($visit->VisitDate, 5, 2) . "/" . substr($visit->VisitDate, 8, 2);
                				if ($visit->VisitDate < $LastYear)
                  					$firstdate = "<i>$firsttime</i>";
                				elseif ($lasttime == $firsttime) 
                            $firstdate = "<b>$firsttime</b>";
                				else
                            $firstdate = $firsttime;  
                    }
                }
                else  /* no visits */
                {
                    $firstdate = '';
                }
                $visitorsmap = new Application_Model_VisitorsMapper();
                $where = array(
                    array('id', ' = ', $person->id),
                    );            
                $visitor = current($visitorsmap->fetchWhere($where));
                $visitorname = $person->firstname . ' ' . $person->lastname;
                if ($visitor <> false)
                {
                    $PriorUU = $visitor->prioruu;
                    $Comment = $visitor->comment;
                    if ($person->creationdate < $ThreeMos && $firstdate[0] <> '<') 
                    {
//var_dump($visitor);
//echo "visitor signed $visitor->signeddate creation $person->creationdate<br>"; exit;
                        
                        $firstdate = '<b>' . $firstdate . '</b>';
                        $visitorname = '<b>' . $visitorname . '</b>';
                    }
                }
              	
                
                $table[$number++] = array('' => $number, 'ID' => $person->id,
              	'1' => " ", '2' => " ", 'Name' => $visitorname, 
                  'Miss' => $missing, 'First' => $firstdate, 
              		'Visits' => $list, 'UU' => $PriorUU, 'Comments' => substr($Comment, 0, 39));
            }  /* end visitor type selection*/
            
            elseif ($Status == 'NewFriend')
            {
                $connectionsmap = new Application_Model_ConnectionsMapper();
                $where = array(
                    array('peopleid', ' = ', $person->id),
                    );            
                $connection = current($connectionsmap->fetchWhere($where));
                if ($connection == false)
                {
                    $frienddate = '';
                    $comments = '';
                }
                else  /* connections record exists */
                {
                    $comments = $connection->comments;
                    $frienddate = $connection->frienddate;
                    
                    if ($frienddate == '0000-00-00')
                        $frienddate = '';
                    if ($frienddate < $LastYear)         
                        $frienddate='<b>' . $frienddate . '</b>';
                    if ($frienddate < $SixMos)
                        $frienddate = '<i>' . $frienddate . '</i>';
                    if ($frienddate < $FourMos)
                   	  $frienddate = '<u>' . $frienddate . '</u>';
                }
                      
                $visitorsmap = new Application_Model_VisitorsMapper();
                $where = array(
                    array('id', ' = ', $person->id),
                    );            
                $visitor = current($visitorsmap->fetchWhere($where));
                if ($visitor <> false)
                    $PriorUU = $visitor->prioruu;
                  
              	$table[$number++] = array('' => $number, 'ID' => $person->id,
              	'X' => " ", 'Name' => $person->firstname . ' ' . $person->lastname, 'Friend Date' => $frienddate,
              		'UU' => $PriorUU, 'Comments' => substr($comments, 0, 39),
                  'Miss' => $missing);
            }
            
        }  /* person loop */

        if ($Status == 'Visitor')
            $CalType = 'VISITOR';
        else
            $CalType = 'NEW FRIENDS';
        if ($Format <> 'HTML') 
        {
            $pdf->ezTable($table, '', $caldate . " " . $CalType . " CALENDAR     ATT: 9:15 _____  11:00 _____", array('showHeadings'=>1,'shaded'=>1,
            	'showLines' => 1, 'fontSize' => 8, 'titleFontSize' => 12, 'rowGap'=>0));
//exit;
            $pdf->ezStream();
            exit;
        }
        else
        {
            $this->view->date = $lastdate;
            $this->view->table = $table;
            $this->view->Status = $Status;
            $this->view->CalType = $CalType;
            $this->view->style = 'zform.css';
        }
    }


    public function showvisitorsAction()
    {
        $Format = 'HTML';
        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['Format']))
                    $Format = $getvalues['Format'];
            }
        }

        $theDate = $this->today();
        if (isset($getvalues['Format']))
            $Format = $getvalues['Format'];
        
        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'Visitor'),
            );            
        $persons = $peoplemap->fetchWhere($where, array('creationdate', 'lastname', 'firstname'));

        $number = 0; 
        foreach ($persons as $person) {
            $hid = $person->householdid;
            $householdmap = new Application_Model_HouseholdsMapper();
            $house = $householdmap->find($hid);

            $visitorsmap = new Application_Model_VisitorsMapper();
            $where = array(
                array('id', ' = ', $person->id),
                );            
            $visitor = current($visitorsmap->fetchWhere($where));
            if ($visitor <> false)
                $PriorUU = $visitor->prioruu;
            else
                $PriorUU = '';
          
          	$creationdate = $person->creationdate;
            $table[$number++] = array('' => $number, 'ID' => $person->id,
          		'Name' => $person->firstname . ' ' . $person->lastname,
          		'Created' => substr($creationdate, 5, 2) . "/" . substr($creationdate, 8, 2)
                 . "/" . substr($creationdate, 0, 4),
          		'Household' => $house['householdname'],
          		'Address' => $house['street'] . ',' . $house['city'] . ', ' . $house['state'] . '	' . $house['zip'],
          		'Phone' => $this->formatPhoneNumber($house['phone'], '-'),
          		'UU' => $PriorUU);
        }

        if ($Format <> 'HTML')
        {
            require_once 'EzPDF/class.ezpdf.php';
            $pdf = new Cezpdf('LETTER', 'portrait');
            $pdf->selectFont('./fonts/Helvetica.afm');
          	$pdf->ezTable($table, '', $theDate . " VISITORS BY DATE", array('showHeadings' => 1, 'shaded' => 1,
          		'showLines' => 1, 'fontSize' => 7, 'titleFontSize' => 12));
          	$pdf->ezStream();
            exit;
        }

        $this->view->table = $table;
        $this->view->style = 'zform.css';
       
    }





    public function showmembersAction()
    {
        $Format = 'HTML';
        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['Format']))
                    $Format = $getvalues['Format'];
            }
        }

        $theDate = $this->today();
        if (isset($getvalues['Format']))
            $Format = $getvalues['Format'];
        
        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'Member'),
            );            
        $persons = $peoplemap->fetchWhere($where, array('membershipdate', 'lastname', 'firstname'));

        $number = 0; 
        foreach ($persons as $person) {
         
          	$membershipdate = $person->membershipdate;
        		$table[$number++] = array('ID' => $person->id, 'Name' => $person->firstname . ' ' . $person->lastname,
        		    'Date' => substr($membershipdate, 5, 2) . "/" . substr($membershipdate, 8, 2)
                 . "/" . substr($membershipdate, 0, 4));
        }

        if ($Format <> 'HTML')
        {
            require_once 'EzPDF/class.ezpdf.php';
            $pdf = new Cezpdf('LETTER', 'portrait');
            $pdf->selectFont('./fonts/Helvetica.afm');
          	$pdf->ezTable($table, '', $theDate . " MEMBERS BY DATE", array('showHeadings' => 1, 'shaded' => 1,
          		'showLines' => 1, 'fontSize' => 7, 'titleFontSize' => 12));
          	$pdf->ezStream();
            exit;
        }

        $this->view->table = $table;
        $this->view->style = 'zform.css';
       
    }




    public function showfriendsAction()
    {
        $Format = 'HTML';
        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['Format']))
                    $Format = $getvalues['Format'];
            }
        }

        $theDate = $this->today();
        if (isset($getvalues['Format']))
            $Format = $getvalues['Format'];
        
        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'NewFriend'),
            );            
        $persons = $peoplemap->fetchWhere($where);

        $number = 0; 
        foreach ($persons as $person) {
         
            $connectionsmap = new Application_Model_ConnectionsMapper();
            $where = array(
                array('peopleid', ' = ', $person->id),
                );            
            $connection = current($connectionsmap->fetchWhere($where));
            if ($connection == false)
                $frienddate = '';
            else  /* connections record exists */
                $frienddate = $connection->frienddate;

            $missing = '';
            $hid = $person->householdid;
//var_dump($hid);
            $housemap = new Application_Model_HouseholdsMapper();
            $where = array(
                array('id', ' = ', $hid),
                );            
            $house = current($housemap->fetchWhere($where));
//var_dump($house);
            $phone = $house->phone;
            if (strlen($phone) == 0)
                $missing = $missing . 'P';
//var_dump($phone);
            $email = $person->email;
            if (strlen($email) == 0)
                $missing = $missing . 'E';
//var_dump($email);
            $addr = $house->street;
            if (strlen($addr) == 0)
                $missing = $missing . 'A';
//var_dump($addr);
//var_dump($missing);
//exit;


        		$table[$number++] = array('ID' => $person->id, 'Name' => $person->firstname . ' ' . $person->lastname,
        		    'Date' => $frienddate, 'Missing' => $missing);



        }
          function cmp($a, $b)
          {
              return strcmp($a["Date"], $b["Date"]);
          }
        usort($table, "cmp");
//var_dump($table); 
//exit;
        if ($Format <> 'HTML')
        {
            require_once 'EzPDF/class.ezpdf.php';
            $pdf = new Cezpdf('LETTER', 'portrait');
            $pdf->selectFont('./fonts/Helvetica.afm');
          	$pdf->ezTable($table, '', $theDate . " NEW FRIENDS BY DATE", array('showHeadings' => 1, 'shaded' => 1,
          		'showLines' => 1, 'fontSize' => 7, 'titleFontSize' => 12));
          	$pdf->ezStream();
            exit;
        }

        $this->view->table = $table;
        $this->view->style = 'zform.css';
       
    }




    public function recentmembersAction()
    {
        $functions = new Cvuuf_functions();
        $Format = 'HTML';
        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['Format']))
                    $Format = $getvalues['Format'];
            }
        }

        $theDate = $this->today();
        $lastyear = $functions->lastYear();
        
        if (isset($getvalues['Format']))
            $Format = $getvalues['Format'];
        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'Member'),
            array('membershipdate', ' > ', $lastyear),
            );            
        $persons = $peoplemap->fetchWhere($where);
        $number = 0; 
        foreach ($persons as $person) {
         
            $housemap = new Application_Model_HouseholdsMapper();
            $house = $housemap->find($person->householdid);
            $phone = $this->formatPhoneNumber($house['phone'], '-');
            
            $connectionsmap = new Application_Model_ConnectionsMapper();
            $where = array(
                array('peopleid', ' = ', $person->id),
                );            
            $connections = $connectionsmap->fetchWhere($where);
            $connectionobj = current($connections);
            $inducted = $connectionobj->inducted;
            $angelid = $connectionobj->angelid;
            $angel = $peoplemap->find($angelid);
            if ($angel <> false)
                $angelname = $angel['firstname'];
    
          	$table[$number++] = array('ID' => $person->id,
          		'Name' => $person->firstname . ' ' . $person->lastname,
          		'Angel' => $angelname,
          		'Inducted' => $inducted,
          		'Date' => $person->membershipdate,
          		'Phone' => $phone,
          		'Comments'=>$connectionobj->comments
          		);
        }

          function cmp($a, $b)
          {
              return strcmp($a["Date"], $b["Date"]);
          }
        usort($table, "cmp");

        if ($Format <> 'HTML')
        {
            require_once 'EzPDF/class.ezpdf.php';
            $pdf = new Cezpdf('LETTER', 'portrait');
            $pdf->selectFont('./fonts/Helvetica.afm');
          	$pdf->ezTable($table, '', $theDate . " RECENT MEMBERS", array('showHeadings' => 1, 'shaded' => 1,
          		'showLines' => 1, 'fontSize' => 7, 'titleFontSize' => 12));
          	$pdf->ezStream();
            exit;
        }

        $this->view->table = $table;
        $this->view->style = 'zform.css';
       
    }




    public function recentfriendsAction()
    {
        $functions = new Cvuuf_functions();
        $Format = 'HTML';
        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['Format']))
                    $Format = $getvalues['Format'];
            }
        }

        $theDate = $this->today();
        $lastyear = $functions->lastYear();
        if (isset($getvalues['Format']))
            $Format = $getvalues['Format'];
        
        $mwhere = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'Member'),
            array('membershipdate', ' > ', $lastyear),
            );
        $fwhere = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'NewFriend'),
            );
        
        $number = 0; 
        foreach (array($mwhere, $fwhere) as $pwhere)
        {
            $peoplemap = new Application_Model_PeopleMapper();
            $persons = $peoplemap->fetchWhere($pwhere, array('lastname', 'firstname'));
            foreach ($persons as $person) 
            {
                
                $connectionsmap = new Application_Model_ConnectionsMapper();
                $where = array(
                    array('peopleid', ' = ', $person->id),
                    );            
                $connections = $connectionsmap->fetchWhere($where);
                $connectionobj = current($connections);
                if ($connectionobj)
                {
                    $comments = $connectionobj->comments;
                    $frienddate = $connectionobj->frienddate;
                    $inducted = $connectionobj->inducted;
                    $angelid = $connectionobj->angelid;
                    $angel = $peoplemap->find($angelid);
                    if ($angel <> false)
                        $angelname = $angel['firstname'];
                    else
                        $angelname = '';
                }    
                else
                {
                    $comments = '';
                    $frienddate = '';
                    $inducted = '';
                    $angelname = '';
                }

                $hid = $person->householdid;
                $housemap = new Application_Model_HouseholdsMapper();
                $house = $housemap->find($hid);
                $phone = $this->formatPhoneNumber($house['phone'], '-');
                $zip = $house['zip'];
                $hoodmap = new Application_Model_NeighborhoodsMapper();
                $where = array(
                    array('householdid', ' = ', $hid),
                    );            
                $hood = current($hoodmap->fetchWhere($where));
                $nid = $hood->hoodid;
                $namemap = new Application_Model_HoodsMapper();
                $where = array(
                    array('id', ' = ', $nid),
                    );            
                $names = $namemap->fetchWhere($where);
                $name = current($names);
                $hoodname = $name->hoodname;
    
                if ($person->status == 'NewFriend')
                    $date = $frienddate;
                else
                    $date = $person->membershipdate;
                    
                if (($person->status == 'NewFriend' && $frienddate > $lastyear)
                  || $person->status == 'Member')
                  	$table[$number++] = array('ID' => $person->id,
                  		'Name' => $person->firstname . ' ' . $person->lastname,
                  		'Created' => $person->creationdate,
                  		'Angel' => $angelname,
                  		'Status' => $person->status,
                  		'Date' => $date,
                      'Neighborhood' => $hoodname,
                  		'Phone' => $phone,
                  		'Email' => $person->email,
                  		'Comments'=>$comments
                  	);
            }
        }

          function cmp($a, $b)
          {
              return strcmp($a["Date"], $b["Date"]);
          }
        usort($table, "cmp");

        if ($Format <> 'HTML')
        {
            require_once 'EzPDF/class.ezpdf.php';
            $pdf = new Cezpdf('LETTER', 'landscape');
            $pdf->selectFont('./fonts/Helvetica.afm');
          	$pdf->ezTable($table, '', $theDate . " RECENT FRIENDS AND MEMBERS", array('showHeadings' => 1, 'shaded' => 1,
          		'showLines' => 1, 'fontSize' => 7, 'titleFontSize' => 12));
          	$pdf->ezStream();
            exit;
        }

        $this->view->table = $table;
        $this->view->style = 'zform.css';
       
    }




    public function allfriendsAction()
    {
        $functions = new Cvuuf_functions();
        $Format = 'HTML';
        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['Format']))
                    $Format = $getvalues['Format'];
            }
        }

        $theDate = $this->today();
        if (isset($getvalues['Format']))
            $Format = $getvalues['Format'];
        $fwhere = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'NewFriend'),
            );
        
        $number = 1; 
        $table = array();
        foreach (array($fwhere) as $pwhere)
        {
            $peoplemap = new Application_Model_PeopleMapper();
            $persons = $peoplemap->fetchWhere($pwhere, array('lastname', 'firstname'));
            foreach ($persons as $person) 
            {
                
                $connectionsmap = new Application_Model_ConnectionsMapper();
                $where = array(
                    array('peopleid', ' = ', $person->id),
                    );            
                $connections = $connectionsmap->fetchWhere($where);
                $connectionobj = current($connections);
                if ($connectionobj)
                {
                    $comments = $connectionobj->comments;
                    $frienddate = $connectionobj->frienddate;
                    $inducted = $connectionobj->inducted;
                    $angelid = $connectionobj->angelid;
                    $angel = $peoplemap->find($angelid);
                    if ($angel <> false)
                        $angelname = $angel['firstname'];
                    else
                        $angelname = '';
                }    
                else
                {
                    $comments = '';
                    $frienddate = '';
                    $inducted = '';
                    $angelname = '';
                }

                $hid = $person->householdid;
                $housemap = new Application_Model_HouseholdsMapper();
                $house = $housemap->find($hid);
                $phone = $this->formatPhoneNumber($house['phone'], '-');
                $zip = $house['zip'];
                $hoodmap = new Application_Model_NeighborhoodsMapper();
                $where = array(
                    array('householdid', ' = ', $hid),
                    );            
                $hood = current($hoodmap->fetchWhere($where));
                $nid = $hood->hoodid;
                $namemap = new Application_Model_HoodsMapper();
                $where = array(
                    array('id', ' = ', $nid),
                    );            
                $names = $namemap->fetchWhere($where);
                $name = current($names);
                $hoodname = $name->hoodname;
    
                $date = $frienddate;
                    
                if ($person->status == 'NewFriend')
                    $table[$number++] = array('ID' => $person->id,
                  		'Name' => $person->firstname . ' ' . $person->lastname,
                  		'Created' => $person->creationdate,
                  		'Angel' => $angelname,
                  		'Status' => $person->status,
                  		'Date' => $date,
                      'Neighborhood' => $hoodname,
                  		'Phone' => $phone,
                  		'Email' => $person->email,
                  		'Comments'=>$comments
                  	);
            }
        }

          function cmp($a, $b)
          {
              return strcmp($a["Date"], $b["Date"]);
          }
        usort($table, "cmp");

        if ($Format <> 'HTML')
        {
            require_once 'EzPDF/class.ezpdf.php';
            $pdf = new Cezpdf('LETTER', 'landscape');
            $pdf->selectFont('./fonts/Helvetica.afm');
          	$pdf->ezTable($table, '', $theDate . " ALL FRIENDS", array('showHeadings' => 1, 'shaded' => 1,
          		'showLines' => 1, 'fontSize' => 7, 'titleFontSize' => 12));
          	$pdf->ezStream();
            exit;
        }

        $this->view->table = $table;
        $this->view->style = 'zform.css';
       
    }





    public function inductionAction()
    {
        $functions = new Cvuuf_functions();
        $lastyear = $functions->lastYear();
        $Format = 'HTML';
        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'Member'),
            array('membershipdate', ' > ', $lastyear),
            );            
        $persons = $peoplemap->fetchWhere($where, array('lastname', 'firstname'));
        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['Format']))
                    $Format = $getvalues['Format'];
            }
        }
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['status']))
                {
                    $status = $getvalues['status'];
                  	if ($status[0] == 'I')
                  		  $mark = 'Yes';
                  	else
                  		  $mark = 'No';
                  	unset($vname);
                    $this->view->message = array();
                  	if ($status[0] == 'I') $this->view->message[] = "The following are marked inducted:";
                  	else $this->view->message[] = "The following are marked not inducted:";
                  	
                    foreach ($persons as $person)
                    {
                  		  $name = $person->firstname . ' ' . $person->lastname;
                        $vname[$person->id] = $name;
                    }
                  	if (isset($getvalues['id']))	
                    {
                    		$vid = $getvalues['id'];
                    		$n = count($vid);
                    		for ($i = 0; $i < $n; $i++) 
                        {
                    		    $theID = $vid[$i];
                    		    if ($theID) 
                            {
                                $connectionsmap = new Application_Model_ConnectionsMapper();
                                $where = array(
                                    array('peopleid', ' = ', $theID),
                                    );            
                                $connection = current($connectionsmap->fetchWhere($where));
                                $connection->inducted = $mark;
                                $connectionsmap->save($connection);
                    				    $this->view->message[] = $theID . ' ' . $vname[$theID];
                            }
                    		}
                  	}
              
                }
                
                if (isset($getvalues['ask']))
                {
                    $ask = $getvalues['ask'];
                  	if ($ask[0] == 'D')
                  		  $mark = 'Yes';
                  	else
                  		  $mark = 'No';
                  	unset($vname);
                    $this->view->message = array();
                  	if ($ask[0] == 'D') $this->view->message[] = "Do not ask the following:";
                  	else $this->view->message[] = "Remove do not ask from the following:";
                  	
                    foreach ($persons as $person)
                    {
                  		  $name = $person->firstname . ' ' . $person->lastname;
                        $vname[$person->id] = $name;
                    }
                  	if (isset($getvalues['id']))	
                    {
                    		$vid = $getvalues['id'];
                    		$n = count($vid);
                    		for ($i = 0; $i < $n; $i++) 
                        {
                    		    $theID = $vid[$i];
                    		    if ($theID) 
                            {
                                $connectionsmap = new Application_Model_ConnectionsMapper();
                                $where = array(
                                    array('peopleid', ' = ', $theID),
                                    );            
                                $connection = current($connectionsmap->fetchWhere($where));
                                $connection->inductiondontask = $mark;
                                $connectionsmap->save($connection);
                    				    $this->view->message[] = $theID . ' ' . $vname[$theID];
                            }
                    		}
                  	}
              
                }
            }

        }
        
        
        $number = 0; 
        $table = array();
        foreach ($persons as $person) 
        {
            $hid = $person->householdid;
            $housemap = new Application_Model_HouseholdsMapper();
            $house = $housemap->find($hid);
            $phone = $this->formatPhoneNumber($house['phone'], '-');
            
            $connectionsmap = new Application_Model_ConnectionsMapper();
            $where = array(
                array('peopleid', ' = ', $person->id),
                );            
            $connection = current($connectionsmap->fetchWhere($where));
            $inducted = $connection->inducted;
            $inductiondontask = $connection->inductiondontask;
            
            $name = $person->firstname . ' ' . $person->lastname;
            $bold = ($connection->inductiondontask <> 'Yes') && ($connection->Inducted <> 'Yes');
            $table[] = array("ID" => $person->id, "Name" => ($bold ? '<b>' : '') . $name . ($bold == '' ? '</b>' : ''),
              "Dont" => $connection->InductionDontAsk == 'Yes' ? 'NO' : 'ok', "Date" => $person->membershipdate,
              "Inducted" => $connection->inducted, "Phone" => $phone, "Email" => $person->email);
        
        }

        $this->view->table = $table;
        $this->view->style = 'zform.css';
    }






    public function newclassAction()
    {
        $Format = 'HTML';

        $theDate = $this->today();
        $functions = new Cvuuf_functions();
        $lastyear = $functions->lastYear();
        $request = $this->getRequest();
        
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['id']))
                {
                    $connectionsmap = new Application_Model_ConnectionsMapper();
                    $when = $getvalues['when'];
                    $sqldate= $functions->date_validate($when);
                    $ids = $getvalues['id'];
                    foreach ($ids as $id)
                    {
                        $where = array(
                            array('peopleid', ' = ', $id),
                            );            
                        $connection = current($connectionsmap->fetchWhere($where));
                        if (isset($getvalues['marku']))
                        {
                            if ($when <> '')
                                $connection->classdate = $sqldate;
                            else
                                $this->view->message = "Must specify class date.";
                        }
                        else
                            $connection->classdate = '';
                        $connectionsmap->save($connection);
                    }
                  
                }
                else
                    $this->view->message = "No person selected.";
            }
        }    
        
        $mwhere = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'Member'),
            array('creationdate', ' > ', $lastyear),
            );
        $fwhere = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'NewFriend'),
            );
        
        $number = 0; 
        foreach (array($mwhere, $fwhere) as $pwhere)
        {
            $peoplemap = new Application_Model_PeopleMapper();
            $persons = $peoplemap->fetchWhere($pwhere, array('lastname', 'firstname'));
            foreach ($persons as $person) 
            {
                $connectionsmap = new Application_Model_ConnectionsMapper();
                $where = array(
                    array('peopleid', ' = ', $person->id),
                    );            
                $connection = current($connectionsmap->fetchWhere($where));
                if ($connection)
                {
                    $frienddate = $connection->frienddate;
                    $date = $connection->classdate;
        		        $classdate = ($date == '0000-00-00') ? '' : $date;
                }
                else
                {
                    $frienddate = '';
                    $classdate = '';
                }
                $name = $person->firstname . ' ' . $person->lastname;

                $hid = $person->householdid;
                $housemap = new Application_Model_HouseholdsMapper();
                $house = $housemap->find($hid);
                $phone = $this->formatPhoneNumber($house['phone'], '-');

                if ($frienddate > $lastyear)
                    $table[] = array('ID' => $person->id, "Name" => $name, 
                      "Status" => $person->status, "Date" => $frienddate, "Class" => $classdate,
                      "Email" => $person->email, "Phone" => $phone);
            }
        }

          function cmp($a, $b)
          {
              return strcmp($a["Date"], $b["Date"]);
          }
        usort($table, "cmp");

        $this->view->table = $table;
        $this->view->style = 'zform.css';
       
    }


    public function angelsAction()
    {
        $functions = new Cvuuf_functions();
        $lastyear = $functions->lastYear();
        $request = $this->getRequest();
        $peoplemap = new Application_Model_PeopleMapper();

        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['assignbutton']))
                {
                    if ($getvalues['angel'] <> '')
                        $angelid = $getvalues['angel'];
                    else
                        $angelid = $this->userid;
                }
                $angel = $peoplemap->find($angelid);
                $angelName = $angel['firstname'] . " " . $angel['lastname'];                
                $this->view->message = array();

          			if (isset($getvalues['id']))	
                {
              			$vid = $getvalues['id'];
              			$n = count($vid);
              			for($i = 0; $i < $n;) 
                    {
              				  $theID = $vid[$i++];
              				  if ($theID) 
                        {
                            $person = $peoplemap->find($theID);
            
                            $connectionsmap = new Application_Model_ConnectionsMapper();
                            $where = array(
                                array('peopleid', ' = ', $theID),
                                );            
                            $connection = current($connectionsmap->fetchWhere($where));
                            $connection->angelid = $angelid;
                            $connectionsmap->save($connection);
                				    $this->view->message[] = "Angel for " . $person['firstname'] . " " . $person['lastname'] . " is now " . $angelName;

              				  }
            			   }
                 }               
            }
        }
        
        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' IN ', "('Member', 'NewFriend', 'Affiliate')"),
            );            
        $persons = $peoplemap->fetchWhere($where, array('lastname', 'firstname'));

        foreach ($persons as $person) 
        {
            $connectionsmap = new Application_Model_ConnectionsMapper();
            $where = array(
                array('peopleid', ' = ', $person->id),
                );            
            $connection = current($connectionsmap->fetchWhere($where));
            if ($connection)
            {
                $frienddate = $connection->frienddate;
                $angelid = $connection->angelid;
                if ($angelid == 0)
                {
                    if ($person->membershipdate <> '0000-00-00')
                        $date = $person->membershipdate;
                    else
                        $date = $frienddate;
                    $name = $person->firstname . ' ' . $person->lastname;
                    
                    if ($date > $lastyear)
                    $table[] = array('ID' => $person->id, "Name" => $name, 
                      "Status" => $person->status, "Date" => $person->creationdate, "StatusDate" => $date);
                }
            }
        }
          function cmp($a, $b)
          {
              return strcmp($a["Date"], $b["Date"]);
          }
        usort($table, "cmp");

        $this->view->table = $table;
        $this->view->style = 'zform.css';
       
    }
            

    public function photosAction()
    {
        /* returns personid from name */
        function idFromName($name)
        {
            $peoplemap = new Application_Model_PeopleMapper();
            $space = strpos($name, ' ');
            if ($space === false)
                return(0);
            $space2 = strpos(substr($name, $space + 1), ' ');
            if ($space2 <> false)
                $space = $space2 + $space + 1;
            $firstname = substr($name, 0, $space);
            $lastname = substr($name, $space + 1);
            
            $where = array(
                array('firstname', ' = ', $firstname),
                array('lastname', ' = ', $lastname),
                );            
            $people = $peoplemap->fetchWhere($where);
            if (count($people) <> 1)
                return(0);
            else
                return(current($people)->id);
            
        }

        $theDate = $this->today();
        $peoplemap = new Application_Model_PeopleMapper();

        $types = "'NewFriend', 'Member', 'Affiliate'";
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('photolink', ' <> ', ''),
            array('status', ' IN ', "($types)"),
              );
        $Pics = $peoplemap->fetchWhere($where);
        $numPics = count($Pics);

        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('photolink', ' <> ', ''),
            array('status', ' = ', 'Member'),
              );
        $PicsMem = $peoplemap->fetchWhere($where);
        $numPicsMem = count($PicsMem);

        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('photolink', ' = ', ''),
            array('status', ' IN ', "($types)"),
              );
        $noPics = $peoplemap->fetchWhere($where);
        $numNoPics = count($noPics);
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('photolink', ' = ', ''),
            array('status', ' = ', 'Member'),
              );
        $noPicsMem = $peoplemap->fetchWhere($where);
        $numNoPicsMem = count($noPicsMem);
      
//echo "numPics $numPics <br>numPicsMem $numPicsMem <br>noPics $numNoPics <br>noPicsMem $numNoPicsMem <br>";
//exit;
        $request = $this->getRequest();
        if ($this->getRequest()->isPost())
        {
            $postvalues = $request->getParams();
            if (isset($postvalues))
            {
                if (isset($postvalues['listbutton']))
                {
                    require_once 'EzPDF/class.ezpdf.php';
                    $pdf = new Cezpdf('LETTER', 'landscape');
                    $pdf->selectFont('./fonts/Helvetica.afm');
                    $number = 0; 
                    foreach ($noPics as $person) {
                    	$table[$number++] = array(''=>$number, 'ID'=>$person->id,
                		  'Name'=>$person->firstname.' '.$person->lastname,
                      'Status'=>$person->status);
                    }
                  	$pdf->ezTable($table,'',$theDate." PEOPLE WITHOUT PHOTOS", array('showHeadings'=>1,'shaded'=>1,
                	   	'showLines'=>1, 'fontSize' => 7, 'titleFontSize' => 12));
                    $pdf->ezStream();
                    exit;                  

                }
                if (isset($postvalues['selectbutton']))
                {
                    if ($postvalues['id'] > 0)
                        $pid = $postvalues['id'];
                    elseif ($postvalues['name'] <> '')
                    {
                        $name = $postvalues['name'];
                        $pid = idFromName($name);
                    }   
                    if ($pid == 0)
                    {
                        $this->view->message = "Cannot process SELECT request without ID# or Name.";
                    }
                    else
                    {
                        $peep = $peoplemap->find($pid);
                        $photolink = $peep['photolink'];
                        $this->view->photolink = $photolink;
                        $this->view->name = $peep['firstname'] . ' ' . $peep['lastname'];
                        $this->view->pid = $peep['id'];
                    }
                
                }
                
                if (isset($postvalues['upbutton']))
                {
                		$this->view->message = array();
                		$files = $_FILES['THEPHOTO'];
                    $publicdir = $_SERVER["DOCUMENT_ROOT"];
                    $filedir = $publicdir . '/media/photos/members/';
                	 	$path_to_file = $filedir;
                    $pid = $postvalues['pid'];
                    $where = array(
                        array('id', ' = ', $pid),
                          );
                    $theperson = current($peoplemap->fetchWhere($where));
                    if ($theperson->photolink <> '')
                        $theName = $theperson->photolink;
                    else
                        $theName = $theperson->firstname . $theperson->lastname . '.jpg';
                                       
                		if ($files['size']) {
                  			$location = $path_to_file . $theName;
                        $tmpfile = $files['tmp_name'];
                  			copy($tmpfile, $location);
//var_dump($location); 
                  			unlink($tmpfile);
                  			$this->view->message[] = "Successfully uploaded photo '".$theName."'";
                        $theperson->photolink = $theName;
                        $peoplemap->save($theperson);
//var_dump($theperson); 
                        $this->view->photolink = $theName;
                        $this->view->name = $theperson->firstname . ' ' . $theperson->lastname;
                        $this->view->pid = $theperson->id;

                		}
                		else
                		    $this->view->message[] = "There is a problem with file '".$theName."'";
                }

            }
        }
    
        $this->view->counts = array($numPics, $numPicsMem, $numNoPics, $numNoPicsMem);
        $this->view->style = 'zform.css';
    
    }





    public function resignedmembersAction()
    {
        $functions = new Cvuuf_functions();
        $Format = 'HTML';
        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                if (isset($getvalues['Format']))
                    $Format = $getvalues['Format'];
            }
        }

        $theDate = $this->today();
        $lastyear = $functions->lastYear();
        
        if (isset($getvalues['Format']))
            $Format = $getvalues['Format'];
        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' = ', 'yes'),
            array('status', ' <> ', ''),
            );            
        $persons = $peoplemap->fetchWhere($where);
//var_dump(count($persons));
        $number = 0; 
        foreach ($persons as $person) {
            if (in_array($person->status, array("Deceased", "Resigned", "Member")))
            {
//var_dump($person);
//exit;

            
              $connectionsmap = new Application_Model_ConnectionsMapper();
              $where = array(
                  array('peopleid', ' = ', $person->id),
                  );            
              $connections = $connectionsmap->fetchWhere($where);
              $connectionobj = current($connections);
              if ($connectionobj <> false) {
//var_dump($connectionobj);                	
                  $comments = $connectionobj->comments;
              }
              else
                  $comments = '';
      
            	$table[$number++] = array('ID' => $person->id,
            		'Name' => $person->firstname . ' ' . $person->lastname,
            		'Status' => $person->status,
            		'Date' => $person->membershipdate,
            		'Resign' => $person->resigndate,
            		'Comments'=>$comments,
            		);
                
            }
        }
//var_dump (sizeof($table));
//var_dump($table);
//exit;

          function cmp($a, $b)
          {
              return strcmp($b["Resign"], $a["Resign"]);
          }
        usort($table, "cmp");

        if ($Format <> 'HTML')
        {
            require_once 'EzPDF/class.ezpdf.php';
            $pdf = new Cezpdf('LETTER', 'portrait');
            $pdf->selectFont('./fonts/Helvetica.afm');
          	$pdf->ezTable($table, '', $theDate . " RESIGNED MEMBERS", array('showHeadings' => 1, 'shaded' => 1,
          		'showLines' => 1, 'fontSize' => 7, 'titleFontSize' => 12));
          	$pdf->ezStream();
            exit;
        }

        $this->view->table = $table;
        $this->view->style = 'zform.css';
       
    }



    public function testAction()
    {
        $functions = new Cvuuf_functions();
        echo $functions->lastSunday(), "<br>";
        echo $functions->lastYear(), "<br>";
        echo $this->lastThreemonths(), "<br>";
        echo $this->lastFourmonths(), "<br>";
        echo $this->lastSixmonths(), "<br>";
        echo $this->nextSunday(), "<br>";
        exit;


            require_once 'EzPDF/class.ezpdf.php';
	$pdf = new Cezpdf('LETTER', 'portrait');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezTable($table,'',$caldate." ".$CalType." CALENDAR     ATT: 9:15 _____  11:00 _____", array('showHeadings'=>1,'shaded'=>1,
		'showLines'=>1, 'fontSize' => 8, 'titleFontSize' => 12, 'rowGap'=>0));
	$pdf->ezStream();
        exit;
    }

}

