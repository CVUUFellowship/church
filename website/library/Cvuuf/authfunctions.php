<?php
    // /library/Cvuuf/Cvuuf_functions
     
class Cvuuf_authfunctions
{

    public function getAuth($level = null, Application_Model_Auth $auth = null)
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
            return $auth;
        }
        return null;
    }


   
    public function getPermissions(Application_Model_Auth $auth)
    {
        $permissions = $this->permissionCodes();
        $level = $auth->level;
        $result = array();
        foreach ($permissions as $code)
        {
            if (($level && $code) <> 0)
                $result[$code] = true;
        }
        return($result);
    }


    
    public function hasPermission($permission, Application_Model_Auth $auth)
    {
        $p = $this->permissionCodes();
        $level = $auth->level;
        $result = array();
        $code = $p[$permission];
        if (($level & $code) <> 0)
            return(true);
        else
            return(false);
    }


    /* returns array with value index to names */
    public function permissionNames()
    {
        return array(
            0x0008 => 'membership',
            0x0400 => 're',
            0x0080 => 'neighborhood',
            0x0020 => 'ministry',
            0x0040 => 'change',
            0x0200 => 'admin',
        );

    }

    /* returns array with name index to value */
    public function permissionCodes()
    {
        return array(
            'membership' => 0x008,
            're' => 0x0400,
            'neighborhood' => 0x080,
            'ministry' => 0x020,
            'change' => 0x040,
            'admin' => 0x0200,
        );

    }
    
    public function privateAllowed()
    {
        return "'member','newfriend','affiliate', 'staff', 'spouse'";
    }
    
    public function adminAllowed()
    {
        return "'member','newfriend','affiliate', 'visitor', 'guest', 'child', 'resigned', 'staff', 'deceased', 'special', 'spouse', 'friend', 'guardian'";
    }
    
    public function reAllowed()
    {
        return "'member','newfriend','affiliate', 'visitor', 'staff', 'guardian', 'guest'";
    }


    public function boardAuth($pid)
    {
        $boardPositions = '"President", "Treasurer", "Vice-President", "Secretary", "Trustee at Large", "WebCrafter"';
        $positionsmap = new Application_Model_PositionsMapper();
        $where = array(
            array('title', ' IN ', "($boardPositions)"),
              );
        $positions = $positionsmap->fetchWhere($where);
        foreach ($positions as $pos)
        {
            if ($pos->contact1 == $pid)
                return true;
        }
        return false;
    }

    
}
