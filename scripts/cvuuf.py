#!/usr/bin/python
import subprocess
import secrets
import mailchimp_lists
import itertools

def pull_db_list():
    p = subprocess.Popen(['mysql', '-h', 'mysql.cvuuf.net', '-u', 'cvuuf_cvuuf', '-D', 'cvuuf_cvuufinfo', '-p' + secrets.mysql_pword, '-e',
        'select people.Email, people.FirstName, people.LastName, people.Status from people left join unsub on people.recordid=unsub.personid where (unsub.personid is null or unsub.all=0) and people.inactive <> "yes" and people.email <> "" order by people.email;'], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
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
    entries = []
    for line in out.rstrip('\n').split('\n')[1:]:
        try:
            email, fname, lname, status = [x.strip() for x in line.split('\t')]
            email = email.lower()
            if email not in expected_anomalies:
                entries.append((email, fname, lname, status))
        except:
            print 'failed split on \\t: "%s"' % (line.strip())
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

def sync():
    all_db = sorted(pull_db_list())
    all_ch = sorted(mailchimp_lists.fetch_list2())
    def map_all(all, desc):
        email2idx, name2idx = {}, {}
        dup_ems = {}
        for i, x in enumerate(all):
            email = x[0]
            name = (x[1], x[2])
            if email in email2idx:
                prev = all[email2idx[email]]
                print '%s appears multiple times in %s (%s and %s)' % (email, desc, (prev[1], prev[2]), name)
                dup_ems[email] = email2idx[email]
            email2idx[email] = i
            if name in name2idx:
                prev = all[name2idx[name]]
                print '%s appears multiple times in %s (%s and %s)' % (name, desc, prev[0], email)
            name2idx[name] = i
        return email2idx, name2idx, dup_ems
    db_email2idx, db_name2idx, dup_dbems = map_all(all_db, 'db')
    ch_email2idx, ch_name2idx, dup_chems = map_all(all_ch, 'chimp')

    cur_dbn = set(db_name2idx.keys())
    cur_dbn -= set([(x[1],x[2]) for x in [all_db[y] for y in dup_dbems.values()]])
    cur_chn = set(ch_name2idx.keys())
    both_n = cur_dbn & cur_chn
    db_not_ch_n = cur_dbn - cur_chn
    ch_not_db_n = cur_chn - cur_dbn

    #db_only_entries = sorted([all_db[db_name2idx[name]] for name in list(db_not_ch_n)], key=lambda a: (a[0]))
    db_only_entries = [all_db[db_name2idx[name]] for name in list(db_not_ch_n)]
    res = mailchimp_lists.fetch_activity([x[0] for x in db_only_entries])
    db_only_entries = sorted([(res[e[0]], e) for e in db_only_entries])
    for chimpres, values in itertools.groupby(db_only_entries, lambda x: x[0]):
        if chimpres != 'to_welcome':
            continue
        for cres, (email, fname, lname, status) in sorted(values, key=lambda x:(x[1][3],x[1][1])):
            print 'http://cvuuf.us9.list-manage1.com/subscribe?u=400639c1114daae501c63a104&id=0ba3014544&MERGE0=%s&MERGE1=%s&MERGE2=%s&group[13233][1]=true&group[13233][2]=true&group[13233][8]=true' % (email, fname, lname)

    for chimpres, values in itertools.groupby(db_only_entries, lambda x: x[0]):
        print '%s:' % chimpres
        for cres, (email, fname, lname, status) in sorted(values, key=lambda x:(x[1][3],x[1][1])):
            print '   %s,%s,%s,%s' % (email, fname, lname, status) # res.get(email, "unknown"))
    print ' --> %d names in db not chimp' % (len(db_not_ch_n))
    '''returns {email:val} where val is in [Objected, Hard_bounce, Soft_bounce, Unsub, summary_*, to_welcome]'''

    chimp_only_entries = [all_ch[ch_name2idx[name]] for name in sorted(list(ch_not_db_n))]
    chimp_active_entries = set(filter_inactive([x[0] for x in chimp_only_entries]))
    for email, fname, lname in chimp_only_entries:
        if email in chimp_active_entries:
            print '%s,%s,%s' % (email, fname, lname)
    print " --> %d names in chimp not db (%d inactive not shown)" % (len(ch_not_db_n), len(chimp_only_entries) - len(chimp_active_entries))
    n_different = 0
    for dbe, che, name in [(all_db[db_name2idx[name]][0], all_ch[ch_name2idx[name]][0], name) for name in sorted(list(both_n))]:
        if dbe != che:
            n_different += 1
            print "db:    %s   (%s,%s)" % (dbe, name[0], name[1])
            print "chimp: %s" % (che)
    if n_different != 0:
        print " --> %d changed emails" % (n_different)
    print "in db %d, in chimp %d, in both: %d" % (len(cur_dbn), len(cur_chn), len(both_n))
    '''
    cur_db = set([x[0] for x in all_db])
    cur_chimp = set([x[0] for x in all_chimp])

    both = cur_db & cur_chimp
    db_not_chimp = cur_db - cur_chimp
    chimp_not_db = cur_chimp - cur_db

    for email, fname, lname, status in [all_db[db_email2idx[em]] for em in sorted(list(db_not_chimp))]:
        print '%s,%s,%s,%s' % (email, fname, lname, status)
    print ' --> %d emails in db not chimp' % (len(db_not_chimp))
    for email, fname, lname in [all_chimp[chimp_email2idx[em]] for em in sorted(list(chimp_not_db))]:
        print '%s,%s,%s' % (email, fname, lname)
    print " --> %d emails in chimp not db" % (len(chimp_not_db))
    print "in db %d, in chimp %d, in both: %d" % (len(cur_db), len(cur_chimp), len(both))
    '''

if __name__=="__main__":
    sync()

