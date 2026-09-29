# Requirements

## Functional Requirements

- The user can select a sample student or volunteer identity.
- A student can view their accepted requests and schedule an available volunteer slot.
- A participant can cancel an eligible scheduled session.
- A volunteer can mark an eligible session complete.
- A student can submit one rating and optional comment for a completed session.
- Users can review their session history.

## Data Requirements

The MySQL schema stores users, accepted support requests, volunteer availability, sessions, and session feedback. Foreign keys relate records; a unique session identifier in feedback limits each session to one feedback record.

## Constraints and Assumptions

- This module is a course-project prototype. The user picker is not production authentication.
- The app expects PHP with MySQLi and a MySQL database named `support_system`.
- Authorization and real notification delivery depend on the broader platform and are outside this module's current scope.

Review these requirements with the team and lecturer; update them when the agreed assignment scope changes.
