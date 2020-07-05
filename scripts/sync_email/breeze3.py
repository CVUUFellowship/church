
import sys
import os
from urllib.request import urlopen, Request
from urllib.parse import urlencode, urlparse
from urllib.error import HTTPError, URLError
from http.client import HTTPException
import json
import traceback
import time
import math
from pathlib import Path
import collections
import datetime
import ssl
import smtplib
import subprocess

# import secret from outside my code path.
secrets_path = os.path.join(os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__)))), 'secrets')
sys.path.append(secrets_path)

import breezeapi_secrets
import email_secrets

# https://www.breezechms.com/api
# curl -H "Content-type: application/json" -H "Api-key: $api_key" 'https://$domain/api/tags/list_folders/'
verbose = False
domain = breezeapi_secrets.domain()
api_key = breezeapi_secrets.api_key()

debug_dir = None
debug_req_idx = 0

class FilterSet(object):
    hood_mails = {
        '10 Degrees Cooler':'tendegreescooler',
        'Suddenly Seymour':'suddenlyseymour',
        'Eastenders':'eastenders',
        'Newbury Neighbors':'newburyneighbors',
        'Wild West':'wildwest',
        'Meadowood':'meadowood',
        'Unassigned':'unassigned',
    }
    def __init__(self, nbrhood, status):
        self.nbrhood = nbrhood
        self.group = FilterSet.hood_mails.get(nbrhood, 'unknown')
        self.status = status

    def __str__(self):
        return '%s-%s' % (self.nbrhood, self.status)

class BreezePerson(object):
    '''breeze_people() returns a list of these'''
    def __init__(self, email, fname, lname, lat, lon, filter_set, street, city, state, zipcode):
        self.email = email.lower()
        self.fname = fname
        self.lname = lname
        #self.expected_neighborhood = neighborhood
        self.lat = lat
        self.lon = lon
        self.filter_set = filter_set
        self.street = street
        self.city = city
        self.state = state
        self.zipcode = zipcode
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
    global debug_dir, debug_req_idx
    debug_path = None
    tail = None
    if debug_dir:
        debug_req_idx += 1
        parts = urlparse(req.full_url)
        subpaths = parts.path.split('/')
        tail = subpaths[-1]
        if len(subpaths) > 1 and tail == '':
            tail = subpaths[-2]
        debug_path = debug_dir.joinpath('%d.%s.0.txt' % (debug_req_idx, tail))
        with debug_path.open('w') as f:
            print(str(req), file=f)
    try:
        resp = urlopen(req)
    except HTTPError as e:
        print('HTTPError: %s (code=%s)' % (e, e.code))
        # print('HTTPError: %s (code=%s, reason=%s)' % (e, e.code, e.reason))
        print('  contents:\n%s' % (e.read()))
        raise
    except URLError as e:
        print('URLError: %s (reason=%s)' % (e, e.reason))
        raise
    except HTTPException as e:
        print('HTTPException: %s' % (e))
        raise
    except Exception as e:
        print("Unexpected error:", sys.exc_info()[0])
        print(traceback.format_exc())
        raise
    else:
        resp_data = resp.read()
        if debug_dir:
            orig_path = debug_path
            debug_path = debug_dir.joinpath('%d.%s.%s.txt' % (debug_req_idx, tail, resp.getcode()))
            orig_path.rename(debug_path)
            with debug_path.open('a') as f:
                print('----', file=f)
                print('got %s from %s' % (resp.getcode(), resp.geturl()), file=f)
                print('headers:', file=f)
                resp_info = resp.info()
                for header,val in resp_info.items():
                    print('   %s: %s' % (header, val), file=f)
                print('----', file=f)
                try:
                    dump_data=json.dumps(json.loads(resp_data), indent=4)
                    if dump_data != resp_data:
                        print('(prettified)', file=f)
                except Exception as e:
                    dump_data=resp_data
                print(dump_data, file=f)
        return resp_data


breeze_explanation_ = '''
The Breeze API as of July 2020 has a few awkward characteristics:
https://app.breezechms.com/api

Most notably, there's no way to get a list of tags for a person.
Ideally they would appear as a list in details when requesting a
set of people, perhaps if we set tags=1 like we set details=1, but
no such luck.

Instead, the only way we have to associate tags with people that
I could find in the API is to make a filtered request for a set of
tags (or excluding a set of tags), which gives us a list of people
that match the filter.

This means I'm doing 112 queries for people, which comes from
multiplying the 7 different neighborhood states (each neighborhood
plus "no neighborhood set") by the 16 different statuses.  These
are generated by first querying the list of tags, and matching
their names with the id numbers that get used in the filter.

Much preferable would be to fetch all the unarchived people in
one request (or a series of paginated requests) and to have their
tags inside their details.  Writing to breeze support about that...
-Jake
'''

