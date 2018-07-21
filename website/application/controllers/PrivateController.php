<?php

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

/* My preferred way to launch external scripts is to always log the
   output to a file, always launch in background, and only if the output
   file is not "too young", indicating another instance of this executable
   might be in progress or recently run.
   I intend to use this to re-build slow sync-up kinds of pages that pull
   from external sources such as google groups and mailchimp on demand
   (but not each time a page is loaded or something).
*/
function launchExternal($cmd, $outfname, $stableOutfSeconds) {
	$launch = 0;
	$rel = '..'; // $_SERVER["DOCUMENTROOT"];
	$flockName = $rel.'/application/controllers/launch.lockf';
	$fp = fopen($flockName, "w+");
	if (flock($fp, LOCK_EX)) {
		if (!file_exists($outfname) || filemtime($outfname) + $stableOutfSeconds < time()) {
			// when output doesn't exist or it's old enough, wipe out and launch
			$fout = fopen($outfname, "w");
			fclose($fout);
			$launch = 1;
		} else {
			// when output is young enough, don't launch.
			$launch = 0;
		}
		fflush($fp);
		flock($fp, LOCK_UN);
	} else {
		$launch = 0;
	}
	fclose($fp);

	if ($launch) {
		// exec("nohup " . $cmd . " 2>&1 > '" . $outfname . "' &");
		exec($cmd . " > '" . $outfname . "' 2>&1 &");
	}
	return $launch;
}

class PrivateController extends Zend_Controller_Action
{

  
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
      
      public function fillPerson($person, $line)
      {
          $line[] = $person['firstname'] . ' ' . $person['lastname'];
          $line[] = $person['photolink'];
          
          $housemap = new Application_Model_HouseholdsMapper();
          $house = $housemap->find($person['householdid']);
          if ($person['pphone'] <> '')
          {
              $line[] = $this->formatPhoneNumber($person['pphone'], '-');
          }
          else
          {
              $line[] = $this->formatPhoneNumber($house['phone'], '-');
          }
          $line[] = $person['email'];
          return($line);
      }

    
    
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

    /*
     this jtest pattern seems pretty ugly, but I'm not clear on how
     else to get the login guard to work under this Zend Framework
     setup.
     The idea is that we have this jtestpageAction and the associated
     application/views/scripts/private/jtestpage.phtml, which is hit
     by going to members.cvuuf.org/private/jtestpage.
     That has a button in it that will POST to jtestscript, and on
     successful response, will poll jteststatus.
     The script output is sent to a file that is pulled in by
     jteststatus.
     */
    public function jtestpageAction()
    {
        // action body
    }

    public function jtestscriptAction()
    {
        // action body
	$rel = '..'; // $_SERVER["DOCUMENTROOT"];
	if (launchExternal($rel."/test_fn.sh", $rel."/application/views/scripts/private/jtestscriptoutput.txt", 5)) {
		echo "launched";
	} else {
		echo "not launched";
	}
    }

    public function jteststatusAction()
    {
        // action body
    }

    /* sync rebuild flow is copied from the jtest one. I hate it, but not
       as much as not having a button. One day I should learn how to do this
       for real. */
    public function syncAction()
    {
        // action body
    }

    public function syncscriptAction()
    {
        // action body
	$rel = '..'; // $_SERVER["DOCUMENTROOT"];
	$dump_to = $rel."/application/views/scripts/private/sync_contents.html";
	$out = $rel."/application/views/scripts/private/syncscriptoutput.txt";
	echo '<div id="script_start_result">';
	if (launchExternal("PYTHONPATH=".$rel."/secrets python ".$rel."/script/sync_email/dump_sync_page.py ".$dump_to, $out, 120)) {
		echo "rebuild launched: ".date("Y-m-d H:i:s");
	} else {
		echo "rebuild NOT launched, wait 2 minutes between rebuilds: ".date("Y-m-d H:i:s");
	}
	echo '</div>';
    }

    public function syncstatusAction()
    {
        // action body
    }

    /* group rebuild flow is copied from the jtest one. I hate it, but not
       as much as not having a button. One day I should learn how to do this
       for real. */
    public function groupAction()
    {
        // action body
    }

    public function groupscriptAction()
    {
        // action body
	$rel = '..'; // $_SERVER["DOCUMENTROOT"];
	$dump_to = $rel."/application/views/scripts/private/group_contents.html";
	$out = $rel."/application/views/scripts/private/groupscriptoutput.txt";
	echo '<div id="script_start_result">';
	if (launchExternal("PYTHONPATH=".$rel."/secrets python ".$rel."/script/sync_email/dump_groups_page.py ".$dump_to, $out, 120)) {
		echo "rebuild launched: ".date("Y-m-d H:i:s");
	} else {
		echo "rebuild NOT launched, wait 2 minutes between rebuilds: ".date("Y-m-d H:i:s");
	}
	echo '</div>';
    }

    public function groupstatusAction()
    {
        // action body
    }

    public function listpositionsAction()
    {
        $position = new Application_Model_Positions();
        $positionsmap = new Application_Model_PositionsMapper();
        $headingsmap = new Application_Model_HeadingsMapper();
        $where = array(
            array('type', ' = ', 'position'),
            );            
        $headings = $headingsmap->fetchWhere($where);
        $data = array();
        $heads = array();
        foreach ($headings as $heading)
        {
            $where = array(
                array('headingid', ' = ', $heading->id),
                );            
            $positions = $positionsmap->fetchWhere($where);
            $lines = array();
            unset($lines);
            $line = array();
            foreach ($positions as $position)
            {
                unset($line);
                $line[] = $position->title;

                $peoplemap = new Application_Model_PeopleMapper();
                $person = $peoplemap->find($position->contact1);
//var_dump($person);
                $line = $this->fillPerson($person, $line);
                $lines[] = $line;
            }
            $heads[] = $heading->heading;
            $data[] = array($heading->heading, $lines);          
        }
        $this->view->data = $data;
        $this->view->heads = $heads;
    }


