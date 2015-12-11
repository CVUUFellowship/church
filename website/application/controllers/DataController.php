<?php

class DataController extends Zend_Controller_Action
{

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

      
      public function today()
      {
          return(date("Y-m-d"));    
      }
      
      
      public function getHood(array $house)
      {
          $functions = new Cvuuf_functions();
          $hoodzips = $functions->hoodZips();
          $thezip = $house['zip'];

        	$neighborhood=0;
        	foreach ($hoodzips as $ziprange) {
        		if ($ziprange[1] <= $thezip) {
        			if ($ziprange[2] >= $thezip) {
        				$neighborhood = $ziprange[0];
        				break;
        			}
        		}	
        	}
          return ($neighborhood);
      }


      
      
      public function getClasses($type)
      {
          $headmap = new Application_Model_HeadingsMapper();
          $where = array(
              array('type', ' = ', $type),
              );            
          $headings = $headmap->fetchWhere($where);
          $classes = array();
          $classes[0] = '';
          foreach ($headings as $heading)
              $classes[$heading->id] = $heading->heading;
          return ($classes);
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


    public function visitorsentryAction()
    {
        $this->view->message = array();
        $efunctions = new Cvuuf_emailfunctions();
        $functions = new Cvuuf_functions();
        $entryForm = new Application_Form_DataVisitorEntry();
        $request = $this->getRequest();
        $this->view->explain = '';
        $styleChange = 'font-weight:bold; color:green;';
        $styleError = 'font-style:italic; font-weight:bold; color:darkred;';
        $initFormData = array('date' => $this->lastSunday(), 'hid' => '', 
          'street' => '', 'city' => '', 'state' => 'CA', 'zip' => '', 'area' => '805', 'phone' => '',  
          'name' => '', 'email' => '', 'comments' => '', 'prioruu' => 'no', 
          'name2' => '', 'email2' => '', 'comments2' => '', 'prioruu2' => 'no', 
          'refer' => 'none');
    
        if ($this->getRequest()->isPost())
        {
            $formData = $request->getParams();

    /* enter button */
            if (isset($formData['ebutton']))
            {
                $this->view->explain = "Labels of changed fields are <span style='$styleChange'> bold green</span>; 
                  labels of fields with errors detected are <span style='$styleError'> bold italic red</span>.";
                foreach ($initFormData as $key => $value) 
                {
                    if ($formData[$key] <> $value)
                    {
                        $entryForm->getElement($key)->getDecorator('label')->setOption('style', $styleChange);
                    }
                }
                $name = $formData['name'];
                $name2 = $formData['name2'];

                $pfunctions = new Cvuuf_personfunctions();

                if ($name <> '' || $name2 <> '')
                {
                    $n = explode(' ', $name);
                    if ($name2 <> '')
                    {
                        $n2 = explode(' ', $name2);
                        $names = $pfunctions->fillFindNames($n2[0], $n2[1], "'private'");
                        if (count($names) <> 0)
                        {
                                $entryForm->getElement('name')->getDecorator('label')->setOption('style', $styleError);
                        }
                        if ($n[1] == $n2[1])
                            $householdname = $n[0] . ' & ' . $n2[0] . ' ' . $n[1];
                        else
                            $householdname = $n[0] . ' ' . $n[1] . ' & ' . $n2[0] . ' ' . $n2[1];
                    }
                    else 
                    {


                        $names = $pfunctions->fillFindNames($n[0], $n[1], "private");
                        if (count($names) <> 0)
                        {
                                $entryForm->getElement('name')->getDecorator('label')->setOption('style', $styleError);
                        }
                        $householdname = $n[0] . ' ' . $n[1];
                    }    
                    $entryForm->getElement('household')->getDecorator('label')->setOption('style', $styleChange);
                    $formData['household'] = $householdname;
                }
                if ($formData['phone'] == '')
                    $formData['area'] = '';

                $e = $formData['email'];
                if ($e <> '')
                {
                    if ($efunctions->isValidEmail($e))
                        $entryForm->getElement('email')->getDecorator('label')->setOption('style', $styleChange);
                    else
                        $entryForm->getElement('email')->getDecorator('label')->setOption('style', $styleError);
                }

                $e = $formData['email2'];
                if ($e <> '')
                {
                    if ($efunctions->isValidEmail($e))
                        $entryForm->getElement('email2')->getDecorator('label')->setOption('style', $styleChange);
                    else
                        $entryForm->getElement('email2')->getDecorator('label')->setOption('style', $styleError);
                }

                $zip = $formData['zip'];
                if ($zip <> '')
                {
                    if ($functions->is_zip($zip))
                        $entryForm->getElement('zip')->getDecorator('label')->setOption('style', $styleChange);
                    else
                        $entryForm->getElement('zip')->getDecorator('label')->setOption('style', $styleError);
                    
                }

                $state = $formData['state'];
                if ($state <> '')
                {
                    if ($functions->is_state($state))
                        $entryForm->getElement('state')->getDecorator('label')->setOption('style', $styleChange);
                    else
                        $entryForm->getElement('state')->getDecorator('label')->setOption('style', $styleError);
                    
                }
               
                $entryForm->populate($formData);

            }


    /* clear button */
            if (isset($formData['xbutton']))
            {
                unset($formData);
            }


    /* commit button */

            if (isset($formData['cbutton']))
            {
                if (!isset($formData['household']))
                    $this->view->message = 'No names entered, cannot do anything.';
                else
                {
                    $householdname = $formData['household'];
                    if ($householdname <> '')
                    {
                        $householdid = $formData['hid'];
                        if ($householdid == 0)
                        {
                            $housemap = new Application_Model_HouseholdsMapper();
                            $house = new Application_Model_Households();
                            $house->creationdate = $formData['date'];
                            $house->householdname = $householdname;
                            $house->street = $formData['street'];
                            $house->city = $formData['city'];
                            $house->state = $formData['state'];
                            $house->zip = $formData['zip'];
                            $house->phone = $formData['area'] . $formData['phone'];
                            $housemap->save($house);
                            $this->view->message[] = "Household $house->id inserted.";
                            $householdid = $house->id;
                        }
                        
                        $thedate = $formData['date'];
                        $peoplemap = new Application_Model_PeopleMapper();
                        $visitorsmap = new Application_Model_VisitorsMapper();
                        $visitsmap = new Application_Model_VisitsMapper();

                        
                    /* first visitor */
                        $person = new Application_Model_People();
                        $person->creationdate = $formData['date'];
                      	$n = explode(' ', $formData['name']);
                      	if (isset($n[0])) $firstname = $n[0];
                            else $firstname = '';
                      	if (isset($n[1])) $lastname = $n[1];
                            else $lastname = '';
                        $person->firstname = $firstname;
                        $person->lastname = $lastname;
                        $person->householdid = $householdid;
                        $person->status = 'Visitor';
                        $person->email = $formData['email'];
                        $peoplemap->save($person);
                        $this->view->message[] = "Person $person->id inserted as a Visitor.";

                        $visitor = new Application_Model_Visitors();
                        $visitor->id = $person->id;
                        $visitor->reference = $formData['refer'];
                        $visitor->comment = $formData['comments'];
                        $visitor->prioruu = $formData['prioruu'];
                        $visitorsmap->save($visitor, 'new');
                        $this->view->message[] = "Inserted visitor record for $person->id.";

                        $visit = new Application_Model_Visits();
                        $visit->personid = $person->id;
                        $visit->visitdate = $thedate;
                        $visitsmap->save($visit, 'new');
                        $this->view->message[] = "Inserted visits record for $person->id on $thedate.";

                    /* second visitor */
                        if ($formData['name2'] <> '')
                        {
                            $person = new Application_Model_People();
                            $person->creationdate = $formData['date'];
                          	$n = explode(' ', $formData['name2']);
                          	if (isset($n[0])) $firstname = $n[0];
                                else $firstname = '';
                          	if (isset($n[1])) $lastname = $n[1];
                                else $lastname = '';
                            $person->firstname = $firstname;
                            $person->lastname = $lastname;
                            $person->householdid = $householdid;
                            $person->status = 'Visitor';
                            $person->email = $formData['email2'];
                            $peoplemap->save($person);
                            $this->view->message[] = "Person $person->id inserted as a Visitor.";
                            
                            $visitorsmap = new Application_Model_VisitorsMapper();
                            $visitor = new Application_Model_Visitors();
                            $visitor->id = $person->id;
                            $visitor->reference = $formData['refer'];
                            $visitor->comment = $formData['comments2'];
                            $visitor->prioruu = $formData['prioruu2'];
                            $visitorsmap->save($visitor, 'new');
                            $this->view->message[] = "Inserted visitor record for $person->id.";

                            $visit = new Application_Model_Visits();
                            $visit->id = $person->id;
                            $visit->visitdate = $thedate;
                            $visitsmap->save($visit, 'new');
                            $this->view->message[] = "Inserted visits record for $person->id on $thedate.";
                        }
                        unset($formData);
                    }                
                }
            }
        }

        if (!isset($formData))
        {
            $entryForm->populate($initFormData);
            $entryForm->household->setAttrib('disabled', true);
        }
               
        $this->view->style = 'zform.css';
        $this->view->entryForm = $entryForm;
    }





    public function personentryAction()
    {
        $this->view->message = array();
        $efunctions = new Cvuuf_emailfunctions();
        $functions = new Cvuuf_functions();
        $entryForm = new Application_Form_DataPersonEntry();
        $request = $this->getRequest();
        $this->view->explain = '';
        $styleChange = 'font-weight:bold; color:green;';
        $styleError = 'font-style:italic; font-weight:bold; color:darkred;';
        $initFormData = array('date' => $this->lastSunday(), 'hid' => '', 
          'street' => '', 'city' => '', 'state' => 'CA', 'zip' => '', 'area' => '805', 'phone' => '',  
          'name' => '', 'email' => '', 'comments' => '', 
          'status' => '');
    
        if ($this->getRequest()->isPost())
        {
            $formData = $request->getParams();

    /* enter button */
            if (isset($formData['ebutton']))
            {
                $name = $formData['name'];
                if ($name == '')
                    $this->view->message = 'No name entered, cannot do anything.';
                else
                {
                    $this->view->explain = "Labels of changed fields are <span style='$styleChange'> bold green</span>; 
                      labels of fields with errors detected are <span style='$styleError'> bold italic red</span>.";
                    foreach ($initFormData as $key => $value) 
                    {
                        if ($formData[$key] <> $value)
                        {
                            $entryForm->getElement($key)->getDecorator('label')->setOption('style', $styleChange);
                        }
                    }
    
                    $status = $formData['status'];
                    if ($status <> '')
                        $entryForm->getElement('status')->getDecorator('label')->setOption('style', $styleChange);
                    else
                        $entryForm->getElement('status')->getDecorator('label')->setOption('style', $styleError);
                    if ($status == 'Visitor')
                    {
                        $this->view->message = 'Cannot enter a new Visitor here, use "Enter visitors" option instead.';
                        $entryForm->getElement('status')->getDecorator('label')->setOption('style', $styleError);
                    }
    
                    if ($formData['hid'] == '')
                    {
                        $entryForm->getElement('household')->getDecorator('label')->setOption('style', $styleChange);
                        $formData['household'] = $name;
                    }
                }
                $entryForm->populate($formData);
            }
            
    /* commit button */
            if (isset($formData['cbutton']))
            {
                if (!isset($formData['name']))
                    $this->view->message = 'No name entered, cannot do anything.';
                else
                {
                    $householdid = $formData['hid'];
                    if ($householdid == '')
                    {
                        $housemap = new Application_Model_HouseholdsMapper();
                        $house = new Application_Model_Households();
                        $house->creationdate = $formData['date'];
                        $house->householdname = $householdname;
                        $house->street = $formData['street'];
                        $house->city = $formData['city'];
                        $house->state = $formData['state'];
                        $house->zip = $formData['zip'];
                        $house->phone = $formData['area'] . $formData['phone'];
                        $housemap->save($house);
                        $this->view->message[] = "Household $house->id inserted.";
                        $householdid = $house->id;
                    }
    
                    $peoplemap = new Application_Model_PeopleMapper();
                    $person = new Application_Model_People();
                    $person->creationdate = $formData['date'];
                  	$n = explode(' ', $formData['name']);
                  	if (isset($n[0])) $firstname = $n[0];
                        else $firstname = '';
                  	if (isset($n[1])) $lastname = $n[1];
                        else $lastname = '';
                    $person->firstname = $firstname;
                    $person->lastname = $lastname;
                    $person->householdid = $householdid;
                    $person->status = $formData['status'];
                    $person->email = $formData['email'];
                    $peoplemap->save($person);
                    $this->view->message[] = "Person $person->id inserted as a $person->status.";


                }
                        

                $entryForm->populate($formData);
            }


    /* clear button */
            if (isset($formData['xbutton']))
            {
                unset($formData);
            }


            
        }  /* end of form data items */
    

        if (!isset($formData))
        {
            $entryForm->populate($initFormData);
            $entryForm->household->setAttrib('disabled', true);
        }
        $values = $functions->statusValues();
        $entryForm->status->addMultiOptions($values);
               
        $this->view->style = 'zform.css';
        $this->view->entryForm = $entryForm;
    
    }   



    public function personchangeAction()
    {
        $personid = 0;
        $this->view->message = array();
        $efunctions = new Cvuuf_emailfunctions();
        $functions = new Cvuuf_functions();
        $changeForm = new Application_Form_DataPersonChange();
        $request = $this->getRequest();
        $getvalues = $request->getParams();
        $this->view->explain = '';
        $styleChange = 'font-weight:bold; color:green;';
        $styleError = 'font-style:italic; font-weight:bold; color:darkred;';

    
        if ($this->getRequest()->isPost())
        {
            if (isset($getvalues['changebutton']))  /* entry from person info display */
            {
                $pid = $getvalues['personid'];
            }
            else
            {
                $formData = $request->getParams();
//var_dump($formData); 
    /* enter button */
                if (isset($formData['ebutton']))
                {
                    $pid = $formData['pid'];
                    $formData['hid'] = $formData['householdid'];
                    $changeForm->hid->setAttrib('disabled', true);
    
                    $origkeys = explode(',', $formData['keys']);
                    $origvalues = explode(',', $formData['values']);
                    $origData = array_combine($origkeys, $origvalues);
                    $this->view->explain = "Labels of changed fields are <span style='$styleChange'> bold green</span>; 
                      labels of fields with errors detected are <span style='$styleError'> bold italic red</span>.";
                    foreach ($origData as $key => $value) 
                    {
//var_dump($key, $value);
                        if (!in_array($key, array('id', 'creationdate', 'timestamp', 'resigndate', 'birthdate',
                          'gender', 'peopleid', 'classdate', 'inductiondontask')))
                        {
                            if ($formData[$key] <> $value)
                            {
                                $changeForm->getElement($key)->getDecorator('label')->setOption('style', $styleChange);
                            }
                        }
                    }

                    $e = $formData['email'];
                    if ($e <> '' && !$efunctions->isValidEmail($e))
                        $changeForm->getElement('email')->getDecorator('label')->setOption('style', $styleError);
    
                    $zip = $formData['zip'];
                    if (!$functions->is_zip($zip))
                        $changeForm->getElement('zip')->getDecorator('label')->setOption('style', $styleError);
    
                    $state = $formData['state'];
                    if (!$functions->is_state($state))
                        $changeForm->getElement('state')->getDecorator('label')->setOption('style', $styleError);
                    
                    $membershipdate = $formData['membershipdate'];
                    if ($membershipdate == '')
                        $membershipdate = '0000-00-00';
                    $result = $functions->date_validate($membershipdate);
                    if (strlen($result) <> 10)
                        $changeForm->getElement('membershipdate')->getDecorator('label')->setOption('style', $styleError);
                    else
                    {
                        if ($result == '0000-00-00')
                            $result = '';
                        $formData['membershipdate'] = $result;
                    }
                    
                    $frienddate = $formData['frienddate'];
                    if ($frienddate == '')
                        $frienddate = '0000-00-00';
                    $result = $functions->date_validate($frienddate);
                    if (strlen($result) <> 10)
                        $changeForm->getElement('frienddate')->getDecorator('label')->setOption('style', $styleError);
                    else
                    {
                        if ($result == '0000-00-00')
                            $result = '';
                        $formData['frienddate'] = $result;
                    }
                    
                    $angelid = $formData['angelid'];
                    if ($angelid == 0)
                        $formData['angelid'] = '';
                       
                }



    /* clear button */
                if (isset($formData['xbutton']))
                {
                    $pid = $formData['pid'];
                    unset($formData);
                }
                

    /* commit button */
                if (isset($formData['cbutton']))
                {
                    $origkeys = explode(',', $formData['keys']);
                    $origvalues = explode(',', $formData['values']);
                    $origData = array_combine($origkeys, $origvalues);
                    $pid = $formData['pid'];
                    $personmap = new Application_Model_PeopleMapper();
                    $person = $personmap->find($pid);
                    $columns = array('firstname', 'lastname', 'email', 'pphone', 'photolink',
                      'inactive', 'status', 'membershipdate');
    
                    if ($person['inactive'] == 'no' && $formData['inactive'] == 'yes')
                    {
                        $person['resigndate'] = $this->today();    
                    }  
    
                    foreach($columns as $key)
                    {
                        $person[$key] = $formData[$key];
                    }
                    $membershipdate = $person['membershipdate'];
                    if ($membershipdate == '')
                        $membershipdate = '0000-00-00';
                    $personmap->savearray($person);
    
                    $housemap = new Application_Model_HouseholdsMapper();
                    $house = $housemap->find($person['householdid']);
                    $hid = $house['id'];
                    
                    if ($house['zip'] <> $formData['zip'])
                        $zipchanged = true;
                    else
                        $zipchanged = false;                    
                    $columns = array('householdname', 'street', 'city', 'state', 'zip',
                      'phone');
                    foreach($columns as $key)
                    {
                        $house[$key] = $formData[$key];
                    }
                    $housemap->savearray($house);
    
                    if (in_array($person['status'], array('Member', 'Affiliate', 'NewFriend')))
                    {
    
                        $connmap = new Application_Model_ConnectionsMapper();
                        $where = array(
                            array('peopleid', ' = ', $pid),
                            );            
                        $connobj = current($connmap->fetchWhere($where));
                        if ($connobj == false)
                        {
                            $connobj = new Application_Model_Connections();
                            $connobj->peopleid = $pid;
                            $connobj->timestamp = date('Y-m-d H:i:s', time());
                            $connmap->save($connobj);
                        }
                        $cid = $connobj->id;
                        $conn = $connmap->find($cid);
                        $columns = array('angelid', 'inducted', 'frienddate', 'comments', 'timestamp');
                        $connobj->timestamp = date('Y-m-d H:i:s', time());
                        foreach($columns as $key)
                        {
                            if (isset($formData[$key]))
                                $conn[$key] = $formData[$key];
                        }
    
                        $frienddate = $conn['frienddate'];
                        if ($frienddate == '')
                            $frienddate = '0000-00-00';
                        $connobj->frienddate = $frienddate;
                        $connobj->angelid = $conn['angelid'];
                        $connobj->inducted = $conn['inducted'];
                        $connobj->comments = $conn['comments'];
                        $connmap->save($connobj);
    
                        $hoodmap = new Application_Model_NeighborhoodsMapper();
                        $where = array(
                            array('householdid', ' = ', $hid),
                            );            
                        $hoodobj = current($hoodmap->fetchWhere($where));

//echo "Zip changed? $zipchanged";
//var_dump($hoodobj); 

                        if ($hoodobj == false  ||  $zipchanged)
                        {
                            $neighborhood = $this->getHood($house);
                            if ($hoodobj == false)
                                $hoodobj = new Application_Model_Neighborhoods();
                            $oldhid = $hoodobj->hoodid;
                            $hoodobj->hoodid = $neighborhood;
                            $hoodobj->householdid = $hid;
                            $hoodmap->save($hoodobj);

                            if ($oldhid <> $neighborhood) {
                                $hoodsmap = new Application_Model_HoodsMapper();
                                $oldhood = $hoodsmap->find($oldhid);
    //var_dump($oldhood);
                                $ohname = $oldhood['hoodname'];
                                $newhood = $hoodsmap->find($neighborhood);
    //var_dump($newhood);         
                                $nhname = $newhood['hoodname'];                 
                                
    // send email to neighborhood coordinator here                            
                                $now = date("m/d/Y", mktime(date('H'), date('i'), 0, date('m'), date('d'),date('Y')));
                                $subj = "Neighborhood change for " . $person['firstname'] . " " . $person['lastname'] . " on " . $now;
                                $et = $subj . "\n<br>Now $nhname changed from $ohname";
    //echo $et;
    //var_dump($hoodobj);
    //var_dump($person); exit;            
                                
                            		$TEXT=$et;
                                $SUBJECT=$subj;
//                                $TO_array=array('neighborhoods@cvuuf.org', 'mike@talvola.com');
                                $TO_array=array('neighborhood@cvuuf.org', 'mike@talvola.com');
    //                            $LOG_array=array('security@cvuuf.org', 'michael.talvola@gmail.com');
                            
                                $efunctions = new Cvuuf_emailfunctions();
                                $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, array('webmail@cvuuf.org' => "CVUUF Database"));
                                $log = $efunctions->log_email($this, 1, $totalsent, 0, 0);
                                
                                $this->view->message = $et;
                            }
            

//var_dump($hoodobj); 
                            
                        }                      
    
                    }
                    elseif ($person['status'] == 'Visitor')
                    {
                        $visitorsmap = new Application_Model_VisitorsMapper();
                        $where = array(
                            array('id', ' = ', $pid),
                            );            
                        $visitor = current($visitorsmap->fetchWhere($where));
                        $visitor->comment = $formData['comments'];
                        $visitorsmap->save($visitor);
                    }
                    
                    $nlemailsmap = new Application_Model_NewsletterEmailsMapper();
                    $formNlemail = $formData['emailnewsletter'];
                    $dbNlemail = $nlemailsmap->find($pid);
                    if ($formNlemail == 'yes')
                    {
                        if ($dbNlemail < 1)
                        {
                            $nlemails = new Application_Model_NewsletterEmails();
                            $nlemails->id = $pid;
                            $nlemailsmap->save($nlemails);
                        }
                    }
                    else
                    {
                        if (isset($dbNlemail))
                        {
                            $nlemailsmap->delete($pid);
                        }
                      
                    }
                        
                }  /* end of commit */
                
            }  /* end of change */
        
        }  /* end of post */


        if (!isset($formData))
        {
            $origData = array();
            $peoplemap = new Application_Model_PeopleMapper();
            $person = $peoplemap->find($pid);
            $changeForm->populate(array('pid' => $pid)) ;
            $membershipdate = $person['membershipdate'];
            if ($membershipdate == '0000-00-00')
                $person['membershipdate'] = '';            

            $hid = $person['householdid'];

            $housemap = new Application_Model_HouseholdsMapper();
            $house = $housemap->find($hid);
            $origData['householdid'] = $hid;
            $changeForm->populate(array('hid' => $hid)) ;
            $changeForm->hid->setAttrib('disabled', true);

            $hoodmap = new Application_Model_NeighborhoodsMapper();
            $where = array(
                array('householdid', ' = ', $hid),
                );            
            $hoodobj = current($hoodmap->fetchWhere($where));
            if ($hoodobj <> false)
            {
                $nid = $hoodobj->hoodid;
                $hoodsmap = new Application_Model_HoodsMapper();
                $hood = $hoodsmap->find($nid);
                $changeForm->populate(array('hood' => $nid)) ;
                $origData['hood'] = $nid;
            }

            $connmap = new Application_Model_ConnectionsMapper();
            $where = array(
                array('peopleid', ' = ', $pid),
                );            
            $connobj = current($connmap->fetchWhere($where));
            if ($connobj <> false)
            {
                $cid = $connobj->id;
                $conn = $connmap->find($cid);
                $frienddate = $conn['frienddate'];
                if ($frienddate == '0000-00-00')
                    $conn['frienddate'] = '';            
                $angelid = $conn['angelid'];
                if ($angelid == 0)
                    $conn['angelid'] = '';
                $inducted = $conn['inducted'];
                if ($inducted == '')
                    $conn['inducted'] = 'No';
            }

//var_dump($origData);
            $nlemailsmap = new Application_Model_NewsletterEmailsMapper();
            $nlemail = $nlemailsmap->find($pid);
            $changeForm->populate(array('emailnewsletter' => $nlemail)) ;
            $origData['emailnewsletter'] = $nlemail;
            if (!isset($inducted))
                $origData['inducted'] = 'No';
            else
                $origData['inducted'] = $inducted;
            if (!isset($frienddate))
                $origData['frienddate'] = '';
            $origData = array_merge($origData, $house, $person);
            if (isset($conn))
            {
                $origData = array_merge($origData, $conn);            
                $changeForm->populate($conn);
            }

            $changeForm->populate($house);
            $changeForm->populate($person);
        }
        else
        {
            $changeForm->populate($formData);
        }
        
        $values = $functions->statusValues();
        $changeForm->status->addMultiOptions($values);

        $values = $functions->hoodValues();
        $changeForm->hood->addMultiOptions($values);

        $values = implode(',', $origData);
        $keys = implode(',', array_keys($origData));
        $changeForm->values->setValue($values);
        $changeForm->keys->setValue($keys);
               
        $this->view->style = 'zform.css';
        $this->view->changeForm = $changeForm;
    
    }




