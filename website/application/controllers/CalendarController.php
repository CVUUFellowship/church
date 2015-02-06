<?php

class CalendarController extends Zend_Controller_Action
{

        function emails()
        {
//echo "IN EMAILS <br>"; 
            $eventsmap = new Application_Model_CalEventsMapper();
            $where = array(
                array('resultemail', ' < ', '2000-00-00'),
                );            
            $actives = $eventsmap->fetchWhere($where);
//var_dump($actives); 
            if (count($actives) > 0)
            {
//echo "ACTIVE EVENTS FOUND <br>"; 
                $unixtime=mktime(date('G'), date('i'), date('s'), date('m'), date('d'),date('Y'));
                $theDateTime = date("Y-m-d G:i:s", $unixtime);
                $serverName = $_SERVER["SERVER_NAME"];
                foreach($actives as $event)
                {
                    $EventID = $event->eventid;
//echo "ACTIVE EVENT ID $EventID <br>";
                    $entryusermap = new Application_Model_CalEntryUserMapper();                
                    $where = array(
                        array('id', ' = ', $EventID),
                        array('status', ' <> ', 'A'),
                        );
                    $unapEvents = $entryusermap->fetchWhere($where);            
//var_dump($unapEvents); 
                    if (count($unapEvents) == 0)  /* check if all approvals done */
                    {
//echo "APPROVED $event->eventid <br>"; 
                        $event->resultemail = $theDateTime;
                        $eventsmap->save($event);
                        $entrymap = new Application_Model_CalEntryMapper();
                        $entry = $entrymap->find($EventID);
                        $eventName = $entry['name']; 

                        $msg = "Your event request $EventID, '$eventName' has been approved.".
                          "<br><br>Display the event <a href='http://$serverName/webcal/view_entry.php?id=$EventID'> here</a>.";
                        $peoplemap = new Application_Model_PeopleMapper();
                        $person = $peoplemap->find($event->requesterid);
                        $sEmail = $person['email'];
                        $FROM = "calendarForm@cvuuf.org";
                        $sTO = $sEmail;

                        $TO_array = array($sTO, 'calendar@cvuuf.org');
                        $SUBJECT = 'Event #' . $EventID . ' Approved: ' . $eventName;
                        $TEXT = $msg;
                        $efunctions = new Cvuuf_emailfunctions();
                        $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, array('webmail@cvuuf.org' => "CVUUF"));
                        $log = $efunctions->log_email($this, 1, $totalsent, 0, 0);
                    }
                    else
                    {
                        $where = array(
                            array('id', ' = ', $EventID),
                            array('status', ' = ', 'R'),
                            );
                        $rejEvents = $entryusermap->fetchWhere($where);            
                        if (count($rejEvents) > 0)
                        {
                            $event->resultemail = $theDateTime;
                            $eventsmap->save($event);
                            $entrymap = new Application_Model_CalEntryMapper();
                            $entry = $entrymap->find($EventID);
                            $eventName = $entry['name']; 

                            $msg = "Your event request $EventID has been rejected.".
                              "<br><br>Display the event <a href='http://$serverName/webcal/view_entry.php?id=$EventID'> here</a>.";
                            $peoplemap = new Application_Model_PeopleMapper();
                            $person = $peoplemap->find($event->requesterid);
                            $sEmail = $person['email'];
                            $FROM = "calendarForm@cvuuf.org";
                            $sTO = $sEmail;

                            $TO_array = array($sTO, 'calendar@cvuuf.org');
                            $SUBJECT = 'Event #' . $EventID . ' Rejected: ' . $eventName;
                            $TEXT = $msg;
                            $efunctions = new Cvuuf_emailfunctions();
                            $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, array('webmail@cvuuf.org' => "CVUUF"));
                            $log = $efunctions->log_email($this, 1, $totalsent, 0, 0);
                        }
                        $where = array(
                            array('id', ' = ', $EventID),
                            array('status', ' = ', 'D'),
                            );
                        $delEvents = $entryusermap->fetchWhere($where);            
                        if (count($delEvents) > 0)
                        {
                            $event->resultemail = $theDateTime;
                            $eventsmap->save($event);
                            $entrymap = new Application_Model_CalEntryMapper();
                            $entry = $entrymap->find($EventID);
                            $eventName = $entry['name']; 

                            $msg = "Your event request $EventID has been deleted by the Scheduling Coordinator.".
                              "<br><br>Display the event <a href='http://$serverName/webcal/view_entry.php?id=$EventID'> here</a>.";
                            $peoplemap = new Application_Model_PeopleMapper();
                            $person = $peoplemap->find($event->requesterid);
                            $sEmail = $person['email'];
                            $FROM = "calendarForm@cvuuf.org";
                            $sTO = $sEmail;

                            $TO_array = array($sTO, 'calendar@cvuuf.org');
                            $SUBJECT = 'Event #' . $EventID . ' Deleted: ' . $eventName;
                            $TEXT = $msg;
                            $efunctions = new Cvuuf_emailfunctions();
                            $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, array('webmail@cvuuf.org' => "CVUUF"));
                            $log = $efunctions->log_email($this, 1, $totalsent, 0, 0);
                        }
                    }
                }          
            }
        }        





    public function init()
    {
    }

    public function indexAction()
    {
        $this->emails();
        $this->redirect('/webcal');
    }


    function validate_time($timehour, $timeminute) 
    {
        //echo "Begin validation of ", $timehour,' ', $timeminute, '<br>';
        if ($timeminute<10)
            $timeminute='0'.$timeminute;
        $timefield=$timehour.$timeminute;
        //echo "Begin validation of ", $timefield, '<br>';
        
        $hh = substr($timefield,0,2);
        if ($hh<8 || $hh>32) 
        {
        		$time_err="Error: Invalid hour.";
        		return $time_err;
      	}
      //echo "Valid time ", $timefield, '<br>';
      	return($timefield);
    }



    public function requestAction()
    {
        $this->view->theme = 'private';        /* Initialize action controller here */
        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth('');
        $this->auth = $auth;
        if (!isset($auth))
            $this->_redirect('/auth/index');
        $this->view->level = $auth->level;
        $functions = new Cvuuf_functions();
        $request = $this->getRequest();
        $getvalues = $request->getParams();
        if ($this->getRequest()->isPost())
        {
            $ValidErrors = '';
            $unixtime=mktime(date('G'),date('i'),date('s'), date('m'), date('d'),date('Y'));
            $theDateTime = date("Y-m-d G:i:s", $unixtime);
            $modDate = date('Y') . date('m') . date('d');
            $timeToGMT=0;
            $modTime = (date('G') + $timeToGMT) . date('i') . date('s');
            $userid = $this->auth->memberid;

            $submitid = $getvalues['submitid'];
            if ($userid <> $submitid)
            {
                $ValidErrors = "Unrecognized name<br>";
            }
		    
            if ($getvalues['ischange'] == 'new')
            {
                if ($getvalues['eventname'] == '')
                    $ValidErrors = $ValidErrors."Event title missing<br>";
                $eventname = $getvalues['eventname'];
                
                $eventdate = $getvalues['eventdate'];
                if ($eventdate == 'DD-Mon-YYYY')
                    $ValidErrors = $ValidErrors."Event date missing<br>";    
                $eventunix = strtotime($eventdate);
                $eventdate = date("Y-m-d", $eventunix);
                if ($eventdate == 'yyyy-mm-dd')
                    $ValidErrors = $ValidErrors."Date missing<br>";
                else
                {
                    $stdDate = $functions->date_validate($eventdate);
				            if (substr($stdDate,0,5) == "Error")
                        $ValidErrors = $ValidErrors."Invalid event date<br>";	
				            else 
                    {
              					$da = explode('-', $stdDate);
              					$codeDate = mktime(0,0,0, $da[1], $da[2],$da[0]);
              					$theDate = date("Ymd", $codeDate);
                        $dayOfWeek=strtoupper(substr(date('l', $codeDate), 0, 2));
				            }
                }
                $eventtimehour = $getvalues['eventtimehour'];
                $eventtimeminute = $getvalues['eventtimeminute'];
                $eventTime = $eventtimehour . ':' . $eventtimeminute;
                $eventendhour = $getvalues['eventendhour'];
                $eventendminute = $getvalues['eventendminute'];
                $eventEnd = $eventendhour . ':' . $eventendminute;
                $eventYear = substr($eventdate, 0, 4);
                $eventMonth = substr($eventdate, 5, 2);
                $eventDay = substr($eventdate, 8, 2);
                $eventTime = mktime($eventtimehour, 0, 0, $eventMonth, $eventDay, $eventYear);
                $isDST = date("I", $eventTime);
                $TimeToGMT = 8-$isDST;            
                $eventtimehour += $TimeToGMT;      
          			$EventTime = $this->validate_time($eventtimehour, $eventtimeminute);
          			if (substr($EventTime,0,5) == "Error")
          				  $ValidErrors = $ValidErrors."Event starting time format in error<br>";	
                $eventendhour += $TimeToGMT;			
          			$EndTime = $this->validate_time($eventendhour, $eventendminute);
          			if (substr($EndTime,0,5) == "Error")
          				$ValidErrors = $ValidErrors."Event ending time format in error<br>";
          //echo "Start: $EventTime End: $EndTime <br>";			
          //echo "Start: $eventTime End: $eventEnd <br>";			
          			if (strcmp($EventTime,$EndTime) > 0)
          				$ValidErrors = $ValidErrors."Event ending time not after starting time<br>";
                $minTime = $eventtimehour*60 + $eventtimeminute;
                $minEnd = $eventendhour*60 + $eventendminute;
                $calDuration = $minEnd - $minTime;
                $calTime = $EventTime . '00';
          			$eventdesc = $getvalues['eventdesc'];
                $ed = str_replace("\n", '\n', $eventdesc);
          			$ed	= str_replace("\r", '', $ed);
          			$etext = $ed . "\n\n";
			
                $doesrepeat = $getvalues['doesrepeat'];
                if ($doesrepeat <> 'no')
                {
                    $rep_freq = 1;
                    if ($doesrepeat == 'daily') 
                    {
                        $repeat = "Daily";
                        $rep_type = 'daily';
                    }
                    else 
                    {
                        $primaryweek = $getvalues['primaryweek'];
                        if ($primaryweek == "I1") 
                        {
                            $repeat = "WEEKLY";
                            $rep_type = "weekly";
                            $rep_byday = $dayOfWeek;
                        }
                        else 
                        {
                            if ($primaryweek[0] == 'I') 
                            {
                                $repeat = "WEEKLY;INTERVAL=" . $primaryweek[1];
                                $rep_type = "weekly";
                                $rep_freq = $primaryweek[1];
                            }
                            else 
                            {
                                $repeat = "MONTHLY;BYDAY=$primaryweek$dayOfWeek";
                                $rep_type = "monthlyByDay";
                                $rep_byday = $primaryweek . $dayOfWeek;
                                $secondweek = $getvalues['secondweek'];
                                if ($secondweek <> '') {
                                    $repeat .= ",$secondweek$dayOfWeek";
                                    $rep_byday .= ",$secondweek$dayOfWeek";
                                }
                            }
                        }              
                    }
                    $enddate = $getvalues['enddate'];
                    $endunix = strtotime($eventdate);
                    $enddate = date("Y-m-d", $eventunix);
                    if ($enddate<>'yyyy-mm-dd') 
                    {
                        $stdDate = $functions->date_validate($enddate);
            //echo 'Standardized date: ', $stdDate, '<br>';
            				    if (substr($stdDate,0,5)=="Error")
                          $ValidErrors = $ValidErrors."Invalid event date<br>";	
            				    else 
                        {
                          $da = explode('-', $stdDate);
                          $endDate = date("Ymd", mktime(0,0,0, $da[1], $da[2],$da[0]));
            //echo $da[1], '-', $da[2], '-', $da[0], ' = ',$theDate;
            				    }
                        if ($eventtimeminute < 10)
                            $eventtimeminute = '0' . $eventtimeminute;
                        $untilTime = $eventtimehour . ':' . $eventtimeminute;
                        $until = $endDate . "T" . $EventTime . "00Z";
                        $rep_end = $endDate;
                    }
                    else {
                        $rep_end = NULL;
                    }
                }
                $dtstart = $theDate . 'T' . $EventTime . '00Z';
                $dtend = $theDate . 'T' . $EndTime . '00Z';

                if (isset($getvalues['eventloc']))
                {              
                    $eventloc = $getvalues['eventloc'];
                    if ($eventloc == 'None' && $eventlocoutside == '')
                        $ValidErrors = $ValidErrors."Event with no room requires a location<br>";
                    $eventlocStr = $eventloc <> '' ? implode(",", $eventloc) : '';                    
                }
                else
                    $ValidErrors = $ValidErrors."Event has no location<br>";
                $eventlocoutside = $getvalues['eventlocoutside'];
                $eventgrp = $getvalues['eventgrp'];
                if ($eventgrp == 'None')
                    $ValidErrors = $ValidErrors."Event requires a Council Group<br>";
                $eventpub = $getvalues['eventpub'];
                $eventfood = $getvalues['eventfood'];
                if ($eventpub == '' || $eventfood == '')
                    $ValidErrors = $ValidErrors . "Both public and food options must be specified<br>";
            }   /* end of new event */
                
            else   /* change request */
            {
                if (!isset($getvalues['eventold']))
                    $ValidErrors = $ValidErrors . "Change request requires an event number<br>";
                else 
                {
                    $eventOld = $getvalues['eventold'];
                    $changeDesc = $getvalues['changedesc'];
                    $chText = "Event: $eventOld\nDesc: $changeDesc\n";

                    $peoplemap = new Application_Model_PeopleMapper();
                    $person = $peoplemap->find($userid);
                    $email = strtolower($getvalues['email']);
                    $first_name = strtolower($getvalues['first_name']);
                    $last_name = strtolower($getvalues['last_name']);
                    $serverName = $_SERVER["SERVER_NAME"];
                    $Event = "Event request is from " . $first_name . " " . $last_name . " at " . $email . "\n";
                    $message = $Event . "<br><br>Display event at http://$serverName/webcal/view_entry.php?id=$eventOld&user=__public__<br><br>".
                    $chText;
                    $FROM = "calendarForm@cvuuf.org";

                    $calgroupsmap = new Application_Model_CalGroupsMapper();
                    $where = array(
                        array('groupname', ' = ', 'Scheduler'),
                        );            
                    $scheduler = current($calgroupsmap->fetchWhere($where));
                    $positionsmap = new Application_Model_PositionsMapper();
                    $position = $positionsmap->find($scheduler->groupid);
                    $personid = $position['contact1'];
                    $peoplemap = new Application_Model_PeopleMapper();
                    $person = $peoplemap->find($personid);
                    $sEmail = $person['email'];
                    $TO_array=array($sEmail, 'calendar@cvuuf.org');

//echo "<br><br>";
//var_dump ($TO_array);

//$TO_array=array('michael.talvola@gmail.com', 'mike@talvola.com');

//echo "<br><br>";
//var_dump ($TO_array);

                    $SUBJECT = 'Event Change Request : #' . $eventOld;
                    $TEXT=$message;
                    $efunctions = new Cvuuf_emailfunctions();
//                    $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, $FROM);
//$TO_array = array('brian.pletcher@gmail.com','michael.talvola@gmail.com', 'webcrafter@cvuuf.org');

//echo "<br><br>";
//var_dump ($TO_array);

                    $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, array('webmail@cvuuf.org' => "CVUUF"));                        


                    $log = $efunctions->log_email($this, 1, $totalsent, 0, 0);
//var_dump($SUBJECT);
//var_dump ($TO_array);
//var_dump($TEXT);
//var_dump($FROM);
//exit;
                    $this->view->message = "Change submitted successfully for $eventOld.";
                    return $this->render('changeok');
                }  
            }  /* end of change processing */

//echo "<br>validity errors if any '$ValidErrors'<br>";

            if ($ValidErrors=='') 
            {
                $calentry = new Application_Model_CalEntry();
                $calentrymap = new Application_Model_CalEntryMapper();     
/* NOTE POTENTIAL RACE HERE WITH ANOTHER USER ACQUIRING MAX BEFORE SAVE IS DONE */
//                    $newID = $calentrymap->max() + 1;

                $times = 0;
                $success = false;
                do
                {
                    try
                    {
                        $newID = $calentrymap->max() + 1;
                        if ($newID > 10000)
                            $newID -= 10000;
                        $calentry->id = $newID;
                        $calentrymap->save($calentry);
                        $success = true;
                    } 
                    catch (Exception $e)
                    {
                        $msg = $e->getMessage();
                        $times++;
                    }
                    if ($success)
                        break;
                }
                while ($times < 3);
/* END OF RACE WINDOW */
                if ($times > 2)
                    $ValidErrors = "Error $msg adding calendar entry";            
                else
                {
                    $calentry->priority = 5;
                    $calentry->access = 'P';
                    $calentry->type = 'E';

                    $calgroupsmap = new Application_Model_CalGroupsMapper();
                    $eventgrp = $getvalues['eventgrp'];
                    $where = array(
                        array('groupname', ' = ', $eventgrp),
                        );            
                    $group = current($calgroupsmap->fetchWhere($where));
                    $positionsmap = new Application_Model_PositionsMapper();
                    $position = $positionsmap->find($group->groupid);
                    $personid = $position['contact1'];
                    $peoplemap = new Application_Model_PeopleMapper();
                    $grpperson = $peoplemap->find($personid);
    // **************************************        
    // SPECIAL CASE FOR 2011-12
                    if ($eventgrp=='Ministry')
                    {
            //echo "REPLACE WITH VP<br>";
                      $eventgrp="Minister";
                    }
    // SPECIAL CASE FOR 2011-12
    // **************************************        
                    elseif ($grpperson==0)
                    {
                      $eventgrp="Administrator";
                    }
    
                    $where = array(
                        array('groupname', ' = ', $eventgrp),
                        );            
                    $group = current($calgroupsmap->fetchWhere($where));
                    $aprNUC="_NUC_" . $group->groupcode . "_Appr";
    
               	    $modName = str_replace("'", "&#8217;", $eventname);
               	    $modText = str_replace("'", "&#8217;", $etext);
                    $calentry->createby = 'SOFTWARE';
                    $calentry->date = $theDate;
                    $calentry->time = $calTime;
                    $calentry->duration = $calDuration;
                    $calentry->name = $modName;
                    $calentry->description = $modText;
                    $calentry->location = $eventlocStr;
                    $calentry->moddate = $modDate;
                    $calentry->modtime = $modTime;
                    $calentrymap->save($calentry);
//echo "new event written <br>";
//var_dump($calentry); 
                    
                    $setuptime = $getvalues['setuptime'];
                    $breakdowntime = $getvalues['breakdowntime'];
                    $setupRequest=($setuptime>0 || $breakdowntime>0);
                    $entryusermap = new Application_Model_CalEntryUserMapper();

                    if ($setupRequest) 
                    {        
                        $roomTime = $eventtimehour * 60 + $eventtimeminute - $setuptime;
                        $roomHour = intval($roomTime / 60);
                        $roomMin = $roomTime - $roomHour * 60;
                        $roomDuration = $calDuration + $setuptime + $breakdowntime;
                        $roomStart = $this->validate_time($roomHour, $roomMin) . '00';
              //echo "calTime: $calTime  roomTime: $roomTime  setuptime: $setuptime  roomStart: $roomStart <br>";
              //echo "Room hour: $roomHour  <br>";                
                        $roomID = $newID + 1;  
                        $calentry = new Application_Model_CalEntry();
                        $calentry->id = $roomID;
                    // create the empty record
                        $calentrymap->save($calentry);
                        
                        $calentry->createby = 'SOFTWARE';
                        $calentry->date = $theDate;
                        $calentry->time = $roomStart;
                        $calentry->duration = $roomDuration;
                        $calentry->name = $eventname;
                        $calentry->description = $etext;
                        $calentry->location = $eventlocStr;
                        $calentry->moddate = $modDate;
                        $calentry->modtime = $modTime;
                        $calentrymap->save($calentry);
                    // write the filled record

                        $extText='<a href="view_entry.php?id=' . $roomID . '">yes</a>';

                        $extras = new Application_Model_CalSiteExtras();
                        $extrasmap = new Application_Model_CalSiteExtrasMapper();
                        $extras->id = $newID;
                        $extras->name = 'ExtraTime';
                        $extras->type = 1;
                        $extras->data = $extText;
                        $extrasmap->save($extras, 'new');
                        
                        $extText='for '.$newID;
                        $extras = new Application_Model_CalSiteExtras();
                        $extras->id = $roomID;
                        $extras->name = 'ExtraTime';
                        $extras->type = 1;
                        $extras->data = $extText;
                        $extrasmap->save($extras, 'new');
                    }  /* end setup request */

                    $entryuser = new Application_Model_CalEntryUser();
                    $entryuser->id = $newID;
                    $entryuser->login = $aprNUC;
                    $entryuser->status = 'W';
                    $entryusermap->save($entryuser, 'new');

                    $entryuser = new Application_Model_CalEntryUser();
                    $entryuser->id = $newID;
                    $entryuser->login = '__public__';
                    $entryuser->status = 'W';
                    $entryusermap->save($entryuser, 'new');
                    
                    if (isset($eventloc))
                    {
                        foreach ($eventloc as $room)
                        {
                            if ($room <> 'NO')
                            {
                                $roomsmap = new Application_Model_CalRoomsMapper();
                                $where = array(
                                    array('roomname', ' = ', $room),
                                    );            
                                $roomrow = current($roomsmap->fetchWhere($where));
                                $code = $roomrow->roomcode;
                                $bkNUC = "_NUC_" . $code . "_Book";
                                if ($setupRequest)
                                    $roomCode = $roomID;
                                else
                                    $roomCode = $newID;
                                $entryuser = new Application_Model_CalEntryUser();
                                $entryuser->id = $roomCode;
                                $entryuser->login = $bkNUC;
                                $entryuser->status = 'W';
                                $entryusermap->save($entryuser, 'new');
                            }
                        }

                        if ($doesrepeat <> 'no')
                        {
                            $rep_endTime = $EndTime . '00';
                            $repeatsmap = new Application_Model_CalEntryRepeatsMapper();
                            $repeat = new Application_Model_CalEntryRepeats();
                            $repeat->id = $newID;
                            $repeat->type = $rep_type;
                            $repeat->frequency = $rep_freq;
                            if (isset($rep_byday))
                                $repeat->byday = $rep_byday;
                            if (isset($rep_end))
                            {
                                $repeat->end = $rep_end;
                                $repeat->endtime = $rep_endTime;
                            }
                            $repeatsmap->save($repeat, 'new');
                        }

                        $extrasmap = new Application_Model_CalSiteExtrasMapper();
                        $extra = new Application_Model_CalSiteExtras();
                        $extra->id = $newID;
                        $extra->name = 'ID';
                        $extra->type = 1;
                        $extra->data = $newID;
                        $extrasmap->save($extra, 'new');
                        $eventcon = $getvalues['eventcon'];
                        $conField = $eventcon . $eventpub . $eventfood;
                        $extra = new Application_Model_CalSiteExtras();
                        $extra->id = $newID;
                        $extra->name = 'Contact';
                        $extra->type = 1;
                        $extra->data = $conField;
                        $extrasmap->save($extra, 'new');

                        $eventsmap = new Application_Model_CalEventsMapper();
                        $event = new Application_Model_CalEvents();
                        $event->eventid = $newID;
                        $event->requesterid = $userid;
                        $event->requesttime = $theDateTime;
                        $event->resultemail = '0000-00-00 00:00:00';
                        $eventsmap->save($event, 'new');
                        
                        $dbcoms = addslashes($getvalues['eventcoms']);
                        $commentsmap = new Application_Model_CalCommentsMapper();
                        $comment = new Application_Model_CalComments();
                        $comment->id = $newID;
                        $comment->comments = $dbcoms;
                        $commentsmap->save($comment, 'new');
                    }
                    else
                        $Event = '';
                }
            }

            if ($ValidErrors <> '')
            {
                $this->view->message = $ValidErrors;
            }
            else
            {
                $positionsmap = new Application_Model_PositionsMapper();
                $groupsmap = new Application_Model_CalGroupsMapper(); 
                if ($eventloc <> '') 
                {
                    $where = array(
                        array('groupname', ' = ', 'Scheduler'),
                        );            
                    $group = current($groupsmap->fetchWhere($where));
                    $groupid = $group->id;
                    $position = $positionsmap->find($groupid);
                }
                else
                {
                    $where = array(
                        array('title', ' = ', 'WebCrafter'),
                        );            
                    $position = current($positionsmap->fetchWhere($where));
                }
                $personid = $position['contact1']; 
                $peoplemap = new Application_Model_PeopleMapper();
                $person = $peoplemap->find($personid);
                $sEmail = $person['email'];

                $FROM = "calendarForm@cvuuf.org";
                $sTO = $sEmail;
                
                $where = array(
                    array('groupname', ' = ', $eventgrp),
                    );            
                $group = current($groupsmap->fetchWhere($where));
                $groupid = $group->groupid;
                $position = $positionsmap->find($groupid);
                $personid = $position['contact1']; 
                $peoplemap = new Application_Model_PeopleMapper();
                $person = $peoplemap->find($personid);
//var_dump($person); exit;
                $gTO = $person['email'];
                $gName = $person['firstname'];
                $email = strtolower($getvalues['email']);
                $first_name = strtolower($getvalues['first_name']);
                $last_name = strtolower($getvalues['last_name']);
                $serverName = $_SERVER["SERVER_NAME"];
                $eventcoms = $getvalues['eventcoms'];
  
                $TO_array = array($sTO, $gTO, $email, 'calendar@cvuuf.org');
//var_dump($TO_array); exit;
              	$SUBJECT = 'Event Request "' . $eventname . '" for ' . $eventgrp . ' approval';
                $TEXT = "Hello $gName<br><br>A Calendar event request was submitted by $first_name $last_name at $email. This"; 
                $TEXT .= " Event needs to be approved or rejected by you as $eventgrp within"; 
                $TEXT .= " 48 hours. If rejected, please contact the requester, $first_name $last_name (before rejecting ";
                $TEXT .= "the request) with an explanation for the rejection. If not rejected or approved in 48 ";
                $TEXT .= "hours, the request will be forwarded to the Vice President for approval or rejection.";
              
                $TEXT .= "<br><br>Approve or Reject the request <a href='http://$serverName/calendar/process?calid=$newID&user=$aprNUC'> here</a>.";
              
                $TEXT .= "<br><br><br>Thank you for your service to our fellowship,<br>The CVUUF Calendar Crew";
                $TEXT .= "<br><br>Send any questions regarding the technical aspects of this request to calendar@cvuuf.org";
                
                $TEXT .= "<br><br>Comments from requester:<br>" . $eventcoms;
                
                $efunctions = new Cvuuf_emailfunctions();
//                $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, $FROM);
                    $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, array('webmail@cvuuf.org' => "CVUUF"));                        

                $log = $efunctions->log_email($this, 1, $totalsent, 0, 0);
                
                $this->view->message = "Event $newID '$eventname' has been entered.";
            }   

        }
        $personid = $this->auth->memberid;
        $peoplemap = new Application_Model_PeopleMapper();
        $person = $peoplemap->find($personid);
        $this->view->firstname = $person['firstname'];
        $this->view->lastname = $person['lastname'];
        $this->view->email = $person['email'];
        $this->view->id = $person['id'];
        $groupsmap = new Application_Model_CalGroupsMapper(); 
        $where = array(
            array('groupname', ' <> ', 'Scheduler'),
            array('groupname', ' <> ', 'Education'),
            array('groupname', ' <> ', 'Fellowship'),
            array('groupname', ' <> ', 'Ministry'),
            array('groupname', ' <> ', 'CouncilClerk'),
            
            array('groupname', ' <> ', ''),
            );            
        $groups = $groupsmap->fetchWhere($where);
        $groupnames = array();
        foreach ($groups as $group) {
            $groupnames[] = $group->groupname;
        }      
        $this->view->groupnames = $groupnames;

        $roomsmap = new Application_Model_CalRoomsMapper(); 
        $roomsinfo = $roomsmap->fetchAll();
        $rooms = array();
        foreach ($roomsinfo as $roomrow) {
            $rooms[] = $roomrow->roomname;
        }      
        $this->view->rooms = $rooms;


        $this->view->style = 'zform.css';

    }


    public function approvalsAction()
    {
        $this->view->theme = 'private';        /* Initialize action controller here */
        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth('');
        $this->auth = $auth;
        if (!isset($auth))
            $this->_redirect('/auth/index');
        $this->view->level = $auth->level;
    
        $serverName = $_SERVER["SERVER_NAME"];
        $userid = $this->auth->memberid;
        $entryusermap = new Application_Model_CalEntryUserMapper();                
        $where = array(
            array('login', ' <> ', 'SCHEDULE'),
            array('status', ' = ', 'W'),
            );
        $waits = $entryusermap->fetchColumn($where, array('cal_id'), true);  /* select distinct */
//var_dump($waits);
        $table = array();
        $row = array();          
        $roomlist = array();
        if (count($waits) <> 0)
            foreach($waits as $waiting)
            {
                $books = ''; 
                $rooms = array(); 
                $roomCount = 0;
                $grNUC = '';
                $public = '';
                $theApprover = '';
                $scheduler = '';
                $calID = $waiting;
                $extrasmap = new Application_Model_CalSiteExtrasMapper(); 
                $where = array(
                    array('id', ' = ', $calID),
                    array('name', ' = ', 'ExtraTime'),
                    );            
                $extras = current($extrasmap->fetchWhere($where));
//var_dump($extras);
//echo "ID IS $calID <br>";
                if ($extras <> false)
                    $setup = $extras->data;
                else
                    $setup = '';
//echo "SETUP is $setup <br>";
                if (substr($setup, 0, 3) <> 'for') 
                {
                    $where = array(
                        array('id', ' = ', $calID),
                        array('status', ' = ', 'W'),
                        );
//echo "CAL ID is $calID <br>";
                    if ($setup <> '')
                    {
//echo "SETUP: '$setup' <br>";
                        $locateID = substr($setup, strpos($setup, 'id=') + 3);
                        $setupID=substr($locateID, 0, strpos($locateID, '"'));
//echo "725  LOCATEID '$locateID'  SETUPID $setupID <br>";

                        // NOTE: following is a workaround for bad link info
                        if ($setupID <> '')
                            $where = array(
                                "OR",
                                array('id', ' = ', $calID),
                                array('id', ' = ', $setupID),
                                array('status', ' = ', 'W'),
                                );
//echo "WHERE inside setup <br>";
//var_dump($where); 
     
//echo "SETUP ID $setupID <br>";
//echo "SELECT ID $calID <br>";
                    }
//echo "WHERE outside setup <br>";
//var_dump($where); 
//echo "SELECTID FOLLOWS <br>";
                    $allCals = $entryusermap->fetchWhere($where, array('cal_id'), true);  /* select distinct */
//if ($calID == 1528) exit;
                    $rejected = '';
                    foreach ($allCals as $thisCal)
                    {
if ($calID == 9999)
{
echo "<b>749</b>";
var_dump($thisCal);
}
                        $where = array(
                            array('id', ' = ', $calID),
                            array('status', ' = ', 'R'),
                            );
                        $rejCals = $entryusermap->fetchWhere($where, array('cal_id'), true);  /* select distinct */
                        if (count($rejCals) > 0)
                        {
//echo "REJECTED FOUND <br>";
                            $rejected = 'yes';
                            $where = array(
                                array('id', ' = ', $calID),
                                );
                            $rejects = $entryusermap->fetchWhere($where);
                            foreach ($rejects as $reject)
                            {
                                $reject->status = 'R';
                                $entryusermap->save($reject);
                            }
                           
                        }
                        else
                        {
//echo "NOT REJECTED <br>";
//var_dump($thisCal);
                            $theCal = $thisCal->login;
//echo "THECAL is $theCal WITH ID $calID <br>";
//var_dump($thisCal);
                            if (substr($theCal, 8, 1) == 'A')
                                $grNUC = $theCal;
                            if ($theCal == '__public__')
                                $public = 'w';
//echo "ID $calID  GRNUC $grNUC <br>";
                            $accessmap = new Application_Model_CalAccessUserMapper();
                            $where = array(
                                array('otheruser', ' = ', $theCal),
                                array('login', ' <> ', 'admin'),
                                array('login', ' <> ', '__public__'),
                                array('login', ' <> ', 'SCHEDULE'),
                                array('login', ' <> ', 'BILL'),
                                );
                            $access = $accessmap->fetchWhere($where);
                            if (count($access) > 0)
                            {
//echo "839  <b>ACCESS FOUND</b> for $calID <br>";
//var_dump($access);
                                $approver = current($access)->login;
                                $theApprover = $approver;
                            }
                            
                            $where = array(
                                array('otheruser', ' = ', $theCal),
                                array('login', ' = ', 'SCHEDULE'),
                                );
                            $access = $accessmap->fetchWhere($where);
//var_dump($access);
//echo count($access), " SCHEDULE access <br>";
                            if (count($access) > 0)
                            {
//var_dump($access); 
                                $scheduler = current($access)->login;
//echo "SCHEDULER $scheduler <br>";                            
                                if (substr($theCal, 8, 1) <> 'A' && substr($theCal, 8, 1 )<> '_' && substr($theCal, 8, 1 )<> 'o') 
                                {
                                    $code = substr($theCal, 5, 2);
                                      $rooms[$roomCount++] = $code;
                                      $books .= $code.' ';
                                }
                            } 
if ($calID == 9999)
{
echo "CODE IS $code  BOOKS IS $books <br>";

}
                            $entrymap = new Application_Model_CalEntryMapper();
                            $entry = $entrymap->find($calID);
                            $evname = $entry['name'];
//echo "823 EVNAME $evname <br>";                            
                            $eventsmap = new Application_Model_CalEventsMapper();
                            $where = array(
                                array('eventid', ' = ', $waiting),
                                );
                            $event = $eventsmap->fetchWhere($where);
if ($calID == 9999)
{
var_dump($event);

}
                            if (count($event) > 0)
                            {
                                $theRequester = current($event)->requesterid;
                                $peoplemap = new Application_Model_PeopleMapper();
                                $person = $peoplemap->find($theRequester);
                                $reqName = $person['firstname'] . ' ' . $person['lastname'];
                            }
                            else {
                                $reqName = 'unknown';
                                $theRequester = 0;
                            }    
                        
                        }
                    }        
//echo "++++++++++ Event:$calID Appr:$theApprover for $grNUC Sched:$books Req:$reqName $theRequester $desc<br>";        
                    
                    $apprURL = "http://$serverName/calendar/process?calid=" . $waiting . "&user=" . $grNUC;
                    $eventURL = "http://$serverName/webcal/view_entry.php?id=" . $waiting . "&user=__public__";
if ($calID == 9999)
{
echo "APPR $apprURL  EVENT $eventURL <br>";
exit;
}
                
                    if ($theRequester == $userid) 
                        $reqName = '<b>' . $reqName . '</b>';
//echo "Approver: $theApprover<br>";
                    
                    if (($theApprover <> '' || $scheduler <> '') && $rejected <> 'yes') 
                    {
                        if ($theApprover<>'')
                        {
                            $where = array(
                                array ('groupname', ' = ', $theApprover),
                                );
                            $groupsmap = new Application_Model_CalGroupsMapper(); 
                            $group = $groupsmap->fetchWhere($where);
                            $groupid = current($group)->groupid;
                            
                            $positionsmap = new Application_Model_PositionsMapper();
                            $position = $positionsmap->find($groupid);
                            $personid = $position['contact1'];
                            
                            $peoplemap = new Application_Model_PeopleMapper();
                            $person = $peoplemap->find($personid);
                            $id = $person['id'];
                            $name = $person['firstname'] . ' ' . $person['lastname'];
                            
                            if ($id == $userid) 
                                $aprName='<b>'.$name.'</b>';
                            else
                                $aprName = $name;
                        }
                        else
                            $aprName = '';

                        unset($row);
                        $row['id'] = $waiting;
                        $row['evurl'] = $eventURL;
                        $row['reqname'] = $reqName;
                        $row['evname'] = $evname;
                        $row['aprname'] = $aprName;
                        $row['aprurl'] = $apprURL;
                        $row['approver'] = $theApprover;

                        unset($roomlist);
                        foreach($rooms as $room)
                        {
                            if ($setup)
                                $roomID = $setupID;
                            else
                              $roomID = $waiting;
                            $roomURL = "http://$serverName/webcal/view_entry.php?id=" . $roomID . "&user=_NUC_" . $room . "_Book";
                            $roominfo = array($roomURL, $room);
                            $roomlist[] = $roominfo;
                        }
                        
                        $row['rooms'] = $roomlist;
                        $row['public'] = $public;
                        
                        $commentsmap = new Application_Model_CalCommentsMapper();
                        $where = array(
                          array('id', ' = ', $calID),
                          );
                        $comments = $commentsmap->fetchWhere($where);
                        if (count($comments) > 0)
                        {
                            $calComments = current($comments)->comments;
                            if ($calComments <> '')
                                $row['comments'] = $calComments;
                            else
                                $row['comments'] = '';
                        }

                    $table[] = $row;
                    }
                }
            }
            
   
        $this->view->data = $table;
    }


    
    
    
    
    public function processAction()
    {
        $this->view->theme = 'private';        /* Initialize action controller here */
        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth('');
        $this->auth = $auth;
        if (!isset($auth))
            $this->_redirect('/auth/index');
        $this->view->level = $auth->level;
        $id = $auth->memberid;
        $this->view->done = '';

        $request = $this->getRequest();
        $getvalues = $request->getParams();
        if ($this->getRequest()->isGet())
        {
            $calid = $getvalues['calid'];
            $this->view->calid = $calid;
            $user = $getvalues['user'];
            $this->view->user = $user;
            $code = substr($user, 5, 2);
            $calgroupsmap = new Application_Model_CalGroupsMapper();
            $where = array(
                array('groupcode', ' = ', $code),
                );            
            $group = current($calgroupsmap->fetchWhere($where));
            $name = $group->groupname;
            $this->view->group = $name;
            $groupid = $group->groupid;
            $positionsmap = new Application_Model_PositionsMapper();
            $position = $positionsmap->find($groupid);
            $contact1 = $position['contact1'];
            if ($contact1 <> $id)
            {
//var_dump($contact1);
//var_dump($id);
//var_dump($name);

                $this->view->message = "You are not authorized to approve for $name.";
                $this->view->done = 'done';
            }
            else
                $this->view->message = "You are approving for $name.";
        }
        
        if ($this->getRequest()->isPost())
        {
            $calid = $getvalues['calid'];
            $this->view->calid = $calid;
            $user = $getvalues['user'];
            $this->view->user = $user;
            $code = substr($user, 5, 2);
            $calgroupsmap = new Application_Model_CalGroupsMapper();
            $where = array(
                array('groupcode', ' = ', $code),
                );            
            $group = current($calgroupsmap->fetchWhere($where));
            $name = $group->groupname;
            $this->view->group = $name;
            $calid = $getvalues['calid'];
            $this->view->calid = $calid;
            $user = $getvalues['user'];
            $this->view->user = $user;
            
            if (isset($getvalues['approvebutton']))
            {
                $calmap = new Application_Model_CalEntryUserMapper();
                $where = array(
                    array('login', ' = ', $user),
                    array('id', ' = ', $calid),
                    );
                $cal = current($calmap->fetchWhere($where));
                if ($cal->status <> 'W')
                {
                    $this->view->message = "Event status is not 'Waiting.'";
                }
                else
                {
                    $cal->status = 'A';
                    $calmap->save($cal);
                    $this->view->message = "Event approved for $name.";
                    $this->view->done = 'done';
                }
            }           
            
            if (isset($getvalues['rejectbutton']))
            {
                if ($getvalues['reason'] == '')
                {
                    $this->view->message = "Reject requires a reason.";
                }
                else
                {
                    $calmap = new Application_Model_CalEntryUserMapper();
                    $where = array(
                        array('login', ' = ', $user),
                        array('id', ' = ', $calid),
                        );
                    $cals = $calmap->fetchWhere($where);
                    foreach ($cals as $cal)
                    {
                        $cal->status = 'R';
                        $calmap->save($cal);
                    }
                    $this->view->message = "Event rejected by $name.";
                    $this->view->done = 'done';
                }
            
            }           
        
        }


        $this->view->style = 'zform.css';
    
    }


    public function testAction()
    {
        $entryusermap = new Application_Model_CalEntryUserMapper();                
        $where = array(
            "OR",
            array('login', ' <> ', 'SCHEDULE'),
            array('status', ' = ', 'W'),
            );
        $waits = $entryusermap->fetchWhere($where);    

    }

    
    
}