    public function listgroupsAction()
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
        foreach ($headings as $heading)
        {
            $where = array(
                array('headingid', ' = ', $heading->id),
                );            
            $groups = $groupsmap->fetchWhere($where);

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
        $this->view->data = $data;
//var_dump($data);
//exit;
        $this->view->heads = $heads;
        $this->view->style = 'zform.css';
        
        if (isset($getvalues['pdfbutton']))
            return $this->render('pdfgroups');
            
    }


        
        function prepHood(Application_Form_PrivateHoodSelect $hoodForm) 
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
            $this->view->style = 'zform.css';
            $this->view->hoodForm = $hoodForm;
        
        }


    public function listhoodsAction()
    {
        $functions = new Cvuuf_functions();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();

            if (isset($getvalues['hood']))
            {
                $data = array();
                $hoodsmap = new Application_Model_NeighborhoodsMapper();
                $hoodresult = $getvalues['hood'];
                $hoodid = $hoodresult;
                $where = array(
                    array('hoodid', ' = ', $hoodid),
                    );            
                $housesinhood = $hoodsmap->fetchWhere($where);
                foreach($housesinhood as $hoodhouse)
                {
                    $hid = $hoodhouse->householdid;
                    $housemap = new Application_Model_HouseholdsMapper();
                    $house = $housemap->find($hid);
                    $where = array(
                        array('inactive', ' <> ', 'yes'),
                        array('householdid', ' = ', $hid),
                        array('status', ' IN ', "('member', 'newfriend')"),
                        );
                    $peoplemap = new Application_Model_PeopleMapper();            
                    $peopleinhouse = $peoplemap->fetchWhere($where);
                    if (count($peopleinhouse > 0))
                    {
                        $line = array();
                        foreach($peopleinhouse as $person)
                        {
                            unset($line);
                            $line['First Name'] = $person->firstname;
                            $line['Last Name'] = $person->lastname;
                            if ($person->pphone == '') {
                                if ($house['phone'] == '') {
                                    $line['Phone'] = '';
                                } else {
                                    $line['Phone'] = $this->formatPhoneNumber($house['phone'], '-') . ' (home)';
                                }
                            } else {
                                $line['Phone'] = $this->formatPhoneNumber($person->pphone, '-') . ' (cell)';
                            }
                            $line['Address'] = $house['street'] . ', ' . $house['city'] . ', ' . $house['state'] . ' ' . $house['zip'];
                            $line['Email'] = $person->email;
                            $line['Status'] = $person->status;
                            $data[] = $line;
                        }
                    }
                }
                 // Sort the multidimensional array
                // Define the custom sort function
                function custom_sort($a,$b) {
                    return strcmp($a['Last Name'].$a['First Name'], $b['Last Name'].$b['First Name']);
                }
                usort($data, "custom_sort");
//var_dump($getvalues); exit;
                if (isset($getvalues['pdfbutton']))
                {
                    $hoodarray = $functions->hoodValues();
                    $hood = $hoodarray[$hoodid];
                    $table = array();
                    $line = array();
                    foreach ($data as $row)
                    {
                        unset($line);
                        $line['Name'] = $row['First Name'] . ' ' . $row['Last Name'];
                        $line['Phone'] = $row['Phone'];
                        $line['Address'] = $row['Address'];
                        $line['Email'] = $row['Email'];
                        $line['Status'] = $row['Status'];
                        $table[] = $line;
                    }

                    require_once 'EzPDF/class.ezpdf.php';
                    $pdf = new Cezpdf('LETTER', 'landscape');
                    $pdf->selectFont('./fonts/Helvetica.afm');
//var_dump($table); exit;
                    $pdf->ezTable($table,'', $hood.' Neighborhood '.$functions->today(), array('showHeadings'=>1,'shaded'=>1,
                      'showLines'=>1, 'fontSize' => 9, 'titleFontSize' => 12));
                    $pdf->ezStream();
                    exit;
                }
                else
                {
                    $this->view->hoodid = $hoodid;
                    $this->view->data = $data;
                }
            }


        }
        
        $hoodForm = new Application_Form_PrivateHoodSelect();
        
        $this->prepHood($hoodForm);
        if (isset($hoodresult))
        {
            $hoodForm->hood->setValue($hoodresult);
        }

    }




    public function phonelistAction()
    {
        $makepdf = false;
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();

//var_dump($getvalues); 
            if (isset($getvalues['pdfbutton']))
                $makepdf = true;
            else
                $makepdf = false;
        }

        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' IN ', "('member', 'newfriend')"),
            );
        $order = array("lastname", "firstname");
        $people = $peoplemap->fetchWhere($where, $order);
        $data = array();
        $line = array();
        foreach ($people as $person)
        {
            unset($line);
            $hid = $person->householdid;
            $housemap = new Application_Model_HouseholdsMapper();
            $house = $housemap->find($hid);
            if ($makepdf)
            {
                if ($person->status == 'Member')
                    $line['Name'] = '<b>' . $person->firstname . ' ' . $person->lastname . '</b>';
                else
                    $line['Name'] = $person->firstname . ' ' . $person->lastname;
            }
            else
            {
                $line['First Name'] = $person->firstname;
                $line['Last Name'] = $person->lastname;
            }
            $line['Home Phone'] = $this->formatPhoneNumber($house['phone'], '-');
            $line['Cell Phone'] = $this->formatPhoneNumber($person->pphone, '-');
            $line['Email'] = $person->email;
            if (!$makepdf)
                $line['Status'] = $person->status;
            $data[] = $line;
        }
        $this->view->data = $data;
        
        if ($makepdf)
        {
            $functions = new Cvuuf_functions();
            require_once 'EzPDF/class.ezpdf.php';
            $pdf = new Cezpdf('LETTER', 'portrait');
            $pdf->selectFont('./fonts/Helvetica.afm');
//var_dump($table); exit;
            $pdf->ezTable($data,'', 'MEMBERS AND FRIENDS ' . $functions->today(), array('showHeadings'=>1,'shaded'=>1,
              'showLines'=>1, 'fontSize' => 9, 'titleFontSize' => 12));
            $pdf->ezStream();
            exit;
        }


        $local_alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      	unset($letters);
      	for ($i = 0; $i < strlen($local_alphabet); $i += 1)
      		  $letters[$i] = substr($local_alphabet, $i, 1);
      	$list = "<center>Browse Names<br>";
      	foreach ($letters as $let)
      		  $list = $list."[<a href='#initial".$let."'>$let</a>] ";
      	$list = $list."</center><br><br>";
      	$this->view->list = $list;
        $this->view->style = 'zform.css';


    }

    public function permissionlistAction()
    {
        $request = $this->getRequest();
        $setUser = 0;
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            foreach (array_keys($getvalues) as $key) {
                if (startsWith($key, 'user_')) {
                    $setUser = substr($key, 5);
                }
            }
// var_dump($getvalues); 
        }

        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' IN ', "('member', 'newfriend', 'friend', 'staff', 'child')"),
            );
        $order = array("lastname", "firstname");
        $people = $peoplemap->fetchWhere($where, $order);
        $data = array();
        $line = array();
        $this->view->permtypes = ['unknown','internal','congregation','uu'];
        foreach ($people as $person)
        {
            unset($line);
            $line['First Name'] = $person->firstname;
            $line['Last Name'] = $person->lastname;
            $line['Status'] = $person->status;
            $line['Id'] = $person->id;
            $line['Photolink'] = $person->photolink;
            $photoopt = 'unknown';
            $photoextra = '';
            if ($person->lastname=='Holland') {
                $photoopt = 'uu';
                $photoextra = 'jake test of extra info';
            }
            $save='Save';
            if ($person->id == $setUser) {
                $save = 'Saved';
            }

            $form = '<form method="post"><select name="photooption">';
            foreach (['unknown','internal','congregation','uu'] as $permtype) {
               $sel = '';
               if ($permtype == $photoopt) {
                   $sel = ' selected';
               }
	       $form = $form.'<option value="'.$permtype.'"'.$sel.'>'.$permtype.'</option>';
            }
            $form = $form.'<input type="hidden" name="user_'.$person->id.'" /><br/><input name="photoextra" type="text" value="'.$photoextra.'" /><br/><input type="submit" value="'.$save.'" /></form>';
            $line['Photoform'] = $form;

            $data[] = $line;
        }
        $this->view->data = $data;

        $this->view->style = 'zform.css';
    }


    public function showminutesAction()
    {
        $request = $this->getRequest();
            $getvalues = $request->getParams();
            if (isset($getvalues['org']))
            {
                $organization = $getvalues['org'];
            }
            else
                $organization = 'Board';
            if (isset($getvalues['select']))
            {
                $nodeid = $getvalues['select'];
                $minutesmap = new Application_Model_NodesMapper();
                $where = array(
                    array('nodeid', ' = ', $nodeid),
                    );
                $minutesarray = $minutesmap->fetchWhere($where);
                $minutes = current($minutesarray);
                $text = stripslashes($minutes->body);
                $start = strpos($text, '<body>');
                $end = strpos($text, '</body>');
                $len = strlen($text);
            //echo "\nStart at $start and end at $end - length is $len<br>"; 

                $xml = strpos($text, '<xml>');
            //echo "\nXML at $xml <br>";
                if ($xml > 0){
                    $endjunk = strpos($text, "<!--[endif]--></p>");
            //echo "ENDJUNK at $endjunk <br>";
                }
//exit;
//echo "\nCLIP BEGINS: '", substr($text, $start, 16), "'<br>";
                $trim = substr($text, 0, $end - 1);
                if (isset($endjunk)){
                    if ($endjunk > 0)
                        $clip = substr($trim, $endjunk + 14);
                }
                else
                  $clip = substr($trim, $start + 6);
            //echo "\nBEGINS: '", substr($clip, 0, 8), "'<br>";    
            //exit;
                $begins=substr($clip, 0, 8);
            //    echo $begins, "<br>"; exit;    
                $this->view->text = $clip;
                $this->view->style = 'zform.css';
                return $this->render('view');
    
//    echo stripslashes($db->get_var($rQuery));
//    exit;

        }    
        
        $minutesmap = new Application_Model_NodesMapper();
        $where = array(
            array('organization', ' = ', $organization),
            );
        $minutes = $minutesmap->fetchWhere($where);
        $data = array();
        $line = array();
        $this->view->organization = $organization;
        foreach ($minutes as $meeting)
        {
            unset($line);
            $line[] = $meeting->nodeid;
            $line[] = $meeting->title;
            $data[] = $line;
        }
        $this->view->data = $data;
    }

        /* returns grid object from form */
        function fillGrid($param, Application_Model_WorshipGrid $grid)
        {
            $grid->sunday = isset($param['sunday']) ? $param['sunday'] : 'yes';
            $grid->servicedate = isset($param['servicedate']) ? $param['servicedate'] : '';
            $grid->servicetime = isset($param['servicetime']) ? $param['servicetime'] : '';
            $grid->presenter = isset($param['presenter']) ? $param['presenter'] : '';
            $grid->topic = isset($param['topic']) ? $param['topic'] : '';
            $grid->music = isset($param['music']) ? $param['music'] : '';
            $grid->specialmusic = isset($param['specialmusic']) ? $param['specialmusic'] : '';
            $grid->hymns = isset($param['hymns']) ? $param['hymns'] : '';
            $grid->early = isset($param['early']) ? $param['early'] : '';
            $grid->late = isset($param['late']) ? $param['late'] : '';
            $grid->organizer = isset($param['organizer']) ? $param['organizer'] : '';
            $grid->worshipassoc = isset($param['worshipassoc']) ? $param['worshipassoc'] : '';
            $grid->otherinfo = isset($param['otherinfo']) ? $param['otherinfo'] : '';
            $grid->attearly = isset($param['attearly']) ? $param['attearly'] : 0;
            $grid->attlate = isset($param['attlate']) ? $param['attlate'] : 0;
        }

        function validateMysqlDate( $date )
        {
            return preg_match( '#^(?P<year>\d{2}|\d{4})([- /.])(?P<month>\d{1,2})\2(?P<day>\d{1,2})$#', $date, $matches )
                   && checkdate($matches['month'],$matches['day'],$matches['year']);
        }
        
        function getCurrentYear()
        {
            $unixtime=mktime(date('G'),date('i'),date('s'), date('m'), date('d'),date('Y'));
            $date = date("Y-m-d H:i", $unixtime);
            $year = substr($date, 0, 4);
            $month = substr($date, 5, 2);
            if ($month < '07')
                $year = $year - 1;
            return($year);
        }



    public function worshipgridAction()
    {
        $functions = new Cvuuf_authfunctions();
        $ministry = $functions->hasPermission('ministry', $this->auth);
        if ($ministry == false)
            {
                $this->view->message = "Access not allowed, ministry permission required.";
                return ($this->render('index'));
            }
        
        $request = $this->getRequest();
            $getvalues = $request->getParams();
            if (isset($getvalues['year']))
            {
                $year = $getvalues['year'];
            }
            else
            {
                $year = $this->getCurrentYear();
            }

            if (isset($getvalues['ID']))
            {
                $id = $getvalues['ID'];
                $gridmap = new Application_Model_WorshipGridMapper();
                $gridarray = $gridmap->find($id);
                $gridForm = new Application_Form_WorshipGridEdit();
                $gridForm->populate($gridarray);
                $gridForm->year->setValue($getvalues['Year']);
                $gridForm->id->setValue($id);
                $gridForm->submit->setDescription('Write record with changes.'); 
                $gridForm->done->setDescription('Done with this record.'); 
                $gridForm->remove->setDescription('Remove this record [be careful].'); 
                $this->view->style = 'zform.css';
                $gridForm->setAction('/private/WorshipGrid');
                $this->view->gridForm = $gridForm;
                return($this->render('worshipgridedit'));
            }

            if ($this->getRequest()->isPost()) 
            {
                $getvalues = $request->getParams();
                if (isset($getvalues['addbutton']))
                {
                    $gridForm = new Application_Form_WorshipGridEdit();
                    $gridForm->servicedate->setValue('yyyy-mm-dd');
                    $gridForm->attearly->setValue(0);
                    $gridForm->attlate->setValue(0);
                    $gridForm->submit->setDescription('Write record with changes.'); 
                    $gridForm->done->setDescription('Done with this record.'); 
                    $gridForm->remove->setDescription('Remove this record [be careful].'); 
                    $this->view->style = 'zform.css';
                    $gridForm->setAction('/private/WorshipGrid');
                    $this->view->gridForm = $gridForm;
                    return($this->render('worshipgridedit'));
                }
                
                if (isset($getvalues['remove']))
                {
                    $id = $getvalues['id'];
                    $gridmap = new Application_Model_WorshipGridMapper();
                    $grid = $gridmap->find($id);
                    $date = $grid['servicedate'];
                    $result = $gridmap->delete($id);
                    $this->view->message = "Grid record for $date removed.";
                }
                if (isset($getvalues['submit']))
                {
                    $grid = new Application_Model_WorshipGrid();
                    $gridmap = new Application_Model_WorshipGridMapper();
                    $formData = $request->getParams();
                    if ($this->validateMysqlDate($formData['servicedate']) == false)
                    {
                        $this->view->message = "Date format not valid.";
                    }
                    else
                    {
                        $this->fillGrid($formData, $grid);
                        $id = $getvalues['id'];
                        if ($id > 0)
                            $grid->id = $id;
                        $gridmap->save($grid);
                        if ($id == '')
                            $this->view->message = "New record $grid->id written.";
                        else
                            $this->view->message = "Record $id written.";
                    }
                    
                    $gridForm = new Application_Form_WorshipGridEdit();
                    $gridForm->populate($formData);
                    $this->view->style = 'zform.css';
                    $gridForm->setAction('/private/WorshipGrid');
                    $this->view->gridForm = $gridForm;

                    return($this->render('worshipgridedit'));
                }
                if (isset($getvalues['done']))
                {
//echo "DONE <BR>"; 
                }
            }
                
            if ($year == '')
                $year = $this->getCurrentYear();
            $year = sprintf("%04u", $year);
            $this->view->year = $year;
            $nextyear = sprintf("%04u", ($year + 1));
            $servicedates = "'$year-07-01' AND '$nextyear-06-30'";

            $gridmap = new Application_Model_WorshipGridMapper();
            $where = array(
                array('servicedate', ' BETWEEN ', $servicedates),
                );
            $gridlines = $gridmap->fetchWhere($where, array ("ServiceDate", "ServiceTime"));

            $servicemap = new Application_Model_WorshipservicesMapper();
            $data = array();
            $line = array();
            foreach ($gridlines as $grid)
            {
                unset($line);
                $line[] = $grid->id;
                $date = $grid->servicedate;
                $line[] = $date;
                $topic = $grid->topic;
                $presenter = $grid->presenter;
                $line[] = $grid->sunday;
                if ($grid->sunday == 'yes')
                {
                    $where = array(
                        array('sunday', ' = ', $date),
                        );
                    $service = $servicemap->fetchWhere($where);
                    if (count($service) == 1)
                    {
                        $info = $service[0];
                        $topic = '<i>' . $info->title . '</i>';
                        $presenter = '<i>' . $info->presenter . '</i>';
                    }
                }
                $line[] = $presenter;
                $line[] = $topic;
                $line[] = $grid->music;
                $line[] = $grid->specialmusic;
                $line[] = $grid->hymns;
                $line[] = $grid->early;
                $line[] = $grid->late;
                $line[] = $grid->organizer;
                $line[] = $grid->worshipassoc;
                $line[] = $grid->otherinfo;
                $line[] = $grid->attearly;
                $line[] = $grid->attlate;
                $line[] = $grid->attearly + $grid->attlate;
                $data[] = $line;
                
            }
            $yearForm = new Application_Form_PrivateYearSelect();
            $yearForm->year->setValue($year);
            $this->view->style = 'zform.css';
            $this->view->yearForm = $yearForm;

            $this->view->data = $data;
            return $this->render('worshipgrid');
      }




    public function worshipplansAction()
    {
            $year = $this->getCurrentYear();
            $year = sprintf("%04u", $year);
            $this->view->year = $year;
            $nextyear = sprintf("%04u", ($year + 1));
            $unixtime=mktime(date('G'),date('i'),date('s'), date('m'), date('d'),date('Y'));
            $today = date("Y-m-d", $unixtime);
            $servicedates = "'$today' AND '$nextyear-06-30'";

            $gridmap = new Application_Model_WorshipGridMapper();
            $where = array(
                array('servicedate', ' BETWEEN ', $servicedates),
                );
            $gridlines = $gridmap->fetchWhere($where, array ("ServiceDate", "ServiceTime"));

            $servicemap = new Application_Model_WorshipservicesMapper();
            $data = array();
            $line = array();
            foreach ($gridlines as $grid)
            {
                unset($line);
                $date = $grid->servicedate;
                $line[] = $date;
                $topic = $grid->topic;
                $presenter = $grid->presenter;
                if ($grid->sunday == 'yes')
                {
                    $where = array(
                        array('sunday', ' = ', $date),
                        );
                    $service = $servicemap->fetchWhere($where);
                    if (count($service) == 1)
                    {
                        $info = $service[0];
                        $topic = $info->title;
                        $presenter = $info->presenter;
                    }
                }
                $line[] = $presenter;
                $line[] = $topic;
                $data[] = $line;
            }

            $this->view->data = $data;
      }

      


    public function showfileAction()
    {
        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['file']))
            {
                $file = $getvalues['file'];
            }
            if (isset($getvalues['type']))
            {
                $type = $getvalues['type'];           
            }
            if (isset($file) && isset($type))
            {
                $functions = new Cvuuf_functions();
                $functions->showFile($file, $type);
            }
            else
            {
                $this->view->message = "Cannot locate file to show.";
            }
        } 
        else
        {
            $this->view->message = "Missing file name and type.";
        }
    }
  




    public function fulldirectoryAction()
    {
    
        function addRow($row, $string)
        {
            $rowstr = implode(':', $row);
            if ($string <> '') 
                $break = ';';
            else 
                $break = '';
            return ($string .= $break . $rowstr);
        }        

        function …define($a){foreach($a as $k => $v)define($k, $v);}
        
        …define(array(
          "Street" => 0,
          "CityStateZip" => 1,
          "PhoneNumber" => 2,
          "FirstNameAdult1" => 3,
          "LastNameAdult1" => 4,
          "EmailAdult1" => 5,
          "PPhoneAdult1" => 6,
          "FirstNameAdult2" => 7,
          "LastNameAdult2" => 8,
          "EmailAdult2" => 9,
          "PPhoneAdult2" => 10,
          "FirstNameAdult3" => 11,
          "LastNameAdult3" => 12,
          "EmailAdult3" => 13,
          "PPhoneAdult3" => 14,
          "Child1" => 15,
          "Child2" => 16,
          "Child3" => 17,
          "Child4" => 18,
          "Child5" => 19,
        ));
        

        $functions = new Cvuuf_functions();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();

//var_dump($getvalues); 
            if (isset($getvalues['pdfbutton']))
            {
                $directory = $getvalues['directory'];
                $dirrows = explode(';', $directory);
//echo count($dirrows), " rows exploded <br>"; 
//var_dump($dirrows);
//exit;
                foreach ($dirrows as $row)
                {
                    $dirtable[] = explode(':', $row);
                }
                  function cmp($a, $b)
                  {
                      return strcmp($a[LastNameAdult1], $b[LastNameAdult1]);
                  }
                usort($dirtable, "cmp");

                $today = $functions->today();
                $month = intval(substr($today, 5,2));
                $monthName = jdmonthname (juliantojd($month, 1, 2002 ), 1);
                $theDate = '<b>' . $monthName . " " . substr($today, 8,2) . ", " . substr($today, 0,4).'</b>';

//echo "$theDate <br>";
                
                require_once 'EzPDF/class.ezpdf.php';
                $pdf = new Cezpdf('LETTER', 'portrait');
                $pdf->selectFont('./fonts/Helvetica.afm');
                
                // Put out a cover page
                
                $pdf->ezText("\n\nConejo Valley",24, array('left'=>50));
                $pdf->ezText("Unitarian Universalist",24, array('left'=>60));
                $pdf->ezText("Fellowship",24, array('left'=>70));
                $pdf->ezSetDy(-200);
                $pdf->ezText("<b>Directory Of\nMembers, Affiliates, and Friends</b>",30,array('justification'=>'center'));
                $pdf->ezSetDy(-20);
                $pdf->ezText($theDate,24,array('justification'=>'center'));
                $pdf->ezSetDy(-240);
                $pdf->ezText("We meet Sundays at 9:15 and 11 A.M.\n3327 Old Conejo Road in Newbury Park / Thousand Oaks, CA\nOur phone number is 805-498-9548"
                ,10,array('justification'=>'center'));
                $pdf->ezNewPage();

                // Put out header here
                $HEADER='<b>Conejo Valley Unitarian Universalist Fellowship Directory</b>';
                $hwidth=$pdf->getTextWidth(12,$HEADER);
                //echo $HEADER, '<br>', $hwidth, " is header width.<br>";
                $dwidth=$pdf->getTextWidth(12,$theDate);
                //echo $dwidth, " is date width.<br>";
                
                
                $FOOTER='This directory is not to be used for commercial purposes.';
                $CENTER=array("justification"=>"center");
                
                //$pdf->ezText($HEADER,12);
                $pdf->addText(30,762,12,$HEADER);
                $pdf->addText(582-$dwidth,762,12,$theDate);
                $pdf->ezText('   ',12);
                
                $pdf->ezColumnsStart(array('num'=>3));

                $LINESPERCOLUMN = 54;
                $linesincolumn = 0;
                $columnsonpage = 0;
                foreach ($dirtable as $dir)
                {
                  	$lines = 1;
                  	
                  	$textout[$lines] = '<b>' . $dir[FirstNameAdult1] . ' ' . $dir[LastNameAdult1] . '</b>';
                  	$sizeout[$lines] = 12;
                  	
                  	if ($dir[PPhoneAdult1]) {
                  		$lines++;
                  		$textout[$lines] = '   ' . $dir[PPhoneAdult1];
                  		$sizeout[$lines] = 9;
                  	}
                  	if ($dir[EmailAdult1]) {
                  		$lines++;
                  		$textout[$lines] = '   ' . $dir[EmailAdult1];
                  		$sizeout[$lines] = 9;
                  	}
                  
                  	if ($dir[FirstNameAdult2]) {
                  		$lines++;
                  		$textout[$lines] = '<b>' . $dir[FirstNameAdult2] . ' ' . $dir[LastNameAdult2] . '</b>';
                  		$sizeout[$lines] = 12;
                  	}
                  	if ($dir[PPhoneAdult2]) {
                  		$lines++;
                  		$textout[$lines] = '   ' . $dir[PPhoneAdult2];
                  		$sizeout[$lines] = 9;
                  	}
                  	if ($dir[EmailAdult2]) {
                  		$lines++;
                  		$textout[$lines] = '   ' . $dir[EmailAdult2];
                  		$sizeout[$lines] = 9;
                  	}
                  
                  	if ($dir[FirstNameAdult3]) {
                  		$lines++;
                  		$textout[$lines] = '<b>' . $dir[FirstNameAdult3] . ' ' . $dir[LastNameAdult3] . '</b>';
                  		$sizeout[$lines] = 12;
                  	}
                  	if ($dir[PPhoneAdult3]) {
                  		$lines++;
                  		$textout[$lines] = '   ' . $dir[PPhoneAdult3];
                  		$sizeout[$lines] = 9;
                  	}
                  	if ($dir[EmailAdult3]) {
                  		$lines++;
                  		$textout[$lines] = '   ' . $dir[EmailAdult3];
                  		$sizeout[$lines] = 9;
                  	}
                  	if ($dir[Child1]) {
                  		$lines++;
                  		$textout[$lines] = '<i>' . $dir[Child1] . '</i>';
                  		$sizeout[$lines] = 11;
                  	}
                  	if ($dir[Child2]) {
                  		$lines++;
                  		$textout[$lines] = '<i>' . $dir[Child2] . '</i>';
                  	$sizeout[$lines] = 11;
                  	}
                  	if ($dir[Child3]) {
                  		$lines++;
                  		$textout[$lines] = '<i>' . $dir[Child3] . '</i>';
                  		$sizeout[$lines] = 11;
                  	}
                  	if ($dir[Child4]) {
                  		$lines++;
                  		$textout[$lines] = '<i>' . $dir[Child4] . '</i>';
                  		$sizeout[$lines] = 11;
                  	}
                  	if ($dir[Child5]) {
                  		$lines++;
                  		$textout[$lines] = '<i>' . $dir[Child5] . '</i>';
                  		$sizeout[$lines] = 11;
                  	}
                  
                  
                  	if ($dir[Street]) {
                  		$lines++;
                  		$textout[$lines] = $dir[Street];
                  		$sizeout[$lines] = 11;
                  		$lines++;
                  		$textout[$lines] = $dir[CityStateZip];
                  		$sizeout[$lines] = 11;
                  	}
                  
                  	if ($dir[PhoneNumber]>' ') {
                  		$lines++;
                  		$textout[$lines] = $dir[PhoneNumber];
                  		$sizeout[$lines] = 11;
                  
                  	}	
                  		$lines++;
                  		$textout[$lines] = ' ';
                  		$sizeout[$lines] = 11;
                  
                  //echo "lines is ", $lines, "<BR>";
                  
                  	if ($lines + $linesincolumn > $LINESPERCOLUMN) {
                  		$columnsonpage++;
                  		if ($columnsonpage > 2) {
                  			$columnsonpage = 0;
                  			// Put out footer here
                  			$yposition = $pdf->ezText('   ',12);
                  //echo $yposition, " y position before footer.<br>";
                  
                  			$pdf->ezColumnsStop();
                  			$yposition = $pdf->ezText('   ',12);
                  //echo $yposition, " y position after column stop.<br>";
                  			$pdf->ezSetY(50);
                  			$yposition=$pdf->ezText($FOOTER, 11, $CENTER);
                  //echo $yposition, " y position after footer.<br>";
                  
                  		// Put out header here
                  			$pdf->ezText($HEADER,12);
                  //			$pdf->addText(30,762,12,$HEADER);
                  			$pdf->addText(582-$dwidth, 762, 12, $theDate);
                  			$pdf->ezText('   ',12);
                  			$pdf->ezColumnsStart(array('num'=>3));
                  		}
                  		else {
                  			$pdf->ezNewPage();
                  		}
                  		$linesincolumn=0;
                  	}
                  
                  	for ($line = 1; $line <= $lines; $line++) {
                  		$yposition = $pdf->ezText($textout[$line], $sizeout[$line]);
                  //echo $yposition, " y position at end of line.<br>";
                  //echo $line, $textout[$line], " " , $sizeout[$line], "<br>";
                  		}
                  
                  	$linesincolumn += $lines;
                  	
                  //echo $linesincolumn, " lines in column.<br>";
                  //echo $yposition, " y position at end of group.<br>";
                
                }

//echo $yposition, " ending y position.<br>";
          			$pdf->ezColumnsStop();
          			$pdf->ezSetY(50);
          			$yposition=$pdf->ezText($FOOTER, 11, $CENTER);

                $pdf->ezStream();
                exit;

                for ($i = 0; $i < 10; $i++)
                {
//                    var_dump($dirtable[$i]);
                    $row = $dirtable[$i];
                    echo FirstNameAdult1, '<br>';
                    echo $row[FirstNameAdult1], '<br>';
                    echo $row[LastNameAdult1], '<br>';
                            
                }
                exit;

            }
            if (isset($getvalues['csvbutton']))
            {
                $tabledir = $_SERVER["DOCUMENT_ROOT"].'/reports/';
                $tablename = tempnam($tabledir, "dir");
                $fp = fopen ($tablename, "wb");
                if ($fp == false)
                    echo "FAIL<BR>";
                else
                {
                    $directory = $getvalues['directory'];
                    $dirrows = explode(';', $directory);
                    foreach ($dirrows as $row)
                    {
                        $dirtable[] = explode(':', $row);
                    }
                      function cmp($a, $b)
                      {
                          return strcmp($a[LastNameAdult1], $b[LastNameAdult1]);
                      }
                    usort($dirtable, "cmp");
    
                    foreach ($dirtable as $dir)
                    {
                        $CSZ = $dir[CityStateZip];
                        $thecomma = strpos($CSZ, ',');
                        $city = substr($CSZ, 0, $thecomma);
                        $state = substr($CSZ, $thecomma+2, 2);
                        $zip = substr($CSZ, $thecomma+5);
                        $record = '"'.$dir[LastNameAdult1].'","'.$dir[FirstNameAdult1].'",,,,'.$dir[PhoneNumber].',,,'
                        	.$dir[EmailAdult1].',"' 
                        	.$dir[Street].'","'.$city.'",'.$state.','.$zip.',,'
                        	.$dir[Child1].','.$dir[Child2].','.$dir[Child3].','.$dir[Child4].','.$dir[Child5].",,CVUUF\n";
                        fwrite ($fp, $record);
                        
                        if ($dir[LastNameAdult2] == $dir[LastNameAdult1]) 
                        {
                        $record = '"'.$dir[LastNameAdult2].'","'.$dir[FirstNameAdult2].'",,,,'.$dir[PhoneNumber].',,,'
                        	.$dir[EmailAdult2].',"' 
                            	.$dir[Street].'","'.$city.'",'.$state.','.$zip.',,'
                            	.$dir[Child1].','.$dir[Child2].','.$dir[Child3].','.$dir[Child4].','.$dir[Child5].",,CVUUF\n";
                          	fwrite ($fp, $record);
                        }
    			if ($dir[LastNameAdult3] == $dir[LastNameAdult1]) 
                        {
                        $record = '"'.$dir[LastNameAdult3].'","'.$dir[FirstNameAdult3].'",,,,'.$dir[PhoneNumber].',,,'
                            .$dir[EmailAdult3].',"' 
                                .$dir[Street].'","'.$city.'",'.$state.','.$zip.',,'
                                .$dir[Child1].','.$dir[Child2].','.$dir[Child3].','.$dir[Child4].','.$dir[Child5].",,CVUUF\n";
                              fwrite ($fp, $record);
                        }
                    }
                    fclose($fp);
    
                    header("Content-Type: application/force-download");
                    header("Content-Length: ".filesize($tablename));
                    header("Content-Disposition: attachment; filename=cvuuf_directory.csv");
                    readfile($tablename);
                    unlink ($tablename);
                    exit;
                }
            }
        }

    
        $directory = '';
        $entries = 0;
        $dirrow = array();

        $remap = new Application_Model_REMapper();        
        $peoplemap = new Application_Model_PeopleMapper();
        
        $housemap = new Application_Model_HouseholdsMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            );            
        $houses = $housemap->fetchWhere($where);

