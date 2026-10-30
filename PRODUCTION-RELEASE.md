# Production release 1.1.0

## Artifact

- File: `enrol_bycourse-1.1.0.zip`
- SHA-256: `0D078B34DC6F7EA5C45FF33BF56366A09C7C4184F2313FFD386781AA5D3267D9`
- Moodle requirement: 4.5.1 or later

## Deployment

1. Back up the Moodle database and `moodledata`, and confirm a rollback path.
2. Schedule a maintenance window and enable maintenance mode.
3. Upload the exact ZIP above through **Site administration > Plugins > Install plugins**.
4. Visit **Site administration > Notifications** and complete the upgrade.
5. Purge Moodle caches and disable maintenance mode.
6. Test one eligible learner, one suspended learner and one learner who does not
   meet the requirement before releasing the affected courses.
7. Confirm cron and the Moodle logs remain healthy.

Do not remove or replace the homologated plugin directory manually during the
upgrade. Moodle preserves existing enrolment-method configuration through its
normal upgrade process.
