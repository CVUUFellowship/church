with(milonic=new menuname("About")){
style=menuStyle;
aI("text=Welcome;url=/;");
aI("text=Minister;url=/about/minister;");
aI("text=Leadership;url=/about/leadership;");
aI("text=History;url=/about/history;");
}
with(milonic=new menuname("News")){
style=menuStyle;
aI("text=Calendar of Events;url=/calendar;");
aI("text=News and Notes;url=/public/newsandnotes;");
aI("text=Newsletter;url=/public/newsletter;");
}
with(milonic=new menuname("Worship")){
style=menuStyle;
aI("text=Upcoming Services;url=/worship/upcoming;");
aI("text=Sermon Text;url=/worship/sermons;");
aI("text=Sermon Audio;url=/worship/audiosermons;");
aI("text=Sermon Archive;url=/worship/archive;");
}
with(milonic=new menuname("RE")){
style=menuStyle;
aI("text=Welcome;url=/re;");
aI("text=For Children;url=/re/children;");
aI("text=For Adults;url=/re/adults;");
}
with(milonic=new menuname("Programs")){
style=menuStyle;
aI("text=Welcome;url=/public/welcome;");
aI("text=Community Forum;url=http://forum.cvuuf.org;");
aI("text=Outreach;url=/public/outreach;");
aI("text=Rabbit in the Moon;url=http://conejomoon.org;");
aI("text=Library;showmenu=Library;");
}
with(milonic=new menuname("New")){
style=menuStyle;
aI("text=Intro;url=/visitors/intro;");
aI("text=Answers;url=/visitors/answers;");
aI("text=Principles;url=/visitors/principles;");
aI("text=Voices;url=/visitors/voices;");
aI("text=Links;url=/visitors/links;");
}
with(milonic=new menuname("Library")){
style=menuStyle;
aI("text=The Library;url=/library;");
aI("text=Catalog of Books;url=/library/catalog;");
aI("text=Catalog of Audio Sermons;url=/library/audiocatalog;");
aI("text=Wish List;url=/library/wishlist;");
aI("text=New Books;url=/library/newbooks;");
}
drawMenus();
