<?php
class Cvuuf_emailfunctions
{
      function isValidEmail($email)
      {  
          return filter_var(filter_var($email, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);  
      }  



    public function sendEmail($SUBJECT, $TO_array, $TEXT, $context, 
      // $FROM = array('webmail@cvuuf.org' => "CVUUF"), $REPLYTO = null, $ATTACHMENT = null) 
      $FROM = array('website@members.cvuuf.org' => "CVUUF"), $REPLYTO = null, $ATTACHMENT = null) 
    {
        $from = 'website@members.cvuuf.org'; // current(array_keys($FROM));
        // $from = 'cvuuf_org@bilbo.dreamhost.com'; // current(array_keys($FROM));
        $reply = 'communicationsdirector@cvuuf.org'; // current(array_keys($FROM));
        $sentcount = 0;
        $message = $TEXT;
        $header = "From: Cvuuf Website <".$from.">\r\n";
        $header .= "Reply-To: ".$reply."\r\n";
        $header .= "MIME-Version: 1.0" . "\r\n";
        $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $debug=0;
        if ($debug) {
            $myfile=fopen("/home/cvuuf_org/jakedebug.txt", "a");
        }
        $ok = 0;
        $bad = 0;
        foreach ($TO_array as $address) {
            if ($debug) {
                fwrite($myfile, "sendEmail, from:".$from.", to:".($address)."\n");
            }
            if (mail($address, $SUBJECT, $TEXT, $header, "-f".$from)) {
                $ok += 1;
            } else {
                $bad += 1;
            }
            $sentcount += 1;
        }
        if ($debug) {
            fwrite($myfile, "   sent ".$sentcount.", ok:".$ok.", bad:".$bad."\n");
            fclose($myfile);
        }
        /*
//set the recipient email address, where to send emails to
$to_email = incoming@example.com;
//set the sender email address
$your_email = administrator@example.com;
//use your email address as the sender
$header = "From: " . $your_email . "\r\n";
//put the site visitor's address in the Reply-To header
$header .= "Reply-To: " . $visitor_email . "\r\n";
//set the email Subject using the site visitor's name
$subject = "Contact Form Submission from " . $visitor_name;
//set the email body with all the site visitor's information
$emailMessage = "Name: " . $visitor_name . "\r\n";
$emailMessage .= "Email: " . $visitor_email . "\r\n";
$emailMessage .= "Message: " $message . "\r\n";
//send the email
//mail($to_email, $subject, $emailMessage, $header);
        $to = $TO_array;
        $startfrom = current(array_keys($FROM));
        $sentcount = 0;
        foreach ($to as $address) {
            $sentcount += 1;
        }

        $to_email = implode($TO_array);
*/

    if (0) {
        /* force the FROM.
        http://wiki.dreamhost.com/Sender_Domain_Policy_and_Spoofing#What_is_the_sender_domain_policy.3F
        That plus hosting our forwarders via google groups has made it so
        we can't send email, except by having another different domain which
        has email hosted entirely by dreamhost. We must use an address from
        that domain. I have it forwarding to jakeholand.net at gm. */
        // $FROM = array('website@members.cvuuf.org' => 'website@members.cvuuf.org');
        $FROM = array('cvuuf.org@cvuuf.net' => 'cvuuf.org@cvuuf.net');
        $functions = new Cvuuf_functions(); 
        $sentcount = 0;
        $server = $_SERVER["SERVER_NAME"];
        $myfile=fopen("/home/cvuuf_org/jakedebug.txt", "a");
        fwrite($myfile, "sendEmail, from:".implode($FROM).", to:".implode($TO_array)."\n");
        if (preg_match('/(www\.)?(members\.)?cvuuf\.(org|us|net|com)/i', $server))
        {
            require_once 'Swiftmailer/swift_required.php';
            
            //create transport
            /*
            $transport = Swift_SmtpTransport::newInstance()
                ->setHost('mail.cvuuf.org')
                ->setPort(25)
                ->setUsername('server@cvuuf.org')
                ->setPassword('xxxx');
            */
            $transport = Swift_SendmailTransport::newInstance();
            //Create mailer
            $mailer = Swift_Mailer::newInstance($transport);
            //Create the message
            $message = Swift_Message::newInstance()
                ->setSubject($SUBJECT)
                ->setFrom($FROM)
                ->setContentType('text/html');
           if (isset($REPLYTO))
           {
                $message->setReplyTo(array(
                  $REPLYTO
                  ));
            }

            if (isset($ATTACHMENT))
            {
                // Create the attachment
                // * Note that you can technically leave the content-type parameter out
                $attachment = Swift_Attachment::fromPath($ATTACHMENT);
                // Attach it to the message
                $message->attach($attachment);
            }
            //Send the message
            $failedRecipients = array();
            $numSent = 0;
            $to = $TO_array;
            $startfrom = current(array_keys($FROM));
            foreach ($to as $address)
            {
                if (substr($startfrom, 0, 7) == 'webmail')
                {
                    $encaddr = $functions->encryptData($address);
                    $sendtext = str_replace('~', '~' . $encaddr . '~  DO NOT CHANGE ANYTHING ON THIS LINE!', $TEXT);
                }
                else
                    $sendtext = $TEXT;
                $message->setTo($address)
                    ->setBody($sendtext);
                $numSent += $mailer->send($message, $failedRecipients);
            }
            $sentcount = $numSent;
            fwrite($myfile, "    matched: ".$sentcount."\n");
        }
  		  else {
            fwrite($myfile, "     unmatched:".$server."\n");
            $context->view->message = $TEXT;
  		  }
        fclose($myfile);
    }
        return ($sentcount);
    }
    

    public function sendBatchEmail($peeps, $unsubtype, $SUBJECT, $TEXT, $context, 
      // $FROM = array('webmail@cvuuf.org' => "Conejo Valley UU Fellowship"), $REPLYTO = null, $ATTACHMENT = null) 
      $FROM = array('website@members.cvuuf.org' => "Conejo Valley UU Fellowship"), $REPLYTO = null, $ATTACHMENT = null) 
    {
            /* force the FROM.
            http://wiki.dreamhost.com/Sender_Domain_Policy_and_Spoofing#What_is_the_sender_domain_policy.3F
            That plus hosting our forwarders via google groups has made it so
            we can't send email, except by having another different domain which
            has email hosted entirely by dreamhost. We must use an address from
            that domain. I have it forwarding to jakeholand.net at gm. */
            // $FROM = array('website@members.cvuuf.org' => "Conejo Valley UU Fellowship");
            $FROM = array('cvuuf.org@cvuuf.net' => 'cvuuf.org@cvuuf.net');
            $efunctions = new Cvuuf_emailfunctions();
            $peoplemap = new Application_Model_PeopleMapper();
            $unsmap = new Application_Model_UnsubMapper();

            $results = array();
            $orwhere = array(
                array('`all`', ' = ', 1),
                array($unsubtype, ' = ', 1),
                  );
            $unsubs = $unsmap->fetchOrWhere($orwhere);
            $unsubids = array();
            foreach ($unsubs as $unsub)
            {
                $person = $peoplemap->find($unsub->id);
                if ($person['email'] <> '')
                    $unsubids[$person['email']] = 1;
            }

            $emailCount = count($peeps);            
            $results[] = "$emailCount copies to be sent.";
        		$count = 0;
        		$fullcount = 0;

            $totalsent = 0;
            $invalid = array();
            $unsub = array();
            if ($emailCount >= 30) {
               return array('results' => $results." (rejected to avoid spam blacklists, 30-email max. Contact jakeholland.net@gmail.com to work out how to send this)", 'log' => 'rejected to avoid spam blacklists', 'totalsent' => $totalsent, 
                   'invalid' => $invalid, 'unsub' => $unsub);
            }
            unset($TO_array);
        		foreach ($peeps as $peep) 
            {
            	  $emailAddress = $peep->email;
            	  if (!$efunctions->isValidEmail($emailAddress))
                { 
                    $invalid[] = $emailAddress;
                }
                elseif (isset($unsubids[$emailAddress]))
                {
                    $unsub[] = $emailAddress;
                } 
                else 
                {
                    $fullcount++;
                    $TO_array[$count++] = $emailAddress;
              			if (($count%20) == 0) 
                    {
                        $numsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $context, $FROM, $REPLYTO, $ATTACHMENT);
                        $totalsent += $numsent;
                        unset($TO_array);
                        $count = 0;
                        sleep(1);
            		    }
            // Check section limit and delay if reached
                    if (($fullcount % 10) == 0)
                    {
                        $results[] = "Progress count $fullcount sent"; 
                        sleep(5);
                    }
              	}
            } 
      // send last email segment
        $numsent = $efunctions->sendEmail($SUBJECT, $TO_array, $TEXT, $context, $FROM, $REPLYTO, $ATTACHMENT);
        $totalsent += $numsent;
        $results[] = sprintf("Ending fraction count %d copies\n", $numsent);
        $log = $efunctions->log_email($context, $emailCount, $totalsent, count($invalid), count($unsub));
        $results[] = "Last Segment sent";

        return array('results' => $results, 'log' => $log, 'totalsent' => $totalsent, 
          'invalid' => $invalid, 'unsub' => $unsub);
    }
    
    
    public function log_email($env, $emailcount, $totalsent, $invalid, $unsub)
    {
        $logmap = new Application_Model_LogEmailMapper();
        $logitem = new Application_Model_LogEmail();
        $controller = $env->getRequest()->getControllerName();
        $action = $env->getRequest()->getActionName();
        $logitem->controller = $controller;
        $logitem->action = $action;
        $logitem->listcount = $emailcount;
        $logitem->sentcount = $totalsent;
        $logitem->invalid = $invalid;
        $logitem->unsub = $unsub;
        $logmap->save($logitem);
        return($logitem);
    }
    
    
    
}
