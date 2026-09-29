# Support Session Management Tests

## Local Setup

Follow the XAMPP setup in the root [README](../../../README.md), then use the sample users and records loaded by [`database/general/schema.sql`](../../../database/general/schema.sql).

## Manual Smoke Tests

| Scenario | Expected result |
| --- | --- |
| Open the user-selection page | Sample users are listed and the page styling loads. |
| Select a student with an accepted request | The session page shows the request and available actions. |
| Schedule an available slot | The session is created and the slot is no longer offered. |
| Attempt to book an unavailable slot | The booking is rejected and the user can choose another time. |
| Cancel an eligible scheduled session | The session status changes to cancelled and the updated state is shown. |
| Mark an eligible session complete as its volunteer | The status changes to completed. |
| Submit valid feedback for a completed session | The rating and optional comment are saved once. |
| Open session history | Previously scheduled and completed sessions are shown. |

## Evidence and Defects

For each verification pass, record the date, tester, environment, scenarios run, and pass/fail results. File defects with reproduction steps and link the fixing pull request. Do not mark a test as passed without running it.
