import 'vite/modulepreload-polyfill'
import './styles'
import { FocusTrap } from 'focus-trap-vue'
import VueClickAway from "vue3-click-away";
import VueDiff from 'vue-diff';
import 'vue-diff/dist/index.css';
import {markRaw} from "vue";
import VueShepherdPlugin from './shepherd-plugin';
import results from "./components/Online/Results/results.js";
import Home from "./components/Website/Pages/Home.vue";
import SubmitWorkshop from "./components/Website/Pages/SubmitWorkshop.vue";

import Offline from "./components/Website/Pages/Offline.vue";
import Docs from "./components/Website/Pages/Docs.vue";

import App from "./components/Website/App.vue";

import {docs} from "./components/Website/Docs/contents.js";
import Layout from "./components/Website/Layout.vue";
import CompactLayout from "./components/Website/CompactLayout.vue";

import Dashboard from "./components/Online/Dashboard.vue";
import Events from "./components/Website/Pages/Events.vue";
import BlogIndex from "./components/Website/Pages/BlogIndex.vue";
import BlogPost from "./components/Website/Pages/BlogPost.vue";
import Terminal from "./components/Website/Docs/Terminal.vue";
import {ViteSSG} from "vite-ssg";
import { createPinia } from 'pinia'
import {useBlogStore} from "./stores/blog";
import {useEventStore} from "./stores/events";

import {useAdminStore} from "./stores/admin";
import AdminLayout from "./components/Admin/Layout.vue";
import EmptyAdminLayout from "./components/Admin/EmptyLayout.vue";
import AdminLogin from "./components/Admin/Login.vue";
import AminHome from "./components/Admin/Home.vue";
import AdminWorkshops from "./components/Admin/Workshops.vue";
import AdminWorkshop from "./components/Admin/Workshop.vue";
import AdminWorkshopInstalls from "./components/Admin/WorkshopInstalls.vue";
import AdminNewWorkshops from "./components/Admin/NewWorkshops.vue";
import AdminStudents from "./components/Admin/Students.vue";
import AdminSettings from "./components/Admin/Settings.vue";
import AdminEvents from "./components/Admin/Events.vue";
import {useStudentStore} from "./stores/student";
import {useWorkshopStore} from "./stores/workshops";
import ExerciseEditor from "./components/Online/ExerciseEditor.vue";

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
    { path: '/online/editor/:workshop/:exercise', component: ExerciseEditor, props:true, meta: {layout: CompactLayout} },
    { path: '/offline', component: Offline, meta: {layout: Layout} },
    { path: '/submit', component: SubmitWorkshop, meta: {layout: Layout} },
    { path: '/docs', component: Docs, children: docRoutes, meta: {layout: Layout} },
    { path: '/events', component: Events, name: "events", meta: {layout: Layout} },
    { path: '/blog', component: BlogIndex, name: "blog", meta: {layout: Layout} },
    { path: '/blog/:page(\\d+)?', component: BlogIndex, props: true, meta: { layout: Layout } },
    { path: '/blog/:slug', component: BlogPost, name: "blog-post", props: true, meta: { layout: Layout } },
    { path: '/login', component: AdminLogin, name: "admin-login", props: true, meta: { layout: EmptyAdminLayout }},
    { path: '/admin', component: AminHome, name: "admin", props: true, meta: { layout: AdminLayout } },
    { path: '/admin/workshops', component: AdminWorkshops, props: true, meta: { layout: AdminLayout }},
    { path: '/admin/workshop/:id', component: AdminWorkshop, props: true, meta: { layout: AdminLayout }},
    { path: '/admin/workshop-installs', component: AdminWorkshopInstalls, props: true, meta: { layout: AdminLayout }},
    { path: '/admin/new-workshops', component: AdminNewWorkshops, props: true, meta: { layout: AdminLayout }},
    { path: '/admin/students', component: AdminStudents, props: true, meta: { layout: AdminLayout }},
    { path: '/admin/settings', component: AdminSettings, props: true, meta: { layout: AdminLayout }},
    { path: '/admin/events', component: AdminEvents, props: true, meta: { layout: AdminLayout }},
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
        pinia.use(({ store }) => { store.router = markRaw(router) });
        app.use(pinia)

        if (isClient) {
            pinia.state.value = (initialState.pinia) || {}

            const studentStore = useStudentStore(pinia);
            await studentStore.initialize();
        }

        if (!isClient || import.meta.env.DEV) {
            const blogStore = useBlogStore(pinia)
            await blogStore.initialize();

            const eventStore = useEventStore(pinia)
            await eventStore.initialize();

            const workshopStore = useWorkshopStore(pinia)
            await workshopStore.initialize();

            onSSRAppRendered(() => {
                initialState.pinia = pinia.state.value
            })
        }

        const adminStore = useAdminStore(pinia);

        router.beforeEach(async (to, from) => {
            //if not admin route and not login route, skip, it's a non-authenticated route
            if (!to.path.startsWith('/admin') && to.name !== 'admin-login') {
                return;
            }

            //if we are not logged in redirect to login page
            if (!adminStore.admin && to.name !== 'admin-login') {
                return { name: 'admin-login' }
            }
        })
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