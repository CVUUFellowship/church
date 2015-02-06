<!-- The CVUUF Milonic license number is 189800 -->
with(milonic=new menuname("Main Menu")){
style=menuStyle;
screenposition="center";
top=124;
align="center";
itemwidth="120px";
itemheight="30px";
alwaysvisible=1;
orientation="horizontal";
!512
aI("text=Navigation;showmenu=AdminNav;");
aI("text=Login Maintenance;showmenu=AdminLogin;");
aI("text=Menus;showmenu=Menus;");
aI("text=User Permissions;showmenu=Permissions;");
aI("text=People;showmenu=People;");
!512
aI("text=Calendar;showmenu=AdminCalendar;");
aI("text=Website Text Edit;url=/admin/webtextedit;");
}
