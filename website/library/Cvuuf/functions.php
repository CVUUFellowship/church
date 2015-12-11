<?php
    // /library/Cvuuf/Cvuuf_functions
     
class Cvuuf_functions
{

    public function getAuth($team = null, Application_Model_Auth $auth = null)
    {
        if (isset($_COOKIE['KEY'])) $key = $_COOKIE['KEY'];
            else $key='';
        $cookie = new Application_Model_Cookies();
        $cookmap = new Application_Model_CookiesMapper();
        $thecookie = $cookmap->find($key, $cookie);
        $authid = $cookie->id;
        if ($authid > 0)
        {
            if (!isset($auth))
                $auth = new Application_Model_Auth();
            $authmap = new Application_Model_AuthMapper();
            $authmap->find($authid, $auth);
            if (isset($level))
            {
                if ($auth->level <> $level)
                    return null;
            }
            return $auth;
        }
        return null;
    }
    
  
    public function compress($srcFileName, $dstFileName){
        // getting file content
        $fp = fopen( $srcFileName, "r" );
        $data = fread ( $fp, filesize( $srcFileName ) );
        fclose( $fp );
       
        // writing compressed file
        $zp = gzopen( $dstFileName, "w9" );
        gzwrite( $zp, $data );
        gzclose( $zp );
    }


    public function uncompress($srcName, $dstName){
        $zp = gzopen($srcName, "r");
        $string='';
        while(!gzeof($zp))
          $string .= gzread($zp, 4096);
        gzclose($zp);
        $fp = fopen($dstName, "w");
        fwrite($fp, $string, strlen($string));
        fclose($fp);
    }




    public function showDate($date)
    {
        list($yr, $mon, $day) = explode('-', substr($date, 0, 10));
        return (date('M j, Y', mktime(0,0,0,$mon,$day,$yr))); 
    }
    
    
    public function showFile($filename, $filetype)
    {
        $publicdir = $_SERVER["DOCUMENT_ROOT"];
        $filedir = substr($publicdir, 0, strrpos($publicdir, 'public_html')) . 'private/';
        // if ($filedir == 'private/') {
        //     $filedir = '/home/cvuuf_org/'.$filedir;
        // }

        switch ($filetype)
        {
    		case 'pdf':
    			$content='application/pdf';
          break;
    		case 'pdf.gz':
    			$content='application/pdf';
          break;
    		case 'gif':
    			$content='image.gif';
    		case 'zip':
    			$content='application/zip';
          break;
    		case 'htm':
    			$content='text/html';
          break;
    		case 'html':
    			$content='text/html';
    		case 'doc':
    		case 'docx':
    		case 'rtf':
    			$content='application/msword';
          break;
    		default:
    			$content='text/plain';
          break;
        }    
      	$fullPath = $filedir . $filename . '.' . $filetype;
//echo "FULLPATH $fullPath";
// exit;
        if ($filetype == 'pdf.gz' || $filetype == 'rtf.gz')
        {
            $tmpfname = tempnam($filedir, "ptmp");
            $exists = file_exists($fullPath);
       		  if ($exists)
            {
                $gfsize = filesize($fullPath);
//echo "GFSIZE $gfsize <br>"; 
//exit;
                $this->uncompress($fullPath, $tmpfname);
          		  if ($fd = fopen($tmpfname, "rb")) {
                    $fsize = filesize($tmpfname);
                    if ($filetype == 'pdf.gz')
                      $fname = 'policy.pdf';
                    else
                      $fname = 'policy.rtf';
            		    header("Pragma: ");
            			  header("Cache-Control: ");
                    if ($filetype=='rtf.gz')
                      header("Content-Disposition: attachment; filename=\"".$fname."\"");
                    else
                      header("Content-Disposition: inline; filename=\"".$fname."\"");
            			  header("Content-type: ".$content);
            			  header("Content-length: $fsize");
            			  fpassthru($fd);
                }
            }
            else
                return false;
        }
      	elseif ($fd = fopen($fullPath, "rb")) 
            {
             		$fsize    =filesize($fullPath);
             		$fname    = basename ($fullPath);
          
            		header("Pragma: ");
            		header("Cache-Control: ");
            		header("Content-type: ".$content);
                if ($filetype=='rtf' || $filetype=='doc')
                  header("Content-Disposition: attachment; filename=\"".$fname."\"");
                else
                  header("Content-Disposition: inline; filename=\"".$fname."\"");
            		header("Content-length: $fsize");
            
            		fpassthru($fd);
          	}
      	fclose($fd);
        exit;
    }    


