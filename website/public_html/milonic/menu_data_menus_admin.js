with(milonic=new menuname("AdminNav")){
style=menuStyle;
aI("text=Public Welcome;url=/;");
aI("text=Private;url=/private;");
aI("text=Database;url=/data;");
aI("text=RE Admin;url=/re/admin;");
}
with(milonic=new menuname("AdminLogin")){
style=menuStyle;
aI("text=Logout;url=/auth/logout;");
aI("text=Change Password;url=/auth/change;");
}
with(milonic=new menuname("Menus")){
style=menuStyle;
aI("text=Public;url=/menus/select?page=public;");
aI("text=Private;url=/menus/select?page=private;");
aI("text=Database;url=/menus/select?page=data;");
aI("text=Admin;url=/menus/select?page=admin;");
aI("text=RE;url=/menus/select?page=re;");
}
with(milonic=new menuname("Permissions")){
style=menuStyle;
!512
aI("text=Summary;url=/admin/permissions;");
!512
aI("text=Invalid authorizations;url=/admin/perminvalid;");
!512
aI("text=Additional permissions;url=/admin/permspecial;");
!512
aI("text=One person;url=/admin/permperson;");
}
with(milonic=new menuname("People")){
style=menuStyle;
!512
aI("text=Process Unsubscribes;url=/admin/unsub;");
!512
aI("text=Person Search;url=/person/find?theme=admin;");
}
with(milonic=new menuname("AdminCalendar")){
style=menuStyle;
!512
aI("text=Rooms;url=/admin/calrooms;");
!512
aI("text=Approvers;url=/admin/calgroups;");
!512
aI("text=Logins;url=/admin/calapprove;");
}
drawMenus();
