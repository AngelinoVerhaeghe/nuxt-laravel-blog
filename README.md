# Codewave blog

A blog with an admin panel. Posts are written in a Laravel + Filament admin panel and shown on a Nuxt site, which can also publish posts written as markdown files.

| Part | Stack | Local URL |
|---|---|---|
| [`backend/`](backend) | Laravel 13 (PHP 8.5), Filament 5 admin panel, MySQL 8 | API `http://localhost:8000/api/v1`, admin `http://localhost:8000/dashboard` |
| [`frontend/`](frontend) | Nuxt 4, Nuxt Content 3, Tailwind CSS 4 | `http://localhost:3000` |
| Mailpit (local only) | Catches outgoing email | `http://localhost:8025` |

## Requirements

- PHP 8.5 with the `intl`, `pdo_mysql`, `gd` and `zip` extensions, and Composer 2
- Node 22.5+ (Node 24 recommended, see `frontend/.nvmrc`)
- Docker, for MySQL and Mailpit (or your own MySQL 8 and SMTP server)

## Local setup

**1. MySQL and Mailpit**

```bash
docker run -d --name nlb-mysql -p 3306:3306 \
  -e MYSQL_ALLOW_EMPTY_PASSWORD=yes -e MYSQL_DATABASE=backend \
  -v nlb-mysql-data:/var/lib/mysql mysql:8.4

docker run -d --name nlb-mailpit -p 1025:1025 -p 8025:8025 axllent/mailpit
```

Later, start them again with `docker start nlb-mysql nlb-mailpit`.

**2. Backend**

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve            # http://localhost:8000
```

**3. Your admin account**

```bash
php artisan app:make-admin you@example.com --name="Your Name"
```

This emails you an invitation. Open it in Mailpit (`http://localhost:8025`), choose a password, and you're logged in to `http://localhost:8000/dashboard`.

**4. Frontend**

```bash
cd frontend
npm install
npm run dev                  # http://localhost:3000
```

### Configuration

The defaults work for the local setup above. The settings you are most likely to change:

| Setting | Where | Default | What it does |
|---|---|---|---|
| `APP_NAME` | `backend/.env` | `Codewave` | Name in the emails (subject, sender, header) |
| `APP_URL` | `backend/.env` | `http://localhost:8000` | Base of the image URLs in the API |
| `FRONTEND_URL` | `backend/.env` | `http://localhost:3000` | Origin allowed by CORS, and the base of password reset links |
| `DB_*` | `backend/.env` | MySQL on `127.0.0.1:3306`, database `backend`, user `root` | Database connection |
| `MAIL_*` | `backend/.env` | Mailpit on `127.0.0.1:1025`, from `hello@example.com` | Outgoing email; set a real provider and sender in production |
| `NUXT_PUBLIC_API_BASE` | environment or `frontend/.env` | `http://localhost:8000/api/v1` | Where the frontend loads posts from |

## How it works

### Users and roles

There is no public registration. An admin adds a user under **Users → New user** with a name, email and role. The user gets an email with a one-time link (valid for 48 hours) to choose a password, and is logged in right after. Pending invitations can be resent from the users list. Users can change their own name, email and password under **Edit profile** in the user menu.

| Role | Can manage |
|---|---|
| `admin` | Everything: users, roles and permissions, the shop (products, brands, customers, orders) and the dashboard widgets |
| `editor` | Blog content: posts, articles, tags, categories |

Users without one of these roles can't open the admin panel.

### Posts

Posts are written in the admin panel (title, slug, description in markdown, author, category, thumbnail). A post only appears on the site once its **Active** toggle is on (shown as **Published** in the posts list): drafts are left out of `/api/v1/posts`, and a draft's URL returns 404.

### Markdown posts

Markdown files in `frontend/content/blog/*.md` are published too, without going through the admin panel. They appear on `/blog` and the homepage mixed with the admin panel posts, newest first, and open at `/blog/<file name>`. If a markdown file and an admin panel post have the same slug, the markdown post is shown. See [`backend/docs/Nuxt-Content.md`](backend/docs/Nuxt-Content.md) for the frontmatter fields and the components you can use in markdown.

### Products

Products are managed under the shop in the admin panel. The API only returns a product when **Visibility** is on and its **Availability** date is today or earlier; otherwise the product is left out of the list and its URL returns 404. The frontend doesn't show products yet.

### API

Public, read-only, and limited to 60 requests per minute per user or IP address.

| Endpoint | Returns |
|---|---|
| `GET /api/v1/posts` | Published posts |
| `GET /api/v1/posts/{slug}` | One published post, or 404 |
| `GET /api/v1/products` | Visible, available products |
| `GET /api/v1/products/{slug}` | One visible, available product, or 404 |

### Frontend pages

| Page | Shows |
|---|---|
| `/` | Latest posts (API and markdown) next to a featured article |
| `/blog` | All posts (API and markdown), newest first |
| `/blog/<slug>` | One post |

Posts from the API are loaded in the browser; markdown posts are rendered on the server.

## Tests and CI

```bash
cd backend && php artisan test    # uses in-memory SQLite, no MySQL needed
cd frontend && npx nuxt build
```

GitHub Actions (`.github/workflows/ci.yml`) runs both on every pull request and on pushes to `main`.

## Not built yet

- The **Projects** and **Cases** links in the navigation have no pages.
- The featured article on the homepage is hard-coded in `frontend/app/pages/index.vue`.
- The products API exists, but the frontend has no shop pages.

## More documentation

- [`backend/README.md`](backend/README.md): backend stack, setup and admin panel screenshots
- [`frontend/README.md`](frontend/README.md): frontend stack, setup and production build
- [`backend/docs/Nuxt-Content.md`](backend/docs/Nuxt-Content.md): writing markdown posts
