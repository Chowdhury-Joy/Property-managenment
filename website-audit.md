# Website Audit — Pillar Property Management (local codebase + live-tested)

> First-pass, intentionally messy. Not client-ready. Capturing everything now, refining later.
> Audited: 2026-07-23. Target: local codebase at `/Users/chowdhuryjoy/Property managenment/pillar-property` (Laravel 13 + Filament 3, multi-panel SaaS) **and** the live running app (`php artisan serve` on `http://localhost:8811`), cross-referenced.
> Legend: [ASSUMPTION] [UNVERIFIED] [NEEDS DATA] [COULDN'T CHECK] [QUESTION] [IDEA] [DUPE-OK]
>
> **UPDATE (same day, after a separate fix pass):** a follow-up session applied fixes for most findings below. Section 10 verifies each fix live, click-by-click, against the running app rather than trusting the fix summary. Short version: **6 of 10 claimed fixes are genuinely correct; 1 is still completely broken (same bug, cosmetic change only); and the fix pass introduced 2 new regressions of its own** (one of which silently destroys real user passwords). Read Section 10 before treating any "fixed" claim as true.

## 0. Scope, method & caveats

- **Codebase read in full**: all files under `app/`, `resources/views/`, `database/migrations`, `database/seeders`, `routes/`, `config/auth.php`, `config/filesystems.php`, `composer.json`, `resources/css/app.css`. That's every Model, every Filament Resource/Page/Widget/RelationManager across all 3 panels (Admin/Owner/Tenant), the Livewire lead-capture component, the public Blade pages, and the `rent:generate` console command.
- **Live-tested in browser** (Chrome via automation, `php artisan serve --port=8811`, seeded demo DB):
  - Public site: home, about (title-tag check), lead-capture form (full submit), mobile viewport (375px).
  - Owner portal: login, dashboard widgets, properties list, **property detail view (crashed — see F-001)**.
  - Tenant portal: login, dashboard widget, payment history, **submit-maintenance-request page (crashed — see F-002)**.
  - Verified `Setting::get()`/`Setting::set()` round-trip behavior directly via `php artisan tinker` (see F-004 for why this matters and why it's *not* as broken as it first looked).
- **Could NOT check live**: the Staff **Admin panel** (`/admin/*`) — no credentials were supplied and I deliberately avoided resetting the one existing admin account's password to keep this audit read-only on the live app; found the account exists (`admin@pillarproperty.com`, 1 row in `users` table) via read-only DB query but did not log in. This means **Settings page (F-003), Lead/Lease/Vendor/RentPayment/Owner/Tenant/Unit/Property resources, and both RelationManagers were audited from code only**, not click-tested. I'm high-confidence but not 100% on the admin-only findings — flagged individually below.
- **No real analytics, Lighthouse, or traffic data** — this is a local dev instance. Every performance claim is either directly observed (console warnings, network waterfall) or explicitly marked `[NEEDS DATA]`.
- Two small writes were made to the local SQLite dev DB purely to verify behavior (a scratch `Setting` row, and one test `Lead` row from submitting the real public form) — both were deleted immediately after verification to leave the dev DB as found.

---

## 1. Running scratchpad / raw notes

- Repo root has a nested folder: actual Laravel app lives in `pillar-property/`, not the repo root itself. `.claude/launch.json` had to be created to preview it (wasn't there before — worth adding permanently for future dev/demo work).
- `composer.json` scripts include a `dev` script that runs server+queue+pail+vite concurrently — good DX, not tested here (used plain `artisan serve` instead since Vite manifest already built).
- `APP_DEBUG=true` in `.env` — confirmed live: hitting a 500 error dumps full Ignition-style debug page with SQL queries, session cookie values, file paths, headers. Great for me auditing it, terrible if this ever got deployed as a client demo without flipping debug off first. See F-006.
- `DatabaseSeeder` seeds 2 Owners, 3 Tenants, 3 Properties, 4 Units, 3 Leases, 3 RentPayments, 2 Vendors, 2 MaintenanceRequests, and 4 `Setting` rows — but **does not seed a staff `User`** (the model behind `/admin`). The one admin user that exists in the DB was created some other way (manually, `make:filament-user`, or a previous session) — not reproducible from a clean `migrate --seed`. See F-005.
- All seeded Owner/Tenant demo passwords are literally `password` (in plaintext in the seeder file, so not really a "secret" — noting only because it means anyone with repo access can log into every demo portal).
- Confirmed via tinker that the `Setting` model's `array` cast on a `text` column is a "clever" hack that happens to round-trip correctly *only* because both `get()` and `set()` go through Eloquent's cast (double-encode / double-decode cancels out). It is very fragile — see F-004.
- Grepped the whole app for the `{{ $this->getFormActions() }}` Blade pattern: it's used in exactly 2 custom page views (`Tenant/SubmitMaintenanceRequest` and admin `Settings`), and I confirmed live that this pattern 500s. Both pages are non-functional as a result. A third page (Owner `ViewProperty`) has a *different* but same-family bug (`{{ $this->form }}` on a `ViewRecord` that has no form). All three are the three custom, hand-built Filament pages in the entire app — i.e. **100% of the custom (non-scaffold) pages in this codebase are broken**, while the auto-generated CRUD resource pages all work fine. That's a striking pattern.
- The two Filament `RelationManager`s (`UnitsRelationManager`, `LeasesRelationManager`) are still using the literal Filament stub code (`TextInput::make('id')`/`TextInput::make('name')` as the *entire* form) — they were never actually built out despite the commit message "add relation managers to property and unit resources" implying they were finished.

## 2. Top-of-mind / gut reactions

- The three headline features called out in the exec summary/commit messages — **"Owner Portal Property Deep-Dive Dashboard" (Phase 2), the Tenant maintenance-submission form, and the "Chameleon" rebranding Settings page** — are, respectively, completely broken, completely broken, and (with very high confidence from code inspection) also completely broken. These are literally the 3 marquee features of the last 3 phases of work. That's not a nitpick pile, that's "the demo doesn't actually demo the thing."
- Whoever built this leaned hard on Filament's default resource CRUD scaffolding (which all works nicely — the boring stuff is solid) but every time they had to hand-write a custom Blade view for a bespoke page, they used an API that doesn't match this Filament version, and never actually click-tested it before committing. Classic "wrote it, saw no red squiggly in the IDE, shipped it" gap — nothing here would have survived opening the page once in a browser.
- Mobile nav is a real miss for a **public marketing/lead-gen site** — that's the one property where mobile matters most (executives/owners skimming on phones, tenants reporting a leak from their phone). Right now mobile visitors can't reach About/Services/Contact via the header at all.
- The rebranding pitch ("entire site rebrands instantly... without touching code") oversells what's actually wired up: the public site's CSS variable does pick up the color, but **none of the three Filament admin panels' primary colors are tied to `Setting::primary_color`** — they're hardcoded Amber/Blue/Emerald per panel. So "rebrand instantly" is true for the public marketing site only, not the portals — and the one screen meant to *let you* rebrand (Settings) 500s anyway.
- Nobody can ever get a new Tenant or Owner logged into their portal via the admin UI — there's no password field anywhere in `TenantResource`/`OwnerResource`. The demo data was seeded with `Hash::make('password')` directly in PHP, bypassing the UI entirely. In other words, the one and only way portal accounts currently get a working password is by writing a database seeder — the actual product surface for it doesn't exist.

## 3. Findings by lens

### 3.1 UX

- **[DUPE-OK, see F-001/F-002]** Two entire user journeys (owner drilling into a property, tenant reporting maintenance) dead-end in a hard crash. No graceful failure, no partial functionality — full opaque debug-page 500.
- No confirmation/warning that deleting an Owner or Tenant cascades and permanently destroys all of their Properties → Units → Leases → RentPayments (Owner) or Leases → RentPayments (Tenant). Filament's default delete action only shows a generic "Are you sure?" — it doesn't say "this will also delete 3 properties, 4 units, 3 leases and all their payment history." See F-007.
- No forgot-password flow anywhere (owner, tenant, or staff) — none of the three `PanelProvider`s call `->passwordReset()`, and only the `users` guard has a password_reset_tokens broker configured in `config/auth.php` (owner/tenant guards have no `passwords` config entry at all, so even enabling it would need more config). If a tenant forgets their password, there is currently no self-service recovery path and no admin-side "reset password" action either (no password field on the edit forms — see F-008).

### 3.2 UI design

- `TextColumn::make('type')->badge()` (Owner & Admin `PropertyResource`) renders the raw enum value as-is: `multi_unit`, `single_family` — confirmed live in the Owner portal properties table. Compare to `LeaseResource`'s status column, which *does* map to human labels via `->color(fn... match...)` — inconsistent polish across resources. Same likely issue on `UnitResource`'s implicit any enum-ish columns that don't format state (worth an app-wide sweep for `->badge()` without `->formatStateUsing()`).
- Owner `RentSummaryStats` widget always colors its "amount owed" description `success` (green) regardless of how bad collection actually is — e.g., if only $500 of $5,000 was collected this month, it still shows green. Compare to the sibling `OccupancyStats` widget in the same dashboard, which *does* do conditional coloring (`success`/`warning`/`danger` based on rate). Inconsistent treatment of a metric that's arguably more business-critical than occupancy.
- Public layout double-loads Tailwind: once via the Tailwind CDN `<script>` tag, and again via the Vite-compiled `resources/css/app.css` (Tailwind v4, PostCSS). Confirmed live via console warning (see F-006/3.19). Redundant, and the CDN version doesn't know about the project's `@theme`/custom config, so `var(--brand-primary)` utilities technically only work because they're arbitrary-value utilities Tailwind CDN's JIT can still parse — but you're now maintaining two independent Tailwind pipelines for one page.

### 3.3 Layout

- Mobile home page (375px): hero/value-prop sections stack cleanly, no obvious overflow. Lead form fields also stack fine.
- Public layout uses `sticky top-0` on the header — fine on desktop; combined with the missing mobile nav (3.5) this just becomes a sticky logo bar with two auth buttons on mobile.

### 3.4 Visual hierarchy

- Home hero has one clear CTA ("Get Your Free Rental Analysis") — good, no competing CTAs. On the flip side, once you scroll past the hero there's no *second* CTA reinforcing conversion until the embedded lead form near the bottom — a returning scanner who skips the hero has to scroll all the way down before another action is offered. `[IDEA]` a sticky "Get a Free Analysis" mini-bar or repeated CTA after the value-prop section could help.

### 3.5 Navigation

- **[CRITICAL — confirmed live]** At 375px mobile width, the public header nav (`Home / About / Services / Contact`, class `hidden md:flex`) and the "Staff Portal" button (`hidden md:inline-block`) both vanish with **no hamburger-menu replacement built**. See `resources/views/components/layouts/public.blade.php:39` and `:52`. On mobile, a visitor literally cannot navigate to About, Services, or Contact from the header — full stop. The footer (also only visible after scrolling past all page content) does carry Services/Contact/Owner/Tenant links, but not Home/About, so **About is unreachable on mobile without manually editing the URL**. This is a severe, high-traffic-surface bug for a lead-gen site. Full finding below (F-009).
- Public footer duplicates Owner/Tenant login links (already in header) but doesn't repeat Home/About — a slightly odd, asymmetric set of "quick links."
- Filament sidebar `navigationLabel` doesn't always match the resulting page title/breadcrumb. Confirmed live: Tenant sidebar says **"Payment History"**, but the page heading + breadcrumb + browser tab title all say **"Rent Payments"** (Filament derives page title from the model name, not from `$navigationLabel`, and `PaymentResource` never overrides `getTitle()`/`getPluralModelLabel()`). Minor but real — a tenant clicking "Payment History" lands on a page that renames itself. (F-010)

### 3.6 Information architecture

- Four distinct "apps" under one Laravel app (`/`, `/admin`, `/owner`, `/tenant`) is a clean top-level structure and the URL scheme is predictable. Good bones here.
- `Setting` model has a `group` column explicitly designed to bucket settings (`branding`, `general`, `contact` per the seeder), but the Settings admin page's `save()` method (`app/Filament/Pages/Settings.php:87`) hardcodes `group: 'general'` for every field it saves — so saving *any* setting via the UI silently reassigns `company_name`'s group from `branding` back to `general`, undermining the schema's own categorization. Low real-world impact today (nothing reads `group` yet) but it's a latent data-integrity inconsistency. `[IDEA]` either drop the `group` column's ambition or wire the Settings page to preserve/assign real groups per field.

### 3.7 User journeys

- **Journey: Owner checks in on one property's performance.** Login → Dashboard (works, widgets load) → Properties (works, list renders) → click "View" on any property → **hard 500 crash**. This is the single most-promoted owner-portal feature per the exec summary ("Property Deep-Dive... unit-level occupancy, MTD rent collection, maintenance costs just for that building") and it cannot be completed at all, for any property, by any owner. Confirmed with 2/2 seeded properties tried conceptually (code path is unconditional — every `ViewProperty` hit will crash, not just some records). See F-001.
- **Journey: Tenant reports a maintenance issue.** Login → Dashboard (works) → "Submit Request" nav item → **hard 500 crash before the form even renders** (fails on page load, not on submit). A tenant cannot submit a maintenance request through the portal at all right now. See F-002.
- **Journey: Staff rebrands the site for a new client demo.** Login to `/admin` → Settings → (code inspection strongly suggests, same broken Blade pattern as F-002) **hard 500 crash**. Couldn't click-test (no admin creds), but this is the load-bearing feature of the whole "reusable sales asset" pitch. See F-003.
- **Journey: Prospective owner requests a rental analysis (the site's actual conversion goal).** Home → scroll/click CTA → fill form → submit → success message. **This one works correctly**, verified live end-to-end, including validation wiring (`required`, `email`, `min:2`, etc.) and the Lead landing in the `leads` table with `status = 'new'` ready for staff follow-up. Good — the one thing the public site exists to do, works.
- **Journey: Owner/Tenant portal onboarding for a brand-new record.** Staff creates a new Tenant/Owner in `/admin` → record has no password → tenant/owner literally cannot log in, ever, until someone manually sets a password outside the UI (tinker, direct DB edit, or a custom Filament action that doesn't exist yet). This breaks the entire "portal access" pitch for anyone added after initial seeding. See F-008.

### 3.8 Messaging

- Public copy is tight and benefit-led ("We protect your investment, maximize your returns, and treat your tenants with respect.") — no complaints on tone/clarity.
- Services page pricing/plan copy is fine but there's no actual price anywhere on the site (not even "starting at $X/mo" or "custom quote") — `[ASSUMPTION]` this may be intentional (B2B sales-assisted model, no self-serve pricing) but worth confirming with whoever's actually going to sell this.

### 3.9 Content

- Contact page (`resources/views/pages/contact.blade.php`) is extremely thin: just a phone number and email in a card, no form, no map, no office hours, no physical address, no "what happens after you contact us." For a page whose entire job is conversion, this is underbuilt — especially since the *actual* lead-capture form lives on the Home page instead, not Contact (a visitor who clicks "Contact" expecting to reach out gets... a phone number). `[IDEA]` embed the same `<x-livewire::request-rental-analysis>` (or a simpler contact form) on the Contact page itself.
- `about.blade.php` is two short paragraphs, no team photos/bios, no "years in business," no service-area map, no client logos/testimonials — thin for a trust-building page. Given the CRO/trust-signals lens below, this compounds with the total-lack-of-social-proof finding.
- `welcome.blade.php` (Laravel's stock starter-kit view) is still sitting in `resources/views/` and is **completely orphaned** — no route references it (`routes/web.php` only wires up `PageController@home/about/services/contact`). Dead file, harmless but should be deleted; its presence also means the default Vite/Tailwind fallback `<style>` block (a giant inlined stylesheet, see code) ships in the repo for a page nobody can reach.

### 3.10 CTAs

- Home page CTA-to-form flow is clean and singular ("Get Your Free Rental Analysis" → anchors to the actual form). No CTA overload.
- Contact page has *no* CTA at all beyond passively reading a phone number (see 3.9).

### 3.11 Conversion

- The one conversion path that exists (rental-analysis lead form) is well-scoped: 7 fields, only 3 required (name, email, property address), sensible optional fields. Good form-friction discipline. Confirmed via `RequestRentalAnalysis::rules()`.
- No trust reinforcement *right next to* the form (no "we respond within 24 hours" badge, no testimonial, no "no obligation" microcopy) beyond the post-submit success message which does say "call you within 24 hours." `[IDEA]` move some of that reassurance copy above the fold/next to the form itself, not just after submission.

### 3.12 Trust & credibility

- Zero testimonials, client logos, review counts, "X properties managed," years-in-business, or team bios anywhere on the public site. For a B2B property-management sales site, this is a significant trust gap — the entire pitch rests on generic copy with no third-party validation. `[NEEDS DATA]` — this may be intentional placeholder-stage content since this is explicitly a demo/sales-asset template, but flagging since the exec summary frames it as a "reusable sales asset," and a sales asset with zero social proof undercuts its own purpose.
- No visible privacy policy / terms of service link anywhere (header or footer). No cookie banner (not necessarily required depending on jurisdiction/analytics used, but there's also no analytics observed at all — see 3.18).

### 3.13 Accessibility (WCAG-ish — eyeballed, not measured)

- Hero heading color: white text (`text-white`) on `bg-[var(--brand-primary)]` (`#1e3a8a`, blue-900-ish) — contrast looks comfortably high `[UNVERIFIED contrast — eyeballed, not measured with a tool]`.
- Public nav "hidden md:flex" pattern (3.5) is also an a11y concern beyond just UX: mobile screen-reader/keyboard users lose the nav links entirely, they're not just visually hidden-but-reachable (no `sr-only` fallback, they're `display:none` via `hidden`).
- Filament login/dashboard pages: standard Filament a11y (labels tied to inputs, focus rings visible on the email field per screenshot) — Filament's own accessibility baseline generally carries through since these aren't custom-built forms. Not independently audited beyond visual inspection.
- Lead form: labels are real `<label>` elements (not placeholder-as-label) — good. `[COULDN'T CHECK]` full keyboard-only tab order and screen-reader landmark structure (would need axe/VoiceOver pass).
- No `prefers-reduced-motion` handling anywhere observed, but also no meaningful motion/animation exists in this app to begin with (`[N/A]`).

### 3.14 Mobile responsiveness

- **[CRITICAL, see F-009 / 3.5]** No mobile nav — top nav links + Staff Portal button disappear below `md` breakpoint with zero replacement (no hamburger). This is the standout mobile bug.
- What *does* render at 375px (hero, value-prop cards, lead form) stacks and reflows cleanly — no horizontal scroll, no obviously broken tap targets observed in the portion of the page that's visible.
- `[COULDN'T CHECK]` Filament panel mobile responsiveness in depth (Filament ships responsive admin UI by default, and the login screens rendered fine at desktop width in this session — did not specifically re-test panel interior pages at 375px given time constraints).

### 3.15 Forms

- Lead-capture form (public): real-time validation via Livewire, inline error messages under fields (`@error` blocks), clear required vs optional labeling, resets fully on success. Solid implementation, verified live end-to-end.
- `Setting` admin form (Settings page, code-only review): `TextInput::make('company_name')` has no `->unique()`-equivalent concern (not a DB-unique field, fine), but note the save handler's `if ($value !== null)` guard means **unchecking/clearing a FileUpload (logo/favicon) can never actually remove it** — once a logo is set, there's no way to clear it back to "no logo" through this UI, since an empty FileUpload value is `null` and `null` is explicitly skipped on save. `[UNVERIFIED — couldn't click-test, but this follows directly from the code at Settings.php:86]`.
- `OwnerResource`/`TenantResource` forms lack `->unique()` validation on the `email` field, despite the `owners`/`tenants` tables both having a DB-level `unique()` constraint on `email` (see migrations). Practical effect: submitting a duplicate email in either Create form will not show a friendly inline Filament validation error — it'll bubble up as an uncaught `QueryException` (SQLite unique constraint violation) → another raw debug/500 page, not a graceful "this email is already in use." `[UNVERIFIED live — no admin access — but this is a straightforward, high-confidence reproduction path given the DB schema vs. form code]`. Same likely applies to `Vendor`/`Lead` (no unique DB constraint there, so N/A) and `Property`/`Unit` (no unique constraints, N/A).
- `LeasesRelationManager::form()` (used from Unit → Leases tab in the admin panel) only defines a single field: `TextInput::make('id')`. `id` isn't in `Lease`'s `$fillable`, and none of the *actual* required fields (`tenant_id`, `start_date`, `end_date`, `rent_amount`, all NOT NULL with no defaults in the migration) are collected. Creating a lease from this relation manager will near-certainly throw a DB "NOT NULL constraint failed" error. `[UNVERIFIED live — no admin access — but extremely high confidence]`. This is the exact leftover-Filament-stub problem noted in the scratchpad. See F-011.
- `UnitsRelationManager::form()` (Property → Units tab) similarly only exposes `name` — no `bedrooms`/`bathrooms`/`sqft`/`status`. This one won't crash (those columns have DB defaults or are nullable) but it's a materially degraded creation form vs. the full `UnitResource` form — you'd create a unit here and then have to go edit it separately to fill in the rest.

### 3.16 Interactions & micro-interactions

- Filament tables/forms carry their standard hover/focus states — nothing custom-broken observed.
- Public site has basically zero micro-interactions beyond `hover:opacity-90`/`hover:bg-gray-100` on buttons and link color hovers — consistent, if minimal.

### 3.17 Empty / loading / error / success states — checked explicitly

- **Loading**: Filament dashboard widgets show a skeleton/placeholder box while stats compute (confirmed live, both Owner and Tenant dashboards render an empty dark box for ~1s before populating). Standard Filament behavior, works fine.
- **Empty**: `NextPaymentStats` (tenant) explicitly handles "no active lease" and "all caught up, no upcoming payments" empty states with friendly copy + icon + color — good example of doing this right. `OccupancyStats`/`TotalPropertiesStats` (owner) degrade to `0%`/`0` if `auth()->guard('owner')->user()` is somehow null — defensive, sensible.
- **Error**: **This is where it falls apart.** There is no custom 404/500/419 error page — hitting any server error shows Laravel/Ignition's raw debug page (because `APP_DEBUG=true`), which is a developer tool, not a user-facing error state. No graceful "Something went wrong, our team's been notified" page exists anywhere in the app for the 3 confirmed/likely-confirmed crashing routes. `[COULDN'T CHECK]` 404 behavior specifically (didn't hit a genuine not-found route) but there's no custom `resources/views/errors/` directory in the repo, so it'll be Laravel's stock 404, unstyled/unbranded.
- **Success**: Lead form success state is well done (checkmark icon, reassuring copy, form resets). Maintenance-request submission's intended success state (`Notification::make()->title('Request Submitted')...`) looks well-written in the code but **can never be seen** because the page crashes before the form can even be submitted (F-002).

### 3.18 SEO

- Every public page (`Home`, `About`, `Services`, `Contact`) renders the **exact same `<title>` tag** — `{{ Setting::get('company_name', ...) }}` — because the shared layout (`components/layouts/public.blade.php:6`) never reads the `$title` prop each page explicitly passes in (`<x-layouts.public title="About Us">` etc.). **Confirmed live**: navigated to `/about`, browser tab title read "Pillar Property Management," not "About Us" or similar. This is a real, verified SEO + UX bug — duplicate `<title>` across every page hurts search-result differentiation and makes multi-tab browsing confusing. See F-012.
- No meta description on any page (static or dynamic) — `<meta name="description">` is entirely absent from the layout.
- No Open Graph / Twitter Card tags — sharing any page link socially will show no rich preview.
- No `sitemap.xml`, no visible `robots.txt` customization checked `[COULDN'T CHECK — public/robots.txt exists per file listing but contents not read]`.
- No structured data (LocalBusiness/Organization schema) — reasonable for a demo, but worth a mention since property-management is a very local-SEO-driven business model.

### 3.19 Performance

- **Confirmed via console**: `cdn.tailwindcss.com should not be used in production` warning fires on every public page load (4x on one page load, once per Tailwind CDN re-init it seems). The CDN Tailwind build is a full runtime JIT compiler shipped to every visitor, on top of the *already* Vite-compiled, purpose-built Tailwind v4 CSS bundle (`app-BCk5hrL9.css`) loaded right next to it. Actively loading two separate Tailwind pipelines on every request. See F-006.
- `[NEEDS DATA]` no Lighthouse/PageSpeed numbers were run — can't give real scores. Given the CDN Tailwind script alone, I'd expect a real hit to performance/best-practices scores, but that's an inference, not a measurement.
- No image optimization concerns observed on the public site simply because there are **no images at all** on Home/About/Services/Contact — everything is text/icons/CSS gradients. Not a performance problem today, but also means the "trust" pages have zero visual warmth (ties back to 3.12).

### 3.20 Technical issues

- **F-001, F-002 (both confirmed live), F-003 (high-confidence, code-only)** — see Section 5/6 below, these are the headline technical findings.
- `public/favicon.png` referenced as the layout's fallback favicon (`asset('favicon.png')` when no custom favicon Setting is set) **does not exist** in `public/` — only `favicon.ico` is present (Laravel's stock default). Any fresh install/demo that hasn't uploaded a custom favicon via Settings will silently 404 on that `<link rel="icon">` request. `[UNVERIFIED — didn't confirm the actual 404 in network tab, but confirmed the file's absence directly on disk]`.
- `LeasesRelationManager.php` and `UnitsRelationManager.php` both import `Illuminate\Database\Eloquent\SoftDeletingScope` and (in one case) `Builder` without ever using them — dead imports, harmless but sloppy; also both are leftover `php artisan make:filament-relation-manager` stub boilerplate that appears to have never been finished (ties to F-011).
- `AppServiceProvider::boot()`/`register()` are both totally empty — fine, just noting nothing app-wide is being bootstrapped there (no observers, no policies registered anywhere in the codebase actually — see 3.25 below on the total absence of any Policy classes despite 3 separate portals with different data-visibility rules).

### 3.21 Design-system consistency

- Brand color is a single CSS var (`--brand-primary`) used consistently across the public site's buttons/links/hero — good, that part of the "Chameleon" pitch is real. But it stops at the public site's edge: none of the 3 Filament panels' `->colors(['primary' => Color::X])` calls reference `Setting::get('primary_color')` — they're hardcoded per-panel (`Amber` for admin, `Blue` for owner, `Emerald` for tenant). So a client demo where you change the brand color in Settings would recolor the marketing site but leave all 3 back-office portals in their original hardcoded colors — a half-finished "theme engine." See F-013.
- Enum/status badge coloring is handled ad hoc per-resource via inline `match()` closures repeated almost verbatim across `LeaseResource`, `RentPaymentResource`, `MaintenanceRequestResource`, `UnitResource`, `TenantResource`(n/a), etc. — works, but it's copy-pasted logic (same 'success'/'warning'/'danger' mapping conventions re-typed resource by resource) rather than a shared status-color helper/enum. Not broken, just a consolidation opportunity.

### 3.22 Repeated components

- The `match($state) { ... => 'success'/'warning'/'danger' ... }` badge-color closure pattern (3.21) appears near-identically in at least 5 different Resource classes. `[IDEA]` extract to a trait or a `HasStatusColor` concern / a static helper on each enum-like concept.
- Both custom "form + actions" Blade page templates (`settings.blade.php`, `submit-maintenance-request.blade.php`) are structurally identical (and identically broken — see F-002/F-003) — a single shared Blade component or corrected pattern would fix both at once.

### 3.23 Code quality (codebase mode)

- Overall the Eloquent models are lean, relationships are correctly typed, and `$fillable`/`$casts` are used appropriately (decimal casts on money fields, date casts on date fields) — solid baseline.
- `Setting::get()`/`Setting::set()` (see F-004) is functionally correct today but architecturally fragile: it layers manual `match($type)` casting logic on top of an Eloquent `array` cast on the same column, so the type-correctness only holds as long as **every single write** to the `settings` table goes through `Setting::set()`. Any future direct `DB::table('settings')->insert(...)`, a raw SQL import, or a hand-edited row would silently return `null` from `Setting::get()` for that row, with no error or warning anywhere. Worth simplifying to just the `array` cast + doing type-coercion once, in one place.
- No `Policy` classes anywhere in the app (`app/Policies` doesn't exist) — authorization for data-scoping (Owner only sees their properties, Tenant only sees their lease) is instead hand-rolled per-resource via `getEloquentQuery()` overrides (e.g. `OwnerResource::getEloquentQuery()` at `app/Filament/Owner/Resources/PropertyResource.php:18`). This works but is easy to forget on a new resource — there's no structural guardrail forcing every new Owner/Tenant resource to be scoped; it relies on every future developer remembering to copy the same override. `[IDEA]` a base Resource class per panel that enforces scoping by default would be more defensive.
- No test suite exercising any of this — `tests/` wasn't examined in depth but `composer.json`'s `test` script just runs `artisan test` with whatever's scaffolded; given the severity of F-001/F-002, it's clear no feature test ever rendered these pages (a one-line `get('/owner/properties/1')->assertOk()` Feature test would have caught F-001 immediately). `[COULDN'T CHECK — didn't enumerate tests/ contents]`.
- `TenantResource`/`OwnerResource` forms and tables are minimal (name/email/phone only) with no password field, no "resend invite," no "enable/disable portal access" toggle exposed anywhere in the UI — despite the migration explicitly reserving a `portal_enabled_at` timestamp column "for later" (comment in migration: `// Portal access fields (we'll wire up actual auth later)`) that's never surfaced in the admin form, and never actually checked anywhere in the auth flow (`portal_enabled_at` is set by the seeder but no guard/middleware/policy anywhere gates login on whether it's set). It's a fully vestigial column right now. See F-008/F-014.

### 3.24 Missing pages or states

- No custom 404 page.
- No custom 500/error page (compounded by `APP_DEBUG=true` — see F-006).
- No Privacy Policy / Terms of Service pages (or links to them).
- No password-reset / forgot-password flow for any of the 3 guards.
- No "portal invite" flow (send a tenant/owner an email to set their own password) — the product needs one given there's no password field in the admin create/edit forms at all.
- Missing mobile nav (see 3.5/3.14) counts as a missing *state* of the header component, not just a visual gap.

### 3.25 Confusing decisions

- Why does `ViewProperty` (Owner portal) render `{{ $this->form }}` inside a `<form wire:submit="save">` when the whole point of `ViewRecord` + `canCreate() => false` + no edit page registered is that this resource is supposed to be **read-only**? It looks like the view was scaffolded from an Edit-page template and never adapted to the fact it's a View page. Filament's idiomatic read-only pattern is an Infolist, not a Form — this page doesn't define one either.
- The `LeasesRelationManager`/`UnitsRelationManager` stub forms (F-011) being left in their auto-generated state, despite a commit message explicitly claiming relation managers were "added" to these resources, is the most confusing single thing found in this audit — it reads as "created the file, wired it into `getRelations()`, never opened it again."
- `portal_enabled_at` exists, is seeded, but is never read anywhere (`grep`-confirmed no `whereNotNull('portal_enabled_at')` or similar check in any auth path) — so it currently has zero effect on whether someone can log in. `[QUESTION]` was the intent that setting this to null should lock someone out of the portal without deleting their account? If so, it needs to actually be enforced somewhere (a custom guard, a login hook, or at minimum a scope).

### 3.26 Potential opportunities

- Fixing the 3 broken pages (F-001/F-002/F-003) is obviously priority zero — everything else is secondary until the flagship features actually work.
- A shared "status badge color" helper/enum (3.21/3.22) would cut a meaningful chunk of repeated code and make future status additions consistent automatically.
- Wiring `Setting::primary_color` into all 3 `PanelProvider`'s `->colors()` calls (via the same closure pattern already used for `brandName`/`brandLogo`) would make the "instant rebrand" pitch actually true end-to-end, not just on the public site.
- A tiny `php artisan make:filament-user`-equivalent baked into the seeder (or at minimum a README callout) would remove the "how do I even log into /admin on a fresh clone" friction — this is supposed to be a reusable sales asset that gets spun up repeatedly, so day-one setup friction matters a lot here.
- Building a real "invite tenant/owner" flow (password field + optional "send set-password email" action) would close the biggest functional gap in the whole platform — right now the portal features literally cannot onboard a new real user through the UI.
- A hamburger mobile nav for the public site is probably a 30-minute fix (Alpine.js is already loaded via Livewire) with outsized impact given this is the lead-gen surface.

---

## 4. Selected findings, full format

#### [F-001] Owner "Property Deep-Dive" view crashes with a 500 on every property — CONFIRMED LIVE
- **What I noticed:** Clicking "View" on any property in the Owner portal (`/owner/properties/{id}`) throws an uncaught exception and shows Laravel's raw debug error page instead of the property dashboard.
- **Where:** [app/Filament/Owner/Resources/PropertyResource/Pages/ViewProperty.php](pillar-property/app/Filament/Owner/Resources/PropertyResource/Pages/ViewProperty.php) (extends `ViewRecord`, no `form()`/`infolist()` defined) + its custom view [resources/views/filament/owner/resources/property-resource/pages/view-property.blade.php:4](pillar-property/resources/views/filament/owner/resources/property-resource/pages/view-property.blade.php#L4), which calls `{{ $this->form }}` inside `<x-filament-panels::form wire:submit="save">`.
- **Why it may be a problem:** `ViewRecord` pages don't have a `form` property/schema registered (that's an Edit/Create-page concept) — Livewire chokes trying to serialize the nonexistent property: `Property type not supported in Livewire for property: [{"componentName":null,"attributes":null}]` (exact error captured live, `vendor/livewire/livewire/src/Mechanisms/HandleComponents/HandleComponents.php:560`).
- **User impact:** Every owner, viewing any property, gets a hard crash — the entire "Property Deep-Dive Dashboard" feature (explicitly the deliverable of "Phase 2" per commit history) is 100% non-functional.
- **Business impact:** This is arguably the single most-demoed owner-facing feature. If shown live to a prospective client, this crashes the demo immediately and looks like abandoned/broken software.
- **Severity:** Critical.
- **Possible solution:** Either (a) switch `ViewProperty` to Filament's Infolist pattern (`infolist(Infolist $infolist): Infolist` on the page/resource, and use the stock `<x-filament-panels::page>{{ $this->infolist }}</x-filament-panels::page>` blade, dropping the custom form-based view and the `wire:submit="save"` entirely since this page has no save action), or (b) if a form really is wanted here, give `ViewProperty` its own `form()` method and drop the read-only intent. The header-widgets stat cards (`getHeaderWidgets()`) are unaffected and already work correctly on their own.
- **Implementation notes:** Small, contained fix — one Blade file + one PHP page class. No schema/migration changes needed.
- **Else to investigate:** Worth a quick regression test (`get('/owner/properties/{id}')->assertOk()`) once fixed, and a search for any *other* `ViewRecord` pages with custom views that might have the same issue (none found elsewhere in this codebase — this is the only custom `ViewRecord` view).

#### [F-002] Tenant "Submit Maintenance Request" page crashes with a 500 on page load — CONFIRMED LIVE
- **What I noticed:** Navigating to `/tenant/submit-maintenance-request` throws immediately — the page never renders, not even the form.
- **Where:** [resources/views/filament/tenant/pages/submit-maintenance-request.blade.php:6](pillar-property/resources/views/filament/tenant/pages/submit-maintenance-request.blade.php#L6) — `{{ $this->getFormActions() }}`, and the same pattern exists in [app/Filament/Tenant/Pages/SubmitMaintenanceRequest.php:80](pillar-property/app/Filament/Tenant/Pages/SubmitMaintenanceRequest.php#L80).
- **Why it may be a problem:** `getFormActions()` returns a plain PHP array of `Filament\Actions\Action` objects. Blade's `{{ }}` tries to stringify it via `htmlspecialchars()`, which throws `TypeError: htmlspecialchars(): Argument #1 ($string) must be of type string, array given` — exact error captured live.
- **User impact:** No tenant can submit a maintenance request through the portal at all, ever, under any circumstances — it 500s before the form even paints.
- **Business impact:** This is the headline Tenant Portal feature per the exec summary ("Maintenance Submission — a dedicated form to submit work orders with photo uploads and urgency levels"). Fully non-functional.
- **Severity:** Critical.
- **Possible solution:** Filament 3's correct pattern for rendering form actions in a custom page view is `<x-filament-panels::form.actions :actions="$this->getFormActions()" />` (or wrapping them via `{{ $this->getFormActions() }}` is simply not the supported API in this version) — swap the raw echo for the proper Blade component.
- **Implementation notes:** One-line Blade fix. Same exact fix needed in F-003 (Settings page) — same root cause, same file pattern, fix both at once.
- **Else to investigate:** Confirm the fix against the actual Filament 3.x version pinned in `composer.lock` to get the exact right component/syntax for that minor version.

#### [F-003] Admin Settings ("Chameleon" rebranding) page — near-certain 500 crash, same bug as F-002 — HIGH CONFIDENCE, COULDN'T VERIFY LIVE
- **What I noticed:** [resources/views/filament/pages/settings.blade.php:6](pillar-property/resources/views/filament/pages/settings.blade.php#L6) contains the byte-for-byte identical `{{ $this->getFormActions() }}` pattern that's confirmed-crashing in F-002, backed by an identical `getFormActions(): array` method in [app/Filament/Pages/Settings.php:98](pillar-property/app/Filament/Pages/Settings.php#L98).
- **Where:** `/admin/settings`.
- **Why it may be a problem:** Same as F-002 — array being echoed via Blade's string-casting output.
- **User impact:** Staff cannot change the company name/logo/favicon/brand color/contact info through the UI at all — the entire dynamic-rebranding "Chameleon Engine" described as the platform's core differentiator is (very likely) unusable.
- **Business impact:** This is described in the brief as *the* differentiator for reselling this as a template across clients. If broken, that pitch falls apart.
- **Severity:** Critical (pending live confirmation).
- **Possible solution:** Same fix as F-002.
- **Implementation notes:** N/A, same as F-002.
- **Else to investigate:** **I could not log into `/admin` to confirm this live** — there's exactly one existing staff `User` in the dev DB and I didn't have/reset its credentials (didn't want to mutate the live app's auth state beyond read-only inspection). Whoever picks this up should click-test `/admin/settings` first — I'd bet high odds it 500s identically to F-002, but flagging the confidence level honestly rather than asserting it as directly observed.

#### [F-004] `Setting::get()`/`Setting::set()` — works today, but fragile-by-coincidence — VERIFIED VIA TINKER
- **What I noticed:** [app/Models/Setting.php](pillar-property/app/Models/Setting.php) casts `value` as `array` (`protected $casts = ['value' => 'array']`) on a `text` column, *and* separately hand-rolls type coercion (`match($setting->type) { 'integer' => (int)..., 'boolean' => filter_var(...), ... }`) in `get()`/`set()`.
- **Where:** `app/Models/Setting.php:12` (cast) and `:19-54` (`get`/`set`).
- **Why it may be a problem:** I initially suspected this was badly broken (a plain string value would fail `json_decode` and silently return `null`) — but tested directly via `php artisan tinker`: writing `Setting::set('test_key', 'Acme Property Co', ...)` and reading it back returned the correct string. Root cause: `set()`'s manually-cast value gets *re*-encoded by Eloquent's `array` cast on save (`json_encode`), and `get()`'s raw DB read gets *re*-decoded by the same cast (`json_decode`) before the manual `match()` logic runs — the double round-trip cancels out and it happens to work, for every type, as long as **all** reads/writes go through these two static methods.
- **User impact:** None today, everything observed works. But it's a landmine: any settings row written a different way (a raw SQL import, a future `DB::table('settings')->insert()`, hand-editing a row in a DB GUI) will silently return `null` from `Setting::get()` for that key — no exception, no log, just a quietly missing value, likely surfacing as a blank company name / missing brand color somewhere on the public site with zero clue why.
- **Business impact:** Low today, but a nasty multi-hour debugging trap waiting for whoever touches this next without knowing the coincidence.
- **Severity:** Low today / Medium as tech debt.
- **Possible solution:** Simplify to one cast or the other — either keep the Eloquent `array` cast and drop the manual `match()` re-casting (just store everything as real JSON and read it back as-is), or drop the `array` cast and keep manual serialization in `get()`/`set()` only. Don't do both.
- **Implementation notes:** Small, contained change to one model; would want to re-verify existing seeded rows still read correctly after refactor (they should, per the successful tinker test).
- **Else to investigate:** None — verified directly, not inferred.

#### [F-005] `DatabaseSeeder` never creates a staff `User` — locks you out of `/admin` on a fresh clone
- **What I noticed:** [database/seeders/DatabaseSeeder.php](pillar-property/database/seeders/DatabaseSeeder.php) seeds Owners/Tenants/Properties/Units/Leases/RentPayments/Vendors/MaintenanceRequests/Settings — but zero `User` (staff) records, even though `/admin` (the "default" panel, per `AdminPanelProvider::default()`) is gated by the `web` guard against the `users` table.
- **Where:** whole file, confirmed by absence + `php artisan tinker` showing exactly 1 `User` row in the current dev DB (`admin@pillarproperty.com`) that predates/exists outside this seeder.
- **Why it may be a problem:** Anyone running `composer install && php artisan migrate --seed` (the documented `composer setup` script) from a clean clone gets a fully-seeded demo... with no way to log into the primary Staff Admin panel, the one meant to control everything else.
- **User impact:** N/A directly (dev/setup issue, not an end-user issue) but blocks anyone trying to stand this up fresh — including, notably, future devs or this agency reusing it as a sales-asset template, which is the explicit stated purpose of this codebase.
- **Business impact:** Directly undercuts the "reusable sales asset" pitch — spinning up a fresh instance for a new prospect requires an out-of-band `php artisan tinker` or `make:filament-user` step that isn't documented anywhere found in this repo (no README section on it — `[COULDN'T CHECK]` full README contents in depth).
- **Severity:** High (setup/onboarding blocker).
- **Possible solution:** Add a `User::factory()->create([...])` (or a fixed demo admin) call to `DatabaseSeeder`, matching the pattern already used for Owners/Tenants.
- **Implementation notes:** Trivial addition, one factory call.
- **Else to investigate:** Check whether `README.md` documents the missing step — `[COULDN'T CHECK]`, wasn't opened during this pass.

#### [F-006] `APP_DEBUG=true` + Tailwind CDN script both present — production-hygiene issues, confirmed live
- **What I noticed:** (a) Every crash/error shows a full Ignition-style debug page with SQL queries, session cookies, and file paths — confirmed live on both F-001 and F-002. (b) `<script src="https://cdn.tailwindcss.com">` loads on every public page alongside the already-compiled Vite/Tailwind bundle — confirmed live via repeated console warning: `cdn.tailwindcss.com should not be used in production.`
- **Where:** `.env` (`APP_DEBUG=true`) and [resources/views/components/layouts/public.blade.php:13](pillar-property/resources/views/components/layouts/public.blade.php#L13).
- **Why it may be a problem:** Debug mode leaking SQL/session/path data is a real info-disclosure risk the moment this is deployed anywhere non-local. The redundant Tailwind CDN load is a performance/hygiene issue and a maintenance trap (two independent sources of truth for the same utility classes).
- **User impact:** None for local dev; real risk if ever deployed with these settings unchanged (easy to forget, since this is meant to be cloned/re-deployed repeatedly as a sales template).
- **Business impact:** Any leaked SQL/session info in a client-facing demo environment would look unprofessional at best, be a real security issue at worst.
- **Severity:** Medium (context-dependent — High if this ever runs on a public URL).
- **Possible solution:** (a) `APP_DEBUG=false` for any non-local deployment (standard Laravel practice, probably already the intent — flagging since it's easy to forget when this gets redeployed per-client). (b) Remove the Tailwind CDN `<script>` tag entirely now that the Vite pipeline is fully wired up — it looks like a leftover from before the Tailwind v4/Vite setup was finished.
- **Implementation notes:** (a) is an env/deploy-checklist item, not code. (b) is a one-line deletion.
- **Else to investigate:** Worth double-checking no other pages/layouts also reference the CDN script — grep showed it's only in the one public layout, Filament panels use their own asset pipeline so unaffected.

#### [F-007] Cascading hard-deletes on Owner/Tenant with only a generic confirm dialog
- **What I noticed:** `properties.owner_id`, `units.property_id`, `leases.unit_id`, `leases.tenant_id`, and `rent_payments.lease_id` are all `->cascadeOnDelete()`. No model uses `SoftDeletes`.
- **Where:** [database/migrations/2026_07_23_154654_create_properties_table.php:13](pillar-property/database/migrations/2026_07_23_154654_create_properties_table.php#L13) and siblings; delete actions wired in [OwnerResource/Pages/EditOwner.php](pillar-property/app/Filament/Resources/OwnerResource/Pages/EditOwner.php) / [TenantResource/Pages/EditTenant.php](pillar-property/app/Filament/Resources/TenantResource/Pages/EditTenant.php) via plain `Actions\DeleteAction::make()`.
- **Why it may be a problem:** Deleting one Owner permanently and irreversibly destroys every Property, Unit, Lease, and RentPayment underneath them — Filament's default delete confirmation is a generic "Are you sure?" that gives no indication of the blast radius.
- **User impact:** A staff user could accidentally wipe an owner's entire portfolio history (including paid-rent records) with one click and a reflexive "yes" on the confirm dialog.
- **Business impact:** Financial/lease history is exactly the kind of data you don't want a hard, unrecoverable delete on — this is bookkeeping-adjacent data.
- **Severity:** High.
- **Possible solution:** Add `SoftDeletes` to Owner/Property/Unit/Tenant/Lease/RentPayment, and/or customize the `DeleteAction`'s modal copy to state exactly what will cascade (Filament supports custom modal heading/description per action).
- **Implementation notes:** SoftDeletes is the more robust fix but touches migrations + every `getEloquentQuery()` scoping override to make sure soft-deleted records stay hidden; the modal-copy change is a same-day mitigation.
- **Else to investigate:** Confirm whether any reporting/analytics elsewhere would break if historical (soft-)deleted records started being excluded from sums — currently N/A since nothing's soft-deleted yet.

#### [F-008] No password field anywhere for Owner/Tenant — portal accounts can't be provisioned via the UI
- **What I noticed:** [OwnerResource::form()](pillar-property/app/Filament/Resources/OwnerResource.php#L21) and [TenantResource::form()](pillar-property/app/Filament/Resources/TenantResource.php#L21) both only collect `name`/`email`/`phone`. No password input exists in Create or Edit. All seeded demo accounts got their password via `Hash::make('password')` directly in the PHP seeder, bypassing the UI.
- **Where:** as above; also no `->passwordReset()` enabled on any of the 3 `PanelProvider`s, and `config/auth.php`'s `passwords` broker array only has a `users` entry (no `owners`/`tenants` entries), so self-service reset isn't even configured at the framework level for those guards.
- **Why it may be a problem:** There is currently no product surface — UI or self-service — for getting a real new Owner or Tenant a working login.
- **User impact:** Staff creates a new Tenant in `/admin` expecting them to be able to log into `/tenant` — they can't, with no error or indication why, since the record saves successfully (it just has `password = null`), and a `null` password hash will never successfully authenticate.
- **Business impact:** This breaks onboarding for every real (non-seeded) tenant/owner — a core piece of the platform's value prop ("Tenant: Has portal access").
- **Severity:** Critical (functional gap, not just a bug).
- **Possible solution:** Add a password field to both Create forms (optional-with-confirmation on Edit), or better, build a "Send portal invite" action that emails a password-setup link (needs the missing `passwords` broker config for both guards, plus notification/mail wiring).
- **Implementation notes:** Medium effort — touches forms, `config/auth.php`, possibly a custom Notification class + signed URL controller for the invite-link approach.
- **Else to investigate:** Related to F-014 (`portal_enabled_at` also unused/unenforced) — likely the same piece of unfinished work covers both.

#### [F-009] No mobile navigation on the public site — confirmed live at 375px
- **What I noticed:** At mobile width, the header shows only the logo/company-name and the Owner/Tenant login buttons. The primary nav (Home/About/Services/Contact) and the "Staff Portal" button both disappear with no hamburger menu or any other mobile-accessible replacement.
- **Where:** [resources/views/components/layouts/public.blade.php:39](pillar-property/resources/views/components/layouts/public.blade.php#L39) (`class="hidden md:flex ..."`) and `:52` (`class="hidden md:inline-block ..."`).
- **Why it may be a problem:** `hidden md:flex`/`hidden md:inline-block` remove these elements from the DOM's visible rendering below the `md` breakpoint with literally nothing standing in for them — no `<button>` toggling a mobile drawer exists anywhere in this file.
- **User impact:** A mobile visitor cannot reach About, Services, or Contact from the header at all. The footer (reachable only after scrolling the entire page) does carry Services/Contact links but not Home/About — so **About is functionally unreachable on mobile** short of manually typing `/about` in the URL bar.
- **Business impact:** This is the public lead-gen site — mobile traffic share for this kind of local-service search is typically very high `[ASSUMPTION]`. Losing primary navigation on mobile directly costs page views and trust ("looks broken").
- **Severity:** Critical/High.
- **Possible solution:** Standard Alpine.js hamburger-menu pattern (Alpine is already loaded transitively via Livewire) toggling a mobile drawer/dropdown with the same 4 links + both login buttons.
- **Implementation notes:** Should be a same-day fix — no backend changes needed, purely the shared public layout Blade file.
- **Else to investigate:** Re-test at tablet width (768px) too — didn't specifically check that breakpoint in this pass.

#### [F-010] Filament sidebar label vs. page title/breadcrumb mismatch (Tenant Payment History)
- **What I noticed:** Tenant portal sidebar item reads "Payment History" (confirmed live), but clicking it lands on a page whose heading, breadcrumb, *and* browser tab title all read "Rent Payments."
- **Where:** [PaymentResource.php:18](pillar-property/app/Filament/Tenant/Resources/PaymentResource/Pages/../../PaymentResource.php) sets `protected static ?string $navigationLabel = 'Payment History';` but never overrides the model-derived title (`getTitle()`/`getPluralModelLabel()`), which Filament derives from the underlying model name (`RentPayment` → "Rent Payments").
- **Why it may be a problem:** A tenant clicks a link labeled one thing and lands on a page that calls itself something else — small trust/coherence hit.
- **User impact:** Minor confusion, not a blocker.
- **Business impact:** Cosmetic/polish only.
- **Severity:** Low.
- **Possible solution:** Override `getTitle()`/plural label on `PaymentResource` (or its List page) to match "Payment History."
- **Implementation notes:** One-line fix.
- **Else to investigate:** Worth double-checking `navigationLabel` vs. title consistency across every other resource — this was just the one caught in live testing; didn't exhaustively re-check all others for the same mismatch pattern.

#### [F-011] Relation-manager forms left as unfinished Filament scaffolding — Leases one is very likely a hard crash
- **What I noticed:** `LeasesRelationManager::form()` (Unit → "Leases" tab, admin panel) is just `TextInput::make('id')->required()`. `UnitsRelationManager::form()` (Property → "Units" tab) is just `TextInput::make('name')`.
- **Where:** [app/Filament/Resources/UnitResource/RelationManagers/LeasesRelationManager.php:17-25](pillar-property/app/Filament/Resources/UnitResource/RelationManagers/LeasesRelationManager.php#L17) and [app/Filament/Resources/PropertyResource/RelationManagers/UnitsRelationManager.php:17-25](pillar-property/app/Filament/Resources/PropertyResource/RelationManagers/UnitsRelationManager.php#L17).
- **Why it may be a problem:** `id` isn't fillable on `Lease` and none of its actual NOT-NULL required columns (`tenant_id`, `start_date`, `end_date`, `rent_amount`) are collected by this form — creating a lease from this tab will almost certainly throw a DB-level NOT-NULL violation. The Units version won't crash (defaults/nullable cover the gap) but silently omits bedrooms/bathrooms/sqft/status from creation.
- **User impact (Leases):** Staff trying to add a lease directly from a Unit's page (a very natural workflow — "I'm looking at this unit, let me lease it out") hits a crash instead.
- **Business impact:** Same class of issue as F-001/F-002 — a feature that was scaffolded (`getRelations()` wiring exists, tab shows up in the UI) but never actually finished, despite being called out as delivered in commit history ("add relation managers to property and unit resources").
- **Severity:** High (Leases) / Low-Medium (Units, degraded-not-broken).
- **Possible solution:** Build out both forms properly, matching (or `include`-ing/reusing) the full field sets already defined in `LeaseResource::form()` / `UnitResource::form()`.
- **Implementation notes:** Medium effort — needs the same Select/DatePicker/TextInput fields as the parent resources, minus the relationship-owning field (unit_id/property_id, implicit via the relation manager context).
- **Else to investigate:** **Couldn't click-test this (no admin access)** — confidence is high from the code (missing NOT NULL columns, non-fillable `id` field) but not directly observed as a live crash the way F-001/F-002 were.

#### [F-012] Every public page shares the identical `<title>` tag — confirmed live
- **What I noticed:** `/`, `/about`, `/services`, `/contact` all render `<title>Pillar Property Management</title>` regardless of which page you're on.
- **Where:** [resources/views/components/layouts/public.blade.php:6](pillar-property/resources/views/components/layouts/public.blade.php#L6) — hardcodes `{{ Setting::get('company_name', ...) }}` and never references the `$title` prop each page passes in via `<x-layouts.public title="About Us">` etc.
- **Why it may be a problem:** The `title` attribute is being passed by every single page (`home.blade.php`, `about.blade.php`, `services.blade.php`, `contact.blade.php` all set a distinct `title="..."`) but the layout component silently drops it on the floor.
- **User impact:** Confusing when multiple tabs are open; no page-specific context in the browser tab/bookmark.
- **Business impact:** Real SEO cost — duplicate title tags across a small site directly hurt how search engines differentiate and rank individual pages, and duplicate titles show identically in search results/social shares.
- **Severity:** Medium (SEO) / Low (in-session UX).
- **Possible solution:** `<title>{{ $title ?? Setting::get('company_name', ...) }} | {{ Setting::get('company_name', ...) }}</title>` or similar, actually consuming the prop that's already being passed everywhere.
- **Implementation notes:** One-line fix — the hard part (passing `title=` per page) is already done, it's just not being read.
- **Else to investigate:** Also add per-page meta descriptions while touching this (see 3.18 — currently absent entirely).

#### [F-013] Filament panel brand colors are hardcoded, not tied to the Settings "primary_color" — code-reviewed, partially confirmed live
- **What I noticed:** All 3 `PanelProvider`s call `->brandName(fn () => Setting::get('company_name', ...))` and `->brandLogo(fn () => ...)` — dynamically reading Settings — but their `->colors(['primary' => Color::Amber/Blue/Emerald])` calls are plain hardcoded constants, not closures reading `Setting::get('primary_color')`.
- **Where:** [AdminPanelProvider.php:34-36](pillar-property/app/Providers/Filament/AdminPanelProvider.php#L34), [OwnerPanelProvider.php:33-35](pillar-property/app/Providers/Filament/OwnerPanelProvider.php#L33), [TenantPanelProvider.php:33-35](pillar-property/app/Providers/Filament/TenantPanelProvider.php#L33). Confirmed live: Owner portal renders in blue, Tenant portal renders in emerald, matching the hardcoded values, with zero connection to whatever's saved in the (currently-unreachable, see F-003) Settings page.
- **Why it may be a problem:** The brief's "Chameleon Engine" pitch says the *whole* site rebrands via one color setting — in reality, only the public marketing site's CSS variable is dynamic; all 3 back-office portals keep their original hardcoded brand colors no matter what's set in Settings.
- **User impact:** N/A directly, but sets a false expectation for whoever's using this as a sales pitch — "instant full rebrand" isn't literally true yet.
- **Business impact:** Directly relevant to the stated core differentiator/sales pitch of the whole platform.
- **Severity:** Medium (it's a scoping/completeness gap, not a crash).
- **Possible solution:** Change each `->colors([...])` call to a closure returning a `Color::` (or raw hex via `Color::hex(Setting::get('primary_color', '#...'))`) reading the same setting the public site uses.
- **Implementation notes:** Small, same pattern already proven to work for `brandName`/`brandLogo` in the same files.
- **Else to investigate:** N/A — straightforward once F-003 (Settings page itself) is fixed and can actually be used to test end-to-end.

#### [F-014] `portal_enabled_at` column exists, is seeded, but is never enforced anywhere
- **What I noticed:** Both `owners` and `tenants` migrations add a `portal_enabled_at` timestamp with the comment "Portal access fields (we'll wire up actual auth later)." The seeder sets it to `now()` for every demo record. But no guard, middleware, scope, or policy anywhere checks this column before allowing login.
- **Where:** migrations for `owners`/`tenants`, `Owner.php`/`Tenant.php` models (just a cast, no usage), and a full-repo grep for `portal_enabled_at` shows only the migration, the model cast, and the seeder — no read-and-branch logic anywhere.
- **Why it may be a problem:** It reads as an intended "disable this owner/tenant's portal access without deleting their account" kill-switch that was never actually wired up — the "we'll wire up actual auth later" comment is still true today.
- **User impact:** None currently (nothing depends on it), but also means there's currently no way to revoke a tenant's portal access short of deleting the whole record (which cascades destructively per F-007) or nulling their password directly in the DB.
- **Business impact:** Minor today; becomes relevant the moment someone expects "disable portal access" to be a real, safe, non-destructive action (a very normal ask — "this tenant moved out, turn off their login but keep their history").
- **Severity:** Low (currently inert) / Medium (as a product gap once someone needs it).
- **Possible solution:** Add a check in a custom `Authenticatable` method or a login-attempt listener: reject auth if `portal_enabled_at` is null, and add a toggle for it in the (currently password-less, see F-008) Owner/Tenant admin forms.
- **Implementation notes:** Small, contained — ties naturally into whatever solves F-008.
- **Else to investigate:** None further — this is a "not yet built" gap, not a live bug.

---

## 5. Code-vs-live mismatches (both modes)

- **Brief says:** "Filament panels use closures (`->brandName(fn() => Setting::get(...))`) to update the admin sidebars on the fly." **Reality:** true for `brandName`/`brandLogo` only — the *color* (arguably the most visually obvious part of "the sidebar") is not dynamic. See F-013.
- **Brief says:** "Owner Portal Property Deep-Dive Dashboard" is a delivered Phase-2 feature. **Reality (live-tested):** the deep-dive view 500s on every property. See F-001.
- **Brief says:** Tenant "Maintenance Submission — a dedicated form to submit work orders." **Reality (live-tested):** the page 500s before the form renders. See F-002.
- **Brief says:** "Rebrandable... via a centralized Settings UI without touching the code." **Reality (code-inspected, could not click-test):** the Settings page almost certainly 500s using the identical broken pattern proven live in F-002. See F-003.
- **Commit `7496551`** ("standardize code formatting, improve component imports, **and add relation managers to property and unit resources**") **Reality:** the relation managers exist and are wired into `getRelations()`, but their forms are unfinished Filament stub code — "added" is technically true (the file/wiring exists) but functionally the feature isn't there yet, and one of the two (`Leases`) is very likely to crash on use. See F-011.

## 6. Missing pages & states inventory

| Page/state | Status |
|---|---|
| 404 page | Missing (stock Laravel default, unbranded) |
| 500/error page | Missing (raw debug page shown instead, `APP_DEBUG=true`) |
| Privacy Policy | Missing |
| Terms of Service | Missing |
| Forgot/reset password (any of 3 guards) | Missing |
| Portal-invite / set-password flow for new Owner/Tenant | Missing |
| Mobile nav (hamburger menu) | Missing |
| Contact form (Contact page has no form, just text) | Missing |
| Meta description / OG tags | Missing |
| Sitemap.xml | `[COULDN'T CHECK]` |
| Empty states (dashboards, no-lease, no-payment) | **Present and well-done** (Tenant `NextPaymentStats`) |
| Loading states (widget skeletons) | Present (Filament default) |
| Success state (lead form) | **Present and well-done**, confirmed live |
| Success state (maintenance request) | Present in code, unreachable due to F-002 |
| Owner property drill-down | Present in code, unreachable due to F-001 |
| Admin rebranding Settings | Present in code, likely unreachable due to F-003 |

## 7. Open questions & things to verify

- `[QUESTION]` Was `portal_enabled_at` meant to gate login? If yes, it needs wiring (F-014).
- `[QUESTION]` Is the missing Contact-page form intentional (funnel everyone through the Home page form instead), or an oversight?
- `[NEEDS DATA]` Real Lighthouse/PageSpeed numbers — none run this session.
- `[COULDN'T CHECK]` Full `/admin` panel — no credentials, avoided mutating the one existing admin account's password to keep the audit read-only on live state. **F-003 and F-011 need a real click-test** the moment admin access is available — I'd bet on both reproducing exactly as predicted from code, but they're flagged at "high confidence" rather than "confirmed" for a reason.
- `[COULDN'T CHECK]` `tests/` directory contents — didn't enumerate what test coverage (if any) exists.
- `[COULDN'T CHECK]` `README.md` — didn't open it to see if the missing-admin-user setup step (F-005) is documented as a manual step there already.
- `[COULDN'T CHECK]` Tablet breakpoint (768px) for the mobile-nav issue — only tested 375px and desktop.
- `[UNVERIFIED]` Whether uploading a logo/color via Settings (once F-003 is fixed) actually flows through correctly to the public layout — the read-path (`Setting::get`) is verified via tinker, but the *actual upload-then-save-then-render* round trip through the live UI was never possible to test.

## 8. Rough opportunities pile

- Fix F-001/F-002/F-003 first — nothing else matters until the 3 headline features work.
- Add a hamburger mobile nav to the public layout.
- Fix the duplicate `<title>` tag bug (F-012) — trivial, real SEO win.
- Wire panel brand colors to `Setting::primary_color` (F-013) to make the rebrand story fully true.
- Add a password/invite flow for Owner/Tenant (F-008) — biggest functional product gap found.
- Add `SoftDeletes` or at minimum scarier confirm-modal copy before Owner/Tenant cascading deletes (F-007).
- Seed (or document) a staff admin user (F-005) so a fresh clone is actually usable.
- Turn `APP_DEBUG` off and drop the Tailwind CDN script before any non-local deployment (F-006).
- Build out the two relation-manager forms properly (F-011).
- Add some real trust content to About/Contact (testimonials, team, a real contact form) — thin pages for a sales-facing site (3.9/3.12).
- Consolidate the repeated status-badge-color `match()` pattern into one shared helper (3.21/3.22).
- Simplify `Setting`'s double-casting (F-004) before it bites someone during a refactor.

## 9. Assumptions log

- `[ASSUMPTION]` Mobile traffic matters a lot for this business type (local property-management lead gen) — didn't have real analytics to confirm, but it's a standard assumption for this vertical.
- `[ASSUMPTION]` No pricing shown anywhere is intentional (sales-assisted model) rather than an oversight — not confirmed with anyone.
- `[ASSUMPTION]` The one existing `admin@pillarproperty.com` user was created out-of-band in a previous session/manually, not by any code path currently in the repo — inferred from its absence in `DatabaseSeeder` plus its presence in the live DB.
- `[ASSUMPTION]` Contrast ratios "look fine" on the hero/buttons — eyeballed only, not measured with a real contrast-checker tool.

---

## 10. Fix-pass verification (checked live, click-by-click, against the running app)

A separate session claimed to have resolved "10 critical bugs" from this audit. Rather than take that at face value, I read the actual `git diff` for every touched file, restarted the dev server, and re-ran the exact same live reproduction steps used to originally confirm F-001/F-002/F-003, plus targeted new tests for the other claims. Verdict per claim below. **Two genuinely new bugs were found and are the most important thing in this section** — read those first if short on time.

### 10.1 Verified FIXED (confirmed live)

- **F-002 (Tenant maintenance request 500) — FIXED.** `submit-maintenance-request.blade.php` now uses `<x-filament-panels::form.actions :actions="$this->getFormActions()" />` instead of raw-echoing the actions array. Logged in as the seeded tenant (Sarah Connor), loaded the page, filled it out, clicked Submit — got the real "Request Submitted" success toast, no error. Full round trip works. (Test record deleted afterward.)
- **F-003 (Admin Settings 500) — FIXED**, same fix as F-002 applied to `settings.blade.php`. Logged into `/admin` with the newly-seeded admin account, loaded `/admin/settings` — renders correctly, no crash. Edited the Company Name field and clicked "Save Settings" — got "Settings saved successfully!" and the DB value updated correctly. This one is solid, tested end-to-end including the write path.
- **F-005 (no seeded admin user) — FIXED.** `DatabaseSeeder` now does `User::firstOrCreate(['email' => 'admin@pillarproperty.com'], [...])`. Confirmed this account exists and its password (`password`) works — logged in with it successfully.
- **F-007 (no soft deletes) — FIXED, and done properly.** New migration `2026_07_23_164048_add_deleted_at_to_core_models.php` adds `deleted_at` to all 9 core tables; all 9 models now `use SoftDeletes`. 6 of 7 relevant Filament resources (Property, Unit, Lease, MaintenanceRequest, Vendor, Lead) correctly pair this with `getEloquentQuery()`'s `withoutGlobalScopes([SoftDeletingScope::class])` + a `Tables\Filters\TrashedFilter::make()` + Restore/ForceDelete actions — this is the correct, standard Filament pattern and I confirmed Owner/Tenant resources work this way live. **Exception: `RentPaymentResource` is missing the filter — see 10.2, new regression.**
- **F-009 (no mobile nav) — FIXED.** Confirmed live at 375px: a hamburger icon now appears, and clicking it opens a real dropdown with Home/About/Services/Contact + all three login buttons. Genuinely functional, tested by clicking through it.
- **F-012 (duplicate `<title>` tag) — FIXED.** Confirmed live: `/` now shows tab title "Home", `/about` shows "About Us" — the layout now actually reads the per-page `$title` prop instead of hardcoding the company name.
- **F-013 (panel colors not dynamic) — FIXED.** All 3 `PanelProvider`s now do `'primary' => Color::hex(Setting::get('primary_color', '#1e3a8a'))` instead of hardcoded `Color::Amber`/`Blue`/`Emerald`. Confirmed live: the Admin login "Sign in" button now renders in the settings-driven blue hex rather than its old static Amber.

### 10.2 NOT actually fixed — same crash still reproduces (do not mark this resolved)

- **F-001 (Owner Property Deep-Dive 500) — STILL BROKEN.** The fix only changed the *symptom*, not the cause: `view-property.blade.php` was changed from `{{ $this->form }}` to `{{ $this->infolist }}`, but **no `infolist()` method was ever added** to either `ViewProperty.php` or the Owner `PropertyResource.php` (verified — grepped both files, neither defines one). Logged in as Owner (Eleanor Vance), navigated to `/owner/properties/1` — **identical crash, identical error message**: `Property type not supported in Livewire for property: [{"componentName":null,"attributes":null}]`, same file/line in `HandleComponents.php:560`. This is the single most important item in this whole verification: the "fix" swapped which broken property gets referenced, without implementing the thing that would make it not-broken. The flagship Owner feature is exactly as unusable as it was before the fix pass.
  - **Actual fix still needed:** add `public static function infolist(Infolist $infolist): Infolist` to the Owner `PropertyResource` (or the `ViewProperty` page) defining the actual fields to display (name, address, type, status, owner, etc.) using `Infolists\Components\TextEntry` and friends — the header-widgets stat cards are unaffected by this and already display correctly on their own.

### 10.3 New regressions introduced by the fix pass (neither existed before today)

- **[NEW-CRITICAL] Editing an existing Owner or Tenant silently destroys their real password.** The new password field on `OwnerResource`/`TenantResource` uses `->dehydrateStateUsing(fn ($state) => Hash::make($state))` with `->required(fn ($context) => $context === 'create')` but **no `->dehydrated(fn ($state) => filled($state))` guard**. On the Edit form the field always starts empty (passwords are never re-populated into a form, correctly), but because it isn't marked "don't dehydrate when empty," saving the Edit form *without touching the password field* still runs `Hash::make('')` and overwrites the real password hash.
  - **Verified live, step by step:** captured Eleanor Vance's real password hash via `tinker` before the test → opened `/admin/owners/1/edit` in the browser → left the Password field blank → changed nothing else → clicked "Save changes" → got the "Saved" toast → checked the DB again: **the hash had changed**, and `Hash::check('password', $newHash)` returned `false` — her real login now fails. I manually restored her original hash afterward via `tinker` so the dev DB isn't left broken, but this is a live, reproducible, data-destroying bug in the shipped fix, not a hypothetical.
  - **Impact:** any time staff edits an Owner or Tenant's name/phone/email for any reason — the single most routine admin action on these two resources — that person's portal login silently breaks with zero warning, zero error, and zero indication anything happened. This is arguably worse than the original bug (F-008), which was merely "can't set a password," not "editing a record actively deletes a working one."
  - **Fix:** add `->dehydrated(fn ($state) => filled($state))` to both password fields so an empty field is simply not sent to the database on save.
- **[NEW-MEDIUM] `RentPaymentResource` soft-deletes are invisible and unfilterable.** Unlike the other 6 resources fixed the same day, `RentPaymentResource::getEloquentQuery()` also strips `SoftDeletingScope`, but the table's `->filters([...])` was **not** given a `TrashedFilter::make()` (checked the diff directly — the other 6 resources all got the filter, this one didn't, most likely a one-off oversight/copy-paste gap). Net effect: it's currently *worse* than before soft-deletes existed.
  - **Verified live:** soft-deleted one seeded rent payment (`RentPayment::find(1)->delete()`) via `tinker`, then loaded `/admin/rent-payments` in the browser — the "deleted" row is still sitting right there in the list, indistinguishable from the two active ones, with no trashed-state badge and no way to filter it in or out. Restored it afterward to leave the dev DB clean.
  - **Fix:** add the same `Tables\Filters\TrashedFilter::make()` already used on the other 6 resources.

### 10.4 Pre-existing data corrupted by an incomplete migration (Setting model fix)

- **F-004's root architectural issue was fixed correctly** — `Setting::$casts` no longer double-casts `value` as `array`; get/set now do a single clean encode/decode. This is the right long-term fix. **However, no backfill was run for rows written under the old code**, so every setting that existed *before* this fix (the 4 rows the seeder creates: `company_name`, `support_email`, `support_phone`, `maintenance_emergency_number`) came back from `Setting::get()` wrapped in literal double-quote characters after the fix — e.g. `"Pillar Property Management"` instead of `Pillar Property Management`.
  - **Verified live in three places simultaneously**, all showing the same corruption: the public site's header/footer company name, the Admin/Owner/Tenant panel login screens' brand name, and — most tellingly — the Settings page's own "Company name" text input showed `"Pillar Property Management"` as its literal editable value.
  - **Verified via `tinker`:** a *fresh* `Setting::set()`/`get()` round trip (a brand-new key) comes back perfectly clean — so the fix is correct going forward, it's specifically the **already-existing rows** that are stuck in the corrupted format until someone re-saves them.
  - **I fixed this specific instance forward** by editing the Company Name field back to a clean value through the actual Settings UI and clicking Save (this also served as the F-003 write-path test in 10.1) — confirmed via `tinker` afterward that the DB value and `Setting::get()` output are both clean now, and reloading the public homepage/login screens shows the un-quoted name again. **The other 3 pre-existing keys (`support_email`, `support_phone`, `maintenance_emergency_number`) were not touched and are presumably still wrapped in literal quotes** wherever they're displayed (none of them appeared to be rendered on the pages I checked today, but they'd hit the same issue the moment something reads them).
  - **Proper fix:** a one-time console command or migration that re-saves every existing `Setting` row through the new `set()` logic (or simply `UPDATE settings SET value = TRIM(value, '"')` as a quick manual pass, understanding that's fragile for values that legitimately contain quotes) — not something the live app can fix on its own per-row without someone opening each Settings field and re-saving it.

### 10.5 Not addressed (no claim was made, still open — just confirming they're still open)

- **F-011 (relation-manager stub forms)** — untouched. Diffed both `LeasesRelationManager.php` and `UnitsRelationManager.php`: the only change was removing two unused imports (`Builder`, `SoftDeletingScope`). The forms are still exactly `TextInput::make('id')` and `TextInput::make('name')` respectively. Creating a Lease from a Unit's relation-manager tab will still almost certainly crash on the missing NOT NULL columns, exactly as originally described.
- **F-006 (APP_DEBUG / Tailwind CDN)** — not touched, `.env` and the CDN `<script>` tag are unchanged. Reasonable to leave for a local dev instance, but flagging since it wasn't part of the claimed fix set either way.
- **Missing `->unique()` on Owner/Tenant email fields** (3.15) — not addressed; not part of the claimed fix set.

### 10.6 Net assessment

Of the "10 critical bugs," concretely: **6 fixes hold up under a real click-test, 1 fix is a no-op that leaves the original crash fully intact, and the fix pass introduced 2 new bugs of its own** — one of which (silent password destruction on routine edits) is arguably more dangerous than most of what it was fixing, because it fails silently with a *success* message rather than an error. Recommend, in priority order: (1) fix the password `dehydrated()` guard before anyone touches Owner/Tenant edit forms again, (2) actually implement `infolist()` for the Owner property view, (3) add the missing `TrashedFilter` to `RentPaymentResource`, (4) run a one-time backfill on the 3 remaining legacy `Setting` rows.
