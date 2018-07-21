with(milonic=new menuname("DataNav")){
style=menuStyle;
aI("text=Public Welcome;url=http://cvuuf.org/;");
aI("text=Private;url=/private;");
!1024
aI("text=RE Admin;url=/re/admin;");
!512
aI("text=System Admin;url=/admin;");
}
with(milonic=new menuname("DataPeople")){
style=menuStyle;
aI("text=Find a Person;url=/person/find?theme=private;");
!72
aI("text=Enter visitors;url=/data/visitorsentry;");
!8
aI("text=Enter new person;url=/data/personentry;");
!8
aI("text=Visitor Calendar;url=/welcoming/createcalendar;");
!8
aI("text=Adjust visits;url=/data/maintvisits;");
!8
aI("text=Analyze visit dates;url=/data/analyzevisits;");
!8
aI("text=Visitors by Date;url=/welcoming/showvisitors;");
!8
aI("text=Friends by Date;url=/welcoming/showfriends;");
!8
aI("text=Members by Date;url=/welcoming/showmembers;");
!8
aI("text=Recent Members;url=/welcoming/recentmembers;");
!8
aI("text=Resigned Members;url=/welcoming/resignedmembers;");
!8
aI("text=Recent Friends and Members;url=/welcoming/recentfriends;");
!8
aI("text=All New Friends;url=/welcoming/allfriends;");
!8
aI("text=New Member Induction;url=/welcoming/induction;");
!8
aI("text=New Member Class;url=/welcoming/newclass;");
!64
aI("text=Assign Angels;url=/welcoming/angels;");
aI("text=Membership Statistics;url=/support/memberstats;");
!8
aI("text=Print name tags;url=/support/nametags;");
!8
aI("text=Directory Photos;url=/welcoming/photos;");
!8
aI("text=Examine email issues;url=/private/sync;");
}
with(milonic=new menuname("REAdmin")){
style=menuStyle;
!1024
aI("text=RE Admin;url=/re/admin;");
!1024
aI("text=RE Tracking;url=/re/track;");
!1024
aI("text=RE Enroll;url=/re/enroll;");
!1024
aI("text=RE Class Assignment;url=/re/assign;");
!1024
aI("text=RE Attendance;url=/re/attendance;");
}
with(milonic=new menuname("DataAdmin")){
style=menuStyle;
!1024
aI("text=Religious Education;showmenu=REAdmin;");
aI("text=Library Catalog Admin;url=/support/librarycatalog;");
aI("text=Groups and Positions Admin;showmenu=GroupsPositions;");
aI("text=Announcements Admin;showmenu=AdminAnnouncements;");
!64
aI("text=Send Bulk Email;url=/news/bulkemail;");
aI("text=Send neighborhood emails;url=/news/hoodemail;");
aI("text=Newsletter Admin;showmenu=NLAdmin;");
aI("text=News and Notes Upload;url=/support2/nnupload;");
aI("text=Minutes Management;url=/support2/minutesmanage;");
aI("text=Show email forwarders;url=/private/group;");
!8
aI("text=Maintain unsubscribes;url=/news/unsubscribe;");
}
with(milonic=new menuname("GroupsPositions")){
style=menuStyle;
!64
aI("text=Positions Admin;url=/data/positions;");
!64
aI("text=Position Classes Admin;url=/data/positionclasses;");
!64
aI("text=Groups Admin;url=/data/groups;");
!64
aI("text=Group Classes Admin;url=/data/groupclasses;");
}
with(milonic=new menuname("AdminAnnouncements")){
style=menuStyle;
!64
aI("text=Announcement maintenance;url=/news/announcemaint;");
!64
aI("text=Announcements send email;url=/news/announceemail;");
}
with(milonic=new menuname("NLAdmin")){
style=menuStyle;
aI("text=Upload newsletter;url=/support2/nlupload;");
aI("text=Send newsletters by email;url=/support2/nlsend;");
aI("text=Send newsletter announcement emails;url=/support2/nlannounce;");
}
drawMenus();
