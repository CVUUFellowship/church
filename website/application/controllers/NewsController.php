<?php

class NewsController extends Zend_Controller_Action
{


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
        
        
        public function Ymd()
        {
            $unixtime=mktime(date('G'),date('i'),date('s'), date('m'), date('d'),date('Y'));
            return (date("Ymd", $unixtime));
        }
        
        
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
//array('lastname', ' = ', 'talvola'),
                array('email', ' <> ', ''),
                array('creationdate', ' > ', $TwoYrs),
                array('status', ' IN ', "($types)"),
                );
            $inactivepeeps = $peoplemap->fetchWhere($where);
            $peeps = array_merge($activepeeps, $inactivepeeps);
            return $peeps;
        }
        
        
        
        public function showannouncement($anc)
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
                $linkText = isset($anc->linktext) ? $anc->linktext : "Web address";
                $row['link'] = " <b>Link:</b> <a href='$anc->link'> $linkText </a>";
            }
            else
                $row['link'] = '';
                
            $row['desc'] = $this->showTrans($anc->description);              
            $row['id'] = $anc->id;
            $row['title'] = $anc->title;
            
            return $row;
        }


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




    public function init()
    {
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


    public function announcereqAction()
    {
        $functions = new Cvuuf_functions();
        $ancmap = new Application_Model_AnnouncementsMapper();
        $this->view->message = array();

        $request = $this->getRequest();    
        if ($this->getRequest()->isPost())    
        {
            $formData = $request->getParams();
            if (isset($formData))
            {
//var_dump($formData); exit;
                    $anc = new Application_Model_Announcements();
                    if ($formData['anmtname'] == '')
                        $this->view->message[] = "Announcement title missing.";
                    else
                        $anc->title = filter_var($formData['anmtname'], FILTER_SANITIZE_STRING);
                        
                    $xdate = $formData['anmtdate'];
//var_dump($xdate);
                    if ($xdate == 'DD-Mon-YYYY')
                        $this->view->message[] = "Announcement expiration date missing.";
                    else
                    {
                        if (!is_int($xdate[3]))
                            $xdate = $functions->caltomysql($xdate);
                        $stddate = $functions->date_validate($xdate);
                        if ((substr($stddate, 0, 5 )== "Error"))
                            $this->view->message[] = "Invalid expiration date.";
                        else
                        {
                  					$da = explode('-', $stddate);
                  					$codedate = mktime(0,0,0, $da[1], $da[2], $da[0]);
                  					$anc->xdate = date("Ymd", $codedate);
                            if ($anc->xdate < $this->Ymd())
                                $this->view->message[] = "Expiration date $stddate is not in the future.";
//var_dump($anc->xdate);
//var_dump($this->Ymd());
                        }    
                    }
                    
                    $edate = $formData['eventdate'];
                    if ($edate == 'DD-Mon-YYYY')
                        $anc->date = $anc->xdate;
                    else
                    {
                        if (!is_int($edate[3]))
                            $edate = $functions->caltomysql($edate);
                        $stddate = $functions->date_validate($edate);
                        if ((substr($stddate, 0, 5 )== "Error"))
                            $this->view->message[] = "Invalid event date.";
                        else
                        {
                  					$da = explode('-', $stddate);
                  					$codedate = mktime(0,0,0, $da[1], $da[2], $da[0]);
                  					$anc->date = date("Ymd", $codedate);
                            if ($anc->date < $this->Ymd())
                                $this->view->message[] = "Event date $stddate is not in the future.";
                        }    
                    }
                    
                    $hour = $formData['eventtimehour'];
                    $min = $formData['eventtimeminute'];
                    $annTime = $hour  . ':' . $min;
              			$AnnTime = $functions->validate_time($hour, $min);
              			if (substr($AnnTime,0,5) == "Error")
              			     $this->view->message[] = "Time format in error.";
                    $anc->time = $AnnTime;

                    $anc->description = filter_var($formData['anmtdesc'], FILTER_SANITIZE_STRING);

                    if ($formData['anmtloc'] =='')
                        $this->view->message[] = "Location is required.";
                    $anc->place = filter_var($formData['anmtloc'], FILTER_SANITIZE_STRING);
                    
                    $anc->contact = filter_var($formData['anmtcon'], FILTER_SANITIZE_STRING);
                    
                    $link = $formData['anmtlink'];
                    if ($link <> '')
                    {
                        if (strtolower(substr($link, 0, 7)) <> 'http://')
                            $link = 'http://' . $link;
                        if (filter_var($link, FILTER_VALIDATE_URL) === false)
                            $this->view->message[] = "Invalid link.";
                    }
                    $anc->link = $link;
                    $anc->linktext = filter_var($formData['anmtlinktext'], FILTER_SANITIZE_STRING);
                    $anc->place = filter_var($formData['anmtloc'], FILTER_SANITIZE_STRING);
                    
                    $anc->owner = $formData['submitid'];
                    $anc->type = 'CVUUF';
                    $anc->status = 'entered';
                    if (count($this->view->message) == 0)
                    {
                        $ancmap->save($anc);
                        $id = $anc->id;
                        $this->view->message[] = "Announcement $id created.";
//var_dump($anc); exit;
                        $ancarray = $this->showannouncement($anc);
                        $efunctions = new Cvuuf_emailfunctions();
                        $TEXT = "New announcement $id entered.";
                        $totalsent = $efunctions->sendEmail('New CVUUF Announcement', array('brian.pletcher@gmail.com','michael.talvola@gmail.com'), $TEXT, $this, array('webmail@cvuuf.org' => "CVUUF"));                        
                        $log = $efunctions->log_email($this, 1, $totalsent, 0, 0);
                        $this->view->anc = $ancarray;
                        $this->view->theme = 'private';
                        return $this->render('announcecreated');
                    }
            }
            
        }
        if (count($this->view->message) == 0)
            unset($this->view->message);

        $this->view->theme = 'private';
        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth('');
        $this->auth = $auth;
        $personid = $this->auth->memberid;
        $peoplemap = new Application_Model_PeopleMapper();
        $person = $peoplemap->find($personid);
        $this->view->firstname = $person['firstname'];
        $this->view->lastname = $person['lastname'];
        $this->view->email = $person['email'];
        $this->view->id = $person['id'];

        $this->view->style = 'zform.css';
    }
    


    public function announceapprAction()
    {
        $request = $this->getRequest();
        $anmtmap = new Application_Model_AnnouncementsMapper();
        $getvalues = $request->getParams();

        if ($this->getRequest()->isGet())
        {
            if (isset($getvalues))
            {
                if (isset($getvalues['id']))
                    $id = $getvalues['id'];
                else
                    $message = "No announcement ID.";
            }
            
            elseif (isset($postvalues))
            {
            
            }
        }

        $this->view->theme = 'data';
        $this->view->style = 'zform.css';
    }
    
    


    public function announcemaintAction()
    {
        $functions = new Cvuuf_functions();
        $ancmap = new Application_Model_AnnouncementsMapper();

        $request = $this->getRequest();    
        if ($this->getRequest()->isPost())    
        {
            $postvalues = $request->getParams();
            if (isset($postvalues))
            {
                $todo = $postvalues['todo'];
                if ($todo == 'STORE')
                {
                    $aid = $postvalues['aid'];
                    $where = array(
                        array('id', ' = ', $aid),
                          );
                    $anc = current($ancmap->fetchWhere($where));
                    $formData = $postvalues;
                    $this->view->message = array();

                    if ($formData['anmtname'] == '')
                        $this->view->message[] = "Announcement title missing.";
                    else
                        $anc->title = filter_var($formData['anmtname'], FILTER_SANITIZE_STRING);
                        
                    $xdate = $formData['anmtdate'];
                    if ($xdate == 'DD-Mon-YYYY' || $xdate == '')
                        $this->view->message[] = "Announcement expiration date missing.";
                    else
                    {
                        if (!is_int($xdate[3]))
                            $xdate = $functions->caltomysql($xdate);
                        $stddate = $functions->date_validate($xdate);
                        if ((substr($stddate, 0, 5 )== "Error"))
                            $this->view->message[] = "Invalid expiration date.";
                        else
                        {
                  					$da = explode('-', $stddate);
                  					$codedate = mktime(0,0,0, $da[1], $da[2], $da[0]);
                  					$anc->xdate = date("Ymd", $codedate);
                        }    
                    }
                    
                    $edate = $formData['eventdate'];
                    if ($edate == 'DD-Mon-YYYY')
                        $anc->date = $anc->xdate;
                    else
                    {
                        if (!is_int($edate[3]))
                            $edate = $functions->caltomysql($edate);
                        $stddate = $functions->date_validate($edate);
                        if ((substr($stddate, 0, 5 )== "Error"))
                            $this->view->message[] = "Invalid event date.";
                        else
                        {
                  					$da = explode('-', $stddate);
                  					$codedate = mktime(0,0,0, $da[1], $da[2], $da[0]);
                  					$anc->date = date("Ymd", $codedate);
                        }    
                    }
                    
                    $hour = $formData['eventtimehour'];
                    $min = $formData['eventtimeminute'];
                    $annTime = $hour  . ':' . $min;
              			$AnnTime = $functions->validate_time($hour, $min);
              			if (substr($AnnTime,0,5) == "Error")
              			     $this->view->message[] = "Time format in error.";
                    $anc->time = $AnnTime;

                    $anc->description = filter_var($formData['anmtdesc'], FILTER_SANITIZE_STRING);

                    if ($formData['anmtloc'] =='')
                        $this->view->message[] = "Location is required.";
                    $anc->place = filter_var($formData['anmtloc'], FILTER_SANITIZE_STRING);
                    
                    $anc->contact = filter_var($formData['anmtcon'], FILTER_SANITIZE_STRING);
                    
                    $link = $formData['anmtlink'];
                    if ($link <> '')
                    {
                        if (strtolower(substr($link, 0, 7)) <> 'http://')
                            $link = 'http://' . $link;
                        if (filter_var($link, FILTER_VALIDATE_URL) === false)
                            $this->view->message[] = "Invalid link.";
                    }
                    $anc->link = $link;
                    $anc->linktext = filter_var($formData['anmtlinktext'], FILTER_SANITIZE_STRING);
                    $anc->place = filter_var($formData['anmtloc'], FILTER_SANITIZE_STRING);
                    
                    if (count($this->view->message) > 0)
                    {
                        $this->view->message[] = '';
                        $this->view->message[] = "Please go back, correct form data, and resubmit.";
                    }
                    else
                    {
                        $ancmap->save($anc);
                        unset($this->view->message);
                        $this->view->message = "Announcement $aid stored.";
                    }
                }
                else
                {
                    if (!isset($postvalues['id']))
                        $this->view->message = "No announcement selected.";
                    else
                    {
                        $ids = $postvalues['id'];
                        foreach ($ids as $id)
                        {
                            $where = array(
                                array('id', ' = ', $id),
                                  );
                            $anc = current($ancmap->fetchWhere($where));
                            
                            if ($todo == 'SHOW')
                            {
                                $this->view->theme = 'data';
                                $row = array();
    
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
                                
                                $this->view->anc = $row;
                                $this->view->style = 'zform.css';
    
                                return $this->render('announceshow');
    
    
                            }
                            if ($todo == 'EDIT')
                            {
                                $old = array();
                                $old['id'] = $id;                
                                $old['title'] = filter_var($anc->title, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                $oldDate = $anc->date;
                                    $old['date'] = substr($oldDate, 0, 4).'-'.substr($oldDate,4, 2).'-'.substr($oldDate, 6, 2);
                                $oldExdate = $anc->xdate;
                                    $old['xdate'] = substr($oldExdate, 0, 4).'-'.substr($oldExdate,4, 2).'-'.substr($oldExdate, 6, 2);
                                $oldTime = $anc->time;
                                    $oldColon = strpos($oldTime, ':');
                                    $old['hour'] = substr($oldTime, 0, $oldColon);
                                    $old['min'] = substr($oldTime, $oldColon + 1, 2);
                                    $old['pm'] = substr($oldTime, $oldColon + 3, 1);
                                $old['con'] = $anc->contact;
                                $old['loc'] = filter_var($anc->place, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                $old['link'] = $anc->link;
                                $old['linktext'] = filter_var($anc->linktext, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                $old['desc'] = filter_var($anc->description, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                
                                $this->view->old = $old;
                                $this->view->theme = 'data';
                                $this->view->style = 'zform.css';
                                return $this->render('announceedit');
                            }
                            
                            
                            if ($todo == 'DELETE')
                            {
                              $anc->status = 'deleted';
                              $ancmap->save($anc);
                              $this->view->message = "Announcement $id marked deleted.";
                            }
                            
                            if ($todo == 'APPROVE')
                            {
                              $anc->status = 'approved';
                              $ancmap->save($anc);
                              $this->view->message = "Announcement $id marked approved.";
                            }
                            
                            if ($todo == 'REJECT')
                            {
                              $anc->status = 'rejected';
                              $ancmap->save($anc);
                              $this->view->message = "Announcement $id marked rejected.";
                            }
                        }
                    }
                }
            }
        }

        $today = date("Ymd");
        $where = array(
            array('xdate', ' > ', $today),
            );            
        $announcements = $ancmap->fetchWhere($where);
        $this->view->announcements = $announcements;            

        $this->view->theme = 'data';
        $this->view->style = 'zform.css';
    }
    

    


    public function announceemailAction()
    {
$SEND='YES';
        $functions = new Cvuuf_functions();
        $efunctions = new Cvuuf_emailfunctions();
        $ancmap = new Application_Model_AnnouncementsMapper();
        $peoplemap = new Application_Model_PeopleMapper();
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
                        
                    $unsubText="<br><br><small>To unsubscribe from special announcements DO NOT REPLY, please click ";
                    $unsubText=$unsubText."<a href='mailto:unsubscribe@cvuuf.org?subject=individual'>here.</a><br>";
                    $unsubText=$unsubText."Or, to unsubscribe from ALL CVUUF announcements, please click ";
                    $unsubText=$unsubText."<a href='mailto:unsubscribe@cvuuf.org?subject=all'>here.</a></small><br>";
                    $etext = $postvalues['etext'] . $unsubText;    

                    $results = array();
                    $peeps = $this->fullList($peoplemap, 'plus');
                    $SUBJECT = 'Conejo Valley UU Fellowship Announcements';
                    $result = $efunctions->sendBatchEmail($peeps, 'weekly', $SUBJECT, $etext, $this, array('webmail@cvuuf.org' => "CVUUF"));
                    
                    $log = $result['log'];
                    $this->view->sent = $log->sentcount;
                    $this->view->invalid = $result['invalid'];
                    $this->view->unsub = $result['unsub'];
                    $this->view->results = $result['results'];
                    $this->view->theme = 'data';
                    return $this->render('announcesent');         

                }
            }
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
        
            $etext .= "\n<p><b>" . $row['date'] . $row['time'] . " </b><span style=\"font-size:50%;\"> [$anc->id]</span>";
            $etext .= "<br><blockquote><b>";
            $etext .= "\n" . $anc->title . "</b> - " . $row['contact'] . $row['location'] . $row['link'];
            $etext .= "\n<br>" . $row['desc'] . "</blockquote><br><hr>";
        }
        

//echo $etext;        
//var_dump($etext); exit;

        $this->view->etext = $etext;        
        $this->view->data = $data;
        $this->view->theme = 'data';
        $this->view->style = 'zform.css';
        
        
    }

    


    public function bulkemailAction()
    {
$SEND='YES';
        $functions = new Cvuuf_functions();
        $efunctions = new Cvuuf_emailfunctions();
        $unsmap = new Application_Model_UnsubMapper();

        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth('');
        $this->auth = $auth;
        $personid = $this->auth->memberid;
        $peoplemap = new Application_Model_PeopleMapper();
        $person = $peoplemap->find($personid);
        $replyto = $person['email'];
        $this->view->email = $replyto;

        $request = $this->getRequest();
        if ($this->getRequest()->isPost())    
        {
            $postvalues = $request->getParams();
            if (isset($postvalues))
            {         
                if ($postvalues['doit'] == 'SUBMIT')
                {
                    $this->view->message = array();
                    $FROM = filter_var($postvalues['FROM'], FILTER_SANITIZE_EMAIL);
                    if (filter_var($FROM, FILTER_VALIDATE_EMAIL) === false)
                        $this->view->message[] = "Invalid From: email address.";
                    
                    $SUBJECT = $postvalues['SUBJECT'];
//                    $SUBJECT = filter_var($SUBJECT, FILTER_SANITIZE_STRING);
                    if ($SUBJECT == '')
                        $this->view->message[] = "Subject is missing.";
                    $unsubText="<br><br><small>To unsubscribe from special announcements DO NOT REPLY, please click ";
                    $unsubText=$unsubText."<a href='mailto:unsubscribe@cvuuf.org?subject=individual&body=~'>here.</a><br>";
                    $unsubText=$unsubText."Or, to unsubscribe from ALL CVUUF announcements, please click ";
                    $unsubText=$unsubText."<a href='mailto:unsubscribe@cvuuf.org?subject=all&body=~'>here.</a></small><br>";
                    $unsubText=$unsubText."</body>";
                    $unsubText=$unsubText."</html>";
                    
                    $thebody = $postvalues['TEXT'];
                    $bodyend = strpos($thebody, '</body>');
                    $thebodypart = substr($thebody, 0, $bodyend);
                    $TEXT = $thebodypart . $unsubText;    

                    $peeps = array();
                    $TO = $postvalues['TO'];
                    switch ($TO)
                    {
                      case 'ALL':
                          $peeps = $this->fullList($peoplemap, 'plus');
                          break;
                          
                      case 'CURRENT':
                          $peeps = $this->fullList($peoplemap);
                          break;
                          
                      case 'MEMBERS':
                          $where = array(
                              array('status', ' = ', 'Member'),
                              array('email', ' <> ', ''),
                              array('inactive', ' <> ', 'yes'),
                                );
                          break;
                          
                      case 'VISITORS':
                          $where = array(
                              array('status', ' = ', 'Visitor'),
                              array('email', ' <> ', ''),
                              array('inactive', ' <> ', 'yes'),
                                );
                          break;
                          
                      case 'RE':
                          $remap = new Application_Model_REMapper();
                          $where = array(
                              array('inactive', ' <> ', 'yes'),
                                );
                          $regs = $remap->fetchWhere($where);
                          $peeps = array();
                          foreach ($regs as $reg)
                          {
                              if ($reg->apersonid <> 0)
                              {
                                  $where = array(
                                      array('email', ' <> ', ''),
                                      array('id', ' = ', $reg->apersonid),
                                        );
                                  $peep = current($peoplemap->fetchWhere($where));
                                  if ($peep <> false)
                                      $peeps[] = $peep;
                              }
                              if ($reg->ppersonid <> 0)
                              {
                                  $where = array(
                                      array('email', ' <> ', ''),
                                      array('id', ' = ', $reg->ppersonid),
                                        );
                                  $peep = current($peoplemap->fetchWhere($where));
                                  if ($peep <> false)
                                      $peeps[] = $peep;
                              }
                          }      
                          break;
                          
                      case 'TEST':
                          $testids =  "'1500', '1085'";
                          $where = array(
                              array('id', ' IN ', "($testids)"),
                                );
                          break;
                    }
                    
                    if (count($peeps) == 0)
                    {
                        $peeps = $peoplemap->fetchWhere($where);
                    }
                    $emailCount = count($peeps);            
                    if (count($this->view->message) > 0)
                    {
                        $this->view->message[] = '';
                        $this->view->message[] = "Please go back, correct form data, and resubmit.";
                    }

                    else 
                    {
                        $result = $efunctions->sendBatchEmail($peeps, 'individual', $SUBJECT, $TEXT, $this, array('webmail@cvuuf.org' => "CVUUF"), $replyto);

                        $log = $result['log'];
                        $this->view->sent = $log->sentcount;
                        $this->view->invalid = $result['invalid'];
                        $this->view->unsub = $result['unsub'];
                        $this->view->results = $result['results'];
                        $this->view->theme = 'data';
                        return $this->render('bulkemailsent');         
                    }
                }
            }
        }
         
        $this->view->theme = 'data';
        $this->view->style = 'zform.css';
    }



    public function hoodemailAction()
    {

        
        function prepHood(Application_Form_NewsHoodSelect $hoodForm) 
        {
            $hoodsmap = new Application_Model_HoodsMapper();
            $hoods = $hoodsmap->fetchAll();
            $names = array();
            foreach ($hoods as $hood)
            {
                $names[$hood->id] = $hood->hoodname;
            }
            $hoodForm->hood->addMultiOptions(array('')); 
            $hoodForm->hood->addMultiOptions($names);
            $hoodForm->hood->setValue($names); 
            $options = count($hoods);
            $hoodForm->hood->setAttrib('size', $options);
        }

        $functions = new Cvuuf_functions();
        $efunctions = new Cvuuf_emailfunctions();
        $unsmap = new Application_Model_UnsubMapper();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $postvalues = $request->getParams();
//var_dump($postvalues);

            if (isset($postvalues['hood']))
            {
                $this->view->message = array();
                $FROM = filter_var($postvalues['FROM'], FILTER_SANITIZE_EMAIL);
                if (filter_var($FROM, FILTER_VALIDATE_EMAIL) === false)
                    $this->view->message[] = "Invalid From: email address.";
//var_dump($FROM);                
                $SUBJECT = $postvalues['SUBJECT'];
                $SUBJECT = filter_var($SUBJECT, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                if ($SUBJECT == '')
                    $this->view->message[] = "Subject is missing.";
//var_dump($postvalues['TEXT']);                     
                $unsubText="<br><br><small>To unsubscribe from neighborhood announcements DO NOT REPLY, please click ";
                $unsubText=$unsubText."<a href='mailto:unsubscribe@cvuuf.org?subject=neighborhood&body=~'>here.</a><br>";
                $unsubText=$unsubText."Or, to unsubscribe from ALL CVUUF announcements, please click ";
                $unsubText=$unsubText."<a href='mailto:unsubscribe@cvuuf.org?subject=all&body=~'>here.</a></small><br>";
                $unsubText=$unsubText."</body>";
                $unsubText=$unsubText."</html>";
                
                $thebody = $postvalues['TEXT'];
                $bodyend = strpos($thebody, '</body>');
                $thebodypart = substr($thebody, 0, $bodyend);
//var_dump($thebodypart);
                $TEXT = $thebodypart . $unsubText;    
//var_dump($TEXT); exit;
                $peeps = array();
                $peoplemap = new Application_Model_PeopleMapper();
                $hoodsmap = new Application_Model_NeighborhoodsMapper();
                $hoodresult = $postvalues['hood'];
                $hoodid = $hoodresult;
                $where = array(
                    array('active', ' = ', 'yes'),
                    array('hoodid', ' = ', $hoodid),
                    );            
                $housesinhood = $hoodsmap->fetchWhere($where);
                foreach ($housesinhood as $house)
                {
                    $where = array(
                        array('inactive', ' = ', 'no'),
                        array('email', ' <> ', ''),
                        array('status', ' IN ', "('Member', 'NewFriend', 'Affiliate')"),
                        array('householdid', ' = ', $house->householdid),
                        );
                    $housepeeps = $peoplemap->fetchWhere($where);
                    foreach ($housepeeps as $peep)
                    {
                        $peeps[] = $peep;
                    }
                }
                
                $emailCount = count($peeps);            
//echo "COUNT IS $emailCount <br>"; 
                if (count($this->view->message) > 0)
                {
                    $this->view->message[] = '';
                    $this->view->message[] = "Please go back, correct form data, and resubmit.";
                }

                else 
                {    

                        $hoodmap = new Application_Model_HoodsMapper();
                        $hood = $hoodmap->find($hoodid);
                        $hoodname = $hood['hoodname'];
                        $result = $efunctions->sendBatchEmail($peeps, 'neighborhood', $SUBJECT, $TEXT, $this, array('webmail@cvuuf.org' => $hoodname), $FROM);

                        $log = $result['log'];
                        $this->view->sent = $log->sentcount;
                        $this->view->invalid = $result['invalid'];
                        $this->view->unsub = $result['unsub'];
                        $this->view->results = $result['results'];
                        $this->view->theme = 'data';

                        return $this->render('hoodemailsent');         
                }
            }
        }


        $this->view->style = 'zform.css';
        $this->view->theme = 'data';
    }
    

    public function listemailAction()
    {

        $listnames = array();
        $emaillistsmap = new Application_Model_EmailListsMapper();
        $elrows = $emaillistsmap->fetchAll();
        foreach($elrows as $row)
        {
            $listnames[] = strtolower($row->listname);
        }
        $listnames = array_unique($listnames);
        $namecount = count($listnames);        
        $request = $this->getRequest();
        $postvalues = $request->getParams();
//var_dump($postvalues);
        if ($this->getRequest()->isPost())
        {
            if (isset($postvalues['submitbutton']))
            {
                $personid = $this->auth->memberid;
                $peoplemap = new Application_Model_PeopleMapper();
                $person = $peoplemap->find($personid);
                $replyto = $person['email'];
                $this->view->email = $replyto;
//echo "Submit<br>"; 
//exit;
                $this->view->message = array();
//var_dump($postvalues);
//exit;
                if (isset($postvalues['list']))
                {
                    $listname = $postvalues['list'];
                    $where = array(
                        array('listname', ' = ', $listname),
                          );
                    $listemails = $emaillistsmap->fetchWhere($where);
                    $emails = array();
                    foreach($listemails as $row)
                    {
                        $emails[] = $row->email;
                    }
    
                    $this->view->message = array();
                    $FROM = filter_var($postvalues['FROM'], FILTER_SANITIZE_EMAIL);
                    if (filter_var($FROM, FILTER_VALIDATE_EMAIL) === false)
                        $this->view->message[] = "Invalid From: email address.";
    //var_dump($FROM);                
                    $SUBJECT = $postvalues['SUBJECT'];
                    $SUBJECT = filter_var($SUBJECT, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    if ($SUBJECT == '')
                        $this->view->message[] = "Subject is missing.";
    //var_dump($postvalues['TEXT']);                     
                    $thebody = $postvalues['TEXT'];
                    $bodyend = strpos($thebody, '</body>');
                    $thebodypart = substr($thebody, 0, $bodyend);
    //var_dump($thebodypart);
                    $TEXT = $thebodypart."</body></html>";    
    //var_dump($TEXT); exit;
                    
                    $emailCount = count($emails);            
    //echo "COUNT IS $emailCount <br>"; 
                    if (count($this->view->message) > 0)
                    {
                        $this->view->message[] = '';
                        $this->view->message[] = "Please go back, correct form data, and resubmit.";
                    }
    
                    else 
                    {    
//var_dump($emails); 
//exit;                        
                        $efunctions = new Cvuuf_emailfunctions();
                        $totalsent = $efunctions->sendEmail($SUBJECT, $emails, $TEXT, $this, $FROM = array('webmail@cvuuf.org' => "CVUUF $listname"));
    
                        $this->view->theme = 'data';
    
                        return $this->render('listemailsent');         
                    }
                }
                else
                {
                        $this->view->message[] = '';
                        $this->view->message[] = "Can't send, no list selected.  Go back and select a list.";
                }   
                
            }
            
            elseif (isset($postvalues['showbutton']))
            {
//echo "Show<br>"; 
//exit;
                if (isset($postvalues['list']))
                {
                    $listname = $postvalues['list'];
                    $where = array(
                        array('listname', ' = ', $listname),
                          );
                    $emails = $emaillistsmap->fetchWhere($where);
                    if (count($emails) > 0)
                    {
                        $names = array();
                        $peoplemap = new Application_Model_PeopleMapper();
                        foreach ($emails as $email)
                        {
                            $where = array(
                                array('email', ' = ', $email->email),
                            );
                            $who = $peoplemap->fetchWhere($where);
                            if (count($who) > 0)
                                $person = $who[0]->firstname . ' ' . $who[0]->lastname;
                            else
                                $person = "";
                            $names[] = $person;
                        }
 
                        $this->view->names = $names;
                        $this->view->emails = $emails;
                        $this->view->list = $listname;
                        $this->view->style = 'zform.css';
                        $this->view->theme = 'data';

                        return($this->render('listemailsshow'));
                    }
                    else
                        $this->view->message = "List $listname is empty.";
                }
                else
                    $this->view->message = "Need to select a list name.";
            }
            
            elseif (isset($postvalues['editbutton']))
            {
//echo "Edit<br>"; exit;
                if (isset($postvalues['list']))
                {
                    $listname = $postvalues['list'];
                    $where = array(
                        array('listname', ' = ', $listname),
                          );
                    $lists = $emaillistsmap->fetchWhere($where);
                    if (count($lists) > 0)
                    {
                        $names = array();
                        $emails = array();
                        $peoplemap = new Application_Model_PeopleMapper();
                        foreach ($lists as $email)
                        {
                            $emails[] = $email->email;
                            $where = array(
                                array('email', ' = ', $email->email),
                            );
                            $who = $peoplemap->fetchWhere($where);
                            if (count($who) > 0)
                                $person = $who[0]->firstname . ' ' . $who[0]->lastname;
                            else
                                $person = "";
                            $names[] = $person;
                        }
 
                        $this->view->names = $names;
                        $this->view->emails = $emails;
                        $this->view->list = $listname;
                        $this->view->style = 'zform.css';
                        $this->view->theme = 'data';

                        return($this->render('listemailsedit'));
                    }
                    else
                        $this->view->message = "List $listname is empty.";
                }
                else
                    $this->view->message = "Need to select a list name.";
                

            }
            
            elseif (isset($postvalues['createbutton']))
            {
//echo "Create<br>"; 
                if ($postvalues['newname'] <> '')
                {
                    $newname = $postvalues['newname'];
                    if ($namecount > 0)
                    {

                        if (in_array($newname, $listnames))
                        {
                            $this->view->message = "Name is already used.";
                        }
                        else
                        {
                            $listentry = new Application_Model_EmailLists();
                            $listentry->listname = $newname;
                            $email = $postvalues['newemail'];
                            $efunctions = new Cvuuf_emailfunctions();
                            if (($email <> "") && $efunctions->isValidEmail($email))
                            {
                                $listentry->email = $email;
                                $emaillistsmap->save($listentry);
                                $this->view->message = "New list $newname created with email $listentry->email.";
                            }
                            else
                                $this->view->message = "Need a valid email address to create a list.";
                        }
                    }
                    
                }
                else
                    $this->view->message = "Need a name for a new list.";
                    
            }
        }


        $listnames = array();
        $emaillistsmap = new Application_Model_EmailListsMapper();
        $elrows = $emaillistsmap->fetchAll();
        foreach($elrows as $row)
        {
            $listnames[] = $row->listname;
        }
        $listnames = array_unique($listnames);
        $namecount = count($listnames);        

        $personid = $this->auth->memberid;
        $peoplemap = new Application_Model_PeopleMapper();
        $person = $peoplemap->find($personid);
        $this->view->email = $person['email'];

        $this->view->listnames = $listnames;    

        $this->view->style = 'zform.css';
        $this->view->theme = 'data';
    }



    public function listemailseditAction()
    {

        $emaillistsmap = new Application_Model_EmailListsMapper();

        $request = $this->getRequest();
        $postvalues = $request->getParams();
        $oldnames = explode(",", $postvalues['oldnames']);
        $oldlist = explode(",", $postvalues['oldlist']);
        
//var_dump($postvalues); 
//exit;
        if ($this->getRequest()->isPost())
        {
            $editlist = $postvalues['listname'];
            if (isset($postvalues['deletebutton']))
            {
                if ($postvalues['number'] <> "")
                {
                    $delemail = $oldlist[$postvalues['number'] - 1];
                    $where = array(
                        array('email', ' = ', $delemail),
                        array('listname', ' = ', $editlist),
                    );
                    $delentry = current($emaillistsmap->fetchWhere($where));
                    $delid = $delentry->id;
                    $emaillistsmap->delete($delid);

                    $emails = array();
                    $names = array();
                    for ($i=0; $i<count($oldlist); $i++)
                    {
                        if ($delemail <> $oldlist[$i])
                        {
                            $emails[] = $oldlist[$i];
                            $names[] = $oldnames[$i];
                        }
                    }
                    $oldlist = $emails;
                    $oldnames = $names;

                    
                }
                else
                    $this->view->message = "Need a number to delete.";
            }
            
            elseif (isset($postvalues['addbutton']))
            {
//echo "Add"; 
//exit;            
                if ($postvalues['newemail'] <> "")
                {
                    $listsrow = new Application_Model_EmailLists();
                    $efunctions = new Cvuuf_emailfunctions();
                    $newemail = $postvalues['newemail'];
                	  if (!$efunctions->isValidEmail($newemail))
                    { 
                        $this->view->message = "$newemail is not a valid email address.";
                    }
                    else
                    {
                        $listsrow->email = $newemail;
                        $listsrow->listname = $editlist;
                        $emaillistsmap->save($listsrow);
    
                        $peoplemap = new Application_Model_PeopleMapper();
                        $where = array(
                            array('email', ' = ', $newemail),
                            );
                        $who = $peoplemap->fetchWhere($where);
                        if (count($who) > 0)
                            $person = $who[0]->firstname . ' ' . $who[0]->lastname;
                        else
                            $person = "";
                        $newname = $person;
                        $this->view->message = "Added $newemail [$newname] to list.";
                        $oldnames[] = $newname;
                        $oldlist[] = $newemail;
                                        
                    }
                }
                else
                    $this->view->message = "Need an email to add.";
            }
            
            elseif (isset($postvalues['removebutton']))
            {
//echo "Remove"; exit;
                $where = array(
                    array('listname', ' = ', $editlist),
                );
                $all = $emaillistsmap->fetchWhere($where);
                if (count($all) > 0)
                {
                    foreach ($all as $one)
                    {
                        $id = $one->id;
                        $emaillistsmap->delete($id);
                    }
                }
                $count = count($all);
                $this->view->message = "Deleted $count emails. List $editlist removed.";                            
                $editlist = "";
                $oldnames = array();
                $oldlist = array();
            }

        }

        $this->view->list = $editlist;
        $this->view->names = $oldnames;
        $this->view->emails = $oldlist;
        $this->view->style = 'zform.css';
        $this->view->theme = 'data';
    }
        
        
    


    public function unsubscribeAction()
    {
        $functions = new Cvuuf_functions();
        $efunctions = new Cvuuf_emailfunctions();
        $peoplemap = new Application_Model_PeopleMapper();
        $unsubmap = new Application_Model_UnsubMapper();
        $unsublogmap = new Application_Model_UnsubLogMapper();
        $request = $this->getRequest();
        $postvalues = $request->getParams();
        if ($this->getRequest()->isPost())
        {
            if (isset($postvalues['submitbutton']))
            {
                $row = new Application_Model_Unsub();
                $row->all =
                $row->weekly = 
                $row->newsletter = 
                $row->neighborhood = 
                $row->individual = 0;
                $pid = $postvalues['unsubid'];
                $row->id = $pid;
                if (isset($postvalues['boxall']))
                    $row->all = 1;
                if (isset($postvalues['boxweek']))
                    $row->weekly = 1;
                if (isset($postvalues['boxnews']))
                    $row->newsletter = 1;
                if (isset($postvalues['boxhood']))
                    $row->neighborhood = 1;
                if (isset($postvalues['boxbulk']))
                    $row->individual = 1;
                $unsub = $unsubmap->find($pid);
                if ($unsub['id'] > 0)
                    $unsubmap->save($row);
                else
                    $unsubmap->save($row, 'new');
                $postvalues['unsubbutton'] = 'SUBMIT';
            }     
            if (isset($postvalues['unsubbutton']))
            {
                if ($postvalues['unsubid'] > 0)
                    $pid = $postvalues['unsubid'];
                elseif ($postvalues['unsubname'] <> '')
                {
                    $name = $postvalues['unsubname'];
                    $pid = $this->idFromName($name);
                }
    
                if (!isset($pid))
                {
                    $this->view->message = "Cannot process UNSUB request without ID# or Name.";
                }
                else
                {
                    $unsub = $unsubmap->find($pid);
                    $row = array();
                    $peep = $peoplemap->find($pid);
                    $row['firstname'] = $peep['firstname'];
                    $row['lastname'] = $peep['lastname'];
                    if ($unsub['id'] > 0)
                    {
                        $row['id'] = $unsub['id'];
                        $row['all'] = $unsub['all'];
                        $row['week'] = $unsub['weekly'];
                        $row['news'] = $unsub['newsletter'];
                        $row['hood'] = $unsub['neighborhood'];
                        $row['bulk'] = $unsub['individual'];
                        $row['when'] = $unsub['timestamp'];
                    }
                    else
                    {
                        $row['id'] = $pid;
                        $row['all'] = 
                        $row['week'] = 
                        $row['news'] = 
                        $row['hood'] = 
                        $row['bulk'] = 0;
                        $row['when'] = 'none';
                    }    
                    $this->view->unsub = $row;
                    $this->view->style = 'zform.css';
                    $this->view->theme = 'data';
                    return $this->render('unsubone');
                }
            }
            
        }


        $unsubs = $unsubmap->fetchAll();
//var_dump($unsubs); 
        $row = array();
        $peeps = array();
        foreach($unsubs as $unsub)
        {
            if ($unsub->id <> 9999)
            {
                unset($row);
                $peep = $peoplemap->find($unsub->id);
                $row['firstname'] = $peep['firstname'];
                $row['lastname'] = $peep['lastname'];
                $row['id'] = $unsub->id;
                $row['all'] = $unsub->all;
                $row['week'] = $unsub->weekly;
                $row['news'] = $unsub->newsletter;
                $row['hood'] = $unsub->neighborhood;
                $row['bulk'] = $unsub->individual;
                $peeps[] = $row;
            }
        }
        function cmpName($a,$b){
            return strcmp($a['lastname'].$a['firstname'], $b['lastname'].$b['firstname']);
        }
        uasort($peeps, 'cmpName'); 
//var_dump($peeps); exit;
        
        $this->view->unsubs = $peeps;        

        $this->view->style = 'zform.css';
        $this->view->theme = 'data';
    }
    

    


   
}

