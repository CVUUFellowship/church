
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

import json
import httplib2
import urllib

# easy_install --user --upgrade oauth2client
from oauth2client.client import flow_from_clientsecrets, SignedJwtAssertionCredentials
from oauth2client.file import Storage
from oauth2client import tools

# import secret from outside my code path.
#secrets_path = os.path.join(os.path.dirname(os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))), 'secrets')
#sys.path.append(secrets_path)

# if you don't have googleapi_secrets, you can still use auth
from_client=True
try:
  from googleapi_secrets import key_data, service_account_name, pk_password, sub_account_name
  from_client=False
except ImportError:
  pass

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
  storage = Storage('listmemberperms.dat')
  credentials = storage.get()
  if credentials is None or credentials.invalid:
    # print 'invalid credentials'
    if from_client:
      CLIENT_SECRETS = 'client_secrets.json'
      MISSING_CLIENT_SECRETS_MESSAGE = """
WARNING: Please configure OAuth 2.0
To make this sample run you will need to populate the client_secrets.json file
found at:
   %s
with information from the APIs Console <https://code.google.com/apis/console>.
""" % os.path.join(os.path.dirname(__file__), CLIENT_SECRETS)

      # Set up a Flow object to be used if we need to authenticate.
      flow = flow_from_clientsecrets(CLIENT_SECRETS,
          scope='https://www.googleapis.com/auth/admin.directory.group.member.readonly',
          message=MISSING_CLIENT_SECRETS_MESSAGE)

      # Save the credentials in storage to be used in subsequent runs.
      flags = tools.argparser.parse_args(args=['--noauth_local_webserver'])
      credentials = tools.run_flow(flow, storage, flags)
    else:
      # wtf. Finally found you need to use 'sub' for admin/directory, the
      # poorly documented api. Found in this sample.
      # without sub you get a 403. smh
      # https://github.com/alfasin/Google-Admin-Directory-API/blob/master/group_members_using_sdk.py
      credentials = SignedJwtAssertionCredentials(
          service_account_name=service_account_name(),
          private_key=key_data(),
          scope="https://www.googleapis.com/auth/admin.directory.group.member.readonly",
          private_key_password=pk_password(),
          sub=sub_account_name())
      storage.put(credentials)

  # Create an httplib2.Http object to handle our HTTP requests and authorize it
  # with our good Credentials.
  http = httplib2.Http()
  http = credentials.authorize(http)
  params = {'maxResults':200}
  emails = []
  while True:
    resp, content = http.request('https://www.googleapis.com/admin/directory/v1/groups/{0}/members?{1}'.format(urllib.quote(groupEmail), urllib.urlencode(params)))
    data = json.loads(content)
    members = data.get('members')
    if members is None:
      print 'no members entry found. Response:'
      print resp
      print content
      raise BadResponse('no members found')

    # print 'got {0} members'.format(len(members))
    for member in members:
      email = member.get('email')
      if email:
        emails.append(GoogleUser(email))
    nextPageToken = data.get('nextPageToken')
    if not nextPageToken:
      break
    params['pageToken'] = nextPageToken

  return emails

if __name__=="__main__":
  import sys
  if len(sys.argv) < 2:
    group = 'neighborhood@cvuuf.org'
  else:
    group = sys.argv[1]
  print 'checking {0}'.format(group)
  member_emails = emailgroup_members(group)
  print 'found {0} members'.format(len(member_emails))
  print '  ' + '\n  '.join(str(m) for m in member_emails)

