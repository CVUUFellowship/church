#!/usr/bin/python
#
# Copyright 2011 Google Inc. All Rights Reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#      http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

"""Sample for the Group Settings API demonstrates get and update method.

Usage:
  $ python groupsettings.py

You can also get help on all the command-line flags the program understands
by running:

  $ python groupsettings.py --help
"""

__author__ = 'Shraddha Gupta <shraddhag@google.com>'

from optparse import OptionParser
import os

import pprint
import sys
from apiclient.discovery import build
import httplib2
#from oauth2client.client import SignedJwtAssertionCredentials
from oauth2client.client import flow_from_clientsecrets
from oauth2client.file import Storage
from oauth2client.tools import run_flow
from oauth2client import tools


# CLIENT_SECRETS, name of a file containing the OAuth 2.0 information for this
# application, including client_id and client_secret, which are found
# on the API Access tab on the Google APIs
# Console <http://code.google.com/apis/console>
CLIENT_SECRETS = 'client_secrets.json'

# Helpful message to display in the browser if the CLIENT_SECRETS file
# is missing.
MISSING_CLIENT_SECRETS_MESSAGE = """
WARNING: Please configure OAuth 2.0

To make this sample run you will need to populate the client_secrets.json file
found at:

   %s

with information from the APIs Console <https://code.google.com/apis/console>.

""" % os.path.join(os.path.dirname(__file__), CLIENT_SECRETS)


def add_user(service, groupId, settings):
  """Makes a group.  settings should have email, description, name strs

  Args:
    service: object service for the Group Settings API.
    groupId: string identifier of the group@domain.
    settings: dictionary key-value pairs of properties of group.
  """

  # Get the resource 'group' from the set of resources of the API.
  # The Group Settings API has only one resource 'group'.
  member = service.members()

  # Retrieve the group properties
  # If dictionary is empty, return without updating the properties.
  if not settings.keys():
    print '\nGive access parameters to update group access permissions\n'
    return

  body = {}

  # Settings might contain null value for some keys(properties).
  # Extract the properties with values and add to dictionary body.
  for key in settings.iterkeys():
    if settings[key] is not None:
      body[key] = settings[key]

  # Update the properties of group
  h1 = member.insert(groupKey=groupId, body=body)
  #h1 = group.update(groupUniqueId=groupId, body=body)
  #h1.method='POST'
  #h1.uri = 'https://www.googleapis.com/admin/directory/v1/groups'
  g1 = None
  try:
    g1 = h1.execute()
  except:
    print h1.methodId
    print h1.method
    print h1.uri
    print h1.headers
    print h1.body
    raise

  print '\ncreated the group\n'
  pprint.pprint(g1)


def main(argv):
  """Demos the setting of the access properties by the Groups Settings API."""
  usage = 'usage: %prog [options]'
  parser = OptionParser(usage=usage)
  parser.add_option('--groupId',
                    help='Group email address')
  parser.add_option('--role', choices=['OWNER', 'MEMBER', 'MANAGER'],
                    help='role (https://developers.google.com/admin-sdk/directory/v1/guides/manage-group-members#create_member)', default='MEMBER')
  parser.add_option('--email',
                    help='user email')
  (options, args) = parser.parse_args()

  if options.groupId is None:
    print 'Give the groupId for the group'
    parser.print_help()
    return

  settings = {}

  settings = {'role': options.role,
              'email': options.email }

  '''
  f=open('jwt_key.p12')
  key_data=f.read()
  f.close()
  credentials = SignedJwtAssertionCredentials(
      service_account_name='640699125222-s9e3dvjmlni3l6k5sfstor266ee7rqe2@developer.gserviceaccount.com',
      # service_account_name='communicationsdirector@cvuuf.org',
      private_key=key_data,
      scope=["https://www.googleapis.com/auth/admin.directory.group"],
      private_key_password='notasecret')
  '''

  # Set up a Flow object to be used if we need to authenticate.
  FLOW = flow_from_clientsecrets(CLIENT_SECRETS,
      scope="https://www.googleapis.com/auth/admin.directory.group",
      message=MISSING_CLIENT_SECRETS_MESSAGE)

  storage = Storage('directory.dat')
  credentials = storage.get()

  if credentials is None or credentials.invalid:
    print 'invalid credentials'
    # Save the credentials in storage to be used in subsequent runs.
    flags = tools.argparser.parse_args(args=['--noauth_local_webserver'])
    credentials = run_flow(FLOW, storage, flags)

  # Create an httplib2.Http object to handle our HTTP requests and authorize it
  # with our good Credentials.
  http = httplib2.Http()
  http = credentials.authorize(http)

  service = build('admin', 'directory_v1', http=http)
  add_user(service=service, groupId=options.groupId, settings=settings)

if __name__ == '__main__':
  main(sys.argv)

