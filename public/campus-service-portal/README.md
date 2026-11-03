# Campus Service Portal — Frontend Prototype (MUBS)

Static HTML + Tailwind CSS screens for the Campus Service Portal (Version 1.1 spec). No Laravel and no project JavaScript — each page contains its own header, sidebar, and footer.

## Design

- Light theme: white cards on `#f8fafc`, slate borders and text
- **Education blue** (`#2563eb`) as primary
- **Gold / amber** (`#d97706`) as the second academic colour
- Inter typeface, Lucide icons (CDN), rounded cards, sidebar + top bar
- Key blocks are marked with `<!-- Start: … -->` / `<!-- End: … -->` comments

## Pages

| Area | Files |
| --- | --- |
| Public | `index.html`, `login.html`, `register.html`, `forgot-password.html`, `reset-password.html` |
| Requester | `dashboard.html`, `requests/*`, `notifications.html`, `profile.html` |
| Handler | `staff/dashboard.html` |
| Admin | `admin/dashboard.html`, `admin/departments/*`, `admin/categories/*`, `admin/users/*` |

Open `index.html` in a browser, or serve the folder:

```bash
npx --yes serve .
```

Login includes prototype shortcuts for student, staff, and admin dashboards.

## Later (Laravel)

Map these files 1:1 onto Blade views and Jetstream auth. Split the repeated header, sidebar, and footer into Blade partials at that point.