//echo "HOUSES COUNT ",count($houses),'<br>'; exit;

        $lastid = -1;
      	foreach ($houses as $house)
        {
            $hid = $house->id;
//echo "START HOUSE $hid <br>";
            $where = array(
                array('inactive', ' <> ', 'yes'),
                array('householdid', ' = ', $hid),
                );            
            $persons = $peoplemap->fetchWhere($where);
          	$adultcount = 0;
          	$childcount = 0;

            $emptyrow = array('street' => '', 'citystatezip' => '', 'phone' => '',
              'firstnamea1' => '', 'lastnamea1' => '', 'emaila1' => '', 'pphonea1' => '', 
              'firstnamea2' => '', 'lastnamea2' => '', 'emaila2' => '', 'pphonea2' => '', 
              'firstnamea3' => '', 'lastnamea3' => '', 'emaila3' => '', 'pphonea3' => '',
              'child1' => '', 'child2' => '', 'child3' => '', 'child4' => '', 'child5' => '',  
              );
            

            $childarray = array('child1', 'child2', 'child3', 'child4', 'child5');
          	$spouse = 0;
          	foreach ($persons as $person) 
            {
                $status = $person->status;
            		if (in_array($status, array('Member', 'Affiliate', 'NewFriend', 'Spouse')))
                {
                    if ($status == 'Spouse')
                        $spouse = $adultcount + 1;
                    if ($adultcount == 0) 
                    {  // First non spouse
                        $dirrow = $emptyrow;
          				      $dirrow['street'] = $house->street;
                        $dirrow['citystatezip'] = $house->city . ", " . $house->state .
                          " " . $house->zip;
                        $dirrow['phone'] = $this->formatPhoneNumber($house->phone, '-');
          
                        $adultcount++;
                        $dirrow['firstnamea1'] = $person->firstname;
                        $dirrow['lastnamea1'] = $person->lastname;
                        $dirrow['emaila1'] = $person->email;
                        $dirrow['pphonea1'] = $this->formatPhoneNumber($person->pphone, '-');
                    }  elseif ($adultcount == 1) {
                      	$adultcount++;
                      	$dirrow['firstnamea2'] = $person->firstname;
                      	$dirrow['lastnamea2'] = $person->lastname;
                      	$dirrow['emaila2'] = $person->email;
                        $dirrow['pphonea2'] = $this->formatPhoneNumber($person->pphone, '-');
                    }  elseif ($adultcount == 2) {
                      	$adultcount++;
                      	$dirrow['firstnamea3'] = $person->firstname;
                      	$dirrow['lastnamea3'] = $person->lastname;
                      	$dirrow['emaila3'] = $person->email;
                      	$dirrow['pphonea3'] = $this->formatPhoneNumber($person->pphone, '-');
                    }
                }
	          }
            

// Look for children in this household
            $where = array(
                array('inactive', ' <> ', 'yes'),
                array('householdid', ' = ', $hid),
                array('status', ' = ', 'child'),
                );            
            $people = $peoplemap->fetchWhere($where);
            foreach ($people as $person)
            {
                $where = array(
                    array('inactive', ' <> ', 'yes'),
                    array('childid', ' = ', $person->id),
                    );                        
                $child = $remap->fetchWhere($where);
                if (count($child) == 1)
                    $dirrow[$childarray[$childcount]] = $person->firstname . ' ' . $person->lastname;
            }
//echo "&nbsp;&nbsp;$adultcount ADULTS <br>";            
          	if ($spouse == 1) {
            		$fname = $dirrow['firstnamea2'];
            		$lname = $dirrow['lastnamea2'];
            		$dirrow['lastnamea2'] = $dirrow['lastnamea1'];
            		$dirrow['firstnamea2'] = $dirrow['firstnamea1'];
            		$dirrow['lastnamea1'] = $lname;
            		$dirrow['firstnamea1'] = $fname;
          	}
            
            if ($adultcount > 0)
            {
//echo "COUNT $adultcount SPOUSE $spouse<br>";
                if (!(($spouse == 1) && ($adultcount == 1)))
                {
                    $directory = addRow($dirrow, $directory);
                    $entries++;
//echo "NORMAL HOUSE $hid <br>";
                }
            }          
          // If Spouse is first adult, flip first and second
    
            // Flip first and second adults if last names are different
        		if ($spouse == 0 && $adultcount > 1) 
            {
          			if (isset($dirrow['lastnamea2'])) 
                {  // if 2 exists,
                    $lname = $dirrow['lastnamea2'];
                    if ($lname <> $dirrow['lastnamea1']) 
                    {
//var_dump($dirrow);
              					$fname = $dirrow['firstnamea2'];
                    		$email = $dirrow['emaila2'];
                    					$dirrow['lastnamea2'] = $dirrow['lastnamea1'];
                    					$dirrow['firstnamea2'] = $dirrow['firstnamea1'];
                    		$dirrow['emaila2'] = $dirrow['emaila1'];
                    					$dirrow['lastnamea1'] = $lname;
                    					$dirrow['firstnamea1'] = $fname;
                    		$dirrow['emaila1'] = $email;
                        
                        $directory = addRow($dirrow, $directory);
                        $entries++;
//echo "SPOUSE HOUSE $hid <br>";          
                    }
                }
            }

            if ($adultcount > 0)
            {
//echo count($persons), " PEOPLE IN HOUSE $hid <br>";
            }

//if ($entries > 30) exit;  
        }  /* end of house loop */

