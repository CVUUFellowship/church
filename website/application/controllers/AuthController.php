<?php

class AuthController extends Zend_Controller_Action
{

        public function createRandomPassword() 
        {
            $chars = "0123456789ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
            srand((double)microtime()*1000000);
            $i = 0;
            $pass = '' ;
        
            while ($i <= 7) {
                $num = rand() % 59;
                $tmp = substr($chars, $num, 1);
                $pass = $pass . $tmp;
                $i++;
            }
            return $pass;
        }


    public function init()
    {
        /* Initialize action controller here */
    }


    public function indexAction()
    {
        // action body
        $this->view->style = 'zform.css';
        $loginForm = new Application_Form_AuthLogin();
        $loginForm->remember->setDescription('Remember me on this computer');
        $this->view->loginForm = $loginForm;
        $loginForm->setAction('/auth/login');
        
        $newpassForm = new Application_Form_AuthNewpass();
        $this->view->newpassForm = $newpassForm;
        $newpassForm->setAction('pass');
    }


    public function loginAction()
    {

        $loginForm = new Application_Form_AuthNewpass();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) {
            if ($loginForm->isValid($request->getPost())) {
                $formData = $request->getParams();
                $email = $formData['email'];
                $password = $formData['passwd'];
                $people = new Application_Model_People();
                $peoplemap = new Application_Model_PeopleMapper();
                $peoplerow = $peoplemap->findEmail($email);
                $memberid = $peoplerow['id'];
        
                $auth = new Application_Model_Auth();
                $authmap = new Application_Model_AuthMapper();
                $authmap->findMatch($memberid, $password, $auth);
                if ($auth->id === null)
                {
                    setcookie ("KEY", FALSE, time()-3600,"/");
                    return $this->render('fail');
                }
                elseif ($auth->level == 1) 
                {
                    RedirectPage($admin);
                }
                else 
                {
        /* from va_vauthenticate */
        /* update login time and count */    
                    $authmap->save($auth);
        /* compute coded cookie and save it */
                    $userhash = substr(md5($memberid), 0, 6);
                    $keyvalue = sprintf("%04x%04x%04x%04x%04x%04x%04x%04x",
                        rand(0,65535), rand(0,65535), rand(0,65535), rand(0,65535),
                        rand(0,65535), rand(0,65535), rand(0,65535), rand(0,65535));
                    $value=$userhash.$keyvalue;
                    $rememberMe=$formData['remember'];
                    if ($rememberMe)
                        $die = 	time()+60*60*24*30;
                    else
                        $die=FALSE;
                    setcookie ("KEY", $value, $die,"/");
        
                    $cookie = new Application_Model_Cookies();
                    $cookmap = new Application_Model_CookiesMapper();
                    $cookie->value = $value;
                    $cookie->auth = $auth->id;
                    $cookmap->save($cookie); 
        /* return to continue where login wanted */
        if (0)
        {
        
                    $scriptBase=$_POST['scriptBase'];
        //echo "vAuth scriptBase is $scriptBase <br>"; exit;  
                    $REQ=$_COOKIE['REQ'];
                    if ($REQ<>'') {
                        $host  = $_SERVER['HTTP_HOST'];
                        header("Location: http://$host$REQ");
                    }
        }
                  
        	        $logmap = new Application_Model_LogLoginMapper();
                  $log = new Application_Model_LogLogin();
                  $log->memberid = $memberid;
                  $ip = $_SERVER['REMOTE_ADDR'];
                  $log->ip = $ip;
                  $log->timestamp = '0000-00-00 00:00:00';
                  $logmap->save($log);

                  $functions = new Cvuuf_functions();
                  $lastmonth = $functions->lastMonth();
                  $cookmap = new Application_Model_CookiesMapper();
                  $cookies = $cookmap->fetchAll();
                  $delcount = 0;
                  foreach ($cookies as $cookie)
                  {
                      if (substr($cookie->timestamp, 0, 10) < $lastmonth)
                      {
                          $result = $cookmap->delete($cookie->timestamp);
                          $delcount++;
                      }
                  }
                  $this->redirect('/private');
                  }
        	    }
        	}
        	else $this->render('fail');

    }
    
    
    public function passAction()
    {
        $passForm = new Application_Form_AuthNewpass();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) {
            if ($passForm->isValid($request->getPost())) {
                $formData = $request->getParams();
                $email = $formData['email'];
                $people = new Application_Model_People();
                $peoplemap = new Application_Model_PeopleMapper();
                $persons = $peoplemap->fetchEmail($email);
                $person = current($persons);
                if ($person == false)
                    return $this->render('unkemail');
                if ($person->id == 0)
                    return $this->render('unkemail');
                $memberid = $person->id;
                $pwd = $this->createRandomPassword();
                $auth = new Application_Model_Auth();
                $authmap = new Application_Model_AuthMapper();
                $authmap->findUser($memberid, $auth);
                if ($auth->id > 0)
                {
        // Person is in the authuser list
                    $auth->passwd = MD5($pwd);
                    $auth->status = 'active';
                    $authmap->save($auth);
                }
            // New person requesting access
              	else 
                {
                    $auth->passwd = MD5($pwd);
                    $auth->team = '';
                    $auth->memberid = $memberid;
                    $auth->status = 'active';
                    $auth->level = 2;
                    $authmap->save($auth);
                }
                $dispname = $person->firstname.' '.$person->lastname;
                $id = $person->id;
        //  log_email($_SERVER['SCRIPT_NAME'], 2);
        
            		$message="This message is in response to a request for a new ";
            		$message=$message."CVUUF web site restricted area password.\n\n";
            		$message=$message."If you did not request a new password, please ";
            		$message=$message."contact our webcrafter immediately to report a security ";
            		$message=$message."violation.\n\nYour new password is ".$pwd;
            		$message=$message."\n\nPlease keep this password ";
            		$message=$message."confidential.\n\n";
            
                $now = date("Y-m-d H:i:s", mktime(date('H'), date('i'), 0, date('m'), date('d'),date('Y')));
            
            		$TEXT="New password ".$pwd." for ".$id.":".$dispname." stamp ".$now;
                $SUBJECT="New CVUUF Password";
                $TO_array=array($email);
            
                $efunctions = new Cvuuf_emailfunctions();
                $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, array('security@cvuuf.org' => "CVUUF Security"));
                
                $log = $efunctions->log_email($this, 1, $totalsent, 0, 0);
            }
        }
    }

    
    public function logoutAction()
    {
        if (isset($_COOKIE['KEY']))
        {       
          	$key = $_COOKIE['KEY'];
          	setcookie ("KEY", FALSE, time()-3600, "/");
            $cookmap = new Application_Model_CookiesMapper();
                
            $cookmap->delete($key);
        }
    }

    
    public function changeAction()
    {

        $functions = new Cvuuf_authfunctions();
        $auth = $functions->getAuth('');
        if (!isset($auth))
            $this->_redirect('/auth/index');

        if ($functions->getAuth(null, $auth) === null)
            $this->_redirect('/auth/index');

        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->_request->getPost();
            $getvalues = $request->getParams();
            $password = $formData['change'];
            $verify = $formData['verify'];
            if ($password <> $verify)
            {
                $this->view->message = "New Password and Re-enter New don't match";
            }
            else
            {
                /* current user auth record is $auth */
                $auth->passwd = MD5($password);
                $authmap = new Application_Model_AuthMapper();
                $authmap->save($auth);
                $this->view->message = "New password accepted";

                $pid = $auth->memberid;
                $peoplemap = new Application_Model_PeopleMapper();
                $where = array(
                    array('id', ' = ', $pid),
                      );
                $person = current($peoplemap->fetchWhere($where));
                $dispname = $person->firstname.' '.$person->lastname;
                $email = $person->email;
                $id = $person->id;
        //  log_email($_SERVER['SCRIPT_NAME'], 2);
        
            		$message="This message is in response to a request for a new ";
            		$message=$message."CVUUF web site restricted area password.\n\n";
            		$message=$message."If you did not request a new password, please ";
            		$message=$message."contact our webcrafter immediately to report a security ";
            		$message=$message."violation.\n\nYour new password is ".$password;
            		$message=$message."\n\nPlease keep this password ";
            		$message=$message."confidential.\n\n";
            
                $now = date("Y-m-d H:i:s", mktime(date('H'), date('i'), 0, date('m'), date('d'),date('Y')));
            
if (0) // only for troubleshooting
{
            		$TEXT="Changed password ".$password." for ".$id.":".$dispname." stamp ".$now;
                $SUBJECT="New CVUUF Password";
                $TO_array=array('security@cvuuf.org');           
                $efunctions = new Cvuuf_emailfunctions();
                $totalsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $this, array('security@cvuuf.org' => "CVUUF Security"));
                $log = $efunctions->log_email($this, 1, $totalsent, 0, 0);
}                

            }


        }
        
        $this->view->style = 'zform.css';
        $changeForm = new Application_Form_AuthChange();
        $this->view->changeForm = $changeForm;
        $this->view->theme = 'private';
        
    }


    
    public function notauthAction()
    {
        $request = $this->getRequest();
        $getvalues = $request->getParams();
        $page = $getvalues['from'];
        $qmark = strpos($page, '?');
        if ($qmark !== false)
        {
            $page = substr($page, 0, $qmark);
        }
        $this->view->page = $page;
    }



    public function testAction()
    {
                  $functions = new Cvuuf_functions();
                  $lastmonth = $functions->lastMonth();
var_dump($lastmonth);
                  $cookmap = new Application_Model_CookiesMapper();
                  $cookies = $cookmap->fetchAll();
                  foreach ($cookies as $cookie)
                  {
                      if (substr($cookie->timestamp, 0, 10) < $lastmonth)
                      {
var_dump($cookie);
                      }
                  }
                  exit;
    }

    
}