    public function findFile($filename)
    {
        $content='text/html';
        $here = $_SERVER["DOCUMENT_ROOT"];
        $filedir = substr($here, 0, strlen($here) - 12) . '/private/';
        $fullPath = $filedir . $filename;
        
        return ($fullPath);
    }


    public function findPublicFile($filename, $filegroup)
    {
        $content='text/html';
        $publicdir = $_SERVER["DOCUMENT_ROOT"];
        $filedir = $filedir = $filedir = $publicdir . '/media/' . $filegroup . '/';
        $fullPath = $filedir . $filename;
        
        return ($fullPath);
    }


    
    
    public function findPrivateFiles($filegroup)
    {
        $publicdir = $_SERVER["DOCUMENT_ROOT"];
        $filedir = substr($publicdir, 0, strlen($publicdir) - 12) . 'private/' . $filegroup;
      	$filecount = 0;
      	$files = array();
      	if ($handle = opendir($filedir)) 
        {
        		while (false !== ($file = readdir($handle))) 
            {
            		if ($file != "." && $file != "..") 
            			$files[$filecount++] = $file;
        		}
        		closedir($handle);
      	}
      	return ($files);
    }


    
    
    public function findPublicFiles($filegroup)
    {
        $publicdir = $_SERVER["DOCUMENT_ROOT"];
        $filedir = $filedir = $publicdir . '/media/' . $filegroup;
      	$filecount = 0;
      	$files = array();
      	if ($handle = opendir($filedir)) 
        {
        		while (false !== ($file = readdir($handle))) 
            {
            		if ($file != "." && $file != "..") 
            			$files[$filecount++] = $file;
        		}
        		closedir($handle);
      	}
      	return ($files);
    }

    
    
    public function findPrivateMaxFile($filegroup)
    {
        $publicdir = $_SERVER["DOCUMENT_ROOT"];
        $filedir = substr($publicdir, 0, strlen($publicdir) - 12) . 'private/' . $filegroup;
      	$filecount = 0;
      	$files = array();
      	if ($handle = opendir($filedir)) 
        {
        		while (false !== ($file = readdir($handle))) 
            {
            		if ($file != "." && $file != "..") 
            			$files[$filecount++] = $file;
        		}
        		closedir($handle);
      	}
      	return (max($files));
    }

    
    
    public function findPublicMaxFile($filegroup, $begin = null)
    {
        $publicdir = $_SERVER["DOCUMENT_ROOT"];
        $filedir = $filedir = $publicdir . '/media/' . $filegroup;
      	$filecount = 0;
      	$files = array();
        if (isset($begin))
            $beginsize = strlen($begin);
      	if ($handle = opendir($filedir)) 
        {
        		while (false !== ($file = readdir($handle))) 
            {
            		if (isset($begin))
                {
                    if ($file != "." && $file != ".." && substr($file, 0, $beginsize) == $begin)
                    { 
                        $files[$filecount++] = $file;
                    }
                }
                else
                {
            		    if ($file != "." && $file != "..") 
                        $files[$filecount++] = $file;
                }
        		}
        		closedir($handle);
      	}
      	if ($filecount == 0)
            return '';
        return (max($files));
    }
        

    
    
