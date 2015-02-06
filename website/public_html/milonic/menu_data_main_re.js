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
aI("text=Navigation;showmenu=RENav;");
aI("text=Find a Person;url=/person/find?theme=re;");
aI("text=Enroll;showmenu=REEnroll;");
!1024
aI("text=Class Assignment;url=/re/assign;");
aI("text=Attendance;showmenu=REAttendance;");
!1024
aI("text=List Parents;url=/re/listparents;");
!1024
aI("text=Registration;showmenu=RERegistration;");
}
