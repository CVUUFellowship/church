# In theory, I probably should be able to use a framework like Jinja to do this
# better. It seems more trouble than it saves to set up atm, however. I'm
# probably doing it wrong.
# dump a tabbed page with tables in it based on the Dynamic Tabs section of:
# http://www.w3schools.com/bootstrap/tryit.asp?filename=trybs_tabs_dynamic&stacked=h

import datetime
from cvuufdb import cvuuf_members
from googlegroups import emailgroup_members
from googlegrouplist import domain_groups
from itertools import chain
import urllib
import shutil
import cgi

suppress = set([
    'eastenders@cvuuf.org',
    'meadowood@cvuuf.org',
    'newburyneighbors@cvuuf.org',
    'suddenlyseymour@cvuuf.org',
    'tendegreescooler@cvuuf.org',
    'wildwest@cvuuf.org',
  ])

def dump_groups(fname):
  build_name = fname + '.building'
  # fail early if we can't write to this file.
  with open(build_name, "a") as outf:
    pass
  with open(fname, "a") as outf:
    pass
  start_msg = '<p>Last built at {when}</p>'.format(when=datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S'))
  print('%s: fetching list of groups...' % (datetime.datetime.now()))
  groups = domain_groups('cvuuf.org')
  for group in groups:
    if group.group_email in suppress:
      group.members = []
      continue
    print('%s: fetching group %s...' % (datetime.datetime.now(), group.group_email))
    group.members = emailgroup_members(group.group_email)

  db_members = dict((mem.email, mem) for mem in cvuuf_members())
  db_check_emails = list(chain(*[[gmem.email for gmem in group.members if gmem.email not in db_members] for group in groups]))
  if db_check_emails:
    db_check_members = cvuuf_members(db_check_emails)
    for mem in db_check_members:
      if mem.email not in db_members:
        db_members[mem.email] = mem

  print('%s: dumping page...' % (datetime.datetime.now()))
  with open(build_name, "w") as outf:
    print(start_msg, file=outf)
    last_log = datetime.datetime.now()
    ngroups = len(groups)
    idx = 0
    for group in groups:
      idx += 1
      if group.group_email in suppress:
        continue
      now = datetime.datetime.now()
      if now - last_log > datetime.timedelta(seconds=2):
        print('%s: %s/%s done (on %s)' % (now, idx, ngroups, group.group_email))
        last_log = now
      print('<b>{0}</b>:<br/>'.format(group.group_email), file=outf)
      for member in group.members:
        if member.email in db_members:
          mem = db_members[member.email]
          name = '{0} {1} '.format(mem.fname, mem.lname)
        else:
          name = ''
        print('&nbsp;&nbsp;&nbsp;{0}({1})<br/>'.format(name, member.email), file=outf)
      print('<br/>', file=outf)
    print('<p>Finished building at {when}</p>'.format(when=datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')), file=outf)
  print('%s: moving page into place...' % (datetime.datetime.now()))
  shutil.move(build_name, fname)
  print('%s: cleanly finished' % (datetime.datetime.now()))

if __name__=="__main__":
  import sys
  fname = sys.argv[1]
  dump_groups(fname)

