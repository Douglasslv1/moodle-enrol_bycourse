# Moodle Marketplace submission pack

This document contains the publication copy and the remaining owner actions for
`enrol_bycourse` 1.1.0. It is not required at runtime.

## Product identity

- Component: `enrol_bycourse`
- Product name: Conditional self-enrolment by course
- Maintainer: Douglas Silva
- Support contact: douglas.aarq95@gmail.com
- Licence: GNU GPL v3 or later
- Supported release: Moodle 4.5.1 or later
- External services, telemetry and licence key: none

## Short description

Restrict self-enrolment to learners who have an active enrolment in any or all
selected prerequisite courses.

## Full description

Conditional self-enrolment by course adds an enrolment method for Moodle that
lets administrators control access to a destination course using the learner's
existing course enrolments.

For each instance, an administrator selects the prerequisite courses and
chooses whether the learner must have an active enrolment in any selected course
or in all selected courses. Suspended, expired and not-yet-started enrolments do
not qualify. Course completion does not bypass the active-enrolment requirement.
The plugin never creates, changes, suspends or removes enrolments in prerequisite
courses.

The method also supports custom learner instructions, enrolment start and end
dates, enrolment duration, maximum capacity, role assignment and welcome
messages. Eligible learners enrol themselves in the destination course using
the standard Moodle enrolment flow.

No licence key, activation service, telemetry or third-party runtime service is
required.

## Suggested screenshots

Use anonymised data and capture at least these images in English:

1. The enrolment-method configuration with prerequisite course selection.
2. The eligible learner's self-enrolment screen.
3. The ineligible learner notice and custom instructions.
4. The enrolment icon on a course card, using a standard Moodle theme.

## Functional acceptance test

Run with developer debugging enabled and a non-default database prefix.

1. Install `enrol_bycourse-1.1.0.zip` through the Moodle plugin installer.
2. Add the method to a destination course and select two prerequisite courses.
3. Confirm that **Any** permits a learner active in either prerequisite.
4. Confirm that **All** requires active enrolment in both prerequisites.
5. Suspend one prerequisite enrolment and confirm that it no longer qualifies.
6. Set an enrolment start date in the future and confirm that it does not qualify.
7. Set an enrolment end date in the past and confirm that it does not qualify.
8. Complete a prerequisite course, keep its enrolment active, and confirm that it qualifies.
9. Confirm that the plugin does not modify any prerequisite enrolment.
10. Confirm dates, duration, capacity, assigned role and welcome message.
11. Back up and restore the destination course on the same site.
12. Repeat the core scenarios on MySQL/MariaDB and PostgreSQL.

## Remaining publication actions

- Create a public repository named `moodle-enrol_bycourse`.
- Enable its public issue tracker and use it as the support/tracker URL.
- Publish the English documentation from `README.md`.
- Run Moodle Plugin CI, PHP lint, Moodle coding-style checks and PHPUnit on the
  public repository for Moodle 4.5 with MySQL/MariaDB and PostgreSQL.
- Add the anonymised screenshots listed above.
- Register the component in Moodle Marketplace and confirm that
  `enrol_bycourse` is available.
- Add seller, payment, tax, support, refund and pricing details in the Marketplace.
- Upload the stable ZIP and address any automated or human review feedback.

The production ZIP must not be rebuilt after testing unless its new checksum is
recorded and the rebuilt package is tested again.
