# In theory, I probably should be able to use a framework like Jinja to do this
# better. It seems more trouble than it saves to set up atm, however. I'm
# probably doing it wrong.
# dump a tabbed page with tables in it based on the Dynamic Tabs section of:
# http://www.w3schools.com/bootstrap/tryit.asp?filename=trybs_tabs_dynamic&stacked=h

import datetime
from cvuufdb import cvuuf_members
from googlegroups import emailgroup_members
from mailchimp import mailchimp_receivers
import urllib
import shutil
import cgi

page_head = '''
  <p>Last built at {when}<br/>
  </p>
  <ul class="nav nav-tabs">'''.format(
    when=datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S'))

first_head = '''    <li class="active"><a data-toggle="tab" href="#{0}_pane">{1}</a></li>'''
later_head = '''    <li><a data-toggle="tab" href="#{0}_pane">{1}</a></li>'''

tabs_start = '''</ul>

  <div class="tab-content">'''


pane_open = '''
<script>
$(document).ready(function()
    {{
        $("#{0}_table").tablesorter( {{sortList: [[3,0], [4,1], [1,0], [0,0]]}} );
    }}
);
</script>
    <div id="{0}_pane" class="tab-pane fade{1}">'''
# pane_open = '''    <div id="{0}" class="tab-pane fade in active">'''
pane_close = '''    </div>'''

finish_page = '''
</div>'''

table_head = '''<table id="{0}_table" class="tablesorter"> 
<thead> 
<tr>'''

table_body = '''</tr> 
</thead> 
<tbody>'''
table_done = '''</tbody></table>'''

class Pane(object):
  def __init__(self, divid, title, tables):
    self.divid = divid
    self.title = title
    self.tables = tables

class Table(object):
  def __init__(self, tableId, emailRows, use_groups=True):
    self.tableId = tableId
    self.emailRows = emailRows
    self.use_groups = use_groups

def build_page(outf, panes):
  print(page_head, file=outf)
  cur_head = first_head
  for pane in panes:
    print(cur_head.format(pane.divid, pane.title), file=outf)
    cur_head = later_head
  print(tabs_start, file=outf)
  pane_arg = ' in active'
  for pane in panes:
    print(pane_open.format(pane.divid, pane_arg), file=outf)
    pane_arg = ''
    probset = ProblemSet()
    for table in pane.tables:
      print(table_head.format(table.tableId), file=outf)
      print(EmailRow.row_header(use_groups=table.use_groups), file=outf)
      print(table_body, file=outf)
      for row in table.emailRows:
        print('  <tr>', file=outf)
        print(row.row_contents(probset, use_groups=table.use_groups), file=outf)
        print('  </tr>', file=outf)
      print(table_done, file=outf)
    print('<p>', file=outf)
    just_announcements = probset.no_announcements - probset.no_chimps
    if just_announcements:
      print("<strong>addresses not receiving announcements: </strong>%s<br/>" % (', '.join(just_announcements)), file=outf)
    if probset.no_chimps:
      print("<strong>addresses not receiving any mailchimp: </strong>%s<br/>" % (', '.join(probset.no_chimps)), file=outf)
    if probset.neighborhood:
      print("<strong>addresses not on neighborhood list: </strong>%s<br/>" % (', '.join(probset.neighborhood)), file=outf)
    print('</p>', file=outf)
    print(pane_close, file=outf)
  print('<p>Finished rebuild at {0}</p>'.format(datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')), file=outf)
  print(finish_page, file=outf)

class Neighborhood(object):
  def __init__(self, group):
    self.group = group
    self.emails = []

class ProblemSet(object):
  def __init__(self):
    self.no_chimps = set()
    self.no_announcements = set()
    self.neighborhood = set()

class EmailRow(object):
  def __init__(self, email):
    self.email = email
    self.members = []
    self.receivers = []
    self.groups = []
    self.group_set = set()
    self.problems = 0

  @staticmethod
  def row_header(use_groups=True):
    if use_groups:
      return '''    <th>First Name</th>
    <th>Last Name</th>
    <th>Email</th>
    <th>Status</th>
    <th>Problems</th>
    <th>Descriptions</th>
    <th>Groups</th>'''
    else:
      return '''    <th>First Name</th>
    <th>Last Name</th>
    <th>Email</th>
    <th>Status</th>
    <th>Problems</th>
    <th>Descriptions</th>'''

  def row_contents(self, probset, use_groups=True):
    fnames = []
    lnames = []
    emails = [self.email]
    statuses = []
    for mem in self.members:
      if mem.fname not in fnames:
        fnames.append(mem.fname)
      if mem.lname not in lnames:
        lnames.append(mem.lname)
      if mem.email not in emails:
        emails.append(mem.email)
      if mem.status not in statuses:
        statuses.append(mem.status)
    for rcv in self.receivers:
      if rcv.fname not in fnames:
        fnames.append(rcv.fname)
      if rcv.lname not in lnames:
        lnames.append(rcv.lname)
      if rcv.email not in emails:
        emails.append(rcv.email)
    problemCount = 0
    problems = []
    if len(self.members) != 1:
      problemCount += 1
      if len(self.members) == 0:
        problems.append('db:missing')
      else:
        problems.append('db:dup_email')

    if len(self.receivers) != 1:
      if len(self.receivers) == 0:
        if not any(map(lambda mem: mem.status == 'Special', self.members)):
          problemCount += 1
          problems.append('chimp:missing')
          if any(map(lambda mem: mem.status == 'Member' or mem.status == 'NewFriend', self.members)):
            probset.no_chimps.add(self.members[0].email)
      else:
        problemCount += 1
        problems.append('chimp:multiple')

    rcv_names = set((rcv.fname, rcv.lname) for rcv in self.receivers)
    mem_names = set((mem.fname, mem.lname) for mem in self.members)
    all_names = rcv_names.union(mem_names)
    if all_names != rcv_names and all_names != mem_names:
      # problemCount += 1
      problems.append('db_chimp:name_mismatch')
    if self.members:
      if any(map(lambda mem: mem.status == 'Member', self.members)):
        if not any(map(lambda rcv: 'Member Meetings and Special Announcements' in rcv.mail_segments, self.receivers)):
          problemCount += 1
          problems.append('chimp:announcements')
          probset.no_announcements.add(self.members[0].email)

    if self.members:
      if any(map(lambda mem: not mem.hood_group, self.members)):
        if any(map(lambda mem: mem.status in ['Member','Friend','NewFriend'], self.members)):
          problemCount += 1
          problems.append('db:no_neighborhood')
      elif any(map(lambda mem: mem.hood_group not in self.group_set, self.members)):
        problemCount += 1
        problems.append("expected:{0}".format(list(self.group_set)))
        for mem in self.members:
            if mem.hood_group not in self.group_set:
                problems.append('in group:{0}'.format(mem.hood_group))
        #problems.append('group:{0}'.format(mem.hood_group))
        if any(map(lambda mem: mem.status == 'Member' or mem.status == 'NewFriend', self.members)):
          probset.neighborhood.add(self.members[0].email)

    if self.members and self.receivers and not any(map(lambda rcv: rcv.email == self.email, self.receivers)):
      problemCount += 1
      problems.append('db_chimp:email_mismatch')

    '''expected:
     exactly one members. (0 means not in db, 2 means dup email in db)
     exactly one receiver (0 means not in mailchimp, 2 means mailchimp non-unique by email? would be confusing, probably hard assert.)
     receiver name matches mem name
     mem.status = Member => "Member Meetings and Special Announcements" in receiver.mail_segments
     mem.hood_group is not None (else no group set in db)
     mem.hood_group in groups
     (weakly: groups == [mem.hood_group], outside the known special cases)
     emails in groups are in email2mem
'''
    self.problems = problemCount

    row = {
      'fname':'<br/>'.join(fnames),
      'lname':'<br/>'.join(lnames),
      'email':'<br/>'.join(emails),
      'status':'<br/>'.join(statuses),
      'problemCount':problemCount,
      'problems':'<br/>'.join(problems),
      'groups':'<br/>'.join(self.groups),
    }

    if use_groups:
      return u'''    <td>{fname}</td>
    <td>{lname}</td>
    <td>{email}</td>
    <td>{status}</td>
    <td>{problemCount}</td>
    <td>{problems}</td>
    <td>{groups}</td>'''.format(**row)
    else:
      return u'''    <td>{fname}</td>
    <td>{lname}</td>
    <td>{email}</td>
    <td>{status}</td>
    <td>{problemCount}</td>
    <td>{problems}</td>'''.format(**row)
  

class Overview(object):
  def __init__(self):
    self.email2row = {}
    self.neighborhoods = {}

  def get_row(self, email):
    row = self.email2row.get(email)
    if not row:
      row = EmailRow(email)
      self.email2row[email] = row
    return row

  def add_members(self, members):
    for mem in members:
      row = self.get_row(mem.email)
      row.members.append(mem)
      if mem.hood_group not in self.neighborhoods:
        self.neighborhoods[mem.hood_group] = Neighborhood(mem.hood_group)

  def add_receivers(self, receivers):
    for receiver in receivers:
      row = self.get_row(receiver.email)
      row.receivers.append(receiver)

  def add_group(self, group, members):
    print(' ({0} members)'.format(len(members)))
    for gmem in members:
      row = self.get_row(gmem.email)
      if group not in self.neighborhoods:
        self.neighborhoods[group] = Neighborhood(group)
      row.groups.append(group)
      row.group_set.add(group)
      self.neighborhoods[group].emails.append(gmem.email)

  def finalize(self):
    non_mems_by_name = {}
    for em, row in self.email2row.items():
      if row.receivers and not row.members:
        rcv = row.receivers[0]
        name = (rcv.fname, rcv.lname)
        non_mems_by_name[name] = row

    for em, row in list(self.email2row.items()):
      if row.members and not row.receivers:
        for mem in row.members:
          name = (mem.fname, mem.lname)
          rcv_row = non_mems_by_name.get(name)
          if not rcv_row:
            continue
          row.receivers = rcv_row.receivers
          row.groups.extend(rcv_row.groups)
          row.group_set.update(rcv_row.group_set)
          del self.email2row[rcv_row.email]
          break
    db_check_emails = [row.email for row in self.email2row.values() if not row.members]
    db_check_members = cvuuf_members(db_check_emails)
    for mem in db_check_members:
      assert(mem.email in self.email2row)
      self.email2row[mem.email].members.append(mem)

def sync_emails(fname):
  build_name = fname + '.building'
  # fail early if we can't write to this file.
  with open(build_name, "a") as outf:
    pass
  with open(fname, "a") as outf:
    pass
  overview = Overview()
  print('%s: fetching db members...' % (datetime.datetime.now()))
  overview.add_members(cvuuf_members())
  print('%s: fetching mailchimp receivers...' % (datetime.datetime.now()))
  overview.add_receivers(mailchimp_receivers())
  for group, hood in overview.neighborhoods.items():
    if group:
      print('%s: fetching group %s...' % (datetime.datetime.now(), group))
      overview.add_group(group, emailgroup_members('{0}@cvuuf.org'.format(group)))

  print('%s: hooking up results...' % (datetime.datetime.now()))
  overview.finalize()

  # oops. problems is only set when building, so i guess build.
  print('%s: building tables...' % (datetime.datetime.now()))
  probset = ProblemSet()
  for row in overview.email2row.values():
    x = row.row_contents(probset)
  all_table = Table('all', overview.email2row.values(), use_groups=True)
  probRowCount = len([row for row in all_table.emailRows if row.problems])
  all_pane = Pane('all', 'All ({0}/{1})'.format(probRowCount, len(all_table.emailRows)), [all_table])
  panes = [all_pane]
  for group in overview.neighborhoods.keys():
    if not group:
      gname = 'none'
    else:
      gname = group
    table = Table(gname, [row for row in overview.email2row.values() if group in row.group_set or group in [mem.hood_group for mem in row.members]], use_groups=True)
    probRowCount = len([row for row in table.emailRows if row.problems])
    pane = Pane(gname, '{0} ({1}/{2})'.format(gname, probRowCount, len(table.emailRows)), [table])
    panes.append(pane)

  print('%s: dumping page...' % (datetime.datetime.now()))
  with open(build_name, "w") as outf:
    build_page(outf, panes)
  print('%s: moving page into place...' % (datetime.datetime.now()))
  shutil.move(build_name, fname)
  print('%s: cleanly finished' % (datetime.datetime.now()))

if __name__=="__main__":
  import sys
  fname = sys.argv[1]
  sync_emails(fname)

