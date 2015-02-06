<?php

class PersonController extends Zend_Controller_Action
{

        function getPerson($id)
        {

            $peoplemap = new Application_Model_PeopleMapper();
            $person = $peoplemap->find($id);
            $inactive = $person['inactive'];
            
            $comments = '';
            $cid = 0;
            $aid = 0;
            $inducted = '';
            $angelname = '';
            $hooddot = 'dot-none';
            $hoodname = '';
            
            $status = $person['status'];
            $this->view->status = $status;
            if ($status == 'Visitor')
            {
                $visitorsmap = new Application_Model_VisitorsMapper();
                $visitor = $visitorsmap->find($id);
                $comments = $visitor['comment'];
            }
            else
            {
                $where = array(
                    array('peopleid', ' = ', $id),
                    );            
                $connectionsmap = new Application_Model_ConnectionsMapper();
                $connections = $connectionsmap->fetchWhere($where);
                $connectionobj = current($connections);
                if (null <> $connectionobj)
                {
                    $cid = $connectionobj->id;
                    $connection = $connectionsmap->find($cid);
                    $comments = $connection['comments'];
                    $aid = $connection['angelid'];
                    $angel = $peoplemap->find($aid);
                    $angelname = $angel['firstname'] . ' ' . $angel['lastname'];
                    $inducted = $connection['inducted'];
                    if ($inducted == '') $inducted = 'No';
                    if ($inducted == 'X') $inducted = 'Yes';
                }
            }                
          
            $hid = $person['householdid'];
            $householdmap = new Application_Model_HouseholdsMapper();
            $household = $householdmap->find($hid);

            $authmap = new Application_Model_AuthMapper();
            $authuser = new Application_Model_Auth();
            $authmap->findUser($id, $authuser);
            
            $emailnlmap = new Application_Model_NewsletterEmailsMapper();
            $result = $emailnlmap->find($id);
            if ($result == 'yes')
                $emailnewsletter = 'yes';
            else
                $emailnewsletter = 'no';
                
            $hoodmap = new Application_Model_NeighborhoodsMapper();
            $where = array(
                array('householdid', ' = ', $hid),
                );            
            $hoods = $hoodmap->fetchWhere($where);
            if ($hoods != null)
            {
                $hood = current($hoods);
                $nid = $hood->hoodid;
                $namemap = new Application_Model_HoodsMapper();
                $where = array(
                    array('id', ' = ', $nid),
                    );            
                $names = $namemap->fetchWhere($where);
                $name = current($names);
                $hoodname = $name->hoodname;
                $hooddot = $name->dot;
            }
            $data = array();
            $data[] = $inactive;
            $data[] = $id;
            $data[] = $person['firstname'] . ' ' . $person['lastname'];
            $data[] = $person['photolink'];
            $data[] = $comments;
            $data[] = $person['creationdate'];
            $data[] = $household['creationdate'];
            $data[] = $hid;
            $data[] = $household['householdname'];
            $data[] = $hooddot;
            $data[] = $hoodname;
            $data[] = $household['street'];
            $data[] = $household['city'] . ', ' . $household['state'] . ' ' . $household['zip'];
            $data[] = $this->formatPhoneNumber($household['phone'], '-');
            $data[] = $this->formatPhoneNumber($person['pphone'], '-');
            $data[] = $emailnewsletter;
            $data[] = $person['email'];
            $data[] = $person['status'];
            $data[] = $person['membershipdate'];
            $data[] = $inducted;
            $data[] = $angelname;
            $data[] = $cid;
            $data[] = $authuser->id;
            $data[] = $authuser->status;
            return($data);
        }

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

    public function init()
    {
        $this->view->theme = 'private';
        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth();
        $this->view->userid = $auth->memberid;
        if (isset($auth))
        {
            $this->view->permprivate = true;
            $this->view->level = $auth->level;
        }
        else
            $this->view->permprivate = false;

        if ($functions->hasPermission('membership', $auth))
            $this->view->permmembership = true;     
        else
            $this->view->permmembership = false;

        if ($functions->hasPermission('admin', $auth) == true)
            $this->view->permadmin = true;
         else
            $this->view->permadmin = false;

        if ($functions->hasPermission('change', $auth) == true)
            $this->view->permchange = true;
        else
            $this->view->permchange = false;
    }


    public function indexAction()
    {
    }