//var_dump($directory); exit;
        $this->view->directory = $directory;
        $this->view->message = "Directory has $entries entries.";
        $this->view->style = 'zform.css';
    }
    


    


    public function worshipserviceAction()
    {

        $functions = new Cvuuf_functions();
      	$today = $functions->today();
      	$year = substr($today, 0, 4);
        $servicemap = new Application_Model_WorshipservicesMapper;
        $gridmap = new Application_Model_WorshipGridMapper();

        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $postvalues = $request->getParams();
            if (isset($postvalues['submit']))
            {
                $formData = $request->getParams();
                if ($this->validateMysqlDate($formData['servicedate']) == false)
                {
                    $this->view->message = "Date format not valid.";
                }
                else
                {
                    $sunday = $formData['servicedate'];
                    $where = array(
                        array('servicedate', ' = ', $sunday),
                        );            
                    $grid = $gridmap->fetchWhere($where);
                    if (count($grid) <> 1)
                        {
                            $this->view->message = "Date not in worship grid.";
                        }
                    else
                    {
                        $where = array(
                            array('sunday', ' = ', $sunday),
                            );            
                        $services = $servicemap->fetchWhere($where);
                        if (count($services) == 0)
                            $service = new Application_Model_Worshipservices;
                        else
                            $service = current($services);
                        $date = $service->sunday;
                        $service->sunday = $sunday;
                        $service->title = filter_var($formData['title'], FILTER_SANITIZE_STRING);
                        $service->presenter = filter_var($formData['presenter'], FILTER_SANITIZE_STRING);
                        $service->summary = filter_var($formData['summary'], FILTER_SANITIZE_STRING);

                        $servicemap->save($service);
                        if ($date == '')
                            $this->view->message = "New sunday $sunday written.";
                        else
                            $this->view->message = "Record $sunday re-written.";
                    }
                }
            }
        }


        $lastdate = $servicemap->max();
        $nextdate = $functions->nextSunday('mysql', $lastdate);

        $serviceform = new Application_Form_WorshipService();
        $serviceform->servicedate->setValue($nextdate);
        
        $this->view->serviceform = $serviceform;
        $this->view->style = 'zform.css';
        
    }




    public function onlinepledgeAction()
    {
        $functions = new Cvuuf_functions();
        $this->view->message = array();

        $months = array('', 'January', 'February', 'March', 'April', 'May', 'June',
          'July', 'August', 'September', 'October', 'November', 'December');
        $sched = array('', 'Weekly', 'Monthly', 'Quarterly', 'Annually', 'Other');
        
        $this->view->theme = 'private';
        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth('');
        $this->auth = $auth;
        $personid = $this->auth->memberid;
        $peoplemap = new Application_Model_PeopleMapper();
        $person = $peoplemap->find($personid);
        $firstname = $person['firstname'];
        $lastname = $person['lastname'];
        $email = $person['email'];
        $id = $person['id'];

        $hhid = $person['householdid'];
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'Member'),
            array('householdid', ' = ', $hhid),
            );                        
        $peeps = $peoplemap->fetchWhere($where);
        $numpeeps = count($peeps);
        
