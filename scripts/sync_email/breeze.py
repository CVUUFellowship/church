
import sys
import os
import urllib
import urllib2
import json
import traceback
import httplib
import time
import math

# import secret from outside my code path.
secrets_path = os.path.join(os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__)))), 'secrets')
sys.path.append(secrets_path)

import breezeapi_secrets

# https://www.breezechms.com/api
# curl -H "Content-type: application/json" -H "Api-key: $api_key" 'https://$domain/api/tags/list_folders/'
verbose = False
domain = breezeapi_secrets.domain()
api_key = breezeapi_secrets.api_key()
print(domain)

class FilterSet(object):
    def __init__(self, nbrhood, status):
        hood_mails = {
            '10 Degrees Cooler':'tendegreescooler',
            'Suddenly Seymour':'suddenlyseymour',
            'Eastenders':'eastenders',
            'Newbury Neighbors':'newburyneighbors',
            'Wild West':'wildwest',
            'Meadowood':'meadowood',
            'Unassigned':'unassigned',
        }
        self.nbrhood = nbrhood
        self.group = hood_mails.get(nbrhood)
        self.status = status

    def __str__(self):
        return '%s-%s' % (self.nbrhood, self.status)

class BreezePerson(object):
    '''breeze_people() returns a list of these'''
    def __init__(self, email, fname, lname, lat, lon, filter_set):
        self.email = email.lower()
        self.fname = fname
        self.lname = lname
        #self.expected_neighborhood = neighborhood
        self.lat = lat
        self.lon = lon
        self.filter_set = filter_set
        self.recommended_group = None

    @property
    def hood_group(self):
        return self.filter_set.group

    @property
    def status(self):
        return self.filter_set.status

    @property
    def hood_name(self):
        return self.filter_set.nbrhood

    def __str__(self):
        return self.email

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

debug_idx = 0
def get_filter_sets():
    filter_sets = []
    #filt_str = 'filter_json=%7B%22tag_does_not_contain%22%3A+%22n_1760789-n_1621083%22%7D'
    if True:
        req = urllib2.Request('https://%s/api/tags/list_tags/' % domain,
                headers={'Content-type':'application/json','Api-key':api_key})
        resp_data = get_response_data(req)
        tags = json.loads(resp_data)
        excludes = []
        for tag in tags:
            if tag.get('name') == 'Archived':
                tag_id = tag.get('id')
                if tag_id:
                    excludes.append('n_' + tag_id)

            if tag.get('name') == 'Not Active':
                tag_id = tag.get('id')
                if tag_id:
                    excludes.append('n_' + tag_id)

        filt = {}
        if excludes:
            exclude_str = '-'.join(excludes)
            filt['tag_does_not_contain'] = exclude_str

        req = urllib2.Request('https://%s/api/profile/' % domain,
                headers={'Content-type':'application/json','Api-key':api_key})
        resp_data = get_response_data(req)
        custom_top_fields = json.loads(resp_data)
        global debug_idx
        if debug_idx == 0: print(json.dumps(custom_top_fields, indent=4, sort_keys=True))
        debug_idx += 1
        #print(json.dumps(custom_top_fields, indent=4, sort_keys=True))
        neighbor_ids = ['Unassigned']
        neighbor_names = ['Unassigned']
        status_ids = ['Unassigned']
        status_names = ['Unassigned']
        nbr_id = None
        status_id = None
        #print('top fields: %s' % (len(custom_top_fields)))
        for top_field in custom_top_fields:
            field_name = top_field.get('name')
            #print('field_name=%s' % field_name)
            if field_name == 'Groups':
                group_fields = top_field.get('fields', [])
                for group_field in group_fields:
                    if group_field.get('name') == 'Neighborhood Networks':
                        if nbr_id:
                            raise Exception('error: Neighborhood Networks field appeared twice, the model seems out of sync with breeze.py\n' + json.dumps(custom_top_fields, indent=4, sort_keys=True))
                        nbr_id = group_field.get('field_id')
                        nbr_options = group_field.get('options')
                        for option in nbr_options:
                            neighbor_ids.append(option.get('option_id'))
                            neighbor_names.append(option.get('name'))
            elif field_name == 'Main':
                main_fields = top_field.get('fields', [])
                for main_field in main_fields:
                    if main_field.get('name') == 'Status':
                        if status_id:
                            raise Exception('error: Main/Status field appeared twice, the model seems out of sync with breeze.py\n' + json.dumps(custom_top_fields, indent=4, sort_keys=True))
                        status_id = main_field.get('field_id')
                        status_options = main_field.get('options', [])
                        for option in status_options:
                            status_name = option.get('name')
                            if status_name in set(['Friend', 'NewFriend', 'Member', 'Added by Finance']):
                                status_option = option.get('option_id')
                                status_ids.append(status_option)
                                status_names.append(status_name)

        #gen_filt_str = urllib.urlencode({'filter_json':json.dumps(filt, sort_keys=True)})
        #if gen_filt_str != filt_str:
        #    print('broken filt_str:\n  old: %s\n  new: %s' % (filt_str, gen_filt_str))
        #    filt_str = gen_filt_str

        if not nbr_id:
            raise Exception('error: failed to find neighborhoods in get_filter_sets')
        if not status_id:
            raise Exception('error: failed to find statuses in get_filter_sets')
        for hood_name, hood_id in zip(neighbor_names, neighbor_ids):
            filt[nbr_id] = hood_id
            for status, status_opt in zip(status_names, status_ids):
                filt[status_id] = status_opt
                gen_filt_str = urllib.urlencode({'filter_json':json.dumps(filt, sort_keys=True)})
                #print('%s, %s:\n%s' % (hood_name, status, json.dumps(filt, sort_keys=True, indent=4)))
                filter_sets.append((FilterSet(hood_name, status), gen_filt_str))

    return filter_sets

