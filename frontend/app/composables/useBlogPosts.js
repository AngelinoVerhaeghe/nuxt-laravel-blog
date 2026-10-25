// Posts from the Laravel API and the markdown files in content/blog, merged into
// one list (newest first). Markdown posts are converted to the API's shape so
// both render with PostCard.
export async function useBlogPosts() {
    const { apiBase } = useRuntimeConfig().public;

    // Start both before awaiting: after an await, a composable loses the Nuxt context
    const [ { data: markdownPosts }, { data: apiPosts, status } ] = await Promise.all([
        useAsyncData('blog-markdown-posts', () => queryCollection('blog').all()),
        useFetch(() => `${ apiBase }/posts`, {
            transform: (_posts) => _posts.data,
            server: false
        }),
    ]);

    const posts = computed(() => [
        ...(apiPosts.value ?? []),
        ...(markdownPosts.value ?? []).map(fromMarkdown),
    ].sort((a, b) => new Date(b.createAt) - new Date(a.createAt)));

    return { posts, status };
}

function fromMarkdown(post) {
    return {
        id: post.id,
        title: post.title,
        slug: post.path.replace(/^\/blog\//, ''),
        description: post.description,
        imageUrl: post.originalUrl ? `/images/blog/${ post.originalUrl }` : null,
        // Same format as the API ("Aug 22, 2023"); UTC so server and client agree
        createAt: new Date(post.createAt).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', timeZone: 'UTC' }),
        author: { name: post.author },
        category: { name: post.category },
    };
}
