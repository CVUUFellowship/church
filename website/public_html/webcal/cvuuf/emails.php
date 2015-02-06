<?php

return;

$PUPDATE="April 15, 2009";
//$DOEMAIL='yes';
$serverName=$_SERVER["SERVER_NAME"];
if ($serverName == "cvuuf.us")
  $whichName="cvuuforg";
else
  $whichName='cvuuf';
if ($serverName=='localhost')
  $serverName.='/cvuuf';

include_once "../ez_sql.php";
date_default_timezone_set('America/Los_Angeles'); 
$unixtime=mktime(date(G),date(i),date(s), date(m), date(d),date(Y));
//echo $unixtime;
$theDateTime = date("Y-m-d G:i:s", $unixtime);
//echo $theDateTime, '<br>'; exit;

$self=$_SERVER["PHP_SELF"];
$lastSlash=strrchr($self, "/");  
$thisScript=substr($lastSlash, 1);
$thisPath=substr($self, 0, strrpos($self, "/"));

// See if there are events without email sent

  $db->select($whichName."_cvuuf");
	$actvQuery="SELECT * FROM cal_events WHERE resultEmail<'2000-01-01'";
//echo $actvQuery, '<br>';
	$actvEvents = $db->get_results($actvQuery);

  if ($actvEvents) foreach($actvEvents AS $event){
    $EventID=$event->EventID;
    $db->select($whichName."_cal");
    $unapQuery="SELECT cal_login, cal_status FROM webcal_entry_user 
      WHERE cal_id=$EventID AND cal_status<>'A'";
//echo $unapQuery, '<br>';
    $unapEvents = $db->get_row($unapQuery);
    $db->select($whichName."_cvuuf");
//echo "Count is ",count($unapEvents)," <BR>";
    if (count($unapEvents)==0){
      $lockQuery="LOCK TABLES cal_events WRITE";
//echo $lockQuery, '<br>';
      $result = $db->query($lockQuery);
      $emQuery="SELECT resultEmail FROM cal_events WHERE EventID=$EventID";
//echo $emQuery, '<br>';
      $resEmail = $db->get_var($emQuery);
      if ($resEmail<'2009-01-01'){
        $setQuery="UPDATE cal_events SET resultEmail='$theDateTime' WHERE EventID=$EventID";
//echo $setQuery, '<br>';
        $result = $db->query($setQuery);
        $lockQuery="UNLOCK TABLES";
//echo $lockQuery, '<br>';
        $result = $db->query($lockQuery);
//echo "SEND APPROVED EMAIL HERE <br>";

        require_once  "../log_email.php";
        $db->select($whichName."_cal");
        $nameQuery="SELECT cal_name FROM webcal_entry WHERE cal_id=$EventID";
//echo $nameQuery, '<br>';
        $eventName = $db->get_var($nameQuery);
        $db->select($whichName."_cvuuf");
        $msg = "Your event request $EventID, '$eventName' has been approved.".
        "\n\nDisplay the event at http:$serverName/webcal/view_entry.php?id=$EventID";
        $sQuery="SELECT Email FROM people p WHERE p.RecordID=".$event->RequesterID;
//echo $sQuery, '<br>';
        $sEmail=$db->get_var($sQuery);
        $FROM="calendarForm@cvuuf.org";
        $sTO=$sEmail;
        $TO_array=array($sTO, 'calendar@cvuuf.org');
        log_email($_SERVER['SCRIPT_NAME'], 2);
        $SUBJECT = 'Event #'.$EventID.' Approved: '.$eventName;
        $TEXT=$msg;
        if ($DOEMAIL) {  
          require_once "../runSwift/swiftCCSend.php";
          $runner = new swiftCCSend();
          $runner->go($TO_array, $FROM, $SUBJECT, $TEXT, false);
//echo "Message Sent<br>"; exit;
        }
        else {
          echo "<br><br><b>Mail to Requester: $sTO</b><br>";
          echo "$SUBJECT<br><blockquote>";
          echo $TEXT, '<br>';
          echo $messageProcess, '<br>';
          echo "</blockquote><br><br>";
        }
      }
      else {
        $lockQuery="UNLOCK TABLES";
//echo $lockQuery, '<br>';
        $result = $db->query($lockQuery);
      }
    }  
    else {
        $lockQuery="UNLOCK TABLES";
//echo $lockQuery, '<br>';
        $result = $db->query($lockQuery);
        $db->select($whichName."_cal");
        $rejQuery="SELECT cal_login, cal_status FROM webcal_entry_user 
          WHERE cal_id=$EventID AND cal_status='R'";
//echo $rejQuery, '<br>';
        $rejEvent = $db->get_row($rejQuery);
        if ($rejEvent){
          $db->select($whichName."_cvuuf");
          $setQuery="UPDATE cal_events SET resultEmail='$theDateTime' WHERE EventID=$EventID";
//echo $setQuery, '<br>';
          $result = $db->query($setQuery);
          $lockQuery="UNLOCK TABLES";
//echo $lockQuery, '<br>';
          $result = $db->query($lockQuery);
//echo "SEND REJECTED EMAIL HERE <br>";

          require_once  "../log_email.php";
          $msg = "Your event request $EventID has been rejected.".
            "\n\nDisplay the event at http:$serverName/webcal/view_entry.php?id=$EventID";
          $sQuery="SELECT Email FROM people p WHERE p.RecordID=".$event->RequesterID;
//echo $sQuery, '<br>';
          $sEmail=$db->get_var($sQuery);
          $FROM="calendarForm@cvuuf.org";
          $sTO=$sEmail;
          $TO_array=array($sTO, 'calendar@cvuuf.org');
          log_email($_SERVER['SCRIPT_NAME'], 2);
          $SUBJECT = 'Event Request : '.$EventID;
          $TEXT=$msg;
          if ($DOEMAIL) {  
            require_once "../runSwift/swiftCCSend.php";
            $runner = new swiftCCSend();
            $runner->go($TO_array, $FROM, $SUBJECT, $TEXT, false);
          }
          else {
            echo "<br><br><b>Mail to Requester: $sTO</b><br>";
            echo "$SUBJECT<br><blockquote>";
            echo $TEXT, '<br>';
            echo $messageProcess, '<br>';
            echo "</blockquote><br><br>";
          }
          $db->select($whichName."_cal");
        }
// check if deleted        
        $delQuery="SELECT cal_login, cal_status FROM webcal_entry_user 
          WHERE cal_id=$EventID AND cal_status='D'";
//echo $delQuery, '<br>';
        $delEvent = $db->get_row($delQuery);
        if ($delEvent){
          $db->select($whichName."_cvuuf");
          $setQuery="UPDATE cal_events SET resultEmail='$theDateTime' WHERE EventID=$EventID";
//echo $setQuery, '<br>';
          $result = $db->query($setQuery);
          $lockQuery="UNLOCK TABLES";
//echo $lockQuery, '<br>';
          $result = $db->query($lockQuery);
//echo "SEND DELETED EMAIL HERE <br>";

          require_once  "../log_email.php";
          $msg = "Your event request $EventID has been deleted by the Scheduling Coordinator.".
            "\n\nDisplay the event at http:$serverName/webcal/view_entry.php?id=$EventID";
          $sQuery="SELECT Email FROM people p WHERE p.RecordID=".$event->RequesterID;
//echo $sQuery, '<br>';
          $sEmail=$db->get_var($sQuery);
          $FROM="calendarForm@cvuuf.org";
          $sTO=$sEmail;
          $TO_array=array($sTO, 'calendar@cvuuf.org');
          log_email($_SERVER['SCRIPT_NAME'], 2);
          $SUBJECT = 'Event Request : '.$EventID;
          $TEXT=$msg;
          if ($DOEMAIL) {  
            require_once "../runSwift/swiftCCSend.php";
            $runner = new swiftCCSend();
            $runner->go($TO_array, $FROM, $SUBJECT, $TEXT, false);
          }
          else {
            echo "<br><br><b>Mail to Requester: $sTO</b><br>";
            echo "$SUBJECT<br><blockquote>";
            echo $TEXT, '<br>';
            echo $messageProcess, '<br>';
            echo "</blockquote><br><br>";
          }
        }
    }
  }
  $db->select($whichName."_cal");
// echo "Exiting emails.php<br>"; exit;
?>
