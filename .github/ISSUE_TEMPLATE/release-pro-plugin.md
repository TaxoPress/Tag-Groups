---
name: Release the Pro version (team only)
about: Describes default checklist for releasing the Pro plugin;
title: Release Tag Groups Pro v[VERSION]
labels: release
assignees: ''

---

To release the Pro plugin please make sure to check all the checkboxes below.

### Pre-release Checklist

- [ ] Create the release branch as `release-<version>` based on the development branch
- [ ] Make sure to directly merge or use Pull Requests to merge hotfixes or features branches into the release branch
- [ ] Run `composer update` and check if there is any relevant update. Check if you need to lock the current version for any dependency. The `--no-dev` argument is optional here, since the build script will make sure to run the build with that argument.
- [ ] Refresh language .pot file to update new string. You can use loco translate plugin and go to Loco Translate > Plugins > Tag Groups > Edit template > Sync > Save. Copy languages folder to repository after process is complete to update the language
- [ ] Update the Version number to the next stable version in readme.txt
- [ ] Update the Version number to the next stable version in tag-groups-pro.php
- [ ] Update the changelog - make sure all the changes are there with a user-friendly description and that the release date is correct
- [ ] Commit the changes to the release branch
- [ ] Build the zip package
- [ ] Send to the team for testing

### Release Checklist

- [ ] Create a Pull Request and merge the release branch it into the `master` branch
- [ ] Merge the `master` branch into the `development` branch
- [ ] Create the Github release (make sure it is based on the `master` branch and correct tag)
- [ ] Update EDD registry and upload the new package
- [ ] Make the final test updating the plugin in a staging site