        /* returns position object from init form */
        function fillPosition($param, Application_Model_Positions $position)
        {
            $position->sequence = isset($param['sequence']) ? $param['sequence'] : 0;
            $position->title = isset($param['title']) ? $param['title'] : '';
            $position->contact1 = isset($param['contact1']) ? $param['contact1'] : 0;
            $position->headingid = isset($param['class'][0]) ? $param['class'][0] : '';
            $position->id = isset($param['pid']) ? $param['pid'] : 0;
        }


        /* returns group object from init form */
        function fillGroup($param, Application_Model_Groups $group)
        {
            $group->sequence = isset($param['sequence']) ? $param['sequence'] : 0;
            $group->title = isset($param['title']) ? $param['title'] : '';
            $group->contact1 = isset($param['contact1']) ? $param['contact1'] : 0;
            $group->contact2 = isset($param['contact2']) ? $param['contact2'] : 0;
            $group->contact3 = isset($param['contact3']) ? $param['contact3'] : 0;
            $group->contact4 = isset($param['contact4']) ? $param['contact4'] : 0;
            $group->headingid = isset($param['class'][0]) ? $param['class'][0] : 0;
        }



        /* returns personid from name */
        function idFromName($name)
        {
            $peoplemap = new Application_Model_PeopleMapper();
            $space = strpos($name, ' ');
            if ($space === false)
                return(0);
//echo substr($name, $space + 1), '<br>';
            $space2 = strpos(substr($name, $space + 1), ' ');
//echo "SPACE2 $space2 AND SPACE $space <br>"; 
            if ($space2 <> false)
                $space = $space2 + $space + 1;
            $firstname = substr($name, 0, $space);
            $lastname = substr($name, $space + 1);
            
//echo "SPACE2 $space2 AND SPACE at $space so FIRST $firstname LAST $lastname"; exit;
            $where = array(
                array('firstname', ' = ', $firstname),
                array('lastname', ' = ', $lastname),
                );            
            $people = $peoplemap->fetchWhere($where);
//var_dump($people); exit;
            if (count($people) <> 1)
                return(0);
            else
                return(current($people)->id);
            
        }



