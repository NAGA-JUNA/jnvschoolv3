

## Move Quote Highlight Editor into About Us Content Settings

### What Changes
The quote editing fields (Quote Message and Author Name) will be embedded directly inside the **About Us** tab of the Page Content Manager, appearing right below the "Show Inspirational Quote" toggle. The standalone Quote Highlight page will remain functional but become optional/redundant.

### How It Will Work
1. When admin selects "About Us" in Page Content Manager, they will see the existing fields plus two new fields right after the "Show Inspirational Quote" toggle:
   - **Quote Message** (textarea, required)
   - **Quote Author** (text, optional)
2. These fields will read from and write to the `site_quotes` table (not the `settings` table), preserving the existing data structure.
3. The "Last updated" info will display below the fields.
4. Saving the About Us form will also save the quote in one submission.

### Technical Details

**File: `php-backend/admin/page-content-manager.php`**

1. **Add quote fields to the `about` config** (after line 54, the `about_quote_show` toggle):
   - Add two new special-type fields:
     - `about_quote_text` (textarea, label: "Quote Message")
     - `about_quote_author` (text, label: "Quote Author Name")
   - These will have a special flag like `'source' => 'site_quotes'` to differentiate them from regular settings fields.

2. **Load the current quote at the top of the file** (near line 5):
   - Query `site_quotes` table for the active quote, similar to `quote-highlight.php` line 33-37.

3. **Update the form rendering logic** (around lines 227-265):
   - When rendering the `about` page fields, detect the special `source` flag.
   - For quote fields, populate values from the `site_quotes` query instead of the `settings` table.
   - Show the "Last updated" info below the author field.

4. **Update the save handler** (around lines 130-157):
   - When saving the `about` page, detect the quote fields and save them to the `site_quotes` table (INSERT or UPDATE) instead of the `settings` table.
   - Use the same upsert logic from `quote-highlight.php` lines 16-23.

**No other files need to change.** The standalone `quote-highlight.php` continues to work independently if needed, and the `site_quotes` table structure stays the same.

