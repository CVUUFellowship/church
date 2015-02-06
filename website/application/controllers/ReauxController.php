<?php

class ReauxController extends Zend_Controller_Action
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

            
        public function today()
        {
            return(date("Y-m-d"));    
        }

            
        public function name($personarray)
        {
            return($personarray['firstname'] . ' ' . $personarray['lastname']);    
        }

            
        public function address($personarray)
        {
            return($personarray['street'] . ', ' . $personarray['city'] . ', ' .
              $personarray['state'] . ' ' . $personarray['zip']);    
        }

            
        public function childname($reg)
        {
            $peoplemap = new Application_Model_PeopleMapper();
            $child = $peoplemap->find($reg->childid);
            return($child['firstname'] . ' ' . $child['lastname']);
        }
    
    
    public function listparentsAction()
    {
        $this->admin($this);
        $remap = new Application_Model_REMapper();
        $peoplemap = new Application_Model_PeopleMapper();
        $housemap = new Application_Model_HouseholdsMapper();

        $request = $this->getRequest();
        if ($this->getRequest()->isPost())
            $getvalues = $request->getParams();
        if (isset($getvalues['Format']))
            $Format = $getvalues['Format'];
        else 
            $Format = 'HTML';       
        if ($Format <> 'HTML')
        {
            require_once 'EzPDF/class.ezpdf.php';
            $pdf = new Cezpdf('LETTER', 'landscape');
            $pdf->selectFont('./fonts/Helvetica.afm');
        }
echo "FORMAT set to $Format <br>";        
        $where = array(
            array('inactive', ' <> ', 'yes'),
            );
        $allreg = $remap->fetchWhere($where);
        $parents = array();
        
        $parentids = array();
        foreach($allreg as $reg)
        {
            $childid = $reg->childid;
            $regid = $reg->id;
            if (($parentid = ($reg->ppersonid)) > 0)
                $parentids[] = array($childid, $parentid, $regid);
            if (($parentid = ($reg->apersonid)) > 0)
                $parentids[] = array($childid, $parentid, $regid);
        }
//var_dump($parentids); 
        foreach ($parentids as $id)
        {
            $parent = $peoplemap->find($id[1]);
            $house = $housemap->find($parent['householdid']);
            $regid = $id[2];
            $childid = $id[0];
            $child = $peoplemap->find($childid);
            $parents[] = array($childid, $this->name($child), $regid, $parent['id'], $parent['firstname'], $parent['lastname'], 
              $parent['status'], $parent['email'], $house['phone'], $this->address($house),
              );
        }
          function custom_sort2($a,$b) {
            return strcmp($a[5] . $a[4], $b[5] . $b[4]); }
        usort($parents, 'custom_sort2');

        if ($Format <> 'HTML')
        {
            $table = array();
            $prev = -1;
            foreach ($parents as $line)
            {
                if ($prev == $line[4])
                {
                    $table[] = array('ID'=>'',
              		    'Name'=>'', 'Status'=>'',
              		    'Email'=>'','Phone'=>'',
              		    'Address'=>'', 'ChildID'=>'',
              		    'ChildName'=>$line[1], 'RegID'=>$line[2]);
                }
                else
                {
                    $table[] = array('ID' => $line[3],
              		    'Name' => $line[4] . ' ' . $line[5], 'Status' => $line[6],
              		    'Email' => $line[7], 'Phone' => $line[8],
              		    'Address' => $line[9], 'ChildID' => $line[0],
              		    'ChildName' => $line[1], 'RegID' => $line[2]);
                }
                $prev = $line[4];
            }
//var_dump($table); exit;
          	$pdf->ezTable($table, '', $this->today() . " RE Parents", array('showHeadings' => 1, 'shaded' => 1,
          		'showLines' => 1, 'fontSize' => 7, 'titleFontSize' => 12));
          	$pdf->ezStream();
            exit;
        }

//var_dump($parents); 
        $this->view->parents = $parents;
        $this->view->style = 'zform.css';        
    }

    
    
    public function maintainAction()
    {
        $this->admin($this);
        $remap = new Application_Model_REMapper();
        $peoplemap = new Application_Model_PeopleMapper();
        $housemap = new Application_Model_HouseholdsMapper();
        $this->view->message = array();
                
        $where = array(
            array('inactive', ' <> ', 'yes'),
            );
        $allreg = $remap->fetchWhere($where);
        $regs = count($allreg);
        foreach($allreg as $reg)
        {
            $regid = $reg->id;
            $childid = $reg->childid;
            $child = $peoplemap->find($childid);
            $childname = $this->name($child);
            $childstatus = $reg->registered;
            $pri = $reg->ppersonid;
            if ($pri < 1)
                $this->view->message[] = "No primary contact in registration record for $childname ID $regid registration: $childstatus.";

            elseif ($child['householdid'] < 1)
                $this->view->message[] = "No household ID in people record for $childname ID $regid registration: $childstatus.";
            
            $where = array(
              array('id', ' = ', $childid),
              array('firstname', ' = ', $child['firstname']),
              array('lastname', ' = ', $child['lastname']),
              );
            $found = $peoplemap->fetchWhere($where);
            if (count($found) > 1)
            {
                foreach ($found as $person)
                {
                    if ($person->id <> $childid)
                    {
                        $this->view->message[] = "People record with ID $person->id has same name as $childname ID $childid.";
                    }
                }
            }
            
            if ($child['inactive'] <> 'no')
                $this->view->message[] = "Person record marked inactive for $childname ID $regid registration: $childstatus.";
    
            if ($reg->signame <> '')
                if ($reg->registered <> 'yes'  &&  $reg->registered <> 'track')
                    $this->view->message[] = "Registration record signed but status not registered for $childname ID $regid registration: $childstatus.";
        }
        $count = count($this->view->message);
        if ($count == 0)
            $this->view->message[] = "No problems discovered in $regs records checked.";
        else
            $this->view->message[] = "$count problems discovered in $regs records checked.";

        $request = $this->getRequest();
        if ($this->getRequest()->isPost())
        {
            $formData = $request->getParams();
            if (isset($formData['show']))
                $show = $formData['show'];
            elseif (isset($formData['todo']))
                {
                    $action = $formData['todo'];
                    switch($action)
                    {
                      case 'ACTIVE':
                      case 'INACTIVE':
                        if (!isset($formData['id']))
                            $this->view->message[] = "No child selected, nothing to do.";
                        else
                        {
                            $ids = $formData['id'];
                            foreach ($ids as $id)
                            {
                                $where = array(
                                  array('id', ' = ', $id),
                                );
                                $reg = current($remap->fetchWhere($where));
                                if ($action == 'ACTIVE')
                                    $reg->inactive = 'no';
                                else
                                    $reg->inactive = 'yes';
                                $remap->save($reg);
                                $this->view->message[] = $this->childname($reg) . " set to $action.";
                                
                            }
                        }
                      
                      case 'ENROLL':
                      case 'UNENROLL':
                        if (!isset($formData['id']))
                            $this->view->message[] = "No child selected, nothing to do.";
                        else
                        {
                            $ids = $formData['id'];
                            foreach ($ids as $id)
                            {
                                $where = array(
                                  array('id', ' = ', $id),
                                );
                                $reg = current($remap->fetchWhere($where));
                                $sig = $reg->signame;
                                if ($action == 'ENROLL')
                                {
                                    if ($sig <> '')
                                        $this->view->message[] = $this->childname($reg) . " is already enrolled.";
                                    else
                                    {
                                        $reg->signame = 'DRE';
                                        $this->view->message[] = $this->childname($reg) . " now signed by DRE.";
                                    }
                                }
                                else
                                    if ($sig == '')
                                        $this->view->message[] = $this->childname($reg) . " is already not enrolled.";
                                    else
                                    {
                                        $reg->signame = '';
                                        $this->view->message[] = $this->childname($reg) . " signature removed.";
                                    }
                                $remap->save($reg);                               
                            }
                        }
                      
                    
                    }
                }
        }


        $this->view->shows = array('Normal', 'Inactive', 'Enrolled');
      	unset($where);
        if (isset($show)) 
        {
        		switch($show) {
        			case 'Normal':
        				$where = array(array('inactive', ' <> ', 'yes'));
        				$what="All Active";
        				break;
        			case 'Inactive':
        				$where = array(array('inactive', ' = ', 'yes'));
        				$what = "Inactive only";
        				break;
        			case 'Enrolled':
        				$where = array(
                  array('inactive', ' <> ', 'yes'),
                  array('signame', ' <> ', ''),
                  );
        				$what="Active and Enrolled";
        				break;
        		}
      	}
        else
        {
        		$where = array(array('inactive', ' <> ', 'yes'));
        		$what = "All Active";
            $show = 'Normal';
        }
        $theshow[$show] = true;
        $this->view->theshow = $theshow;
        $data = array();
        $row = array();
        $regs = $remap->fetchWhere($where);
        foreach ($regs as $reg)
        {
            unset($row);
            $rid = $reg->id;
//var_dump($reg);
            $row['id'] = $rid;
            $cid = $reg->childid;
            $person = $peoplemap->find($cid);
            $row['firstname'] = $person['firstname'];
            $row['lastname'] = $person['lastname'];
            $row['name'] = $this->name($person);
            $row['signed'] = $reg->signame;
            $row['registered'] = $reg->registered;
            
            $data[] = $row;
        }
          function custom_sort3($a,$b) {
            return strcmp($a['lastname'] . $a['firstname'], $b['lastname'] . $b['firstname']); }
        usort($data, 'custom_sort3');

        $this->view->what = $what;        
        $this->view->data = $data;        
        $this->view->style = 'zform.css';        
    }



    
    
    public function showregAction()
    {
        $this->admin($this);
        $remap = new Application_Model_REMapper();
        $peoplemap = new Application_Model_PeopleMapper();
        
        $request = $this->getRequest();
        if ($this->getRequest()->isGet())
        {
            $getvalues = $request->getParams();
            $regid = $getvalues['id'];
        }
        
        $data = array();
        $reg = $remap->find($regid);
        foreach ($reg as $key => $value)
        {
            $name = null;
            if (in_array($key, array('childid', 'ppersonid', 'apersonid')))
            {
                $pid = $reg[$key];
                if ($pid > 0)
                {
                    $person = $peoplemap->find($pid);
                    $name = $this->name($person);
                }
                
            }
            $data[] = array($key, $value, $name);
        }
        $this->view->data = $data;
    }
        



    
    
    public function showregpagesAction()
    {
        $this->admin($this);
        $remap = new Application_Model_REMapper();
        $peoplemap = new Application_Model_PeopleMapper();
        $housemap = new Application_Model_HouseholdsMapper();

        require_once 'EzPDF/class.ezpdf.php';
        $pdf = new Cezpdf('LETTER', 'landscape');
        $pdf->selectFont('./fonts/Helvetica.afm');

				$where = array(
          array('inactive', ' <> ', 'yes'),
          array('signame', ' <> ', ''),
          );
        $kids = $remap->fetchWhere($where);

        $HEADER = "<b>*** PRIVATE AND CONFIDENTIAL ***   CVUUF Religious Education Directory</b>          ";
        $HEADER = $HEADER . $this->today();
        
        foreach ($kids as $kid) {
          	$pdf->ezText($HEADER,12);
          	$info = "\n" . $this->childname($kid);
          	$info = $info . "  RecordID: " . $kid->id;
          	$pdf->ezText($info, 12);

          	$info = "    Birthdate: "  . $kid->birth . "  Grade: " . $kid->grade.
          		"  [" . $kid->Gender . "]";
          	$pdf->ezText($info, 10);
          	
          	$pri = $peoplemap->find($kid->ppersonid);
            $info = "\n<b>Primary Contact:</b> " . $this->name($pri);
          	$pdf->ezText($info, 10);
            
            $house = $housemap->find($pri['householdid']);
            $info = "    Home Phone: " . $house['phone'];
            if ($pri['pphone'] <> '')
                $info = $info . "  Cell: " . $pri['pphone'];
          	$pdf->ezText($info, 10);
          	
            $info = "    Email: " . $pri['email'];
          	$pdf->ezText($info,10);
          	
            if ($kid->apersonid>0)
            {
              	$alt = $peoplemap->find($kid->apersonid);
                $info = "\n<b>Alternate Contact:</b> " . $this->name($alt);
              	$pdf->ezText($info, 10);

                $house = $housemap->find($alt['householdid']);
                $info = "    Home Phone: " . $house['phone'];
                if ($alt['pphone'] <> '')
                    $info = $info . "  Cell: " . $alt['pphone'];
              	$pdf->ezText($info, 10);
              	
                $info = "    Email: " . $alt['email'];
            }
            else
                $info = "\n<b>Alternate Contact:</b> none";
          	$pdf->ezText($info,10);
          
          	$info="\n<b>Allergies:</b> " . $kid->allergies;
          		  $pdf->ezText($info, 10);
          	if ($kid->foodallergies) 
            {
            		$info="    Food Allergies: " . $kid->foodallergies;
            		$pdf->ezText($info, 10);
          	}
          	if ($kid->allergymeds) 
            {
          		$info="    Allergy Meds: " . $kid->allergymeds;
          		$pdf->ezText($info,10);
          	}
          
          	$info = "\n<b>Health Info:</b> " . $kid->health;
          		  $pdf->ezText($info,10);
          	
            if ($kid->behavissues) 
            {
            		$info = "    Behavior: " . $kid->behavissues;
            		$pdf->ezText($info, 10);
          	}
          	if ($kid->DevelIssues) 
            {
            		$info = "    development: " . $kid->develissues;
            		$pdf->ezText($info, 10);
          	}
          	if ($kid->langissues) {
            		$info = "    Language: " . $kid->langissues;
            		$pdf->ezText($info, 10);
          	}
          	if ($kid->otherissues) {
            		$info = "    Other: " . $kid->otherissues;
            		$pdf->ezText($info, 10);
          	}
          	if ($kid->medications) {
            		$info = "    Medications: " . $kid->medications;
            		$pdf->ezText($info, 10);
          	}
          
          	$info = "\n<b>Characteristics:</b> " . $kid->characteristics;
          		$pdf->ezText($info, 10);
          
          	if ($kid->othertext) {
            		$info = "\n<b>Other Info:</b> " . $kid->othertext;
            		$pdf->ezText($info, 10);
          	}
          
          	$info = "\n<b>To Receive:</b> " . $kid->receive;
          	$pdf->ezText($info, 10);
          		
          	$info = "\n<b>Discuss?:</b> " . $kid->discuss;
          	$pdf->ezText($info, 10);
          
          	$info = "\n<b>Insurance:</b> " . $kid->insurance;
          	$pdf->ezText($info, 10);
          	$info = "    Policy Number: " . $kid->insnumber;
          	$pdf->ezText($info, 10);
          
          	$info = "\n<b>Signed:</b> " . $kid->signame;
          		$pdf->ezText($info, 10);
//exit;          
          	$pdf->ezNewPage();
        }
        
        $pdf->ezStream();
        exit;
    }
                
        

}