    public function showPrivatePDFFile($filegroup, $filename)
    {
        $content = 'application/pdf';
        $publicdir = $_SERVER["DOCUMENT_ROOT"];
        $filedir = substr($publicdir, 0, strlen($publicdir) - 12) . 'private/' . $filegroup;
    		$fullPath = $filedir . '/' . $filename;
  			if ($fd = fopen ($fullPath, "rb")) 
        {
     				$fsize    = filesize($fullPath);
     				$fname    = basename ($fullPath);
  
    				header("Pragma: ");
    				header("Cache-Control: ");
    				header("Content-type: ".$content);
    				header("Content-length: $fsize");
    
    				fpassthru($fd);
  			}
  			fclose($fd);
        exit;
    }
        

    
    
    public function showPublicPDFFile($filegroup, $filename)
    {
        $content = 'application/pdf';
        $publicdir = $_SERVER["DOCUMENT_ROOT"];
        $filedir = $publicdir . '/media/' . $filegroup;
    		$fullPath = $filedir . '/' . $filename;
  			if ($fd = fopen ($fullPath, "rb")) 
        {
     				$fsize    = filesize($fullPath);
     				$fname    = basename ($fullPath);
  
    				header("Pragma: ");
    				header("Cache-Control: ");
    				header("Content-type: ".$content);
    				header("Content-length: $fsize");
    
    				fpassthru($fd);
  			}
  			fclose($fd);
        exit;
    }


    public function caltomysql($caldate)
    {
        $dateunix = strtotime($caldate);
        return date("Y-m-d", $dateunix);
        
    }



    public function validate_time($timehour, $timeminute) {
  //echo "Begin validation of ", $timehour,' ', $timeminute, '<br>';
      if ($timeminute<10)
        $timeminute='0'.$timeminute;
      $timefield=$timehour.':'.$timeminute;
      $hh=$timehour;
  
  //echo "Valid time ", $timefield, '<br>';
      if ($hh=='00') {
        $timehour='12';
        $ap='AM';
      }
      elseif ($hh>11) {
        $timehour-=12;
        if ($timehour==0)
          $timehour=12;
        $ap='PM';
      }
      else
        $ap='AM';
  
    	return($timehour.':'.$timeminute.$ap);
    }


/*
    Function Name: date_validate
  
    Author: Eric Sammons, Vansam Software, Inc. (www.vansam.com)
    Email: eric@vansam.com

    Date: 2001-09-01
    Version 1.0.0

    Purpose:
        Receives various date field formats, validates them, and then and
        converts them to MySQL standard date format

    Valid date fields:
        mm-dd-yyyy, mm/dd/yyyy, yyyy-mm-dd, yyyy/mm/dd

    Returns:
        if valid input, the date in MySQL standard format
        if invalid input, error message with "Error:" at the beginning of message

    Sample Use:
        $MySQLDate=date_validate($datefield);
        if (substr($MySQLDate, 0, 5)=="Error") {
            // Insert Error Code
        } else {
            // Insert Valid Date Code
        }
*/

