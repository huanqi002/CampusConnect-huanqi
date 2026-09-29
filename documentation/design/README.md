# General Design

## Shared Boundaries

- `frontend/` contains shared styles and browser helpers.
- `backend/general/` contains reusable configuration and page layout.
- `backend/use_cases/<use_case>/` contains one use case's pages and request handlers.
- `database/general/schema.sql` is the shared platform schema; use cases must not maintain duplicate copies of shared tables.

Keep each use case cohesive within its own folder. Use cases may depend on shared components; shared components must not import or call use-case-specific code. Add cross-cutting design decisions here and use-case-specific flows and decisions in that use case's design document.

See [Support Session Management design](use_cases/support_session_management.md) for the current module.