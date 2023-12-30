import 'vite/modulepreload-polyfill'
import './styles'
import { FocusTrap } from 'focus-trap-vue'
import VueClickAway from "vue3-click-away";
import VueDiff from 'vue-diff';
import 'vue-diff/dist/index.css';
import VueShepherdPlugin from './shepherd-plugin';
import results from "./components/Online/Results/results.js";
import Home from "./components/Website/Pages/Home.vue";
import SubmitWorkshop from "./components/Website/Pages/SubmitWorkshop.vue";

import Offline from "./components/Website/Pages/Offline.vue";
import Docs from "./components/Website/Pages/Docs.vue";

import App from "./components/Website/App.vue";


import {docs} from "./components/Website/Docs/contents.js";
import Layout from "./components/Website/Layout.vue";
import Dashboard from "./components/Online/Dashboard.vue";
import CompactLayout from "./components/Website/CompactLayout.vue";
import Events from "./components/Website/Pages/Events.vue";
import BlogIndex from "./components/Website/Pages/BlogIndex.vue";
import BlogPost from "./components/Website/Pages/BlogPost.vue";
import Terminal from "./components/Website/Docs/Terminal.vue";
import {ViteSSG} from "vite-ssg";
import { createPinia } from 'pinia'
import {useBlogStore} from "./stores/blog";
import {useEventStore} from "./stores/events";

const docRoutes = [].concat(...docs.map(doc => {
    return doc.sections.map(section => {
        const parts = ['docs', doc.path, section.path];

        return {
            path: '/' + parts.filter(part => part !== '').join('/'),
            component: section.component,
            meta: {section: section, group: doc}
        };
    });
}));

const routes = [
    { path: '/', component: Home, meta: {layout: Layout} },
    { path: '/online', component: Dashboard, meta: {layout: CompactLayout} },
    { path: '/editor', component: Dashboard, meta: {layout: CompactLayout} },
    { path: '/offline', component: Offline, meta: {layout: Layout} },
    { path: '/submit', component: SubmitWorkshop, meta: {layout: Layout} },
    { path: '/docs', component: Docs, children: docRoutes, meta: {layout: Layout} },
    { path: '/events', component: Events, name: "events", meta: {layout: Layout} },
    { path: '/blog', component: BlogIndex, name: "blog", meta: {layout: Layout} },
    { path: '/blog/:page(\\d+)?', component: BlogIndex, props: true, meta: { layout: Layout } },
    { path: '/blog/:slug', component: BlogPost, name: "blog-post", props: true, meta: { layout: Layout } },
];

export const createApp = ViteSSG(
    App,
    {
        routes,
        scrollBehavior(to, from, savedPosition) {
            // always scroll to top
            return { top: 0 }
        },
    },
    async ({ app, router, routes, isClient, initialState, onSSRAppRendered }) => {
        Object.entries(results).forEach(([name, resultComponent]) => {
            app.component(name, resultComponent);
        });
        app.component('FocusTrap', FocusTrap)
        app.component('Terminal', Terminal)

        app.use(VueClickAway);
        app.use(VueDiff);
        app.use(VueShepherdPlugin);
        const pinia = createPinia()
        app.use(pinia)

        if (isClient) {
            pinia.state.value = (initialState.pinia) || {}
        } else {
            const blogStore = useBlogStore(pinia)
            await blogStore.initialize();

            const eventStore = useEventStore(pinia)
            await eventStore.initialize();

            onSSRAppRendered(() => {
                initialState.pinia = pinia.state.value
            })
        }
    }
);

export async function includedRoutes(paths, routes) {
    let pathsToRender = routes.filter(route => {
        return route.name === 'blog' || route.name === 'blog-post' || route.name === 'events';
    });

    const response = await fetch(import.meta.env.VITE_API_URL + '/api/posts');
    const posts = await response.json();
    const slugs = posts.posts.map(post => post.slug);

    return pathsToRender.flatMap((route) => {
        return route.name === 'blog-post'
            ? slugs.map(slug => `/blog/${slug}`)
            : route.path
    });
}