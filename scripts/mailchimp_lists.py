import urllib
import sys
import urllib2
import json
import traceback
import httplib
import time
import secrets  # secrets.py contains 1 line: "mailchimp_key='mysecretkey-usX'"

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
    if False:
        domain = secrets.mailchimp_key.rsplit('-', 1)[1]
        api_fields = json.dumps({'apikey':secrets.mailchimp_key, 'filters':{}, 'start':0, 'limit':100})
        req = urllib2.Request('https://%s.api.mailchimp.com/2.0/lists/list.format' % (domain), api_fields)
        resp_data=get_response_data(req)
        resp_json = json.loads(resp_data)
        if resp_json['total'] != 1:
            print "Expected 1 list, got %s" % (resp_json['total'])
        list_id = resp_json['data'][0]['id']
        '''
        The whole response looks like this:
        {"total":1,"data":[{"id":"0ba3014544","web_id":479853,"name":"Conejo Valley UU Fellowship Members and Friends","date_created":"2014-11-01 16:12:14","email_type_option":false,"use_awesomebar":true,"default_from_name":"Conejo Valley UU Fellowship","default_from_email":"communications@cvuuf.org","default_subject":"","default_language":"en","list_rating":3.5,"subscribe_url_short":"http:\/\/eepurl.com\/8oPrf","subscribe_url_long":"http:\/\/cvuuf.us9.list-manage.com\/subscribe?u=400639c1114daae501c63a104&id=0ba3014544","beamer_address":"us9-2fba6ae691-d3fe2d387f@inbound.mailchimp.com","visibility":"pub","stats":{"member_count":298,"unsubscribe_count":14,"cleaned_count":29,"member_count_since_send":0,"unsubscribe_count_since_send":0,"cleaned_count_since_send":0,"campaign_count":9,"grouping_count":2,"group_count":10,"merge_var_count":2,"avg_sub_rate":0,"avg_unsub_rate":8,"target_sub_rate":27,"open_rate":39.498141263941,"click_rate":1.5173067804647,"date_last_campaign":"2014-12-14 16:49:44"},"modules":[]}],"errors":[]}
        '''
    else:
        list_id = '0ba3014544'
    return list_id