def get_filter_sets():
    filter_sets = []
    #filt_str = 'filter_json=%7B%22tag_does_not_contain%22%3A+%22n_1760789-n_1621083%22%7D'
    if True:
        req = Request('https://%s/api/tags/list_tags/' % domain,
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

        req = Request('https://%s/api/profile/' % domain,
                headers={'Content-type':'application/json','Api-key':api_key})
        resp_data = get_response_data(req)
        custom_top_fields = json.loads(resp_data)
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
                            if True or status_name in set(['Friend', 'NewFriend', 'Member', 'Added by Finance']):
                                status_option = option.get('option_id')
                                status_ids.append(status_option)
                                status_names.append(status_name)

        #gen_filt_str = urlencode({'filter_json':json.dumps(filt, sort_keys=True)})
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
                gen_filt_str = urlencode({'filter_json':json.dumps(filt, sort_keys=True)})
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
        req = Request('https://%s/api/people/?details=1&%s' % (domain, filt_str),
                headers={'Content-type':'application/json','Api-key':api_key})
        resp_data = get_response_data(req)
        resp = json.loads(resp_data)
        
        #print('%d, first: %s' % (len(resp), json.dumps(resp[0], indent=4) if resp else ''))
        #if resp: print('%s' % json.dumps(resp[0].get('details', {}), indent=4))
        for person in resp:
            details1 = person.get('details', {})
            fname = details1.get('first_name')
            lname = details1.get('last_name')
            det2 = details1.get('details', {})
            email = det2.get('email_primary', '')
            street = det2.get('street_address', '')
            city = det2.get('city','')
            state = det2.get('state','')
            zipcode = det2.get('zip','')
            lat = det2.get('latitude')
            lon = det2.get('longitude')
            if lat and lon:
                lat = float(lat)
                lon = float(lon)
            else:
                lat, lon = 0,0
            people.append(BreezePerson(email, fname, lname, lat, lon, filt_set, street, city, state, zipcode))

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

def dump_breeze_csv(outfile):
    with open(outfile, 'a'):
      pass
    debug_top = Path('debug')
    if debug_top.is_dir():
        debug_path_idx = 5
        dead_dir = debug_top.joinpath('%d' % debug_path_idx)
        if dead_dir.exists():
            if dead_dir.is_dir():
                from shutil import rmtree
                rmtree(dead_dir)
            else:
                dead_dir.unlink()
        while debug_path_idx > 0:
            debug_path_idx -= 1
            debug_dir = debug_top.joinpath('%d' % debug_path_idx)
            if debug_dir.exists():
                debug_dir.rename(dead_dir)
            dead_dir = debug_dir
        debug_dir.mkdir()

    people = breeze_people()
    mismatch = {}
    statuses = {}
    missing_email_statuses = {}

    with open(outfile, 'w') as outf:
      headers = [
        'first',
        'last',
        'email',
        'status',
        'recommended',
        'neighborhood',
        'address',
      ]
      print(','.join(headers), file=outf)
      for person in people:
          recommended = ''
          if person.recommended_hood != person.hood_group:
              key = '->'.join([person.hood_group, person.recommended_hood])
              mismatch[key] = mismatch.get(key, 0) + 1
              recommended = person.recommended_hood

          vals = [
            person.fname,
            person.lname,
            person.email,
            person.status,
            recommended,
            person.hood_group,
            '%s' % ('/'.join([v.strip() for v in [person.street, person.city, person.state, person.zipcode] if v])),
          ]
          vals = [v.replace(',','_').strip() for v in vals]
          print(','.join(vals), file=outf)
          statuses[person.status] = statuses.get(person.status, 0) + 1
          if person.email.strip() == '':
              missing_email_statuses[person.status] = missing_email_statuses.get(person.status, 0) + 1

    if False:
      print('%s people, mismatched neighborhoods: %s' % (len(people), json.dumps(mismatch, indent=4)))
      for status, count in statuses.items():
          if status in missing_email_statuses:
            print('%s: %d (%d missing)' % (status, count, missing_email_statuses[status]))
          else:
            print('%s: %d' % (status, count))

def do_import(fname, tname, outf):
  lines = None
  with open(fname, encoding='utf-8', errors='replace') as f:
    lines = f.readlines()

  # first line should be field names, and should include:
  # first,last,email,address (others are ignored)
  
  head_line = lines[0]
  fields = head_line.strip().split(',')

  ImportType = collections.namedtuple(tname, fields)

  by_name = {}
  for line in lines[1:]:
    vals = ImportType(*line.strip().split(','))
    name = (vals.last, vals.first)
    if name in by_name:
      print('skipping line in %s: %s duplicated' % (fname, name), file=outf)
    by_name[name] = vals

  return by_name


def breeze_diffs(in1, in2, outfname):
  with open(outfname, 'w') as outf:
    vold = do_import(in1, 'ImportOld', outf)
    vnew = do_import(in2, 'ImportNew', outf)

    old_names = set(vold.keys())
    new_names = set(vnew.keys())

    both = old_names.intersection(new_names)
    added = new_names - old_names
    removed = old_names - new_names

    changed = set()

    for name in both:
      od = vold[name]
      nw = vnew[name]

      if od.address != nw.address or od.email != nw.email:
        changed.update((name,))

    updates = list(sorted(added.union(removed).union(changed)))

    print('%d updates:' % len(updates), file=outf)
    email_changed = False
    address_changed = False
    for name in updates:
      if name in added:
        v = vnew[name]
        print('added %s %s: %s,%s' % (v.first,v.last,v.email,v.address), file=outf)
      elif name in removed:
        v = vold[name]
        print('removed %s %s: %s,%s' % (v.first,v.last,v.email,v.address), file=outf)
      elif name in changed:
        od = vold[name]
        nw = vnew[name]
        fields = [nw.first,nw.last]
        if od.email != nw.email:
          print('changed email %s %s: from(%s) to(%s)' % (nw.first, nw.last, od.email, nw.email), file=outf)
          email_changed = True
        if od.address != nw.address:
          print('changed address %s %s: from(%s) to(%s)' % (nw.first, nw.last, od.address, nw.address), file=outf)
          address_changed = True
      else:
        print('error: %s changed but not in added, removed, or changed list', file=outf)

    if email_changed:
      print('', file=outf)
      print('Please check and update changed emails in our groups and let them know you did.', file=outf)
      print('https://members.cvuuf.org/private/group', file=outf)

    if address_changed or len(added):
      print('', file=outf)
      print('Please check if a new neighborhood is appropriate for added or changed addresses, and if so update the group membership:', file=outf)
      for hood in FilterSet.hood_mails.values():
          print('- https://groups.google.com/a/cvuuf.org/g/%s' % hood, file=outf)
      print('Mapping:', file=outf)
      print('- https://docs.google.com/spreadsheets/d/1Ni2PHK84EAaWqjIWmwAP817n_cMmb9ZL/', file=outf)
      print('4-digit zip code lookup:', file=outf)
      print('- https://tools.usps.com/zip-code-lookup.htm?byaddress', file=outf)

  return len(updates)

def send_notice(message):
  login_address = email_secrets.smtp_name()
  login_pw = email_secrets.smtp_pw()
  send_address = email_secrets.from_address()
  receive_address = email_secrets.breeze_update_receiver()
  port = 465  # For SSL

  header = '''Subject: Breeze Automated Scan (%s)

'''  % datetime.datetime.now()
  message = header + message.encode('ascii', errors='replace').decode('ascii')

  # Create a secure SSL context
  context = ssl.create_default_context()
  with smtplib.SMTP_SSL("smtp.gmail.com", port, context=context) as server:
      server.login(login_address, login_pw)
      server.sendmail(send_address, receive_address, message)

if __name__=="__main__":
    dir_path = sys.argv[1]
    prior_csv = os.path.join(dir_path, sys.argv[2])
    new_csv = os.path.join(dir_path, sys.argv[3])
    report_out = os.path.join(dir_path, sys.argv[4])
    dump_breeze_csv(new_csv)
    updates = breeze_diffs(prior_csv, new_csv, report_out)
    mod_time = datetime.datetime.fromtimestamp(os.stat(prior_csv).st_mtime)
    if datetime.datetime.now() - datetime.timedelta(days=6, hours=12) > mod_time or updates > 0:
      with open(report_out) as reportf:
        out_message = reportf.read()

      send_notice(out_message)
      os.rename(new_csv, prior_csv)

      with open(os.path.join(dir_path, 'git.out'), 'w') as git_std:
        subprocess.call(['/usr/bin/git', '-C', dir_path, 'add', prior_csv], stdout=git_std)
        subprocess.call(['/usr/bin/git', '-C', dir_path, 'commit', '-m', 'Automated update %s' % datetime.datetime.now()], stdout=git_std)

