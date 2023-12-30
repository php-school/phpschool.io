import {defineStore} from 'pinia';

const useBlogStore = defineStore('blog', {
    state: () => ({
        posts: [],
    }),
    actions: {
        async initialize() {
            if (this.posts.length > 0) {
                return
            }

            try {
                const response = await fetch(import.meta.env.VITE_API_URL + '/api/posts');
                const data = await response.json();

                this.posts = data.posts;
            } catch (error) {
                console.error('Error fetching posts:', error);
            }
        },
    },
});

export {useBlogStore};