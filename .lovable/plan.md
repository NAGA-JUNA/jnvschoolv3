
# Fix Dashboard and Students to Use Real Backend Data

## Problems Found

1. **API Base URL is wrong**: The file `src/api/client.ts` has `BASE_URL = "https://schooldomain.com/api"` -- a placeholder that was never updated. This means every API call fails, and the dashboard falls back to showing zeros or old hardcoded values.

2. **Students page uses hardcoded mock data**: The Students list page imports fake student data from a local file (`mockStudents`) and never calls the PHP backend API. That's why the frontend shows "Rahul Sharma, Sneha Patel..." instead of your real database students ("Priya Patel, Rohit Kumar...").

## What Will Be Fixed

### Step 1: Update the API Base URL
Change the base URL in `src/api/client.ts` from `https://schooldomain.com/api` to `https://jnvschool.awayindia.com/api` so the frontend actually connects to your backend.

### Step 2: Connect Students List to the PHP Backend
Rewrite `src/pages/admin/students/StudentsList.tsx` to:
- Fetch students from `GET /admin/students` API endpoint instead of mock data
- Show loading skeletons while data loads
- Show error state with retry if the API fails
- Keep all existing features (search, filters, pagination, bulk actions) but operate on real data
- Server-side or client-side filtering depending on what the backend supports

### Step 3: Remove the "Quick Demo Access" hardcoded credentials display
The login page currently shows demo credentials (admin123, office123, teacher123) publicly. This will be noted but kept for now since you may still need it during development.

## Files to Modify
1. `src/api/client.ts` -- Fix BASE_URL
2. `src/pages/admin/students/StudentsList.tsx` -- Replace mock data with API calls

## After Deployment
Once these changes are made in Lovable, you will need to rebuild and re-upload the `dist/` folder to your cPanel to see the changes on your live site.
