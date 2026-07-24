# Pillar Property Management

A rebrandable, multi-panel property management platform built on Laravel + Filament. It's designed as a reusable template: a public marketing/lead site, a staff admin back office, and separate self-service portals for property owners and tenants, all skinnable from one Settings screen.

This README is written for whoever picks this codebase up next — including future-you. If something below is out of date, fix the doc, not just the code.

## Stack

- **Laravel 13** / PHP 8.3+
- **Filament 3** — powers all three authenticated panels (Admin, Owner, Tenant)
- **Livewire 3 + Alpine.js** — the public site's interactive bits (contact form, mobile nav, FAQ accordion)
- **Tailwind CSS v4** via the Vite plugin — content-scanned automatically, no CDN script, no manual `content: []` config
- **SQLite** by default (`database/database.sqlite`) — fine for local dev/demo, swap `DB_CONNECTION` for anything else in production

## Getting started

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

`composer setup` runs all of the above except the last two lines. **The `--seed` flag matters** — without it you get a fully-populated demo database but *no staff login*, because the one `User` record (the `/admin` account) only gets created by the seeder, not by any part of the base Laravel installer.

Seeded logins (all local demo data, password `password` for everyone except the admin, which is documented here because it's also `password` — **change these before ever exposing this anywhere but localhost**):

| Panel | URL | Email |
|---|---|---|
| Staff Admin | `/admin` | `admin@pillarproperty.com` |
| Owner Portal | `/owner` | `eleanor@vanceholdings.com` (or `marcus@brodyrealestate.com`) |
| Tenant Portal | `/tenant` | `sarah@skynet-resistance.org` (or `john.doe@example.com`, `alice@example.com`) |

If you ever change frontend classes/templates and the site looks unstyled or missing spacing after a fresh pull, run `npm run build` — Tailwind v4's Vite plugin only picks up classes that exist in already-built output; a stale `public/build` bundle from before your template changes will visibly break spacing/layout utilities that are only used in the new markup.

## The four "apps" in one Laravel app

| Surface | Path | Guard | Who |
|---|---|---|---|
| Public marketing site | `/` | none | prospective owners/tenants, SEO/lead-gen |
| Staff Admin | `/admin` | `web` (`User` model) | your team — full CRUD over everything |
| Owner Portal | `/owner` | `owner` (`Owner` model) | property owners — scoped to their own properties |
| Tenant Portal | `/tenant` | `tenant` (`Tenant` model) | tenants — scoped to their own active lease |

Each panel is its own `Filament\PanelProvider` in `app/Providers/Filament/`, with its own resources under `app/Filament/{Admin implicit,Owner,Tenant}/`. **Owner- and tenant-scoped resources must override `getEloquentQuery()`** to filter by the logged-in guard's user (see `app/Filament/Owner/Resources/PropertyResource.php` for the pattern) — there's no global guardrail enforcing this, so a new resource that forgets this override will leak other people's data. If you add a new Owner/Tenant resource, copy that pattern deliberately.

## The branding / "Settings" system

`App\Models\Setting` is a simple key/value store (`app/Models/Setting.php`) read through `Setting::get($key, $default)` and written through `Setting::set($key, $value, $group, $type)`, cached for an hour per key. Staff manage it at `/admin/settings` (`app/Filament/Pages/Settings.php`).

- **`company_name`, `logo`, `favicon`, `primary_color`** drive the public site's `<title>`, header/footer branding, and the `--brand-primary` CSS variable — and also the `brandName`/`brandLogo`/primary color on all three Filament panels (each `PanelProvider` reads the same `Setting::get('primary_color')`). Changing the color in one place re-themes the whole product.
- **`contact_phone`, `contact_email`** show up in the public footer and Contact page.
- If you write to a `Setting` row any way *other than* `Setting::set()` (a raw `DB::table('settings')->insert()`, a manual seeder, editing a row directly in a DB browser), make sure the stored `value` matches what `Setting::get()` expects to read back given that row's `type` column — there's no cast doing this for you anymore, it's a plain string column with manual type coercion in `get()`/`set()`. Always go through `Setting::set()`.

## Soft deletes

Every core domain model (`Owner`, `Property`, `Unit`, `Tenant`, `Lease`, `RentPayment`, `MaintenanceRequest`, `Vendor`, `Lead`, `ContactMessage`) uses `SoftDeletes`. The pattern for a Filament resource to support this correctly is:

```php
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
}
```

paired with `Tables\Filters\TrashedFilter::make()` in the table's `->filters([...])` and `RestoreAction`/`ForceDeleteAction` in `->actions([...])`. **Both halves are required together** — removing the global scope without adding the `TrashedFilter` means soft-deleted records stay visible in the list forever with no way to tell them apart from active ones (this exact mismatch was a real bug in `RentPaymentResource` at one point — if you add a new soft-deletable resource, copy the *whole* pattern from an existing one like `OwnerResource`, not just half of it).

## Password fields on Owner / Tenant resources

`OwnerResource` and `TenantResource` forms include a password field that must keep this exact shape:

```php
Forms\Components\TextInput::make('password')
    ->password()
    ->dehydrated(fn ($state) => filled($state))
    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
    ->required(fn (string $context): bool => $context === 'create')
    ->maxLength(255),
```

The `->dehydrated(fn ($state) => filled($state))` line is not optional decoration — without it, saving an *Edit* form with the password field left blank (the normal case: editing someone's phone number, not their password) silently overwrites their real password hash with a hash of an empty string, locking them out with no error and no warning. This was a real, shipped bug once. Don't remove that line.

## Directory map

```text
app/
├── Console/Commands/
│   └── GenerateRentInvoices.php      # `php artisan rent:generate` — monthly rent invoice generation
├── Filament/
│   ├── Pages/Settings.php            # the branding/Settings UI
│   ├── Owner/                        # Owner Portal: Pages, Resources, Widgets — all scoped to auth()->guard('owner')
│   ├── Tenant/                       # Tenant Portal: Pages, Resources, Widgets — all scoped to auth()->guard('tenant')
│   └── Resources/                    # Staff Admin: full CRUD for every domain model
├── Http/Controllers/
│   └── PageController.php            # public site routes (home/about/services/contact/properties/blog/faq)
├── Livewire/
│   ├── RequestRentalAnalysis.php     # public "free rental analysis" lead form → Lead model (see note below)
│   └── ContactForm.php               # public Contact page form → ContactMessage model
├── Models/                           # Eloquent models
└── Providers/Filament/
    ├── AdminPanelProvider.php
    ├── OwnerPanelProvider.php        # guard: 'owner'
    └── TenantPanelProvider.php       # guard: 'tenant'

resources/views/
├── components/layouts/public.blade.php   # shared public-site layout: SEO/OG tags, header/footer, mobile nav
├── livewire/                              # public Livewire component views
└── pages/                                 # Home, About, Services, Properties, Blog, FAQ, Contact
```

**Note on `RequestRentalAnalysis`:** this component (and the `Lead` model/admin resource behind it) was the original public lead-capture flow. During the site's visual redesign it stopped being referenced from any page — the component, its Blade view, the `leads` table, and `LeadResource` in `/admin` are all still fully functional, they're just currently orphaned. If the intent is to bring lead capture back, wire `<livewire:request-rental-analysis />` into a page again rather than rebuilding it from scratch.

## Testing

```bash
php artisan test
```

Feature tests for the public routes live in `tests/Feature/PageControllerTest.php`. There's no coverage yet for the Filament panels themselves — if you fix a bug in one of them, consider adding a `get('/owner/properties/{id}')->assertOk()`-style test alongside the fix so it can't silently regress.

## Deploying anywhere other than localhost

- Set `APP_DEBUG=false` and a real `APP_ENV` — local dev intentionally runs with debug on, which dumps SQL queries, session cookies, and file paths on any unhandled error.
- Change every seeded demo password before the instance is reachable by anyone but you.
- `.env` is git-ignored on purpose; each environment (including a fresh clone of this repo for a new client) needs its own.