    public function positionsAction()
    {
        $request = $this->getRequest();
        $peoplemap = new Application_Model_PeopleMapper();
        $positionsmap = new Application_Model_PositionsMapper();

        $getvalues = $request->getParams();
        if ($this->getRequest()->isGet())
        {
            if (isset($getvalues['id']))
            {
                $id = $getvalues['id'];
                $position = $positionsmap->find($id);          
//var_dump($position); exit;
                $pid = $position['contact1'];
                $person = $peoplemap->find($pid);
                if ($person['firstname'] <> '')
                    $name = $person['firstname'] . ' ' . $person['lastname'];
                else
                    $name = '';

                $positionForm = new Application_Form_DataPositions();
                $positionForm->populate($position);
                
                $positionForm->populate(array('id' => $id, 'pid' => $id, 'name' => $name));
                $positionForm->id->setAttrib('disabled', true);
                
                $classes = $this->getClasses('position');
                $positionForm->class->addMultiOptions(array('')); 
                $positionForm->class->addMultiOptions($classes);
                $positionForm->class->setValue($position['headingid']); 
                

                $this->view->positionForm = $positionForm;

                $this->view->style = 'zform.css';
                return $this->render('positionchange');

            
            }
        }

        if ($this->getRequest()->isPost())
        {
            if (isset($getvalues))
            {
                if (isset($getvalues['addbutton']))
                {
                    $position = new Application_Model_Positions();
                    $position->title = "*** NEW ***";
                    $positionsmap->save($position);
                    $id = $position->id;
                    $this->view->message = "New position $id created.";
                }
                elseif (isset($getvalues['sbutton']))
                {
                    $position = new Application_Model_Positions();
                    $formData = $this->_request->getPost();
//var_dump($formData); 
                    $name = $formData['name'];
                    $cid = $this->idFromName($name);
//var_dump($name);
//var_dump($cid);
                    $formData['contact1'] = $cid;
                    $this->fillPosition($formData, $position);
//var_dump($position); exit;
                    $positionsmap->save($position);
                    $this->view->message = "Changed $position->id: $position->title.";
                }

                elseif (isset($getvalues['dbutton']))
                {
                    $formData = $this->_request->getPost();
                    $id = $formData['pid'];
                    $positionsmap->delete($id);
                    $this->view->message = "Deleted $id.";
                }
            }
        }

        
        $classes = $this->getClasses('position');        
        $positions = $positionsmap->fetchAll();
        
        $peoplemap = new Application_Model_PeopleMapper();

        foreach ($positions as $position) 
        {
//var_dump($position);
            $title = $position->title;
            $id = $position->id;
            $class = $position->headingid;
            $sequence = $position->sequence;

            $pid = $position->contact1;
            $person = $peoplemap->find($pid);
            $name = $person['firstname'] . ' ' . $person['lastname'];
//echo "$class <br>";
//var_dump($classes);        
            $table[] = array("ID" => $id, "Title" => $title, "Name" => $name, 
              "Class" => $classes[$class], "Sequence" => $sequence
              );
        }

          function cmp($a, $b)
          {
              return strcmp($a["Title"], $b["Title"]);
          }
        usort($table, "cmp");
//var_dump($table); exit;
        $this->view->table = $table;
        $this->view->style = 'zform.css';


    }




