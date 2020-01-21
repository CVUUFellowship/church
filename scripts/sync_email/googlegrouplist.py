
# Useful:
# https://developers.google.com/oauthplayground
# 
# from apiclient.discovery import build
# build stuff is confusing, not clear how you get the service name or what
# to pass to the underlying objects.
# from the api you find when searching to do what you want. e.g.
# https://developers.google.com/admin-sdk/directory/v1/reference/members/list
# is the page you find when searching how to use google api to find the list
# of members in a group, which tells you you will use
# GET https://www.googleapis.com/admin/directory/v1/groups/groupKey/members,
# and that you need scope
# https://www.googleapis.com/auth/admin.directory.group.member.readonly
# (or some others). Not clear how you're supposed to know a service name
# from that. I think it's "admindirectory", and then "groups" is the service.
# so presumably you do:
# service = build("admindirectory", "v1", http=http)
# (actually, no: serviceName='admin', version='directory_v1'--found in example online)
# mygroup = service.groups().get(groupUniqueId='groupemail@domain.org').execute()
# members = mygroup.members().execute()
# This seems way more confusing than just sending the message.
#
# from https://google-api-python-client.googlecode.com/hg/docs/epy/apiclient.discovery-module.html#DISCOVERY_URI, it looks like it's fetching from
# 'https://www.googleapis.com/discovery/v1/apis/' '{api}/{apiVersion}/rest'
# the first of those suggests:
# https://www.googleapis.com/discovery/v1/apis/admin/directory_v1/rest

import os
import sys

from google.oauth2 import service_account
from google.auth.transport.requests import Request
from googleapiclient.discovery import build

# import secret from outside my code path.
#secrets_path = os.path.join(os.path.dirname(os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))), 'secrets')
#sys.path.append(secrets_path)
from googleapi_secrets import load_json_key_file

class GoogleGroup(object):
  def __init__(self, group):
    self.group_email = group['email'].lower()
    '''
  {u'nonEditableAliases': [u'office@cvuuf.org.test-google-a.com'], u'kind': u'admin#directory#group', u'name': u'office', u'adminCreated': True, u'directMembersCount': u'2', u'email': u'office@cvuuf.org', u'etag': u'"iwpzoDgSq9BJw-XzORg0bILYPVc/PP7aArdSiIYRyU9ENxyUVCh_c2c"', u'id': u'03bj1y381hipwcc', u'description': u''}
'''

  def __str__(self):
    return str(self.group_email)

class BadResponse(Exception):
  def __init__(self, val):
    self.val = val
  def __str__(self):
    return repr(self.val)

def domain_groups(domain):
  creds = service_account.Credentials.from_service_account_file(load_json_key_file(), scopes=['https://www.googleapis.com/auth/admin.directory.group.readonly']).with_subject('communicationsdirector@cvuuf.org')
  service = build('admin', 'directory_v1', credentials=creds)

  nextPageToken = None
  req_count = 0
  group_list = []
  while True:
    if nextPageToken:
      results = service.groups().list(domain=domain, pageToken=nextPageToken, maxResults=200).execute()
    else:
      results = service.groups().list(domain=domain, maxResults=200).execute()
    req_count += 1
    groups = results.get('groups', [])
    print('req {0} got {1} groups'.format(req_count, len(groups)))
    for group in groups:
      group_list.append(GoogleGroup(group))
    nextPageToken=results.get('nextPageToken')
    if not nextPageToken:
        break

  return group_list

if __name__=="__main__":
  import sys
  if len(sys.argv) < 2:
    domain = 'cvuuf.org'
  else:
    domain = sys.argv[1]
  print('checking {0}'.format(domain))
  groups = domain_groups(domain)
  print('found {0} groups'.format(len(groups)))
  print('  ' + '\n  '.join(str(m) for m in groups))

