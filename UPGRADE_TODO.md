# Upgrade TODO

Package audit of 2026-10-25. Locked versions come from `composer.lock` / `package-lock.json`, latest versions from Packagist / npm.

Local toolchain: PHP 8.3 / 8.4 / 8.5 side by side (ondrej PPA), Composer 2.9, Node 24, npm 11.

**PHP target: 8.5.** Done in step 2: `composer.json` requires `^8.5` and CI runs on 8.5. Make 8.5 your default CLI with `sudo update-alternatives --set php /usr/bin/php8.5` (the default `php` is 8.4 right now).

## Backend (Laravel): before vs. now

All backend packages are on their latest major. ✅

| Package | Before (audit) | Now |
|---|---|---|
| laravel/framework | 10.28 | **13.38** |
| filament/filament (+ media-library plugin) | 3.0.73 / 3.0.0 | **5.10** |
| livewire/livewire (comes with Filament) | 3.0.7 | **4.4** |
| laravel/sanctum | 3.3 | **4.3** |
| laravel/breeze | 1.25 | removed |
| laravel/tinker | 2.8 | **3.0** |
| spatie/laravel-permission | 5.11 | **8.3** |
| spatie/laravel-medialibrary | 10.13 | **11.23** |
| althinect/filament-spatie-roles-permissions | 2.0 | **4.0** |
| pxlrbt/filament-excel | 2.1 | **4.1** |
| pxlrbt/filament-spotlight | 1.0 | **2.1** |
| nunomaduro/collision | 7.9 | **8.9** |
| phpunit/phpunit | 10.4 | **13.4** |
| spatie/laravel-ignition | 2.3 | **2.12** |
| pint / sail / guzzle | 1.13 / 1.25 / 7.8 | 1.32 / 1.69 / 7.16 |

## Frontend (Nuxt): before vs. now

