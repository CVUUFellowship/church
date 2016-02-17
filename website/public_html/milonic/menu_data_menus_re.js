with(milonic=new menuname("RENav")){
style=menuStyle;
aI("text=Public Welcome;url=http://cvuuf.org/;");
aI("text=Private;url=/private;");
aI("text=Database;url=/data;");
!512
aI("text=System Admin;url=/admin;");
}
with(milonic=new menuname("REEnroll")){
style=menuStyle;
!1024
aI("text=Enroll;url=/re/enroll;");
!1024
aI("text=Tracking;url=/re/track;");
}
with(milonic=new menuname("REAttendance")){
style=menuStyle;
!1024
aI("text=Attendance;url=/re/attendance;");
!1024
aI("text=Attendance Report;url=/re/attreport;");
}
with(milonic=new menuname("RERegistration")){
style=menuStyle;
!1024
aI("text=Registration Maintenance;url=/re/maintain;");
!1024
aI("text=Print All Enrolled;url=/reaux/showregpages;");
}
drawMenus();
