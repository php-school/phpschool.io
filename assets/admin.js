import { createApp } from 'vue'
import Dashboard from "./components/Admin/Dashboard.vue";
import Workshops from "./components/Admin/Workshops.vue";
import Workshop from "./components/Admin/Workshop.vue";
import WorkshopInstalls from "./components/Admin/WorkshopInstalls.vue";
import NewWorkshops from "./components/Admin/NewWorkshops.vue";
import Students from "./components/Admin/Students.vue";
import Settings from "./components/Admin/Settings.vue";
import Home from "./components/Admin/Home.vue";
import Events from "./components/Admin/Events.vue";

import { createRouter, createWebHistory } from 'vue-router'

import './styles/admin.js'

const components = {
    Dashboard,
}
const app = createApp({components});

const routes = [
    { path: '/', component: Home },
    { path: '/workshops', component:  Workshops},
    { path: '/workshop/:id', component:  Workshop},
    { path: '/workshop-installs', component:  WorkshopInstalls},
    { path: '/new-workshops', component:  NewWorkshops},
    { path: '/students', component:  Students},
    { path: '/settings', component:  Settings},
    { path: '/events', component:  Events},
]

const router = createRouter({
    history: createWebHistory('/admin'),
    routes,
})

app.use(router);

app.mount('#app');
