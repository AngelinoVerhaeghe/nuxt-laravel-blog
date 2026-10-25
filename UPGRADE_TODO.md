# Upgrade TODO

Package audit of 2026-10-25. Locked versions come from `composer.lock` / `package-lock.json`, latest versions from Packagist / npm.

Local toolchain: PHP 8.3 / 8.4 / 8.5 side by side (ondrej PPA), Composer 2.9, Node 24, npm 11.

**PHP target: 8.5.** Done in step 2: `composer.json` requires `^8.5` and CI runs on 8.5. Make 8.5 your default CLI with `sudo update-alternatives --set php /usr/bin/php8.5` (the default `php` is 8.4 right now).

## Backend (Laravel): installed vs. latest

| Package | Locked | Latest | Gap |
|---|---|---|---|
| laravel/framework | 10.28 | **13.38** (needs PHP 8.3) | 3 majors |
| filament/filament (+ media-library plugin) | 3.0.73 / 3.0.0 → **4.15** | **5.10** | 2 majors |
| livewire/livewire (comes with Filament) | 3.0.7 | 4.4 | major |
| laravel/sanctum | 3.3 | 4.3 | major |
| laravel/breeze | 1.25 | 2.4 | major (replaced by starter kits) |
| laravel/tinker | 2.8 | 3.0 | major |
| spatie/laravel-permission | 5.11 | 8.3 | 3 majors |
| spatie/laravel-medialibrary | 10.13 | 11.23 | major |
| althinect/filament-spatie-roles-permissions | 2.0 | 4.0 (Filament 4/5) | major |
| pxlrbt/filament-excel | 2.1 | 3.6 / 4.1 (Filament 4/5) | major |
| pxlrbt/filament-spotlight | 1.0 | 2.1 | major |
| nunomaduro/collision | 7.9 | 8.9 | major |
| phpunit/phpunit | 10.4 | 13.4 (needs PHP 8.4+, fine with the 8.5 target) | major |
| spatie/laravel-ignition | 2.3 | 2.12 | minor |
| pint / sail / mockery / faker / guzzle | old minors | current | minor |

## Frontend (Nuxt): installed vs. latest

| Package | Locked | Latest | Notes |
|---|---|---|---|
| nuxt | 3.7.2 | **4.6** | major (new `app/` folder layout) |
| vue | 3.3 | 3.5 | minor |
| @nuxt/content | 2.8 | 3.16 | major rewrite, and **barely used** |
| @nuxt/image | 1.0.0-rc.1 | 2.2 | major |
| nuxt-icon | 0.5 | renamed to **@nuxt/icon** 2.6 | swap the package |
| @nuxt/devtools | `latest` (0.8.3) | built into Nuxt 4 | remove |
| tailwindcss | 3.3 | **4.3** | major (config moves into CSS) |
| flowbite | 1.8 | 4.0 | **not used anywhere** in the templates |
| axios | 1.5 | 1.20 | **not used**, the code uses `useFetch` |
| @types/node | 18 | match Node 24 | |
| @headlessui/vue, @heroicons/vue, typography, postcss, autoprefixer | | minor bumps | autoprefixer isn't needed with Tailwind 4 |

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
- [ ] **L12 → 13.** Bump to `phpunit ^13` (possible now that we're on PHP 8.5).

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
- [ ] **Filament 4 → 5.** This is mainly the move to Livewire 4. Bump everything Filament-related to `^5`, then run `php artisan filament:upgrade`.

### 3. Frontend: Nuxt 3.7 → 4
- [ ] First bump to the latest Nuxt 3.x and set `future: { compatibilityVersion: 4 }`, then move to `nuxt@^4`.
- [ ] Move `pages/`, `components/`, `assets/`, `app.vue` into `app/`. Run `npx codemod@latest nuxt/4/migration-recipe` or move them by hand.
- [ ] Remove `@nuxt/devtools` from devDependencies. It's pinned to `latest`, and Nuxt 4 already includes it.
- [ ] Replace `nuxt-icon` with `@nuxt/icon`. `<Icon>` keeps working, so only the module name changes.
- [ ] `@nuxt/image` → `^2`. Also fix the `alt="{{ post.title }}"` bug in `components/PostCardApi.vue:14` and `pages/blog/[slug].vue:16`. Vue doesn't interpolate inside attributes, so it has to be `:alt="post.title"`.
- [ ] Replace `import.meta.env.VITE_API_BASE_URL` with `runtimeConfig.public.apiBase` (env var `NUXT_PUBLIC_API_BASE`) in the 3 pages that use `useFetch`.
- [ ] `@types/node` → `^24`.

### 4. Frontend: decide on the unused packages
- [ ] **@nuxt/content:** the blog pulls its posts from the Laravel API. Content is only used by `content/blog/palia.md`, `components/content/Callout.vue` and `PostCardMarkdown.vue`, which isn't used anywhere.
  - Either remove it, along with `backend/docs/Nuxt-Content.md`,
  - or migrate to v3 (`content.config.ts` collections, `queryCollection`, highlight config moves to `content.build.markdown.highlight`).
- [ ] **flowbite:** no Flowbite components or `data-*` attributes are used. Remove it, along with its plugin and `content` path in `tailwind.config.js`.
- [ ] **axios:** not imported anywhere. Remove it.

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
