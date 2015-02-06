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
aI("text=About;showmenu=About;");
aI("text=News & Events;showmenu=News;");
aI("text=Worship;showmenu=Worship;");
aI("text=Religious Education;showmenu=RE;");
aI("text=Programs;showmenu=Programs;");
aI("text=Visitors;showmenu=New;");
}
drawMenus();
