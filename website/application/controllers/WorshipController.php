<?php

class WorshipController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }


    public function sermonsAction()
    {

        $functions = new Cvuuf_functions();
        $sermons = $functions->findPublicFiles('sermontexts');
        rsort($sermons);

        $table = array();
        foreach ($sermons as $sermon)
        {
            $size = strlen($sermon);
            $suffix = substr($sermon, $size - 4);
//echo "SUFFIX IS $suffix <br>";
            if ($suffix <> 'iles')
            {
                $entry = substr($sermon, 0, strlen($sermon) - 4);
                $title = substr($entry, 10);
                $table[] = array(substr($sermon, 0, 10), $functions->showDate($sermon), $title);
            }
        }
        
        $this->view->sermons = $table;
    }
    


    public function showsermonAction()
    {

        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            $date = $getvalues['which'];
        }
        $functions = new Cvuuf_functions();
        $sermons = $functions->findPublicFiles('sermontexts');
        foreach ($sermons as $sermon)
        {
            if (substr($sermon, 0, 10) == $date)
            {
                $filename = $functions->findPublicFile($sermon, 'sermontexts');
                $size = strlen($filename);
                $suffix = substr($filename, $size - 4);
//echo "`$suffix`";
//var_dump($filename);
                if ($suffix <> 'iles')
                {
                    $fp = fopen($filename, "rb");
                    $text = fread($fp, filesize($filename));
                    fclose($fp);
//    var_dump(substr($text, 0, 100)); exit;
                    $charset = strpos($text, 'charset=');
                    $endset = strpos(substr($text, $charset), '"') + $charset;
    //echo "charset= IS AT $charset  TRAILING QUOTE IS AT $endset <br>";
                    $start = $charset + 8;
                    $size = $endset - $start;
    //echo "SIZE IS $size <br>";
                    $type = strtolower(substr($text, $start, $size));
    //echo "TYPE IS `$type` <br>";
                    if (substr($type, 0, 3) <> 'utf')
                    {
    //    echo "CONVERTING TO UTF-8 <br>";
    
    //echo "START $start  SIZE $size <br>";
                        $utftext = substr_replace($text, 'utf-8          ', $charset + 8, $size);
                        $utftext = utf8_encode($utftext);
    //var_dump(substr($utftext, 0, 100));
    //exit;    
                    }
                    else
                        $utftext = $text;
    //echo "Charset found at $charset.  Type is '$type' <br>";
    
    //var_dump($text); exit;
                    $this->view->sermon = $utftext;
                    break;
                }
            }
        }
    }


    


    public function audiosermonsAction()
    {
    
            function file2($filename) 
            {
                   $fp = fopen($filename, "rb");
                   $buffer = fread($fp, filesize($filename));
                   fclose($fp);
                   $lines = preg_split("/\r?\n|\r/", $buffer);
                   return $lines;
            }
            
        $URLBASE = $_SERVER["DOCUMENT_ROOT"];
       	$theFile=$URLBASE . "/media/sermons/sermons.html";
//echo "File is ", $theFile, '<br>'; exit;
        $theLines = file2($theFile);

        $count = sizeof($theLines);
        for ($i=0; $i<$count; $i++)
            if (strtolower(substr($theLines[$i], 0, 16)) == '<div class="item') break;

        $this->view->count = $count;
        $this->view->start = $i;
        $this->view->lines = $theLines;
        $this->view->style = 'sermons.css';

//echo "COUNT $count  START $i <br>";
//var_dump($theLines);
//exit;
    }




    


    public function upcomingAction()
    {
    
        $functions = new Cvuuf_functions();
      	$today = $functions->today();
      	$day = substr($today, 8, 2);
      	$month = substr($today, 5, 2);
      	$year = substr($today, 0, 4);
      	$firstOfMonth = date("w", mktime(0, 0, 0, $month , 1, $year ));
      	if ($firstOfMonth > 0 )
      		  $firstSunday = 8 - $firstOfMonth;
      	else 
            $firstSunday = 1;
      	if ($firstSunday < 10)
      		  $firstSunday = '0' . $firstSunday;
        $sunday = $year . '-' . $month . '-' . $firstSunday;
        $servicesmap = new Application_Model_WorshipservicesMapper();
        $where = array(
            array('sunday', ' >= ', $sunday),
            );
        $sundays = $servicesmap->fetchWhere($where, array('sunday'));
        
        $this->view->sundays = $sundays;
    }


    


    public function archiveAction()
    {

        $functions = new Cvuuf_functions();
      	$today = $functions->today();
      	$year = substr($today, 0, 4);

        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $formData = $request->getParams();
            if (isset($formData['year']))
                $year = $formData['year'];
            $this->_redirect("/worship/sundaysyear?year=$year");
        }

        $this->view->year = $year;
        $this->view->style = 'zform.css';

    }


    public function sundaysyearAction()
    {

        $functions = new Cvuuf_functions();
        $year = substr($functions->today(), 0, 4);
        $request = $this->getRequest();
        if ($this->getRequest()->isGet()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['year']))
                $year = $getvalues['year'];
        }
        if ($year < 2007)
            $year = 2007;

        $months = array();
        for ($themonth = 1; $themonth <13; $themonth++) 
        { 
          	$unixtime = mktime(0,0,0, $themonth, 1, $year);
          	$thesunday = date("Y-m-d", $unixtime);
          	$month = substr($thesunday, 5, 2);
          	$year = substr($thesunday, 0, 4);
          	$firstOfMonth = date("Y-m-d", mktime(0, 0, 0, $month , 1, $year ));
          	$endOfMonth = date("Y-m-d", mktime(0, 0, 0, $month + 1 , 0, $year ));

            $servicesmap = new Application_Model_WorshipservicesMapper();
            $where = array(
                array('sunday', ' >= ', $firstOfMonth),
                array('sunday', ' <= ', $endOfMonth),
                );
            $sundays = $servicesmap->fetchWhere($where, array('Sunday'));
          //  NOTE: "Sunday" should be mapped from "sunday"           
            if (count($sundays) == 0)
                break;
            $monthname = date("F", $unixtime);
        		$n=0;
        		$nn=0;
        		
            $startmonth = $month;
            unset ($theweeks);
        		foreach($sundays as $sunday) 
            {
          			$day = sprintf("%01d", substr($sunday->sunday, 8, 2));
          			$week = array($day, $sunday->title, $sunday->presenter, 
                  $sunday->summary);
          			$theweeks[] = $week;
        		}
           
            $months[] = array($monthname, $theweeks);
        }

        $this->view->year = $year;
        $this->view->months = $months;
    }
}

