
Goal: resolve the “Submit Application not working” issue and restore admin visibility/actions for submitted admissions with a safe, non-destructive fix.

What I found from the current code and your screenshot:
1) The submit button is likely being blocked by browser validation, not by button click failure.
   - `father_phone` is optional, but it has `pattern="[0-9]{10}"`.
   - In your screenshot, the review shows a non-numeric father phone value (“Father’s P”), which fails native HTML pattern validation and silently blocks submit.
2) Public submit can also fail if DB schema is partially upgraded.
   - `admission-form.php` inserts into `admission_status_history` immediately after inserting into `admissions`, without transaction safety.
   - If `admission_status_history` table/columns are missing, submit appears broken.
3) “Submitted info not viewable / actions not working” is consistent with schema mismatch + one SQL bug.
   - Drawer data depends on `admission_notes` and `admission_status_history`.
   - In `admin/admissions.php`, the `set_interview` history INSERT is malformed (wrong VALUES placeholders), which can break action flow.

Implementation plan (after your approval):

Phase 1 — Fix submit reliability on public admission form
- File: `php-backend/public/admission-form.php`
- Changes:
  1. Replace fragile submit behavior with explicit “find first invalid field and navigate to its step”.
  2. Keep optional fields optional, but validate format only when they are filled.
     - `father_phone`: allow blank OR exactly 10 digits.
     - same logic for optional fields with pattern constraints.
  3. On submit:
     - run step-by-step validation,
     - auto-jump user to the step with first invalid field,
     - show a clear inline message (so it never feels like “button not working”).
  4. Add submit-state UX:
     - disable submit button after valid click,
     - show “Submitting…” to avoid double clicks.

Phase 2 — Make server-side submission fail-safe
- File: `php-backend/public/admission-form.php`
- Changes:
  1. Wrap DB operations in transaction:
     - insert admission,
     - insert status history,
     - commit only if both succeed.
  2. On error:
     - rollback,
     - show friendly error alert on page,
     - log exact exception in `error_log` for diagnostics.
  3. Add strict server-side validation for optional numeric fields before DB insert (never rely on browser only).

Phase 3 — Restore admin “view + action” reliability
- Files:
  - `php-backend/admin/ajax/admission-actions.php`
  - `php-backend/admin/admissions.php`
- Changes:
  1. Fix malformed SQL in `set_interview` block in `admissions.php` (placeholder/value order).
  2. Ensure drawer endpoint gracefully handles missing note/history rows and returns valid JSON.
  3. Standardize action error responses so the UI shows meaningful messages instead of generic failures.
  4. Keep AJAX actions idempotent and consistent with CSRF/session checks.

Phase 4 — Safe schema verification (non-destructive)
- File: DB (phpMyAdmin SQL runbook; no DROP TABLE)
- I will provide and align a minimal migration checklist to verify/add only missing pieces:
  - `admission_status_history` table
  - `admission_notes` table
  - `class_seat_capacity.is_active`
  - required new columns in `admissions` (if any still missing)
- This avoids full schema re-import and prevents data loss.

Phase 5 — End-to-end verification checklist
- Public form:
  1. Fill valid data with blank optional father phone → should submit and show Application ID.
  2. Enter invalid father phone text → should show clear validation message and auto-focus relevant step/field.
  3. Upload docs and submit → should persist correctly.
- Admin:
  1. New submission appears in list.
  2. Drawer opens with details/notes/timeline.
  3. Status update, note add, follow-up/interview actions complete successfully.
- Seat management:
  1. Add class + toggle on/off.
  2. Public class dropdown reflects only active classes.

Technical notes
- I cannot run true PHP submit tests inside Lovable’s preview runtime (it serves the frontend shell and shows “this project runs as a PHP application on cPanel”). So implementation will be done in code, and final validation should be performed on your cPanel-hosted environment.
- I will keep fixes backward-compatible and avoid destructive schema operations.