    public function findAction()
    {
        $request = $this->getRequest();
        $getvalues = $request->getParams();
        if (isset($getvalues['theme']))
        {
            $theme = $getvalues['theme'];
            $this->view->theme = $theme;
        }    
            
        if ($this->getRequest()->isPost()) 
        {
            if (isset($getvalues['find']))
            {
                $first = $getvalues['first'];
                $last = $getvalues['last'];
                $functions = new Cvuuf_personfunctions();
                $names = $functions->fillFindNames($first, $last, 'admin');
                if (count($names) == 1)
                {
                    $this->view->data = $this->getPerson($names[0][0]);              
                    $this->view->style = 'zform.css';
                    return $this->render('show');                        
                }
                else
                    $this->view->names = $names;
            }
             
            if (isset($getvalues['findemail']))
            {
                $email = $getvalues['email'];
                $functions = new Cvuuf_personfunctions();
                $names = $functions->fillFindEmails($email, 'admin');
                if (count($names) == 1)
                {
                    $this->view->data = $this->getPerson($names[0][0]);              
                    $this->view->style = 'zform.css';
                    return $this->render('show');                        
                }
                else
                    $this->view->emails = $names;
            }
             
             
            if (isset($getvalues['findid']))
            {
                $findid = $getvalues['id'];
                $data = $this->getPerson($findid);
                if ($data[0] === null)
                {
                    $this->view->message = "Person ID not found.";
                }
                else
                {
                    $this->view->data = $data;
                    $this->view->style = 'zform.css';
                    return $this->render('show');                        
                }
            }

        }

        $this->view->style = 'zform.css';
        $personForm = new Application_Form_AdminPersonSelect();
        if (isset($first))
        {
            $personForm->first->setValue($first);
            $personForm->last->setValue($last);
            $this->view->first = $first;
            $this->view->last = $last;
        }
        $this->view->personForm = $personForm;

        $emailForm = new Application_Form_AdminEmailSelect();
        if (isset($email))
        {
            $emailForm->email->setValue($email);
            $this->view->email = $email;
        }
        $this->view->emailForm = $emailForm;

        $idForm = new Application_Form_AdminIDSelect();
        if (isset($findid))
        {
            $idForm->id->setValue($findid);
        }
        $this->view->idForm = $idForm;
    }



    public function showAction()
    {
        $request = $this->getRequest();
        $functions = new Cvuuf_personfunctions();
        $getvalues = $request->getParams();
        if (isset($getvalues['theme']))
        {
            $this->view->theme = $getvalues['theme'];
        }    
        if ($this->getRequest()->isPost()) 
        {
            if (isset($getvalues['personbutton']))
            {
                if (!isset($getvalues['id']))
                {
                    $this->view->message = "No person selected, nothing done.";
                    if (isset($getvalues['email']))
                    {
                        $email = $getvalues['email'];
                        $names = $functions->fillFindEmails($email, 'admin');
                        $this->view->emails = $names;
                        $emailForm = new Application_Form_AdminEmailSelect();
                        if (isset($email))
                        {
                            $emailForm->email->setValue($email);
                            $this->view->email = $email;
                        }
                        $this->view->emailForm = $emailForm;
                    }
                    if (isset($getvalues['first']))
                    {
                        $first = $getvalues['first'];
                        $last = $getvalues['last'];
                        $names = $functions->fillFindNames($first, $last, 'admin');
                        $this->view->names = $names;
                        $this->view->style = 'zform.css';
                        $personForm = new Application_Form_AdminPersonSelect();
                        $personForm->first->setValue($first);
                        $personForm->last->setValue($last);
                        $this->view->first = $first;
                        $this->view->last = $last;
                        $this->view->personForm = $personForm;
                    }
                    
                    return $this->render('find');
                }
                else
                {
                    $ids = $getvalues['id'];
                    $id = $ids[0];
                    $peoplemap = new Application_Model_PeopleMapper();
                    $person = $peoplemap->find($id);
                    if ($person === null)
                    {
                        $this->view->message = "Person ID not found.";
                    }
                    else
                    {
                        $this->view->data = $this->getPerson($id);              
                        $this->view->style = 'zform.css';
                        return $this->render('show');
                    }
                }
            
            }

            if (isset($getvalues['changebutton']))
            {
//echo "CHANGE in Person button";exit;            
            
            }
            
            
        }
    }
    
    
    public function showpersonAction()
    {
        $request = $this->getRequest();
        $functions = new Cvuuf_personfunctions();
        $getvalues = $request->getParams();
        if ($this->getRequest()->isGet()) 
        {
            $findid = $getvalues['id'];
            $data = $this->getPerson($findid);
            if ($data[0] === null)
            {
                $this->view->message = "Person ID not found.";
            }
            else
            {
                $this->view->theme = 'data';
                $this->view->data = $data;
                $this->view->style = 'zform.css';
                return $this->render('show');
            }
        }
    }                        
    
}

