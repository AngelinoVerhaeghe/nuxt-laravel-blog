# Upgrade TODO

Package audit of 2026-10-25. Locked versions come from `composer.lock` / `package-lock.json`, latest versions from Packagist / npm.

Local toolchain: PHP 8.3.6, Composer 2.9, Node 24, npm 11.

## Backend (Laravel): installed vs. latest

| Package | Locked | Latest | Gap |
|---|---|---|---|
| laravel/framework | 10.28 | **13.38** (needs PHP 8.3) | 3 majors |
| filament/filament (+ media-library plugin) | 3.0.73 / 3.0.0 | **5.10** | 2 majors |
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
| phpunit/phpunit | 10.4 | 13.4 (**needs PHP 8.4**), so use 12.x | major |
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
- [ ] Create a branch, e.g. `feature/NLB-x/upgrade-stack`.
- [ ] Run `composer install` and `npm ci` on the current lock files and check that both apps still run, so there is a working starting point.
- [ ] Add a few smoke tests before upgrading. Right now there are only the example tests and the Breeze auth tests. Cover:
  - API: `GET /api/v1/posts`, `/posts/{slug}`, `/products`, `/products/{slug}`
  - Filament: the login page and each resource index page load
- [ ] Optional: add a GitHub Actions workflow that runs `php artisan test` and `nuxt build`.

### 1. Backend: Laravel 10 → 11 → 12 → 13 (one major at a time)
- [ ] **L10 → 11.** The biggest step. Use Laravel Shift or follow the upgrade guide by hand.
  - Bump `sanctum ^4`, `collision ^8`, `phpunit ^11`, `spatie/laravel-permission ^6`, `spatie/laravel-medialibrary ^11`.
  - Optional: switch to the slim app structure. This means removing `app/Http/Kernel.php`, `app/Console/Kernel.php`, `Exceptions/Handler.php`, the 9 default middleware classes and most of the providers, and moving their config into `bootstrap/app.php`.
  - Sanctum 4: publish its migrations (Laravel 11 no longer loads them automatically). The API routes need `install:api` or registering in `bootstrap/app.php` if going slim.
  - Medialibrary 11: check whether the `media` table migration needs changes.
  - Laravel 11 no longer needs `doctrine/dbal`, so it should drop out of the lock file.
- [ ] **L11 → 12.** Small step. Bump `tinker`, and update `spatie/laravel-permission` and medialibrary to versions that support 12.
- [ ] **L12 → 13.** Requires PHP 8.3 or newer. Use `phpunit ^12`, not 13, unless moving to PHP 8.4. Set the root `"php"` constraint to `^8.3`.
- [ ] Remove `laravel/breeze`. It's only a scaffolding tool, the generated auth code is already in `routes/auth.php` and `Controllers/Auth`, and Breeze is deprecated.

### 2. Backend: Filament 3 → 4 → 5
Filament 4 needs Laravel 11.28 or newer, so do this after step 1, or in between the Laravel steps.
- [ ] Run the official upgrade script: `composer require filament/upgrade:"^4.0" --dev` → `vendor/bin/filament-v4`. It rewrites most namespaces automatically.
- [ ] Expect manual fixes in all 9 resources:
  - `Tables\Actions\*` (View/Edit/Delete/BulkActionGroup/CreateAction) move to `Filament\Actions\*`.
  - `->actions()` / `->bulkActions()` become `->recordActions()` / `->toolbarActions()`.
  - `form(Form $form)` becomes `form(Schema $schema)`. `Section`, `Group` and `Card` move to `Filament\Schemas\Components`.
  - Remove `Card` and `BooleanColumn`, which were already deprecated in v3. Use `Section` and `IconColumn::boolean()` instead.
  - `Get`/`Set` in the `afterStateUpdated` closures (Tag, Product, Category) get new namespaces.
- [ ] Update the panel plugins: `althinect/...-roles-permissions ^4`, `pxlrbt/filament-excel ^3`, `pxlrbt/filament-spotlight ^2`, `filament/spatie-laravel-media-library-plugin` to the same version as Filament.
- [ ] Check `AdminPanelProvider`: `->font('Dunbar')`, the empty `->favicon('')`, and the empty `Settings` `->url('')` user menu item.
- [ ] Check the widgets. The chart and stat widget APIs changed slightly.
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