def fetch_list1():
    # this case does not work for the 2nd request. start appears broken?
    # https://apidocs.mailchimp.com/api/2.0/lists/members.php
    '''
    The response would look like this if it had just 2 entries (i added whitespace)

    {"total":298,"data":[
      {"email":"susandixon55@hotmail.com","id":"0126867a95",
       "euid":"0126867a95","email_type":"html","ip_signup":null,
       "timestamp_signup":null,"ip_opt":null,"timestamp_opt":"2014-11-01 20:53:36",
       "member_rating":4,"info_changed":"2014-11-01 22:01:24","web_id":222556045,
       "leid":222556045,"language":"en","list_id":"0ba3014544",
       "list_name":"Conejo Valley UU Fellowship Members and Friends",
       "merges":{"EMAIL":"susandixon55@hotmail.com","FNAME":"Susan","LNAME":"Dixon",
       "GROUPINGS":[{"id":13233,"name":"Announcements","form_field":"checkboxes",
          "groups":[{"name":"Weekly Announcements","interested":true},
                    {"name":"Monthly Newsletter","interested":true},
                    {"name":"Auction Updates","interested":true}]},
            {"id":13237,"name":"Neighborhoods","form_field":"radio",
             "groups":[
               {"name":"Newbury Neighbors","interested":false},
               {"name":"Eastenders","interested":true},
               {"name":"10 Degrees Cooler","interested":false},
               {"name":"Wild West","interested":false},
               {"name":"Suddenly Seymour","interested":false},
               {"name":"Meadowood","interested":false},
               {"name":"No Neighborhood Emails Please","interested":false}]}]},
        "status":"subscribed","timestamp":"2014-11-01 20:53:36",
        "is_gmonkey":false,"lists":[],
        "geo":{"latitude":"47.6836000","longitude":"-122.1220000","gmtoff":"-8","dstoff":"-7","timezone":"America\/Los_Angeles","cc":"US","region":"WA"},
        "clients":{"name":"Hotmail","icon_url":"http:\/\/us9.admin.mailchimp.com\/images\/email-client-icons\/hotmail.png"},
        "static_segments":[],"notes":[]},
      {"email":"dweeblet2@yahoo.com","id":"0167dfbe85",
       "euid":"0167dfbe85","email_type":"html","ip_signup":null,
       "timestamp_signup":null,"ip_opt":null,"timestamp_opt":"2014-11-01 20:53:32",
       "member_rating":3,"info_changed":"2014-11-01 22:03:09","web_id":222555265,
       "leid":222555265,"language":null,"list_id":"0ba3014544",
       "list_name":"Conejo Valley UU Fellowship Members and Friends",
       "merges":{"EMAIL":"dweeblet2@yahoo.com","FNAME":"Judy","LNAME":"Anderson",
       "GROUPINGS":[{"id":13233,"name":"Announcements","form_field":"checkboxes",
          "groups":[{"name":"Weekly Announcements","interested":true},
                    {"name":"Monthly Newsletter","interested":true},
                    {"name":"Auction Updates","interested":true}]},
            {"id":13237,"name":"Neighborhoods","form_field":"radio",
             "groups":[
               {"name":"Newbury Neighbors","interested":false},
               {"name":"Eastenders","interested":false},
               {"name":"10 Degrees Cooler","interested":false},
               {"name":"Wild West","interested":false},
               {"name":"Suddenly Seymour","interested":true},
               {"name":"Meadowood","interested":false},
               {"name":"No Neighborhood Emails Please","interested":false}]}]},
        "status":"subscribed","timestamp":"2014-11-01 20:53:32",
        "is_gmonkey":false,"lists":[],
        "geo":{"latitude":"34.2690000","longitude":"-118.7700000","gmtoff":"-8","dstoff":"-7","timezone":"America\/Los_Angeles","cc":"US","region":"CA"},
        "clients":{"name":"Thunderbird","icon_url":"http:\/\/us9.admin.mailchimp.com\/images\/email-client-icons\/thunderbird.png"},
        "static_segments":[],"notes":[]}
]}
    '''
    global verbose
    step=100
    start=0
    done=False
    list_id = get_list_id()
    domain = secrets.mailchimp_key.rsplit('-', 1)[1]
    while not done:
        api_fields = json.dumps({'apikey':secrets.mailchimp_key, 'id':list_id, 'opts':{'limit':step, 'start':start}})
        req = urllib2.Request('https://%s.api.mailchimp.com/2.0/lists/members.format' % (domain), api_fields)
        resp_data = get_response_data(req)
        resp_json = json.loads(resp_data)
        for mem in resp_json['data']:
            fname, lname, email = mem['merges']['FNAME'], mem['merges']['LNAME'], mem['email']
            print '%s,%s,%s' % (email, fname, lname)
        total = resp_json['total']
        #if verbose:
        #    print 'finished block %d-%d of %d' % (start*step, start*step+len(resp_json['data']), total)

        if len(resp_json['data']) == 0:
            print '0-length, data:'
            print resp_data
            break
        if total <= (start+1) * step:
            done=True
            break
        start += 1
        time.sleep(2)
    if done:
        print 'finished %d' % (total)

def fetch_list2():
    # https://<dc>.api.mailchimp.com/export/1.0/list/
    '''
    Here's what the response looks like if it has 2 entries:
 ["Email Address","First Name","Last Name","Announcements","Neighborhoods","MEMBER_RATING","OPTIN_TIME","OPTIN_IP","CONFIRM_TIME","CONFIRM_IP","LATITUDE","LONGITUDE","GMTOFF","DSTOFF","TIMEZONE","CC","REGION","LAST_CHANGED","LEID","EUID","NOTES"]
["ltc.insurance@yahoo.com","Carol","Knowles","Monthly Newsletter, Auction Updates","No Neighborhood Emails Please",4,"",null,"2014-11-01 20:53:34",null,"34.2085000","-118.8740000","-8","-7","America\/Los_Angeles","US","CA","2014-11-01 22:12:53","222555609","2cbd66e9c4",null]
["ussb@ussb.org","Unitarian Society of Santa Barbara","","Monthly Newsletter","No Neighborhood Emails Please",2,"",null,"2014-11-03 04:29:37","108.184.30.134",null,null,null,null,null,null,null,"2014-11-03 04:29:37","222637881","fbd96ba9d4",null]
    '''
    domain = secrets.mailchimp_key.rsplit('-', 1)[1]
    list_id = get_list_id()
    api_fields = urllib.urlencode({'apikey':secrets.mailchimp_key, 'id':list_id})
    req = urllib2.Request(('https://%s.api.mailchimp.com/export/1.0/list?' % (domain))+api_fields)
    resp_data = get_response_data(req)

    #print resp_data
    lines = [line for line in [lin.strip() for lin in resp_data.split('\n')] if line != '']
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