    public function groupsAction()
    {
        $request = $this->getRequest();
        $peoplemap = new Application_Model_PeopleMapper();
        $groupsmap = new Application_Model_GroupsMapper();

        $getvalues = $request->getParams();
        if ($this->getRequest()->isGet())
        {
            if (isset($getvalues['id']))
            {
                $id = $getvalues['id'];
                $group = $groupsmap->find($id);          
//var_dump($group); exit;
                $contactNames = array();
                foreach (array('contact1', 'contact2', 'contact3', 'contact4') as $contact)
                {
                    $pid = $group[$contact];
                    $person = $peoplemap->find($pid);
                    if ($person['firstname'] <> '')
                        $name = $person['firstname'] . ' ' . $person['lastname'];
                    else
                        $name = '';
                    $contactNames[] = $name;
                }
                $groupForm = new Application_Form_DataGroups();
                $groupForm->populate($group);
                $groupForm->populate(array('id' => $id, 'pid' => $id, 
                    'contact1' => $contactNames[0], 'contact2' => $contactNames[1], 
                    'contact3' => $contactNames[2], 'contact4' => $contactNames[3],
                    ));
                $groupForm->id->setAttrib('disabled', true);
                $classes = $this->getClasses('group');
                $groupForm->class->addMultiOptions(array('')); 
                $groupForm->class->addMultiOptions($classes);
                $groupForm->class->setValue($group['headingid']); 
                

                $this->view->groupForm = $groupForm;

                $this->view->style = 'zform.css';
                return $this->render('groupchange');
            }
        }

        if ($this->getRequest()->isPost())
        {
            if (isset($getvalues))
            {
                if (isset($getvalues['addbutton']))
                {
                    $group = new Application_Model_Groups();
                    $group->title = "*** NEW ***";
                    $group->sequence = "9999";
                    $groupsmap->save($group);
                    $id = $group->id;
                    $this->view->message = "New group $id created.";
                }
                elseif (isset($getvalues['sbutton']))
                {
                    $group = new Application_Model_Groups();
                    $formData = $this->_request->getPost();
//var_dump($formData);
                    $name = $formData['contact1'];
                    $cid = $this->idFromName($name);
                    $formData['contact1'] = $cid;
                    $name = $formData['contact2'];
                    $cid = $this->idFromName($name);
                    $formData['contact2'] = $cid;
                    $name = $formData['contact3'];
                    $cid = $this->idFromName($name);
                    $formData['contact3'] = $cid;
                    $name = $formData['contact4'];
                    $cid = $this->idFromName($name);
                    $formData['contact4'] = $cid;
                    $this->fillgroup($formData, $group);
                    $group->id = $formData['pid'];
                    $group->publicpage = $formData['publicpage'];
//var_dump($group); exit;
                    $groupsmap->save($group);
                    $this->view->message = "Changed $group->id: $group->title.";
                }

                elseif (isset($getvalues['dbutton']))
                {
                    $formData = $this->_request->getPost();
                    $id = $formData['pid'];
                    $groupsmap->delete($id);
                    $this->view->message = "Deleted $id.";
                }

                elseif (isset($getvalues['cbutton']))
                {
                    $formData = $this->_request->getPost();
                    $id = $formData['pid'];
                    $group = $groupsmap->find($id);
                    unset($group['id']);
                    $newgroup = new Application_Model_Groups();
                    $this->fillGroup($group, $newgroup);
                    $groupsmap->save($newgroup);
                    $newid = $newgroup->id;
                    
                    $this->view->message = "Cloned $id to new group $newid.";
                }
            }
        }

        
        $classes = $this->getClasses('group');        
        $groups = $groupsmap->fetchAll();
        
        $peoplemap = new Application_Model_PeopleMapper();

        foreach ($groups as $group) 
        {
            if ($group->sequence <> 0)
            {
                $class = $group->headingid;
                $title = $group->title;
                $pub = $group->publicpage;
                $id = $group->id;
                $sequence = $group->sequence;
    
                $contactNames = array();
                foreach (array('contact1', 'contact2', 'contact3', 'contact4') as $contact)
                {
                    $pid = $group->$contact;
                    $person = $peoplemap->find($pid);
                    if ($person['firstname'] <> '')
                        $name = $person['firstname'] . ' ' . $person['lastname'];
                    else
                        $name = '';
                    $contactNames[] = $name;
                }
    
                $table[] = array("ID" => $id, "Title" => $title, "Name0" => $contactNames[0], 
                  "Name1" => $contactNames[1],"Name2" => $contactNames[2],"Name3" => $contactNames[3],
                  "Class" => $classes[$class], "Sequence" => $sequence, "Public" => $pub,
                  );
            }
        }

          function cmp($a, $b)
          {
              $class = strcmp($a["Class"], $b["Class"]);
              if ($class <> 0)
                  return ($class);
              return $a["Sequence"] - $b["Sequence"];
          }
        usort($table, "cmp");

        $this->view->table = $table;
        $this->view->style = 'zform.css';


    }




