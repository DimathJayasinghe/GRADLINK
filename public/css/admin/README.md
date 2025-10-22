Admin CSS organization
======================

Files
- common.css — shared UI primitives used by all admin pages (buttons, flash messages, tables, modals).
- dashboard-common.css — layout and navigation used by admin dashboard pages (sidebar, nav, main-content).
- dashboard-overview.css — overview-specific blocks (stats, cards, quick actions, metrics).
- login.css — admin login / auth styling.
- posts.css — posts moderation page styles (kept as-is).
 - dashboard.css — compatibility shim that @imports the above files (KEEP DISABLED: renamed to `dashboard.css.disabled`).

Migration complete
- The admin CSS has been reorganized into per-view files under `public/css/admin/` (see file list above).
- Compatibility shims and backups have been removed. If you need the original files, check your VCS history.

-Guidelines
- Include `common.css` first in the view, then include the per-view file(s) needed for that page.
- Prefer adding a new per-view file for any new admin view rather than modifying `common.css`.
- Keep `dashboard.css` only as a compatibility shim. When all references are migrated, you can remove it.

Note: The compatibility shims (`admin.css` and `dashboard.css`) have been removed/neutralized in the
working tree. The original contents are preserved in VCS history (if you need to restore them).

If you run into missing styles on any admin page, update the view to include the appropriate per-view
CSS files (`common.css`, `dashboard-common.css`, `dashboard-overview.css`, etc.) or restore the shim
from VCS.

How to test
- Open an admin page and inspect the <link> tags in the head; you should see `common.css` and the relevant per-view CSS.
- Clear browser cache or append a ?v=1 query string while testing to avoid cached CSS.

Automated smoke test
- There's a simple PHP smoke test at `dev/admin_css_smoke_test.php`.
	Run from project root (PHP CLI + dev server running):

```sh
php dev/admin_css_smoke_test.php http://localhost/GRADLINK
```

It will fetch the main admin pages and verify the expected CSS files are present in the page head.
