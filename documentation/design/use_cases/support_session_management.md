# Support Session Management Design

## Module Structure

- `frontend/` contains the stylesheet and browser-side scheduling helper.
- `backend/use_cases/support_session_management/` contains this use case's PHP pages and request handlers.
- `backend/general/` contains the shared database connection and page layout.
- `database/general/schema.sql` creates the shared schema and inserts a small sample dataset.

The PHP pages render the interface and process form submissions. The scheduling helper requests available times from the PHP JSON endpoint. Shared page markup is provided by PHP header and footer includes.

## Data Model

- `users` stores student and volunteer sample identities.
- `support_requests` associates a student with an accepting volunteer.
- `volunteer_availability` stores bookable time slots.
- `sessions` stores bookings and their status.
- `feedback` stores a rating and optional comment for a completed session.

See [`database/general/schema.sql`](../../../database/general/schema.sql) for the authoritative schema.

## Design Decisions

Keep this small module in the existing PHP/MySQL stack. Share the schema in version control, but use local database credentials for development and never commit production secrets. Record agreed changes to this design here.
