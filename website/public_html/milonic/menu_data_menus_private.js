with(milonic=new menuname("PrivNav")){
style=menuStyle;
aI("text=Public Welcome;url=/;");
aI("text=Database;url=/data;");
!1024
aI("text=RE Admin;url=/re/admin;");
!512
aI("text=System Admin;url=/admin;");
}
with(milonic=new menuname("PrivLogin")){
style=menuStyle;
aI("text=Logout;url=/auth/logout;");
aI("text=Change Password;url=/auth/change;");
}
with(milonic=new menuname("Directories")){
style=menuStyle;
aI("text=Find a Person;url=/person/find;");
aI("text=Members and Friends Directory;url=/private/fulldirectory;");
aI("text=Staff, Board, Council, Committees, Other;url=/private/listpositions;");
aI("text=Programs and Groups;url=/private/listgroups;");
aI("text=Neighborhoods;url=/private/listhoods;");
aI("text=Phone and Email List;url=/private/phonelist;");
}
with(milonic=new menuname("Minutes")){
style=menuStyle;
aI("text=Board meeting minutes;url=/private/showminutes?org=Board;");
aI("text=Council meeting minutes;url=/private/showminutes?org=Council;");
aI("text=Fellowship meeting minutes;url=/private/showminutes?org=Fellowship;");
}
with(milonic=new menuname("Worship")){
style=menuStyle;
!32
aI("text=Worship Grid;url=/private/worshipgrid;");
aI("text=Worship Service Plans;url=/private/worshipplans;");
!32
aI("text=Enter Service Descriptions;url=/private/worshipservice;");
!32
aI("text=Sermon audio upload;url=/support2/sermonaudioupload;");
}
with(milonic=new menuname("/private/info")){
style=menuStyle;
aI("text=Small Groups Charters;url=/info/chartersgroups;");
aI("text=Organization Charters;url=/info/chartersorg;");
aI("text=Policies;url=/info/policies;");
aI("text=News Release List;url=/private/showfile?file=news_release_list&type=pdf;");
aI("text=Bylaws;url=/private/showfile?file=bylaws&type=pdf;");
aI("text=Conflict Resolution Procedure;url=/private/showfile?file=CCR_procedure&type=pdf;");
aI("text=History;showmenu=PrivateHistory;");
aI("text=Forms;showmenu=PrivateForms;");
}
with(milonic=new menuname("PrivateHistory")){
style=menuStyle;
aI("text=History - our first 50 years;url=/private/showfile?file=history&type=pdf;");
aI("text=Staff, Presidents, and Officers;url=/private/showfile?file=history-officers&type=pdf;");
aI("text=Fellowship Sweethearts;url=/private/showfile?file=history-sweethearts&type=pdf;");
aI("text=Newsletters in the archives;url=/private/showfile?file=archived_newsletters&type=pdf;");
aI("text=Our Church homes;url=/private/showfile?file=archives_homes&type=pdf;");
}
with(milonic=new menuname("Calendar")){
style=menuStyle;
aI("text=Submit an Event;url=/calendar/request;");
aI("text=Approvals;url=/calendar/approvals;");
aI("text=Show calendar;url=/calendar;");
}
with(milonic=new menuname("PrivateForms")){
style=menuStyle;
aI("text=Artist's agreement;url=/artist_agreement_cvuuf.pdf;");
aI("text=Request for funds or reimbursement;url=/private/showfile?file=request_for_funds&type=pdf;");
aI("text=Fundraising Application Form;url=/private/showfile?file=Fundraising&type=pdf;");
}
drawMenus();