| Package | Before (audit) | Now |
|---|---|---|
| nuxt | 3.7.2 | **4.6** |
| vue | 3.3 | **3.5** |
| @nuxt/content | 2.8 | **3.16** |
| @nuxt/image | 1.0.0-rc.1 | **2.2** |
| nuxt-icon | 0.5 | replaced by **@nuxt/icon 2.6** (+ `@iconify-json/uil`) |
| @nuxt/devtools | `latest` (0.8.3) | removed (built into Nuxt 4) |
| flowbite | 1.8 | removed (unused) |
| axios | 1.5 | removed (unused) |
| @types/node | 18 | **24** |
| @headlessui/vue, @heroicons/vue | 1.7.16 / 2.0.18 | **1.7.23 / 2.2** |
| tailwindcss | 3.3 | 3.3 → **4.3** in step 5 |
| typography, postcss, autoprefixer | | step 5 (autoprefixer isn't needed with Tailwind 4) |

---

## TODOs

**Recommended order:** 0 → 1 → 2 → 3/4 → 5. Backend and frontend are separate, so they can go in two PRs. Doing step 4 before step 5 makes the Tailwind migration smaller.

### 0. Prep
- [x] Create a branch: `feature/NLB-0/upgrade-prep`.
- [x] Run `composer install` and `npm ci` on the current lock files and check that both apps still run, so there is a working starting point.
  - Needed `ext-intl` (Filament) and an update of `ezyang/htmlpurifier` 4.16 → 4.19.1, because the locked version refused PHP 8.3.
- [x] Add smoke tests before upgrading. Tests now run on in-memory SQLite (`phpunit.xml`).
  - API: `tests/Feature/Api/PostApiTest.php`, `ProductApiTest.php` (list, show by slug, 404)
  - Filament: `tests/Feature/Filament/PanelSmokeTest.php` (login, dashboard, all 11 resource index pages)
- [x] Add a GitHub Actions workflow (`.github/workflows/ci.yml`) that runs `php artisan test` and `nuxt build`. Bump its `php-version` when switching to 8.5.

### 1. Backend: Laravel 10 → 12 → 13
Laravel 11 was skipped: Composer 2.9 blocks every 11.x release because of security advisories that won't be patched (11 is end-of-life). The upgrade guides for 11 and 12 were applied in one step instead.

- [x] **L10 → 12** (branch `feature/NLB-0/laravel-12`). Now on Laravel 12.70, Filament 3.3, Livewire 3.8, Carbon 3.
  - Bumped `sanctum ^4`, `tinker ^2.9`, `collision ^8.1`, `phpunit ^11.5`, `ignition ^2.4`, `sail ^1.26`, Filament + media-library plugin `^3.2`. That pulled in `spatie/laravel-permission` 6 and `spatie/laravel-medialibrary` 11.
  - Removed `laravel/breeze`. The generated auth code in `routes/auth.php` and `Controllers/Auth` stays.
  - Pinned `althinect/filament-spatie-roles-permissions` to **2.3.2**: 2.3.3 claims Filament 3 support but uses the Filament 4 `getNavigationGroup()` signature and crashes. Unpin in step 2.
  - Replaced the published permission migration with the v6 version. Same schema; v6 removed the static `PermissionRegistrar::$pivot*` properties it used.
  - `UserFactory`: replaced the hard-coded bcrypt hash with `Hash::make()`. Laravel 11+ rejects hashes whose cost doesn't match the config.
  - Sanctum 4: migration was already published; renamed the config keys to `validate_csrf_token` + `authenticate_session`.
  - Medialibrary 11: no changes needed (no custom conversions). Laravel 12 no longer needs `doctrine/dbal`.
  - Kept the Laravel 10 app structure (still supported).
- [ ] Optional: switch to the slim app structure. This means removing `app/Http/Kernel.php`, `app/Console/Kernel.php`, `Exceptions/Handler.php`, the 9 default middleware classes and most of the providers, and moving their config into `bootstrap/app.php`.
- [x] Switch to PHP 8.5: done in step 2 (branch `feature/NLB-0/filament-4`).
- [x] **L12 → 13** (branch `feature/NLB/laravel-13`). Now on Laravel 13.38, Tinker 3, PHPUnit 13 (Symfony 8).
  - CSRF middleware renamed to `PreventRequestForgery` (it now also checks the `Sec-Fetch-Site` header): `App\Http\Middleware\VerifyCsrfToken` now extends it, and the Filament panel middleware uses it.
  - Cache prefix / session cookie name: unchanged, because `config/cache.php` and `config/session.php` set them explicitly.
  - Cache `serializable_classes` (now `false` by default): checked that the spatie permission cache still works.
  - The other changes in the upgrade guide don't apply (no upsert, polymorphic pivots, custom cache stores, queues or domain routes).

### 2. Backend: Filament 3 → 4 → 5
- [x] **Filament 3 → 4** (branch `feature/NLB-0/filament-4`). Now on Filament 4.15.
  - Ran `vendor/bin/filament-v4 app` (Rector) on 41 files: `Form` → `Schema`, layout components → `Filament\Schemas\Components`, table actions → `Filament\Actions`, `->recordActions()` / `->toolbarActions()`, `Get`/`Set` → `Filament\Schemas\Components\Utilities`, new property types on resources and widgets. Reverted its unrelated import-only changes in `app/Http`.
  - Manual: replaced `BooleanColumn` with `IconColumn::boolean()`, `->reactive()` with `->live()`, removed unused `Card`/`Get` imports.
  - Plugins: `althinect/...-roles-permissions ^4` (pulls `spatie/laravel-permission` 6 → 8; none of the v7/v8 breaking changes apply), `pxlrbt/filament-spotlight ^2`, media-library plugin `^4`.
  - `pxlrbt/filament-excel ^4` instead of 3: only v4 allows `maatwebsite/excel` 4 → `phpspreadsheet` 5, which supports PHP 8.5.
  - PHP 8.5: `"php": "^8.5"`, CI on 8.5, `config/database.php` uses `Pdo\Mysql::ATTR_SSL_CA` (the old `PDO::MYSQL_ATTR_SSL_CA` gave a deprecation on every DB connection).
  - Smoke tests now also render all 11 create forms and the post edit page (41 tests).
  - Removed `filament/upgrade` again after use.
- [x] Check the widgets: the dashboard smoke test renders all 4.
- [ ] Check `AdminPanelProvider`: `->font('Dunbar')`, the empty `->favicon('')`, and the empty `Settings` `->url('')` user menu item.
- [x] **Filament 4 → 5** (branch `feature/NLB/filament-5`). Now on Filament 5.10 and Livewire 4.4.
  - `vendor/bin/filament-v5 app` found nothing to change. Bumped `filament/filament` and the media-library plugin to `^5`; the other plugins (roles-permissions 4, filament-excel 4, spotlight 2) already support 5.
  - The app has no own Livewire config, components or `wire:` markup, so the Livewire 4 breaking changes don't apply.
  - Removed `filament/upgrade` again after use.

### 3 + 4. Frontend: Nuxt 3.7 → 4 and unused packages
- [x] **Nuxt 4** (branch `feature/NLB/nuxt-4`). Now on Nuxt 4.6, Content 3, Image 2, Icon 2.
  - Moved `app.vue`, `assets/`, `components/`, `pages/` into `app/` (`content/`, `public/`, `server/` stay at the root). Updated the Tailwind `content` paths.
  - Removed `axios`, `flowbite` (+ its Tailwind plugin) and `@nuxt/devtools`; replaced `nuxt-icon` with `@nuxt/icon` and installed `@iconify-json/uil` so the footer icons render on the server too.
  - API URL: `runtimeConfig.public.apiBase` (override with `NUXT_PUBLIC_API_BASE`) instead of `import.meta.env.VITE_API_BASE_URL`. CI no longer needs the env var.
  - Nuxt 4 `pending` is no longer `true` on the server for `server: false` fetches, which caused hydration mismatches. The pages now check `status === 'idle' || status === 'pending'`, and `blog/[slug].vue` only renders the post once it's loaded.
  - Bug fixes found on the way: `:alt="post.title"` instead of `alt="{{ post.title }}"`; no `<nuxt-img>` for posts without an image (it requested `/null`); `@headlessui/vue` 1.7.23 fixes the menu's hydration ID mismatch.
- [x] **@nuxt/content 2 → 3** (kept, not removed).
  - `content.config.ts` defines a `blog` collection (`content/blog/**/*.md`) with the frontmatter fields; highlighting moved to `content.build.markdown.highlight`; uses Node's built-in SQLite (`experimental.sqliteConnector: 'native'`).
  - `PostCardMarkdown.vue`: `post._path` → `post.path`.
  - `Callout.vue`: slot wrapper `<p>` → `<div>` (markdown text is already a `<p>`, and `<p>` in `<p>` broke hydration).
  - `palia.md`: fixed the image path to `/images/blog/image.png`.
  - Verified with a temporary page (not committed): list query, frontmatter, `nord` highlighting and the `::callout` component all render.
- [ ] No page renders the markdown posts yet (the `/blog/[slug]` route uses the Laravel API). Add one if markdown posts should be public, e.g. `app/pages/articles/[...slug].vue` with `queryCollection('blog')`. Note: the collection paths are `/blog/...`, so a new route needs its own prefix or `.path()` mapping.
- [ ] Update `backend/docs/Nuxt-Content.md`: it still shows the v2 API (`queryContent`, `ContentDoc`).
- [ ] Add `"type": "module"` to `package.json` after step 5. Node warns about it now, but `tailwind.config.js` still uses `module.exports`.

### 5. Frontend: Tailwind 3 → 4
- [ ] Run `npx @tailwindcss/upgrade`.
- [ ] Switch to `@tailwindcss/vite`. Remove the `postcss` block and `autoprefixer`.
- [ ] In `main.css`, replace the three `@tailwind` lines with `@import "tailwindcss"` and `@plugin "@tailwindcss/typography"`. The font families move into `@theme`.
- [ ] Delete `tailwind.config.js`.
- [ ] Check the pages visually. Default border and ring colors and some shadow and rounded sizes changed in v4.

### 6. Finish
- [ ] Bump the minor versions (`@headlessui/vue`, `@heroicons/vue`, `vue`, pint, sail, etc.).
- [ ] Commit the new `composer.lock` and `package-lock.json`, then run the tests and do `nuxt build` + `nuxt preview` on the blog and the dashboard.
- [ ] Update both READMEs with the new requirements (PHP 8.3+, Node 22+).
- [ ] Optional: add `.nvmrc` and set `"config.platform.php"` in composer.json so the versions are pinned.
