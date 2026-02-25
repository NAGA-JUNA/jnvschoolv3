

## Fix Duplicated Content on Forgot Password and Reset Password Pages

### Problem

Both `forgot-password.php` and `reset-password.php` have broken HTML on line 58/63 respectively. The left panel content is nested inside itself, causing the school name and tagline to appear **twice**:

```html
<!-- Current broken structure -->
<div class="left-panel"><div class="left-content">
  <div class="left-panel"><div class="left-content">  <!-- DUPLICATE -->
    ...logo, name, tagline...
  </div></div>
  ...name, tagline again...  <!-- DUPLICATE -->
</div></div>
```

### Fix

Replace the broken line in each file with clean, properly structured HTML (matching login.php's working structure):

```html
<div class="left-panel">
  <div class="left-content">
    <div class="school-icon">
      <img src="<?= htmlspecialchars($logoSrc) ?>" alt="<?= htmlspecialchars($schoolName) ?> Logo">
    </div>
    <h1 style="font-size:2rem;font-weight:800"><?= htmlspecialchars($schoolName) ?></h1>
    <p style="opacity:.85">Reset your account password securely</p>
  </div>
</div>
```

### Files Changed

| File | Change |
|------|--------|
| `php-backend/forgot-password.php` (line 58) | Fix duplicated left-panel HTML |
| `php-backend/reset-password.php` (line 63) | Fix duplicated left-panel HTML |

