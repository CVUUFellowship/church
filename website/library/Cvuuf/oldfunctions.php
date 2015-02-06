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
    
    

    public function sendEmail($SUBJECT, $TO_array, $TEXT, $context, $FROM = array('webmail@cvuuf.org' => "CVUUF")) {
        if ($_SERVER["SERVER_NAME"] == 'cvuuf.info') {
            
            require_once 'Swiftmailer/swift_required.php';
            
            //create transport
            $transport = Swift_SmtpTransport::newInstance()
                ->setHost('mail.cvuuf.org')
                ->setPort(25)
                ->setUsername('server@cvuuf.org')
                ->setPassword('areI4email');
             
            //Create mailer
            $mailer = Swift_Mailer::newInstance($transport);
             
            //Create the message
            $message = Swift_Message::newInstance()
                ->setSubject($SUBJECT)
                ->setFrom($FROM)
                ->setTo($TO_array)
                ->setBody($TEXT, 'text/html');
      
            //Send the message
            $mailer->send($message);
        }
  		  else {
            $context->view->message = $TEXT;
  		  }
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
    
}
