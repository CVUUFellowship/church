#!/usr/bin/python
import subprocess
import itertools
from cvuufdb_secrets import mysql_host, mysql_uname, mysql_pword, mysql_db

class CvuufMember(object):
    def __init__(self, email, fname, lname, status, hood_name, hood_group):
        self.email = email.lower()
        self.fname = fname
        self.lname = lname
        self.status = status
        self.hood_name = hood_name
        self.hood_group = hood_group

    def __str__(self):
        return self.email

def cvuuf_members(email_list=None):
    hood_mails = {
        '10 Degrees Cooler':'tendegreescooler',
        'Suddenly Seymour':'suddenlyseymour',
        'Eastenders':'eastenders',
        'Newbury Neighbors':'newburyneighbors',
        'Wild West':'wildwest',
        'Meadowood':'meadowood',
    }
    entries = []
    offset, step = 0, 20
    more = True
    while more:
        if not email_list:
            sql = 'select people.Email, people.FirstName, people.LastName, people.Status, people.inactive, hoods.hoodname from people left join neighborhoods on people.householdid=neighborhoods.householdid left join hoods on neighborhoods.hoodid=hoods.recordid where people.inactive <> "yes" and people.email <> "" and people.status in ("Member","NewFriend","Friend","Visitor");'
            more = False
        else:
            sql = 'select people.Email, people.FirstName, people.LastName, people.Status, people.inactive, hoods.hoodname from people left join neighborhoods on people.householdid=neighborhoods.householdid left join hoods on neighborhoods.hoodid=hoods.recordid where people.email in ({0});'.format(','.join('"{0}"'.format(em) for em in email_list[offset:offset+step]))
            offset += step
            more = (offset < len(email_list))

        p = subprocess.Popen(['mysql', '-h', mysql_host, '-u', mysql_uname, '-D', mysql_db, '-p' + mysql_pword, '-e', sql], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        out, outerr = p.communicate()
        if outerr:
            print 'stderr from mysql: %s' % (outerr)
        expected_anomalies = set([
            'admin@cvuuf.org',
            'brian_pletcher@hotmail.com',
            'cvuufmembership@cvuuf.org',
            'member@cvuuf.org',
            'ministerialcandidate@cvuuf.org',
            'holland@alumni.caltech.edu',
            'sample@email.tst',
            ])
        for line in out.rstrip('\n').split('\n')[1:]:
            try:
                email, fname, lname, status, inactive, hoodname = [x.strip() for x in line.split('\t')]
                if inactive == 'yes':
                    status = status + ' (inactive)'
                member = CvuufMember(email, fname, lname, status, hoodname, hood_mails.get(hoodname))
                if member.email not in expected_anomalies:
                    entries.append(member)
            except:
                print 'failed split on \\t: "%s"' % (line.strip())
                raise
    return entries

def filter_inactive(emails):
    p = subprocess.Popen(['mysql', '-h', 'mysql.cvuuf.net', '-u', 'cvuuf_cvuuf', '-D', 'cvuuf_cvuufinfo', '-p' + secrets.mysql_pword, '-e',
        'select people.Email, people.Inactive from people where people.email in (%s);' % (','.join(['"%s"' % e for e in emails]))], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    out, outerr = p.communicate()
    if outerr:
        print 'stderr from mysql in filter_inactive: %s' % (outerr)
    still_active = []
    for line in out.rstrip('\n').split('\n')[1:]:
        try:
            email, inactive = [x.strip() for x in line.split('\t')]
            if inactive != "yes":
                email = email.lower()
                still_active.append(email)
        except:
            print 'filter_inactive failed split on \\t: "%s"' % (line.strip())
    return still_active

if __name__=="__main__":
    members = cvuuf_members()
    hood_count = {}
    status_count = {}
    for mem in members:
        if mem.hood_group not in hood_count:
            hood_count[mem.hood_group] = 0
        hood_count[mem.hood_group] += 1
        if mem.status not in status_count:
            status_count[mem.status] = 0
        status_count[mem.status] += 1
    print 'Neighborhood counts:'
    for k,v in hood_count.items():
        print '  {0}: {1}'.format(v, k)
    print 'Status counts:'
    for k,v in status_count.items():
        print '  {0}: {1}'.format(v, k)

