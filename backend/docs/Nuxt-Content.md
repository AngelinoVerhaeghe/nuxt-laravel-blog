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

### Showing one post

A post's `path` is its file path without the extension, e.g. `content/blog/palia.md` → `/blog/palia`. Note that `/blog/[slug]` is already used for the API posts, so a markdown page needs its own route, for example `app/pages/articles/[...slug].vue`:

```vue
<script setup>
const route = useRoute();

const { data: post } = await useAsyncData( `article-${ route.path }`, () => {
    return queryCollection( 'blog' )
        .path( route.path.replace( '/articles', '/blog' ) )
        .first()
} )
</script>

<template>
    <article class="mx-auto max-w-7xl prose prose-invert p-2 sm:p-6 lg:p-8">
        <ContentRenderer v-if="post" :value="post"/>
    </article>
</template>
```

### Listing posts

Pass the posts to the `PostCardMarkdown` component:

```vue
<script setup>
const { data: posts } = await useAsyncData( 'posts', () => {
    return queryCollection( 'blog' ).all()
} );
</script>

<template>
    <PostCardMarkdown :posts="posts"/>
</template>
```

The component links to `post.path` (v2 used `post._path`). If the page lives under another route (like `/articles`), adjust the links there.

For the three latest posts on the homepage:

```javascript
const { data: posts } = await useAsyncData( 'latest-posts', () => {
    return queryCollection( 'blog' )
        .order( 'createAt', 'DESC' )
        .limit( 3 )
        .all()
} )
```

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
