<?php

class Cvuuf_menufunctions
{
    public function menuFile($file, $level, $end = null)
    {
        echo "<script language='JavaScript' type='text/javascript'>";
//echo "\n\nSTART OF $file level is $level\n";
        $startToken = "<?php";
        $fd = fopen ($file, "r"); 

        $lineno = 0;    
    //echo strlen($forwardLine), " ", ord($forwardLine[0]), "<br>";
      	while (!feof ($fd)) { 
          	$buffer = fgets($fd, 4096);
          	$menuLine = substr($buffer, 0, strlen($buffer)-1);
            if ($menuLine == '')
            {
                if ($end === null)
                    echo "</script>";
                return;
            }

            if ($menuLine[0] == '!')
            {
//echo "\nFOUND CONDITIONAL \n"; 
                $condition = substr($menuLine, 1);
                $buffer = fgets($fd, 4096);
                $menuLine = substr($buffer, 0, strlen($buffer)-1);
                if (($level & $condition) > 0)
                {
                    echo $menuLine;
                }
                else
                {
//                        echo "SKIPPED LINE condition $condition AND level $level"; exit;
                }     
            }
            else
            {
                echo $menuLine;
            }
        $lineno++;
        };

    if ($end === null)
        echo "</script>";
    return;
    }


    public function buildMenus() 
    {
    
        function writemenu($filename, $themenu, $menulno)
        {
//echo "<br><br>$filename<br>";
//var_dump($themenu); 
            $filedir=$_SERVER["DOCUMENT_ROOT"].'/milonic/';
          	$fullname = $filedir.$filename;
//echo "NAME '$fullname' <br>"; 
          	$fp = fopen ($fullname, "wb");
//echo "FP is $fp <br>";
//exit;
          	for ($i = 0; $i < $menulno; $i++)
          			fwrite ($fp, $themenu[$i] . "\r\n");	
          	fclose($fp);
        }

        $menusmap = new Application_Model_MenusMapper();
        $where = array(
            array('page', ' <> ', ' '),
            );            
        $menupages = $menusmap->fetchColumn($where, 'page', true);
        foreach ($menupages as $menupage)
        {
//          the main menu(s)    
          	$themenu=array(
                "<!-- The CVUUF Milonic license number is 189800 -->",
            		"with(milonic=new menuname(\"Main Menu\")){",
            		"style=menuStyle;",
            		"screenposition=\"center\";",
            		"top=124;",
            		"align=\"center\";",
                "itemwidth=\"120px\";",
                "itemheight=\"30px\";",
            		"alwaysvisible=1;",
            		"orientation=\"horizontal\";",
          	);
    
            // Public menu
          	$menufixed = count($themenu);
            $menus = new Application_Model_Menus();
            $where = array(
                array('name', ' = ', 'Main'),
                array('page', ' = ', $menupage->page)
                );            
            $menus = $menusmap->fetchWhere($where);
            $menulno = $menufixed;
            $phpstart = "<?php ";
            $phpend = "?>";
            $endif = "$phpstart endif $phpend";
          	foreach($menus as $menu) {
                if ($menu->level > 0)
                {
//                    $ifline = "$phpstart" . 'if (($this->level & ' . $menu->level . ") <> 0) : $phpend";
                    $ifline = "!$menu->level";
                    $themenu[$menulno++] = $ifline;
                		$themenu[$menulno++] = "aI(\"text=" . $menu->Text . ';' .
                			($menu->type == 'url' ? 'url' : 'showmenu') . '=' . $menu->item . ';");';
//                    $themenu[$menulno++] = $endif;
                }
                else
                 		$themenu[$menulno++] = "aI(\"text=" . $menu->Text . ';' .
                    ($menu->type == 'url' ? 'url' : 'showmenu') . '=' . $menu->item . ';");';
          	}
          	$themenu[$menulno++] = '}';
            $pagename = $menupage->page;
            if ($pagename == 'Public')
                $menufile = "menu_data_main.js";
            else
                $menufile = "menu_data_main_".strtolower($pagename).".js";
            writemenu($menufile, $themenu, $menulno);
            
                        // the referenced menus	
            $menus = new Application_Model_Menus();
            $where = array(
                array('type', ' = ', 'menu'),
                array('page', ' = ', $pagename)
                );            
            $menus = $menusmap->fetchWhere($where);
//echo "<br><br><br>MENU<br>";var_dump($menus);    
          	$menulno = 0;
          	foreach($menus as $menu) {
            		$themenu[$menulno++] = "with(milonic=new menuname(\"" . $menu->item . "\")){";
            		$themenu[$menulno++]="style=menuStyle;";
                $where = array(
                    array('name', ' = ', $menu->item),
                    array('page', ' = ', $pagename)
                    );            
                $submenus = $menusmap->fetchWhere($where);
//echo "<br><br>SUBMENU<BR>";var_dump($submenus);
              	foreach($submenus as $submenu) {
                if ($submenu->level > 0)
                {

//echo "<br>$submenu->Text<br>";
                    $ifline = "!$submenu->level";
                    $themenu[$menulno++] = $ifline;
                		$themenu[$menulno++] = "aI(\"text=" . $submenu->Text . ';' .
                			($submenu->type == 'url' ? 'url' : 'showmenu') . '=' . $submenu->item . ';");';
                }
                else
                 		$themenu[$menulno++] = "aI(\"text=" . $submenu->Text . ';' .
                    ($submenu->type == 'url' ? 'url' : 'showmenu') . '=' . $submenu->item . ';");';

//              		$themenu[$menulno++] = "aI(\"text=" . $submenu->text . ';' .
//              			($submenu->type == 'url' ? 'url' : 'showmenu') . '=' . $submenu->item . ';");';
              	}
            $themenu[$menulno++] = '}';
        }
        $themenu[$menulno++]='drawMenus();';
        $pagename = $menupage->page;
        $menufile = "menu_data_menus_".strtolower($pagename).".js";
        writemenu($menufile, $themenu, $menulno);
        }        
    }



    public function buildMenumap() 
    {
        $menus = new Application_Model_Menus();
        $menusmap = new Application_Model_MenusMapper();
        $where = array(
            array('type', ' = ', 'url'),
            array('page', ' = ', 'Public'),
            array('name', ' <> ', 'Links'),
            array('item', ' <> ', '/calendar'),
            array('item', ' <> ', '/index/pageconstruction'),
            );            
        $menus = $menusmap->fetchWhere($where);

        $maplno = 0;
        $themap = array();
        foreach ($menus as $item)
        {
            $line = '<a href="'. $item->item . '">' . $item->text . '</a>';
            $themap[$maplno++] = $line;
        }    


	/*
        $filedir=$_SERVER["DOCUMENT_ROOT"];
      	$fullname = $filedir.'/map.html';
      	$fp = fopen ($fullname, "wb");
      	for ($i = 0; $i < $maplno; $i++)
      			fwrite ($fp, $themap[$i] . "\r\n");	
      	fclose($fp);
	*/
    
    }


}

