<?php

class ReController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {
        // action body
    }


        public function admin($there)
        {
            $there->view->theme = 're';        /* Initialize action controller here */
            $functions = new Cvuuf_authfunctions();
            $auth = $functions->getAuth();
            $there->view->level = $auth->level;
            $there->userid = $auth->memberid;    
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
      
            
            public function today()
            {
                return(date("Y-m-d"));    
            }
            

            function setContact($which, $formData, $here)
            {
                $which1 = $which[0];
                $Which = strtoupper($which1) . substr($which, 1);
                $first = $formData['firstname'];
                $last = $formData['lastname'];
//var_dump($first); var_dump($last);
                if ($first == '' && $last == '')
                {
                    $this->view->message = "No $which contact name entered.";
                }
                else
                {
                    $functions = new Cvuuf_personfunctions();
                    $names = $functions->fillFindNames($first, $last, 're');
//var_dump($names);
                    if (count($names) == 0)
                    {
                        $here->view->message = "$Which contact name not found.";
                    }
                    else
                    {
                        $vname = $which1 . 'names';
                        if (count($names) > 1)
                            $here->view->$vname = $names;
                        $where = array(
                            array('id', ' = ', $names[0][0]),
                            );
                        $peoplemap = new Application_Model_PeopleMapper();
                        $pid = current($peoplemap->fetchWhere($where))->id;
//var_dump($pid);
                        return $pid;
                    }
                }
            }
            
            
            function setSelect($formData, $here)
            {
                $ids = $formData['id'];
                if (count($ids) <> 1)
                {
                    $here->view->message = "Exactly one person must be selected.";
                }
                else
                {
                    $pid = $ids[0];
                    $where = array(
                        array('id', ' = ', $pid),
                        );
                    $peoplemap = new Application_Model_PeopleMapper();
                    $person = current($peoplemap->fetchWhere($where));
                    return ($person->id);            
                }
            }


            function getClasses()
            {
                return array('None', 'Child Space', 'Pre-School', 'Lower Elementary',
                  'Upper Elementary', 'Middle School', 'YRUU');
            }

        public function stage1($here, $formData, &$workData, $newForm, $existingForm, $regtype = 'yes')
        {
            $remap = new Application_Model_REMapper();
            $peoplemap = new Application_Model_PeopleMapper();
            $housemap = new Application_Model_HouseholdsMapper();

            if (isset($formData))
            {
                    if (isset($formData['fbutton']))
                    {
                        $findid = $formData['recid'];
                        if ($findid > 0)
                            $recid = $findid;                        
                        else
                        {
                            $first = $formData['firstname'];
                            $last = $formData['lastname'];
                            if ($first == '' && $last == '')
                            {
                                $this->view->message = "Nothing entered to FIND.";
                            }
                            else
                            {
                                $functions = new Cvuuf_personfunctions();
                                $names = $functions->fillFindNames($first, $last, "'Child'");
                                if (count($names) == 0)
                                {
                                    $workData['firstname'] = $formData['firstname'];
                                    $workData['lastname'] = $formData['lastname'];
                                    $this->view->message = "Child name not found, moving name to New Entry.";
                                    $recid = 0;
                                }

                                elseif (count($names) > 1)
                                    $this->view->names = $names;
                              
                                else
                                {
                                    $where = array(
                                        array('childid', ' = ', $names[0][0]),
                                        );
                                    $reg = current($remap->fetchWhere($where));
                                    $recid = $reg->id;
                                }
//var_dump($reg); exit;
                            }
                        }
                    }
                
                if (isset($formData['cbutton']))
                {
                    $childid = 0;
                    $data['childid'] = 0;
                    $data['recid'] = 0;
                    $data['birth'] = '';
                    $data['grade'] = '';
                    $data['gender'] = 'F';
                    $data['firstname'] = '';
                    $data['lastname'] = '';
                }
                
                if (isset($formData['selectbutton']))
                {
                    $ids = $formData['id'];
                    if (count($ids) <> 1)
                    {
                        $message = "Exactly one person must be selected.";
                    }
                    else
                    {
                        $pid = $ids[0];
                        $where = array(
                            array('childid', ' = ', $pid),
                            );
                        $reg = current($remap->fetchWhere($where));
                        $thePerson = $reg->id;            
                    }
                }                        
                
                if (isset($formData['ebutton']))
                {
//var_dump($formData);
//echo "LINE 268 <br>"; var_dump($workData); 

                    if ($formData['firstname'] == '')
                    {
                        $here->view->message = "No name entered, cannot generate new child record.";
                    }
                    else
                    {
                        if (!isset($workData['childid']))
                        {
                                $person = new Application_Model_People();
                                $person->status = 'Child';
                                $person->creationdate = $this->today();
                                $peoplemap->save($person);
                                $childid = $person->id;
                                $workData['childid'] = $childid;
                                $reg = new Application_Model_RE();
                                $reg->childid = $childid;
                                $reg->registered = $regtype;
                                $remap->save($reg);
                                $recid = $reg->id;
                                $workData['recid'] = $recid;
                                $here->view->message = "New database person $childid and registration $recid created.";
                        }
                        else
                        {
                            $childid = $workData['childid'];
                            $recid = $workData['recid'];
                        }
//    echo "LINE 283 <br>"; var_dump($person);
//    echo "LINE 284 <br>"; var_dump($reg); 
                        
                        $where = array(
                            array('id', ' = ', $childid),
                            );
                        $person = current($peoplemap->fetchWhere($where));
                        $where = array(
                            array('id', ' = ', $recid),
                            );
                        $reg = current($remap->fetchWhere($where));
//    echo "NEAR LINE 295 <br>";
//    var_dump($formData);
//var_dump($workData); 
//exit; 
                        $person->firstname = $formData['firstname'];
                        $person->lastname = $formData['lastname'];
                        $peoplemap->save($person);
                        
                        $reg->birth = $formData['birth'];
                        $reg->grade = $formData['grade'];
                        if (isset($formData['gender']))
                            $reg->gender = $formData['gender'];
                        $remap->save($reg);
                    }
                }                        
            }
              

            if (!isset($recid))
                $recid = 0;
//echo " LINE 326 thePerson = $recid <br>";
            if ($recid > 0)
            {
                $reg = $remap->find($recid);
                if ($reg == false)
                {
                    $here->view->message = "Registration ID $recid is not registered.";
                }
                else
                { 
                    $pid = $reg['childid'];
//echo "LINE 337  pid (childid from reg) = $pid <br>";
                    $person = $peoplemap->find($pid);
                    
                    $childid = $person['id'];
                    $workData['childid'] = $childid;

                    $workData['recid'] = $reg['id'];
                    $workData['birth'] = $reg['birth'];
                    $workData['grade'] = $reg['grade'];
                    $workData['gender'] = $reg['gender'];
                    
                    $workData['firstname'] = $person['firstname'];
                    $workData['lastname'] = $person['lastname'];
                    $here->view->childid = $childid;
                }
            }
//echo "LINE 365 <br>"; var_dump($workData);
            if (isset($workData))
                $newForm->populate($workData);
            $workData['stage'] = 1;
            if ($regtype <> 'track')
                $this->view->existform = $existingForm;
        }
        
        
    /* STAGE 2 */
        public function stage2($here, $formData, &$workData, $newForm, $newdForm, $newaForm, $newadForm)
        {
//echo "stage2()";
            $remap = new Application_Model_REMapper();
            $peoplemap = new Application_Model_PeopleMapper();
            $housemap = new Application_Model_HouseholdsMapper();

            $recid = $workData['recid'];
            $reg = $remap->find($recid);
            $childid = $workData['childid'];
//echo "reg"; var_dump($reg);
//echo "formData"; var_dump($formData);
//echo "workData"; var_dump($workData);
       
            if (isset($workData['ppersonid']))
                $pripid = $workData['ppersonid'];
            else
                $pripid = $reg['ppersonid'];
            if (isset($workData['apersonid']))
                $altpid = $workData['apersonid'];
            else
                $altpid = $reg['apersonid'];

            if (isset($formData))
            {
                if (isset($formData['pselectbutton']))
                    $pripid = $this->setSelect($formData, $this);
                if (isset($formData['aselectbutton']))
                    $altpid = $this->setSelect($formData, $this);
                if (isset($formData['pbutton']))
                    $pripid = $this->setContact('primary', $formData, $this);
//var_dump($pripid);

                if (isset($formData['abutton']))
                    $altpid = $this->setContact('alternate', $formData, $this);
            }
//echo "STAGE $stage <br>"; exit;
            $pri = false;
            if ($pripid <> 0)
                $pri = $peoplemap->find($pripid);
            $alt = false;
            if ($altpid <> 0)                
                $alt = $peoplemap->find($altpid);

            if ($pri <> false)
            {
        /* not the first entry to stage 2 */


                $workData['ppersonid'] = $pripid;
                $newForm->populate($pri);
                $newForm->status->setAttrib('disabled', true);
                $house = $housemap->find($pri['householdid']);

                if (isset($formData['priphone']))
                    $house['phone'] = $formData['priphone'];
                if (isset($formData['priemail']))
                    $pri['email'] = $formData['priemail'];
                if (isset($formData['pripphone']))
                    $pri['pphone'] = $formData['pripphone'];

                $workData['priphone'] = $house['phone'];
                $workData['priemail'] = $pri['email'];
                $workData['pripphone'] = $pri['pphone'];

                if (isset($formData['pdbutton']))
                {
                    $hcols = array('phone');
                    foreach($hcols as $col)
                    {
                        $value = $formData[$col];
                        $workData['pri'.$col] = $value;
                        $house[$col] = $value;
                    }
                    $pcols = array('email', 'pphone');                    
                    foreach($pcols as $col)
                    {
                        $value = $formData[$col];
                        $workData['pri'.$col] = $value;
                        $pri[$col] = $value;
                    }
                }
                
                $where = array(
                    array('id', ' = ', $childid),
                    );
                $peopleobj = current($peoplemap->fetchWhere($where));
                $oldhid = $peopleobj->householdid;
                if ($oldhid == 0)
                {
                    $peopleobj->householdid = $house['id'];
//var_dump($peopleobj); exit;            
                    $peoplemap->save($peopleobj);
                    $here->view->message = array();
                    $here->view->message[] = "Child person record has been updated with householdid.";
                }
                    
            }
             
            if ($alt <> false)
            {
                $workData['apersonid'] = $altpid;
                $newaForm->populate($alt);
                $newaForm->status->setAttrib('disabled', true);
                $this->view->newaform = $newaForm;
                $housea = $housemap->find($alt['householdid']);

                if (isset($formData['altphone']))
                    $housea['phone'] = $formData['altphone'];
                if (isset($formData['altemail']))
                    $alt['email'] = $formData['altemail'];
                if (isset($formData['altpphone']))
                    $alt['pphone'] = $formData['altpphone'];

                $workData['altphone'] = $housea['phone'];
                $workData['altemail'] = $alt['email'];
                $workData['altpphone'] = $alt['pphone'];

                $newadForm->populate($housea);
                $newadForm->populate($alt);
                $this->view->newadform = $newadForm;

//var_dump($formData);
                if (isset($formData['aldbutton']))
                {
                    $hcols = array('phone');
                    foreach($hcols as $col)
                    {
                        $value = $formData[$col];
                        $workData['alt'.$col] = $value;
                        $house[$col] = $value;
                    }
                    $pcols = array('email', 'pphone');                    
                    foreach($pcols as $col)
                    {
                        $value = $formData[$col];
                        $workData['alt'.$col] = $value;
                        $alt[$col] = $value;
                    }
                }
            }

//var_dump($workData); 
            $workData['stage'] = 2;
            if (isset($house))
                $newdForm->populate($house);
            if ($pri <> false)
                $newdForm->populate($pri);
            $this->view->newdform = $newdForm;
            $this->view->newaform = $newaForm;
            if (isset($ahouse))
                $newadForm->populate($ahouse);
            if ($alt <> false)
                $newadForm->populate($alt);
            $this->view->newadform = $newadForm;
        }


    /* FINAL */
        public function finalStage($here, $workData, $regtype, $allergies = null, $dbnames = null, $items = null, $issues = null)
        {

            $remap = new Application_Model_REMapper();
            $peoplemap = new Application_Model_PeopleMapper();
            $housemap = new Application_Model_HouseholdsMapper();
            
            $here->view->message = array();
    /* finalize - write changes to re, people, household records  */ 
            $cid = $workData['childid'];
            $recid = $workData['recid'];
//echo "FINALIZE WITH CHILD $cid REGISTRATION $recid <br>"; 
            $recid = $workData['recid'];
            $reg = $remap->find($recid);

            if ($regtype <> 'track')
            {
                $AllergiesStr = '';
                if (isset($workData['allergies']))
                {
                    $values = explode(',', $workData['allergies']);
                    $i = 0;
                    foreach ($allergies as $key => $value)
                    {
                        $checked = $values[$i++];
                        if ($checked <> '')
                        {
                            if ($AllergiesStr == '')
                                $AllergiesStr = $key;
                            else
                                $AllergiesStr .= ',' . $key;
                        }
                    }
                }
//var_dump($AllergiesStr); 
                $HealthStr = '';
                if (isset($workData['health']))
                {
                    $values = explode(',', $workData['health']);
                    $i = 0;
                    foreach ($dbnames as $key => $value)
                    {
                        $checked = $values[$i++];
                        if ($checked <> '')
                        {
                            if ($HealthStr == '')
                                $HealthStr = $key;
                            else
                                $HealthStr .= ',' . $key;
                        }
                    }
                }
//var_dump($HealthStr); 
 
                if (isset($workData['health']))
                {
                    $values = explode(',', $workData['health']);
                    $i = 0;
                    foreach ($issues as $key => $value)
                    {
                        $v = $values[$i++];
                        if (isset($workData[$key]))
                        {
                            if ($v <> '')
                            {
                                $val = $workData[$key];
                                $$key = $val;
//echo "KEY $key  <br>";
//echo "$val <br>";
                            }
                        }
                    }
                }

//echo "NEW VALUES:<br>$otherissues <br>";
//echo "$medications <br>";
//exit;
                $CharacterStr = '';
                if (isset($workData['characteristics']))
                {
                    $values = explode(',', $workData['characteristics']);
                    $i = 0;
                    foreach ($items as $key => $value)
                    {
                        $checked = $values[$i++];
                        if ($checked <> '')
                        {
                            if ($CharacterStr == '')
                                $CharacterStr = $key;
                            else
                                $CharacterStr .= ',' . $key;
                        }
                    }
                }
//var_dump($reg['characteristics']);
//var_dump($CharacterStr); 
//var_dump($characteristics);
                if (isset($workData['othertext']))
                    $othertext = $workData['othertext'];
                
                $items6 = array('receive', 'discuss', 'insurance', 'insnumber', 'signame');
                foreach ($items6 as $item)
                    $$item = $workData[$item];
            }
          /* update $reg and write to database */

//echo "WORK "; var_dump($workData);
//echo "REG "; var_dump($reg);
            $where = array(
                array('id', ' = ', $recid),
                );
            $regobj = current($remap->fetchWhere($where));
            $regobj->birth = $workData['birth'];
            $regobj->grade = $workData['grade'];
            $regobj->gender = $workData['gender'];
            if (isset($workData['ppersonid']))
                $regobj->ppersonid = $workData['ppersonid'];
            if (isset($workData['apersonid']))
                $regobj->apersonid = $workData['apersonid'];

            if ($regtype <> 'track')
            {
                $regobj->allergies = $AllergiesStr;
                $regobj->foodallergies = $workData['foodallergies'];
                $regobj->allergymeds = $workData['allergymeds'];
                $regobj->health = $HealthStr;
                $regobj->behavissues = $workData['behavissues'];
                $regobj->develissues = $workData['develissues'];
                $regobj->langissues = $workData['langissues'];
                $regobj->otherissues = $workData['otherissues'];
                $regobj->medications = $workData['medications'];
                $regobj->othertext = $workData['othertext'];
                $regobj->characteristics = $CharacterStr;
                $regobj->receive = $workData['receive'];
                $regobj->discuss = $workData['discuss'];
                $regobj->insurance = $workData['insurance'];
                $regobj->insnumber = $workData['insnumber'];
                $regobj->signame = $workData['signame'];
                $regobj->registered = 'yes';
            }

                $remap->save($regobj);
                $here->view->message[] = "RE table has been updated.";
//var_dump($regobj); exit;
                
                if (isset($workData['ppersonid']))
                {
                    $where = array(
                        array('id', ' = ', $workData['ppersonid']),
                        );
                    $pri = current($peoplemap->fetchWhere($where));
                    $email = $pri->email;
                    $pphone = $pri->pphone;
                    $wemail = $workData['priemail'];
                    $wpphone = $workData['pripphone'];
                    if ($email <> $wemail || $pphone <> $wpphone)
                    {
                        $pri->email = $wemail;
                        $pri->pphone = $wpphone;
                        $peoplemap->save($pri);
                        $here->view->message[] = "People table email and personal phone has been updated for $pri->firstname $pri->lastname.";
                    }
                    $hid = $pri->householdid;
                    $where = array(
                        array('id', ' = ', $hid),
                        );
                    $house = current($housemap->fetchWhere($where));
                    $phone = $house->phone;
                    $wphone = $workData['priphone'];
                    if ($phone <> $wphone)
                    {
    //echo "Primary $phone $wphone <br>";
                        $house->phone = $wphone;
                        $housemap->save($house);
                        $here->view->message[] = "Households table home phone has been updated for $pri->firstname $pri->lastname.";
                    }
                }
                
                if (isset($workData['apersonid']))
                {
                    $where = array(
                        array('id', ' = ', $workData['apersonid']),
                        );
                    $alt = current($peoplemap->fetchWhere($where));
                    $email = $alt->email;
                    $pphone = $alt->pphone;
                    $wemail = $workData['altemail'];
                    $wpphone = $workData['altpphone'];
                    if ($email <> $wemail || $pphone <> $wpphone)
                    {
                        $alt->email = $wemail;
                        $alt->pphone = $wpphone;
                        $peoplemap->save($alt);
                        $this->view->message[] = "People table email and personal phone has been updated for $alt->firstname $alt->lastname.";
                    }
                    $hid = $alt->householdid;
                    $where = array(
                        array('id', ' = ', $hid),
                        );
                    $house = current($housemap->fetchWhere($where));
                    $phone = $house->phone;
                    $wphone = $workData['altphone'];
                    if ($phone <> $wphone)
                    {
    //echo "Alternate $phone $wphone <br>";
                        $house->phone = $wphone;
                        $housemap->save($house);
                        $this->view->message[] = "Households table home phone has been updated for $alt->firstname $alt->lastname.";
                    }
                }
            }
      



    public function adminAction()
    {
        $this->admin($this);    
    
    }


    public function trackAction()
    {
        $this->view->track = 'track';
        $this->admin($this);    
        $existingForm = new Application_Form_REEnroll1exist();
        $newForm = new Application_Form_REEnroll1new();
        $remap = new Application_Model_REMapper();
        $peoplemap = new Application_Model_PeopleMapper();
        $housemap = new Application_Model_HouseholdsMapper();
        $workData = array();
        $data = array();
        $childid = 0;
        $request = $this->getRequest();
//echo "LINE 115 <br>"; var_dump($workData); 

        if ($this->getRequest()->isPost())
        {
            $formData = $request->getParams();
            if (isset($formData['keys']))
            {
                $workkeys = explode('|', $formData['keys']);
                $workvalues = explode('|', $formData['values']);
                $workData = array_combine($workkeys, $workvalues);
            }

            if (isset($formData['nbutton']))
            {
                $stage = $workData['stage'] + 1;
            }
            elseif (isset($workData['stage']))
                $stage = $workData['stage']; 
            for ($i = 1; $i < 7; $i++)
            {
                $button = 's' . $i . 'button';
                if (isset($formData[$button]))
                {
                    $stage = $i;
                    break;
                }
            }


            if (isset($formData['zbutton']))
            {
                $this->finalStage($this, $workData, 'track');

            }


//            if (isset($workData['childid']))
//                $childid = $workData['childid'];

        }
        
        if (!isset($stage))
            $stage = 1;
        if ($stage <> 3)
            $view = 'enrollstage' . $stage;    
        else
            $view = 'trackstage' . $stage;    
        switch ($stage)
        {

    /* stage 1 - child information */
            case 1:
                if (isset($formData))
                    $form = $formData;
                else
                    $form = null;
                $this->stage1($this, $form, $workData, $newForm, $existingForm, 'track');
              
                break;

    /* stage 2 - Contact information  */
            case 2:
                $newForm = new Application_Form_REEnroll2p();
                $newdForm = new Application_Form_REEnroll2pd();
                $newaForm = new Application_Form_REEnroll2a();
                $newadForm = new Application_Form_REEnroll2ad();
                $this->stage2($this, $formData, $workData, $newForm, $newdForm, $newaForm, $newadForm);
                
                break;

    /* stage 3 - Review and Submit */
            case 3:
                $recid = $workData['recid'];
                $reg = $remap->find($recid);

//var_dump($workData); 

                $workData['stage'] = 3;
                $this->view->data = $workData;
                break;
       }

        if ($stage == 3)
            $nextForm = new Application_Form_REEnrollfinal();
        else   
            $nextForm = new Application_Form_REEnrollnext();
        $values = implode('|', $workData);
        $keys = implode('|', array_keys($workData));
        $nextForm->values->setValue($values);
        $nextForm->keys->setValue($keys);
        $this->view->nextform = $nextForm;
        if ($stage == 2)
        {
            $newdForm->values->setValue($values);
            $newdForm->keys->setValue($keys);
            $newaForm->values->setValue($values);
            $newaForm->keys->setValue($keys);
            $newadForm->values->setValue($values);
            $newadForm->keys->setValue($keys);
        }
        
        if ($stage < 3)
        {
            $newForm->values->setValue($values);
            $newForm->keys->setValue($keys);
            $this->view->newform = $newForm;
        }
        
        if (isset($workData['firstname']))
        {
            $this->view->firstname = $workData['firstname'];
            $this->view->lastname = $workData['lastname'];
        }
        
        $this->view->style = 'zform.css';        

//var_dump($view); 

        return $this->render($view);
    }




    public function enrollAction()
    {


        $issues = array('behavissues' => '',
          'develissues' => '', 'langissues' => '', 
          'otherissues' => '', 
          'medications' => '', 
        );
        $issuevalues = array('behavissues' => '',
          'develissues' => '', 'langissues' => '', 
          'otherissues' => '', 
          'medications' => '', 
        );
        $issuenames = array('behavissues' => 'ADD/ADHD/LD/Behavioral Issues',
          'develissues' => 'Developmental/social emotional', 
          'langissues' => 'Hearing/Visual/Speech Language', 
          'otherissues' => 'Other', 
          'medications' => 'Medications', 
        );
        $dbnames = array('Behavioral box' => 'behavissues',
          'Developmental box'=> 'develissues', 
          'Language box' => 'langissues', 
          'Other box' => 'otherissues', 
          'Medications box' => 'medications', 
        );

        $allergies = array('Weeds/Grasses/Pollens' => '',
          'Bee Stings' => '', 'Child Carries Inhaler' => '', 
          'Child carries Epinephrine Pen' => '', 
          'Food Allergies' => '', 
          'Allergy Medications' => '', 
        );

        $items = array('Artistic' => '', 'Athletic' => '', 'Cerebral' => '', 
          'Creative' => '', 'Agitated' => '', 'Embarrassed' => '', 'Follower' => '', 
          'Helpful' => '', 'Idealistic' => '', 'Imaginative' => '', 'Leader' => '', 
          'Musical' => '', 'Quiet' => '', 'Resourceful' => '', 'Temper' => '', 
          'Shy' => '', 'Spirited' => '', 'Solitary' => '', 'Talkative' => '', 
          'Other' => '', 
        );

        $this->admin($this);    
        $existingForm = new Application_Form_REEnroll1exist();
        $newForm = new Application_Form_REEnroll1new();
        $remap = new Application_Model_REMapper();
        $peoplemap = new Application_Model_PeopleMapper();
        $housemap = new Application_Model_HouseholdsMapper();
        $workData = array();
        $data = array();
        $childid = 0;
        $request = $this->getRequest();
//echo "LINE 115 <br>"; var_dump($workData); 

        if ($this->getRequest()->isPost())
        {
            $formData = $request->getParams();
//echo "formData <br>"; var_dump($formData); 
//echo "workData <br>"; var_dump($workData);

            if (isset($formData['keys']))
            {
                $workkeys = explode('|', $formData['keys']);
                $workvalues = explode('|', $formData['values']);
                $workData = array_combine($workkeys, $workvalues);
            }
//echo "workData <br>"; var_dump($workData);
            if (isset($formData['nbutton']))
            {
                $stage = $workData['stage'] + 1;
            }
            elseif (isset($workData['stage']))
                $stage = $workData['stage']; 
            for ($i = 1; $i < 3; $i++)
            {
                $button = 's' . $i . 'button';
                if (isset($formData[$button]))
                {
                    $stage = $i;
                    break;
                }
            }
//echo "STAGE $stage";
            
            if (isset($formData['zbutton']))
            {
                $this->finalStage($this, $workData, 'yes', $allergies, $dbnames, $items, $issues);
                
            }


//            if (isset($workData['childid']))
//                $childid = $workData['childid'];

        }
        
        if (!isset($stage))
            $stage = 1;
        
        $view = 'enrollstage' . $stage;    
//echo "VIEW $view <br>"; exit;
        switch ($stage)
        {

    /* stage 1 - child information */
            case 1:
                if (isset($formData))
                    $form = $formData;
                else
                    $form = null;
                $this->stage1($this, $form, $workData, $newForm, $existingForm, 'yes');
              
                break;

    /* stage 2 - Contact information  */
            case 2:
//echo "STAGE case 2<br>"; 
                $newForm = new Application_Form_REEnroll2p();
                $newdForm = new Application_Form_REEnroll2pd();
                $newaForm = new Application_Form_REEnroll2a();
                $newadForm = new Application_Form_REEnroll2ad();
                $this->stage2($this, $formData, $workData, $newForm, $newdForm, $newaForm, $newadForm);
                break;


    /* stage 3 - Allergies  */
            case 3:
//echo "STAGE case 3<br>"; 
                $recid = $workData['recid'];
                $reg = $remap->find($recid);

            /* Allergies from existing re table */
                if ($reg['allergies'] <> '')
                {
                    $Allergies = explode(',', $reg['allergies']);
//    echo "LINE 552"; var_dump($reg['allergies']);
                    foreach ($Allergies as $Allergy)
                    {
                        $allergies[$Allergy] = 'checked';
                    }
                }
                $foodallergies = $reg['foodallergies'];
                $allergymeds = $reg['allergymeds'];

            /* Overlay with Allergies from workData */
                if (isset($workData['allergies']))
                {
                    $values = explode(',', $workData['allergies']);
                    $i = 0;
                    foreach ($allergies as $key => $value)
                    {
                        $allergies[$key] = $values[$i++];
                    }
                }
//var_dump($allergies); exit;
                if (isset($workData['foodallergies']))
                {
                    $foodallergies = $workData['foodallergies'];
                }
                if (isset($workData['allergymeds']))
                {
                    $allergymeds = $workData['allergymeds'];
                }

            /* Overlay with Allergies from formData */
                if (isset($formData))
                {
//var_dump($workData);
//var_dump($formData); 
                    if (isset($formData['Allergies']))
                    {
                        $Allergies = $formData['Allergies'];
//var_dump($allergies); 
//var_dump($Allergies); 
                        foreach ($Allergies as $Allergy)
                        {
                            $allergies[$Allergy] = 'checked';
                        }
//var_dump($allergies); 
                    }               
                    if (isset($formData['foodallergies']))
                    {
                        $value = $formData['foodallergies'];
                        $foodallergies = $value;
                        $workData['foodallergies'] = $value;
                    }
                    if (isset($formData['allergymeds']))
                    {
                        $value = $formData['allergymeds'];
                        $allergymeds = $value;
                        $workData['allergymeds'] = $value;
                    }
                }                
                $this->view->foodallergies = $foodallergies;
                $this->view->allergymeds = $allergymeds;
                
                $this->view->allergies = $allergies;
                $AllergiesStr = implode(',', $allergies);
                $workData['allergies'] = $AllergiesStr;
                $workData['foodallergies'] = $foodallergies;
                $workData['allergymeds'] = $allergymeds;

    //var_dump($workData); var_dump($formData); exit;        
//echo "ALLERGIES"; var_dump($allergies); 
//echo "END STAGE 3 <br>"; 
                $workData['stage'] = 3;
                break;


    /* stage 4 - Other Health Issues  */
            case 4:
                $recid = $workData['recid'];
                $reg = $remap->find($recid);
                $Issues = explode(',', $reg['health']);
                $names = array('behavissues' => 'ADD/ADHD/LD/Behavioral Issues',
                  'develissues' => 'Developmental/social emotional', 
                  'langissues' => 'Hearing/Visual/Speech Language', 
                  'otherissues' => 'Other', 
                  'medications' => 'Medications', 
                );
                $this->view->names = $names;

            /* Issues from existing re table - used only first time*/
                if ($reg['health'] <> '')
                {
                    $Issues = explode(',', $reg['health']);
//echo $reg['health'], '<br>';
//var_dump($Issues); exit;
                    foreach ($Issues as $Issue)
                    {
                        $key = $dbnames[$Issue];
                        $issues[$key] = 'checked';
                        $value = $reg[$key];
                        $issuevalues[$key] = $value;
                    }
                }

            /* Overlay with Issues from workData */

                if (isset($workData['health']))
                {
                    $values = explode(',', $workData['health']);
                    $i = 0;
                    foreach ($issues as $key => $value)
                    {
                        $value = $values[$i++];
                        $issues[$key] = $value;
                    }
                }


                if (isset($workData['health']))
                {
                    $values = explode(',', $workData['health']);
                    $i = 0;
                    foreach ($issues as $key => $value)
                    {
                        $issues[$key] = $values[$i++];
                        if (isset($workData[$key]))
                        {
                            $value = $workData[$key];
                            $issuevalues[$key] = $value;
                        }
                    }
                }


            /* Overlay with Issues from formData */

                if (isset($formData))
                {
                    if (isset($formData['health']))
                    {
                        $i = 0;
                        $formname = $formData['health'][0];
                        foreach ($issues as $key => $value)
                        {
                            $issues[$key] = '';
                            if ($formname == $key)
                            {
                                $issues[$key] = 'checked';
                                if (isset($formData['health'][++$i]))
                                    $formname = $formData['health'][$i];
                            }
                            
                            $formvalue = $formData[$key];
                            $issuevalues[$key] = $formvalue;
                            $workData[$key] = $formvalue;
                        }

                        foreach ($formData['health'] as $Issue)
                        {
                            $issues[$Issue] = 'checked';
                            $value = $formData[$Issue];
                            $issuevalues[$Issue] = $value;
                            $workData[$Issue] = $value;
//echo "in formData ISSUE $Issue VALUE $value <br>";
                        }
                    }


                }                

//echo "After formData merge <br>";           
//var_dump($issues);
//var_dump($issuevalues);
                $this->view->issuenames = $issuenames;
                $this->view->issuevalues = $issuevalues;
                $this->view->issues = $issues;
                $IssuesStr = implode(',', $issues);
                $workData['health'] = $IssuesStr;
                foreach ($issuevalues as $key => $value)
                {
                    $workData[$key] = $value;
                }

                $workData['stage'] = 4;
                break;




    /* stage 5 - Description  */
            case 5:
                $recid = $workData['recid'];
                $reg = $remap->find($recid);
                if ($reg['characteristics'] <> '')
                {
                    $Items = explode(',', $reg['characteristics']);
                    foreach ($Items as $Item)
                    {
                        $items[$Item] = 'checked';
                    }
                }

                if (isset($formData))
                {
                    if (isset($formData['Items']))
                    {
                        $Items = $formData['Items'];
                        foreach ($Items as $Item)
                        {
                            $items[$Item] = 'checked';
                        }
                    }
                }
                $othertext = $reg['othertext'];
                if (isset($workData['othertext']))
                    $othertext = $workData['othertext'];
                if (isset($formData['othertext']))
                    $othertext = $formData['othertext'];
                if ($items['Other'] <> 'checked')
                    $othertext = '';
                $workData['othertext'] = $othertext;
                $this->view->othertext = $othertext;
               
                $this->view->characteristics = $items;
                $ItemsStr = implode(',', $items);
                $workData['characteristics'] = $ItemsStr;

                $workData['stage'] = 5;
                break;




    /* stage 6 - Finish  */
            case 6:
                $recid = $workData['recid'];
                $reg = $remap->find($recid);

                $items = array('receive', 'discuss', 'insurance', 'insnumber', 'signame');
                foreach ($items as $item)
                {                
                    $value = $reg[$item];
                    if (isset($workData[$item]))
                        $value = $workData[$item];
                    if (isset($formData[$item]))
                        $value = $formData[$item];
                    $workData[$item] = $value;
                    $this->view->$item = $value;
                }

                $workData['stage'] = 6;
                break;



    /* stage 7 - Review and Submit */
            case 7:
                $recid = $workData['recid'];
                $reg = $remap->find($recid);

//var_dump($workData); 
                if (isset($workData['allergies']))
                {
                    $values = explode(',', $workData['allergies']);
                    $i = 0;
                    foreach ($allergies as $key => $value)
                    {
                        $allergies[$key] = $values[$i++];
                    }
                }
                if (isset($workData['foodallergies']))
                {
                    $this->view->foodallergies = $workData['foodallergies'];
                }
                if (isset($workData['allergymeds']))
                {
                    $this->view->allergymeds = $workData['allergymeds'];
                }
                $this->view->allergies = $allergies;
                $this->view->issues = $issues;
                $this->view->items = $items;

                if (isset($workData['health']))
                {
                    $values = explode(',', $workData['health']);
                    $i = 0;
                    foreach ($issues as $key => $value)
                    {
                        $value = $values[$i++];
                        $issues[$key] = $value;
                    }
                }
                if (isset($workData['health']))
                {
                    $values = explode(',', $workData['health']);
                    $i = 0;
                    foreach ($issues as $key => $value)
                    {
//echo "KEY $key <br>";
                        $issues[$key] = $values[$i++];
                        if (isset($workData[$key]))
                        {
                            $value = $workData[$key];
                            $issuevalues[$key] = $value;
                            $workData[$key] = $value;
                        }
                    }
                }
                $this->view->issuenames = array('Behavioral', 'Developmental',
                  'Language', 'Other', 'Medications');
                $this->view->issues = $issues;
                $this->view->issuevalues = $issuevalues;

                if (isset($workData['characteristics']))
                {
                    $values = explode(',', $workData['characteristics']);
                    $i = 0;
                    foreach ($items as $key => $value)
                    {
                        $characteristics[$key] = $values[$i++];
                    }
                }
                $this->view->characteristics = $characteristics;
                if (isset($workData['othertext']))
                {
                    $this->view->othertext = $workData['othertext'];
                }
                
                $items6 = array('receive', 'discuss', 'insurance', 'insnumber', 'signame');
                foreach ($items6 as $item)
                {
                    $items[] = $workData[$item];
                }
                $this->view->items = $items;


                $workData['stage'] = 7;
                $this->view->data = $workData;
                break;
       }

        if ($stage == 7)
            $nextForm = new Application_Form_REEnrollfinal();
        else   
            $nextForm = new Application_Form_REEnrollnext();
        $values = implode('|', $workData);
        $keys = implode('|', array_keys($workData));
        $nextForm->values->setValue($values);
        $nextForm->keys->setValue($keys);
        $this->view->nextform = $nextForm;
        if ($stage == 2)
        {
            $newdForm->values->setValue($values);
            $newdForm->keys->setValue($keys);
            $newaForm->values->setValue($values);
            $newaForm->keys->setValue($keys);
            $newadForm->values->setValue($values);
            $newadForm->keys->setValue($keys);
        }
        
        if ($stage < 3)
        {
            $newForm->values->setValue($values);
            $newForm->keys->setValue($keys);
            $this->view->newform = $newForm;
        }
//echo "STAGE IS 3 at 1296";
        
        if (isset($workData['firstname']))
        {
            $this->view->firstname = $workData['firstname'];
            $this->view->lastname = $workData['lastname'];
        }
        
        $this->view->style = 'zform.css';        
//var_dump($view);
        return $this->render($view);
    }


    public function assignAction()
    {
        $this->admin($this);    
        $Classes = $this->getClasses();
        $this->view->classes = $Classes;
        $remap = new Application_Model_REMapper();
        $peoplemap = new Application_Model_PeopleMapper();

        $request = $this->getRequest();
        if ($this->getRequest()->isPost())
        {
            $getvalues = $request->getParams();
            $class = $getvalues['class'];
            if (!isset($getvalues['id']))
            {
                $this->view->message = "No children selected, nothing to do.";
            }
            else
            {
                $this->view->message = array();
                $ids = $getvalues['id'];
                foreach ($ids as $id)
                {
                    $where = array(
                      array('id', ' = ', $id),
                      );
                    $reg = current($remap->fetchWhere($where));
                    $reg->class = $class;
                    $remap->save($reg);
                    $cid = $reg->childid;
                    $child = $peoplemap->find($cid);
                    $name = $child['firstname'] . ' ' . $child['lastname'];

                    $this->view->message[] = "$name class set to $class.";
                }
            }


        }

        $orWhere = array(
            array('registered', ' = ', 'track'),
            array('registered', ' = ', 'yes'),
            array('registered', ' = ', 'no'),
            );
        $allre = $remap->fetchOrWhere($orWhere);
        $Children = array();
        foreach ($allre as $anre)
        {
            if ($anre->inactive <> 'yes')
            {
                $where = array(
                  array('id', ' = ', $anre->childid),
                  array('inactive', ' <> ', 'yes'),
                  );
                $person = $peoplemap->fetchWhere($where);
                if (count($person) == 1)
                    $Children[] = array($anre->class, $anre->id, current($person));
            }
        }
          function custom_sort($a,$b) {
            return strcmp($a[2]->Lastname . $a[2]->Firstname, $b[2]->Lastname . $b[2]->Firstname);
        }
        usort($Children, "custom_sort");
        $data = array();
        $row = array();
        $theClass = array();
        $n = 0;
        foreach($Children as $child)
        {
            $id = $child[1];
            $name = $child[2]->firstname . ' ' . $child[2]->lastname;
            unset($theClass);
            $theClass[$child[0]] = 'X';
            unset($row);
            foreach ($Classes as $class)
            {
                $row[] = isset($theClass[$class]) ? $theClass[$class] : '';
            }
            $data[] = array($id, $name, $row);
        }
        $this->view->data = $data;
        $this->view->style = 'zform.css';        
    }




    public function attendanceAction()
    {
        $this->admin($this);
        $lastdate = $this->lastSunday();
        $yearmon=substr($lastdate, 0, 7);

        $Tdate = getdate(mktime(0,0,0, date('m'), date('d'),date('Y')));
        $today = $Tdate['wday'];
        $Month = $Tdate['mon'];
        $Year = $Tdate['year'];

        $lastmonth = mktime(0,0,0,$Month-1,1,$Year);
        $prevdate = date("Y-m-d", $lastmonth);
        $prevym = substr($prevdate, 0, 7);

        $attmap = new Application_Model_REAttendanceMapper();
        $gridmap = new Application_Model_WorshipGridMapper();
        $where = array(
            array('servicedate', ' = ', $lastdate),
            array('sunday', ' = ', 'yes'),
            );
        $gridrec = current($gridmap->fetchWhere($where)); 
        $dateid = $gridrec->id;

        $servicedates = "'$prevym-01' AND '$yearmon-31'";

        $gridmap = new Application_Model_WorshipGridMapper();
        $where = array(
            array('servicedate', ' BETWEEN ', $servicedates),
            array('sunday', ' = ', 'yes'),
            );
        $sundays = $gridmap->fetchWhere($where); 

        unset($dates);
        $d = 0;
        $firstdate = '';
        foreach ($sundays AS $day) {
          $dates[$d++] = $day->ServiceDate;
          if ($firstdate == '')
            $firstdate = $day->ServiceDate;
        }
        $this->view->dates = $dates;

        $request = $this->getRequest();
        if ($this->getRequest()->isPost())
        {
            $formData = $request->getParams();
            if (isset($formData['class']))
                $class = $formData['class'];
            if (isset($formData['act']))
                $action = $formData['act'];
            else
                $action = '';            

            if ($action == 'GUEST')
            {
                $guestForm = new Application_Form_REGuest();
                $this->view->guestForm = $guestForm;
                $this->view->style = 'zform.css';        
                return $this->render('guest');
            }
            
            if (isset($formData['ebutton']))
            {
                $peoplemap = new Application_Model_PeopleMapper();
                $child = new Application_Model_People();
                $firstname = $formData['firstname'];
                $lastname = $formData['lastname'];
                $child->firstname = $firstname;
                $child->lastname = $lastname;
                $child->status = 'Child';
                $child->creationdate = $this->today();
                $peoplemap->save($child);
                $id = $child->id;
                $this->view->message = "Created people entry $id for $firstname $lastname.";
                $remap = new Application_Model_REMapper();
                $reg = new Application_Model_RE();
                $reg->childid = $id;
                $reg->registered = 'no';
                $reg->class = 'None';
                $remap->save($reg);
                $rid = $reg->id;
                $this->view->message = "Created registration entry $rid for $firstname $lastname.";
            }


            if ($action == 'MARK'  ||  $action == 'UNMARK')
            {
                unset($attDates);
                if (isset($formData['did']))
                {
                    $did = $formData['did'];
                    foreach ($did as $id)
                    {
                        $theDate = $id;
                        $attDates[] = $theDate;
                    }
                }
    
                unset($attChildren);
                if (isset($formData['cid']))
                {
                    $cid = $formData['cid'];
                    foreach ($cid as $id)
                    {
                        $theChild = $id;
                        $attChildren[] = $theChild;
                    }
                }
    
                $this->view->message = array();
                
                if (!isset($attDates))
                    $this->view->message[] = "No date selected, nothing done.";
                elseif (!isset($attChildren))
                    $this->view->message[] = "No children selected, nothing done.";
                else
                    foreach($attDates as $date)
                    {
                        foreach($attChildren as $child)
                        {
                            if ($action == 'MARK')
                            {
                                $att = new Application_Model_REAttendance();
                                $att->childid = $child;
                                $att->date = $date;
                                $attmap->save($att);
                                $this->view->message[] = "Added $date for ID $child.";
                            }
                            else
                            {
                                $where = array(
                                    array('date', ' >= ', $date),
                                    array('childid', ' >= ', $child),
                                    );
                                $att = current($attmap->fetchWhere($where));
                                if ($att == false)
                                {
                                    $this->view->message[] = "Nonexistent $date for ID $child.";
                                }
                                else
                                {
                                $this->view->message[] = "Removed $date for ID $child.";
                                }
                            }
                        }
                    }
            }  
        }


        $where = array(
            array('date', ' >= ', $firstdate),
            );
        $att = $attmap->fetchWhere($where);

        if (count($att) > 0)
            foreach ($att as $row)
            {
                if (!isset($attCount[$row->childid]))
                    $attCount[$row->childid] = 0;
                $attend[$row->childid][$attCount[$row->childid]] = $row->Date;
                $attCount[$row->ChildID]++;
              }
        $this->view->attend = $attend;
        
        if (!isset($class))
            $class = 'None';  
        $this->view->class = $class;
        $Classes = $this->getClasses();
        $Classes[-1] = 'All';
        unset($theClass);
        $theClass[$class] = 'checked';
        $this->view->theclass = $theClass;
        $this->view->classes = $Classes;

        $request = $this->getRequest();
        if ($class <> 'All')
            $where = array(
                array('class', ' = ', $class),
                array('inactive', ' <> ', 'yes'),
                );
        else
            $where = array(
                array('inactive', ' <> ', 'yes'),
                );
        $remap = new Application_Model_REMapper();
        $Children = $remap->fetchWhere($where);
        $peoplemap = new Application_Model_PeopleMapper();

        $data = array();
        foreach($Children as $child)
        {
            $cid = $child->childid;
            $rec = $peoplemap->find($cid);
            $data[] = array($cid, $rec['firstname'], $rec['lastname'], $child->class);
        }
          function custom_sort($a,$b) {
            return strcmp($a[1] . $a[2], $b[1] . $b[2]);
        }
        usort($data, "custom_sort");
        $this->view->data = $data;        
        $this->view->style = 'zform.css';        
    }






    public function attreportAction()
    {
        $this->admin($this);
        $lastdate = $this->lastSunday();

        $year = substr($lastdate, 0, 4);
        $month = substr($lastdate, 5, 2);
        if ($month < 7)
        {
            $year4 = $year[3];
            $year4 -= 1;
            $year = substr($year, 0, 3) . $year4;
        }
        $startdate = $year . "-07-01";
        $yearmon = substr($lastdate, 0, 7);
        $servicedates = "'$startdate' AND '$yearmon-31'";

        $attmap = new Application_Model_REAttendanceMapper();
        $gridmap = new Application_Model_WorshipGridMapper();
        $where = array(
            array('servicedate', ' BETWEEN ', $servicedates),
            array('sunday', ' = ', 'yes'),
            );
        $sundays = $gridmap->fetchWhere($where); 

        unset($dates);
        unset($attendance);
        $firstdate = '';
        foreach ($sundays AS $day) 
        {
          $attendance[] = 0;
          $dates[] = $day->ServiceDate;
          if ($firstdate == '')
            $firstdate = $day->ServiceDate;
        }
        $this->view->dates = $dates;
        $this->view->attendance = $attendance;

        $request = $this->getRequest();
        if ($this->getRequest()->isPost())
        {
            $formData = $request->getParams();
            if (isset($formData['class']))
                $class = $formData['class'];
        }
        if (!isset($class))
            $class = 'None';
            
        $where = array(
            array('date', ' >= ', $firstdate),
            );
        $att = $attmap->fetchWhere($where);

        foreach ($att as $row)
        {
            if (!isset($attCount[$row->ChildID]))
                $attCount[$row->ChildID] = 0;
            $attend[$row->ChildID][$attCount[$row->ChildID]] = $row->Date;
            $attCount[$row->ChildID]++;
        }
        $this->view->attend = $attend;

        if (!isset($class))
            $class = 'None';  
        $this->view->class = $class;
        $Classes = $this->getClasses();
        $Classes[-1] = 'All';
        unset($theClass);
        $theClass[$class] = 'checked';
        $this->view->theclass = $theClass;
        $this->view->classes = $Classes;


        $request = $this->getRequest();
        if ($class <> 'All')
            $where = array(
                array('class', ' = ', $class),
                array('inactive', ' <> ', 'yes'),
                );
        else
            $where = array(
                array('inactive', ' <> ', 'yes'),
                );
        $remap = new Application_Model_REMapper();
        $Children = $remap->fetchWhere($where);
        $peoplemap = new Application_Model_PeopleMapper();


        $data = array();
        foreach($Children as $child)
        {
            $cid = $child->childid;
            $rec = $peoplemap->find($cid);
            $data[] = array($cid, $rec['firstname'], $rec['lastname'], $child->class);
        }
          function custom_sort($a,$b) {
            return strcmp($a[1] . $a[2], $b[1] . $b[2]);
        }
        usort($data, "custom_sort");
        $this->view->data = $data;        
        $this->view->style = 'zform.css';        
    }
    
    
    
    public function maintainAction()
    {
        $this->_redirect("/reaux/maintain");
    }
    
    
    public function listparentsAction()
    {
        $this->_redirect("/reaux/listparents");
    }
    
    public function childrenAction()
    {

    }
    
    public function adultsAction()
    {

    }
    
}

