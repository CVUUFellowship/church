<?php

class AdminController extends Zend_Controller_Action
{

    function Bits16($dec) 
    {
      	$rightbits=base_convert($dec, 10, 2);
      	return(substr('0000000000000000', 0, 16-strlen($rightbits)).$rightbits);
    }
    
    
    function prepPerm(Application_Form_AdminPermChange $permForm, $level, $memberid, $perm) 
    {
        $functions = new Cvuuf_authfunctions();
        $codes = $functions->permissionNames();
        $permForm->permission->addMultiOptions(array('')); 
        $permForm->permission->addMultiOptions($codes);

        $codes = array();
        $pvalues = $functions->permissionCodes();
        foreach ($pvalues as $key => $value)
        {
            $thebit = $level & $value;
            if (($level & $value) <> 0)
            {
                $codes[$key] = $value;
            }
        }
        $permForm->permission->setValue($codes); 
        $options = count($pvalues) + 1;
        $permForm->permission->setAttrib('size', $options);
        $permForm->id->setValue($memberid);
        $permForm->perm->setValue($perm);
        $this->view->style = 'zform.css';
        $this->view->permForm = $permForm;
    
    }

    function prepUser($memberid) 
    {
        $auth = new Application_Model_Auth();
        $authmap = new Application_Model_AuthMapper();
        $authmap->findUser($memberid, $auth);
        
        $peoplemap = new Application_Model_PeopleMapper();
        $person = $peoplemap->find($memberid);
        
        $this->view->memberid = $memberid;
        $this->view->name = $person['firstname'] . ' ' . $person['lastname'];
        
        $permForm = new Application_Form_AdminPermChange();
        $level = $auth->level;
    }


    public function init()
    {
        $this->view->theme = 'admin';
        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth('');
        $this->auth = $auth;
        if (!isset($auth))
            $this->_redirect('/auth/index');
        if ($functions->hasPermission('admin', $auth) == false)
        {
            $page = $_SERVER['REQUEST_URI'];
            $this->_redirect("/auth/notauth?from=$page");
        }
        $this->view->level = $auth->level;
    }


    public function indexAction()
    {
        // action body
    }

