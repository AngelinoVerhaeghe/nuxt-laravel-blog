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
