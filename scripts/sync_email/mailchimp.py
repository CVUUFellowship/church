import urllib
import sys
import urllib2
import json
import traceback
import httplib
import time
# import secret from outside my code path.
# secrets_path = os.path.join(os.path.dirname(os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))), 'secrets')
# sys.path.append(secrets_path)
from mailchimp_secrets import mailchimp_key

# mailchimp_receivers() returns list of these:
class MailchimpReceiver(object):
  '''mailchimp_users() returns a list of these'''

  seg_map = {
    'Weekly Announcements':'wa',
    'Community Forum':'cf',
    'Monthly Newsletter':'mn',
    'Member Meetings and Special Announcements':'mm',
  }

  def __init__(self, email, fname, lname, mail_segments):
    self.email = email.lower()
    self.fname = fname
    self.lname = lname
    self.mail_segments = mail_segments

    self.seg_sum = ','.join(MailchimpReceiver.seg_map[seg] for seg in mail_segments)

  def __str__(self):
    return self.email


# https://apidocs.mailchimp.com/api/2.0/
verbose = False

def get_response_data(req):
    try:
        resp = urllib2.urlopen(req)
    except urllib2.HTTPError as e:
        print 'HTTPError: %s (code=%s)' % (e, e.code)
        # print 'HTTPError: %s (code=%s, reason=%s)' % (e, e.code, e.reason)
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

def get_list_id():
    # https://apidocs.mailchimp.com/api/2.0/lists/list.php
    if True:
        domain = mailchimp_key.rsplit('-', 1)[1]
        api_fields = json.dumps({'apikey':mailchimp_key, 'filters':{}, 'start':0, 'limit':100})
        req = urllib2.Request('https://%s.api.mailchimp.com/2.0/lists/list.format' % (domain), api_fields)
        resp_data=get_response_data(req)
        resp_json = json.loads(resp_data)
        if resp_json['total'] != 1:
            print "Expected 1 list, got %s" % (resp_json['total'])
        list_id = resp_json['data'][0]['id']
        '''
        The whole response looks like this:
        {"total":1,"data":[{"id":"0ba3014544","web_id":479853,"name":"Conejo Valley UU Fellowship Members and Friends","date_created":"2014-11-01 16:12:14","email_type_option":false,"use_awesomebar":true,"default_from_name":"Conejo Valley UU Fellowship","default_from_email":"communicate@cvuuf.org","default_subject":"","default_language":"en","list_rating":3.5,"subscribe_url_short":"http:\/\/eepurl.com\/8oPrf","subscribe_url_long":"http:\/\/cvuuf.us9.list-manage.com\/subscribe?u=400639c1114daae501c63a104&id=0ba3014544","beamer_address":"us9-2fba6ae691-d3fe2d@inbound.mailchimp.com","visibility":"pub","stats":{"member_count":298,"unsubscribe_count":14,"cleaned_count":29,"member_count_since_send":0,"unsubscribe_count_since_send":0,"cleaned_count_since_send":0,"campaign_count":9,"grouping_count":2,"group_count":10,"merge_var_count":2,"avg_sub_rate":0,"avg_unsub_rate":8,"target_sub_rate":27,"open_rate":39.498141263941,"click_rate":1.5173067804647,"date_last_campaign":"2014-12-14 16:49:44"},"modules":[]}],"errors":[]}
        '''
    else:
        list_id = '0ba3014544'
    return list_id

def mailchimp_receivers():
    # https://<dc>.api.mailchimp.com/export/1.0/list/
    '''
    Here's what the response looks like if it has 2 entries:
 ["Email Address","First Name","Last Name","Announcements","Neighborhoods","MEMBER_RATING","OPTIN_TIME","OPTIN_IP","CONFIRM_TIME","CONFIRM_IP","LATITUDE","LONGITUDE","GMTOFF","DSTOFF","TIMEZONE","CC","REGION","LAST_CHANGED","LEID","EUID","NOTES"]
["jakeholland@yahoo.com","Jake","Holland","Monthly Newsletter, Auction Updates","No Neighborhood Emails Please",4,"",null,"2014-11-01 20:53:34",null,"34.4085000","-118.7740000","-8","-7","America\/Los_Angeles","US","CA","2014-11-01 22:12:53","222555609","2cbd66e9c4",null]
["ussb@ussb.uua.org","Unitarian Society of Santa Barbara","","Monthly Newsletter","No Neighborhood Emails Please",2,"",null,"2014-11-03 04:29:37","108.184.30.134",null,null,null,null,null,null,null,"2014-11-03 04:29:37","222637881","fbd96ba9d4",null]
    '''
    domain = mailchimp_key.rsplit('-', 1)[1]
    list_id = get_list_id()
    api_fields = urllib.urlencode({'apikey':mailchimp_key, 'id':list_id})
    req = urllib2.Request(('https://%s.api.mailchimp.com/export/1.0/list?' % (domain))+api_fields)
    resp_data = get_response_data(req)

    #print resp_data
    lines = [line for line in [lin.strip() for lin in resp_data.split('\n')] if line != '']
    objs = []
    for line in lines[1:]:
        line_data = json.loads(line)
        email, fname, lname, mail_lists = line_data[0:4]
        objs.append(MailchimpReceiver(email, fname, lname, mail_lists.split(', ')))

    return objs
    '''
    to_lines = []
    with open('last_chimp.txt', 'w') as f:
        for line in lines[1:]:
            line_data = json.loads(line)
            email, fname, lname = line_data[0], line_data[1], line_data[2]

            to_lines.append((email.lower(), fname, lname))
            print >>f, '%s,%s,%s' % to_lines[-1]
            #to_lines.append('%s,%s,%s' % (email.lower(), fname, lname))
        with open('last_goodchimp.txt', 'w') as goodf:
            for line in to_lines:
                print >>goodf, '%s,%s,%s' % line
    return to_lines
    '''

if __name__=="__main__":
    #print "you probably mean 'python cvuuf.py'"
    # lines=fetch_list2()
    # print '%d lines in last_chimp.txt' % (len(lines))
    objs = mailchimp_receivers()
    print 'mailchimp list has {0} members'.format(len(objs))
    segs = {}
    for obj in objs:
        for seg in obj.mail_segments:
            if seg not in segs:
                segs[seg] = 0
            segs[seg] += 1

    for seg, count in segs.items():
        print '  {0} in {1}'.format(count, seg)