    public function date_validate ($datefield) {
    
        // First check to see if the input ($datefield) is in one of the accepted formats
        
        // Check for delimiters ("-" or "/") and put three fields into an array
        if (strpos($datefield, "-")) {
          $datesplit = explode("-", $datefield);
        } elseif (strpos($datefield, "/")) {
          $datesplit = explode("/", $datefield);
        } else {
            $date_err="Error: Invalid date field. No proper delimiters (- or /) found";
            return $date_err;
        }
    
        // Check for three input fields (month, day, year)
        if (count($datesplit)<>3) {
            $date_err="Error: Invalid date field. Must be three fields (".count($datesplit).") found";
            return $date_err;
        }
    
        // Put date array into single format
        if (strlen($datesplit[2])==4) { // The year is listed last - switch fields around
            $newdatesplit[0]=$datesplit[2]; // Move Year to first field
            $newdatesplit[1]=$datesplit[0]; // Move Month to second field
            $newdatesplit[2]=$datesplit[1]; // Move Day to third field
            $datesplit=$newdatesplit;
        } elseif (strlen($datesplit[0])==4) { // The year is first listed - do nothing
            // nothing to be done
        } else { // Date entered is not valid; could not find year field
            $date_err="Error: Date not valid. No Year field found (Year must be 4 digits)";
            return $date_err;
        }
        
        // Main validation code
    
        if ($datesplit[1]>12) { // No valid month field
            $date_err="Error: Invalid Month field (".$datesplit[1].") ";
            return $date_err;
        } else {
           switch ($datesplit[1]) { // Check number of days in a month
               case 4:
               case 6:
               case 9:
               case 11:
                    if ($datesplit[2]>30) {
                        $date_err="Error: Invalid # of days (".$datesplit[2].") for month ".$datesplit[1]." and year ".$datesplit[0];
                        return $date_err;
                    }
                    break;
               case 2: // February Check
                       if (($datesplit[0]/4)==(floor($datesplit[0]/4))) {
                        if (($datesplit[0]/100)==(floor($datesplit[0]/100))) {
                            if (($datesplit[0]==1600) or ($datesplit[0]==2000) or ($datesplit[0]==2400)) {
                                if ($datesplit[2]>29) {
                                    $date_err="Error: Invalid # of days (".$datesplit[2].") for month ".$datesplit[1]." and year ".$datesplit[0];
                                    return $date_err;
                                }
                            } else {
                                if ($datesplit[2]>28) {
                                    $date_err="Error: Invalid # of days (".$datesplit[2].") for month ".$datesplit[1]." and year ".$datesplit[0];
                                    return $date_err;
                                }
                            }
                        } else {
                            if ($datesplit[2]>29) {
                                $date_err="Error: Invalid # of days (".$datesplit[2].") for month ".$datesplit[1]." and year ".$datesplit[0];
                                return $date_err;
                            }
                        }
                    } else {
                        if ($datesplit[2]>28) {
                            $date_err="Error: Invalid # of days (".$datesplit[2].") for month ".$datesplit[1]." and year ".$datesplit[0];
                            return $date_err;
                        }
                    }
                    break;
               default:
                    if ($datesplit[2]>31) {
                        $date_err="Error: Invalid # of days (".$datesplit[2].") for month ".$datesplit[1]." and year ".$datesplit[0];
                        return $date_err;
                    }
            }
          }
              // Add leading zero if month or day field is only one character
          if (strlen($datesplit[1])==1) {
              $datesplit[1]="0".$datesplit[1];
          }
          if (strlen($datesplit[2])==1) {
              $datesplit[2]="0".$datesplit[2];
          }
          
          // Create date field in MySQL format
          $newdate=$datesplit[0]."-".$datesplit[1]."-".$datesplit[2];
          return $newdate;    
          
    } // End date_validate function
    
    
    //	Valid postal zip code? true or false
    
      	function is_zip ($zipcode = "")
      	{
        		if(empty($zipcode))
        		{
            		return false;
        		}
      
        		$Bad = preg_replace("/([-0-9]+)/i","",$zipcode);
        		if(!empty($Bad))
        		{
            		return false;
        		}
        		if (strlen($zipcode)<>10){
                return false;
            }
            
        		if ($zipcode[5] <> '-') {
          			return false;
        		}
      
        		$Num = preg_replace("/\-/i","",$zipcode);
        		$len = strlen($Num);
        		if ($len <> 9)	{
          			return false;
        		}
        		return true;
        }



      	function is_state ($state = "")
      	{
            $us_state_abbrevs = array(
            'AL',
            'AK',
            'AS',
            'AZ',
            'AR',
            'CA',
            'CO',
            'CT',
            'DE',
            'DC',
            'FM',
            'FL',
            'GA',
            'GU',
            'HI',
            'ID',
            'IL',
            'IN',
            'IA',
            'KS',
            'KY',
            'LA',
            'ME',
            'MH',
            'MD',
            'MA',
            'MI',
            'MN',
            'MS',
            'MO',
            'MT',
            'NE',
            'NV',
            'NH',
            'NJ',
            'NM',
            'NY',
            'NC',
            'ND',
            'MP',
            'OH',
            'OK',
            'OR',
            'PW',
            'PA',
            'PR',
            'RI',
            'SC',
            'SD',
            'TN',
            'TX',
            'UT',
            'VT',
            'VI',
            'VA',
            'WA',
            'WV',
            'WI',
            'WY',
            'AE',
            'AA',
            'AP'
            );
    
            return in_array($state, $us_state_abbrevs);
        }



