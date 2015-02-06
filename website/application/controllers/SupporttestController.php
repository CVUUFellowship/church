<?php

class SupportTestController extends Zend_Controller_Action
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
    

      public function lastYear($format = 'mysql')
      {
          $Tdate = getdate(mktime(0, 0, 0, date('m'), date('d')+8, date('Y')-1));
          $today = $Tdate['wday'];
          $TDay = $Tdate['mday'];
          $Month = $Tdate['mon'];
          $Year = $Tdate['year'];
          $MDay = $TDay - $today;
          $lastyear = mktime(0, 0, 0, $Month, $MDay, $Year);
          if ($format == 'mysql')
          {
              $lastdate = date("Y-m-d", $lastyear);
              return($lastdate);
          }
          else
              return($lastyear);      
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


        function printTags($which, $tablestr) {
        
            $hoodmap = new Application_Model_HoodsMapper();        
            $hoods = $hoodmap->fetchAll();
            $dots = array();
            foreach ($hoods as $hood)
                $dots[$hood->id] = $hood->dot;
            $tablerows = explode(';', $tablestr);
            $table = array();
            foreach ($tablerows as $tablerow)
            {
                $row = explode(',', $tablerow);
                if ($row[3] == $which)
                    $table[] = $row;
            }
            $peoplemap = new Application_Model_PeopleMapper();
            $neighborhoodsmap = new Application_Model_NeighborhoodsMapper();
            $names = array();
            foreach ($table as $tag)
            {
                $pid = $tag[0];
                $person = $peoplemap->find($pid);
                $row = new stdClass();
                $row->FirstName = $person['firstname'];
                $row->LastName = $person['lastname'];
                $row->HoodDot = $dots[0];
                if ($which <> 'Visitor' && $which <> 'Guest' && $which <> 'Staff')
                {
                    $where = array(
                        array('householdid', ' = ', $person['householdid']),
                        );            
                    $hood = current($neighborhoodsmap->fetchWhere($where));
                    $hoodid = $hood->hoodid;
                    $row->HoodDot = $dots[$hoodid];
                }
                $row->Title = $tag[4];
                $names[] = $row;
            }
            
            require_once ('EzPDF/class.cvuuflabel.php');
            if (in_array($which, array('Member', 'Guest', 'Affiliate', 'Spouse', 'Staff')))
                $which = 'regular';
            $labeltype = strtolower($which);
            $label = new Clabel($labeltype);
            $label->makeLabel($names, 'no');   /* no blank tags */
            exit;
        }



    public function nametagsAction()
    {
        $table = array();
        $tablestr = '';
        $request = $this->getRequest();
        $peoplemap = new Application_Model_PeopleMapper();
        $getvalues = $request->getParams();

        if ($this->getRequest()->isPost())
        {
            if (isset($getvalues))
            {
                if (isset($getvalues['tablestr']))
                    $tablestr = $getvalues['tablestr'];
                if (isset($getvalues['printvbutton']))
                    $this->printTags('Visitor', $tablestr);
                elseif (isset($getvalues['printfbutton']))
                    $this->printTags('NewFriend', $tablestr);
                elseif (isset($getvalues['printmbutton']))
                    $this->printTags('Member', $tablestr);
                elseif (isset($getvalues['printgbutton']))
                    $this->printTags('Guest', $tablestr);
                elseif (isset($getvalues['printobutton']))
                    $this->printTags('Staff', $tablestr);
                elseif (isset($getvalues['printsbutton']))
                    $this->printTags('Spouse', $tablestr);

                if (isset($getvalues['trimbutton']))
                {
                    if (isset($getvalues['tablestr']))
                    {
                        $tid = $getvalues['tid'];
                        $tablerows = explode(';', $tablestr);
                        $count = count($tablerows);
                        $table = array();
                        for ($i = 0; $i < $count; $i++)
                        {
                            $row = explode(',', $tablerows[$i]);
                            if ($row[0] <> $tid)
                                $table[] = $row;
                        }
                    }
                }


                if (isset($getvalues['clearbutton']))
                {
                    $table = array();
                    $tablestr = '';
                }


                if (isset($getvalues['blankbutton']))
                {
                    $file = 'visitortags';
                    $type = 'pdf';
                    $functions = new Cvuuf_functions();
                    $functions->showFile($file, $type);
                }


                elseif (isset($getvalues['newbutton']))
                {
                    $where = array(
                        array('inactive', ' <> ', 'yes'),
                        array('status', ' = ', 'Visitor'),
                        array('creationdate', ' >= ', $this->lastSunday()),
                        );
                    $new = $peoplemap->fetchWhere($where);
                    if (count($new) > 0)
                    {
                        if (isset($getvalues['tablestr']))
                        {
                            $tablestr = $getvalues['tablestr'];
                            $tablerows = explode(';', $tablestr);
                            foreach ($tablerows as $row)
                            {
                                $table[] = explode(',', $row);
                            }
                        }
                        foreach($new as $person)
                        {
                            $table[] = array($person->id, $person->firstname, $person->lastname, 'Visitor',
                              '', );
                        }
                    }
                }

                elseif (isset($getvalues['addbutton']))
                {
                    if (isset($getvalues['tablestr']))
                    {
                        $tablestr = $getvalues['tablestr'];
                        $tablerows = explode(';', $tablestr);
                        foreach ($tablerows as $row)
                        {
                            $table[] = explode(',', $row);
                        }
                    }
                    
                    if ($getvalues['pid'] > 0)
                    {
                        $pid = $getvalues['pid'];
                    }
                    elseif ($getvalues['name'] <> '')
                    {
                        $name = $getvalues['name'];
                        $pid = $this->idFromName($name);
                    }

                    if (!isset($pid))
                    {
                        $this->view->message = "Cannot process ADD request without ID# or Name.";
                    }
                    else
                    {
                        $person = $peoplemap->find($pid);
                        if ($person <> false)
                        {
                            $first = $person['firstname'];
                            $last = $person['lastname'];
                            if ($person['inactive'] == 'yes')
                                $this->view->message = "$first $last is marked Inactive.";
                            
                            $positionsmap = new Application_Model_PositionsMapper();
                            $ids = "'2', '5', '6'";
                            $where = array(
                                array('contact1', ' = ', $pid),
                                array('headingid', ' IN ', "($ids)"),
                                );
                            $position = $positionsmap->fetchWhere($where);
                            if (count($position) == 1)
                                $title = current($position)->title;
                            else
                                $title = '';
//var_dump($position); exit;                                        
                            $table[] = array($pid, $first, $last, $person['status'],
                              $title, );
                        }
                        else 
                            $this->view->message = "ID $pid does not exist.";
                    }
                }
            }
        }
        

          function cmp($a, $b)
          {
              $status = strcmp($a[3], $b[3]);
              if ($status <> 0)
                  return ($status);
              $last = strcmp($a[2], $b[2]);
              if ($last <> 0)
                  return ($last);
              return strcmp($a[1], $b[1]);
          }
        usort($table, "cmp");
        
        $this->view->oldtable = $tablestr;
        $this->view->table = $table;
        $this->view->style = 'zform.css';
    
    }





    public function memberstatsAction()
    {
        $peoplemap = new Application_Model_PeopleMapper();
        $visitsmap = new Application_Model_VisitsMapper();
        $connectionsmap = new Application_Model_ConnectionsMapper();
        
        $earliest = $this->lastFourmonths();
        $lastyear = $this->lastYear();

        $where = array(
            array('creationdate' , ' > ', $earliest),
            array('status' , ' <> ', 'Child'),
            );
        $people4months = $peoplemap->fetchWhere($where);
        $where = array(
            array('inactive' , ' <> ', 'yes'),
            array('status' , ' = ', 'Visitor'),
            );
        $activevisitors = $peoplemap->fetchWhere($where);
        $this->view->visitors = count($activevisitors);
        $this->view->visitors4months = count($people4months);
        
        $count1 = 0;
        $countmore = 0;
        foreach ($people4months as $person)
        {
            $where = array(
                array('personid' , ' = ', $person->id),
                );
            $visitscount = count($visitsmap->fetchWhere($where));
            if ($visitscount == 1)
                $count1++;
            elseif ($visitscount > 1)
                $countmore++;
        }

        $this->view->once4months = $count1;
        $this->view->multi4months = $countmore;

        $where = array(
            array('inactive' , ' <> ', 'yes'),
            array('status' , ' = ', 'Member'),
            );
        $this->view->members = count($peoplemap->fetchWhere($where));

        $where = array(
            array('inactive' , ' <> ', 'yes'),
            array('membershipdate' , ' > ', $earliest),
            );
        $this->view->members4months = count($peoplemap->fetchWhere($where));
        

        $where = array(
            array('inactive' , ' <> ', 'yes'),
            array('status' , ' = ', 'NewFriend'),
            );
        $newfriends = $peoplemap->fetchWhere($where);
        $this->view->newfriends = count($newfriends);
        
        $countnew = 0;
        $countold = 0;
        foreach ($newfriends as $person)
        {
            $where = array(
                array('peopleid', ' = ', $person->id),
                array('frienddate', ' > ', $earliest),
                );            
            $connection = $connectionsmap->fetchWhere($where);

            if (count($connection) > 0)
                $countnew++;

            $where = array(
                array('peopleid', ' = ', $person->id),
                array('frienddate', ' < ', $lastyear),
                );            
            $connection = $connectionsmap->fetchWhere($where);
            if (count($connection) > 0)
                $countold++;
        }

        $this->view->newnewfriends = $countnew;
        $this->view->oldnewfriends = $countold;

        $where = array(
            array('inactive' , ' <> ', 'yes'),
            array('status' , ' = ', 'Guest'),
            );
        $guests = $peoplemap->fetchWhere($where);
        $this->view->guests = count($guests);
        
        $where = array(
            array('inactive' , ' <> ', 'yes'),
            array('status' , ' = ', 'Affiliate'),
            );
        $affiliates = $peoplemap->fetchWhere($where);
        $this->view->affiliates = count($affiliates);
        
               
        $count1 = 0;
        $countmore = 0;
        foreach ($activevisitors as $person)
        {
            $where = array(
               array('personid' , ' = ', $person->id),
                );
            $visitscount = count($visitsmap->fetchWhere($where));
//echo "<br>", $person->id, " count is ", $visitscount;

            if ($visitscount == 1)
                $count1++;
            elseif ($visitscount > 1)
                $countmore++;
        }

//echo "<br><br> single count is ", $count1;
//var_dump($activevisitors);
//exit;

        $this->view->oncevisitors = $count1;
        $this->view->multivisitors = $countmore;
   
    }

    public function librarycatalogAction()
    {



          function fitWidth($theField, $theWidth, $maxWidth, $pdf){

            unset ($parts);
            $partNum=0;
            if ($theWidth<$maxWidth){
              return array($theField, NULL);
            }
            $theLen=strlen($theField);
            $charWidth=$theWidth/$theLen;       
            $partLen=floor($maxWidth/$charWidth);       
            $partFirst=substr($theField, 0, $partLen);
            $partRest=substr($theField, $partLen);
            $spEnd=strrpos($partFirst, ' ');
            $hyEnd=strrpos($partFirst, '-');
            if ($hyEnd>$spEnd) $partEnd=$hyEnd+1;
            else $partEnd=$spEnd+1;
            if ($partEnd<1)
              $parts[0]=$partFirst;
            else {
              $parts[0]=substr($theField, 0, $partEnd);
              $partRest=substr($theField, $partEnd);
            }  
            $parts[0]=substr($theField, 0, $partEnd);
            $partRest=substr($theField, $partEnd);

            $start1=$partEnd;
            $restLen=$theLen-$start1;
            
            if ($restLen<$partLen)
              $parts[1]=$partRest;
            else {  
              $partFirst=substr($theField, $start1, $partLen);
              $partRest=substr($theField, $start1+$partLen);
              $spEnd=strrpos($partFirst, ' ');
              $hyEnd=strrpos($partFirst, '-');
              if ($hyEnd>$spEnd) $partEnd=$hyEnd+1;
              else $partEnd=$spEnd+1;
              if ($partEnd<1)
                $parts[1]=$partFirst;
              else {
                $parts[1]=substr($theField, $start1, $partEnd);
                $partRest=substr($theField, $start1+$partEnd);
              }  
              $start2=$start1+$partEnd;
              $restLen=$theLen-$start2;
              if ($restLen<$partLen)
                $parts[2]=$partRest;
              else {
                $partFirst=substr($theField, $start2, $partLen);
                $partRest=substr($theField, $start2+$partLen);
                $spEnd=strrpos($partFirst, ' ');
                $hyEnd=strrpos($partFirst, '-');
                if ($hyEnd>$spEnd) $partEnd=$hyEnd+1;
                else $partEnd=$spEnd+1;
                if ($partEnd<1)
                  $parts[2]=$partFirst;
                else
                  $parts[2]=substr($theField, $start2, $partEnd);
              }
            }
            return $parts;
          }




        $functions = new Cvuuf_functions();

      	$Tdate = getdate(mktime(0,0,0, date('m'), date('d'),date('Y')));
      	$theDate = date("l, F j, Y", mktime(0,0,0, date('m'), date('d'),date('Y')));
        $fontSize=9;
        $CENTER=array("justification"=>"center");

        $request = $this->getRequest();
        $catmap = new Application_Model_LibCatalogMapper();
        $getvalues = $request->getParams();
        $catNumber = 0;

        if ($this->getRequest()->isPost())
        {
            if (isset($getvalues))
            {

                $print = '';
                $print100 = '';
                if (isset($getvalues['print100button']))
                    $print100 = $getvalues['print100button'];
                if (isset($getvalues['printbutton']))
                    $print = $getvalues['printbutton'];
                if (isset($getvalues['addbutton']))
                {
                    $cat = new Application_Model_LibCatalog();
                    $catmap->save($cat);
                    $catNumber = $cat->number;
                    $where = array(
                      array('number', ' = ', $catNumber),
                      );            
                    $cat = current($catmap->fetchWhere($where));                    
                    $thedate = $this->today();
                    $cat->createdate = $thedate;
                    $catmap->save($cat);
                    $getvalues['findbutton'] = 'here';
                }

                if (isset($getvalues['findbutton']))
                {
                    if ($catNumber == 0)
                    {
                        if (isset($getvalues['findnumber']))
                            $catNumber = $getvalues['findnumber'];
                    }
                    if ($catNumber == 0)
                    {
                        $this->view->message = "No number given, nothing to do.";
                    }
                    else
                    {
                        $catarray = $catmap->find($catNumber);

                        if (count($catarray) < 1)
                        {
                            $this->view->message = "$catNumber not found in catalog.";
                            $this->view->style = 'zform.css';
                            $catalog = $catmap->fetchAll();
                            $this->view->count = count($catalog);
                            return $this->render('librarycatalog');
                        }

                        $entryForm = new Application_Form_LibraryCatalogEntry();
                        $entryForm->number->setAttrib('disabled', true);
                        $entryForm->populate($catarray);
                        $entryForm->num->setValue($catNumber);
                        $this->view->style = 'zform.css';
                        $this->view->entryForm = $entryForm;
                        $values = implode('|', $catarray);
                        $keys = implode('|', array_keys($catarray));
                        $entryForm->values->setValue($values);
                        $entryForm->keys->setValue($keys);
                                        
                        return $this->render('catchange');
                    }
                }
                
                elseif ($print100 == 'PRINT' || $print == 'PRINT')
                {
                    if (isset($getvalues['call#s']))
                        $callField = $getvalues['call#s'];
                    elseif (isset($getvalues['id']))
                    {
                        $num = $getvalues['id'];
                  		  $nn = count($num);
                  		  $callField = '';
                        for($i = 0; $i < $nn; $i++) 
                        {
                  		    if ($i > 0)
                              $callField .= ' ' . $num[$i];
                          else
                              $callField = $num[0];
                        }
                    }
                    else
                    {
                        $this->view->message = "Nothing selected.";
                        $where = array(
                                array('title', ' <> ', ''),
                                array('createdate', ' <> ', "0000-00-00"),
                                      );            
                        $catalog = $catmap->fetchWhere($where, array('number'));
                        $this->view->check = 'none';
                        $this->view->catalog = $catalog;
                        $this->view->style = 'zform.css';
                        return $this->render('library100');       
                    }

                    $calls=explode(" ", $callField);
                    require_once 'EzPDF/class.ezpdf.php';
                    $pdf = new Cezpdf('LETTER', 'portrait');
                    $pdf->selectFont('./fonts/Helvetica.afm');
                    $HEADER1='<b>CVUUF LIBRARY</b>';
                    $h1width=$pdf->getTextWidth(14,$HEADER1);
                    $HEADER1='<b>CVUUF LIBRARY</b>';
                    $pdf->ezText($HEADER1, 14, $CENTER);
                    $HEADER2='Selected Call Numbers';
                    $pdf->ezText($HEADER2, 12, $CENTER);
                    $pdf->ezText('   ',12);
                    $HEADER3='         CALL_NO               NUM    TITLE';
                    $pdf->ezText($HEADER3, 12);
                    $yposition=$pdf->ezText('   ',60);
                    
                    $hyp=strpos($calls[0], '-');
                    if ($hyp > 0)
                    {
                        $call=$calls[0];
                        $from=substr($call, 0, $hyp);
                        $to=substr($call, $hyp+1);
                        $where = array(
                            array('number', ' >= ', $from),
                            array('number', ' <= ', $to),
                            );
                        $order = array('number');
                        $nums = $catmap->fetchWhere($where, $order);            
                        unset($calls);
                        $i=0;
                        foreach ($nums as $num)
                            $calls[$i++] = $num->number;        
                    }
              
                    
                    foreach ($calls as $call){
                        $info = $catmap->find($call);
                        $pdf->addText( 60, $yposition, 12, $info['callnumber'] );
                        $pdf->addText( 160, $yposition, 8, $info['number'] );
                        $pdf->addText( 196, $yposition, 8, $info['title'] );
                        $yposition=$pdf->ezText('   ',60);
                     }
        	
                    $pdf->ezStream();
                    exit;
                }
    
                elseif (isset($getvalues['printbutton']))
                {
                    if ($print <> '')
                    {
                        $widthTitle=312;
                        $widthCall=50;
                        $widthAuthor=95;
                        $widthSubject=95;
                    
                        $FOOTERd="$theDate";
                    
                        $leftMargin=30;
                        $rightMargin=582;
                        $bottomMargin=30;
                        $topMargin=762;
                    
                        $pageNo=0;
                    
                        $filter = null;
                        switch($print)
                        {
                          case 'TITLE':
                            $fields="Title, CallNumber, Author, Subject1";
                            $order=array("title");
                            $show="Title Order";
                            $where = array(
                                    array('title', ' <> ', ''),
                                    );            
                            $xTitle=$leftMargin;
                            $xCall=$xTitle+$widthTitle;
                            $xAuthor=$xCall+$widthCall;
                            $xSubject=$xAuthor+$widthAuthor;
                            break;
                          case 'CALL':
                            $fields="Title, CallNumber, Author, Subject1";
                            $order=array("callnumber");
                            $show="Call Number Order";
                            $where = array(
                                    array('title', ' <> ', ''),
                                    );            
                            $xCall=$leftMargin;
                            $xTitle=$xCall+$widthCall;
                            $xAuthor=$xTitle+$widthTitle;
                            $xSubject=$xAuthor+$widthAuthor;
                            break;
                          case 'AUTHOR':
                            $fields="Title, CallNumber, Author, Subject1";
                            $order=array("author");
                            $show="Author Order";
                            $where = array(
                                    array('title', ' <> ', ''),
                                    );            
                            $xAuthor=$leftMargin;
                            $xTitle=$xAuthor+$widthAuthor;
                            $xCall=$xTitle+$widthTitle;
                            $xSubject=$xCall+$widthCall;
                            break;
                          case 'RECENT':
                            $fields="Title, CallNumber, Number AS Author, CreateDate AS Subject1";
                            $order=array('create');
                            $show="Most Recent First";
                            $where = array(
                                    array('title', ' <> ', ''),
                                    array('createdate', ' <> ', "0000-00-00"),
                                    );            
                            $xTitle=$leftMargin;
                            $xCall=$xTitle+$widthTitle;
                            $xAuthor=$xCall+$widthCall;
                            $xSubject=$xAuthor+$widthAuthor;
                            break;

                        }


                        if ($print<>'All') 
                        {        
                            $catalog = $catmap->fetchWhere($where, $order);
                            require_once 'EzPDF/class.ezpdf.php';
                            $pdf = new Cezpdf('LETTER', 'portrait');
                            $pdf->selectFont('./fonts/Helvetica.afm');
    
                   
                            $dwidth=$pdf->getTextWidth(12,$theDate);
                      
                        // Put out header here
                            $HEADER1='<b>CVUUF LIBRARY</b>';
                            $h1width=$pdf->getTextWidth(14,$HEADER1);
                            $HEADER2="<b>$show</b>";
                            $h2width=$pdf->getTextWidth(14,$HEADER2);
                            $HEADER3t='<b>TITLE</b>';
                            $h3twidth=$pdf->getTextWidth(12,$HEADER3t);
                            $HEADER3c='<b>CALL#</b>';
                            $h3cwidth=$pdf->getTextWidth(12,$HEADER3c);
                            if ($print=='RECENT') $HEADER3a='<b>NUMBER</b>';
                            else $HEADER3a='<b>AUTHOR</b>';
                            $h3awidth=$pdf->getTextWidth(12,$HEADER3a);
                            if ($print=='RECENT') $HEADER3s='<b>CREATED</b>';
                            else $HEADER3s='<b>SUBJECT</b>';
                            $h3swidth=$pdf->getTextWidth(12,$HEADER3s);
                            $pdf->ezText($HEADER1, 14, $CENTER);
                            $pdf->ezText($HEADER2, 12, $CENTER);
                            $pdf->ezText('   ',12);
                      
                            $nameRow=$topMargin-60;
                      			$pdf->addText($xTitle,$nameRow,$fontSize,$HEADER3t);
                      			$pdf->addText($xCall,$nameRow,$fontSize,$HEADER3c);
                      			$pdf->addText($xAuthor,$nameRow,$fontSize,$HEADER3a);
                      			$pdf->addText($xSubject,$nameRow,$fontSize,$HEADER3s);
                            $pdf->setLineStyle(2);
                            $pdf->line($leftMargin, $nameRow-4, $rightMargin, 700);
                            $pdf->ezText('   ',14);
                      
                            $pdf->setLineStyle(1);
                            $pdf->line($leftMargin, $bottomMargin+10, $rightMargin, $bottomMargin+10);
                      			$pdf->addText($leftMargin,$bottomMargin,7,$FOOTERd);
                      			$FOOTERp="Page ".++$pageNo;
                      			$position=$rightMargin-$pdf->getTextWidth(7,$FOOTERp);
                      			$pdf->addText($position,$bottomMargin,7,$FOOTERp);
                            $yposition=$pdf->ezText('    ', 18);
                            unset($table);
                            $number=0;
                            foreach ($catalog AS $record)
                            {
                                $number++;
                                $theTitle=strtoupper($record->title);
                                $theWidth=$pdf->getTextWidth($fontSize,$theTitle);
                                $allTitles=fitWidth($theTitle, $theWidth, $widthTitle-8, $pdf);
                                $pdf->addText( $xTitle, $yposition, $fontSize, $allTitles[0]);
                        
                                $pdf->addText( $xCall, $yposition, $fontSize, $record->callnumber);
                        
                                if ($print <> 'RECENT')
                                    $theAuthor=$record->author;
                                else
                                    $theAuthor=$record->number;
                                $theWidth=$pdf->getTextWidth($fontSize,$theAuthor);
                                $allAuthors=fitWidth($theAuthor, $theWidth, $widthAuthor-4, $pdf);
                                $pdf->addText( $xAuthor, $yposition, $fontSize, $allAuthors[0]);
                        
                                if ($print <> 'RECENT')
                                    $theSubject=$record->subject1;
                                else
                                    $theSubject=$record->createdate;
                                $theWidth=$pdf->getTextWidth($fontSize,$theSubject);
                                $allSubjects=fitWidth($theSubject, $theWidth, $widthSubject-4, $pdf);
                        
                                $pdf->addText( $xSubject, $yposition, $fontSize, $allSubjects[0]);
                        
                                if ($allTitles[1]<>'' || $allAuthors[1]<>'' || $allSubjects[1]<>''){
                        		      $yposition=$pdf->ezText('    ', $fontSize);
                                  $pdf->addText( $xTitle, $yposition, $fontSize, $allTitles[1]);
                                  $pdf->addText( $xAuthor, $yposition, $fontSize, $allAuthors[1]);
                                  $pdf->addText( $xSubject, $yposition, $fontSize, $allSubjects[1]);
                                }
                        
                                if (isset($allTitles[2]) || isset($allAuthors[2]) || isset($allSubjects[2])){
                        		      $yposition=$pdf->ezText('    ', $fontSize);
                                  if (isset($allTitles[2])) $pdf->addText( $xTitle, $yposition, $fontSize, $allTitles[2]);
                                  if (isset($allAuthors[2])) $pdf->addText( $xAuthor, $yposition, $fontSize, $allAuthors[2]);
                                  if (isset($allSubjects[2])) $pdf->addText( $xSubject, $yposition, $fontSize, $allSubjects[2]);
                                }
                        		    $yposition=$pdf->ezText('    ', 18);
                         		    if ($yposition<62)
                                {
                              	    if ($print=='RECENT') break;
                          		      $pdf->ezNewPage();
                                    $pdf->ezText($HEADER1, 14, $CENTER);
                                    $pdf->ezText($HEADER2, 12, $CENTER);
                                    $pdf->ezText('   ',12);
                          
                                    $nameRow=$topMargin-60;
                                    $pdf->addText($xTitle,$nameRow,$fontSize,$HEADER3t);
                                    $pdf->addText($xCall,$nameRow,$fontSize,$HEADER3c);
                              			$pdf->addText($xAuthor,$nameRow,$fontSize,$HEADER3a);
                              			$pdf->addText($xSubject,$nameRow,$fontSize,$HEADER3s);
                                    $pdf->setLineStyle(2);
                                    $pdf->line($leftMargin, $nameRow-4, $rightMargin, 700);
                                    $pdf->ezText('   ',14);
                          
                                    $pdf->setLineStyle(1);
                                    $pdf->line($leftMargin, $bottomMargin+10, $rightMargin, $bottomMargin+10);
                                    $pdf->addText($leftMargin,$bottomMargin,7,$FOOTERd);
                              			$FOOTERp="Page ".++$pageNo;
                              			$position=$rightMargin-$pdf->getTextWidth(7,$FOOTERp);
                              			$pdf->addText($position,$bottomMargin,7,$FOOTERp);
                                    $yposition=$pdf->ezText('    ', 18);
                                }
        		    
                            }
                            $pdf->ezStream();
                            exit;
                        }

                    }
                }
                elseif (isset($getvalues['checkbutton']))
                {
                    $where = array(
                            array('title', ' <> ', ''),
                            array('createdate', ' <> ', "0000-00-00"),
                                  );            
                    $catalog = $catmap->fetchWhere($where, array('number'));
                    $this->view->check = 'none';
                    $this->view->catalog = $catalog;
                    $this->view->style = 'zform.css';
                    return $this->render('library100');       
                }
                
            }
        }


        $catalog = $catmap->fetchAll();
        $this->view->count = count($catalog);
        $this->view->style = 'zform.css';       
    }   





    public function librarycatchangeAction()
    {
        $functions = new Cvuuf_functions();
        $entryForm = new Application_Form_LibraryCatalogEntry();
        $request = $this->getRequest();
        $this->view->explain = '';
        $styleChange = 'font-weight:bold; color:green;';
        $styleError = 'font-style:italic; font-weight:bold; color:darkred;';

        $catmap = new Application_Model_LibCatalogMapper();

        if ($this->getRequest()->isPost())
        {
            $formData = $request->getParams();
    /* enter button */
            if (isset($formData['ebutton']))
            {
                $origkeys = explode('|', $formData['keys']);
                $origvalues = explode('|', $formData['values']);
                $origData = array_combine($origkeys, $origvalues);
                $number = $origData['number'];
                $this->view->explain = "Labels of changed fields are <span style='$styleChange'> bold green</span>; 
                  labels of fields with errors detected are <span style='$styleError'> bold italic red</span>.";
                foreach ($origData as $key => $value) 
                {
                    if (!in_array($key, array('number', 'createdate', 'timestamp')))
                    {
                        if ($formData[$key] <> $value)
                        {
                            $entryForm->getElement($key)->getDecorator('label')->setOption('style', $styleChange);
                        }
                    }
                }
                
                $entryForm->number->setAttrib('disabled', true);
                $entryForm->populate($formData);
                $entryForm->number->setValue($number);
                $entryForm->num->setValue($number);

                $avalues = array();
                $akeys = array();
                foreach ($origData as $key =>$value)
                {
                    $akeys[]  = $key;
                    if (isset($formData[$key]))
                        $avalues[] = $formData[$key];
                    else
                        $avalues[] = '';
                }
                $akeys[] = 'number';
                $avalues[] = $formData['num'];
                $akeys[] = 'createdate';
                $avalues[] = $origData['createdate'];
                $values = implode('|', $avalues);
                $keys = implode('|', $akeys);
                $entryForm->values->setValue($values);
                $entryForm->keys->setValue($keys);

                $this->view->style = 'zform.css';
                $this->view->entryForm = $entryForm;
                return $this->render('catchange');
            }
            


    /* clear button */
            if (isset($formData['xbutton']))
            {
                $number = $formData['num'];
                unset($formData);
            }

            


    /* remove button */
            if (isset($formData['rbutton']))
            {
                $number = $formData['num'];
                $catmap->delete($number);
                $this->view->message = "Catalog entry $number deleted.";
                $catalog = $catmap->fetchAll();
                $this->view->count = count($catalog);
                $this->view->style = 'zform.css';
                return $this->render('librarycatalog');

            }

                

  /* commit button */
            if (isset($formData['cbutton']))
            {
                $origkeys = explode('|', $formData['keys']);
                $origvalues = explode('|', $formData['values']);
                $origData = array_combine($origkeys, $origvalues);
                $number = $origData['number'];
                $catNumber = $origData['number'];
                $columns = array('title', 'author', 'callnumber', 
                  'subject1', 'subject2', 'subject3', 'subject4', 
                  'publisher', 'date', 'price', 
                  'number', 'createdate');
                $cat = new Application_Model_LibCatalog();
                foreach($columns as $key)
                {
                    $cat->$key = $origData[$key];
                }
                $catmap->save($cat);
              }


    /* done button */
            if (isset($formData['dbutton']))
            {
                return $this->redirect('/support/librarycatalog');
            }
        }
        if (!isset($formData))
        {
            $origData = $catmap->find($number);
            $entryForm->populate($origData);
        }

        else
        {
            $entryForm->populate($formData);
        }
        $values = implode('|', $origData);
        $keys = implode('|', array_keys($origData));
        $entryForm->values->setValue($values);
        $entryForm->keys->setValue($keys);
        $entryForm->populate(array('num' => $number)) ;
        $entryForm->populate(array('number' => $number)) ;
        $entryForm->number->setAttrib('disabled', true);
               
        $this->view->style = 'zform.css';
        $this->view->entryForm = $entryForm;
        return ($this->render('catchange'));
    
    }



    public function makePeopleGrid($gridmap, $peopleids, $peoplearray, $tablearray, $pid, $statusmap, $earlylate=TRUE)
    {

        $peoplemap = new Application_Model_PeopleMapper();
        $initialsBoxes = '';
        $initialsList = '';

        $startdate = $this->lastSunday();
        $where = array(
            array('servicedate' , ' >= ', $startdate),
            array('sunday' , ' = ', 'yes'),
        );
        $gridentries = $gridmap->fetchWhere($where);

      	$statusCodes[''] = ' ';
        $statusCodes['away'] = 'x';
      	$statusCodes['clear'] = '&nbsp;';
      	$statusCodes['available'] = '&nbsp;';
      	$statusCodes['scheduled'] = 'S';

        foreach ($peopleids as $id)
        {
            if ($id->id == $pid)
                $checked = ' checked';
            else
                $checked = '';
            $person = $peoplemap->find($id->id);
            $initials = $person['firstname'][0] . substr($person['lastname'], 0, 2);
        		$initialsList = $initialsList . '<th>' . $initials;
            $initialsBoxes = $initialsBoxes . '<td><input type="checkbox" name="pid[]" value=' . $id->id . $checked . '>';
      	}
        $this->view->initialsBoxes = $initialsBoxes;
        $this->view->initialsList = $initialsList;

        $countDates = 0;
      	foreach($gridentries as $gridentry) 
        {
            if ($countDates++ > 15)
                break;
        		$dateID = $gridentry->id;
            $peoparray = array();
            foreach ($peopleids as $personid)
            {
                $id = $personid->id;
                $where = array(
                    array('dateid' , ' = ', $dateID),
                    array('peopleid' , ' = ', $id),
                );
                $starray = $statusmap->fetchWhere($where);
                if (count($starray) == 1) 
                {
                    $theStatus = current($starray);
            		    if ($earlylate)
                        $column = $statusCodes[$theStatus->early] . '|' .
                				    $statusCodes[$theStatus->late];
                    else
                        $column = $statusCodes[$theStatus->early];

            		}
                else 
                    $column = '&nbsp;';
                $peoparray[] = $column;
            }
            $tablearray[] = array($dateID, $gridentry->servicedate, $peoparray);
        }

        foreach ($peopleids as $id)
        {
            $person = $peoplemap->find($id->id);
            $name = $person['firstname'] . ' ' . $person['lastname'];
            $initials = $person['firstname'][0] . substr($person['lastname'], 0, 2);
            $peoplearray[] = array($id->id, $name, $initials);
      	}
        return array($peoplearray, $tablearray);
    }




    public function processPeople($getvalues, $peoplemap, $person, $personmap, $gridmap, $statmap, $statobj)
    {

        if ($this->getRequest()->isPost())
        {
            if (isset($getvalues['addbutton']))
            {
                if ($getvalues['addid'] > 0)
                    $pid = $getvalues['addid'];
                elseif ($getvalues['name'] <> '')
                {
                    $name = $getvalues['name'];
                    $pid = $this->idFromName($name);
                }
    
                if (!isset($pid))
                {
                    $this->view->message = "Cannot process ADD request without ID# or Name.";
                }
                else
                {
                    $person->id = $pid;
                    $personmap->save($person, 'new');
                }
            }
            elseif (isset($getvalues['removebutton']))
            {
                if (isset($getvalues['rmpid']))
                {
                    $ids = ($getvalues['rmpid']);
                    if (count($ids) > 0)
                    {
                        $this->view->message = array();
                        foreach ($ids as $id)
                        {
                            $person = $peoplemap->find($id);
                            $name = $person['firstname'] . ' ' . $person['lastname'];
                            $person = $personmap->find($id);
                            $personmap->delete($id);
                            $this->view->message[] = "$name removed from the list.";
                        }
                    }
                    else
                        $this->view->message = "No ID selected.";
                }
            
            }
            
            else
            {
              $person = $getvalues['pid'];
              if (count($person) <> 1)
                  $this->view->message = "There must be exactly 1 person selected.";
              else
              {
                  $pid = $person[0];
                  $dates = $getvalues['did'];
                  if (count($dates) == 0)
                      $this->view->message = "No dates selected, nothing to do.";
                  else
                  {
                      if (isset($getvalues['status']))
                      {
                          $early = $late = strtolower($getvalues['status']);
                      }  
                      else
                      {
                          if (isset($getvalues['early']))
                              $early = strtolower(substr($getvalues['early'], 6));
                          else
                              $early = ' ';
                          if (isset($getvalues['late']))
                              $late = strtolower(substr($getvalues['late'], 5));
                          else
                              $late = ' ';
                      }
                      if ($early == 'clear')
                          $early = 'available';
                      if ($late == 'clear')
                          $late = 'available';
                          
                      $initstatus = clone $statobj;
                      foreach($dates as $date)
                      {
                          $gridentry = $gridmap->find($date);
                          $dateid = $gridentry['id'];
                          $olddates[] = $dateid;
                          $where = array(
                              array('dateid' , ' = ', $dateid),
                              array('peopleid' , ' = ', $pid),
                          );
                          $status = $statmap->fetchWhere($where);

                          if (count($status) == 0)
                          {
                            /* no old entry */
                              $status = clone $initstatus;
                              $status->peopleid = $pid;
                              $status->dateid = $dateid;
                              $status->early = $early;
                              $status->late = $late;
                              $statmap->save($status);
                          }
                          else
                          {

                              $status = current($status);
                              if ($early <> ' ')
                                  $status->early = $early;
                              if ($late <> ' ')
                                  $status->late = $late;
                              if ($early <> 'available' || $late <> 'available')
                                  $statmap->save($status);
                              else
                                  $statmap->delete($status->id);
  
                          }
                      }
                  }
              }
            }            
        }
    }




    public function avAction()
    {
        $request = $this->getRequest();
        $getvalues = $request->getParams();
        $peoplemap = new Application_Model_PeopleMapper();
        $gridmap = new Application_Model_WorshipGridMapper();

        $pid = 0;
        $olddates = array();

        $avpmap = new Application_Model_AVPeopleMapper();
        $avsmap = new Application_Model_AVStatusMapper();
        $avperson = new Application_Model_AVPeople();
        $avstatus = new Application_Model_AVStatus();
        $this->processPeople($getvalues, $peoplemap, $avperson, $avpmap, $gridmap, $avsmap, $avstatus);
        
        $people = array();
        $table = array();
        $statobj = new Application_Model_AVStatus();
        $grid = $this->makePeopleGrid($gridmap, $avpmap->fetchAll(), $people, $table, $pid, $avsmap, TRUE);        

        $this->view->people = $grid[0];
        $this->view->olddates = $olddates;
        $this->view->table = $grid[1];
        $this->view->style = 'zform.css';       
        
    }


    public function emAction()
    {
        $request = $this->getRequest();
        $getvalues = $request->getParams();
        $peoplemap = new Application_Model_PeopleMapper();
        $gridmap = new Application_Model_WorshipGridMapper();

        $pid = 0;
        $olddates = array();

        $empmap = new Application_Model_EMPeopleMapper();
        $emsmap = new Application_Model_EMStatusMapper();
        $emperson = new Application_Model_EMPeople();
        $emstatus = new Application_Model_EMStatus();
        $this->processPeople($getvalues, $peoplemap, $emperson, $empmap, $gridmap, $emsmap, $emstatus);
        
        $people = array();
        $table = array();
        $statobj = new Application_Model_EMStatus();
        $grid = $this->makePeopleGrid($gridmap, $empmap->fetchAll(), $people, $table, $pid, $emsmap, FALSE);        

        $this->view->people = $grid[0];
        $this->view->olddates = $olddates;
        $this->view->table = $grid[1];
        $this->view->style = 'zform.css';       
        
    }



    public function welcomingAction()
    {
        $request = $this->getRequest();
        $getvalues = $request->getParams();
        $peoplemap = new Application_Model_PeopleMapper();
        $gridmap = new Application_Model_WorshipGridMapper();

        $pid = 0;
        $olddates = array();

        $wlpmap = new Application_Model_WelcomingPeopleMapper();
        $wlsmap = new Application_Model_WelcomingStatusMapper();
        $wlperson = new Application_Model_WelcomingPeople();
        $wlstatus = new Application_Model_WelcomingStatus();
        $this->processPeople($getvalues, $peoplemap, $wlperson, $wlpmap, $gridmap, $wlsmap, $wlstatus);
        
        $people = array();
        $table = array();
        $statobj = new Application_Model_WelcomingStatus();
        $grid = $this->makePeopleGrid($gridmap, $wlpmap->fetchAll(), $people, $table, $pid, $wlsmap, TRUE);        

        $this->view->people = $grid[0];
        $this->view->olddates = $olddates;
        $this->view->table = $grid[1];
        $this->view->style = 'zform.css';       
        
    }




    public function sextonsAction()
    {
        $request = $this->getRequest();
        $getvalues = $request->getParams();
        $peoplemap = new Application_Model_PeopleMapper();
        $gridmap = new Application_Model_WorshipGridMapper();

        $pid = 0;
        $olddates = array();

        $sextonspmap = new Application_Model_SextonsPeopleMapper();
        $sextonssmap = new Application_Model_SextonsStatusMapper();
        $sextonsperson = new Application_Model_SextonsPeople();
        $sextonsstatus = new Application_Model_SextonsStatus();

        $this->processPeople($getvalues, $peoplemap, $sextonsperson, $sextonspmap, $gridmap, $sextonssmap, $sextonsstatus);
        
        $people = array();
        $table = array();
        $grid = $this->makePeopleGrid($gridmap, $sextonspmap->fetchAll(), $people, $table, $pid, $sextonssmap, TRUE);        

        $this->view->people = $grid[0];
        $this->view->olddates = $olddates;
        $this->view->table = $grid[1];
        $this->view->style = 'zform.css';       
        
    }


    public function testAction()
    {


    }   
    


}