class Centroid(object):
    def __init__(self):
        self.lat_sum = 0.
        self.lon_sum = 0.
        self.count = 0

    def add(self, lat, lon):
        if lat == 0 and lon == 0:
            return
        self.lat_sum += lat
        self.lon_sum += lon
        self.count += 1

def breeze_people():

    people = []
    filt_sets = get_filter_sets()
    for filt_set, filt_str in filt_sets:
        # with a filter, it seems to ignore the limit field--jake 2019-05-23
        req = urllib2.Request('https://%s/api/people/?details=1&%s' % (domain, filt_str),
                headers={'Content-type':'application/json','Api-key':api_key})
        resp_data = get_response_data(req)
        resp = json.loads(resp_data)
        
        #print('%s: %s' % (filt_set, len(resp)))
        print('%d, first: %s' % (len(resp), json.dumps(resp[0], indent=4) if resp else ''))
        #if resp: print('%s' % json.dumps(resp[0].get('details', {}), indent=4))
        for person in resp:
            details1 = person.get('details', {})
            fname = details1.get('first_name')
            lname = details1.get('last_name')
            det2 = details1.get('details', {})
            email = det2.get('email_primary', '')
            lat = det2.get('latitude')
            lon = det2.get('longitude')
            if lat and lon:
                lat = float(lat)
                lon = float(lon)
            else:
                lat, lon = 0,0
            people.append(BreezePerson(email, fname, lname, lat, lon, filt_set))

    centroids = {}
    for person in people:
        if person.hood_group not in centroids:
            centroids[person.hood_group] = Centroid()
        centroids[person.hood_group].add(person.lat, person.lon)

    for g, c in centroids.items():
        if c.count == 0:
            continue
        c.lat_cent = c.lat_sum / c.count
        c.lon_cent = c.lon_sum / c.count

    for person in people:
        person.distance_hood = 'unknown'
        if person.hood_group != 'Unassigned':
            person.recommended_hood = person.hood_group
        else:
            person.recommended_hood = 'unknown'
        if person.lat == 0 and person.lon == 0:
            continue
        best_group = 'unfound'
        best_dist = 10e10
        for g, c in centroids.items():
            if c.count == 0:
                continue
            dist = math.sqrt((person.lat - c.lat_cent)**2 + (person.lon - c.lon_cent)**2)
            if dist < best_dist:
                best_group = g
                best_dist = dist

        if person.recommended_hood == 'unknown' or person.recommended_hood == 'unassigned':
            person.recommended_hood = best_group
        person.distance_hood = best_group

    return people

if __name__=="__main__":
    people = breeze_people()
    mismatch = {}
    statuses = {}
    missing_email = 0
    for person in people:
        print('%s,%s,%s,%s,%s,%s,%s' % (person.fname, person.lname, person.email, person.status, person.hood_group, person.hood_name, person.recommended_hood))
        if person.recommended_hood != person.hood_group:
            key = '->'.join([person.hood_group, person.recommended_hood])
            mismatch[key] = mismatch.get(key, 0) + 1
        statuses[person.status] = statuses.get(person.status, 0) + 1
        if person.email.strip() == '':
            missing_email += 1
    print('%s people, mismatched neighborhoods: %s' % (len(people), json.dumps(mismatch, indent=4)))
    for status, count in statuses.items():
        print('%s: %d' % (status, count))
    print('missing emails: %d' % missing_email)