    public function statusValues()
    {
        return array(
            '' => '',
            'Member' => 'Member',
            'NewFriend' => 'NewFriend',
            'Visitor' => 'Visitor',
            'Affiliate' => 'Affiliate',
            'Guest' => 'Guest',
            'Staff' => 'Staff',
            'Friend' => 'Friend',
            'Child' => 'Child',
            'Spouse' => 'Spouse',
            'Special' => 'Special',
            'Deceased' => 'Deceased',
            'Resigned' => 'Resigned',
            'Guardian' => 'Guardian',
        );

    }



    public function hoodValues()
    {
        $hoodsmap = new Application_Model_HoodsMapper();
        $hoods = $hoodsmap->fetchAll();
        $hoodarray = array();
        foreach ($hoods as $hood)
        {
            $hoodarray[$hood->id] = $hood->hoodname;
        }
        return ($hoodarray);

    }


    public function hoodZips()
    {
        $hoodsmap = new Application_Model_HoodFromZipMapper();
        $where = array(
            array('id', ' > ', 0),
            );            
        $hoods = $hoodsmap->fetchWhere($where);
        $hoodarray = array();
        foreach ($hoods as $hood)
        {
            $hoodarray[] = array($hood->hoodid, $hood->low, $hood->high);
        }
        return ($hoodarray);
    }



      
      public function today()
      {
          return(date("Y-m-d"));    
      }


      public function lastSunday($format = 'mysql')
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
          if ($format == 'mysql')
          {
              $lastdate = date("Y-m-d", $lastsunday);
              return($lastdate);
          }
          else
              return($lastsunday);      
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



      public function lastMonth($format = 'mysql')
      {
          $Tdate = getdate(mktime(0, 0, 0, date('m')-1, date('d'), date('Y')));
          $today = $Tdate['wday'];
          $TDay = $Tdate['mday'];
          $Month = $Tdate['mon'];
          $Year = $Tdate['year'];
          $MDay = $TDay - $today;
          $lastmonth = mktime(0, 0, 0, $Month, $MDay, $Year);
          if ($format == 'mysql')
          {
              $lastdate = date("Y-m-d", $lastmonth);
              return($lastdate);
          }
          else
              return($lastmonth);      
      }


      public function nextSunday($format = 'mysql', $start = null)
      {
          if (isset($start))
          {
              $Tdate = getdate(mktime(0,0,0, substr($start, 5, 2), substr($start, 8, 2) + 7, substr($start, 0, 4)));
          }
          else
              $Tdate = getdate(mktime(0,0,0, date('m'), date('d')+7, date('Y')));
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


      public function encryptData($value){
         $key = "dt5gvwa7$&A^";
         $text = $value;
         $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
         $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
         $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
         return base64_encode($crypttext);
      }
      
      public function decryptData($value){
         $key = "dt5gvwa7$&A^";
         $crypttext = base64_decode($value);
         $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
         $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
         $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
         return trim($decrypttext);
      }


      public function getWebtext(){
          $title = substr($_SERVER['REQUEST_URI'], 1);
          if ($title == FALSE)
              $title = 'index/index';
          $slash = strpos($title, "/");
          if ($slash == FALSE)
              $title .= '/index';
          $nodesmap = new Application_Model_NodesMapper();
          $where = array(
              array('title', ' = ', $title),
              );
          $nodesarray = $nodesmap->fetchWhere($where);
          if (count($nodesarray) == 1)
          {
              $node = current($nodesarray);
              $text = $node->body;
          }
          else
          {
              $text = '';
          }
               
          return($text);
      }
      

    }  /* end of class */
