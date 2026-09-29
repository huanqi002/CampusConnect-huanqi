# Campus-Connect

BIT 216 University Student Support and Volunteer Platform.

## Project Structure

```text
Campus-Connect/
|-- frontend/                 Shared CSS and browser JavaScript
|-- backend/
|   |-- general/              Shared PHP configuration and page layout
|   `-- use_cases/
|       `-- support_session_management/
|-- database/
|   `-- general/schema.sql    Shared platform schema and sample data
|-- documentation/
|   |-- project-plan/
|   |-- requirements/         General and per-use-case requirements
|   |-- design/               General and per-use-case design
|   `-- testing/              General and per-use-case tests
|-- README.md
`-- .gitignore
```

Keep each use case's pages, handlers, requirements, design, and tests in its own named folder. Put cross-cutting code and artifacts in `general/`; use cases may depend on shared components, while shared components must not depend on a specific use case.

## Support Session Manager

The current module lets students and volunteers schedule support sessions, manage bookings, mark sessions complete, and submit feedback.

### Run Locally

1. Install XAMPP with Apache, PHP, and MySQL.
2. Place or clone this repository inside XAMPP's `htdocs` directory.
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Import [`database/general/schema.sql`](database/general/schema.sql) in phpMyAdmin. It creates the shared `support_system` database and sample records.
5. If your local MySQL credentials differ from XAMPP defaults, copy `backend/general/config.local.example.php` to `backend/general/config.local.php` and update the local values there. The local file is ignored by Git; never commit production credentials.
6. Open `http://localhost/Campus-Connect/backend/use_cases/support_session_management/select_user.php` in a browser. Adjust `Campus-Connect` in the URL if the repository folder has a different name under `htdocs`.

## Git Collaboration

- Keep `main` stable. Create a short-lived branch for each feature, fix, or documentation task, such as `feature/session-feedback` or `docs/project-plan`.
- Commit focused changes with imperative messages, for example `Add session cancellation flow`.
- Push the branch and open a pull request to `main`. Request review from a teammate; add the lecturer as a repository collaborator if required and permitted by the repository owner.
- Before starting work and before opening a pull request, fetch and integrate the latest `main`. Resolve conflicts in the affected feature branch, run the relevant checks, and describe the files and decisions in the pull request. Record actual conflict resolutions in `documentation/collaboration.md`; do not record conflicts that did not occur.
- Merge reviewed pull requests using the repository's agreed merge method, then remove the merged branch.

See [`documentation/collaboration.md`](documentation/collaboration.md) for the team workflow and conflict record template. Invite contributors and the lecturer from the repository's GitHub **Settings > Collaborators** page; invitations require repository-owner access.

## Documentation

- [Project plan](documentation/project-plan/README.md)
- [General requirements](documentation/requirements/README.md)
- [Support Session Management requirements](documentation/requirements/use_cases/support_session_management.md)
- [General design](documentation/design/README.md)
- [Support Session Management design](documentation/design/use_cases/support_session_management.md)
- [General testing guidance](documentation/testing/README.md)
- [Support Session Management tests](documentation/testing/use_cases/support_session_management.md)
