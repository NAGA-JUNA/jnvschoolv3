

## Add Enquiries KPI to Dashboard

### What Changes

Add an "Enquiries" count card to the dashboard KPI row, sitting between "Teachers" and "Pending Admissions" to match your requested order:

**Students → Teachers → Enquiries → Pending Admissions → Pending Notifications → Pending Gallery → Upcoming Events**

### File: `php-backend/admin/dashboard.php`

1. **Add query** (line ~12 area): Count new/open enquiries from the `enquiries` table:
   ```php
   $totalEnquiries = $db->query("SELECT COUNT(*) FROM enquiries WHERE status='new'")->fetchColumn();
   ```

2. **Add KPI card** to the `$kpis` array, inserted after Teachers:
   ```php
   ['Enquiries', $totalEnquiries, 'bi-envelope-fill', 'purple', '/admin/enquiries.php'],
   ```

3. **Adjust grid**: Since there will now be 7 KPI cards, the existing `col-6 col-md-4 col-xl-2` classes will still work fine -- the 7th card wraps naturally on smaller screens.

### Result

The dashboard will show 7 KPI cards in a single row on large screens, with "Enquiries" showing the count of new/unread enquiries linking to the enquiries management page.

### Files Changed

| Action | File | Change |
|--------|------|--------|
| Modify | `php-backend/admin/dashboard.php` | Add enquiries count query + KPI card |

