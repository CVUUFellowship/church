<?PHP
$PUPDATE="December 9, 2009";
$debug='no';
	require	"dva_start.php";

if (0) {  // Don't make the check
	if (($userlevel & 8) == 0) {
		print "<font face=\"Arial\" size=\"5\" color=\"#FF0000\">";
		print "<b>Illegal Access</b>";
		print "</font><br>";
  		print "<font face=\"Verdana\" size=\"2\" color=\"#000000\">";
		print "<b>You do not have permission to view this page.</b></font>";
		exit;	// Stop script execution
	}
}

	$Tdate = getdate(mktime(0,0,0, date('m'), date('d'),date('Y')));
	$theDate = date("Y-m-d", mktime(0,0,0, date('m'), date('d'),date('Y')));

	$smarty->assign("PUPDATE", $PUPDATE);
	$smarty->assign("HEAD1", "CVUUF Membership Model Statistics ".$theDate);
	$smarty->assign("USERLEVEL", "$userlevel");
	$smarty->assign("HOME", "../cvuuf/");
	$smarty->assign("STYLE", "db_cvuuf.css");
	$smarty->assign("STYLE2", "db_iframe.css");
	$smarty->display("db_head.tpl");

  $query_delete_count="DROP TABLE IF EXISTS visitor_count";
if ($debug=='yes') echo $query_delete_count, '<br>';  
  $queryCreateVisitorCount="CREATE TABLE visitor_count AS
SELECT RecordID, FirstName, LastName, count(*) AS visits FROM people p, visits v
 WHERE DATEDIFF(CURRENT_DATE, CreationDate) < 121 
 AND v.PersonID=p.RecordID 
 GROUP BY RecordID";
  $queryCreateCurrentVisitorCount="CREATE TABLE visitor_count AS
SELECT RecordID, FirstName, LastName, count(*) AS visits FROM people p, visits v
 WHERE v.PersonID=p.RecordID 
 AND p.Status='Visitor'
 AND p.Inactive<>'yes'
 GROUP BY RecordID";

if ($debug=='yes') echo $queryCreateVisitorCount, '<br>';

  $queryCountOneVisitor="SELECT count(*) FROM visitor_count WHERE visits=1";
if ($debug=='yes') echo $queryCountOneVisitor, '<br>';  
  $queryCountMultiVisitor="SELECT count(*) FROM visitor_count WHERE visits>1";
if ($debug=='yes') echo $queryCountMultiVisitor, '<br>';  

  $queryMembers="SELECT count(*) FROM people WHERE Inactive<>'yes' AND Status='Member'";
if ($debug=='yes') echo $queryMembers, '<br>';
  $queryNewMembers="SELECT count(*) FROM people WHERE Inactive<>'yes' AND Status='Member'
    AND DATEDIFF(CURRENT_DATE, MembershipDate) < 121 ";
if ($debug=='yes') echo $queryNewMembers, '<br>';

  $queryNewFriends="SELECT count(*) FROM people WHERE Inactive<>'yes' AND Status='NewFriend'";
if ($debug=='yes') echo $queryNewFriends, '<br>';
  $queryNewNewFriends="SELECT count(*) FROM people p, connections c WHERE Inactive<>'yes' AND Status='NewFriend'
    AND c.PeopleID=p.RecordID AND DATEDIFF(CURRENT_DATE, c.FriendDate) < 121 ";
if ($debug=='yes') echo $queryNewNewFriends, '<br>';
  $queryOldNewFriends="SELECT count(*) FROM people p, connections c WHERE Inactive<>'yes' AND Status='NewFriend'
    AND c.PeopleID=p.RecordID AND DATEDIFF(CURRENT_DATE, c.FriendDate) > 365 ";
if ($debug=='yes') echo $queryNewNewFriends, '<br>';

  $queryAffiliates="SELECT count(*) FROM people WHERE Inactive<>'yes' AND Status='Affiliate'";
if ($debug=='yes') echo $queryAffiliates, '<br>';

  $queryVisitors="SELECT count(*) FROM people WHERE Inactive<>'yes' AND Status='Visitor'";
if ($debug=='yes') echo $queryVisitors, '<br>';
  $queryNewVisitors="SELECT count(*) FROM people p WHERE ((Status = 'Visitor') OR (Status<>'Child' AND Inactive<>'yes'))
    AND DATEDIFF(CURRENT_DATE, CreationDate) < 121 ";
if ($debug=='yes') echo $queryNewVisitors, '<br>';

  $queryGuests="SELECT count(*) FROM people WHERE Inactive<>'yes' AND Status='Guest'";
if ($debug=='yes') echo $queryGuests, '<br>';

echo '<br><br>';
  $db->query($query_delete_count);
  $db->query($queryCreateVisitorCount);
  $oneVisitor=$db->get_var($queryCountOneVisitor);
  echo "$oneVisitor Visitors created within last four months and visited one time <br>";

  $db->query($query_delete_count);
  $db->query($queryCreateVisitorCount);
  $multiVisitor=$db->get_var($queryCountMultiVisitor);
  echo "$multiVisitor people with multiple visits during last four months [may no longer be visitors]<br>";

  $members=$db->get_var($queryMembers);
  echo "<br>$members Members now <br>";
  $newmembers=$db->get_var($queryNewMembers);
  echo "$newmembers became Members in last 4 months <br>";

  $newfriends=$db->get_var($queryNewFriends);
  echo "<br>$newfriends New Friends now <br>";
  $newnewfriends=$db->get_var($queryNewNewFriends);
  echo "$newnewfriends became New Friends in last 4 months <br>";
  $oldnewfriends=$db->get_var($queryOldNewFriends);
  echo "$oldnewfriends became New Friends more than 12 months ago <br>";

  $visitors=$db->get_var($queryVisitors);
  echo "<br>$visitors Visitors now <br>";
  $newvisitors=$db->get_var($queryNewVisitors);
  echo "$newvisitors new Visitors in last 4 months <br>";

  $guests=$db->get_var($queryGuests);
  echo "<br>$guests Guests now <br>";
  $affiliates=$db->get_var($queryAffiliates);
  echo "$affiliates Affiliates now <br>";

  
echo '<br><br>';
  $db->query($query_delete_count);
  $db->query($queryCreateCurrentVisitorCount);
  $oneVisitor=$db->get_var($queryCountOneVisitor);
  echo "$oneVisitor current visitors who have visited only one time <br>";
  $multiVisitor=$db->get_var($queryCountMultiVisitor);
  echo "$multiVisitor current visitors with multiple visits [over any time period]<br>";
  

	$smarty->display("stdfoot.tpl");

?>

