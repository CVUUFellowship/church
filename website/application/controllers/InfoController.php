<?php

class InfoController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->theme = 'private';        /* Initialize action controller here */
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



    public function chartersgroupsAction()
    {

            $label['name']="Group name";
            $label['type']="Type of group";
            $label['nmem']="Maximum number of members";
            $label['purp']="What is the group's purpose/goal and how does it support the CVUUF Mission?";
            $label['incl']="Policy on inclusivity?";
            $label['conf']="Policy on confidentiality?";
            $label['meet']="What are the desired days/times/frequency of the group's meetings?";
            $label['mloc']="Where will the group meet?";
            $label['nonc']="Policy on participation by individuals who are not members/friends of the Fellowship?";
            $label['appr']="Approval date";
            $this->view->label = $label;

        $chmap = new Application_Model_ChartersGroupsMapper();
        $action = '';
        $lock = 'yes';

        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $postvalues = $request->getParams();

            if (isset($postvalues['todo']))
            {
                $action = $postvalues['todo'];
                
                if (isset($postvalues['lock']))
                {
                    $lock = $postvalues['lock'];
                    $recid = $postvalues['recid'];
                    $newold = $postvalues['newold'];
                }

                if ($action == 'REMOVE')
                {
                    $recid = 0;
                    if (isset($postvalues['id'])) 
                    {
                        $vid = $postvalues['id'];
                        $recid = $vid[0];
                        $chmap->delete($recid);
                        $this->view->message = "Charter ID $recid removed from database.";
                        $action = '';
                    }
                    else
                    {
                        $this->view->message = "No charter selected.";
                    }
                    
                }


                if ($action == 'GET' || $action == 'PRINT' || $action == 'UNLOCK')
                {
                    $newold='old';                   
                    $recid = 0;
                    if (isset($postvalues['id'])) 
                    {
                      $vid = $postvalues['id'];
                      $recid = $vid[0];
                    }
                    if ($recid < 1)
                    {
                        if (isset($postvalues['recid'])) 
                            $recid = $postvalues['recid'];
                    }
                    if ($recid > 0) 
                    {
                        
                        $where = array(
                            array('id', ' = ', $recid),
                            );
                        $charter = $chmap->fetchWhere($where);
                        if (count($charter) > 0) 
                        {
                            $charter = current($charter);
                            $name = $charter->groupname;
                            $type = $charter->grouptype;
                            $purp = $charter->purpose;
                            $incl = $charter->inclusivepolicy;
                            $conf = $charter->confidentialitypolicy;
                            $nmem = $charter->numbermembers;
                            $mloc = $charter->meetinglocation;
                            $meet = $charter->meetings;
                            $nonc = $charter->noncvuufpolicy;
                            $appr = $charter->approvaldate;
                        }
                        else
                            $this->view->message = "Charter $recid not found.";      
                    }
                    else 
                    {
                        $this->view->message = "No charter selected.";
                        $action = '';
                    }
                }


                if ($action=='PRINT')
                {
                    require_once 'EzPDF/class.ezpdf.php';
                    $pdf = new Cezpdf('LETTER', 'portrait');
                    $pdf->selectFont('./data/fonts/Helvetica.afm');
              
                    $pdf->ezText("Small Group Charter - ".$name, NULL, array('justification'=>'center'));
                    $pdf->ezText("Approved ".$appr, NULL, array('justification'=>'center'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['name'].'</b>');
                    $pdf->ezText($name, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['type'].'</b>');
                    $pdf->ezText($type, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['nmem'].'</b>');
                    $pdf->ezText($nmem, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['purp'].'</b>');
                    $pdf->ezText($purp, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['incl'].'</b>');
                    $pdf->ezText($incl, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['conf'].'</b>');
                    $pdf->ezText($conf, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['mloc'].'</b>');
                    $pdf->ezText($mloc, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['meet'].'</b>');
                    $pdf->ezText($meet, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['nonc'].'</b>');
                    $pdf->ezText($nonc, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                  
                    $pdf->ezStream();
                    exit;
                } 

                        
                if ($action == 'DIR')
                    $action = '';

    
                if ($action == 'FILE')
                {
                    $name = $postvalues['name'];
                    $typearray = $postvalues['type'];
                   	$type = implode(",", $typearray);
                    $purp = $postvalues['purp'];
                    $inclarray = $postvalues['incl'];
                   	$incl = implode(",", $inclarray);
                    $conf = $postvalues['conf'];
                    $nmem = $postvalues['nmem'];
                    $mloc = $postvalues['mloc'];
                    $meet = $postvalues['meet'];
                    $nonc = $postvalues['nonc'];
                    $appr = $postvalues['appr'];
                }
                
                if ($action == 'UNLOCK' || $action == 'FILE')
                {
                    if ($recid < 1)
                        $recid = $postvalues['recid'];
                    $where = array(
                        array('id', ' = ', $recid),
                        );
                    $charter = current($chmap->fetchWhere($where));
                }
                
                if ($action == 'UNLOCK')
                    $lock = 'no';
                elseif ($lock <> 'yes')
                    $lock = $postvalues['lock'];
          
                if ($action == 'FILE')
                {
                    $charter->groupname = $name;
                    $charter->grouptype = $type;
                    $charter->purpose = $purp;
                    $charter->inclusivepolicy = $incl;
                    $charter->confidentialitypolicy = $conf;
                    $charter->numbermembers = $nmem;
                    $charter->meetinglocation = $mloc;
                    $charter->meetings = $meet;
                    $charter->noncvuufpolicy = $nonc;
                    $charter->approvaldate = $appr;
                    
                    $chmap->save($charter);
                    $lock = 'yes';
                    $where = array(
                        array('id', ' = ', $recid),
                        );
                    $charter = current($chmap->fetchWhere($where));
                }
                
                if ($action == 'UNLOCK')
                {
                    $type = $charter->grouptype;
                }


                if ($action == 'NEW') 
                {
                    $newold = 'old';
                    $lock = 'no';
                    $name = $purp = $incl = $conf = $nmem = $mloc = $meet = $nonc = $appr = $type = '';

                    $charter = new Application_Model_ChartersGroups();
                    $charter->groupname = $name;
                    $charter->grouptype = $type;
                    $charter->purpose = $purp;
                    $charter->inclusivepolicy = $incl;
                    $charter->confidentialitypolicy = $conf;
                    $charter->numbermembers = $nmem;
                    $charter->meetinglocation = $mloc;
                    $charter->meetings = $meet;
                    $charter->noncvuufpolicy = $nonc;
                    $charter->approvaldate = $appr;
                    
                    $chmap->save($charter);
                    $recid = $charter->id;
                }
            }

            if ($lock == 'yes') {
                $readonly = 'disabled';
                $disabled = 'disabled';
                $textstyle = 'STYLE="background-color: white;"';
            }  
            else {
                $readonly = '';
                $disabled = '';
                $textstyle = '';
            }
           

            if ($action <> '')
            {
                $this->view->recid = $recid;
                $this->view->lock = $lock;
                $this->view->newold = $newold;
                $this->view->type = $type;
                $this->view->readonly = $readonly;
                $this->view->disabled = $disabled;
                $this->view->textstyle = $textstyle;
                $this->view->charter = $charter;
                $this->view->style = 'zform.css';
                return ($this->render('showchartergroups'));                
            }
        }

        $charters = $chmap->fetchAll();
        
        function custom_sort($a,$b) {
            return strcmp($a->groupname, $b->groupname);
        }
        usort($charters, "custom_sort");

        $this->view->charters = $charters;              
        $this->view->style = 'zform.css';
    }



    public function chartersorgAction()
    {

           
        $label['name']="What is the formal name of your group, committee, or key contributor?";
        $label['type']="Type of organization";
        $label['nmem']="Maximum number of members";
        $label['purp']="Describe the key purpose(s) of your group or committee in two or three sentences.  (Describe what you are trying to accomplish in terms of goals rather than how you will accomplish them.)";
        $label['orgn']="Describe in one or two sentences how your group fits within the Fellowship's organization. ".  
"(For example, \"The Outreach Group is established by the Board of Trustees and is one of the six principal ".
"operating Groups of the Fellowship.\")";
        $label['lsel']="Describe how your group or committee leader (e.g. Group Director or Chair) is ". 
"chosen.  (For example, \"The Group Director is appointed by the Board of Trustees\"  or \"The Chair of ".
"the Committee is elected by the Committee members\").";
        $label['ltrm']="What is the term of service for your leader?";
        $label['mtrm']="What is the term of service for your members?";
        $label['mnum']="Describe any limits on the number of members in your Group or Committee (if there are none, specify as such).";
        $label['rpto']="If the Group or Committee will make regular reports to another governing body, please specify the ".
"identity of the body.   For example, the \"Group Director ".
"will make regular reports to the Council of Directors\" or \"the Committee chair will make regular reports to ".
"the [Group] or [Group Director].\"";
        $label['meet']="Specify how often your Group or Committee will meet (For example, once a month, once a quarter or ".
"as needed if that is the case).";
        $label['dtes']="Specify the key duties and responsibilities of your Group or Committee.  ".
"(In this section, you should list specific activities ".
"that your Group or Committee undertakes, such as planning worship services, reserving ".
"facilities etc.)";
        $label['appr']="Approval date";
        $this->view->label = $label;
        $chmap = new Application_Model_ChartersOrgMapper();
        $action = '';
        $lock = 'yes';

        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $postvalues = $request->getParams();

            if (isset($postvalues['todo']))
            {
                $action = $postvalues['todo'];
                
                if (isset($postvalues['lock']))
                {
                    $lock = $postvalues['lock'];
                    $recid = $postvalues['recid'];
                    $newold = $postvalues['newold'];
                }

                if ($action == 'REMOVE')
                {
                    $recid = 0;
                    if (isset($postvalues['id'])) 
                    {
                        $vid = $postvalues['id'];
                        $recid = $vid[0];
                        $chmap->delete($recid);
                        $this->view->message = "Charter ID $recid removed from database.";
                        $action = '';
                    }
                    else
                    {
                        $this->view->message = "No charter selected.";
                    }
                    
                }


                if ($action == 'GET' || $action == 'PRINT' || $action == 'UNLOCK')
                {
                    $newold='old';                   
                    $recid = 0;
                    if (isset($postvalues['id'])) 
                    {
                      $vid = $postvalues['id'];
                      $recid = $vid[0];
                    }
                    if ($recid < 1)
                    {
                        if (isset($postvalues['recid'])) 
                            $recid = $postvalues['recid'];
                    }
                    if ($recid > 0) 
                    {
                        $where = array(
                            array('id', ' = ', $recid),
                            );
                        $charter = current($chmap->fetchWhere($where));
//var_dump($charter);
//exit;
                        if ($charter <> false) 
                        {
                            $name = $charter->name;
                            $type = $charter->type;
                            $purp = $charter->purpose;
                            $orgn = $charter->organization;
                            $lsel = $charter->leaderselection;
                            $ltrm = $charter->leaderterm;
                            $mtrm = $charter->memberterm;
                            $mnum = $charter->numbermembers;
                            $rpto = $charter->reportto;
                            $meet = $charter->meetings;
                            $dtes = $charter->duties;
                            $appr = $charter->approvaldate;
                        }
                        else
                            $this->view->message = "Charter $recid not found.";      
                    }
                    else 
                    {
                        $this->view->message = "No charter selected.";
                        $action = '';
                    }
                }


                if ($action == 'PRINT')
                {
//var_dump($charter);
//exit;                
                    require_once 'EzPDF/class.ezpdf.php';
                    $pdf = new Cezpdf('LETTER', 'portrait');
                    $pdf->selectFont('./data/fonts/Helvetica.afm');
              
                    $pdf->ezText("Charter - ".$name, NULL, array('justification'=>'center'));
                    $pdf->ezText("Approved ".$appr, NULL, array('justification'=>'center'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['name'].'</b>');
                    $pdf->ezText($name, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['type'].'</b>');
                    $pdf->ezText($type, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['purp'].'</b>');
                    $pdf->ezText($purp, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['orgn'].'</b>');
                    $pdf->ezText($orgn, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['lsel'].'</b>');
                    $pdf->ezText($lsel, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['ltrm'].'</b>');
                    $pdf->ezText($ltrm, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['mtrm'].'</b>');
                    $pdf->ezText($mtrm, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['mnum'].'</b>');
                    $pdf->ezText($mnum, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['rpto'].'</b>');
                    $pdf->ezText($rpto, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['meet'].'</b>');
                    $pdf->ezText($meet, NULL, array('left'=>'20'));
                    $pdf->ezText(" ");
                    $pdf->ezText('<b>'.$label['dtes'].'</b>');
                    $pdf->ezText($dtes, NULL, array('left'=>'20'));
              
                    $pdf->ezStream();
                    exit;      
                } 

                        
                if ($action == 'DIR')
                    $action = '';

    
                if ($action == 'FILE')
                {
                    $name = $postvalues['name'];
                    $typearray = $postvalues['type'];
                   	$type = implode(",", $typearray);
                    $purp = $postvalues['purp'];
                    $orgn = $postvalues['orgn'];
                    $lsel = $postvalues['lsel'];
                    $ltrm = $postvalues['ltrm'];
                    $mtrm = $postvalues['mtrm'];
                    $nmem = $postvalues['nmem'];
                    $rept = $postvalues['rept'];
                    $meet = $postvalues['meet'];
                    $dtes = $postvalues['dtes'];
                    $appr = $postvalues['appr'];
                }
                
                if ($action == 'UNLOCK' || $action == 'FILE')
                {
                    if ($recid < 1)
                        $recid = $postvalues['recid'];
                    $where = array(
                        array('id', ' = ', $recid),
                        );
                    $charter = current($chmap->fetchWhere($where));
                }
                
                if ($action == 'UNLOCK')
                    $lock = 'no';
                elseif ($lock <> 'yes')
                    $lock = $postvalues['lock'];
          
                if ($action == 'FILE')
                {
                    $charter->name = $name;
                    $charter->type = $type;
                    $charter->purpose = $purp;
                    $charter->organization = $orgn;
                    $charter->leaderselection = $lsel;
                    $charter->leaderterm = $ltrm;
                    $charter->numbermembers = $nmem;
                    $charter->memberterm = $mtrm;
                    $charter->reportto = $rept;
                    $charter->meetings = $meet;
                    $charter->duties = $dtes;
                    $charter->approvaldate = $appr;
                    
                    $chmap->save($charter);
                    $lock = 'yes';
                    $where = array(
                        array('id', ' = ', $recid),
                        );
                    $charter = current($chmap->fetchWhere($where));
                }
                
                if ($action == 'UNLOCK')
                {
                    $where = array(
                        array('id', ' = ', $recid),
                        );
                    $charter = current($chmap->fetchWhere($where));
                    $type = $charter->type;
                }


                if ($action == 'NEW') 
                {
                    $newold = 'old';
                    $name = $type = $purp = $orgn = $lsel = $ltrm = $nmem = $mtrm = $rept = $meet = $dtes = $appr = '';

                    $charter = new Application_Model_ChartersOrg();
                    $charter->name = $name;
                    $charter->type = $type;
                    $charter->purpose = $purp;
                    $charter->organization = $orgn;
                    $charter->leaderselection = $lsel;
                    $charter->leaderterm = $ltrm;
                    $charter->numbermembers = $nmem;
                    $charter->memberterm = $mtrm;
                    $charter->reportto = $rept;
                    $charter->meetings = $meet;
                    $charter->duties = $dtes;
                    $charter->approvaldate = $appr;
                    
                    $chmap->save($charter);
                    $readonly = '';
                    $lock = 'no';
                    $recid = $charter->id;
                }
            }

            if ($lock == 'yes') {
                $readonly = 'disabled';
                $disabled = 'disabled';
                $textstyle = 'STYLE="background-color: white;"';
            }  
            else {
                $readonly = '';
                $disabled = '';
                $textstyle = '';
            }

            
            if ($action <> '')
            {
                $this->view->recid = $recid;
                $this->view->lock = $lock;
                $this->view->newold = $newold;
                $this->view->type = $type;
                $this->view->readonly = $readonly;
                $this->view->disabled = $disabled;
                $this->view->textstyle = $textstyle;
                $this->view->charter = $charter;
    
                $this->view->style = 'zform.css';
                return ($this->render('showcharterorg'));                
            }
        }

        $charters = $chmap->fetchAll();
        
        function custom_sort($a,$b) {
            return strcmp($a->type . $a->name, $b->type . $b->name);
        }
        usort($charters, "custom_sort");

        $this->view->charters = $charters;              
        $this->view->style = 'zform.css';
    }



    public function policiesAction()
    {

            function datetime()
            {
                $unixtime = mktime(date('G'), date('i'), date('s'), date('m'), date('d'), date('Y'));
                return date("Y-m-d H:i", $unixtime);
            }
            
            function typefromfirstletter($letter)
            {
                $types = array('C' => 'Council Limitations', 'B' => 'Board-Council Linkage', 'O' => 'Organizational Documents',
                  'E' => 'Ends Policies', 'G' => 'Governance Process', 'X' => 'Other');
                return $types[$letter];
            }
            
    
        $polmap = new Application_Model_PoliciesMapper();
        $functions = new Cvuuf_functions();

        $authfunctions = new Cvuuf_authfunctions();
        $pid = $this->auth->memberid;
        $boardauth = $authfunctions->hasPermission('admin', $this->auth);  // $authfunctions->boardAuth($pid);
//$boardauth = false;
        $this->view->boardauth = $boardauth;
   
        $action = '';
        $select = '';

        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $postvalues = $request->getParams();

            if (isset($postvalues['todo']))
            {
                $action = $postvalues['todo'];
                $pid = 0;
                
                if ($action == 'NEW')
                {
                    $policy = new Application_Model_Policies();
                    $policy->id = 0;
                    $policy->status = 'Proposed';
                    $this->view->policy = $policy;
                    $this->view->newold = 'new';
                    $this->view->style = 'zform.css';
                    return $this->render('policyedit');
                }
                
                if ($action == 'CANCEL' || $action == 'EXIT')
                {
                    $action = '';
                }

                if (isset($postvalues['id'])) 
                {
                    $vid = $postvalues['id'];
                    $pid = $vid[0];
                }
                elseif ($action <> '')
                {
                    $this->view->message = "No policy selected.";
                }
                        
                if ($pid > 0)
                {

            /* Index changes */
                    
                    if ($action == 'CHANGE')
                    {
            /* get existing policy info */
                        $where = array(
                            array('id', ' = ', $pid),
                            );
                        $this->view->policy = current($polmap->fetchWhere($where));
                        $this->view->datetime = datetime();

                        $this->view->newold = 'old';
                        $this->view->style = 'zform.css';
                        return $this->render('policyedit');
                    
                    }
                    
                    if ($action == 'DELETE')
                    {
                        $recid = 0;
                        if (isset($postvalues['id'])) 
                        {
                            $vid = $postvalues['id'];
                            $recid = $vid[0];

                            $where = array(
                                array('id', ' = ', $recid),
                                );
                            $policy = current($polmap->fetchWhere($where));
                            $policy->status = 'Deleted';
                            $polmap->save($policy);

                            $this->view->message = "Policy ID $recid status now Deleted.";
                            $action = '';
                        }
                        else
                        {
                            $this->view->message = "No policy selected.";
                        }
                    
                    }
    
    
                /* get documents themselves */
                                
                    if ($action == 'PDF')
                    {
                        $pol = $polmap->find($pid);
                        $pdf = $pol['pdffile'];
//var_dump($pol);                  
                        $file = '/policies/' . substr($pdf, 0, strlen($pdf) - 4);
//echo "PDF FILE $file <br>";      exit;                  
                        if ($functions->showFile($file, 'pdf.gz') === false)
                            $this->view->message = "PDF file for policy $pid does not exist.";
                        $action = '';
                    }
                    
                    if ($action == 'RTF')
                    {
                        $pol = $polmap->find($pid);
                        $rtf = $pol['rtffile'];
                        $file = '/policies/' . substr($rtf, 0, strlen($rtf) - 4);
//echo "rtf FILE $file <br>";      exit;                  
                        if ($functions->showFile($file, 'rtf.gz') === false)
                            $this->view->message = "RTF file for policy $pid does not exist.";
                        $action = '';
                    }
                }
                
            /* Changes to index display */
                            
                if ($action == 'CURRENT')
                    $select = 'Current';
                elseif ($action == 'ALL')
                    $select = 'All';
                elseif ($action == 'PROPOSED')
                    $select = 'Proposed';
                elseif ($action == 'UPDATING')
                    $select = 'Updating';
                if ($select <> '')
                {
                    $action = '';
                    unset ($this->view->message);
                }
                
            /* editing buttons */
                if ($action == 'CREATE')
                {
                    $policy = new Application_Model_Policies();
                    $polmap->save($policy);
                    $recid = $policy->id;
                    $action = 'UPDATE';
                }
                
                if ($action == 'UPDATE')
                {
//var_dump($postvalues);
                    unset($this->view->message);
                    if (!isset($recid))
                        $recid = $postvalues['recid'];                                     
                    $where = array(
                        array('id', ' = ', $recid),
                        );
                    $policy = current($polmap->fetchWhere($where));
                    if ($policy->status == 'Current' || $policy->status == 'Deleted')
                    {
                        $policy->status = 'Updating';
                        $policy->revision++;
                    }
                    else
                        $policy->revision = $postvalues['revision'];
                    $policy->name = $postvalues['policyname'];
                    $type = $postvalues['type'];
                    $policy->policytype = typefromfirstletter($type);
                    $policy->level = $postvalues['level'];                    
                    $policy->belowpolicy = $postvalues['below'];
                    $where = array(
                        array('number', ' = ', $policy->belowpolicy),
                        );
                    $above = $polmap->fetchWhere($where);
                    if ($above == false)
                        $this->view->message = "Next higher policy does not exist.";
                    $policy->description = filter_var($postvalues['description'], FILTER_SANITIZE_STRING);
                    $policy->submitdate = datetime();
                    $polmap->save($policy);
                    $this->view->policy = $policy;
                    $this->view->type = array();
                    $this->view->type[$policy->policytype[0]] = true;
                    
                    $this->view->datetime = datetime();
                    $this->view->newold = 'old';
                    $this->view->style = 'zform.css';
                    return $this->render('policyedit');
                                        
//var_dump($policy);
//echo "UPDATE $recid<br>"; exit;                
                }
                
                if ($action == 'NEW PDF')
                {
                    $recid = $postvalues['recid'];
                    $this->view->recid = $recid;                   
                    $this->view->filetype = 'pdf';
                    unset($this->view->message);
                    $this->view->style = 'zform.css';
                    return $this->render('policychoose');
                }
                
                if ($action == 'upload')
                {                
                    $this->view->message = array();
                    $recid = $postvalues['recid'];                   
                    $where = array(
                        array('id', ' = ', $recid),
                        );
                    $policy = current($polmap->fetchWhere($where));
                    $id = sprintf('%03d', $recid);                
                    $rev = sprintf('%02d', $policy->revision);
                    $filename = 'policy_r' . $id . '.' . $postvalues['filetype'];
                		$files = $_FILES['THEPDF'];
                    
                	 	$location = $functions->findfile("/policies/$filename") . '.gz';
                		if ($files['size']) {
                  			$functions->compress($files['tmp_name'], $location);
                  			$this->view->message[] = "Successfully uploaded policy " . $postvalues['filetype'] . " file.";
                		}
                		else
                		    $this->view->message[] = "There is a problem with uploading the policy " . $postvalues['filetype'] . " file.";
                    
                    $which = $postvalues['filetype'] . 'file';
                    $policy->$which = $filename;
                    $polmap->save($policy);
                    
                    $this->view->policy = $policy;
                    $this->view->newold = 'old';
                    $this->view->style = 'zform.css';
                    return $this->render('policyedit');

                }
                
                if ($action == 'NEW RTF')
                {
                    $recid = $postvalues['recid'];
                    $this->view->recid = $recid;                   
                    $this->view->filetype = 'rtf';
                    unset($this->view->message);
                    $this->view->style = 'zform.css';
                    return $this->render('policychoose');
                }
                
                if ($action == 'APPROVE')
                {
                    $this->view->message = array();
                    $recid = $postvalues['recid'];                   
                    $where = array(
                        array('id', ' = ', $recid),
                        );
                    $policy = current($polmap->fetchWhere($where));
                    if ($policy->pdffile == '')
                        {
                            $this->view->message[] = "PDF file has not been uploaded.";
                        }                    
                    if ($policy->rtffile == '')
                        {
                            $this->view->message[] = "RTF file has not been uploaded.";
                        }
                    $where = array(
                        array('number', ' = ', $policy->belowpolicy),
                        );
                    $above = $polmap->fetchWhere($where);
                    if ($above == false)
                        $this->view->message[] = "Next higher policy does not exist.";
                    if ($policy->name == '')
                        $this->view->message[] = "Policy has no name.";
                        
                    if (count($this->view->message) == 0)
                    {
                        $policy->status = "Current";
                        $now = datetime();
                        $policy->submitdate = $now;
                        $policy->approvaldate = $now;
                        $polmap->save($policy);
                        $this->view->message[] = "Policy $policy->number status changed to Current.";
                    }
                    
                    $this->view->policy = $policy;
                    $this->view->oldnew = 'old';   
                    $this->view->style = 'zform.css';
                    return $this->render('policyedit');
                    
                }
                   
                
            }
        }
    
        if ($action == '' || $pid == 0)
        {
      // Show directory if no actions in progress
        
            $status = '';
            if ($select == '')
                $select = 'Current';  
            $status = $select;
              
            if ($select == 'All')
                $where = array(
                    array('id', ' > ', 0),
                    );
            else
                $where = array(
                    array('status', ' = ', $status),
                    );
            $policies = $polmap->fetchWhere($where);

            $this->view->select = $select;
            $this->view->policies = $policies;
            $this->view->style = 'zform.css';
        }    


    }
    

    public function fixpoliciesAction()
    {
        echo "First, add Number after RecordID in policies table.";
        exit;        

    }



    public function fixpolicies1Action()
    {
        $polmap = new Application_Model_PoliciesMapper();
        $functions = new Cvuuf_functions();
        $policies = $polmap->fetchAll();
        function custom_sort($a,$b) {
            return strcmp($a->policytype . $a->name, $b->policytype . $b->name);
        }
        usort($policies, "custom_sort");
        $num = 0;
        $oldkey = '';
        foreach ($policies as $policy)
        {
            if ($policy->policytype == '')
                $policy->policytype = "Other";
            $key = $policy->policytype . $policy->name;
            if ($key <> $oldkey)
            {
                $num++;
                $oldkey = $key;
            }
            $policy->number = $num;
            $polmap->save($policy);
            echo "$num: $key <br>";
        }
        
        exit;
        
    }


    public function fixpolicies2Action()
    {
        
        $polmap = new Application_Model_PoliciesMapper();
        $functions = new Cvuuf_functions();
        $policies = $polmap->fetchAll();
        function custom_sort($a,$b) {
            return strcmp($a->policytype . $a->name, $b->policytype . $b->name);
        }
        usort($policies, "custom_sort");
        $num = 0;
        $oldkey = '';
        foreach ($policies as $policy)
        {
            if ($policy->belowpolicy <> 0)
            {
                $where = array(
                    array('id', ' = ', $policy->belowpolicy),
                    );
                $above = $polmap->fetchWhere($where);
                if (count($above) > 0)
                {
                    $was = $policy->belowpolicy;
                    $is = current($above)->number;
                    $policy->belowpolicy = $is;
                    echo "Policy above changed from $was to $is<br>";
                }
                else
                {
                    echo "Policy above $policy->belowpolicy doesn't exist - set to 0.<br>";
                    $policy->belowpolicy = 0;
                }
            }
            $polmap->save($policy);
        }
        
        exit;
        
    }


    public function testAction()
    {
        $pid = $this->auth->memberid;
        echo "ID IS $pid <br>";
        
        $authfunctions = new Cvuuf_authfunctions();
        var_dump($authfunctions->boardAuth($pid));
        exit;
        
    }
    
    
}

