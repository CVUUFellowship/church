#!/bin/bash
for d in script application library public_html private; do
	rsync -crvz --exclude-from rsync_ex.txt ~/gdrive/live_members_org/$d/ ~/church/website/$d/
done
