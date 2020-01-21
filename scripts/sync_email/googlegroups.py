
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

class GoogleUser(object):
  def __init__(self, email):
    self.email = email.lower()
  def __str__(self):
    return self.email

class BadResponse(Exception):
  def __init__(self, val):
    self.val = val
  def __str__(self):
    return repr(self.val)

def emailgroup_members(groupEmail):
  creds = service_account.Credentials.from_service_account_file(load_json_key_file(), scopes=['https://www.googleapis.com/auth/admin.directory.group.member.readonly']).with_subject('communicationsdirector@cvuuf.org')
  service = build('admin', 'directory_v1', credentials=creds)

  nextPageToken = None
  req_count = 0
  emails = []
  while True:
    if nextPageToken:
      results = service.members().list(groupKey=groupEmail, pageToken=nextPageToken, maxResults=200).execute()
    else:
      results = service.members().list(groupKey=groupEmail, maxResults=200).execute()
    members = results.get('members', [])

    req_count += 1
    print('  got {0} members in req {1}'.format(len(members), req_count))
    for member in members:
      email = member.get('email')
      if email:
        emails.append(GoogleUser(email))
    nextPageToken=results.get('nextPageToken')
    if not nextPageToken:
        break

  return emails

if __name__=="__main__":
  import sys
  if len(sys.argv) < 2:
    group = 'neighborhood@cvuuf.org'
  else:
    group = sys.argv[1]
  print('checking {0}'.format(group))
  member_emails = emailgroup_members(group)
  print('found {0} members'.format(len(member_emails)))
  print('  ' + '\n  '.join(str(m) for m in member_emails))