        /* returns position object from init form */
        function fillHeading($param, Application_Model_Headings $heading, $type = 'position')
        {
            $heading->type = $type;
            $heading->sequence = isset($param['sequence']) ? $param['sequence'] : 0;
            $heading->heading = isset($param['title']) ? $param['title'] : '';
            $heading->id = isset($param['hid']) ? $param['hid'] : 0;
        }



    public function positionclassesAction()
    {
        $type = 'position';
        $this->classes($type);

    }


    public function groupclassesAction()
    {
        $type = 'group';
        $this->classes($type);

    }


    public function classes($type)
    {
        $request = $this->getRequest();
        $peoplemap = new Application_Model_PeopleMapper();
        $headingsmap = new Application_Model_HeadingsMapper();

        $getvalues = $request->getParams();
        if ($this->getRequest()->isGet())
        {
            if (isset($getvalues['id']))
            {
                $id = $getvalues['id'];
                $where = array(
                    array('id', ' = ', $id),
                    );            
                $heading = current($headingsmap->fetchWhere($where));
                $headingForm = new Application_Form_DataClasses();
                $values = array('title' => $heading->heading, 'sequence' => $heading->sequence);
                $headingForm->populate($values);
              
                $headingForm->populate(array('id' => $id, 'hid' => $id));
                $headingForm->id->setAttrib('disabled', true);
                
                $this->view->headingForm = $headingForm;

                if ($type == 'group')
                    $headingForm->setAction('/data/groupclasses');
                else
                    $headingForm->setAction('/data/positionclasses');
                    
                $this->view->style = 'zform.css';
                return $this->render('classeschange');
                
            }
            
        }

        if ($this->getRequest()->isPost())
        {
            if (isset($getvalues))
            {
                if (isset($getvalues['addbutton']))
                {
                    $heading = new Application_Model_Headings();
                    $heading->heading = "*** NEW ***";
                    $heading->type = $type;
                    $headingsmap->save($heading);
                    $id = $heading->id;
                    $this->view->message = "New class heading $id created.";
                }
                
                elseif (isset($getvalues['sbutton']))
                {
                    $heading = new Application_Model_Headings();
                    $formData = $this->_request->getPost();
                    $this->fillheading($formData, $heading);
                    $heading->type = $type;
                    $headingsmap->save($heading);
                    $this->view->message = "Changed $heading->id: $heading->heading.";
                }

                elseif (isset($getvalues['dbutton']))
                {
                    $formData = $this->_request->getPost();
                    $id = $formData['hid'];
                    $headingsmap->delete($id);
                    $this->view->message = "Deleted $id.";
                }
                
            }
        }
        
        $where = array(
            array('type', ' = ', $type),
            );            
        $headings = $headingsmap->fetchWhere($where);
        foreach ($headings as $heading) 
        {
            $table[] = array("ID" => $heading->id, "Title" => $heading->heading, "Sequence" => $heading->sequence, 
              );
        }
        
        $this->view->table = $table;
        $this->view->style = 'zform.css';
        $this->view->type = strtoupper($type);
        return $this->render('classes');

    }
    

