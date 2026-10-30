# Codewave blog: frontend

Nuxt 4 site for the Codewave blog. It shows posts from the Laravel API in [`../backend`](../backend) together with markdown posts from `content/blog`, in a soft pastel theme.

![Codewave homepage](home_page.png)

## Stack

- [Nuxt 4](https://nuxt.com/docs) (Vue 3.5), source in `app/`
- [Nuxt Content 3](https://content.nuxt.com) for markdown posts, collection defined in `content.config.ts`
- [Tailwind CSS 4](https://tailwindcss.com) via `@tailwindcss/vite`, with the typography plugin; theme in `app/assets/css/main.css`
- `@nuxt/image`, `@nuxt/icon`, Headless UI and Heroicons
- TypeScript checks with `vue-tsc`

## Requirements

- Node 22.5+ (Node 24 recommended, see `.nvmrc`). Nuxt Content uses Node's built-in SQLite.
- The backend API running (default `http://localhost:8000`). See the [root README](../README.md) for the full local setup.

## Setup

```bash
npm install
npm run dev        # http://localhost:3000
```

The API URL defaults to `http://localhost:8000/api/v1`. Override it with an environment variable or a `.env` file:

```bash
NUXT_PUBLIC_API_BASE=https://api.example.com/api/v1
```

Run the site on the origin set as `FRONTEND_URL` in `backend/.env` (`http://localhost:3000` by default). The API only allows CORS requests from that origin, so on another port the API posts won't load.

## Scripts

| Command | What it does |
|---|---|
| `npm run dev` | Dev server with hot reload on `http://localhost:3000` |
| `npm run build` | Production build in `.output/` |
| `npm run preview` | Serve the production build locally |
| `npm run typecheck` | Type check the app with `vue-tsc` |

CI (`.github/workflows/ci.yml`) runs `npm run typecheck` and `npx nuxt build` on every pull request and on pushes to `main`. See the [deployment documentation](https://nuxt.com/docs/getting-started/deployment) for hosting options.

## Pages

| Page | Shows |
|---|---|
| `/` | Hero, a featured article and the three latest posts |
| `/blog` | All posts, newest first |
| `/blog/<slug>` | One post |

## Posts

`app/composables/useBlogPosts.js` loads both kinds of posts and merges them into one list, newest first:

- **API posts** come from `GET /api/v1/posts` and are loaded in the browser.
- **Markdown posts** are the files in `content/blog/*.md`, rendered on the server. They are converted to the API's shape so both render with the same `PostCard` component. Their images go in `public/images/blog/`.

On `/blog/<slug>`, a markdown post with that slug wins over an API post with the same slug. See [`backend/docs/Nuxt-Content.md`](../backend/docs/Nuxt-Content.md) for the frontmatter fields and the components you can use in markdown (such as `Callout`).

## Theme

The pastel palette and fonts are Tailwind theme tokens in `app/assets/css/main.css`, so they're available as utilities like `bg-lavender` or `text-ink-soft`:

| Token | Use |
|---|---|
| `cream` | Page background |
| `ink`, `ink-soft` | Text and muted text |
| `lavender`, `rose`, `peach`, `mint` | Light tints for surfaces, badges and the hero gradient |
| `lavender-deep`, `mint-deep` | Accent text on those tints |

Headings use Fraunces (`font-fraunces`) and body text uses Quicksand (`font-quicksand`), both loaded from Bunny Fonts.

## Project structure

```
app/
  app.vue               Layout: navigation, page, footer
  assets/css/main.css   Tailwind setup and theme tokens
  components/           Button, PostCard, TheNavigation, TheFooter
  components/content/   Components for use inside markdown posts
  composables/          useBlogPosts
  pages/                index, blog/index, blog/[slug]
content/blog/           Markdown posts
public/images/blog/     Images for markdown posts
nuxt.config.ts          Modules, API base URL, content and image settings
content.config.ts       Frontmatter schema for the blog collection
```

## Not built yet

- The **Projects** and **Cases** links in the navigation have no pages.
- The featured article on the homepage is hard-coded in `app/pages/index.vue`.
