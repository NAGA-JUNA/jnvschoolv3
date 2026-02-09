

# Fix Admin Dashboard to Load Real Data from PHP Backend

## The Problem

The Admin Dashboard (`src/pages/admin/Dashboard.tsx`) is entirely hardcoded with mock data:
- KPI cards show fixed numbers (1248 students, 56 teachers, etc.)
- Alert banner shows a static message
- TrendChart uses placeholder chart data
- RecentActivity uses placeholder announcements
- No API calls are made anywhere on this page

Meanwhile, the PHP backend already has three working endpoints:
- `GET /admin/dashboard/metrics` -- returns real counts from the database
- `GET /admin/dashboard/activity` -- returns recent audit log entries
- `GET /admin/alerts` -- returns dynamic alert messages

## What Will Change

### 1. Update AdminDashboard page to fetch real data
- Call `api.get(ADMIN.dashboard)` for KPI metrics
- Call `api.get(ADMIN.activity)` for recent activity
- Call `api.get(ADMIN.alerts)` for alert banners
- Show loading spinners while data loads
- Show error state if API fails, with a retry button
- Fall back gracefully if individual sections fail

### 2. Update KPI Cards section
- Map API response fields (`total_students`, `total_teachers`, `pending_notifications`, `pending_gallery`, `pending_admissions`) to the five KPI cards
- Remove hardcoded values (1248, 56, 8, 14, 23)
- Trend data will be removed for now since the backend doesn't calculate month-over-month changes

### 3. Update AlertBanner section
- Fetch alerts from `/admin/alerts` endpoint
- Show the first/most important alert dynamically
- If no alerts exist, hide the banner entirely

### 4. Update RecentActivity component
- Accept and display data from `/admin/dashboard/activity`
- Map audit log fields (`user_name`, `action`, `entity_type`, `created_at`) to the announcement card format
- Show "No recent activity" if empty

### 5. Update TrendChart component
- Keep the chart structure but pass real data if the reports endpoint provides trend data
- For now, will use the existing placeholder since the backend doesn't have a monthly trend endpoint yet

## Technical Details

### Files to modify:
1. **`src/pages/admin/Dashboard.tsx`** -- Add API fetching with useApi hook, pass data to child components
2. **`src/components/dashboard/RecentActivity.tsx`** -- Update interface to accept API activity data format
3. **`src/components/dashboard/AlertBanner.tsx`** -- Minor update to support dynamic alert from API

### API Response Structure (from DashboardController.php):
```text
/admin/dashboard/metrics returns:
  total_students, total_teachers, pending_admissions,
  pending_notifications, pending_gallery, upcoming_events,
  total_alumni, gender_breakdown, class_wise

/admin/dashboard/activity returns:
  Array of { id, action, entity_type, entity_id, created_at, user_name, user_role }

/admin/alerts returns:
  Array of { type, message, link }
```

### Data flow:
- Dashboard fetches all 3 endpoints in parallel on mount
- Loading state shown via skeleton cards
- Error state shown with retry button
- Data passed as props to child components, replacing hardcoded values
