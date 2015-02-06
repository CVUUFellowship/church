<?php
    // /library/Cvuuf/Cvuuf_functions
     
class Cvuuf_personfunctions
{

    public function fillFindNames($first, $last, $allowed = 'private')
    {
        $functions = new Cvuuf_authfunctions();
        $inactive = 'yes';
        
        switch ($allowed)
        {
            case 'private':
                $allowed = $functions->privateAllowed();
                break;
            
            case 'admin':
                $allowed = $functions->adminAllowed();
                $inactive = '*';
                break;
            
            case 're':
                $allowed = $functions->reAllowed();
                break;
        }
        $findfirst = $first.'%';
        $findlast = $last.'%';
        $where = array(
            array('firstname', ' LIKE ', $findfirst),
            array('lastname', ' LIKE ', $findlast),
            array('inactive', ' <> ', $inactive),
            array('status', ' IN ', "($allowed)"),
        );
        $peoplemap = new Application_Model_PeopleMapper();
        $people = $peoplemap->fetchWhere($where);
        $names = array();
        foreach ($people as $person)
        {
            $names[] = array($person->id, $person->firstname . ' ' . $person->lastname, $person->status, $person->inactive);    
        }
        return($names);
    }


    public function fillFindEmails($email, $allowed = 'private')
    {
        $functions = new Cvuuf_authfunctions();
        switch ($allowed)
        {
            case 'private':
                $allowed = $functions->privateAllowed();
                break;
            
            case 'admin':
                $allowed = $functions->adminAllowed();
                break;
        }
        $findemail = $email.'%';
        $where = array(
            array('email', ' LIKE ', $findemail),
            array('inactive', ' <> ', 'yes'),
            array('status', ' IN ', "($allowed)"),
        );
        $peoplemap = new Application_Model_PeopleMapper();
        $people = $peoplemap->fetchWhere($where);
        $names = array();
        foreach ($people as $person)
        {
            $names[] = array($person->id, $person->firstname . ' ' . $person->lastname, $person->status, $person->inactive);    
        }
        return($names);
    }
    
    
}