    public function maintvisitsAction()
    {
        $request = $this->getRequest();
        if ($this->getRequest()->isPost())
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                $visitsmap = new Application_Model_VisitsMapper();
                $this->view->message = array();
                $visitdate = $personid = '';
                if (isset($getvalues['visitdate']))
                    $visitdate = $getvalues['visitdate'];
                if (isset($getvalues['personid']))
                $personid = $getvalues['personid'];
                if (($visitdate == '') || ($personid == ''))
                    $this->view->message = "Both personid and date must be entered.";
                else
                {
                    if (isset($getvalues['ebutton']))
                    {
                        $where = array(
                            array('personid', ' = ', $personid),
                            array('visitdate', ' = ', $visitdate),
                        );
                        $result = $visitsmap->fetchWhere($where);
                        if (count($result) > 0)
                            $this->view->message = "Visit on $visitdate by $personid already in database.";
                        else
                        {
                            $visit =  new Application_Model_Visits();
                            $visit->personid = $personid;
                            $visit->visitdate = $visitdate;
                            $visitsmap->save($visit);
                            $this->view->message = "Visit on $visitdate by $personid written.";
                        }
                    }
                    
                    if (isset($getvalues['dbutton']))
                    {
                        $where = array(
                            array('personid', ' = ', $personid),
                            array('visitdate', ' = ', $visitdate),
                        );
                        $result = $visitsmap->fetchWhere($where);
                        if (count($result) == 0)
                            $this->view->message = "Visit on $visitdate by $personid not in database.";
                        else
                        {
                            $id = current($result)->id;
                            $visitsmap->delete($id);
                            $this->view->message = "Visit on $visitdate by $personid deleted.";
                        }
                    }
                    
                    
                }
            }
        }

        $entryForm = new Application_Form_DataMaintVisits();
        $this->view->form = $entryForm;
        $this->view->style = 'zform.css';       
   
    }





    

    public function analyzevisitsAction()
    {

            function _getNumber($details) 
            {
                return $details->visitdate;
            }

        // Compute three months ago
        
        $startY=date('Y');
        $startM=date('m');
        $startD=date('d');
        $Tdate = getdate(mktime(0,0,0, $startM-3, $startD+8, $startY));
        $theDate = date("d/m/Y", mktime(0,0,0, $startM-3, $startD+8, $startY));
        $today = $Tdate['wday'];
        $TDay = $Tdate['mday'];
        $Month = $Tdate['mon'];
        $Year = $Tdate['year'];
        $MDay = $TDay-($today);
        
        $threemos = mktime(0,0,0,$Month,$MDay+6,$Year);
        $ThreeMos = date("Y-m-d", $threemos);

        $nowdate = mktime(0,0,0,$startM,$startD,$startY);
        $Nowdate = date("Y-m-d", $nowdate);


        $visitsmap = new Application_Model_VisitsMapper();
        $peoplemap = new Application_Model_PeopleMapper();
        $table = array();

        $request = $this->getRequest();
        if ($this->getRequest()->isPost())
        {
            $getvalues = $request->getParams();
            if (isset($getvalues))
            {
                $this->view->message = array();
                if (isset($getvalues['id']))
                {
                    $ids = $getvalues['id'];
                    foreach ($ids as $id)
                    {
                        $where = array(
                            array('id', ' = ', $id),
                            );
                        $visitor = current($peoplemap->fetchWhere($where));
                        $visitor->inactive = 'yes';
                        $peoplemap->save($visitor);
                        $this->view->message[] = "Visitor $id now is inactive.";            
                    }
                }
                else 
                    $this->view->message = "No visitor selected.";
            }
            
        }
        
        $where = array(
            array('inactive', ' <> ','yes'),
            array('status', ' = ','Visitor'),
            );            
        $visitors = $peoplemap->fetchWhere($where);
        foreach ($visitors as $visitor)
        {
            $where = array(
                array('personid', ' = ', $visitor->id),
                );
            $visits = $visitsmap->fetchWhere($where);
            $visitcount = count($visits);
            if ($visitcount > 0)
            {
                $dates = array_map('_getNumber', $visits);
                $lastvisit = max($dates);
            }
            else
            {
                $visitsmap = new Application_Model_VisitsMapper();
                $visit = new Application_Model_Visits();
                $visit->personid = $visitor->id;
//var_dump($Nowdate);
//exit;
                $visit->visitdate = $Nowdate;
                $visit->service = 0;
                $visitsmap->save($visit, 'new');

            }
            
        		if ($lastvisit < $ThreeMos) 
            {
                $name = $visitor->firstname . ' ' . $visitor->lastname;
                $table[] = array($visitor->id, $lastvisit, $visitcount, $name);
        		}
        }
        
        $this->view->table = $table;
        $this->view->style = 'zform.css';       
                
    }








    public function testAction()
    {
            require_once 'EzPDF/class.ezpdf.php';
	$pdf = new Cezpdf('LETTER', 'portrait');
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->ezTable($table,'',$caldate." ".$CalType." CALENDAR     ATT: 9:15 _____  11:00 _____", array('showHeadings'=>1,'shaded'=>1,
		'showLines'=>1, 'fontSize' => 8, 'titleFontSize' => 12, 'rowGap'=>0));
	$pdf->ezStream();
        exit;
    }

}

