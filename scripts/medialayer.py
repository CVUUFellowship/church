#!/usr/bin/python

import urllib
import urllib2
import cookielib
import traceback
import httplib
import time
import re
import datetime
import sys
import secrets  # secrets.py contains 2 lines: "medialayer_pw='mypword'" and "medialayer_uname='cvuuf'"

# run as:
# python medialayer.py domains/cvuuf.org/application/views/scripts/support2/emailforwards.phtml

# https://apidocs.mailchimp.com/api/2.0/
verbose = False

def get_response_data(opener, req):
    try:
        req.add_header('Connection', 'keep-alive')
        req.add_header('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8')
        # req.add_header('Accept-Encoding', 'gzip, deflate')
        req.add_header('Accept-Language', 'en-US,en;q=0.8')
        req.add_header('User-Agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36')
        resp = opener.open(req)
    except urllib2.HTTPError as e:
        print 'HTTPError: %s (code=%s, reason=%s)' % (e, e.code, e.reason)
        print '  contents:\n%s' % (e.read())
        raise
    except urllib2.URLError as e:
        print 'URLError: %s (reason=%s)' % (e, e.reason)
        raise
    except httplib.HTTPException as e:
        print 'HTTPException: %s' % (e)
        raise
    except Exception as e:
        print "Unexpected error:", sys.exc_info()[0]
        print traceback.format_exc()
        raise
    else:
        global verbose
        if verbose:
            print 'got %s from %s' % (resp.getcode(), resp.geturl())
            print 'headers:'
            resp_info = resp.info()
            for header,val in resp_info.items():
                print '   %s: %s' % (header, val)
        resp_data = resp.read()
        return resp_data

def get_forwarders(out_filename):
    cj = cookielib.CookieJar()
    opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(cj))

    req = urllib2.Request('https://apollo.medialayer.net:2222/')
    actual_fetch = True
    if actual_fetch:
        first_page_resp = get_response_data(opener, req)

    time.sleep(1)
    reqd = urllib.urlencode([('referer','/'), ('username',secrets.medialayer_uname), ('password',secrets.medialayer_pw)])
    req = urllib2.Request('https://apollo.medialayer.net:2222/CMD_LOGIN', reqd)
    req.add_header('Origin', 'https://apollo.medialayer.net:2222')
    req.add_header('Content-Type', 'application/x-www-form-urlencoded')
    req.add_header('Referer', 'https://apollo.medialayer.net:2222/')
    if actual_fetch:
        login_resp = get_response_data(opener, req)

    def req_str(page):
        return 'https://apollo.medialayer.net:2222/CMD_EMAIL_FORWARDER?sort1=1&domain=cvuuf%2Eorg&page=' + str(page)

    page_tail_re = re.compile(r'Page:.*>(?P<last_page>\d+)</a>')
    fwd_head_re = re.compile(r'<tr\s*>\s*<td class=list2?\s*>(?P<from_mail>[^@]+@[^@]+)</td\s*>\s*<td class=list2?\s*>-></td\s*><td class=list2?\s*>')
    fwd_tail_re = re.compile(r'</td\s*>')
    subst_re = re.compile(r'&#(?P<val>\d+)')
    cur_page = 1
    top_page = 2
    all_mails = []
    while cur_page <= top_page:
        time.sleep(1)
        req = urllib2.Request(req_str(cur_page))
        if actual_fetch:
            forwarder_resp = get_response_data(opener, req)
            f = open('forwarder_page_%d.html' % (cur_page), 'w')
            f.write(forwarder_resp)
            f.close()
        else:
            f = open('forwarder_page_%d.html' % (cur_page), 'r')
            forwarder_resp = f.read()
            f.close()
        lines = forwarder_resp.split('\n')
        for line in lines:
            if 'Page:' in line:
                m = page_tail_re.search(line)
                if m:
                    top_page = int(m.group('last_page'))
            else:
                m = fwd_head_re.match(line)
                if m:
                    from_mail = m.group('from_mail')
                    off = m.end()
                    m = fwd_tail_re.search(line, off)
                    to_mails = []
                    if m:
                        to_tail = m.start()
                        for em in line[off:to_tail].split(',<br>'):
                            em = em.strip()
                            prev = 0
                            cur_em = ''
                            for m in subst_re.finditer(em):
                                repl = chr(int(m.group('val')))
                                cur_em += em[prev:m.start()] + repl
                                prev = m.end()
                            cur_em += em[prev:]
                            to_mails.append(cur_em)

                        all_mails.append((from_mail, to_mails))
                    # print ('%s: ' % (from_mail)) + (', '.join(to_mails))

        cur_page += 1

    f = open(out_filename, 'w')
    print >>f, '''<?php $this->head = "EMAIL FORWARDING" ?>
    <?php if (isset($this->message)) : ?>
        <?php echo "<hr><b><i>Notice:</i><br> " ?>
        <?php if (gettype($this->message) == 'array') : ?>
            <?php foreach ($this->message as $line) : ?>
                <?php echo $line, '<br>' ?>
            <?php endforeach ?>
        <?php else : ?>
            <?php echo $this->message ?>
        <?php endif ?>
</b><hr><br>
    <?php endif ?>
<br><br>
<center>
<strong><big>This directory information is for exclusive use by CVUUF Members and Friends
and is not to be used for any commercial purposes.</big></strong>
</center>

<br><br>
'''
    for from_mail, to_mails in all_mails:
        print >>f, '<b>%s</b><br/>' % (from_mail)
        for to_mail in sorted(to_mails):
            print >>f, '    &nbsp;&nbsp;&nbsp;%s<br/>' % (to_mail)
        print >>f, '<br/>'

    print >>f, '<br/>'
    loc = 'domains/cvuuf.org/application/views/scripts/support2/emailforwards.phtml'
    print >>f, '<small>last auto-generated at %s<br/>(python %s %s)<small/>' % (datetime.datetime.now(), sys.argv[0], loc)
    f.close()

if __name__=="__main__":
    get_forwarders(sys.argv[1])

