# Campus-Connect

BIT 216 University Student Support and Volunteer Platform.

## Project Structure

```text
Campus-Connect/
|-- frontend/                 CSS and browser JavaScript
|-- backend/                  PHP support-session application
|-- database/                 SQL schema and sample data
|-- documentation/
|   |-- project-plan/
|   |-- requirements/
|   |-- design/
|   `-- testing/
|-- README.md
`-- .gitignore
```

## Support Session Manager

The current module lets students and volunteers schedule support sessions, manage bookings, mark sessions complete, and submit feedback.

### Run Locally

1. Install XAMPP with Apache, PHP, and MySQL.
2. Place or clone this repository inside XAMPP's `htdocs` directory.
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Import [`database/db.sql`](database/db.sql) in phpMyAdmin. It creates the `support_system` database and sample records.
5. If your local MySQL credentials differ from XAMPP defaults, copy `backend/config.local.example.php` to `backend/config.local.php` and update the local values there. The local file is ignored by Git; never commit production credentials.
6. Open `http://localhost/Campus-Connect/backend/select_user.php` in a browser. Adjust `Campus-Connect` in the URL if the repository folder has a different name under `htdocs`.

## Git Collaboration

- Keep `main` stable. Create a short-lived branch for each feature, fix, or documentation task, such as `feature/session-feedback` or `docs/project-plan`.
- Commit focused changes with imperative messages, for example `Add session cancellation flow`.
- Push the branch and open a pull request to `main`. Request review from a teammate; add the lecturer as a repository collaborator if required and permitted by the repository owner.
- Before starting work and before opening a pull request, fetch and integrate the latest `main`. Resolve conflicts in the affected feature branch, run the relevant checks, and describe the files and decisions in the pull request. Record actual conflict resolutions in `documentation/collaboration.md`; do not record conflicts that did not occur.
- Merge reviewed pull requests using the repository's agreed merge method, then remove the merged branch.

See [`documentation/collaboration.md`](documentation/collaboration.md) for the team workflow and conflict record template. Invite contributors and the lecturer from the repository's GitHub **Settings > Collaborators** page; invitations require repository-owner access.

## Documentation

- [Project plan](documentation/project-plan/README.md)
- [Requirements](documentation/requirements/README.md)
- [Design](documentation/design/README.md)
- [Testing](documentation/testing/README.md)