//var_dump($peeps); 
        $name1 = $peeps[0]->firstname . ' ' .  $peeps[0]->lastname;
//var_dump($name1);
        if (count($peeps) > 1)        
            $name2 = $peeps[1]->firstname . ' ' .  $peeps[1]->lastname;
        else 
            $name2 = '';
//var_dump($name2);
            if ($name1 <> '' || $name2 <> '')
            {
                $n = explode(' ', $name1);
                if ($name2 <> '')
                {
                    $n2 = explode(' ', $name2);
                    if ($n[1] == $n2[1])
                        $householdname = $n[0] . ' & ' . $n2[0] . ' ' . $n[1];
                    else
                        $householdname = $n[0] . ' ' . $n[1] . ' & ' . $n2[0] . ' ' . $n2[1];
                }
                else
                    $householdname = $n[0] . ' ' . $n[1];
            }

//var_dump($householdname);
        $housename = $householdname;

        $this->view->firstname = $firstname;       
        $this->view->lastname = $lastname;       
        $this->view->email = $email;       
        $this->view->id = $id;       
        $this->view->housename = $housename;


        $request = $this->getRequest();    
        if ($this->getRequest()->isPost())    
        {
            $formData = $request->getParams();
            if (isset($formData))
            {
//var_dump($formData); exit;
                $startmonth = $formData['startmonth'];
                if ($startmonth == '0')
                    $this->view->message[] = "You must have a starting month.";
                $permonth = $formData['permonth'];
                $peryear = $formData['peryear'];
                if ($permonth == '' && $peryear == '')
                    $this->view->message[] = "You must have an amount per month or year.";
                $schedule = $formData['schedule'];
                if ($schedule == '0')
                    $this->view->message[] = "You must have a payment schedule.";
                $schedother = $formData['schedother'];
                
//var_dump($schedule, $schedother);
                if ($schedule == '5' && $schedother == '')
                    $this->view->message[] = "If your schedule is 'Other' you must explain your schedule.";
                    
                if (count($this->view->message) > 0) {
                    $this->view->style = 'zform.css';
                    return;
                }

                $now = date("m/d/Y", mktime(date('H'), date('i'), 0, date('m'), date('d'),date('Y')));
                $et = "Pledge for $housename submitted $now";

                $et = $et . "\n\n<br><br>Starting month: $months[$startmonth]";
                if ($permonth <> '')
                    $et = $et . "\n<br>Dollar amount per month: $$permonth";
                else
                    $et = $et . "\n<br>Dollar amount per year: $$peryear";
                $et = $et . "\n<br>Schedule: $sched[$schedule]";
                if ($schedule == 5)
                    $et = $et . " - $schedother";
                
                $text =  "\n\nThank you for sustaining this congregation with your gifts and joining in community for the benefit of all! ";

//var_dump($et); exit;

                $now = date("Y-m-d H:i:s", mktime(date('H'), date('i'), 0, date('m'), date('d'),date('Y')));
            
            		$TEXT=$et;
                $SUBJECT="Online Pledge";
                $TO_array=array($email, 'pledgesecretary@cvuuf.org');


                $LOG_array=array('security@cvuuf.org', 'michael.talvola@gmail.com');
            
                $efunctions = new Cvuuf_emailfunctions();
                $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, array('webmail@cvuuf.org' => "CVUUF Online Pledge"));
                
                $log = $efunctions->log_email($this, 1, $totalsent, 0, 0);
                
                $this->view->message = $et;
//var_dump($TEXT);
//var_dump($SUBJECT);
//var_dump($TO_array);
//var_dump($totalsent);
//exit;
                $this->view->style = 'zform.css';
                return $this->render('onlinepledgecreated');

            }
            
        }

//var_dump($this->view->message);

        if (count($this->view->message) == 0)
            unset($this->view->message);

        $this->view->style = 'zform.css';
        
    }
    


    public function whoamiAction()
    {
        $auth = $this->auth;
        $id = $auth->memberid;
        $level = $auth->level;
        $peoplemap = new Application_Model_PeopleMapper();
        $person = $peoplemap->find($id);
        $name = $person['firstname'] . ' ' . $person['lastname'];
        
        echo "I am $name with ID $id and permissions code $level ";
        exit;
    }
    


    

        
}