    public function permissionsAction()
    {
        $functions = new Cvuuf_authfunctions();
        $allowed = $functions->privateAllowed();
        $people = new Application_Model_People();
        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' IN ', "($allowed)"),
            );            
        $eligible = $peoplemap->fetchWhere($where);

        $authusers = new Application_Model_Auth();
        $authmap = new Application_Model_AuthMapper();
        $where = array(
            array('status', ' <> ', 'removed'),
            );            
        $authorized = $authmap->fetchWhere($where);

        $authmap = new Application_Model_AuthMapper();
        $where = array(
            array('status', ' <> ', 'removed'),
            array('level', ' <= ', 3),
            );            
        $normal = $authmap->fetchWhere($where);

        $authmap = new Application_Model_AuthMapper();
        $where = array(
            array('status', ' <> ', 'removed'),
            array('level', ' > ', 3),
            );            
        $special = $authmap->fetchWhere($where);

        $invalid = $authmap->fetchInvalids();

        $this->view->eligible = count($eligible);
        $this->view->authorized = count($authorized);
        $this->view->normal = count($normal);
        $this->view->special = count($special);
        $this->view->invalid = count($invalid);
    }


    public function perminvalidAction()
    {
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['removebutton']))
            {
                if (!isset($getvalues['id']))
                {
                    $this->view->message = 'No users selected, nothing done.';
                }
                else
                {
                    $ids = $getvalues['id'];
                    foreach ($ids as $memberid)
                    {
                        $user = new Application_Model_Auth();
                        $authmap = new Application_Model_AuthMapper();
                        $authmap->findUser($memberid, $user);
                        $user->status = 'removed';
                        $authmap->save($user);
                    }
                }
            }
        }   


        $authmap = new Application_Model_AuthMapper();
        $invalids = $authmap->fetchInvalids();
        $people = new Application_Model_People();
        $peoplemap = new Application_Model_PeopleMapper();
        $users = array();
        foreach($invalids as $auth)
        {
            $person = $peoplemap->find($auth->memberid);
            $users[] = array($person['id'], $person['firstname'].' '.$person['lastname'], $person['status'], $auth->status);
        }
        $this->view->invalid = $users;
        $this->view->style = 'zform.css';
    }



    public function permspecialAction()
    {
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['select']))
            {
                $perm = $getvalues['permission'];
                $permForm = new Application_Form_AdminPermissions();
                $formData['permission'] = $perm;
                $permForm->populate($formData);
            }
            
            if (isset($getvalues['modifybutton']))
            {
                if (!isset($getvalues['id']))
                {
                    $this->view->message = 'No users selected, nothing done.';
                }
                else
                {
                    $ids = $getvalues['id'];
                    $memberid = $ids[0];
                    $auth = new Application_Model_Auth();
                    $authmap = new Application_Model_AuthMapper();
                    $authmap->findUser($memberid, $auth);
                    
                    $peoplemap = new Application_Model_PeopleMapper();
                    $person = $peoplemap->find($memberid);
                    
                    $this->view->memberid = $memberid;
                    $this->view->name = $person['firstname'] . ' ' . $person['lastname'];
                    
                    $permForm = new Application_Form_AdminPermChange();
                    $level = $auth->level;
                    $perm = $getvalues['perm'];
                    $this->prepPerm($permForm, $level, $memberid, $perm);
                    
                    return $this->render('permchange');
                    
                }
            }
                        
            if (isset($getvalues['submit']))
            {
                $perm = $getvalues['perm'];

                $functions = new Cvuuf_authfunctions();
                $newperms = $getvalues['permission'];
                $code = 0;
                foreach ($newperms as $bit)
                    $code |= $bit;
                $memberid = $getvalues['id'];
                $auth = new Application_Model_Auth();
                $authmap = new Application_Model_AuthMapper();
                $authmap->findUser($memberid, $auth);
                $auth->level = $code;
                if ($auth->memberid == 0)
                {
                    $this->view->message = "Person is not signed up for private access.";
                    $this->view->name = "not authorized";
                }
                else
                {
                    $authmap->save($auth);
                    $peoplemap = new Application_Model_PeopleMapper();
                    $person = $peoplemap->find($memberid);
                    $this->view->memberid = $memberid;
                    $this->view->name = $person['firstname'] . ' ' . $person['lastname'];
                    
                    $permForm = new Application_Form_AdminPermChange();
                    $level = $auth->level;
                    $this->prepPerm($permForm, $level, $memberid, $perm);
    
                    if (isset($getvalues['continue']))
                        $permForm->continue->setValue($getvalues['continue']);
                    
                    $this->view->message = "Permissions updated";
                }
                return $this->render('permchange');
            }
            
            
            if (isset($getvalues['cancel']))
            {
                $perm = $getvalues['perm'];
                if (isset($getvalues['continue']))
                {
                    $continue = $getvalues['continue'];
                    if ($continue == 'person')
                    {
                        $this->redirect('/admin/permperson');
                    }
                }
                
            }
        }   

        $authmap = new Application_Model_AuthMapper();
        $where = array(
            array('status', ' <> ', 'removed'),
            array('level', ' > ', 3),
            );            
        if (!isset($perm)) $perm = 0;
        if ($perm <> 0)
            $where[] = array('level', ' & ', $perm);
        $this->view->perm = $perm;
        $specials = $authmap->fetchWhere($where);

        $people = new Application_Model_People();
        $peoplemap = new Application_Model_PeopleMapper();
        $users = array();
        foreach($specials as $auth)
        {
            $person = $peoplemap->find($auth->memberid);
            $users[] = array($person['id'], $person['firstname'], $person['lastname'], sprintf('%03x', ($auth->level & 0xffc)));
        }

        function cmpUserField($a,$b){
            return strcmp($a[2].$a[1], $b[2].$b[1]);
        }
        uasort($users, 'cmpUserField'); 

        $this->view->special = $users;
        $this->view->perm = $perm;

        $this->view->style = 'zformnodt.css';
        if (!isset($permForm))
        {
            $permForm = new Application_Form_AdminPermissions();
            $permForm->permission->setValue($perm);
        }
        $functions = new Cvuuf_authfunctions();
        $codes = $functions->permissionNames();
        $permForm->permission->addMultiOptions(array('All')); 
        $permForm->permission->addMultiOptions($codes); 

        $this->view->permForm = $permForm;

    }



    public function permpersonAction()
    {
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['find']))
            {
                $first = $getvalues['first'];
                $last = $getvalues['last'];
                $functions = new Cvuuf_personfunctions();
                $this->view->names = $functions->fillFindNames($first, $last);
            }

          if (isset($getvalues['personbutton']))
          {
              $ids = $getvalues['id'];
              $memberid = $ids[0];
              $this->prepUser($memberid);
              $perm = $getvalues['perm'];
              $permForm = new Application_Form_AdminPermissions();
              $auth = new Application_Model_Auth();
              $authmap = new Application_Model_AuthMapper();
              $authmap->findUser($memberid, $auth);
              $level = $auth->level;
              $permForm = new Application_Form_AdminPermChange();
              $this->prepPerm($permForm, $level, $memberid, $perm);
              $permForm->continue->setValue('person');
              $permForm->setAction('permspecial');
              return $this->render('permchange');
          }
            
        }

        $this->view->style = 'zform.css';
        $personForm = new Application_Form_AdminPersonSelect();
        if (isset($first))
        {
            $personForm->first->setValue($first);
            $personForm->last->setValue($last);
        }
        $this->view->personForm = $personForm;
        $emailForm = new Application_Form_AdminEmailSelect();
        if (isset($email))
        {
            $emailForm->first->setValue($email);
        }
        $this->view->emailForm = $emailForm;
    
    }



    public function calapproveAction()
    {

        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['coaction']))
            {
                $action = $getvalues['coaction'];
                if ($action == 'LOGINS')
                {
                    $groupsmap = new Application_Model_CalGroupsMapper();
                    $groups = $groupsmap->fetchAll();
                    $this->view->message = array();
                    foreach($groups as $group)
                    {
                        $peoplemap = new Application_Model_PeopleMapper();
                        $positionsmap = new Application_Model_PositionsMapper();
                        $where = array(
                            array('id', ' = ', $group->groupid),
                            );            
                        $positions = $positionsmap->fetchWhere($where);
echo "Group $group->groupid";
var_dump($positions);
                        if (count($positions) > 0)
                        {
                            $position = current($positions);
                            $personid = $position->contact1;
                            if ($personid <> 0) {
                                $authmap = new Application_Model_AuthMapper();
                                $where = array(
                                    array('memberid', ' = ', $personid),
                                    );            
    var_dump($personid); 
                                $authrows = $authmap->fetchWhere($where);
                                $passwd = current($authrows)->passwd;
                                
                                $usermap = new Application_Model_CalUserMapper();
                                $where = array(
                                    array('login', ' = ', $group->groupname),
                                    );            
                                $users = $usermap->fetchWhere($where);
                                if (count($users) == 1)
                                {
                                    $usermap->delete($group->groupname);
                                }
                                $user = new Application_Model_CalUser();
                                $user->login = $group->groupname;
                                $user->passwd = $passwd;
                                $user->lastname = $group->groupname;
                                $user->firstname = 'Personal';
                                $usermap->save($user, 'new');
                                $this->view->message[] = "Password synchronized for $group->groupname.";
                            }
                        }
                            else
                                $this->view->message[] = "<i>No person assigned to $group->groupname.</i>";
                    }
                
                }
            }
        }

        $peoplemap = new Application_Model_PeopleMapper();
        $groupsmap = new Application_Model_CalGroupsMapper();
        $where = array(
            array('groupid', ' <> ', 0),
            );            
        $groups = $groupsmap->fetchWhere($where, array('groupname'));
        $table = array();
      	foreach($groups as $group) 
        {
        		$groupName = $group->groupname;
            $positionsmap = new Application_Model_PositionsMapper();
            $position = $positionsmap->find($group->groupid);
            $name = '';
            if ($position['contact1'] <> '') 
            {
                $person = $peoplemap->find($position['contact1']);
                if (isset($person))
                {
                    $name = $person['firstname'] . ' ' . $person['lastname'];
                }
            }

            $table[] = array($groupName, $name);
        }

        $this->view->style = 'zform.css';
        $this->view->data = $table;

    }


    public function calroomsAction()
    {
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['action']))
            {
                $action = $getvalues['roomAction'];
                if ($action == 'NAME')
                {
                    if (!isset($getvalues['id']))
                    {
                        $this->view->message = 'No room selected, nothing done.';
                    }
                    else
                    {
                        $ids = $getvalues['id'];
                        $theRoomID = $ids[0];
                        $theRoomName = $getvalues['newRoomName'];
                        $roomsmap = new Application_Model_CalRoomsMapper();
                        $where = array(
                            array('id', ' = ', $theRoomID),
                            );            
                        $room = current($roomsmap->fetchWhere($where));
                        $name = $room->roomname;
                        if ($theRoomName <> '')
                        {
                            $room->roomname = $theRoomName;
                            $room->roomcode = '';
                            $roomsmap->save($room);
                            $this->view->message = "$name changed to $theRoomName.";
                        }
                        else
                            $this->view->message = "Invalid room ID.";
                    }
                }
                
                if ($action == 'CODE')
                {
                    if (!isset($getvalues['id']))
                    {
                        $this->view->message = 'No room selected, nothing done.';
                    }
                    else
                    {
                        $ids = $getvalues['id'];
                        $theRoomID = $ids[0];
                        $theRoomCode = $getvalues['newRoomCode'];
                        $roomsmap = new Application_Model_CalRoomsMapper();
                        $where = array(
                            array('id', ' = ', $theRoomID),
                            );            
                        $room = current($roomsmap->fetchWhere($where));
                        $roomcode = $room->roomcode;
                        if ($theRoomCode <> '')
                        {
                            $room->roomcode = $theRoomCode;
                            $roomsmap->save($room);
                            $this->view->message = "$roomcode changed to $theRoomCode.";
                            
                            $nucname = "_NUC_" . $theRoomCode . "_Book";
                            $oldnucname = "_NUC_" . $room->roomcode . "_Book";

                            $nucmap = new Application_Model_CalNonuserCalsMapper();
                            $nucmap->delete($oldnucname);
                            $nuc = new Application_Model_CalNonuserCals();
                            $nuc->login = $nucname;
                            $nuc->lastname = 'Booking';
                            $nuc->firstname = $room->roomname;
                            $nuc->admin = 'SCHEDULE';
                            $nucmap->save($nuc, 'new');
                        }
                        else
                            $this->view->message = "Invalid room ID.";
                    }
                }
                
                if ($action == 'ADD')
                {
                    $theRoomName = $getvalues['newRoom'];
                    $roomsmap = new Application_Model_CalRoomsMapper();
                    $room = new Application_Model_CalRooms();
                    $room->roomname = $theRoomName;
                    $roomsmap->save($room, 'new');
                    $this->view->message = "$theRoomName room added.";
                }
                
                if ($action == 'DROP')
                {
                    if (!isset($getvalues['id']))
                    {
                        $this->view->message = 'No room selected, nothing done.';
                    }
                    else
                    {
                        $ids = $getvalues['id'];
                        $theRoomID = $ids[0];
                        $roomsmap = new Application_Model_CalRoomsMapper();
                        $room = $roomsmap->find($theRoomID);
                        $name = $room['roomname'];
                        
                        $roomsmap->delete($theRoomID);

                        $this->view->message = "$name deleted from rooms.";
                    }
                }
                    
            }

        }

        $roomsmap = new Application_Model_CalRoomsMapper();
        $where = array(
            array('timestamp', ' <> ', ''),
            );            
        $rooms = $roomsmap->fetchWhere($where, array('roomname'));
        $table = array();
        foreach ($rooms as $room)
        {
            $nucmap = new Application_Model_CalNonuserCalsMapper();
            $where = array(
                array('firstname', ' = ', $room->roomname),
                );            
            $nuc = $nucmap->fetchWhere($where);
            if (count($nuc) > 0)
                $calLogin = current($nuc)->login;
            else
                $calLogin = '';
            $table[] = array($room->id, $room->roomname, $room->roomcode, $calLogin);
        }
        $this->view->style = 'zform.css';
        $this->view->data = $table;
    }

            public function getApprovers()
            {
                $approversmap = new Application_Model_CalApproversMapper();
                $approvers = $approversmap->fetchAll();
                $approvalPositions = '(';
                foreach($approvers as $approver)
                {
                    if ($approvalPositions <> '(')
                        $new = ", '";
                    else
                        $new = "'";
                    $approvalPositions .= $new . $approver->title . "'";
                }
                $approvalPositions .= ")";
                return($approvalPositions);
            }


    public function calgroupsAction()
    {

        $approvalPositions = $this->getApprovers();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $getvalues = $request->getParams();
            if (isset($getvalues['action']))
            {
                $action = $getvalues['graction'];
                if ($action == 'ADD')
                {
                    $title = $getvalues['newTitle'];
                    $positionsmap = new Application_Model_PositionsMapper();
                    $where = array(
                        array('title', ' = ', $title),
                        );            
                    $positions = $positionsmap->fetchWhere($where);
                    if (count($positions) > 0)
                    {
                        $approversmap = new Application_Model_CalApproversMapper();
                        $approver = new Application_Model_CalApprovers();
                        $approver->title = $title;
                        $approversmap->save($approver, 'new');
                        $this->view->message = "$title approver added.";
                        $approvalPositions = $this->getApprovers();
                    }
                    else
                        $this->view->message = "$title not found in Positions list.";
                }
                
                if ($action == 'DROP')
                {
                    if (!isset($getvalues['id']))
                    {
                        $this->view->message = 'No position selected, nothing done.';
                    }
                    else
                    {
                        $ids = $getvalues['id'];
                        $id = $ids[0];
                        $positionsmap = new Application_Model_PositionsMapper();
                        $position = $positionsmap->find($id);
                        
                        $approversmap = new Application_Model_CalApproversMapper();
                        $where = array(
                            array('title', ' = ', $position['title']),
                            );            
                        $approver = current($approversmap->fetchWhere($where));
                        $name = $approver->title;
                        $id = $approver->id;
                        
                        $approversmap->delete($id);
                        $approvalPositions = $this->getApprovers();
                        $this->view->message = "$name deleted from approvers.";
                    }
                }
                
                if ($action == 'NAME')
                {
                    if (!isset($getvalues['id']))
                    {
                        $this->view->message = 'No position selected, nothing done.';
                    }
                    else
                    {
                        $ids = $getvalues['id'];
                        $groupid = $ids[0];
                        $theGroupName = $getvalues['newGroupName'];
                        $groupsmap = new Application_Model_CalGroupsMapper();
                        $where = array(
                            array('groupid', ' = ', $groupid),
                            );            
                        $groups = $groupsmap->fetchWhere($where);
                        if (count($groups) == 0)
                        {
                            $group = new Application_Model_CalGroups();
                        }
                        else
                            $group = current($groups);
                        $name = $group->groupname;
                        if ($theGroupName <> '')
                        {
                            $group->groupname = $theGroupName;
                            $group->groupid = $groupid;
                            $group->groupcode = '';
                            $groupsmap->save($group);
                            $this->view->message = "'$name' changed to $theGroupName.";
                        }
                        else
                            $this->view->message = "Invalid group ID.";
                    }
                }
                
                                
                if ($action == 'CODE')
                {
                    if (!isset($getvalues['id']))
                    {
                        $this->view->message = 'No position selected, nothing done.';
                    }
                    else
                    {
                        $ids = $getvalues['id'];
                        $id = $ids[0];
                        $newcode = $getvalues['newGroupCode'];
                        $groupsmap = new Application_Model_CalGroupsMapper();
                        $where = array(
                            array('groupid', ' = ', $id),
                            );            
                        $group = current($groupsmap->fetchWhere($where));
                        $groupcode = $group->groupcode;
                        if ($newcode <> '')
                        {
                            $group->groupcode = $newcode;
                            $groupsmap->save($group);
                            $this->view->message = "'$groupcode' changed to $newcode.";
                            
                            $nucname = "_NUC_" . $newcode . "_Book";
                            $oldnucname = "_NUC_" . $group->groupcode . "_Book";

                            $nucmap = new Application_Model_CalNonuserCalsMapper();
                            $nucmap->delete($oldnucname);
                            $nuc = new Application_Model_CalNonuserCals();
                            $nuc->login = $nucname;
                            $nuc->lastname = 'Approvals';
                            $nuc->firstname = $group->groupname;
                            $nuc->admin = 'SCHEDULE';
                            $nucmap->save($nuc, 'new');
                        }
                        else
                            $this->view->message = "Invalid group code.";
                    }
                }

                
                
            }
            
        }

        $positionsmap = new Application_Model_PositionsMapper();
        $where = array(
            array('title', ' IN ', $approvalPositions),
            );            
        $positions = $positionsmap->fetchWhere($where, array('title'));
        $table = array();
        foreach($positions as $position)
        {
            $title = $position->title;
            $groupsmap = new Application_Model_CalGroupsMapper();
            $where = array(
                array('groupid', ' = ', $position->id),
                );            
            $group = current($groupsmap->fetchWhere($where));
            $id = $position->id;            
            if ($group <> null)
            {
                $name = $group->groupname;
                $code = $group->groupcode;
                if ($code <> '')
                    $nuc = '_NUC_' . $code . '_Appr';
                else
                    $nuc = '';
            }
            else
            {
                $name = '';
                $code = '';
                $nuc = '';
            }
            
            $table[] = array($id, $title, $name, $code, $nuc);
        }
        $this->view->style = 'zform.css';
        $this->view->data = $table;
    }



    public function dbcleanAction()
    {
        echo "Checking for Resigned but not Inactive.<br>";
        $peoplemap = new Application_Model_PeopleMapper();
        $where = array(
            array('inactive', ' <> ', 'yes'),
            array('status', ' = ', 'Resigned'),
            );
        $resignedactive = $peoplemap->fetchWhere($where);
        $count = count($resignedactive);
        echo "Found $count Resigned but not Inactive.<br>";
        
        foreach ($resignedactive as $peep)
        {
            $name = $peep->firstname . ' ' . $peep->lastname;
            $peep->inactive = 'yes';
            $peoplemap->save($peep);
            echo "&nbsp;&nbsp;$name now Inactive.<br>";
        }
        echo "Done with Resigned not Inactive.<br><br>";
        exit;
    } 


    public function unsubAction()
    {
        $functions = new Cvuuf_functions();
        $efunctions = new Cvuuf_emailfunctions();
        $peoplemap = new Application_Model_PeopleMapper();
        $unsubmap = new Application_Model_UnsubMapper();
        $unsublogmap = new Application_Model_UnsubLogMapper();
        $this->view->message = array();

        $server="{mail.cvuuf.org/notls:110/pop3}";
        $login="unsubscribe@cvuuf.org";
        $password="RRVqrsP7";
        $connection = imap_open($server, $login, $password, OP_SILENT);
        $count = imap_num_msg($connection);
        $this->view->message[] = "$count emails found.<br>";
        for($i = 1; $i <= $count; $i++) 
        {
            $header = imap_header($connection, $i);
            $fromadr=$header->from[0]->mailbox.'@'.$header->from[0]->host;
            $fromdate=$header->MailDate;
//echo "HEADER <br>";
//var_dump($header); echo "<br>";
//echo "FROMDATE <br>";
//var_dump($fromdate); echo "<br>";

            $this->view->message[] = "Unsubscribe received from $fromadr";
            $encemailadr = imap_body($connection, $i);
echo "FROMADR <br>";
var_dump($fromadr); echo "<br>";
            if (strlen($encemailadr) < 10)
            {
                $emailadr = $fromadr;
            }
            else
            {
echo "ENCEMAILADR <br>";
var_dump($encemailadr); echo "<br>";
                $encbegin = strpos($encemailadr, '~') + 1;
                $encemailadr = substr($encemailadr, $encbegin);
                $encend = strpos($encemailadr, '~');
                $encemailadr = substr($encemailadr, 0, $encend);

//echo "PART <br>";
//var_dump($part); echo "<br>"; 
//echo "TRIMMED ENCEMAILADR <br>";
var_dump($encemailadr); echo "<br>";
                $emailadr = $functions->decryptData($encemailadr);
echo "EMAILADR <br>";
var_dump($emailadr); echo "<br>"; exit;
// exit;
             }
             $where = array(
                array('email', ' LIKE ', $emailadr),
                );
            $people = $peoplemap->fetchWhere($where); 
//var_dump($people);
            if (count($people) == 0)
            {
                $this->view->message[] = "$emailadr not in CVUUF database.<br>";
            }
            else
            {
                $person = current($people);
                $name = $person->firstname . ' ' . $person->lastname;
                $this->view->message[] = "Found $name for $emailadr in CVUUF database.";
                $pid = $person->id;
    
                $subject = $header->subject;
                $this->view->message[] = "Subject of email is '$subject' ";
                $types = array('all', 'weekly', 'newsletter', 'neighborhood', 'individual');
                if (in_array($subject, $types)) 
                { 
                    $where = array(
                        array('id', ' = ', $pid),
                        );
                    $unsubs = $unsubmap->fetchWhere($where); 
                    if (count($unsubs) <> 0)
                    {
                        $unsub = current($unsubs);
                        $unsub->$subject = TRUE;
                        $unsubmap->save($unsub, 'old');
                    }
                    else 
                    {
                        $unsub = new Application_Model_Unsub();
                        $unsub->id = $pid;
                        $unsub->$subject = TRUE;
                        $unsubmap->save($unsub, 'new');
                    }  
                    $this->view->message[] = "Person $name unsubscribed from $subject.<br>";
                    $unsublog = new Application_Model_UnsubLog();
                    $unsublog->personid = $pid;
                    $unsublog->email = $emailadr;
                    $unsublog->unsubtype = $subject;
                    $unsublogmap->save($unsublog);
                    
                    $SUBJECT = "Unsubscribed from CVUUF email";
                    $TO_array = array($emailadr);
                    $from = array('webmail@cvuuf.org' => "CVUUF");
                    $TEXT = "You have been sucessfully unsubscribed from CVUUF emails of type $subject.  If in the future you wish to resume receiving CVUUF emails, just let us know.";                   
                    $numsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, array('webmail@cvuuf.org' => "CVUUF"));
                }
                else
                {
                $this->view->message[] = "No action taken.<br>";
                
                }
            }
            imap_delete($connection, $i);
        }
        $close=imap_close($connection, CL_EXPUNGE);
    } 



    public function webtexteditAction()
    {
        $nodesmap = new Application_Model_NodesMapper();

        $request = $this->getRequest();    
        if ($this->getRequest()->isPost())    
        {
            $postvalues = $request->getParams();
            if (isset($postvalues))
            {

//var_dump($postvalues); 
                $todo = $postvalues['todo'];
                if ($todo == 'SELECT')
                {
                    $controller = '?';
                    $view = '?';
                    
                    $id = $postvalues['nodeid'];
                    if ($id <> '') {
                        $where = array(
                            array('id', ' = ', $id),
                            );
                    }
                    else {
                        if (isset($postvalues['contname']))
                        {
                            $controller = $postvalues['contname'];
                        }
                        if (isset($postvalues['viewname']))
                        {
                            $view = $postvalues['viewname'];
                        }
                        $title = $controller . '/' . $view;
//var_dump($title);
                        $where = array(
                            array('title', ' = ', $title),
                            );
                    }
                    $nodesarray = $nodesmap->fetchWhere($where);
//var_dump($nodesarray); exit;
                    if (count($nodesarray) == 1)
                    {
                        $node = current($nodesarray);
                        $id = $node->id;
                        $title = $node->title;
                        $slash = strpos($title, "/");
                        $controller = substr($title, 0, $slash);
                        $view = substr($title, $slash + 1);
                        $oldbody = $node->body;
                    }
                    else
                    {
                        $id = null;
                        $node = new Application_Model_Nodes();
                        $node->title = $title;
                        $node->content = 'Text';
                        $newbody = '';
                        $node->body = $newbody;
                        $nodesmap->save($node);
                        $id = $node->id;
//var_dump($node); exit;
                    }
                    
                    $this->view->old = array('controller' => $controller,
                        'view' => $view,
                        'id' => $id,
                        'thetext' => $oldbody);

                }
                    
                elseif ($todo == 'SAVE')
                {
                    $controller = $postvalues['contname'];
                    $view = $postvalues['viewname'];
                    $title = $controller . '/' . $view;
//var_dump($title); exit;
                        $where = array(
                            array('title', ' = ', $title),
                            );
                    $nodesarray = $nodesmap->fetchWhere($where);
//var_dump($nodesarray); exit;
                    if (count($nodesarray) == 1)
                    {
                        $node = current($nodesarray);
                        $id = $node->id;
                        $title = $node->title;
                        $slash = strpos($title, "/");
                        $controller = substr($title, 0, $slash);
                        $view = substr($title, $slash + 1);
                        $oldbody = $node->body;
                    }

                    $node->title = $title;
                    $node->content = 'Text';
                    $newbody = $postvalues['thetext'];
                    $node->body = $newbody;
                    if ($id <> null)
                        $node->id = $id;
                    $nodesmap->save($node);
                    
                    $this->view->message = array();
                    $this->view->message[] = "Controller:" . $controller . "  View:" . $view;
                    if ($id === null)
                        $this->view->message[] = 'Text node created.';
                    else
                    {
                        $this->view->message[] = 'Text node updated.';
                        $this->view->message[] = 'Old value <br>' . htmlentities($oldbody);
                    }
                        $this->view->message[] = 'New value <br>' . htmlentities($newbody);

//var_dump($newbody); exit;
                    $this->view->old = array('controller' => $controller,
                            'view' => $view,
                            'id' => $id,
                            'thetext' => $newbody);
                }
                
            }
            
        }

        $where = array(
            array('content', ' = ', 'Text'),
            );
        $this->view->nodes = $nodesmap->fetchWhere($where);
        $this->view->style = 'zform.css';

    }


    public function testAction()
    {
        $roomsmap = new Application_Model_CalRoomsMapper();
        $roomsmap->deleteall();    
    } 

}

