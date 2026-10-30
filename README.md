# Conditional self-enrolment by course (`enrol_bycourse`)

An enrolment method for Moodle that allows users to self-enrol only when they
have active enrolments in selected prerequisite courses.

## How it works

An administrator selects one or more prerequisite courses and chooses a rule:

- **Any selected course:** one active prerequisite enrolment is sufficient.
- **All selected courses:** every selected prerequisite enrolment is required.

Suspended, expired and not-yet-started enrolments do not qualify. A completed
course qualifies only while its enrolment remains active. The plugin never
changes enrolments in prerequisite courses.

## Features

- multiple prerequisite courses;
- any/all matching rules;
- custom instructions for users;
- enrolment icon in course listings supported by the active theme;
- availability dates, enrolment duration and capacity;
- assigned role and welcome message;
- English and Brazilian Portuguese;
- safe backup and restore of prerequisite configuration.

## Requirements

- Moodle 4.5.1 or later;
- a PHP version and database engine supported by the installed Moodle release.

## Installation

1. Install the ZIP or place the directory at `enrol/bycourse`.
2. Visit **Site administration > Notifications**.
3. In the destination course, open **Participants > Enrolment methods**.
4. Add **Conditional self-enrolment by course** and configure the prerequisites.

## Privacy

The plugin does not store additional personal data or send data to external services.

## Maintainer

Douglas Silva — <douglas.aarq95@gmail.com>

## Licence

GNU GPL v3 or later. The plugin has no licence key, activation server,
telemetry or external runtime dependency.