def fetch_list3():
    to_lines = []
    with open('last_goodchimp.txt') as f:
        for line in f:
            to_lines.append(tuple(line.strip().split(',')))
    return to_lines

def fetch_activity(emails):
    '''
    The response would look like this if it had just 2 entries (i added whitespace and removed most of campaign_data fields)

    For emails that have been known before, I see:
        - sometimes mysterious presence with only 'open's and 'sent's of email I thought was missing,
        - sometimes a bounce in front
        - sometimes a abuse in front
        - sometimes a unsub in front
    Plus if it's in "error" list with "error" field "Email_NotExists" means it's legitimately new in db, not in chimp list.

    {"success_count":3,"error_count":0,"errors":[],"data":[
        {"email":{"email":"erhauger@adelphia.net"},
        "activity":[
            {"action":"open",
                "timestamp":"2014-12-29 08:16:44","url":null,"type":null,"campaign_id":"05e76fdcf3","title":"NEWS AND NOTES 12\/28\/14",
                "campaign_data":{"id":"05e76fdcf3","web_id":766441,"list_id":"0ba3014544","folder_id":0,"template_id":100,"content_type":"template","content_edited_by":"Jake Holland","title":"NEWS AND NOTES 12\/28\/14","type":"regular","create_time":"2014-12-23 22:01:16","send_time":"2014-12-28 16:44:22","content_updated_time":"2014-12-28 16:44:21","status":"sent","from_name":"Conejo Valley UU Fellowship","from_email":"communications@cvuuf.org","subject":"CVUUF Weekly News and Notes 12\/28\/14","to_name":"*|FNAME|*","archive_url":"http:\/\/eepurl.com\/_43pn","archive_url_long":"http:\/\/us9.campaign-archive2.com\/?u=400639c1114daae501c63a104&id=05e76fdcf3",
                "many_more_things":"long, long and redundant message for campaign_data"},
                "bounce_type":null},
            {"action":"open",
                "timestamp":"2014-12-29 08:13:33","url":null,"type":null,"campaign_id":"62f672df07","title":"NEWS AND NOTES 12\/14\/14",
                "campaign_data":{"id":"62f672df07","web_id":736553,"list_id":"0ba3014544","folder_id":9689,"template_id":100,"content_type":"template","content_edited_by":"Jake Holland","title":"NEWS AND NOTES 12\/14\/14","type":"regular","create_time":"2014-12-12 20:16:25","send_time":"2014-12-14 16:49:33","content_updated_time":"2014-12-14 16:49:32","status":"sent","from_name":"Conejo Valley UU Fellowship","from_email":"communications@cvuuf.org","subject":"CVUUF Weekly News and Notes 12\/14\/14","to_name":"*|FNAME|*","archive_url":"http:\/\/eepurl.com\/-41jn","archive_url_long":"http:\/\/us9.campaign-archive1.com\/?u=400639c1114daae501c63a104&id=62f672df07",
                    "many_more_things":"long, long and redundant message for campaign_data"},
                "bounce_type":null},
            {"action":"open",
                "timestamp":"2014-12-29 08:09:18","url":null,"type":null,"campaign_id":"5f4398b5b3","title":"NEWS AND NOTES 12\/21\/14",
                "campaign_data":{"id":"5f4398b5b3","web_id":756929,"list_id":"0ba3014544","folder_id":0,"template_id":100,"content_type":"template","content_edited_by":"Jessica Lesko","title":"NEWS AND NOTES 12\/21\/14","type":"regular","create_time":"2014-12-19 19:14:11","send_time":"2014-12-19 23:00:00","content_updated_time":"2014-12-19 20:14:27","status":"sent","from_name":"Conejo Valley UU Fellowship","from_email":"communications@cvuuf.org","subject":"CVUUF Weekly News and Notes 12\/21\/14","to_name":"*|FNAME|*","archive_url":"http:\/\/eepurl.com\/_KGVf","archive_url_long":"http:\/\/us9.campaign-archive2.com\/?u=400639c1114daae501c63a104&id=5f4398b5b3",
                    "many_more_things":"long, long and redundant message for campaign_data"},
                "bounce_type":null}]},
    {"email":
        {"email":"hnaerheim@hotmail.com"},
        "activity":[
            {"action":"abuse",
                "timestamp":"2014-11-17 22:10:02","url":null,"type":null,"campaign_id":"6af7dcad27","title":"2014-11-14 News and Notes",
                "campaign_data":{"id":"6af7dcad27","web_id":663753,"list_id":"0ba3014544","folder_id":9689,"template_id":100,"content_type":"template","content_edited_by":"Jake Holland","title":"2014-11-14 News and Notes","type":"regular","create_time":"2014-11-15 06:02:18","send_time":"2014-11-15 20:37:18","content_updated_time":"2014-11-15 20:37:17","status":"sent","from_name":"UU Fellowship of Conejo Valley","from_email":"communications@cvuuf.org","subject":"Weekly News and Notes","to_name":"*|FNAME|*","archive_url":"http:\/\/eepurl.com\/8v1Yv","archive_url_long":"http:\/\/us9.campaign-archive1.com\/?u=400639c1114daae501c63a104&id=6af7dcad27",
                    "many_more_things":"long, long and redundant message for campaign_data"},
                "bounce_type":null},
            {"action":"open",
                "timestamp":"2014-11-17 22:02:32","url":null,"type":null,"campaign_id":"6af7dcad27","title":"2014-11-14 News and Notes",
                "campaign_data":{"id":"6af7dcad27","web_id":663753,"list_id":"0ba3014544","folder_id":9689,"template_id":100,"content_type":"template","content_edited_by":"Jake Holland","title":"2014-11-14 News and Notes","type":"regular","create_time":"2014-11-15 06:02:18","send_time":"2014-11-15 20:37:18","content_updated_time":"2014-11-15 20:37:17","status":"sent","from_name":"UU Fellowship of Conejo Valley","from_email":"communications@cvuuf.org","subject":"Weekly News and Notes","to_name":"*|FNAME|*","archive_url":"http:\/\/eepurl.com\/8v1Yv","archive_url_long":"http:\/\/us9.campaign-archive1.com\/?u=400639c1114daae501c63a104&id=6af7dcad27",
                    "many_more_things":"long, long and redundant message for campaign_data"},
                "bounce_type":null},
            {"action":"open",
                "timestamp":"2014-11-17 01:36:44","url":null,"type":null,"campaign_id":"c2ae4995d5","title":"Lora Resignation Letter",
                "campaign_data":{"id":"c2ae4995d5","web_id":660645,"list_id":"0ba3014544","folder_id":9693,"template_id":105,"content_type":"template","content_edited_by":"Jake Holland","title":"Lora Resignation Letter","type":"regular","create_time":"2014-11-13 23:12:42","send_time":"2014-11-13 23:44:57","content_updated_time":"2014-11-13 23:44:55","status":"sent","from_name":"Reverend Lora","from_email":"minister@cvuuf.org","subject":"Personnel Changes","to_name":"*|FNAME|*","archive_url":"http:\/\/eepurl.com\/8oIg1","archive_url_long":"http:\/\/us9.campaign-archive1.com\/?u=400639c1114daae501c63a104&id=c2ae4995d5",
                    "many_more_things":"long, long and redundant message for campaign_data"},
                "bounce_type":null}]},
    {"email":
        {"email":"goorge720@yahoo.com"},
        "activity":[
            {"action":"bounce",
            "timestamp":"2014-11-12 17:43:38","url":null,"type":"hard","campaign_id":"1a1a864ca5","title":"Auction 2014, Nov 11",
            "campaign_data":{"id":"1a1a864ca5","web_id":628989,"list_id":"0ba3014544","folder_id":9697,"template_id":99,"content_type":"template","content_edited_by":"Jake Holland","title":"Auction 2014, Nov 11","type":"regular","create_time":"2014-11-02 00:50:45","send_time":"2014-11-12 17:32:41","content_updated_time":"2014-11-12 17:32:37","status":"sent","from_name":"Conejo Valley UU Fellowship","from_email":"auction@cvuuf.org","subject":"Auction Saturday at 5!","to_name":"*|FNAME|* *|LNAME|*","archive_url":"http:\/\/eepurl.com\/7i0bX","archive_url_long":"http:\/\/us9.campaign-archive1.com\/?u=400639c1114daae501c63a104&id=1a1a864ca5",
                    "many_more_things":"long, long and redundant message for campaign_data"},
            "bounce_type":"hard"},
            {"action":"sent",
                "timestamp":"2014-11-12 17:32:41","url":null,"type":"regular","campaign_id":"1a1a864ca5","title":"Auction 2014, Nov 11","parent_campaign":null,
                "campaign_data":{"id":"1a1a864ca5","web_id":628989,"list_id":"0ba3014544","folder_id":9697,"template_id":99,"content_type":"template","content_edited_by":"Jake Holland","title":"Auction 2014, Nov 11","type":"regular","create_time":"2014-11-02 00:50:45","send_time":"2014-11-12 17:32:41","content_updated_time":"2014-11-12 17:32:37","status":"sent","from_name":"Conejo Valley UU Fellowship","from_email":"auction@cvuuf.org","subject":"Auction Saturday at 5!","to_name":"*|FNAME|* *|LNAME|*","archive_url":"http:\/\/eepurl.com\/7i0bX","archive_url_long":"http:\/\/us9.campaign-archive1.com\/?u=400639c1114daae501c63a104&id=1a1a864ca5",
                    "many_more_things":"long, long and redundant message for campaign_data"},
            "bounce_type":"regular"}]}
]}
    '''
    domain = secrets.mailchimp_key.rsplit('-', 1)[1]
    list_id = get_list_id()
    step = 50
    results = {}
    for start, stop in zip(range(0, len(emails), step), range(step, len(emails), step) + [len(emails)]):
        use_em = [{'email':em} for em in emails[start:stop]]
        # print 'activity req(%d-%d): %s'%(start, stop, emails[start:stop])
        api_fields = json.dumps({'apikey':secrets.mailchimp_key, 'id':list_id, 'emails':use_em})
        req = urllib2.Request('https://%s.api.mailchimp.com/2.0/lists/member-activity.format' % (domain), api_fields)
        resp_data = get_response_data(req)
        resp_json = json.loads(resp_data)
        if 'data' in resp_json:
            for entry in resp_json['data']:
                try:
                    email = entry['email']['email']
                    if email in results:
                        print 'warning: %s appears multiple times in fetch_activity (prev: %s)' % (email, results[email])
                    activities = {}
                    for activity in entry['activity']:
                        action = activity['action']
                        if action == 'unsub' or action == 'mandrill_unsub':
                            results[email] = 'Unsub'
                            break
                        elif action == 'abuse' or action == 'mandrill_spam' or action == 'mandrill_reject':
                            results[email] = 'Objected'
                            break
                        elif action == 'bounce':
                            if activity['bounce_type'] == 'hard':
                                results[email] = 'Hard_bounce'
                            else:
                                results[email] = 'Soft_bounce'
                            break
                        elif action == 'mandrill_hard_bounce':
                            results[email] = 'Hard_bounce'
                            break
                        elif action == 'mandrill_soft_bounce':
                            results[email] = 'Soft_bounce'
                            break
                        else:
                            if action not in activities:
                                activities[action] = 1
                            else:
                                activities[action] += 1
                    if email not in results:
                        results[email] = 'summary_' + '_'.join(['%s(%d)' % (action, activities[action]) for action in sorted(activities.keys())])
                except:
                    print 'got exception "%s" while handling:' % (sys.exc_info()[0])
                    if 'activity' in entry:
                        if 'action' in entry['activity']:
                            for action in entry['activity']['action']:
                                if 'campaign_data' in action:
                                    del action['campaign_data']
                    print json.dumps(entry, indent=3)
                    continue

        if 'errors' in resp_json:
            for entry in resp_json['errors']:
                try:
                    email = entry['email']['email']
                    if email in results:
                        print 'warning: %s appears multiple times in fetch_activity (prev: %s)' % (email, results[email])
                    err = entry['error']
                    code = entry['code']
                    if code == 232:  # 'Email_NotExists':
                        results[email] = 'to_welcome'
                    else:
                        results[email] = 'other_error:code(%s):%s' % (code, err)
                except:
                    print 'got exception "%s" while handling:' % (sys.exc_info()[0])
                    print json.dumps(entry, indent=3)
                    continue

                
        if stop != len(emails):
            time.sleep(2)

    for email in emails:
        if email not in results:
            print 'unmentioned email in activity queries: %s' % (email)

    '''returns {email:val} where val is in [Objected, Hard_bounce, Soft_bounce, Unsub, summary_*, to_welcome]'''
    return results

if __name__=="__main__":
    print "you probably mean 'python cvuuf.py'"
    # lines=fetch_list2()
    # print '%d lines in last_chimp.txt' % (len(lines))

