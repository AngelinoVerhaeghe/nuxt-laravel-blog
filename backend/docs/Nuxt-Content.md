## Nuxt Content (v3)

Markdown posts live in `frontend/content/blog/*.md`. They belong to the `blog` collection defined in `frontend/content.config.ts`:

```typescript
import { defineCollection, defineContentConfig, z } from '@nuxt/content'

export default defineContentConfig( {
    collections: {
        blog: defineCollection( {
            type: 'page',
            source: 'blog/**/*.md',
            schema: z.object( {
                createAt: z.string(),
                originalUrl: z.string(),
                category: z.string(),
                author: z.string(),
            } ),
        } ),
    },
} );
```

Add a field to `schema` before using it in the frontmatter of a post. `title` and `description` are built in for `page` collections.

### On the site

Markdown posts are shown together with the API posts:

- `/blog` and the homepage list both, newest first. `useBlogPosts()` (`frontend/app/composables/useBlogPosts.js`) fetches both and converts the markdown posts to the API's shape, so `PostCard` renders either kind.
- `/blog/<slug>` (`frontend/app/pages/blog/[slug].vue`) renders the markdown post at `/blog/<slug>` with `<ContentRenderer>` if there is one, and otherwise loads the post from the API. A post's `path` is its file path without the extension, e.g. `content/blog/palia.md` → `/blog/palia`. If a markdown file and an API post share a slug, the markdown post wins.

The frontmatter maps to the card like this:

| Frontmatter | Card |
|---|---|
| `title`, `description` | title, description |
| `createAt` (`YYYY-MM-DD`) | date, shown as "Aug 22, 2023" like the API dates |
| `originalUrl` | image file in `frontend/public/images/blog/` |
| `category`, `author` | category badge, author |

Markdown posts are rendered on the server; API posts are still fetched in the browser.

### Components in markdown

Vue components in `frontend/app/components/content/` can be used in markdown with MDC syntax, for example the `Callout` component:

```markdown
::callout{title="Explore a Vibrant New World"}
From sprawling flower fields to hilly forests and rocky beaches.
::
```

Don't wrap `<slot/>` in a `<p>` in these components: the markdown text is already a paragraph, and a `<p>` inside a `<p>` breaks hydration.

### Code highlighting

Configured in `frontend/nuxt.config.ts` under `content.build.markdown.highlight` (theme `nord`).
